<?php
namespace app\adminz\controller;

// 销售管理
class Sell extends Base
{
    /**
     * 列表页面
     * @return [type] [description]
     */
    public function detail($id=''){
        $map = "";
        $yhid = '';
        if(!empty($id)){
            $map = 'id='.$id;
            $yhid = db('user')->where($map)->value('yhid');
        }
        
        $this->assign('id',$yhid);

        return view();
    }

    // 人员列表
    public function personnel()
    {
        $admin = session(config('admin_site.session_name'));
        $role_id = $admin['role_id'];
        if($role_id > 1){
            $user_id = db('user u')
            ->field('u.id,u.yhid,u.phone,u.pid,u.level,u.last_login_time,u.amount_money')
            ->join('admin a','u.phone=a.mobile','LEFT')
            ->where('a.id='.$admin['id'])
            ->value('u.id');
        }else{
            $user_id = 0;
        }
        $user = db('user')
        ->field('id,yhid,phone,pid,level,last_login_time,amount_money')
        ->where('level<3')
        ->select();

        $data = get_data($user,$user_id);

        $array = array(1=>'主管',2=>'销售',3=>'用户');
        $today_where = ' and add_time>"'.date('Y-m-d H:i:s', strtotime(date('Y-m-d', time()))).'"';
        $seven_where = ' and add_time>"'.date('Y-m-d H:i:s', time()-10080).'"';
        $month_where = ' and add_time>"'.date('Y-m-d H:i:s', time()-43200).'"';
        if(!empty($data)){
            $data_money = 0;
            foreach ($data as $key => $value) {
                $data[$key]['identity'] = $array[$data[$key]['level']];
                $data[$key]['order_money'] = db('order')->where('user_id ='.$data[$key]['id'])->sum('order_money');
                $data[$key]['today_order_money'] = db('order')->where('user_id ='.$data[$key]['id'].$today_where)->sum('order_money');
                $data[$key]['seven_order_money'] = db('order')->where('user_id ='.$data[$key]['id'].$seven_where)->sum('order_money');
                $data[$key]['month_order_money'] = db('order')->where('user_id ='.$data[$key]['id'].$month_where)->sum('order_money');

                if(!empty($data[$key]['child'])){
                    $data_child = $data[$key]['child'];
                    $data_child_money = 0;
                    $today_data_child_money = 0;
                    $seven_data_child_money = 0;
                    $month_data_child_money = 0;
                    foreach ($data_child as $ke => $val) {
                        $data[$key]['child'][$ke]['identity'] = $array[$data_child[$ke]['level']];
                        $data[$key]['child'][$ke]['order_money'] = db('order')->where('user_id ='.$data_child[$ke]['id'])->sum('order_money');
                        $data[$key]['child'][$ke]['today_order_money'] = db('order')->where('user_id ='.$data_child[$ke]['id'].$today_where)->sum('order_money');
                        $data[$key]['child'][$ke]['seven_order_money'] = db('order')->where('user_id ='.$data_child[$ke]['id'].$seven_where)->sum('order_money');
                        $data[$key]['child'][$ke]['month_order_money'] = db('order')->where('user_id ='.$data_child[$ke]['id'].$month_where)->sum('order_money');
                        
                        if(!empty($data_child[$ke]['child'])){
                            $data_cchild = $data_child[$ke]['child'];
                            $data_cchild_money = 0;
                            $today_data_cchild_money = 0;
                            $seven_data_cchild_money = 0;
                            $month_data_cchild_money = 0;
                            foreach ($data_cchild as $k => $v) {
                                $data[$key]['child'][$ke]['child'][$k]['identity'] = $array[$data_cchild[$k]['level']];
                                $data[$key]['child'][$ke]['child'][$k]['order_money'] = db('order')->where('user_id ='.$data_cchild[$k]['id'])->sum('order_money');
                                $data[$key]['child'][$ke]['child'][$k]['today_order_money'] = db('order')->where('user_id ='.$data_cchild[$k]['id'].$today_where)->sum('order_money');
                                $data[$key]['child'][$ke]['child'][$k]['seven_order_money'] = db('order')->where('user_id ='.$data_cchild[$k]['id'].$seven_where)->sum('order_money');
                                $data[$key]['child'][$ke]['child'][$k]['month_order_money'] = db('order')->where('user_id ='.$data_cchild[$k]['id'].$month_where)->sum('order_money');
                                $data_cchild_money += $data[$key]['child'][$ke]['child'][$k]['order_money'];
                                $today_data_cchild_money += $data[$key]['child'][$ke]['child'][$k]['today_order_money'];
                                $seven_data_cchild_money += $data[$key]['child'][$ke]['child'][$k]['seven_order_money'];
                                $month_data_cchild_money += $data[$key]['child'][$ke]['child'][$k]['month_order_money'];
                            }
                            $data[$key]['child'][$ke]['order_money'] = $data_cchild_money;
                            $data[$key]['child'][$ke]['today_order_money'] = $today_data_cchild_money;
                            $data[$key]['child'][$ke]['seven_order_money'] = $seven_data_cchild_money;
                            $data[$key]['child'][$ke]['month_order_money'] = $month_data_cchild_money;
                        }
                        $data_child_money += $data[$key]['child'][$ke]['order_money'];
                        $today_data_child_money += $data[$key]['child'][$ke]['today_order_money'];
                        $seven_data_child_money += $data[$key]['child'][$ke]['seven_order_money'];
                        $month_data_child_money += $data[$key]['child'][$ke]['month_order_money'];
                    }
                    $data[$key]['order_money'] += $data_child_money;
                    $data[$key]['today_order_money'] += $today_data_child_money;
                    $data[$key]['seven_order_money'] += $seven_data_child_money;
                    $data[$key]['month_order_money'] += $month_data_child_money;
                }
            }
        }
        
        // echo json_encode(['data'=>$data]);
        // die;
        $this->assign('_list',$data);
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

        if(!empty($data['phone'])){
            $yhid = db('user')->where('phone like "%'.phone_encrypt($data['phone']).'%"')->value('yhid');
            $map .= ' and yhid = "'.$yhid.'"';
            $where .= ' and yhid = "'.$yhid.'"';
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
            $map .= ' and add_time>='.$data['add_time'];
            $where .= ' and add_time>='.$data['add_time'];
        }
        if(!empty($data['end_time'])){
            $map .= ' and add_time<='.$data['end_time'];
            $where .= ' and add_time<='.$data['end_time'];
        }

        if(!empty($data['end_time'])){
            $map .= ' and add_time<='.$data['end_time'];
            $where .= ' and add_time<='.$data['end_time'];
        }
        
        $user_id_arr = db('user')->where('status=1')->column('yhid');
        foreach ($user_id_arr as $key => $value) {
            $where .= ' and yhid != "'.$user_id_arr[$key].'"';
            $map .= ' and yhid != "'.$user_id_arr[$key].'"';
        }
        
        $statistics['cz_money'] = db('account_details')->where('deal_cate=1 and pay_status=1'.$where)->sum('deal_money');
        $statistics['tx_money'] = db('account_details')->where('deal_cate=2 and present_status=3'.$where)->sum('deal_money');
        $statistics['tz_money'] = db('account_details')->where('deal_cate=3'.$where)->sum('deal_money');
        $statistics['win_money'] = db('account_details')->where('deal_cate=4'.$where)->sum('deal_money');

        $count = db("account_details")->where($map)->order('id desc')->count();
        $list = db("account_details")->where($map)->order('id desc')->paginate(20,$count);

        //获取分页
        $page = $list->render();
        //遍历数据
        $list->each(function($item,$key){
            if($item['deal_cate'] == 1){
                $item['zf_status'] = db('code_info')->where('code_pid=5 and code='.$item['pay_status'])->value('code_name');
            }
            if($item['deal_cate'] == 1){
                $item['p_status'] = db('code_info')->where('code_pid=6 and code='.$item['present_status'])->value('code_name');
            }
            $item['status'] = $item['deal_cate'];
            $item['deal_cate'] = db('code_info')->where('code_pid=3 and code='.$item['deal_cate'])->value('code_name');
            $item['phone'] = db('user')->where('id="'.$item['user_id'].'"')->value('phone');
            $item['yhid'] = db('user')->where('id="'.$item['user_id'].'"')->value('yhid');

            return $item;
        });
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
