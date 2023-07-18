<?php
/**
 * 手机支付订单接口，生成本地订单，调用银联接口获取交易流水号
 * User: Bidong
 * Date: 13-4-24
 * To change this template use File | Settings | File Templates.
 */

//引入银联API相关文件
Yii::import('application.vendors.*');
require_once('upmp/conf/upmp_config.php');
require_once('upmp/lib/upmp_service.php');
$pay_params = Yii::app()->params['payment']['unionPayConfig'];

$token = $params['token']; //用户token
$fee = $params['fee']; //充值金额
$payChannel = $params['channel']; //支付通道
//验证用户
$validate = CustomerToken::model()->validateToken($token);
$trans_orderNo = TradeOrder::model()->createOrderNo(); //创建本地交易订单号

if ($validate) {
    /*********银联支付接口*************/
    if($pay_params){
        UpmpService::init($pay_params); //初始化配置
        $orderTime=date("YmdHis");  //交易开始日期时间

        //需要填入的部分
        $req['version'] =upmp_config::$version;  // 版本号
        $req['charset'] = upmp_config::$charset; // 字符编码 全大写
        $req['signMethod']=upmp_config::$sign_method; //签名方法
        $req['signature']=$pay_params['sign_msg'];   //签名信息
        $req['transType'] = "01"; // 交易类型  01 消费； 02 预授权
        $req['merId'] = $pay_params['mer_id']; // 商户代码 定长15位数字
        $req['backEndUrl'] = $pay_params['mer_back_end_url']; // 通知URL
        //$req['frontEndUrl'] = $pay_params['mer_front_end_url']; // 前台通知URL(可选)
        //$req['orderDescription'] = "E代驾充值"; // 订单描述(可选)
        $req['orderTime'] = $orderTime; // 交易开始日期时间yyyyMMddHHmmss
        //$req['orderTimeout'] = ""; // 订单超时时间(可选)
        $req['orderNumber'] = $trans_orderNo; // 订单号
        $req['orderAmount'] = $fee*100; // 订单金额,单位为分
        $req['orderCurrency'] = "156"; // 交易币种(可选)

        // 保留域填充方法
        $merReserved['ver']= "3";
        $merReserved['method']= "customer.account.upmpnotify";
        $merReserved['appkey']='20000001';
        $merReserved['sig']='4269b326cd65174dca9d039a618e6841';
        $merReserved['timestamp']=date('Y-m-d H:i');
        $req['merReserved']= UpmpService::buildReserved($merReserved); // 商户保留域(可选)

        $resp = array();
        $validResp = UpmpService::trade($req,$resp);
        /************银联支付接口 END***********************/
        // 商户的业务逻辑
        if ($validResp) {
            // 银联服务器应答签名验证成功
            //print_r($resp); //银行返回结果，是否成功和交易流水号

            $transactionId=$resp['tn'];  //交易流水号
            if($transactionId && $resp['respCode']='00'){
                //生成支付订单
                //后期加上 orderTime 订单交易时间，已备日后交易查询 撤销使用
                $trade_order = TradeOrder::model()->orderInsert($validate->phone,$fee,$transactionId,$orderTime,$trans_orderNo,TradeOrder::CHANNELUNIONPAY);
                if($trade_order){
                    // 返回银联交易流水号
                    $ret = array(
                        'code' => 0,
                        'message' =>$transactionId);
                    echo json_encode($ret);
                }else{
                    $ret = array(
                        'code' => 1,
                        'message' => '创建订单失败');
                    echo json_encode($ret);
                }
            }
        } else {
            // 服务器应答签名验证失败
            // print_r($resp);
            $ret = array(
                'code' => 1,
                'message' => ' 服务器应答签名验证失败');
            echo json_encode($ret);
        }
    }
} else {
    $ret = array(
        'code' => 2,
        'message' => 'token已失效请重新进行预注册');
    echo json_encode($ret);
}