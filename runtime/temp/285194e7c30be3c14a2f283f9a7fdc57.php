<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:61:"D:\phpStudy\WWW\cp/application/adminz\view\tpl\node_list.html";i:1539149286;}*/ ?>
<div class="box-body">
  	<table class="layui-table table">
        <tbody>
            <tr style="background-color: #f5f5f5;">
				<th>节点名称</th>
				<th>控制器名称</th>
				<th>方法名称</th>
				<th>参数</th>
				<th>权限编码</th>
				<th>是否左侧导航显示</th>
				<th>排序</th>
				<th style="width: 100px">操作</th>
            </tr>
            <?php if(is_array($_list) || $_list instanceof \think\Collection || $_list instanceof \think\Paginator): $key = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($key % 2 );++$key;?>
				<tr  class="treegrid-<?php echo $info['node_id']; ?>">
					<td>
						<?php if(!(empty($info['child']) || (($info['child'] instanceof \think\Collection || $info['child'] instanceof \think\Paginator ) && $info['child']->isEmpty()))): ?>
							<i class="fa fa-caret-right treegrid" data-nodeid="<?php echo $info['node_id']; ?>"></i>
						<?php endif; ?>
						<?php echo (isset($info['node_name']) && ($info['node_name'] !== '')?$info['node_name']:''); ?>
					</td>
					<td><?php echo $info['controller']; ?></td>
					<td><?php echo $info['action']; ?></td>
					<td>
						<?php echo !empty($info['parameter'])?$info['parameter']:' - '; ?>
					</td>
					<td><?php echo $info['authority_code']; ?></td>
					<td>
						<?php if($info['is_menu'] == 1): ?>
							<a class='btn-field' name='is_menu' href='javascript:;' title='点击更改状态' data-status='0' data-id='<?php echo $info['node_id']; ?>'' data-url="<?php echo url('node/node_edit_field'); ?>"><i class='fa fa-check'></i></a>
						<?php else: ?>
							<a class='btn-field' name='is_menu' href='javascript:;' title='点击更改状态' data-status='1' data-id='<?php echo $info['node_id']; ?>' data-url="<?php echo url('node/node_edit_field'); ?>"><i class='fa fa-close'></i></a>
						<?php endif; ?>
					</td>
					<td>
						<input class="inputs" data-id="<?php echo $info['node_id']; ?>" data-url="<?php echo url('node/node_edit_field'); ?>" name="sort_order" type="text" value="<?php echo $info['sort_order']; ?>" >
					</td>
					<td>
						<div class="btn-group btn-group-sm">
							<a href="javascript:;" data-container='body' data-placement="left" data-html='true' data-trigger="focus" data-title="删除提示" data-content='<p>您确定要删除吗?</p><button type="button" onclick="delete_data(this);" data-id="<?php echo $info['node_id']; ?>" data-url="<?php echo url('node/node_delete'); ?>" class="btn btn-danger btn-sm">删除</button>&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-info btn-sm">取消</button>' name="delete" style="color:red;" onclick="win_delete(this);">删除</a>&nbsp;&nbsp;&nbsp;
							<a href="<?php echo url('node/edit'); ?>?node_id=<?php echo $info['node_id']; ?>">编辑</a>
						</div>
					</td>
				</tr>
				<?php if(is_array($info['child']) || $info['child'] instanceof \think\Collection || $info['child'] instanceof \think\Paginator): $key = 0; $__LIST__ = $info['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$child): $mod = ($key % 2 );++$key;?>
					<tr  class="treegrid-<?php echo $child['node_id']; ?> treegrid-parent-<?php echo $child['pid']; ?>" style="background-color: #e9f0f5;display: none;">
						<td>
							<?php echo (isset($child['node_name']) && ($child['node_name'] !== '')?$child['node_name']:''); ?>
						</td>
						<td><?php echo $child['controller']; ?></td>
						<td><?php echo $child['action']; ?></td>
						<td>
							<?php echo !empty($child['parameter'])?$child['parameter']:' - '; ?>
						</td>
						<td><?php echo $child['authority_code']; ?></td>
						<td>
							<?php if($child['is_menu'] == 1): ?>
								<a class='btn-field' name='is_menu' href='javascript:;' title='点击更改状态' data-status='0' data-id='<?php echo $child['node_id']; ?>'' data-url="<?php echo url('node/node_edit_field'); ?>"><i class='fa fa-check'></i></a>
							<?php else: ?>
								<a class='btn-field' name='is_menu' href='javascript:;' title='点击更改状态' data-status='1' data-id='<?php echo $child['node_id']; ?>' data-url="<?php echo url('node/node_edit_field'); ?>"><i class='fa fa-close'></i></a>
							<?php endif; ?>
						</td>
						<td>
							<input class="inputs" data-id="<?php echo $child['node_id']; ?>" data-url="<?php echo url('node/node_edit_field'); ?>" name="sort_order" type="text" value="<?php echo $child['sort_order']; ?>" >
						</td>
						<td>
							<div class="btn-group btn-group-sm">
								<a href="javascript:;" data-container='body' data-placement="left" data-html='true' data-trigger="focus" data-title="删除提示" data-content='<p>您确定要删除吗?</p><button type="button" onclick="delete_data(this);" data-id="<?php echo $child['node_id']; ?>" data-url="<?php echo url('node/node_delete'); ?>" class="btn btn-danger btn-sm">删除</button>&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-info btn-sm">取消</button>' name="delete" style="color:red;" onclick="win_delete(this);">删除</a>&nbsp;&nbsp;&nbsp;
								<a href="<?php echo url('node/edit'); ?>?node_id=<?php echo $child['node_id']; ?>">编辑</a>
							</div>
						</td>
					</tr>
				<?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; if(empty($_list) || (($_list instanceof \think\Collection || $_list instanceof \think\Paginator ) && $_list->isEmpty())): ?>
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