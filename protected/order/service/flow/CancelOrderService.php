<?php

class CancelOrderService extends BaseFlowService
{
    private static $instance;

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function driverCancelOrder($params)
    {
        $order_id = isset($params['order_id']) ? $params['order_id'] : '';
        $order_number = isset($params['order_number']) ? $params['order_number'] : '';

        $cancel_type = isset($params['cancel_type']) ? $params['cancel_type'] : '';
        $log = isset($params['log']) ? $params['log'] : '';
        $content = isset($params['content']) ? $params['content'] : '';
        $isComplaint = isset($params['isComplaint']) ? $params['isComplaint'] : '';
        $name = isset($params['name']) ? $params['name'] : '';
        $phone = isset($params['phone']) ? $params['phone'] : '';
        $complaint_type = isset($params['complaint_type']) ? $params['complaint_type'] : '';
        $source = isset($params['source']) ? $params['source'] : '';
        $complaint = isset($params['complaint']) ? $params['complaint'] : '';

        //验证消单类型与内容
        if ($complaint_type == 99 && empty($content)) {
            return array('code' => 2 , 'message' => '请选择销单理由并填写销单原因');
        }

        if (empty($phone)) {
            return array('code' => 0 , 'message' => '成功!');
        }

        //验证token
        $token = isset($params['token']) ? trim($params['token']) : '';
        $driver = DriverStatusService::getInstance()->getByToken($token);
        if (!$driver) {
            return array('code' => 1,'message' => '请重新登录');
        }

        //$driver_info = DriverStatus::model()->get($driver->driver_id);
        //$driver_info->status = 0;


        $task = array(
                'class' => __CLASS__,
                'params' => array(
                    'cancel_type' => $cancel_type,
                    'log' => $log,
                    'content' => $content,
                    'order_id' => $order_id,
                    'order_number' => $order_number,
                    'isComplaint' => $isComplaint,
                    'driver_id' => $driver->driver_id,
                    'name' => $name,
                    'phone' => $phone,
                    'complaint_type' => $complaint_type,
                    'complaint' => $complaint,
                    'source' => $source,
                ),
            );
        //添加task队列
        if (OrderDriverMatchType::checkType($order_id) == OrderDriverMatchType::CHOOSE_DRIVER) {
            $task['method'] = 'chooseDriverCancelOrderJob';
        } else {
            $task['method'] = 'automaticDriverCancelOrderJob';
        }

        Queue::model()->putin($task, 'apporder');

        return array('code' => 0 , 'message' => '成功!');
    }

    public function automaticDriverCancelOrderJob($params)
    {
        //记录log
        EdjLog::info($params['order_id'].'|504司机取消|'.$params['driver_id'].'|begin', 'console');

        $order_id = isset($params['order_id']) ? $params['order_id'] : '';
        $order_number = isset($params['order_number']) ? $params['order_number'] : '';
        $order = Push::model()->getCancelOrder($order_id, $order_number);

        $params['cancel_type'] = Common::convertCancelType($params['cancel_type']);

        //订单不存在则生成一个取消订单
        if (empty($order)) {
            return true;
        }

        //订单不是该司机或订单状态不为0则直接返回
        if (strtoupper($order->driver_id) != strtoupper($params['driver_id']) || $order->status != Order::ORDER_READY) {
            return false;
        }

        $attr = array(
            'cancel_desc' => $params['log'],
            'cancel_type' => $params['cancel_type'],
            'status' => Order::ORDER_COMFIRM, );

        $cancelComplaintLog = array(
            'driver_id'    => $params['driver_id'],
            'phone'        => $params['phone'],
            'order_id'     => $order->order_id,
            'order_number' => $params['order_number'],
            'cid'          => Common::convertCancelType($params['cancel_type']),
            'cid_desc'     => $params['log'],
            'content'      => $params['content'],
            'isComplaint'  => $params['isComplaint'],
            'source'       => $params['source'],
        );

        if (Order::model()->updateByPk($order->order_id, $attr)) {
            //记录log
            EdjLog::info($params['order_id'].'|504司机取消|'.$params['driver_id'].'|订单取消成功|end', 'console');

            //取消优惠劵使用
            //未处理订单中取消订单 参数params中phone格式为138****8888
            $bonus_phone = $params['phone'];
            if ($bonus_phone === substr_replace($order->phone, '****', 3, 4)) {
                $bonus_phone = $order->phone;
            }
            BonusLibrary::model()->BonusUsed($bonus_phone, $order->order_id, 0, 2);
            if ($params['isComplaint'] == 1) {
                $complaintArr = array();
                $complaintArr['order_id'] = $order->order_id;
                $complaintArr['driver_user'] = $params['driver_id'];
                $complaintArr['customer_name'] = $params['name'];
                $complaintArr['city'] = $order->city_id;
                $complaintArr['customer_phone'] = $params['phone'];
                $complaintArr['order_type'] = Order::ORDER_COMFIRM;
                $complaintArr['complaint_type'] = Common::convertCancelType($params['complaint_type'], 1);
                $complaintArr['complaint_content'] = $params['complaint'];
                $complaintArr['driver_time'] = $order->start_time;//只记录出发时间
                $complaintArr['complaint_status'] = 0;
                DriverComplaint::model()->saveDriverComplaint($complaintArr);
            }
            //记录orderLog
            $log_attr = array(
                'order_id'    => $order->order_id,
                'description' => $params['log'],
                'operator'    => $params['driver_id'],
            );
            OrderLog::model()->insertLog($log_attr);

            //记录销单或投诉Log开始
            CancelComplaintLog::model()->insertLog($cancelComplaintLog);

            //更新redis BY AndyCong
            $cancel_type = $params['cancel_type'] == 0 ? '' : Dict::item('qx_o_type', $params['cancel_type']);

        // cancel_type 可能是投诉类型
            if (!$cancel_type) {
                $cancel_type = $params['cancel_type'] == 0 ? '' : Dict::item('ts_o_type', $params['cancel_type']);
            }

            CustomerApiOrder::model()->updateOrderRedisByOrderFlag(
                $order->order_id,
                OrderProcess::ORDER_PROCESS_DRIVER_DESTORY,
                $params['driver_id'],
                $cancel_type
            );

            OrderStatusChangedPublisher::addQueue(array(
                'orderId'   => $params['order_id'],
                'status'    => OrderProcess::ORDER_PROCESS_DRIVER_DESTORY,
                'message'   => '司机取消订单',
                'driverId'  => $params['driver_id'],
                'phone'     => $params['phone'],
            ));

            /*
             * 第三方合作，司机销单后操作
             * author : zhangtingyi 2013-11-04
             */
            $order_channel = Order::model()->getOrderChannel($order->order_id);
            if ($order_channel) {
                $partner = PartnerFactory::factory($order_channel);
                if ($partner instanceof AbstractPartner) {
                    if (method_exists($partner, 'cancelOrderHandler')) {
                        $partner->cancelOrderHandler($order->order_id);
                    }
                }
            }

            //记录log
            EdjLog::info($params['order_id'].'|504司机取消|'.$params['driver_id'].'|取消成功|end', 'console');

            return true;
        } else {
            //记录log
            EdjLog::warning($params['order_id'].'|504司机取消|'.$params['driver_id'].'|司机取消失败|end', 'console');

            return false;
        }
    }

    public function chooseDriverCancelOrderJob($params)
    {
        if (empty($params['order_id']) || empty($params['driver_id'])) {
            return false;
        }

    //验证订单是否已报单
    //order_id 是unique_order_id,即order_number
        $order = Push::model()->getCancelOrder('', $params['order_id']);
        if (!empty($order) && $order->status != Order::ORDER_READY) {
            //订单状态不是未报单,不能取消
            EdjLog::info('SingleOrder Cancel status Error|'
            .$order->status.'|'.$order->id);

            return false;
        }

        $result = DalOrder::model()->cancelOrder($params);

        //给客户发短信
        $phone = ROrder::model()->getOrder($params['order_id'], 'phone');
        $driver_id = ROrder::model()->getOrder($params['order_id'], 'driver_id');
        $order_channel = ROrder::model()->getOrder($params['order_id'], 'channel');
        if (!empty($phone) || !empty($driver_id)) {
            $temp = SmsTemplate::model()->getContentBySubject('dianping_cancel_order', array('$driver_id$' => $driver_id.'师傅', '昨天的e代驾司机' => ','));
            if (!empty($temp['content'])) {
                $message = $temp['content'];
                //判断订单是否是合作方订单，并且是否可以发短信给客户 author zhangtingyi
                $partner_common = new PartnerCommon();
                $is_forbid = $partner_common->checkForbidSmsByChannel($order_channel);
                if (!$is_forbid) {
                    Sms::SendForOrder($phone, $message);
                }
            }
        }

        return $result;
    }

    public function customerCancelOrder($params)
    {
        //接收并验证参数
        $token = isset($params['token']) ? trim($params['token']) : '';
        $booking_id = isset($params['booking_id']) ? trim($params['booking_id']) : '';
        $type = isset($params['type']) ? $params['type'] : CustomerApiOrder::CANCEL_QUEUE;
        $reason_code = isset($params['reason_code']) ? $params['reason_code'] : '';//取消原因 对应文本的数字 5.4.1版本添加 可以多选格式为101,102,103
        $reason_detail = isset($params['reason_detail']) ? $params['reason_detail'] : '';//取消具体原因客户填写 5.4.1版本添加
        EdjLog::info('----ORDER-Cancel: '.json_encode($params));
        if (empty($booking_id)) {
            return array('code' => 2 , 'data' => '' , 'message' => '参数有误');
        }

        //验证token
        $validate = CustomerService::service()->validateToken($token);
        if (!$validate) {
            return array('code' => 1 , 'data' => '' , 'message' => '验证失败');
        }

        //处理
        //取消redis
        $cancel_redis = CustomerApiOrder::model()->cancelQueueRedis($validate['phone'], $booking_id);
        if (!$cancel_redis && $cancel_redis['result'] ==  false) {
            return array('code' => 2 , 'data' => '' , 'message' => '司机处于服务中,该订单不能被取消');
        }

        switch ($type) {
            case CustomerApiOrder::CANCEL_QUEUE:
                $task = array(
                    'class' => __CLASS__,
                    'method' => 'apiQueueCancelJob',
                    'params' => array(
                        'phone' => $validate['phone'],
                        'booking_id' => $booking_id,
                    ),
                );
                break;
            case CustomerApiOrder::CANCEL_ORDER:
                $task = array(
                    'class' => __CLASS__,
                    'method' => 'apiOrderCancelJob',
                    'params' => array(
                        'phone' => $validate['phone'],
                        'booking_id' => $booking_id,
                        'reason_code' => $reason_code,
                        'reason_detail' => $reason_detail,
                    ),
                );
                break;
            default:
                $task = array(
                    'class' => __CLASS__,
                    'method' => 'apiQueueCancelJob',
                    'params' => array(
                        'phone' => $validate['phone'],
                        'booking_id' => $booking_id,
                    ),
                );
                break;
        }
        Queue::model()->putin($task, 'apporder');

        return array('code' => 0 , 'data' => array('booking_id' => $booking_id, 'order_ids' => $cancel_redis['order_ids']) , 'message' => '取消成功');
    }

    public function apiOrderCancelJob($params)
    {
        $cancel_queue = CustomerApiOrder::model()->cancelOrderQueue($params['phone'], $params['booking_id']);
        $cancel_order = CustomerApiOrder::model()->cancelOrders($params['phone'], $params['booking_id'], $params['reason_code'], $params['reason_detail']);

        return true;
    }

    public function apiQueueCancelJob($params)
    {
        $result = CustomerApiOrder::model()->cancelOrderQueue($params['phone'], $params['booking_id']);

        return $result;
    }
}
