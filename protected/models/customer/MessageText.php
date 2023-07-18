<?php
class MessageText
{

	/**
	 * 给客户发送的信息
	 */
	//优惠券充值成功
	const CUSTOMER_BONUS_RECHARGE_SUCCESS_TEXT = 1;		//优惠券充值成功提示文本 
	const CUSTOMER_BONUS_RECHARGE_SUCCESS_SMS = 2;		//优惠券充值成功短信
		
	const CUSTOMER_DRIVER_DIRECT = 3;					//直接拨打司机电话,派单通知客户短信     
	const CUSTOMER_CALLCENTER_DISPATCH = 4;				//400 派单短信
	
	const CUSTOMER_ORDER_COMMENT = 5;					//客户评价司机短信
	const CUSTOMER_ORDER_CANCEL = 6;					//客户反馈司机是否销单短信
	const CUSTOMER_BONUS_USED = 7;						//客户优惠券已消费短信
	const CUSTOMER_VIP_USED = 8;						//客户Vip已消费短信
	const CUSTOMER_NEW_CUSTOMER_BONUS_RECHANGE_SUCCESS_SMS = 9; //新客邀请码充值成功短信
	const CUSTOMER_VIP_SLAVE_USED = 10;					//客户Vip副卡已消费短信
	const CUSTOMER_VIP_ACTIVE = 11;						//客户Vip卡激活短信
	const NEW_CUSTOMER_ORDER_COMMENT = 46;				//新的客户评价司机短信
	const NEW_CUSTOMER_ORDER_CANCEL = 47;				//新的客户反馈司机是否销单短信	
	const CUSTOMER_NOTICE_CONNECT_PHONE = 48;				//代叫司机给被叫手机号发送通知短信
	
	
	/**
	 * 给司机发送的信息
	 */	
	const DRIVER_PRICE_LIST = 31;						//代驾价格表短信
	const DRIVER_CUSTOMER_BONUS = 32;					//优惠券客户提示短信
	const DRIVER_CUSTOMER_VIP = 33;						//VIP客户提示短信
	const DRIVER_CALLCENTER_DISPATCH_ONE = 34;			//呼叫中心单个司机派单短信
	const DRIVER_CALLCENTER_DISPATCH_GROUP_LEADER = 35; //呼叫中心多个司机派单组长短信
	const DRIVER_CALLCENTER_DISPATCH_GROUP_MEMBER = 36;	//呼叫中心多个司机派单组员短信
	const DRIVER_PRICE_LIST_CQ = 37;					//代驾价格表短信-重庆	
	const DRIVER_PRICE_LIST_HZ = 38;					//代驾价格表短信-杭州	
	const DRIVER_TRAINING = 39;							//司机通知培训短信
	const DRIVER_CUSTOMER_VIP_FIXED = 40;				//VIP定额卡客户提示短信
	const DRIVER_ACCOUNT_LACK = 41;						//司机信息费欠费短信
	const DRIVER_ORDER_NOT_CONFIRM = 42;				//司机未报单短信
	const DRIVER_MARK_AUTO = 43;						//司机欠费自动屏蔽短信	
	const DRIVER_MARK_MANUAL = 44;						//司机差评手动屏蔽短信
	const DRIVER_MARK_CANCEL = 45;						//司机取消屏蔽,恢复正常短信
	
	const DRIVER_PRICE_LIST_WX = 49;						//代驾价格表短信-省会城市以下的普通城市价格信息
	const CANCEL_ORDER_QUEUE = 50;						//点击派单队列页面等待派单状态的取消按钮发送短信

	static $type = array (
		'1' => '客户',
		'2' => '司机'
	);
	
	static $messageDesc = array (
	
		//发给客户
		self::CUSTOMER_BONUS_RECHARGE_SUCCESS_TEXT => array (
			'type' => '1',
			'desc' => '优惠券充值成功提示文本 ',
			'content' => '优惠券充值成功提示文本 ',
			'var_num' => 2,
			'vars' => array ( '手机号', '金额')
		),
		self::CUSTOMER_BONUS_RECHARGE_SUCCESS_SMS => array (
			'type' => '1',		
			'desc' => '优惠券充值成功短信',
			'var_num' => 4,
			'vars' => array ( '优惠券名称', '优惠券号码', '优惠券金额', 'App下载地址:注意后面要加\n')		
		),
		self::CUSTOMER_DRIVER_DIRECT => array (
			'type' => '1',		
			'desc' => '直接拨打司机电话,派单通知客户短信 ',
			'var_num' => 3,
			'vars' => array ( '司机工号', '司机姓名', '司机手机号')		
		),
		self::CUSTOMER_CALLCENTER_DISPATCH => array (
			'type' => '1',		
			'desc' => '400 派单短信,派单通知客户短信 ',
			'var_num' => 3,
			'vars' => array ( '司机工号', '司机姓名', '司机手机号')		
		),
		self::CUSTOMER_ORDER_COMMENT => array (
			'type' => '1',			
			'desc' => '客户评价司机短信 ',
			'var_num' => 1,
			'vars' => array ( '司机姓名' )			
		),
		self::CUSTOMER_ORDER_CANCEL => array (
			'type' => '1',			
			'desc' => '客户反馈司机是否销单短信 ',
			'var_num' => 1,
			'vars' => array ( '司机姓名' )		
		),
		self::NEW_CUSTOMER_ORDER_COMMENT => array (
			'type' => '1',			
			'desc' => '客户评价司机短信新内容 ',
			'var_num' => 1,
			'vars' => array ( '司机姓名' )		
		),
			
			self::NEW_CUSTOMER_ORDER_CANCEL => array (
					'type' => '1',
					'desc' => '客户反馈司机是否销单短信新内容 ',
					'var_num' => 1,
					'vars' => array ( '司机姓名' )
			),
		self::CUSTOMER_BONUS_USED => array (
			'type' => '1',			
			'desc' => '客户优惠券已消费短信 ',
			'var_num' => 6,
			'vars' => array ( '消费日期', '消费金额', '优惠券名称', '优惠券号码', '优惠金额','现金交付金额' )			
		),
		
		self::CUSTOMER_VIP_USED => array (
			'type' => '1',			
			'desc' => 'VIP客户已消费短信',
			'var_num' => 7,
			'vars' => array ( 'VIP客户姓名', '消费日期', '出发地点', '到达地点', '路程公里数', '消费金额','VIP卡余额' )			
		
		),
		
		self::CUSTOMER_NEW_CUSTOMER_BONUS_RECHANGE_SUCCESS_SMS => array (
			'type' => '1',			
			'desc' => '新客邀请码充值成功短信',
			'var_num' => 2,
			'vars' => array ( '手机尾号', 'App下载地址:注意后面要加\n')			
		),
		
		self::CUSTOMER_VIP_SLAVE_USED => array (
			'type' => '1',			
			'desc' => 'VIP客户副卡已消费短信',
			'var_num' => 8,
			'vars' => array ( 'VIP客户姓名', 'VIP副卡手机号', '消费日期', '出发地点', '到达地点', '路程公里数', '消费金额','VIP卡余额' )				
		
		),	

		self::CUSTOMER_VIP_ACTIVE => array (
			'type' => '1',			
			'desc' => 'VIP客户卡激活短信',
			'var_num' => 1,
			'vars' => array ( 'VIP卡金额' )			
		
		),		
                self::CUSTOMER_NOTICE_CONNECT_PHONE => array (
		        'type' => '1',			
			'desc' => '代叫司机给被叫手机号发送通知短信',
			'var_num' => 2,
			'vars' => array ( '下单用户手机号', '订单数量' )
		),
                49 => array (
                        'type' => '1',
                        'desc' => '收费标准',
                        'var_num' => 0,
                        'vars' => array ()
                ),
		
		//发给司机
		self::DRIVER_PRICE_LIST => array (
			'type' => '2',		
			'desc' => '代驾价格表短信 ',
			'var_num' => 0,
			'vars' => array ( )			
		),
		self::DRIVER_PRICE_LIST_CQ => array (
			'type' => '2',		
			'desc' => '代驾价格表短信 -重庆',
			'var_num' => 0,
			'vars' => array ( )		
		),
		self::DRIVER_PRICE_LIST_HZ => array (
			'type' => '2',		
			'desc' => '代驾价格表短信-杭州 ',
			'var_num' => 0,
			'vars' => array ( )		
		),
				
		self::DRIVER_CUSTOMER_BONUS => array (
			'type' => '2',		
			'desc' => '发给司机优惠券客户提示短信 ',
			'var_num' => 2,
			'vars' => array ('客户手机号', '优惠券金额')				
		),
		
		self::DRIVER_CUSTOMER_VIP => array (
			'type' => '2',		
			'desc' => '发给司机Vip客户提示短信 ',
			'var_num' => 4,
			'vars' => array ('客户手机号', 'VIP客户姓名', 'VIP卡号', 'VIP账户余额')			
		),
		
		self::DRIVER_CALLCENTER_DISPATCH_ONE => array (
			'type' => '2',			
			'desc' => '呼叫中心单个司机派单短信 ',
		),		
	
		self::DRIVER_CALLCENTER_DISPATCH_GROUP_LEADER => array (
			'type' => '2',			
			'desc' => '呼叫中心多个司机派单组长短信 ',
		),	

		self::DRIVER_CALLCENTER_DISPATCH_GROUP_MEMBER => array (
			'type' => '2',			
			'desc' => '呼叫中心多个司机派单组员短信',
		),
		
		self::DRIVER_TRAINING => array (
			'type' => '2',			
			'desc' => '司机通知培训短信',
		),

		self::DRIVER_CUSTOMER_VIP_FIXED => array (
			'type' => '2',			
			'desc' => 'VIP礼品卡定额卡客户提示短信 ',
			'var_num' => 3,
			'vars' => array ('客户手机号', 'VIP卡号', 'VIP账户余额')			
		),	
				
		self::DRIVER_ACCOUNT_LACK => array (
			'type' => '2',			
			'desc' => '司机信息费欠费短信 ',
		),		
		
		self::DRIVER_ORDER_NOT_CONFIRM => array(
			'type' => '2',			
			'desc' => '司机未报单短信 '
		),
					
		self::DRIVER_MARK_AUTO => array(
			'type' => '2',			
			'desc' => '司机欠费自动屏蔽短信 '
		),		

		self::DRIVER_MARK_MANUAL => array(
			'type' => '2',			
			'desc' => '司机差评手动屏蔽短信 '
		),			
		
		self::DRIVER_MARK_CANCEL => array(
			'type' => '2',			
			'desc' => '司机取消屏蔽,恢复正常短信 '
		),			
		self::DRIVER_PRICE_LIST_WX => array(
			'type' => '2',
			'desc' => '省会城市以下的普通城市代驾价格信息 '
		),
		self::CANCEL_ORDER_QUEUE => array(
			'type' => '2',
			'desc' => '单队列页面等待派单状态的取消按钮发送短信'
		),

		
	);
	
	

	//邀请码
	/**
	 * 邀请码分享
	 */
	const APP_INVITE_TEXT_TOP = 1; 
	const APP_INVITE_WEIBO_SHARE_TEXT = 1; 
	const APP_INVITE_SMS_SHARE_TEXT = 1;
	const APP_INVITE_TEXT = 1;
	
//	const INVITE_TEXT = "1. 每个用户可以获得一个邀请码，您可以将此邀请码通过短信、微博发送给好友或者直接口头告诉朋友，邀请他使用e代驾；\n2. 此邀请码价值10元，限晚10点前、10公里内。如超出须补差额。仅限App绑定充值，出示给司机无效;\n3. 邀请码使用的最终解释权归e代驾所有；";
	const INVITE_TEXT = "";
	const INVITE_TEXT_TOP = "为什么邀请好友使用e代驾？\n 在饭局聚会的时候，是否有朋友因为开车不能喝酒而扫兴？赶紧邀请他使用e代驾，即解决了他的后顾之忧，告诉他邀请码还送10元优惠券，自己也倍有面子！";
	const INVITE_WEIBO_SHARE_TEXT = "e代驾真的很实用！司机十几分钟就到了,最关键是便宜！22点前起步价才￥39.下载客户端>>http://t.cn/zj5nQ8D，输入邀请码%s，立即获得10元优惠,大家大力转发吧！"	;
	const INVITE_SMS_SHARE_TEXT = "哥们儿,送你e代驾的10元邀请码:%s, 下载客户端>>http://t.cn/zj5nY68\n输入绑定. e代驾还是挺实用，司机十几分钟就到了,关键是便宜！22点前起步价才￥39，告诉你的酒友哦！";	

	public static function getFormatContent($code){
		$params = func_get_args();
		$format = DictContent::item('message', $code);
		if (!empty($format))
			return self::formatMessage($format, $params);
		return '';
	}
	
	private static function formatMessage($format, $params){
		$strResult = $format;
		foreach($params as $k=>$v){
			$strResult = str_replace('\\'.$k, $v, $strResult);
		}

		$strResult = str_replace('\n', "\n", $strResult);		
		return $strResult;
	}
	
	public function init(){
		ksort(self::$messageDesc);
		foreach (self::$messageDesc as $key => $value)
		{		
			$model = DictContent::model()->find("dictname='message' and code='$key'");
			
			if (!$model)
			{
				$desc = $value['desc'];
				$modelMessage = new DictContent();
				$dataMessage = array (
					'dictname' => 'message',
					'code' => $key,
					'name' => $desc,
					'postion' => $key
				);
				$modelMessage->attributes = $dataMessage;
				$modelMessage->insert();
			}
			
		}
		
	}
	
	
}
