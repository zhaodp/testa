<?php
/* @var $this AdminAppController */
/* @var $model AdminApp */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'admin-app-form',
        'enableAjaxValidation'=>false,
    )); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->labelEx($model,'name'); ?>
    <?php echo $form->textField($model,'name'); ?>

    <?php echo $form->labelEx($model,'desc'); ?>
    <?php echo $form->textarea($model,'desc'); ?>

    <?php echo $form->labelEx($model,'url'); ?>
    <?php echo $form->textField($model,'url'); ?>

    <?php echo $form->labelEx($model,'status'); ?>
    <?php echo $form->dropDownList($model,'status',AdminApp::getStatus()); ?>
    <?php echo $form->error($model,'status'); ?>

    <div class="buttons">
        <?php echo CHtml::submitButton('保存',array('class'=>'btn btn-large')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div>
