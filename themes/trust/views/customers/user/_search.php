<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 14-1-9
 * Time: 下午10:32
 * auther mengtianxue
 */
?>

<div class="well span12">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    )); ?>
    <div class="span12">
        <div class="span3">
            <?php echo $form->label($model, 'name'); ?>
            <?php echo $form->textField($model, 'name', array('size' => 50, 'maxlength' => 50)); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, 'phone'); ?>
            <?php echo $form->textField($model, 'phone', array('size' => 32, 'maxlength' => 32)); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, 'city_id'); ?>
            <?php echo $form->dropDownList($model, 'city_id', Dict::items('city')); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, 'gender'); ?>
            <?php echo $form->dropDownList($model, 'gender', Dict::items('gender')); ?>
        </div>

    </div>

    <div class="span12">

        <div class="span3">
            <?php echo $form->label($model, 'email'); ?>
            <?php echo $form->textField($model, 'email', array('size' => 50, 'maxlength' => 50)); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, '&nbsp;'); ?>
            <?php echo CHtml::submitButton('Search', array('class' => 'btn')); ?>
        </div>

    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->