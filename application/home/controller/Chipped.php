<?php
namespace app\home\controller;
use app\home\model\Group;
use think\Db;
use think\Log;
//合买控制器

class Chipped extends Base
{
    // 发起合买
    /*
    *$_data['order']         订单数据
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
    *$_data['chipped']       合买数据
    *	hm_title 			 方案标题
    *	hm_desc 			 方案描述
    *	hm_money 			 方案金额
    *	hm_num 				 方案总共份数
    *	one_money 			 每份金额
    *	brokerage 			 中奖佣金 (1% ~ 10%)
    *	pay_num 			 发起人所购买的份数 最少30%
    *	bd_num 				 保底份数
    *	add_time 			 下单时间
    */
     function __construct(){
        Log::init([
            'type' =>  'File',
            'path' =>  LOG_PATH,
        ]);
    }
    public function chipped()
    {
        // 获取订单数据 和 合买信息
        $_data = $_REQUEST;


        if(is_string($_data['order'])){
            $order = json_decode($_data['order'],true); // 订单数据
        }else{
            $order = $_data['order']; // 订单数据
        }
        if(is_string($_data['chipped'])){
            $chipped = json_decode($_data['chipped'],true); // 合买数据
        }else{
            $chipped = $_data['chipped']; // 合买数据
        }
        if($chipped['hm_money'] < 10){
            echo json_encode(['msg'=>'方案金额不能小于10','code'=>3103,'success'=>false]);
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
        
        $order_data['user_id'] = $_data['user_id'];
        $order_data['multiple'] = $order['multiple'];
        $order_data['chuan'] = $order['chuan'];
        $order_data['tz'] = json_decode($order['tz'],true);
        // 开启事务
        Db::startTrans();

        $chaun = explode(',', $order_data['chuan']);
        $touz = $order_data['tz'];
        $yh = db('user')->where('id='.$order_data['user_id'])->find();
        $tz_num = $order['tz_num'];
        $order_money = $chipped['one_money']*($chipped['pay_num']+$chipped['bd_num']);

        if($order_money > $yh['amount_money']){
            echo json_encode(['msg'=>'投注金额超出可用金额','code'=>1103,'success'=>false]);
            exit;  
        }
        $order_arr['order_no'] = date('YmdHis').$order_data['user_id'].'FB'.mt_rand(1000,9999); // 订单编号
        $order_arr['user_id'] = $yh['id']; // 用户ID
        $order_arr['add_time'] = date('Y-m-d H:i:s'); // 下单时间
        $order_arr['multiple'] = $order_data['multiple']; // 倍数
        $order_arr['chuan'] = $order_data['chuan']; // 串法集合
        $order_arr['is_win'] = 0; // 是否中奖 (0.未开奖|1.中奖|2.未中奖)
        $order_arr['order_type'] = 2; // 订单类型 (1.普通订单,2.合买)
        $order_arr['game_cate'] = $order['game_cate']; // 游戏分类
        $order_arr['order_status'] = 1; // 付款状态 (0.已作废|1.已付款|2.未付款)
        $order_arr['tz_num'] = $tz_num; // 注数
        $order_arr['order_money'] = $order_money; // 订单金额
        $order_id = db('order')->insert($order_arr,false,true);

        if($order['game_cate'] == 1) $db_name = '`fb_game`';
        if($order['game_cate'] == 2) $db_name = '`nba_game`';
        if($order['game_cate'] >= 3) $db_name = '`fbo_game`';
        
        foreach ($touz as $key => $value) {
            $order_info[$key]['order_id'] = $order_id; // 订单ID
            $order_info[$key]['game_id'] = $touz[$key]['game_id']; // 比赛场次
            $order_info[$key]['dan'] = $touz[$key]['dan']; // 胆(0:未选中|1.选中)
            $order_info[$key]['tz_result'] = is_array($touz[$key]['tz_result'])?implode(',', $touz[$key]['tz_result']):$touz[$key]['tz_result']; // 投注内容
            if($order['game_cate'] <= 2){
                $touz[$key]['tz_result'] = is_array($touz[$key]['tz_result'])?$touz[$key]['tz_result']:explode(',', $touz[$key]['tz_result']);
                foreach ($touz[$key]['tz_result'] as $ke => $val) {
                    $order_info[$key]['tz_odds'][$ke] = db($db_name.'_cate')->where('cate_id='.$touz[$key]['tz_result'][$ke])->value('cate_odds');
                }    
                $order_info[$key]['tz_odds'] = implode(',', $order_info[$key]['tz_odds']);
            }
            
            $order_info[$key]['game_status'] = 0; // 游戏状态(0:未结束|1:已结算)
            $order_info[$key]['add_time'] = date('Y-m-d H:i:s'); // 下单时间
            $order_info[$key]['game_cate'] = $order['game_cate']; // 比赛类型
            $game_id[] = $touz[$key]['game_id'];
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
        $hm['order_id'] = $order_id; // 方案标题
        $hm['user_id'] = $order_data['user_id']; // 方案标题
        $hm['hm_title'] = $chipped['hm_title']; // 方案标题
        $hm['hm_desc']  = $chipped['hm_desc']; // 方案描述
        $hm['hm_money'] = $chipped['hm_money']; // 方案金额
        $hm['hm_num']   = $chipped['hm_num']; // 方案总共份数
        $hm['one_money']   = $chipped['one_money']; // 每份金额
        $hm['brokerage']   = intval($chipped['brokerage'])/100; // 中奖佣金 (1% ~ 10%)
        $hm['pay_num']   = $chipped['pay_num']; // 发起人所购买的份数 最少30%
        $hm['bd_num']   = $chipped['bd_num']; // 保底份数
        $hm['residue_num']   = $chipped['hm_num'] - $chipped['pay_num']; // 剩余可购买份数
        $hm['add_time']   = date('Y-m-d H:i:s'); // 下单时间
        $hm['end_time']   = $min_time; // 截止时间
        $hm['hm_status']   = 1; // 默认0 合买状态:(0.作废|1.进行中|2.已出票)
        $hm['game_cate']   = $order['game_cate']; // 订单类型 (1.普通订单,2.合买)
        $hm_id = db('order_hm_desc')->insertGetId($hm);

        $hm_user['hm_id'] = $hm_id;
        $hm_user['user_id'] = $yh['id'];
        $hm_user['order_id'] = $order_id;
        $hm_user['pay_num'] = $chipped['pay_num'];
        $hm_user['pay_money'] = $chipped['one_money']*$chipped['pay_num'];
        $hm_user['add_time'] = date('Y-m-d H:i:s');
        $hm_user_id = db('order_hm_user')->insertGetId($hm_user);

        if($order_id && $order_info_res > 0 && $hm_id && $hm_user_id) {
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

            echo json_encode(['msg'=>'发起合买成功','code'=>1,'success'=>true,'order_id'=>$order_id]);
            exit;
        }else{
            // 回滚事务
            Db::rollback();

            echo json_encode(['msg'=>'发起合买失败','code'=>3001,'success'=>false]);
            exit;
        }
    }

    // 参与合买
    /*
	* user_id      用户ID
	* order_id     合买订单ID
	* hm_id        合买ID
	* pay_num      购买份数
	* one_money    每份金额
    */
    public function join_chipped()
    {
    	$_data = $_REQUEST;
    	// 开启事务
        Db::startTrans();

    	$order = db('order')->where('order_id='.$_data['order_id'])->find();
        $hm_id = db('order_hm_desc')->where('order_id='.$_data['order_id'])->value('hm_id');
        $o_hm_user = db('order_hm_user')->where('hm_id='.$hm_id)->column('user_id');
        if(in_array($_data['user_id'], $o_hm_user)){
            echo json_encode(['msg'=>'此合买已参与','code'=>4000,'success'=>false]);
            exit;
        }
    	$order_hm_desc = db('order_hm_desc')->where('hm_id='.$_data['hm_id'])->find();
        if($order_hm_desc['end_time'] < date('Y-m-d H:i:s')){
            echo json_encode(['msg'=>'该合买已过期','code'=>4004,'success'=>false]);
            exit;
        }
    	// 判断购买份数  >  剩余可购买份数
    	if($_data['pay_num'] > $order_hm_desc['residue_num']){
    		echo json_encode(['msg'=>'超出剩余购买份数','code'=>4001,'success'=>false]);
            exit;
    	}
    	// 如果不大于 代表可以购买 直接减掉 剩余可购买份数
    	db('order_hm_desc')->where('hm_id='.$_data['hm_id'])->setDec('residue_num',$_data['pay_num']);
    	// 判断如果 剩余可购买份数 为 0 改变合买状态 hm_status  (0.未完成|1.进行中|2.已出票)
    	$residue_num = db('order_hm_desc')->where('hm_id='.$_data['hm_id'])->value('residue_num');
    	$user = db('user')->where('id='.$_data['user_id'])->find();
        $order_money = $_data['pay_num'] * $order_hm_desc['one_money'];
        if($order_money > $user['amount_money']){
            echo json_encode(['msg'=>'投注金额超出可用金额','code'=>1103,'success'=>false]);
            exit;
        }
    	if($residue_num == 0){
    		db('order_hm_desc')->where('hm_id='.$_data['hm_id'])->setField('hm_status',2);
    		// 如果合买任务完成了 将发起人的保底金额退还  deal_cate 交易类型，1.充值|2.提现|3.投注|4.中奖金额|5.奖励金|6.彩金退还
            if($order_hm_desc['bd_num'] > 0){
                $yh = db('user')->where('id='.$order_hm_desc['user_id'])->find();
                $money = $order_hm_desc['one_money']*$order_hm_desc['bd_num'];
                db('user')->where('id='.$yh['id'])->setInc('no_balance',$money);
                db('user')->where('id='.$yh['id'])->setInc('amount_money',$money);
                /**添加账单明细**/
                $detail['user_id'] = $yh['id'];
                $detail['deal_cate'] = 6;
                $detail['deal_money'] = $order_hm_desc['one_money']*$order_hm_desc['bd_num'];
                $detail['new_money'] = $yh['amount_money'];
                $detail['add_time'] = date('Y-m-d H:i:s');
                $detail['game_id'] = $order['game_cate'];
                $detail_res = db('account_details')->insert($detail,false,true);    
            }
	        /**添加账单明细end**/
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
        $order_data['order_money'] = $order_hm_desc['one_money']*$_data['pay_num'];
        $order_data['add_time'] = date('Y-m-d H:i:s');
        $order_id = db('order')->insertGetId($order_data);

        foreach ($order_info as $key => $value) {
            unset($order_info[$key]['order_info_id']);
            $order_info[$key]['order_id'] = $order_id;
        }
        $order_info_res = db('order_info')->insertAll($order_info);

    	// 在合买订单的合买列表里面插入一条参与记录
    	$order_hm_user['hm_id'] = $_data['hm_id'];
    	$order_hm_user['user_id'] = $_data['user_id'];
    	$order_hm_user['order_id'] = $order_id;
    	$order_hm_user['pay_num'] = $_data['pay_num'];
    	$order_hm_user['pay_money'] = $order_hm_desc['one_money']*$_data['pay_num'];
    	$order_hm_user['add_time'] = date('Y-m-d H:i:s');
    	$order_hm_user_res = db('order_hm_user')->insertGetId($order_hm_user);

        $yh = db('user')->where('id='.$user['id'])->find();
    	// 添加明细
    	if($order_hm_user['pay_money'] >= $yh['no_balance']){
            db('user')->where('id='.$yh['id'])->setField('no_balance',$yh['no_balance']);
            $residue = $order_hm_user['pay_money'] - $yh['no_balance'];
            db('user')->where('id='.$yh['id'])->setDec('balance',$residue);
        }else{
            db('user')->where('id='.$yh['id'])->setDec('no_balance',$order_hm_user['pay_money']);
        }
        db('user')->where('id='.$yh['id'])->setDec('amount_money',$order_hm_user['pay_money']);
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
        $detail['deal_money'] = $order_hm_user['pay_money'];
        $detail['new_money'] = $balance['amount_money'];
        $detail['add_time'] = date('Y-m-d H:i:s');
        $detail['game_id'] = $order['game_cate'];
        $detail_ress = db('account_details')->insert($detail,false,true);
        /**添加账单明细end**/

        if($order_id && $order_info_res && $order_hm_user_res && $detail_ress){
        	// 提交事务
            Db::commit();

            echo json_encode(['msg'=>'参与合买成功','code'=>1,'success'=>true]);
            exit;
    	}else{
            // 回滚事务
            Db::rollback();

            echo json_encode(['msg'=>'参与合买失败','code'=>4101,'success'=>false]);
            exit;
        }
    }

    // 合买列表
    public function chipped_list($page=1, $count=10)
    {
        $start = ($page-1)*$count;
        $list = db('order_hm_desc')->where('residue_num>0 and hm_status=1 and end_time>="'.date('Y-m-d H:i:s').'"')->limit($start, $count)->select();
        $data = array();

        if(!empty($list)){
            foreach ($list as $key => $value) {
                $data[$key]['user_id'] = $list[$key]['user_id'];
                $data[$key]['order_id'] = $list[$key]['order_id'];
                $data[$key]['hm_id'] = $list[$key]['hm_id'];
                $data[$key]['head_img'] = db('user')->where('id='.$list[$key]['user_id'])->value('head_img');
                $data[$key]['user_name'] = db('user')->where('id='.$list[$key]['user_id'])->value('user_name');
                if(empty($data[$key]['user_name'])){
                    $data[$key]['user_name'] = db('user')->where('id='.$list[$key]['user_id'])->value('phone');
                }
                $data[$key]['end_time'] = $list[$key]['end_time'];
                $data[$key]['hm_title'] = $list[$key]['hm_title'];
                $data[$key]['game_cate'] = $list[$key]['game_cate']==1?'竞彩足球':'竞彩篮球';
                $data[$key]['self_money'] = $list[$key]['pay_num'] * $list[$key]['one_money']; // 自购金额
                $data[$key]['residue_num'] = $list[$key]['residue_num'];
                $data[$key]['one_money'] = $list[$key]['one_money'];
            }    
        }
        
        echo json_encode(['msg'=>'请求成功','code'=>1,'success'=>true,'data'=>$data]);
        exit;
    }

    // 合买详情
    public function chipped_desc($hm_id)
    {
        $order_hm_desc = db('order_hm_desc')->where('hm_id='.$hm_id)->find();
        $data['hm_id'] = $order_hm_desc['hm_id'];
        $data['order_id'] = $order_hm_desc['order_id'];
        $data['self_money'] = $order_hm_desc['pay_num'] * $order_hm_desc['one_money']; // 自购金额
        $data['one_money'] = $order_hm_desc['one_money']; // 每份价格
        $data['game_cate'] = $order_hm_desc['game_cate']==1?'竞彩足球':'竞彩篮球'; // 比赛分类
        $data['residue_num'] = $order_hm_desc['residue_num']; // 剩余可购买金额
        $data['hm_money'] = $order_hm_desc['hm_money']; // 方案总额
        $data['brokerage'] = ($order_hm_desc['brokerage']*100).'%'; // 佣金
        $data['hm_status'] = $order_hm_desc['hm_status']; // 合买状态:(0.作废|1.进行中|2.已出票)
        $order = db('order')->where('order_id='.$order_hm_desc['order_id'])->find();
        $data['add_time'] = $order['add_time']; // 下单时间
        $data['order_no'] = $order['order_no']; // 订单编号
        $data['hm_title'] = $order_hm_desc['hm_title']; // 方案标题
        $data['hm_desc'] = $order_hm_desc['hm_desc']; // 方案描述

        echo json_encode(['msg'=>'请求成功','code'=>1,'success'=>true,'data'=>$data]);
        exit;
    }


    // 合买人员列表
    public function chipped_user_list($hm_id)
    {
        $list = db('order_hm_user')->where('hm_id='.$hm_id)->select();
        foreach ($list as $key => $value) {
            $list[$key]['user_name'] = db('user')->where('id='.$list[$key]['user_id'])->value('user_name');
        }

        echo json_encode(['msg'=>'请求成功','code'=>1,'success'=>true,'data'=>$list]);
        exit;
    }

    // 合买个人主页
    public function chipped_user()
    {
        $_data = $_REQUEST;
        $list = db('order_hm_desc')->where('user_id='.$_data['usr_id'])->order('add_time','desc')->select();

        if(!empty($list)){
            $count = count($list);
            $win_num = 0;
            foreach ($list as $key => $value) {
                $order = db('order')->where('order_id='.$list[$key]['order_id'])->find();
                if($order['is_win'] == 1){
                    $win_num += 1;
                }
                $data[$key]['order_id'] = $list[$key]['order_id'];
                $data[$key]['hm_id'] = $list[$key]['hm_id'];
                $data[$key]['head_img'] = db('user')->where('id='.$_data['usr_id'])->value('head_img');
                $data[$key]['user_name'] = db('user')->where('id='.$_data['usr_id'])->value('user_name');
                if(empty($data[$key]['user_name'])){
                    $data[$key]['user_name'] = db('user')->where('id='.$_data['usr_id'])->value('phone');
                }
                $data[$key]['add_time'] = $list[$key]['add_time'];
                $data[$key]['hm_status'] = $list[$key]['hm_status'];
                $data[$key]['end_time'] = $list[$key]['end_time'];
                $data[$key]['hm_title'] = $list[$key]['hm_title'];
                $data[$key]['game_cate'] = $list[$key]['game_cate']==1?'竞彩足球':'竞彩篮球';
                $data[$key]['self_money'] = $list[$key]['pay_num'] * $list[$key]['one_money']; // 自购金额
                $data[$key]['residue_num'] = $list[$key]['residue_num'];
                $data[$key]['one_money'] = $list[$key]['one_money'];
                $data[$key]['win_money'] = $order['win_money'];
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
