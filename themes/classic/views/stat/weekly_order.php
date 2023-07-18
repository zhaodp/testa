<?php $this->pageTitle = '订单月报统计数据'; ?>

<h1><?php echo $this->pageTitle; ?></h1>
<div class="wide form">
<?php 
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('silver_table', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<?php echo CHtml::link('高级搜索','#',array('class'=>'search-button  btn-primary btn')); ?>
<div class="search-form" style="display:none">
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
<section>
	
		<label>城市</label>
		<?php $citys = Dict::items('city');?>
	<select class="span2" name="weekly_order[city_id]" id="weekly_order_city_id">
	<?php foreach ($citys as $code => $city) {?>
	<option value="<?php echo $code;?>"><?php echo $city; ?></option>
	<?php }?>
</select>	
		
	
		<label>日期</label>		
		<?php
			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'weekly_order[current_date]', 
				//'model'=>$model,  //Model object
				'value'=>'', 
				'mode'=>'date',  //use "time","date" or "datetime" (default)
				'options'=>array (
					'dateFormat'=>'yy-mm-dd'
				),  // jquery plugin options
				'language'=>'zh'
			));
			?>	
		<?php echo CHtml::submitButton('Search'); ?>
</section>
<?php $this->endWidget(); ?>

</div><!-- search-form -->
</div><!-- search-form -->
<?php
function city_short_name ($city_id){
	switch ($city_id){
		case 1:
			return 'BJ';
			break;
		case 2:
			return 'CD';
			break;
		case 3:
			return 'SH';
			break;
	}
}
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'silver_table',
	'itemsCssClass'=>'table table-striped',
	'dataProvider'=>$dataProvider,
	//'htmlOptions'=>array('class'=>''),
	'columns'=>array(
		array (
			'name'=>'日期', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'value'=>'$data["current_date"] . " " . date("D", strtotime($data["current_date"]))'
		), 
		array (
			'name'=>'城市', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'Dict::item("city", $data["city_id"])'
		),
		array (
			'name'=>'人工', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data["by_callcenter"]'
		),
		array (
			'name'=>'客户端', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data["by_application"]'
		),
		array (
			'name'=>'取消', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data["canceled"]'
		),
		array (
			'name'=>'订单总数', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data["by_callcenter"] + $data["by_application"]'
		),
		array (
			'name'=>'上周同期', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data["last_weekday"]'
		),
		array (
			'name'=>'同比增长', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'($data["last_weekday"] > 0) ? round(($data["by_callcenter"] + $data["by_application"] - $data["last_weekday"]) / $data["last_weekday"] * 100, 1) . "%" : 0'
		),
		array (
			'name'=>'未完成订单', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'type'=>'raw',
			'value'=>'CHtml::link($data["unfinished"], array("order/admin", "Order"=>array("location_start"=>$data["city_id"], "status" =>0, "call_time"=>$data["current_date"] . " 09:00", "booking_time"=>date("Y-m-d", strtotime($data["current_date"]) + 86400) . " 09:00")))'
		),
		array (
			'name'=>'新用户', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'(empty($data["fresh"]) ? 0 : $data["fresh"])'
		),
		array (
			'name'=>'老用户', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'(empty($data["repeat"]) ? 0 : $data["repeat"])'
		),
	)
	
)); 

?>
