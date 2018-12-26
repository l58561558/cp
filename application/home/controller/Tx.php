<?php
namespace app\home\controller;
use think\Db;
//用户提现控制器
class Tx extends Base
{
  	// 个人中心--提现
  	/*
  	*user_id  int      用户ID
  	*money    floot    提现金额
  	*/
    public function tixian(){
        // 开启事务
        Db::startTrans();

        $data = $_REQUEST;

        $user = db('user')->where('id',$data['user_id'])->find();

        if($user['is_band'] == 0){
            echo json_encode(['msg'=>'该用户未实名认证','code'=>110001,'success'=>false]);
            exit;
        }

        if($user['balance'] - $data['money'] < 0){
            echo json_encode(['msg'=>'申请失败,余额不足','code'=>110002,'success'=>false]);
            exit;
        }
        if($data['money'] < 10){
            echo json_encode(['msg'=>'申请失败,提现金额必须大于10','code'=>110003,'success'=>false]);
            exit;
        }


        db('user')->where('id='.$data['user_id'])->setInc('freezing_amount',$data['money']);
        db('user')->where('id='.$data['user_id'])->setDec('balance',$data['money']);
        db('user')->where('id='.$data['user_id'])->setDec('amount_money',$data['money']);

        $count = db('tix')->count();
        $withdraw_no = date('YmdHis').$data['user_id'].mt_rand(1000,9999);

        $arr['user_id'] = $data['user_id'];
        $arr['withdraw_no'] = $withdraw_no;
        $arr['withdraw_money'] = $data['money'];
        $arr['add_time'] = date('Y-m-d H:i:s');
        $arr['withdraw_status'] = 1;

        $res = db('tix')->insert($arr,false,true);

        if($res>0){
            // 添加明细列表
            $detail['user_id'] = $data['user_id'];
            $detail['deal_cate'] = 2;
            $detail['status'] = 2;
            $detail['deal_money'] = $data['money'];
            $detail['new_money'] = $user['amount_money']-$data['money'];
            $detail['tx_id'] = $withdraw_no;
            $detail['add_time'] = date('Y-m-d H:i:s');
            $detail['present_status'] = 1;
            $detail_id = db('account_details')->insertGetId($detail);

            if($detail_id > 0){
            	// 提交事务
            	Db::commit();
            	echo json_encode(['msg'=>'申请成功,请等待管理员审核','code'=>1,'success'=>true]);
            	exit;
            }else{
            	// 回滚事务
            	Db::rollback();
            	echo json_encode(['msg'=>'申请失败,请重新操作','code'=>110004,'success'=>false]);
            	exit;
            }
        }else{
        	// 回滚事务
            Db::rollback();
            echo json_encode(['msg'=>'申请失败,请重新操作','code'=>110004,'success'=>false]);
            exit;
        }

    }

    // 取消提现
    public function del_tixian()
    {
        // 开启事务
        Db::startTrans();
        $data = $_REQUEST;

        $tx = db('tix')->where('withdraw_no="'.$data['withdraw_no'].'"')->find();

        if($tx['withdraw_status'] > 1){
            echo json_encode(['msg'=>'取消失败，管理员已操作','code'=>0,'success'=>true]);
            exit; 
        }
        $tix = db('tix')->where('withdraw_no="'.$data['withdraw_no'].'"')->setField('withdraw_status',2);
        $account_details = db('account_details')->where('withdraw_no="'.$data['withdraw_no'].'"')->setField('present_status',2);

        if($tix > 0 && $account_details > 0){
            db('user')->where('id='.$data['user_id'])->setDec('freezing_amount',$tx['Txdjje']);
            db('user')->where('id='.$data['user_id'])->setInc('balance',$tx['Txdjje']);
            db('user')->where('id='.$data['user_id'])->setInc('amount_money',$money);
            // 提交事务
        	Db::commit();
            echo json_encode(['msg'=>'取消成功','code'=>1,'success'=>true]);
            exit; 
        }else{
        	// 回滚事务
            Db::rollback();
            echo json_encode(['msg'=>'操作失败','code'=>0,'success'=>false]);
            exit; 
        }
    }

}
