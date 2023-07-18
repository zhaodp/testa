<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('rank-day-list-grid', {
		data: $(this).serialize()
	});
	var city_id = $('#DailyDriverOrderReport_city_id').val();
	var type = $('#DailyDriverOrderReport_current_day').val();
	if(city_id!='' && type!=''){
//		var data = 'city_id='+ city_id + '&type='+type;
//		$.ajax({
//			type: 'get',
//			url: '".Yii::app()->createUrl('/notice/driverrankcountajax')."',
//			data: data,
//			dataType : 'html',
//			success: function(html){
//				$('#rank').html(html);
//		}});
	}
	return false;
});
");

Yii::app()->clientScript->registerScript('searchd', "
$('.search-formd form').submit(function(){
	$.fn.yiiGridView.update('notice-grid', {
		data: $(this).serialize()
	});	
});
");

if($params['city_id'] > 0 && $params['category'] == 0 && !isset($_GET['Notice'])){
?>
<div id="rank">
<?php
	//if($driverRankCount){
		//echo $driverRankCount;
	//}
?>
</div>
<h2>排行榜<small>（按收入排行）</small></h2>

	<div class="span12 search-form" style="display:none;">
	
	<?php $form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl('notice/index'),
		'method'=>'get',
	)); ?>
		<input type="hidden" name="DailyDriverOrderReport[city_id]" id="DailyDriverOrderReport_city_id" value = "<?php echo $params['city_id'];?>"/>
		<input type="hidden" name="DailyDriverOrderReport[current_day]" id="DailyDriverOrderReport_current_day" value = "0"/>
		<?php echo CHtml::submitButton('Search'); ?>
	<?php $this->endWidget(); ?>
	</div><!-- search-form -->
	<div class="btn-group">
	<?php 
		echo CHtml::link('昨日排行','javascript:viod(0);',array('onclick'=>'daily(0)','id'=>'btn0','class'=>"search-button btn-primary btn"));
		echo CHtml::link('上月排行','javascript:viod(0);',array('onclick'=>'daily(1)','id'=>'btn1','class'=>"btn"));
	?>
	</div>

	
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
					'name'=>'总接单天数',
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
	
	
	//echo CHtml::link('更多司机',"javascript:void(0);",array("onclick"=>"{driverDialogdivInit($params[city_id],$type);}",'id'=>'more','style'=>'float:right;'));
}

$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
	'id'=>'view_driver_dialog', 
	'options'=>array (
		'title'=>'查看司机信息', 
		'autoOpen'=>false, 
		'width'=>'780', 
		'height'=>'580', 
		'modal'=>true, 
		'buttons'=>array (
			'关闭'=>'js:function(){$("#view_driver_dialog").dialog("close");}'))));
echo '<div id="view_driver_dialog"></div>';
echo '<iframe id="view_driver_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

$click_view = <<<EOD
function(){
	$("#view_driver_frame").attr("src",$(this).attr("href"));
	$("#view_driver_dialog").dialog("open");
	return false;
}
EOD;

//Yii::app()->getClientScript()->registerCssFile(SP_URL_CSS . 'table.css',CClientScript::POS_END);

$category = isset($_GET['category'])?$_GET['category']:0;

switch ($category){
	case 0:
		echo '<h1>近期公告</h1>';
		echo '<div>';
		echo '<div class="search-formd span12">';
		$this->renderPartial('_searchnotice',array('model'=>$model,'is_city'=>$is_city));
		echo '</div></div>';
		$this->pageTitle = Yii::app()->name . ' - 近期公告';
		break;
	case 1:
		echo '<h1>培训教材</h1>';
		$this->pageTitle = Yii::app()->name . ' - 培训教材';
		break;
}

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'mydialog',
    'options'=>array(
        'title'=>'查看公告',
        'autoOpen'=>false,
		'width'=>'750',
		'height'=>'450',
		'modal'=>true,
		'buttons'=>array(
            'OK'=>'js:function(){dialogClose($("#n_id").val())}',  
        ),
    ),
));
echo '<div id="dialogdiv"></div>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'silver_table',
	'itemsCssClass'=>'table table-striped',
	'pagerCssClass'=>'pagination text-center', 
	'pager'=>Yii::app()->params['formatGridPage'], 
	'dataProvider'=>$dataProvider,
	//'htmlOptions'=>array('class'=>''),
	'columns'=>array(
		array (
			'name'=>'title', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'CHtml::link($data->title, "javascript:void(0);", array("onclick"=>"{dialogInit(\'$data->id\');}"))'
		), 
		array(
			'name'=>'class',
			'headerHtmlOptions'=>array(
				'width'=>'60px',
				'nowrap'=>'nowrap',
			),
			'type'=>'raw',
			'value'=>'Yii::app()->controller->getNoticeStatus($data->class)',
		),
        'author',
		array(
			'name'=>'deadline',
			'headerHtmlOptions'=>array(
				'width'=>'140px',
				'nowrap'=>'nowrap',
			),
			'type'=>'raw',
			'value'=>'$data->deadline',
		),
		array (
			'name'=>'created', 
			'headerHtmlOptions'=>array (
				'width'=>'60px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'date("m-d",$data->created)'
		),
	)
)); 

//$city_id = ($employee) ? $employee->city_id : Yii::app()->user->city;
//
//if ($category == 0){
//	if (date('H') >= 9){
//		$dateBegin = mktime(9, 0, 0, date("m"), date("d") - 1, date("Y"));
//		$dateEnd = mktime(9, 0, 0, date("m"), date("d"), date("Y"));
//	} else {
//		$dateBegin = mktime(9, 0, 0, date("m"), date("d") - 2, date("Y"));
//		$dateEnd = mktime(9, 0, 0, date("m"), date("d") - 1, date("Y"));
//	}
//	
//	$criteria = new CDbCriteria();
//	$criteria->join = 'inner join t_driver on t.driver_id = t_driver.user';
//	if ($c$params['category'] 0){
//		$criteria->addCondition('city_id=:city_id');
//		$criteria->params = array (
//			':city_id'=>$city_id
//		);
//	}
//	$criteria->addInCondition('status', array(Order::ORDER_NOT_COMFIRM, Order::ORDER_COMPLATE));
//	$criteria->addBetweenCondition('booking_time', $dateBegin, $dateEnd);
//	$criteria->select = 'count(order_id) / count(DISTINCT driver_id) as driver_id';
//	
//	$order_count = Order::model()->find($criteria);
//	
//	echo '<h2><br>昨日排行</h2>  昨日人均单数：' . sprintf("%1\$.1f单", $order_count->attributes['driver_id']);
//	
//	$criteria = new CDbCriteria();
//	$criteria->select = 'count(order_id) as order_id, sum(income) as income, driver_id';
//	$criteria->addBetweenCondition('booking_time', $dateBegin, $dateEnd);
//	$criteria->addCondition('status = 1');
//	$criteria->order = 'order_id DESC';
//	$criteria->group = 'driver_id';
//	$criteria->limit = 10;
//
//	$last_order = new CActiveDataProvider(
//'Order',
//array(
//	'criteria'=>$criteria, 
//	'pagination'=>array (
//		'pageSize'=>10
//	)
//)
//			);
//	
//	$this->widget('zii.widgets.grid.CGridView', array(
//		'id'=>'order_table',
//		'pager'=>false,
//		'itemsCssClass'=>'table table-striped',
//		'dataProvider'=>$last_order,
//		'template'=>'{items}',
//		//'htmlOptions'=>array('class'=>''),
//		'columns'=>array(
//			array (
//'name'=>'司机工号', 
//'headerHtmlOptions'=>array (
//	'width'=>'60px',
//	'nowrap'=>'nowrap'
//),
//'value'=>'$data->driver_id'
//			), 
//			array (
//'name'=>'接单数', 
//'headerHtmlOptions'=>array (
//	'width'=>'60px',
//	'nowrap'=>'nowrap'
//), 
//'value'=>'$data->order_id'
//			),
//			array (
//'name'=>'接单收入', 
//'headerHtmlOptions'=>array (
//	'width'=>'60px',
//	'nowrap'=>'nowrap'
//), 
//'value'=>'$data->income'
//			),
//		)
//	)); 
//}
?>
<script>

function daily(type){
	$("#DailyDriverOrderReport_current_day").val(type);
 	$("#yw0").submit();
 	$(".btn-group a").removeClass("btn-primary");
 	$("#btn"+type).addClass("btn-primary");
 	var city_id = <?php echo $params['city_id'];?>;
 	var more_url = 'driverDialogdivInit('+city_id+','+type+')';
 	$("#more").attr('onclick', more_url);
}

function dialogInit(id){
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/notice/view');?>',
		'data':'id='+id,
		'type':'get',
		'success':function(data){
				$('#dialogdiv').html(data);
			
		},
		'cache':false		
	});
	$("#mydialog").dialog("open");
	return false;
}
function closedDialog_rank(id){
	$("#"+id).dialog("close");
	$.fn.yiiGridView.update('driver-exam-grid');
}

function openDialog_rank(url){
	$("#view_exam_frame").attr("src",url);
	$("#view_exam_dialog").dialog("open");
	return false;
}

function dialogClose(id){
	if(id!=''){
		$.ajax({
			'url':'<?php echo Yii::app()->createUrl('/notice/read');?>',
			'data':'id='+id,
			'type':'get',
			'success':function(data){
				unreadNoticeOpen();
			},
			'cache':false		
		});	
	}else{
		unreadNoticeOpen();
	}
	$("#mydialog").dialog("close");
	return false;
}

function unreadNoticeOpen(){
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/notice/newest');?>',
		'type':'get',
		'success':function(data){
			if (data > 0){
				dialogInit(data);
			}else{
				$("#mydialog").dialog("close");
				return false;
			}
			
		},
		'cache':false		
	});
}

window.onload=function(){
	unreadNoticeOpen();
}

function driverDialogdivInit(city_id,type){
	var src = "<?php echo Yii::app()->createUrl('/notice/rank');?>";
	src += '&city_id=' + city_id + '&type=' + type;
	$("#view_driver_frame").attr("src",src);
	$("#view_driver_dialog").dialog("open");
	return false;
}
function showDiv(sDiv){
	document.getElementById('searchDiv').style.display = (document.getElementById('searchDiv').style.display=="block") ? "none" : "block";
}
</script>