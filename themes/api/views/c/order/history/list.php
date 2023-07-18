<?php
/**
 * 客户端API：客户的历史订单列表。验证客户信息，返回订单列表
 * @param token
 * @param 标示订单的唯一标示
 * @param
 * @param
 * @param
 * @param
 * @author
 * @return json,下单成功或者失败信息
 *
 * @version 1.0
 * @see
 */

//参数有效性验证
$token = trim($params['token']);
$pageNo = (isset($params['pageNo']) && empty($params['pageNo'])) ? 0 : $params['pageNo'];
$pageSize = isset($params['pageSize']) ? $params['pageSize'] : 10;


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

//这里读取的是历史订单的信息，需要加缓存。
//$orders = CustomerApiOrder::model()->getOrderByPhone($phone, $pageNo, $pageSize);

//从redis中读取数据 bidong 2014-1-16
//暂时在这里加载缓存 bidong 2014-1-17
ROrderHistory::model()->loadCustomerOrder($phone);
$sort_num=$pageNo*$pageSize;
$step=$pageSize;
$orders=ROrderHistory::model()->getOrderList($phone, $sort_num, $step);
//当前时间戳
$now_timestap=time();

if ($orders) {
    // 获取订单的评价
    if (!empty($orders['orderList'])) {
        foreach ($orders['orderList'] as $k => $list) {
            $comment = CustomerApiOrder::model()->checkedCommentByOrderID($list['order_id']);
            $orders['orderList'][$k]['is_comment'] = $comment->is_comment;
            if(($now_timestap-$list['start_time'])/86400>15){
                $orders['orderList'][$k]['can_comment'] = 0;//大于15天不可以评价
            }else{
                $orders['orderList'][$k]['can_comment'] = 1;
            }
//            $orders['orderList'][$k]['start_time'] = date('Y-m-d H:i:s', $list['start_time']);
        }
    }

    $ret = array(
        'code' => 0,
        'data' => array('orderList' => $orders['orderList'], 
            'orderCount' => $orders['orderCount'],
            'tip_message' => '超过15天不能进行评价'
            ),
        'message' => '获取成功'
    );
} else {
    $ret = array(
        'code' => 2,
        'message' => '获取失败'
    );
}
echo json_encode($ret);