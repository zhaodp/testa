<?php

/**
 * This is the model class for table "{{push_message}}".
 *
 * The followings are the available columns in table '{{push_message}}':
 * @property integer $id
 * @property string $type
 * @property string $content
 * @property integer $city_id
 * @property integer $level
 * @property string $version
 * @property string $pre_send_time
 * @property integer $user_id
 * @property integer $status
 * @property integer $del_flag
 * @property string $created 
 */
class PushMessage extends ReportActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PushMessage the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{push_message}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, content, pre_send_time, user_id, created', 'required'),
			array('city_id, level, user_id, status, del_flag', 'numerical', 'integerOnly'=>true),
			array('type, version', 'length', 'max'=>10),
			array('content', 'length', 'max'=>3000),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, type, content, city_id, level, version, pre_send_time, user_id, status, del_flag, created', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'type' => 'Type',
			'content' => 'Content',
			'city_id' => 'City',
			'level' => 'Level',
			'version' => 'Version',
			'pre_send_time' => 'Pre Send Time',
			'user_id' => 'User',
			'status' => 'Status',
			'del_flag' => 'Del Flag',
			'created' => 'Created',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('type',$this->type);
		$criteria->compare('content',$this->content);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('level',$this->level);
		$criteria->compare('version',$this->version);
		$criteria->compare('pre_send_time',$this->pre_send_time);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('del_flag',$this->del_flag);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * 查询ClientID信息
	 * @param int $city_id
	 * @param int $mark
	 * @return array $clients;
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-05-07
	 */
	public function getClientsByDrivers($drivers) {
		$driver_id_str = '';
		foreach ($drivers as $key=>$val) {
			$driver_id_str .= "'".$val['user']."',";
		}
		$driver_id_str = substr($driver_id_str , 0 , strlen($driver_id_str)-1);
		$sql = "SELECT client_id,driver_id FROM t_getui_client WHERE driver_id IN(".$driver_id_str.")";
		$clients = GetuiClient::getDbMasterConnection()->createCommand($sql)->queryAll();
		return $clients;
	}
	
	/**
	 * 通过四级工号查询ClientID信息
	 * @param array $drivers
	 * @return array $clients
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-05-07
	 */
	public function getClientsByDriverID($drivers = array()) {
		$driver_id_str = '';
		foreach ($drivers as $key=>$val) {
			$driver_id_str .= "'".$val."',";
		}
		$driver_id_str = substr($driver_id_str , 0 , strlen($driver_id_str)-1);
		$sql = "SELECT client_id,driver_id FROM t_getui_client WHERE driver_id IN(".$driver_id_str.")";
		$clients = GetuiClient::getDbMasterConnection()->createCommand($sql)->queryAll();
		return $clients;
	}
	
	/**
	 * 记录推送消息记录
	 * @param array $clients
	 * @param array $data
	 * @return boolean
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-05-07
	 */
	function insertRecord($clients = array() , $data = array()) {
		if (empty($data['content']) || empty($data['pre_send_time'])) {
			return false;
		}
		
		$sql = "INSERT INTO t_push_message(`client_id`,`driver_id`,`type`,`content`,`city_id`,`level`,`version`,`pre_send_time`,`user_id`,`created`) VALUES";
		if (isset($data['version'])) {
			$type = $data['type']."_".$data['version'];
		} else {
			$type = $data['type']."_driver";
			$data['version'] = 'driver';
		}
		if (IGtPush::TYPE_CMD == $data['type']) {
			$type = IGtPush::TYPE_CMD;
		}
		if (isset($data['user_id'])) {
			$user_id = $data['user_id'];
		} else {
		    $user_id = Yii::app()->user->user_id;
		}
		$created = date('Y-m-d H:i:s' , time());
		
		$current_time = time();
		$white_list = $this->DriverWhiteList();
		$sql_str = '';
		
		foreach ($clients as $val) {
			$recommand = 0;
			
			//如果是呼叫转移指令  则判定是否为皇冠  皇冠部推送指令
			if (IGtPush::TYPE_CMD == $type) {
				$driver = DriverStatus::model()->get($val['driver_id']);
				$driver_recommand = $driver->recommand;
				if (!empty($driver_recommand)) {
					$begin_time = isset($driver_recommand['begin_time']) ? strtotime($driver_recommand['begin_time']) : 0;
					$end_time = isset($driver_recommand['end_time']) ? strtotime($driver_recommand['end_time']) : 0;
					if ($current_time > $begin_time && $current_time < $end_time) {
						$recommand = 1;
					}
				}
				
				if (in_array($val['driver_id'] , $white_list)) {
					$recommand = 1;
				}
				
			}
			
			if ($recommand == 0) {
				$sql_str .= "('".$val['client_id']."' , '".$val['driver_id']."' , '".$type."' , '".$data['content']."' , '".$data['city_id']."' ,'".$data['level']."' , '".$data['version']."' , '".$data['pre_send_time']."' , ".$user_id." , '".$created."'),";
			}
		}
		
		if (empty($sql_str)) {
			return true;
		}
		
		$sql .= substr($sql_str , 0 , strlen($sql_str)-1);
		$result = Yii::app()->dbreport->createCommand($sql)->execute();
		return $result;
	}
	
	/**
	 * 消息列表
	 * @param array $clients
	 * @param array $data
	 * @return boolean
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-05-07
	 */
	public function getMessage($condition = array()) {
		$criteria=new CDbCriteria;
		if ($condition['status'] !== '') {
			$criteria->compare('status',$condition['status']);
		}
		$criteria->order = 'id desc';
		return new CActiveDataProvider($this, array(
		    'criteria'=>$criteria,
			'pagination'=>array (
				'pageSize'=>50)
		));
	}
	
	/**
	 * 推送黑名单-单个司机推送
	 * @param string $driver_id
	 * @param string $phone
	 * @param int $mark
	 * @return boolean $result
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-05-13
	 */
	public function push_single($driver_id = '' , $phone = '' , $mark = 1) {
		if ($driver_id == '' || $phone == '') {
			return false;
		}
		if (!is_array($phone)) {
			$phone = array($phone);
		}
		$params = array(
		    'type' => IGtPush::TYPE_BLACK_CUSTOMER ,
		    'content' => '黑名单',
		    'driver_id' => $driver_id,
		    'level' => IGtPush::LEVEL_LOW ,
		    'phone' => $phone,
		    'mark' => $mark,
		    'created' => date("Y-m-d H:i:s" , time()),
		);
		$result = self::model()->organize_message_push($params);
		return $result;
	}
	
	/**
	 * 推送黑名单-司机群推
	 * @param array $drivers
	 * @param string $phone
	 * @param int $city_id
	 * @return boolean
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-05-13
	 */
	public function push_list($drivers , $phone , $city_id = 1 , $mark = 1) {
		if (empty($phone)) {
			return false;
		}
		if (!empty($drivers)) {  //司机工号不为空
			foreach ($drivers as $val) {
				self::model()->push_single($val , $phone);
			}
		} else {                 //通过city_id获取工号
			$driver_mark = array(
			    Driver::MARK_ENABLE,
			    Driver::MARK_DISNABLE,
			    Driver::MARK_CHANGE
			);
			$drivers = Driver::model()->getDriverList($city_id , $driver_mark);  //需要有改动
			foreach ($drivers as $val) {
				self::model()->push_single($val['driver_id'] , $phone , $mark);
			}
		}
		return true;
	}
	
	/**
	 * 推送开关消息
	 * @param array $data
	 * @param int $city_id
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-05-14
	 */
	public function push_update_config($data , $city_id = 1) {
		$params = array(
		     'type' => IGtPush::TYPE_UPDATE_CONFIG ,
		     'content' => '配置开关' ,
		     'config' => $data,
		     'level' => IGtPush::LEVEL_LOW,
		     'created' => date("Y-m-d H:i:s" , time()),
		);
		
		$driver_mark = array(
		    Driver::MARK_ENABLE,
		    Driver::MARK_DISNABLE,
		    Driver::MARK_CHANGE
		);
		$drivers = Driver::model()->getDriverList($city_id , $driver_mark);  //需要有改动
		foreach ($drivers as $val) {
			$params['driver_id'] = $val['driver_id'];
			self::model()->organize_message_push($params);
		}
		return true;
	}
	
	/**
	 * 发送黑名单消息
	 * @param string $driver_id
	 * @param string $msg
	 * @return boolean
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-05-13
	 */
	public function push_black_msg($driver_id = '' , $msg = '') {
		if ($driver_id == '' || $msg == '') {
			return false;
		}
		$params = array(
		    'type' => 'msg_driver',
		    'driver_id' => $driver_id,
		    'level' => IGtPush::LEVEL_LOW,
		    'content' => $msg,
		    'created' => date("Y-m-d H:i:s" , time()),
		);
		$result = self::model()->organize_message_push($params);
		return $result;
	}
	
	/**
	 * 发送报单消息
	 * @param string $driver_id
	 * @param boolean $status
	 * @return boolean
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-05-26
	 */
	public function push_order_submit($driver_id , $order_id , $status = TRUE ) {
		if (empty($driver_id) || empty($order_id)) {
			return false;
		}
		if ($status) {
			$msg = '报单成功';
			$flag = 'finished';
		} else {
			$msg = '报单失败';
			$flag = 'failed';
		}
		$params = array(
		    'type' => IGtPush::TYPE_ORDER_SUBMIT,
		    'driver_id' => $driver_id,
		    'order_id' => $order_id,
		    'level' => IGtPush::LEVEL_HIGN,
		    'content' => $msg,
		    'status' => $flag,
		    'created' => date("Y-m-d H:i:s" , time()),
		);
		$result = self::model()->organize_message_push($params);
		return $result;
	}
	
	/**
	 * 组织短信内容推送
	 * @param array $params
	 * @return boolean
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-05-13
	 */
	public function organize_message_push($params) {
		//判定类型
		$message=self::getMessageByType($params);
		
		if (isset($params) && isset($params['_message_type_'])) {
			EPush::set_message_type($message, $params['_message_type_']);
		}
		
		//client_id或者content为空
		if (empty($params['client_id'])) {
			return false;
		}
		//获取ClientID END
		$push_msg_id = $this->_genMessageLog($params);
		if ($push_msg_id) {
			if (IGtPush::TYPE_NOTICE_DRIVER_AUDIO == $params['type'] || IGtPush::TYPE_NOTICE_DRIVER_UPY == $params['type']) {
				$message['push_msg_id'] = $push_msg_id;
			} else {
				$message['content']['push_msg_id'] = $push_msg_id;
			}
			
			if(isset($params['category'])){
				$message['content']['category'] = $params['category'];
			}

			//其他包装推送参数
			$data=array(
					'client_id'=>$params['client_id'],
					'level'=>$params['level']
			);
			if (isset($params['offline_time'])) {
				$data['offline_time']=$params['offline_time'];
			}
			
			if (IGtPush::TYPE_NOTICE_DRIVER_AUDIO != $params['type'] && IGtPush::TYPE_NOTICE_DRIVER_UPY != $params['type']) {
			    $message['content'] = json_encode($message['content']);
			}
			$data['message']=json_encode($message);
			if (isset($params['queue_id'])) {
				$data['queue_id']=$params['queue_id'];
			} else {
				$data['queue_id']=0;
			}
			$data['driver_id']=$params['driver_id'];
			//包装推送参数 END
			$result=IGtPush::model($params['version'])->PushToSingle($data, $params['version']);
			return $result;
		} else {
			return false;
		}
	}
	
	public function organize_message_push_test($params) {
		//判定类型
		$message=self::getMessageByType($params);
		if (empty($params['client_id'])) {
			return false;
		}
		print_r('test_1');
		//获取ClientID END
		$push_msg_id = $this->_genMessageLog($params);
		if ($push_msg_id) {
			print_r('test_2');
			if (IGtPush::TYPE_NOTICE_DRIVER_AUDIO == $params['type']) {
				$message['push_msg_id'] = $push_msg_id;
			} else {
				$message['content']['push_msg_id'] = $push_msg_id;
			}
			print_r('test_3');
			//其他包装推送参数
			$data=array(
					'client_id'=>$params['client_id'],
					'level'=>$params['level']
			);
			if (isset($params['offline_time'])) {
				$data['offline_time']=$params['offline_time'];
			}
			print_r('test_4');
			if (IGtPush::TYPE_NOTICE_DRIVER_AUDIO != $params['type']) {
			    $message['content'] = json_encode($message['content']);
			}
			$data['message']=json_encode($message);
			if (isset($params['queue_id'])) {
				$data['queue_id']=$params['queue_id'];
			} else {
				$data['queue_id']=0;
			}
			$data['driver_id']=$params['driver_id'];
			//包装推送参数 END
			print_r('test_5');
			$result=IGtPush::model($params['version'])->PushToSingle($data, $params['version']);
			return $result;
		} else {
			return false;
		}
	}
	
	/**
	 * 分表去记录message_log
	 * @param array $params
	 * @return int 
	 */
	private function _genMessageLog($params) {
		$tab = 't_message_log_'.date('Ym');
		$attr = array(
			'client_id'=>$params['client_id'],
			'type'=>$params['type'],
			'content'=>json_encode($params['content']),
			'level'=>$params['level'],
			'driver_id'=>$params['driver_id'],
			'queue_id'=>isset($params['queue_id']) ? $params['queue_id'] : 0,
			'version'=>$params['version'],
            'created'=>isset($params['created']) ? $params['created'] : date('Y-m-d'),
		);
		$result = Yii::app()->dbreport->createCommand()->insert($tab , $attr);
		if ($result) {
			$push_msg_id = Yii::app()->dbreport->getLastInsertID(); 
			return $push_msg_id;
		} else {
			return 0;
		}
	}
	
	/**
	 * 通过类型获取推送消息体 
	 * @param array $params
	 * @return array $message
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-04-28
	 */
	private function getMessageByType(&$params) {
		switch ($params['type']) {
			case IGtPush::TYPE_ORDER : //订单 司机端
				$message=array(
						'type'=>IGtPush::TYPE_ORDER,
						'content'=>array(
								'message'=>$params['content'],
								'queue_id'=>$params['queue_id'],
						),
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case IGtPush::TYPE_ORDER_DETAIL : //订单详情 司机端
				$message=array(
						'type'=>IGtPush::TYPE_ORDER_DETAIL,
						'content'=>array(
								'message'=>$params['content'],
								'order_id'=>$params['order_id'],
						),
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case IGtPush::TYPE_STATUS : //订单状态  司机端
				$message=array(
						'type'=>IGtPush::TYPE_STATUS,
						'content'=>array(
								'message'=>$params['content'],
								'status'=>$params['status']
						),
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case IGtPush::TYPE_MSG_DRIVER : //消息  司机端
				$message=array(
						'type'=>'msg',
						'content'=>array(
								'message'=>$params['content'].'[e代驾]',
								'feedback' => 0,
						),
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case IGtPush::TYPE_MSG_LEADER : //消息  司机端
				$message=array(
						'type'=>'msg',
						'content'=>array(
								'message'=>$params['content'],
								'feedback' => 1,
						),
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case IGtPush::TYPE_MSG_CUSTOMER : //消息  客户端
				$message=array(
						'type'=>'msg',
						'content'=>array(
								'message'=>$params['content'],
								'feedback' => 0,
						),
						'timestamp'=>time()
				);
				$params['version']='customer';
				$client=GetuiClient::model()->getCustomerInfo($params['udid']);
				break;
			case IGtPush::TYPE_NOTICE_DRIVER : //公告 司机端
				$message=array(
						'type'=>'notice',
						'content'=>array(
								'message'=>$params['content']
						),
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case IGtPush::TYPE_NOTICE_CUSTOMER : //公告 客户端
				$message=array(
						'type'=>'notice',
						'content'=>array(
								'message'=>$params['content']
						),
						'timestamp'=>time()
				);
				$params['version']='customer';
				$client=GetuiClient::model()->getCustomerInfo($params['udid']);
				break;
			case IGtPush::TYPE_BLACK_CUSTOMER : //公告 客户端
				$message=array(
						'type'=>IGtPush::TYPE_BLACK_CUSTOMER ,
						'content'=>array(
								'message'=>$params['content'],
								'phone'=>$params['phone'],
								'mark'=>$params['mark'],
						),
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case IGtPush::TYPE_UPDATE_CONFIG : //配置 开关 司机端
				$message=array(
						'type'=>IGtPush::TYPE_UPDATE_CONFIG ,
						'content'=>array(
								'message'=>'开关配置',
						),
						'timestamp'=>time()
				);
				foreach ($params['config'] as $key=>$val) {
					$message['content'][$key] = $val;
				}
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case IGtPush::TYPE_CMD:
				$message=array(
						'type'=>IGtPush::TYPE_CMD ,
						'content'=>array(
								'message'=>$params['message'],
						),
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case IGtPush::TYPE_ORDER_SUBMIT:
			    $message=array(
						'type'=>IGtPush::TYPE_ORDER_SUBMIT ,
						'content'=>$params['content'],
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case IGtPush::TYPE_NOTICE_DRIVER_AUDIO : //公告 司机端
				$message=array(
						'type'=>IGtPush::TYPE_NOTICE_DRIVER_AUDIO,
						'content' => isset($params['content']) ? $params['content'] : '',
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case IGtPush::TYPE_NOTICE_DRIVER_UPY : //公告 司机端
				$message=array(
						'type'=>IGtPush::TYPE_NOTICE_DRIVER_UPY,
						'content' => isset($params['content']) ? $params['content'] : '',
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			default :
				break;
		}
		$params['client_id'] = $client['client_id'];
		return $message;
	}
	
	/**
	 * 获取热点地区
	 * @return object
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-06-15
	 */
	public function getHotAreaAll() {
		$result = Yii::app()->db_readonly->createCommand()
		             ->select('id , address')
		             ->from('t_hot_area')
		             ->queryAll();
		return $result;
	}
	
	/**
	 * 通过id获取热点地区
	 * @return object
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-06-15
	 */
	public function getHotAreaByID($id) {
		if (empty($id)) {
			return '';
		}
		$result = Yii::app()->db_readonly->createCommand()
		             ->select('lng , lat , area')
		             ->from('t_hot_area')
		             ->where('id = :id' , array(':id' => $id))
		             ->queryRow();
	    return $result;
	}
	
	/**
	 * 通过city_id获取热点地区
     * @param <int> $cityId 城市Id
	 * @return <array>
	 * @author liuxiaobo
	 * @since 2014-02-11
	 */
	public function getHotAreaByCityID($cityId) {
		if (empty($cityId)) {
			return $this->getHotAreaAll();
		}
		$result = Yii::app()->db_readonly->createCommand()
		             ->select('id , address')
		             ->from('t_hot_area')
		             ->where('city_id = :city_id' , array(':city_id' => $cityId))
		             ->queryAll();
	    return $result;
	}
	
	/**
	 * 再次推送订单详情
	 * @param array $params
	 * @return boolean
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-05-13
	 */
	public function organize_message_push_again($params) {
		//判定类型
		$message=self::getMessageByType($params);
		//获取ClientID END
		$model=new MessageLog();
		$msg = MessageLog::model()->find('queue_id = :queue_id and driver_id = :driver_id and type = :type' , array(
		    ':queue_id' => $params['queue_id'],
		    ':driver_id' => $params['driver_id'],
		    ':type' => $params['type'],
		));
		if (!$msg) {
			return false;
		}
		$order_id = $msg->push_msg_id;
		$message['content']['push_msg_id']=$order_id;
		//其他包装推送参数
		$data=array(
				'client_id'=>$params['client_id'],
				'level'=>$params['level']
		);
		if (isset($params['offline_time'])) {
			$data['offline_time']=$params['offline_time'];
		}
		$message['content'] = json_encode($message['content']);
		$data['message']=json_encode($message);
		if (isset($params['queue_id'])) {
			$data['queue_id']=$params['queue_id'];
		} else {
			$data['queue_id']=0;
		}
		$data['driver_id']=$params['driver_id'];
		//包装推送参数 END
		$result=IGtPush::model($params['version'])->PushToSingle($data, $params['version']);
		return $result;
	}
	
	/**
     * 发送指令
     * @param array $params
     * @return boolean $result;
     */
    public function PushCmd($params) {
    	//判定类型
		$message=self::getMessageByType($params);
		
		//add by yangzhi 2015-02-10
		EPush::set_message_type($message, "push_cmd");
		
		$message['push_msg_id']=1;
		//其他包装推送参数
		$data=array(
				'client_id'=>$params['client_id'],
				'level'=>$params['level']
		);
		if (isset($params['offline_time'])) {
			$data['offline_time']=$params['offline_time'];
		}
		$data['message']=json_encode($message);
		if (isset($params['queue_id'])) {
			$data['queue_id']=$params['queue_id'];
		} else {
			$data['queue_id']=0;
		}
		$data['driver_id']=$params['driver_id'];
		//包装推送参数 END
		$result=IGtPush::model($params['version'])->PushToSingle($data, $params['version']);
		return $result;
    }
   
    /**
     * 添加直接推送消息
     * @param array $params
     * @return boolean
     */
    public function addPushMessage($params) {
    	$driver_id = isset($params['driver_id']) ? strtoupper($params['driver_id']) : '';
    	$content = isset($params['content']) ? $params['content'] : '';
    	if (empty($driver_id) || empty($content)) {
    		return false;
    	}
    	$time = time();
    	$data = array(
                'type' => IGtPush::TYPE_MSG_DRIVER , 
                'content' => $content,
                'level' => IGtPush::LEVEL_LOW, //级别
                'driver_id' => $driver_id,
                'created' => date('Y-m-d H:i:s', $time),
            );
            
       $result = $this->organize_message_push($data);
       $push_id = 0;
       if ($result) {
       	   //记录message
       	   $push_id = $this->_insertPushMessage($data);
       }
       return $push_id;
    }
    
    /**
     * 设置白名单（不推送呼叫转移指令）
     * @return array $list
     */
    public function DriverWhiteList() {
    	$list = array(
    	    'BJ3517',
    	    'BJ0217',
    	);
    	return $list;
    }
    
    /**
     * 插入已发送message
     * @param array $params
     * @return unknown
     */
    private function _insertPushMessage($params) {
    	$driver_id = $params['driver_id'];
    	$content = $params['content'];
    	
    	$client=GetuiLog::model()->getDriverInfoByDriverID($driver_id);
    	if (empty($client)) {
    		return 0;
    	}
    	$client_id = $client['client_id'];
    	$user_id = Yii::app()->user->user_id;
    	$driver = DriverStatus::model()->get($driver_id);
    	$city_id = !empty($driver->city_id) ? $driver->city_id : 1;
    	
    	$attributes = array(
    	    'client_id' => $client_id,
    	    'driver_id' => $driver_id,
    	    'type' => $params['type'],
    	    'content' => $content,
    	    'level' => $params['level'],
    	    'version' => 'driver',
    	    'pre_send_time' => date('Y-m-d H:i:s'),
    	    'created' => date('Y-m-d H:i:s'),
    	    'status' => 1,
    	    'user_id' => $user_id,
    	    'city_id' => $city_id,
    	);
    	
    	$model = new PushMessage();
    	$model->attributes = $attributes;
    	$model->client_id = $client_id;
    	$model->driver_id = $driver_id;
    	if ($model->save()) {
    		return $model->id;
    	} else {
    		return 0;
    	}
    }
    
    /**
     * 推送语音公告
     * @param array $params
     * @return array $result
     * @version 2013-08-30
     */
    public function pushNoticeAudio($params) {
    	$result = array('code' => 1);
    	//接收并验证参数
    	$notice_id = isset($params['notice_id']) ? intval($params['notice_id']) : 0;
    	$content = isset($params['content']) ? trim($params['content']) : '';
    	$url = isset($params['url']) ? trim($params['url']) : '';
    	$city_id = isset($params['city_id']) ? $params['city_id'] : 0;
    	$test_drivers = isset($params['drivers']) ? $params['drivers'] : '';
    	if (0 == $notice_id || 0 == $city_id ) {
    		$result['msg'] = '参数验证失败';
    		return $result;
    	}
    	
    	$drivers = $this->_getPushDrivers($city_id , $test_drivers);
    	if (empty($drivers)) {
    		$result['msg'] = '获取司机失败';
    		return $result;
    	}

    	$type = isset($params['type']) ? $params['type'] : IGtPush::TYPE_NOTICE_DRIVER_AUDIO;
    	$category = isset($params['category']) ? trim($params['category']) : '';
    	$title = isset($params['title']) ? trim($params['title']) : '';
    	$publish_time = isset($params['created']) ? $params['created'] : date('m-d H:i');
    	$audio_time_long = isset($params['audio_time']) ? trim($params['audio_time']) : 0;
    	
    	//推送
    	$content_arr = array(
    	    'notice_id' => $notice_id , 
    	    'url' => $url , 
    	    'content' => $content , 
    	    'type' => $type,
    	    'category' => $category,
    	    'title' => $title,
    	    'publish_time' => $publish_time,
    	    'audio_time_long' => $audio_time_long,
    	);
    	$this->_pushNoticeAudioToDriver($drivers , $content_arr);
    	$result = array('code' => 0 , 'msg' => '推送成功');
    	return $result;
    }
    
    /**
     * 获取要推送的司机
     * @param int $city_id
     * @param array $test_drivers
     * @return array
     */
    private function _getPushDrivers($city_id , $test_drivers) {
    	$drivers = array();
    	if (empty($test_drivers)) {
    		$drivers = Driver::model()->getDrivers($city_id , 0);
    	} else {
    		foreach ($test_drivers as $val) {
    			$drivers[] = array('user'=>$val , 'driver_id' => $val);
    		}
    	}
    	return $drivers;
    }
    
    /**
     * 推送公告音频给司机
     * @param array $drivers
     * @param array $content_arr
     * @return boolean 
     */ 
    private function _pushNoticeAudioToDriver($drivers , $content_arr) {
    	$arr = array( 
    	    'content' => $content_arr['content'],
    	    'audio_url' => $content_arr['url'],
    	    'id' => $content_arr['notice_id'],
    	    'category' => $content_arr['category'],
    	    'title' => $content_arr['title'],
    	    'publish_time' => $content_arr['publish_time'],
    	    'audio_time_long' => $content_arr['audio_time_long'],
    	);
    	foreach ($drivers as $driver) {
    		$params = array(
                'type' => $content_arr['type'],
                'content' => $arr,
                'level' => IGtPush::LEVEL_LOW, //级别
                'driver_id' => $driver['user'],
                'created' => date('Y-m-d H:i:s'),
            );
            $this->organize_message_push($params);
    	}
    	return true;
    }
    
    /**
     * 推送消息(不记录log)
     * @param string $content
     * @param string $driver_id
     * @param int $level  --- 推送级别 1(低级别),2（中级）,3(高级)
     * @param int $offline_time --- 离线时间（默认3600秒）
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-13 13:48
     */
    public function PushDriverMsg($msg , $driver_id , $level = 1 , $offline_time = 3600) {
    	if (!empty($message) || empty($driver_id)) {
    		return false;
    	}
    	//获取消息体
    	$params = array(
    	    'content' => array(
    	        'message' => $msg,
    	    ),
    	);
    	$message = PushMsgFactory::model()->orgPushMsg($params , PushMsgFactory::TYPE_MSG);
    	
    	//获取client_id
    	$driver = DriverStatus::model()->get($driver_id);
    	if (!$driver) {
    		return false;
    	}
    	$client_id = $driver->client_id;
    	
    	//推送
    	$result = EPush::model('driver')->send($client_id , $message , $level , $offline_time);
    	if ($result['result'] == 'ok') {
    	    return true;	
    	} else {
    		return false;
    	}
    }
}
