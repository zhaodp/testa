<?php
//需要写清楚注释 add by sunhongjing at 2013-5-19
/*
 * modify  zhanglimin 2013-06-07
 * 切换验证token方式 操作走队列
 */

if(Yii::app()->params['order_architecture_refactor_on']) {
    $result = DriverPlaceOrderService::getInstance()->replenishOrder($params);
    echo json_encode($result);
    return;
}
$driver = DriverStatus::model()->getByToken($params['token']);
if (empty($driver) ||  $driver->token===null||$driver->token!==$params['token']) {
    $ret=array(
        'code'=>1,
        'message'=>'请重新登录'
    );
    echo json_encode($ret);
    return;
}

if (empty($params['call_time'])  &&  empty($params['booking_time'])  && empty($params['phone'])  && !preg_match('%^[0-9]*$%', $params['phone'])){
    $ret = array (
        'code'=>2,
        'message'=>'数据格式不正确');
    echo json_encode($ret);
    return;
}
$time = time();
//验证预约时间和当前时间不能相隔半小时以上
//if (abs(strtotime($params['booking_time']) - $time) >= 3600) {
//	$params['booking_time'] = date('Y-m-d H:i:s' , $time);  //这个逻辑测试和产品觉得有问题，从代码来看不知道为啥当初加上这个
//}

//增加练习电话 BY AndyCong 2013-12-20
$contact_phone = isset($params['contact_phone']) ? trim($params['contact_phone']) : $params['phone'];

//日间模式校验
if(in_array($params['source'], Order::$daytime_sources)) {
    // 用当前服务器时间做校验
    $check_ret = Order::CheckSpecialOrderSource($params['source'],
	$driver->city_id, strtotime($params['call_time']));

    if(!$check_ret['flag']) {
        if($check_ret['code'] == ApiErrorCode::ORDER_CITY_ERROR) {
	    $ret = array('code' => $check_ret['code'],
	        'data' => '', 'message' => '当前城市没有开通日间服务,请选择普通订单');
	    EdjLog::info("CheckSpecialOrderSource|".$driver->driver_id.'|'.$params['source']
                .'|'.$driver->city_id.'|'.$ret['code']);
	    echo json_encode($ret);
            return;
	}
	elseif($check_ret['code'] == ApiErrorCode::ORDER_TIME_ERROR) {
	    $ret = array('code' => $check_ret['code'],
	        'data' => '', 'message' => '订单时段为普通按里程计费方式，请选择普通订单');
	    EdjLog::info("CheckSpecialOrderSource|".$driver->driver_id.'|'.$params['source']
                .'|'.$driver->city_id.'|'.$ret['code']);
	    echo json_encode($ret);
            return;
	}
	elseif($check_ret['code'] == ApiErrorCode::ORDER_SOURCE_ERROR) {
            $ret = array('code' => $check_ret['code'],
	        'data' => '',
		'message' => isset($check_ret['message']) ? $check_ret['message'] : '下单失败');
	    EdjLog::info("CheckSpecialOrderSource|".$driver->driver_id.'|'.$params['source']
                .'|'.$driver->city_id.'|'.$ret['code']);
	    echo json_encode($ret);
            return;
	}
    }
}

//添加task队列
$task=array(
    'method'=>'push_order_create',
    'params'=>array(
		'city_id' => $driver->city_id,
        'call_time'=>strtotime($params['call_time']),
        'order_number' => isset($params['order_number']) ? $params['order_number'] : '',   //BY AndyCong 2013-08-02
        'booking_time'=>strtotime($params['booking_time']),
        'phone'=>$params['phone'],
        'contact_phone'=>$contact_phone,
        'source'=>$params['source'],
        'description'=> Order::SourceToString($params['source']),
        'driver'=>$driver->name,
        'driver_id'=>$driver->driver_id,
        'driver_phone'=>$driver->driver_phone,
        'imei'=>$driver->imei,
        'created'=>strtotime($params['booking_time']),
    )
);

Queue::model()->putin($task,'order');

$ret=array(
    'code'=>0,
    'message'=>'成功!'
);
echo json_encode($ret);
