<?php
namespace app\nba\controller;
use app\nba\model\Group;
use app\home\controller\Base; 
use think\Db;
// NBA比赛竞猜
class Nba extends Base
{
    public $game_id = 2;
    // 玩法列表
    public function nba_code()
    {
        $data = db('nba_code')->field('id,code_name')->where('code_pid=0')->select();
        $arr['id'] = 0;
        $arr['code'] = 'all';
        $arr['code_name'] = '混合投注';
        $data[] = $arr;
        foreach ($data as $key => $value) {
            $data[$key]['code_id'] = $data[$key]['id'];
            unset($data[$key]['id']);
        }
        echo json_encode(['msg'=>'请求成功','code'=>1,'success'=>true,'data'=>$data]);
        exit;
    }
    // 竞猜列表
    public function nba_list($code_id=0)
    {
        $data = db('nba_game')->where('status>1 and end_time>"'.date('Y-m-d H:i:s').'"')->order(['end_time'=>'asc','id'=>'asc'])->select();
        if(!empty($data)){
            $array = array('road_win','home_win','let_score_road_win','let_score_home_win','total_small','total_big');
            foreach ($data as $key => $value) {
                $today = date('Y-m-d',strtotime($data[$key]['end_time'])-43200);// 用结算时间减去12个小时
                $id = $data[$key]['id'];
                $data[$key]['date'] = $today;

                if($code_id == 0){
                    $tz_result = db('nba_game_cate')->field('cate_id,cate_name,cate_code,cate_odds')->where('game_id='.$id)->select();
                    foreach ($tz_result as $k => $val) {
                        // $tz_result[$k]['cate_odds'] = round($tz_result[$k]['cate_odds'],3);
                        if($tz_result[$k]['cate_code'] == 'let_score_home_win'){
                            $tz_result[$k]['cate_name'] .= '('.$data[$key]['let_score'].')';
                        }
                        if($tz_result[$k]['cate_code'] == 'total_small' || $tz_result[$k]['cate_code'] == 'total_big'){
                            $tz_result[$k]['cate_name'] .= '('.$data[$key]['total_score'].')';
                        }
                        if(!in_array($tz_result[$k]['cate_code'], $array)){
                            $differ[] = $tz_result[$k];

                            unset($tz_result[$k]);
                        }
                    }
                    $data[$key]['tz_result'] = $tz_result;
                    $data[$key]['differ'] = $differ;
                    unset($differ);
                }else{
                    $code = db('nba_code')->where('code_pid='.$code_id)->column('code');

                    foreach ($code as $k => $v) {
                        $tz_result[$k] = db('nba_game_cate')->field('cate_id,cate_name,cate_odds')->where('game_id='.$id.' and cate_code="'.$code[$k].'"')->find();
                        if($code[$k] == 'let_score_win_home'){
                            $tz_result[$k]['cate_name'] .= '('.$data[$key]['let_score'].')';
                        }
                        if($code[$k] == 'total_small' || $code[$k] == 'total_big'){
                            $tz_result[$k]['cate_name'] .= '('.$data[$key]['total_score'].')';
                        }
                        $tz_result[$k]['cate_odds'] = round($data[$key]['cate_odds'],3);
                    }
                    $data[$key]['tz_result'] = $tz_result;
                    unset($tz_result);
                }
                
            }
            $count = count($data);
            $game_data = array();
            for ($i=0; $i < $count; $i++) {
                if(isset($data[$i]) && empty($game_data[$i])){
                    $game_data[$i]['head'] = $data[$i]['date'].' '.$data[$i]['week'];
                    $game_data[$i]['data'][] = $data[$i];
                }
                for ($j=$i+1; $j < $count; $j++) {
                    if(isset($data[$i])){
                        if(isset($data[$j])){
                            if($data[$i]['date'] == $data[$j]['date']){
                                $game_data[$i]['head'] = $data[$i]['date'].' '.$data[$i]['week'];
                                $game_data[$i]['data'][] = $data[$j];
                                unset($data[$j]);
                            }else{
                                continue;
                            }
                        }
                    }else{
                        continue;
                    }
                }
            }
            $game_data = array_merge($game_data);
            foreach ($game_data as $key => $value) {
                $game_data[$key]['head'] .= ' '.count($game_data[$key]['data']).'场比赛可投';
            }
            echo json_encode(['msg'=>'请求成功','code'=>1,'success'=>true,'list'=>$game_data]);
            exit;
        }else{
            echo json_encode(['msg'=>'暂无比赛','code'=>2001,'success'=>false,'list'=>[]]);
            exit;
        }
        
    }


}
