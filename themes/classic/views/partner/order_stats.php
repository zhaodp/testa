<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-10-16
 * Time: 下午12:46
 * To change this template use File | Settings | File Templates.
 */
$this->pageTitle = '订单统计';
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('order-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>


<div class="btn-group">
    <?php echo CHtml::link('当月', array("partner/orderStats", "Order"=>array("month"=>1)),array('class'=>"btn", 'id' => 'btn_now_month'));?>
    <?php echo CHtml::link('上月', array("partner/orderStats", "Order"=>array("last_month" => 2)),array('class'=>"btn", 'id' => 'btn_last_month'));?>
</div>

<div class="search-form" style="display:block; margin-top: 5px;">
    <?php $this->renderPartial('_search_stats',array(
        'model'=>$model,
        'callCenterUserType' => $callCenterUserType,
        'call_time' => $call_time,
        'booking_time' => $booking_time,
    ));?>
</div>

<?php
$this->widget('zii.widgets.grid.CGridView', array (
    'id'=>'order-grid',
    'dataProvider'=>$dataProvider,
    'cssFile'=>SP_URL_CSS . 'table.css',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass'=>'table table-condensed',
    //'rowCssClassExpression'=>array($this,'orderStatus'),
    'htmlOptions'=>array('class'=>'row-fluid'),
    'columns'=>array (
        array (
            'name'=>'合作商家',
            'headerHtmlOptions'=>array (
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=> 'Partner::model()->getPartnerName($data["channel"])',
        ),
        array (
            'name'=>'订单数',
            /*'headerHtmlOptions'=>array (
                'width'=>'80px'
            ),*/
            'type'=>'raw',
            'value' => '$data["total"]',
        ),
        array (
            'name'=>'报单数',
            'headerHtmlOptions'=>array (
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=> '$data["count_complete"]'
        ),
        array (
            'name'=>'销单数',
            'headerHtmlOptions'=>array (
                'style'=>'',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data["count_cancel"]'
        ),
        array (
            'name'=>'VIP余额',
            'headerHtmlOptions'=>array (
                'style'=>'',
                'nowrap'=>'nowrap'
            ),
            'type' => 'raw',
            'value' => 'Partner::model()->getPartnerName($data["channel"], 1)'
        ),
        array (
            'name'=>'优惠券余量',
            'headerHtmlOptions'=>array (
                'style'=>'',
                'nowrap'=>'nowrap'
            ),
            'type' => 'raw',
            'value' => 'Partner::model()->getPartnerName($data["channel"], 2)'
        ),
        array (
            'header'=>'结算金额',
            'headerHtmlOptions'=>array (
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'Partner::model()->getPartnerSharingTotal($data["channel"],$data["count_complete"])'
        ),
        array (
            'header' => '操作',
            'htmlOptions' => array(
                'style' => ''
            ),
            'class'=>'CButtonColumn',
            'template' => '{order}',
            'buttons' => array(
                'order' => array(
                    'label' => '订单明细',
                    'url' => 'Yii::app()->createUrl("partner/orderList", array("Order" =>array("channel"=>$data["channel"], "call_time" => "'.$call_time.'", "booking_time" => "'.$booking_time.'")))',
                    'options' => array(
                        'target' => '_blank',
                    ),
                ),
            )
        ),
    )
));
?>