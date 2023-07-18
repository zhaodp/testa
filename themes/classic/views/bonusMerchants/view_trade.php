<style>
    h4{
        height: 36px;
        line-height: 36px;
        text-indent: 12px;
        border: 1px solid rgb(212, 212, 212);
        border-radius:4px;
        background-color: rgb(250, 250, 250) !important;
        background-image: linear-gradient(to bottom, rgb(255, 255, 255), rgb(242, 242, 242));
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.067) !important;

    }
</style>
<h1>优惠劵商家状态</h1>

<div class="row-fluid">
    <div class="span12 well">
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'trade-form',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'method' => 'get',
            'errorMessageCssClass' => 'alert alert-error'
        )); ?>
        <div class="span3">
            <label for="start_time">开始时间</label>
            <?php
            $start_time = isset($data['start_time']) ? date('Y-m-d', $data['start_time']) : date('Y-m-d', strtotime("-1 month"));
            //$start_time = isset($data['start_time']) ? $data['start_time'] : date('Y-m-d',strtotime("-1 day"));
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'start_time',
                //		'model'=>$model,  //Model object
                'value' => $start_time,
                'mode' => 'date', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
            ));
            ?>
        </div>
        <div class="span3">
            <label for="end_time">结束时间</label>
            <?php
            $end_time = isset($data['end_time']) ? date('Y-m-d', $data['end_time']) : date('Y-m-d', time());
            //$end_time = isset($data['end_time']) ? $data['end_time'] : date('Y-m-d',time());
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'end_time',
                //		'model'=>$model,  //Model object
                'value' => $end_time,
                'mode' => 'date', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
            ));
            ?>
        </div>
        <div class="span3">
            <label>&nbsp;</label>
            <?php echo CHtml::submitButton('搜索', array('class' => 'btn')); ?>
            <?php 
		    $params=(isset($data['start_time'])?'&start_time='.$data['start_time']:'').
                            (isset($data['end_time'])?'&end_time='.$data['end_time']:'').'&id='.$_GET['id'];
                    echo CHtml::link('导出',Yii::app()->createUrl('/bonusMerchants/export'.$params),array('class' => 'btn-primary btn')).'&nbsp;';
	     ?>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div>

<h4>消费记录</h4>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'bonus-grids',
    'dataProvider' => $dataProviderBonus,
    'itemsCssClass' => 'table table-bordered table-striped',
    'columns' => array(
        array(
            'name' => '优惠劵名称',
            'value' => 'BonusCode::model()->findByPk($data->bonus_type_id)->name'
        ),
        array(
            'name' => '优惠码',
            'value' => '$data->bonus_sn'
        ),  
        array(  
            'name' => '金额',
            'value' => '$data->balance'
        ),
        array(
            'name' => '订单号', 
            'value' => '$data->order_id' 
        ),
	array(
            'name' => '客户电话',
            'value' => '$data->customer_phone'
        ),
	array(
            'name' => '消费时间',
            'value' => 'date("Y-m-d H:i:s",$data->used)'
        ),
	array(
            'name' => '司机工号',
            'value' => array($this,'getDriverCode')
        ),
	array(
            'name' => '消费金额',
            'value' => '$data->use_money'
        ),
	array(
            'name' => '报单时间',
            'value' => 'date("Y-m-d H:i:s",$data->used)'
        ),
	array(
            'name' => '订单路程',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap',
                'style' => 'width: 180px',
            ),
            'type' => 'raw',
            'value' => array($this,'getOrderLocation')
        ),
        array(
            'name' => '出发/到达时间',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap',
                 'style' => 'width: 140px',
            ),
            'type' => 'raw',
            'value' => '"预约：".Yii::app()->controller->getDriverId($data->order_id, "booking_time")."<br/>".
                        "出发：".Yii::app()->controller->getDriverId($data->order_id, "start_time")."<br/>".
                        "到达：".Yii::app()->controller->getDriverId($data->order_id, "end_time")'
        ),
    array(
            'name' => '收费明细',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap',
                'style' => 'width: 200px',
            ),
            'type' => 'raw',
            'value' => 'Order::model()->vipListPriceInfo($data->order_id)'
        ),
    ),
));
?>
