<h1>副卡管理</h1>
<table class="table table-striped">
	<tr>
		<th>姓名</th>
		<th>手机号</th>
		<th>状态</th>
		<th>操作</th>
	</tr>
	<?php 
	if (!empty($models)) {
		foreach ($models as $model) {
	?>
	<tr id="<?php echo 'sub_info_'.$model['id'];?>">
		<td><?php echo CHtml::textField('sub_name_'.$model['id'], $model['name']);?></td>		
		<td><?php echo CHtml::textField('sub_phone_'.$model['id'], $model['phone']);?></td>
		<td><?php echo CHtml::dropDownList('sub_status_'.$model['id'],$model['status'],CustomerSub::$status_dict);?></td>
		<td>
			<?php echo CHtml::button('修改', array('func'=>'save','action'=>'update','sub_id'=>$model['id'],'class' => 'btn btn-success','style' => 'margin-right:15px'));?>
			<?php echo CHtml::button('删除', array('func'=>'save','action'=>'delete','sub_id'=>$model['id'],'class' => 'btn btn-success','style' => 'margin-right:15px'));?>
		</td>
	</tr>
	<?php 
		}
	} 
	?>
	<tr>
		<td><?php echo CHtml::textField("sub_name_0", '', array('style'=>'width:150px;')); ?></td>
		<td><?php echo CHtml::textField("sub_phone_0", '', array('style'=>'width:150px;')); ?></td>
		<td><?php echo CHtml::dropDownList("sub_status_0", 1, CustomerSub::$status_dict); ?></td>
		<td><?php echo CHtml::button('添加', array('func'=>'save','action'=>'create','sub_id'=>0,'class' => 'btn btn-success','style' => 'margin-right:15px')); ?></td>		
	</tr>	
</table>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('[func="save"]').click(function(){
		var sub_id = jQuery(this).attr('sub_id');
		var post_data = getData(sub_id);
		post_data['action'] = jQuery(this).attr('action');
		post_data['customer_id'] = <?php echo $customer_id;?>;
	
		if (post_data['action'] == 'delete') {
			if (confirm('确认要删除该副卡信息吗？')) {
				saveByAjax(post_data, jQuery(this));
			}
		} else {
			saveByAjax(post_data, jQuery(this));
		}
	});
})

function saveByAjax(post_data, button) {
	button.attr("disabled","disabled" );
	jQuery.post(
		'<?php echo Yii::app()->createUrl('/CustomerMain/SubAjax');?>',
		post_data,
		function(d) {
			alert(d.msg);
			if (d.status) {
				window.location.reload();
			}
		},
		'json'
	);
}

function getData(id) {
	var data = {};
	data['sub_id'] = id;
	data['name'] = jQuery('#sub_name_'+id).val();
	data['phone'] = jQuery('#sub_phone_'+id).val();
	data['status'] = jQuery('#sub_status_'+id).val();
	return data;
}

</script>