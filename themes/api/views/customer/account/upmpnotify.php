<?php
/**
 * 银联回调接口.
 * User: Bidong
 * Date: 13-4-24
 * Time: 下午4:48
 * To change this template use File | Settings | File Templates.
 */
//引入银联API相关文件
Yii::import('application.vendors.*');
require_once('upmp/lib/upmp_service.php');

if (UpmpService::verifySignature($_POST)){// 服务器签名验证成功
    //请在这里加上商户的业务逻辑程序代码
    //获取通知返回参数，可参考接口文档中通知参数列表(以下仅供参考)
    $transStatus = $_POST['transStatus'];// 交易状态
    //版本号	 version 1.0.0
    //签名方法	signMethod
    //签名信息	signature
    //交易类型	transType
    //商户代码	merId
    //交易状态	transStatus    00:交易成功结束
    //响应码	     respCode
    //查询流水号	 qn
    //商户订单号	 orderNumber

    if (""!=$transStatus && "00"==$transStatus){
        // 交易处理成功，更新 t_trade_order 添加账户金额
        //更新订单状态并记录用户账户流水和账户总额
        $tradeNo= $_POST['orderNumber'];
        $outTradeNo=$_POST['qn'];
        $fee=$_POST['settleAmount'];
        $status=1;
        $channel=TradeOrder::CHANNELUNIONPAY;
        $ret = TradeOrder::model()->updateOrderStatus($tradeNo,$outTradeNo,$fee/100,$status,$channel);

        echo "success";
    }else{
        echo "交易失败";
    }

}else {// 服务器签名验证失败
    echo "fail";
}