<?php
/* @var $this AdminActionController */
/* @var $model AdminActions */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'admin-specialauth-form',
        'enableAjaxValidation'=>false,
    )); ?>

    <?php echo $form->errorSummary($model); ?>


    <?php
    echo chtml::textField('name',$user_info->name,array('readonly'=>'true','disabled'=>'true'));
    echo chtml::hiddenField('AdminSpecialAuth[user_id]',$user_info->id); ?>

    <?php echo $form->labelEx($model,'driver_phone'); ?>
    <?php echo $form->dropDownList($model,'driver_phone',AdminSpecialAuth::getDriverPhoneStatus()); ?>
    <?php echo $form->error($model,'driver_phone'); ?>

    <?php echo $form->labelEx($model,'user_phone'); ?>
    <?php echo $form->dropDownList($model,'user_phone',AdminSpecialAuth::getUserPhoneStatus()); ?>
    <?php echo $form->error($model,'user_phone'); ?>

    <?php echo $form->labelEx($model,'bonus'); ?>
    <?php echo $form->dropDownList($model,'bonus',AdminSpecialAuth::getBonusStatus()); ?>
    <?php echo $form->error($model,'bonus'); ?>

    <div class="buttons">
        <?php echo CHtml::submitButton('保存',array('class'=>'btn btn-large')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div>