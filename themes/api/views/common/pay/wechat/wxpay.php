<?php
/**
 * 手机支付订单接口，生成本地订单，调用微信支付接口
 * Created by PhpStorm.
 * User: jack
 * Date: 2015/1/14
 * Time: 14:17
 */

Yii::import('application.models.pay.*');
Yii::import('application.models.pay.wxUtils.*');
$ret = array();
$token = $fee = $payChannel = $validate = '';
$port=0;
if ($params) {
    EdjLog::info('----WXPay1-- params is '.json_encode($params));
    $token = isset($params['token']) ? $params['token'] : 0; //用户token
    $fee = isset($params['fee']) ? $params['fee'] : 0; //充值金额
    $payChannel = isset($params['channel']) ? $params['channel'] : 0; //支付通道
    $port = isset($params['port']) ? $params['port'] : 0; //调用端  1、司机端 2、客户端
    $spbill_create_ip = isset($params['spbill_create_ip']) ? $params['spbill_create_ip'] : '192.168.0.1'; //支付机器的ip
    if(empty($token)||empty($fee)||empty($payChannel)||empty($port)||empty($spbill_create_ip)||$fee <= 0){
        $ret=array('code'=>6,'message'=>'您有必选项为空或金额值不对');
        echo json_encode($ret);
        return;
    }
    if($payChannel!=EmployeeAccount::CHANNEL_WX_RECHARGE && $payChannel!=EmployeeAccount::CHANNEL_WX_PAY){
        $ret=array('code'=>2,'message'=>'您不是在使用微信渠道');
        echo json_encode($ret);
        return;
    }
    $phone='';
    switch ($port) {
        case 1://验证司机token
            $validate = DriverStatus::model()->getByToken($token);
            if(empty($validate)){
                $ret = array('code' => 1, 'message' => 'token验证失败');
                echo json_encode($ret);
                return;
            }else  if (empty($validate->token) || $validate->token !== $token) {
                EdjLog::info('----WXPay2--token '.$token.' get no validate driver params ');
                $ret = array('code' => 1, 'message' => 'token验证失败');
                echo json_encode($ret);
                return;
            }else if(empty($validate->phone)||strlen(trim($validate->phone))<7){
                EdjLog::warning('----WXPay3--user with no phone params '.serialize($validate));
                $ret=array('code'=>3,'message'=>'请先联系分公司完善手机号码信息.');
                echo json_encode($ret);
                return;
            }
            $phone=trim($validate->phone);//司机手机号
            break;
        case 2: //验证用户token
            $validate = CustomerToken::model()->validateToken($token);
            if (!$validate) {
                EdjLog::info('----WXPay4--token '.$token.' get no validate user params ');
                $ret = array(
                    'code' => 1,
                    'message' => 'token验证失败',
                );
                echo json_encode($ret);
                return;
            }else{
                $phone = trim($validate['phone']);//客户电话  不能充值也不能支付么？
                if(!empty($phone)){
                    $data=VipPhone::model()->getPrimary($phone);
                    //判断vip副卡
                    if($data && $data['type']==VipPhone::TYPE_VICE){
                        EdjLog::warning('----WXPay5--vip 副卡用户不能充值  data is '.serialize($data));
                        $ret = array(
                            'code' => 3,
                            'message' => 'VIP副卡用户不支持充值支付功能',
                        );
                        echo json_encode($ret);
                        return;
                    }
                }
            }
            break;
        default:
            EdjLog::info("----WXPay6--port is $port and invalid\n");
            break;
    }
    //token验证通过，提交订单
    $orderTime = date("YmdHis"); //交易开始日期时间
    $arrMakeOrderIdParams=array('channel'=>$payChannel,'port'=>$port,'phone'=>$phone);
    $order_id = BUpmpPayOrder::model()->newMakeOrderNo($arrMakeOrderIdParams); //创建本地交易订单号
    if(empty($order_id)){
        EdjLog::info('----WXPay7--orderTime: '.$orderTime.'-- order id make fail order_id is '.$order_id.' arrMakeOrderIdParams is '.serialize($arrMakeOrderIdParams));
        $ret=array('code'=>4,'message'=>'系统生成订单出错，请联系edaijia客服人员');
        echo json_encode($ret);
        return;
    }
    EdjLog::info("----WXPay8--order_id: $order_id -- params is ".serialize($arrMakeOrderIdParams));

    $user_id='';//用户或司机id
    if($port==1){
        $user_id = $validate->driver_id;//司机用司机工号
        EdjLog::info("----WXPay9--driver pay params is user_id=$user_id order_id=$order_id payChannel=$payChannel".
            " fee=$fee orderTime=$orderTime ");
        $result = BUpmpPayOrder::model()->insertDriverOrder($user_id, $order_id, '', $payChannel, $fee, $orderTime, '');
        EdjLog::info("----WXPay10--driver insert result is $result");
    }
    if($port==2){
        $user_id = trim($phone);//客户用客户的手机号
        EdjLog::info("----WXPay11--user pay params is user_id=$user_id order_id=$order_id payChannel=$payChannel".
            " fee=$fee orderTime=$orderTime ");
        $result = BUpmpPayOrder::model()->insertOrder($user_id, $order_id, '', $payChannel, $fee, $orderTime, '');
        EdjLog::info("----WXPay12--user insert result is $result");
    }
    if(!$result){
        $ret = array(
            'code' => 4,
            'message' => '系统入库失败，请联系客服人员');
        echo json_encode($ret);
        return;
    }
    sleep(1);////////////////////////////
    $intSleepCnt=0;
    if($port==1){
        //保证读写一致才给客户端发送反馈，否则干脆不要发了
        $pay_order = CarPayDriverOrder::model()->find('order_id=:order_id',array(':order_id'=>$order_id));
        while(!$pay_order || !is_object($pay_order)){
            if($intSleepCnt>3){
                $ret=array('code'=>5,'message'=>'后端连接超时，请联系客服');
                echo json_encode($ret);
                return;
            }
            ++$intSleepCnt;
            sleep(1);
            $pay_order = CarPayDriverOrder::model()->find('order_id=:order_id',array(':order_id'=>$order_id));
        }
    } else if($port==2){
        $pay_order = CarPayOrder::model()->find('order_id=:order_id',array(':order_id'=>$order_id));
        while(!$pay_order||!is_object($pay_order)){
            if($intSleepCnt>3){
                $ret=array('code'=>5,'message'=>'后端连接超时，请联系客服');
                echo json_encode($ret);
                return;
            }
            ++$intSleepCnt;
            sleep(1);
            $pay_order = CarPayOrder::model()->find('order_id=:order_id',array(':order_id'=>$order_id));
        }
    }

    /**--以下代码为跟微信服务器交互-
    1.app请求订单
     * 2.获取access_token
     * 3.生成预支付订单package包添加签名
     * 4.提交预支付订单，获取prepayid  再次签名
     * 1.1--返回app请求的参数
     */
//获取token值
    $Token = '';
    $reqHandler = new RequestHandler();
    $reqHandler->init(WxPayConfig::APP_ID, WxPayConfig::APP_SECRET, WxPayConfig::PARTNER_KEY, WxPayConfig::APP_KEY);
    $isFetchToken = WxToken::model()->isFetchAccessToken(time(),WxToken::CHANNEL_WX);
    if($isFetchToken){
        //超过一个小时则重新去获取access_token
        $i = 0;
        while(empty($Token) && $i < 5){//循环获取
            $Token= $reqHandler->GetToken();
            $i++;
        }
        if(empty($Token)){
            $tokenModel = WxToken::model()->getAccessTokenByChannel(WxToken::CHANNEL_WX);
            $Token = $tokenModel ? $tokenModel->access_token : '';
        }else{
            //如果token为空就不存数据库 防止腾讯返回的token为空
            $ret = WxToken::model()->updateAccessTokenByChannel(WxToken::CHANNEL_WX,$Token);//将最新的access_token更新到表中
        }
    }else{
        //没有过期则用原来的access_token 但是需要考虑数据库是否是空的access_token
        $tokenModel = WxToken::model()->getAccessTokenByChannel(WxToken::CHANNEL_WX);
        $Token = $tokenModel ? $tokenModel->access_token : '';
        if(empty($Token)){
            $i = 0;
            while(empty($Token) && $i < 5){//循环获取
                $Token= $reqHandler->GetToken();
                $i++;
            }
            $ret = WxToken::model()->updateAccessTokenByChannel(WxToken::CHANNEL_WX,$Token);//将最新的access_token更新到表中
        }
    }

    if ( $Token !='' ){
        //生成预支付单
        //设置packet支付参数
        $reqHandler->Token = $Token;//更新token
        $packageParams =array();
        $notify_url = 'http://'.$_SERVER['HTTP_HOST'].'/wxnotify';
        $packageParams['bank_type']		= 'WX';	            //支付类型
        $packageParams['body']			= '微信客户支付';					//商品描述
        $packageParams['fee_type']		= '1';				//银行币种
        $packageParams['input_charset']	= 'GBK';		    //字符集
        $packageParams['notify_url']	= $notify_url;//WxPayConfig::notify_url;	    //通知地址
        $packageParams['out_trade_no']	= $order_id;		        //商户订单号
        $packageParams['partner']		= WxPayConfig::PARTNER;		        //设置商户号
        $packageParams['total_fee']		= $fee;			//---商品总金额,以分为单位
        $packageParams['spbill_create_ip']= $spbill_create_ip;  //支付机器IP 由手机端那边传过来
        //获取package包
        $package= $reqHandler->genPackage($packageParams);
        $time_stamp = time();
        $nonce_str = md5(rand());
        //设置支付参数
        $signParams =array();
        $signParams['appid']	=WxPayConfig::APP_ID;//开放平台账户的唯一标识
        $signParams['appkey']	=WxPayConfig::APP_KEY;
        $signParams['noncestr']	=$nonce_str;//32 位内的随机串，防重发
        $signParams['package']	=$package;//订单详情
        $signParams['timestamp']=strval($time_stamp);
        $signParams['traceid']	= $order_id;//'mytraceid_001'可用于订单的查询与跟踪，建议根据支付用户信息生成此id;
        //生成支付签名
        $sign = $reqHandler->createSHA1Sign($signParams);
        //增加非参与签名的额外参数
        $signParams['sign_method']		='sha1';//密方式，默认为sha1
        $signParams['app_signature']	=$sign;//签名
        //剔除appkey
        unset($signParams['appkey']);
        //获取prepayid
        $prepayid=$reqHandler->sendPrepay($signParams);

        if ($prepayid != null) {
            $pack	= 'Sign=WXPay';//此处应置为微信APP 支付接口文档Sign=WXPay
            //输出参数列表
            $prePayParams =array();
            $prePayParams['appid']		=WxPayConfig::APP_ID;
            $prePayParams['appkey']		=WxPayConfig::APP_KEY;
            $prePayParams['noncestr']	=$nonce_str;
            $prePayParams['package']	=$pack;
            $prePayParams['partnerid']	=WxPayConfig::PARTNER;
            $prePayParams['prepayid']	=$prepayid;
            $prePayParams['timestamp']	=$time_stamp;
            //添加prepayid再次签名
            $sign=$reqHandler->createSHA1Sign($prePayParams);

            $retParams = array();
            $sucCode = 0;
            $retParams['appid']=WxPayConfig::APP_ID;
            $retParams['noncestr']=$nonce_str;
            $retParams['package']=$pack;
            $retParams['prepayid']=$prepayid;
            $retParams['partnerid']=WxPayConfig::PARTNER;//该参数在demo里面没有
            $retParams['timestamp']=$time_stamp;
            $retParams['sign']=$sign;
            $ret = array('code' => $sucCode ,
                'data' => $retParams ,
                'message' => 'ok'
            );
            EdjLog::info('----WXPay15 To clients CANSHU: --------'.json_encode($ret));
            echo json_encode($ret); return;
        }else{
            $ret['code']=-2;
            $ret['message']='错误：获取prepayId失败';
            echo json_encode($ret); return;
        }
    }else{
        $ret['code']=-1;
        $ret['message']='错误：您的网络暂时获取不到支付凭证请暂时更换支付方式';
        echo json_encode($ret); return;
    }
    echo json_encode($arrRet);
    return;
}else {
    $ret = array(
        'code' => 6,
        'message' => '参数不全');
    EdjLog::info('----WXPay13--params not enough --------');
    echo json_encode($ret);return;
}