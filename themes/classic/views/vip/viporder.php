<?php
/* @var $this VipController */
/* @var $model Vip */

$this->breadcrumbs=array(
	'Vips'=>array('index'),
	'Manage',
);


$this->pageTitle = 'VIP 交易列表';

$selVip_id = isset($_POST['vip_id'])? $_POST['vip_id'] : '';
$selCityId = isset($_POST['city_id'])? $_POST['city_id'] : 0;
$selStatus = isset($_POST['status'])? $_POST['status'] : 0;
$selType = isset($_POST['type'])? $_POST['type'] : 0;
$start_time = isset($_POST['start_time'])? $_POST['start_time'] : '';
$end_time = isset($_POST['end_time'])? $_POST['end_time'] : '';
$errorTime = '';
if ($start_time < $end_time)
{
	$errorTime = '结束时间不能小于开始时间'; 
}
?>
<div class="row">
<h1>VIP 交易列表</h1>
</div>
<div class="row">
<div class="span12">
<p>卡号：<?php echo $Vip->id;?> 主卡人姓名：<?php echo $Vip->name;?> 手机号码：<?php echo $Vip->phone;?> 余额：<?php echo $Vip->remain;?> 状态：<?php echo Vip::model()->arrStatus[$Vip->status];?></p>
</div>
</div>
<div class="row">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-admin-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>false,
	'errorMessageCssClass'=>'alert alert-error'
)); ?>
<div class="span3">
<?php
	$type = VipOrder::model()->arrType;
	$type[0] = '--全部--';
	ksort($type);
	echo CHtml::label('交易类型','type'); 
	echo CHtml::dropDownList('type',
				$selType,
				$type,
		array()
	); 
?>
</div>
<div class="span3">
<label for="start_time">开始时间</label>		
<?php
	Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
	$this->widget('CJuiDateTimePicker', array (
		'name'=>'start_time', 
//		'model'=>$model,  //Model object
		'value'=>$start_time, 
		'mode'=>'date',  //use "time","date" or "datetime" (default)
		'options'=>array (
			'dateFormat'=>'yyyy-mm-dd'
		),  // jquery plugin options
		'language'=>'zh',
	));
?>
</div>
<div class="span3">
<label for="end_time">结束时间</label>		
<?php
	Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
	$this->widget('CJuiDateTimePicker', array (
		'name'=>'end_time', 
//		'model'=>$model,  //Model object
		'value'=>$end_time, 
		'mode'=>'date',  //use "time","date" or "datetime" (default)
		'options'=>array (
			'dateFormat'=>'yyyy-mm-dd'
		),  // jquery plugin options
		'language'=>'zh',
	));
	if (!empty($errorTime))
	{
		echo '<div class="alert alert-error">' . $errorTime . '</div>';
	}	
?>
</div>
</div>
<div class="row">
<div class="span2">
<?php echo CHtml::submitButton('搜索',array('class'=>'btn btn-success')); ?>
</div>
<div class="span2">
<?php echo CHtml::button('导出数据',array('class'=>'btn btn-success')); ?>
</div>
</div>
<?php $this->endWidget(); ?>
<div class="row">
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'vip-grid',
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array (
			'name' => 'ctime',
			'value' => 'date("Y-m-d", $data->ctime)'
		),
		array (
		'name' => 'type',
		'value' => 'Yii::app()->controller->getTradeType($data->type)'
		),
		'amount',				
		'remain',
		array (
			'name' => '操作人',
		),
		'phone',		
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
</div>