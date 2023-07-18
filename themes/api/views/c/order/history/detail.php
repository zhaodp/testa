<?php
/**
 * 客户端API：订单详情信息。验证客户信息，返回订单列表
 * @param token
 * @param 标示订单的唯一标示
 * @param
 * @param
 * @param
 * @param
 * @author sunhongjing 2013-10-10
 * @return json,下单成功或者失败信息
 *
 * @version 1.0
 * @see
 */
$ret = array('code' => 0, 'message' => '操作成功');

//参数有效性验证

$token = trim($params['token']);
$order_id = trim($params['order_id']);

if (empty($order_id) || empty($token)) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误'
    );
    echo json_encode($ret);
    return;
}

//兼顾报单后详情页展示
//如果是order_number 则将缓存的order_id取出
if (strlen($order_id) > 11 && is_numeric($order_id)) {
	$db_order_id = ROrder::model()->getOrder($order_id , 'order_id');
	if (!empty($db_order_id)) {
		$order_id = $db_order_id;
	}
}

//需要优化，增加缓存。add by sunhongjing
$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
    $ret = array(
        'code' => 1,
        'message' => '验证失败'
    );
    echo json_encode($ret);
    return;
}

//订单信息
//获取redis订单详情 bidong 2014-1-16
//CustomerApiOrder::model()->getOrderInfoByOrderID($order_id);
//ROrderHistory::model()->getOrder($order_id);
$order= CustomerApiOrder::model()->getOrderInfoByOrderID($order_id);
if (!$order) {
    $ret = array(
        'code' => 2,
        'message' => '操作失败'
    );
} else {
	// 过滤掉 没有就不需要展示的
	$filterArr = array(
		'coupon_detail'     => '',
		'kilo_fee'          => '0元',
		'subsidy'           => '0元',
		'tip'               => '0元',
		'serve_time'        => 0,
		'time_cost'         => '0元',
		'subsidy_back'      => '0元',
	);
	FinanceUtils::paramsFilter($order, $filterArr);
    //当前时间戳
    $now_timestap=time();
    // 查看是否评价
    $comment = CustomerApiOrder::model()->checkedCommentByOrderID($order_id);
    $order['is_comment'] = $comment->is_comment;
    $order['level'] = $comment->level;

    $driver_info = Helper::foramt_driver_detail($order['driver_id'], 'google');
    //司机信息
    $order['driver'] = $driver_info;

    if(($now_timestap-$order['start_time'])/86400>15){
        $order['can_comment'] = 0;//大于15天不可以评价
    }else{
        $order['can_comment'] = 1;
    }

    $ret = array(
        'code' => 0,
        'data' => $order,
        'message' => '获取成功'
    );
}
if($ret['code'] == 0){
    $order = Order::model()->queryOrder($order_id);
    if($order){
        $share_activity = PageConfig::model()->getSharedInfoByOrderIdAndTriggerTime($order, PageConfig::TRIGGER_DETAILS);
        if($share_activity){//获取活动成功
            $ret['share_activity'] = $share_activity;
        }
    }
}
EdjLog::info('-----history-detail-return value------ '.json_encode($ret));
echo json_encode($ret);
return;