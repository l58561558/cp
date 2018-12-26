<?php
namespace app\home\controller;

//明细列表控制器
class AccountDetails extends Base
{

    public function detail($user_id, $deal_cate=0, $page=1, $count=10)
    {
        $start = ($page-1)*$count;
    	$where = '1=1';
    	if($deal_cate > 0){
    		$where = 'deal_cate ='.$deal_cate;
    	}
    	if($page > 1){
    		$page = ($page-1)*$count;
    	}
    	$where .= ' and user_id='.$user_id;
    	$data = db('account_details')->field('id,deal_cate,deal_money,add_time,status,pay_status,present_status')->where($where)->limit($start,$count)->order('add_time','desc')->select();
    	if(!empty($data)){
    		foreach ($data as $key => $value) {
                $data[$key]['add_time'] = $data[$key]['add_time'];
                // if($data[$key]['deal_cate'] == 1){
                //     $data[$key]['pay_status'] = db('code_info')->where('code_pid=5 and code='.$data[$key]['pay_status'])->value('code_name');
                // }
                // if($data[$key]['deal_cate'] == 2){
                //     $data[$key]['present_status'] = db('code_info')->where('code_pid=6 and code='.$data[$key]['present_status'])->value('code_name');
                // }
	    	}
	    	echo json_encode(['msg'=>'请求成功','code'=>1,'success'=>true,'data'=>$data]);
	    	exit;	
    	}else{
    		echo json_encode(['msg'=>'没有了','code'=>90001,'success'=>false,'data'=>[]]);
	    	exit;	
    	}
    }
}
