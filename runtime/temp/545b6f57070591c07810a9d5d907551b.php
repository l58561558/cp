<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:67:"D:\phpStudy\WWW\cp/application/adminz\view\sell\personnel_copy.html";i:1545294586;}*/ ?>

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
			<th style="text-align: center;"></th>
			<th style="text-align: center;"></th>
			<th style="text-align: center;"></th>
			<th style="text-align: center;">当日投注总金额</th>
			<th style="text-align: center;">近七天投注总金额</th>
			<th style="text-align: center;">近三十天投注总金额</th>
			<th style="text-align: center;">投注总金额</th>
			<th style="text-align: center;"></th>
			<th style="text-align: center;"></th>
		</tr>
	</thead>
	<tbody>
		<tr style="background-color: #009688;color: #fff;">
			<td style="text-align: center;"></td>
			<td style="text-align: center;"></td>
			<td style="text-align: center;"></td>
			<td style="text-align: center;"></td>
			<td style="text-align: center;"></td>
			<td style="text-align: center;"></td>
			<td style="text-align: center;"><?php echo (isset($head_data['today_order_money']) && ($head_data['today_order_money'] !== '')?$head_data['today_order_money']:'0'); ?></td>
			<td style="text-align: center;"><?php echo (isset($head_data['seven_order_money']) && ($head_data['seven_order_money'] !== '')?$head_data['seven_order_money']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($head_data['month_order_money']) && ($head_data['month_order_money'] !== '')?$head_data['month_order_money']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($head_data['order_money']) && ($head_data['order_money'] !== '')?$head_data['order_money']:'-'); ?></td>
			<td style="text-align: center;"></td>
			<td style="text-align: center;"></td>	
		</tr>
	</tbody>
	<thead>
		<tr >
			<th style="text-align: center;" style="width: 10px">#</th>
			<th style="text-align: center;">相互关系(id)</th>
			<th style="text-align: center;">用户编号</th>
			<th style="text-align: center;">姓名</th>
			<th style="text-align: center;">手机号</th>
			<th style="text-align: center;">当前身份</th>
			<th style="text-align: center;">当日投注总金额</th>
			<th style="text-align: center;">近七天投注总金额</th>
			<th style="text-align: center;">近三十天投注总金额</th>
			<th style="text-align: center;">投注总金额</th>
			<th style="text-align: center;">最后登录时间</th>
			<th style="text-align: center;" style="width: 100px">操作</th>
		</tr> 
	</thead>
	<tbody>
		<?php if(is_array($_list) || $_list instanceof \think\Collection || $_list instanceof \think\Paginator): $key = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($key % 2 );++$key;?>
		<tr style="<?php if($info['level'] == 3): ?>background-color: #e9f0a5;<?php endif; if($info['level'] == 2): ?>background-color: #96b7ca;<?php endif; ?>">
			<td style="text-align: center;"><?php echo $key; ?></td>
			<td style="text-align: center;"><?php echo $info['id']; ?></td>
			<td style="text-align: center;">
				<?php if(!(empty($info['child']) || (($info['child'] instanceof \think\Collection || $info['child'] instanceof \think\Paginator ) && $info['child']->isEmpty()))): ?>
					<i class="fa fa-caret-right treegrid" data-nodeid="<?php echo $info['id']; ?>"></i>
				<?php endif; ?>
				<?php echo $info['yhid']; ?>
			</td>
			<td style="text-align: center;"><?php echo (isset($info['user_name']) && ($info['user_name'] !== '')?$info['user_name']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($info['phone']) && ($info['phone'] !== '')?$info['phone']:'-'); ?></td>
			<td style="text-align: center;"><?php echo (isset($info['identity']) && ($info['identity'] !== '')?$info['identity']:'-'); ?></td>
			<td><?php echo $info['today_order_money']; ?></td>
			<td><?php echo $info['seven_order_money']; ?></td>
			<td><?php echo $info['month_order_money']; ?></td>
			<td><?php echo $info['order_money']; ?></td>
			<td style="text-align: center;"><?php echo (isset($info['last_login_time']) && ($info['last_login_time'] !== '')?$info['last_login_time']:'-'); ?></td>
			<td style="text-align: center;"><a href="<?php echo url('order/index'); ?>?id=<?php echo $info['id']; ?>">查看订单</a></td>
		</tr>
		<?php if(isset($info['child'])): if(is_array($info['child']) || $info['child'] instanceof \think\Collection || $info['child'] instanceof \think\Paginator): $ke = 0; $__LIST__ = $info['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$child): $mod = ($ke % 2 );++$ke;?>
			<tr class="treegrid-<?php echo $child['id']; ?> treegrid-parent-<?php echo $child['pid']; ?> td" style="<?php if($child['level'] == 3): ?>background-color: #e9f0a5;<?php endif; if($child['level'] == 2): ?>background-color: #96b7ca;<?php endif; ?>display: none;">
				<td style="text-align: center;"><?php echo $ke; ?></td>
				<td style="text-align: center;"><?php echo $info['id']; ?>-<?php echo $child['id']; ?></td>
				<td style="text-align: center;"><?php echo $child['yhid']; ?></td>
				<td style="text-align: center;"><?php echo (isset($child['user_name']) && ($child['user_name'] !== '')?$child['user_name']:'-'); ?></td>
				<td style="text-align: center;"><?php echo (isset($child['phone']) && ($child['phone'] !== '')?$child['phone']:'-'); ?></td>
				<td style="text-align: center;"><?php echo (isset($child['identity']) && ($child['identity'] !== '')?$child['identity']:'-'); ?></td>
				<td><?php echo $child['today_order_money']; ?></td>
				<td><?php echo $child['seven_order_money']; ?></td>
				<td><?php echo $child['month_order_money']; ?></td>
				<td><?php echo $child['order_money']; ?></td>
				<td style="text-align: center;"><?php echo (isset($child['last_login_time']) && ($child['last_login_time'] !== '')?$child['last_login_time']:'-'); ?></td>
				<td style="text-align: center;"><a href="<?php echo url('order/index'); ?>?id=<?php echo $child['id']; ?>">查看订单</a></td>
			</tr>
			<?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; if(empty($_list) || (($_list instanceof \think\Collection || $_list instanceof \think\Paginator ) && $_list->isEmpty())): ?>
		<tr>
			<td colspan="15">空空如也~</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>

<script type="text/javascript">
	$(".open").click(function(){
		$(".table .td").css('display','table-row');
		$(".table .fa").removeClass('fa-caret-right');
		$(".table .fa").addClass('fa-caret-down');
	})
	$(".fold").click(function(){
		$(".table .td").css('display','none');
		$(".table .fa").addClass('fa-caret-right');
		$(".table .fa").removeClass('fa-caret-down');
	})
</script>
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
        ,type: 'datetime'
    });
    laydate.render({
        elem: '#date1'
        ,type: 'datetime'
    }); 
    laydate.render({
        elem: '#cate_id'
    });
})
</script>
