<?php
/**
 * 一键预约接口
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-03-28
 */
if(Yii::app()->params['order_architecture_refactor_on']) {
    $result = DriverPlaceOrderService::getInstance()->placeOrderManually($params);
    echo json_encode($result);
    return;
}
//验证参数信息 
if ( empty($params['lat']) || empty($params['lng']) || empty($params['phone']) || empty($params['token']) || empty($params['order_number'])) {
	$ret = array(
		'code'=>2,
		'message'=>'信息有误，请仔细检查');
	echo json_encode($ret);return;
}

$gps_type = isset($params['gps_type']) ? $params['gps_type'] : 'wgs84';
$source = isset($params['source']) ? $params['source'] : Order::SOURCE_CLIENT_INPUT;
//验证token
$token = isset($params['token']) ? trim($params['token']) : '';
//验证token
$driver = DriverStatus::model()->getByToken($token);
if (empty($driver) || $driver->token===null||$driver->token!==$token) {
    $ret=array('code'=>1 , 'message'=>'token失效');
    echo json_encode($ret);return;
}
//需要优化，增加缓存。add by sunhongjing
$driver_id = DriverToken::model()->getDriverIdByToken($token);
if (!empty($driver_id)){
    if($driver->status != DriverStatus::STATUS_DRIVING){
        $driverPosition = DriverPosition::model()->getDriverPosition($driver->id);
        $appVer = $driverPosition->app_ver;
        if(!empty($appVer) && $appVer>="2.6.0.0"){
            $unlock = QueueDispatchDriver::model()->isUnLock($driver_id);
            if($unlock){
                $des = new DriverExamStudy();
                $examUrl = $des->getExamUrl($driver_id);
                if(isset($examUrl ['num'] )&&isset($examUrl ['url'] )){
                    if($examUrl ['num']>0){
                        $ret = array(
                            'code'=>11,
                            'message'=>'司机需要答题'
                        );
                        echo json_encode($ret);
                        return;
                    }
                }
            }
        }
    }
	//通过gps反推城市及地址
	$gps_location = array(
	    'longitude' => $params['lng'],
	    'latitude' => $params['lat'],
	);
	$gps = GPS::model()->convert($gps_location , $gps_type);
	
	$driver = DriverStatus::model()->get($driver_id);
	$city_id = $driver->city_id;
	$params['city_id'] = $city_id;
	if (isset($gps['street']) && !empty($gps['street'])) {
		$params['address'] = $gps['street'];
	} else {
		$params['address'] = Dict::item('city' , $city_id);//这里是什么意思
	}
	
	$params['driver_id'] = $driver_id; 
	//通过gps反推城市及地址 end
	if (empty($params['name'])) {
		$params['name'] = '先生';
	}
	//处理订单(放入队列)
//	$ret = OrderQueue::model()->booking($params , Order::SOURCE_CLIENT_INPUT , OrderQueue::QUEUE_AGENT_DRIVERBOOKING);

    $task = array(
        'method' => 'driver_supplement_order',
        'params' => $params,
    );
    Queue::model()->putin($task , 'order');

	//处理订单 END
} else {
	$ret = array(
		'code'=>1,
		'message'=>'验证失败'
	);
        echo json_encode($ret);
	return;
}

//返回数据信息
$ret = array('code'=>0 , 'message' => '下单成功');
echo json_encode($ret);
