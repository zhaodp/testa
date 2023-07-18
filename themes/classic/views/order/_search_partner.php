<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-10-16
 * Time: 下午1:20
 * To change this template use File | Settings | File Templates.
 */
?>
<div class="span12">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route, array('Order' => array('channel' => $channel, 'call_time' => $call_time, 'booking_time' => $booking_time))),
        'method' => 'get',
    )); ?>
    <input type="hidden" name="Order_page" value="1"/>

    <?php if ($callCenterUserType == 1) { ?>
        <div class="row-fluid">
            <div class="span3">
                <label>订单城市</label>
                <?php echo $form->dropDownList($model,
                    'city_id',
                    Dict::items('city'),
                    array('class' => "")
                ); ?>
            </div>
            <div class="span3">
                <?php echo $form->label($model, '&nbsp;')?>
                <?php echo CHtml::submitButton('搜索', array('class' => 'btn span3')); ?>
            </div>
        </div>
    <?php } else { ?>
        <div class="row span12">
            <div class="span3">
                <label><?php echo $form->label($model, 'driver_id'); ?></label>
                <?php echo $form->textField($model, 'driver_id', array('size' => 10, 'maxlength' => 10, 'class' => "span12")); ?>
            </div>

            <div class="span3">
                <label><?php echo $form->label($model, 'phone'); ?></label>
                <?php echo $form->textField($model, 'phone', array('size' => 20, 'maxlength' => 20, 'class' => "span12")); ?>
            </div>

            <div class="span3">
                <label><?php echo $form->label($model, 'vipcard'); ?></label>
                <?php echo $form->textField($model, 'vipcard', array('size' => 20, 'maxlength' => 20, 'class' => "span12")); ?>
            </div>
        </div>

        <div class="row span12">
            <?php echo CHtml::submitButton('搜索', array('class' => 'btn span2')); ?>
        </div>
    <?php } ?>

    <?php $this->endWidget(); ?>

</div>