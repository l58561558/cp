<?php
namespace app\adminz\controller;

class User extends Base
{
    public function index(){
        return view();
    }

    public function add($login_name = ''){
        if(IS_POST){
            $data = $_REQUEST;

            $map=array();
            $map['login_name']= $login_name;
            $users = db("user")->where($map)->find();
            if($users) $this->error('用户名已存在!');

            $data['login_pass'] = md5($data['login_pass']);
            $data['add_time'] = date('Y-m-d H:i:s');
            //删除非数据库字段
            // unset($data['pic_img']);
            $id = db("user")->insert($data,false,true);
            if($id){
                $this->success("添加成功");
            }
            $this->error("添加失败");
        }
        $role_list = db("role")->select();
        $this->assign('role_list',$role_list);
        return view();
    }
    
    public function edit($id = 0){
        if(IS_POST){
            $data = $_REQUEST;
            $arr = array();
            if(!empty($data['pswd'])){
                // $key = GetRandStr();// 4位秘钥
                $key = db('user')->where('id='.$data['id'])->value('key');// 4位秘钥
                // $arr['key'] = $key;
                $arr['pswd'] = md5($data['pswd'].$key);
            }

            $arr['id'] = $data['id'];
            $arr['status'] = $data['status'];
            
            //删除非数据库字段
            // unset($data['pic_img']);
            $flag = db("user")->update($arr);
            if($flag || $flag === 0){
                $this->success("保存成功");
            }
            $this->error("保存失败");
        }
        $user = db('user')->where("id=".$id)->find();

        $user['phone'] = empty($user['phone'])?'':$user['phone'];
        $user['is_invite'] = empty($user['is_invite'])?'':$user['is_invite'];
        
        $this->assign("user",$user);
        return view();
    }

    public function get_user_list(){
        $data = $_REQUEST;
        $map = '1=1';
        if(!empty($data['result']['yhid'])){
            $map .= ' and yhid like "%'.$data['result']['yhid'].'%"';
        }
        if(!empty($data['result']['user_name'])){
            $map .= ' and user_name like "%'.$data['result']['user_name'].'%"';
        }
        if(!empty($data['result']['phone'])){
            $map .= ' and phone like "%'.$data['result']['phone'].'%"';
        }
        if(!empty($data['result']['pid'])){
            $map .= ' and pid="'.db('user')->where('phone="'.$data['result']['pid'].'"')->value('id').'"';
        }
        if(!empty($data['result']['add_time'])){
            $map .= ' and reg_time>='.$data['result']['add_time'];
        }
        if(!empty($data['result']['end_time'])){
            $map .= ' and reg_time<='.$data['result']['end_time'];
        }
        
        $count = db("user")->where($map)->order('reg_time')->order('reg_time','desc')->count();
        $list = db("user")->where($map)->order('reg_time')->order('reg_time','desc')->paginate(20,$count);
        //获取分页
        $page = $list->render();
        //遍历数据
        $list->each(function($item,$key){
            // $item['role_name'] = db("Role")->where("role_id=".$item['role_id'])->value('role_name');
            $item['phone'] = $item['phone'];
            if(!empty($item['is_invite'])){
                $item['is_invite'] = $item['is_invite'];
            }
            
            return $item;
        });
        $this->assign("page",$page);
        $this->assign("count",$count);
        $this->assign("_list",$list);
        $html = $this->fetch("tpl/user_list");
        $this->ajaxReturn(['data'=>$html,'code'=>1]);
    }

    public function user_edit_field($id = 0){
        //模块化更新
        // $flag = model('user')->allowField(true)->save($_REQUEST,['id'=>$id]);
        $data = $_REQUEST;
        //删除非数据库字段
        unset($data['id']);
        $data['id'] = $id;
        $flag = db("user")->update($data);
        ($flag || $flag===0)  && $this->success("保存成功");
        $this->error("保存失败");
    }

    /**
     * 删除数据
     * @param  integer $id [description]
     * @return [type]      [description]
     */
    public function user_delete($id = 0){
        if($id==1)
            $this->eroor("无法删除超级管理员");
        $map = array();
        $map['id'] = $id;
        $flag = db('user')->where($map)->delete();
        if($flag){
            $this->success("删除成功");
        }
        $this->error('删除失败');
    }
}
