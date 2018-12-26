<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:57:"D:\phpStudy\WWW\cp/application/home\view\index\index.html";i:1544493128;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>System Error</title>
    <meta name="robots" content="noindex,nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <style>
        /* Base */
        body {
            color: #333;
            font: 14px Verdana, "Helvetica Neue", helvetica, Arial, 'Microsoft YaHei', sans-serif;
            margin: 0;
            padding: 0 20px 20px;
            word-break: break-word;
        }
        h1{
            margin: 10px 0 0;
            font-size: 28px;
            font-weight: 500;
            line-height: 32px;
        }
        h2{
            color: #4288ce;
            font-weight: 400;
            padding: 6px 0;
            margin: 6px 0 0;
            font-size: 18px;
            border-bottom: 1px solid #eee;
        }
        h3.subheading {
            color: #4288ce;
            margin: 6px 0 0;
            font-weight: 400;
        }
        h3{
            margin: 12px;
            font-size: 16px;
            font-weight: bold;
        }
        abbr{
            cursor: help;
            text-decoration: underline;
            text-decoration-style: dotted;
        }
        a{
            color: #868686;
            cursor: pointer;
        }
        a:hover{
            text-decoration: underline;
        }
        .line-error{
            background: #f8cbcb;
        }

        .echo table {
            width: 100%;
        }

        .echo pre {
            padding: 16px;
            overflow: auto;
            font-size: 85%;
            line-height: 1.45;
            background-color: #f7f7f7;
            border: 0;
            border-radius: 3px;
            font-family: Consolas, "Liberation Mono", Menlo, Courier, monospace;
        }

        .echo pre > pre {
            padding: 0;
            margin: 0;
        }
        /* Layout */
        .col-md-3 {
            width: 25%;
        }
        .col-md-9 {
            width: 75%;
        }
        [class^="col-md-"] {
            float: left;
        }
        .clearfix {
            clear:both;
        }
        @media only screen 
        and (min-device-width : 375px) 
        and (max-device-width : 667px) { 
            .col-md-3,
            .col-md-9 {
                width: 100%;
            }
        }
        /* Exception Info */
        .exception {
            margin-top: 20px;
        }
        .exception .message{
            padding: 12px;
            border: 1px solid #ddd;
            border-bottom: 0 none;
            line-height: 18px;
            font-size:16px;
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
            font-family: Consolas,"Liberation Mono",Courier,Verdana,"微软雅黑";
        }

        .exception .code{
            float: left;
            text-align: center;
            color: #fff;
            margin-right: 12px;
            padding: 16px;
            border-radius: 4px;
            background: #999;
        }
        .exception .source-code{
            padding: 6px;
            border: 1px solid #ddd;

            background: #f9f9f9;
            overflow-x: auto;

        }
        .exception .source-code pre{
            margin: 0;
        }
        .exception .source-code pre ol{
            margin: 0;
            color: #4288ce;
            display: inline-block;
            min-width: 100%;
            box-sizing: border-box;
        font-size:14px;
            font-family: "Century Gothic",Consolas,"Liberation Mono",Courier,Verdana;
            padding-left: 48px;
        }
        .exception .source-code pre li{
            border-left: 1px solid #ddd;
            height: 18px;
            line-height: 18px;
        }
        .exception .source-code pre code{
            color: #333;
            height: 100%;
            display: inline-block;
            border-left: 1px solid #fff;
        font-size:14px;
            font-family: Consolas,"Liberation Mono",Courier,Verdana,"微软雅黑";
        }
        .exception .trace{
            padding: 6px;
            border: 1px solid #ddd;
            border-top: 0 none;
            line-height: 16px;
        font-size:14px;
            font-family: Consolas,"Liberation Mono",Courier,Verdana,"微软雅黑";
        }
        .exception .trace ol{
            margin: 12px;
        }
        .exception .trace ol li{
            padding: 2px 4px;
        }
        .exception div:last-child{
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;
        }

        /* Exception Variables */
        .exception-var table{
            width: 100%;
            margin: 12px 0;
            box-sizing: border-box;
            table-layout:fixed;
            word-wrap:break-word;            
        }
        .exception-var table caption{
            text-align: left;
            font-size: 16px;
            font-weight: bold;
            padding: 6px 0;
        }
        .exception-var table caption small{
            font-weight: 300;
            display: inline-block;
            margin-left: 10px;
            color: #ccc;
        }
        .exception-var table tbody{
            font-size: 13px;
            font-family: Consolas,"Liberation Mono",Courier,"微软雅黑";
        }
        .exception-var table td{
            padding: 0 6px;
            vertical-align: top;
            word-break: break-all;
        }
        .exception-var table td:first-child{
            width: 28%;
            font-weight: bold;
            white-space: nowrap;
        }
        .exception-var table td pre{
            margin: 0;
        }

        /* Copyright Info */
        .copyright{
            margin-top: 24px;
            padding: 12px 0;
            border-top: 1px solid #eee;
        }

        /* SPAN elements with the classes below are added by prettyprint. */
        pre.prettyprint .pln { color: #000 }  /* plain text */
        pre.prettyprint .str { color: #080 }  /* string content */
        pre.prettyprint .kwd { color: #008 }  /* a keyword */
        pre.prettyprint .com { color: #800 }  /* a comment */
        pre.prettyprint .typ { color: #606 }  /* a type name */
        pre.prettyprint .lit { color: #066 }  /* a literal value */
        /* punctuation, lisp open bracket, lisp close bracket */
        pre.prettyprint .pun, pre.prettyprint .opn, pre.prettyprint .clo { color: #660 }
        pre.prettyprint .tag { color: #008 }  /* a markup tag name */
        pre.prettyprint .atn { color: #606 }  /* a markup attribute name */
        pre.prettyprint .atv { color: #080 }  /* a markup attribute value */
        pre.prettyprint .dec, pre.prettyprint .var { color: #606 }  /* a declaration; a variable name */
        pre.prettyprint .fun { color: red }  /* a function name */
    </style>
</head>
<body>
    <div class="echo">
            </div>
        <div class="exception">
    <div class="message">
        
            <div class="info">
                <div>
                    <h2>[8]&nbsp;<abbr title="think\exception\ErrorException">ErrorException</abbr> in <a class="toggle" title="/data/www/default/extend/sendsms/SendTemplateSMS.php line 69">SendTemplateSMS.php line 69</a></h2>
                </div>
                <div><h1>Trying to get property 'statusCode' of non-object</h1></div>
            </div>
        
    </div>
	        <div class="source-code">
            <pre class="prettyprint lang-php"><ol start="60"><li class="line-60"><code>        $rest-&gt;setAppId($appId);
</code></li><li class="line-61"><code>        
</code></li><li class="line-62"><code>        // 发送模板短信
</code></li><li class="line-63"><code>        // echo &quot;Sending TemplateSMS to $to &lt;br/&gt;&quot;;
</code></li><li class="line-64"><code>        $result = $rest-&gt;sendTemplateSMS($to,$datas,$tempId);
</code></li><li class="line-65"><code>        if($result == NULL ) {
</code></li><li class="line-66"><code>            echo json_encode(['msg'=&gt;'result error']);
</code></li><li class="line-67"><code>            exit;
</code></li><li class="line-68"><code>        }
</code></li><li class="line-69"><code>        if($result-&gt;statusCode!=0) {
</code></li><li class="line-70"><code>            echo json_encode(['error_code'=&gt;$result-&gt;statusCode,'error_msg'=&gt;$result-&gt;statusMsg]);
</code></li><li class="line-71"><code>            exit;
</code></li><li class="line-72"><code>            //TODO 添加错误处理逻辑
</code></li><li class="line-73"><code>        }else{
</code></li><li class="line-74"><code>            echo &quot;Sendind TemplateSMS success!&lt;br/&gt;&quot;;
</code></li><li class="line-75"><code>            // 获取返回信息
</code></li><li class="line-76"><code>            $smsmessage = $result-&gt;TemplateSMS;
</code></li><li class="line-77"><code>            echo json_encode(['dateCreated'=&gt;$smsmessage-&gt;dateCreated,'smsMessageSid'=&gt;$smsmessage-&gt;smsMessageSid,'success'=&gt;true]);
</code></li><li class="line-78"><code>            exit;
</code></li></ol></pre>
        </div>
	        <div class="trace">
            <h2>Call Stack</h2>
            <ol>
                <li>in <a class="toggle" title="/data/www/default/extend/sendsms/SendTemplateSMS.php line 69">SendTemplateSMS.php line 69</a></li>
                                <li>
                at <abbr title="think\Error">Error</abbr>::appError(8, '<a class="toggle" title="Trying to get property 'statusCode' of non-object">Trying to get proper...</a>', '<a class="toggle" title="/data/www/default/extend/sendsms/SendTemplateSMS.php">/data/www/default/ex...</a>', 69, ['to' => '15394244605', 'datas' => ['5864', '60秒'], 'tempId' => 395660, ...]) in <a class="toggle" title="/data/www/default/extend/sendsms/SendTemplateSMS.php line 69">SendTemplateSMS.php line 69</a>                </li>
                                <li>
                at <abbr title="sendsms\SendTemplateSMS">SendTemplateSMS</abbr>->sendTemplateSMS('15394244605', ['5864', '60秒'], 395660) in <a class="toggle" title="/data/www/default/application/home/controller/Login.php line 82">Login.php line 82</a>                </li>
                                <li>
                at <abbr title="app\home\controller\Login">Login</abbr>->send_code()                </li>
                                <li>
                at <abbr title="ReflectionMethod">ReflectionMethod</abbr>->invokeArgs(<em>object</em>(<abbr title="app\home\controller\Login">Login</abbr>), []) in <a class="toggle" title="/data/www/default/thinkphp/library/think/App.php line 197">App.php line 197</a>                </li>
                                <li>
                at <abbr title="think\App">App</abbr>::invokeMethod([<em>object</em>(<abbr title="app\home\controller\Login">Login</abbr>), 'send_code'], []) in <a class="toggle" title="/data/www/default/thinkphp/library/think/App.php line 411">App.php line 411</a>                </li>
                                <li>
                at <abbr title="think\App">App</abbr>::module(['home', 'login', 'send_code'], ['app_host' => '', 'app_debug' => <em>true</em>, 'app_trace' => <em>false</em>, ...], <em>true</em>) in <a class="toggle" title="/data/www/default/thinkphp/library/think/App.php line 296">App.php line 296</a>                </li>
                                <li>
                at <abbr title="think\App">App</abbr>::exec(['type' => 'module', 'module' => ['home', 'login', 'send_code']], ['app_host' => '', 'app_debug' => <em>true</em>, 'app_trace' => <em>false</em>, ...]) in <a class="toggle" title="/data/www/default/thinkphp/library/think/App.php line 124">App.php line 124</a>                </li>
                                <li>
                at <abbr title="think\App">App</abbr>::run() in <a class="toggle" title="/data/www/default/thinkphp/start.php line 18">start.php line 18</a>                </li>
                                <li>
                at require('<a class="toggle" title="/data/www/default/thinkphp/start.php">/data/www/default/th...</a>') in <a class="toggle" title="/data/www/default/index.php line 17">index.php line 17</a>                </li>
                            </ol>
        </div>
    </div>
        
        <div class="exception-var">
        <h2>Exception Datas</h2>
                <table>
                        <caption>Error Context</caption>
            <tbody>
                                <tr>
                    <td>to</td>
                    <td>
                        15394244605                    </td>
                </tr>
                                <tr>
                    <td>datas</td>
                    <td>
                        [
    &quot;5864&quot;,
    &quot;60\u79d2&quot;
]                    </td>
                </tr>
                                <tr>
                    <td>tempId</td>
                    <td>
                        395660                    </td>
                </tr>
                                <tr>
                    <td>accountSid</td>
                    <td>
                        8aaf07086772ac61016781e9b604111c                    </td>
                </tr>
                                <tr>
                    <td>accountToken</td>
                    <td>
                        04ff7718c6bd472a9ec7ee496006fd72                    </td>
                </tr>
                                <tr>
                    <td>appId</td>
                    <td>
                        8aaf07086772ac61016781e9b6591122                    </td>
                </tr>
                                <tr>
                    <td>serverIP</td>
                    <td>
                        app.cloopen.com                    </td>
                </tr>
                                <tr>
                    <td>serverPort</td>
                    <td>
                        8883                    </td>
                </tr>
                                <tr>
                    <td>softVersion</td>
                    <td>
                        2013-12-26                    </td>
                </tr>
                                <tr>
                    <td>rest</td>
                    <td>
                        {}                    </td>
                </tr>
                                <tr>
                    <td>result</td>
                    <td>
                        {
    &quot;msg&quot;: &quot;\u53d1\u9001\u6210\u529f&quot;,
    &quot;code&quot;: 1,
    &quot;success&quot;: true
}                    </td>
                </tr>
                            </tbody>
                    </table>
            </div>
    
        <div class="exception-var">
        <h2>Environment Variables</h2>
                <div>
                        <div class="clearfix">
                <div class="col-md-3"><strong>GET Data</strong></div>
                <div class="col-md-9"><small>empty</small></div>
            </div>
                    </div>
                <div>
                        <h3 class="subheading">POST Data</h3>
            <div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>phone</strong></div>
                    <div class="col-md-9"><small>
                        15394244605                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>cate</strong></div>
                    <div class="col-md-9"><small>
                        1                    </small></div>
                </div>
                            </div>
                    </div>
                <div>
                        <div class="clearfix">
                <div class="col-md-3"><strong>Files</strong></div>
                <div class="col-md-9"><small>empty</small></div>
            </div>
                    </div>
                <div>
                        <h3 class="subheading">Cookies</h3>
            <div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>PHPSESSID</strong></div>
                    <div class="col-md-9"><small>
                        36530ba71e2829797fedd9c24f23b4a5                    </small></div>
                </div>
                            </div>
                    </div>
                <div>
                        <div class="clearfix">
                <div class="col-md-3"><strong>Session</strong></div>
                <div class="col-md-9"><small>empty</small></div>
            </div>
                    </div>
                <div>
                        <h3 class="subheading">Server/Request Data</h3>
            <div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>REDIRECT_UNIQUE_ID</strong></div>
                    <div class="col-md-9"><small>
                        XA8YGl9xUbA3uk-VpZd@RAAAAJU                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>REDIRECT_PATH_INFO</strong></div>
                    <div class="col-md-9"><small>
                        home/login/send_code/                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>REDIRECT_STATUS</strong></div>
                    <div class="col-md-9"><small>
                        200                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>UNIQUE_ID</strong></div>
                    <div class="col-md-9"><small>
                        XA8YGl9xUbA3uk-VpZd@RAAAAJU                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>HTTP_HOST</strong></div>
                    <div class="col-md-9"><small>
                        zhxbg.com                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>HTTP_CONNECTION</strong></div>
                    <div class="col-md-9"><small>
                        keep-alive                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>CONTENT_LENGTH</strong></div>
                    <div class="col-md-9"><small>
                        24                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>HTTP_CACHE_CONTROL</strong></div>
                    <div class="col-md-9"><small>
                        max-age=0                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>HTTP_ORIGIN</strong></div>
                    <div class="col-md-9"><small>
                        null                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>HTTP_USER_AGENT</strong></div>
                    <div class="col-md-9"><small>
                        Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.84 Safari/537.36                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>CONTENT_TYPE</strong></div>
                    <div class="col-md-9"><small>
                        application/x-www-form-urlencoded                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>HTTP_ACCEPT</strong></div>
                    <div class="col-md-9"><small>
                        */*                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>HTTP_ACCEPT_ENCODING</strong></div>
                    <div class="col-md-9"><small>
                        gzip, deflate                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>HTTP_ACCEPT_LANGUAGE</strong></div>
                    <div class="col-md-9"><small>
                        zh-CN,zh;q=0.9                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>HTTP_COOKIE</strong></div>
                    <div class="col-md-9"><small>
                        PHPSESSID=36530ba71e2829797fedd9c24f23b4a5                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>PATH</strong></div>
                    <div class="col-md-9"><small>
                        /usr/local/mysql/bin:/usr/local/pcre/bin:/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:/root/bin                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>LD_LIBRARY_PATH</strong></div>
                    <div class="col-md-9"><small>
                        /usr/local/apache/lib:/usr/local/openssl/lib                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>SERVER_SIGNATURE</strong></div>
                    <div class="col-md-9"><small>
                                            </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>SERVER_SOFTWARE</strong></div>
                    <div class="col-md-9"><small>
                        Apache                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>SERVER_NAME</strong></div>
                    <div class="col-md-9"><small>
                        zhxbg.com                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>SERVER_ADDR</strong></div>
                    <div class="col-md-9"><small>
                        45.248.68.235                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>SERVER_PORT</strong></div>
                    <div class="col-md-9"><small>
                        80                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>REMOTE_ADDR</strong></div>
                    <div class="col-md-9"><small>
                        125.120.228.54                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>DOCUMENT_ROOT</strong></div>
                    <div class="col-md-9"><small>
                        /data/www/default                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>REQUEST_SCHEME</strong></div>
                    <div class="col-md-9"><small>
                        http                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>CONTEXT_PREFIX</strong></div>
                    <div class="col-md-9"><small>
                                            </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>CONTEXT_DOCUMENT_ROOT</strong></div>
                    <div class="col-md-9"><small>
                        /data/www/default                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>SERVER_ADMIN</strong></div>
                    <div class="col-md-9"><small>
                        admin@localhost                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>SCRIPT_FILENAME</strong></div>
                    <div class="col-md-9"><small>
                        /data/www/default/index.php                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>REMOTE_PORT</strong></div>
                    <div class="col-md-9"><small>
                        52939                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>REDIRECT_URL</strong></div>
                    <div class="col-md-9"><small>
                        /home/login/send_code/                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>GATEWAY_INTERFACE</strong></div>
                    <div class="col-md-9"><small>
                        CGI/1.1                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>SERVER_PROTOCOL</strong></div>
                    <div class="col-md-9"><small>
                        HTTP/1.1                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>REQUEST_METHOD</strong></div>
                    <div class="col-md-9"><small>
                        POST                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>QUERY_STRING</strong></div>
                    <div class="col-md-9"><small>
                                            </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>REQUEST_URI</strong></div>
                    <div class="col-md-9"><small>
                        /home/login/send_code/                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>SCRIPT_NAME</strong></div>
                    <div class="col-md-9"><small>
                        /index.php                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>PHP_SELF</strong></div>
                    <div class="col-md-9"><small>
                        /index.php                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>REQUEST_TIME_FLOAT</strong></div>
                    <div class="col-md-9"><small>
                        1544493082.205                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>REQUEST_TIME</strong></div>
                    <div class="col-md-9"><small>
                        1544493082                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>PATH_INFO</strong></div>
                    <div class="col-md-9"><small>
                        home/login/send_code/                    </small></div>
                </div>
                            </div>
                    </div>
                <div>
                        <div class="clearfix">
                <div class="col-md-3"><strong>Environment Variables</strong></div>
                <div class="col-md-9"><small>empty</small></div>
            </div>
                    </div>
                <div>
                        <h3 class="subheading">ThinkPHP Constants</h3>
            <div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>APP_PATH</strong></div>
                    <div class="col-md-9"><small>
                        /data/www/default/application/                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>THINK_VERSION</strong></div>
                    <div class="col-md-9"><small>
                        5.0.12                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>THINK_START_TIME</strong></div>
                    <div class="col-md-9"><small>
                        1544493082.2066                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>THINK_START_MEM</strong></div>
                    <div class="col-md-9"><small>
                        404488                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>EXT</strong></div>
                    <div class="col-md-9"><small>
                        .php                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>DS</strong></div>
                    <div class="col-md-9"><small>
                        /                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>THINK_PATH</strong></div>
                    <div class="col-md-9"><small>
                        /data/www/default/thinkphp/                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>LIB_PATH</strong></div>
                    <div class="col-md-9"><small>
                        /data/www/default/thinkphp/library/                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>CORE_PATH</strong></div>
                    <div class="col-md-9"><small>
                        /data/www/default/thinkphp/library/think/                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>TRAIT_PATH</strong></div>
                    <div class="col-md-9"><small>
                        /data/www/default/thinkphp/library/traits/                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>ROOT_PATH</strong></div>
                    <div class="col-md-9"><small>
                        /data/www/default/                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>EXTEND_PATH</strong></div>
                    <div class="col-md-9"><small>
                        /data/www/default/extend/                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>VENDOR_PATH</strong></div>
                    <div class="col-md-9"><small>
                        /data/www/default/vendor/                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>RUNTIME_PATH</strong></div>
                    <div class="col-md-9"><small>
                        /data/www/default/runtime/                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>LOG_PATH</strong></div>
                    <div class="col-md-9"><small>
                        /data/www/default/runtime/log/                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>CACHE_PATH</strong></div>
                    <div class="col-md-9"><small>
                        /data/www/default/runtime/cache/                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>TEMP_PATH</strong></div>
                    <div class="col-md-9"><small>
                        /data/www/default/runtime/temp/                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>CONF_PATH</strong></div>
                    <div class="col-md-9"><small>
                        /data/www/default/application/                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>CONF_EXT</strong></div>
                    <div class="col-md-9"><small>
                        .php                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>ENV_PREFIX</strong></div>
                    <div class="col-md-9"><small>
                        PHP_                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>IS_CLI</strong></div>
                    <div class="col-md-9"><small>
                        false                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>IS_WIN</strong></div>
                    <div class="col-md-9"><small>
                        false                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>IS_CGI</strong></div>
                    <div class="col-md-9"><small>
                        0                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>_PHP_FILE_</strong></div>
                    <div class="col-md-9"><small>
                        /index.php                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>ROOT_URL</strong></div>
                    <div class="col-md-9"><small>
                                            </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>IS_AJAX</strong></div>
                    <div class="col-md-9"><small>
                        false                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>IS_GET</strong></div>
                    <div class="col-md-9"><small>
                        false                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>IS_POST</strong></div>
                    <div class="col-md-9"><small>
                        true                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>MODULE_NAME</strong></div>
                    <div class="col-md-9"><small>
                        home                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>CONTROLLER_NAME</strong></div>
                    <div class="col-md-9"><small>
                        Login                    </small></div>
                </div>
                                <div class="clearfix">
                    <div class="col-md-3"><strong>ACTION_NAME</strong></div>
                    <div class="col-md-9"><small>
                        send_code                    </small></div>
                </div>
                            </div>
                    </div>
            </div>
    
    <div class="copyright">
        <a title="官方网站" href="http://www.thinkphp.cn">ThinkPHP</a> 
        <span>V5.0.12</span> 
        <span>{ 十年磨一剑-为API开发设计的高性能框架 }</span>
    </div>
        <script>
        var LINE = 69;

        function $(selector, node){
            var elements;

            node = node || document;
            if(document.querySelectorAll){
                elements = node.querySelectorAll(selector);
            } else {
                switch(selector.substr(0, 1)){
                    case '#':
                        elements = [node.getElementById(selector.substr(1))];
                        break;
                    case '.':
                        if(document.getElementsByClassName){
                            elements = node.getElementsByClassName(selector.substr(1));
                        } else {
                            elements = get_elements_by_class(selector.substr(1), node);
                        }
                        break;
                    default:
                        elements = node.getElementsByTagName();
                }
            }
            return elements;

            function get_elements_by_class(search_class, node, tag) {
                var elements = [], eles, 
                    pattern  = new RegExp('(^|\\s)' + search_class + '(\\s|$)');

                node = node || document;
                tag  = tag  || '*';

                eles = node.getElementsByTagName(tag);
                for(var i = 0; i < eles.length; i++) {
                    if(pattern.test(eles[i].className)) {
                        elements.push(eles[i])
                    }
                }

                return elements;
            }
        }

        $.getScript = function(src, func){
            var script = document.createElement('script');
            
            script.async  = 'async';
            script.src    = src;
            script.onload = func || function(){};
            
            $('head')[0].appendChild(script);
        }

        ;(function(){
            var files = $('.toggle');
            var ol    = $('ol', $('.prettyprint')[0]);
            var li    = $('li', ol[0]);   

            // 短路径和长路径变换
            for(var i = 0; i < files.length; i++){
                files[i].ondblclick = function(){
                    var title = this.title;

                    this.title = this.innerHTML;
                    this.innerHTML = title;
                }
            }

            // 设置出错行
            var err_line = $('.line-' + LINE, ol[0])[0];
            err_line.className = err_line.className + ' line-error';

            $.getScript('//cdn.bootcss.com/prettify/r298/prettify.min.js', function(){
                prettyPrint();

                // 解决Firefox浏览器一个很诡异的问题
                // 当代码高亮后，ol的行号莫名其妙的错位
                // 但是只要刷新li里面的html重新渲染就没有问题了
                if(window.navigator.userAgent.indexOf('Firefox') >= 0){
                    ol[0].innerHTML = ol[0].innerHTML;
                }
            });

        })();
    </script>
    </body>
</html>