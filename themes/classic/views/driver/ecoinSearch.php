<?php
/* @var $model driverExt */
/* @var $form CActiveForm */
?>

<div class="wide form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',

    )); ?>
    <div>
            <div class="row-fluid">

        <div class="span3">
            <?php echo CHtml::label('城市','city_id');?>
            <?php echo CHtml::dropDownList('city_id',$city_id, Dict::items('city')); ?>
        </div>
        <div class="span3">
            <?php echo $form->label($model, 'driver_id'); ?>
            <?php echo $form->textField($model, 'driver_id', array('size' => 10, 'maxlength' => 10)); ?>
        </div>
    </div>
        <div class="row-fluid">

        <div class="span3">
            <?php echo $form->label($model, '&nbsp;'); ?>
            <?php echo CHtml::submitButton('Search', array('class' => 'btn')); ?>
        </div>
    </div>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->