<?php


Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#driver-upload-sms-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>短信上报信息管理</h1>



<?php
$driver_id = isset($_GET['driver_id']) ? $_GET['driver_id'] : '';
$driver_id_text = "<input type='text' name='driver_id' value='$driver_id'>";
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'driver-upload-sms-grid',
    'dataProvider'=>$model->search(),
    'itemsCssClass'=>'table table-striped',
    'filter'=>$model,
    'columns'=>array(
        'id',
        'phone',
        'content',
        'driver_id'=>array(
            'header'=>'司机工号',
            'filter'=>$driver_id_text,
            'type'=>'raw',
            'value'=>'$data->targetPosition()',
        ),
        //'user_id',
        'status' =>array(
            'filter'=>CHtml::dropDownList('status',$model->status,DriverUploadSms::$status),
            'name'=>'status',
            'value'=>'$data->status==0 ?  "未处理" : "己处理"',
        ),
        'type' =>array(
            'filter'=>CHtml::dropDownList('type',$model->type,DriverUploadSms::$type),
            'name'=>'type',
            'value'=>'$data->type=="sms" ?  "短信" : "呼叫"',
        ),
        'created',
        //'update_time',
        array(
            'header'=>'操作',
            'type'=>'raw',
            'value'=>'$data->getHref()',
        ),
    ),
)); ?>

