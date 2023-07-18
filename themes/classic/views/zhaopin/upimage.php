<h1>上传司机头像</h1>

<div class="span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'), 
//	'action'=> Yii::app()->createUrl('driver/create')
));
?>
	<div class="row">
		<?php echo $form->labelEx($model,'picture'); ?>
		<?php echo CHtml::activeFileField($model,'picture'); ?> 
		<?php echo CHtml::image($model->attributes['picture'], $model->attributes['name'], array("width"=>120, "height"=>144)); ?>
		<?php echo $form->error($model,'picture'); ?>
	</div>
	<div class="row buttons">
	<br/>
		<?php echo CHtml::submitButton($model->isNewRecord ? '新建' : '保存',array('class'=>'btn btn-success span3')); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->