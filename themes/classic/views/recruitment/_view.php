<?php
/* @var $this ZhaopinController */
/* @var $data DriverZhaopin */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('mobile')); ?>:</b>
	<?php echo CHtml::encode($data->mobile); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('city_id')); ?>:</b>
	<?php echo CHtml::encode($data->city_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('district_id')); ?>:</b>
	<?php echo CHtml::encode($data->district_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('work_type')); ?>:</b>
	<?php echo CHtml::encode($data->work_type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('gender')); ?>:</b>
	<?php echo CHtml::encode($data->gender); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('age')); ?>:</b>
	<?php echo CHtml::encode($data->age); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('id_card')); ?>:</b>
	<?php echo CHtml::encode($data->id_card); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('domicile')); ?>:</b>
	<?php echo CHtml::encode($data->domicile); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('assure')); ?>:</b>
	<?php echo CHtml::encode($data->assure); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('marry')); ?>:</b>
	<?php echo CHtml::encode($data->marry); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('political_status')); ?>:</b>
	<?php echo CHtml::encode($data->political_status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('edu')); ?>:</b>
	<?php echo CHtml::encode($data->edu); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('pro')); ?>:</b>
	<?php echo CHtml::encode($data->pro); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('driver_type')); ?>:</b>
	<?php echo CHtml::encode($data->driver_type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('driver_card')); ?>:</b>
	<?php echo CHtml::encode($data->driver_card); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('driver_year')); ?>:</b>
	<?php echo CHtml::encode($data->driver_year); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('driver_cars')); ?>:</b>
	<?php echo CHtml::encode($data->driver_cars); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('contact')); ?>:</b>
	<?php echo CHtml::encode($data->contact); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('contact_phone')); ?>:</b>
	<?php echo CHtml::encode($data->contact_phone); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('contact_relate')); ?>:</b>
	<?php echo CHtml::encode($data->contact_relate); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('experience')); ?>:</b>
	<?php echo CHtml::encode($data->experience); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($data->status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('recycle')); ?>:</b>
	<?php echo CHtml::encode($data->recycle); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('recycle_reason')); ?>:</b>
	<?php echo CHtml::encode($data->recycle_reason); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ip')); ?>:</b>
	<?php echo CHtml::encode($data->ip); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ttime')); ?>:</b>
	<?php echo CHtml::encode($data->ttime); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('etime')); ?>:</b>
	<?php echo CHtml::encode($data->etime); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('htime')); ?>:</b>
	<?php echo CHtml::encode($data->htime); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('rtime')); ?>:</b>
	<?php echo CHtml::encode($data->rtime); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ctime')); ?>:</b>
	<?php echo CHtml::encode($data->ctime); ?>
	<br />

	*/ ?>

</div>