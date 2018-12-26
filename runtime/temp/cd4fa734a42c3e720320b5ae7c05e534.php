<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:64:"D:\phpStudy\WWW\cp/application/adminz\view\football\fb_list.html";i:1545616001;}*/ ?>
<table class="layui-table table">
	<!-- <colgroup>
		<col width="150">
		<col width="200">
		<col>
	</colgroup> -->
	<thead>
		<tr >
			<th style="text-align: center;" style="width: 10px">#</th>
			<th style="text-align: center;" style="width: 10px">编号</th>
			<th style="text-align: center;">赛事名称</th>
			<th style="text-align: center;">主队</th>
			<th style="text-align: center;">客队</th>
			<th style="text-align: center;">上半场比分</th>
			<th style="text-align: center;">最终比分</th>
			<th style="text-align: center;">总球数</th>
			<th style="text-align: center;">停止投注时间</th>
			<th style="text-align: center;">投注状态</th>
			<th style="text-align: center;" style="width: 100px">操作</th>
		</tr> 
	</thead>
	<tbody>
		<?php if(is_array($_list) || $_list instanceof \think\Collection || $_list instanceof \think\Paginator): $key = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($key % 2 );++$key;?>
		<tr>
			<td style="text-align: center;"><?php echo $key; ?></td>
			<td style="text-align: center;"><?php echo (isset($info['game_no']) && ($info['game_no'] !== '')?$info['game_no']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($info['game_name']) && ($info['game_name'] !== '')?$info['game_name']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($info['home_team']) && ($info['home_team'] !== '')?$info['home_team']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($info['road_team']) && ($info['road_team'] !== '')?$info['road_team']:'-'); ?></td>
			<td style="text-align: center;">
				<?php echo (isset($info['top_score']) && ($info['top_score'] !== '')?$info['top_score']:'-'); ?>
			</td>
			<td style="text-align: center;">
				<?php echo (isset($info['down_score']) && ($info['down_score'] !== '')?$info['down_score']:'-'); ?>
			</td>
			<td style="text-align: center;"><?php echo (isset($info['total_score']) && ($info['total_score'] !== '')?$info['total_score']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($info['end_time']) && ($info['end_time'] !== '')?$info['end_time']:'-'); ?></td>
			<td style="text-align: center;color: red"><?php echo $info['tz_status']; ?></td>
			<td style="text-align: center;">
				<?php if($info['status'] != 0): if($info['end_time'] <= time()): ?>
				<a href="<?php echo url('football/fb_over'); ?>?id=<?php echo $info['id']; ?>" style="color: red">结算</a>&nbsp;&nbsp;&nbsp;
				<?php endif; endif; ?>
				<a href="<?php echo url('football/edit'); ?>?id=<?php echo $info['id']; ?>">编辑</a>&nbsp;&nbsp;&nbsp;
				<a href="<?php echo url('football/look'); ?>?id=<?php echo $info['id']; ?>">查看</a>&nbsp;&nbsp;&nbsp;
				<a style="color: red" href="<?php echo url('football/drop'); ?>?id=<?php echo $info['id']; ?>">删除</a>
				<?php if($info['is_postpone'] == 0): ?>
				<a data-id="<?php echo $info['id']; ?>" data-url="<?php echo url('football/edit_field'); ?>" class="is_postpone" value="1" href="javascript:void(0)">延期</a>
				<?php endif; ?>
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
<script type="text/javascript">
	$(".is_postpone").click(function(){
		var id = $(this).attr('data-id');
		$.ajax({
			url:"<?php echo url('football/edit_field'); ?>",
			type:'post',
			data:{is_postpone:1,id:id},
			success:function(res){
				alert(res.msg);
				window.location.reload();
			}
		})
	})
</script>