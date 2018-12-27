<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018.12.24
 * Time: 16:09
 */

namespace app\football\controller;


use app\home\controller\Base;
use Symfony\Component\Yaml\Tests\DumperTest;
use think\Db;
use think\Log;

class Optional extends Base
{
    static $GAME_TYPE_NINE = 9;
    static $GAME_TYPE_FOURTEEN = 14;
    static $GAME_TYPE_NINE_IN_DB = 3;
    static $GAME_TYPE_FOURTEEN_IN_DB = 4;
    static $GAME_RESULT_WIN = 3;
    static $GAME_RESULT_DREW = 1;
    static $GAME_RESULT_LOSE = 0;

    public function get_session_list()
    {
        $sessionLists = db('fbo_game')
            ->field('id as game_id,name,session')
            ->where('deadline', '>', date('Y-m-d H:i:s'))
            ->where('status', '=', '0')
            ->select();
        echo json_encode(['msg' => '请求成功', 'code' => 1, 'success' => false, 'list' => $sessionLists]);
    }

    public function get_list()
    {
        $data = $_REQUEST;
        if (empty($data['game_id'])) {
            $game = db('fbo_game')
                ->where('deadline', '>', date('Y-m-d H:i:s'))
                ->where('status', '=', 0)
                ->order('id desc')
                ->find();
        } else {
            $game = db('fbo_game')
                ->where('deadline', '>', date('Y-m-d H:i:s'))
                ->where('status', '=', 0)
                ->find($data['game_id']);
        }
        if (!$game) {
            echo json_encode(['msg' => '无比赛', 'code' => 1001, 'success' => false, 'list' => []]);
            exit();
        }

        $info = db('fbo_game_info')
            ->where('fbo_game_id', '=', $game['id'])
            ->field('id as fbo_info_id,league_type,match_time,home,load')
            ->select();
        $result = [
            'game_id' => $game['id'],
            'session' => $game['session'],
            'lottery_time' => $game['lottery_time'],
            'deadline' => $game['deadline'],
            'bonus_fourteen_1' => $game['bonus_fourteen_1'],
            'bonus_fourteen_2' => $game['bonus_fourteen_2'],
            'bonus_nine_1' => $game['bonus_nine_1'],
            'info' => $info
        ];
        echo json_encode(['msg' => '请求成功', 'code' => 1, 'success' => true, 'data' => $result]);
    }

    public function create_order()
    {
        Db::startTrans();
        $_data = $_REQUEST;
        $user_id = isset($_data['user_id']) ? $_data['user_id'] : 0;
        $game_id = isset($_data['game_id']) ? $_data['game_id'] : 0;
        $multiple = isset($_data['multiple']) ? $_data['multiple'] : 0;
        $type = isset($_data['type']) ? $_data['type'] : self::$GAME_TYPE_NINE;
        $ticket = isset($_data['ticket']) ? json_decode($_data['ticket'], true) : [];

        $courage = [];
        $notEmpty = 0;
        foreach ($ticket as $k => $o) {
            if (strlen($o['tz'])) {
                $temp = str_split($o['tz']);
                $validData = [];
                foreach ($temp as $t) {
                    if (strlen($t) && in_array($t, [self::$GAME_RESULT_WIN, self::$GAME_RESULT_DREW, self::$GAME_RESULT_LOSE])) {
                        $validData[] = $t;
                    }
                }
                if (!empty($validData)) {
                    $notEmpty++;
                    rsort($validData);
                    $ticket[$k]['tz'] = implode('', $validData);
                } else {
                    if ($o['is_courage']) {
                        echo json_encode(['msg' => '请输入正确的投注编号', 'code' => 1001, 'success' => false, 'list' => []]);
                        exit;
                    }
                    unset($ticket[$k]);
                }
                if ($o['is_courage']) {
                    $courage[] = $o['game_info_id'];
                }
            }
        }
        if (empty($game_id)) {
            echo json_encode(['msg' => '请输入GAME_ID', 'code' => 1001, 'success' => false, 'list' => []]);
            exit;
        }
        if (empty($user_id)) {
            echo json_encode(['msg' => '请输入USER_ID', 'code' => 1001, 'success' => false, 'list' => []]);
            exit;
        }
//        if (count($notEmpty) != 14) {
//            echo json_encode(['msg' => '出票有误', 'code' => 1001, 'success' => false, 'list' => []]);
//            exit;
//        }
        if ($notEmpty < 9 && $type == self::$GAME_TYPE_NINE) {
            echo json_encode(['msg' => '至少选取9场比赛', 'code' => 1001, 'success' => false, 'list' => []]);
            exit;
        }
        if ($notEmpty < 14 && $type == self::$GAME_TYPE_FOURTEEN) {
            echo json_encode(['msg' => '至少选取14场比赛', 'code' => 1001, 'success' => false, 'list' => []]);
            exit;
        }
        if ($notEmpty > 14) {
            echo json_encode(['msg' => '不能超过14场', 'code' => 1001, 'success' => false, 'list' => []]);
            exit;
        }

        $game = \db('fbo_game')
            ->where('deadline', '>', date('Y-m-d H:i:s'))
            ->where('status', '=', 0)
            ->find($game_id);

        if (empty($game)) {
            echo json_encode(['msg' => '本场比赛已停止投注', 'code' => 1001, 'success' => false, 'list' => []]);
            exit;
        }

        if ($type == self::$GAME_TYPE_FOURTEEN) {
            $game_c = 'FB14S';
            $chuan = 14;
            $game_cate = self::$GAME_TYPE_FOURTEEN_IN_DB;
        } elseif ($type == self::$GAME_TYPE_NINE) {
            $game_c = 'FB9S';
            $chuan = 9;
            $game_cate = self::$GAME_TYPE_NINE_IN_DB;
        } else {
            echo json_encode(['msg' => '请选择任九或者任十四', 'code' => 1001, 'success' => false, 'list' => []]);
            exit;
        }

        $quantityAndPrice = self::get_quantity_and_price_pr($ticket, $courage, $type, $multiple);

        $yh = db('user')->where('id=' . $user_id)->find();

        $amount_money = $yh['amount_money'];
        if ($quantityAndPrice['price'] == 0) {
            echo json_encode(['msg' => '投注金额为零，请检查后重试', 'code' => 1103, 'success' => false, 'order_money' => $quantityAndPrice['price'], 'balance' => $amount_money]);
            exit;
        }
        if ($quantityAndPrice['price'] > $amount_money) {
            echo json_encode(['msg' => '投注金额超出可用金额', 'code' => 1103, 'success' => false, 'order_money' => $quantityAndPrice['price'], 'balance' => $amount_money]);
            exit;
        }

        $order['order_no'] = date('YmdHis') . $user_id . $game_c . mt_rand(1000, 9999);
        $order['user_id'] = $yh['id'];
        $order['add_time'] = date('Y-m-d H:i:s');
        $order['multiple'] = $multiple;
        $order['chuan'] = $chuan;
        $order['is_win'] = 0;
        $order['order_type'] = 1;
        $order['game_cate'] = $game_cate;
        $order['order_status'] = 1;
        $order['tz_num'] = $quantityAndPrice['quantity'] * $multiple;
        $order['order_money'] = $quantityAndPrice['price'];
        $order_id = db('order')->insert($order, false, true);

        $game_info = db('fbo_game_info')->where('fbo_game_id', '=', $game_id)->select();
        $order_info = [];

        $competition_list = array_column($game_info, 'competition');

        $game_info_ids = \db('fbo_game_info')->where('fbo_game_id', '=', $game_id)->field('id')->select();

        $game_info_ids = !empty($game_info_ids) ? array_column($game_info_ids, 'id') : [];

        foreach ($ticket as $a_ticket) {
            if (!in_array($a_ticket['game_info_id'], $game_info_ids)) {
                // 回滚事务
                Db::rollback();
                echo json_encode(['msg' => '[game_info_id]错误', 'code' => 1103, 'success' => false]);
                exit;
            }
            $order_info[] = [
                'order_id' => $order_id,
                'game_id' => $a_ticket['game_info_id'],
                'dan' => $a_ticket['is_courage'] ? 1 : 0,
                'tz_result' => $a_ticket['tz'],
                'tz_odds' => '',
                'game_status' => 0,
                'add_time' => date('Y-m-d H:i:s'),
                'game_cate' => $game_cate,
            ];
        }

        $order_info_res = db('order_info')->insertAll($order_info);

        if ($order_id && $order_info_res > 0) {
            if ($quantityAndPrice['price'] >= $yh['no_balance']) {
                db('user')->where('id=' . $yh['id'])->setDec('no_balance', $yh['no_balance']);
                $residue = $quantityAndPrice['price'] - $yh['no_balance'];
                db('user')->where('id=' . $yh['id'])->setDec('balance', $residue);
            } else {
                db('user')->where('id=' . $yh['id'])->setDec('no_balance', $quantityAndPrice['price']);
            }
            db('user')->where('id=' . $yh['id'])->setDec('amount_money', $quantityAndPrice['price']);
            $user = db('user')->where('id=' . $yh['id'])->find();
            if ($user['amount_money'] < 0) {
                // 回滚事务
                Db::rollback();
                echo json_encode(['msg' => '投注金额超出可用金额', 'code' => 1103, 'success' => false]);
                exit;
            }
            /**添加账单明细**/
            $detail['user_id'] = $yh['id'];
            $detail['deal_cate'] = 3;
            $detail['deal_money'] = $quantityAndPrice['price'];
            $detail['new_money'] = $user['amount_money'];
            $detail['add_time'] = date('Y-m-d H:i:s');
            $detail['game_id'] = $game_cate;
            $detail_res = db('account_details')->insert($detail, false, true);
            /**添加账单明细end**/

            // 提交事务
            Db::commit();
            echo json_encode(['msg' => '投注成功', 'code' => 1, 'success' => true, 'order_id' => $order_id]);
            exit;
        } else {
            // 回滚事务
            Db::rollback();
            echo json_encode(['msg' => '投注失败', 'code' => 1104, 'success' => false]);
            exit;
        }

    }

    public function get_quantity_and_price()
    {
        $_data = $_REQUEST;
        $multiple = isset($_data['multiple']) ? $_data['multiple'] : 0;
        $type = isset($_data['type']) ? $_data['type'] : 0;
        $ticket = isset($_data['ticket']) ? json_decode($_data['ticket'], true) : [];

        if (empty($multiple)) {
            echo json_encode(['msg' => '倍率必须大于0', 'code' => 1001, 'success' => false, 'list' => []]);
            exit;
        }
//        dump($ticket);
//        exit;
        $courage = [];
        $notEmpty = 0;
        foreach ($ticket as $k => $o) {
            if (strlen($o['tz'])) {
                $temp = str_split($o['tz']);
                $validData = [];
                foreach ($temp as $t) {
                    if (strlen($t) && in_array($t, [self::$GAME_RESULT_WIN, self::$GAME_RESULT_DREW, self::$GAME_RESULT_LOSE])) {
                        $validData[] = $t;
                    }
                }
                if (!empty($validData)) {
                    $notEmpty++;
                    $ticket[$k]['tz'] = implode('', $validData);
                } else {
                    if ($o['is_courage']) {
                        echo json_encode(['msg' => '请输入正确的投注编号', 'code' => 1001, 'success' => false, 'list' => []]);
                        exit;
                    }
                    unset($ticket[$k]);
                }
                if ($o['is_courage']) {
                    $courage[] = $o['game_info_id'];
                }
            }
        }

//        dump($courage);
        if ($notEmpty < 9 && $type == self::$GAME_TYPE_NINE) {
            echo json_encode(['msg' => '至少选取9场比赛', 'code' => 1001, 'success' => false, 'list' => []]);
            exit;
        }
        if ($notEmpty < 14 && $type == self::$GAME_TYPE_FOURTEEN) {
            echo json_encode(['msg' => '至少选取14场比赛', 'code' => 1001, 'success' => false, 'list' => []]);
            exit;
        }
        if ($notEmpty > 15) {
            echo json_encode(['msg' => '不能超过14场', 'code' => 1001, 'success' => false, 'list' => []]);
            exit;
        }
//        dump($orderToArr);exit;
//        foreach ($courages as $courage) {
//            if (is_null($orderToArr[$courage-1])) {
//            }
//        }
        if ($type != self::$GAME_TYPE_FOURTEEN && $type != self::$GAME_TYPE_NINE) {
            echo json_encode(['msg' => '请选择任九或者任十四', 'code' => 1001, 'success' => false, 'list' => []]);
            exit;
        }
        echo json_encode(['msg' => '请求成功', 'code' => 1, 'success' => false, 'list' => self::get_quantity_and_price_pr($ticket, $courage, $type, $multiple)]);
    }

    private function get_quantity_and_price_pr($ticket, $courage, $type, $multiple)
    {
        $betList = [];
        $courage = [];
//        $orderToArr = explode('-', $ticket);
        foreach ($ticket as $key => $val) {
            $temp = [];
            $temp = str_split($val['tz']);
            $tempArr = [];
            foreach ($temp as $t) {
                if (strlen($t) && in_array($t, [self::$GAME_RESULT_WIN, self::$GAME_RESULT_DREW, self::$GAME_RESULT_LOSE])) {
                    $tempArr[] = [$t];
                }
            }
            if (!empty($tempArr)) {
                $betList[$key + 1] = $tempArr;
                if ($val['is_courage']) {
                    $courage[] = $key;
                }
            }
        }
        if ($type == self::$GAME_TYPE_FOURTEEN) {
            $resultData = [
                'quantity' => self::operation($betList),
                'price' => self::operation($betList) * 2 * $multiple
            ];
            return $resultData;
        }
        $chuanList = [count($betList) - 9];
        $arr = [];
        for ($i = 0; $i < count($betList); $i++) {
            $arr[] = $i;
        }
        $allPossibilitygroup = [];
        foreach ($chuanList as $chuan) {
            $t2 = self::getCombinationToString($arr, $chuan); // 根据场次数量，遍历生成被抛弃场次
            if (!empty($t2)) {
                foreach ($t2 as $t2Val) {
                    $temp = $betList; // 全部场次
                    $cUse = []; // 被抛弃的场次
                    foreach ($t2Val as $t2_val) {
                        $cUse[] = $t2_val;
                        unset($temp[$t2_val + 1]); // 去除被抛弃场次
                    }
                    $continue = false;
                    foreach ($courage as $c) {
                        if (in_array($c, $cUse)) {
                            $continue = true;
                        }
                    }
                    if ($continue) continue;
                    $allPossibilitygroup[] = self::operation($temp);
                }
            } else {
                $temp = $betList;
                $allPossibilitygroup[] = self::operation($temp);
            }
        }
        $resultData = [
            'quantity' => array_sum($allPossibilitygroup),
            'price' => array_sum($allPossibilitygroup) * 2 * $multiple
        ];
        return $resultData;
    }

    private function operation($betList)
    {
        $betListMax = [];
        foreach ($betList as $value) {
            $session = [];
            foreach ($value as $val) {
                $session[] = max($val);
            }
            $betListMax[] = $session;
        }
        $middle = [];
        $countBet = count($betListMax);
        foreach ($betListMax as $k => $max) {
            $middle[$k]['now'] = 0;
            $middle[$k]['max'] = count($max) - 1;
            $middle[$k]['weighting'] = 0;
        }
        $list = [];
        $times = 0;
        while (1) {
            for ($i = 0; $i < $countBet; $i++) {
                if ($middle[$i]['weighting'] == 1) {
                    if ($middle[$i]['now'] < $middle[$i]['max']) {
                        $middle[$i]['now']++;
                        $middle[$i]['weighting'] = 0;
                        break;
                    }
                    if ($i < $countBet) {
                        $middle[$i]['now'] = 0;
                        $middle[$i]['weighting'] = 0;
                        $middle[$i + 1]['weighting']++;
                    }
                }
            }
            $temp = [];
            $isOver = true;
            for ($i = 0; $i < $countBet; $i++) {
                if ($middle[$i]['now'] < $middle[$i]['max']) {
                    $isOver = false;
                }
                $temp[] = $betListMax[$i][$middle[$i]['now']];
            }
            Log::error(implode('-', $temp) . '<br/>');
//            echo implode('-', $temp),'<br/>';
            $list[] = $temp;
            if ($isOver) break;
            if ($middle[0]['now'] < $middle[0]['max']) {
                $middle[0]['now']++;
            } elseif ($middle[0]['now'] == $middle[0]['max']) {
                $middle[0]['now'] = 0;
                $middle[1]['weighting'] = 1;
            }
        }
        return count($list);
    }

    private function getCombinationToString($arr, $m)
    {
        $result = array();
        $result_im = [];
        $tmpArr = $arr;
        if ($m == 1) {
            foreach ($arr as $a) {
                $result[] = [$a];
            }
            return $result;
        }
        unset($tmpArr[0]);
        for ($i = 0; $i < count($arr); $i++) {
            $s = $arr[$i];
            $ret = self::getCombinationToString(array_values($tmpArr), ($m - 1), $result);

            foreach ($ret as $row) {
                $temp = array();
                $temp[] = $s;
                if (is_array($row)) {
                    $temp = array_merge($temp, $row);
                } else {
                    $temp[] = $row;
                }
                sort($temp);
                if (count($temp) != count(array_unique($temp)) || in_array(implode(',', $temp), $result_im)) {
                    continue;
                } else {
                    $result[] = $temp;
                    $result_im[] = implode(',', $temp);
                }
            }
        }
        return $result;
    }

    public function test(){
        dump(\db('order_info')->whereIn('game_cate', '3,4')->where('win_result','<',1)->select());exit;
    }
}