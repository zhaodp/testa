<?php
//废弃
//2088801265827061
//inn8vu1g3clg16f4e5lyk9fw5jnb8lqt
//paypay@edaijia.cn
return array (
	'sms_sn'=>'SDK-YQD-010-00092', 
	'sms_password'=>'836725', 
	'sms_soap'=>'http://sdk2.entinfo.cn:8060/webservice.asmx?WSDL', 
	//'http://sdk2.entinfo.cn:8060/webservice.asmx?WSDL'
	'api_password'=>'zAcU!(^$26&8B*#g9hz', 
	
	'mtk_sig'=>'5db6d387680bd559f85a101a4a152044', 
	'edj_api_key'=>'20000001', 
	
	'payment'=>array (
		'alipayConfig'=>array (
			'partner'=>'2088501737596294',  //合作身份者id，以2088开头的16位纯数字
			'key'=>'dk1hiq6a5z6b4z1hyu2zieiza1bsykub',  //安全检验码，以数字和字母组成的32位字符
			'seller_email'=>'shanliying@oucamp.com',  //签约支付宝账号或卖家支付宝帐户
			'return_url'=>'http://www.edaijia.cc/alipay/return_url.php',  //页面跳转同步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
			'notify_url'=>'http://www.edaijia.cc/alipay/notify_url.php',  //服务器异步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
			'sign_type'=>'MD5',  //签名方式 不需修改
			'input_charset'=>'utf-8',  //字符编码格式 目前支持 gbk 或 utf-8
			'transport'=>'http',  //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
			'gateway'=>'https://mapi.alipay.com/gateway.do?',  //支付宝网关地址
			'https_verify_url'=>'https://mapi.alipay.com/gateway.do?service=notify_verify&',  //HTTPS形式消息验证地址
			'http_verify_url'=>'http://notify.alipay.com/trade/notify_query.do?'),  //HTTP形式消息验证地址
		'unionPayConfig'=>array (
			'MerchantId'=>'898110248990040', 
			'TerminalId'=>'00000001', 
			'SP_ID'=>'0282', 
			'SysProvider'=>'11571000', 
			'keyPath'=>dirname(__FILE__).'/../../config/cert/edaijia.pem', 
			'name'=>'edaijia', 
			'currency'=>'156', 
			'country'=>'156')), 
	
	'callcenter_sn'=>'edaijia', 
	'callcenter_password'=>'000000', 
	
	'formatDateTime'=>'Y-m-d H:i:s', 
	'formatShortDateTime'=>'Y-m-d H:i', 
	'formatDate'=>'Y-m-d', 
	'formatTime'=>'H:i:s', 
	'formatShortTime'=>'H:i', 
	
	'WB_AKEY'=>'1963938208', 
	'WB_SKEY'=>'7b2a095502bba514c3eaf2ab9128f7b4', 
	'WB_CALLBACK_URL'=>'http://www.edaijia.cn/v2/index.php?r=weibo/callback', 
	
	'httpsqs'=>array (
		'task'=>array (
			'queue'=>'task', 
			'host'=>'sqs.edaijia.cn', 
			'port'=>'1218', 
			'password'=>'edaijia2012'), 
		'location'=>array (
			'queue'=>'location', 
			'host'=>'sqs.edaijia.cn', 
			'port'=>'1218', 
			'password'=>'edaijia2012'), 
		'driver_track'=>array (
			'queue'=>'driver_track', 
			'host'=>'sqs.edaijia.cn', 
			'port'=>'1218', 
			'password'=>'edaijia2012'), 
		'driver_position'=>array (
			'queue'=>'driver_position', 
			'host'=>'sqs.edaijia.cn', 
			'port'=>'1218', 
			'password'=>'edaijia2012'), 
		'driver_call_log'=>array (
			'queue'=>'driver_call_log', 
			'host'=>'sqs.edaijia.cn', 
			'port'=>'1218', 
			'password'=>'edaijia2012'), 
		'heartbeat'=>array (
			'queue'=>'heartbeat', 
			'host'=>'sqs.edaijia.cn', 
			'port'=>'1218', 
			'password'=>'edaijia2012'), 
		'dispatch'=>array (
			'queue'=>'dispatch', 
			'host'=>'sqs.edaijia.cn', 
			'port'=>'1218', 
			'password'=>'edaijia2012')), 
	
	'CACHE_KEY_DRIVER_INFO'=>'driver_info_cache_', 
	'CACHE_KEY_API'=>'api_cache', 
	
	'whitelist'=>array (
		'15301058035', 
		'4006506955', 
		'4006913939', 
		'10086', 
		'1008611', 
		'61358339', 
		'02161358339', 
		'61358369', 
		'02161358369', 
		'58247957', 
		'01058247957', 
		'58693979', 
		'58694576', 
		'58697487', 
		'01058694576', 
		'01058694525', 
		'58694525', 
		'12319', 
		'96166', 
		'95559', 
		'01095510', 
		'12580', 
		'13800138000', 
		'114', 
		'1222', 
		'10010', 
		'057188134859', 
		'88134859', 
		'667367', 
		'667667', 
		'13065710861', 
		'#43#', 
		'01058699137', 
		'01058699147', 
		'01058697740', 
		'01058697840', 
		'01058103539', 
		'01058103540', 
		'58699137', 
		'58699147', 
		'58697740', 
		'58697840', 
		'58103539', 
		'58103540', 
		'15321382203', 
		'15321382201', 
		'15321382879', 
		'15321382503', 
		'15321382035', 
		'15321382811', 
		'15321382672', 
		'15321382758', 
		'15321382379', 
		'15321382197'), 
	
	'Fee'=>array (
		'1'=>array (
			'minFee'=>39,  //基础价格（单位：元）
			'firstFee'=>59,  //第一次变价后的价格（单位：元） 
			'secondFee'=>79,  //第二次变价后的价格（单位：元）
			'thirdFee'=>99,  //第三次变价后的价格（单位：元）
			'minDistance'=>10,  //基础距离（单位：公里）
			'feeStep'=>20,  //变价步长（单位：元）
			'distanceStep'=>10,  //距离计价步长（单位：公里）
			'distanceFeeStep'=>20,  //距离费用步长（单位：元）
			'beforeWaitingFee'=>20,  //代驾开始前的等候费用步长（单位：元）
			'onWaitingFee'=>20,  //代驾中的等候费用步长（单位：元）
			'beforeWaitingStep'=>30,  //代驾开始前的等候时长步长（单位：元）
			'onWaitingStep'=>30,  //代驾中的等候时长步长（单位：元）
			'minFeeHour'=>'07:00',  //基价开始时间（单位：时，24小时制）
			'firstFeeHour'=>'22:00',  //第一次变价开始时间（单位：时，24小时制）
			'secondFeeHour'=>'23:00',  //第二次变价开始时间（单位：时，24小时制）
			'thirdFeeHour'=>'00:00'),  //第三次变价开始时间（单位：时，24小时制）
		'2'=>array (
			'minFee'=>39,  //基础价格（单位：元）
			'firstFee'=>59,  //第一次变价后的价格（单位：元） 
			'secondFee'=>79,  //第二次变价后的价格（单位：元）
			'thirdFee'=>99,  //第三次变价后的价格（单位：元）
			'minDistance'=>10,  //基础距离（单位：公里）
			'feeStep'=>20,  //变价步长（单位：元）
			'distanceStep'=>10,  //距离计价步长（单位：公里）
			'distanceFeeStep'=>20,  //距离费用步长（单位：元）
			'beforeWaitingFee'=>20,  //代驾开始前的等候费用步长（单位：元）
			'onWaitingFee'=>20,  //代驾中的等候费用步长（单位：元）
			'beforeWaitingStep'=>30,  //代驾开始前的等候时长步长（单位：元）
			'onWaitingStep'=>30,  //代驾中的等候时长步长（单位：元）
			'minFeeHour'=>'07:00',  //基价开始时间（单位：时，24小时制）
			'firstFeeHour'=>'22:00',  //第一次变价开始时间（单位：时，24小时制）
			'secondFeeHour'=>'23:00',  //第二次变价开始时间（单位：时，24小时制）
			'thirdFeeHour'=>'00:00'),  //第三次变价开始时间（单位：时，24小时制）
		'3'=>array (
			'minFee'=>39,  //基础价格（单位：元）
			'firstFee'=>59,  //第一次变价后的价格（单位：元） 
			'secondFee'=>79,  //第二次变价后的价格（单位：元）
			'thirdFee'=>99,  //第三次变价后的价格（单位：元）
			'minDistance'=>10,  //基础距离（单位：公里）
			'feeStep'=>20,  //变价步长（单位：元）
			'distanceStep'=>10,  //距离计价步长（单位：公里）
			'distanceFeeStep'=>20,  //距离费用步长（单位：元）
			'beforeWaitingFee'=>20,  //代驾开始前的等候费用步长（单位：元）
			'onWaitingFee'=>20,  //代驾中的等候费用步长（单位：元）
			'beforeWaitingStep'=>30,  //代驾开始前的等候时长步长（单位：元）
			'onWaitingStep'=>30,  //代驾中的等候时长步长（单位：元）
			'minFeeHour'=>'07:00',  //基价开始时间（单位：时，24小时制）
			'firstFeeHour'=>'22:00',  //第一次变价开始时间（单位：时，24小时制）
			'secondFeeHour'=>'23:00',  //第二次变价开始时间（单位：时，24小时制）
			'thirdFeeHour'=>'00:00'),  //第三次变价开始时间（单位：时，24小时制）
		'4'=>array (
			'minFee'=>39,  //基础价格（单位：元）
			'firstFee'=>59,  //第一次变价后的价格（单位：元） 
			'secondFee'=>null,  //第二次变价后的价格（单位：元）
			'thirdFee'=>null,  //第三次变价后的价格（单位：元）
			'minDistance'=>10,  //基础距离（单位：公里）
			'feeStep'=>20,  //变价步长（单位：元）
			'distanceStep'=>5,  //距离计价步长（单位：公里）
			'distanceFeeStep'=>20,  //距离费用步长（单位：元）
			'beforeWaitingFee'=>20,  //代驾开始前的等候费用步长（单位：元）
			'onWaitingFee'=>20,  //代驾中的等候费用步长（单位：元）
			'beforeWaitingStep'=>30,  //代驾开始前的等候时长步长（单位：元）
			'onWaitingStep'=>30,  //代驾中的等候时长步长（单位：元）
			'minFeeHour'=>'07:00',  //基价开始时间（单位：时，24小时制）
			'firstFeeHour'=>'22:00',  //第一次变价开始时间（单位：时，24小时制）
			'secondFeeHour'=>'07:00',  //第二次变价开始时间（单位：时，24小时制）
			'thirdFeeHour'=>null),  //第三次变价开始时间（单位：时，24小时制）
		'5'=>array (
			'minFee'=>39,  //基础价格（单位：元）
			'firstFee'=>59,  //第一次变价后的价格（单位：元） 
			'secondFee'=>79,  //第二次变价后的价格（单位：元）
			'thirdFee'=>99,  //第三次变价后的价格（单位：元）
			'minDistance'=>10,  //基础距离（单位：公里）
			'feeStep'=>20,  //变价步长（单位：元）
			'distanceStep'=>10,  //距离计价步长（单位：公里）
			'distanceFeeStep'=>20,  //距离费用步长（单位：元）
			'beforeWaitingFee'=>20,  //代驾开始前的等候费用步长（单位：元）
			'onWaitingFee'=>20,  //代驾中的等候费用步长（单位：元）
			'beforeWaitingStep'=>30,  //代驾开始前的等候时长步长（单位：元）
			'onWaitingStep'=>30,  //代驾中的等候时长步长（单位：元）
			'minFeeHour'=>'07:00',  //基价开始时间（单位：时，24小时制）
			'firstFeeHour'=>'22:00',  //第一次变价开始时间（单位：时，24小时制）
			'secondFeeHour'=>'23:00',  //第二次变价开始时间（单位：时，24小时制）
			'thirdFeeHour'=>'00:00'),  //第三次变价开始时间（单位：时，24小时制）
		'6'=>array (
			'minFee'=>39,  //基础价格（单位：元）
			'firstFee'=>59,  //第一次变价后的价格（单位：元） 
			'secondFee'=>79,  //第二次变价后的价格（单位：元）
			'thirdFee'=>99,  //第三次变价后的价格（单位：元）
			'minDistance'=>10,  //基础距离（单位：公里）
			'feeStep'=>20,  //变价步长（单位：元）
			'distanceStep'=>10,  //距离计价步长（单位：公里）
			'distanceFeeStep'=>20,  //距离费用步长（单位：元）
			'beforeWaitingFee'=>20,  //代驾开始前的等候费用步长（单位：元）
			'onWaitingFee'=>20,  //代驾中的等候费用步长（单位：元）
			'beforeWaitingStep'=>30,  //代驾开始前的等候时长步长（单位：元）
			'onWaitingStep'=>30,  //代驾中的等候时长步长（单位：元）
			'minFeeHour'=>'07:00',  //基价开始时间（单位：时，24小时制）
			'firstFeeHour'=>'22:00',  //第一次变价开始时间（单位：时，24小时制）
			'secondFeeHour'=>'23:00',  //第二次变价开始时间（单位：时，24小时制）
			'thirdFeeHour'=>'00:00'),  //第三次变价开始时间（单位：时，24小时制）
		'7'=>array (
			'minFee'=>39,  //基础价格（单位：元）
			'firstFee'=>39,  //第一次变价后的价格（单位：元） 
			'secondFee'=>39,  //第二次变价后的价格（单位：元）
			'thirdFee'=>39,  //第三次变价后的价格（单位：元）
			'minDistance'=>5,  //基础距离（单位：公里）
			'feeStep'=>20,  //变价步长（单位：元）
			'distanceStep'=>5,  //距离计价步长（单位：公里）
			'distanceFeeStep'=>20,  //距离费用步长（单位：元）
			'beforeWaitingFee'=>20,  //代驾开始前的等候费用步长（单位：元）
			'onWaitingFee'=>20,  //代驾中的等候费用步长（单位：元）
			'beforeWaitingStep'=>30,  //代驾开始前的等候时长步长（单位：元）
			'onWaitingStep'=>30,  //代驾中的等候时长步长（单位：元）
			'minFeeHour'=>'00:00',  //基价开始时间（单位：时，24小时制）
			'firstFeeHour'=>'00:00',  //第一次变价开始时间（单位：时，24小时制）
			'secondFeeHour'=>'00:00',  //第二次变价开始时间（单位：时，24小时制）
			'thirdFeeHour'=>'00:00')),  //第三次变价开始时间（单位：时，24小时制）
	'appVersion'=>array (
		'appVersionIphone'=>array (
			'latest'=>'2.0.0', 
			'deprecated'=>'1.0.0', 
			'url'=>'http://itunes.apple.com/cn/app/id468591734?mt=8'), 
		'appVersionAndroid'=>array (
			'latest'=>'2.2.6', 
			'deprecated'=>'2.2.5', 
			'url'=>'http://img.edaijia.cn/edaijia-m/eDaijia_2.2.6_edaijia.apk'), 
		'appVersionDriver'=>array (
			'latest'=>'1.0.3', 
			'deprecated'=>'1.0.2', 
			'url'=>'http://img.edaijia.cn/edaijia-m/eDriverClient_1.0.1_edaijia.apk')), 
	'appContent'=>array (
		'expireAt'=>date('Y-m-d', time()+24*3600), 
		'PageContentText'=>array (
			'zh'=>'e代驾真是个实用的APP软件！司机十几分钟就到了，最关键是便宜！22点前10公里才￥39。据说为1600多名司机师傅提供了就业机会--社会企业啊！[赞]推荐弟兄们都下载一个试试~让更多司机师傅能够摆脱黑中介的盘剥【e代驾手机客户端下载地址>>http://wap.edaijia.cn】', 
			'en'=>''), 
		'RecommendText'=>array (
			'zh'=>'e代驾真是个实用的APP软件！司机十几分钟就到了，最关键是便宜！22点前10公里才￥39。据说为1600多名司机师傅提供了就业机会--社会企业啊！[赞]推荐弟兄们都下载一个试试~让更多司机师傅能够摆脱黑中介的盘剥【e代驾手机客户端下载地址>>http://wap.edaijia.cn】', 
			'en'=>''), 
		'EmailMessageBody'=>array (
			'zh'=>'e代驾真是个实用的APP软件！司机十几分钟就到了，最关键是便宜！22点前10公里才￥39。据说为1600多名司机师傅提供了就业机会--社会企业啊！[赞]推荐弟兄们都下载一个试试~让更多司机师傅能够摆脱黑中介的盘剥【e代驾手机客户端下载地址>>http://wap.edaijia.cn】', 
			'en'=>''), 
		'MicBlogMessage'=>array (
			'zh'=>'e代驾真是个实用的APP软件！司机十几分钟就到了，最关键是便宜！22点前10公里才￥39。据说为1600多名司机师傅提供了就业机会--社会企业啊！[赞]推荐弟兄们都下载一个试试~让更多司机师傅能够摆脱黑中介的盘剥【e代驾手机客户端下载地址>>http://wap.edaijia.cn】', 
			'en'=>''), 
		'SMSBodyText'=>array (
			'zh'=>'e代驾真是个实用的APP软件！司机十几分钟就到了，最关键是便宜！22点前10公里才￥39。据说为1600多名司机师傅提供了就业机会--社会企业啊！[赞]推荐弟兄们都下载一个试试~让更多司机师傅能够摆脱黑中介的盘剥【e代驾手机客户端下载地址>>http://wap.edaijia.cn】', 
			'en'=>''), 
		'RechargeText'=>array (
			'zh'=>'充值使用说明:\n1.您可以通过e代驾发放的优惠券或者短信获取优惠券号码；\n2.一个新手机号只能使用一次优惠券；\n3.充值成功后，只要通过app呼叫司机使用代驾，该优惠即可立即生效；\n4.优惠券使用的最终解释权归e代驾所有，如有疑问请拨打4006-91-3939咨询；', 
			'en'=>''), 
		'LocationError'=>array (
			'zh'=>'抱歉，暂时无法获得您的位置，请猛击下方电话，继续享受e代驾提供的服务', 
			'en'=>''), 
		'NetworkError'=>array (
			'zh'=>'抱歉，您的网络连接中断，请猛击下方电话，继续享受e代驾提供的服务或者尝试重新连接网络！', 
			'en'=>''), 
		'SearchError'=>array (
			'zh'=>'抱歉，由于附近没有可以为您服务的司机，暂时无法为您提供服务！', 
			'en'=>''), 
		'LocationEnabledAlert'=>array (
			'zh'=>'请打开您的定位服务,或者授权e代驾的定位服务!', 
			'en'=>''), 
		'TipsText'=>array (
			'zh'=>'温馨提示：e代驾现已开通了北京、上海、广州、杭州、深圳、重庆六个城市的服务，其他城市会陆续开通，敬请期待！', 
			'en'=>''), 
		'priceContent'=>array (
			'title'=>array (
				'zh'=>'e代驾%s服务价格表', 
				'en'=>'Edaijia Service Price'), 
			'period'=>array (
				'zh'=>'时间段', 
				'en'=>'Period'), 
			'pricingStart'=>array (
				'zh'=>'起步价(%s公里以内)', 
				'en'=>'Pricing Start(less than %s Km)'), 
			'memo'=>array (
				'memo'=>array (
					'zh'=>'注：', 
					'en'=>'PS:'), 
				'unit'=>array (
					'zh'=>'元', 
					'en'=>'RMB'), 
				'1'=>array (
					'zh'=>'1.不同时间段的代驾起步费用以约定时间为准，默认最短约定时间为客户呼叫时间延后20分钟。', 
					'en'=>'bulabula'), 
				'2'=>array (
					'zh'=>'2.按照车内里程总表计算公里数代驾距离超过%s公里后，每超过%s公里，加收%s元，不足%s公里按%s公里计算。', 
					'en'=>'bulabula'), 
				'3'=>array (
					'zh'=>'3.约定时间前到达客户指定位置，从约定时间开始，每满30分钟收费20元等候费，不满30分钟不收费；约定时间之后到达客户指定位置，从司机到达时间后，每满30分钟收费20元等候费，不满30分钟不收费。', 
					'en'=>'bulabula')))));