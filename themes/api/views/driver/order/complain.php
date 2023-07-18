<?php
/**
 * 司机投诉客人
 * User: mtx
 * Date: 13-6-22
 * Time: 下午5:59
 */

// 验证参数是否有效
if (!isset($params['order_id'])
    || !isset($params['type'])
    || !isset($params['content'])
    || !isset($params['token'])
) {
    $ret = array(
        'code' => 1,
        'message' => '参数不正确');
    echo json_encode($ret);
    return;
}

// 验证token是否有效
$driver_id = DriverStatus::model()->getByToken($params['token']);
//$driver_id = $params['token'];
if (!$driver_id) {
    $ret = array(
        'code' => 1,
        'message' => '请重新登录');
    echo json_encode($ret);
    return;
}
$params['driver_id'] = $driver_id->driver_id;
//refresh cache by aiguoxin fix bug 1568
$cache_key = Order::SUBMIT_ORDER_CACHE_KEY . 'detail_' . $params['driver_id'] . '_' . $params['order_id'];
$data = Yii::app()->cache->get($cache_key);
if($data){
    $data['complaint'] = 1; // set complainted
    Yii::app()->cache->set($cache_key, $data, 180);
}
//refresh cache by aiguoxin
//添加task队列向数据中添加
$task = array(
    'method' => 'driver_complain',
    'params' => $params
);
//Queue::model()->task($task);
//Queue::model()->putin($params=null,$queue_type=null)第二个参数是队列名称,可接受值见Queue::$queue_type_list;
Queue::model()->putin($task,'task');

$ret = array(
    'code' => 0,
    'message' => '提交成功！');
echo json_encode($ret);

