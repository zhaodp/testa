<?php
$this->pageTitle = '派单司机列表';
?>
<h3>派单司机列表</h3>
<div class="form-inline">

<?php
Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
$form = $this->beginWidget('CActiveForm', array(
    'action' => Yii::app()->createUrl($this->route),
    'method' => 'get',
));
?>
开始时间
<?php
Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
$this->widget('CJuiDateTimePicker', array(
    'id' => 'start_time',
    'name' => 'start_time',
    'value' => $condition['start_time'],
    'mode' => 'datetime',
    'options' => array(
        'width' => '60',
        'dateFormat' => 'yy-mm-dd'
    ),
    'htmlOptions' => array(
        'style' => 'width:140px;margin:0px 10px 0px 5px'
    ),
    'language' => 'zh'
));?>
结束日期
<?php $this->widget('CJuiDateTimePicker', array(
    'id' => 'end_time',
    'name' => 'end_time',
    'value' => $condition['end_time'],
    'mode' => 'datetime',
    'options' => array(
        'dateFormat' => 'yy-mm-dd'
    ),
    'htmlOptions' => array(
        'style' => 'width:140px;margin:0px 10px 0px 5px'
    ),
    'language' => 'zh'
));?>
订单状态：
<?php 
echo CHtml::dropDownList('flag', $condition['flag'], array(''=>'全部' , '1'=>'派单中' , '2' => '已接单' , '接单成功') , array('style' => 'width:100px;margin:0px 10px 0px 0px')); ?>
QueueID：
<?php
echo CHtml::textField('queue_id', $condition['queue_id'] , array('style' => 'width:60px;'));
?>
订单号：
<?php
echo CHtml::textField('order_id', $condition['order_id'] , array('style' => 'width:60px;'));
echo CHtml::submitButton('Search', array('class' => "btn btn-primary span1",'style' => 'margin:0px 0px 0px 5px;'));

$this->endWidget();
?>

</div>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'log-grid',
    'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped',
    //'filter'=>$model,
    'columns'=>array(
		 array(
			'name'=>'QueueID',
			'headerHtmlOptions'=>array(
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["queue_id"]'),
		array(
			'name'=>'OrderID',
			'headerHtmlOptions'=>array(
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["order_id"]'),
		array(
			'name'=>'派单司机工号',
			'headerHtmlOptions'=>array(
				'width'=>'300px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["driver_id"]'),
		array(
			'name'=>'手机号',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => array($this , 'getQueuePhone')),
		array(
			'name'=>'渠道号',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => array($this , 'getQueueChannel')),
		array(
			'name'=>'派单次数',
			'headerHtmlOptions'=>array(
				'width'=>'300px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["number"]'),
		array(
			'name'=>'接单状态',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value' => array($this , 'getLogFlag')),
		array(
			'name'=>'派单时间',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["dispatch_time"]'),
		array(
			'name'=>'成功接单时间',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["success_time"]'),
     ),
));

Yii::app()->clientScript->registerScript('search', "
$('.form-inline form').submit(function(){
	$.fn.yiiGridView.update('log-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>