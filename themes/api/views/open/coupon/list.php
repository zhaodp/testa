<?php
/**
 * 客户端API：c.coupon.list 优惠券列表
 * @param token
 * @author 
 * @return json,成功信息，异常返回错误代码
 * 
 */

$token = isset($params['token']) ? $params['token'] : '';
$pageNO = isset($params['pageNO']) ? $params['pageNO'] : 0;
$pageSize = isset($params['pageSize']) ? $params['pageSize'] : 10;
$sort = isset($params['sort']) ? $params['sort'] : 0;
$status = isset($params['status']) ? $params['status'] : 1;
$app_ver = isset($params['app_ver']) ? $params['app_ver'] : '';
//增加参数 appkey 用来后面一些其他操作  by liutuanwang @ 2014-06-18
$channel = isset($params['channel']) ? $params['channel'] : 0;
$phone  = isset($params['phone'])   ? $params['phone']  : 0;
if (empty($token) and empty($phone)) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误'
    );
    echo json_encode($ret);
    return;
}
$params = array();
$params['phone'] = $phone;
//如果有传 token 就从系统取手机号
if(!empty($token)){
    $validate = CustomerToken::model()->validateToken($token);
    if (!$validate) {
        $ret = array(
            'code' => 1,
            'message' => 'Token验证失败',
        );
        echo json_encode($ret);
        return;
    }
    $params['phone'] = $validate['phone'];
}

$params['pageNO'] = $pageNO;
$params['pageSize'] = $pageSize;
$params['sort'] = $sort;
$params['status'] = $status;
//当 token 不存在的时候就用 channel 查
if(empty($token)){
    $params['channel'] = $channel;
}

$bonus_list = BBonus::model()->getCustomerBonus($params);

//add by aiguoxin
CustomerMain::model()->updateAppversion($params['phone'],$app_ver);

$ret = array(
    'code' => 0,
    'message' => '获取成功',
    'data' => $bonus_list
);
echo json_encode($ret);