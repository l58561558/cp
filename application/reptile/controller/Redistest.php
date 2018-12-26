<?php
namespace app\reptile\controller;
use think\cache\driver\Redis;
use app\home\controller\Base; 
//用户登录注册控制器
class Redistest extends Base
{
    public function index()
    {
        $data = $_REQUEST;
    	$Redis = new Redis();
        $redis = $Redis->handler();
        // 设置锁KEY
        $lock_key = "FB_USER_ID:".$data['user_id'];
        // 设置锁的有效期
        $lock_expire = 5;
        // 设置锁值未当前时间戳 + 有效期
        $lock_value = time() + $lock_expire;

        // 判断锁是否存在
        if($redis->exists($lock_key) == 1){
            // 如果存在 判断锁是否过期
            if($redis->ttl($lock_key) == 0){
                // 如果过期了 那么重新赋值
                $lock = $redis->setnx($lock_key, $lock_value);
            }else{
                // 如果没过期 给用户一个提示

            }
        }else{
            //如果锁不存在 生成锁
            $lock = $redis->setnx($lock_key, $lock_value);
        }

        // 逻辑代码 start ....

        // 逻辑代码 end ....

        // 逻辑执行完毕 删除锁
        $redis->del($lock_key);

    	dump($redis);
    	// dump($_SERVER);
        // return view();
    }
}
