<?php
/**
 * 银联回调接口.
 * User: Bidong
 * Date: 13-4-24
 * Time: 下午4:48
 * To change this template use File | Settings | File Templates.
 */
//引入银联API相关文件
Yii::import('application.vendors.alipay.*');
Yii::import('application.vendors.alipay.lib.*');
//引入业务逻辑model
Yii::import('application.models.pay.*');
require_once('alipay.config.php');
EdjLog::info('alinotify post is '.serialize($_POST).' get is '.serialize($_GET));
$_POST=$_REQUEST;//add by liding cai for test
//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();
if($verify_result){
	if(empty($_POST['out_trade_no'])||empty($_POST['trade_status'])){
		EdjLog::info('post params loss '.serialize($_POST));
		echo "fail";
		return;
	}
	if($_POST['trade_status'] == 'TRADE_FINISHED'|| $_POST['trade_status'] == 'TRADE_SUCCESS'){
		$order_id=$_POST['out_trade_no'];
		if(false==RPay::model()->addOrderIdIfNotExist($order_id)){
			EdjLog::info("order_id $order_id exists");
			echo "fail";
			return;
		}
		$alipay_trade_no=isset($_POST['trade_no'])?$_POST['trade_no']:'';
		$ret=BUpmpPayOrder::model()->splitOrderId($order_id);
		if(empty($ret)){
			EdjLog::info("params fail ret empty ".serialize($_POST));
			echo "fail";
			return;
		}
		$arrResp=array();
		if(isset($_POST['notify_time'])){
			$arrResp['notify_time']=$_POST['notify_time'];
		}
		if(isset($_POST['notify_id'])){
			$arrResp['notify_id']=$_POST['notify_id'];
		}
		if(isset($_POST['subject'])){
			$arrResp['subject']=$_POST['subject'];
		}
		if(isset($_POST['buyer_id'])){
			$arrResp['buyer_id']=$_POST['buyer_id'];
		}
		if(isset($_POST['buyer_email'])){
			$arrResp['buyer_email']=$_POST['buyer_email'];
		}
		if(isset($_POST['total_fee'])){
			$arrResp['total_fee']=$_POST['total_fee'];
		}
		if(isset($_POST['gmt_payment'])){
			$arrResp['gmt_payment']=$_POST['gmt_payment'];
		}
		$resp_msg_second=json_encode($arrResp);
		if($ret['port']==1){
			$pay_order = CarPayDriverOrder::model()->find('order_id=:order_id',array(':order_id'=>$order_id));
			if($pay_order && is_object($pay_order) && $pay_order->trans_status==BUpmpPayOrder::TRANS_STATUS_FRIST){
				EdjLog::info("post ".serialize($_POST)." is ok");
				echo "success";
				//fastcgi_finish_request();//我方后台太慢，只能自己慢慢处理了
				BUpmpPayOrder::model()->updateDriverOrder($order_id,$alipay_trade_no,$resp_msg_second);
				return;
			}
			else{
				EdjLog::info("params ".serialize($_POST)." failed for ".serialize($pay_order));
				echo "fail";
				return;
			}
		}
		else if($ret['port']==2 ){
                $pay_order = CarPayOrder::model()->find('order_id=:order_id',array(':order_id'=>$order_id));
                if($pay_order && is_object($pay_order) && $pay_order->trans_status==BUpmpPayOrder::TRANS_STATUS_FRIST){
                    EdjLog::info("post ".serialize($_POST)." is ok");
                    echo "success";
                        //fastcgi_finish_request();//我方后台太慢，只能自己慢慢处理了
                    EdjLog::info("order_id $order_id ali_id $alipay_trade_no $resp_msg_second");
                    if($pay_order['source']==0 ){//E代驾业务，单独处理（按以前流程走）
                        BUpmpPayOrder::model()->updateOrder($order_id,$alipay_trade_no,$resp_msg_second);
                        return;
                    }else {
                        //所有非代驾的用户业务，统一在这里处理
                        BUpmpPayOrder::model()->updateAllOrder($order_id,$alipay_trade_no,$resp_msg_second);
                        return ;
                    }
                }
                else{
                        EdjLog::info("params ".serialize($_POST)." failed for ".serialize($pay_order));
                        echo "fail";
                        return;
                }
		}

		else{
			EdjLog::info('ret not ok '.serialize($ret));
			echo "fail";
			return;
		}
	}
	else{
		EdjLog::info("params fail ".serialize($_POST));
		echo "fail";
		return;
	}	
}
else{
	EdjLog::info('verify_result is '.serialize($verify_result).' params '.serialize($_POST));
	echo "fail";
	return;
}



