<?php
/* @var $this VipController */
/* @var $model Vip */

$this->breadcrumbs=array(
	'Vips'=>array('index'),
	'Manage',
);


$this->pageTitle = 'VIP 交易列表';

$selVipcard = isset($_GET['vipcard'])? $_GET['vipcard'] : '';
$selCityId = isset($_GET['city_id'])? $_GET['city_id'] : 0;
$selStatus = isset($_GET['status'])? $_GET['status'] : 0;
$selType = isset($_GET['type'])? $_GET['type'] : '';
$start_time = isset($_GET['start_time'])? $_GET['start_time'] : '';
$end_time = isset($_GET['end_time'])? $_GET['end_time'] : '';
$errorTime = '';
if (!empty($end_time) && strtotime($start_time) > strtotime($end_time))
{
	$errorTime = '结束时间不能小于开始时间'; 
}

?>
<div class="row">
<h1>VIP 交易列表</h1>
</div>
<?php 
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('viptrade-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<div class="search-form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'vip-trade-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>false,
	'errorMessageCssClass'=>'alert alert-error',
)); ?>
<div class="row-fluid">
<div class="span3">
<?php
	$type1 = VipTrade::model()->arrType;
	$type[''] = '--全部--';
	$type +=$type1;
	echo CHtml::label('交易类型','type'); 
	echo CHtml::dropDownList('type',
				$selType,
				$type,
		array()
	); 
?>
</div>
<div class="span3">
<?php
	$status = Vip::model()->arrStatus;
	$status[0] = '--全部--';
	ksort($status);
	echo CHtml::label('状态选择','status'); 
	echo CHtml::dropDownList('status',
				$selStatus,
				$status,
		array()
	); 
?>
</div>
</div>

<div class="row-fluid">
<div class="span3">
<label for="vip_id">请输入VIP卡号</label>
<input type="text" id="vipcard" name="vipcard" value="<?php echo $selVipcard;?>" />
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
			'dateFormat'=>'yy-mm-dd'
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
			'dateFormat'=>'yy-mm-dd'
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
<div class="row-fluid">
<div class="span2">
<?php echo CHtml::submitButton('搜索',array('class'=>'btn btn-success')); ?>
</div>
<div class="span2">
<?php echo CHtml::button('导出数据',array('class'=>'btn btn-success')); ?>
</div>
</div>
</div>
<?php $this->endWidget(); ?>
<div class="row">
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'viptrade-grid',
	'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped',
	'columns'=>array(
		array(
			'name' => 'created',
			'value' => 'date("Y-m-d", $data->created)'
		),
		array(
			'name' => 'type',
			'value' => 'Yii::app()->controller->getTradeType($data->type)'
		),	
		'amount',				
		'vipcard'
	),
)); ?>
</div>