<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:56:"D:\phpStudy\WWW\cp/application/adminz\view\nba\edit.html";i:1545616005;s:59:"D:\phpStudy\WWW\cp/application/adminz\view\public\base.html";i:1545616011;}*/ ?>
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
                        <h3 class="box-title">编辑</h3>
                        <div class="pull-right">
                            <a href="<?php echo url('nba/index'); ?>" class="btn btn-link btn-sm"><i class="fa fa-angle-double-left"></i>&nbsp;返回列表</a>
                        </div>
                    </div>
                    <ul class="nav nav-tabs">
                        <li class="active" data-id="tab_base"><a href="javascript:;">基础信息</a></li>
                    </ul>
                    <form id="form_submit" enctype="multipart/form-data" class="layui-form layui-form-pane" action="" method="post">
                        <div class="box-body" id="tab_base">
                            <div class="layui-form-item">
                                <label class="layui-form-label">比赛编号</label>
                                <div class="layui-input-block">
                                    <input type="text" name="game_no" value="<?php echo $data['game_no']; ?>" lay-verify="required" class="layui-input" placeholder="比赛名称" >
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">比赛名称</label>
                                <div class="layui-input-block">
                                    <input type="text" name="game_name" value="<?php echo $data['game_name']; ?>" class="layui-input" placeholder="比赛名称" >
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">主队</label>
                                <div class="layui-input-block">
                                    <input type="text" name="home_team" value="<?php echo $data['home_team']; ?>" class="layui-input" placeholder="主队" >
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">主队分数</label>
                                <div class="layui-input-block">
                                    <input type="text" name="home_score" value="<?php echo $data['home_score']; ?>" placeholder="主队分数" lay-verify="required" class="layui-input" >
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">客队</label>
                                <div class="layui-input-block">
                                    <input type="text" name="road_team" value="<?php echo $data['road_team']; ?>" class="layui-input" placeholder="客队" >
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">客队分数</label>
                                <div class="layui-input-block">
                                    <input type="text" name="road_score" value="<?php echo $data['road_score']; ?>" placeholder="客队分数" lay-verify="required" class="layui-input" >
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">总分</label>
                                <div class="layui-input-block">
                                    <input type="text" name="total_score" value="<?php echo $data['total_score']; ?>" placeholder="总分" lay-verify="required" class="layui-input" >
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">让分数</label>
                                <div class="layui-input-block">
                                    <input type="text" name="let_score" value="<?php echo $data['let_score']; ?>" placeholder="“-”主胜“+”客胜" lay-verify="required" class="layui-input" >
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">投注截止时间</label>
                                <div class="layui-input-block">
                                    <input type="text" id="date" name="end_time" value="<?php echo $data['end_time']; ?>" class="layui-input" placeholder="比赛结束时间" >
                                </div>
                            </div>
                            <h3 class="box-title">投注选项赔率</h3>
                            <?php if(is_array($code) || $code instanceof \think\Collection || $code instanceof \think\Paginator): $i = 0; $__LIST__ = $code;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                            <div class="layui-form-item">
                                <?php if(is_array($item) || $item instanceof \think\Collection || $item instanceof \think\Paginator): $i = 0; $__LIST__ = $item;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($i % 2 );++$i;?>
                                <div style="width: 130px;float: left;margin-bottom: 15px">
                                    <label class="layui-form-label"><?php echo $info['cate_name']; ?></label>
                                    <input style="width: 120px;text-align: center;" type="text" name="<?php echo $info['cate_code']; ?>" value="<?php echo $info['cate_odds']; ?>" lay-verify="required" placeholder="<?php echo $info['cate_name']; ?>赔率" class="layui-input"> 
                                </div>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </div>
                            <?php endforeach; endif; else: echo "" ;endif; ?>                      
                        </div></div>
                        <div class="layui-input-block">
                            <button type="submit" class="layui-btn">保存</button>
                            <label for="jump_list" style="margin-left: 15px;"><input type="checkbox" data-url="<?php echo url('nba/index'); ?>" id="jump_list" value="1" >返回列表页</label>
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

<script>
layui.use(['form', 'layedit', 'laydate','upload','table'], function(){
    var form = layui.form
    ,layer = layui.layer
    ,layedit = layui.layedit
    ,laydate = layui.laydate
    ,upload = layui.upload;

    var table = layui.table; 
    //日期
    laydate.render({
        elem: '#date'
        ,type: 'datetime'
    });
    laydate.render({
        elem: '#date1'
        ,type: 'datetime'
    }); 
    laydate.render({
        elem: '#cate_id'
    });
})
</script>

</html>