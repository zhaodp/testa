<?php
/* @var $this VipRecordController */
/* @var $model VipRecord */
/* @var $form CActiveForm */
?>

<div class="span12 form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'vip-record-form',
        'enableAjaxValidation' => false,
    ));
    ?>

        <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'mark_content'); ?>
        <?php echo $form->textArea($model, 'mark_content', array('rows' => 12, 'cols' => 300, 'class' => 'span10')); ?>
        <?php echo $form->error($model, 'mark_content'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? '创建' : '保存', array('class' => 'btn')); ?>
    </div>

<?php $this->endWidget();