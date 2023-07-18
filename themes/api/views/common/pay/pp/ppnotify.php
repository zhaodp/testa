<?php
/**
 * pp钱包回调接口.
 * User: Tuanwang,liu
 * Date: 2014=09-01
 * Time: 下午4:48
 * To change this template use File | Settings | File Templates.
 */
//引入业务逻辑model
Yii::import('application.vendors.pppay.*');
Yii::import('application.models.pay.*');
EdjLog::info('ppnotify post is '.serialize($_POST).' get is '.serialize($_GET));
MerConfig::$MER_ID = Yii::app()->params['payment']['ppmoneyConfig']['merchantId'];
MerConfig::$KEY=Yii::app()->params['payment']['ppmoneyConfig']['key'];
$_POST=$_REQUEST;
$encType = $_REQUEST ["encType"];
$signType = $_REQUEST ["signType"];
$zipType = $_REQUEST ["zipType"];
$encode = $_REQUEST ["encode"];
$transData = $_REQUEST ["transData"];
$orderResult = PaypalmSDK::unpackOrderResult ($encType, $signType, $zipType, $encode, $transData);
$merOrderNo = $orderResult->getValueAt ( "merOrderNo" );
$errorCode = $orderResult->getValueAt ( "errorCode" );
$errorMsg = $orderResult->getValueAt ( "errorMsg" );
$merRepData = PaypalmSDK::packNotifySuccess ();
if(empty($merOrderNo)||empty($errorCode)||empty($errorMsg)||empty($merRepData)){
	EdjLog::error("orderResult something empty ".serialize($orderResult));
	return;
}
if($errorCode!='000000'){
	EdjLog::info("errorCode is $errorCode errorMsg is $errorMsg order_id is $merOrderNo result is ".serialize($orderResult));
	return;
}
$order_id=$merOrderNo;
if(false==RPay::model()->addOrderIdIfNotExist($order_id)){
	EdjLog::info("order_id $order_id exists");
	return;
}
$ret=BUpmpPayOrder::model()->splitOrderId($order_id);
if(empty($ret)){
	EdjLog::info("params fail ret order_id $order_id empty request ".serialize($_REQUEST));
	return;
}
$qn='';
$resp_msg_second=$merRepData;
if($ret['port']==1){
	$pay_order = CarPayDriverOrder::model()->find('order_id=:order_id',array(':order_id'=>$order_id));
	if($pay_order && is_object($pay_order) && $pay_order->trans_status==BUpmpPayOrder::TRANS_STATUS_FRIST){
		EdjLog::info("params  ".serialize($orderResult)." is ok order_id is $order_id");
                EdjLog::info("order_id $order_id pp_id $qn $resp_msg_second");
		BUpmpPayOrder::model()->updateDriverOrder($order_id,$qn,$resp_msg_second);
		echo $merRepData;
		return;
	}
	else{
		EdjLog::info("params ".serialize($orderResult)." failed for ".serialize($pay_order));
                return;
        }
}
else if($ret['port']==2){
	$pay_order = CarPayOrder::model()->find('order_id=:order_id',array(':order_id'=>$order_id));
        if($pay_order && is_object($pay_order) && $pay_order->trans_status==BUpmpPayOrder::TRANS_STATUS_FRIST){
		EdjLog::info("params ".serialize($orderResult)." is ok order_id $order_id");
                EdjLog::info("order_id $order_id pp_id $qn $resp_msg_second");
                BUpmpPayOrder::model()->updateOrder($order_id,$qn,$resp_msg_second);
                echo $merRepData;
		return;
        }
        else{
		EdjLog::info("params ".serialize($orderResult)." failed for ".serialize($pay_order));
                return;
       }
}
else{
	EdjLog::info('ret not ok '.serialize($ret));
        return;
}
EdjLog::error("should never go here");
return;
