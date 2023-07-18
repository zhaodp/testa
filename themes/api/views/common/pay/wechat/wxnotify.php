<?php
/**微信回调接口
 * Created by PhpStorm.
 * User: jack
 * Date: 2015/1/14
 * Time: 18:21
 */
//---------------------------------------------------------
//即时到帐支付后台回调示例，商户按照此文档进行开发即可
//---------------------------------------------------------

Yii::import('application.models.pay.*');
Yii::import('application.models.pay.wxUtils.*');
/* 创建支付应答对象 */
$resHandler = new ResponseHandler();
	//判断签名
$realip = '';//真是访问端ip
if (isset($_SERVER)){
    if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
        $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
        $realip = $_SERVER["HTTP_CLIENT_IP"];
    } else {
        $realip = $_SERVER["REMOTE_ADDR"];
    }
} else {
    if (getenv("HTTP_X_FORWARDED_FOR")){
        $realip = getenv("HTTP_X_FORWARDED_FOR");
    } else if (getenv("HTTP_CLIENT_IP")) {
        $realip = getenv("HTTP_CLIENT_IP");
    } else {
        $realip = getenv("REMOTE_ADDR");
    }
}

if($resHandler->isTenpaySign() == true) {
        $orderTime =date('Y-m-d H:i:s',time());
        EdjLog::info("访问回调的ip:$realip \n");

    /*****获取token************/
        $Token = '';
        $isFetchToken = WxToken::model()->isFetchAccessToken(time(),WxToken::CHANNEL_WX);
        if($isFetchToken){
            //超过一个小时则重新去获取access_token
            $i = 0;
            while(empty($Token) && $i < 5){//循环获取
                $Token= $reqHandler->GetToken();
                $i++;
            }
            $ret = WxToken::model()->updateAccessTokenByChannel(WxToken::CHANNEL_WX,$Token);//将最新的access_token更新到表中
        }else{
            //没有过期则用原来的access_token
            $tokenModel = WxToken::model()->getAccessTokenByChannel(WxToken::CHANNEL_WX);
            $Token = $tokenModel ? $tokenModel->access_token : '';
        }
    /***********获取腾讯服务的所有ip************/
        $ipUrl = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=".$Token;
        $ipJson  = file_get_contents($ipUrl);
        $ipArr = json_decode($ipJson,true);
        $ipListArr = isset($ipArr['ip_list']) ? $ipArr['ip_list'] : ''; //{ "ip_list":["127.0.0.1","127.0.0.1"]}
        EdjLog::info("腾讯的地址列表:".json_encode($ipListArr,true));
        if(!empty($ipListArr)){
            if(!in_array($realip,$ipListArr)){
                $ret = Sms::SendSMS('18301221389', "$orderTime == 签名验证失败 ip:$realip");
                EdjLog::info("---WXnotify13--$orderTime 签名验证失败 ip:$realip");
                echo  "签名验证失败";return;
            }
        }
        //商户在收到后台通知后根据通知ID向财付通发起验证确认，采用后台系统调用交互模式
        $notify_id = $resHandler->getParameter("notify_id");//通知id
        $out_trade_no = $resHandler->getParameter("out_trade_no");//商户交易单号即我们自己生成的orderid
        $transaction_id = $resHandler->getParameter("transaction_id"); //财付通订单号
        $total_fee = $resHandler->getParameter("total_fee");//商品金额,以分为单位
        $discount = $resHandler->getParameter("discount"); //如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
        $trade_state = $resHandler->getParameter("trade_state"); //支付结果
        EdjLog::info("微信回调返回的参数====notify_id=$notify_id--out_trade_no=$out_trade_no--transaction_id=$transaction_id--total_fee=$total_fee==trade_state=$trade_state");
        //可获取的其他参数还有
        //bank_type			银行类型,默认：BL fee_type			现金支付币种,目前只支持人民币,默认值是1-人民币
        //input_charset		字符编码,取值：GBK、UTF-8，默认：GBK。
        //partner			商户号,由财付通统一分配的10位正整数(120XXXXXXX)号
        //product_fee		物品费用，单位分。如果有值，必须保证transport_fee + product_fee=total_fee
        //sign_type			签名类型，取值：MD5、RSA，默认：MD5
        //time_end			支付完成时间
        //transport_fee		物流费用，单位分，默认0。如果有值，必须保证transport_fee +  product_fee = total_fee

        //判断签名及结果
        if ("0" == $trade_state){
            $order_id = $out_trade_no;//根据交易id分析出端口等信息
            if(false==RPay::model()->addOrderIdIfNotExist($order_id)){
//                $ret = Sms::SendSMS('18301221389', "$orderTime==订单id: $order_id redis读取失败");
                EdjLog::info("---WXnotify3--order_id $order_id not exists");
                echo "fail";return;
            }
            $ret=BUpmpPayOrder::model()->splitOrderId($order_id);//拆解订单号取信息
            if(empty($ret)){
                $ret = Sms::SendSMS('18301221389', "$orderTime==订单id: $order_id 拆解失败");
                EdjLog::info("---WXnotify4--params fail ret order_id $order_id empty request ");
                echo "fail";return;
            }
            if($ret['port']==1){
                //司机端
                $pay_order = CarPayDriverOrder::model()->find('order_id=:order_id',array(':order_id'=>$order_id));
                if($pay_order && is_object($pay_order) && $pay_order->trans_status==BUpmpPayOrder::TRANS_STATUS_FRIST){
                    BUpmpPayOrder::model()->updateDriverOrder($order_id,$transaction_id,$notify_id);/////////////////////////
                    echo "success"; return;
                }else{
                    $ret = Sms::SendSMS('18301221389', "$orderTime=司机订单: $order_id 更新状态失败");
                    EdjLog::info("---WXnotify7--司机订单: $order_id 更新状态失败");
                    echo "fail";return;
                }
            } else if($ret['port']==2){
                //客户端
                $pay_order = CarPayOrder::model()->find('order_id=:order_id',array(':order_id'=>$order_id));
                if($pay_order && is_object($pay_order) && $pay_order->trans_status==BUpmpPayOrder::TRANS_STATUS_FRIST){
                    BUpmpPayOrder::model()->updateOrder($order_id,$transaction_id,$notify_id);
                    echo "success";return;
                }else{
//                    $ret = Sms::SendSMS('18301221389', "$orderTime ==客户订单: $order_id 更新状态失败(可能已经支付完成)");
                    EdjLog::info("---WXnotify8--params $orderTime = 客户订单: $order_id 更新状态失败(可能已经支付完成) ");
                    echo "success";return;
                }
            } else{
                $ret = Sms::SendSMS('18301221389', "$orderTime ==订单: $order_id 端口分解失败");
                EdjLog::info("---WXnotify9-- $orderTime 订单: $order_id 端口分解失败");
                echo "fail";return;
            }
        } else {
            $ret = Sms::SendSMS('18301221389', "$orderTime 微信支付返回交易失败,单号:$out_trade_no");
            EdjLog::info("---WXnotify10--$orderTime 微信支付返回交易失败,单号:$out_trade_no");
            echo "fail";return;
        }
    } else {
        $ret = Sms::SendSMS('18301221389', "$orderTime == 客户签名验证失败");
        EdjLog::info("---WXnotify11--$orderTime 客户签名验证失败");
        echo  "验证签名失败";return;
    }






















