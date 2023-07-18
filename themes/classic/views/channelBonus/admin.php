<?php
$this->pageTitle = '管理渠道优惠券';

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('channel-bonus-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo $this->pageTitle; ?></h1>

<?php echo CHtml::link('高级搜索','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'channel-bonus-grid',
	'dataProvider'=>$model->search(),
	//'filter'=>$model,
	'itemsCssClass'=>'table table-striped',
	'columns'=>array(
		array (
			'name'=>'id', 
			'headerHtmlOptions'=>array (
				'width'=>'10px',
				'nowrap'=>'nowrap'
			),
		), 
		array (
			'name'=>'owner', 
			'headerHtmlOptions'=>array (
				'width'=>'150px',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'channel_id', 
			'headerHtmlOptions'=>array (
				'width'=>'30px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'Dict::item("bonus_channel", $data->channel_id)'
		), 
		array (
			'name'=>'type_id', 
			'headerHtmlOptions'=>array (
				'width'=>'30px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'BonusType::getBonusType($data->type_id)->name'
		),
		array (
			'name'=>'sn_start', 
			'headerHtmlOptions'=>array (
				'width'=>'60px',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'sn_end', 
			'headerHtmlOptions'=>array (
				'width'=>'60px',
				'nowrap'=>'nowrap'
			),
		),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
