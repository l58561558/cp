<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:61:"D:\phpStudy\WWW\cp/application/adminz\view\tpl\role_list.html";i:1513663628;}*/ ?>
<div class="box-body">
  	<table class="layui-table table">
        <tbody>
            <tr style="background-color: #f5f5f5;">
				<th style="width: 10px">#</th>
				<th>角色名称</th>
				<th>启用</th>
				<th style="width: 100px">操作</th>
            </tr>
            <?php if(is_array($_list) || $_list instanceof \think\Collection || $_list instanceof \think\Paginator): $key = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($key % 2 );++$key;?>
				<tr>
					<td><?php echo $key; ?></td>
					<td><?php echo (isset($info['role_name']) && ($info['role_name'] !== '')?$info['role_name']:'-'); ?></td>
					<td>
						<?php if($info['is_enable'] == 1): ?>
							<a class='btn-field' name='is_enable' href='javascript:;' title='点击更改状态' data-status='0' data-id='<?php echo $info['role_id']; ?>'' data-url="<?php echo url('role/role_edit_field'); ?>"><i class='fa fa-check'></i></a>
						<?php else: ?>
							<a class='btn-field' name='is_enable' href='javascript:;' title='点击更改状态' data-status='1' data-id='<?php echo $info['role_id']; ?>' data-url="<?php echo url('role/role_edit_field'); ?>"><i class='fa fa-close'></i></a>
						<?php endif; ?>
					</td>
					<td>
						<div class="btn-group btn-group-sm">
							<?php if($info['role_id'] != 1): ?>
								<a href="javascript:;" data-container='body' data-placement="left" data-html='true' data-trigger="focus" data-title="删除提示" data-content='<p>您确定要删除吗?</p><button type="button" onclick="delete_data(this);" data-id="<?php echo $info['role_id']; ?>" data-url="<?php echo url('role/role_delete'); ?>" class="btn btn-danger btn-sm">删除</button>&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-info btn-sm">取消</button>' name="delete" style="color:red;" onclick="win_delete(this);">删除</a>&nbsp;&nbsp;&nbsp;
							<?php endif; ?>
							<a href="<?php echo url('role/edit'); ?>?role_id=<?php echo $info['role_id']; ?>">编辑</a>
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