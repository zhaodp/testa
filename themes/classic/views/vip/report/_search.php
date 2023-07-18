<?php
/* @var $this VipCostMonthController */
/* @var $model VipCostMonth */
/* @var $form CActiveForm */
/* @author liuxiaobo */
/* @since 2014-1-7 */
?>

<div class="well form span12">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    ));
    ?>

    <style>
        .ui-datepicker-calendar { 
            display: none; 
        } 
    </style>
    <div class="row span3">
        <?php echo $form->label($model, 'month', array('label' => '开始月份')); ?>
        <?php
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'model' => $model,
            'attribute' => 'month',
            'language' => 'zh-CN',
            // additional javascript options for the date picker plugin
            'options' => array(
                'showAnim' => 'slideDown',
                'showButtonPanel' => TRUE,
                'changeMonth' => TRUE,
                'changeYear' => TRUE,
                'dateFormat' => 'yy-mm',
                'onClose' => 'js:function(dateText, inst) { 
                    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).datepicker("setDate", new Date(year, month, 1));
                }',
            ),
        ));
        ?>
    </div>

    <div class="row span3">
        <?php echo $form->label($model, 'end_month', array('label' => '结束月份')); ?>
        <?php
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'model' => $model,
            'attribute' => 'end_month',
            'language' => 'zh-CN',
            // additional javascript options for the date picker plugin
            'options' => array(
                'showAnim' => 'slideDown',
                'showButtonPanel' => TRUE,
                'changeMonth' => TRUE,
                'changeYear' => TRUE,
                'dateFormat' => 'yy-mm',
                'onClose' => 'js:function(dateText, inst) { 
                    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).datepicker("setDate", new Date(year, month, 1));
                }',
            ),
        ));
        ?>
    </div>

    <div class="row span3">
        <?php echo $form->label($model, 'city_id', array('label' => '城市')); ?>
        <?php echo $form->dropDownList($model, 'city_id', Dict::items('city')); ?>
    </div>

    <div class="row span1 buttons">
        <br>
        <?php echo CHtml::submitButton('查询', array('class' => 'btn')); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->