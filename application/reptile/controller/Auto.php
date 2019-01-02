<?php
namespace app\reptile\controller;
use think\Db;
use think\Log;
use app\adminz\model\Detail; 
use app\home\controller\Base; 
use app\reptile\model\Football; 
use app\reptile\model\Basketball; 

class Auto extends Base
{
	function __construct()
    {
        Log::init([
            'type' =>  'File',
            'path' =>  ROOT_PATH.'/logs/over/',
        ]);
    }
	public function fb_over()
	{
		// 开启事务
        Db::startTrans();
		$gamedate = db('fb_game')
            ->where('status=2 and end_time <"'.date("Y-m-d H:i:s").'"')
            ->order(['end_time'=>'asc'])
            ->value('end_time');
        if(!empty($gamedate)){
	        $datetime = date('Y-m-d',strtotime('-12 hour',strtotime($gamedate)));
	    	$Football = new Football();
	        $data = $Football->getScore($datetime);
	        if (!empty($data)) {
	        	foreach ($data as $key => $value) {
	        		$where = 'week="'.$data[$key]['week'].'" and game_no="'.$data[$key]['game_no'].'" and home_team="'.$data[$key]['home_team'].'" and road_team="'.$data[$key]['road_team'].'" and status>0';
	        		$game_id = db('fb_game')->where($where)->value('id');
	        		if(!empty($game_id)){
	        			$arr['top_score'] = $data[$key]['halfscore'];
	        			$arr['down_score'] = $data[$key]['score'];
	        			$score = explode(':', $arr['down_score']);
	        			$arr['total_score'] = $score[0]+$score[1];
	        			$res = db('fb_game')->where('id='.$game_id)->update($arr);
	        			if($res > 0){
	        				Log::write('比分修改成功:'.$game_id);
	        				Db::commit();
	        				$Football->fb_over($game_id);
	        			}else{
	        				Log::write('比分修改失败:'.$game_id);
	        			}
	        		}else{
	        			Log::write('该场比赛已结算:'.$where);
	        			continue;
	        		}
	        	}
	        }else{
	        	Log::write('暂无可结算比赛:'.$gamedate);
	        	exit;
	        }        	
        }else{
        	Log::write('暂无可结算比赛!~');
        	exit;
        }

	}

	public function bb_over()
	{
		// 开启事务
        Db::startTrans();
		$gamedate = db('nba_game')
            ->where('status=2 and end_time <"'.date("Y-m-d H:i:s").'"')
            ->order(['end_time'=>'asc'])
            ->value('end_time');

        if(!empty($gamedate)){
	        $datetime = date('Y-m-d',strtotime('-12 hour',strtotime($gamedate)));
	    	$Basketball = new Basketball();
	        $data = $Basketball->getScore($datetime);
	        if (!empty($data)) {
	        	foreach ($data as $key => $value) {
	        		$where = 'week="'.$data[$key]['week'].'" and game_no="'.$data[$key]['game_no'].'" and home_team="'.$data[$key]['home_team'].'" and road_team="'.$data[$key]['road_team'].'" and status>0';
	        		$game_id = db('nba_game')->where($where)->value('id');
	        		if(!empty($game_id)){
	        			$arr['home_score'] = $data[$key]['home_score'];
            	        $arr['road_score'] = $data[$key]['road_score'];

                        if(!empty($arr['home_score']) && !empty($arr['road_score'])){
			                if($arr['home_score'] > $arr['road_score']){
			                    $arr['win_team'] = $data[$key]['home_team'];
			                }else if($arr['home_score'] < $arr['road_score']){
			                    $arr['win_team'] = $data[$key]['road_team'];
			                }else if($arr['home_score'] == $arr['road_score']){
	                            continue;
	                        } 
			            }
	        			$res = db('nba_game')->where('id='.$game_id)->update($arr);
	        			if($res > 0){
	        				Log::write('比分修改成功:'.$game_id);
	        				Db::commit();
	        				$Basketball->nba_over($game_id);
	        			}else{
	        				Log::write('比分修改失败:'.$game_id);
	        			}
	        		}else{
	        			Log::write('该场比赛已结算:'.$where);
	        			continue;
	        		}
	        	}
	        }else{
	        	Log::write('暂无可结算比赛:'.$gamedate);
	        	exit;
	        }        	
        }else{
        	Log::write('暂无可结算比赛!~');
        	exit;
        }

	}

	public function xunjian()
	{
		// 开启事务
        Db::startTrans();
		$Detail = new Detail();

		$order_hm_desc = db('order_hm_desc')->where('hm_status=1')->select();
		foreach ($order_hm_desc as $key => $value) {
			if($order_hm_desc[$key]['end_time'] <= date('Y-m-d H:i:s')){
				if($order_hm_desc[$key]['residue_num'] > 0){
					$shengyu = $order_hm_desc[$key]['residue_num'] - $order_hm_desc[$key]['bd_num'];
					if($shengyu <= 0){
						db('order_hm_desc')->where('hm_id='.$order_hm_desc[$key]['hm_id'])->setField('hm_status',2);
						if($shengyu != 0){
							$shengyu = abs($shengyu);
                			$res = $Detail->add_detail($order_hm_desc[$key]['user_id'], 6, $shengyu, 1);
							if($res > 0){
								db('user')->where('id='.$order_hm_desc[$key]['user_id'])->setInc('balance',$shengyu);
                				db('user')->where('id='.$order_hm_desc[$key]['user_id'])->setInc('amount_money',$shengyu);
								// 提交事务
	            				Db::commit();
							}else{
								// 回滚事务
	            				Db::rollback();
							}
						}
					}else{
						db('order_hm_desc')->where('hm_id='.$order_hm_desc[$key]['hm_id'])->setField('hm_status',0);
						$order_hm_user = db('order_hm_user')->where('hm_id='.$order_hm_desc[$key]['hm_id'])->select();
						foreach ($order_hm_user as $ke => $val) {
							$order = db('order')->where('order_id='.$order_hm_user[$ke]['order_id'])->find();
							db('order')->where('order_id='.$order_hm_user[$ke]['order_id'])->setField('order_status',0);
	            			$res = $Detail->add_detail($order_hm_user[$ke]['user_id'], 6, $order['order_money'], 1);
							if($res > 0){
								db('user')->where('id='.$order_hm_user[$ke]['user_id'])->setInc('balance',$order['order_money']);
	            				db('user')->where('id='.$order_hm_user[$ke]['user_id'])->setInc('amount_money',$order['order_money']);
								// 提交事务
	            				Db::commit();
							}else{
								// 回滚事务
	            				Db::rollback();
							}
						}
					}
				}
			}
		}
	}
	
}
	