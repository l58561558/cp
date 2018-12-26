<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:63:"D:\phpStudy\WWW\cp/application/adminz\view\sell\order_list.html";i:1545183314;}*/ ?>
<table class="layui-table table">
  <colgroup>
    <col width="150">
    <col width="200">
    <col>
  </colgroup>
  <thead>
    <tr >
		<th style="text-align: center;" style="width: 10px">#</th>
		<th style="text-align: center;">游戏分类</th>
		<th style="text-align: center;">投注编号</th>
		<th style="text-align: center;">用户编号</th>
		<th style="text-align: center;">下单时间</th>
		<th style="text-align: center;">付款状态</th>
		<th style="text-align: center;">投注总金额</th>
		<th style="text-align: center;">中奖状态</th>
		<th style="text-align: center;">中奖金额</th>
    </tr> 
  </thead>
    <tbody>
  	<?php if(is_array($_list) || $_list instanceof \think\Collection || $_list instanceof \think\Paginator): $key = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($key % 2 );++$key;?>
    <tr>
		<td style="text-align: center;"><?php echo $key; ?></td>
		<td style="text-align: center;"><?php echo (isset($info['game_cate']) && ($info['game_cate'] !== '')?$info['game_cate']:'-'); ?></td>
		<td style="text-align: center;"><?php echo (isset($info['order_no']) && ($info['order_no'] !== '')?$info['order_no']:'-'); ?></td>
		<td style="text-align: center;"><?php echo (isset($info['yhid']) && ($info['yhid'] !== '')?$info['yhid']:'-'); ?></td>
		<td style="text-align: center;"><?php echo (isset($info['add_time']) && ($info['add_time'] !== '')?$info['add_time']:'-'); ?></td>
		<td style="text-align: center;"><?php echo (isset($info['order_status']) && ($info['order_status'] !== '')?$info['order_status']:'-'); ?></td>
		<td style="text-align: center;"><?php echo (isset($info['order_money']) && ($info['order_money'] !== '')?$info['order_money']:'-'); ?></td>
		<?php if($info['is_win'] == 0): ?><td style="text-align: center;color: #ce6a14">未开奖</td><?php endif; if($info['is_win'] == 2): ?><td style="text-align: center;color: red">中奖</td><?php endif; if($info['is_win'] == 1): ?><td style="text-align: center;color: green">未中奖</td><?php endif; ?>
		<td style="text-align: center;"><?php echo (isset($info['win_money']) && ($info['win_money'] !== '')?$info['win_money']:'-'); ?></td>
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