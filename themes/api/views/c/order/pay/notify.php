<?php
/*
	c.order.pay.notify
	多人订单时推送给司机哪些订单必须使用现金（主叫用户不给这些订单支付）
*/
//引入银联API相关文件
Yii::import('application.vendors.upmp.*');
//引入业务逻辑model
Yii::import('application.models.pay.*');
Yii::import("application.models.order.*");
Yii::import("application.models.driver.*");
if(empty($params['token'])){
	EdjLog::info('token is empty ');
	$ret=array('code'=>1,'message'=>'请重新登录');
	echo json_encode($ret);
	return;	
}
if(empty($_REQUEST['orders'])){
	EdjLog::info('orders is empty');
	$ret=array('code'=>1,'message'=>'您没有选择任何订单');
	echo json_encode($ret);
	return;
}
$token=$params['token'];
$orders=$_REQUEST['orders'];
$validate=CustomerToken::model()->validateToken($token);
if(!$validate){
	$ret=array('code'=>1,'message'=>'验证失败，请重新登录');
	echo json_encode($ret);
	return;
}
$arrOrders=json_decode($orders,true);
foreach($arrOrders as $order_info){
	if(empty($order_info['order_id'])||!isset($order_info['is_cash_only'])){
		EdjLog::info('order_info is '.serialize($order_info));
		continue;
	}
       //$order_id_for_driver=$order_info['order_id'];
       if (strlen($order_info['order_id']) > 11 && is_numeric($order_info['order_id'])) {
           //获取数据库中的order_id
           $order_id = ROrder::model()->getOrder($order_info['order_id'] , 'order_id');
           if (empty($order_id)) {
                   $ret = array('code' => 2 , 'message' => '订单异常,请稍后再报单');
                   echo json_encode($ret);return ;
           }
       }
       else{
	   $order_id=$order_info['order_id'];
       }
	$is_cash_only=$order_info['is_cash_only'];
	$order=Order::model()->findByPk($order_id);
	if(!$order){
		EdjLog::info('find order for order_id '.$order_id.' fail');
		continue;
	}
	if($is_cash_only){
		RSubmitOrder::model()->setCashOnly($order->order_id);
	}
	$order_id_for_driver=$order['order_number'];
	if(empty($order_id_for_driver)){
		$order_id_for_driver=$order_id;
	}
	$driver_id=$order['driver_id'];
	$driver=Driver::model()->getProfile($driver_id);
	$driverPosition=DriverPosition::model()->getDriverPosition($driver->id);	
	if(empty($driverPosition['app_ver'])||$driverPosition['app_ver']<'2.3.4'){
		$ret=array('code'=>0,'message'=>'success');
		echo json_encode($ret);
		return;
	}
	$user_phone=$validate['phone'];
	$balance=BUpmpPayOrder::model()->forceGetBalance($user_phone);
	$pushResult=DriverPush::model()->pushCustomPayChange($driver_id,$balance,$order_id_for_driver,$is_cash_only);
        $driver_content=$driver_id."师傅，客户的账户余额已发生变化，请按照计价器显示的最新余额收取费用。";
        $driverResult=Sms::SendSMS($driver->phone,$driver_content);
        if(!$driverResult){
		EdjLog::info("send sms to ".$driver->phone." content ".$driver_content." fail");
        }
	if(!$pushResult){
		EdjLog::info("push message custome_pay_change fail args: $driver_id $balance $order_id_for_driver $is_cash_only");
                $key=RApiKey::model()->key('30000001');
                $sig=md5('EDJCPAYCHANGE'.$order_id_for_driver.sprintf("%.2f",$balance).(int)$is_cash_only.$key['secret']);
                $sms_content='EDJCPAYCHANGE#'.$order_id_for_driver.'#'.sprintf("%.2f",$balance).'#'.(int)$is_cash_only.'#'.substr($sig,0,8).'#';
                if(!$driver||empty($driver->phone)){
                        EdjLog::info("get profile for driver ".$driver_id." is empty");
                        continue;
                }
                $smsResult=Sms::SendSMS($driver->phone,$sms_content);
                if(!$smsResult){
                        EdjLog::info("send sms to ".$driver->phone." content ".$sms_content." fail");
                }
	}
}
$ret=array('code'=>0,'message'=>'success');
echo json_encode($ret);
return;
?>
