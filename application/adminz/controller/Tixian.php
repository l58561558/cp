<?php
namespace app\adminz\controller;
use think\Db;
class Tixian extends Base
{
    /**
     * 列表页面
     * @return [type] [description]
     */
    public function index(){
        return view();
    }


    // 确认提现
    public function succeed($id,$user_id,$status=3){
        // $this->error('维护中');
        // 开启事务
        Db::startTrans();

        $tx = db('tix')->where('id='.$id)->find();
        if($tx['withdraw_status'] == 2 || $tx['withdraw_status'] == 4){
            $this->error('用户已取消提现');
        }
        $tix_money = db('tix')->where('id='.$id)->value('withdraw_money');

        $user_res = db('user')->where("id='".$user_id."'")->setDec('freezing_amount',$tix_money);
        $account_details_res = db('account_details')->where("tx_id='".$tx['withdraw_no']."'")->setField('present_status',$status);

        $arr['withdraw_status'] = $status;
        $arr['edit_time'] = date('Y-m-d H:i:s');

        $tix_res = db('tix')->where('id='.$id)->update($arr);

        if($user_res>0 && $account_details_res>0 && $tix_res>0){
            // 提交事务
            Db::commit();
            $this->success("操作成功");
        }else{
            // 回滚事务
            Db::rollback();
            $this->error('操作失败');
        }
        

    }

    // 拒绝提现
    public function refuse($id,$user_id,$status=4){
        // $this->error('维护中');
        // 开启事务
        Db::startTrans();

        $tx = db('tix')->where('id='.$id)->find();
        if($tx['withdraw_status'] == 2 || $tx['withdraw_status'] == 4){
            $this->error('用户已取消提现');
        }
        $tix_money = db('tix')->where('id='.$id)->value('withdraw_money');

        db('user')->where("id='".$user_id."'")->setInc('balance',$tix_money);
        db('user')->where("id='".$user_id."'")->setInc('amount_money',$tix_money);
        db('user')->where("id='".$user_id."'")->setDec('freezing_amount',$tix_money);
        db('account_details')->where("tx_id='".$tx['withdraw_no']."'")->setField('present_status',$status);

        $arr['withdraw_status'] = $status;
        $arr['edit_time'] = date('Y-m-d H:i:s');

        $tix_res = db('tix')->where('id='.$id)->update($arr);

        if($tix_res){
            // 提交事务
            Db::commit();

            $this->success("操作成功");
        }else{
            // 回滚事务
            Db::rollback();
            $this->error('操作失败');
        }
    }


    /**
     * 获取数据
     * @param  integer $position_id [description]
     * @return [type]               [description]
     */
    public function get_tixian_list(){
        $map = '';

        $data = $_REQUEST;

        if(!empty($data['id'])){
            $map .= 'id='.$data['id'];
        }

        $count = db("tix")->where($map)->order('add_time desc')->count();
        $list = db("tix")->where($map)->order('add_time desc')->paginate(20,$count);

        //获取分页
        $page = $list->render();
        //遍历数据
        $list->each(function($item,$key){
            $user = db('user')->where('id="'.$item['user_id'].'"')->find();
            $item['status'] = $item['withdraw_status'];
            $item['withdraw_status'] = db('code_info')->where('code_pid=6 and code='.$item['withdraw_status'])->value('code_name');
            $item['name'] = $user['name'];
            $item['bank_card'] = $user['bank_card'];
            $item['yhid'] = $user['yhid'];
            $item['user_id'] = $user['id'];
            if($item['edit_time'] == '0000-00-00 00:00:00'){
                $item['edit_time'] = '';
            }
            return $item;
        });
        $this->assign("page",$page);
        $this->assign("_list",$list);
        $html = $this->fetch("tpl/tixian_list");
        $this->ajaxReturn(['data'=>$html,'code'=>1]);
    }
}
