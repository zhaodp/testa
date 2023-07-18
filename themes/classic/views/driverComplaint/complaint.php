<h1>司机投诉管理</h1>
<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#driver-complaint-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<?php
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl('DriverComplaint/Coustomer/phone/'.$phone),
	'method'=>'post',
)); 
?>
<div class="row-fluid">
	投诉时间：
	<?php   
    $this->widget('zii.widgets.jui.CJuiDatePicker',array(  
        'attribute'=>'visit_time',  
        'language'=>'zh_cn',  
        'name'=>'start_ts',  
        'options'=>array(  
            'showAnim'=>'fold',  
            'showOn'=>'both',  
            'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.gif',  
            'buttonImageOnly'=>true,  
            //'minDate'=>'new Date()',  
            'dateFormat'=>'yy-mm-dd',
			'changeYear'=>true,
			'changeMonth'=> true,	
        ),  
        'htmlOptions'=>array(  
            'style'=>'height:18px',  
        ),  
    ));  
    ?> 
	到
	<?php   
    $this->widget('zii.widgets.jui.CJuiDatePicker',array(  
        'attribute'=>'visit_time',  
        'language'=>'zh_cn',  
        'name'=>'end_ts',  
        'options'=>array(  
            'showAnim'=>'fold',  
            'showOn'=>'both',  
            'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.gif',  
            'buttonImageOnly'=>true,  
            //'minDate'=>'new Date()',  
            'dateFormat'=>'yy-mm-dd',
            'changeYear'=>true,
			'changeMonth'=> true,			
        ),  
        'htmlOptions'=>array(  
            'style'=>'height:18px',  
        ),  
    ));  
    ?>
		<input type="submit" value="查询" class='btn btn-success'/>
</div>
<?php $this->endWidget(); ?>
<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#driver-complaint-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-complaint-grid',
	'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped',
	'columns'=>array(
		'driver_user',
		array(
					'name'=>'司机姓名',
					'type' => 'raw',
					'value' =>'Yii::app()->controller->getDriverName($data->driver_user)'
		),
		array(
					'name'=>'司机手机',
					'type' => 'raw',
					'value' =>'Yii::app()->controller->getDriverPhone($data->driver_user)'
		),
		'customer_phone',
		array(
					'name'=>'投诉时间',
					'type' => 'raw',
					'value' =>'date("Y-m-d H:i",$data->create_time)'
		),
		array(
					'name'=>'代驾时间',
					'type' => 'raw',
					'value' =>'($data->driver_time!=0) ? date("Y-m-d H:i",$data->driver_time) : "无"'
		),
		array(
					'name'=>'订单类型',
					'type' => 'raw',
					'value' =>'($data->order_type==1) ? "报单" :"销单"'
		),
		array(
					'name'=>'投诉类型',
					'type' => 'raw',
					'value' =>array($this,'getComplaintType'),
		),
		'complaint_content'
	),
)); ?>
