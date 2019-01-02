<?php
namespace app\home\controller;
use app\home\model\Group;
class Index extends Base
{
    public function index()
    {
    	dump($_SERVER);
        // return view();
    }

    public function ads()
    {
    	$list = db('ads')->select();
    	foreach ($list as $key => $value) {
    		$data[$key]['ads_id'] = $list[$key]['ads_id'];
    		$data[$key]['title'] = $list[$key]['title'];
            $data[$key]['pic'] = config('uploads_path.web').'ads/'.$list[$key]['pic'];
    		// $data[$key]['pic'] = 'http://192.168.0.108/cp/public/uploads/ads/'.$list[$key]['pic'];
    	}
    	echo json_encode(['msg'=>'请求成功','code'=>1,'success'=>true,'data'=>$data]);
        exit;
    }

    public function article()
    {
        $list = db('article')->field('article_id,title,pic,add_time')->order('add_time desc')->select();
        foreach ($list as $key => $value) {
            $list[$key]['pic'] = config('uploads_path.web').'article/'.$list[$key]['pic'];
        }
        echo json_encode(['msg'=>'请求成功','code'=>1,'success'=>true,'data'=>$list]);
        exit;
    }

    public function get_order_group()
    {
    	$Group = new Group();
    	$Group->get_group();
    }

    public function version($type,$version)
    {
        $type = strtoupper($type);
        $versions = db('versions')->where('type="'.$type.'"')->find();
        $version = str_replace('.', '', $version);
        $versions['version'] = str_replace('.', '', $versions['version']);
        $data[] = '本次更新内容:';
        $data[] = '1.';
        $data[] = '2.';
        $data[] = '3.';
        $data[] = '4.';
        $data[] = '5.';

        if($versions['version'] != $version){
            echo json_encode(['msg'=>'请更新','code'=>1,'success'=>true,'download'=>$versions['download'],'data'=>$data]);
            exit;
        }else{
            echo json_encode(['msg'=>'不用更新了','code'=>0,'success'=>false]);
            exit;
        }
    }
}
