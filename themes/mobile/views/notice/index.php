<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('rank-day-list-grid', {
		data: $(this).serialize()
	});
	var city_id = $('#DailyDriverOrderReport_city_id').val();
	var type = $('#DailyDriverOrderReport_current_day').val();
	if(city_id!='' && type!=''){
		var data = 'city_id='+ city_id + '&type='+type;
		$.ajax({
			type: 'get',
			url: '".Yii::app()->createUrl('/notice/driverrankcountajax')."',
			data: data,
			dataType : 'html',
			success: function(html){
				$('#rank').html(html);
		}});
	}
	return false;
});
");

if($params['city_id'] > 0 && $params['category'] == 0){
	?>
<!--<div id="rank">--><?php
//if($driverRankCount){
//	echo $driverRankCount;
//}
//?><!--</div>-->
<!-- search-form -->
<div class="btn-group"><?php 
if($type == 0){
	echo CHtml::link('昨日排行',Yii::app()->createUrl('notice/index',array('DailyDriverOrderReport[city_id]'=>$params['city_id'],'DailyDriverOrderReport[current_day]'=>0)),array('id'=>'btn0','class'=>"search-button btn-primary btn"));
	echo CHtml::link('上月排行',Yii::app()->createUrl('notice/index',array('DailyDriverOrderReport[city_id]'=>$params['city_id'],'DailyDriverOrderReport[current_day]'=>1)),array('id'=>'btn1','class'=>"btn"));
}else{
	echo CHtml::link('昨日排行',Yii::app()->createUrl('notice/index',array('DailyDriverOrderReport[city_id]'=>$params['city_id'],'DailyDriverOrderReport[current_day]'=>0)),array('id'=>'btn0','class'=>"btn"));
	echo CHtml::link('上月排行',Yii::app()->createUrl('notice/index',array('DailyDriverOrderReport[city_id]'=>$params['city_id'],'DailyDriverOrderReport[current_day]'=>1)),array('id'=>'btn1','class'=>"search-button btn-primary btn"));
}
?></div>

<h2>排行榜<small>（按收入排行）</small></h2>
<?php
switch ($type){
	case DailyDriverOrderReport::TYPE_DAILY:
		$this->widget('zii.widgets.grid.CGridView', array(
		    'id'=>'rank-day-list-grid',
			'template'=>"{items}", 
		    'dataProvider'=>$dataDailyOrderRank,
			'itemsCssClass'=>'table table-striped',
		    'columns'=>array(
		array(
					'name'=>'排名',
					'headerHtmlOptions'=>array(
						'width'=>'40px',
						'nowrap'=>'nowrap'
						),
					'value' => '$row+1'),
						array(
					'name'=>'司机姓名',
					'headerHtmlOptions'=>array(
						'width'=>'80px',
						'nowrap'=>'nowrap'
						),
					'value' => '$data["name"]'),
						array(
					'name'=>'司机工号',
					'headerHtmlOptions'=>array(
						'width'=>'80px',
						'nowrap'=>'nowrap'
						),
					'value' => '$data["driver_id"]'),
						array(
					'name'=>'总接单量',
					'headerHtmlOptions'=>array(
						'width'=>'80px',
						'nowrap'=>'nowrap'
						),
					'value' => '$data["order_count"]'),
						array(
					'name'=>'呼叫中心派单量',
					'headerHtmlOptions'=>array(
						'width'=>'80px',
						'nowrap'=>'nowrap'
						),
					'value' => '$data["app_count"]'),
						array(
					'name'=>'客户直接呼叫量',
					'headerHtmlOptions'=>array(
						'width'=>'80px',
						'nowrap'=>'nowrap'
						),
					'value' => '$data["callcenter_count"]'),
						array(
					'name'=>'总收入',
					'headerHtmlOptions'=>array(
						'width'=>'80px',
						'nowrap'=>'nowrap'
						),
					'value' => '$data["income"]'),
						),
						));
						break;
	case DailyDriverOrderReport::TYPE_MONTHLY:
		$this->widget('zii.widgets.grid.CGridView', array(
		    'id'=>'rank-day-list-grid',
			'template'=>"{items}", 
		    'dataProvider'=>$dataDailyOrderRank,
			'itemsCssClass'=>'table table-striped',
		    'columns'=>array(
		array(
					'name'=>'排名',
					'headerHtmlOptions'=>array(
						'width'=>'40px',
						'nowrap'=>'nowrap'
						),
					'value' => '$row+1'),
						array(
					'name'=>'司机姓名',
					'headerHtmlOptions'=>array(
						'width'=>'80px',
						'nowrap'=>'nowrap'
						),
					'value' => '$data["name"]'),
						array(
					'name'=>'司机工号',
					'headerHtmlOptions'=>array(
						'width'=>'80px',
						'nowrap'=>'nowrap'
						),
					'value' => '$data["driver_id"]'),
						array(
					'name'=>'出勤天数',
					'headerHtmlOptions'=>array(
						'width'=>'80px',
						'nowrap'=>'nowrap'
						),
					'value' => '$data["created"]'),
						array(
					'name'=>'总接单量',
					'headerHtmlOptions'=>array(
						'width'=>'80px',
						'nowrap'=>'nowrap'
						),
					'value' => '$data["order_count"]'),
						array(
					'name'=>'呼叫中心派单量',
					'headerHtmlOptions'=>array(
						'width'=>'80px',
						'nowrap'=>'nowrap'
						),
					'value' => '$data["app_count"]'),
						array(
					'name'=>'客户直接呼叫量',
					'headerHtmlOptions'=>array(
						'width'=>'80px',
						'nowrap'=>'nowrap'
						),
					'value' => '$data["callcenter_count"]'),
						array(
					'name'=>'总收入',
					'headerHtmlOptions'=>array(
						'width'=>'80px',
						'nowrap'=>'nowrap'
						),
					'value' => '$data["income"]'),
						),
						));
						break;
}
}
//Yii::app()->getClientScript()->registerCssFile(SP_URL_CSS . 'table.css',CClientScript::POS_END);

$category = isset($_GET['category'])?$_GET['category']:0;

switch ($category){
	case 0:
		echo '<h3>近期公告</h3>';
		break;
	case 1:
		echo '<h3>培训教材</h3>';
		break;
}

$this->widget('zii.widgets.CListView', array(
	'id'=>'well',
	'itemView'=>'_list',
	'summaryText'=>'',
	'itemsCssClass'=>'table table-stripe',
	'dataProvider'=>$dataProvider,
));

?>

