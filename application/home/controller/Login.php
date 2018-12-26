<?php
namespace app\home\controller;
use sendsms\SendTemplateSMS;
use think\Session;
use think\captcha\Captcha;

//用户登录注册控制器
class Login extends Base
{
    // 用户编号基础值
    public $user_id = 'YH';
    public $yhid = 100000;

    /*登录
    *user_id       int       用户ID
    */ 
    public function login()
    {
        $data = $_REQUEST;
        $phone = db('user')->where("phone='".$data['phone']."'")->find();

        if(empty($phone)){
            $this->ajaxReturn(['msg'=>'用户不存在','code'=>10003,'success'=>false]);
        }

        $last_login_time = date('Y-m-d H:i:s');
        $last_ip = $this->get_ip();
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        db('user')->where('id='.$phone['id'])->update(array('last_ip'=>$last_ip,'last_login_time'=>$last_login_time,'user_agent'=>$user_agent));
        if($phone['pswd'] == md5($data['password'].$phone['key'])){
            $this->ajaxReturn(['msg'=>'登录成功','code'=>1,'success'=>true,'user_id'=>$phone['id']]);
        }else{
            $this->ajaxReturn(['msg'=>'登录失败,密码错误','code'=>10004,'success'=>false]);
        }
    }

    //不同环境下获取真实的IP
    public function get_ip()
    {
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            $cip = $_SERVER['HTTP_CLIENT_IP'];
        }
        else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        else if(!empty($_SERVER["REMOTE_ADDR"])){
            $cip = $_SERVER["REMOTE_ADDR"];
        }else{
            $cip = '';
        }
        preg_match("/[\d\.]{7,15}/", $cip, $cips);
        $cip = isset($cips[0]) ? $cips[0] : 'unknown';
        unset($cips);
        return $cip;
    }
    
    // 发送短信验证码
    /**
    *phone string 手机号码
    *cate  int    短信类型:1.注册|2.修改密码|3.修改银行卡
    **/
    public function send_code()
    {
        $data = $_REQUEST;
        if($data['cate'] == 1){
            $user = db('user')->where("phone='".$data['phone']."'")->find();
            if(!empty($user)){
                $this->ajaxReturn(['msg'=>'账号已存在','code'=>40002,'success'=>false]);
            }    
        }
        
        $time = date('Y-m-d H:i:s');
        $code = GetRandStr(4);
        $find = db('message')->where('is_delete=1 and phone="'.$data['phone'].'" and cate='.$data['cate'].' and past_time>"'.$time.'"')->order('add_time desc')->find();
        if(!empty($find)){
            db('message')->where('id='.$find['id'])->setField('is_delete',0);
        }
        $arr['phone'] = $data['phone'];
        $arr['cate'] = $data['cate'];
        $arr['code'] = $code;
        $arr['add_time'] = $time;
        $arr['past_time'] = date('Y-m-d H:i:s', strtotime($time)+60); // 过期时间
        $arr['login_ip'] = $this->get_ip();
        $res = db('message')->insert($arr);
        if($res > 0){
            // 短信接口
            $send_sms = new SendTemplateSMS();
            $to = $data['phone'];
            $datas = array($code,'60秒');
            $send_sms->sendTemplateSMS($to,$datas,395660);
        }
    }
    
    // 验证 验证码
    /**
    *phone string 手机号码
    *cate  int    短信类型:1.注册|2.修改密码
    *code  int    验证码
    **/
    public function verify_code()
    {
        $data = $_REQUEST;
        $time = date('Y-m-d H:i:s');
        $message = db('message')->where('is_delete=1 and phone="'.$data['phone'].'" and cate='.$data['cate'].' and past_time>"'.$time.'"')->order('add_time desc')->find();
        if(empty($message)){
            $this->ajaxReturn(['msg'=>'验证码已过期','code'=>30001,'success'=>false]);
        }
        if($data['code'] == $message['code']){
        // if($data['code'] == '1234'){
            $this->ajaxReturn(['msg'=>'验证成功','code'=>1,'success'=>true]);
        }else{
            $this->ajaxReturn(['msg'=>'验证码错误','code'=>30002,'success'=>false]);
        }
    }

    // 注册
    /**
    *phone          string    手机号码
    *user_name      string    用户昵称
    *password       string    密码
    *zpassword      string    再次输入密码
    *invite_code    int       邀请码
    **/
    public function reg()
    {
    	$data = $_REQUEST;

        if(empty($data['invite_code'])){
            $this->ajaxReturn(['msg'=>'请输入邀请码','code'=>40001,'success'=>false]);
        }
    	$p_user = db('user')->where('invite_code="'.$data['invite_code'].'"')->find();

        if($data['invite_code'] != 100000){
            if(empty($p_user)){
                $this->ajaxReturn(['msg'=>'邀请码错误','code'=>40000,'success'=>false]);
            }
        }

        $user = db('user')->where("phone='".$data['phone']."'")->find();

        if(!empty($user)){
            $this->ajaxReturn(['msg'=>'账号已存在','code'=>40002,'success'=>false]);
        }

        $user_name = db('user')->where('user_name like "%'.$data['user_name'].'%"')->find();

        if(!empty($user_name)){
            $this->ajaxReturn(['msg'=>'用户名已存在','code'=>40003,'success'=>false]);
        }

        $reg_time = date('Y-m-d H:i:s');

        $arr = array();
        $key = GetRandStr();// 4位秘钥
        
        if($data['invite_code'] == 100000){
            $arr['level'] = 3;
            $arr['pid'] = 0;
        }else{
            $user_arr = db('user')->where('invite_code like "'.$data['invite_code'].'"')->find();
            $arr['level'] = 3;
            $arr['pid'] = $user_arr['id'];
            $arr['invite_phone'] = $user_arr['phone'];
        }
        // $arr['invite_code'] = $data['invite_code'];
        
        $arr['phone'] = $data['phone'];
        $arr['key'] = $key;
        $arr['pswd'] = md5($data['password'].$key);
        $arr['reg_time'] = $reg_time;
        $arr['user_name'] = $data['user_name'];

    	$id = db("user")->insertGetId($arr);

        if($id > 0){
            $yhid = 'YH'.($this->yhid+$id);
            db('user')->where('id='.$id)->setField('yhid',$yhid);
            $this->ajaxReturn(['msg'=>'注册成功,返回登录','code'=>1,'success'=>true]);
        }else{
            $this->ajaxReturn(['msg'=>'注册失败,请重新填写信息','code'=>40007,'success'=>false]);
        }
    }

    // 修改密码
    /**
    *phone        string    手机号
    *password     string    密码
    *zpassword    string    再次输入密码
    **/
    public function save_pswd(){
        $data = $_REQUEST;
        $key = GetRandStr();// 4位秘钥
        $arr['pswd'] = md5($data['password'].$key);
		$arr['key'] = $key;

        $res = db('user')->where('phone="'.$data['phone'].'"')->update($arr);

        if($res > 0){
            $this->ajaxReturn(['msg'=>'修改成功','code'=>1,'success'=>true]);
        }else{
            $this->ajaxReturn(['msg'=>'修改失败','code'=>50003,'success'=>false]);
        }
    }


}
?>