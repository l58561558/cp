<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:57:"D:\phpStudy\WWW\cp/application/adminz\view\user\edit.html";i:1545123003;s:59:"D:\phpStudy\WWW\cp/application/adminz\view\public\base.html";i:1539586792;}*/ ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title><?php echo $site['seo_title']; ?></title>
  <link rel="stylesheet" href="__ADMIN__/layui/css/layui.css">
  <link rel="stylesheet" href="__ADMIN__/css/admin.css">
  <link rel="stylesheet" href="__ADMIN__/layui/layer/theme/default/layer.css">
  <link href="__PLUGINS__/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
  <link href="__PLUGINS__/adminlte/css/AdminLTE.min.css" rel="stylesheet" type="text/css"/>
  <link rel="stylesheet" href="__PLUGINS__/fonts/css/font-awesome.min.css">
  <link rel="stylesheet" href="__PLUGINS__/fonts/css/ionicons.min.css">
  <link rel="stylesheet" href="__PLUGINS__/adminlte/css/skins/_all-skins.min.css">
</head>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
  <div class="layui-header">
    <div class="layui-logo" style="font-size: 22px"><?php echo $site['admin_title']; ?></div>

    <!-- 头部区域（可配合layui已有的水平导航） -->
    <ul class="layui-nav layui-layout-right">

      <li class="layui-nav-item"><a href="<?php echo url('/'); ?>" target="_blank"><span class="fa fa-reply"></span>回首页</a></li>
      <li class="layui-nav-item"><a href="javascript:;" class="dropdown-toggle btn-cache "><span class="fa fa-refresh"></span>清除缓存</a></li>
      <li class="layui-nav-item">
        <a href="javascript:;">
          <span class="hidden-xs"><i class="fa fa-user"></i>管理员</span>
        </a>
        <dl class="layui-nav-child">
          <dd><a onclick="this.innerHTML='正在退出...';$.post('<?php echo url('login/logout'); ?>',function(data){location.reload();},'json');" href="javascript:;">退出登录</a></dd>
        </dl>
      </li>
    </ul>
  </div>
  
  <div class="layui-side layui-bg-black"> 
    <div class="layui-side-scroll">
      <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
      <ul class="layui-nav layui-nav-tree"  lay-filter="test">
      	<?php if(is_array((isset($menu_list) && ($menu_list !== '')?$menu_list:array())) || (isset($menu_list) && ($menu_list !== '')?$menu_list:array()) instanceof \think\Collection || (isset($menu_list) && ($menu_list !== '')?$menu_list:array()) instanceof \think\Paginator): $i = 0; $__LIST__ = (isset($menu_list) && ($menu_list !== '')?$menu_list:array());if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['is_menu'] == 1): ?>
	        <li  class="layui-nav-item <?php if(in_array($names['controllerName'],$vo['controllers'])): ?>layui-nav-itemed<?php endif; ?> ">
	          <a class="" href="javascript:;">
              <span><?php echo $vo['node_name']; ?></span>
              <?php if(isset($vo['count'])): ?>
              <img src="__ADMIN__/images/red.png">
              <span style="position: relative;right: 18px;"><?php echo $vo['count']; ?></span>
              <?php endif; ?>
            </a>
	          <dl class="layui-nav-child">
	          	<?php if(is_array($vo['menu_list']) || $vo['menu_list'] instanceof \think\Collection || $vo['menu_list'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['menu_list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo2): $mod = ($i % 2 );++$i;if($vo2['is_menu'] == 1): ?> 
	            	<dd class="<?php if($names['controllerName'] == $vo2['controller'] and $names['actionName'] == $vo2['action']): ?>layui-this<?php endif; ?>">
                  <a href="<?php echo url($vo2['url']); ?>">
                  <?php echo $vo2['node_name']; if(isset($vo['count'])): ?>
                  <img src="__ADMIN__/images/red.png">
                  <span style="position: relative;right: 18px;"><?php echo $vo['count']; ?></span>
                  <?php endif; ?>
                  </a>
                </dd>
	            	<?php endif; endforeach; endif; else: echo "" ;endif; ?>
	          </dl>
	        </li>
	        <?php endif; endforeach; endif; else: echo "" ;endif; ?>
      </ul>
    </div>
  </div>
  
  <div class="layui-body">
    
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">编辑用户</h3>
                        <div class="pull-right">
                            <a href="<?php echo url('admin/index'); ?>" class="btn btn-link btn-sm"><i class="fa fa-angle-double-left"></i>&nbsp;返回列表</a>
                        </div>
                    </div>
                    <ul class="nav nav-tabs">
                        <li class="active" data-id="tab_base"><a href="javascript:;">基础信息</a></li>
                    </ul>
                    <form id="form_submit" enctype="multipart/form-data" class="layui-form layui-form-pane" action="" method="post">
                        <div class="box-body" id="tab_base">
                            <style type="text/css">
                                .layui-form-pane .layui-form-label{
                                    width: 15%;
                                }
                                .layui-form-pane .layui-input-block{
                                    margin-left: 15%;
                                }
                            </style>
                            <div class="layui-form-item">
                                <label class="layui-form-label">用户编号</label>
                                <div class="layui-input-block">
                                    <input type="text" style="background-color: #FBFBFB" readonly="readonly" value="<?php echo $user['yhid']; ?>" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">手机号</label>
                                <div class="layui-input-block">
                                    <input type="text" style="background-color: #FBFBFB" readonly="readonly" value="<?php echo $user['phone']; ?>" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">上级用户ID</label>
                                <div class="layui-input-block">
                                    <input type="text" style="background-color: #FBFBFB" readonly="readonly" value="<?php echo $user['pid']; ?>" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">用户名</label>
                                <div class="layui-input-block">
                                    <input type="text" style="background-color: #FBFBFB" readonly="readonly" value="<?php echo $user['user_name']; ?>" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">银行卡号</label>
                                <div class="layui-input-block">
                                    <input type="text" style="background-color: #FBFBFB" readonly="readonly" value="<?php echo $user['bank_card']; ?>" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">密码</label>
                                <div class="layui-input-block">
                                    <input type="text" name="pswd" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">状态</label>
                                <div class="layui-input-block">
                                    <input type="radio" id="status_yes" name="status" value="0" title="正常玩家" <?php if($user['status'] == 0): ?>checked<?php endif; ?>>
                                    <div class="layui-unselect layui-form-radio layui-form-radioed">
                                        <i class="layui-anim layui-icon layui-anim-scaleSpring"></i>
                                        <div>启用</div>
                                    </div>
                                    <input type="radio" id="status_no" name="status" value="1" title="内部账号" <?php if($user['status'] == 1): ?>checked<?php endif; ?>>
                                    <div class="layui-unselect layui-form-radio">
                                        <i class="layui-anim layui-icon"></i>
                                        <div>禁用</div>
                                    </div>
                                </div>
                            </div>
                        </div></div>
                        <div class="layui-input-block">
                            <button type="submit" class="layui-btn">保存</button>
                            <label for="jump_list" style="margin-left: 15px;"><input type="checkbox" data-url="<?php echo url('user/index'); ?>" id="jump_list" value="1" >返回列表页</label>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

  </div>

  <div class="layui-footer">
    <!-- 底部固定区域 -->
  <?php
  	$url = strtolower(request()->action());
  	if($url == 'index'){
  ?>
    <!-- <strong>Copyright © 2017-2018 <a href="/">故乡人网络技术支持</a>.</strong> -->
   <?php } ?>
  </div>

</div>

</body>

<script src="__PLUGINS__/jquery/js/jquery-2.2.3.min.js" type="text/javascript"></script>
<script src="__PLUGINS__/jquery/js/jquery.cookie.min.js" type="text/javascript"></script>
<script src="__PLUGINS__/jquery/js/jquery.form.js" type="text/javascript"></script>
<script type="text/javascript" src="__PLUGINS__/bootstrap/js/bootstrap.js"></script>
<script type="text/javascript" src="__PLUGINS__/bootstrap/js/bootstrap-notify.js"></script>
<script type="text/javascript" src="__PLUGINS__/adminlte/js/app.js"></script>
<script type="text/javascript" src="__PLUGINS__/adminlte/js/demo.js"></script>
<script type="text/javascript" src="__PLUGINS__/validform/Validform_v5.3.2.js"></script>
<script type="text/javascript" src="__ADMIN__/js/tips.js"></script>
<script type="text/javascript" src="__ADMIN__/js/admin.js"></script>
<script type="text/javascript" src="__ADMIN__/layui/layui.js"></script>
<script type="text/javascript" src="__ADMIN__/layui/layui.all.js"></script>
<script type="text/javascript" src="__ADMIN__/layui/layer/layer.js"></script>
<script>
//JavaScript代码区域
layui.use('element', function(){
  var element = layui.element;
  
});
</script>

<script charset="utf-8" src="__PLUGINS__/KindEditor/kindeditor.js"></script>
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
          var form = layui.form
          ,layer = layui.layer
          ,layedit = layui.layedit
          ,laydate = layui.laydate;
          
          //日期
          laydate.render({
            elem: '#date'
          });
          laydate.render({
            elem: '#date1'
          }); 
        });
        // $(function(){
        //     //初始化载入编辑器
        //     var editor; //定义局部顶部变量
        //     editorCreate();
        //     function editorCreate(){
        //         editor = KindEditor.create('textarea[name="remarks"]',{
        //             afterCreate:function(){
        //                 if(editor.isEmpty()){ //跟自动保存的条件相对应
        //                     //editorAutorestore();
        //                 }
        //             },
        //             afterBlur: function(){this.sync()},
        //             uploadJson:"<?php echo url('base/editorUpload'); ?>"
        //         });
        //     };
        // });
    </script>

</html>