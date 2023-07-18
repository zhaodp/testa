<?php
//新的报单接口, 和原来的不同在于 写 订单数据以及修改会同步,如果失败会给司机端返回错误
//TODO... 是否可以改为同步,需要问一下建平

//
if(Yii::app()->params['order_architecture_refactor_on']) {
    $result = SubmitOrderAutoService::getInstance()->submitSettle($params);
    echo json_encode($result);
    return;
}

EdjLog::info('submit_settle '.json_encode($params));
//1.check params
if(checkParams($params)){
	$ret=array('code'=>2, 'message'=>'参数不正确!');
	echo json_encode($ret);return;
}
//2.校验 driver token
$token  = $params['token'];
$driver_id = DriverToken::model()->getDriverIdByToken($token);
if(!$driver_id){
	$ret = array (
		'code'=>1,
		'message'=>'请重新登录');
	echo json_encode($ret);return;
}else{
	$params['driver_id'] = $driver_id;
}

//判断 order_number order_id 是否存在
if(empty($params['order_id']) && empty($params['order_number'])){
	$ret = array (
		'code'=>2,
		'message'=>'订单ID或订单号不能为空');
	echo json_encode($ret);
	return;
}

//选司机下单订单报单 ，将虚拟的order_id转化成数据库中的order_id才可以报单
if (strlen($params['order_id']) > 11 && is_numeric($params['order_id'])) {
	//获取数据库中的order_id
	EdjLog::info('order_id too long , order_id is  '.$params['order_id']);
	$order_id = ROrder::model()->getOrder($params['order_id'] , 'order_id');
	if (empty($order_id)) {
		$ret = array('code' => 2 , 'message' => '订单异常,请稍后再报单');
		echo json_encode($ret);return ;
	}
	$params['unique_order_id'] = $params['order_id'];
	$params['order_id'] = $order_id;
}


$submitRedisTag='';
if(!empty($params['order_id'])){
    $submitRedisTag.=$params['order_id'];
}
if(!empty($params['order_number'])){
    $submitRedisTag.=$params['order_number'];
}
if(!empty($params['order_id']) && !empty($params['order_number']) ){
    RSubmitOrder::model()->setOrderNumberByOrderId($params['order_id'],$params['order_number']);
}
if(!RSubmitOrder::model()->addOrderIdIfNotExist($submitRedisTag)){
    $ret = array (
        'code'=>0,
        'message'=>'提交成功！');
    echo json_encode($ret);
    return;
}

$orderId = $params['order_id'];
$channel = $params['channel'];
$source  = $params['source'];
EdjLog::info('before  ger order ---- '.$orderId);
$order   = Order::model()->getOrderById($orderId);
if (empty($order)) {
	$ret = array('code' => 2 , 'message' => '订单异常,请稍后再报单');
	echo json_encode($ret);return ;
}
// check money

//3.锁用户/和订单
//4.看用户的余额是否足够,不够返回错误提示,已经发提示短信
$price = $params['price'];
$couponMoney = $params['cash_card_balance'];
$totalExcepted = $params['income'];//总费用,也是必须传的
$userMoney = FinanceCastHelper::getMoneyOfUser($orderId, $order['phone']) + $price + $couponMoney;//TODO ... 现金和实体卡是否应该后面来加
if(($userMoney < $totalExcepted) && ($order['status'] == Order::ORDER_READY )){
	$send_phone = $order['phone'];
	$vip_info=VipPhone::model()->getVipInfoByPhone($order['phone']);
	if($vip_info&&!empty($vip_info['phone'])&&!empty($vip_info['vipid'])){
		$vip=Vip::model()->getPrimary($vip_info['vipid']);
		if($vip){
			$send_phone=$vip['phone'];
		}
	}
	$orderExt = OrderExt::model()->getPrimary($params['order_id']);
	//报单的时候这两个值还没有写入数据库,需要手动写入
	$needCharge = $totalExcepted - $userMoney;
	if($needCharge > 0 ){
		$content=sprintf("尊敬的用户，您的订单因为您余额不足而无法报单支付，订单号".$order['order_id']."，预计缺少".sprintf("%.2f", $needCharge)."元人民币,请您及时充值，以便我们更好为您服务");
		Sms::SendSMS($send_phone, $content);
	}
}
$waitingTime = isset($params['waiting_time']) ? $params['waiting_time'] : 0;
//5.订单快照
$orderType = FinanceUtils::getOrderType($order);
//校验
if(FinanceConstants::ORDER_TYPE_DAYTIME == $orderType){

}
$endTime = round($params['end_time'] / 1000);// 司机端上传上来的是毫秒级的
$startTime = round($params['start_time'] / 1000);
//司机端在某些极端情况会出现没有 end_time 的时候,这里进行兼容
if($endTime < $startTime){
    $endTime = $startTime;
}
$snapshootAttributes = FinanceUtils::getLegalParamList($orderType, $params);
$tmpArr = $snapshootAttributes;
$snapshootAttributes['income'] = $totalExcepted;
$snapshootAttributes['start_time']  = $startTime;
$snapshootAttributes['end_time']    = $endTime;
$snapshootAttributes['wait_time']   = $waitingTime;
$snapshootAttributes['price']       = $price;
$snapshootAttributes['coupon_money'] = $couponMoney;
$snapshootAttributes['serve_time']   = $endTime - $startTime; //服务时间
$waitPrice = isset($params['wait_price']) ? $params['wait_price'] : 0.00;
$snapshootAttributes['wait_price']   = $waitPrice;
$modifyType  = isset($params['modify_type']) ? $params['modify_type'] : 0;
$modifyAmount = isset($params['modify_price']) ? $params['modify_price'] : 0;
$modifyName   = isset($params['modify_name']) ? $params['modify_name'] : '';
$beyond_time_cost = isset($params['beyond_time_cost']) ? $params['beyond_time_cost'] : 0;// 超过基础时间60分钟收取的费用
$beyond_distance_cost = isset($params['beyond_distance_cost']) ? $params['beyond_distance_cost'] : 0;//超过基础距离10km收取的费用
$distance = isset($params['distance']) ? $params['distance'] : 0;//订单总距离
$cityId = isset($order['city_id']) ? $order['city_id'] : 0;//取司机的城市id从$driver取改为取订单的cityid
$app_ver = isset($params['app_ver'])?$params['app_ver']:'0';
$subsidy_hour = 0;
$beyond_time = 0;
$beyond_distance = 0;
$metaArr = array();//专门用来保存meta的json
if(version_compare($app_ver,'2.5.4') > 0){
    $subsidy_hour = 1;//为1的时候取消补贴
    $daytime_type = RCityList::model()->getCityById($cityId,'daytime_price');
    if($daytime_type && isset(Yii::app()->params['daytime_price_new'][$daytime_type])){
        $day_time_data = Yii::app()->params['daytime_price_new'][$daytime_type];
        $basic_time  = $day_time_data['basic_time']; // 基础时间60
        $basic_distance  = $day_time_data['basic_distance']; // 基础距离10

        $beyond_distance = $distance - $basic_distance;//超过的距离
        if((($endTime - $startTime)/60) -$basic_time > 0){
            $beyond_time = (($endTime - $startTime)/60) - $basic_time;//超过的时间
        }
    }
    if($beyond_time_cost > 0 || $beyond_distance_cost > 0){
        $metaArr = @OrderSnapshoot::model()->appendSnapshootDetail($beyond_time,$beyond_time_cost,$beyond_distance,$beyond_distance_cost,$metaArr);
    }
}
$fee = OrderExt::model()->getBadWeatherSurchargeByOrderId($params['order_id']);//是否恶劣天气加价的金额
if(!$fee){
    if(0 != $modifyType){
        //保存调整价
        $modifyArr = array(
            'type'	=> $modifyType,
            'amount' => $modifyAmount,
            'name'   => $modifyName,
        );
        $metaArr['modify_fee'] = $modifyArr;
    }
}
if(!empty($metaArr)){
	$snapshootAttributes['meta'] = json_encode($metaArr);//统一保存meta信息如:{"modify_fee":{"type":1,"amount":2,"name":3},"daytime_beyondcost":{"beyond_time_cost":"a","beyond_distance_cost":"b"},}
}
$status = @OrderSnapshoot::model()->saveSnapshoot($orderId, $source, $channel, $totalExcepted, $snapshootAttributes);
if(!$status){
	$ret = array(
		'code' => 2,
		'message' => '提交失败,请重试',
	);
	echo json_encode($ret);return;
}
//添加task队列向数据中添加//6.发布异步结账队列
$taskParams = array(
	'order_id' => $orderId,
	'order_number' => isset($params['order_number']) ? $params['order_number'] : 0,
	'driver_id'    => $driver_id,
	'lat'          => $params['lat'],
	'lng'          => $params['lng'],
	'gps_type'     => isset($params['gps_type']) ? $params['gps_type'] : 'baidu',
	'end_time'     => date('Y-m-d H:i:s', $endTime),
	'start_time'     => date('Y-m-d H:i:s', $startTime),
	'distance'     => $params['distance'],
	'cost_type'    => $params['cost_type'],
	'log_time'     => date('Y-m-d H:i:s', $endTime),
	'name'         => $params['name'],
	'car_type'     => isset($params['car_type']) ? $params['car_type'] : 0,
	'car_number'   => isset($params['car_number']) ? $params['car_number'] : 0 ,
	'income'       => $totalExcepted,
	'price'        => isset($params['price']) ? $params['price'] : 0,
	'waiting_time' => $waitingTime,
	'tip'          => isset($params['tip']) ? $params['tip'] : 0,
	'car_cost'     => isset($params['car_cost']) ? $params['car_cost'] : 0, //order_ext 里面 没有使用
	'other_cost'   => isset($params['other_cost']) ? $params['other_cost'] : 0,//order_ext 里面插入都是最后一次是13年
	'cost_mark'    => isset($params['cost_mark']) ? $params['cost_mark'] : 0,
	'card'		   => isset($params['card']) ? $params['card'] : 0,
	'invoiced'	   => isset($params['invoice']) ? intval($params['invoice']) : '', //发票
	'coupon_money' => $couponMoney,
	'subsidy_hour' => $subsidy_hour,
);

$taskParams = array_merge($taskParams, $tmpArr);

$task = array(
	'method'=>'order_submit',
	'params'=>$taskParams,
);

Queue::model()->putin($task,'settlement');

$dumpTask = array(
	'method'    => 'saveOrderInfo',
	'params'    => array(
		'order_id' => $orderId,
		'car_number' => isset($params['car_number']) ? $params['car_number'] : '',
		'car_type' => isset($params['car_type']) ? $params['car_type'] : '',
		'order_detail' => isset($params['order_detail']) ? $params['order_detail'] : '',
		'driver_id'    => $driver_id,
		'comment' => isset($params['remark']) ? $params['remark'] : '',
		'customer_status' => isset($params['customer_status']) ? $params['customer_status'] : '',
	),
);

Queue::model()->putin($dumpTask,'dumplog');
FinanceUtils::orderLog($orderId,$driver_id,$source,$startTime);
$ret = array (
	'code'=>0,
	'message'=>'提交成功！');
echo json_encode($ret);
return;

function checkParams($params = array())
{
	return (!isset($params['token'])
		|| !isset($params['order_number'])
		|| !isset($params['order_id'])
		|| !isset($params['source'])
		|| !isset($params['channel'])
		|| !isset($params['income'])
		|| !isset($params['lat'])
		|| !isset($params['lng'])
		|| !isset($params['cost_type'])
		|| !isset($params['name'])
	);
}
