<?php
/* @var $this CustomerComplainController */
/* @var $model CustomerComplain */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'customer-complain-index-form',
        'enableAjaxValidation'=>false,
    )); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'name'); ?>
        <?php echo $form->textField($model,'name'); ?>
        <?php echo $form->error($model,'name'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'phone'); ?>
        <?php echo $form->textField($model,'phone'); ?>
        <?php echo $form->error($model,'phone'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'driver_id'); ?>
        <?php echo $form->textField($model,'driver_id'); ?>
        <?php echo $form->error($model,'driver_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'driver_phone'); ?>
        <?php echo $form->textField($model,'driver_phone'); ?>
        <?php echo $form->error($model,'driver_phone'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'order_id'); ?>
        <?php echo $form->textField($model,'order_id'); ?>
        <?php echo $form->error($model,'order_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'service_time'); ?>
        <?php echo $form->textField($model,'service_time'); ?>
        <?php echo $form->error($model,'service_time'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'create_time'); ?>
        <?php echo $form->textField($model,'create_time'); ?>
        <?php echo $form->error($model,'create_time'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'operator'); ?>
        <?php echo $form->textField($model,'operator'); ?>
        <?php echo $form->error($model,'operator'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'complain_type'); ?>
        <?php echo $form->textField($model,'complain_type'); ?>
        <?php echo $form->error($model,'complain_type'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'source'); ?>
        <?php echo $form->textField($model,'source'); ?>
        <?php echo $form->error($model,'source'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'status'); ?>
        <?php echo $form->textField($model,'status'); ?>
        <?php echo $form->error($model,'status'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'cs_process'); ?>
        <?php echo $form->textField($model,'cs_process'); ?>
        <?php echo $form->error($model,'cs_process'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'sp_process'); ?>
        <?php echo $form->textField($model,'sp_process'); ?>
        <?php echo $form->error($model,'sp_process'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'dm_process'); ?>
        <?php echo $form->textField($model,'dm_process'); ?>
        <?php echo $form->error($model,'dm_process'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'finance_process'); ?>
        <?php echo $form->textField($model,'finance_process'); ?>
        <?php echo $form->error($model,'finance_process'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'customer_phone'); ?>
        <?php echo $form->textField($model,'customer_phone'); ?>
        <?php echo $form->error($model,'customer_phone'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'detail'); ?>
        <?php echo $form->textField($model,'detail'); ?>
        <?php echo $form->error($model,'detail'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'update_time'); ?>
        <?php echo $form->textField($model,'update_time'); ?>
        <?php echo $form->error($model,'update_time'); ?>
    </div>


    <div class="row buttons">
        <?php echo CHtml::submitButton('Submit'); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form --> 