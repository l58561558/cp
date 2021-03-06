<?php
namespace app\home\controller;
use app\home\model\Group;
use think\Db;
//订单控制器
class Order extends Base
{
    /*
    * user_id     (int)       用户ID
    * game_cate   (int)       游戏类型 1.足球 , 2.篮球
    * multiple    (int)       倍数
    * chuan       (int)       串法
    * tz          (array多维数组)     投注内容
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
    .       .       .       .       .       .
    .       .       .       .       .       .
    .       .       .       .       .       .
    */
    public function add_order()
    {
        $_data = $_REQUEST;
        // $file_name = 'D:\phpStudy\WWW\cp\application\home\controller\test.php';
        // $tz_data = json_decode($_data['tz'],true);
        // $text = '<?php $rows='.var_export($_data['bigArr'],true).';';
        // if(false!==fopen($file_name,'w+')){ 
            // file_put_contents($file_name,var_export($tz_data, true));//写入缓存 
            // file_put_contents($file_name,var_export($_data['tz'], true));//写入缓存 
        // }
        // die;
        $data['game_cate'] = $_data['game_cate']; // 游戏类型
        $data['user_id'] = $_data['user_id'];
        $data['multiple'] = $_data['multiple'];
        $data['chuan'] = $_data['chuan'];
        $data['tz'] = json_decode($_data['tz'],true);

        // 开启事务
        Db::startTrans();

        if(empty($data['chuan'])){
            echo json_encode(['msg'=>'请选择串法','code'=>1101,'success'=>false]);
            exit;
        }

        $touz = $data['tz'];

        $yh = db('user')->where('id='.$data['user_id'])->find();
        
        $game_cate = $data['game_cate']==1?'fb_game':'nba_game';
        if($data['game_cate'] == 1){
            $game_cate = 'fb_game';
            $game_c = 'FB';
        }else{
            $game_cate = 'nba_game';
            $game_c = 'NBA';
        }
        foreach ($touz as $ke => $val) {
            $game = db($game_cate)->where('id='.$touz[$ke]['game_id'])->find();
            if($game['end_time'] <= date('Y-m-d H:i:s')){
                echo json_encode(['msg'=>'投注失败,该场比赛已停止投注','code'=>1102,'success'=>false]);
                exit;
            }
        }

        /*****获取注数*****/
        $Group = new Group();
        $tz_num = $Group->tz_num($data);
        /*****获取注数*****/

        $order_money = $tz_num*2;

        if($order_money > $yh['balance']+$yh['no_balance']){
            echo json_encode(['msg'=>'投注金额超出可用金额','code'=>1103,'success'=>false]);
            exit;  
        }

        $order['order_no'] = date('YmdHis').$data['user_id'].$game_c.mt_rand(1000,9999);
        $order['user_id'] = $yh['id'];
        $order['add_time'] = date('Y-m-d H:i:s');
        $order['multiple'] = $data['multiple'];
        $order['chuan'] = $data['chuan'];
        $order['is_win'] = 0;
        $order['order_type'] = 1;
        $order['game_cate'] = $data['game_cate'];
        $order['order_status'] = 1;
        $order['tz_num'] = $tz_num;
        $order['order_money'] = $order_money;
        $order_id = db('order')->insert($order,false,true);

        foreach ($touz as $key => $value) {
            $order_info[$key]['order_id'] = $order_id;
            $order_info[$key]['game_id'] = $touz[$key]['game_id'];
            $order_info[$key]['dan'] = $touz[$key]['dan'];
            $order_info[$key]['tz_result'] = is_array($touz[$key]['tz_result'])?implode(',', $touz[$key]['tz_result']):$touz[$key]['tz_result'];
            $order_info[$key]['game_status'] = 0;
            $order_info[$key]['add_time'] = date('Y-m-d H:i:s');
            $order_info[$key]['game_cate'] = $data['game_cate'];
        }
        $order_info_res = db('order_info')->insertAll($order_info);

        if($order_id && $order_info_res > 0) {
            if($order_money >= $yh['no_balance']){
                db('user')->where('id='.$yh['id'])->setField('no_balance',0);
                $residue = $order_money - $yh['no_balance'];
                db('user')->where('id='.$yh['id'])->setDec('balance',$residue);
            }else{
                db('user')->where('id='.$yh['id'])->setDec('no_balance',$order_money);
            }
            db('user')->where('id='.$yh['id'])->setDec('amount_money',$order_money);
            $balance = db('user')->where('id='.$yh['id'])->find();
            /**添加账单明细**/
            $detail['user_id'] = $yh['id'];
            $detail['deal_cate'] = 3;
            $detail['deal_money'] = $order_money;
            $detail['new_money'] = $balance['amount_money'];
            $detail['add_time'] = date('Y-m-d H:i:s');
            $detail['game_id'] = $data['game_cate'];
            $detail_res = db('account_details')->insert($detail,false,true);
            /**添加账单明细end**/

            // 提交事务
            Db::commit();

            echo json_encode(['msg'=>'投注成功','code'=>1,'success'=>true,'order_id'=>$order_id]);
            exit;
        }else{
            // 回滚事务
            Db::rollback();

            echo json_encode(['msg'=>'投注失败','code'=>1104,'success'=>false]);
            exit;
        }

    }

    // 获取注数和订单金额
    public function get_tz_num()
    {
        $_data = $_REQUEST;
        $Group = new Group();
        $data['multiple'] = $_data['multiple'];
        $data['chuan'] = $_data['chuan'];
        $data['tz'] = json_decode($_data['tz'],true);
        $order_tz_data = $Group->get_group($data['chuan'],$data['tz']);
        $order_total_odds = 0;
        foreach ($order_tz_data as $key => $value) {
            $order_tz_odds = 1;
            $order_tz = explode(',', $order_tz_data[$key]);
            foreach ($order_tz as $ke => $val) {
                $order_tz_odds *= $order_tz[$ke];
            }
            $order_total_odds += $order_tz_odds;
        }
        /*****获取注数*****/
        // $Group = new Group();
        $tz_num = $Group->tz_num($data);
        /*****获取注数*****/

        $order_money = $tz_num*2;
        $order_win_money = round($order_total_odds,2)*$data['multiple']*2;

        echo json_encode(['msg'=>'请求成功','code'=>1,'success'=>true,'data'=>['tz_num'=>$tz_num,'money'=>$order_money,'order_win_money'=>(string)$order_win_money]]);
        exit;
    }

    // 订单列表
    /*
    *$user_id   int   用户ID
    *$type      int   筛选类型(0:全部|1:中奖|2:未中奖|3:合买)
    */
    public function order($user_id=1, $type=0, $page=1, $count=10)
    {
        $start = ($page-1)*$count;
        if($type == 0) $where = '';
        if($type == 1) $where = 'user_id='.$user_id.' and order_status=1 and is_win=1';
        if($type == 2) $where = 'user_id='.$user_id.' and order_status=1 and is_win=2';

        $list = db('order')
            ->field('order_id,user_id,order_money,add_time,is_win,game_cate,order_type,order_status')
            ->where($where)
            ->order('add_time desc')
            ->limit($start,$count)
            ->select();

        $data = array();
        $order_type = array('已作废','未付款','待开奖','未中奖','已中奖'); // 订单状态(0:已作废|1:未付款|2:待开奖|3:未中奖|4.已中奖)

        foreach ($list as $key => $value) {
            // if($list[$key]['order_type'] == 2){
            //     $order_hm_desc = db('order_hm_desc ohd')
            //                 ->field('u.user_name,ohd.hm_money,ohd.win_money,ohd.hm_num,ohd.brokerage,ohu.pay_num,ohu.pay_money,ohd.bd_num')
            //                 ->join('order_hm_user ohu','ohd.hm_id=ohu.hm_id')
            //                 ->join('user u','u.id=ohd.user_id')
            //                 ->where('ohu.order_id='.$list[$key]['order_id'].' and ohu.user_id='.$list[$key]['user_id'])
            //                 ->find();
            //     $data[$key]['user_name'] = $order_hm_desc['user_name']; // 发起人昵称
            //     $data[$key]['hm_desc'] = $order_hm_desc['hm_money'];    // 方案总金额
            //     $data[$key]['win_money'] = $order_hm_desc['win_money']; // 方案总中奖
            //     $data[$key]['hm_num'] = $order_hm_desc['hm_num'];       // 方案总份数
            //     $data[$key]['brokerage'] = $order_hm_desc['brokerage']; // 佣金比列
            //     $data[$key]['pay_num'] = $order_hm_desc['pay_num'];     // 我的认购份数
            //     $data[$key]['pay_money'] = $order_hm_desc['pay_money']; // 我的认购金额
            //     $data[$key]['bd_num'] = $order_hm_desc['bd_num'];       // 保底份数
            // }
            $data[$key]['order_id'] = $list[$key]['order_id'];
            $data[$key]['order_money'] = $list[$key]['order_money'];
            $data[$key]['add_time'] = $list[$key]['add_time'];
            $data[$key]['game_cate'] = db('game_cate')->where('game_id='.$list[$key]['game_cate'])->value('game_name');
            $data[$key]['order_type'] = db('code_info')->where('code_pid=11 and code='.$list[$key]['order_type'])->value('code_name');
            
            if($list[$key]['order_status'] == 1){
                $data[$key]['order_state'] = 2;
                if($list[$key]['is_win'] == 1){
                    $data[$key]['order_state'] = 4;
                }else if($list[$key]['is_win'] == 2){
                    $data[$key]['order_state'] = 3;
                }
            }else if($list[$key]['order_status'] == 2){
                $data[$key]['order_state'] = 1;
            }else{
            	$data[$key]['order_state'] = 0;
            }
        }
        echo json_encode(['msg'=>'请求成功','code'=>1,'success'=>true,'data'=>$data]);
        exit;
    }
                // $order_hm_desc = db('order_hm_desc ohd')
                //             ->field('u.user_name,ohd.hm_money,ohd.win_money,ohd.hm_num,ohd.brokerage,ohu.pay_num,ohu.pay_money,ohd.bd_num')
                //             ->join('order_hm_user ohu','ohd.hm_id=ohu.hm_id')
                //             ->join('user u','u.id=ohd.user_id')
                //             ->where('ohu.order_id='.$list[$key]['order_id'].' and ohu.user_id='.$list[$key]['user_id'])
                //             ->find();
    // 订单详情
    public function order_info($order_id)
    {
        $order = db('order')->where('order_id='.$order_id)->find();
        if($order['order_type'] == 1){
	        $order_info = db('order_info')->where('order_id='.$order_id)->select(); // 订单明细
	        $data['order_id'] = $order['order_id']; // 订单ID
	        $data['game_cate'] = db('game_cate')->where('game_id='.$order['game_cate'])->value('game_name'); // 游戏类型
	        $data['order_money'] = $order['order_money']; // 订单金额
	        $data['win_money'] = $order['win_money']; // 中奖金额
	        $data['order_no'] = $order['order_no']; // 订单编号
	        $data['add_time'] = $order['add_time']; // 下单时间
	        $data['game_num'] = count($order_info); // 比赛场次
	        $chuan = explode(',', $order['chuan']);
	        foreach ($chuan as $key => $value) {
	        	$chuann[] = db('chuan')->where('chuan_id='.$chuan[$key])->value('chuan_name');
	        }
	        $data['chuan'] = implode(',', $chuann); // 串法
	        $data['multiple'] = $order['multiple']; // 倍数
	        $data['is_win'] = $order['is_win']; // 是否中奖
	        if($order['order_type'] == 1) $data['order_type'] = '普通订单';
	        if($order['order_type'] == 2) $data['order_type'] = '合买';
	        if($order['order_status'] == 1){
	            $data['order_state'] = 2; // 订单状态(0:已作废|1:未付款|2:待开奖|3:未中奖|4.已中奖)
	            if($order['is_win'] == 1){
	                $data['order_state'] = 4;
	            }else if($order['is_win'] == 2){
	                $data['order_state'] = 3;
	            }
	        }else if($order['order_status'] == 2){
	            $data['order_state'] = 1;
	        }else{
	        	$data['order_state'] = 0;
	        }

	        $let_arr = array('let_score_home_win','let_score_home_eq','let_score_home_lose');
	        $total_arr = array('total_small','total_big');

	        foreach ($order_info as $key => $value) {
	            $game[$key] = db('fb_game')->where('id='.$order_info[$key]['game_id'])->find();
	            $data['order_info'][$key]['week'] = $game[$key]['week']; // 周几
	            $data['order_info'][$key]['game_no'] = $game[$key]['game_no']; // 比赛编号
	            $data['order_info'][$key]['down_score'] = $game[$key]['down_score']; // 最终比分
	            $data['order_info'][$key]['home_team'] = $game[$key]['home_team']; // 主队名称
	            $data['order_info'][$key]['road_team'] = $game[$key]['road_team']; // 客队名称
	            $data['order_info'][$key]['win_result'] = []; // 中奖内容

	            $tz_result = explode(',', $order_info[$key]['tz_result']);

	            if(!empty($order_info[$key]['tz_result'])){
	                foreach ($tz_result as $ke => $val) {
	                    $gc = db('fb_game_cate')->where('cate_id='.$tz_result[$ke])->find();
	                    if(in_array($gc['cate_code'], $let_arr)){
		                    $let_score = db('fb_game')->where('id='.$gc['game_id'])->value('let_score');
		                    $gc['cate_name'] .= '('.$let_score.')';
		                }
		                if(in_array($gc['cate_code'], $total_arr)){
		                    $total_score = db('nba_game')->where('id='.$gc['game_id'])->value('total_score');
		                    $gc['cate_name'] .= '('.$total_score.')';
		                }
	                    $tz_result[$ke] = $gc;
	                }
	                $data['order_info'][$key]['tz_result'] = $tz_result;
	            }

	            if(!empty($order_info[$key]['win_result'])){
	                $win_result = explode(',', $order_info[$key]['win_result']);
	                foreach ($win_result as $ke => $val) {
	                    $gc = db('fb_game_cate')->where('cate_id='.$win_result[$ke])->find();
	                    if(in_array($gc['cate_code'], $let_arr)){
		                    $let_score = db('fb_game')->where('id='.$gc['game_id'])->value('let_score');
		                    $gc['cate_name'] .= '('.$let_score.')';
		                }
		                if(in_array($gc['cate_code'], $total_arr)){
		                    $total_score = db('nba_game')->where('id='.$gc['game_id'])->value('total_score');
		                    $gc['cate_name'] .= '('.$total_score.')';
		                }
	                    $win_result[$ke] = $gc;
	                }
	                $data['order_info'][$key]['win_result'] = $win_result;                      
	            }
	        }        	
        }else{
            $order_hm_desc = db('order_hm_desc ohd')
                        ->field('u.user_name,ohd.hm_money,ohd.win_money,ohd.hm_num,ohd.brokerage,ohu.pay_num,ohu.pay_money,ohd.bd_num')
                        ->join('order_hm_user ohu','ohd.hm_id=ohu.hm_id')
                        ->join('user u','u.id=ohd.user_id')
                        ->where('ohu.order_id='.$order['order_id'].' and ohu.user_id='.$order['user_id'])
                        ->find();
            $data['user_name'] = $order_hm_desc['user_name']; // 发起人昵称
            $data['hm_desc'] = $order_hm_desc['hm_money'];    // 方案总金额
            $data['win_money'] = $order_hm_desc['win_money']; // 方案总中奖
            $data['hm_num'] = $order_hm_desc['hm_num'];       // 方案总份数
            $data['brokerage'] = $order_hm_desc['brokerage']; // 佣金比列
            $data['pay_num'] = $order_hm_desc['pay_num'];     // 我的认购份数
            $data['pay_money'] = $order_hm_desc['pay_money']; // 我的认购金额
            $data['bd_num'] = $order_hm_desc['bd_num'];       // 保底份数
        	$order_hm_desc = db('order_hm_desc')->where('order_id='.$order_id)->find();

	        $data['hm_id'] = $order_hm_desc['hm_id'];
	        $data['order_id'] = $order_hm_desc['order_id'];
	        $data['self_money'] = $order_hm_desc['pay_num'] * $order_hm_desc['one_money']; // 自购金额
	        $data['one_money'] = $order_hm_desc['one_money']; // 每份价格
	        $data['game_cate'] = $order_hm_desc['game_cate']==1?'竞彩足球':'竞彩篮球'; // 比赛分类
	        $data['residue_num'] = $order_hm_desc['residue_num']; // 剩余可购买金额
	        $data['hm_money'] = $order_hm_desc['hm_money']; // 方案总额
	        $data['brokerage'] = $order_hm_desc['brokerage']; // 佣金
	        $data['hm_status'] = $order_hm_desc['hm_status']; // 合买状态:(0.作废|1.进行中|2.已出票)
	        $data['add_time'] = $order['add_time']; // 下单时间
	        $data['order_no'] = $order['order_no']; // 订单编号
	        $data['hm_title'] = $order_hm_desc['hm_title']; // 方案标题
	        $data['hm_desc'] = $order_hm_desc['hm_desc']; // 方案描述
        }


        echo json_encode(['msg'=>'请求成功','code'=>1,'success'=>true,'data'=>$data]);
        exit;
    }    
}
