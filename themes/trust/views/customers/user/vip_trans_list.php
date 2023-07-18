<?php

$this->pageTitle='vip管理-交易流水';
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
<?php /*$this->renderPartial('user/user_nav');*/ ?>

<div class="row-fluid">
    <?php $this->renderPartial('user/_vip_search_trans',array(
        'model'=>$model,
    )); ?>
    <h1><?php
        echo "这段时间内vip账户总计 ||";
	foreach(VipTrade::$trans_type as $key=>$type){
		echo $type;
		echo " ";
		echo $statistics[$key]." 元||     ";
	}
    ?></h1>
    <h1><?php
	echo "这段时间内 ||vip直充vip有 ".$statistics['type_income']." 人|| ";
	echo "充值卡客户充值vip有 ".$statistics['type_card_income']." 人|| ";
	echo "app充值vip有 ".$statistics['type_pay']." 人|| ";
	echo "消费的vip用户有 ".$statistics['consume_cnt']." 人";
    ?></h1>
</div><!-- search-form -->


<div class="row-fluid">
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'customer-trans-grid',
        'dataProvider' => $dataProvider,
        'itemsCssClass' => 'table table-striped',
        'columns' => array(
            array(
                'name' => '时间',
                'value' => 'date("Y-m-d H:i:s",$data->created)'
            ),
            array(
                'name' => '客户手机号',
                'value' =>'Vip::model()->getPhoneByCard($data->vipcard)'
            ),
            array(
                'name' => '交易类型',
                'value' => 'VipTrade::$trans_type[$data->type]'
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
                'value' => '$data->vipcard'
            ),
            array(
                'name' => '订单号',
                'type'=>'raw',
                'value' => ' ($data->type==VipTrade::TYPE_ORDER||$data->type==VipTrade::TYPE_SUBSIDY)? CHtml::link($data->order_id,Yii::app()->createUrl("/order/view",array("id"=>$data->order_id)), array("target" => "_blank")):$data->order_id'
            ),
            array(
                'name' => '备注',
                'value' => '$data->comment'
            ),
        ),
    )); ?>
</div>
