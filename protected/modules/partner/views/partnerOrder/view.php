<?php
if(isset($order) && !empty($order)){
	$data =  $order;
}else{
    die();
}
$this->pageTitle = '订单详情 - '.$this->pageTitle;
?>

<!--订单摘要信息 start-->
<div class="row_fluid">
    <div class="box box_border span12">
        <h4>订单基本信息</h4>
        <table class="table">
            <tr>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>订单单号</strong><br><?php echo CHtml::encode($data->order_number); ?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>订单流水号</strong><br><?php echo CHtml::encode($data->order_id); ?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>客户电话</strong><br><?php echo preg_replace("/(1\d{1,2})\d\d(\d{0,3})/", "\$1*****\$3", $data->phone); ?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>司机工号</strong><br><?php echo CHtml::encode($data->driver_id);//echo CHtml::link($data->driver_id,array('driver/archives', 'id'=>$data->driver_id), array('target'=>'_blank')); ?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>订单来源</strong><br><?php echo CHtml::encode($data->description); ?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>呼叫时间</strong><br><?php echo date('Y-m-d H:i',$data->call_time); ?>
                </td>
            </tr>
            <tr>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>订单里程</strong><br><?php echo CHtml::encode($data->distance);?>公里
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>等候时间</strong><br><?php echo !empty($order_ext->wait_time) ? $order_ext->wait_time : 0;?>分钟
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>报单金额</strong><br><?php echo $data->income; ?>元
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>开始地点</strong><br><?php echo CHtml::encode($data->location_start);?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>结束地点</strong><br><?php echo CHtml::encode($data->location_end);?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>订单状态</strong><br><?php echo OrderController::confirmOrderCacnel($data);?>
                </td>
            </tr>
            <tr>
                <td style="border-bottom:1px solid #DDD;" colspan="6">
                    <strong>备注</strong><br><?php echo isset($order_ext->mark) ? CHtml::encode($order_ext->mark) : '';?>
                </td>
            </tr>
        </table>
    </div>
</div>
<!--订单摘要信息 end-->
<div class="row_fluid">
    <!--时间轴 start-->
    <div class="box box_border span8">
        <h4>订单时间轴</h4>
        <table class="table">
            <tr>
                <td style="border:0">
                    <?php
                    $this->widget('application.widgets.order.OrderTimeLineWidget', array('orderId' => $data->order_id, 'is_partner'=>true));
                    ?>
                </td>
            </tr>
        </table>
    </div>
    <!--时间轴 end-->
    <div class="span4">
        <!--客户基本信息 start-->
        <div class="box box_border span12" style="float:right;">
            <h4>客户基本信息</h4>
            <table class="table">
                <tr>
                    <td style="border:0">
                        <strong>客户电话</strong>：<?php echo preg_replace("/(1\d{1,2})\d\d(\d{0,3})/", "\$1*****\$3", $data->phone);//Common::parseCustomerPhone($data->phone); ?><br>
                        <strong>客户名称</strong>：<?php echo CHtml::encode($data->name);?><br>
                        <strong>VIP卡号</strong>：<?php echo CHtml::encode($data->vipcard);?><br>
                    </td>
                </tr>
            </table>
        </div>
        <!--客户基本信息 end-->
        <!--订单操作记录 start-->
        <div class="box box_border span12" style="float:right;">
            <h4 style="padding-right: 10px;">
                订单操作记录
                <?php echo CHtml::ajaxLink(
                        '展开',
                        array('partnerOrder/view','id'=>$data->order_id,'part'=>'order_log'),
                        array('update'=>'#order_info_order_log'),
                        array('style'=>'float:right;','onclick'=>'js:if($("#order_info_order_log").text()){$("#order_info_order_log .tr").toggle();return false;}')
                ); ?>
            </h4>
            <table class="table" id="order_info_order_log"></table>
        </div>
        <!--订单操作记录 end-->
        <!--优惠劵信息 start-->
        <div class="box box_border span12" style="float:right;">
            <h4 style="padding-right: 10px;">
                优惠劵信息
                <?php echo CHtml::ajaxLink(
                        '展开',
                        array('partnerOrder/view','id'=>$data->order_id,'part'=>'bonus'),
                        array('update'=>'#order_info_bonus'),
                        array('style'=>'float:right;','onclick'=>'js:if($("#order_info_bonus").text()){$("#order_info_bonus tr").toggle();return false;}')
                ); ?>
            </h4>
            <table class="table" id="order_info_bonus"></table>
        </div>
        <!--优惠劵信息 end-->
        <!--短信回评 start-->
        <div class="box box_border span12" style="float:right;">
            <h4 style="padding-right: 10px;">
                短信回评
                <?php echo CHtml::ajaxLink(
                        '展开',
                        array('partnerOrder/view','id'=>$data->order_id,'part'=>'comment_sms'),
                        array('update'=>'#order_info_comment_sms'),
                        array('style'=>'float:right;','onclick'=>'js:if($("#order_info_comment_sms").text()){$("#order_info_comment_sms tr").toggle();return false;}')
                ); ?>
            </h4>
            <table class="table" id="order_info_comment_sms"></table>
        </div>
        <!--短信回评 end-->
        <!--投诉信息 start-->
        <div class="box box_border span12" style="float:right;">
            <h4 style="padding-right: 10px;">
                投诉信息
                <?php echo CHtml::ajaxLink(
                        '展开',
                        array('partnerOrder/view','id'=>$data->order_id,'part'=>'complain'),
                        array('update'=>'#order_info_complain'),
                        array('style'=>'float:right;','onclick'=>'js:if($("#order_info_complain").text()){$("#order_info_complain tr").toggle();return false;}')
                ); ?>
            </h4>
            <table class="table" id="order_info_complain"></table>
        </div>
        <!--投诉信息 end-->
    </div>
</div>
