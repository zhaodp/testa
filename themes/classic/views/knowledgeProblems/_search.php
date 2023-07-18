<?php
/* @var $this ProblemsCollectController */
/* @var $model ProblemsCollect */
/* @var $form CActiveForm */
?>

<div class="wide form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    )); ?>
    <div class="row-fluid">
        <div class="span3">
            <?php echo $form->label($model, 'title'); ?>
            <?php echo $form->textField($model, 'title', array('size' => 60, 'maxlength' => 100)); ?>
        </div>
        <div class="span3">
            <?php echo $form->label($model, 'driver_id'); ?>
            <?php echo $form->textField($model, 'driver_id', array('size' => 10, 'maxlength' => 10)); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, 'phone'); ?>
            <?php echo $form->textField($model, 'phone', array('size' => 20, 'maxlength' => 20)); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, 'status'); ?>
            <?php echo $form->dropDownList($model, 'status',array('' => '全部','0' => '未解决', '1' => '已解决')); ?>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span3">
            <?php echo $form->label($model, 'solve'); ?>
            <?php echo $form->textField($model, 'solve', array('size' => 20, 'maxlength' => 20)); ?>
        </div>

        <div class="span3 buttons">
            <?php echo $form->label($model, '&nbsp;'); ?>
            <?php echo CHtml::submitButton('Search', array('class' => 'btn')); ?>
        </div>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->