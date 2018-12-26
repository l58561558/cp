<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:61:"D:\phpStudy\WWW\cp/application/adminz\view\tpl\user_list.html";i:1545616023;}*/ ?>
<div class="box-body">
  	<table class="layui-table table">
        <tbody>
            <tr style="background-color: #f5f5f5;">
				<th style="width: 10px">#</th>
				<th>用户编号</th>
				<th>手机号</th>
				<th>用户名</th>
				<th>上级用户编号</th>
				<th>不可提现金额</th>
				<th>可用金额</th>
				<th>冻结金额</th>
				<th>总金额</th>
				<th>注册时间</th>
				<th>修改时间</th>
				<th>最后登录时间</th>
				<th>最后登录IP</th>
				<th>登录机型</th>
				<th>用户状态</th>
				<th style="width: 140px">操作</th>
            </tr>
            <?php if(is_array($_list) || $_list instanceof \think\Collection || $_list instanceof \think\Paginator): $key = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($key % 2 );++$key;?>
				<tr>
					<td><?php echo $key; ?></td>
					<td><?php echo (isset($info['yhid']) && ($info['yhid'] !== '')?$info['yhid']:'-'); ?></td>
					<td><?php echo (isset($info['phone']) && ($info['phone'] !== '')?$info['phone']:' - '); ?></td>
					<td><?php echo (isset($info['user_name']) && ($info['user_name'] !== '')?$info['user_name']:' - '); ?></td>
					<td><?php echo (isset($info['p_user']) && ($info['p_user'] !== '')?$info['p_user']:' - '); ?></td>
					<td><?php echo (isset($info['no_balance']) && ($info['no_balance'] !== '')?$info['no_balance']:' - '); ?></td>
					<td><?php echo (isset($info['balance']) && ($info['balance'] !== '')?$info['balance']:' - '); ?></td>
					<td><?php echo (isset($info['freezing_amount']) && ($info['freezing_amount'] !== '')?$info['freezing_amount']:' - '); ?></td>
					<td><?php echo (isset($info['amount_money']) && ($info['amount_money'] !== '')?$info['amount_money']:' - '); ?></td>
					<td><?php echo $info['reg_time']; ?></td>
					<td><?php echo (isset($info['edit_time']) && ($info['edit_time'] !== '')?$info['edit_time']:' - '); ?></td>
					<td><?php echo $info['last_login_time']; ?></td>
					<td><?php echo (isset($info['last_ip']) && ($info['last_ip'] !== '')?$info['last_ip']:' - '); ?></td>
					<td><?php echo (isset($info['user_agent']) && ($info['user_agent'] !== '')?$info['user_agent']:' - '); ?></td>
					<?php if($info['status'] == 0): ?>
					<td style="color: green">正常玩家</td>
					<?php else: ?>
					<td style="color: red">内部账号</td>
					<?php endif; ?>
					<td>
						<div class="btn-group btn-group-sm">
							<a href="<?php echo url('account_details/index'); ?>?id=<?php echo $info['id']; ?>">明细</a>&nbsp;&nbsp;&nbsp;
							<!-- <a href="javascript:;" data-container='body' data-placement="left" data-html='true' data-trigger="focus" data-title="删除提示" data-content='<p>您确定要删除吗?</p><button type="button" onclick="delete_data(this);" data-id="<?php echo $info['id']; ?>" data-url="<?php echo url('user/user_delete'); ?>" class="btn btn-danger btn-sm">删除</button>&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-info btn-sm">取消</button>' name="delete" style="color:red;" onclick="win_delete(this);">删除</a>&nbsp;&nbsp;&nbsp; -->
							<a href="<?php echo url('user/edit'); ?>?id=<?php echo $info['id']; ?>">编辑</a>
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
</div>
<div class="box-footer clearfix"><p class="pull-right" style="margin-right: 20px;color: #3c8dbc">当前用户数量 : <?php echo $count; ?></p></div>
<div class="box-footer clearfix">
	<ul class="pagination pull-right"><?php echo $page; ?></ul>
</div>