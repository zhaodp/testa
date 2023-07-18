<?php
/* @var $this DriverTrainDataController */
/* @var $model DriverTrainData */

$this->breadcrumbs=array(
	'Driver Train Datas'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#driver-train-data-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>资料管理</h1>
<div class="search-form">
<?php $this->renderPartial('_material_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-train-data-grid',
	'dataProvider'=>$model->search(),
	'pagerCssClass'=>'pagination text-center', 
	'pager'=>Yii::app()->params['formatGridPage'], 
	'itemsCssClass'=>'table table-striped',
//	'filter'=>$model,
	'columns'=>array(
		'id',
		array (
			'name'=>'city_id', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'Dict::item("city", $data->city_id)'
		),
		'title',
		array (
			'name'=>'created', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			), 
			'value'=>'date("Y-m-d H:i",$data->created)'
		), 
		 array(  
		    'class'=>'CButtonColumn', 
		    'template'=>'{view}{update}{delete}',
		 	'buttons' => array(
		 		'view' => array(
		 			'url' => 'Yii::app()->createUrl("notice/materialView",array("id"=>$data->id))'
		 		),
		 		'update' => array(
		 			'url' => 'Yii::app()->createUrl("notice/materialUpdate",array("id"=>$data->id))'
		 		),
		 		'delete' => array(
		 			'url' => 'Yii::app()->createUrl("notice/materialDelete",array("id" => $data->id))'
		 		)
		 	),
		),  
	),
)); ?>
