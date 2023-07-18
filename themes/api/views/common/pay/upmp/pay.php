<?php
/**
 * 手机支付订单接口，生成本地订单，调用银联接口获取交易流水号
 * User: Bidong
 * Date: 13-4-24
 * To change this template use File | Settings | File Templates.
 */

//引入银联API相关文件
Yii::import('application.vendors.upmp.*');
//引入业务逻辑model
Yii::import('application.models.pay.*');

$ret = array();
$token = $fee = $payChannel = $validate = '';
//$ret = array(
//	'code' => 2,
//	'message' => '暂时不能充值,请稍等');
//echo json_encode($ret);return;
if ($params) {
EdjLog::info("pay params is ".serialize($params));
    $token = $params['token']; //用户token
    $fee = $params['fee']; //充值金额
    $payChannel = $params['channel']; //支付通道
    //交易类型，默认 01 ，留作扩展使用
    $trans_type=$params['trans_type'];
    $port = $params['port']; //调用端  1、司机端 2、客户端
    if(empty($token)){
        $ret = array(
	    'code' => 1,
            'message' => 'token为空',
        );
        echo json_encode($ret);
	EdjLog::info("ret is ".serialize($ret));
        return;
    }
    if($port!=1&&$port!=2){
	$ret = array(
        'code' => 2,
        'message' => 'port不在可接受范围内');
	EdjLog::info("ret is ".serialize($ret));
	echo json_encode($ret);
        return;
    }
    //兼容客户端3.2.0版本
    if($payChannel==0 || $payChannel==1){
        $payChannel=1;
    }

    $order_des = '订单摘要';
    switch ($port) {
        case 1:
            //验证司机token
            $validate = DriverStatus::model()->getByToken($token);
            if (empty($validate) || $validate->token === null || $validate->token !== $token) {
                $ret = array('code' => 1, 'message' => 'token验证失败');
                echo json_encode($ret);
		EdjLog::info("ret is ".serialize($ret));
                return;
            }
            if($validate->phone===null||strlen(trim($validate->phone))<7){
                $ret=array('code'=>3,'message'=>'您没有工作电话？');
                echo json_encode($ret);
                return;
            }
            $phone=trim($validate->phone);
            $order_des = '司机充值';
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
		EdjLog::info("ret is ".serialize($ret));
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
		    EdjLog::info("ret is ".serialize($ret));
                    return;
                    }
                }
            }
            $order_des = '用户充值';
            break;
        default:
            break;
    }


    //验证用户通过，提交订单
    $orderTime = date("YmdHis"); //交易开始日期时间
    //创建本地交易订单号
    $trans_order_id = BUpmpPayOrder::model()->newMakeOrderNo(array('channel'=>$payChannel,'port'=>$port,'phone'=>$phone));
    EdjLog::info("gen order id $trans_order_id");
    $user_id='';
    if($port==1){
	$user_id=$validate->driver_id;
    }
    else if($port==2){
	$user_id = trim($validate['phone']);
    }
    $reserved=json_encode(array('p'=>$port,'u'=>$user_id));
    //调用银联接口推送订单
    $upmpPay = new UpmpPay();
    $result = $upmpPay->purchase($fee, $orderTime, $trans_order_id, $order_des, $reserved);
    EdjLog::info("purchase result is ".serialize($result));
    //$upmpPay-> query('01','20131225111144','201312251111441825480');
    $succ = $result['succ'];
    $resp = $result['data'];

    //无论生成订单是否成功，都保存请求返回状态
    $respCode = isset($resp['respCode']) ? $resp['respCode'] : 0;
    $resp_tn = isset($resp['tn']) ? $resp['tn'] : 0;
    $resp_msg = json_encode($resp);
    if($port==1){
	EdjLog::info("driver pay params is user_id=$user_id trans_order_id=$trans_order_id resp_tn=$resp_tn payChannel=$payChannel".
	" fee=$fee orderTime=$orderTime resp_msg=$resp_msg");
        $result = BUpmpPayOrder::model()->insertDriverOrder($user_id, $trans_order_id, $resp_tn, $payChannel, $fee, $orderTime, $resp_msg);
	EdjLog::info("driver insert result is $result");
    }
    if($port==2){
	EdjLog::info("user pay params is user_id=$user_id trans_order_id=$trans_order_id resp_tn=$resp_tn payChannel=$payChannel".
        " fee=$fee orderTime=$orderTime resp_msg=$resp_msg");
        $result = BUpmpPayOrder::model()->insertOrder($user_id, $trans_order_id, $resp_tn, $payChannel, $fee, $orderTime, $resp_msg);
	EdjLog::info("user insert result is $result");
    }
    if(!$result){
        $ret = array(
            'code' => 2,
            'data' => $result,
            'message' => '入库失败');
        echo json_encode($ret);
	EdjLog::info("ret is ".serialize($ret));
        return;
    }


    //请求成功
    if ($succ) {
        // 服务器应答签名验证成功
        if ($resp_tn && $respCode = '00') {
            $ret = array(
                'code' => 0,
                'data' => $resp_tn,
                'message' => '提交订单成功');
        } else {
            $ret = array(
                'code' => 2,
                'data' => '',//伪造resp_tn，绝不允许客户端在失败时交易
                'message' => 'error');
            EdjLog::info('error：'.$resp['respMsg']);
        }

    } else {
        // 服务器应答签名验证失败
        $ret = array(
            'code' => 2,
            'data' => '',//伪造resp_tn,绝不允许客户端在失败时交易 
            'message' => 'error');
        EdjLog::info('error：'.$resp['respMsg']);
    }
} else {
    $ret = array(
        'code' => 2,
        'message' => '参数不全');
}
EdjLog::info("ret is ".serialize($ret));
echo json_encode($ret);






