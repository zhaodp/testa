
<?php
//    print_r($data);die;
    $order = $data['order'];
    //输出的时间点信息
    $times = array();
    //订单执行到哪一步
    $step_tmp = '{{step}}';
    //是否补单
    $isResubmit = FALSE;
    $sourceStr = '';
    if(isset($order['source'])){
        $isResubmit = in_array($order['source'], Order::$callcenter_sources)
	    || in_array($order['source'], Order::$callcenter_input_sources);
        $c = $order['source'];
        if(!empty($c)){
            $sourceStr = Order::SourceToString($c);
        }
    }
    //下单时间
    if(isset($data['create']) && !empty($data['create'])){
        $d = $data['create'];
        $time = $d['created'];
        $operater = $d['phone'];
        $channel = Dict::item('order_channel', $order['channel']);
        $operation = $step_tmp.' '.$d['description'].' '.($channel ? '('.$channel.')' : '');
        $otherInfo = isset($data['dispatch']['agent_id']) ? '接单人：'.$data['dispatch']['agent_id'] : '';
        $times[] = array('time'=>$time, 'operater'=>$operater, 'operation'=>$operation, 'step'=>empty($sourceStr) ? '客户下单' : $sourceStr, 'otherInfo'=>$otherInfo);
    }
    //补单不显示预约和接单
    if(!$isResubmit){
        
        //派单时间
    //    if(isset($data['dispatch']) && !empty($data['dispatch'])){
    //        $d = $data['dispatch'];
    //        $time = strtotime($d['dispatch_time']);
    //        $operater = $d['dispatch_agent'] ? $d['dispatch_agent'] : '系统';
    //        $operation = $step_tmp;
    //        $otherInfo = isset($data['dispatch']['dispatch_agent']) ? '派单人：'.$data['dispatch']['dispatch_agent'] : '';
    //        $times[] = array('time'=>$time, 'operater'=>$operater, 'operation'=>$operation, 'step'=>'派单', 'otherInfo'=>$otherInfo);
    //    }
        //预约时间
        if(isset($data['create']) && !empty($data['create'])){
            $d = $data['create'];
            $time = $d['created'];
            if($time != $d['booking_time']){
                $otherInfo = isset($data['dispatch']['dispatch_agent']) ? '派单人：'.$data['dispatch']['dispatch_agent'] : '';
                $times[] = array('time'=>$d['booking_time'], 'operater'=>$operater, 'operation'=>$step_tmp, 'step'=>'预约时间', 'otherInfo'=>$otherInfo);
            }
        }
        //接单时间
        if(isset($data['recevingOrder']) && !empty($data['recevingOrder'])){
            $d = $data['recevingOrder'];
            $time = strtotime($d['log_time']);
            $operater = '司机';
            $operation = $step_tmp;
            $otherInfo = '司机工号：'.  $is_partner ? $order['driver_id'] : CHtml::link($order['driver_id'],array('driver/archives', 'id'=>$order['driver_id']), array('target'=>'_blank'));
            $otherInfo .= '<br>接单地点：'.$d['street'];
            $times[] = array('time'=>$time, 'operater'=>$operater, 'operation'=>$operation, 'step'=>'司机接单', 'otherInfo'=>$otherInfo);
        }
    }
    //到达时间
    if(isset($data['arrivePlace']) && !empty($data['arrivePlace'])){
        $d = $data['arrivePlace'];
        $time = strtotime($d['log_time']);
        $operater = '司机';
        $operation = $step_tmp.' 客户指定位置';
        $otherInfo = '位置：'.$d['street'];
        $useTime = 0;
        if(!empty($data['recevingOrder']['log_time'])){
            $useTime = (int)(($time - strtotime($data['recevingOrder']['log_time']))/60);
        }
//        if($useTime<=0 && !empty($data['create']['created'])){
//            $useTime = (int)(($time - $data['create']['created'])/60);
//        }
        $useTime = $useTime > 0 ? $useTime : 0;
//        $otherInfo .= '<br>到达用时：'.$useTime.'分钟';
        $otherInfo .= ($useTime > 0) ? '<br>到达用时：'.$useTime.'分钟' : '';//当400手动派单没有司机接单动作的时候不显示司机到达用时
        $times[] = array('time'=>$time, 'operater'=>$operater, 'operation'=>$operation, 'step'=>'司机到达', 'otherInfo'=>$otherInfo);
    }
    //开车时间
    if(isset($data['drivingStart']) && !empty($data['drivingStart'])){
        $d = $data['drivingStart'];
        $time = strtotime($d['log_time']);
        $operater = '司机';
        $operation = $step_tmp;
        $useTime = 0;
        $waitTimeBeforeDriving = !empty($d['wait_time_before_driving']) ? $d['wait_time_before_driving'] : 0;
        $midWaitTime = !empty($d['mid_wait_time']) ? $d['mid_wait_time'] : 0;
        if(!empty($data['drivingEnd']['log_time'])){
            $useTime = (int)((strtotime($data['drivingEnd']['log_time']) - $time)/60);
        }
        if(!$useTime && !empty($data['submitOrder']['log_time'])){
            $useTime = (int)((strtotime($data['submitOrder']['log_time']) - $time)/60);
        }
        $useTime = $useTime > 0 ? $useTime : 0;
        $otherInfo = '总耗时：'.$useTime.'分钟  总里程：'.$order['distance'].'公里 总费用：'.$order['income'].'元';
        $otherInfo .= $waitTimeBeforeDriving ? '<br>开车前等候时间：'.$waitTimeBeforeDriving.'分钟' : '';
        $otherInfo .= $midWaitTime ? '<br>中途等候时间：'.$midWaitTime.'分钟' : '';
        $times[] = array('time'=>$time, 'operater'=>$operater, 'operation'=>$operation, 'step'=>'司机开车', 'otherInfo'=>$otherInfo);
    }
    //结束服务时间
    if(isset($data['drivingEnd']) && !empty($data['drivingEnd'])){
        $d = $data['drivingEnd'];
        $time = strtotime($d['log_time']);
        $operater = '司机';
        $operation = $step_tmp;
        $times[] = array('time'=>$time, 'operater'=>$operater, 'operation'=>$operation, 'step'=>'结束服务');
    }
    //报单时间
    if(isset($data['submitOrder']) && !empty($data['submitOrder'])){
        $d = $data['submitOrder'];
        $time = strtotime($d['log_time']);
        $operater = '司机';
        $operation = $step_tmp;
//        $otherInfo = '报单金额：'.$order['income'].'元&nbsp;&nbsp;&nbsp;实收金额：'.$order['price'].'元';
	    $format = '订单总金额:%s元,里程费:%s元,现金:%s元,远程订单补贴:%s元,小费:%s元,';
	    $otherInfo = sprintf($format, $order_money['total_money'], $order_money['income_money'],
		                        $order_money['price_money'], $order_money['subsidy_money'], $order_money['tip_money']);
        $times[] = array('time'=>$time, 'operater'=>$operater, 'operation'=>$operation, 'step'=>'司机报单', 'otherInfo'=>$otherInfo);
    }
    //销单时间
    if(isset($data['cancelOrder']) && !empty($data['cancelOrder'])){
        $d = $data['cancelOrder'];
        $time = strtotime($d['created']);
        $operater = '';
        $operation = $step_tmp;
        $orderProcess = array(
            OrderProcess::PROCESS_SYS_CANCEL => '系统取消',
            OrderProcess::PROCESS_USER_CANCEL => '客户取消',
            OrderProcess::PROCESS_USER_DESTROY => '客户销单',
            OrderProcess::PROCESS_DRIVER_DESTROY => '司机销单',
        );
        $otherInfo = isset($orderProcess[$d['state']]) ? $orderProcess[$d['state']] : '';
        $cancel_str = '';
        $cancel_type = $order['cancel_type'];
        if (0 != $cancel_type) {
            $cancel_type = Common::convertCancelType($cancel_type); //转换老销单原因为新的id
            $cancel_str .= Dict::item('qx_o_type', $cancel_type);
        }
        $cancel_str .= (isset($order['cancel_desc']) && $order['cancel_desc']) ? '<br>'.$order['cancel_desc'] : '';
        $otherInfo .= '<br>销单原因：' . $cancel_str;
        $times[] = array('time'=>$time, 'operater'=>$operater, 'operation'=>$operation, 'step'=>'销单', 'otherInfo'=>$otherInfo);
    }
?>
<style>
    .time_point {width:15px;height:14px;margin-left: -9px;float: left;top:0px;background:url('<?php echo SP_URL_IMG; ?>time-line/icon07.gif') no-repeat;}
    .time_item {line-height: 18px;padding-left:15px;}
</style>
<div class="span11">
    <div class="span12 row_fluid" style="float:left;">
    <div></div>
    <?php $count = count($times); ?>
    <?php
        $prePath = array('司机开车','结束服务','司机报单','销单');
    ?>
    <?php foreach($times as $key => $item){ ?>
        <?php if(isset($item['step']) && in_array($item['step'], $prePath)){ ?>
            <?php !isset($showPath) ? $showPath = 1 : null; ?>
            <?php $path = $this->widget('application.widgets.order.OrderPathWidget', array(
                'orderId' => $order['order_id'],
                'htmlOptions' => array(
                    'style'=>'height:450px;padding-left: 20px;padding-bottom: 20px;',
                ),
            ),TRUE);?>
        <?php } ?>
        <?php $step = '<strong style="color:red;">'.(isset($item['step']) ? $item['step'] : '').'</strong>'; ?>
        <div class="span12" style="margin-left:0px;<?php echo $key == $count-1 ? 'border-left:2px solid white;' : 'border-left:2px solid #00B83F;'; ?>">
            
            <span class="time_point"></span>
            <div class="time_item span11">
                <div class="span3">
                <font class="muted"><?php echo date('Y-m-d',$item['time']); ?></font>
                <?php echo date('H:i',$item['time']); ?>
                </div>
                <div class="span9" style="padding-bottom:20px;">
                    <p><?php echo str_replace($step_tmp, $step, $item['operation']); ?><br></p>
                    <p>
                        <?php echo isset($item['otherInfo']) ? $item['otherInfo'] : ''; ?>
                        <?php
                            if (!$is_partner && isset($showPath) && $showPath == 1 && empty($path)) {
                                echo '<br>没有订单轨迹信息<a target="_blank" href="'
                                     . Yii::app()->createUrl('driver/orderposition', 
                                           array('order_id' => $order->order_id, 
                                                 'driver_id' => $order->driver_id,
                                                 'startDate' => date('Y-m-d H:i:s', $order['created']),
                                                 'endDate' => date('Y-m-d H:i:s', $order['end_time'])))
                                    . '" class="btn">查看司机轨迹</a>';
                            }
                        ?>
                    </p>
                </div>
            </div>
            <?php if(isset($showPath) && $showPath == 1 && !empty($path)){ ?>
                <div class="span12">
                    <?php echo $path; ?>
                </div>
            <?php } ?>
            <?php isset($showPath) ? $showPath = 0 : ''; ?>
        </div>
    <?php } ?>
    </div>
</div>
