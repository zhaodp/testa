<?php
/** 手动报单接口 */
Yii::import("application.models.redis.*");
Yii::import('application.models.pay.calculator.*');

//
if(Yii::app()->params['order_architecture_refactor_on']) {
    $result = SubmitOrderManualService::getInstance()->submitSettle($params);
    echo json_encode($result);
    return;
}

EdjLog::info('driver.order.report_settel import args is '.json_encode($params));
// check params
if (checkParams($params)) {
	$ret = array('code' => 2, 'message' => '参数不正确!');
	echo json_encode($ret);
	return;
}
//check driver token
$driver_id = DriverToken::model()->getDriverIdByToken($params['token']);
if(empty($driver_id)){
	$ret = array(
		'code' => 1,
		'message' => '请重新登录');
	echo json_encode($ret);return;
}
//lock order
$orderId = $params['order_id'];
$order = Order::model()->getOrderInfomation($orderId, $driver_id);
$orderNumber = isset($order['order_number']) ? $order['order_number'] : 0;
if(empty($order)){
	$ret = array(
		'code' => 2,
		'message' => '订单号有误');
	echo json_encode($ret);return;
}
$submitRedisTag = '';
$submitRedisTag .= $orderId;
if(!empty($orderNumber)){
	$submitRedisTag .= $orderNumber;
}
if(!empty($params['order_id']) && !empty($params['order_number']) ){
    RSubmitOrder::model()->setOrderNumberByOrderId($params['order_id'],$params['order_number']);
}
if(!RSubmitOrder::model()->addOrderIdIfNotExist($submitRedisTag)){
	$ret = array (
		'code'=>0,
		'message'=>'订单已处理');
	echo json_encode($ret);
	return;
}
$driver = Driver::getProfile($driver_id);
//check order
$status = $order['status'];
if (($status == Order::ORDER_COMPLATE && EmployeeAccount::model()->getOrderfee($order) > 0)
	|| $status == Order::ORDER_CANCEL || $status == Order::ORDER_COMFIRM
	|| $status == Order::ORDER_NOT_COMFIRM
) {
	$ret = array(
		'code' => 0,
		'message' => '订单已处理');
	echo json_encode($ret);
	RSubmitOrder::model()->delOrderId($submitRedisTag);
	return;
}

if(empty($orderNumber)){
	$orderNumber = $orderId;
}

//检查工单号是否已经使用
$ret = Order::model()->find('order_number=:order_number and order_id != :order_id', array(
	':order_number' => $orderNumber,
	':order_id' => $orderId));

if ($ret) {
	$ret = array(
		'code' => 2,
		'message' => '工单号已使用');
	echo json_encode($ret);
	RSubmitOrder::model()->delOrderId($submitRedisTag);
	return;
}

$attributes = array();
$attributes['cost_type'] = 0;
$deducte_money = $income = 0;
if (isset($params['cost_type']) && !empty($params['cost_type'])) {
	$favorable = Order::model()->getOrderFavorable($order->phone, $order->booking_time, $order->source, $params['order_id']);
	if ($favorable) {
		$attributes['cost_type'] = $favorable['code'];
		$order->cost_type = $favorable['code'];
		$deducte_money = $favorable['money'] + $favorable['user_money'];

		switch ($favorable['code']) {
			case 1:
				$order->vipcard = $favorable['card'];
				$order->name = $favorable['name'];
				$attributes['vipcard'] = $favorable['card'];
				break;
			case 2:
				$order->bonus_code = $favorable['card'];
				break;
			case 4:
				$order->bonus_code = $favorable['card'];
				break;
			case 8:
				break;
		}
	}
}

$attributes['order_number'] = $orderNumber;
$attributes['name'] = $params['name'];//用户姓名
$attributes['location_start'] = $params['location_start'];
$attributes['location_end'] = $params['location_end'];
$attributes['distance'] = !empty($params['distance']) ? $params['distance'] : 0;
$attributes['price'] = empty($params['price']) ? 0 : $params['price'];
$attributes['car_number'] = isset($params['car_number']) ? $params['car_number'] : 0;
$attributes['log'] = isset($params['log']) ? $params['log'] : '';
$attributes['wait_time'] = $params['waiting_time'];
$attributes['end_time'] = isset($params['end_time']) ? strtotime($params['end_time']) : 0;
$attributes['start_time'] = isset($params['start_time']) ? strtotime($params['start_time']) : 0;

//添加实物劵金额  --mengtianxue 修改时间：2014-03-20
$attributes['coupon_money'] = isset($params['cash_card_balance']) ? intval($params['cash_card_balance']) : 0.00; //定额卡可抵扣金额
$attributes['price']=$attributes['price']-$attributes['coupon_money'];

EdjLog::info('whytest=invoiced update  before4:'.$params['invoice'],'console');
$invoice = isset($params['invoice']) ? $params['invoice'] : false;
EdjLog::info('whytest=invoiced update  before3:'.$invoice,'console');
if ($invoice == 'false'){
    $attributes['invoiced'] = 0;
}else{
    $attributes['invoiced'] = 1;
}
//$attributes['invoiced'] = $invoice ? '1' : '0'; //发票
EdjLog::info('whytest=invoiced update  before2:'.$attributes['invoiced'],'console');
$city_id = isset($order['city_id']) ? $order['city_id'] : 0 ;//cityid由从司机对象取改为从order对象取
$wait_time = empty($attributes['wait_time']) ? 0 : $attributes['wait_time'];

//计算价格时候的时间  默认是开车时间，如果没有开车时间或者格式不对，选预约时间  --孟天学 2013-09-02
$booking_time = $attributes['start_time'];

$income  = ''; //TODO... 计算

$price = $attributes['price'] + $attributes['coupon_money'];
$total_money = 0 ;//TODO... 计算

$orderType = FinanceUtils::getOrderType($order);
$snapshootAttributes = array();
$startTime = $attributes['start_time'];
$endTime   = $attributes['end_time'];
$distance   = $attributes['distance'];//订单实际距离
//司机端在某些极端情况会出现没有 end_time 的时候,这里进行兼容
if($endTime < $startTime){
    $endTime = $startTime;
}
$beyondTimeCost = 0;
$beyondTime = 0;
$beyondDistance = 0;
$beyondDistanceCost = 0;
$app_ver = isset($params['app_ver'])?$params['app_ver']:'0';
if(FinanceConstants::ORDER_TYPE_DAYTIME == $orderType){
    if(version_compare($app_ver,'2.5.4') > 0){
        //2.5.4版本开始日间单收费规则开始调整为时间距离双向收费
	    $timeCostCal = new TimeDistanceCostCalculator($city_id, $orderType, ($endTime - $startTime),$distance);
        $attributes['subsidy_hour'] = 1;//该参数判断是否每小时补贴司机10元的补贴  新版本去掉
        $costArr = $timeCostCal->calculator();
        if(!empty($costArr)){
            $snapshootAttributes['time_cost'] = $costArr['totalFee'];
            $beyondTime = $costArr['beyondTime'];
            $beyondTimeCost = $costArr['beyondTimeCost'];
            $beyondDistance = $costArr['beyondDistance'];
            $beyondDistanceCost = $costArr['beyondDistanceCost'];
        }
    }else{
        $attributes['subsidy_hour'] = 0;
	    $timeCostCal = new TimeCostCalculator($city_id, $orderType, ($endTime - $startTime));
        $snapshootAttributes['time_cost'] = $timeCostCal->calculator();
        $snapshootAttributes['subsidy_back'] = isset($params['subsidy_back']) ? $params['subsidy_back'] : 0;//返补贴在新2.5.4版本去掉返程补贴由司机端上传
    }
	$subsidyCal  = new SubsidyCalculator($city_id, $orderType, $startTime, $endTime);
//	$backSubsidyCal = new BackSubsidyCalculator($city_id, $attributes['distance'], $orderType);
	//计算了 income 之后, 还应该考虑原有的调整费, 调整费,在退单之后会重结
	$tmpMeta = FinanceCastHelper::getOrderModifyFee($orderId);
	if($tmpMeta){
		$modifyFee = isset($tmpMeta['amount']) ? $tmpMeta['amount'] : 0;
		$tmpTimeCost = $snapshootAttributes['time_cost'] + $modifyFee * -1;
		if($tmpTimeCost < 0){
			$tmpTimeCost = 0;
		}
		$snapshootAttributes['time_cost'] = $tmpTimeCost;

	}
	$snapshootAttributes['subsidy']   = $subsidyCal->calculator();//夜间补贴仍然保留
//	$snapshootAttributes['subsidy_back'] = $backSubsidyCal->calculator();

}
if(FinanceConstants::ORDER_TYPE_UNIT == $orderType){
	$snapshootAttributes['unit_cost']  =  FinanceConfigUtil::getUnitOrderPrice($orderType, $city_id);
	$tmpMeta = FinanceCastHelper::getOrderModifyFee($orderId);
	if($tmpMeta){
		$modifyFee = isset($tmpMeta['amount']) ? $tmpMeta['amount'] : 0;
		$snapshootAttributes['unit_cost'] = $snapshootAttributes['unit_cost'] + $modifyFee * -1;
	}
}
$income = $total_money = array_sum($snapshootAttributes);
$attributes = array_merge($attributes, $snapshootAttributes);//把需要扣款的参数,给合并到数组里面
if ($attributes['cost_type'] == 0) {
	$total_money = $price;
} else {
	if($income > $price){
		//要收取帐户金额
		$balance = $income - $price;
		if ($balance > $deducte_money) {
			$total_money = $deducte_money + $price;
		} else {
			$total_money = $income;
		}
	}else{
		$total_money = $price;
	}
}
$snapshootAttributes['income'] = $income;
$snapshootAttributes['start_time']  = $startTime;
$snapshootAttributes['end_time']    = $endTime;
$snapshootAttributes['wait_time']   = $attributes['wait_time'];
$snapshootAttributes['price']       = $attributes['price'];
$snapshootAttributes['coupon_money'] = $attributes['coupon_money'];
$snapshootAttributes['serve_time']   = $endTime - $startTime;//服务时间
EdjLog::info(sprintf(' report money infoes |calculate income|%s|price|%s|deducte_money|%s|total_money|%s',
	$income, $price, $deducte_money, $total_money));
//添加录入代驾费 小于（计算费用) 提示错误
$enough = FinanceCastHelper::isMoneyEnough($params['order_id'], $income, $price);
$orderExt = OrderExt::model()->getPrimary($params['order_id']);
//TODO ... 这个是应该采用新上传上来的值
if($attributes['cost_type'] == 0){//如果是纯现金支付,那么现金不能小于里程费
	$order['income'] = $income;
	$order['price']  = $attributes['price'];//这里加上了实体卡金额
	$orderMoney = FinanceCastHelper::getOrderIncome($order, $orderExt);
	if($price < $orderMoney){
		$enough = false;
	}
}
if (!$enough) {
	$ret = array(
		'code' => 2,
		'message' => '输入的代驾费有误'
	);
	RSubmitOrder::model()->delOrderId($submitRedisTag);
	echo json_encode($ret);
	return;
}

$attributes['income'] = $total_money;
$isRemote = FinanceUtils::isRemoteOrder($order, $orderExt);
if($isRemote){
	$attributes['income'] = $income;//远程订单的 price 可能大于 income
}
$attributes['city_id'] = $city_id;

$attributes['ready_time']     = isset($params['ready_time']) ? intval($params['ready_time']) : 0;
$attributes['ready_distance'] = isset($params['ready_distance']) ? floatval($params['ready_distance']) : 0.00;

$metaArr = array();//专门用来保存meta的json
if($app_ver && $app_ver >= '2.5.4'){
    if($beyondTimeCost > 0 || $beyondDistanceCost > 0){
        $metaArr = @OrderSnapshoot::model()->appendSnapshootDetail($beyondTime,$beyondTimeCost,$beyondDistance,$beyondDistanceCost,$metaArr);
    }
}
//$metaArr['modify_fee'] = OrderSnapshoot::model()->getSnapshootMeta($orderId);
$modifyArr = OrderSnapshoot::model()->getSnapshootMeta($orderId);
if(!empty($modifyArr)){
    if(isset($modifyArr['modify_fee'])){
        $metaArr['modify_fee'] = $modifyArr['modify_fee'];//只有里面存在modify_fee的时候才放进去
    }
}
if(!empty($metaArr)){
    $snapshootAttributes['meta']  = json_encode($metaArr); //如果 meta 字段包含了其他的需要更新
}
$ret = OrderSnapshoot::model()->saveSnapshoot($orderId, $params['source'], $order['channel'], $income, $snapshootAttributes);
if(!$ret){
	$ret = array(
		'code' => 2,
		'message' => '提交失败,请重试',
	);
	echo json_encode($ret);return;
}
$order_status = Order::model()->report($order, $attributes, TRUE);
$order_status = true;
if ($order_status) {
	//清除订单详情缓存
	OrderCache::model()->delOrderInfoAttribute($orderId);
	//清除客户端历史订单缓存
	CustomerApiOrder::model()->deleteOrderInfoByOrderID($orderId);

	//更新redis BY AndyCong
	$task = array(
		'method' => 'update_order_redis',
		'params' => array(
			'order_id' => $orderId,
			'order_state' => OrderProcess::ORDER_PROCESS_FINISH,
			'order_process_flag' => OrderProcess::FLAG_PROCESS_DRIVER_SUBMIT,
			'driver_id' => $driver_id,
		),
	);
	Queue::model()->putin($task, 'apporder');
	//更新redis BY AndyCong
    FinanceUtils::orderLog($orderId,$driver_id,$params['source'],$startTime);
	$ret = array(
		'code' => 0,
		'message' => '报单完成');
}
echo json_encode($ret);return;

function checkParams($params){
	return (
		!isset($params['token'])
		||!isset($params['order_id'])
		||!isset($params['source'])
		||empty($params['location_start'])
		||empty($params['location_end'])
		||!isset($params['distance'])
		||empty($params['price'])
	);
}