<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:62:"D:\phpStudy\WWW\cp/application/adminz\view\sell\user_list.html";i:1545282752;}*/ ?>
<table class="layui-table table">
	<colgroup>
		<col width="150">
		<col width="200">
		<col>
	</colgroup>
	<thead>
		<tr style="background-color: #009688;color: #fff;">
			<th style="text-align: center;"></th>
			<th style="text-align: center;"></th>
			<th style="text-align: center;"></th>
			<th style="text-align: center;">总注册量</th>
			<th style="text-align: center;">自购总额</th>
			<th style="text-align: center;">合买总额</th>
			<th style="text-align: center;">投注总额</th>
			<th style="text-align: center;"></th>
		</tr>
	</thead>
	<tbody>
		<tr style="background-color: #009688;color: #fff;">
			<td style="text-align: center;"></td>
			<td style="text-align: center;"></td>
			<td style="text-align: center;"></td>
			<td style="text-align: center;"><?php echo (isset($total['reg_user']) && ($total['reg_user'] !== '')?$total['reg_user']:'0'); ?></td>
			<td><?php echo (isset($total['self_money']) && ($total['self_money'] !== '')?$total['self_money']:'-'); ?></td>
			<td><?php echo (isset($total['hm_money']) && ($total['hm_money'] !== '')?$total['hm_money']:'-'); ?></td>
			<td><?php echo (isset($total['total_money']) && ($total['total_money'] !== '')?$total['total_money']:'-'); ?></td>
			<td style="text-align: center;"></td>
		</tr>
	</tbody>
	<thead>
		<tr >
			<th style="text-align: center;" style="width: 10px">#</th>
			<th style="text-align: center;">昵称</th>
			<th style="text-align: center;">手机号</th>
			<th style="text-align: center;">注册时间</th>
			<th style="text-align: center;">自购总额</th>
			<th style="text-align: center;">合买总额</th>
			<th style="text-align: center;">投注总额</th>
			<th style="text-align: center;" style="width: 100px">操作</th>
		</tr> 
	</thead>
	<tbody>
		<?php if(is_array($_list) || $_list instanceof \think\Collection || $_list instanceof \think\Paginator): $key = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($key % 2 );++$key;?>
		<tr>
			<td style="text-align: center;"><?php echo $key; ?></td>
			<td style="text-align: center;"><?php echo (isset($info['user_name']) && ($info['user_name'] !== '')?$info['user_name']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($info['phone']) && ($info['phone'] !== '')?$info['phone']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($info['reg_time']) && ($info['reg_time'] !== '')?$info['reg_time']:'-'); ?></td>
			<td><?php echo (isset($info['self_money']) && ($info['self_money'] !== '')?$info['self_money']:'-'); ?></td>
			<td><?php echo (isset($info['hm_money']) && ($info['hm_money'] !== '')?$info['hm_money']:'-'); ?></td>
			<td><?php echo (isset($info['total_money']) && ($info['total_money'] !== '')?$info['total_money']:'-'); ?></td>
			<td style="text-align: center;"><a href="<?php echo url('sell/order'); ?>?id=<?php echo $info['id']; ?>">查看订单</a></td>
		</tr>
		<?php endforeach; endif; else: echo "" ;endif; if(empty($_list) || (($_list instanceof \think\Collection || $_list instanceof \think\Paginator ) && $_list->isEmpty())): ?>
		<tr>
			<td colspan="15">空空如也~</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>