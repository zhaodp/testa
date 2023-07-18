<?php
/**
 * 手机支付订单接口，生成本地订单，调用pp钱包接口
 * User: Tuanwang,liu
 * Date: 2014-09-01
 * To change this template use File | Settings | File Templates.
 */

//引入业务逻辑model
Yii::import('application.models.pay.*');
$ret = array();
$token = $fee = $payChannel = $validate = '';
$port=0;
//$ret = array(
//	'code' => 2,
//	'message' => '暂时不能充值,请稍等');
//echo json_encode($ret);return;
if ($params) {
    EdjLog::info('pppay params is '.serialize($params));
    $token = $params['token']; //用户token
    $fee = $params['fee']; //充值金额
    $payChannel = $params['channel']; //支付通道
    //交易类型，默认 01 ，留作扩展使用
    //$trans_type=$params['trans_type'];
    $port = $params['port']; //调用端  1、司机端 2、客户端
    if(empty($token)||empty($fee)||empty($payChannel)||empty($port)){
	$ret=array('code'=>2,'message'=>'您有必选项为空');
	echo json_encode($ret);
	return;
    }
    if($fee<0.0){
	$ret=array('code'=>2,'message'=>'pp钱包不能充值或支付负数');
	echo json_encode($ret);
	return;
    }
    if($payChannel!=EmployeeAccount::CHANNEL_PP_RECHARGE && $payChannel!=EmployeeAccount::CHANNEL_PP_PAY){
	$ret=array('code'=>2,'message'=>'您不是在使用pp钱包');
	echo json_encode($ret);
	return;
    }
    $phone='';
    switch ($port) {
        case 1:
            //验证司机token
            $validate = DriverStatus::model()->getByToken($token);
            if (empty($validate) || $validate->token === null || $validate->token !== $token) {
		EdjLog::error('token '.$token.' get no validate driver params '.serialize($params));
                $ret = array('code' => 1, 'message' => 'token验证失败');
                echo json_encode($ret);
                return;
            }
	    if($validate->phone===null||strlen(trim($validate->phone))<7){
		EdjLog::warning('user with no phone params '.serialize($params).' validate '.serialize($validate));
		$ret=array('code'=>3,'message'=>'您没有工作电话？');
		echo json_encode($ret);
		return;
	    }
	    $phone=trim($validate->phone);
            break;
        case 2:
            //验证用户
            $validate = CustomerToken::model()->validateToken($token);
            if (!$validate) {
		EdjLog::error('token '.$token.' get no validate user params '.serialize($params));
                $ret = array(
                    'code' => 1,
                    'message' => 'token验证失败',
                );
                echo json_encode($ret);
                return;
            }else{
                $phone = trim($validate['phone']);
                if(!empty($phone)){
                    $data=VipPhone::model()->getPrimary($phone);
                    //判断vip副卡
                    if($data && $data['type']==VipPhone::TYPE_VICE){
                        EdjLog::warning('vip 副卡用户不能充值  data is '.serialize($data));
                        $ret = array(
                        'code' => 3,
                        'message' => 'VIP副卡用户不支持充值功能',
                    );
                    echo json_encode($ret);
                    return;
                    }
                }
            }
            break;
        default:
	    EdjLog::info("port is $port and invalid");
            break;
    }

    //验证用户通过，提交订单
    $orderTime = date("YmdHis"); //交易开始日期时间
    //创建本地交易订单号
    $arrMakeOrderIdParams=array('channel'=>$payChannel,'port'=>$port,'phone'=>$phone);
    $order_id = BUpmpPayOrder::model()->newMakeOrderNo($arrMakeOrderIdParams);
    if(empty($order_id)){
	EdjLog::info('orderTime '.$orderTime.' order id make fail order_id is '.$order_id.' params is '.serialize($arrMakeOrderIdParams));
	$ret=array('code'=>4,'message'=>'系统生成订单出错，请联系edaijia客服人员');
	echo json_encode($ret);
	return;
    }
   EdjLog::info("order_id $order_id params is ".serialize($arrMakeOrderIdParams)); 

    $user_id='';
    if($port==1){
        $user_id = $validate->driver_id;
	EdjLog::info("driver pay params is user_id=$user_id order_id=$order_id payChannel=$payChannel".
	" fee=$fee orderTime=$orderTime ");
        $result = BUpmpPayOrder::model()->insertDriverOrder($user_id, $order_id, '', $payChannel, $fee, $orderTime, '');
	EdjLog::info("driver insert result is $result");
    }
    if($port==2){
        $user_id = trim($phone);
	EdjLog::info("user pay params is user_id=$user_id order_id=$order_id payChannel=$payChannel".
        " fee=$fee orderTime=$orderTime ");
        $result = BUpmpPayOrder::model()->insertOrder($user_id, $order_id, '', $payChannel, $fee, $orderTime, '');
	EdjLog::info("user insert result is $result");
    }
    if(!$result){
        $ret = array(
            'code' => 2,
            'message' => '系统入库失败，请联系客服人员');
        echo json_encode($ret);
        return;
    }
    sleep(1);
    $intSleepCnt=0;
    if($port==1){
    	//保证读写一致才给客户端发送反馈，否则干脆不要发了
    	$pay_order = CarPayDriverOrder::model()->find('order_id=:order_id',array(':order_id'=>$order_id));
    	while(!$pay_order || !is_object($pay_order)){
		if($intSleepCnt>3){
			$ret=array('code'=>4,'message'=>'后端连接超时，请联系客服');
			echo json_encode($ret);
			return;
		}
		++$intSleepCnt;
		sleep(1);
		$pay_order = CarPayDriverOrder::model()->find('order_id=:order_id',array(':order_id'=>$order_id));
    	}
    }
    else if($port==2){
	$pay_order = CarPayOrder::model()->find('order_id=:order_id',array(':order_id'=>$order_id));
	while(!$pay_order||!is_object($pay_order)){
		if($intSleepCnt>3){
                        $ret=array('code'=>4,'message'=>'后端连接超时，请联系客服');
                        echo json_encode($ret);
                        return;
		}
		++$intSleepCnt;
		sleep(1);
		$pay_order = CarPayOrder::model()->find('order_id=:order_id',array(':order_id'=>$order_id));
	}
    }
    $notify_url='http://'.Yii::app()->params['payment']['ppmoneyConfig']['notify_url'];
    $arrRet=array(
        'code'=>0,
        'message'=>'success',
        'order'=>$order_id,
        'phone'=>$phone,
        'merchantId'=>Yii::app()->params['payment']['ppmoneyConfig']['merchantId'],
        'merchantUserId'=>$phone,
        'payAmt'=>$fee,
        'productId'=>Yii::app()->params['payment']['ppmoneyConfig']['productId'],
        'payMethod'=>Yii::app()->params['payment']['ppmoneyConfig']['payMethod'],
        'notifyUrl'=>$notify_url,
        'orderDesc'=>'edaijia',
        'reserve'=>'edaijia',
    );
    EdjLog::info('ret arrRet is '.serialize($arrRet)); 
    echo json_encode($arrRet);
    return;
} else {
    $ret = array(
        'code' => 2,
        'message' => '参数不全');
    EdjLog::info('params not enough '.serialize($params));
    echo json_encode($ret);
    return;
}






