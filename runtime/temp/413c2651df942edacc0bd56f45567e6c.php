<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:66:"D:\phpStudy\WWW\cp/application/adminz\view\tpl\nba_order_list.html";i:1540535160;}*/ ?>
<table class="layui-table table">
	<colgroup>
		<col width="150">
		<col width="200">
		<col>
	</colgroup>
	<thead>
		<tr >
			<th style="text-align: center;" style="width: 10px">#</th>
			<th style="text-align: center;">用户ID</th>
			<th style="text-align: center;">倍数</th>
			<th style="text-align: center;">注数</th>
			<th style="text-align: center;">串法</th>
			<th style="text-align: center;">订单金额</th>
			<th style="text-align: center;">中奖金额</th>
			<th style="text-align: center;">中奖结果</th>
			<th style="text-align: center;">订单创建时间</th>
			<th style="text-align: center;">操作</th>
		</tr> 
	</thead>
	<tbody>
		<?php if(is_array($_list) || $_list instanceof \think\Collection || $_list instanceof \think\Paginator): $key = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($key % 2 );++$key;?>
		<tr>
			<td style="text-align: center;"><?php echo $key; ?></td>
			<td style="text-align: center;"><?php echo $info['user_id']; ?></td>
			<td style="text-align: center;"><?php echo (isset($info['multiple']) && ($info['multiple'] !== '')?$info['multiple']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($info['tz_num']) && ($info['tz_num'] !== '')?$info['tz_num']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($info['chuan']) && ($info['chuan'] !== '')?$info['chuan']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($info['order_money']) && ($info['order_money'] !== '')?$info['order_money']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($info['win_money']) && ($info['win_money'] !== '')?$info['win_money']:'-'); ?></td>
			<?php if($info['is_win'] == 0): ?><td style="text-align: center;color: #ce6a14">未开奖</td><?php endif; if($info['is_win'] == 1): ?><td style="text-align: center;color: red">中奖</td><?php endif; if($info['is_win'] == 2): ?><td style="text-align: center;color: green">未中奖</td><?php endif; ?>
			<td style="text-align: center;"><?php echo (date('Y-m-d H:i:s',$info['add_time']) ?: '-'); ?></td>
			<td style="text-align: center;"><a href="<?php echo url('nba/order_info'); ?>?order_id=<?php echo $info['order_id']; ?>">查看明细</a></td>
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