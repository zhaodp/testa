<?php
$this->pageTitle = '优惠券统计报表';

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('bonus-type-order-list-grid', {
		data: $(this).serialize()
	});
	
	return false;
});
");

?>
<h1><?php echo $this->pageTitle; ?></h1>

<?php echo CHtml::link('高级搜索','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search_order_list',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php
	

$timeToday = strtotime($today);

$arrData = array();
for ($i = 0; $i <= $nCount; $i ++)
{
	$time = $timeToday - ($i * 86400);
	$date = date('Y-m-d', $time);	
	$data['date'] = $date;
	$data['bonus_type_id'] = $bonus_type_id;
	array_push($arrData, $data);
	
}	

$dataProvider = new CArrayDataProvider($arrData, array('id'=>'bonus-type-order-list-data', 
		'keyField'=>'date', 'pagination'=>array('pageSize'=>10)));

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'bonus-type-order-list-grid',
	'itemsCssClass'=>'table table-striped',
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array (
			'name'=>'日期', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'value'=>array($this,'getWeekDate')
		), 
		array (
			'name'=>'绑定个数', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>array($this, 'getGridDataBindCount')
		),
		array (
			'name'=>'消费个数', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value'=>array($this, 'getGridDataUsedCount')
		),
		array (
			'name'=>'绑定总数', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value'=>array($this, 'getGridDataBindCountSum')
		),
		array (
			'name'=>'消费总数', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value'=>array($this, 'getGridDataUsedCountSum')
		),				
	)
));
?>
