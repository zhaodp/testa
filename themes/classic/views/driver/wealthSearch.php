<?php
/* @var $this score */
/* @var $model DriverWealthMonthStat */
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
            <?php echo CHtml::label('月份','stat_month');?>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array (
                'name'=>'stat_month',
                'value'=>$stat_month,
                'mode'=>'date',  //use "time","date" or "datetime" (default)
                'options'=>array (
                    'dateFormat'=>'yy-mm'
                ),
                'language'=>'zh',
                'htmlOptions'=>array(
                    'placeholder'=>"月份",
                ),


            ));?>
        </div>
        <div class="span3">
            <?php echo $form->label($model, '&nbsp;'); ?>
            <?php echo CHtml::submitButton('Search', array('class' => 'btn')); ?>
        </div>
    </div>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->