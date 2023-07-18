<?php
/* @var $this DriverCardController */
/* @var $model DriverCard */

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('driver-card-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>考试列表</h1>

<?php echo CHtml::link('高级搜索','#',array('class'=>'search-button  btn-primary btn')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_drivertestsearch',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-card-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-striped',
	'columns'=>array(
		'user',
		'name',
		'id_card',
		array (
				'name'=>'city_id',
				'headerHtmlOptions'=>array (
						'width'=>'100px',
						'nowrap'=>'nowrap'
				),
				'value'=>'Dict::item("city", $data->city_id)'
				
		),
		'num',
		array (
				'name'=>'status',
				'headerHtmlOptions'=>array (
						'width'=>'100px',
						'nowrap'=>'nowrap'
				),
				'value'=>'($data->status == 0 ? "未考试" : ($data->status == 1 ? "已通过":"违纪考试"))'
				
		),
//		array (
//			'name'=>'操作', 
//			'headerHtmlOptions'=>array (
//				'width'=>'60px',
//				'nowrap'=>'nowrap'
//			),
//			'type'=>'raw',
//			'value'=>'($data->status == 2) ? "已通知":CHtml::link("违纪考试", "javascript:void(0);", array("id"=>"exam_$data->id","onclick"=>"{ajaxexam($data->id);}"))'
//		), 
	),
)); ?>


<script>
function ajaxexam(id){
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/zhaopin/ajaxexam');?>',
		'data':'id='+id,
		'type':'get',
		'success':function(data){
			$('#exam_'+id).parent().html("已通知");
			$('#exam_'+id).parent().prev().html("违纪考试");
		}	
	});
}
</script>