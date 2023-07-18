<?php
/**
 * Created by JetBrains PhpStorm.
 * author: mtx
 * Date: 13-7-9
 * Time: 上午7:31
 */
?>
<div class="well form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    )); ?>
    <div class="row-fluid">
        <div class="row span3">
            <?php echo $form->label($model, 'city_id'); ?>
            <?php echo $form->dropDownList($model, 'city_id', Dict::items('city')); ?>
        </div>

        <div class="row span3">
            <?php echo $form->label($model, 'driver_id'); ?>
            <?php echo $form->textField($model, 'driver_id', array('size' => 10, 'maxlength' => 10)); ?>
        </div>

        <div class="row span3">
            <?php echo $form->label($model, 'name'); ?>
            <?php echo $form->textField($model, 'name', array('size' => 20, 'maxlength' => 20)); ?>
        </div>

        <div class="row span3">
            <?php echo $form->label($model, 'balance'); ?>
            <?php echo $form->textField($model, 'balance', array('size' => 8, 'maxlength' => 8)); ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="row span3">
            <?php echo $form->label($model, '&nbsp;'); ?>
            <?php echo CHtml::submitButton('Search',array('class'=>'btn span9')); ?>
        </div>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->