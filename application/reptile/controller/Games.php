<?php
namespace app\reptile\controller;
use think\Db;
use think\Log;
use app\home\controller\Base;
use app\reptile\model\Soccer;
use app\reptile\model\Crawler;

class Games extends Base {
    
    function __construct(){
        Log::init([
            'type' =>  'File',
            'path' =>  ROOT_PATH.'/logs/add/',
        ]);
    }

    public function addFootball()
    {
    	// 开启事务
        Db::startTrans();

    	$microtime = microtime();
    	list($msec, $sec) = explode(' ', $microtime);
    	$msectime = (float)sprintf('%.0f',(floatval($msec)+floatval($sec))*1000);
    	$rand = lcg_value();
    	$url = $this->getCurl("http://info.sporttery.cn/interface/interface_mixed.php?action=fb_list&pke=".$rand."&_=".$msectime);
		$api = iconv("gb2312", "utf-8//IGNORE",$url);
		$p1 = explode('var data=', $api);
		$p2 = explode(';getData();', $p1[1]);
		$data = json_decode($p2[0]);

    	if(!empty($data)){
    		foreach ($data as $key => $value) {
				$game_info            = $data[$key];
				$fb_game['week']      = substr($game_info[0][0],0,6);
				$fb_game['game_no']   = substr($game_info[0][0],-3,3);				
				$fb_game['game_name'] = $game_info[0][1];
				$fb_game['end_time']  = date('Y-m-d H:i:s', strtotime($game_info[0][3])-180);
				if($fb_game['end_time'] < date('Y-m-d H:i:s')){
					continue;
				}
				$arr                  = explode("$", $game_info[0][2]);
				$fb_game['home_team'] = $arr[0];
				$fb_game['let_score'] = $arr[1];
				$fb_game['road_team'] = $arr[2];
				$fb_game['add_time']  = date('Y-m-d H:i:s');

				$id = db('fb_game')
						->where('week','=',$fb_game['week'])
						->where('game_no','=',$fb_game['game_no'])
						->where('home_team="'.$fb_game['home_team'].'"')
						->where('road_team="'.$fb_game['road_team'].'"')
						// ->where('end_time="'.$fb_game['end_time'].'"')
						->value('id');
				if(!empty($id) && $id >0){
					$game_id = db('fb_game')->where('id',$id)->update($fb_game);
					$fb_game_cate['home_win']            = empty($game_info[5][0])?0:$game_info[5][0];
					$fb_game_cate['home_eq']             = empty($game_info[5][1])?0:$game_info[5][1];
					$fb_game_cate['home_lose']           = empty($game_info[5][2])?0:$game_info[5][2];
					$fb_game_cate['let_score_home_win']  = empty($game_info[1][0])?0:$game_info[1][0];
					$fb_game_cate['let_score_home_eq']   = empty($game_info[1][1])?0:$game_info[1][1];
					$fb_game_cate['let_score_home_lose'] = empty($game_info[1][2])?0:$game_info[1][2];
					$fb_game_cate['one_zero']            = empty($game_info[2][0])?0:$game_info[2][0];
					$fb_game_cate['two_zero']            = empty($game_info[2][1])?0:$game_info[2][1];
					$fb_game_cate['two_one']             = empty($game_info[2][2])?0:$game_info[2][2];
					$fb_game_cate['three_zero']          = empty($game_info[2][3])?0:$game_info[2][3];
					$fb_game_cate['three_one']           = empty($game_info[2][4])?0:$game_info[2][4];
					$fb_game_cate['three_two']           = empty($game_info[2][5])?0:$game_info[2][5];
					$fb_game_cate['four_zero']           = empty($game_info[2][6])?0:$game_info[2][6];
					$fb_game_cate['four_one']            = empty($game_info[2][7])?0:$game_info[2][7];
					$fb_game_cate['four_two']            = empty($game_info[2][8])?0:$game_info[2][8];
					$fb_game_cate['five_zero']           = empty($game_info[2][9])?0:$game_info[2][9];
					$fb_game_cate['five_one']            = empty($game_info[2][10])?0:$game_info[2][10];
					$fb_game_cate['five_two']            = empty($game_info[2][11])?0:$game_info[2][11];
					$fb_game_cate['win_other']           = empty($game_info[2][12])?0:$game_info[2][12];				
					$fb_game_cate['zero_zero']           = empty($game_info[2][13])?0:$game_info[2][13];
					$fb_game_cate['one_one']             = empty($game_info[2][14])?0:$game_info[2][14];
					$fb_game_cate['two_two']             = empty($game_info[2][15])?0:$game_info[2][15];
					$fb_game_cate['three_three']         = empty($game_info[2][16])?0:$game_info[2][16];			
					$fb_game_cate['eq_other']            = empty($game_info[2][17])?0:$game_info[2][17];				
					$fb_game_cate['zero_one']            = empty($game_info[2][18])?0:$game_info[2][18];
					$fb_game_cate['zero_two']            = empty($game_info[2][19])?0:$game_info[2][19];
					$fb_game_cate['one_two']             = empty($game_info[2][20])?0:$game_info[2][20];
					$fb_game_cate['zero_three']          = empty($game_info[2][21])?0:$game_info[2][21];
					$fb_game_cate['one_three']           = empty($game_info[2][22])?0:$game_info[2][22];
					$fb_game_cate['two_three']           = empty($game_info[2][23])?0:$game_info[2][23];
					$fb_game_cate['zero_four']           = empty($game_info[2][24])?0:$game_info[2][24];
					$fb_game_cate['one_four']            = empty($game_info[2][25])?0:$game_info[2][25];
					$fb_game_cate['two_four']            = empty($game_info[2][26])?0:$game_info[2][26];
					$fb_game_cate['zero_five']           = empty($game_info[2][27])?0:$game_info[2][27];
					$fb_game_cate['one_five']            = empty($game_info[2][28])?0:$game_info[2][28];
					$fb_game_cate['two_five']            = empty($game_info[2][29])?0:$game_info[2][29];			
					$fb_game_cate['lose_other']          = empty($game_info[2][30])?0:$game_info[2][30];			
					$fb_game_cate['total_zero']          = empty($game_info[3][0])?0:$game_info[3][0];
					$fb_game_cate['total_one']           = empty($game_info[3][1])?0:$game_info[3][1];
					$fb_game_cate['total_two']           = empty($game_info[3][2])?0:$game_info[3][2];
					$fb_game_cate['total_three']         = empty($game_info[3][3])?0:$game_info[3][3];
					$fb_game_cate['total_four']          = empty($game_info[3][4])?0:$game_info[3][4];
					$fb_game_cate['total_five']          = empty($game_info[3][5])?0:$game_info[3][5];
					$fb_game_cate['total_six']           = empty($game_info[3][6])?0:$game_info[3][6];
					$fb_game_cate['total_seven_gt']      = empty($game_info[3][7])?0:$game_info[3][7];			
					$fb_game_cate['win_win']             = empty($game_info[4][0])?0:$game_info[4][0];
					$fb_game_cate['win_eq']              = empty($game_info[4][1])?0:$game_info[4][1];
					$fb_game_cate['win_lose']            = empty($game_info[4][2])?0:$game_info[4][2];
					$fb_game_cate['eq_win']              = empty($game_info[4][3])?0:$game_info[4][3];
					$fb_game_cate['eq_eq']               = empty($game_info[4][4])?0:$game_info[4][4];
					$fb_game_cate['eq_lose']             = empty($game_info[4][5])?0:$game_info[4][5];
					$fb_game_cate['lose_win']            = empty($game_info[4][6])?0:$game_info[4][6];
					$fb_game_cate['lose_eq']             = empty($game_info[4][7])?0:$game_info[4][7];
					$fb_game_cate['lose_lose']           = empty($game_info[4][8])?0:$game_info[4][8];
					foreach ($fb_game_cate as $k => $v) {
						$game_cate_data['cate_code'] = $k;
						$game_cate_data['cate_odds'] = $v;
						$fb_game_cate_data[] = $game_cate_data;
					}
					$game_cate = db('fb_game_cate')->field('cate_id,cate_code,cate_odds')->where('game_id='.$id)->select();
					for ($i=0; $i < count($game_cate); $i++) { 
						if($game_cate[$i]['cate_code'] == $fb_game_cate_data[$i]['cate_code']){
							if($game_cate[$i]['cate_odds'] != $fb_game_cate_data[$i]['cate_odds']){
								$fb_res = db('fb_game_cate')
								->where('cate_id='.$game_cate[$i]['cate_id'].' and cate_code="'.$game_cate[$i]['cate_code'].'"')
								->setField('cate_odds',$fb_game_cate_data[$i]['cate_odds']);
								if($fb_res > 0){
									Db::commit();
								}else{
									Db::rollback();
								}
							}
						}
					}
					unset($fb_game_cate_data);
    			}else{
    				$game_id = db('fb_game')->insertGetId($fb_game);
    				if($game_id > 0){
						Db::commit();
					}else{
						Db::rollback();
					}
					$fb_game_cate['home_win']            = empty($game_info[5][0])?0:$game_info[5][0];
					$fb_game_cate['home_eq']             = empty($game_info[5][1])?0:$game_info[5][1];
					$fb_game_cate['home_lose']           = empty($game_info[5][2])?0:$game_info[5][2];
					$fb_game_cate['let_score_home_win']  = empty($game_info[1][0])?0:$game_info[1][0];
					$fb_game_cate['let_score_home_eq']   = empty($game_info[1][1])?0:$game_info[1][1];
					$fb_game_cate['let_score_home_lose'] = empty($game_info[1][2])?0:$game_info[1][2];
					$fb_game_cate['one_zero']            = empty($game_info[2][0])?0:$game_info[2][0];
					$fb_game_cate['two_zero']            = empty($game_info[2][1])?0:$game_info[2][1];
					$fb_game_cate['two_one']             = empty($game_info[2][2])?0:$game_info[2][2];
					$fb_game_cate['three_zero']          = empty($game_info[2][3])?0:$game_info[2][3];
					$fb_game_cate['three_one']           = empty($game_info[2][4])?0:$game_info[2][4];
					$fb_game_cate['three_two']           = empty($game_info[2][5])?0:$game_info[2][5];
					$fb_game_cate['four_zero']           = empty($game_info[2][6])?0:$game_info[2][6];
					$fb_game_cate['four_one']            = empty($game_info[2][7])?0:$game_info[2][7];
					$fb_game_cate['four_two']            = empty($game_info[2][8])?0:$game_info[2][8];
					$fb_game_cate['five_zero']           = empty($game_info[2][9])?0:$game_info[2][9];
					$fb_game_cate['five_one']            = empty($game_info[2][10])?0:$game_info[2][10];
					$fb_game_cate['five_two']            = empty($game_info[2][11])?0:$game_info[2][11];
					$fb_game_cate['win_other']           = empty($game_info[2][12])?0:$game_info[2][12];				
					$fb_game_cate['zero_zero']           = empty($game_info[2][13])?0:$game_info[2][13];
					$fb_game_cate['one_one']             = empty($game_info[2][14])?0:$game_info[2][14];
					$fb_game_cate['two_two']             = empty($game_info[2][15])?0:$game_info[2][15];
					$fb_game_cate['three_three']         = empty($game_info[2][16])?0:$game_info[2][16];			
					$fb_game_cate['eq_other']            = empty($game_info[2][17])?0:$game_info[2][17];				
					$fb_game_cate['zero_one']            = empty($game_info[2][18])?0:$game_info[2][18];
					$fb_game_cate['zero_two']            = empty($game_info[2][19])?0:$game_info[2][19];
					$fb_game_cate['one_two']             = empty($game_info[2][20])?0:$game_info[2][20];
					$fb_game_cate['zero_three']          = empty($game_info[2][21])?0:$game_info[2][21];
					$fb_game_cate['one_three']           = empty($game_info[2][22])?0:$game_info[2][22];
					$fb_game_cate['two_three']           = empty($game_info[2][23])?0:$game_info[2][23];
					$fb_game_cate['zero_four']           = empty($game_info[2][24])?0:$game_info[2][24];
					$fb_game_cate['one_four']            = empty($game_info[2][25])?0:$game_info[2][25];
					$fb_game_cate['two_four']            = empty($game_info[2][26])?0:$game_info[2][26];
					$fb_game_cate['zero_five']           = empty($game_info[2][27])?0:$game_info[2][27];
					$fb_game_cate['one_five']            = empty($game_info[2][28])?0:$game_info[2][28];
					$fb_game_cate['two_five']            = empty($game_info[2][29])?0:$game_info[2][29];			
					$fb_game_cate['lose_other']          = empty($game_info[2][30])?0:$game_info[2][30];			
					$fb_game_cate['total_zero']          = empty($game_info[3][0])?0:$game_info[3][0];
					$fb_game_cate['total_one']           = empty($game_info[3][1])?0:$game_info[3][1];
					$fb_game_cate['total_two']           = empty($game_info[3][2])?0:$game_info[3][2];
					$fb_game_cate['total_three']         = empty($game_info[3][3])?0:$game_info[3][3];
					$fb_game_cate['total_four']          = empty($game_info[3][4])?0:$game_info[3][4];
					$fb_game_cate['total_five']          = empty($game_info[3][5])?0:$game_info[3][5];
					$fb_game_cate['total_six']           = empty($game_info[3][6])?0:$game_info[3][6];
					$fb_game_cate['total_seven_gt']      = empty($game_info[3][7])?0:$game_info[3][7];			
					$fb_game_cate['win_win']             = empty($game_info[4][0])?0:$game_info[4][0];
					$fb_game_cate['win_eq']              = empty($game_info[4][1])?0:$game_info[4][1];
					$fb_game_cate['win_lose']            = empty($game_info[4][2])?0:$game_info[4][2];
					$fb_game_cate['eq_win']              = empty($game_info[4][3])?0:$game_info[4][3];
					$fb_game_cate['eq_eq']               = empty($game_info[4][4])?0:$game_info[4][4];
					$fb_game_cate['eq_lose']             = empty($game_info[4][5])?0:$game_info[4][5];
					$fb_game_cate['lose_win']            = empty($game_info[4][6])?0:$game_info[4][6];
					$fb_game_cate['lose_eq']             = empty($game_info[4][7])?0:$game_info[4][7];
					$fb_game_cate['lose_lose']           = empty($game_info[4][8])?0:$game_info[4][8];

	     			$array = array('home_win','home_eq','home_lose','let_score_home_win','let_score_home_eq','let_score_home_lose');
					foreach ($fb_game_cate as $k => $v) {
						$fb_game_cate_data['game_id']   = $game_id;
						$fb_game_cate_data['cate_name'] = db('fb_code')->where('code="'.$k.'"')->value('code_name');
						$fb_game_cate_data['cate_code'] = $k;
						// $fb_game_cate_data['cate_odds'] = $v==0?0:(in_array($k, $array)?floor($v*1.085*100)/100:$v);
						$fb_game_cate_data['cate_odds'] = $v;
						$fb_game_cate_data['init_cate_odds'] = $v;
						$fb_game_cate_data['status']    = 1;
						$fb_game_cate_data['is_win']    = 0;

						$fb_game_cate_res = db('fb_game_cate')->insertGetId($fb_game_cate_data);
						if($fb_game_cate_res > 0){
							Db::commit();
						}else{
							Db::rollback();
						}
					}
    			}
    		}
    		echo 'OK';
    	}
    }

    public function addBasketball()
    {
    	// 开启事务
        Db::startTrans();

		$microtime = microtime();
        list($msec, $sec) = explode(' ', $microtime);
        $msectime = (float)sprintf('%.0f',(floatval($msec)+floatval($sec))*1000);
        $rand = lcg_value();
        $url = $this->getCurl("http://info.sporttery.cn/interface/interface_mixed.php?action=bk_list&".$rand."&_=".$msectime);
        $api = iconv("gb2312", "utf-8//IGNORE",$url);
		$p1 = explode('var data=', $api);
		$p2 = explode(';getData();', $p1[1]);
		$data = json_decode($p2[0]);

    	if(!empty($data)){
    		foreach ($data as $key => $value) {
    			$game_info = $data[$key];
    			if($game_info[0][1] == "美职篮" || $game_info[0][1] == "欧篮联"){
    				$nba_game['game_name']     = $game_info[0][1];
					$nba_game['week']          = substr($game_info[0][0],0,6);
    				$nba_game['game_no']       = substr($game_info[0][0],-3,3);   				
    				$nba_game['road_team']     = $game_info[0][2];
    				$nba_game['home_team']     = $game_info[0][3];
    				$nba_game['total_score']   = !isset($game_info[3][0])?0:str_replace("+", "", $game_info[3][0]); // 总分
    				$nba_game['let_score']     = !isset($game_info[2][0])?0:$game_info[2][0]; // 让分   				
    				$nba_game['end_time']      = date('Y-m-d H:i:s', strtotime($game_info[0][4])-180);
    				$nba_game['add_time']      = date('Y-m-d H:i:s'); // 生成时间
    				if($nba_game['end_time'] < date('Y-m-d H:i:s')){
						continue;
					}
    				$id = db('nba_game')
						->where('week','=',$nba_game['week'])
						->where('game_no','=',$nba_game['game_no'])
						->where('home_team="'.$fb_game['home_team'].'"')
						->where('road_team="'.$fb_game['road_team'].'"')
						// ->where('end_time="'.$nba_game['end_time'].'"')
						->value('id');
					if(!empty($id) && $id >0){
						$game_id = db('nba_game')->where('id',$id)->update($nba_game);
						// continue;
						$nba_game_cate['road_win']           = !isset($game_info[1][0])?0:$game_info[1][0];
		                $nba_game_cate['home_win']           = !isset($game_info[1][1])?0:$game_info[1][1];
		                $nba_game_cate['let_score_road_win'] = !isset($game_info[2][1])?0:$game_info[2][1];
		                $nba_game_cate['let_score_home_win'] = !isset($game_info[2][2])?0:$game_info[2][2];
		                $nba_game_cate['total_small']        = !isset($game_info[3][1])?0:$game_info[3][2];
		                $nba_game_cate['total_big']          = !isset($game_info[3][2])?0:$game_info[3][1];                 
		                $nba_game_cate['differ_road_5']      = !isset($game_info[4][0])?0:$game_info[4][0];
		                $nba_game_cate['differ_road_10']     = !isset($game_info[4][1])?0:$game_info[4][1];
		                $nba_game_cate['differ_road_15']     = !isset($game_info[4][2])?0:$game_info[4][2];
		                $nba_game_cate['differ_road_20']     = !isset($game_info[4][3])?0:$game_info[4][3];
		                $nba_game_cate['differ_road_25']     = !isset($game_info[4][4])?0:$game_info[4][4];
		                $nba_game_cate['differ_road_26']     = !isset($game_info[4][5])?0:$game_info[4][5];
		                $nba_game_cate['differ_home_5']      = !isset($game_info[4][6])?0:$game_info[4][6];
		                $nba_game_cate['differ_home_10']     = !isset($game_info[4][7])?0:$game_info[4][7];
		                $nba_game_cate['differ_home_15']     = !isset($game_info[4][8])?0:$game_info[4][8];
		                $nba_game_cate['differ_home_20']     = !isset($game_info[4][9])?0:$game_info[4][9];
		                $nba_game_cate['differ_home_25']     = !isset($game_info[4][10])?0:$game_info[4][10];
		                $nba_game_cate['differ_home_26']     = !isset($game_info[4][11])?0:$game_info[4][11];

						foreach ($nba_game_cate as $k => $v) {
							$game_cate_data['cate_code'] = $k;
							$game_cate_data['cate_odds'] = $v;
							$nba_game_cate_data[] = $game_cate_data;
						}
						$game_cate = db('nba_game_cate')->field('cate_id,cate_code,cate_odds')->where('game_id='.$id)->select();
						for ($i=0; $i < count($game_cate); $i++) { 
							if($game_cate[$i]['cate_code'] == $nba_game_cate_data[$i]['cate_code']){
								if($game_cate[$i]['cate_odds'] != $nba_game_cate_data[$i]['cate_odds']){
									$nba_res = db('nba_game_cate')
									->where('cate_id='.$game_cate[$i]['cate_id'].' and cate_code="'.$game_cate[$i]['cate_code'].'"')
									->setField('cate_odds',$nba_game_cate_data[$i]['cate_odds']);
									if($nba_res > 0){
										Db::commit();
									}else{
										Db::rollback();
									}
								}
							}
						}
						unset($nba_game_cate_data);
	    			}else{
	    				$game_id = db('nba_game')->insertGetId($nba_game);
	    				if($game_id > 0){
							Db::commit();
						}else{
							Db::rollback();
						}
						$nba_game_cate['road_win']           = !isset($game_info[1][0])?0:$game_info[1][0];
		                $nba_game_cate['home_win']           = !isset($game_info[1][1])?0:$game_info[1][1];
		                $nba_game_cate['let_score_road_win'] = !isset($game_info[2][1])?0:$game_info[2][1];
		                $nba_game_cate['let_score_home_win'] = !isset($game_info[2][2])?0:$game_info[2][2];
		                $nba_game_cate['total_small']        = !isset($game_info[3][1])?0:$game_info[3][2];
		                $nba_game_cate['total_big']          = !isset($game_info[3][2])?0:$game_info[3][1];                 
		                $nba_game_cate['differ_road_5']      = !isset($game_info[4][0])?0:$game_info[4][0];
		                $nba_game_cate['differ_road_10']     = !isset($game_info[4][1])?0:$game_info[4][1];
		                $nba_game_cate['differ_road_15']     = !isset($game_info[4][2])?0:$game_info[4][2];
		                $nba_game_cate['differ_road_20']     = !isset($game_info[4][3])?0:$game_info[4][3];
		                $nba_game_cate['differ_road_25']     = !isset($game_info[4][4])?0:$game_info[4][4];
		                $nba_game_cate['differ_road_26']     = !isset($game_info[4][5])?0:$game_info[4][5];
		                $nba_game_cate['differ_home_5']      = !isset($game_info[4][6])?0:$game_info[4][6];
		                $nba_game_cate['differ_home_10']     = !isset($game_info[4][7])?0:$game_info[4][7];
		                $nba_game_cate['differ_home_15']     = !isset($game_info[4][8])?0:$game_info[4][8];
		                $nba_game_cate['differ_home_20']     = !isset($game_info[4][9])?0:$game_info[4][9];
		                $nba_game_cate['differ_home_25']     = !isset($game_info[4][10])?0:$game_info[4][10];
		                $nba_game_cate['differ_home_26']     = !isset($game_info[4][11])?0:$game_info[4][11];

						$array = array('road_win','home_win','let_score_road_win','let_score_home_win','total_small','total_big');
						foreach ($nba_game_cate as $k => $v) {
							$nba_game_cate_data['game_id']   = $game_id;
							$nba_game_cate_data['cate_name'] = db('nba_code')->where('code="'.$k.'"')->value('code_name');
							$nba_game_cate_data['cate_code'] = $k;
							// $nba_game_cate_data['cate_odds'] = $v==0?0:(in_array($k, $array)?floor($v*1.085*100)/100:$v);
							$nba_game_cate_data['cate_odds'] = $v;
							$nba_game_cate_data['init_cate_odds'] = $v;
							$nba_game_cate_data['status']    = 1;
							$nba_game_cate_data['is_win']    = 0;                    

	                    	$nba_game_cate_res = db('nba_game_cate')->insertGetId($nba_game_cate_data);
	                    	if($nba_game_cate_res > 0){
								Db::commit();
							}else{
								Db::rollback();
							}
		                }
    				}
    			}else{
    				continue;
    			}	
    		}
    		echo 'OK';
    	}
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
            Log::error('自动生成足彩任选：没有新比赛');
        }
        $api_result = json_decode($api_result_json, true);
        if (empty($api_result)) {
            Log::error('自动生成足彩任选：爬取数据错误');
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
            Log::error('自动生成足彩任选：爬取数据有改动！生成失败！');
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
            Log::error('自动生成足彩任选：入库失败！');
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
            Log::error('自动生成足彩任选：入库失败！');
        }
        Db::commit();
        $this->success('生成' . $session . '期成功');
    }

    /**
     * curl的get请求
     * @param  string $url 请求的url
     * @return mixed  $result 返回请求结果
     */
    public function getCurl($url){
		$header = 'Accept-Content-Type:application/json;Accept-Charset: utf-8';
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_HEADER,$header);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_HEADER,0);
        curl_setopt($curl,CURLOPT_NOBODY,0);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($curl,CURLOPT_URL,$url);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

}