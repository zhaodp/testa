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
            <label>vip手机</label>
            <?php echo CHtml::textField('phone',isset($model['phone'])?$model['phone']:''); ?>
        </div>

        <div class="span3">
            <label>交易类型</label>
            <?php 
		$magic_number=10000007;
	       	echo CHtml::dropDownList('type',!empty($model['type'])?$model['type']:$magic_number,array_merge(VipTrade::$trans_type,
array($magic_number=>'全部'))); 
	    ?>
        </div>

        <div class="span3">
            <label>开始时间</label>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'start_time',
                'value' => date('Y-m-d H:i:s',$model['start_time']),
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
                'value' =>  date('Y-m-d H:i:s',$model['end_time']),
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
            <?php echo CHtml::textField('order_id',isset($model['order_id'])?$model['order_id']:'');  ?>
        </div>

        <div class="span3">
            <label>VIP类型</label>
            <?php
                echo CHtml::dropDownList('vip_type',!empty($model['vip_type'])?$model['vip_type']:0,array(0=>'普通vip',1=>'定额卡',2=>'补偿卡'));
            ?>
        </div>

        <div class="span3">
            <label>&nbsp;</label>
            <?php echo CHtml::submitButton('Search', array('class' => 'btn')); ?>
        </div>


    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->
