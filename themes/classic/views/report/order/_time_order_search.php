<?php
/* @var $this BOrderTrendController */
/* @var $model BOrderTrend */
/* @var $form CActiveForm */
?>

<div class="well span12">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'time_order_form',
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'post',
)); ?>

    <div class="row span3">
        <?php echo $form->label($model,'day',array('label'=>'日期')); ?>
        <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'attribute' => 'day',
                'model' => $model, //Model object
                'value' => '',
                'mode' => 'date', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yymmdd'
                ), // jquery plugin options
                'language' => 'zh',
            ));
        ?>
    </div>

    <div class="row span3">
        <?php echo $form->label($model,'city_id',array('label'=>'城市')); ?>
        <?php
            $user_city_id = Yii::app()->user->city;

            if ($user_city_id != 0) {
                $city_list = array(
                    '城市' => array(
                        $user_city_id => Dict::item('city', $user_city_id)
                    )
                );
                $city_id = $user_city_id;
            } else {
                $city_id = $model->city_id;
                $city_list = CityTools::cityPinYinSort();
            }
            $this->widget("application.widgets.common.DropDownCity", array(
                'cityList' => $city_list,
                'name' => 'BOrderTrend[city_id]',
                'value' => $city_id,
                'type' => 'modal',
                'htmlOptions' => array(
                    'style' => 'width: 134px; cursor: pointer;',
                    'readonly' => 'readonly',
                )
            ));
        ?>
    </div>

    <style>
        .user-btn-drop-down {padding-bottom: 20px;margin-bottom: -15px;}
    </style>
    <div class="row buttons span4">
        <br>
        <?php echo CHtml::submitButton('查询', array('class'=>'btn', 'onclick'=>'$("#BOrderTrend_otherDay").val("")')); ?>
        <?php echo CHtml::button('添加对比', array('class'=>'btn btn-info '.(($model->otherDay != null) ? "user-btn-drop-down" : ""), 'onclick'=>'$("#other_day_row").toggle();$(this).toggleClass("user-btn-drop-down")')); ?>
    </div>

    <div class="row buttons span11  <?php echo ($model->otherDay == null) ? 'hide' : ''; ?>" id="other_day_row">
        <div style="width:700px;float: right;background-color: rgb(68,170,200);padding:20px 3px 3px;" class="text-center">
        <?php echo CHtml::button('对比昨天', array('class'=>'btn', 'onclick'=>'processOtherDay('.date('Ymd',strtotime('-1 day')).')')); ?>
        <?php echo CHtml::button('对比前天', array('class'=>'btn', 'onclick'=>'processOtherDay('.date('Ymd',strtotime('-2 day')).')')); ?>
        <?php echo CHtml::button('对比上周同日', array('class'=>'btn', 'onclick'=>'processOtherDay('.date('Ymd',strtotime('-1 week')).')')); ?>
        <?php echo CHtml::button('对比上月同日', array('class'=>'btn', 'onclick'=>'processOtherDay('.date('Ymd',strtotime('-1 month')).')')); ?>
        其他日期：<?php
            $this->widget('CJuiDateTimePicker', array(
                'attribute' => 'otherDay',
                'model' => $model, //Model object
                'value' => '',
                'mode' => 'date', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yymmdd'
                ), // jquery plugin options
                'language' => 'zh',
                'htmlOptions' => array('style'=>"width:115px;margin-bottom:0px;")
            ));
        ?>
        <?php echo CHtml::submitButton('对比', array('class'=>'btn')); ?>
        </div>
    </div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->
<script>
    function processOtherDay($otherDay){
        $('#BOrderTrend_otherDay').val($otherDay);
        $('#time_order_form').submit();
    }
</script>