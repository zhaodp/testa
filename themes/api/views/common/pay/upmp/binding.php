<?php
/**
 * 手机支付 绑定银行卡
 * User: Bidong
 * Date: 13-4-24
 * To change this template use File | Settings | File Templates.
 */

//引入银联API相关文件
Yii::import('application.vendors.upmp.*');
//引入业务逻辑model
Yii::import('application.models.pay.*');

$ret = array();
$token = $fee = $payChannel = '';

if ($params) {
    $token = $params['token']; //用户token

    //验证用户
    $validate = CustomerToken::model()->validateToken($token);
    //验证用户通过，提交订单
    if ($validate){
        $orderTime = date("YmdHis"); //交易开始日期时间
        //创建本地交易订单号
        $trans_order_id = BUpmpPayTrade::model()->makeOrderNo();
        $order_des='用户充值';
        //调用银联接口推送订单
        //账单类型：12 账单号码：746281 地区代码：0000 附加地区代码：0000
        $quickPay=new UpmpQuickPay();
        $bill_type=12;
        $bill_no=746281;
        $valid_month=1; //单位月份
        $result = $quickPay->binding($orderTime,$trans_order_id,$bill_type,$bill_no,$valid_month);

        $succ = $result['succ'];
        $resp = $result['data'];

        var_dump($result);

//        //无论生成订单是否成功，都保存请求返回状态
//        $user_id = '';
//        $resp_msg = '订单描述';
//        $respCode = isset($resp['respCode']) ? $resp['respCode'] : 0;
//        $resp_tn = isset($resp['tn']) ? $resp['tn'] : 0;
//        $resp_msg = json_encode($resp);
//        $result = BUpmpPayTrade::model()->insertOrder($user_id, $trans_order_id, $resp_tn, $payChannel, $fee, $orderTime, $resp_msg);

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
                    'code' => 1,
                    'data' => $resp_tn,
                    'message' => $resp['respMsg']);
            }

        } else {
            // 服务器应答签名验证失败
            $ret = array(
                'code' => 1,
                'data' => $resp_tn,
                'message' => $resp['respMsg']);
        }

    }
        else {
            $ret = array(
                'code' => 2,
                'message' => 'token已失效请重新进行预注册');
        }
} else {
    $ret = array(
        'code' => 2,
        'message' => '参数不全');
}

echo json_encode($ret);






