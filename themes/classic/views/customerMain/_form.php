<?php
/* @var $this CustomerMainController */
/* @var $model CustomerMain */
/* @var $form CActiveForm */
?>
<style type="text/css">

label {float: left; width: 90px;}

</style>
<div class="form">
<?php 
	if(Yii::app()->user->hasFlash('error')) {
		echo Yii::app()->user->getFlash('error');
	}
?>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'customer-main-form',
	'enableAjaxValidation'=>false,
)); ?>
	<!--
	<p class="note">Fields with <span class="required">*</span> are required.</p>
	-->
	<?php echo $form->errorSummary($model); ?>
	<?php if (!$model->isNewRecord) {?>
	<div class="row-fluid">
		<div class="span4">
		<?php echo $form->labelEx($model,'id'); ?>
		<?php echo $model->id; ?>
		</div>
	</div>
	
	<div class="row-fluid">
		<div class="span4">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php 
			$status_radio = $form->radioButtonList($model,'status',CustomerMain::$status_dict,array('template'=>'{input}{label}','separator'=>" "));
			$status_radio = str_replace("<label", "<span", $status_radio);
			$status_radio = str_replace("</label", "</span", $status_radio);
		?>
		<?php echo $status_radio; ?>
		<?php echo $form->error($model,'status'); ?>
		</div>
	</div>
	<?php } ?>
	<div class="row-fluid">
		<div class="span4">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'name'); ?>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span4">
		<?php echo $form->labelEx($model,'gender'); ?>
		<?php 
			$radio_list = $form->radioButtonList($model,'gender',CustomerMain::$gender_dict,array('template'=>'{input}{label}','separator'=>" "));
			$radio_list = str_replace("<label", "<span", $radio_list);
			$radio_list = str_replace("</label", "</span", $radio_list);
		?>
		<?php echo $radio_list; ?>
		<?php echo $form->error($model,'gender'); ?>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span4">
		<?php echo $form->labelEx($model,'birthday'); ?>
		<?php echo $form->textField($model,'birthday'); ?>
		<?php echo $form->error($model,'birthday'); ?>
		</div>
	</div>

	<div class="row-fluid">
		<div>
		<?php echo $form->labelEx($model,'phone'); ?>
		<?php echo $form->textField($model,'phone',array('size'=>32,'maxlength'=>32)); ?>
		<?php echo $form->error($model,'phone'); ?>
		</div>
	</div>
	
	<div class="row-fluid">
		<div class="span4">
		<?php echo $form->labelEx($model,'backup_phone'); ?>
		<?php echo $form->textField($model,'backup_phone',array('size'=>32,'maxlength'=>32)); ?>
		<?php echo $form->error($model,'backup_phone'); ?>
		</div>
	</div>
	
	<div class="row-fluid">
		<div>
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>32,'maxlength'=>32)); ?>
		<?php echo $form->error($model,'email'); ?>
		</div>
	</div>
	
	<div class="row-fluid">
		<div class="span4">
		<?php echo $form->labelEx($model,'bill_receive_mode'); ?>
		<?php 
			$receive_radio = $form->radioButtonList($model,'bill_receive_mode',CustomerMain::$bill_receive,array('template'=>'{input}{label}','separator'=>" "));
			$receive_radio = str_replace("<label", "<span", $receive_radio);
			$receive_radio = str_replace("</label", "</span", $receive_radio);
		?>
		<?php echo $receive_radio; ?>
		<?php echo $form->error($model,'bill_receive_mode'); ?>
		</div>
	</div>

	<div class="row-fluid">
		<div>
		<?php echo $form->labelEx($model,'city_id'); ?>
		<?php echo $form->dropDownList($model, 'city_id', Dict::items('city')); ?>
		</div>
	</div>
	
	<div class="row-fluid">
		<div>
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->dropDownList($model,'type',CustomerMain::$type_dict); ?>
		<?php echo $form->error($model,'type'); ?>
		</div>
	</div>
	
	<div class="row-fluid">
		<div class="span4">
		<?php echo $form->labelEx($model,'vip_card'); ?>
		<?php echo $form->textField($model,'vip_card',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'vip_card'); ?>
		</div>
	</div>
	
	<div class="row-fluid">
		<div class="span4">
		<?php echo $form->labelEx($model,'channel'); ?>
		<?php 
			$channel_list = CustomerMain::$channel_dict;
		?>
		<?php echo $form->dropDownList($model,'channel',$channel_list); ?>
		<?php echo $form->error($model,'channel'); ?>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span4">
		<?php echo $form->labelEx($model,'company'); ?>
		<?php echo $form->textField($model,'company',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'company'); ?>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span4">
		<?php echo $form->labelEx($model,'invoice_title'); ?>
		<?php echo $form->textField($model,'invoice_title',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'invoice_title'); ?>
		</div>
	</div>
	
	<div class="row-fluid">
		<div class="span4">
		<?php echo $form->labelEx($model,'invoice_remark'); ?>
		<?php echo $form->textField($model,'invoice_remark',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'invoice_remark'); ?>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span4">
		<?php echo $form->labelEx($model,'remark'); ?>
		<?php echo $form->textArea($model,'remark',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'remark'); ?>
		</div>
	</div>

	<div class="row-fluid buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? '添加' : '保存'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->