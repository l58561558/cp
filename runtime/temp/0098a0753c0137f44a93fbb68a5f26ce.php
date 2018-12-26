<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:62:"D:\phpStudy\WWW\cp/application/adminz\view\sell\sell_list.html";i:1545278250;}*/ ?>
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
			<th style="text-align: center;">小组总注册量</th>
			<th style="text-align: center;">小组自购总额</th>
			<th style="text-align: center;">小组合买总额</th>
			<th style="text-align: center;">小组投注总额</th>
		</tr>
	</thead>
	<tbody>
		<tr style="background-color: #009688;color: #fff;">
			<td style="text-align: center;"></td>
			<td style="text-align: center;"></td>
			<td style="text-align: center;"></td>
			<td style="text-align: center;"><?php echo (isset($head_data['reg_user']) && ($head_data['reg_user'] !== '')?$head_data['reg_user']:'0'); ?></td>
			<td><?php echo (isset($head_data['self_money']) && ($head_data['self_money'] !== '')?$head_data['self_money']:'-'); ?></td>
			<td><?php echo (isset($head_data['hm_money']) && ($head_data['hm_money'] !== '')?$head_data['hm_money']:'-'); ?></td>
			<td><?php echo (isset($head_data['total_money']) && ($head_data['total_money'] !== '')?$head_data['total_money']:'-'); ?></td>
		</tr>
	</tbody>
	<thead>
		<tr >
			<th style="text-align: center;" style="width: 10px">#</th>
			<th style="text-align: center;">昵称</th>
			<th style="text-align: center;">当前身份</th>
			<th style="text-align: center;">总开户量</th>
			<th style="text-align: center;">自购总额</th>
			<th style="text-align: center;">合买总额</th>
			<th style="text-align: center;">投注总额</th>
		</tr> 
	</thead>
	<tbody>
		<?php if(is_array($_list) || $_list instanceof \think\Collection || $_list instanceof \think\Paginator): $key = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($key % 2 );++$key;?>
		<tr>
			<td style="text-align: center;"><?php echo $key; ?></td>
			<td style="text-align: center;"><?php echo (isset($info['user_name']) && ($info['user_name'] !== '')?$info['user_name']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($info['identity']) && ($info['identity'] !== '')?$info['identity']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($info['reg_user']) && ($info['reg_user'] !== '')?$info['reg_user']:'-'); ?></td>
			<td><?php echo (isset($info['self_money']) && ($info['self_money'] !== '')?$info['self_money']:'-'); ?></td>
			<td><?php echo (isset($info['hm_money']) && ($info['hm_money'] !== '')?$info['hm_money']:'-'); ?></td>
			<td><?php echo (isset($info['total_money']) && ($info['total_money'] !== '')?$info['total_money']:'-'); ?></td>
		</tr>
		<?php endforeach; endif; else: echo "" ;endif; if(empty($_list) || (($_list instanceof \think\Collection || $_list instanceof \think\Paginator ) && $_list->isEmpty())): ?>
		<tr>
			<td colspan="15">空空如也~</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>