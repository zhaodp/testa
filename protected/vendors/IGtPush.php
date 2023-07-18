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
class IGtPush {
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
	const TYPE_STATUS='status'; //订单状态-针对司机客户端
	const TYPE_BLACK_CUSTOMER='black_customer'; //黑名单
	const TYPE_UPDATE_CONFIG='update_config'; //黑名单
	const TYPE_CMD='cmd'; //黑名单
	const TYPE_ORDER_SUBMIT='order_submit'; //报单
	
	const TYPE_NOTICE_DRIVER_AUDIO='notice_driver_audio'; //语音公告  
	const TYPE_NOTICE_DRIVER_UPY='notice_driver_upy'; //又拍云  
	
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
        $level = isset($params['level']) ? $params['level'] : self::LEVEL_LOW;
		if ($level == self::LEVEL_HIGN) {
            // 这些代码是从后面的实现copy过来的，为了维持函数原来的功能，真不知道当时
            // 写这个代码的人怎么想的
				//判定发送次数，超过Yii::app()->params['DRIVER_REQUEST_NUM']则发短信
				$key=trim($params['queue_id'])."_".trim($params['driver_id']);
				$count=DriverStatus::model()->orderCount($key);
				if (intval($count)>=Yii::app()->params['GETUI_PUSH_NUM']) {
					$sms=$this->GetuiMoveToSms($params['queue_id'], $params['driver_id']);
					if ($sms) {
						return true;
					}
				}
		}

        // 都用epush
        if (!isset($params['message'])) {
		  //return array('result'=> 'no message');
			return false;
        }


        $msg = json_decode($params['message'], true);
        if (!$msg) {
		  //return array('result'=> 'message not json');
		  return false;
        }

        if (!isset($params['client_id'])) {
		  //return array('result'=> 'no clientid');
		  return false;
        }

        $client_id = $params['client_id'];

        $offline = isset($params['offline_time']) ? $params['offline_time'] : 1800;
        //echo "$client_id $level $offline\n";
        //var_dump($msg);
        //var_dump($this->_version);
        $result = EPush::model($this->_version)->send($client_id, $msg, $level, $offline);

		if ($result['result']=='ok') {
			return true;
		} else {
			return false;
		}



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
				//判定发送次数，超过Yii::app()->params['DRIVER_REQUEST_NUM']则发短信
				$key=trim($params['queue_id'])."_".trim($params['driver_id']);
				$count=DriverStatus::model()->orderCount($key);
				if (intval($count)>=Yii::app()->params['GETUI_PUSH_NUM']) {
					$sms=$this->GetuiMoveToSms($params['queue_id'], $params['driver_id']);
					if ($sms) {
						return true;
					}
				}
				//判定发送次数，超过Yii::app()->params['GETUI_PUSH_NUM']则发短信 END
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
		
		if ($result['result']=='ok') {
			return true;
		} else {
			return false;
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
	public function sendMsg($params) {
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
				//判定发送次数，超过Yii::app()->params['DRIVER_REQUEST_NUM']则发短信
				//$key=trim($params['queue_id'])."_".trim($params['driver_id']);
				//$count=DriverStatus::model()->orderCount($key);
//				if (intval($count)>=Yii::app()->params['GETUI_PUSH_NUM']) {
//					$sms=$this->GetuiMoveToSms($params['queue_id'], $params['driver_id']);
//					if ($sms) {
//						return true;
//					}
//				}
				//判定发送次数，超过Yii::app()->params['GETUI_PUSH_NUM']则发短信 END
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
		
	}
	

	/**
     * 发送订单消息
     * @param int $queue_id
     * @param string $driver_id
     * @param string $message
     * @return boolean
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-05-09
     */
	public function GetuiMoveToSms($queue_id = 0 , $driver_id = '' , $type = '') {
		//判定类型 如果为组长直接发送组长信息
		if (IGtPush::TYPE_MSG_LEADER == $type) {
			$queue = OrderQueue::model()->findByPk($queue_id);
			if ($queue) {
				$leader = Order::model()->sendLeaderSmsGroupMemberMsg($queue_id);
				$driver_leader = DriverStatus::model()->get($leader['driver_id']);
				$leader_phone = $driver_leader->phone;
				$leader_msg = $leader['msg'];
				$result_leader_sms = Sms::SendSMS($leader_phone, $leader_msg);
				if ( $result_leader_sms ) {
					$sql="UPDATE t_message_log SET `flag`=2 WHERE `queue_id` = :queue_id AND driver_id = :driver_id AND type=:type ";
					$result_msg_up = Yii::app()->dbreport->createCommand($sql)->execute(array(
					    ":queue_id" => $queue_id,
					    ":driver_id" => $driver_id,
					    ":type" => $type,
					));
					
					OrderQueue::model()->updateByPk($queue_id,array('flag'=>OrderQueue::QUEUE_SUCCESS , 'dispatch_agent' => '自动派单' , 'dispatch_time'=>date('Y-m-d H:i:s' , time())));
					//更新message状态
                    return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
		//判定类型 如果为组长直接发送组长信息 END
		
		//发送短信
		$result = false;
		$msg_info=Order::model()->setMsgSms($driver_id, $queue_id);
		$msg=self::SMS_PRE.$msg_info['msg'];
		//获取司机电话
		$driver = DriverStatus::model()->get($driver_id);
		$phone=$driver->phone;
		$sms_ret = Sms::SendSMS($phone, $msg);
		if ( $sms_ret ) {
			//判定是不是最后一个组员 如果是最后一个组员 更新OrderQueue表中flag为4
			$queue = OrderQueue::model()->findByPk($queue_id);
			if ($queue) {
				$count = OrderQueueMap::model()->count("queue_id = :queue_id" , array(":queue_id"=>$queue_id));
				if (1 != $queue->number && $queue->number == $count) {
					$leader = Order::model()->sendLeaderSmsGroupMemberMsg($queue_id);
					$driver_leader = DriverStatus::model()->get($leader['driver_id']);
					$leader_phone = $driver_leader->phone;
					$leader_msg = $leader['msg'];
					$result_leader_sms = Sms::SendSMS($leader_phone, $leader_msg);
					if ( $result_leader_sms ) {
						
						//获取评论
						$queue_arr = array(
						    'queue_id' => $queue_id,
						    'comments' => $queue->comments,
						);
						$comments = Order::model()->getQueueComments($queue_arr);
						//获取评论 END
						
						OrderQueue::model()->updateByPk($queue_id,array('flag'=>OrderQueue::QUEUE_SUCCESS , 'comments' => $comments ,'dispatch_agent' => '自动派单' , 'dispatch_time'=>date('Y-m-d H:i:s' , time())));
					} else {
						return $result;
					}
				}
			} else {
				return $result;
			}
			//更新message状态
			if (empty($type)) {
				$type = self::TYPE_ORDER_DETAIL;
			}
			$sql="UPDATE t_message_log SET `flag`=2 WHERE `queue_id` = :queue_id AND driver_id = :driver_id AND type=:type ";
			$result_msg_up = Yii::app()->dbreport->createCommand($sql)->execute(array(
			    ":queue_id" => $queue_id,
			    ":driver_id" => $driver_id,
			    ":type" => $type,
			));
			//更新message状态 END
			if ($result_msg_up) {
				$result = true;
			}
			//判定是不是最后一个组员 如果是最后一个组员 更新OrderQueue表中flag为4 END
		}
		return $result;
	}

	/**
	 * （通过ClientID列表）群推,(废弃 不用);
	 * @param array $params业务层传的参数; client_id、message、level三个参数必须,offline_time 非必须（离线时间 数字）
	 * @param string $version
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-04-19
	 */
	public static function PushToList($params, $version='driver') {
		//获取配置信息
		$config=config_getui::get_config_params($version);
		//获取配置信息 END
		$this->_igt=new IGeTui($config['HOST'], $config['APPKEY'], $config['MASTERSECRET']);
		//消息类型 :状态栏链接 点击通知打开网页 
		$template=new IGtLinkTemplate();
		$template->set_appId($config['APPID']); //应用appid
		$template->set_appkey($config['APPKEY']); //应用appkey
		$template->set_title('e代驾'); //通知栏标题
		$template->set_text($params['message']); //通知栏内容
		//		$template ->set_logo("http://wwww.igetui.com/logo.png");//通知栏logo
		$template->set_isRing(true); //是否响铃
		$template->set_isVibrate(true); //是否震动
		$template->set_isClearable(true); //通知栏是否可清除
		//		$template ->set_url("http://www.igetui.com/");//打开连接地址
		

		//个推信息体
		$message=new IGtSingleMessage();
		$message->set_isOffline(true); //是否离线
		if (isset($params['offline_time'])) {
			$offline_time=intval($params['offline_time']);
		} else {
			$offline_time=1800;
		}
		$message->set_offlineExpireTime($offline_time); //离线时间
		$message->set_data($template); //设置推送消息类型
		$contentId=$this->_igt->getContentId($params['message']);
		//接收方
		if (!empty($params['client_id'])) {
			$targetList=array();
			$i=1;
			foreach($params['client_id'] as $val) {
				$target.$i=new IGtTarget();
				$target.$i->set_appId($config['APPID']);
				$target.$i->set_clientId($val);
				$targetList[]=$target.$i;
				$i++;
			}
		} else {
			//默认推送全部
			$targetList='???';
		}
		
		//级别为3时，发送失败并且发送次数小于3次重新发送，
		switch (intval($params['level'])) {
			case self::LEVEL_HIGN :
				$i=0;
				while($i<self::LEVEL_HIGN) {
					$result=$this->_igt->pushMessageToList($contentId, $targetList);
					if ($result['result']=='ok') { //发送成功退出
						break;
					}
					$i++;
				}
				break;
			case self::LEVEL_MIDDLE :
				$i=0;
				while($i<self::LEVEL_MIDDLE) {
					$result=$this->_igt->pushMessageToList($contentId, $targetList);
					if ($result['result']=='ok') { //发送成功退出
						break;
					}
					$i++;
				}
				break;
			case self::LEVEL_LOW :
				$result=$this->_igt->pushMessageToList($contentId, $targetList);
				break;
			default :
				$result=$this->_igt->pushMessageToList($contentId, $targetList);
				break;
		}
		
		if ($result['result']=='ok') {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * （通过应用AppID列表）群推，给所有符合条件的客户端用户推送
	 * @param array $params业务层传的参数; message、level三个参数必须,offline_time 非必须（离线时间 数字）
	 * @param string $version
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-04-19
	 */
	static function PushToApp($params, $version='driver') {
		//消息类型 : 状态栏通知 点击通知启动应用
		$template=new IGtNotificationTemplate();
		$template->set_appId($this->_appid); //应用appid
		$template->set_appkey($this->_appkey); //应用appkey
		$template->set_transmissionType(2); //透传消息类型
		$template->set_transmissionContent($params['message']); //透传内容
		$template->set_title("e代驾"); //通知栏标题
		$template->set_text($params['message']);
		$template->set_isRing(true); //是否响铃
		$template->set_isVibrate(true); //是否震动
		$template->set_isClearable(true); //通知栏是否可清除
		

		//基于应用消息体
		$message=new IGtAppMessage();
		$message->set_isOffline(true);
		if (isset($params['offline_time'])) {
			$offline_time=intval($params['offline_time']);
		} else {
			$offline_time=600;//推离线缩短为5分钟延迟
		}
		$message->set_offlineExpireTime($offline_time);
		$message->set_data($template);
		$message->set_appIdList($this->_appid);
		
		//级别为3时，发送失败并且发送次数小于3次重新发送，
		switch (intval($params['level'])) {
			case self::LEVEL_HIGN :
				$i=0;
				while($i<self::LEVEL_HIGN) {
					$result=$this->_igt->pushMessageToApp($message);
					if ($result['result']=='ok') { //发送成功退出
						break;
					}
					$i++;
				}
				break;
			case self::LEVEL_MIDDLE :
				$i=0;
				while($i<self::LEVEL_MIDDLE) {
					$result=$this->_igt->pushMessageToApp($message);
					if ($result['result']=='ok') { //发送成功退出
						break;
					}
					$i++;
				}
				break;
			case self::LEVEL_LOW :
				$result=$this->_igt->pushMessageToApp($message);
				break;
			default :
				$result=$this->_igt->pushMessageToApp($message);
				break;
		}
		
		if ($result['result']=='ok') {
			return true;
		} else {
			return false;
		}
	}
}
