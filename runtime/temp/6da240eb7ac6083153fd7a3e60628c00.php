<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:63:"D:\phpStudy\WWW\cp/application/adminz\view\tpl\tixian_list.html";i:1545183322;}*/ ?>
<table class="layui-table table">
  <colgroup>
    <col width="150">
    <col width="200">
    <col>
  </colgroup>
  <thead>
    <tr >
		<th style="text-align: center;" style="width: 10px">#</th>
		<th style="text-align: center;">用户编号</th>
		<th style="text-align: center;">用户名</th>
		<th style="text-align: center;">银行卡号</th>
		<th style="text-align: center;">提现状态</th>
		<th style="text-align: center;">提现金额</th>
		<th style="text-align: center;">申请时间</th>
		<th style="text-align: center;">财务操作时间</th>
		<th style="text-align: center;">操作</th>
    </tr> 
  </thead>
    <tbody>
  	<?php if(is_array($_list) || $_list instanceof \think\Collection || $_list instanceof \think\Paginator): $key = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($key % 2 );++$key;?>
    <tr>
		<td style="text-align: center;"><?php echo $key; ?></td>
		<td style="text-align: center;"><?php echo (isset($info['yhid']) && ($info['yhid'] !== '')?$info['yhid']:'-'); ?></td>
		<td style="text-align: center;"><?php echo (isset($info['name']) && ($info['name'] !== '')?$info['name']:'-'); ?></td>
		<td style="text-align: center;"><?php echo (isset($info['bank_card']) && ($info['bank_card'] !== '')?$info['bank_card']:'-'); ?></td>
		<td style="text-align: center;"><?php echo (isset($info['withdraw_status']) && ($info['withdraw_status'] !== '')?$info['withdraw_status']:'-'); ?></td>
		<td style="text-align: center;"><?php echo (isset($info['withdraw_money']) && ($info['withdraw_money'] !== '')?$info['withdraw_money']:'-'); ?></td>
		<td style="text-align: center;"><?php echo (isset($info['add_time']) && ($info['add_time'] !== '')?$info['add_time']:'-'); ?></td>
		<td style="text-align: center;"><?php echo (isset($info['edit_time']) && ($info['edit_time'] !== '')?$info['edit_time']:'-'); ?></td>
		<td style="text-align: center;">
	      	<div class="btn-group btn-group-sm">
				<?php if($info['status'] == 1): ?>
				<a href="<?php echo url('tixian/succeed'); ?>?id=<?php echo $info['id']; ?>&user_id=<?php echo $info['user_id']; ?>&status=3">提现成功</a>
				<a href="<?php echo url('tixian/refuse'); ?>?id=<?php echo $info['id']; ?>&user_id=<?php echo $info['user_id']; ?>&status=4">拒绝提现</a>
				<?php else: ?>
				<a><?php echo $info['withdraw_status']; ?></a>
				<?php endif; ?>
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