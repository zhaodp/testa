<?php
/* @var $this CustomerVisitBatchController */
/* @var $model CustomerVisitBatch */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'customer-visit-batch-form',
	'enableAjaxValidation'=>false,
)); ?>

<div class='grid-view'>
	<p class='note'></p>
	<?php echo $form->errorSummary($model); ?>

	<label>批次号</label>
	<?php echo $form->textField($model, 'batch',array('readonly'=>'true','value'=>$model->isNewRecord ? date('Ymd',time()) : $model->batch));?>
	
	<label>相关批次城市</label>
	<?php echo $form->dropDownList($model, 'city_id', Dict::items('city')); ?>
	
	<?php
		if ($model->isNewRecord != '1'){
	?>
	<label>是否调查完毕</label>
	<?php echo $form->dropDownList($model, 'type', array('0'=>'调查中','1'=>'调查完毕'))?>
	<?php 
		}
		 
	?>
	
	<label>批次说明</label>
	<?php echo $form->textField($model, 'comment');?>
	<div class="buttons">
	<?php echo CHtml::submitButton($model->isNewRecord ? '创建' : '保存',array('class'=>'btn-large')); ?>
	</div>
	<?php $this->endWidget(); ?>
</div>
</div><!-- form -->