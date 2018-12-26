<?php
namespace app\home\controller;
use think\Request; 
use think\Controller;
use think\Cookie;
use think\Session;
use think\Db;

class Base extends Controller
{
    public function _initialize()
    {
        session::start();
    } 


    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @return void
     */
    protected function ajaxReturn($data) {
        // 返回JSON数据格式到客户端 包含状态信息
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data));
    }
    

}
