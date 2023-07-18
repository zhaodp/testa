<?php
/**
 * 到达、开车、结束代驾统计一个接口
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-12-03 
 */

//接收并验证参数
$token = isset($params['token']) ? trim($params['token']) : '';
$order_id = isset($params['order_id']) ? trim($params['order_id']) : '';
$gps_type = isset($params['gps_type']) ? trim($params['gps_type']) : 'wgs84';
$lng = isset($params['lng']) ? trim($params['lng']) : '';
$lat = isset($params['lat']) ? trim($params['lat']) : '';
$log_time = isset($params['log_time']) ? trim($params['log_time']) : '';

//王栋发版有bug，这个时间传错了，临时补救 add by sunhongjing 2014-01-07
if( strtotime($log_time) < time()-180 ){
	$log_time = date('Y-m-d H:i:s');
}

$position_type = isset($params['position_type']) ? trim($params['position_type']) : '';
$position_type_arr = array(
    OrderPosition::POSITION_FLAG_TYPE_ARRIVE,
    OrderPosition::POSITION_FLAG_TYPE_START,
    OrderPosition::POSITION_FLAG_TYPE_FINISH,
    OrderPosition::POSITION_FLAG_TYPE_SUBMIT,
);
if(empty($token) || empty($order_id) || empty($lng) || empty($lat) || empty($log_time) || !in_array($position_type , $position_type_arr)){
    $ret=array('code'=>2 , 'message'=>'参数不正确!');
    echo json_encode($ret);return;
}

//验证token
$driver = DriverStatus::model()->getByToken($token);
if (empty($driver) || $driver->token===null||$driver->token!==$token) {
    $ret=array('code'=>1 , 'message'=>'token失效');
    echo json_encode($ret);return;
}

switch ($position_type) {
	case OrderPosition::POSITION_FLAG_TYPE_ARRIVE:
		$flag = OrderPosition::FLAG_ARRIVE;
		break;
	case OrderPosition::POSITION_FLAG_TYPE_START:
		$flag = OrderPosition::FLAG_START;
		break;
	case OrderPosition::POSITION_FLAG_TYPE_FINISH:
		$flag = OrderPosition::FLAG_FINISH;
		break;
	case OrderPosition::POSITION_FLAG_TYPE_SUBMIT:
		$flag = OrderPosition::FLAG_SUBMIT;
		break;
	default:
		$ret=array('code'=>2 , 'message'=>'位置类型有误!');
        echo json_encode($ret);return;
		break;
}

//添加task队列
if (strlen($order_id) > 11 && is_numeric($order_id)) {
	$task=array(
	    'method'=>'upload_order_position',
	    'params'=>array(
	        'order_id'=>$order_id,
	        'flag'=>$flag,
	        'gps_type'=>$gps_type,
	        'lng'=>$lng,
	        'lat'=>$lat,
	        'log_time'=>$log_time,
	    )
	);
} else {
	$task=array(
	    'method'=>'push_order_position',
	    'params'=>array(
	        'order_id'=>$order_id,
	        'flag'=>$flag,
	        'gps_type'=>$gps_type,
	        'lng'=>$lng,
	        'lat'=>$lat,
	        'log_time'=>$log_time,
	        'name'=>'',
	        'phone'=>'',
	        'car_number'=>'',
	    )
	);
}
Queue::model()->putin($task , 'apporder');

//返回成功信息
$ret=array('code'=>0 , 'message'=>'成功!');
echo json_encode($ret);return;