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
            <?php echo $form->label($model, '交易订单号（订单号或交易流水号）'); ?>
            <?php echo $form->textField($model, 'trans_order_id'); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, '交易卡号(VIP卡号或优惠劵号码)'); ?>
            <?php echo $form->textField($model, 'trans_card', array('size' => 50, 'maxlength' => 50)); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, '交易类型'); ?>
            <?php echo $form->dropDownList($model, 'trans_type', CarCustomerTrans::$trans_type); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, '交易来源'); ?>
            <?php echo $form->dropDownList($model, 'source', CarCustomerTrans::$trans_source); ?>
        </div>
    </div>

    <div class="span12">
        <div class="span3">
            <?php echo $form->label($model, 'create_time'); ?>
            <?php echo $form->textField($model, 'create_time', array('size' => 50, 'maxlength' => 50)); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, '&nbsp;'); ?>
            <?php echo CHtml::submitButton('Search', array('class' => 'btn')); ?>
        </div>

    </div>
    <?php $this->endWidget(); ?>

</div><!-- search-form -->