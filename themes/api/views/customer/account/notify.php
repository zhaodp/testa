<?php
/* *
 * 功能：支付宝服务器异步通知页面
 * 版本：3.2
 * 日期：2011-03-25
 
 * TRADE_FINISHED(表示交易已经成功结束，并不能再对该交易做后续操作);
 * TRADE_SUCCESS(表示交易已经成功结束，可以对该交易做后续操作，如：分润、退款等);
 */

$pay_params = Yii::app()->params['payment']['alipayConfig'];
Yii::import('application.extensions.payment.AlipayService');
//构造即时到帐接口
$alipayService = new AlipayService($pay_params);
$verify_result = $alipayService->verifyNotify();

if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代
	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
    $out_trade_no	= $_POST['out_trade_no'];	    //获取订单号
    $trade_no		= $_POST['trade_no'];	    	//获取支付宝交易号
    $total_fee		= $_POST['total_fee'];			//获取总价格

    if($_POST['trade_status'] == 'TRADE_FINISHED' || $_POST['trade_status'] == 'TRADE_SUCCESS') {
		//判断该笔订单是否在商户网站中已经做过处理
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序
    	if (TradeOrder::updateOrderByNo($trade_no, $out_trade_no, $total_fee)){
    		//调试用，写文本函数记录程序运行情况是否正常
        	//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");

        	$alipayService->logResult($alipayService->buildRequestParaToString($_POST));
    		echo "success";
    	} else {
    		echo "fail";
    	}
    } else {
		//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
		echo "fail";		//请不要修改或删除
		$alipayService->logResult($alipayService->buildRequestParaToString($_POST));
    }
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    echo "fail";

    //调试用，写文本函数记录程序运行情况是否正常
    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
    $alipayService->logResult($alipayService->buildRequestParaToString($_POST));
}
?>