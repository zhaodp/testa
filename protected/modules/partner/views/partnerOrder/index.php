<div class="container">
    <legend>订单</legend>
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    ));
    ?>
    <div class="row-fluid">
        <div class="span3">
            <label>订单城市</label>
            <?php echo CHtml::dropDownList('order[city_id]', $param['city_id'], Dict::items('city'));?>
        </div>
        <div class="span3">
            <label>订单状态</label>
            <?php
            echo CHtml::dropDownList('order[status]', $param['status'], array(
                'null' => '全部',
                '0' => '未报单的订单',
                '1' => '完成报单的订单',
                '2' => '销单待审核',
                '3' => '已销单',
                '4' => '销单审核不通过',
            ));
            ?>
        </div>
        <div class="span3">
            <label>客户电话</label>
            <?php echo CHtml::textField('order[contact_phone]', $param['contact_phone'] ? $param['contact_phone'] : '');?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span3">
            <label>呼叫开始时间</label>
            <?php
                Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                $this->widget('CJuiDateTimePicker', array(
                    'name' => 'order[call_time_start]',
                    //'model' => $model, //Model object
                    'value' => $param['call_time_start'],
                    'mode' => 'date', //use "time","date" or "datetime" (default)
                    'options' => array(
                        'dateFormat' => 'yy-mm-dd',
                        'minDate'=>'new Date(<?php echo date("Y,m,d", strtotime("-2 months"));?>)',
                        'changeMonth'=> true,
                    ), // jquery plugin options
                    'language' => 'zh',
                    'htmlOptions' => array('class' => "span10")
                ));
            ?>
        </div>
        <div class="span3">
            <label>呼叫结束时间</label>
            <?php
                $this->widget('CJuiDateTimePicker', array(
                    'name' => 'order[call_time_end]',
                    //'model' => $model, //Model object
                    'value' => $param['call_time_end'],
                    'mode' => 'date', //use "time","date" or "datetime" (default)
                    'options' => array(
                        'dateFormat' => 'yy-mm-dd',
                        'maxDate' => 'new Date()',
                        'changeMonth'=> true,
                    ), // jquery plugin options
                    'language' => 'zh',
                    'htmlOptions' => array('class' => "span10")
                ));
                ?>
        </div>
        <div class="span3">
            <label>&nbsp;</label>
            <?php echo CHtml::submitButton('搜索', array('class' => 'btn btn-success span6')); ?>
        </div>
    </div>
    <?php $this->endWidget(); ?>

    <div class="alert alert-info">
        <div class="row-fluid">
            <div class="span12"><?php echo date('Y年m月d日', strtotime($param['call_time_start'])).'~'.date('Y年m月d日', strtotime($param['call_time_end']));?></div>
        </div>
        <div class="row-fluid">
            <div class="span3">司机报单：<?php echo intval($complete);?></div>
            <div class="span3">司机销单：<?php echo intval($cancel);?></div>
            <div class="span3">提交订单：<?php echo intval($total);?></div>
        </div>
    </div>

    <?php
    $this->renderPartial('order_list', array('data'=>$data, 'price_visible'=>$price_visible));
    ?>
</div>