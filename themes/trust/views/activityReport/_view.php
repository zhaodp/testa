<?php
/* @var $this ActivityReportController */
/* @var $data ActivityReport */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('order_id')); ?>:</b>
	<?php echo CHtml::encode($data->order_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('driver_id')); ?>:</b>
	<?php echo CHtml::encode($data->driver_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('driver_name')); ?>:</b>
	<?php echo CHtml::encode($data->driver_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('driver_phone')); ?>:</b>
	<?php echo CHtml::encode($data->driver_phone); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('phone')); ?>:</b>
	<?php echo CHtml::encode($data->phone); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('city_id')); ?>:</b>
	<?php echo CHtml::encode($data->city_id); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($data->status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('total_order')); ?>:</b>
	<?php echo CHtml::encode($data->total_order); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('complate_count')); ?>:</b>
	<?php echo CHtml::encode($data->complate_count); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('complate_p')); ?>:</b>
	<?php echo CHtml::encode($data->complate_p); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('complate_driver_b')); ?>:</b>
	<?php echo CHtml::encode($data->complate_driver_b); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('complate_customer_b')); ?>:</b>
	<?php echo CHtml::encode($data->complate_customer_b); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('order_account')); ?>:</b>
	<?php echo CHtml::encode($data->order_account); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('driver_account')); ?>:</b>
	<?php echo CHtml::encode($data->driver_account); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('company_subsidy')); ?>:</b>
	<?php echo CHtml::encode($data->company_subsidy); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('driver_subsidy')); ?>:</b>
	<?php echo CHtml::encode($data->driver_subsidy); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('customer_subsidy')); ?>:</b>
	<?php echo CHtml::encode($data->customer_subsidy); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('order_date')); ?>:</b>
	<?php echo CHtml::encode($data->order_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('day_date')); ?>:</b>
	<?php echo CHtml::encode($data->day_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('create_date')); ?>:</b>
	<?php echo CHtml::encode($data->create_date); ?>
	<br />

	*/ ?>

</div>