<?php
namespace app\reptile\model;
use Symfony\Component\DomCrawler\Crawler;
use think\Model;
use think\Log;
class Basketball extends Model {
    
    function __construct(){
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
    public function getScore($gamedate = '')
    {	
        $mrand = sprintf('%.0f',(floatval(lcg_value()*1e20)));
        $url = "http://lanqiu.zgzcw.com/live/LivePlay.action?code=200&date=".$gamedate."&r=".$mrand;
    	$res = getCurl($url);
        $data = json_decode($res,true);
        $gamelist = array();
    	foreach ($data as $key => $value) {
            $match_info = $data[$key];
    		$matchdate = date('Y-m-d',strtotime($match_info[7])-43200);
    		if($gamedate == $matchdate){
    			$status = $match_info[8];
    			if($status == '-1'){
                    $matchno            = $match_info[39];
                    $game['gamedate']   = $matchdate;
                    $game['week']       = substr($matchno,0,6);
                    $game['game_no']    = substr($matchno,-3,3);
                    $road_team          = substr($match_info[14],0,strpos($match_info[14],'['));
                    $home_team          = substr($match_info[11],0,strpos($match_info[11],'['));
                    $game['road_team']  = $road_team;
                    $game['home_team']  = $home_team;
                    $game['road_score'] = $match_info[17];
                    $game['home_score'] = $match_info[16];
					$gamelist[] = $game;
    			}else{
    				continue;
    			}
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
}