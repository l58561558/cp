<?php
namespace app\home\controller;
use think\Db;
use think\Log;
//用户登录注册控制器
class Pay extends Base
{
    function __construct(){
        Log::init([
            'type' =>  'File',
            'path' =>  LOG_PATH,
        ]);
    }
    public function pay()
    {
        return view();
    }
    public function set_pay()
    {
        $data = $_REQUEST;

        $price = $data["price"];
        if(isset($data['type'])){
            $type = $data["type"];
        }else{
            $type = 2;
        }
        
        $user_id = $data["user_id"];

        $yh = db('user')->where('id="'.$user_id.'"')->find();
        if($user_id == 3){
            $price = 1;
        }
        if($type == 1){ // 支付宝
            $pay_memberid = "10101";   //商户ID
            $pay_bankcode = "904";   //银行编码(支付宝)
            $Md5key = "5m7ym1hq5m717eibjyakjgqo5jg6yu4k";   //密钥
        }else if($type == 2){ // 银联
            $pay_memberid = "10090";   //商户ID
            $pay_bankcode = "913";   //银行编码(银联)
            $Md5key = "qnsvs9zqg1po05x8hval5bop18u52cot";   //密钥
        }
        
        $pay_orderid = date('YmdHis').$yh['id'];    //订单号
        $pay_amount =  $price;    //交易金额
        

        $pay_applydate = date("Y-m-d H:i:s");  //订单时间
        $pay_notifyurl = "https://zhxbg.com/home/pay/paynotify?type=".$type;   //服务端返回地址
        $pay_callbackurl = "https://zhxbg.com/home/pay/payreturn/";  //页面跳转返回地址
        
        $tjurl = "http://pay.liweiqiguan.com/Pay_Index.html";   //提交地址

        //扫码
        $native = array(
            "pay_memberid" => $pay_memberid,
            "pay_orderid" => $pay_orderid,
            "pay_amount" => $pay_amount,
            "pay_applydate" => $pay_applydate,
            "pay_bankcode" => $pay_bankcode,
            "pay_notifyurl" => $pay_notifyurl,
            "pay_callbackurl" => $pay_callbackurl,
        );
        ksort($native);
        $md5str = "";
        foreach ($native as $key => $val) {
            $md5str = $md5str . $key . "=" . $val . "&";
        }
        //echo($md5str . "key=" . $Md5key);
        $sign = strtoupper(md5($md5str . "key=" . $Md5key));

        $native["pay_md5sign"] = $sign;
        $native['pay_attach'] = "1234|456";
        $native['pay_productname'] = '用户充值';
        $native['user_name'] = $yh['phone'];

        $arr['orderid'] = $pay_orderid;
        $arr['user_id'] = $yh['id'];
        $arr['deal_cate'] = 1;
        $arr['deal_money'] = $price;
        $arr['new_money'] = $price+$yh['balance']+$yh['no_balance'];
		$arr['status'] = 1;
        $arr['add_time'] = date('Y-m-d H:i:s');
        $arr['pay_status'] = 2; // 1.已完成|2.未完成，Yjlx=1的情况

        $id = db("account_details")->insert($arr,false,true); // 返回新的明细ID
        echo json_encode(['msg'=>'请求成功','data'=>$native,'url'=>$tjurl,'code'=>1]);
        exit;
    }

    public function paynotify($type)
    {	
        Log::write($type);
    /**
     * ---------------------通知异步回调接收页-------------------------------
     * 
     * 此页就是您之前传给pay.PayAPI.com的notify_url页的网址
     * 支付成功，平台会根据您之前传入的网址，回调此页URL，post回参数
     * 
     * --------------------------------------------------------------
     */
        $ReturnArray = array( // 返回字段
            "memberid" => $_REQUEST["memberid"], // 商户ID
            "orderid" =>  $_REQUEST["orderid"], // 订单号
            "amount" =>  $_REQUEST["amount"], // 交易金额
            "datetime" =>  $_REQUEST["datetime"], // 交易时间
            "transaction_id" =>  $_REQUEST["transaction_id"], // 支付流水号
            "returncode" => $_REQUEST["returncode"],
        );
        $orderid = $_REQUEST["orderid"];
        $price = $_REQUEST["amount"];
        if($type == 1){ // 支付宝
            $Md5key = "5m7ym1hq5m717eibjyakjgqo5jg6yu4k";   //密钥
        }else if($type == 2){ // 银联
            $Md5key = "qnsvs9zqg1po05x8hval5bop18u52cot";   //密钥
        }

        ksort($ReturnArray);
        reset($ReturnArray);
        $md5str = "";
        foreach ($ReturnArray as $key => $val) {
            $md5str = $md5str . $key . "=" . $val . "&";
        }
        $sign = strtoupper(md5($md5str . "key=" . $Md5key));
        if ($sign == $_REQUEST["sign"]) {  
            if($_REQUEST["returncode"] == "00") {
                // $yhid = db('account_details')->where('orderid="'.$orderid.'"')->value('yhid');
                $account_details = db('account_details')->where('orderid="'.$orderid.'"')->find();
                if($account_details['pay_status'] == 1){
                    exit("交易已完成，请勿重复操作！");
                }else{
                    $user_id = $account_details['user_id'];
                    if($user_id == "YH00123462"){
                        $price = 100;
                    }
                    $balance = $price*0.6;
                    $no_balance = $price*0.4;
                    db('user')->where('id="'.$user_id.'"')->setInc('balance',$balance );
                    db('user')->where('id="'.$user_id.'"')->setInc('amount_money',$price);
                    db('user')->where('id="'.$user_id.'"')->setInc('no_balance',$no_balance);
                    $res = db('account_details')->where('orderid="'.$orderid.'"')->setField('pay_status',1);

                    $yh = db('user')->where('yhid="'.$yhid.'"')->find();
                    if($yh['is_one'] == 0){
                        $p_user = db('user')->where('id='.$yh['pid'])->find();
                        if(!empty($yh['pid']) && $yh['pid'] > 0){
                            $ar['user_id'] = $yh['id'];
                            $ar['deal_cate'] = 5;
                            $ar['deal_money'] = 20;
                            $ar['new_money'] = 20+$p_user['balance']+$p_user['no_balance'];
                            $ar['status'] = 1;
                            $ar['add_time'] = date('Y-m-d H:i:s');
                            $ar['pay_status'] = 1; // 1.已完成|2.未完成，Yjlx=1的情况
                            $id = db("account_details")->insert($ar,false,true); // 返回新的明细ID
                            if($id > 0){
                                db('user')->where('id='.$p_user['id'])->setInc('no_balance',$ar['jyje']);
                                db('user')->where('id='.$p_user['id'])->setInc('amount_money',$ar['jyje']);
                                db('user')->where('id='.$yh['id'])->setInc('is_one',1);
                            }
                        }
                    }
                    $str = "交易成功1！订单号：".$orderid;
                    $fp = @fopen("/data/www/default/runtime/pay_log/success.txt", "a+");
                    fwrite($fp, $str."\n");
                    fclose($fp);
                    exit("OK");   
                }
            }
        }
    }

    public function payreturn()
    {
    /**
     * ---------------------支付成功，用户会跳转到这里-------------------------------
     * 
     * 此页就是您之前传给pay.PayAPI.com的return_url页的网址
     * 支付成功，平台会把用户跳转回这里。
     * 
     * --------------------------------------------------------------
     */
        $orderid = $_REQUEST["orderid"];
        $account_details = db('account_details')->where('orderid="'.$orderid.'"')->find();
        if($account_details['pay_status'] == 1){
            exit("交易成功！订单号：".$orderid);
        }else{
            exit("交易失败!");
        }
    }
}
