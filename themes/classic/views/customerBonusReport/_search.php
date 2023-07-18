<?php
/* @var $this CustomerBonusReportController */
/* @var $model CustomerBonusReport */
/* @var $form CActiveForm */
?>

<div class="wide form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    )); ?>
    <div class="row-fluid">
        <div class="row span2">
            <?php echo $form->label($model, 'city_id'); ?>
            <?php echo $form->dropDownList($model,'city_id',Dict::items('city'),array('class'=>'span12')); ?>
        </div>
        <div class="row span2">
            <?php echo $form->label($model, '开始时间'); ?>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'CustomerBonusReport[report_time]',
                'model' => $model, //Model object
                'value' => $params['report_time'],
                'mode' => 'date', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'htmlOptions' => array(
                    'class' => 'span12'
                ),
                'language' => 'zh'
            ));
            ?>
        </div>

        <div class="row span2">
            <?php echo $form->label($model, '结束时间'); ?>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'CustomerBonusReport[created]',
                'model' => $model, //Model object
                'value' => $params['created'],
                'mode' => 'date', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'htmlOptions' => array(
                    'class' => 'span12'
                ),
                'language' => 'zh'
            ));
            ?>
        </div>

        <div class="row span2">
            <?php echo $form->label($model, 'driver_id'); ?>
            <?php echo $form->textField($model, 'driver_id', array('value'=> $params['driver_id'],'class'=>'span12')); ?>
        </div>
        <div class="row span2">
            <?php echo $form->label($model, '&nbsp;'); ?>
            <?php echo CHtml::submitButton('Search', array('class' => 'btn')); ?>
        </div>
    </div>



    <?php $this->endWidget(); ?>

</div><!-- search-form -->