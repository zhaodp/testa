<?php
/**
 * 封装个推消息推送,只需要支持三个方法，单推，全推，还有就是一批client，推相同的内容。
 * 
 * 
 * @auth AndyCong<congming@edaijia.cn> 
 * @version 2013-04-19
 * @refactoring 2013-11-21 dayuer
 */
Yii::import('application.vendors.getui.*');
Yii::import('application.config.*');
require_once ("IGt.Push.php");
require_once ("config_getui.php");
class EPush {
	const LEVEL_LOW=1; //普通级别
	const LEVEL_MIDDLE=2; //中级级别
	const LEVEL_HIGN=3; //最高级别
 
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
		$config = config_getui::get_config_params($version);
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
	 * 直接发送信息到全部app，慎用。
	 * @param unknown $channel
	 * @param unknown $client_id
	 * @param unknown $message
	 */
	public function sendApp($client_id, $message) {
	
		//消息类型 : 状态栏通知 点击通知启动应用
		$template=new IGtNotificationTemplate();
		$template->set_title("e代驾"); //通知栏标题
		$template->set_text($message);
		$template->set_isRing(true); //是否响铃
		$template->set_isVibrate(true); //是否震动
		$template->set_isClearable(true); //通知栏是否可清除
		

		//基于应用消息体
		$message=new IGtAppMessage();
		$message->set_isOffline(false);
		$message->set_offlineExpireTime(1200);
		$message->set_data($template);
		$message->set_appIdList(array($this->_appid));
		
		//$result=$getui->pushMessageToApp($message);
	}
	
	/**
	 * 将要推送的消息压入到消息队列.
	 *
	 * @author yangzhi
	 *        
	 * @param string $app_ver        	
	 * @param string $getui_client_id        	
	 * @param string $message        	
	 */
	private function add_message_to_newpush_queue($app_ver, $getui_client_id, $message, $msg_type = "") {
		$task = array(
				'method' => 'api_asyn_send_message_through_newpush',
				'params' => array(
						'app_ver' => $app_ver,
						'getui_client_id' => $getui_client_id,
						'message' => $message
				)
		);
		
		$queue = config_getui::get_newpush_queue_name($msg_type);
		if (!empty($queue)) {
			Queue::model()->putin($task, $queue);
		}
	}

	/**
	 * 使用新推送, 异步推送消息. (QueueProcess回调方法)
	 * @author yangzhi
	 *
	 * @param array $params
	 */
	public function asyn_send_message_through_newpush($params) {
		return $this->newpush($params['app_ver'], $params['getui_client_id'], $params['message']);
	}
	
	public function newpush($app_ver, $getui_client_id, $message) {
        // 因为目前存储的是getui的clientid，兼容阶段，做一次转换映射操作
        $client_id = DriverStatus::model()->newpush_client($getui_client_id);

        //$url = sprintf("http://42.121.31.228/linker/push/%s/1/2", $client_id);
        $url = sprintf("http://pusher.edaijia.cn/push/%s/1/2", $client_id);
        //$header = "Connection: Keep-Alive";
        $ch = curl_init();                                  // 初始化curl
        curl_setopt($ch, CURLOPT_URL, $url);                // 设置链接
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);               // 请求超时设置
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        // 设置是否返回信息
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $header);      // 设置HTTP头
        curl_setopt($ch, CURLOPT_POST, 1);                  // 设置为POST方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);     // POST数据
        $response = curl_exec($ch);                         // 接收返回信息
        if (curl_errno($ch)) {                             // 出错则显示错误信息
            $err = curl_error($ch);
            EdjLog::info("EPush::newpush ver:$app_ver error:$err g:$getui_client_id cid:$client_id m:$message");
            $res = array("err"=> $err);
        }
        curl_close($ch);                                    // 关闭curl链接

        EdjLog::info("EPush::newpush ver:$app_ver res:$response g:$getui_client_id cid:$client_id m:$message");
        $res = json_decode($response, true);

        //var_dump($res);

        // 把结果映射到getui的返回结果
        // {"taskId":"OSS-0905_Ih69fPguUr676oVlsZO4S6","result":"ok","status":"successed_online"}
        // {"taskId":"OSS-0905_33sm50sL3zAIbmM6xvIdT6","result":"ok","status":"successed_offline"}
        $result = array("result"=>"notpush");
        if (!isset($res["err"]) && isset($res["msgid"]) && isset($res["link"])) {
            $link = $res["link"];
            if ($link == "CLOSED" || $link == "TMPCLOSED") {
                $result = array("taskId"=>sprintf("%d", $res["msgid"]), "result"=>"ok", "status"=>"successed_offline");
            } else {
                $result = array("taskId"=>sprintf("%d", $res["msgid"]), "result"=>"ok", "status"=>"successed_online");
            }

        }
        //var_dump($result);
        return $result;

    }
	
   /**
    * 将个推消息压入队列.
    * 
    * @author yangzhi
    * 
    * @param string $version
    * @param string $app_ver
    * @param string $client_id
    * @param string $push_distinct_id
    * @param array $message
    * @param string $level
    * @param string $offline
    */
   private function add_message_to_getui_queue($version, $app_ver, $client_id, $push_distinct_id, $message, $level, $offline, $msg_type = "") {
    	$task = array(
    		'method' => 'api_asyn_send_message_through_getui',
    		'params' => array(
    			'version' => $version,
    			'app_ver' => $app_ver,
    			'client_id' => $client_id,
    			'push_distinct_id' => $push_distinct_id,
    			'offline' => $offline,
    			'level' => $level,
    			'message' => $message
    		)
    	);

    	//对未进行映射的消息类型, 默认加入到低优先级的对列.
    	if (empty($msg_type)) {
    	    if ($version === 'driver') {
    	        $msg_type = "default";
    	    } else if ($version === 'customer') {
    	        $msg_type = "default_client_push";
    	    } else {
    	        return;
    	    }
    	}
    	
    	$queue = config_getui::get_getui_queue_name($msg_type);
    	
    	EdjLog::info("getui messgae <version-". $version . ", type-". $msg_type . " , queue-" . $queue . ">");
    	 
    	if (!empty($queue)) {
    		Queue::model()->putin($task, $queue);
    	}
    }
    
    /**
     * 直接调用个推发送消息.
     * 
     * @author yangzhi
     * 
     * @param string $version
     * @param string $app_ver
     * @param string $client_id
     * @param string $push_distinct_id
     * @param array $message
     * @param string $level
     * @param string $offline
     */
    private function send_message_through_getui($version, $app_ver, $client_id, $push_distinct_id, $message, $level, $offline) {
    	$params = array(
    		'version' => $version,
    		'app_ver' => $app_ver,
    		'client_id' => $client_id,
    		'push_distinct_id' => $push_distinct_id,
    		'offline' => $offline,
    		'level' => $level,
    		'message' => $message
    	);
    	
    	return $this->asyn_send_message_through_getui($params);
    }
    
	/**
	 * 使用个推, 异步推送消息. (QueueProcess回调方法)
	 * 
	 * @author yangzhi
	 * @param array $params
	 */
	public function asyn_send_message_through_getui($params) {
		$tunnel = $this->_version;
		
		$app_ver = $params['app_ver'];
		$client_id = $params['client_id'];
		$push_distinct_id = $params['push_distinct_id'];
		$level = $params['level'];
		$offline = $params['offline'];
		$msg_check = $params['message'];
		
		$log_util = "id:$push_distinct_id client_id:$client_id app:$app_ver";
		
		if (config_getui::is_new_getui($app_ver) && $this->_version == "driver") {
			$tunnel = config_getui::get_pri_tunnel();
			//var_dump($tunnel);
			if ($tunnel == "driver_double") {
				$this->up_version("driver_ali");
				$result = $this->push($client_id, $msg_check, $level, $offline);
			
				$log_pref = "EPush::send tunnel:double.driver_ali ".$log_util;
				EdjLog::info("$log_pref res:". json_encode($result) . " msg:$msg_check");
			
				$this->up_version("driver_beijing");
				$result = $this->push($client_id, $msg_check, $level, $offline);
			
				$log_pref = "EPush::send tunnel:double.driver_beijing ".$log_util;
				EdjLog::info("$log_pref res:". json_encode($result) . " msg:$msg_check");
			
			} else {
				$this->up_version($tunnel);
				$result =  $this->push($client_id, $msg_check, $level, $offline);
			
				$log_pref = "EPush::send tunnel:single.$tunnel ".$log_util;
				EdjLog::info("$log_pref res:". json_encode($result) . " msg:$msg_check");
			}
		} else {
			$result =  $this->push($client_id, $msg_check, $level, $offline);
			
			$log_pref = "EPush::send tunnel:direct.$tunnel ".$log_util;
			EdjLog::info("$log_pref res:". json_encode($result) . " msg:$msg_check");
		}
		
		return $result;
	}
    
	
	/**
	 * 设置推送消息类型.
	 * 
	 * @param array $message
	 * @param string $type
	 */
	public static function set_message_type(&$message, $type = "") {
		if (!isset($message) || empty($type) || isset($message['_message_type_'])) {
			return;
		}
		
		$message['_message_type_'] = $type;
	}
	
	/**
	 * 移除推送消息类型参数.
	 * 
	 * @param array $message
	 */
	public static function unset_message_type(&$message) {
		if (isset($message) && isset($message['_message_type_'])) {
			unset($message['_message_type_']);
		}
	}
	
	/**
	 * 获取推送消息类型.
	 * 
	 * @param array $message
	 * @return unknown
	 */
	public static function get_message_type($message) {
		if (isset($message) && isset($message['_message_type_'])) {
			return $message['_message_type_'];
		}
	}
	
	/**
	 * 
	 * @param string $client_id
	 * @param string $message
	 * @param int $level
	 * @param int $offline
	 * @return array
	 */
	public function send($client_id, $message, $level=3, $offline=1800) {
        // 检查有木有push_distinct_id
        $msg_check = $message;

        $msg_type = EPush::get_message_type($msg_check);
        EPush::unset_message_type($msg_check);
        
        if ($msg_check) {
            $push_distinct_id = Tools::getUniqId('nomal');
            if (!isset($msg_check['push_distinct_id'])) {
                $msg_check['push_distinct_id'] = $push_distinct_id;
            } else {
                $push_distinct_id = $msg_check['push_distinct_id'];
            }
        }

        $msg_check = json_encode($msg_check);

        //var_dump($msg_check);

        $app_ver = DriverStatus::model()->app_client_ver($client_id);
        //var_dump($app_ver);
        //var_dump($pri_vers);
        //var_dump($app_ver);
        $result = array("result"=>"notpush");
        $tunnel = $this->_version;
        $log_util = "id:$push_distinct_id client_id:$client_id app:$app_ver";
        $is_dupgt = true;
        
        # 新push通道也推送
        if (config_getui::enable_newpush()
				&& config_getui::is_newpush($app_ver)
        		&& $this->_version == "driver") {

            $this->add_message_to_newpush_queue($app_ver, $client_id, $msg_check, $msg_type);
            $is_dupgt = config_getui::is_dupgetui();
        }

        // 个推推送
        if (config_getui::is_new_getui($app_ver) && $this->_version == "driver") {
            if ($is_dupgt) {
            	$this->add_message_to_getui_queue($this->_version, $app_ver, $client_id, $push_distinct_id, $msg_check, $level, $offline, $msg_type);
            }
        } else {
        	//$this->send_message_through_getui($this->_version, $app_ver, $client_id, $push_distinct_id, $msg_check, $level, $offline);
        	$this->add_message_to_getui_queue($this->_version, $app_ver, $client_id, $push_distinct_id, $msg_check, $level, $offline, $msg_type);
        }

        if (config_getui::is_force_sms()) {
        	$result = array(
        		"result"=>"ok",
        		"taskId" => '',
        		"status"=>"successed_offline"
        	);
        } else {
        	$result = array(
        		"result"=>"ok",
        		"taskId" => '',
        		"status"=>"successed_online"
        	);
        }
        
        return $result;
    }

	public function push($client_id, $message, $level, $offline) {
		$template=new IGtTransmissionTemplate();
		$template->set_transmissionType(2); //透传消息类型
		$template->set_appId($this->_appid); //应用appid
		$template->set_appkey($this->_appkey); //应用appkey

        $template->set_transmissionContent($message); //透传内容

		$offline_time = intval($offline);
		
		//个推信息体
		$gt_message=new IGtSingleMessage();
		if ( $level != self::LEVEL_HIGN) {
			if ( $offline_time ) {	
				//是否离线
				$gt_message->set_isOffline(true);		
				if ( $offline_time < 300 || $offline_time > 86400  ) {	
					$offline_time=1800;
				}	
				//离线消息有效时间，过期作废
				$gt_message->set_offlineExpireTime($offline_time);
			}else{
				$gt_message->set_isOffline(false); //是否离线
				$gt_message->set_offlineExpireTime(0);
			}
			
		} else {
			$gt_message->set_isOffline(false);
			$gt_message->set_offlineExpireTime(0);
		}
		//设置推送消息类型
		$gt_message->set_data($template);
		
		//接收方
		$target=new IGtTarget();
		$target->set_appId($this->_appid);
		$target->set_clientId($client_id);
		
		switch (intval($level)) {
			//级别为3时，发送失败并且发送次数小于3次重新发送
			case self::LEVEL_HIGN :
				$i=0;
				while($i<self::LEVEL_HIGN) {
					$result=$this->_igt->pushMessageToSingle($gt_message, $target);
					if ($result['result']=='ok') { //发送成功退出
						break;
					}
					$i++;
				}
				break;
			case self::LEVEL_MIDDLE :
				$i=0;
				while($i<self::LEVEL_MIDDLE) {
					$result=$this->_igt->pushMessageToSingle($gt_message, $target);
					if ($result['result']=='ok') { //发送成功退出
						break;
					}
					$i++;
				}
				break;
			default :
				$result=$this->_igt->pushMessageToSingle($gt_message, $target);
				break;
		}
		return $result;
		
	}

    public static function sms_push($push_msg_id, $phone, $appkey='30000001') {
        if(empty($push_msg_id) || empty($phone)) {
            EdjLog::warning('短信Push|参数错误');
            return false;
        }

        $tag = 'EDJORDERTAG';
        $timestamp = time();
        $sig = '';

        // 生成sig逻辑与ApiController sig_v2一致
        $key = RApiKey::model()->key($appkey);
        if ($key['enable'] == 1) {
            $sig = md5($tag.$push_msg_id.$timestamp.$key['secret']);
            //截断,防止短信内容过长
            $sig = substr($sig, 0, 8);
        }
        else {
            EdjLog::warning('短信Push|'.$push_msg_id.'|RApiKey获取失败');
            return false;
        }

        $content = $tag.'#'.$push_msg_id.'#'.$sig.'#'.$timestamp.'#';
        if(Sms::SendForOrder($phone, $content)) {
            EdjLog::info('短信Push|'.$push_msg_id.'|'.$phone
                .'|'.$content.'|下发成功');
	    return true;
        }
        else {
            EdjLog::warning('短信Push|'.$push_msg_id.'|'.$phone
                .'|'.$content.'|下发失败');
	    return false;
        }
    }
}
