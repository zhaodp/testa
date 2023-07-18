<?php
/* @var $this ActivityReportController */
/* @var $model ActivityReport */
/* @var $form CActiveForm */
?>

<div class="wide form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    )); ?>
    <div class="row-fluit">
        <div class="span3">
            <?php echo $form->label($model, 'city_id'); ?>
            <?php echo $form->DropDownList($model, 'city_id', Dict::items("city")); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, '&nbsp;'); ?>
            <?php echo CHtml::submitButton('Search', array('class' => 'btn')); ?>
        </div>
    </div>
    <?php $this->endWidget(); ?>

</div><!-- search-form -->