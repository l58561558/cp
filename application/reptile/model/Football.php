<?php
namespace app\reptile\model;
use Symfony\Component\DomCrawler\Crawler;
use app\adminz\model\Detail;
use app\home\model\Group;
use think\Model;
use think\Log;
use think\Db;
class Football extends Model {
	public function __construct()
    {
        Log::init([
            'type' =>  'File',
            'path' =>  LOG_PATH,
        ]);
    }
    /**
     * 获取赛事结算信息
     * @param  string $gamedate 赛事日期
     * @return array  $game_data 赛事数据
     */
    public function getScore($gamedate)
    {
    	$url = "http://live.zgzcw.com/?date=".$gamedate;

    	$html = getCurl($url);
    	//将html编码替换为utf-8
    	$coding = mb_detect_encoding($html);
    	if ($coding != "UTF-8" || !mb_check_encoding($html, "UTF-8")){$html = mb_convert_encoding($html, 'utf-8', 'GBK,UTF-8,ASCII');}
    	$crawler = new Crawler($html);
    	$table = $crawler->filterXPath("//table[@id='matchTable']/tbody/tr");
        $gamelist = array();
    	foreach ($table as $docElement) {
    		$matchdate = $docElement->getElementsByTagName('td')->item(3)->getAttribute('start-time');
			$status_code = $docElement->getElementsByTagName('td')->item(3)->getAttribute('status');
            $match_status = $docElement->getElementsByTagName('td')->item(4)->textContent;
			if($status_code == -1 && $match_status == "完"){
				$matchno = $docElement->getElementsByTagName('td')->item(0)->textContent;
    			$game['week'] = substr($matchno,0,6);
    			$game['game_no'] = substr($matchno,-3,3);
                $game['home_team'] = $docElement->getElementsByTagName('td')->item(5)->getElementsByTagName('a')->item(0)->textContent;
                $game['road_team'] = $docElement->getElementsByTagName('td')->item(7)->getElementsByTagName('a')->item(0)->textContent;
                $halfscore = $docElement->getElementsByTagName('td')->item(8)->textContent;
                $score = $docElement->getElementsByTagName('td')->item(6)->textContent;
				$game['halfscore'] = str_replace('-',':',$halfscore);
				$game['score'] = str_replace('-',':',$score);
				$gamelist[] = $game;
			}else{
				continue;
			} 
    	}
    	if(!empty($gamelist)){
    		return $gamelist;
    	}else{
    		return null;
    	}
    }

    // 结算
    // 通过比赛ID查其所有的投注选项ID -> 在通过投注选项ID查询所有的订单 -> 结算
    public function fb_over($id)
    {
        set_time_limit(0);
        // 开启事务
        Db::startTrans();

        $Detail = new Detail();

        $game = db('fb_game')->where('id='.$id)->find();
        if($game['is_postpone'] == 1){
            db('fb_game_cate')->where('game_id='.$id)->update(array('is_win'=>1,'cate_odds'=>1));
        }else{
            if(empty($game['top_score']) && empty($game['down_score'])){
                $this->error("请输入分数比!");
            }
            $home_score = explode(':', $game['down_score'])[0];
            $road_score = explode(':', $game['down_score'])[1];
            $top_home_score = explode(':', $game['top_score'])[0];
            $top_road_score = explode(':', $game['top_score'])[1];
            //让分分数
            $home_let_score = $home_score+$game['let_score'];
            //俩队总进球数
            $total_score = $game['total_score'];
            //获取比分所有选项
            $score = db('fb_code')->where('code_pid=3')->column('code_name');
            //根据比赛分数修改本场比赛的所有投注选项的中奖状态
            db('fb_game_cate')->where('game_id='.$id)->setField('is_win',2);
            if($top_home_score > $top_road_score){
                $half_full = 'win_';
            }else if($top_home_score < $top_road_score){
                $half_full = 'lose_';
            }else{
                $half_full = 'eq_';
            }
            if($home_score > $road_score){
                $half_full .= 'win';
                db('fb_game_cate')->where('game_id='.$id.' and cate_code="home_win"')->setField('is_win',1);
            }else if($home_score < $road_score){
                $half_full .= 'lose';
                db('fb_game_cate')->where('game_id='.$id.' and cate_code="home_lose"')->setField('is_win',1);
            }else{
                $half_full .= 'eq';
                db('fb_game_cate')->where('game_id='.$id.' and cate_code="home_eq"')->setField('is_win',1);
            }
            db('fb_game_cate')->where('game_id='.$id.' and cate_code="'.$half_full.'"')->setField('is_win',1);

            if($home_let_score > $road_score){
                db('fb_game_cate')->where('game_id='.$id.' and cate_code="let_score_home_win"')->setField('is_win',1);
            }else if($home_let_score < $road_score){
                db('fb_game_cate')->where('game_id='.$id.' and cate_code="let_score_home_lose"')->setField('is_win',1);
            }else{
                db('fb_game_cate')->where('game_id='.$id.' and cate_code="let_score_home_eq"')->setField('is_win',1);
            }
            if(in_array($game['down_score'], $score)){
                db('fb_game_cate')->where('game_id='.$id.' and cate_name="'.$game['down_score'].'"')->setField('is_win',1);
            }else{
                if($home_score > $road_score){
                    db('fb_game_cate')->where('game_id='.$id.' and cate_code="win_other"')->setField('is_win',1);
                }else if($home_score < $road_score){
                    db('fb_game_cate')->where('game_id='.$id.' and cate_code="lose_other"')->setField('is_win',1);
                }else{
                    db('fb_game_cate')->where('game_id='.$id.' and cate_code="eq_other"')->setField('is_win',1);
                }
            }
            db('fb_game_cate')->where('game_id='.$id.' and cate_code="'.$this->rank($total_score).'"')->setField('is_win',1);
        }
        
        // 获取这场比赛的所有中奖选项ID::cate_ids
        $cate_id_arr = db('fb_game_cate')->where('game_id='.$id.' and is_win=1')->column('cate_id');
        
        $cate_ids = implode(',', $cate_id_arr);
        // 并将所有的中奖ID添加到明细里
        db('order_info oi')
        ->join('order o','oi.order_id=o.order_id')
        ->where('o.order_status=1 and oi.game_cate='.$this->game_cate.' and oi.game_id='.$id.' and oi.game_status=0')
        ->setField('win_game_result',$cate_ids);
        // 查询 已付款 订单明细里所有有关于这场比赛数据 并更改他们的比赛状态和中奖彩果
        $order_info_all = db('order_info oi')
        ->join('order o','oi.order_id=o.order_id')
        ->where('o.order_status=1 and oi.game_cate='.$this->game_cate.' and oi.game_id='.$id.' and oi.game_status=0')
        ->select();
        // dump($order_info_all);
        // Db::rollback();
        // die;
        if(!empty($order_info_all)){
            foreach ($order_info_all as $key => $value) {
                $tz_result = explode(',', $order_info_all[$key]['tz_result']);
                foreach ($tz_result as $ke => $val) {
                    // 判断投注选项是否在中奖集合中
                    if(in_array($tz_result[$ke], $cate_id_arr)){
                        $result[] = $tz_result[$ke];
                    }else{
                        $cate_code = db('fb_game_cate')->where('cate_id='.$tz_result[$ke])->value('cate_code');
                        $pid = db('fb_code')->where('code="'.$cate_code.'"')->value('code_pid');
                        $code_arr = db('fb_code')->where('code_pid='.$pid)->column('code');

                        foreach ($code_arr as $k => $v) {
                            $cid = db('fb_game_cate')->where('game_id='.$id.' and is_win=1 and cate_code ="'.$v.'"')->value('cate_id');
                            if(!empty($cid)){
                                $cate_id[] = $cid;
                            }
                        }
                        $result[] = implode(',', $cate_id);
                        unset($cate_id);
                    }
                }
                $win_result = implode(',', $result);
                unset($result);
                
                db('order_info')->where('order_info_id='.$order_info_all[$key]['order_info_id'])->update(array('win_result'=>$win_result,'game_status'=>1));

                $order_ids[] = $order_info_all[$key]['order_id'];
            }
            $order_ids = array_unique($order_ids);
            
            $Group = new Group();
            // $order_ids = db('order_info')->where('game_cate='.$this->game_cate.' and game_id='.$id)->column('order_id');
            foreach ($order_ids as $key => $value) {
                // 判断该笔订单先所有比赛的比赛状态(0:未结束|1:已结算)
                $game_status = db('order_info')->where('game_cate='.$this->game_cate.' and order_id='.$order_ids[$key])->column('game_status');
                if(!in_array('0', $game_status)){
                    // 如果所有比赛全部为 已结算 结算该笔订单
                    $order = db('order')->where('game_cate='.$this->game_cate.' and order_id='.$order_ids[$key])->find();

                    $order_tz_result = db('order_info')->field('dan,tz_result')->where('game_cate='.$this->game_cate.' and order_id='.$order_ids[$key])->select();
                    $order_win_result = db('order_info')->field('dan,win_result tz_result')->where('game_cate='.$this->game_cate.' and order_id='.$order_ids[$key])->select();
                    $order_tz_odds = db('order_info')->field('dan,tz_odds tz_result')->where('game_cate='.$this->game_cate.' and order_id='.$order_ids[$key])->select();

                    $chuan = explode(',', $order['chuan']);
                    $order_tz_data = array();
                    $order_win_data = array();
                    for ($i=0; $i < count($chuan); $i++) { 
                        $get_tz_data = $Group->get_group($chuan[$i],$order_tz_result);
                        for ($j=0; $j < count($get_tz_data); $j++) { 
                            $order_tz_data[] = $get_tz_data[$j];
                        }
                        $get_win_data = $Group->get_group($chuan[$i],$order_win_result);
                        for ($j=0; $j < count($get_win_data); $j++) { 
                            $order_win_data[] = $get_win_data[$j];
                        }
                        $get_tz_odds = $Group->get_group($chuan[$i],$order_tz_odds);
                        for ($j=0; $j < count($get_tz_odds); $j++) { 
                            $order_tz_odds[] = $get_tz_odds[$j];
                        }
                    }

                    $total_odds = 0;

                    if(count($order_tz_data) == count($order_win_data)){
                        for ($i=0; $i < count($order_tz_data); $i++) { 
                            if($order_tz_data[$i] == $order_win_data[$i]){
                                $cate_odds = 1;
                                $exp_tz_data = explode(',', $order_tz_data[$i]);
                                for ($j=0; $j < count($exp_tz_data); $j++) { 
                                    // $order_tz_odds[] = db('fb_game_cate')->where('cate_id='.$exp_tz_data[$j])->value('cate_odds');
                                    // $cate_odds *= db('fb_game_cate')->where('cate_id='.$exp_tz_data[$j])->value('cate_odds');
                                    $cate_odds *= $order_tz_odds[$j];
                                }
                                $total_odds += $cate_odds;
                            }
                        }
                        if($order['order_type'] != 3){
                            $total_odds = ($total_odds*0.08)+$total_odds;
                        }
                        $win_money = $total_odds*$order['multiple']*2;
                    }

                    if($total_odds > 0){
                        // 如果订单为合买订单
                        if($order['order_type'] == 2){

                            // 获取合买信息
                            $order_hm_user = db('order_hm_user')->where('order_id='.$order_ids[$key])->find();
                            $order_hm_desc = db('order_hm_desc')->where('hm_id='.$order_hm_user['hm_id'])->find();
                            if($order_hm_desc['hm_status'] == 2){
                                // 将合买订单总中奖金额添加  
                                db('order_hm_desc')->where('hm_id='.$order_hm_desc['hm_id'])->update(array('total_win_money'=>$win_money));
                                // 然后用总中奖金额 除以 合买总分数 等于 每份中奖金额
                                $one_win_money = $win_money / $order_hm_desc['hm_num']; // 每份中奖金额
                                // 合买订单应分金额
                                $user_win_money = $one_win_money * $order_hm_user['pay_num'];
                                // 判断是不是发起人 不是得话将佣金返给发起人
                                if($order_hm_user['user_id'] != $order_hm_desc['user_id']){
                                    /********* 将佣金发放给发起人 *********/
                                    $commission = $user_win_money * $order_hm_desc['brokerage']; 
                                    $res = $Detail->add_detail($order_hm_desc['user_id'], 7, $commission, 1);
                                    /**添加明细**/
                                    if($res > 0){
                                        db('user')->where('id='.$order_hm_desc['user_id'])->setInc('balance',$commission);
                                        db('user')->where('id='.$order_hm_desc['user_id'])->setInc('amount_money',$commission);
                                    }else{
                                        // 回滚事务
                                        Db::rollback();
                                    }
                                    /********* 将佣金发放给发起人 END *********/
                                    // 扣除佣金
                                    $user_win_money = $user_win_money - $commission;
                                }
                                // 订单生效 将应分金额添加到列表数据中
                                db('order_hm_user')->where('hm_user_id='.$order_hm_user['hm_user_id'])->setField('win_money',$user_win_money);
                                // 将中奖金额发放
                                db('order')->where('order_id='.$order_ids[$key])->update(array('win_money'=>$user_win_money,'is_win'=>1));
                                $res = $Detail->add_detail($order['user_id'], 4, $user_win_money, 1);
                                /**添加明细**/
                                if($res > 0){
                                    // 将中奖金额添加到用户余额里
                                    db('user')->where('id='.$order['user_id'])->setInc('balance',$user_win_money);
                                    db('user')->where('id='.$order['user_id'])->setInc('amount_money',$user_win_money);
                                }else{
                                    // 回滚事务
                                    Db::rollback();
                                }    
                            }
                        }else if($order['order_type'] == 3){
                            $one_win_money = $win_money / $order['multiple'];
                            // 获取跟单信息
                            $order_gd_user = db('order_gd_user')->where('order_id='.$order_ids[$key])->find();
                            $order_gd_desc = db('order_gd_desc')->where('gd_id='.$order_gd_user['gd_id'])->find();

                            // 将方案中奖金额添加  
                            db('order_gd_desc')->where('gd_id='.$order_gd_desc['gd_id'])->update(array('total_win_money'=>$one_win_money));

                            $win_money = $one_win_money * $order_gd_user['pay_num'];

                            if($order_gd_user['user_id'] != $order_gd_desc['user_id']){
                                /********* 将佣金发放给发起人 *********/
                                $commission = $win_money * $order_gd_desc['brokerage']; 
                                // 扣除佣金
                                $win_money = $win_money - $commission;

                                $commission = $commission * 0.7;
                                
                                $res = $Detail->add_detail($order_gd_desc['user_id'], 8, $commission, 1);
                                /**添加明细**/
                                if($res > 0){
                                    db('user')->where('id='.$order_gd_desc['user_id'])->setInc('balance',$commission);
                                    db('user')->where('id='.$order_gd_desc['user_id'])->setInc('amount_money',$commission);
                                }else{
                                    // 回滚事务
                                    Db::rollback();
                                }
                                /********* 将佣金发放给发起人 END *********/
                                
                            }
                            // 订单生效 将应分金额添加到列表数据中
                            db('order_gd_user')->where('gd_user_id='.$order_gd_user['gd_user_id'])->setField('win_money',$win_money);
                            // 将中奖金额发放
                            db('order')->where('order_id='.$order_ids[$key])->update(array('win_money'=>$win_money,'is_win'=>1));
                            $res = $Detail->add_detail($order['user_id'], 4, $win_money, 1);
                            /**添加明细**/
                            if($res > 0){
                                // 将中奖金额添加到用户余额里
                                db('user')->where('id='.$order['user_id'])->setInc('balance',$win_money);
                                db('user')->where('id='.$order['user_id'])->setInc('amount_money',$win_money);
                            }else{
                                // 回滚事务
                                Db::rollback();
                            }  
                        }else{
                            db('order')->where('order_id='.$order_ids[$key])->update(array('win_money'=>$win_money,'is_win'=>1));
                            $res = $Detail->add_detail($order['user_id'], 4, $win_money, 1);
                            /**添加明细**/
                            if($res > 0){
                                db('user')->where('id='.$order['user_id'])->setInc('balance',$win_money);
                                db('user')->where('id='.$order['user_id'])->setInc('amount_money',$win_money);
                            }else{
                                // 回滚事务
                                Db::rollback();
                            } 
                        }
                    }else{
                        db('order')->where('order_id='.$order_ids[$key])->update(array('is_win'=>2));
                    }
                }
            }
        }
        // 操作成功 修改比赛状态
        $res = db('fb_game')->where('id='.$id)->setField('status',0);
        if($res > 0) {
            // 提交事务
            Db::commit();
            echo "结算成功";
            Log::write('结算成功:game_id='.$id);
        }else{
            Log::write('比赛已结算:game_id='.$id);
            echo "比赛已结算,请勿重复提交";
            // 回滚事务
            Db::rollback();
        }
    }
}