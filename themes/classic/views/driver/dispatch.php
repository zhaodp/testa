<?php $this->pageTitle = '派单'; ?>
<h1>派单</h1>
<hr class="divider"/>
<div class="span12">
<?php
$form = $this->beginWidget('CActiveForm', array (
	'id'=>'dispatch-form', 
	'enableClientValidation'=>false,
	'enableAjaxValidation'=>false,
));
?>
    <div class="row">
    	<label>城市：</label>
    	<?php echo CHtml::dropDownList('Order[city_id]', 1, Dict::items('city')); ?>
    </div>
    <div class="row">
    	<label>客户名称：</label>
    	<?php echo CHtml::textField('Order[name]','',array('size'=>20,'maxlength'=>20)); ?>
    </div>
    <div class="row">
    	<label>电话：</label>
		<?php echo CHtml::textField('Order[phone]','',array('size'=>20,'maxlength'=>20)); ?>
    </div>
    <div class="row">
    	<label>地点：</label>
		<?php echo CHtml::textField('Order[address]','',array('size'=>20,'maxlength'=>20)); ?>
    </div>
    <div class="row">
    	<label>时间：</label>
		<?php echo CHtml::textField('Order[booking_time]',date('Y-m-d H:i:s',time()),array('size'=>20,'maxlength'=>20));  ?>
    </div>
    <input size="20" maxlength="20" type="hidden" value="" name="Order[staff_id]" id="Order_staff_id" />
    <input size="20" maxlength="20" type="hidden" value="<?php echo Yii::app()->user->getId();?>" name="Order[agent_id]" id="Order_agent_id" />
    <input size="20" maxlength="20" type="hidden" value="" name="Order[customer_guid]" id="Order_customer_guid" />
<?php
echo CHtml::submitButton('查找司机', array (
	'class'=>'btn-large span2'
));

$this->endWidget();
?>

</div>
