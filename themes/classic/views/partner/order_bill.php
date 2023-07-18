<?php
/**
 * Created by JetBrains PhpStorm.
 * User: daiyihui
 * Date: 13-10-16
 * Time: 下午12:46
 * To change this template use File | Settings | File Templates.
 */
$this->pageTitle = '账单明细';
$partnerInfo = Partner::model()->getPartnerName($channel, 4);

$now_month = idate('m') - 1;
$listMonth = array();
for($i = 1; $i <= $now_month; $i++){
    $listMonth[$i] = $i."月";
}
$orderStats = Order::model()->getPartnerOrderStats($channel, strtotime($call_time), strtotime($booking_time));
$arrayData = $orderStats->getData();

if($selectMonth){
    $month = $selectMonth;
}else{
    $month = idate('m') - 1;
}
?>
<div id="bill_stats">
<h1><?php echo $this->pageTitle;?></h1>

<form class="form-horizontal">
    <div class="control-group"">
        <?php echo CHtml::label('选择月份', 'Bill[listMonth]', array('class' => 'control-label', 'style' => 'width:70px;margin-right: 10px;'));?>
        <div class="controls" style="margin-left: 10px;">
            <?php echo CHtml::dropDownList('Bill[listMonth]', $month, $listMonth)?>
        </div>
    </div>
</form>
<?php
/*Yii::app()->clientScript->registerScript('search', "
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
*/?>
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

<div class="span12" style="margin-left: 0px;">
    <h4>基本信息</h4>
    <div class="row-fluid">
        <div class="span3">商家名称：<?php echo $partnerInfo['name']?></div>
        <div class="span3">联系人姓名：<?php echo $partnerInfo['contact']?></div>
        <div class="span3">联系电话：<?php echo $partnerInfo['phone']?></div>
    </div>
    <div class="row-fluid">
        <div class="span9">商家账单地址：<?php echo $partnerInfo['address']?></div>
    </div>
</div>
<div class="span12" style="margin-left: 0px;">
    <h4>结算信息</h4>
    <div class="row-fluid">
        <div class="span3"><?php echo $month?>月总报单：<?php
            echo $arrayData == '' ? 0 : (isset($arrayData[0]['count_complete']) ? $arrayData[0]['count_complete'] : 0);?> 单</div>
        <!--    VIP    -->
        <?php if($partnerInfo['pay_sort'] == Partner::PAY_SORT_VIP):?>
            <div class="span3"><?php echo $month?>月总消费：
            <?php
            echo $arrayData == '' ? 0 : (isset($arrayData[0]['count_fee']) ? $arrayData[0]['count_fee'] : 0);?> 元
            </div>
            <div class="span3">账户余额：<?php echo Partner::model()->getPartnerName($channel, 1)?> 元</div>
        <?php endif;?>
        <!--  优惠券  -->
        <?php if($partnerInfo['pay_sort'] == Partner::PAY_SORT_BONUS):?>
            <div class="span3"><?php echo $month?>月使用优惠劵数量：
            <?php
                $BonusInfo = CustomerBonus::model()->getBonusUsedSummary($partnerInfo['bonus_phone'], $partnerInfo['bonus_sn'],strtotime($call_time), strtotime($booking_time));
                echo $BonusInfo['used_num'] ? $BonusInfo['used_num'] : 0;
            ?>
            </div>
            <div class="span3">优惠劵余量：<?php $partnerCommon = new PartnerCommon(); echo $partnerCommon->getBonusSurplus($partnerInfo['id'])?></div>
        <?php endif;?>
        <!-- 报单分成 -->
        <?php if($partnerInfo['pay_sort'] == Partner::PAY_SORT_DIVIDED):?>
            <div class="span3">每单分成金额：<?php echo intval($partnerInfo['sharing_amount']);?>元</div>
            <div class="span3">共分成金额：
            <?php
            echo $arrayData == '' ? 0 : (isset($arrayData[0]['count_complete']) ? $arrayData[0]['count_complete'] * intval($partnerInfo['sharing_amount']) : 0);?> 元

            </div>
        <?php endif;?>
    </div>
</div>

<!--<div class="search-form" style="display:block; margin-top: 5px;">
    <?php /*$this->renderPartial('_search_stats',array(
        'model'=>$model,
        'callCenterUserType' => $callCenterUserType,
    ));*/?>
</div>-->
<div class="span12" style="margin-left: 0px;">
    <h4>结算明细</h4>
<?php
Yii::import('application.controllers.OrderController');
$order = new OrderController(1);
//VIP
if($partnerInfo['pay_sort'] == Partner::PAY_SORT_VIP){
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
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($order,'orderTime')
        ),
        array (
            'name'=>'起始地点',
            'headerHtmlOptions'=>array (
                'nowrap'=>'nowrap'
            ),
            'type' => 'raw',
            'value' => array($order,'OrderAddr')
        ),
        array (
            'name'=>'收费',
            'headerHtmlOptions'=>array (
                'nowrap'=>'nowrap'
            ),
            'type' => 'raw',
            'value' => array($order, 'orderFee')
        ),
    )
));
//    优惠券
}elseif($partnerInfo['pay_sort'] == Partner::PAY_SORT_BONUS){
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
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>array($order,'orderTime')
            ),
            array (
                'name'=>'起始地点',
                'headerHtmlOptions'=>array (
                    'nowrap'=>'nowrap'
                ),
                'type' => 'raw',
                'value' => array($order,'OrderAddr')
            ),
            array (
                'name'=>'收费',
                'headerHtmlOptions'=>array (
                    'nowrap'=>'nowrap'
                ),
                'type' => 'raw',
                'value' => array($order, 'orderFee')
            ),
            array (
                'header'=>'是否使用优惠劵',
                'headerHtmlOptions'=>array (
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>array($order, 'isUsedBonus')
            ),
        )
    ));
}else{//报单分成列表
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
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>array($order,'orderTime')
            ),
            array (
                'name'=>'起始地点',
                'headerHtmlOptions'=>array (
                    'nowrap'=>'nowrap'
                ),
                'type' => 'raw',
                'value' => array($order,'OrderAddr')
            ),
        )
    ));
}

?>
</div>
</div>
<script>
    $("#Bill_listMonth").change(function(){
        var myMonth = $("#Bill_listMonth option:selected").val();
        window.location.href = '<?php echo Yii::app()->request->hostInfo.Yii::app()->createUrl('/partner/orderBill', array('Bill'=>array('channel' => $channel)))?>'+'&Bill[listMonth]='+myMonth;
    });
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