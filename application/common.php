<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

    function getCurl($url){
        $header = 'Accept-Content-Type:application/json;Accept-Charset: utf-8';
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_HEADER,$header);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_HEADER,0);
        curl_setopt($curl,CURLOPT_NOBODY,0);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($curl,CURLOPT_URL,$url);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
    /**
     * 获取图片地址
     * @param  string $saveName [文件名称]
     * @param  string $flag     [标识:ads]
     * @return [type]           [description]
     */
    function getImageUrl($saveName = '', $flag = 'ads'){
        if(!$saveName)
    		    return $saveName;
        return config('uploads_path.web').DS.$flag.DS.$saveName;
    }
    function isMobile() { 
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        } 
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset($_SERVER['HTTP_VIA'])) { 
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        } 
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高。其中'MicroMessenger'是电脑微信
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu' ,'android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile','MicroMessenger'); 
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
              return true;
            } 
        } 
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])) { 
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml')  <  strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            } 
        } 
        return false;
    }

    /*
     * 递归遍历
     * @param $data array
     * @param $id int
     * return array
     * */
    //四级分类查询
    function get_data($data, $id=0){
        $list = array();
        foreach($data as $v) {
            if($v['pid'] == $id) {
                $v['child'] = get_data($data, $v['id']);
                if(empty($v['child'])) {
                    unset($v['child']);
                }
                array_push($list, $v);
            }
        }
        return $list;     
    }

    
    // 生成随机数
    function GetRandStr($length=4){
        $str = '0123456789';
        $len = strlen($str)-1;
        $randstr='';
        for($i=0;$i<$length;$i++){
          $num=mt_rand(0,$len);
          $randstr .= $str[$num];
        }
        return $randstr;
    }
    /*
    * array unique_rand( int $min, int $max, int $num )
    * 生成一定数量的不重复随机数
    * $min 和 $max: 指定随机数的范围
    * $num: 指定生成数量
    */
     
    function  unique_rand($min,$max,$num){
        $count = 0;
        $return_arr = array();
        while($count < $num){
            $return_arr[] = mt_rand($min,$max);
            $return_arr = array_flip(array_flip($return_arr));
            $count = count($return_arr);
        }
        shuffle($return_arr);
        return $return_arr;
    }

    /**
     * 只保留字符串首尾字符，隐藏中间用*代替（两个字符时只显示第一个）
     * @param string $user_name 姓名
     * @return string 格式化后的姓名
     */
    function name_substr_cut($user_name){
        $strlen   = mb_strlen($user_name, 'utf-8');
        $firstStr = mb_substr($user_name, 0, 1, 'utf-8');
        $lastStr  = mb_substr($user_name, -1, 1, 'utf-8');
        return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
    }

    /**
     * 只保留字符串前4位和后3位，隐藏中间用*代替
     * @param string $bank_no 银行卡号
     * @return string 格式化后的银行卡号
     */
    function bank_no_substr_cut($bank_no){
        $strlen   = mb_strlen($bank_no, 'utf-8');
        $firstStr = mb_substr($bank_no, 0, 4, 'utf-8');
        $lastStr  = mb_substr($bank_no, -3, 3, 'utf-8');
        return $firstStr . str_repeat('*', $strlen - 7) . $lastStr;
    }

    /**
     * 只保留字符串前6位和后3位，隐藏中间用*代替
     * @param string $id_card 银行卡号
     * @return string 格式化后的银行卡号
     */
    function id_card_substr_cut($id_card){
        $strlen   = mb_strlen($id_card, 'utf-8');
        $firstStr = mb_substr($id_card, 0, 6, 'utf-8');
        $lastStr  = mb_substr($id_card, -3, 3, 'utf-8');
        return $firstStr . str_repeat('*', $strlen - 9) . $lastStr;
    }

    /**
     * 只保留字符串前3位和后4位，隐藏中间用*代替
     * @param string $id_card 手机号码
     * @return string 格式化后的手机号码
     */
    function phone_substr_cut($phone){
        $strlen   = mb_strlen($phone, 'utf-8');
        $firstStr = mb_substr($phone, 0, 3, 'utf-8');
        $lastStr  = mb_substr($phone, -4, 4, 'utf-8');
        return $firstStr . str_repeat('*', $strlen - 7) . $lastStr;
    }

    // 过滤字符串
    function replace_specialChar($strParam){
        $regex = "/\/|\～|\，|\。|\！|\？|\“|\”|\【|\】|\『|\』|\：|\；|\《|\》|\’|\‘|\ |\·|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\/|\;|\'|\`|\-|\=|\\\|\|/";
        return preg_replace($regex,"",$strParam);
    }

    /**
     * 模拟post进行url请求
     * @param string $url
     * @param array $post_data
     */
    function request_post($url = '', $post_data = array()) {
        if (empty($url) || empty($post_data)) {
            return false;
        }
        
        $o = "";
        foreach ( $post_data as $k => $v ) 
        { 
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $post_data = substr($o,0,-1);
		
        $postUrl = $url;
        $curlPost = $post_data;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        // curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		//var_dump(curl_error($ch));
        $data = curl_exec($ch);//运行curl
		//var_dump($data);
        curl_close($ch);
        
        return $data;
    }
    function http_request($url,$timeout=30,$header=array()){
        if (!function_exists('curl_init')) {
            throw new Exception('server not install curl');
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        $data = curl_exec($ch);
        list($header, $data) = explode("\r\n\r\n", $data);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code == 301 || $http_code == 302) {
            $matches = array();
            preg_match('/Location:(.*?)\n/', $header, $matches);
            $url = trim(array_pop($matches));
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $data = curl_exec($ch);
        }

        if ($data == false) {
            curl_close($ch);
        }
        @curl_close($ch);
        return $data;
    }
    /** 计算分销收益 **/
    /* 
     *分红比例--基值  $ratio
     *分红比例--幂函数  $power
    */
    function fx($id,$ratio,$power,$money){
        $pid = db('user')->where('id='.$id)->value('pid');  
        if(!empty($pid)){
            $p_status = db('user')->where('id='.$pid)->value('status');
            // if($p_status == 0){
                $yhid = db('user')->where('id='.$pid)->value('yhid');

                $fenhong = $ratio*$money;
                $balance = db('user')->where('yhid="'.$yhid.'"')->value('balance');

                db('user')->where('id='.$pid)->setInc('balance',$fenhong);
                db('user')->where('id='.$pid)->setInc('amount_money',$fenhong);

                /**添加明细**/
                $arr['yhid'] = $yhid;
                $arr['fx_yhid'] = $id;
                $arr['Jylx'] = 5;
                $arr['jyje'] = $fenhong;
                $arr['new_money'] = $fenhong+$balance;
                $arr['Jysj'] = time();
                $arr['Srhzc'] = 1;
                $res = db('account_details')->insert($arr);
                /**添加明细**/
                
                $p_ratio = $ratio*$power;
                fx($pid,$p_ratio,$power,$money);
            // }
        }    
    }

    function get_qq_people()
    {
        $testurl = 'http://cgi.im.qq.com/cgi-bin/minute_city';  
        $ch = curl_init();    
        curl_setopt($ch, CURLOPT_URL, $testurl);    
        //参数为1表示传输数据，为0表示直接输出显示。  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        //参数为0表示不带头文件，为1表示带头文件  
        curl_setopt($ch, CURLOPT_HEADER,0);  
        $output = curl_exec($ch);   
        curl_close($ch);  
        $data = json_decode($output,true);
        $num = array();
        $num['str'] = substr($data['minute'][0], -3,3);
        $num['arr'][] = substr($num['str'], 0,1);
        $num['arr'][] = substr($num['str'], 1,1);
        $num['arr'][] = substr($num['str'], 2,1);
        return $num;
    }

    // 下注流水奖励
    /**
     * @param  string $url      [路径]
     * @param  string $yhid     [用户编号后六位]
     * @return json 
    */

    function reward($yhid){
        $time = strtotime(date("Y-m-d"),time());

        $desc = db('account_details')->where('yhid="'.$yhid.'" and Jylx=3 and Jysj>'.$time)->order('Jysj desc')->column('jyje');
        // dump($desc);
        /* 奖励规则 */
        $array = [
            10000 => 66,
            50000 => 388,
            100000 => 888,
            200000 => 1888,
        ];
        /* 奖励规则 END */

        $jyje_sum = 0;
        for ($i=0; $i < count($desc) ; $i++) {
            $jyje_sum = $jyje_sum+$desc[$i];
        }
        // dump($jyje_sum);
        foreach ($array as $key => $value) {
            if($jyje_sum - $desc[0] < $key && $jyje_sum >= $key){
                db('user')->where('yhid="'.$yhid.'"')->setInc('balance',$value);
                db('user')->where('yhid="'.$yhid.'"')->setInc('amount_money',$value);
                /**添加明细**/
                $arr['yhid'] = $yhid;
                $arr['Jylx'] = 6;
                $arr['jyje'] = $value;
                $arr['new_money'] = $value+db('user')->where('yhid="'.$yhid.'"')->value('balance');
                $arr['Jysj'] = time();
                $arr['Srhzc'] = 1;
                $res = db('account_details')->insert($arr);
                /**添加明细**/
            }
        }
    }  

    // 加密
    function phone_encrypt($sjhm)
    {
        $sjhm = $sjhm+11;
        return $sjhm;
    }

    // 解密
    function phone_decode($sjhm)
    {
        $sjhm = $sjhm-11;
        return $sjhm;
    }
