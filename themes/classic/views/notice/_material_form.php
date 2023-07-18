<?php
/* @var $this DriverTrainDataController */
/* @var $model DriverTrainData */
/* @var $form CActiveForm */
?>

<div class="form span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-train-data-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>150)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'city_id'); ?>
		<?php echo $form->dropDownList($model,'city_id', Dict::items('city')); ?>
		<?php echo $form->error($model,'city_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'content'); ?>
        <p>图片上传支持格式"jpg","bmp","gif","png"</p>
		<?php $this->widget('application.extensions.ckeditor.CKEditor', array(
			    'model'=>$model,
			    'attribute'=>'content',
			    'language'=>'zh-cn',
			    'editorTemplate'=>'public',
			    'options' => array(
			    	'height' => '300px',
					'filebrowserImageUploadUrl'=>'index.php?r=image/imgupload&type=img&base_path=notice',
					),   
				));
		?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? '创建' : '保存',array('class'=>'btn btn-large')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
    $('input[type="submit"]').click(function(){
        $('#driver-train-data-form').submit();
        $('input[type="submit"]').attr('disabled',true);
    });
</script>