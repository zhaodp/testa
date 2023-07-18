<?php
/* @var $this CustomerBonusReportController */
/* @var $data CustomerBonusReport */
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

	<b><?php echo CHtml::encode($data->getAttributeLabel('bonus_sn')); ?>:</b>
	<?php echo CHtml::encode($data->bonus_sn); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('bonus_count')); ?>:</b>
	<?php echo CHtml::encode($data->bonus_count); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('used_count')); ?>:</b>
	<?php echo CHtml::encode($data->used_count); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('amount')); ?>:</b>
	<?php echo CHtml::encode($data->amount); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('report_time')); ?>:</b>
	<?php echo CHtml::encode($data->report_time); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created')); ?>:</b>
	<?php echo CHtml::encode($data->created); ?>
	<br />

	*/ ?>

</div>