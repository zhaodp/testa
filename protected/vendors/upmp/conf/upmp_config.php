<?php

/**
 * 类名：配置类
 * 功能：配置类
 * 类属性：公共类
 * 版本：1.0
 * 日期：2012-10-11
 * 作者：中国银联UPMP团队
 * 版权：中国银联
 * 说明：以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己的需要，按照技术文档编写,并非一定要使用该代码。该代码仅供参考。
 * */

class upmp_config
{
	
    static $timezone        		= "Asia/Shanghai"; //时区
    
    static $version     			= "1.0.0"; // 版本号
    static $charset    		 		= "UTF-8"; // 字符编码
    static $sign_method 			= "MD5"; // 签名方法，目前仅支持MD5
    static $mer_id     				= "898110248990040"; // 商户号  测试：860000000000036
    static $security_key    		= "OgXHWWcyTtyM4TbgkvQOndLekoQRTTTo"; // 商户密钥 测试：ZHSsXTD5puC6XIsvHIHmVkKs3K8Dz5al
    static $mer_back_end_url     	= "http://call.edaijia.cn/notify"; // 银联回调后台通知地址
    static $mer_front_end_url     	= ""; // 前台通知地址

    //交易测试地址 upmp.trade.url=http://222.66.233.198:8080/gateway/merchant/trade
    static $upmp_trade_url   	 	= "https://mgate.unionpay.com/gateway/merchant/trade";
    //交易查询地址 upmp.query.url=http://222.66.233.198:8080/gateway/merchant/query
    static $upmp_query_url    	 	= "https://mgate.unionpay.com/gateway/merchant/query";

    //委托交易 2013-1-8
    static $quick_trade_url   	= "http://222.66.233.198:8080/sim/gettn";

    //建立委托回调商户地址
    static $bind_trade_back_url   	= "http://222.66.233.198:8080/gateway/merchant/trade";


    const VERIFY_HTTPS_CERT 		= false;
    
    const RESPONSE_CODE_SUCCESS 	= "00"; // 成功应答码
	const SIGNATURE 				= "signature"; // 签名
	const SIGN_METHOD 				= "signMethod"; // 签名方法
	const RESPONSE_CODE 			= "respCode"; // 应答码
	const RESPONSE_MSG				= "respMsg"; // 应答信息
    
    const QSTRING_SPLIT				= "&"; // &
    const QSTRING_EQUAL 			= "="; // =
    
}

?>
