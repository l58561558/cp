<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:60:"D:\phpStudy\WWW\cp/application/adminz\view\nba\nba_list.html";i:1545616006;}*/ ?>
<table class="layui-table table">
	<!-- <colgroup>
		<col width="150">
		<col width="200">
		<col>
	</colgroup> -->
	<thead>
		<tr >
			<th style="text-align: center;" style="width: 10px">#</th>
			<th style="text-align: center;">赛事编号</th>
			<th style="text-align: center;">赛事名称</th>
			<th style="text-align: center;">客队</th>
			<th style="text-align: center;">客队分数</th>
			<th style="text-align: center;">主队</th>
			<th style="text-align: center;">主队分数</th>
			<th style="text-align: center;">停止投注时间</th>
			<th style="text-align: center;">投注状态</th>
			<th style="text-align: center;">获胜队伍</th>
			<th style="text-align: center;" style="width: 100px">操作</th>
		</tr> 
	</thead>
	<tbody>
		<?php if(is_array($_list) || $_list instanceof \think\Collection || $_list instanceof \think\Paginator): $key = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($key % 2 );++$key;?>
		<tr>
			<td style="text-align: center;"><?php echo $key; ?></td>
			<td style="text-align: center;"><?php echo (isset($info['game_no']) && ($info['game_no'] !== '')?$info['game_no']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($info['game_name']) && ($info['game_name'] !== '')?$info['game_name']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($info['road_team']) && ($info['road_team'] !== '')?$info['road_team']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($info['road_score']) && ($info['road_score'] !== '')?$info['road_score']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($info['home_team']) && ($info['home_team'] !== '')?$info['home_team']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($info['home_score']) && ($info['home_score'] !== '')?$info['home_score']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($info['end_time']) && ($info['end_time'] !== '')?$info['end_time']:'-'); ?></td>
			<!-- <?php if($info['status'] == 1): ?><td style="text-align: center;color: red">停止投注</td><?php endif; if($info['status'] == 2): ?><td style="text-align: center;color: green">可以投注</td><?php endif; ?> -->
			<td style="text-align: center;color: red"><?php echo $info['tz_status']; ?></td>
			<th style="text-align: center;"><?php echo (isset($info['win_team']) && ($info['win_team'] !== '')?$info['win_team']:'-'); ?></th>
			<td style="text-align: center;">
				<!-- <a href="javascript:void(0);" class="over" data="<?php echo $info['id']; ?>" style="color: red">结算</a>&nbsp;&nbsp;&nbsp; -->
				<a href="<?php echo url('nba/nba_over'); ?>?id=<?php echo $info['id']; ?>" style="color: red">结算</a>&nbsp;&nbsp;&nbsp;
				<a href="<?php echo url('nba/edit'); ?>?id=<?php echo $info['id']; ?>">编辑</a>&nbsp;&nbsp;&nbsp;
				<a href="<?php echo url('nba/look'); ?>?id=<?php echo $info['id']; ?>">查看</a>&nbsp;&nbsp;&nbsp;
				<a style="color: red" href="<?php echo url('nba/drop'); ?>?id=<?php echo $info['id']; ?>">删除</a>
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
	$(".over").click(function(){
		var id = $(this).attr('data');
		$.ajax({
			url:"<?php echo url('nba/nba_over'); ?>",
			type:'get',
			data:{id:id},
			success:function(res){
				if(res.code == 1){
					layer.msg(res.msg);
					window.location.reload();
				}else{
					layer.msg(res.msg);
				}
			}
		})
	})
</script>