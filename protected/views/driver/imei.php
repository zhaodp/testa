<?php
$this->pageTitle = '可用Imei列表';

Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('driver-imei-grid', {
		data: $(this).serialize()
	});
	
	return false;
});
");

?>
<h1><?php echo $this->pageTitle; ?></h1>


<div class="search-form">
<?php $this->renderPartial('_search_imei',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-imei-grid',
	'dataProvider'=>$dataProvider,
	'cssFile'=>SP_URL_CSS . 'table.css',
	'itemsCssClass'=>'table table-condensed',
	'rowCssClassExpression'=>array($this,'driverMark'),
	'htmlOptions'=>array('class'=>'row span11'),
	'columns'=>array(
		array (
			'name' => 'imei',
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			)
		),
		array (
			'name' => 'update_time',
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data->update_time > 0 ? $data->update_time : ""'				
		)
	),
)); ?>

	