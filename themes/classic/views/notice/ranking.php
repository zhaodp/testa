<?php
/* @var $this RankDayListController */
/* @var $model RankDayList */

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
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

$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
	'id'=>'view_exam_dialog', 
	// additional javascript options for the dialog plugin
	'options'=>array (
		'title'=>'排行榜', 
		'autoOpen'=>false, 
		'width'=>'780', 
		'height'=>'580', 
		'modal'=>true, 
		'buttons'=>array (
			'关闭'=>'js:function(){closedDialog_rank("view_exam_dialog")}'))));
echo '<div id="view_exam_dialog"></div>';
echo '<iframe id="view_exam_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<div id="rank">
	<?php if($driverRankCount){
		echo $driverRankCount;
	}?>
</div>
<h1>排行榜</h1>

<div class="search-form" >
<?php $this->renderPartial('_searchranking',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->
<?php
switch ($type){
	case 0:
		$this->widget('zii.widgets.grid.CGridView', array(
		    'id'=>'rank-day-list-grid',
		    'dataProvider'=>$dataProvider,
			'itemsCssClass'=>'table table-striped',
			'pagerCssClass'=>'pagination text-center', 
			'pager'=>Yii::app()->params['formatGridPage'], 		
		    //'filter'=>$model,
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
	case 1:
			$this->widget('zii.widgets.grid.CGridView', array(
		    'id'=>'rank-day-list-grid',
		    'dataProvider'=>$dataProvider,
			'itemsCssClass'=>'table table-striped',
			'pagerCssClass'=>'pagination text-center', 
			'pager'=>Yii::app()->params['formatGridPage'], 			
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
//echo CHtml::link('更多司机',"http://www.baidu.com",array("id"=>"more","onclick"=>"{openDialog_rank('index.php?r=notice/rank&city_id=$city_id&type=$type');}"));
 ?>
 <script>
function closedDialog_rank(id){
	$("#"+id).dialog("close");
	$.fn.yiiGridView.update('driver-exam-grid');
}
function openDialog_rank(url){
	$("#view_exam_frame").attr("src",url);
	$("#view_exam_dialog").dialog("open");
	return false;
}
</script>
    
