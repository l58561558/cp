<?php
namespace app\football\controller;
use app\football\model\Group;
use app\home\controller\Base; 
use think\Db;

// fb比赛竞猜
class Football extends Base
{
    public $game_id = 1;
    // 玩法列表
    public function fb_code()
    {
        $data = db('fb_code')->field('id,code_name')->where('code_pid=0')->select();
        $arr['id'] = 0;
        $arr['code'] = 'all';
        $arr['code_name'] = '混合投注';
        $data[] = $arr;
        foreach ($data as $key => $value) {
            $data[$key]['code_id'] = $data[$key]['id'];
            unset($data[$key]['id']);
        }
        echo json_encode(['msg'=>$data,'code'=>1,'success'=>true]);
        exit;
    }

    // 竞猜列表
    public function fb_list($code_id=0)
    {
        $data = db('fb_game')->where('status>1 and end_time>"'.date('Y-m-d H:i:s').'"')->order(['end_time'=>'asc','id'=>'asc'])->select();
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $today = date('Y-m-d',strtotime($data[$key]['end_time'])-43200);// 用结算时间减去12个小时
                $id = $data[$key]['id'];
                $data[$key]['date'] = $today;

                if($code_id == 0){
                    // $code = db('fb_code')->where('code_pid=0')->column('id');
                    // foreach ($code as $ke => $val) {
                    //     $code_data[$ke] = db('fb_code')->where('code_pid='.$code[$ke])->column('code');
                    //     foreach ($code_data[$ke] as $k => $v) {
                    //         $code_arr[$k] = db('fb_game_cate')->field('cate_id,cate_name,cate_odds')->where('game_id='.$id.' and cate_code="'.$code_data[$ke][$k].'"')->find();
                    //         if($code[$ke] == 2){
                    //             $code_arr[$k]['cate_name'] = $code_arr[$k]['cate_name'].'('.$data[$key]['let_score'].')';
                    //         }
                    //     }
                    //     $data[$key]['tz_result'][] = $code_arr;
                    //     unset($code_arr);
                    // }

                    $data[$key]['tz_result'][] = db('fb_game_cate')->field('cate_id,cate_name,cate_odds')->where('game_id='.$id)->limit(0,6)->select();
                    $data[$key]['tz_result'][] = db('fb_game_cate')->field('cate_id,cate_name,cate_odds')->where('game_id='.$id)->limit(6,31)->select();
                    $data[$key]['tz_result'][] = db('fb_game_cate')->field('cate_id,cate_name,cate_odds')->where('game_id='.$id)->limit(37,8)->select();
                    $data[$key]['tz_result'][] = db('fb_game_cate')->field('cate_id,cate_name,cate_odds')->where('game_id='.$id)->limit(45,9)->select();
                    $data[$key]['tz_result'][0][0]['score'] = 0;
                    $data[$key]['tz_result'][0][1]['score'] = 0;
                    $data[$key]['tz_result'][0][2]['score'] = 0;
                    $data[$key]['tz_result'][0][3]['score'] = $data[$key]['let_score'];
                    $data[$key]['tz_result'][0][4]['score'] = $data[$key]['let_score'];
                    $data[$key]['tz_result'][0][5]['score'] = $data[$key]['let_score'];
                }else{
                    $code = db('fb_code')->where('code_pid='.$code_id)->column('code');

                    foreach ($code as $k => $v) {
                        $tz_result[$k] = db('fb_game_cate')->field('cate_id,cate_name,cate_odds')->where('game_id='.$id.' and cate_code="'.$code[$k].'"')->find();
                        if($code_id == 1){
                            $tz_result[$k]['score'] = 0;
                        }
                        if($code_id == 2){
                            $tz_result[$k]['score'] = $data[$key]['let_score'];
                        }
                        // $tz_result[$k]['cate_odds'] = round($data[$key]['cate_odds'],2);
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
            echo json_encode(['msg'=>'暂无比赛','code'=>1001,'success'=>false,'list'=>[]]);
            exit;
        }
        
    }

}
