<?php
namespace app\home\controller;

class Downapp extends Base
{
    public function downapp()
    {
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
            echo "<script> window.location.href='itms-services://?action=download-manifest&url=https://www.zhxbg.com/package/property_list.plist'; </script>";
        // }else if(strpos($_SERVER['HTTP_USER_AGENT'], 'Android')){
        }else{
            if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
                echo "<body><div style='width:100vw;height: 100vh;background-color: rgba(133, 133, 133, 0.733);'><img style='width:100vw;margin-top: 3vw;' src='https://www.zhxbg.com/public/home/cue-bac.png'></div></body>";
            }else{
                echo "<script> window.location.href='https://www.zhxbg.com/package/yi_hong_cai_dian.apk'; </script>";
            }
            
        }
    }
}
