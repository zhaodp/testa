<?php
/**
 * 手机支付订单接口，生成本地订单，调用银联接口获取交易流水号
 * User: Bidong
 * Date: 13-4-24
 * To change this template use File | Settings | File Templates.
 */

//引入支付宝API相关文件
Yii::import('application.vendors.alipay.lib.*');
//引入业务逻辑model
Yii::import('application.models.pay.*');
require_once('alipay_rsa.function.php');
$ret = array();
$token = $fee = $payChannel = $validate = '';
$port=0;
//$ret = array(
//	'code' => 2,
//	'message' => '暂时不能充值,请稍等');
//echo json_encode($ret);return;
if ($params) {
    EdjLog::info('alipay params is '.serialize($params));
    $token = $params['token']; //用户token
    $fee = $params['fee']; //充值金额
    $payChannel = $params['channel']; //支付通道
    //交易类型，默认 01 ，留作扩展使用
    //$trans_type=$params['trans_type'];
    $port = $params['port']; //调用端  1、司机端 2、客户端
    $source = isset($params['source'])? $params['source']:0; //订单来源  0：代驾业务(默认)，1：洗车业务
    $callback_url = isset($params['callback_url'])? $params['callback_url']:'';
    $order_number = isset($params['order_number'])? $params['order_number']:'';
    $callback_url = str_replace(',',"&",$callback_url) ;
    if(empty($token)||empty($fee)||empty($payChannel)||empty($port)){
        $ret=array('code'=>1,'message'=>'您有必选项为空');
        echo json_encode($ret);
        return;
    }
    if($fee<0.0){
        $ret=array('code'=>1,'message'=>'支付宝不能支付负数');
        echo json_encode($ret);
        return;
    }
    if($payChannel!=5 && $payChannel!=26){//硬编码
        $ret=array('code'=>1,'message'=>'您不是在用支付宝');
        echo json_encode($ret);
        return;
    }
    $phone='';

    //修饰一下长的订单号
    if (strlen($order_number) > 11 && is_numeric($order_number)) {
        //获取数据库中的order_id
        $order_number = ROrder::model()->getOrder($order_number , 'order_id');
    }

    //洗车客户单跟代驾客户端的验证方式一样
    switch ($port) {
        case 1:
            //验证司机token
            $validate = DriverStatus::model()->getByToken($token);
            if (empty($validate) || $validate->token === null || $validate->token !== $token) {
                $ret = array('code' => 1, 'message' => 'token验证失败');
                echo json_encode($ret);
                return;
            }
	    if($validate->phone===null||strlen(trim($validate->phone))<7){
		$ret=array('code'=>3,'message'=>'请先联系分公司完善手机号码信息.');
		echo json_encode($ret);
		return;
	    }
	    $phone=trim($validate->phone);
            break;
        case 2:
            //验证用户
            $validate = CustomerToken::model()->validateToken($token);
            if (!$validate) {
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
        //洗车消费，代驾消费等用户订单做同样处理，用source进行标记
        $result = BUpmpPayOrder::model()->insertOrder($user_id, $order_id, '', $payChannel, $fee, $orderTime, '',
                    BUpmpPayOrder::TRANS_TYPE_PUSH,$source , $callback_url , $order_number);
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
    else if($port==2 ){
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
	$notify_url='http://'.$_SERVER['HTTP_HOST'].'/rest/?ver=3&method=common.pay.ali.alinotify';
    //arrSign 中的数据必须全部为字符串
    $arrSign = array(
        //'code'=>0,
        //'message'=>'success',
        'service'=>'mobile.securitypay.pay',
        'partner'=>'2088411863680533',
        '_input_charset'=>'utf-8',
        //'sign_type'=>'RSA',
        //'sign'=>'',
        'notify_url'=>urlencode($notify_url),
        'out_trade_no'=>$order_id,
        'subject'=>$user_id,
        'payment_type'=>'1',//消费类型
        'seller_id'=>'app@edaijia-inc.cn',
        'total_fee'=>(string)round($fee/100.0,2),
        'body'=>'edaijia',
        'it_b_pay'=>'1d',
        //'show_url'=>'m.alipay.com',
        //'return_url'=>'m.alipay.com',
    );

    $prestr='';
    foreach($arrSign as $key=>$value){
	$prestr=$prestr."$key=\"$value\"&";
    }
    $strData='';
    foreach($arrSign as $key=>$value){
	$strData=$strData."$key=\"".$value."\"&";
    }
    $prestr=substr($prestr,0,strlen($prestr)-1);
EdjLog::info('prestr is '.$prestr);
    $sign=rsaSign($prestr,'/etc/rsa_pkcs_private_key.pem');
    $arrSign['sign_type']='RSA';
    $arrSign['sign']=urlencode($sign);
    //$strData=$prestr.'&sign_type="RSA"&sign="'.$arrSign['sign'].'"';
    $strData=$strData."sign=\"".urlencode($sign)."\"&sign_type=\"RSA\"";
    $arrRet=array(
	'code'=>0,
	'message'=>'success',
	'data'=>$strData,
    );
    //EdjLog::info('success params '.serialize($params)."\n".' ret is '.serialize($arrRet));
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






