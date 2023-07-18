<?php
/* @var $this RankMonListController */
/* @var $data RankMonList */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('driver_id')); ?>:</b>
	<?php echo CHtml::encode($data->driver_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('city_id')); ?>:</b>
	<?php echo CHtml::encode($data->city_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('order_count')); ?>:</b>
	<?php echo CHtml::encode($data->order_count); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('call_count')); ?>:</b>
	<?php echo CHtml::encode($data->call_count); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('work_day_count')); ?>:</b>
	<?php echo CHtml::encode($data->work_day_count); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('phone_count')); ?>:</b>
	<?php echo CHtml::encode($data->phone_count); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('income')); ?>:</b>
	<?php echo CHtml::encode($data->income); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created')); ?>:</b>
	<?php echo CHtml::encode($data->created); ?>
	<br />

	*/ ?>

</div>