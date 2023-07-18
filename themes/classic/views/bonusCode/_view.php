<?php
/* @var $this BonusCodeController */
/* @var $data BonusCode */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('money')); ?>:</b>
	<?php echo CHtml::encode($data->money); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('channel')); ?>:</b>
	<?php echo CHtml::encode($data->channel); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('type')); ?>:</b>
	<?php echo CHtml::encode($data->type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('sn_type')); ?>:</b>
	<?php echo CHtml::encode($data->sn_type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('sn_start')); ?>:</b>
	<?php echo CHtml::encode($data->sn_start); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('sn_end')); ?>:</b>
	<?php echo CHtml::encode($data->sn_end); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('issued')); ?>:</b>
	<?php echo CHtml::encode($data->issued); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('user_limited')); ?>:</b>
	<?php echo CHtml::encode($data->user_limited); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('repeat_limited')); ?>:</b>
	<?php echo CHtml::encode($data->repeat_limited); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('channel_limited')); ?>:</b>
	<?php echo CHtml::encode($data->channel_limited); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('city_id')); ?>:</b>
	<?php echo CHtml::encode($data->city_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('effective_date')); ?>:</b>
	<?php echo CHtml::encode($data->effective_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('binding_deadline')); ?>:</b>
	<?php echo CHtml::encode($data->binding_deadline); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('end_date')); ?>:</b>
	<?php echo CHtml::encode($data->end_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('coupon_rules')); ?>:</b>
	<?php echo CHtml::encode($data->coupon_rules); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('sms')); ?>:</b>
	<?php echo CHtml::encode($data->sms); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('create_by')); ?>:</b>
	<?php echo CHtml::encode($data->create_by); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created')); ?>:</b>
	<?php echo CHtml::encode($data->created); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('update_by')); ?>:</b>
	<?php echo CHtml::encode($data->update_by); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('updated')); ?>:</b>
	<?php echo CHtml::encode($data->updated); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('remark')); ?>:</b>
	<?php echo CHtml::encode($data->remark); ?>
	<br />

	*/ ?>

</div>