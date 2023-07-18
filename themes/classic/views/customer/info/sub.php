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
		<td><?php echo CHtml::textField('name'.$model['id'], $model['name']);?></td>		
		<td><?php echo CHtml::textField('phone'.$model['id'], $model['phone']);?></td>
		<td><?php echo CHtml::dropDownList('status'.$model['id'],$model['status'],CustomerSub::$status_dict);?></td>
		<td>
			<?php echo CHtml::button('修改', array('func'=>'save','action'=>'update','id'=>$model['id'],'class' => 'btn btn-success','style' => 'margin-right:15px'));?>
			<?php echo CHtml::button('删除', array('func'=>'save','action'=>'delete','id'=>$model['id'],'class' => 'btn btn-success','style' => 'margin-right:15px'));?>
		</td>
	</tr>
	<?php 
		}
	} 
	?>
	<tr>
		<td><?php echo CHtml::textField("name_0", '', array('style'=>'width:150px;')); ?></td>
		<td><?php echo CHtml::textField("phone_0", '', array('style'=>'width:150px;')); ?></td>
		<td><?php echo CHtml::dropDownList("status_0", 1, CustomerSub::$status_dict); ?></td>
		<td><?php echo CHtml::button('添加', array('func'=>'save','action'=>'update','','class' => 'btn btn-success','style' => 'margin-right:15px')); ?></td>		
	</tr>	
</table>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('[func="save"]').click(function(){
		var id = jQuery(this).attr('id');
		var post_data = getData(id);
		post_data['action'] = jQuery(this).attr('action');
	
		if (post_data['action'] == 'delete') {
			if (confirm('确认要删除该副卡信息吗？')) {
				saveByAjax(post_data, jQuery(this));
			}
		} else{
			saveByAjax(post_data, jQuery(this));
		}
	});
})

function saveByAjax(post_data, button) {
	if(post_data['name']==''||post_data['phone']==''){
		alert('请填写手机号和姓名！');
	}else{
		button.attr("disabled","disabled" );
		jQuery.post(
			'<?php echo Yii::app()->createUrl('/customer/mainsubajax');?>',
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
}

function getData(id) {
	var data = {};
	if(id){
		data['id'] = id;
		data['name'] = $('#name'+id).val();
		data['phone'] = $('#phone'+id).val();
		data['status'] = $('#status'+id).val();
	}else{
		data['name'] = $('#name_0').val();
		data['phone'] = $('#phone_0').val();
		data['status'] = $('#status_0').val();
	}
	data['vip_card']='<?php echo $vip_card_;?>';
	return data;
}

</script>