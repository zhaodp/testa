<?php
$this->breadcrumbs=array(
	'Notices'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Notice', 'url'=>array('index')),
	array('label'=>'Create Notice', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('notice-grid', {
		data: $(this).serialize()
	});
	
});
");
?>

<h1>管理公告</h1>

<?php echo CHtml::link('高级搜索','#',array('class'=>'search-button')); ?>
<div class="search-form">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'notice-grid',
	'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped',
	'pagerCssClass'=>'pagination text-center', 
	'pager'=>Yii::app()->params['formatGridPage'], 
	//'filter'=>$model,
	'columns'=>array(
		array (
			'name'=>'id', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
		), 
		array (
			'name'=>'city_id', 
			'headerHtmlOptions'=>array (
				'width'=>'60px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'Yii::app()->controller->getCityName($data->city_id)',
		),
		array (
			'name'=>'category', 
			'headerHtmlOptions'=>array (
				'width'=>'60px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'Yii::app()->controller->getNoticeStatus($data->class)',
		),
		array (
			'name'=>'title', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'author', 
			'headerHtmlOptions'=>array (
				'style'=>'width:35px',
			),
		),
		array (
			'name'=>'created', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'date("Y-m-d H:i",$data->created)'
		), 
		array(
			'name'=>'is_top',
			'headerHtmlOptions'=>array(
				'width'=>'50px',
				'nowrap'=>'nowrap',
			),
			'value'=>'$data->is_top == 0 ? "否" : "是"',
		),
		array(
			'name'=>'top_period',
			'headerHtmlOptions'=>array(
				'width'=>'50px',
				'nowrap'=>'nowrap',
			),
			'value'=>'date("Y-m-d",strtotime($data->top_period))'
		),
		array(
			'name'=>'deadline',
			'headerHtmlOptions'=>array(
				'width'=>'50px',
				'nowrap'=>'nowrap',
			),
			'value'=>'date("Y-m-d",strtotime($data->deadline))',
		),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
