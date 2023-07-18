<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'order-queue-create-form',
    'enableAjaxValidation'=>false,
)); ?>

    <div class="input-prepend input-append">
		<span class="add-on">所在城市</span>
		<?php 
			$city = Dict::items('city');
			$city[0] = '无法定位，请询问客户所在城市';
			echo $form->dropDownList($model,'city_id',$city);
		?>
    </div>

    <div class="input-prepend input-append">
		<span class="add-on">客户电话</span>
		<?php echo $form->textField($model,'phone'); ?>
		<?php echo $form->error($model,'phone'); ?>
    </div>


	<label style="background-color:#F2DEDE">您好，e代驾！很高兴为您服务！</label>
	<label style="background-color:#F2DEDE">请问您现在需要代驾司机吗？</label>
	<label for="OrderQueue_address" style="background-color:#F2DEDE">好的，请问您现在在什么位置？</label>
    <div class="input-prepend input-append">
		<span class="add-on">所在位置</span>
		<?php echo $form->textField($model,'address'); ?>
		<?php echo $form->error($model,'address'); ?>
		<span class="add-on"><?php echo $model->address; ?></span>
	</div>

    <label for="OrderQueue_name" style="background-color:#F2DEDE">好的,请问您贵姓？</label>
    <div class="input-prepend input-append">
		<span class="add-on">客户名称</span>
		<?php echo $form->textField($model,'name'); ?>
		<?php echo $form->error($model,'name'); ?>
    </div>
        
    <div class="input-prepend input-append">
		<span class="add-on">几位司机</span>
		<?php echo $form->textField($model,'number'); ?>
		<?php echo $form->error($model,'number'); ?>
    </div>

	
	<label for="OrderQueue_booking_time" style="background-color:#F2DEDE">请问您什么时间出发呢？(20分钟到达)</label>
    <div class="input-prepend input-append">
		<span class="add-on">出发时间</span>
		<?php echo $form->textField($model,'booking_time'); ?>
		<?php echo $form->error($model,'booking_time'); ?>
	</div>

	<label for="OrderQueue_comments" style="background-color:#F2DEDE">订单备注</label>
	<?php echo $form->textArea($model,'comments',array('row'=>50,'tabindex'=>6,'autocomplete'=>'off','class'=>'span12')); ?>
	<?php echo $form->error($model,'comments'); ?>

	<?php echo $form->hiddenField($model,'agent_id'); ?>
	<?php echo $form->hiddenField($model,'callid'); ?>

	<label for="OrderQueue_booking_time" style="background-color:#F2DEDE">马上给您安排司机，稍后司机会和您联系，谢谢您使用e代驾！再见！</label>
	<div>
	<?php echo CHtml::submitButton('提交订单', array ('class'=>'btn btn-success span4','tabindex'=>6,'style'=>'margin:0 auto'));?>
	</div>			

	<?php $this->endWidget(); ?>

</div><!-- form --> 