<?php
namespace app\home\controller;

class GameCate extends Base
{
    public function index()
    {
        $data = db('game_type')->select();
        foreach ($data as $key => $value) {
            $data[$key] = db('game_cate')->where('status=1 and type_id='.$data[$key]['type_id'])->select();
        }
        echo json_encode(['msg'=>'请求成功','code'=>1,'success'=>true,'data'=>$data]);
        exit;
    }
}
