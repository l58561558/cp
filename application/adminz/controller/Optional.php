<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018.12.26
 * Time: 10:12
 */

namespace app\adminz\controller;


use think\Db;
use think\Log;
use think\View;

class Optional extends Base
{
    public function index()
    {
        return view();
    }

    public function get_game_list()
    {
        $count = db("fbo_game")
            ->join('fbo_game_info', 'fbo_game.id = fbo_game_info.fbo_game_id')
            ->where('status<2')
            ->order(['fbo_game.id' => 'desc', 'fbo_game_info.competition' => 'asc'])
//            ->field('fbo_game.id as fbo_game_id,session,lottery_time,deadline,status,fbo_game_info.add_time,competition,league_type,match_time,home,load,match_result')
            ->count();
        $list = db("fbo_game")
            ->join('fbo_game_info', 'fbo_game.id = fbo_game_info.fbo_game_id')
            ->where('status<2')
            ->field('fbo_game.id as fbo_game_id,is_postpone,name,session,lottery_time,deadline,status,fbo_game_info.id as game_info_id,fbo_game_info.add_time,competition,league_type,match_time,home,load,match_result')
            ->order(['fbo_game.id' => 'desc', 'fbo_game_info.competition' => 'asc'])
            ->paginate(14, $count);

//        dump($list);exit;
        //获取分页
        $page = $list->render();
        //遍历数据
        if (!empty($list)) {
            $list->each(function ($item, $key) {
                if ($item['is_postpone'] == 1) {
                    $item['tz_status'] = '已延期';
                } else {
                    if ($item['status'] == 2) {
                        $item['tz_status'] = '已结算';
                    } else {
                        if ($item['deadline'] <= date('Y-m-d H:i:s')) {
                            $item['tz_status'] = '停止投注';
                        } else {
                            $item['tz_status'] = '可以投注';
                        }
                    }
                }

                return $item;
            });
        }
        $this->assign("page", $page);
        $this->assign("_list", $list);
        $html = $this->fetch("optional/fbo_list");
        $this->ajaxReturn(['data' => $html, 'code' => 1]);
    }


    public function test()
    {
        for ($i = 1; $i < 15; $i++) {
            echo 'league_type_' . $i . ':意甲<br/>';

            echo 'match_time_' . $i . ':' . date('Y-m-d H:i:s', strtotime("-1 day")) . '<br/>';

            echo 'home_' . $i . ':' . chr(64 + $i) . chr(64 + $i) . '<br/>';

            echo 'load_' . $i . ':' . chr(96 + $i) . chr(96 + $i) . '<br/>';
        }
    }

    public function add()
    {
        Db::startTrans();
        $data = $_REQUEST;
        $game = [
            'add_time' => date('Y-m-d H:i:s'),
            'status' => 0,
        ];
        if (!empty($data['session'])) {
            $has_session = \db('fbo_game')->where('session', '=', $data['session'])->find();
            if ($has_session) {
                Db::rollback();
                $this->error('当期已存在');
            }
            $game['session'] = $data['session'];
            $game['name'] = $data['session'] . '期';
        } else {
            Db::rollback();
            $this->error('缺失必要参数：session');
        }
        if (!empty($data['lottery_time'])) {
            $game['lottery_time'] = $data['lottery_time'];
        }
        if (!empty($data['deadline'])) {
            $game['deadline'] = $data['deadline'];
        } else {
            $this->error('缺失必要参数：deadline');
        }
        if (!empty($data['bonus_fourteen_1'])) {
            $game['bonus_fourteen_1'] = $data['bonus_fourteen_1'];
        }
        if (!empty($data['bonus_fourteen_2'])) {
            $game['bonus_fourteen_2'] = $data['bonus_fourteen_2'];
        }
        if (!empty($data['bonus_nine_1'])) {
            $game['bonus_nine_1'] = $data['bonus_nine_1'];
        }
        $game_insert = \db('fbo_game')->insert($game);
        if (!$game_insert) {
            Db::rollback();
            $this->error("数据库保存失败");
        }
        $game_id = db()->getLastInsID();


        $competition = [];
        for ($i = 1; $i < 15; $i++) {
            $competition[$i]['competition'] = $i;
            $competition[$i]['fbo_game_id'] = $game_id;
            $temp_str = 'league_type_' . $i;
            if (!empty($data[$temp_str])) {
                $competition[$i]['league_type'] = $data[$temp_str];
            } else {
                $this->error('缺失必要参数：' . $temp_str);
            }
            $temp_str = 'match_time_' . $i;
            if (!empty($data[$temp_str])) {
                $competition[$i]['match_time'] = $data[$temp_str];
            } else {
                $this->error('缺失必要参数' . $temp_str);
            }
            $temp_str = 'home_' . $i;
            if (!empty($data[$temp_str])) {
                $competition[$i]['home'] = $data[$temp_str];
            } else {
                $this->error('缺失必要参数' . $temp_str);
            }
            $temp_str = 'load_' . $i;
            if (!empty($data[$temp_str])) {
                $competition[$i]['load'] = $data[$temp_str];
            } else {
                $this->error('缺失必要参数' . $temp_str);
            }
        }
        $game_info = \db('fbo_game_info')->insertAll($competition);
        if (!$game_id || $game_info != 14) {
            Db::rollback();
            $this->success("保存失败");
        }
        Db::commit();
        $this->success("保存成功");
    }

    public function add_page()
    {
        $competition = [];
        for ($i = 1; $i < 15; $i++) {
            $competition[] = $i;
        }
        $this->assign("competition", $competition);

        return view();
    }

    public function edit_match_result()
    {
//        dump($_REQUEST);
        $data = $_REQUEST;
//        dump($data['match_result']);exit;
        $game_info_id = isset($data['id']) ? (int)$data['id'] : 0;
        $match_result = trim($data['match_result']);
        if (strlen($match_result) != 1) {
            $this->error("保存失败");
        }
        $ord = ord($match_result);
        if ($ord != 48 && $ord != 49 && $ord != 51) {
            $this->error("保存失败");
        }
        $game_info = \db('fbo_game_info')->find($game_info_id);
        if (empty($game_info) || !strlen($match_result)) {
            $this->error("保存失败");
        }
        $update = \db('fbo_game_info')->where('id', '=', $game_info_id)->update(['match_result' => $match_result]);
        if ($update) {
            $this->success("保存成功");
        }
        $this->error("保存失败");
    }

    public function settle_accounts()
    {
        Db::startTrans();
        $data = $_REQUEST;
        $game_id = isset($data['game_id']) ? trim($data['game_id']) : 0;
        $session = isset($data['session']) ? trim($data['session']) : 0;
        $bonus_fourteen_1 = isset($data['bonus_fourteen_1']) ? trim($data['bonus_fourteen_1']) : 0;
        $bonus_fourteen_2 = isset($data['bonus_fourteen_2']) ? trim($data['bonus_fourteen_2']) : 0;
        $bonus_nine_1 = isset($data['bonus_nine_1']) ? trim($data['bonus_nine_1']) : 0;
        if (empty($session) || empty($bonus_fourteen_1) || empty($bonus_fourteen_2) || empty($bonus_nine_1)) {
            $this->error('参数不全');
        }
        if ($bonus_fourteen_1 < $bonus_fourteen_2 || $bonus_fourteen_2 < $bonus_nine_1 || $bonus_fourteen_1 < $bonus_nine_1) {
            $this->error('中奖金额不合理');
        }
        $game = \db('fbo_game')->find($game_id);
        if (empty($game)) {
            Db::rollback();
            $this->error('game_id错误');
        }
        \db('fbo_game')
            ->where('id', '=', $game_id)
            ->update([
                'bonus_fourteen_1' => $bonus_fourteen_1,
                'bonus_fourteen_2' => $bonus_fourteen_2,
                'bonus_nine_1' => $bonus_nine_1
            ]);
        $game_infos = \db('fbo_game_info')
            ->where('fbo_game_id', '=', $game_id)
            ->select();
        if (empty($game_infos)) {
            Db::rollback();
            $this->error('找不到比赛详情');
        }
        $game_info_ids = array_column($game_infos, 'id');

        $right_item_lists = array_column($game_infos, 'match_result', 'id');
        // 将比赛结果存入order_info
        foreach ($game_infos as $game_info) {
            if (!strlen($game_info['match_result'])) {
                Db::rollback();
                $this->error('请先输入分场比赛结果');
            }
            $update_match_result = \db('order_info')
                ->where('game_id', '=', $game_info['id'])
                ->whereIn('game_cate', '3,4')
                ->update([
                    'win_result' => $game_info['match_result'],
                    'game_status' => 1
                ]);
            if (!$update_match_result) {
                Db::rollback();
                $this->error('数据库保存失败！');
            }
        }
        $order_infos = \db('order_info')
            ->whereIn('game_id', implode(',', $game_info_ids))
            ->whereIn('game_cate', '3,4')
            ->select();

        if (empty($order_infos)) { // 没有订单
            \db('fbo_game')->where('id', $game_id)->update(['status' => 2]);
            Db::commit();
            $this->success('没有订单详情');
        }
        $order_ids = array_column($order_infos, 'game_cate', 'order_id');
        $order_lists = [];
        foreach ($order_ids as $order_id => $game_cate) {
            $order_lists[$order_id] = [
//                'user_id' => 0,
                'game_cate' => $game_cate,
                'right_game_ids' => [],
                'bet_lists' => [],
                'right_times' => 0,
                'courage' => [],
                '9_bonus_1_times' => 0,
                '14_bonus_1_times' => 0,
                '14_bonus_2_times' => 0,
                'all_bet' => [],
                'bet_quantity' => 0
            ];
        }
        foreach ($order_infos as $order_info) {
            $order_lists[$order_info['order_id']]['all_bet'][] = [$order_info['game_id'] => $order_info['tz_result']];
            $order_lists[$order_info['order_id']]['bet_quantity'] += strlen($order_info['tz_result']);
            if ($order_info['dan']) {
                $order_lists[$order_info['order_id']]['courage'][] = ['game_id' => $order_info['game_id'], 'bet_result' => $order_info['tz_result']];
            }
            if (in_array($order_info['win_result'], explode(',', $order_info['tz_result']))) {
                $order_lists[$order_info['order_id']]['right_game_ids'][] = $order_info['game_id'];
                $order_lists[$order_info['order_id']]['right_times']++;
            }
        }
        foreach ($order_lists as $key => $list) {
            if ($list['game_cate'] == self::$GAME_TYPE_NINE_IN_DB) { // 任选9
                if ($list['right_times'] >= 9) { // 猜中9个
                    if (!empty($list['courage'])) { // 没有选胆的话，就是中了一注
                        $sorry = 0;
                        foreach ($list['courage'] as $courageArr) {
                            // 如果选的胆都中了，则发奖
                            if (!in_array($right_item_lists[$courageArr['game_id']], explode(',', $courageArr['bet_result']))) {
                                $sorry++;
                            }
                        }
                        if (!$sorry) {
                            $order_lists[$key]['9_bonus_1_times'] = 1;
                        }
                    }
                    $order_lists[$key]['9_bonus_1_times'] = 1;
                }
            } else { //任选14
                if ($list['right_times'] >= 14) {
                    $order_lists[$key]['14_bonus_1_times'] = 1;
                    if ($list['bet_quantity'] > 14) {
                        $bonus_2_times = 0;
                        foreach ($list['all_bet'] as $bet) {
                            foreach ($bet as $b) {
                                $bonus_2_times += strlen($b) - 1;
                            }
                        }
                        $order_lists[$key]['14_bonus_2_times'] = $bonus_2_times;
                    }
                } elseif ($list['right_times'] >= 13) {
                    $order_lists[$key]['14_bonus_2_times'] = 1;
                }
            }
        }
        foreach ($order_lists as $oid => $ol) {
            $order = \db('order')->find($oid);
            $user = \db('user')->find($order['user_id']);
            $award = 0;
            if ($order['game_cate'] == self::$GAME_TYPE_NINE_IN_DB && $ol['9_bonus_1_times']) {
                $award = $ol['9_bonus_1_times'] * $bonus_nine_1 * $order['multiple'];
            }
            if ($order['game_cate'] == self::$GAME_TYPE_FOURTEEN_IN_DB &&
                ($ol['14_bonus_1_times'] ||
                    $ol['14_bonus_2_times']
                )) {
                $award_14_1 = $ol['14_bonus_1_times'] * $bonus_fourteen_1 * $order['multiple'];
                $award_14_2 = $ol['14_bonus_2_times'] * $bonus_fourteen_2 * $order['multiple'];
                $award = $award_14_1 + $award_14_2;
            }

            if (!empty($award)) {
                $update_data = [
                    'balance' => $user['balance'] + $award,
                    'amount_money' => $user['amount_money'] + $award
                ];
                $update_order = \db('order')
                    ->where('order_id', '=', $oid)
                    ->update([
                        'is_win' => 1,
                        'win_money' => $award,
                    ]);
                $update_user = \db('user')
                    ->where('id', '=', $user['id'])
                    ->update($update_data);

                if (!$update_order || !$update_user) {
                    Db::rollback();
                    $this->error('数据库保存失败！');
                }
            } else {
                $update_order = \db('order')
                    ->where('order_id', '=', $oid)
                    ->update([
                        'is_win' => 2,
                    ]);
                if (!$update_order) {
                    Db::rollback();
                    $this->error('数据库保存失败！');
                }
            }
        }
        $update_fbo_game = \db('fbo_game')->where('id', $game_id)->update(['status' => 2]);
        if (!$update_fbo_game) {
            Db::rollback();
            $this->error('数据库保存失败！');
        }

        Db::commit();
        $this->success('结分完毕');
    }

    public function settle_accounts_page()
    {
        $data = $_REQUEST;
        $game_id = isset($data['id']) ? (int)$data['id'] : 0;
        $game = \db('fbo_game')->find($game_id);
        if (empty($game)) {
            $this->redirect('adminz/optional/index');
        }
        $this->assign('game', $game);
        return view();
    }

    public function auto_create()
    {
        $data = $_REQUEST;
        $session = isset($data['issue']) ? (int)$data['issue'] : 0;

        Db::startTrans();
        $url = 'http://cp.zgzcw.com/lottery/zcplayvs.action?lotteryId=13&issue=';
        $now_session = \db('fbo_game')->order('session desc')->find();
        $api_result_json = '';
        if ($session < $now_session['session']) {
            $session = $now_session['session'] + 1;
        }

        $i = 1;
        while (1) {
            if ($i>10) break;
            $new_url = $url . (string)($session);
            $api_result_json = file_get_contents($new_url);
            if ($api_result_json) break;
            $session ++;
            $i++;
        }

        if (empty($api_result_json)) {
            $this->error('没有新比赛');
        }
        $api_result = json_decode($api_result_json, true);
        if (empty($api_result)) {
            $this->error('爬取数据错误');
        }
        if (!$api_result['matchInfo'][0]['gameStartDate'] ||
            !$api_result['matchInfo'][0]['issue'] ||
            !$api_result['matchInfo'][0]['leageNameFull'] ||
            !$api_result['matchInfo'][0]['hostName'] ||
            !$api_result['matchInfo'][0]['guestName'] ||
            !$api_result['matchInfo'][0]['kj_time'] ||
            !$api_result['matchInfo'][0]['lotteryEndDate'] ||
            $api_result['matchInfo'][0]['issue'] != $session
        ) {
            echo $session;
            $this->error('爬取数据有改动！生成失败！');
        }
        $game = [
            'session' => $session,
            'name' => $session . '期',
            'deadline' => $api_result['matchInfo'][0]['lotteryEndDate'],
            'add_time' => date('Y-m-d H:i:s'),
            'status' => 0
        ];
        $insert_game_id = \db('fbo_game')->insertGetId($game);
        if (!$insert_game_id) {
            Db::rollback();
            $this->error('入库失败！');
        }
        $game_infos = [];
        foreach ($api_result['matchInfo'] as $competition => $game_info) {
            $game_infos[] = [
                'fbo_game_id' => $insert_game_id,
                'competition' => $competition + 1,
                'league_type' => $game_info['leageNameFull'],
                'match_time' => $game_info['gameStartDate'],
                'home' => $game_info['hostName'],
                'load' => $game_info['guestName'],
                'add_time' => date('Y-m-d H:i:s'),
            ];
        }
        $insert_game_info = \db('fbo_game_info')->insertAll($game_infos);
        if (!$insert_game_info) {
            Db::rollback();
            $this->error('入库失败！');
        }
        Db::commit();
        $this->success('生成' . $session . '期成功');
    }

    static $GAME_TYPE_NINE_IN_DB = 3;
    static $GAME_TYPE_FOURTEEN_IN_DB = 4;
}