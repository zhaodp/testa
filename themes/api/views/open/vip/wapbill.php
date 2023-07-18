<?php
/**
 * 客户端API:获取vip客户昨日账单
 * @return json
 * @author cuiluzhe 2014-10-16
 */
//接收并验证参数
$url = isset($params['url']) ? $params['url'] : '';//商户
$created = isset($params['created']) ? intval($params['created']) : intval(date('Ymd',strtotime('0 days ago')));//账单日期 默认是今天
if( empty($url)){
	$ret = array('code' => 2 ,'message' => '参数有误');
        echo json_encode($ret);return ;
}

$vipTrade = VipTradeLogReportUrl::model()->getVipTradeByUrl($url,$created);
if(!$vipTrade){
	$ret = array('code' => 2 , 'message' => '您好,您不存在交易记录');
        echo json_encode($ret);return ;
}

$vipTrades = VipTradeLogReport::model()->getVipTrades($vipTrade['vipcard'], $vipTrade['created']);
if(!$vipTrades || empty($vipTrades)){
        $ret = array('code' => 2 , 'message' => '您好,您昨日不存在交易记录');
        echo json_encode($ret);return ;
}

$order_count = $vipTrade['order_count'];//昨日使用e代驾个数
$consumpte   = -$vipTrade['consumpte'];//总消费
$balance     = $vipTrade['balance'];//现余额
$i = 0;
$data = array();
foreach ($vipTrades as $list){
	$name  = preg_replace( '/\{(.*)\}/', '',$list['name']);
        $name  = str_replace('()', '', $name);
	$phone = substr_replace($list['phone'], '****', 3, 4);
	$date  = date('m月d日 H:i',$list['booking_time']);//预约日期
	$driver_id = $list['driver_id'];//司机
	$location_start = $list['location_start'];//起始地
	$location_end   = $list['location_end'];//结束低
	$distance	= $list['distance'];//公里
	$waiting_time   = $list['waiting_time'];//等候时间
	$waiting_amount = $list['waiting_amount'];//等候金额
	$amount		= -$list['amount'];//总计金额;
	$data[$i++] = array('name'=>$name, 'phone'=>$phone, 'date'=>$date, 'driver_id'=>$driver_id, 
			    'location_start'=>$location_start, 'location_end'=>$location_end, 'distance'=>$distance,
			    'waiting_time'=>$waiting_time, 'waiting_amount'=>$waiting_amount, 'amount'=>$amount);
}

$ret = array('code' => 0 , 'order_count' => $order_count , 'consumpte' => $consumpte , 'balance' => $balance , 'data' => $data, 'message' => '查询成功');
echo json_encode($ret);return ;
