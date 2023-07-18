<?php
/**
 * open.coupon.activityBind  活动绑定优惠券（自定义面额）
 * User: zhangxiaoyin
 * Date: 2015/4/18
 * Time: 15:24
 */
$phone = isset($params['phone']) ? trim($params['phone']) : '';
$code = isset($params['code']) ? trim($params['code']) : 0;
//增加参数 appkey 用来后面一些其他操作  by liutuanwang @ 2014-06-18
$acount = isset($params['acount']) ? $params['acount'] : 0;

$appkey = isset($params['appkey']) ? $params['appkey'] : '';
$ms = isset($params['ms']) ? $params['ms'] : '';
if (empty($phone) || $code == 0 || $acount == 0 || !in_array($appkey, Dict::items('bonus_sn_appkey'))) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误'
    );
    echo json_encode($ret);
    return;
}

$model = BonusCode::model()->getBonusCodeById($code);

if (!$model) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误(code无效)'
    );
    echo json_encode($ret);
    return;
}


if ($model->sn_type != 0 || $model->area_id = 0) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误(code无效)'
    );
    echo json_encode($ret);
    return;
}

$couponRules = CJSON::decode($model->coupon_rules);

if (!isset($couponRules['code_num']) || !isset($couponRules['num']) || !isset($couponRules['num_prdfix'])) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误(code无效)'
    );
    echo json_encode($ret);
    return;
}

$params = array(
    'id' => $code,
    'phone' => $phone,
    'acount' => $acount,
    'ms' => $ms
);
$task = array(
    'method' => 'bonus_no_sn',
    'params' => $params
);
Queue::model()->putin($task, 'bonus_no_sn');


$ret = array(
    'code' => 0,
    'message' => 'OK'
);
echo json_encode($ret);