<?php
/**
 * 报单列表api---改善list接口
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-06-24
 */
//验证参数
$pageNo = isset($params['pageNo']) ? $params['pageNo'] : 1;
$pageSize = isset($params['pageSize']) ? $params['pageSize'] : 20;
$currentId = isset($params['order_id']) ? $params['order_id'] : '';
$token = isset($params['token']) ? $params['token'] : '';
if ('' == $token) {
	$ret=array(
        'code'=>2,
        'message'=>'参数有误!'
    );
    echo json_encode($ret);
    return;
}
//验证参数 END

//验证token
$driver = DriverStatus::model()->getByToken($params['token']);
if (empty($driver) ||  $driver->token===null||$driver->token!==$params['token']) {
    $ret=array(
        'code'=>1,
        'message'=>'请重新登录'
    );
    echo json_encode($ret);
    return;
}
//验证token END
$condition = array();
$condition['driver_id'] = $driver->driver_id;
$condition['order_id'] = $currentId;
$condition['pageSize'] = $pageSize;
$condition['offset'] = ($pageNo-1)*$pageSize;
$orderList = Order::model()->getSubmitOrderList($condition);

$ret = array (
    'code'=>0,
    'orderList'=>$orderList,
    'message'=>'读取成功'
);
echo json_encode($ret);
return;