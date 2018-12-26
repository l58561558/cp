<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:72:"D:\phpStudy\WWW\cp/application/adminz\view\tpl\account_details_list.html";i:1545616018;}*/ ?>
<table class="layui-table table">
  <colgroup>
    <col width="150">
    <col width="200">
    <col>
  </colgroup>
  <thead>
		<tr style="background-color: #009688;color: #fff;">
			<?php if(isset($id)): ?>
			<th colspan="9" style="font-size: 25px;text-align: center;">统计</th>
			<?php else: ?>
			<th colspan="8" style="font-size: 25px;text-align: center;">统计</th>
			<?php endif; ?>
		</tr>
		<tr style="background-color: #009688;color: #fff;">
			<th style="text-align: center;" colspan="2">充值总金额</th>
			<th style="text-align: center;" colspan="2">提现总金额</th>
			<th style="text-align: center;" colspan="2">投注总金额</th>
			<th style="text-align: center;" colspan="2">中奖总金额</th>
		</tr>
	</thead>
	<tbody>
		<tr style="background-color: #009688;color: #fff;">
			<td colspan="2"><?php echo $statistics['cz_money']; ?></td>
			<td colspan="2"><?php echo $statistics['tx_money']; ?></td>
			<td colspan="2"><?php echo $statistics['tz_money']; ?></td>
			<td colspan="2"><?php echo $statistics['win_money']; ?></td>
		</tr>
	</tbody>
  <thead>
  <thead>
    <tr >
		<th style="text-align: center;" style="width: 10px">#</th>
		<th style="text-align: center;">用户编号</th>
		<th style="text-align: center;">手机号码</th>
		<th style="text-align: center;">交易类型</th>
		<th style="text-align: center;">交易金额</th>
		<th style="text-align: center;">账户剩余金额</th>
		<th style="text-align: center;">交易状态</th>		
		<th <?php if(isset($id)): ?>colspan="2"<?php endif; ?> style="text-align: center;">交易时间</th>
    </tr> 
  </thead>
    <tbody>
  	<?php if(is_array($_list) || $_list instanceof \think\Collection || $_list instanceof \think\Paginator): $key = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($key % 2 );++$key;?>
    <tr>
		<td style="text-align: center;"><?php echo $key; ?></td>
		<td style="text-align: center;"><?php echo (isset($info['yhid']) && ($info['yhid'] !== '')?$info['yhid']:'-'); ?></td>
		<td style="text-align: center;"><?php echo (isset($info['phone']) && ($info['phone'] !== '')?$info['phone']:'-'); ?></td>
		<td style="text-align: center;">
			<?php echo (isset($info['deal_cate']) && ($info['deal_cate'] !== '')?$info['deal_cate']:'-'); if($info['status'] == 2 and $info['present_status'] != 0): ?>
			(<?php echo $info['p_status']; ?>)
			<?php endif; ?>
		</td>
		<td style="text-align: center;"><?php echo (isset($info['deal_money']) && ($info['deal_money'] !== '')?$info['deal_money']:'-'); ?></td>
		<td style="text-align: center;"><?php echo (isset($info['new_money']) && ($info['new_money'] !== '')?$info['new_money']:'-'); ?></td>
		<?php if($info['pay_status'] == 1): ?>
		<td style="text-align: center;color: green"><?php echo (isset($info['zf_status']) && ($info['zf_status'] !== '')?$info['zf_status']:'-'); ?></td>
		<?php else: ?>
		<td style="text-align: center;color: red"><?php echo (isset($info['zf_status']) && ($info['zf_status'] !== '')?$info['zf_status']:'-'); ?></td>
		<?php endif; ?>
		<td <?php if(isset($id)): ?>colspan="2"<?php endif; ?> style="text-align: center;"><?php echo (isset($info['add_time']) && ($info['add_time'] !== '')?$info['add_time']:'-'); ?></td>
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