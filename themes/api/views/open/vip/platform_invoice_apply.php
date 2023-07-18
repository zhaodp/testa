<?php
/**
 * 客户端API：c.coupon.bind 绑定优惠券
 * @param token
 * @author
 * @return json,成功信息，异常返回错误代码
 *
 */

//$token = isset($params['token']) ? $params['token'] : '';
//$sdate = isset($params['sdate']) ? trim($params['sdate']) : '';
//$edate = isset($params['edate']) ? trim($params['edate']) : '';
$channel = isset($params['channel'])?trim($params['channel']):0;

if (empty($channel) ) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误'
    );
    echo json_encode($ret);
    return;

}

//$dataProvider = BCustomers::model()->getCustomerTradeList($params);

$ret = array(
    'code' => 0,
    'result' => 0
);

echo json_encode($ret);