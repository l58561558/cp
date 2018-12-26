<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:62:"D:\phpStudy\WWW\cp/application/adminz\view\tpl\admin_list.html";i:1545183318;}*/ ?>
<div class="box-body">
  	<table class="layui-table table">
        <tbody>
            <tr style="background-color: #f5f5f5;">
				<th style="width: 10px">#</th>
				<th>登录名称</th>
				<th>管理员昵称</th>
				<th>手机号</th>
				<th>最后登录时间</th>
				<th>最后登录ip</th>
				<th>状态</th>
				<th style="width: 150px">操作</th>
            </tr>
            <?php if(is_array($_list) || $_list instanceof \think\Collection || $_list instanceof \think\Paginator): $key = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($key % 2 );++$key;?>
				<tr>
					<td><?php echo $key; ?></td>
					<td>
						<?php echo (isset($info['login_name']) && ($info['login_name'] !== '')?$info['login_name']:'-'); ?>|<?php echo (isset($info['role_name']) && ($info['role_name'] !== '')?$info['role_name']:'-'); ?>
					</td>
					<td><?php echo (isset($info['nickname']) && ($info['nickname'] !== '')?$info['nickname']:' - '); ?></td>
					<td><?php echo (isset($info['mobile']) && ($info['mobile'] !== '')?$info['mobile']:' - '); ?></td>
					<td><?php if($info['last_login_time'] == 0): ?> - <?php else: ?><?php echo date('Y-m-d H:i:s',$info['last_login_time']); endif; ?></td>
					<td><?php echo (isset($info['last_ip']) && ($info['last_ip'] !== '')?$info['last_ip']:' - '); ?></td>
					<td>
						<?php if($info['status'] == 1): ?>
							<a class='btn-field' name='status' href='javascript:;' title='点击更改状态' data-status='0' data-id='<?php echo $info['id']; ?>'' data-url="<?php echo url('admin/admin_edit_field'); ?>"><i class='fa fa-check'></i></a>
						<?php else: ?>
							<a class='btn-field' name='status' href='javascript:;' title='点击更改状态' data-status='1' data-id='<?php echo $info['id']; ?>' data-url="<?php echo url('admin/admin_edit_field'); ?>"><i class='fa fa-close'></i></a>
						<?php endif; ?>
					</td>
					<td>
						<div class="btn-group btn-group-sm">
							<?php if($info['id'] != 1): ?>
								<a href="javascript:;" data-container='body' data-placement="left" data-html='true' data-trigger="focus" data-title="删除提示" data-content='<p>您确定要删除吗?</p><button type="button" onclick="delete_data(this);" data-id="<?php echo $info['id']; ?>" data-url="<?php echo url('admin/admin_delete'); ?>" class="btn btn-danger btn-sm">删除</button>&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-info btn-sm">取消</button>' name="delete" style="color:red;" onclick="win_delete(this);">删除</a>&nbsp;&nbsp;&nbsp;
								<a href="<?php echo url('admin/root'); ?>?id=<?php echo $info['id']; ?>">权限</a>&nbsp;&nbsp;&nbsp;
							<?php endif; ?>
							<a href="<?php echo url('admin/edit'); ?>?id=<?php echo $info['id']; ?>">编辑</a>
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
<div class="box-footer clearfix">
	<ul class="pagination pull-right"><?php echo $page; ?></ul>
</div>