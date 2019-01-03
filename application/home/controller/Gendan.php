<?php
namespace app\home\controller;
use app\home\model\Group;
use think\Db;
use think\Log;
//跟单控制器

class Gendan extends Base
{
    // 发起跟单
    /*
    *order =>         订单数据
        * user_id   (int)       用户ID
	    * tz_num    (int)       注数
	    * game_cate (int)       游戏类型 1.足球 , 2.篮球
	    * multiple  (int)       倍数
	    * chuan     (int)       串法
	    * tz        (array多维数组)     投注内容
	    *
		    [
		        'game_id' => 1, (游戏场次ID)
		        'dan' => 0, (是否选中胆: 0.未选中, 1.选中)
		        'tz_result' => ['1','2','3','4'], (投注选项ID | 数组)

		    ],
		    [
		        'game_id' => 2, (游戏场次ID)
		        'dan' => 0, (是否选中胆: 0.未选中, 1.选中)
		        'tz_result' => ['5','6','7'], (投注选项ID | 数组)
		    ],
		    [
		        'game_id' => 3, (游戏场次ID)
		        'dan' => 0, (是否选中胆: 0.未选中, 1.选中)
		        'tz_result' => ['8','9','10'], (投注选项ID | 数组)
		    ],
    *gendan =>       跟单数据
    *	gd_title 			 方案标题
    *   gd_desc              方案描述
    *	gd_money 			 方案金额
    *   brokerage            中奖佣金 (1% ~ 10%)
    *	order_money 	     订单金额
    */
    function __construct(){
        Log::init([
            'type' =>  'File',
            'path' =>  LOG_PATH,
        ]);
    }
    public function gendan()
    {
        // 获取订单数据 和 跟单信息
        $_data = $_REQUEST;


        if(is_string($_data['order'])){
            $order = json_decode($_data['order'],true); // 订单数据
        }else{
            $order = $_data['order']; // 订单数据
        }
        if(is_string($_data['gendan'])){
            $gendan = json_decode($_data['gendan'],true); // 跟单数据
        }else{
            $gendan = $_data['gendan']; // 跟单数据
        }
        $gd_money = $gendan['order_money']/$order['multiple'];
        if($gendan['gd_money'] != $gd_money){
            echo json_encode(['msg'=>'数据错误','code'=>3104,'success'=>false]);
            exit;
        }
        if($gendan['gd_money'] < 0){
            echo json_encode(['msg'=>'方案金额不能小于0','code'=>3103,'success'=>false]);
            exit;
        }
        if($order['multiple'] == 0){
            echo json_encode(['msg'=>'倍数不能为0','code'=>3102,'success'=>false]);
            exit;
        }
        if(!isset($order['game_cate']) || empty($order['game_cate'])){
        	echo json_encode(['msg'=>'游戏分类为空','code'=>3101,'success'=>false]);
            exit;
        }
        
        $order_data['user_id'] = $order['user_id'];
        $order_data['multiple'] = $order['multiple'];
        $order_data['chuan'] = $order['chuan'];
        $order_data['tz'] = json_decode($order['tz'],true);
        // 开启事务
        Db::startTrans();

        $chaun = explode(',', $order_data['chuan']);
        $touz = $order_data['tz'];
        $yh = db('user')->where('id='.$order_data['user_id'])->find();
        $tz_num = $order['tz_num'];
        $order_money = $gendan['order_money'];

        if($order_money > $yh['amount_money']){
            echo json_encode(['msg'=>'订单金额超出可用金额','code'=>1103,'success'=>false]);
            exit;  
        }
        $order_arr['order_no'] = date('YmdHis').$order_data['user_id'].'FB'.mt_rand(1000,9999); // 订单编号
        $order_arr['user_id'] = $yh['id']; // 用户ID
        $order_arr['add_time'] = date('Y-m-d H:i:s'); // 下单时间
        $order_arr['multiple'] = $order_data['multiple']; // 倍数
        $order_arr['chuan'] = $order_data['chuan']; // 串法集合
        $order_arr['is_win'] = 0; // 是否中奖 (0.未开奖|1.中奖|2.未中奖)
        $order_arr['order_type'] = 3; // 订单类型 (1.普通订单,2.合买,3.跟单)
        $order_arr['game_cate'] = $order['game_cate']; // 游戏类型 1.足球 , 2.篮球
        $order_arr['order_status'] = 1; // 付款状态 (0.已作废|1.已付款|2.未付款)
        $order_arr['tz_num'] = $tz_num; // 注数
        $order_arr['order_money'] = $order_money; // 订单金额
        $order_id = db('order')->insert($order_arr,false,true);

        if($order['game_cate'] == 1) $db_name = 'fb_game';
        if($order['game_cate'] == 2) $db_name = 'nba_game';
        if($order['game_cate'] >= 3) $db_name = 'fbo_game';
        
        if($order['game_cate'] <= 2){
            foreach ($touz as $key => $value) {
                $order_info[$key]['order_id'] = $order_id; // 订单ID
                $order_info[$key]['game_id'] = $touz[$key]['game_id']; // 比赛场次
                $order_info[$key]['dan'] = $touz[$key]['dan']; // 胆(0:未选中|1.选中)
                $order_info[$key]['tz_result'] = is_array($touz[$key]['tz_result'])?implode(',', $touz[$key]['tz_result']):$touz[$key]['tz_result']; // 投注内容

                $touz[$key]['tz_result'] = is_array($touz[$key]['tz_result'])?$touz[$key]['tz_result']:explode(',', $touz[$key]['tz_result']);
                foreach ($touz[$key]['tz_result'] as $ke => $val) {
                    $order_info[$key]['tz_odds'][$ke] = db($db_name.'_cate')->where('cate_id='.$touz[$key]['tz_result'][$ke])->value('cate_odds');
                }    
                $order_info[$key]['tz_odds'] = implode(',', $order_info[$key]['tz_odds']);
                
                $order_info[$key]['game_status'] = 0; // 游戏状态(0:未结束|1:已结算)
                $order_info[$key]['add_time'] = date('Y-m-d H:i:s'); // 下单时间
                $order_info[$key]['game_cate'] = $order['game_cate']; // 比赛类型
                $game_id[] = $touz[$key]['game_id'];
            }
            $end_time = db($db_name)->where('id in ('.implode(",", $game_id).')')->column('`end_time`');
            foreach ($end_time as $key => $value) {
                $end_time[$key] = strtotime($end_time[$key]);
            }
        }else{
            foreach ($touz as $key => $value) {
                $order_info[$key]['order_id'] = $order_id; // 订单ID
                $order_info[$key]['game_id'] = $touz[$key]['game_id']; // 比赛场次
                $order_info[$key]['dan'] = $touz[$key]['dan']; // 胆(0:未选中|1.选中)
                $order_info[$key]['tz_result'] = is_array($touz[$key]['tz_result'])?implode(',', $touz[$key]['tz_result']):$touz[$key]['tz_result']; // 投注内容
                $order_info[$key]['game_status'] = 0; // 游戏状态(0:未结束|1:已结算)
                $order_info[$key]['add_time'] = date('Y-m-d H:i:s'); // 下单时间
                $order_info[$key]['game_cate'] = $order['game_cate']; // 比赛类型
                $game_id[] = $touz[$key]['game_id'];
            }
            $end_time = db($db_name)->where('id in ('.implode(",", $game_id).')')->column('`deadline`');
            foreach ($end_time as $key => $value) {
                $end_time[$key] = strtotime($end_time[$key]);
            }
        }
        $order_info_res = db('order_info')->insertAll($order_info);

        // 获取当前订单中比赛截止时间最近的一场
        if($order['game_cate'] <= 2){
            $end_time = db($db_name)->where('id in ('.implode(",", $game_id).')')->column('`end_time`');
            foreach ($end_time as $key => $value) {
                $end_time[$key] = strtotime($end_time[$key]);
            }
        }else{
            $end_time = db($db_name)->where('id in ('.implode(",", $game_id).')')->column('`deadline`');
            foreach ($end_time as $key => $value) {
                $end_time[$key] = strtotime($end_time[$key]);
            }
        }
        $min_time = date('Y-m-d H:i:s', min($end_time));
        $gd['order_id'] = $order_id; // 订单ID
        $gd['user_id'] = $order_data['user_id']; // 用户ID
        $gd['gd_title'] = $gendan['gd_title']; // 方案标题
        $gd['gd_desc']  = $gendan['gd_desc']; // 方案描述
        $gd['gd_money'] = $gendan['gd_money']; // 方案金额
        $gd['brokerage']   = intval($gendan['brokerage'])/100; // 中奖佣金 (1% ~ 10%)
        $gd['add_time']   = date('Y-m-d H:i:s'); // 下单时间
        $gd['end_time']   = $min_time; // 截止时间
        $gd['gd_status']   = 2; // 默认2 跟单状态:(0.作废|1.进行中|2.已出票)
        $gd['game_cate']   = $order['game_cate']; // 游戏类型
        $gd['order_type']   = 3; // 订单类型:(1.普通订单,2.合买,3.跟单)
        $gd_id = db('order_gd_desc')->insertGetId($gd);

        $gd_user['gd_id'] = $gd_id;
        $gd_user['user_id'] = $yh['id'];
        $gd_user['order_id'] = $order_id;
        $gd_user['pay_num'] = $order['multiple'];
        $gd_user['one_money'] = $gendan['gd_money'];
        $gd_user['pay_money'] = $order_money;
        $gd_user['add_time'] = date('Y-m-d H:i:s');
        $gd_user_id = db('order_gd_user')->insertGetId($gd_user);

        if($order_id && $order_info_res > 0 && $gd_id && $gd_user_id) {
            if($order_money >= $yh['no_balance']){
                db('user')->where('id='.$yh['id'])->setDec('no_balance',$yh['no_balance']);
                $residue = $order_money - $yh['no_balance'];
                db('user')->where('id='.$yh['id'])->setDec('balance',$residue);
            }else{
                db('user')->where('id='.$yh['id'])->setDec('no_balance',$order_money);
            }
            db('user')->where('id='.$yh['id'])->setDec('amount_money',$order_money);
            $balance = db('user')->where('id='.$yh['id'])->find();
            if($balance['amount_money'] < 0){
                // 回滚事务
                Db::rollback();
                echo json_encode(['msg'=>'投注金额超出可用金额','code'=>1103,'success'=>false]);
                exit; 
            }
            /**添加账单明细**/
            $detail['user_id'] = $yh['id'];
            $detail['deal_cate'] = 3;
            $detail['deal_money'] = $order_money;
            $detail['new_money'] = $balance['amount_money'];
            $detail['add_time'] = date('Y-m-d H:i:s');
            $detail['game_id'] = $order['game_cate'];
            $detail_res = db('account_details')->insert($detail,false,true);
            /**添加账单明细end**/

            // 提交事务
            Db::commit();

            echo json_encode(['msg'=>'发起跟单成功','code'=>1,'success'=>true]);
            exit;
        }else{
            // 回滚事务
            Db::rollback();

            echo json_encode(['msg'=>'发起跟单失败','code'=>3001,'success'=>false]);
            exit;
        }
    }

    // 参与跟单
    /*
	* user_id      用户ID
	* order_id     跟单订单ID
	* gd_id        跟单ID
	* pay_num      购买份数
    * one_money    每份金额
	* pay_money    总金额
    */
    public function join_gendan()
    {
    	$_data = $_REQUEST;
    	// 开启事务
        Db::startTrans();

    	$order = db('order')->where('order_id='.$_data['order_id'])->find();
        $gd_id = db('order_gd_desc')->where('order_id='.$_data['order_id'])->value('gd_id');
        $o_gd_user = db('order_gd_user')->where('gd_id='.$gd_id)->column('user_id');
        if(in_array($_data['user_id'], $o_gd_user)){
            echo json_encode(['msg'=>'此跟单已参与','code'=>4000,'success'=>false]);
            exit;
        }
    	$order_gd_desc = db('order_gd_desc')->where('gd_id='.$_data['gd_id'])->find();
        if($order_gd_desc['end_time'] < date('Y-m-d H:i:s')){
            echo json_encode(['msg'=>'该跟单已过期','code'=>4004,'success'=>false]);
            exit;
        }

    	$user = db('user')->where('id='.$_data['user_id'])->find();
        $order_money = $_data['pay_money'];
        if($order_money > $user['amount_money']){
            echo json_encode(['msg'=>'投注金额超出可用金额','code'=>1103,'success'=>false]);
            exit;
        }

    	// 将user_id 追加到订单里面
    	// $res = Db::execute('update `order` set user_id = concat(user_id,"'.','.$_data['user_id'].'") where order_id='.$_data['order_id']);
        // 添加订单
        if($order['game_cate'] == 1){
            $game_cate = 'fb_game';
            $game_c = 'FB';
        }else{
            $game_cate = 'nba_game';
            $game_c = 'NBA';
        }
        $order_info = db('order_info')->where('order_id='.$order['order_id'])->select();

        unset($order['order_id']);
        unset($order['order_no']);
        unset($order['user_id']);
        unset($order['order_money']);
        unset($order['add_time']);
        $order_data = $order;
        $order_data['order_no'] = date('YmdHis').$_data['user_id'].$game_c.mt_rand(1000,9999);
        $order_data['user_id'] = $_data['user_id'];
        $order_data['order_money'] = $_data['pay_money'];
        $order_data['add_time'] = date('Y-m-d H:i:s');
        $order_id = db('order')->insertGetId($order_data);

        foreach ($order_info as $key => $value) {
            unset($order_info[$key]['order_info_id']);
            $order_info[$key]['order_id'] = $order_id;
        }
        $order_info_res = db('order_info')->insertAll($order_info);

    	// 在跟单订单的跟单列表里面插入一条参与记录
    	$order_gd_user['gd_id'] = $_data['gd_id'];
    	$order_gd_user['user_id'] = $_data['user_id'];
    	$order_gd_user['order_id'] = $order_id;
        $order_gd_user['pay_num'] = $_data['pay_num'];
    	$order_gd_user['one_money'] = $_data['one_money'];
    	$order_gd_user['pay_money'] = $_data['pay_money'];
    	$order_gd_user['add_time'] = date('Y-m-d H:i:s');
    	$order_gd_user_res = db('order_gd_user')->insertGetId($order_gd_user);

        $yh = db('user')->where('id='.$user['id'])->find();
    	// 添加明细
    	if($order_gd_user['pay_money'] >= $yh['no_balance']){
            db('user')->where('id='.$yh['id'])->setField('no_balance',$yh['no_balance']);
            $residue = $order_gd_user['pay_money'] - $yh['no_balance'];
            db('user')->where('id='.$yh['id'])->setDec('balance',$residue);
        }else{
            db('user')->where('id='.$yh['id'])->setDec('no_balance',$order_gd_user['pay_money']);
        }
        db('user')->where('id='.$yh['id'])->setDec('amount_money',$order_gd_user['pay_money']);
        $balance = db('user')->where('id='.$yh['id'])->find();
        if($balance['amount_money'] < 0){
            // 回滚事务
            Db::rollback();
            echo json_encode(['msg'=>'投注金额超出可用金额','code'=>1103,'success'=>false]);
            exit; 
        }
        /**添加账单明细**/
        $detail['user_id'] = $user['id'];
        $detail['deal_cate'] = 3;
        $detail['deal_money'] = $order_gd_user['pay_money'];
        $detail['new_money'] = $balance['amount_money'];
        $detail['add_time'] = date('Y-m-d H:i:s');
        $detail['game_id'] = $order['game_cate'];
        $detail_ress = db('account_details')->insert($detail,false,true);
        /**添加账单明细end**/

        if($order_id && $order_info_res && $order_gd_user_res && $detail_ress){
        	// 提交事务
            Db::commit();

            echo json_encode(['msg'=>'参与跟单成功','code'=>1,'success'=>true]);
            exit;
    	}else{
            // 回滚事务
            Db::rollback();

            echo json_encode(['msg'=>'参与跟单失败','code'=>4101,'success'=>false]);
            exit;
        }
    }

    // 跟单列表
    /*
    *$type=1  发单金额
    *$type=2  胜率
    *$type=3  发单时间
    */
    public function gendan_list($type=1, $page=1, $count=10)
    {
        $start = ($page-1)*$count;

        if($type == 1){
            $list = db('order_gd_desc')where('gd_status=2 and end_time>="'.date('Y-m-d H:i:s').'"')->order('gd_money','desc')->limit($start, $count)->select();
        }else if($type == 2){
            $list = db('order_gd_desc')where('gd_status=2 and end_time>="'.date('Y-m-d H:i:s').'"')->order('add_time','desc')->limit($start, $count)->select();
        }else if($type == 3){
            $list = db('order_gd_desc')where('gd_status=2 and end_time>="'.date('Y-m-d H:i:s').'"')->order('add_time','desc')->limit($start, $count)->select();
        }
        
        $data = array();
        if(!empty($list)){
            foreach ($list as $key => $value) {
                $order = db('order')->where('order_id='.$list[$key]['order_id'])->find();
                $order_gd_user = db('order_gd_user')->where('gd_id='.$list[$key]['gd_id'].' and user_id='.$list[$key]['user_id'])->find();
                $data[$key]['user_id'] = $list[$key]['user_id'];
                $data[$key]['order_id'] = $list[$key]['order_id'];
                $data[$key]['gd_id'] = $list[$key]['gd_id'];
                $data[$key]['head_img'] = db('user')->where('id='.$list[$key]['user_id'])->value('head_img');
                $data[$key]['user_name'] = db('user')->where('id='.$list[$key]['user_id'])->value('user_name');
                if(empty($data[$key]['user_name'])){
                    $data[$key]['user_name'] = db('user')->where('id='.$list[$key]['user_id'])->value('phone');
                }
                $data[$key]['gd_status'] = $list[$key]['gd_status'];
                $data[$key]['end_time'] = $list[$key]['end_time'];
                $data[$key]['gd_title'] = $list[$key]['gd_title'];
                $data[$key]['game_cate'] = $list[$key]['game_cate']==1?'竞彩足球':'竞彩篮球';
                $data[$key]['pay_money'] = $order_gd_user['pay_money'];
                $data[$key]['pay_num'] = $order_gd_user['pay_num'];
                $data[$key]['one_money'] = $list[$key]['gd_money'];
                if($type == 2){
                    $total_count = db('order')->where('order_type=3 and order_status=1 and is_win>0 and user_id='.$list[$key]['user_id'])->count();
                    $win_count = db('order')->where('order_type=3 and order_status=1 and is_win=1 and user_id='.$list[$key]['user_id'])->count();
                    $win_rate = round($win_count/$total_count, 2);
                    $data[$key]['win_rate'] = $win_rate;
                }
            }
            
            if($type == 2){
                $sort = array();
                foreach($data as $value){
                    $sort[] = $value["win_rate"];
                }
                array_multisort($sort,SORT_DESC,$data);
            }
        }

        echo json_encode(['msg'=>'请求成功','code'=>1,'success'=>true,'data'=>$data]);
        exit;
    }

    // 跟单详情
    public function gendan_desc($gd_id)
    {
        $order_gd_desc = db('order_gd_desc')->where('gd_id='.$gd_id)->find();
        $order_gd_user = db('order_gd_user')->where('gd_id='.$order_gd_desc['gd_id'].' and user_id='.$order_gd_desc['user_id'])->find();
        $data['gd_id'] = $order_gd_desc['gd_id'];
        $data['order_id'] = $order_gd_desc['order_id'];
        $data['pay_money'] = $order_gd_user['pay_money']; // 自购金额
        $data['pay_num'] = $order_gd_user['pay_num'];
        $data['one_money'] = $order_gd_desc['gd_money']; // 每份价格
        $data['game_cate'] = $order_gd_desc['game_cate']==1?'竞彩足球':'竞彩篮球'; // 比赛分类
        $data['brokerage'] = ($order_gd_desc['brokerage']*100).'%'; // 佣金
        $data['gd_status'] = $order_gd_desc['gd_status']; // 跟单状态:(0.作废|1.进行中|2.已出票)
        $order = db('order')->where('order_id='.$order_gd_desc['order_id'])->find();
        $data['add_time'] = $order['add_time']; // 下单时间
        $data['order_no'] = $order['order_no']; // 订单编号
        $data['gd_title'] = $order_gd_desc['gd_title']; // 方案标题
        $data['gd_desc'] = $order_gd_desc['gd_desc']; // 方案描述

        echo json_encode(['msg'=>'请求成功','code'=>1,'success'=>true,'data'=>$data]);
        exit;
    }


    // 跟单人员列表
    public function gendan_user_list($gd_id)
    {
        $list = db('order_gd_user')->where('gd_id='.$gd_id)->select();
        foreach ($list as $key => $value) {
            $list[$key]['user_name'] = db('user')->where('id='.$list[$key]['user_id'])->value('user_name');
        }

        echo json_encode(['msg'=>'请求成功','code'=>1,'success'=>true,'data'=>$list]);
        exit;
    }

    // 跟单个人主页
    public function gendan_user()
    {
        $_data = $_REQUEST;
        $list = db('order_gd_desc')->where('gd_status=2 and user_id='.$_data['usr_id'])->order('add_time','desc')->select();

        if(!empty($list)){
            $count = count($list);
            $win_num = 0;
            foreach ($list as $key => $value) {
                $order = db('order')->where('order_id='.$list[$key]['order_id'])->find();
                $order_gd_user = db('order_gd_user')->where('gd_id='.$list[$key]['gd_id'].' and user_id='.$list[$key]['user_id'])->find();
                if($order['is_win'] == 1){
                    $win_num += 1;
                }
                $data[$key]['order_id'] = $list[$key]['order_id'];
                $data[$key]['gd_id'] = $list[$key]['gd_id'];
                $data[$key]['head_img'] = db('user')->where('id='.$_data['usr_id'])->value('head_img');
                $data[$key]['user_name'] = db('user')->where('id='.$_data['usr_id'])->value('user_name');
                if(empty($data[$key]['user_name'])){
                    $data[$key]['user_name'] = db('user')->where('id='.$_data['usr_id'])->value('phone');
                }
                $data[$key]['add_time'] = $list[$key]['add_time'];
                $data[$key]['gd_status'] = $list[$key]['gd_status'];
                $data[$key]['end_time'] = $list[$key]['end_time'];
                $data[$key]['gd_title'] = $list[$key]['gd_title'];
                $data[$key]['game_cate'] = $list[$key]['game_cate']==1?'竞彩足球':'竞彩篮球';
                $data[$key]['pay_money'] = $order_gd_user['pay_money'];
                $data[$key]['pay_num'] = $order_gd_user['pay_num'];
                $data[$key]['one_money'] = $list[$key]['gd_money'];
            }
            $win_odds = round($win_num/$count, 2);
            $dataa['head_img'] = db('user')->where('id='.$_data['usr_id'])->value('head_img');
            $dataa['count'] = $count;
            $dataa['win_num'] = $win_num;
            $dataa['win_odds'] = $win_odds;
            $dataa['list'] = $data;            
        }else{
            $dataa['head_img'] = db('user')->where('id='.$_data['usr_id'])->value('head_img');
            $dataa['count'] = 0;
            $dataa['win_num'] = 0;
            $dataa['win_odds'] = 0;
            $dataa['list'] = [];
        }

        echo json_encode(['msg'=>'请求成功','code'=>1,'success'=>true,'data'=>$dataa]);
        exit;
    }
}
