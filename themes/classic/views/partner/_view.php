<?php
/* @var $this PartnerController */
/* @var $data Partner */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('city')); ?>:</b>
	<?php echo CHtml::encode($data->city); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('contact')); ?>:</b>
	<?php echo CHtml::encode($data->contact); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('phone')); ?>:</b>
	<?php echo CHtml::encode($data->phone); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('channel_id')); ?>:</b>
	<?php echo CHtml::encode($data->channel_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('seat_number')); ?>:</b>
	<?php echo CHtml::encode($data->seat_number); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('address')); ?>:</b>
	<?php echo CHtml::encode($data->address); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('sms_call')); ?>:</b>
	<?php echo CHtml::encode($data->sms_call); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('pay_sort')); ?>:</b>
	<?php echo CHtml::encode($data->pay_sort); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('sharing_amount')); ?>:</b>
	<?php echo CHtml::encode($data->sharing_amount); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('vip_card')); ?>:</b>
	<?php echo CHtml::encode($data->vip_card); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('bonus_sn')); ?>:</b>
	<?php echo CHtml::encode($data->bonus_sn); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('bonus_quantity')); ?>:</b>
	<?php echo CHtml::encode($data->bonus_quantity); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($data->status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created')); ?>:</b>
	<?php echo CHtml::encode($data->created); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('updated')); ?>:</b>
	<?php echo CHtml::encode($data->updated); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ip')); ?>:</b>
	<?php echo CHtml::encode($data->ip); ?>
	<br />

	*/ ?>

</div>