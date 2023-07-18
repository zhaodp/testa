<?php
$city = Dict::items('city');
$this->pageTitle = '新老客统计';
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
    'id'=>'fresh-repeat-grid',
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
			'name'=>'用户总数',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["total"]'),
		array(
			'name'=>'新客总数',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["fresh_total"]'),
		 array(
			'name'=>'app新客',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["first_app"]'),
		 array(
			'name'=>'400新客',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["first_callcenter"]'),
		 array(
			'name'=>'老客总数',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["repeat_total"]'),
		array(
			'name'=>'400老客',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["repeat_callcenter"]'),
		array(
			'name'=>'app老客',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["repeat_app"]'),
		array(
			'name'=>'小于4次使用400用户数',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["callcenter_sms_num"]'),
     ),
));
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('fresh-repeat-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
