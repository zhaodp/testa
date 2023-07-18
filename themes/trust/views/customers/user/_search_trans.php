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
            <label>用户手机</label>
            <?php echo CHtml::textField('phone',isset($model['phone'])?$model['phone']:''); ?>
        </div>

        <div class="span3">
            <label>交易类型</label>
            <?php echo CHtml::dropDownList('trans_type',isset($model['trans_type'])?$model['trans_type']:0,CarCustomerTrans::$trans_type); ?>
        </div>

        <div class="span3">
            <label>开始时间</label>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'start_time',
                'value' => $model['start_time'],
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
                'htmlOptions' => array('class' => "span9")
            ));
            ?>
        </div>
        <div class="span3">
            <label>结束时间</label>
            <?php  $this->widget('CJuiDateTimePicker', array(
                'name' => 'end_time',
                'value' =>  $model['end_time'],
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
                'htmlOptions' => array('class' => "span9")
            ));?>
        </div>
    </div>

    <div class="span12">

        <div class="span3">
            <label>订单号</label>
            <?php echo CHtml::textField('trans_order_id',isset($model['trans_order_id'])?$model['trans_order_id']:'');  ?>
        </div>
        <div class="span3">
            <label>&nbsp;</label>
            <?php echo CHtml::submitButton('Search', array('class' => 'btn')); ?>
        </div>

    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->