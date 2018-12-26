<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:58:"D:\phpStudy\WWW\cp/application/adminz\view\admin\root.html";i:1536138418;s:59:"D:\phpStudy\WWW\cp/application/adminz\view\public\base.html";i:1539586792;}*/ ?>
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
		              <h3 class="box-title">权限列表</h3>
		              <div class="pull-right">
			              <a href="<?php echo url('admin/add_root',['role_id'=>$role_id]); ?>" class="btn btn-success btn-sm"><i class="fa fa-plus"></i>&nbsp;添加权限</a>
			          </div>
		            </div>
		            <div id="data_list_info">
						<div class="box-body">
						  	<table class="layui-table table">
						        <tbody>
						            <tr style="background-color: #f5f5f5;">
										<th>权限名称</th>
										<th>权限编码</th>
										<th>是否启用</th>
										<th>排序</th>
										<th style="width: 100px">操作</th>
						            </tr>
						            <?php if(is_array($_list) || $_list instanceof \think\Collection || $_list instanceof \think\Paginator): $key = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($key % 2 );++$key;?>
										<tr class="treegrid-<?php echo $info['role_node_id']; ?>">
											<td>
												<?php if(!(empty($info['child']) || (($info['child'] instanceof \think\Collection || $info['child'] instanceof \think\Paginator ) && $info['child']->isEmpty()))): ?>
													<i class="fa fa-caret-right treegrid" data-nodeid="<?php echo $info['role_node_id']; ?>"></i>
												<?php endif; ?>
												<?php echo (isset($info['authority_name']) && ($info['authority_name'] !== '')?$info['authority_name']:''); ?>
											</td>
											<td><?php echo $info['authority_code']; ?></td>
											<td>
												<?php if($info['is_enable'] == 1): ?>
													<a class='btn-field' name='is_enable' href='javascript:;' title='点击更改状态' data-status='0' data-id='<?php echo $info['role_node_id']; ?>'' data-url="<?php echo url('admin/root_edit_field'); ?>"><i class='fa fa-check'></i></a>
												<?php else: ?>
													<a class='btn-field' name='is_enable' href='javascript:;' title='点击更改状态' data-status='1' data-id='<?php echo $info['role_node_id']; ?>' data-url="<?php echo url('admin/root_edit_field'); ?>"><i class='fa fa-close'></i></a>
												<?php endif; ?>
											</td>
											<td>
												<input class="inputs" data-id="<?php echo $info['role_node_id']; ?>" data-url="<?php echo url('admin/root_edit_field'); ?>" name="sort_order" type="text" value="<?php echo $info['sort_order']; ?>" >
											</td>
											<td>
												<div class="btn-group btn-group-sm">
													<!-- <a href="javascript:;" data-container='body' data-placement="left" data-html='true' data-trigger="focus" data-title="删除提示" data-content='<p>您确定要删除吗?</p><button type="button" onclick="delete_data(this);" data-id="<?php echo $info['role_node_id']; ?>" data-url="<?php echo url('admin/root_delete'); ?>" class="btn btn-danger btn-sm">删除</button>&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-info btn-sm">取消</button>' name="delete" style="color:red;" onclick="win_delete(this);">删除</a>&nbsp;&nbsp;&nbsp; -->
													<a class="btn btn-danger btn-sm" href="<?php echo url('admin/root_delete'); ?>?role_node_id=<?php echo $info['role_node_id']; ?>">删除</a>
												</div>
											</td>
										</tr>
									<?php endforeach; endif; else: echo "" ;endif; if(empty($_list) || (($_list instanceof \think\Collection || $_list instanceof \think\Paginator ) && $_list->isEmpty())): ?>
										<tr>
											<td colspan="15">空空如也~</td>
										</tr>
									<?php endif; ?>
						      	</tbody>
						  	</table>
						</div>	            	
		            </div>
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



</html>