<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:67:"D:\phpStudy\WWW\cp/application/adminz\view\football\order_info.html";i:1545708392;s:59:"D:\phpStudy\WWW\cp/application/adminz\view\public\base.html";i:1545616011;}*/ ?>
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
                        <h3 class="box-title">订单详情</h3>
                        <div class="pull-right">
                            <a href="<?php echo url('football/football_order'); ?>" class="btn btn-link btn-sm"><i class="fa fa-angle-double-left"></i>&nbsp;返回列表</a>
                        </div>
                    </div>
                    <table class="layui-table" >
                        <thead>
                            <tr>
                                <th colspan="7" style="text-align: center;">订单信息</th>
                            </tr>
                            <tr>
                                <th style="text-align: center;">订单编号</th>
                                <th style="text-align: center;">订单创建时间</th>
                                <th style="text-align: center;">订单金额</th>
                                <th style="text-align: center;">中奖金额</th>
                                <th style="text-align: center;">比赛场次</th>
                                <th style="text-align: center;">串法</th>
                                <th style="text-align: center;">倍数</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center;"><?php echo $data['order_no']; ?></td>
                                <td style="text-align: center;"><?php echo $data['add_time']; ?></td>
                                <td style="text-align: center;"><?php echo $data['order_money']; ?></td>
                                <td style="text-align: center;"><?php echo $data['win_money']; ?></td>
                                <td style="text-align: center;"><?php echo $data['game_num']; ?></td>
                                <td style="text-align: center;"><?php echo $data['chuan']; ?></td>
                                <td style="text-align: center;"><?php echo $data['multiple']; ?></td>
                            </tr>
                        </tbody>
                        
                        <thead>
                            <tr>
                                <th style="text-align: center;">场次</th>
                                <th style="text-align: center;">星期</th>
                                <th style="text-align: center;">主队VS客队</th>
                                <th style="text-align: center;">主队:客队(比分)</th>
                                <th style="text-align: center;">投注内容</th>
                                <th style="text-align: center;">彩果</th>
                                <th style="text-align: center;">比赛状态</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(is_array($data['order_info']) || $data['order_info'] instanceof \think\Collection || $data['order_info'] instanceof \think\Paginator): $i = 0; $__LIST__ = $data['order_info'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                            <tr>
                                <td style="text-align: center;"><?php echo $item['game_no']; ?></td>
                                <td style="text-align: center;"><?php echo $item['week']; ?></td>
                                <td style="text-align: center;"><?php echo $item['home_team']; ?> VS <?php echo $item['road_team']; ?></td>
                                <td style="text-align: center;"><?php echo $item['down_score']; ?></td>
                                <td style="text-align: center;">
                                    <?php if(is_array($item['tz_result']) || $item['tz_result'] instanceof \think\Collection || $item['tz_result'] instanceof \think\Paginator): $i = 0; $__LIST__ = $item['tz_result'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($i % 2 );++$i;?>
                                        <?php echo $info['cate_name']; ?><?php echo $info['cate_odds']; if($info['is_win'] == 0): ?><span style="color: #ce6a14">(未开奖)</span><?php endif; if($info['is_win'] == 1): ?><span style="color: green">(正确)</span><?php endif; if($info['is_win'] == 2): ?><span style="color: red">(不正确)</span><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if(is_array($item['win_result']) || $item['win_result'] instanceof \think\Collection || $item['win_result'] instanceof \think\Paginator): $i = 0; $__LIST__ = $item['win_result'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cate): $mod = ($i % 2 );++$i;?>
                                        (<?php echo $cate['cate_name']; ?><?php echo $cate['cate_odds']; ?>)
                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if($item['status'] == 0): ?><span style="color: green">已结算</span><?php else: ?><span style="color: red">未结算</span><?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; endif; else: echo "" ;endif; if(empty($data)): ?>
                            <tr><td colspan="7" style="text-align: center;">空空如也~</td></tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if($data['order_type'] == 2): ?>
                        <thead>
                            <tr><th colspan="7" style="text-align: center;">合买信息</th></tr>
                            <tr>
                                <th style="text-align: center;">合买发起人订单号</th>
                                <th style="text-align: center;">合买发起人</th>
                                <th style="text-align: center;">合买标题</th>
                                <th style="text-align: center;">方案佣金</th>
                                <th style="text-align: center;">方案发起时间</th>
                                <th style="text-align: center;">方案截止时间</th>
                                <th style="text-align: center;">合买状态</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center;"><?php echo $data['hm']['order_no']; ?></td>
                                <td style="text-align: center;"><?php echo $data['hm']['user_name']; ?></td>
                                <td style="text-align: center;"><?php echo $data['hm']['hm_title']; ?></td>
                                <td style="text-align: center;"><?php echo $data['hm']['brokerage']; ?></td>
                                <td style="text-align: center;"><?php echo $data['hm']['add_time']; ?></td>
                                <td style="text-align: center;"><?php echo $data['hm']['end_time']; ?></td>
                                <td style="text-align: center;"><?php echo $data['hm']['hm_status']; ?></td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th style="text-align: center;">方案金额</th>
                                <th style="text-align: center;">方案份数</th>
                                <th style="text-align: center;">单份金额</th>
                                <th style="text-align: center;">自购份数</th>
                                <th style="text-align: center;">保底份数</th>
                                <th style="text-align: center;">剩余份数</th>
                                <th style="text-align: center;">方案中奖金额</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center;"><?php echo $data['hm']['hm_money']; ?></td>
                                <td style="text-align: center;"><?php echo $data['hm']['hm_num']; ?></td>
                                <td style="text-align: center;"><?php echo $data['hm']['one_money']; ?></td>
                                <td style="text-align: center;"><?php echo $data['hm']['pay_num']; ?></td>
                                <td style="text-align: center;"><?php echo $data['hm']['bd_num']; ?></td>
                                <td style="text-align: center;"><?php echo $data['hm']['residue_num']; ?></td>
                                <td style="text-align: center;"><?php echo $data['hm']['total_win_money']; ?></td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th style="text-align: center;">订单编号</th>
                                <th style="text-align: center;">用户编号</th>
                                <th style="text-align: center;">用户昵称</th>
                                <th style="text-align: center;">购买份数</th>
                                <th style="text-align: center;">购买金额</th>
                                <th style="text-align: center;">中奖金额</th>
                                <th style="text-align: center;">参与时间</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if(is_array($data['hm_user']) || $data['hm_user'] instanceof \think\Collection || $data['hm_user'] instanceof \think\Paginator): $i = 0; $__LIST__ = $data['hm_user'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                            <tr <?php if($data['order_no'] == $item['order_no']): ?> style="background-color: #00c0ef" <?php endif; ?>>
                                <td style="text-align: center;"><?php echo $item['order_no']; ?></td>
                                <td style="text-align: center;"><?php echo $item['yhid']; ?></td>
                                <td style="text-align: center;"><?php echo $item['user_name']; ?></td>
                                <td style="text-align: center;"><?php echo $item['pay_num']; ?></td>
                                <td style="text-align: center;"><?php echo $item['pay_money']; ?></td>
                                <td style="text-align: center;"><?php echo $item['win_money']; ?></td>
                                <td style="text-align: center;"><?php echo $item['add_time']; ?></td>
                            </tr>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </tbody>
                        <?php endif; ?>
                    </table> 
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
    });
    laydate.render({
        elem: '#date1'
    }); 
    laydate.render({
        elem: '#cate_id'
    });
</script>

</html>