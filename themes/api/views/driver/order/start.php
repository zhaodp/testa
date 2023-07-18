<?php
/**
 * 开始接口 (开车)API
 * Created by JetBrains PhpStorm.
 * User: zhanglimin
 * Date: 13-5-25
 * Time: 下午3:10
 * To change this template use File | Settings | File Templates.
 */

$token=isset($params['token'])&&!empty($params['token']) ? trim($params['token']) : "";

$order_id=isset($params['order_id'])&&!empty($params['order_id']) ? trim($params['order_id']) : "";

$gps_type=isset($params['gps_type'])&&!empty($params['gps_type']) ? strtolower(trim($params['gps_type'])) : "wgs84";

$lng=isset($params['lng'])&&!empty($params['lng']) ? trim($params['lng']) : "";

$lat=isset($params['lat'])&&!empty($params['lat']) ? trim($params['lat']) : "";

$log_time=isset($params['log_time'])&&!empty($params['log_time']) ? trim($params['log_time']) : "";

//添加车牌号及用户信息 BY AndyCong 2013-06-05
$car_number=isset($params['car_number']) ? trim($params['car_number']) : "";
$name = isset($params['name']) ? $params['name'] : '先生';
$phone = isset($params['phone']) ? $params['phone'] : '';
//添加车牌号及用户信息 BY AndyCong 2013-06-05 END

if(empty($token) || empty($order_id) || empty($lng) || empty($lat) || empty($log_time) || empty($phone)){
    $ret=array(
        'code'=>2,
        'message'=>'参数不正确!'
    );
    echo json_encode($ret);
    return;
}

$driver = DriverStatus::model()->getByToken($token);

if (empty($driver) || $driver->token===null||$driver->token!==$token) {
    $ret=array(
        'code'=>1,
        'message'=>'token失效'
    );
    echo json_encode($ret);
    return;
}


//添加task队列
$task=array(
    'method'=>'push_order_position',
    'params'=>array(
        'order_id'=>$order_id,
        'flag'=>OrderPosition::FLAG_START,
        'gps_type'=>$gps_type,
        'lng'=>$lng,
        'lat'=>$lat,
        'log_time'=>$log_time,
        //添加车牌号、客户名、电话号 BY AndyCong 2013-06-05
        'car_number' => $car_number,
        'name' => $name,
        'phone' => $phone,
        //添加车牌号、客户名、电话号 BY AndyCong 2013-06-05 END
    )
);
Queue::model()->putin($task , 'apporder');

$ret=array(
    'code'=>0,
    'message'=>'成功!'
);

echo json_encode($ret);
return;