<?php
namespace app\home\controller;

//个人中心控制器
class User extends Base
{
    public $Txqqid = 100000000;
    // 个人中心--个人信息
    /*
    *参数 :无
    */
    public function user($user_id){
        $yh = db('user')->where('id='.$user_id)->find();

        $list['id'] = $yh['id'];
        $list['yhid'] = $yh['yhid'];
        $list['phone'] = $yh['phone'];
        $list['user_name'] = $yh['user_name'];
        $list['balance'] = $yh['balance'];
        $list['no_balance'] = $yh['no_balance'];
        $list['amount_money'] = number_format($yh['amount_money'], 2, '.', '');
        $list['win_money'] = db('account_details')->where('deal_cate=4 and user_id='.$user_id)->sum('deal_money');
        $list['win_money'] = number_format($list['win_money'], 2, '.', '');
        $list['head_img'] = $yh['head_img'];
        $list['name'] = $yh['name'];
        $list['id_card'] = empty($yh['id_card'])?'':$yh['id_card'];
        $list['bank_card'] = empty($yh['bank_card'])?'':$yh['bank_card'];
        // $list['win_money'] = db('order')->where('is_win=1')->sum('win_money');
        $list['is_band'] = $yh['is_band'];

        $this->ajaxReturn(['msg'=>$list,'code'=>1,'success'=>true]);
    }

    // 个人中心--修改密码
    /**
    *user_id             int       用户ID
    *old_password        string    原始密码
    *password            string    密码
    *zpassword           string    再次输入密码
    **/
    public function save_pswd(){
        $data = $_REQUEST;
        $yh = db('user')->where('id='.$data['user_id'])->find();
        $old_password = md5($data['old_password'].$yh['key']);
        if($old_password != $yh['pswd']){
            $this->ajaxReturn(['msg'=>'原始密码错误','code'=>60001,'success'=>false]);
        }
        $key = GetRandStr();// 4位秘钥
        $arr['pswd'] = md5($data['password'].$key);
        $arr['key'] = $key;

        $res = db('user')->where('id="'.$data['user_id'].'"')->update($arr);

        if($res > 0){
            $this->ajaxReturn(['msg'=>'修改成功','code'=>1,'success'=>true]);
        }else{
            $this->ajaxReturn(['msg'=>'修改失败','code'=>60004,'success'=>false]);
        }
    }

    // 个人中心--更新用户资料
    /**
    *user_id       int       用户ID
    *head_img      file      头像
    *user_name     string    昵称
    **/
    public function save_user_data()
    {
        $data = $_REQUEST;
        $yh = db('user')->where('id='.$data['user_id'])->find();

        if(isset($data['head_img']) && !empty($data['head_img'])){
            $img = base64_decode($data['head_img']);
            $file_name = md5(uniqid(rand())).".png";
            $path = config('uploads_path.path').DS.'head_img'.DS.$yh['phone'];
            $app_path = config('uploads_path.app_path').'head_img/'.$yh['phone'].'/'.$file_name;
            $file_path = $path.DS.$file_name;

            is_dir($path) OR mkdir($path, 0777, true);// 如果文件夹不存在，将以递归方式创建该文件夹

            if (!is_writable($path)) chmod($path, 0777); // 如果无权限，则修改为0777最大权限

            $bytes = file_put_contents($file_path, $img);
            
            if($bytes == 0){
                $this->ajaxReturn(['msg'=>'图片上传失败','code'=>70001,'success'=>false]);
            }

            $arr['head_img'] = $app_path;    
        }
        if(isset($data['user_name']) && !empty($data['user_name'])){
            $arr['user_name'] = $data['user_name'];
        }
        
        $res = db("user")->where('id='.$yh['id'])->update($arr);

        if($res > 0){
            $this->ajaxReturn(['msg'=>'更新成功','code'=>1,'success'=>true]);
        }else{
            $this->ajaxReturn(['msg'=>'更新失败','code'=>70002,'success'=>false]);
        }
    }

    // 个人中心--更新认证信息
    /**
    *user_id       int       用户ID
    *name          string    真实姓名
    *id_card       string    身份证号
    *bank_card     string    银行卡号
    **/
    public function save_attestation()
    {
        $data = $_REQUEST;
        $id = $data['user_id'];
        unset($data['user_id']);
        if(!empty($data['id_card'])){
            $x = substr($data['id_card'], -1, 1);
            if($x == 'x' || $x == 'X'){
                $data['id_card'] = str_replace($x, 'X', $data['id_card']);
            }
        }
        $data['is_band'] = 1;
        db('user')->where('id='.$id)->update($data);
        $this->ajaxReturn(['msg'=>'更新成功','code'=>1,'success'=>true]);
    }
}
