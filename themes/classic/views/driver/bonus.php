 <?php
/* @var $this DriverBonusController */
/* @var $model DriverBonus */
/* @var $form CActiveForm */
$employee = Driver::getProfile(Yii::app()->user->getId());
$city_id = ($employee) ? $employee->city_id : Yii::app()->user->city;
?>
<h1>发卡收入明细</h1>
<hr class="divider"/>
<?php if(!$model->bonus) {?>
<div class="form" <?php if($city_id != 7) echo 'style="display:none"';?>>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-bonus-form',
	'enableAjaxValidation'=>false,
)); ?>
<p class="note">您还未绑定优惠码，请绑定(注意检查号码，保存后不可修改)：</p>
<?php echo $form->textField($model,'bonus'); ?>
<?php echo CHtml::submitButton('绑定优惠码',array('class'=>'btn btn-success','style'=>'margin-left:4px;margin-bottom:9px')); ?>
<?php $this->endWidget(); ?>
</div>
<?php 
}
?>
<?php 
	Yii::app()->clientScript->registerScript('search', "
	$('.search-button').click(function(){
		$('.search-form').toggle();
		return false;
	});
	$('.search-form form').submit(function(){
		$.fn.yiiGridView.update('customer-bonus-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	");
?>

<?php 
$arrBonus = array();
if ($model->bonus)
{
	if (strlen($model->bonus) > 6)
		$bonus = substr($model->bonus,0,strlen($model->bonus)-1);
	else 
		$bonus = $model->bonus;
		
	array_push($arrBonus, $bonus);
}

$driver_id = Yii::app()->user->id;
//$city_prefix = strtoupper(substr($driver_id, 0, 2));
$city_prefix = Dict::item("city_prefix",$city_id);
$driver_no = substr($driver_id,strlen($city_prefix));

$arrCityPrefix = array_flip(Dict::items("bonus_city"));
$city_id = $arrCityPrefix[$city_prefix];

$driverBonus = $city_id . $driver_no;

array_push($arrBonus, $driverBonus);

$criteria = new CDbCriteria();
$criteria->addInCondition('bonus_sn', $arrBonus);

?>
<div class="row-fluid">
<div class="span6">
<h3>新客邀请码<?php echo sprintf('(%s)',$driverBonus);?></h3>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table">
	<tr class="alert alert-info">
		<td>捆绑数: <?php echo $this->getBonusBindCount($driverBonus);?></td>
	</tr>
	<?php 
	$account = $this->getDriverBonus(EmployeeAccount::TYPE_DRIVER_BONUS_RETUEN);;
	$num = $account/20;
	?>
	<tr class="alert alert-error">
		<td>消费数：<?php echo $this->getBonusUsedCount($driverBonus);?>（其中销单数为：<?php echo $this->getBonusUsedCount($driverBonus) - $num;?> ）</td>
	</tr>
	<tr class="alert alert-success">
		<td>收入：<?php echo $account;?>元</td>
	</tr>
</table>
</div>
<?php
if ($model->bonus)
{ 
?>
<div class="span6">
<h3>司机优惠卡<?php if($model->bonus){echo sprintf('(%s)',$model->bonus);}?></h3>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table">
	<tr class="alert alert-info">
		<td>捆绑数: <?php echo $this->getBonusBindCount($bonus);?></td>
	</tr>
	<?php 
	$account = $this->getDriverBonus(EmployeeAccount::TYPE_BONUS_RETUEN);
	$num = $account/20;
	?>
	<tr class="alert alert-error">
		<td>消费数：<?php echo $this->getBonusUsedCount($bonus);?>（其中销单数为：<?php echo $this->getBonusUsedCount($bonus) - $num;?> ）</td>
	</tr>
	<tr class="alert alert-success">
		<td>收入：<?php echo $account;?>元</td>
	</tr>
</table>
</div>

<?php 
}
?>
</div>
<h3>用户绑定列表：</h3>
<?php

$dataProvider = new CActiveDataProvider(CustomerBonus::model(), array (
			'criteria'=>$criteria));

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'customer-bonus-grid',
	'dataProvider'=>$dataProvider,
	'cssFile'=>SP_URL_CSS . 'table.css',
	'itemsCssClass'=>'table table-condensed',
	'columns'=>array(
		array (
			'name'=>'绑定时间',
			'value'=>'($data->created > 0) ? date("Y.m.d",$data->created) : ""',
        ),
		array (
			'name'=>'customer_phone',
			'type'=>'raw',
			'value'=>'mb_substr($data->customer_phone, 0, 3) . "****" . mb_substr($data->customer_phone, 7, 11)'
		),        
		array (
			'name'=>'是否消费',
			'value'=>'($data->order_id > 0) ? "是" : "否"',
        ),        
		array (
			'name'=>'消费时间',
			'value'=>array($this, 'getOrderTime'),
        ),
		array (
			'name'=>'收入(元)',
			'value'=>array($this, 'getBonusCast'),
        ),
        array (
        	'name' => '优惠卡号码',
        	'value'=> array($this, 'getBonus'),
        )                   
	),
)); 
?>

