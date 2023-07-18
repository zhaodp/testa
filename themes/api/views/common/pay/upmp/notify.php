<?php
/**
 * 银联回调接口.
 * User: Bidong
 * Date: 13-4-24
 * Time: 下午4:48
 * To change this template use File | Settings | File Templates.
 */
//引入银联API相关文件
Yii::import('application.vendors.upmp.*');
//引入业务逻辑model
Yii::import('application.models.pay.*');

//去掉API验证参数，保障银联验证成功
$data=$_POST;
unset($data['appkey']);
unset($data['method']);
unset($data['timestamp']);
unset($data['ver']);
unset($data['sig']);

$upmpPay = new UpmpPay();
$result=$upmpPay->notify($data);

//版本号	 version 1.0.0
//签名方法	signMethod
//签名信息	signature
//交易类型	transType
//商户代码	merId
//交易状态	transStatus    00:交易成功结束
//响应码	     respCode
//查询流水号	 qn
//商户订单号	 orderNumber



if($result['succ']){
     


    $qn=$result['data']['qn'];
    $order_id=$result['data']['orderNumber'];
    $resp_msg=json_encode($result['data']);
//$qn='aa';
//$resp_msg='nono';
//$order_id='201403051131502902740';
//$result['data']['reqReserved']=json_encode(array('p'=>1,'u'=>'3223abBJ'));
    //如果没有，就加它，返回true，否则有的话就不管它，返回false，打印log,应当用redis只用一行语句来保证
    EdjLog::info("======success  order_id=".$order_id);
    //echo "======success  order_id=".$order_id."\n";
    if(false==RPay::model()->addOrderIdIfNotExist($order_id)){
        //should print log message here
	EdjLog::info("======fail order already exist in redis  order_id=".$order_id);
	//echo "======fail order already exist in redis  order_id=".$order_id."\n";
    }
    else{
    //接收支付交易回调
    if($result['transType']==UpmpPay::TRANS_TYPE_PUSH){
	EdjLog::info("order id  $order_id attr ".$result['data']['reqReserved']);
	$arrAttrInfo=json_decode($result['data']['reqReserved'],true);
	$port=$arrAttrInfo['p'];
	$user_id=$arrAttrInfo['u'];
        //司机充值回调
        if($port==1){
            //更新银联交易流水//记录交易记录-司机
            $ret = BUpmpPayOrder::model()->updateDriverOrder($order_id,$qn,$resp_msg);
	    
        }
        //用户充值回调
        if($port==2){
            //更新银联交易流水//记录交易记录-用户
            $ret = BUpmpPayOrder::model()->updateOrder($order_id,$qn,$resp_msg);
        }

	if($ret){
		EdjLog::info("============交易成功 orderId=".$order_id);
		//echo "============交易成功 orderId=".$order_id."\n";
	}else{

		EdjLog::info("============交易失败 orderId=".$order_id);
		//echo "============交易失败 orderId=".$order_id."\n";
	}
	
    }
}


}else{
	EdjLog::info("======fail  order_id=".$order_id);
	//echo "======fail  order_id=".$order_id."\n";
	
}


