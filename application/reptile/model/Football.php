<?php
namespace app\reptile\model;
use Symfony\Component\DomCrawler\Crawler;
use think\Model;
use think\Log;
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
}