<?php
/* @var $this CityConfigController */
/* @var $model CityConfig */
/* @var $form CActiveForm */
?>

<div class="wide form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    )); ?>
    <div>
        <div class="span3">
            <?php echo $form->label($model, 'city_id'); ?>
            <?php echo $form->textField($model, 'city_id', array('size' => 10, 'maxlength' => 10)); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, 'city_name'); ?>
            <?php echo $form->textField($model, 'city_name', array('size' => 10, 'maxlength' => 10)); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, 'city_prifix'); ?>
            <?php echo $form->textField($model, 'city_prifix', array('size' => 10, 'maxlength' => 10)); ?>
        </div>
        <div class="span3">
            <?php echo $form->label($model, 'bonus_prifix'); ?>
            <?php echo $form->textField($model, 'bonus_prifix', array('size' => 10, 'maxlength' => 10)); ?>
        </div>
    </div>
    <div>
        <div class="span3">
            <?php echo $form->label($model, 'city_level'); ?>
            <?php echo $form->dropDownList($model, 'city_level', CityConfig::getCityLevel(), array('empty' => '全部')); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, 'status'); ?>
            <?php echo $form->dropDownList($model, 'status', CityConfig::getCityStatus(), array('empty' => '全部')); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, 'captital'); ?>
            <?php echo $form->dropDownList($model, 'captital', CityConfig::getCityCaptital(), array('empty' => '全部')); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, '&nbsp;'); ?>
            <?php echo CHtml::submitButton('Search', array('class' => 'btn'));
            if (AdminActions::model()->havepermission('cityConfig', 'create')) echo CHtml::link('创建', Yii::app()->createUrl('/cityConfig/create',array('back_url'=>Yii::app()->request->getUrl())), array('class' => 'btn btn-primary', 'style'=>'margin-left:90px')); ?>
        </div>
    </div>
    <?php $this->endWidget(); ?>

</div><!-- search-form -->