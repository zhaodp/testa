<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-10-16
 * Time: 下午1:20
 * To change this template use File | Settings | File Templates.
 */
?>
<div class="well span12">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    )); ?>
    <input type="hidden" name="Order_page" value="1"/>

    <?php if ($callCenterUserType == 1) { ?>
        <div class="row span12">
            <div class="span3">
                <label>呼叫开始时间</label>
                <?php
                Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                $this->widget('CJuiDateTimePicker', array(
                    'name' => 'Order[call_time]',
                    'model' => $model, //Model object
                    'value' => $call_time,
                    'mode' => 'datetime', //use "time","date" or "datetime" (default)
                    'options' => array(
                        'dateFormat' => 'yy-mm-dd'
                    ), // jquery plugin options
                    'language' => 'zh',
                    'htmlOptions' => array('class' => "span12")
                ));
                ?>
            </div>
            <div class="span3">
                <label>呼叫结束时间</label>
                <?php
                $this->widget('CJuiDateTimePicker', array(
                    'name' => 'Order[booking_time]',
                    'model' => $model, //Model object
                    'value' => $booking_time,
                    'mode' => 'datetime', //use "time","date" or "datetime" (default)
                    'options' => array(
                        'dateFormat' => 'yy-mm-dd'
                    ), // jquery plugin options
                    'language' => 'zh',
                    'htmlOptions' => array('class' => "span12")
                ));
                ?>
            </div>
            <div class="span3">
                <label><?php echo $form->label($model, 'channel'); ?></label>
                <?php
                $partner = Partner::model()->getPartnerList();
                $partner = array("" => "全部") + $partner;
                echo $form->dropDownList($model,
                    'channel',
                    $partner,
                    array('class' => "span12")
                ); ?>
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


    <?php } ?>
    <div class="row span12">
        <?php echo CHtml::submitButton('搜索', array('class' => 'btn span2')); ?>
    </div>
    <?php $this->endWidget(); ?>

</div>