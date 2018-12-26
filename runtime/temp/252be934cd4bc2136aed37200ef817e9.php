<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:60:"D:\phpStudy\WWW\cp/application/adminz\view\tpl\ads_list.html";i:1544679545;}*/ ?>
<table class="layui-table table">
  <colgroup>
    <col width="150">
    <col width="200">
    <col>
  </colgroup>
  <thead>
    <tr >
      <th style="text-align: center;" width="10%">#</th>
      <th style="text-align: center;" width="20%">广告位置</th>
      <th style="text-align: center;" width="20%">跳转地址</th>
      <th style="text-align: center;" width="20%">图片</th>
      <th style="text-align: center;" width="10%">操作</th>
    </tr> 
  </thead>
  <tbody>
  	<?php if(is_array($_list) || $_list instanceof \think\Collection || $_list instanceof \think\Paginator): $key = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($key % 2 );++$key;?>
    <tr>
      <td style="text-align: center;"><?php echo $key; ?></td>
      <td style="text-align: center;"><?php echo (isset($info['position_name']) && ($info['position_name'] !== '')?$info['position_name']:''); ?></td>
      <td style="text-align: center;"><?php echo (isset($info['url']) && ($info['url'] !== '')?$info['url']:''); ?></td>
      <td style="text-align: center;"><img src="__UPLOADS__/ads/<?php echo (isset($info['pic']) && ($info['pic'] !== '')?$info['pic']:''); ?>"></td>
      <td style="text-align: center;">
      	<div class="btn-group btn-group-sm">
			<a href="<?php echo url('ads/ads_delete'); ?>?id=<?php echo $info['ads_id']; ?>">删除</a>&nbsp;&nbsp;&nbsp;
			<a href="<?php echo url('ads/edit'); ?>?ads_id=<?php echo $info['ads_id']; ?>">编辑</a>
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
<div class="box-footer clearfix">
  <ul class="pagination pull-right"><?php echo $page; ?></ul>
</div>