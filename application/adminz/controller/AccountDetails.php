<?php
namespace app\adminz\controller;

class AccountDetails extends Base
{
    /**
     * 列表页面
     * @return [type] [description]
     */
    public function index($id=''){
        
        $this->assign('id',$id);

        return view();
    }


    /**
     * 编辑记录
     * @param  integer $id [description]
     * @return [type]          [description]
     */
    public function edit($id = 0){
        if(IS_POST){

            $data = $_REQUEST;

            //删除非数据库字段
            unset($data['__cfduid']);
            unset($data['PHPSESSID']);
            unset($data['Hm_lvt_24b7d5cc1b26f24f256b6869b069278e']);
            unset($data['cf_use_ob']);
            unset($data['Hm_lpvt_24b7d5cc1b26f24f256b6869b069278e']);

            $flag = db("account_details")->update($data);
            if($flag || $flag === 0){
                $this->success("保存成功");
            }
            $this->error("保存失败");
        }
        
        $data = db('account_details')->where("id=".$id)->find();
        $this->assign("data",$data);
        return view();
    }

    /**
     * 获取数据
     * @param  integer $position_id [description]
     * @return [type]               [description]
     */
    public function get_account_details_list(){
        $map = '1=1';
        $where = '';

        $data = $_REQUEST;
    
        if(!empty($data['id'])){
            $map .= ' and user_id ='.$data['id'];
            $where .= ' and user_id ='.$data['id'];
        }
        if(!empty($data['phone'])){
            $map .= ' and phone like "%'.$data['phone'].'%"';
            $where .= ' and phone like "%'.$data['phone'].'%"';
        }
        if(!empty($data['yhid'])){
            $user_id = db('user')->where('yhid like "%'.$data['yhid'].'%"')->value('id');
            $map .= ' and user_id = "'.$user_id.'"';
            $where .= ' and user_id = "'.$user_id.'"';
        }
        if($data['cate']>0){
            $map .= ' and deal_cate='.$data['cate'];
        }
        if($data['pay_cate']>0){
            $map .= ' and pay_status='.$data['pay_cate'];
        }
        if($data['tx_cate']>0){
            $map .= ' and present_status='.$data['tx_cate'];
        }
        if(!empty($data['add_time'])){
            $map .= ' and add_time>="'.$data['add_time'].'"';
            $where .= ' and add_time>="'.$data['add_time'].'"';
        }
        if(!empty($data['end_time'])){
            $map .= ' and add_time<="'.$data['end_time'].'"';
            $where .= ' and add_time<="'.$data['end_time'].'"';
        }
        
        // $user_id_arr = db('user')->where('status=1')->column('id');
        // if(!empty($user_id_arr)){
        //     $user_ids = implode(',', $user_id_arr);
        //     $where .= ' and user_id not in ('.$user_ids.')';
        //     $map .= ' and user_id not in ('.$user_ids.')';
        // }

        $statistics['cz_money'] = db('account_details')->where('deal_cate=1 and pay_status=1'.$where)->sum('deal_money');
        $statistics['tx_money'] = db('account_details')->where('deal_cate=2 and present_status=3'.$where)->sum('deal_money');
        $statistics['tz_money'] = db('account_details')->where('deal_cate=3'.$where)->sum('deal_money');
        $statistics['win_money'] = db('account_details')->where('deal_cate=4'.$where)->sum('deal_money');

        $count = db("account_details")->where($map)->order('id desc')->count();
        $list = db("account_details")->where($map)->order('id desc')->paginate(20,$count);

        // //获取分页
        $page = $list->render();
        // //遍历数据
        $list->each(function($item,$key){
            if($item['deal_cate'] == 1){
                $item['zf_status'] = db('code_info')->where('code_pid=5 and code='.$item['pay_status'])->value('code_name');
            }
            if($item['deal_cate'] == 2){
                $item['p_status'] = db('code_info')->where('code_pid=6 and code='.$item['present_status'])->value('code_name');
            }
            $item['status'] = $item['deal_cate'];
            $item['deal_cate'] = db('code_info')->where('code_pid=3 and code='.$item['deal_cate'])->value('code_name');
            $item['phone'] = db('user')->where('id="'.$item['user_id'].'"')->value('phone');
            $item['yhid'] = db('user')->where('id="'.$item['user_id'].'"')->value('yhid');

            return $item;
        });
        // dump($statistics);die;
        $this->assign("page",$page);
        $this->assign("_list",$list);
        $this->assign("statistics",$statistics);
        $html = $this->fetch("tpl/account_details_list");
        $this->ajaxReturn(['data'=>$html,'code'=>1]);
    }

    /**
     * 编辑指定字段
     * @param  integer $id [description]
     * @return [type]      [description]
     */
    public function account_details_edit_field($id = 0){
        //模块化更新
        // $flag = model('article')->allowField(true)->save($_REQUEST,['article_id'=>$id]);
        $data = $_REQUEST;
        //删除非数据库字段
        unset($data['id']);
        $data['id'] = $id;
        $flag = db("account_details")->update($data);
        ($flag || $flag===0)  && $this->success("保存成功");
        $this->error("保存失败");
    }

    /**
     * 删除数据
     * @param  integer $id [description]
     * @return [type]      [description]
     */
    public function account_details_delete($id = 0){
        $map = array();
        $map['id'] = $id;
        $flag = db('account_details')->where($map)->delete();
        if($flag){
            $this->success("删除成功");
        }
        $this->error('删除失败');
    }
}
