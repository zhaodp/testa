<?php
$this->pageTitle = '司机排行统计';
echo "<h1>".$this->pageTitle."</h1><br />";
echo '<div id="ranking">';
echo $driverRankCount;
echo '</div>';
?>
<div class="search-form" >
<?php
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
         'style'=>'width:80px;'
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
         'style'=>'width:80px;'
     ),
	'language'=>'zh'
));
echo "&nbsp;&nbsp;";
$city = Common::getOpenCity();
echo '城市：';
echo "<select name='city_id' id='report_city_id' style='width:80px;'>";
foreach ($city as $k=>$v)
{
	echo $condition['city_id'] == $k ? "<option value='".$k."' selected>".$v."</option>" : "<option value='".$k."' >".$v."</option>";
//	if ($k == $condition['city_id']) 
//	{
//		echo "<option value='".$k."' selected>".$v."</option>";
//	}else {
//	    echo "<option value='".$k."' >".$v."</option>";
//	}
}
echo "</select>&nbsp;&nbsp;";
echo '时间段：';
echo "<select name='time_part' id='report_time_part' style='width:80px;'>";
echo "<option value=''>全部</option>";
echo $condition['time_part'] == 7 ? "<option value='7' selected>7-22点</option>" : "<option value='7'>7-22点</option>";
echo $condition['time_part'] == 22 ? "<option value='22' selected>22-23点</option>" : "<option value='22'>22-23点</option>";
echo $condition['time_part'] == 23 ? "<option value='23' selected>23-24点</option>" : "<option value='23'>23-24点</option>";
echo $condition['time_part'] == 24 ? "<option value='24' selected>24-7点</option>" : "<option value='24'>24-7点</option>";
echo "</select>";
echo "&nbsp;&nbsp;";
echo '距离范围：';
echo "<select name='distance_area' id='report_distance_area' style='width:80px;'>";
echo "<option value=''>全部</option>";
echo $condition['distance_area'] == 10 ? "<option value='10' selected>10公里</option>" : "<option value='10'>10公里</option>";
echo $condition['distance_area'] == 20 ? "<option value='20' selected>20公里</option>" : "<option value='20'>20公里</option>";
echo $condition['distance_area'] == 30 ? "<option value='30' selected>30公里</option>" : "<option value='30'>30公里</option>";
echo $condition['distance_area'] == 9999 ? "<option value='9999' selected>30公里以上</option>" : "<option value='9999'>30公里以上</option>";
echo "</select>";
echo "&nbsp;&nbsp;";
echo '司机工号：';
echo "<input type='text' name='driver_user' id='report_driver_user' style='width:80px;' value='".$condition['driver_user']."'>";
echo "&nbsp;&nbsp;";
//$form->dropDownList($model , 'city_id', Dict::items('city'));
echo CHtml::submitButton('Search');
$this->endWidget();
echo '</div>';
?>
</div>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'ranking-grid',
    'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped',
    //'filter'=>$model,
    'columns'=>array(
		 array(
			'name'=>'司机姓名',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["driver"]'),
		 array(
			'name'=>'司机工号',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["driver_user"]'),
		array(
			'name'=>'总接单量',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["order_count"]'),
		array(
			'name'=>'客户直接呼叫量',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["app_count"]'),
		 array(
			'name'=>'呼叫中心派单量',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["callcenter_count"]'),
		array(
			'name'=>'完成报单数量',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["complate_count"]+$data["not_confirm_count"]'),
		array(
			'name'=>'销单数量',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["cancel_count"]'),
		array(
			'name'=>'总收入',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["income"]'),
     ),
));

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('ranking-grid', {
		data: $(this).serialize()
	});
	
	var start_time = $('#report_start_time').val();
	var end_time = $('#report_end_time').val();
	var driver_user = $('#report_driver_user').val();
	var city_id = $('#report_city_id').val();
	var time_part = $('#report_time_part').val();
	if(start_time!='' && end_time!=''){
		var data = 'start_time='+start_time+'&end_time='+end_time+'&city_id='+ city_id + '&time_part='+time_part;
		$.ajax({
			type: 'get',
			url: '".Yii::app()->createUrl('/report/driverrankingajax')."',
			data: data,
			dataType : 'html',
			success: function(html){
				$('#ranking').html(html);
		}});
	}
	return false;
});
");
?>