<?php

$this->pageTitle='用户管理-交易流水';
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
    $('.search-form').toggle();
    return false;
});
$('.search-form form').submit(function(){
    $('#customer-trans-grid').yiiGridView('update', {
        data: $(this).serialize()
    });
    return false;
});
");
?>
<?php $this->renderPartial('user/user_nav'); ?>

<div class="row-fluid">
    <?php $this->renderPartial('user/_search_trans',array(
        'model'=>$model,
    )); ?>
    <h1><?php
        $cz=!empty($statistics['cz'])?$statistics['cz']:0;
        $xf=!empty($statistics['xf'])?$statistics['xf']:0;
        $bc=!empty($statistics['bc'])?$statistics['bc']:0;
        echo "客户账户充值".$cz."元，消费".$xf."元，赔偿".$bc."元"; ?></h1>
</div><!-- search-form -->


<div class="row-fluid">
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'customer-trans-grid',
        'dataProvider' => $dataProvider,
        'itemsCssClass' => 'table table-striped',
        'columns' => array(
            array(
                'name' => '时间',
                'value' => '$data->create_time'
            ),
            array(
                'name' => '客户手机号',
                'value' => 'BCustomers::model()->getUserPhoneById($data->user_id)'
            ),
            array(
                'name' => '交易类型',
                'value' => 'CarCustomerTrans::$trans_type[$data->trans_type]'
            ),
            array(
                'name' => '交易金额',
                'value' => '$data->amount'
            ),

            array(
                'name' => '当前余额',
                'value' => '$data->balance'
            ),
            array(
                'name' => 'VIP卡号',
                'value' => 'empty($data->trans_card) ? "" : $data->trans_card'
            ),
            array(
                'name' => '订单号',
                'type'=>'raw',
                'value' => '($data->trans_type==CarCustomerTrans::TRANS_TYPE_F||$data->trans_type==CarCustomerTrans::TRANS_TYPE_FV)? CHtml::link($data->trans_order_id,Yii::app()->createUrl("/order/view",array("id"=>$data->trans_order_id)), array("target" => "_blank")):$data->trans_order_id'
            ),
            array(
                'name' => '备注',
                'value' => '$data->remark'
            ),
        ),
    )); ?>
</div>
