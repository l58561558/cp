<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:59:"D:\phpStudy\WWW\cp/application/adminz\view\order\index.html";i:1545729759;s:59:"D:\phpStudy\WWW\cp/application/adminz\view\public\base.html";i:1545616011;}*/ ?>
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
		              	<h3 class="box-title">订单列表</h3>
		            </div>
		            <div class="pull-left">
						<label style="margin-left: 10px;font-size: 18px" class="labels">游戏分类:</label>
				  		<select style="height: 24px;margin-right: 50px" id="cate">
				  			<option value="0">全部</option>
				  			<?php if(is_array($cate) || $cate instanceof \think\Collection || $cate instanceof \think\Paginator): $i = 0; $__LIST__ = $cate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
				  			<option value="<?php echo $item['game_id']; ?>"><?php echo $item['game_name']; ?></option>
                           	<?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
						<label style="margin-left: 10px;font-size: 18px" class="labels">订单类型:</label>
				  		<select style="height: 24px;margin-right: 50px" id="type">
				  			<option value="0">全部</option>
				  			<option value="1">普通订单</option>
				  			<option value="2">合买订单</option>
				  			<option value="3">跟单订单</option>
						</select>
						<label style="margin-left: 10px;font-size: 18px" class="labels">混合搜索:</label>
                        <input type="text" class="order_no" placeholder="订单编号(选填)">
                        <input type="text" class="yhid" placeholder="用户编号(选填)">
                        <input type="text" class="add_time" placeholder="开始时间(选填)" id="date">
                        <input type="text" class="end_time" placeholder="结束时间(选填)" id="date1">
                        <button class="layui-btn" style="height: 27px;padding: 0 10px;line-height: 16px;">搜索</button>
                    </div>
		            <div class="panel-body">
				  		
					</div>
		            <div id="data_list_info"></div>
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

	<script type="text/javascript">
		var user_id = "<?php echo $user_id; ?>";
		$("#cate").change(function(){
			getData();
		})
		$("#type").change(function(){
			getData();
		})
		function getData(p){
			if(p==undefined || p<1)
				p = 1;
			var cate = $("#cate").val();
			var type = $("#type").val();
			var order_no = $(".order_no").val();
			var yhid = $(".yhid").val();
			var add_time = $(".add_time").val();
	    	var end_time = $(".end_time").val();
	    	
			result = {
				page:p,
				cate:cate,
				type:type,
				order_no:order_no,
				add_time:add_time,
				end_time:end_time,
				yhid:yhid,
				user_id:user_id
			}
			$.post("<?php echo url('order/get_order_list'); ?>",{page:p,result:result},function(res){
				if(res.code == 1){
					$("#data_list_info").html('').html(res.data);
				}
			},'json');
		}
		$(function(){
			getData();
			$(".layui-btn").click(function(){
				getData();
			})
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