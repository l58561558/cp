<?php
namespace app\reptile\controller;
use app\home\controller\Base; 
use app\adminz\model\Detail; 
use think\Db;
class Auto extends Base
{
	public function test()
	{
		// $file_name = '/data/www/default/test.php';
		// $fp = @fopen($file_name,'a+');
		// fwrite($fp , date('Y-m-d H:i:s').PHP_EOL);
		// fclose($fp);
		$order_info = db('order_info')
		->field('order_info_id,tz_result,game_cate')
		// ->where('add_time>="'.date('Y-m-d H:i:s',strtotime(date('Y-m-d'))).'"')
		->where('tz_odds', '')
		->select();

		foreach ($order_info as $key => $value) {
			if($order_info[$key]['game_cate'] == 1){
				$game_cate = 'fb_game';
			}else{
				$game_cate = 'nba_game';
			}
			if(!empty($order_info[$key]['tz_result'])){
				$order_info[$key]['tz_result'] = explode(',', $order_info[$key]['tz_result']);
			
				foreach ($order_info[$key]['tz_result'] as $ke => $val) {
	                $order_info[$key]['tz_odds'][$ke] = db($game_cate.'_cate')->where('cate_id='.$order_info[$key]['tz_result'][$ke])->value('cate_odds');
	            }
	            $order_info[$key]['tz_odds'] = implode(',', $order_info[$key]['tz_odds']);
				$res = db('order_info')->where('order_info_id='.$order_info[$key]['order_info_id'])->setField('tz_odds',$order_info[$key]['tz_odds']);
				echo $key;
			}
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
							db('user')->where('id='.$order_hm_desc[$key]['user_id'])->setInc('no_balance',$shengyu*0.04);
							db('user')->where('id='.$order_hm_desc[$key]['user_id'])->setInc('balance',$shengyu*0.06);
                			db('user')->where('id='.$order_hm_desc[$key]['user_id'])->setInc('amount_money',$shengyu);
                			$res = $Detail->add_detail($order_hm_desc[$key]['user_id'], 6, $shengyu, 1);
							if($res > 0){
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
							db('user')->where('id='.$order_hm_user[$ke]['user_id'])->setInc('no_balance',$order['order_money']*0.04);
							db('user')->where('id='.$order_hm_user[$ke]['user_id'])->setInc('balance',$order['order_money']*0.06);
	            			db('user')->where('id='.$order_hm_user[$ke]['user_id'])->setInc('amount_money',$order['order_money']);
	            			$res = $Detail->add_detail($order_hm_user[$ke]['user_id'], 6, $order['order_money'], 1);
							if($res > 0){
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
	