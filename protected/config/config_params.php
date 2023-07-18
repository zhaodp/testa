<?php
//2088801265827061
//inn8vu1g3clg16f4e5lyk9fw5jnb8lqt
//paypay@edaijia.cn

$activity_free_customer_msg = '您满足<新客首单免费>活动条件，服务结束无需再付任何代驾费用。如您对我们的服务满意，请尽情告诉朋友';
$activity_free_driver_msg = '该顾客满足<新客首单免费>条件，服务结束后，请不要向顾客收取任何代驾费用。免单费用第二天返回信息费账户。如有问题请通过司机端意见反馈提交';

$_edaijia_config_params = array(
                
    'order_architecture_refactor_on' => true,
    'c_order_single_refactor_on' => false,
    'c_order_multi_refactor_on' => false,
    'c_driver_position_refactor_on' => false,
    
    'block_new_customer_cities' => array(),
                
    'block_170_customer_cities' => array(6),
    
    //启用访问过滤器 on 2014-3-18 by syang
    'useVisitFiler'=>true,

    //h5访问地址
    'h5_url'=>'http://h5.edaijia.cn',
// 	'sms_sn'=>'SDK-YQD-010-00092',
// 	'sms_password'=>'836725',
	'sms_sn'=>'SDK-WKS-010-00884',
	'sms_password'=>'992504',
	'sms_soap'=>'http://sdk2.entinfo.cn:8060/webservice.asmx?WSDL',
	//'sms_gsms'=>'http://ws.iems.net.cn/GeneralSMS/ws/SmsInterface?wsdl',
	'sms_gsms'=>'http://219.133.59.101/GeneralSMS/ws/SmsInterface?wsdl',
	//'http://sdk2.entinfo.cn:8060/webservice.asmx?WSDL'
	'api_password'=>'zAcU!(^$26&8B*#g9hz',

	//ID 66961  密码78252381  签名：[乐搭车]

	'mtk_sig'=>'5db6d387680bd559f85a101a4a152044',
	'edj_api_key'=>'20000001',
	'baidu_map_key'=>'e84e1c0102539d473db235592f108bea',
	'baidu_map_key_v2'=>'ECfffb5d16a4f1b23c885c0527e91774',
	'app_splash'=>'http://img.edaijia.cn/edaijia/splash/20130501.png',

	//EMAIL
	'emailHost'=>'smtp.qq.com',
	'emailAccount'=>'service@edaijia.cn',
	'emailPassword'=>'edaijia@123',
	'emailFrom'=>'service@edaijia.cn',


    //Email inc   duke add 2014-12-29
    'emailIncHost'=>'smtp.263.net',
    'emailIncAccount'=>'warning@edaijia-inc.cn',
    'emailIncPassword'=>'VOf#AX$kfeKr084z',
    'emailIncFrom'=>'warning@edaijia-inc.cn',
    'emailIncPort'=> 465,
    'emailIncSMTPSecure'=> 'ssl',

    //Email staff   bidong add 2013-12-18
    'emailStaffHost'=>'smtp.exmail.qq.com',
    'emailStaffAccount'=>'service@edaijia-staff.cn',
    'emailStaffPassword'=>'JbSuB4R7QitWqYFC',
    'emailStaffFrom'=>'service@edaijia-staff.cn',

    //低于或等于此版本司机端的不使用短信下发Push
    'SmsPushLimitedVersion' => '2.2.6',
    //选司机下单retry参数
    'SingleRetryStart' => 8, //首次派单8秒后可以尝试重派
    'SingleRetryRange' => 10, //10秒钟内会尝试重派一次


    'OrderSingleRange'            => 5000, //选司机下单取司机默认范围
    'OrderSingleRangeByCity'      => array('1' => 3000), //选司机下单取司机城市范围
    'Order400DispatchRange'       => 5000, //400自动派单距离默认范围(米)
    'Order400DispatchRangeByCity' => array('1' => 3000), //选司机下单取司机城市范围
    'OrderOneKeyDispatchRange'    => 5000, //一键下单派单距离默认范围(米)
    'OrderOneKeyDispatchRangeByCity' => array('1' => 3000), //选司机下单取司机城市范围
    'Order400DispatchNumber'    => 12, //400自动派单查找司机个数
    'OrderOneKeyDispatchNumber' => 12, //一键下单派单查找司机个数
    'EarthRound' => 2 * M_PI * 6378.1 * 1000,	//赤道周长（米）
	//客户评价附加码上限
	'maxSmsEx'=>50000,
	//考试各类型出题数量
    'exam_num'=>array(
			1=>9,
			2=>1,
			3=>1,
			4=>3,
			5=>1,
			6=>1,
			7=>1,
			8=>1,
			9=>1,
			10=>1,
	),
	'payment'=>array(
		'alipayConfig'=>array(
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
        //银联支付配置信息
        'unionPayConfig'=>array(
			'sign_msg'=>'64867890Edaijia',            //签名信息
            'sign_method'=>'MD5',           //签名方法
			'mer_id'=>'860000000000036',       //商户号
			'security_key'=>'ZHSsXTD5puC6XIsvHIHmVkKs3K8Dz5al',                //商户秘钥
			'mer_back_end_url'=>'http://api.d.edaijia.cn/v4/notify',   //银联回调后台通知地址
			'mer_front_end_url'=>'',    //银联前台通知地址
			'upmp_trade_url'=>'http://222.66.233.198:8080/gateway/merchant/trade',  //银联测试后台交易地址
            'upmp_query_url'=>'http://222.66.233.198:8080/gateway/merchant/query'  //银联测试后台交易查询地址
            )),

	'callcenter_sn'=>'edaijia',
	'callcenter_password'=>'000000',

	'formatDateTime'=>'Y-m-d H:i:s',
	'formatShortDateTime'=>'Y-m-d H:i',
	'formatDate'=>'Y-m-d',
	'formatTime'=>'H:i:s',
	'formatShortTime'=>'H:i',
	'formatGridPage'=>array(
		'header'=>'',
		'cssFile'=>false,
		'maxButtonCount'=>10,
		'selectedPageCssClass'=>'active',
		'hiddenPageCssClass'=>'disabled',
		'firstPageCssClass'=>'previous',
		'lastPageCssClass'=>'next',
		'firstPageLabel'=>'<<',
		'lastPageLabel'=>'>>',
		'prevPageLabel'=>'<',
		'nextPageLabel'=>'>'),

	'WB_AKEY'=>'1963938208',
	'WB_SKEY'=>'7b2a095502bba514c3eaf2ab9128f7b4',
	'WB_CALLBACK_URL'=>'http://www.edaijia.cn/v2/index.php?r=weibo/callback',

	'CACHE_KEY_DRIVER_INFO'=>'driver_info_cache_',
	'CACHE_KEY_GETUI_CLIENT_INFO'=>'getui_client_info_cache_',//个推缓存KEY  by zhanglimin 2013-04-26
	'CACHE_KEY_CUSTOMER_BLACKLIST'=>'customer_blacklist_cache',//添加黑名单列表KEY BY AndyCong 2013-05-02
	'CACHE_KEY_DISPATCH_DRIVER'=>'dispatch_driver_cache_',      //添加分配司机KEY
	'CACHE_KEY_API'=>'api_cache',
	'CACHE_ONLINE_DRIVERS'=>'online_drivers',
	'GETUI_PUSH_NUM'=>3,

    //2013--5-24 by zhanglimin
    'dispatch' =>array(
      "time_interval"=> 60*30, //时间间隔
    ),

	'whitelist'=>array(
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
		'15321382197',
        '000',
        '001',
        '002',
        '003',
        '004',
        '005',
        '006',
        '007',
        '008',
        '009',
        '01095568',
	//kk拼车测试
	'13911885162',
	'13311328999',
	'18611139637',
	'13911757892',
	'15321210589',
	'13269363532',
	'15911115136',
	'13522452154',
	'15710010133',
	'18311447138',
	'13121424688',
	'13321108076',
	'13611151110',
	'13601105725',
	'18618415725',
	'13683547230',
	'13401194504',
	'15811363685',
	'18513176057',
	'15910579312',
	'18911944493',
	'13041296610',
	'13011198158',
	'13120047272',
	'15330252793',
	'13911408462',
	'18911498681',
	'18701573890',
	//天润系统座机号
	'01064149599',
	'64149599',
    '01064149596',
    '64149596'
    ),

    'daytime_price'=>array( //日间业务价格 
        1=>array(
            'price'=>19,
            'start_time'=>'07:00',
            'end_time'=>'17:59',
            'subsidy_price' => 10, //日间业务服务时间额外的补贴
            'allowance'=>array(
                array(
                    'start_time'=>'20:00',
                    'end_time'=>'23:59',
                    'price'=>15,
                    'unit'=>30,
                ),
                array(
                    'start_time'=>'00:00',
                    'end_time'=>'06:59',
                    'price'=>30,
                    'unit'=>30,
                )
            ),
            'return_fee'=>array(
                'min_distance'=>30,
                'fee'=>3
            ),
            'desc'=>'29元起步（含1小时、10公里）， 超出部分每增加30分钟15元，代驾距离每增加1公里加收1元。超出部分时间不足30分钟按30分钟计算，里程不足1公里按1公里计算，限市区内代驾服务'
        ),
        2=>array(
            'price'=>19,
            'start_time'=>'07:00',
            'end_time'=>'17:59',
            'subsidy_price' => 10, //日间业务服务时间额外的补贴
            'allowance'=>array(
                array(
                    'start_time'=>'19:00',
                    'end_time'=>'23:59',
                    'price'=>15,
                    'unit'=>30,
                ),
                array(
                    'start_time'=>'00:00',
                    'end_time'=>'06:59',
                    'price'=>30,
                    'unit'=>30,
                )
            ),
            'return_fee'=>array(
                'min_distance'=>30,
                'fee'=>0
            ),
            'desc'=>'29元起步（含1小时、10公里）， 超出部分每增加30分钟15元，代驾距离每增加1公里加收1元。超出部分时间不足30分钟按30分钟计算，里程不足1公里按1公里计算，限市区内代驾服务'
        ),
    ),

    'daytime_price_old'=>array( //日间业务价格
        1=>array(
            'price'=>19,
            'start_time'=>'07:00',
            'end_time'=>'18:59',
            'subsidy_price' => 10, //日间业务服务时间额外的补贴
            'allowance'=>array(
                array(
                    'start_time'=>'20:00',
                    'end_time'=>'23:59',
                    'price'=>15,
                    'unit'=>30,
                ),
                array(
                    'start_time'=>'00:00',
                    'end_time'=>'06:59',
                    'price'=>30,
                    'unit'=>30,
                )
            ),
            'return_fee'=>array(
                'min_distance'=>30,
                'fee'=>3
            ),
            'desc'=>'限市区内代驾服务'
        ),
        2=>array(
            'price'=>19,
            'start_time'=>'07:00',
            'end_time'=>'18:59',
            'subsidy_price' => 10, //日间业务服务时间额外的补贴
            'allowance'=>array(
                array(
                    'start_time'=>'20:00',
                    'end_time'=>'23:59',
                    'price'=>15,
                    'unit'=>30,
                ),
                array(
                    'start_time'=>'00:00',
                    'end_time'=>'06:59',
                    'price'=>30,
                    'unit'=>30,
                )
            ),
            'return_fee'=>array(
                'min_distance'=>30,
                'fee'=>3
            ),
            'desc'=>'限市区内代驾服务'
        ),
    ),

    'daytime_price_client'=>array( //客户端日间业务价格
        1=>array(
            'price'=>19,
            'start_time'=>'07:00',
            'end_time'=>'17:59',
            'subsidy_price' => 0, //日间业务服务时间额外的补贴
            'allowance'=>array(
                array(
                    'start_time'=>'19:00',
                    'end_time'=>'23:59',
                    'price'=>15,
                    'unit'=>30,
                ),
                array(
                    'start_time'=>'00:00',
                    'end_time'=>'06:59',
                    'price'=>30,
                    'unit'=>30,
                )
            ),
            'return_fee'=>array(
                'min_distance'=>30,
                'fee'=>0
            ),
            'desc'=>'%s元起步（含%s小时、%s公里）， 超出部分每增加%s分钟%s元，代驾距离每增加%s公里加收%s元。超出部分时间不足%s分钟按%s分钟计算，里程不足%s公里按%s公里计算，限市区内代驾服务'
        ),
        2=>array(
            'price'=>29,
            'start_time'=>'07:00',
            'end_time'=>'17:59',
            'subsidy_price' => 0, //日间业务服务时间额外的补贴
            'allowance'=>array(
                array(
                    'start_time'=>'19:00',
                    'end_time'=>'23:59',
                    'price'=>15,
                    'unit'=>30,
                ),
                array(
                    'start_time'=>'00:00',
                    'end_time'=>'06:59',
                    'price'=>30,
                    'unit'=>30,
                )
            ),
            'return_fee'=>array(
                'min_distance'=>30,
                'fee'=>0
            ),
            'desc'=>'%s元起步（含%s小时、%s公里）， 超出部分每增加%s分钟%s元，代驾距离每增加%s公里加收%s元。超出部分时间不足%s分钟按%s分钟计算，里程不足%s公里按%s公里计算，限市区内代驾服务'
        ),
    ),

    'daytime_price_new'=>array( //日间业务价格新策略
        1=>array(
            'price'=>19,
            'start_time'=>'07:00',
            'end_time'=>'17:59',
            'basic_time'=>60,
            'basic_distance'=>5,
            'beyond_time_unit'=>30,
            'beyond_time_price'=>15,
            'beyond_distance_unit'=>1,
            'beyond_distance_price'=>1,
            'subsidy_price' => 0, //日间业务服务时间额外的补贴
            'allowance'=>array(
                array(
                    'start_time'=>'19:00',
                    'end_time'=>'23:59',
                    'price'=>15,
                    'unit'=>30,
                ),
                array(
                    'start_time'=>'00:00',
                    'end_time'=>'06:59',
                    'price'=>30,
                    'unit'=>30,
                )
            ),

            'desc'=>'19元起步（含1小时、5公里）， 超出部分每增加30分钟15元，代驾距离每增加1公里加收1元。超出部分时间不足30分钟按30分钟计算，里程不足1公里按1公里计算，限市区内代驾服务'
        ),
        2 =>array(
            'price'=>29,
            'start_time'=>'07:00',
            'end_time'=>'17:59',
            'basic_time'=>60,
            'basic_distance'=>10,
            'beyond_time_unit'=>30,
            'beyond_time_price'=>15,
            'beyond_distance_unit'=>1,
            'beyond_distance_price'=>1,
            'subsidy_price' => 0, //日间业务服务时间额外的补贴
            'allowance'=>array(
                array(
                    'start_time'=>'19:00',
                    'end_time'=>'23:59',
                    'price'=>15,
                    'unit'=>30,
                ),
                array(
                    'start_time'=>'00:00',
                    'end_time'=>'06:59',
                    'price'=>30,
                    'unit'=>30,
                )
            ),

            'desc'=>'29元起步（含1小时、10公里）， 超出部分每增加30分钟15元，代驾距离每增加1公里加收1元。超出部分时间不足30分钟按30分钟计算，里程不足1公里按1公里计算，限市区内代驾服务'
        ),
    ),

	
			#司机常见问题列表
			'faq_list'=>array(
				'1'=>array('title'=>'司机端定位不准、里程表不准怎么办？','answer'=>'定位精度存在一定误差，一般在几百米范围内是属于正常。主要受手机的GPS或者手机网络信号不稳定影响。GPS受到天气、车库、高楼等遮挡影响可能导致定位不准。解决办法：1.确保手机的各类定位服务是开启的，包括GPS、WLAN和移动网络信号正常。需要在手机的系统设置里的“位置服务”这一项进行设置。2.重启手机，重新定位会得到缓解。我们也在不断优化，相信定位会越来越准。3.如出现里程不准导致报单金额不对，请核对实际情况后联系服务同学退单重补。'),
				'2'=>array('title'=>'司机端笑脸不出如何解决？','answer'=>'1.请确保手机所处的移动网络信号良好。2.将手机本地系统当前时间调整为正确的时间。3.在司机端APP->更多->系统设定->清除缓存，重新登录APP后等候30秒。如果1、2步操作无效，请卸载司机端重新安装。'),
				'3'=>array('title'=>'为什么司机端会一直出现设备检测中的提示？','answer'=>'GPS定位中时会显示：设备检测中。待定位完成后会显示正常。定位完成后还处于设备检测中的话，切换一下工作状态即可恢复正常。'),
				'4'=>array('title'=>'司机端手机耗电、发烫问题？','answer'=>'软件使用GPS定位服务，需要频繁定位以保证准确度，所以会比较耗电。解决办法：1.请司机关闭一些处于后台的应用以及服务；2.不用地图时不要一直停留在地图页面；3.在空闲和下班状态下可以关闭屏幕。我们也在不断优化性能，相信会越来越好！'),
				'5'=>array('title'=>'推荐使用哪些机型？','answer'=>'主要推荐使用华为和三星的主流机型。小米、酷派等手机由于某些技术原因，不建议使用。注意，不建议使用双卡手机。'),
				'6'=>array('title'=>'司机端掉线，如何解决？','answer'=>'1.请确保手机时间准确，勾选网络时间。2.司机在线状态时不要将手机锁屏。3.华为手机请开启省电白名单（设置->省电管理-开启保护e代驾司机端）。'),
				'7'=>array('title'=>'司机端接收不到订单怎么办？','answer'=>'1.首先确认司机端是否有笑脸出现，如果没有请检查手机网络信号或重新登录。2.如果有笑脸仍接不了订单，请点击笑脸->系统接单检测是否正常。3.检查手机时间是否正常，需勾选网络时间。4.重启手机、重启APP，确认笑脸、GPS、接单检测是否正常。如果仍然不能解决问题，请联系公司技术工程师。'),
				'8'=>array('title'=>'自动派单被收回？','answer'=>'由于户外网络存在一定的延迟和信号不稳定因素，在系统自动派单时，如果首选是A司机，预计会将订单派送到A司机。由于系统派单需要司机在30s之内做出反馈，如果司机A由于网络不佳等问题在30秒内没有接到订单，那么系统会把这张订单派给B司机。但派出的订单不能撤回，所以A司机仍然会收到订单，但是由于该订单已经被派给司机B，就提示订单已被系统收回。这里并没有任何潜规则，全部有系统自动计算。6.请一定确认手机网络连接始终为开启状态。'),
				'9'=>array('title'=>'收到的短信出现“EDJORDERTAG#1415...” 乱码怎么办？','answer'=>'短信是系统下发的派单短信，确保在笑脸不出的情况下，也可以正常接单，短信内容请忽略或删除。例如：EDJORDERTAG#1415617060415422#cf8011b7#1415617060#【e代驾】'),
				'10'=>array('title'=>'为什么客户取消了订单，司机这边没收到呢？','answer'=>'可能由于网络原因，推送未同步造成的，退出司机端回到手机桌面，点击设置->应用程序管理->e代驾司机端->清除数据->重新登陆司机端即可。'),
				'11'=>array('title'=>'司机处于欠费屏蔽状态，如何解决？','answer'=>'如果司机通过线下交行划款充值，财务划款时间一般在每天下午4-6点进行，划款成功后，解除欠费屏蔽。只要司机不是手动屏蔽，司机可以登录app，通过app充值成功后自动解除屏蔽。如果在线充值到账后，屏蔽状态还未解除，可能因网络延迟和缓存引起。请操作：司机管理->司机资料->点修改->直接保存即可生效。'),
				'12'=>array('title'=>'司机端显示的代驾次数与真实次数不一致，怎么回事？','answer'=>'代驾次数是每天的下午5点计算前一天的代驾次数，所以当前看到的次数会比真实次数略少。司机报单推迟2天以上的订单，不计入代驾次数。'),
				),
			
			#禁用app包名列表
			'forbid_app'=>array(
				'com.blovestorm',
				'com.noad_chatlocation',
				'com.paopao.app',
				'com.qihoo.antivirus',
				'com.qihoo360.contacts',
				'com.qihoo360.mobilesafe',
				'com.sinovoice.teleblocker',
				'com.txy.anywhere.clone',
				'com.kollway.android.mocklocation',
                // 'net.anylocation',  
                'com.shandian.installer.ui',
                'com.txy.anywhere'
				),
			#远距离叫单开通配置
			'long_distance'=>array(
				'A'=>array(
					'max_tip'=>50,
					'per_kilometer_price'=>4,
					'per_kilometer_time'=>240, 
					'start_kilometer'=>5, #起步的公里数
					'reach_time'=>360  #到达的就位时间单位，单位秒
					),
				'B'=>array(
					'max_tip'=>50,
					'per_kilometer_price'=>3,
					'per_kilometer_time'=>240,
					'start_kilometer'=>5,
					'reach_time'=>360
					),
				'C'=>array(
					'max_tip'=>50,
					'per_kilometer_price'=>2,
					'per_kilometer_time'=>240,
					'start_kilometer'=>5,
					'reach_time'=>360
					),
				'open_city'=>array(
					'1'=>'A', //北京
					'3'=>'A', //上海
					'5'=>'A', //广州
					'6'=>'A', //深圳
					),
				),

			#add by aiguoxin driver score config
			'driverScore'=>array(
        		'A'=>array(
                    'block' =>array(
                        '0'=>'3600',
                        '1'=>'7',
                        '2'=>'7',
                        '3'=>'7',
                        '4'=>'3',
                        '5'=>'3',
                        '6'=>'3',
                        '7'=>'0',
                        '8'=>'0',
                        '9'=>'0',
                        '10'=>'0',
                        '11'=>'0',
                        '12'=>'0',
                        ),
                    'disable_score'=>'9',
                    'recoverScore'=>'12',
                    'recoverTimes'=>'60',
                    'online_time' => '1402268400', // 2014-06-01 07:00:00 '1402268400', //2014-06-09 07:00:00
                   	'all_online_time' => '1405465200',//2014-07-16 07:00:00 全国开通时间，排除非5个城市
                    'rule' =>array(
                        '0'=>array(
                            'norm'=>'拒单率',
                            'mark_norm'=>'本地前5%且拒单3单及以上，扣1分',
                            'type'=>'0',
                            ),
                        '1'=>array(
                            'norm'=>'销单率',
                            'mark_norm'=>'本地前5%且销单4单及以上，扣1分',
                            'type'=>'1',
                            ),
                        ),
                     ),
        		'B'=>array(
                    'block' =>array(
                        '0'=>'3600',
                        '1'=>'7',
                        '2'=>'7',
                        '3'=>'7',
                        '4'=>'3',
                        '5'=>'3',
                        '6'=>'3',
                        '7'=>'0',
                        '8'=>'0',
                        '9'=>'0',
                        '10'=>'0',
                        '11'=>'0',
                        '12'=>'0',
                        ),
                    'disable_score'=>'9',
                    'recoverScore'=>'12',
                    'recoverTimes'=>'60',
                    'online_time' => '1402268400', //2014-06-09 07:00:00
                    'rule' =>array(
                        '0'=>array(
                            'norm'=>'拒单率',
                            'mark_norm'=>'本地前10%内扣一分',
                            'type'=>'0',
                            ),
                        '1'=>array(
                            'norm'=>'销单率',
                            'mark_norm'=>'本地前10%内扣一分',
                            'type'=>'1',
                            ),
                        ),
                    ),
        		'scoreCity'=>array(
        			'1'=>'A', //北京
        			'11'=>'A', //西安
        			'2'=>'A', //成都
        			'10'=>'A', //武汉
        			'19'=>'A', //沈阳
        			// '2'=>'B',
        			),
            ),	
			
								#add by aiguoxin 
								'appVersionCity'=>array(
                                        		'appVersionDriver'=>array(
                                        				 'city'=>'1,2,10,11,19',
                                        			     'latest' =>'2.3.7',
                                        				 'url'=>'http://d.edaijia.cn/driver/driverclient_2.3.7.apk',
                                        			     ),
                                        		'appVersionDriverTwo'=>array(
                                        				 'city'=>'3,6',
                                        			     'latest' =>'2.3.7',
                                        				 'url'=>'http://d.edaijia.cn/driver/driverclient_2.3.7.apk',
                                        			     ),
                                        	),

                                'appVersion'=>array(
                                                    'appVersionIphone'=>array(
                                                                                  'latest'=>'5.4.0',
                                                                                  'deprecated'=>'5.0.0',
                                                                                  'updatetime'=>'2015-04-09',
                                                                                  'url'=>'http://itunes.apple.com/cn/app/id468591734?mt=8',
                                                                                  ),
                                                    'appVersionAndroid'=>array(
                                                                               'latest'=>'5.4.2',
                                                                               'deprecated'=>'5.0.1',
                                                                               'updatetime'=>'2015-04-30',
                                                                               'url'=>'http://d.edaijia.cn/customer/edaijia_android_v5.4.2_web.apk',
                                                                               'ysxurl'=>'http://d.edaijia.cn/customer/edaijia_android_v5.4.2_web.apk',
                                                                               'semurl'=>'http://d.edaijia.cn/customer/edaijia_android_v5.4.2_web.apk',
                                                                               ),
                                                    'appVersionWinphone'=>array(
                                                                                'latest'=>'1.0.2.113',
                                                                                'deprecated'=>'0.1.0',
                                                                                'updatetime'=>'2014-01-16',
                                                                                'url'=>'http://www.windowsphone.com/zh-cn/store/app/e代驾/ee8abf90-f100-4cf3-91c5-29af904f0204',
                                                                                ),


                                                    'appVersionDriver'=>array(
                                                                              'beta_latest'=>'2.1.5', //测试版本号
                                                                              'beta_url'=>'http://d.edaijia.cn/driver/DriverClient_2.1.5.apk', ///测试下载地址
                                                                              'latest'=>'2.5.9',
                                                                              'deprecated'=>'2.3.5',
                                                                              'updatetime'=>'2015-04-03',
                                                                              'url'=>'http://d.edaijia.cn/driver/driverclient_2.5.9_20150331.apk',
                                                                              ),
                                                    'appVersionDriverTest'=>array(
                                                                                  'latest'=>'2.1.5',
                                                                                  'deprecated'=>'2.1.2',
                                                                                  'updatetime'=>'2014-01-06',
                                                                                  'url'=>'http://d.edaijia.cn/driver/DriverClient_2.1.5.apk'
                                                                                  ),

                                                    //使用升级规则
                                                    'appVersionRule'=>array(
                                                                            'is_enabled'=>0, //是否启用范围更新  1 启用  0 不启用
                                                                            'config'=>array(    //版本配置信息
                                                                                            'latest'=>'2.2.3',
                                                                                            'deprecated'=>'2.1.9',
                                                                                            'updatetime'=>'2014-04-11',
                                                                                            'url'=>'http://d.edaijia.cn/driver/driverclient_2.2.3.apk',
                                                                                            ),
            /**
             *
             * 使用此规则的城市 citys 为空 则为所有城市
             *
             * type: 0(全部) 1 (指定工号) 2(指定范围工号)
             *
             * 注 : 目前只支持单一规则
             *
             * example 以北京为例
             *
            type 0 全部司机
            'citys'=>array(
            'BJ'=>array(
            'type'=>0,
            )
            )

            type 1 指定工号

            'citys'=>array(
            'BJ'=>array(
            'type'=>1,
            'drivers'=>array('BJ9006','BJ9002'),
            )
            )

            type 2 指定范围工号

            'citys'=>array(
            'BJ'=>array(
            'type'=>2,
            'drivers'=>array('start'=>9001,'end'=>9010)
            )
            )
             *
             *
             */

            'citys'=>array(
//            	  'SH'=>array( 'type'=>0, ),
//                'HZ'=>array( 'type'=>0, ),
//                'CQ'=>array( 'type'=>0, ),
//                'GZ'=>array( 'type'=>0, ),
//                'SZ'=>array( 'type'=>0, ),
//                'SU'=>array( 'type'=>0, ),
//                'JN'=>array( 'type'=>0, ),
//                'TJ'=>array( 'type'=>0, ),
//                'XA'=>array( 'type'=>0, ),
//                'WH'=>array( 'type'=>0, ),
//                'CS'=>array( 'type'=>0, ),
//                'NJ'=>array( 'type'=>0, ),
//                'CD'=>array( 'type'=>0, ),
//                'BJ'=>array( 'type'=>0, ),
//                'SH'=>array( 'type'=>0, ),
//                'GZ'=>array( 'type'=>0, ),
//                'SZ'=>array( 'type'=>0, ),
//				'BJ'=>array(
//	            	'type'=>1,
//	            	'drivers'=>array('BJ9016','BJ9005','BJ9002','BJ9001','BJ9023','BJ1161','BJ9012','BJ9017'),
//	            ),
            ),
        ),
	),
    'townToCity'=>array(//对指定城市进行订单所属城市转换
        //'三河市'=>'北京',
        '义乌市'=>'义乌'
    ),
    'needCheckCity'=>array(//400派单 和手动派单时通过城市判断 该城市id 是否需要检测 城市从属问题 跟townToCity必须成对出现
        //对所有金华市的400 和手动订单检测 是否属于义乌，如果属于义乌则指定城市id 为义乌
        49,
        //34,
    ),

    //禁用的司机端版本,使用代码在driver.login
    'disable_driver_versions' => array(
        '2.2.5',
    ),

    'kkpinchePushCity'=> array(1,5,6,7,22,21,3,11,4,10,36,2,27,19,8,14,12,15,18,20,29,25,33,9,24),//新增22:厦门，21大连 ，3上海

	'appAccessApiList'=>array(
							'open.nearby',
							'driver.nearby',
							'driver.get',

							'driver.comment.list',
							'app.price',
							'app.city.list',
							'app.city.htlist',  //ADD BY AndyCong
							'customer.account.recharge',
							'customer.login',
							'customer.prelogin',
							'customer.logout',
							//'customer.orderqueue.list',
							'customer.orderqueue.booking',
							'customer.calllog',
							'driver.city.price',
							'customer.city.pricelist',
							'customer.city.currentprice',
							'gps.location', //ADD BY AndyCong

							//新版客户端开放接口
							'c.nearby',
							'c.login',
							'c.prelogin',
                            'c.loginstatus',
							'c.logout',
							'c.city.list',
							'c.city.price',
							'c.driver.info',
							'c.driver.position',
							'c.driver.comment.list',
							'c.order.single',
							'c.order.polling',
							'c.gps.location',
							//增加雷石一键下单接口 BY AndyCong 2013-12-13
							'c.order.booking',
							'c.city.pricelist',

                            //平安绑定优惠券
							'c.coupon.binding',

                            //人保下单查询接口
                            'c.order.thirdbook',
                            'c.order.thirdsearch',
                            'common.pay.ali.alinotify',

			),
	'appTokenList'=>
		array(
		'10000001'=>array('name'=>'iPhone Key','action'=>array('*'),),
		'10000002'=>array('name'=>'android','action'=>array('*'),),
		'20000001'=>array('name'=>'调度系统接口','action'=>array('*'),),
		'30000001'=>array('name'=>'司机客户端 Key','action'=>array('*'),),
		'40000001'=>array('name'=>'Windows Phone','action'=>array('*'),),
		'90000001'=>array('name'=>'Dev Test','action'=>array('*'),),
		'51000012'=>array('name'=>'木仓科技','action'=>array(),),
		'51000013'=>array('name'=>'车辆体检','action'=>array(),),
		'51000015'=>array('name'=>'图吧导航','action'=>array(),),
		'51000016'=>array('name'=>'朋友地图','action'=>array(),),
		'51000017'=>array('name'=>'导航犬','action'=>array(),),
		'51000018'=>array('name'=>'宝马M','action'=>array(),),
		'51000019'=>array('name'=>'北京赛德斯汽车','action'=>array(),),
		'51000020'=>array('name'=>'合力金桥','action'=>array(),),
		'51000021'=>array('name'=>'农商银行','action'=>array(),),
		'51000022'=>array('name'=>'招商银行','action'=>array(),),
		'51000023'=>array('name'=>'互动汽车网','action'=>array(),),
		'51000024'=>array('name'=>'广州联通','action'=>array(),),
		'51000025'=>array('name'=>'中科软太平洋保险','action'=>array(),),
		'51000026'=>array('name'=>'重庆安诚保险','action'=>array(),),
		'51000027'=>array('name'=>'翼周边','action'=>array(),),
		'51000028'=>array('name'=>'深圳车友汇','action'=>array(),),
		'51000029'=>array('name'=>'深圳天天行','action'=>array(),),
		'51000030'=>array('name'=>'交通银行','action'=>array(),),
		'51000031'=>array('name'=>'Html5版本','action'=>array('*'),),

		//高德导航appkey BY AndyCong
		'51000033'=>array('name'=>'高德导航','action'=>array(),),
		'51000061'=>array('name'=>'深圳凯伦圣','action'=>array(),),
		'51000062'=>array('name'=>'深圳悦行车联网','action'=>array(),),
		'51000063'=>array('name'=>'广东车联网','action'=>array(),),
		'61000001'=>array('name'=>'雷石KTV','action'=>array(),),
		'61000010'=>array('name'=>'人保','action'=>array(),),
		'61000020'=>array('name'=>'平安','action'=>array(),),
		//'20140001'=>array('name'=>'风景网','action'=>array(),),

		'90000002'=>array('name'=>'iPhone 91渠道','action'=>array('*'),),
		//20140528
                '61000030'=>array('name'=>'凯立德移动导航','action'=>array('*'),),

		//洗车业务
                '61000097'=>array('name'=>'e代驾洗车','action'=>array('*'),),
	),


	'appContent'=>array(
		'expireAt'=>date('Y-m-d', time()+24*3600),
		'PageContentText'=>array(
			'zh'=>'e代驾真是个实用的APP软件！司机十几分钟就到了，最关键是便宜！22点前10公里才￥39。据说为1600多名司机师傅提供了就业机会--社会企业啊！[赞]推荐弟兄们都下载一个试试~让更多司机师傅能够摆脱黑中介的盘剥【e代驾手机客户端下载地址>>http://wap.edaijia.cn】',
			'en'=>''),
		'RecommendText'=>array(
			'zh'=>'e代驾真是个实用的APP软件！司机十几分钟就到了，最关键是便宜！22点前10公里才￥39。据说为1600多名司机师傅提供了就业机会--社会企业啊！[赞]推荐弟兄们都下载一个试试~让更多司机师傅能够摆脱黑中介的盘剥【e代驾手机客户端下载地址>>http://wap.edaijia.cn】',
			'en'=>''),
		'EmailMessageBody'=>array(
			'zh'=>'e代驾真是个实用的APP软件！司机十几分钟就到了，最关键是便宜！22点前10公里才￥39。据说为1600多名司机师傅提供了就业机会--社会企业啊！[赞]推荐弟兄们都下载一个试试~让更多司机师傅能够摆脱黑中介的盘剥【e代驾手机客户端下载地址>>http://wap.edaijia.cn】',
			'en'=>''),
		'MicBlogMessage'=>array(
			'zh'=>'e代驾真是个实用的APP软件！司机十几分钟就到了，最关键是便宜！22点前10公里才￥39。据说为1600多名司机师傅提供了就业机会--社会企业啊！[赞]推荐弟兄们都下载一个试试~让更多司机师傅能够摆脱黑中介的盘剥【e代驾手机客户端下载地址>>http://wap.edaijia.cn】',
			'en'=>''),
		'SMSBodyText'=>array(
			'zh'=>'e代驾真是个实用的APP软件！司机十几分钟就到了，最关键是便宜！22点前10公里才￥39。据说为1600多名司机师傅提供了就业机会--社会企业啊！[赞]推荐弟兄们都下载一个试试~让更多司机师傅能够摆脱黑中介的盘剥【e代驾手机客户端下载地址>>http://wap.edaijia.cn】',
			'en'=>''),
		'RechargeText'=>array(
			'zh'=>'充值使用说明:\n1.您可以通过e代驾发放的优惠券或者短信获取优惠券号码；\n2.一个新手机号只能使用一次优惠券；\n3.充值成功后，只要通过app呼叫司机使用代驾，该优惠即可立即生效；\n4.优惠券使用的最终解释权归e代驾所有，如有疑问请拨打4006-91-3939咨询；',
			'en'=>''),
		'LocationError'=>array(
			'zh'=>'抱歉，暂时无法获得您的位置，请猛击下方电话，继续享受e代驾提供的服务',
			'en'=>''),
		'NetworkError'=>array(
			'zh'=>'抱歉，您的网络连接中断，请猛击下方电话，继续享受e代驾提供的服务或者尝试重新连接网络！',
			'en'=>''),
		'SearchError'=>array(
			'zh'=>'抱歉，由于附近没有可以为您服务的司机，暂时无法为您提供服务！',
			'en'=>''),
		'LocationEnabledAlert'=>array(
			'zh'=>'请打开您的定位服务,或者授权e代驾的定位服务!',
			'en'=>''),
		'TipsText'=>array(
			'zh'=>'温馨提示：e代驾现已开通北京、上海、广州、深圳、重庆、杭州、南京、长沙、西安、武汉、郑州、成都、济南、天津、青岛的代驾业务，其他城市敬请期待！',
			'en'=>''),
		'priceContent'=>array(
			'title'=>array(
				'zh'=>'e代驾%s服务价格表',
				'en'=>'Edaijia Service Price'),
			'period'=>array(
				'zh'=>'时间段',
				'en'=>'Period'),
			'pricingStart'=>array(
				'zh'=>'起步价(%s公里以内)',
				'en'=>'Pricing Start(less than %s Km)'),
			'memo'=>array(
				'memo'=>array(
					'zh'=>'注：',
					'en'=>'PS:'),
				'unit'=>array(
					'zh'=>'元',
					'en'=>'RMB'),
				'1'=>array(
					'zh'=>'不同时间段的代驾起步费用以实际出发时间为准。',
					'en'=>'bulabula'),
				'2'=>array(
					'zh'=>'代驾距离超过%s公里后，每%s公里加收%s元，不足%s公里按%s公里计算。',
					'en'=>'bulabula'),
				'3'=>array(
					'zh'=>'等候时间每满%s分钟收费%s元，不满%s分钟不收费。',
					'en'=>'bulabula'),
                '4'=> array(
                    'zh'=>'白天%s~%s时段%s元起步（含%s小时、%s公里）， 超出部分每增加%s分钟%s元，代驾距离每增加%s公里加收%s元。超出部分时间不足%s分钟按%s分钟计算，里程不足%s公里按%s公里计算。夜间'
                ),
			),
		),
	),

	'display_activity' => array(
		'end_time' =>'2014-07-14 00:00:00',
		),

    //市场活动
    'activity' => array(
        //南京活动
        '8' => array(
	    // 首单免单
            'free' => array(
                'turn_on'       => true,
                'begin'         => '2014-07-18 00:00:00',
                'end'           => '2014-09-19 23:59:59',
                'customer_msg'  => $activity_free_customer_msg,
                'driver_msg'    => $activity_free_driver_msg,
                'name'          => '南京718活动',
                'channel'       => 27, // EmployeeAccount::CHANNEL_NANJING_ACTIVE
            ),
        ),

        '4' => array(
            'free' => array(
                'turn_on'       => true,
                'begin'         => '2014-09-15 00:00:00',
                'end'           => '2014-11-16 23:59:59',
                'customer_msg'  => $activity_free_customer_msg,
                'driver_msg'    => $activity_free_driver_msg,
                'name'          => '杭州新客免单活动',
                'channel'       => 30, //EmployeeAccount::CHANNEL_HANGZHOU_ACTIVE,
            ),
        ),
    ),

  //android app version
  'app_source' => array(
      '0'=> array('url' => 'http://d.edaijia.cn/customer/edaijia_customer_android_v530_20150112_release_main.apk','title'=>'edaijia'),
      '1'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_PCVban.apk','title'=>'PCVban'),
      '2'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_dituika.apk','title'=>'dituika'),
      '3'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_yaqiantong.apk','title'=>'yaqiantong'),
      '4'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_yangang.apk','title'=>'yangang'),
      '5'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_yicheka.apk','title'=>'yicheka'),
      '6'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_zhuopai.apk','title'=>'zhuopai'),
      '7'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_Xzhanjia.apk','title'=>'Xzhanjia'),
      '8'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_buganjiao.apk','title'=>'buganjiao'),
      '9'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_chetie.apk','title'=>'chetie'),
      '10'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_zheyangban.apk','title'=>'zheyangban'),
      '11'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_shaizhong.apk','title'=>'shaizhong'),
      '12'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_taiyangdang.apk','title'=>'taiyangdang'),
      '13'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_tuilatie.apk','title'=>'tuilatie'),
      '14'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_xishouye.apk','title'=>'xishouye'),
      '15'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_360so.apk','title'=>'360so'),
      '16'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_emar_DSP.apk','title'=>'emar_DSP'),
      '17'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_SEM.apk','title'=>'SEM'),
      '18'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_weixin.apk','title'=>'weixin'),
      '19'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_admob.apk','title'=>'admob'),
      '20'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_sougou.apk','title'=>'sougou'),
      '21'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_ccorder.apk','title'=>'ccorder'),
      '22'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_sms.apk','title'=>'sms'),
      '23'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_cancelorder.apk','title'=>'cancelorder'),
      '24'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400once.apk','title'=>'400once'),
      '25'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400more.apk','title'=>'400more'),
      '26'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_H5gift.apk','title'=>'H5gift'),
      '27'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_H5new.apk','title'=>'H5new'),
      '28'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_H5old.apk','title'=>'H5old'),
      '29'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400bonus.apk','title'=>'400bonus'),
      '30'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400new.apk','title'=>'400new'),
      '31'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400Invoice.apk','title'=>'400Invoice'),
      '32'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-1.apk','title'=>'400_1'),
      '33'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-2.apk','title'=>'400_2'),
      '34'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-3.apk','title'=>'400_3'),
      '35'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-4.apk','title'=>'400_4'),
      '36'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-5.apk','title'=>'400_5'),
      '37'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-6.apk','title'=>'400_6'),
      '38'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-7.apk','title'=>'400_7'),
      '39'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-8.apk','title'=>'400_8'),
      '40'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-9.apk','title'=>'400_9'),
      '41'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-10.apk','title'=>'400_10'),
      '42'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-11.apk','title'=>'400_11'),
      '43'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-12.apk','title'=>'400_12'),
      '44'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-13.apk','title'=>'400_13'),
      '45'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-14.apk','title'=>'400_14'),
      '46'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-15.apk','title'=>'400_15'),
      '47'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-16.apk','title'=>'400_16'),
      '48'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-17.apk','title'=>'400_17'),
      '49'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-18.apk','title'=>'400_18'),
      '50'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-19.apk','title'=>'400_19'),
      '51'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-20.apk','title'=>'400_20'),
      '52'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-21.apk','title'=>'400_21'),
      '53'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-22.apk','title'=>'400_22'),
      '54'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-23.apk','title'=>'400_23'),
      '55'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-24.apk','title'=>'400_24'),
      '56'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-25.apk','title'=>'400_25'),
      '57'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-26.apk','title'=>'400_26'),
      '58'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-27.apk','title'=>'400_27'),
      '59'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-28.apk','title'=>'400_28'),
      '60'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-29.apk','title'=>'400_29'),
      '61'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-30.apk','title'=>'400_30'),
      '62'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-31.apk','title'=>'400_31'),
      '63'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-32.apk','title'=>'400_32'),
      '64'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-33.apk','title'=>'400_33'),
      '65'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-34.apk','title'=>'400_34'),
      '66'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-35.apk','title'=>'400_35'),
      '67'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-36.apk','title'=>'400_36'),
      '68'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-37.apk','title'=>'400_37'),
      '69'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-38.apk','title'=>'400_38'),
      '70'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-39.apk','title'=>'400_39'),
      '71'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-40.apk','title'=>'400_40'),
      '72'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-41.apk','title'=>'400_41'),
      '73'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-42.apk','title'=>'400_42'),
      '74'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-43.apk','title'=>'400_43'),
      '75'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-44.apk','title'=>'400_44'),
      '76'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-45.apk','title'=>'400_45'),
      '77'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-46.apk','title'=>'400_46'),
      '78'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-47.apk','title'=>'400_47'),
      '79'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-48.apk','title'=>'400_48'),
      '80'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-49.apk','title'=>'400_49'),
      '81'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_400-50.apk','title'=>'400_50'),
      '82'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_h5_done.apk','title'=>'h5_done'),
      '83'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_h5_failed.apk','title'=>'h5_failed'),
      '84'=> array('url' => 'http://d.edaijia.cn/customer/sp/edaijia_customer_android_v530_20150112_release_jinritoutiao.apk','title'=>'jinritoutiao'),
  ),

    'daytime_yindao' => array(
        1=> array(
            array(
                'img'=>'http://pic.edaijia.cn/client/yindao01_19.png',
//                'href'=>'http://h5.edaijia.cn/activities/PICC2?from=weixin_app',
            ),
            array(
                'img'=>'http://pic.edaijia.cn/client/yindao02.png',
            ),
            array(
                'img'=>'http://pic.edaijia.cn/client/yindao03.png'
            ),
            array(
                'img'=>'http://pic.edaijia.cn/client/yindao04.png'
            )
        ),
        2 => array(
            array(
                'img'=>'http://pic.edaijia.cn/client/yindao01_29.png',
//                'href'=>'http://h5.edaijia.cn/activities/PICC2?from=weixin_app'
            ),
            array(
                'img'=>'http://pic.edaijia.cn/client/yindao02.png',
            ),
            array(
                'img'=>'http://pic.edaijia.cn/client/yindao03.png'
            ),
            array(
                'img'=>'http://pic.edaijia.cn/client/yindao04.png'
            )
        )
    ),

    'daytime_lunbo'=>array(
        1 => array(
            'http://pic.edaijia.cn/client/lunbo01_19.png',
            'http://pic.edaijia.cn/client/lunbo02.png',
            'http://pic.edaijia.cn/client/lunbo03.png',
            'http://pic.edaijia.cn/client/lunbo04.png',
        ),
        2 =>array(
            'http://pic.edaijia.cn/client/lunbo01_29.png',
            'http://pic.edaijia.cn/client/lunbo02.png',
            'http://pic.edaijia.cn/client/lunbo03.png',
            'http://pic.edaijia.cn/client/lunbo04.png',
        )
    ),

    'daytime_banner' => array(
        1 => array(
            'http://pic.edaijia.cn/client/baner_19_new.png'
        ),
        2 =>array(
            'http://pic.edaijia.cn/client/baner_29_new.png'
        )
    ),
    'driver_study_list'=>array(
        'BJ9024','BJ9063'
    ),
    //用户无星回评功能开关，5.4.3版本支持。on，开启，off,关闭。
    'no_star_comment'=>'on'

);


$test_lock = dirname(__FILE__).'/test.lock';
$dev_lock = dirname(__FILE__).'/dev.lock';

if ( is_file($test_lock) || is_file($dev_lock) ) {
        $_edaijia_config_params['sms_config'] =require_once 'config_sms.php';
        $_edaijia_config_params['queue_pool'] = require_once 'config_queue_pool.php';
}else {
        $_edaijia_config_params['sms_config'] =require_once 'publish/config_sms.php';
        $_edaijia_config_params['queue_pool'] = require_once 'publish/config_queue_pool.php';
}

return $_edaijia_config_params;


