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
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-admin-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>false,
	'errorMessageCssClass'=>'alert alert-error'
)); ?>
	<div class="span3">
	
<?php
	echo CHtml::label('城市选择','city_id'); 
	$citys = Dict::items('city');
	$citys[0] = '--全部--';
	
	echo CHtml::dropDownList(
		'city_id',
		$selCityId,
		$citys,
		array()
	);
	
?>
</div>
<div class="span3">
<?php
	$type = Vip::model()->arrType;
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
<div class="row">
<div class="span3">
<label for="vip_id">请输入VIP卡号</label>
<input type="text" id="vip_id" name="vip_id" value="<?php echo $selVip_id;?>" />
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
		'id',
		'vip_id',
		'name',
		'phone',
		array(
			'name' => '城市',
			'value' => 'Yii::app()->controller->getVipCity($data->city_id)'
		),
		array(
			'name' => 'type',
			'value' => 'Yii::app()->controller->getStatus($data->type)'
		),			
		array(
			'name' => '首次开卡时间',
			'value' => 'date("Y-m-d", $data->ctime)'
		),
		'amount',				
		'remain',
		'phone',		
		array(
			'name' => '状态',
			'value' => 'Yii::app()->controller->getStatus($data->status)'
		),		
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
</div>