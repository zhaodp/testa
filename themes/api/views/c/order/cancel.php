<?php

/**
 * 客户端API：c.order.cancel 取消订单接口.
 *
 * @param token
 * @param booking_id
 *
 * @return json
 *
 * @author AndyCong 2013-10-14
 *
 * @version 1.0
 */
if (Yii::app()->params['order_architecture_refactor_on']) {
    $result = CancelOrderService::getInstance()->customerCancelOrder($params);
    echo json_encode($result);

    return;
}

//接收并验证参数
$token = isset($params['token']) ? trim($params['token']) : '';
$booking_id = isset($params['booking_id']) ? trim($params['booking_id']) : '';
$type = isset($params['type']) ? $params['type'] : CustomerApiOrder::CANCEL_QUEUE;
$reason_code = isset($params['reason_code']) ? $params['reason_code'] : '';//取消原因 对应文本的数字 5.4.1版本添加 可以多选格式为101,102,103
$reason_detail = isset($params['reason_detail']) ? $params['reason_detail'] : '';//取消具体原因客户填写 5.4.1版本添加
EdjLog::info('----ORDER-Cancel: '.json_encode($params));
if (empty($booking_id)) {
    $ret = array('code' => 2 , 'data' => '' , 'message' => '参数有误');
    echo json_encode($ret);

    return;
}

//验证token
$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
    $ret = array('code' => 1 , 'data' => '' , 'message' => '验证失败');
    echo json_encode($ret);

    return;
}

//处理
//取消redis
$cancel_redis = CustomerApiOrder::model()->cancelQueueRedis($validate['phone'], $booking_id);
if (!$cancel_redis && $cancel_redis['result'] ==  false) {
    $ret = array('code' => 2 , 'data' => '' , 'message' => '司机处于服务中,该订单不能被取消');
    echo json_encode($ret);

    return;
}

switch ($type) {
    case CustomerApiOrder::CANCEL_QUEUE:
        $task = array(
            'method' => 'api_queue_cancel',
            'params' => array(
                'phone' => $validate['phone'],
                'booking_id' => $booking_id,
            ),
        );
        break;
    case CustomerApiOrder::CANCEL_ORDER:
        $task = array(
            'method' => 'api_order_cancel',
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
            'method' => 'api_queue_cancel',
            'params' => array(
                'phone' => $validate['phone'],
                'booking_id' => $booking_id,
            ),
        );
        break;
}
Queue::model()->putin($task, 'apporder');
$ret = array('code' => 0 , 'data' => array('booking_id' => $booking_id, 'order_ids' => $cancel_redis['order_ids']) , 'message' => '取消成功');
echo json_encode($ret);

return;
