<?php
/**
 * 一键预约接口
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-03-28
 */

//验证参数信息
if (!isset($params['order_timedelta_from_now_on']) || !isset($params['order_gps_latitude']) || !isset($params['order_gps_longitude']) || !isset($params['order_street_name']) || !isset($params['order_street_name_is_edited']) || !isset($params['order_contact_phone']) || !isset($params['order_customer_phone']) || !isset($params['order_drivers_count'])) {
	$ret = array(
		'code'=>1,
		'message'=>'信息有误，请仔细检查');
	echo json_encode($ret);return;
}
$uuid = isset($params['uuid']) ? trim($params['uuid']) : '';

//验证最大预约人数
if (intval($params['order_drivers_count']) > 7) {
	$ret = array(
		'code'=>1,
		'message'=>'预约人数超过最大值!');
	echo json_encode($ret);return;
}
$gps_type = isset($params['gps_type']) ? $params['gps_type'] : 'wgs84';
$open_city = RCityList::model()->getOpenCityList();

//验证token
$token = $params['token'];
$validate = CustomerToken::model()->validateToken($token);
if ($validate){
	//从Redis里出数据吧，此处需要架构改造 add by sunhongjing 2013-06-06
	$is_black = Yii::app()->db_readonly->createCommand()
	                 ->select('phone')
	                 ->from('t_customer_blacklist')
	                 ->where('phone = :phone' , array(':phone' => $params['order_customer_phone']))
	                 ->queryRow();
	if (!empty($is_black)) {
		$ret = array(
			'code'=>1,
			'message'=>'您已被列入黑名单,如果要叫代驾,请呼叫 4006913939');
		echo json_encode($ret);return;
	}
	
	//通过gps反推城市及地址
	$gps_location = array(
	    'longitude' => $params['order_gps_longitude'],
	    'latitude' => $params['order_gps_latitude'],
	);
	$gps = GPS::model()->convert($gps_location , $gps_type);
	$city = GPS::model()->getCityByBaiduGPS($gps['baidu_lng'] , $gps['baidu_lat']);
	$city_id = CityConfig::getIdByName($city);

	if ($params['order_street_name_is_edited']) {
		
		//判定输入地址有效性 BY AndyCong 2013-05-29
		$gps_baidu = Helper::getBaiduGPSByAddress($city , $params['order_street_name']);
		if (empty($gps_baidu['location'])) {
			$ret = array(
				'code'=>1,
				'message'=>'您所输入的地址异常,请重新填写!');
			echo json_encode($ret);return;
		}
		if (empty($gps_baidu))  {
			$ret = array(
				'code'=>1,
				'message'=>'该城市暂时还未开通e代驾服务!');
			echo json_encode($ret);return;
		}
		$address = $params['order_street_name'];
	} else {
	    $address = $gps['street'];  
	}
	
	//校验手机号是否为vip(修改调用vip方法 2013-12-09)
	$is_vip = VipPhone::getPrimary($params['order_customer_phone']);
	$vip = 0;
	if (!empty($is_vip)) {
		$comments = '此用户是vip,该单是一键预约';
		$vip = 1;
	} else {
		$comments = '该单是一键预约';
	}
	
	//验证uuid有无下单成功过
	$cache_uuid_key = 'is_repeat_cache_uuid_'.$uuid;
	if (!empty($uuid)) {
		$data = Yii::app()->cache->get($cache_uuid_key);
		if ($data) {
			$ret = array('code' => 0, 'order' => $data , 'message' => '下单成功');
			echo json_encode($ret);return ;
		}
	}
	
	//参数整理
	$time = time();
	$booking_time = date('Y-m-d H:i:s' ,$time+intval($params['order_timedelta_from_now_on']));
	$queue_arr = array(
	    'phone' => $params['order_customer_phone'],          //客户电话
	    'contact_phone' => $params['order_contact_phone'],   //客户电话
	    'city_id' => $city_id,                               //需要gps反推
	    'callid' => md5(uniqid(rand(), true)),               //callid 时间戳加密
	    'name' => OrderQueue::QUEUE_AGENT_KEYBOOKING,        //需要传进来
	    'number' => $params['order_drivers_count'],          //司机数量
	    'address' => $address,                               //地址                
	    'comments' => $comments,                             //说明
	    'booking_time' => $booking_time,                     //传进来的时间+20分钟
	    'flag' => OrderQueue::QUEUE_WAIT,                    //派单状态
	    'type' => Order::SOURCE_CLIENT,                      //来源
	    'update_time' => '0000-00-00 00:00:00',              //更新时间
	    'agent_id' => OrderQueue::QUEUE_AGENT_KEYBOOKING,    //操作员 --- 
	    'dispatch_agent' => '',                              //派单人员
	    'dispatch_time' => '0000-00-00 00:00:00',            //派单时间
	    'created' => date('Y-m-d H:i:s' , $time),            //创建时间
	    'is_vip' => $vip                                     //vip标识
	);
	//参数整理 end
	$task = array(
	    'method' => 'queue_booking',
	    'params' => $queue_arr,
	);
	Queue::model()->putin($task,'order');
	
	$order = array(
	    'order_id' => md5(uniqid(rand(), true)),
	    'order_status' => OrderQueue::QUEUE_TYPE_ACCEPTED,
	    'order_timedelta_from_now_on' => $params['order_timedelta_from_now_on'],
	    'order_gps_latitude' => $params['order_gps_latitude'],
	    'order_gps_longitude' => $params['order_gps_longitude'],
	    'order_street_name' => $address,
	    'order_street_name_is_edited' => $params['order_street_name_is_edited'],
	    'order_contact_phone' => $params['order_contact_phone'],
	    'order_customer_phone' => $params['order_customer_phone'],
	    'order_drivers_count' => $params['order_drivers_count'],
	    'order_time' => $time,
	    'order_drivers' => array(),
	);
	$cache_key = $token.'_'.$validate['phone'];
	Yii::app()->cache->set($cache_key, $order, 120);
	
	//将uuid对应的order缓存
	if (!empty($udid)) {
		Yii::app()->cache->set($cache_uuid_key, $order, 600);
	}
	
	$ret = array(
		'code'=>0,
		'order'=>$order,
		'message'=>'预约成功'
	);
} else {
	$ret = array(
		'code'=>2,
		'message'=>'验证失败'
	);
}

//返回数据信息 
echo json_encode($ret);