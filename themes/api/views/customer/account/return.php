<?php
/* * 
 * 功能：支付宝页面跳转同步通知页面
 * 版本：3.2
 * 日期：2011-03-25
 * TRADE_FINISHED(表示交易已经成功结束，并不能再对该交易做后续操作);
 * TRADE_SUCCESS(表示交易已经成功结束，可以对该交易做后续操作，如：分润、退款等);
 */
$pay_params = Yii::app()->params['payment']['alipayConfig'];
Yii::import('application.extensions.payment.AlipayService');
//构造即时到帐接口
$alipayService = new AlipayService($pay_params);
//计算得出通知验证结果
$verify_result = $alipayService->verifyReturn();
if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代码
	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
    $out_trade_no	= $_GET['out_trade_no'];	//获取订单号
    $trade_no		= $_GET['trade_no'];		//获取支付宝交易号
    $total_fee		= $_GET['total_fee'];		//获取总价格

    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
		//判断该笔订单是否在商户网站中已经做过处理
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序
			
    	if (TradeOrder::updateOrderByNo($trade_no, $out_trade_no, $total_fee)){
    		//$this->urlredirect();
    	}
    }
    else {
      echo "trade_status=".$_GET['trade_status'];
    }
		
	echo "验证成功<br />";
	echo "trade_no=".$trade_no;
	
	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的verifyReturn函数，比对sign和mysign的值是否相等，或者检查$responseTxt有没有返回true
    echo "验证失败";
}
?>
