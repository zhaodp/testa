<?php
//获取支付宝支付地址
$token = $params['token'];
$fee = $params['fee'];
$payChannel = $params['channel'];

$validate = CustomerToken::validateToken($token);

if ($validate){

	/*
	<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
	<cupMobile version="1.01" >
		<transaction type="Purchase.MARsp">
			<submitTime>20120109134627</submitTime>
			<order id="12347733">机票</order>
			<transAmount currency="156">000000030231</transAmount>
			<terminal id="01042900"/>
			<merchant name="test" country="156" id="303290047228001"/>
			<resultURL>http://172.17.252.214:10081/result</resultURL>
		</transaction>
		<senderSignature>8129c48c2cbbd5d0b35a3a0fcaf5e4b3321fe62d5f09b5598f2fae243f8669b14019e4933042f24d501d60db6ec1ae6add1ea15014f4dd9eb8eda7b804b2d51cb5db7a95144ff5cf92016b31e15745a2e689be85678bbe27578802db8c0ee86825a1d225ebad2d3e9e4bb17508939030abac06292dff153e181257783412b701</senderSignature>
	</cupMobile>
	*/
	
	$trade_order = TradeOrder::getOrderByAttr($validate->phone, $fee);
	$trade_no = $trade_order->trade_no;
	
	if ($payChannel == TradeOrder::CHANNELUNIONPAY) {
		$pay_params = Yii::app()->params['payment']['unionPayConfig'];
		
		$paramsOrder = array(
				'submitTime'=>9, 
				'order_id'=>15, 
				'transAmount'=>24, 
				'currency'=>25, 
				'terminal_id'=>28, 
				'merchant_id'=>32, 
				'name'=>33, 
				);
		$buildFee = 100 * $fee;
		$orderFee = '00000000000' . $buildFee;
		
		$orderFee = substr($orderFee, -12);
		
		$payArray = array(
				'submitTime'=>date('YmdHis'), 
				'order_id'=>$trade_no, 
				'transAmount'=>$orderFee, 
				'currency'=>$pay_params['currency'], 
				'terminal_id'=>$pay_params['TerminalId'], 
				'merchant_id'=>$pay_params['MerchantId'], 
				'name'=>$pay_params['name'], 
				);
		
		$data = $payArray['submitTime'] . $payArray['order_id'] . $payArray['transAmount'] . $payArray['currency'] . $payArray['terminal_id'] . $payArray['merchant_id'] . $payArray['name'];
		
		$fp = fopen($pay_params['keyPath'], "r");
 
		$priv_key = fread($fp, 8192);
		fclose($fp);
		$pkeyid = openssl_get_privatekey($priv_key);
		 
		openssl_sign($data, $signature, $pkeyid);
		openssl_free_key($pkeyid);
		$len = strlen($signature);
		$str = $signature;
		$hexArray = '0123456789abcdef';
		
		$result = "";
		for($i=0;$i<$len;$i++) {
		    if(ord($str[$i]) >= 128){
		        $byte = ord($str[$i]) - 256;
		    }else{
		        $byte =  ord($str[$i]);
			}
		    $bytes[] = $byte ;
		    $byte = $byte & 0xff;
		    $result .= $hexArray[$byte >> 4];
		    $result .= $hexArray[$byte & 0xf];
		}
		
		$xmlstr = "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>\n<cupMobile version='1.01'></cupMobile>";
		$xml = new SimpleXMLElement($xmlstr);
		$transaction = $xml->addChild('transaction');
		$transaction->addAttribute('type', 'Purchase.MARsp');
		
		$transaction->addChild('submitTime', $payArray['submitTime']);
		
		$order = $transaction->addChild('order', '充值');
		$order->addAttribute('id', $payArray['order_id']);
		
		$transAmount = $transaction->addChild('transAmount', $payArray['transAmount']);
		$transAmount->addAttribute('currency', $payArray['currency']);
		
		$terminal = $transaction->addChild('terminal');
		$terminal->addAttribute('id', $payArray['terminal_id']);
		
		$merchant = $transaction->addChild('merchant');
		$merchant->addAttribute('name', $payArray['name']);
		$merchant->addAttribute('country', '156');
		$merchant->addAttribute('id', $payArray['merchant_id']);
		
		$transaction->addChild('resultURL', 'https://42.121.31.232:8443/v2/index.php?r=pay/ebank');
		
		$xml->addChild('senderSignature', $result);
		
		header("Content-Type: text/xml"); 
		
		echo $xml->asXML();
		die();
		
	} elseif ($payChannel == TradeOrder::CHANNELALIPAY) {
		$pay_params = Yii::app()->params['payment']['alipayConfig'];
		Yii::import('application.extensions.payment.AlipayService');
		
		/**************************请求参数**************************/
		
		//必填参数//
		
		//请与贵网站订单系统中的唯一订单号匹配
		$out_trade_no = $trade_order->trade_no;
		//订单名称，显示在支付宝收银台里的“商品名称”里，显示在支付宝的交易管理的“商品名称”的列表里。
		$subject      = 'E代驾充值';
		//订单描述、订单详细、订单备注，显示在支付宝收银台里的“商品描述”里
		$body         = 'E代驾充值，金额：' . $fee . '元';
		//订单总金额，显示在支付宝收银台里的“应付总额”里
		$total_fee    = $fee;
		
		//扩展功能参数——默认支付方式//
		
		//默认支付方式，取值见“即时到帐接口”技术文档中的请求参数列表
		$paymethod    = '';
		//默认网银代号，代号列表见“即时到帐接口”技术文档“附录”→“银行列表”
		$defaultbank  = '';
		
		//扩展功能参数——防钓鱼//
		
		//防钓鱼时间戳
		$anti_phishing_key  = '';
		//获取客户端的IP地址，建议：编写获取客户端IP地址的程序
		$exter_invoke_ip = '';
		//注意：
		//1.请慎重选择是否开启防钓鱼功能
		//2.exter_invoke_ip、anti_phishing_key一旦被使用过，那么它们就会成为必填参数
		//3.开启防钓鱼功能后，服务器、本机电脑必须支持SSL，请配置好该环境。
		//示例：
		//$exter_invoke_ip = '202.1.1.1';
		//$ali_service_timestamp = new AlipayService($aliapy_config);
		//$anti_phishing_key = $ali_service_timestamp->query_timestamp();//获取防钓鱼时间戳函数
		
		
		//扩展功能参数——其他//
		
		//商品展示地址，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
		$show_url			= 'http://www.xxx.com/order/myorder.php';
		//自定义参数，可存放任何内容（除=、&等特殊字符外），不会显示在页面上
		$extra_common_param = '';
		
		//扩展功能参数——分润(若要使用，请按照注释要求的格式赋值)
		$royalty_type		= "";			//提成类型，该值为固定值：10，不需要修改
		$royalty_parameters	= "";
		//注意：
		//提成信息集，与需要结合商户网站自身情况动态获取每笔交易的各分润收款账号、各分润金额、各分润说明。最多只能设置10条
		//各分润金额的总和须小于等于total_fee
		//提成信息集格式为：收款方Email_1^金额1^备注1|收款方Email_2^金额2^备注2
		//示例：
		//royalty_type 		= "10"
		//royalty_parameters= "111@126.com^0.01^分润备注一|222@126.com^0.01^分润备注二"
		
		/************************************************************/
		
		//构造要请求的参数数组
		$parameter = array(
				"service"			=> "create_direct_pay_by_user",
				"payment_type"		=> "1",
				
				"partner"			=> trim($pay_params['partner']),
				"_input_charset"	=> trim(strtolower($pay_params['input_charset'])),
		        "seller_email"		=> trim($pay_params['seller_email']),
		        "return_url"		=> trim($pay_params['return_url']),
		        "notify_url"		=> trim($pay_params['notify_url']),
				
				"out_trade_no"		=> $out_trade_no,
				"subject"			=> $subject,
				"body"				=> $body,
				"total_fee"			=> $total_fee,
				
				"paymethod"			=> $paymethod,
				"defaultbank"		=> $defaultbank,
				
				"anti_phishing_key"	=> $anti_phishing_key,
				"exter_invoke_ip"	=> $exter_invoke_ip,
				
				"show_url"			=> $show_url,
				"extra_common_param"=> $extra_common_param,
				
				"royalty_type"		=> $royalty_type,
				"royalty_parameters"=> $royalty_parameters
		);
		
		//构造即时到帐接口
		$alipayService = new AlipayService($pay_params);
		
		$url = $alipayService->create_direct_pay_by_user($parameter);
		
		if ($url){
			$ret = array(
				'code'=>0,
				'alipayUrl'=>$url,
				'message'=>'读取成功');
		} else {
			$ret = array(
				'code'=>2,
				'message'=>'读取失败');
		}
		echo json_encode($ret);
	}
	
} else {
	$ret = array(
		'code'=>1,
		'message'=>'token已失效请重新进行预注册');
	echo json_encode($ret);
}

