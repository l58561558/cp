<?php
namespace app\adminz\controller;
use think\Db;
// 销售管理
class Sell extends Base
{
    // 人员列表
    public function personnel()
    {
        $admin = session(config('admin_site.session_name'));
        $role_id = $admin['role_id'];
        $this->assign("role_id",$role_id);
        return view();
    }

    /**
     * 获取数据
     * @param  integer $position_id [description]
     * @return [type]               [description]
     */
    public function get_sell_list(){
        $data = $_REQUEST;
        $order_where = '';
        $map = '';
        if(!empty($data['add_time'])){
            $order_where .= ' and add_time>="'.$data['add_time'].'"';
            $map .= ' and reg_time>="'.$data['add_time'].'"';
        }
        if(!empty($data['end_time'])){
            $order_where .= ' and add_time<="'.$data['end_time'].'"';
            $map .= ' and reg_time<="'.$data['end_time'].'"';
        }

        $admin = session(config('admin_site.session_name'));
        $role_id = $admin['role_id'];
        if($role_id == 1){
            $head_data['today_order_money'] = 0;
            $head_data['seven_order_money'] = 0;
            $head_data['month_order_money'] = 0;
            $head_data['order_money'] = 0;

            $array = array(1=>'主管',2=>'销售',3=>'用户');
            $today_where = ' and add_time>"'.date('Y-m-d H:i:s', strtotime(date('Y-m-d', time()))).'"';
            $seven_where = ' and add_time>"'.date('Y-m-d H:i:s', strtotime(date('Y-m-d', time()-604800))).'"';
            $month_where = ' and add_time>"'.date('Y-m-d H:i:s', strtotime(date('Y-m-d', time()-2592000))).'"';

            $user_1 = db('user')
            ->field('id,yhid,user_name,phone,pid,level,last_login_time')
            ->order(['id'=>'asc'])
            ->where('level=1 and status=0')
            ->select();
            for ($i=0; $i < count($user_1); $i++) { 
                $user_1[$i]['identity'] = $array[$user_1[$i]['level']];
                /*********** 主管自己的投注 ************/
                $user_1[$i]['today_order_money'] = db('order')->where('order_status=1 and user_id='.$user_1[$i]['id'].$today_where)->sum('order_money');
                $user_1[$i]['seven_order_money'] = db('order')->where('order_status=1 and user_id='.$user_1[$i]['id'].$seven_where)->sum('order_money');
                $user_1[$i]['month_order_money'] = db('order')->where('order_status=1 and user_id='.$user_1[$i]['id'].$month_where)->sum('order_money');
                $user_1[$i]['order_money']       = db('order')->where('order_status=1 and user_id='.$user_1[$i]['id'])->sum('order_money');
                /*********** 主管下面用户的投注 ************/
                $user_1_child = db('user')
                ->order(['id'=>'asc'])
                ->where('level=3 and status=0 and pid='.$user_1[$i]['id'])
                ->column('id');
                if(!empty($user_1_child)){

                $user_1_child = implode(',', $user_1_child);

                $user_1[$i]['today_order_money'] += db('order')->where('order_status=1 and user_id in ('.$user_1_child.')'.$today_where)->sum('order_money');
                $user_1[$i]['seven_order_money'] += db('order')->where('order_status=1 and user_id in ('.$user_1_child.')'.$seven_where)->sum('order_money');
                $user_1[$i]['month_order_money'] += db('order')->where('order_status=1 and user_id in ('.$user_1_child.')'.$month_where)->sum('order_money');
                $user_1[$i]['order_money']       += db('order')->where('order_status=1 and user_id in ('.$user_1_child.')')->sum('order_money');

                }
                $head_data['today_order_money'] += $user_1[$i]['today_order_money'];
                $head_data['seven_order_money'] += $user_1[$i]['seven_order_money'];
                $head_data['month_order_money'] += $user_1[$i]['month_order_money'];
                $head_data['order_money']       += $user_1[$i]['order_money'];
                /*********** 主管下面用户的投注 END ************/

                /*********** 主管下面的销售 ************/
                $user_2 = db('user')
                ->field('id,yhid,user_name,phone,pid,level,last_login_time')
                ->order(['id'=>'asc'])
                ->where('level=2 and status=0 and pid='.$user_1[$i]['id'])
                ->select();
                for ($j=0; $j < count($user_2); $j++) { 
                    $user_2[$j]['identity'] = $array[$user_2[$j]['level']];
                    /*********** 主管下面销售的自己的投注 ************/
                    $user_2[$j]['today_order_money'] = db('order')->where('order_status=1 and user_id='.$user_2[$j]['id'].$today_where)->sum('order_money');
                    $user_2[$j]['seven_order_money'] = db('order')->where('order_status=1 and user_id='.$user_2[$j]['id'].$seven_where)->sum('order_money');
                    $user_2[$j]['month_order_money'] = db('order')->where('order_status=1 and user_id='.$user_2[$j]['id'].$month_where)->sum('order_money');
                    $user_2[$j]['order_money']       = db('order')->where('order_status=1 and user_id='.$user_2[$j]['id'])->sum('order_money');

                    /*********** 主管下面销售的用户的投注 ************/
                    $user_2_child = db('user')
                    ->field('id,yhid,phone,pid,level,last_login_time')
                    ->order(['id'=>'asc'])
                    ->where('level=3 and status=0 and pid='.$user_2[$j]['id'])
                    ->column('id');
                    if(!empty($user_2_child)){

                    $user_2_child = implode(',', $user_2_child);

                    $user_2[$j]['today_order_money'] += db('order')->where('order_status=1 and user_id in ('.$user_2_child.')'.$today_where)->sum('order_money');
                    $user_2[$j]['seven_order_money'] += db('order')->where('order_status=1 and user_id in ('.$user_2_child.')'.$seven_where)->sum('order_money');
                    $user_2[$j]['month_order_money'] += db('order')->where('order_status=1 and user_id in ('.$user_2_child.')'.$month_where)->sum('order_money');
                    $user_2[$j]['order_money']       += db('order')->where('order_status=1 and user_id in ('.$user_2_child.')')->sum('order_money');
                        
                    }
                    $head_data['today_order_money'] += $user_2[$j]['today_order_money'];
                    $head_data['seven_order_money'] += $user_2[$j]['seven_order_money'];
                    $head_data['month_order_money'] += $user_2[$j]['month_order_money'];
                    $head_data['order_money']       += $user_2[$j]['order_money'];
                }
                $user_1[$i]['child'] = $user_2;
                /*********** 主管下面的销售 END ************/
            }
            $data = $user_1;

            $fetch = 'sell/personnel_copy';
        }else{
            if($role_id == 2){
                $user_id = db('user u')
                ->join('admin a','u.phone=a.mobile','LEFT')
                ->where('a.id='.$admin['id'])
                ->value('u.id');
            }else if($role_id == 3){
                $pid = db('admin')->where('id='.$admin['id'])->value('pid');
                $user_id = db('user u')
                ->join('admin a','u.phone=a.mobile','LEFT')
                ->where('a.id='.$pid)
                ->value('u.id');
            }

            $where = 'level != 3 and pid='.$user_id;
            $array = array(1=>'主管',2=>'销售',3=>'用户');
            $data = db('user')
            ->field('id,yhid,user_name,phone,pid,level,last_login_time,amount_money')
            ->where($where)
            ->select();

            $reg_time = date('Y-m-d H:i:s', strtotime(date('Y-m-d')));

            $head_data['reg_user'] = 0;
            $head_data['self_money'] = 0;
            $head_data['hm_money'] = 0;
            $head_data['total_money'] = 0;

            foreach ($data as $key => $value) {
                $self_money = 0;
                $hm_money = 0;
                $data[$key]['identity'] = $array[$data[$key]['level']]; // 身份
                $data[$key]['reg_user'] = db('user')->where('level=3 and pid='.$data[$key]['id'].$map)->count();
                $user_ids = db('user')->where('level=3 and pid='.$data[$key]['id'])->column('id');
                for ($i=0; $i < count($user_ids); $i++) { 
                    $self_money += db('order')->where('order_type=1 and order_status=1 and user_id='.$user_ids[$i].$order_where)->sum('order_money');
                    $hm_money += db('order')->where('order_type=2 and order_status=1 and user_id='.$user_ids[$i].$order_where)->sum('order_money');
                }
                $data[$key]['self_money'] = $self_money + db('order')->where('order_type=1 and order_status=1 and user_id='.$data[$key]['id'].$order_where)->sum('order_money');
                $data[$key]['hm_money'] = $hm_money + db('order')->where('order_type=2 and order_status=1 and user_id='.$data[$key]['id'].$order_where)->sum('order_money');
                $data[$key]['total_money'] = $data[$key]['self_money'] + $data[$key]['hm_money'];

                $head_data['reg_user'] += $data[$key]['reg_user'];
                $head_data['self_money'] += $data[$key]['self_money'];
                $head_data['hm_money'] += $data[$key]['hm_money'];
                $head_data['total_money'] += $data[$key]['total_money'];
            }

            $last_names = array_column($data,'total_money');
            array_multisort($last_names,SORT_DESC,$data);
            $fetch = "sell/sell_list";
            
        }

        $this->assign("head_data",$head_data);
        $this->assign("_list",$data);
        $this->assign("role_id",$role_id);
        $html = $this->fetch($fetch);
        $this->ajaxReturn(['data'=>$html,'code'=>1]);
    }

    public function user()
    {
        $admin = session(config('admin_site.session_name'));
        $role_id = $admin['role_id'];
        if($role_id > 1){
            $invite_code = db('user u')
            ->field('u.id,u.user_name,u.yhid,u.phone,u.pid,u.level,u.last_login_time,u.amount_money')
            ->join('admin a','u.phone=a.mobile','LEFT')
            ->where('a.id='.$admin['id'])
            ->value('u.invite_code');
            $this->assign('invite_code',$invite_code);
        }
        return view();
    }

    public function get_user_list()
    {
        $data = $_REQUEST;
        $order_where = '';
        $map = '';
        if(!empty($data['add_time'])){
            $order_where .= ' and add_time>="'.$data['add_time'].'"';
            $map .= ' and reg_time>="'.$data['add_time'].'"';
        }
        if(!empty($data['end_time'])){
            $order_where .= ' and add_time<="'.$data['end_time'].'"';
            $map .= ' and reg_time<="'.$data['end_time'].'"';
        }

        $admin = session(config('admin_site.session_name'));
        $role_id = $admin['role_id'];
        if($role_id > 1){
            $user_id = db('user u')
            ->join('admin a','u.phone=a.mobile','LEFT')
            ->where('a.id='.$admin['id'])
            ->value('u.id');
        }else{
            $user_id = 0;
        }
        $where = 'level = 3 and pid='.$user_id;
        $array = array(1=>'主管',2=>'销售',3=>'用户');
        $data = db('user')
        ->field('id,yhid,user_name,phone,pid,level,last_login_time,amount_money,reg_time')
        ->where($where.' and status=0')
        ->order('reg_time','desc')
        ->select();

        $total['reg_user'] = db('user')->where('level=3 and pid='.$user_id.$map)->count();
        $total['self_money'] = 0;
        $total['hm_money'] = 0;
        $total['total_money'] = 0;
        $reg_time = date('Y-m-d H:i:s', strtotime(date('Y-m-d')));
        foreach ($data as $key => $value) {
            $data[$key]['reg_user'] = db('user')->where('status=0 and level=3 and pid='.$data[$key]['id'].$map)->count();
            $data[$key]['self_money'] = db('order')->where('game_cate=1 and user_id='.$data[$key]['id'].$order_where)->sum('order_money');
            $data[$key]['hm_money'] = db('order')->where('game_cate=2 and user_id='.$data[$key]['id'].$order_where)->sum('order_money');
            $data[$key]['total_money'] = $data[$key]['self_money'] + $data[$key]['hm_money'];
            
            $total['self_money'] += $data[$key]['self_money'];
            $total['hm_money'] += $data[$key]['hm_money'];
            $total['total_money'] += $data[$key]['total_money'];
        }

        $this->assign("_list",$data);
        $this->assign("total",$total);
        $html = $this->fetch("sell/user_list");
        $this->ajaxReturn(['data'=>$html,'code'=>1]);
    }

    public function order($id)
    {
        $this->assign('user_id',$id);
        return view();
    }

    /**
     * 获取数据
     * @param  integer $position_id [description]
     * @return [type]               [description]
     */
    public function get_order_list(){
        $map = '1=1';
        $data = $_REQUEST;
        if($data['result']['user_id'] > 0){
            $map .= ' and user_id='.$data['result']['user_id'];
        }
        if(!empty($data['result']['add_time'])){
            $map .= ' and add_time>='.$data['result']['add_time'];
        }
        if(!empty($data['result']['end_time'])){
            $map .= ' and add_time<='.$data['result']['end_time'];
        }
        $count = db("order")->where($map)->order('add_time desc')->count();
        $list = db("order")->where($map)->order('add_time desc')->paginate(20,$count);
        if(!empty($list)){
            $list->each(function($item,$key){
                // $item['is_win'] = db('code_info')->where('code_pid=1 and code='.$item['is_win'])->value('code_name');
                $item['game_cate'] = db('game_cate')->where('game_id='.$item['game_cate'])->value('game_name');
                $item['order_status'] = db('code_info')->where('code_pid=2 and code='.$item['order_status'])->value('code_name');
                $user_id = explode(',', $item['user_id']);
                for ($i=0; $i < count($user_id); $i++) { 
                    $user_ids[$i] = db('user')->where('id='.$user_id[$i])->value('yhid');
                }
                $item['yhid'] = implode(',', $user_ids);
                return $item;
            });
        }
        //获取分页
        $page = $list->render();

        $this->assign("page",$page);
        $this->assign("_list",$list);
        $html = $this->fetch("sell/order_list");
        $this->ajaxReturn(['data'=>$html,'code'=>1]);
    }

    public function add(){
        if(IS_POST){
            // 开启事务
            Db::startTrans();

            $data = $_REQUEST;
            $arr['invite_code'] = $data['invite_code'];
            unset($data['invite_code']);
            $admin = session(config('admin_site.session_name'));
            $map=array();
            $map['login_name']= $data['login_name'];
            $admins = db("Admin")->where($map)->find();
            if($admins) $this->error('用户名已存在!');

            $data['login_pass'] = md5($data['login_pass']);
            $data['add_time'] = time();
            $data['status'] = 0;
            $data['pid'] = $admin['id'];
            $data['role_id'] = 3;

            $user = db('user')->where("phone='".$data['mobile']."'")->find();
            $mobile = db('Admin')->where("id='".$admin['id']."'")->value('mobile');
            $p_user_id = db('user')->where("phone='".$mobile."'")->value('id');
            if(!empty($user)){
                $this->error('账号已存在!');
            }
            $key = GetRandStr();// 4位秘钥
            $reg_time = date('Y-m-d H:i:s');
            
            $arr['pid'] = $p_user_id;
            $arr['phone'] = $data['mobile'];
            $arr['key'] = $key;
            $arr['pswd'] = md5($data['login_pass'].$key);
            $arr['reg_time'] = $reg_time;
            $arr['user_name'] = $data['nickname'];
            $arr['level'] = 2;
            $arr['status'] = 0;
            $user_id = db("user")->insertGetId($arr); 
            $yhid = 'YH'.(100000+$user_id);
            db('user')->where('id='.$user_id)->setField('yhid',$yhid);   
            
            $id = db("admin")->insert($data,false,true);

            if($id){
                // 提交事务
                Db::commit();
                $this->success("添加成功");
            }else{
                // 回滚事务
                Db::rollback();
                $this->error("添加失败");
            }
            
        }
        $p_user = db('admin')->where('role_id=2')->select();
        $role_list = db("role")->select();
        return view();
    }
}
