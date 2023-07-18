<?php
/**
 * 封装个推消息推送
 * @auth AndyCong<congming@edaijia.cn> 
 * @version 2013-04-19
 */
Yii::import('application.vendors.getui.*');
Yii::import('application.config.*');
require_once ("IGt.Push.php");
require_once ("config_getui.php");
class GetuiPush {
	const LEVEL_HIGN=3; //最高级别
	const LEVEL_MIDDLE=2; //中级级别
	const LEVEL_LOW=1; //普通级别

	//定义发送类型
	const TYPE_MSG_DRIVER='msg_driver'; //司机消息
	const TYPE_MSG_CUSTOMER='msg_customer'; //客户消息
	const TYPE_MSG_LEADER='msg_leader'; //客户消息
	const TYPE_NOTICE_DRIVER='notice_driver'; //司机公告
	const TYPE_NOTICE_CUSTOMER='notice_customer'; //客户公告
	const TYPE_ORDER='order'; //订单-针对司机
	const TYPE_ORDER_DETAIL='order_detail'; //订单-订单详情
	const TYPE_ORDER_AUDIO_DETAIL='order_audio_detail'; //订单-订单详情
	const TYPE_STATUS='status'; //订单状态-针对司机客户端
	const TYPE_BLACK_CUSTOMER='black_customer'; //黑名单
	const TYPE_UPDATE_CONFIG='update_config'; //黑名单
	const TYPE_CMD='cmd'; //黑名单
	const TYPE_ORDER_SUBMIT='order_submit'; //报单
	
	const TYPE_ORDER_CANCEL='order_cancel'; //取消订单
	const TYPE_ORDER_NEW_DETAIL='order_new'; //新订单详情

    // @author zhanglimin 2013-10-12
    const DC_DRIVER_TYPE_ORDER_DETAIL = 'dc_driver_order_detail';    // 返程车司机推送订单详情
    const DC_CUSTOMER_TYPE_ORDER = 'dc_customer_order';  // 返程车客户推送订单

	//定义短信内容前缀
	const SMS_PRE = '';
	
	//定义配置
	protected static $_models=array();
	private $_appkey;
	private $_appid;
	private $_mastersecret;
	private $_igt;
	private $_version;

	public static function model($version='driver',$className=__CLASS__) {
		$model=null;
		if (isset(self::$_models[$className]))
			$model=self::$_models[$className];
		else {
			$model=self::$_models[$className]=new $className($version);
		}
        $model->up_version($version);
		return $model;
	}

	public function __construct($version) {
        $this->up_version($version);
    }

	public function up_version($version) {
		//获取配置信息
		$config=config_getui::get_config_params($version);
        $this->_version=$version;
		//获取配置信息 END
		$this->_igt=new IGeTui($config['HOST'], $config['APPKEY'], $config['MASTERSECRET']);
		$this->_appid=$config['APPID'];
		$this->_appkey=$config['APPKEY'];
		$this->_mastersecret=$config['MASTERSECRET'];
        //var_dump($config);
	}

	public function __destruct() {
		if ($this->_igt) {
		}
	}

	/**
	 * 单推消息
	 * @param array $params业务层传的参数; client_id、message、level三个参数必须 driver_user,offline_time 非必须（离线时间 数字）
	 *              $params['message']消息体 json格式:type、message_id、content、time。注（type：msg-消息、order-订单、notice-公告、status-状态）
	 *                                      当type=order时 message中需加入queue_id、customer_name、customer_phone、address、booking_time
	 *                                      当type=status时 message中加入status=0、1
	 * @param string $version 非必须（默认：用户客户端）
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-04-19
	 */
	public function PushToSingle($params) {
        // 都用epush
        if (!isset($params['message'])) {
            return array('result'=> 'no message');
        }


        $msg = json_decode($params['message'], true);
        if (!$msg) {
            return array('result'=> 'message not json');
        }

        if (!isset($params['client_id'])) {
            return array('result'=> 'no clientid');
        }

        $client_id = $params['client_id'];
        $level = isset($params['level']) ? $params['level'] : self::LEVEL_LOW;
        $offline = isset($params['offline_time']) ? $params['offline_time'] : 1800;
        //echo "$client_id $level $offline\n";
        //var_dump($msg);
        //var_dump($this->_version);
        return EPush::model($this->_version)->send($client_id, $msg, $level, $offline);


		$template=new IGtTransmissionTemplate();
		$template->set_transmissionType(2); //透传消息类型
		$template->set_appId($this->_appid); //应用appid
		$template->set_appkey($this->_appkey); //应用appkey
		$template->set_transmissionContent($params['message']); //透传内容


		//个推信息体
		$message=new IGtSingleMessage();
		if ($params['level']!=self::LEVEL_HIGN) {
			$message->set_isOffline(true); //是否离线
			if (isset($params['offline_time'])) {
				$offline_time=intval($params['offline_time']);
			} else {
				$offline_time=1800;
			}
			$message->set_offlineExpireTime($offline_time); //离线时间
		} else {
			$message->set_isOffline(false); //是否离线
			$message->set_offlineExpireTime(0);
		}
		$message->set_data($template); //设置推送消息类型
		//接收方
		$target=new IGtTarget();
		$target->set_appId($this->_appid); //应用appid
		$target->set_clientId($params['client_id']); //client_id
		//级别为3时，发送失败并且发送次数小于3次重新发送，

		switch (intval($params['level'])) {
			case self::LEVEL_HIGN :
				$i=0;
				while($i<self::LEVEL_HIGN) {
					$result=$this->_igt->pushMessageToSingle($message, $target);
					if ($result['result']=='ok') { //发送成功退出
						break;
					}
					$i++;
				}
				break;
			case self::LEVEL_MIDDLE :
				$i=0;
				while($i<self::LEVEL_MIDDLE) {
					$result=$this->_igt->pushMessageToSingle($message, $target);
					if ($result['result']=='ok') { //发送成功退出
						break;
					}
					$i++;
				}
				break;
			case self::LEVEL_LOW :
				$result=$this->_igt->pushMessageToSingle($message, $target);
				break;
			default :
				$result=$this->_igt->pushMessageToSingle($message, $target);
				break;
		}
		return $result;
		
		//
		// if ($result['result']=='ok') {
		//	return true;
		//} else {
		//	return false;
		//}
	}
}
