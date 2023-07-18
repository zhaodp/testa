<?php
/* @var $this AdminusernewController */
/* @var $model Adminusernew */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'admin-dep-form',
        'enableAjaxValidation'=>false,
    )); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->labelEx($model,'name'); ?>
    <?php echo $form->textField($model,'name'); ?>
    <?php echo $form->error($model,'name'); ?>

    <?php echo $form->labelEx($model,'desc'); ?>
    <?php echo $form->textField($model,'desc'); ?>
    <?php echo $form->error($model,'desc'); ?>


    <?php echo $form->labelEx($model,'status'); ?>
    <?php echo $form->dropDownList($model,'status',AdminDepartment::getStatus()); ?>
    <?php echo $form->error($model,'status'); ?>


    <div class="buttons">
        <?php echo CHtml::submitButton('保存',array('class'=>'btn btn-large')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div>