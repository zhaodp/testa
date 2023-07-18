<?php
$isDriver = Yii::app()->user->type == AdminUserNew::USER_TYPE_DRIVER;
if(isset($order) && !empty($order)){
    $data =  $order;
    if($isDriver && trim(strtolower($data->driver_id)) != trim(strtolower(Yii::app()->user->id))){
        die();
    }
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
                    <strong>客户电话</strong><br><?php echo Common::parseCustomerPhone($data->phone); ?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>司机工号</strong><br><?php echo $isDriver ? $data->driver_id : CHtml::link($data->driver_id,array('driver/archives', 'id'=>$data->driver_id), array('target'=>'_blank')); ?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <?php
                        $sourceStr = '';
                        $c = $data->source;
                        $sourceStr = Order::SourceToString($c);
                    ?>
                    <strong>订单来源</strong><br><?php echo CHtml::encode($sourceStr); ?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>订单状态</strong><br><font class="text-error"><?php echo OrderController::confirmOrderCacnel($data);?></font>
                </td>
            </tr>
            <tr>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>呼叫时间</strong><br><?php echo date('Y-m-d H:i',$data->call_time); ?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>预约时间</strong><br><?php echo date('Y-m-d H:i', $data->booking_time); ?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>开车时间</strong><br><?php echo date('Y-m-d H:i',$data->start_time); ?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>结束服务时间</strong><br><?php echo date('Y-m-d H:i',$data->end_time); ?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <?php $submit_time = isset($orderInfoExt['submit_time']) ? $orderInfoExt['submit_time'] : ''; ?>
                    <strong>报单时间</strong><br><?php echo !empty($submit_time) ? date('Y-m-d H:i',$submit_time) : ''; ?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>创建时间</strong><br><?php echo date('Y-m-d H:i', $data->created); ?>
                </td>
            </tr>

            <tr>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>订单里程</strong><br><?php echo CHtml::encode($data->distance);?>公里
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>到达等待时间</strong><br><?php echo (isset($order_ext_infos)) ? $order_ext_infos->wait_time - $order_ext_infos->stop_wait_time : 0;?>分钟
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>开车中等待时间</strong><br><?php echo  isset($order_ext_infos) ? $order_ext_infos->stop_wait_time : 0;?>分钟
                </td>
	            <td style="border-bottom:1px solid #DDD;width:16%;">
		            <strong>开始地点</strong><br><?php echo CHtml::encode($data->location_start);?>
	            </td>
	            <td style="border-bottom:1px solid #DDD;width:16%;">
		            <strong>结束地点</strong><br><?php echo CHtml::encode($data->location_end);?>
	            </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                </td>

            </tr>
            <tr>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>订单服务时间</strong><br><?php echo !empty($order_money['serve_time'])? $order_money['serve_time'] : 0; ?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                </td>
            </tr>

			<!--	 钱相关的展示区       -->
	        <tr>
		        <td style="border-bottom:1px solid #DDD;width:16%;">
			        <strong>订单总金额</strong><br><?php echo $order_money['total_money']; ?>元
		        </td>
		        <td style="border-bottom:1px solid #DDD;width:16%;">
			        <strong>订单里程费</strong><br><?php echo $order_money['income_money']; ?>元
		        </td>
		        <td style="border-bottom:1px solid #DDD;width:16%;">
			        <strong>实收现金</strong><br><?php echo $order_money['price_money']; ?>元
		        </td>
		        <td style="border-bottom:1px solid #DDD;width:16%;">
			        <strong>司机准时补贴</strong><br><?php echo isset($order_ext_infos->driver_subsidy_money) ? $order_ext_infos->driver_subsidy_money : '0'; ?>元
		        </td>
		        <td style="border-bottom:1px solid #DDD;width:16%;">
			        <strong>用户免单补贴</strong><br><?php echo isset( $order_ext_infos->customer_subsidy_money) ? $order_ext_infos->customer_subsidy_money : '0'; ?>元
		        </td>
		        <td style="border-bottom:1px solid #DDD;width:16%;">
			        <strong>远程订单补贴</strong><br><?php echo $order_money['subsidy_money']; ?>元
		        </td>
	        </tr>
	        <tr>
		        <td style="border-bottom:1px solid #DDD;width:16%;">
			        <strong>小费</strong><br><?php echo $order_money['tip_money']; ?>元
		        </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>日间业务服务费</strong><br><?php echo !empty($order_money['time_cost']) ? $order_money['time_cost'] : 0 ; ?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>日间业务夜间补贴</strong><br><?php echo !empty($order_money['subsidy']) ? $order_money['subsidy'] : 0 ; ?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>日间业务返程补贴</strong><br><?php echo !empty($order_money['subsidy_back']) ? $order_money['subsidy_back'] : 0; ?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                </td>
	        </tr>
	        <tr>
		        <td style="border-bottom:1px solid #DDD;width:16%;">
			        <strong>到达时间</strong><br><?php echo isset($order_ext_infos->driver_ready_time) ? $order_ext_infos->driver_ready_time : ''; ?>秒
		        </td>
		        <td style="border-bottom:1px solid #DDD;width:16%;">
			        <strong>到达距离</strong><br><?php echo isset($order_ext_infos->driver_ready_distance)  ? $order_ext_infos->driver_ready_distance : '0'; ?>公里
		        </td>
		        <td style="border-bottom:1px solid #DDD;width:16%;">
			        <strong>距预约地距离</strong><br><?php echo $driver_customer_dis; ?>米
		        </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                </td>
	        </tr>
            <tr>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>客人状态</strong><br>
                    <?php
                        $customerStatus = isset($order_extra['customer_status']) ? $order_extra['customer_status'] : 0;
                        echo $customerStatus == 0 ? '车主不在车上，代办事' : ' 车主在车上，正常代驾';
                    ?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>订单信息:</strong><br>
                    <?php echo isset($order_extra['order_detail']) ? $order_extra['order_detail'] : ''; ?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>车牌号</strong><br>
                    <?php echo isset($order_extra['car_number']) ? $order_extra['car_number'] : ''; ?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                    <strong>车辆型号</strong><br>
                    <?php echo isset($order_extra['car_type']) ? $order_extra['car_type'] : ''; ?>
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
                </td>
                <td style="border-bottom:1px solid #DDD;width:16%;">
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
        <h4>订单时间轴
            <?php echo AdminActions::model()->havepermission('order', 'deleteOrderPathCache') ? CHtml::ajaxButton(
                '信息有误？ 刷新一下',
                array('order/deleteOrderPathCache','orderId'=>$data->order_id),
                array('dataType'=>'json','success'=>'js:function(data){alert(data.msg);location.reload();}'),
                    array('style'=>'float:right;','class'=>'btn')
            ) : ''; ?>
        </h4>
        <table class="table">
            <tr>
                <td style="border:0">
                    <?php
                    $this->widget('application.widgets.order.OrderTimeLineWidget', array('orderId' => $data->order_id));
                    ?>
                </td>
            </tr>
        </table>
    </div>
    <!--时间轴 end-->
    <div class="span4">
        <!--客户基本信息 start-->
        <style>
            .vip {
                background-position: 0px -20px;
                background:url("<?php echo SP_URL_CSS; ?>../i/vip.gif") no-repeat scroll 0 0 transparent;
            }
        </style>
        <div class="box box_border span12" style="float:right;">
            <h4>客户基本信息</h4>
            <table class="table">
                <tr>
                    <td style="border:0">
                        <strong>客户电话</strong>：<span><?php echo ($data->vipcard ? ('<i class="vip icon-vip" title="VIP客户"></i>') : "").Common::parseCustomerPhone($data->phone); ?></span><br>
                        <strong>客户名称</strong>：<?php echo CHtml::encode($data->name);?><br>
                        <?php if(!$isDriver){   //司机页面不展示vip卡号 ?>
                        <strong>VIP卡号</strong>：<?php echo CHtml::encode($data->vipcard);?><br>
                        <?php } ?>
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
                        array('order/view','id'=>$data->order_id,'part'=>'order_log'),
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
                        array('order/view','id'=>$data->order_id,'part'=>'bonus'),
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
                        array('order/view','id'=>$data->order_id,'part'=>'comment_sms'),
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
                        array('order/view','id'=>$data->order_id,'part'=>'complain'),
                        array('update'=>'#order_info_complain'),
                        array('style'=>'float:right;','onclick'=>'js:if($("#order_info_complain").text()){$("#order_info_complain tr").toggle();return false;}')
                ); ?>
            </h4>
            <table class="table" id="order_info_complain"></table>
        </div>
        <!--投诉信息 end-->
    </div>
</div>
