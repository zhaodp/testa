<?php
$city = Dict::items('city');
$this->pageTitle = 'vip及散客统计';
echo "<h1>".$this->pageTitle."</h1><br />";
echo "<div class='search-form'>";
echo '<div class="span12">';
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
));
Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
echo '开始日期：';
$this->widget('CJuiDateTimePicker', array (
    'id' => 'report_start_time',
	'name'=>'start_time', 
	'value'=>$condition['start_time'], 
	'mode'=>'date',
	'options'=>array (
	    'width' => '60',
		'dateFormat'=>'yy-mm-dd'
	),
	'htmlOptions'=>array(
         'style'=>'width:100px;'
     ),
	'language'=>'zh'
));
echo "&nbsp;&nbsp;";
echo '结束日期：';
$this->widget('CJuiDateTimePicker', array (
	'id' => 'report_end_time',
    'name'=>'end_time', 
	'value'=>$condition['end_time'], 
	'mode'=>'date',
	'options'=>array (
		'dateFormat'=>'yy-mm-dd'
	), 
	'htmlOptions'=>array(
         'style'=>'width:100px;'
     ),
	'language'=>'zh'
));
echo "&nbsp;&nbsp;";
$city = Common::getOpenCity();
echo '城市：';
echo "<select id='report_city_id' name='city_id' style='width:80px;'>";
foreach ($city as $k=>$v)
{
	echo $condition['city_id'] == $k ? "<option value='".$k."' selected>".$v."</option>" : "<option value='".$k."' >".$v."</option>";
}
echo "</select>&nbsp;&nbsp;";
echo CHtml::submitButton('Search');
$this->endWidget();

echo '</div>';
echo '</div>';
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'vip-fit-grid',
    'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped',
    //'filter'=>$model,
    'columns'=>array(
		 array(
			'name'=>'日期',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => 'date("Y-m-d" , strtotime($data["date"]))'),
		array(
			'name'=>'城市',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["city"]'),
		array(
			'name'=>'vip数量',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["vip_num"]'),
		array(
			'name'=>'vip单数',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["vip_order_num"]'),
		 array(
			'name'=>'vip消费',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["vip_cost"]'),
		 array(
			'name'=>'散客数量',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["fit_num"]'),
		 array(
			'name'=>'散客单数',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["fit_order_num"]'),
		array(
			'name'=>'散客消费',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["fit_cost"]'),
     ),
));
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('vip-fit-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>