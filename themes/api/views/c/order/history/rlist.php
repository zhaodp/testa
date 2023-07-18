<?php
/**
 * 历史订单在redis中维护
 * @author bidong 2014-1-20
 */
//参数有效性验证
$token = trim($params['token']);
$sort_num = $params['sortNum'];
$step = isset($params['step']) ? $params['step'] : 10;


//需要优化，增加缓存。add by sunhongjing
$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
    $ret = array(
        'code' => 1,
        'message' => '验证失败',
    );
    echo json_encode($ret);
    return;
}
$phone = trim($validate['phone']);


//暂时在这里加载缓存 bidong 2014-1-17
ROrderHistory::model()->loadCustomerOrder($phone);

//这里读取的是历史订单的信息，需要加缓存。
//从redis中读取数据 bidong 2014-1-16
$orders=ROrderHistory::model()->getOrderList($phone, $sort_num, $step);
if ($orders) {
    // 获取订单的评价
    if (!empty($orders['orderList'])) {
        foreach ($orders['orderList'] as $k => $list) {
            $comment = CustomerApiOrder::model()->checkedCommentByOrderID($list['order_id']);
            $orders['orderList'][$k]['is_comment'] = $comment->is_comment;
        }
    }

    $ret = array(
        'code' => 0,
        'data' => array('orderList' => $orders['orderList'], 'orderCount' => $orders['orderCount']),
        'message' => '获取成功'
    );
} else {
    $ret = array(
        'code' => 2,
        'message' => '获取失败'
    );
}
echo json_encode($ret);