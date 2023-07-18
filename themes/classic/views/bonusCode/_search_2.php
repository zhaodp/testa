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
        <input type="hidden" name="user" value="<?php echo $user; ?>">
        <div class="row span2">开始时间
            
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'coupons[start_time]',
                
                'value' => !empty($search_time['start_time'])?$search_time['start_time']:Date("Y-m-01"),
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

        <div class="row span2">结束时间
            
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'coupons[end_time]',
                
                'value' => !empty($search_time['end_time'])?$search_time['end_time']:Date("Y-m-d"),
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

        
        <div class="row span2"><br/>
            
            <?php echo CHtml::submitButton('Search', array('class' => 'btn')); ?>
        </div>
    </div>



    <?php $this->endWidget(); ?>

</div><!-- search-form -->
