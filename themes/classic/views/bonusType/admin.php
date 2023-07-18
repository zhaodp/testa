<?php
$this->pageTitle = '管理优惠券';

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('bonus-type-grid', {
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
	'id'=>'bonus-type-grid',
	'dataProvider'=>$model->search(20),
	'itemsCssClass'=>'table table-striped',
	//'filter'=>$model,
	'columns'=>array(
		array (
			'name'=>'id', 
			'headerHtmlOptions'=>array (
				'width'=>'10px',
				'nowrap'=>'nowrap'
			),
		), 
		array (
			'name'=>'name', 
			'headerHtmlOptions'=>array (
				'width'=>'60px',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'money', 
			'headerHtmlOptions'=>array (
				'width'=>'20px',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'channel', 
			'headerHtmlOptions'=>array (
				'width'=>'30px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'Dict::item("bonus_channel", $data->channel)'
		),
		array (
			'name'=>'type', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'Dict::item("bonus_type", $data->type)'
		),
		array (
			'name'=>'sn_type', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'Dict::item("bonus_sn_type", $data->sn_type)'
		),		
		array (
			'name'=>'sn_start', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'sn_end', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'end_date', 
			'headerHtmlOptions'=>array (
				'width'=>'60px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'date("Y-m-d",$data->end_date)'
		), 
		array (
			'name'=>'is_limited', 
			'headerHtmlOptions'=>array (
				'width'=>'60px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'Dict::item("bonus_type_limit", $data->is_limited)'
		),
			
			array (
 					'name'=>'查看优惠码',
 					'headerHtmlOptions'=>array (
 							'width'=>'60px',
 							'nowrap'=>'nowrap'
 					),
 					'type'=>'raw',
 					'value' => '($data->sn_type == 2)?CHtml::link("查看", array("bonusareastatic/admin","BonusAreaStatic[bonus_type_id]"=>$data->id)) : "暂无下载"'
 			),
			array (
					'name'=>'remark',
					'headerHtmlOptions'=>array (
							'width'=>'60px',
							'nowrap'=>'nowrap'
					),
					'type'=>'raw',
					'value'=>'$data->remark'
			),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
