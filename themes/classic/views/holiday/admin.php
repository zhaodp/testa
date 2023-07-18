<h1>节假日管理</h1>
<?php echo CHtml::Button('添加',array('class'=>'btn btn-success','id'=>'add_btn')); ?>
<?php 
$status = array('0'=>'屏蔽','1'=>'不屏蔽 '); 
?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'holiday-grid',
	'dataProvider'=>$model->search(),
	//'filter'=>$model,
		'itemsCssClass'=>'table table-striped',
	'columns'=>array(
		'id',
		'holiday',
			array (
				'name' => 'status',
					'type' => 'raw',
					'value' => '($data->status=="0")?"屏蔽":"不屏蔽"'
			),
		'create_date',
		array(
			'class'=>'CButtonColumn',
				'header'=>'操作',
			 'template'=>'{update} {delete}',
		),
	),
)); ?>
<script>
$(function(){
	$("#add_btn").click(function(){
		window.location.href="?r=holiday/create";
	});
});
</script>