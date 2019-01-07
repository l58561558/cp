<?php
namespace app\home\controller;
/*
* 开奖信息控制器
*/
class History extends Base
{
	
	public function history()
	{
		$nba_game = db('nba_game')->field('id,end_time,home_team,road_team,home_score,road_score')->where('is_postpone=0 and status=0')->order('end_time','desc')->find();
		$fb_game = db('fb_game')->field('id,end_time,home_team,road_team,down_score')->where('is_postpone=0 and status=0')->order('end_time','desc')->find();
		$fb_game['home_score'] = explode(':', $fb_game['down_score'])[0];
		$fb_game['road_score'] = explode(':', $fb_game['down_score'])[1];
		$data['nba_game'] = $nba_game;
		$data['fb_game'] = $fb_game;

		echo json_encode(['msg'=>'请求成功','code'=>1,'success'=>true,'data'=>$data]);
        exit;
	}

	public function history_list($type, $page=1, $count=10)
	{
		$start = ($page-1)*$count;
		$let_arr = array('let_score_home_win','let_score_home_eq','let_score_home_lose');
		if($type == 1){
			$data = db('fb_game')->where('is_postpone=0 and status=0')->limit($start, $count)->order('end_time','desc')->select();
			foreach ($data as $key => $value) {
				$data[$key]['home_score'] = explode(':', $data[$key]['down_score'])[0];
				$data[$key]['road_score'] = explode(':', $data[$key]['down_score'])[1];
				$data[$key]['result'] = db('fb_game_cate')->field('cate_name,cate_code,cate_odds')->where('is_win=1 and game_id='.$data[$key]['id'])->select();
				foreach ($data[$key]['result'] as $ke => $val) {
					if(in_array($data[$key]['result'][$ke]['cate_code'], $let_arr)){
						$data[$key]['result'][$ke]['cate_name'] = '('.$data[$key]['let_score'].')'.$data[$key]['result'][$ke]['cate_name'];
					}
				}
			}
		}
		if($type == 2){
			$data = db('nba_game')->where('is_postpone=0 and status=0')->limit($start, $count)->order('end_time','desc')->select();
			foreach ($data as $key => $value) {
				$data[$key]['result'] = db('nba_game_cate')->field('cate_name,cate_code,cate_odds')->where('is_win=1 and game_id='.$data[$key]['id'])->select();
				foreach ($data[$key]['result'] as $ke => $val) {
					if(in_array($data[$key]['result'][$ke]['cate_code'], $let_arr)){
						$data[$key]['result'][$ke]['cate_name'] = '('.$data[$key]['let_score'].')'.$data[$key]['result'][$ke]['cate_name'];
					}
				}
			}
		}
		echo json_encode(['msg'=>'请求成功','code'=>1,'success'=>true,'data'=>$data]);
        exit;

	}


}

?>