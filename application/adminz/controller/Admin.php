<?php
namespace app\adminz\controller;
use think\Db;

class Admin extends Base
{
    public function index(){
        return view();
    }

    public function root($id){
        $role_id = db('admin')->where('id='.$id)->value('role_id');
        $list = db('role_auth')->where('role_id='.$role_id)->select();
        // dump($list);die;

        $this->assign('role_id',$role_id);
        $this->assign('_list',$list);
        return view();
    }

    public function add_root($role_id = ''){
        if(IS_POST){
            $data = $_REQUEST;

            $node_data = db('node')->field('node_name,authority_id,authority_code')->where('node_id='.$data['node_id'])->find();
            
            $map=array();
            $map['role_id']= $data['role_id'];
            $map['authority_code']= $node_data['authority_code'];
            $role_auth = db("role_auth")->where($map)->find();
            if($role_auth) $this->error('权限已存在!');

            $role_data['role_id'] = $data['role_id'];
            $role_data['authority_name'] = $node_data['node_name'];
            $role_data['authority_id'] = $node_data['authority_id'];
            $role_data['authority_code'] = $node_data['authority_code'];
            $role_data['authority_pid'] = 0;
            $role_data['sort_order'] = 50;
            $role_data['is_enable'] = 1;
            //删除非数据库字段
            // unset($data['pic_img']);
            $id = db("role_auth")->insert($role_data,false,true);
            if($id){
                $this->success("添加成功",'Admin/index');
            }
            $this->error("添加失败");
        }
        $root_list = db("node")->where('pid=0 and is_menu=1')->select();
        // dump($root_list);die;

        $this->assign('role_id',$role_id);
        $this->assign('root_list',$root_list);
        return view();
    }

    public function root_delete($role_node_id = 0){
        $map = array();
        $map['role_node_id'] = $role_node_id;
        $flag = db('role_auth')->where($map)->delete();
        if($flag){
            $this->success("删除成功");
        }
        $this->error('删除失败');
    }

    public function root_edit_field($id = 0){
        //模块化更新
        // $flag = model('admin')->allowField(true)->save($_REQUEST,['id'=>$id]);
        $data = $_REQUEST;
        //删除非数据库字段
        unset($data['id']);
        $data['role_node_id'] = $id;
        $flag = db("role_auth")->update($data);
        ($flag || $flag===0)  && $this->success("保存成功");
        $this->error("保存失败");
    }

    public function add($login_name = ''){
        if(IS_POST){
            // 开启事务
            Db::startTrans();

            $data = $_REQUEST;
            // dump($data);die;
            $map=array();
            $map['login_name']= $login_name;
            $admins = db("Admin")->where($map)->find();
            if($admins) $this->error('用户名已存在!');

            $data['login_pass'] = md5($data['login_pass']);
            $data['add_time'] = time();
            
            if($data['role_id'] == 2){
                $user = db('user')->where("phone='".$data['mobile']."'")->find();
                if(!empty($user)){
                    $this->error('账号已存在!');
                }
                $key = GetRandStr();// 4位秘钥
                $reg_time = date('Y-m-d H:i:s');
                
                $arr['invite_code'] = $data['invite_code'];
                $arr['pid'] = 1;
                $arr['phone'] = $data['mobile'];
                $arr['key'] = $key;
                $arr['pswd'] = md5($data['login_pass'].$key);
                $arr['reg_time'] = $reg_time;
                $arr['user_name'] = $data['nickname'];
                $arr['level'] = 1;
                $arr['status'] = 0;
                $user_id = db("user")->insertGetId($arr);
                unset($data['invite_code']);
                $yhid = 'YH'.(100000+$user_id);
                db('user')->where('id='.$user_id)->setField('yhid',$yhid);
            }else if($data['role_id'] == 3){
                $user = db('user')->where("phone='".$data['mobile']."'")->find();
                $mobile = db('Admin')->where("id='".$data['pid']."'")->value('mobile');
                $p_user_id = db('user')->where("phone='".$mobile."'")->value('id');
                if(!empty($user)){
                    $this->error('账号已存在!');
                }
                $key = GetRandStr();// 4位秘钥
                $reg_time = date('Y-m-d H:i:s');
                $arr['invite_code'] = $data['invite_code'];
                $arr['pid'] = $p_user_id;
                $arr['phone'] = $data['mobile'];
                $arr['key'] = $key;
                $arr['pswd'] = md5($data['login_pass'].$key);
                $arr['reg_time'] = $reg_time;
                $arr['user_name'] = $data['nickname'];
                $arr['level'] = 2;
                $arr['status'] = 0;
                $user_id = db("user")->insertGetId($arr); 
                unset($data['invite_code']);
                $yhid = 'YH'.(100000+$user_id);
                db('user')->where('id='.$user_id)->setField('yhid',$yhid);   
            }else{
                unset($data['invite_code']);
            }
            
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
        $this->assign('p_user',$p_user);
        $this->assign('role_list',$role_list);
        return view();
    }
    
    public function edit($id = 0){
        if(IS_POST){
            $data = $_REQUEST;

            $map=array();
            $map = "login_name='".$data['login_name']."' and id!=".$id;
            $admins = db("Admin")->where($map)->find();
            if($admins) $this->error('用户名已存在!');

            if($data['login_pass']){
                $data['login_pass'] = md5($data['login_pass']);
            }else{
                unset($data['login_pass']);
            }
            //删除非数据库字段
            // unset($data['pic_img']);
            $flag = db("admin")->update($data);
            if($flag || $flag === 0){
                $this->success("保存成功");
            }
            $this->error("保存失败");
        }
        $role_list = db("role")->select();
        $this->assign('role_list',$role_list);
        $admin = db('admin')->where("id=".$id)->find();
        $this->assign("admin",$admin);
        return view();
    }

    public function get_admin_list(){
        $count = db("admin")->count();
        $list = db("admin")->paginate(20,$count);
        //获取分页
        $page = $list->render();
        //遍历数据
        $list->each(function($item,$key){
            $item['role_name'] = db("Role")->where("role_id=".$item['role_id'])->value('role_name');
            return $item;
        });
        $this->assign("page",$page);
        $this->assign("_list",$list);
        $html = $this->fetch("tpl/admin_list");
        $this->ajaxReturn(['data'=>$html,'code'=>1]);
    }

    public function admin_edit_field($id = 0){
        //模块化更新
        // $flag = model('admin')->allowField(true)->save($_REQUEST,['id'=>$id]);
        $data = $_REQUEST;
        //删除非数据库字段
        unset($data['id']);
        $data['id'] = $id;
        $flag = db("admin")->update($data);
        ($flag || $flag===0)  && $this->success("保存成功");
        $this->error("保存失败");
    }

    /**
     * 删除数据
     * @param  integer $id [description]
     * @return [type]      [description]
     */
    public function admin_delete($id = 0){
        if($id==1)
            $this->eroor("无法删除超级管理员");
        $map = array();
        $map['id'] = $id;
        $flag = db('admin')->where($map)->delete();
        if($flag){
            $this->success("删除成功");
        }
        $this->error('删除失败');
    }

    // 上分
    public function add_money()
    {
        if(IS_POST){
            $data = $_REQUEST;
            $user = db('user')->where('yhid="'.$data['yhid'].'"')->find();
            $balance = $user['balance'];
            $arr['user_id'] = $user['id'];
            $arr['deal_cate'] = 1;
            $arr['deal_money'] = $data['price'];
            $arr['new_money'] = $data['price']+$balance;
            $arr['status'] = 1;
            $arr['add_time'] = date('Y-m-d H:i:s');
            $arr['pay_status'] = 1; // 1.已完成|2.未完成，Yjlx=1的情况
            $id = db("account_details")->insert($arr);
            db('user')->where('yhid="'.$data['yhid'].'"')->setInc('no_balance',$data['price']*0.6);
            db('user')->where('yhid="'.$data['yhid'].'"')->setInc('balance',$data['price']*0.4);
            db('user')->where('yhid="'.$data['yhid'].'"')->setInc('amount_money',$data['price']);
            if($id>0){
                $this->success('上分成功');
            }else{
                $this->error('上分失败');
            }          
        }

        return view();
    } 
}
