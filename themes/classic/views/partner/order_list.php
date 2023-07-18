<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-10-16
 * Time: 下午12:46
 * To change this template use File | Settings | File Templates.
 */

$this->pageTitle = Partner::model()->getPartnerName($channel).'订单详情';

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

<?php

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'mydialog',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'订单信息',
        'autoOpen'=>false,
        'width'=>'750',
        'height'=>'450',
        'modal'=>true,
        'buttons'=>array(
            'OK'=>'js:function(){dialogClose($("#OrderLog_order_id").val(), $("#OrderLog_status").val(), $("#OrderLog_description").val())}',
            'Close'=>'js:function(){$("#mydialog").dialog("close");}'
        ),
    ),
));
echo '<div id="dialogdiv"></div>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
    'id'=>'view_driver_dialog',
    'options'=>array (
        'title'=>'查看司机信息',
        'autoOpen'=>false,
        'width'=>'780',
        'height'=>'580',
        'modal'=>true,
        'buttons'=>array (
            '关闭'=>'js:function(){$("#view_driver_dialog").dialog("close");}'))));
echo '<div id="view_driver_dialog"></div>';
echo '<iframe id="view_driver_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
    'id'=>'view_customer_dialog',
    'options'=>array (
        'title'=>'查看VIP信息',
        'autoOpen'=>false,
        'width'=>'780',
        'height'=>'580',
        'modal'=>true,
        'buttons'=>array (
            '关闭'=>'js:function(){$("#view_customer_dialog").dialog("close");}'))));
echo '<div id="view_customer_dialog"></div>';
echo '<iframe id="view_customer_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
    'id'=>'view_booking_dialog',
    'options'=>array (
        'title'=>'查看预约信息',
        'autoOpen'=>false,
        'width'=>'950',
        'height'=>'580',
        'modal'=>true,
        'buttons'=>array (
            '关闭'=>'js:function(){$("#view_booking_dialog").dialog("close");}'))));
echo '<div id="view_booking_dialog"></div>';
echo '<iframe id="view_booking_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

$click_view = <<<EOD
function(){
	$("#view_driver_frame").attr("src",$(this).attr("href"));
	$("#view_driver_dialog").dialog("open");
	return false;
}
EOD;
?>
<div class="search-form" style="display:block; margin-top: 5px;">
    <?php $this->renderPartial('_search_list',array(
        'model'=>$model,
        'callCenterUserType' => $callCenterUserType,
        'channel' => $channel,
        'call_time' => $call_time,
        'booking_time' => $booking_time,
    ));?>
</div>
<?php
Yii::import('application.controllers.OrderController');
$order = new OrderController(1);
$this->widget('zii.widgets.grid.CGridView', array (
    'id'=>'order-grid',
    'dataProvider'=>$dataProvider,
    'cssFile'=>SP_URL_CSS . 'table.css',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass'=>'table table-condensed',
    'rowCssClassExpression'=>array($order,'orderStatus'),
    'htmlOptions'=>array('class'=>'row-fluid'),
    'columns'=>array (
        array (
            'name'=>'订单编号',
            'headerHtmlOptions'=>array (
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($order,'orderIdAndNumber')
        ),
        array (
            'name'=>'司机信息',
            'headerHtmlOptions'=>array (
                'width'=>'80px'
            ),
            'type'=>'raw',
            'value'=>array($order,'adminDriverInfo'),
        ),
        array (
            'name'=>'客户信息',
            'headerHtmlOptions'=>array (
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($order,'orderPhone')
        ),
        array (
            'name'=>'订单时间',
            'headerHtmlOptions'=>array (
                'style'=>'width:120px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($order,'orderTime')
        ),
        array (
            'name'=>'起始地点',
            'headerHtmlOptions'=>array (
                'style'=>'width:120px',
                'nowrap'=>'nowrap'
            ),
            'type' => 'raw',
            'value' => array($order,'OrderAddr')
        ),
        array (
            'name'=>'收费',
            'headerHtmlOptions'=>array (
                'style'=>'width:120px',
                'nowrap'=>'nowrap'
            ),
            'type' => 'raw',
            'value' => array($order, 'orderFee')
        ),

       /* array (
            'name'=>'description',
            'headerHtmlOptions'=>array (
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'($data->source == "0") ? "客户呼叫" : (($data->source == 1) ? CHtml::link("呼叫中心" , "javascript:void(0);" , array("onclick" => "getBookingInfo($data->order_id)")) : (($data->source == 2) ? "客户呼叫补单" : (($data->source == 3) ? "呼叫中心补单" : ""))) '

        ),
        array (
            'header'=>'司机状态',
            'headerHtmlOptions'=>array (
                'width'=>'62px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'driverState')
        ),*/
        array (
            'header'=>'销单原因',
            'headerHtmlOptions'=>array (
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($order,'orderCancel')
        ),
        array (
            'header'=>'状态',
            'headerHtmlOptions'=>array (
                'width'=>'40px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($order,'confirmOrderCacnel')
        ),
        array (
            'name'=>'操作',
            'headerHtmlOptions'=>array (
                'width'=>'50px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($order,'operateOrder')
            //'value'=>'($data->status == ORDER::ORDER_COMFIRM) ? CHtml::link("确认", "javascript:void(0);", array("onclick"=>"{dialogConfirmInit(\'$data->order_id\', 3);}")) . "<br/>" . CHtml::link("拒绝", "javascript:void(0);", array("onclick"=>"{dialogConfirmInit(\'$data->order_id\', 4);}")) : ""'
        ),
    )
));
?>

<script>
    function orderDialogdivInit(orderId){
        $('#dialogdiv').html("<img src='<?php echo SP_URL_IMG;?>loading.gif' />");
        $.ajax({
            'url':'<?php echo Yii::app()->createUrl('/order/view');?>',
            'data':'id='+orderId,
            'type':'get',
            'success':function(data){
                $('#dialogdiv').html(data);
            },
            'cache':false
        });
        jQuery("#mydialog").dialog("open");
        return false;
    }

    function driverDialogdivInit(src){
        $("#view_driver_frame").attr("src",src);
        $("#view_driver_dialog").dialog("open");
        return false;
    }

    function dialogConfirmInit(id, status){
        if (status == <?php echo Order::ORDER_CANCEL;?>){
            if(!confirm('确认该订单的可以销单？')) return false;
            $.ajax({
                'url':'<?php echo Yii::app()->createUrl('/order/confirm');?>',
                'data':{'id':id},
                'type':'get',
                'success':function(data){
                    $.fn.yiiGridView.update('order-grid');
                },
                'cache':false
            });
        } else {
            $.ajax({
                'url':'<?php echo Yii::app()->createUrl('/order/rejectReason');?>',
                'data':{'id':id, 'status':status},
                'type':'get',
                'success':function(data){
                    $('#dialogdiv').html(data);
                },
                'cache':false
            });
            $("#mydialog").dialog("open");
            return false;
        }
    }
    function dialogClose(id, status, do_reason){
        if (id == '0' && status == '0') {
            $("#mydialog").dialog("close");
            return false;
        }
        if (status == <?php echo Order::ORDER_NOT_COMFIRM;?>
            || status == <?php echo Order::ORDER_CANCEL;?>
            || status == <?php echo Order::ORDER_READY;?>
            || status == <?php echo Order::ORDER_COMFIRM;?>){
            if (do_reason == '') {
                alert ("请填写原因。");
                return false;
            } else {
                $.ajax({
                    'url':'<?php echo Yii::app()->createUrl('/order/doreject');?>',
                    'data':{'id':id, 'status':status, 'description':do_reason},
                    'type':'get',
                    'success':function(data){
                        $.fn.yiiGridView.update('order-grid');
                    },
                    'cache':false
                });
                $("#mydialog").dialog("close");
                return false;
            }
        } else {
            $("#mydialog").dialog("close");
            return false;
        }
    }


    function getCustomerInfo(phone) {
        var src='<?php echo Yii::app()->createUrl('/customer/info');?>'+'&phone='+phone+'&dialog=1';
        $("#view_customer_frame").attr("src",src);
        $("#view_customer_dialog").dialog("open");
        return false;
    }

    function getBookingInfo(order_id) {
        var src='<?php echo Yii::app()->createUrl('/order/queue');?>'+'&order_id='+order_id+'&dialog=1';
        $("#view_booking_frame").attr("src",src);
        $("#view_booking_dialog").dialog("open");
    }
</script>