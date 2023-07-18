<?php
/* @var $this DriverComplaintController */
/* @var $data DriverComplaint */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('order_id')); ?>:</b>
	<?php echo CHtml::encode($data->order_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('driver_user')); ?>:</b>
	<?php echo CHtml::encode($data->driver_user); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('customer_name')); ?>:</b>
	<?php echo CHtml::encode($data->customer_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('city')); ?>:</b>
	<?php echo CHtml::encode($data->city); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('customer_phone')); ?>:</b>
	<?php echo CHtml::encode($data->customer_phone); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('order_type')); ?>:</b>
	<?php echo CHtml::encode($data->order_type); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('complaint_type')); ?>:</b>
	<?php echo CHtml::encode($data->complaint_type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('complaint_content')); ?>:</b>
	<?php echo CHtml::encode($data->complaint_content); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('driver_time')); ?>:</b>
	<?php echo CHtml::encode($data->driver_time); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('complaint_status')); ?>:</b>
	<?php echo CHtml::encode($data->complaint_status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('create_time')); ?>:</b>
	<?php echo CHtml::encode($data->create_time); ?>
	<br />

	*/ ?>

</div>