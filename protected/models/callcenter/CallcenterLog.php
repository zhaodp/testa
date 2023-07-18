<?php

/**
 * This is the model class for table "{{callcenter_log}}".
 *
 * The followings are the available columns in table '{{callcenter_log}}':
 * @property integer $id
 * @property integer $user_id
 * @property string $CallNo
 * @property string $CallsheetId
 * @property string $CalledNo
 * @property string $CallID
 * @property string $CallType
 * @property string $RecordFile
 * @property string $Ring
 * @property string $Begin
 * @property string $End
 * @property string $QueueTime
 * @property string $RingTime
 * @property string $Queue
 * @property string $Agent
 * @property string $Exten
 * @property string $AgentName
 * @property string $ActionID
 * @property string $CallState
 * @property string $State
 * @property string $FileServer
 * @property string $MonitorFilename
 * @property string $RealState
 * @property string $created
 * @property integer $IVRKEY
 */
class CallcenterLog extends CActiveRecord {

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CallcenterLog the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		$table_name = 'callcenter_log_'.date('Ym', time());
		return '{{'.$table_name.'}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
				array(
						'user_id, CallNo, CallsheetId, CalledNo, CallID, CallType',
						'required'
				),
				array(
						'user_id',
						'numerical',
						'integerOnly'=>true
				),
				array(
						'CallNo, CalledNo, CallType',
						'length',
						'max'=>15
				),
				array(
						'CallsheetId, CallID',
						'length',
						'max'=>40
				),
				array(
						'RecordFile, MonitorFilename',
						'length',
						'max'=>255
				),
				array(
						'Queue, ActionID, CallState, State, FileServer, RealState,Province,District',
						'length',
						'max'=>50
				),
				array(
						'Agent, Exten, AgentName',
						'length',
						'max'=>10
				),
				array(
						'Ring, Begin, End, QueueTime, RingTime, IVRKEY',
						'safe'
				),
				// The following rule is used by search().
				// Please remove those attributes that should not be searched.
				array(
						'id, user_id, CallNo, CallsheetId, CalledNo, CallID, CallType, RecordFile, Ring, Begin, End, QueueTime, RingTime, Queue, Agent, Exten, AgentName, ActionID, CallState, State, FileServer, MonitorFilename, RealState, Province,District,created',
						'safe',
						'on'=>'search'
				)
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array();
	}

	public function beforeSave() {
		$this->created=date(Yii::app()->params['formatDateTime'], time());
		return true;
	}

	public function process($params) {
		$phone=$params['phone'];
		//检查是否坐席绑定电话
		$ret=AdminAgent::model()->find('phone=:phone', array(
				':phone'=>$phone
		));
		if ($ret) {
			$tip =  "callcenter_mobile:号码为坐席呼叫号码.\n";
			Putil::report($tip);
			return;
		}
		CallConfig::model()->callHandle($phone);
	}

	/**
	 * 对司机来电未接的处理
	 */
	private function sms_driver($driver) {
		$message=$order_infomation='';
		echo 'callcenter_mobile:司机信息 '.$driver->user."\n";
		//检查当天是否有订单
		$begin_time=date('Y-m-d 07:00:00', time());
		//$end_time = date('Y-m-d 07:00:00', time()+86400);
		//$sql = 'select * from t_order where source =1 and status =0
		//		order by order_id desc limit 1;';


		$sql='select * from t_order
				where driver_id =:driver_id and source =1 and status =0
				and booking_time > :begin_time
				order by order_id desc limit 1;';
		$order=Order::model()->findBySql($sql, array(
				':driver_id'=>$driver->user,
				':begin_time'=>$begin_time
		));

		if ($order) {
			$format='最近派单信息：%s,%s预约,电话%s,%s；';
			$order_infomation=sprintf($format, $order->name, date('n月j日H:i', $order->booking_time), $order->phone, $order->location_start);
		} else {
			$order_infomation='';
		}

		$format='坐席繁忙来电未能接听。您位于%s，状态为%s，%s更新；%s其他问题请于工作日10:00后电话咨询';

		$status='下班';

		//司机位置验证  BY AndyCong 2013-08-09
		$driver_status=isset($driver->position->status) ? $driver->position->status : '';
		$driver_lng=isset($driver->position->baidu_lng) ? $driver->position->baidu_lng : '';
		$driver_lat=isset($driver->position->baidu_lat) ? $driver->position->baidu_lat : '';
		$driver_created=isset($driver->position->created) ? $driver->position->created : time();

		//司机位置验证  BY AndyCong 2013-08-09 END
		if (!empty($driver_status)) {
			switch ($driver->position->status) {
				case 0 :
				case 3 :
					$status='空闲';
					break;
				case 1 :
					$status='服务中';
					break;
				case 2 :
					$status='下班';
					break;
			}
		}
		$street=GPS::model()->getStreetByBaiduGPS($driver_lng, $driver_lat, 3); //3 所有GPS信息
		$update=date('n月j日H:i', strtotime($driver_created));

		$message=sprintf($format, $street['component']['street'], $status, $update, $order_infomation);

		if ($message!='') {
			Sms::SendSMS($driver->phone, $message.' ');
		}

		return $message;
	}

	/**
	 * 对客户来电未接的处理
	 */
	private function sms_customer($call_id, $phone) {
		echo 'callcenter_mobile:客户电话: '.$phone."\n";
		$message=$order_infomation='';
		//检查当天是否有预约订单
		$begin_time=date('Y-m-d 07:00:00', time()-7*60*60);

		$sql='select * from t_order_queue
				where phone=:phone and booking_time>=:booking_time
				order by id desc limit 1;';
		$orderqueue=OrderQueue::model()->findBySql($sql, array(
				':booking_time'=>$begin_time,
				':phone'=>$phone
		));

		if ($orderqueue) {
			$format='%s您好,欢迎致电e代驾，坐席繁忙电话未能接听。您的预约';
			switch ($orderqueue->flag) {
				case OrderQueue::QUEUE_WAIT :
					$message='正在等待派单，请耐心等候；';
					break;
				case OrderQueue::QUEUE_SUCCESS :
					$message='已经派单，请保持电话畅通，司机将很快联系您；';
					break;
				case OrderQueue::QUEUE_CANCEL :
					$message='已经取消，如需服务，';
			}
			$message=sprintf($format, $orderqueue->name).$message;
		} else {
			$message='欢迎致电e代驾，坐席繁忙电话未能接听，';
		}

		//生成在线下单地址
		//		$mobile = new CallcenterMobile();
		//		$mobile->short_url = Helper::shortUrl($call_id);
		//		$mobile->call_id = $call_id;
		//		$mobile->phone = $phone;
		//		if ($mobile->insert())
		//		{
		//			//保存成功后短信
		//			//$format = '欢迎您通过快速通道提交订单>>http://wap.edaijia.cn/o/%s';
		//			//$url = sprintf($format, $mobile->short_url);
		//			//modify by sunhongjing 2013-07-29
		//			$url = '欢迎您通过微信叫代驾，您可在微信“添加朋友”里搜edaijia进行关注，轻松体验代驾服务，10公里仅需￥39！';
		//		}


		$url='微信也可以叫代驾哦，添加e代驾微信公共账号，您可在微信“添加朋友”里搜edaijia进行关注，在微信上即可直接呼叫身边的代驾最近司机，够酷够快！';

		echo 'callcenter_mobile:'.$message.$url."\n";
		Sms::SendSMS($phone, $message.$url.' ');
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
				'id'=>'ID',
				'user_id'=>'User',
				'CallNo'=>'Call No',
				'CallsheetId'=>'Callsheet',
				'CalledNo'=>'Called No',
				'CallID'=>'Call',
				'CallType'=>'Call Type',
				'RecordFile'=>'Record File',
				'Ring'=>'Ring',
				'Begin'=>'Begin',
				'End'=>'End',
				'QueueTime'=>'Queue Time',
				'RingTime'=>'Ring Time',
				'Queue'=>'Queue',
				'Agent'=>'Agent',
				'Exten'=>'Exten',
				'AgentName'=>'Agent Name',
				'ActionID'=>'Action',
				'CallState'=>'Call State',
				'State'=>'State',
				'IVRKEY'=>'IVRKEY',
				'FileServer'=>'File Server',
				'MonitorFilename'=>'Monitor Filename',
				'RealState'=>'Real State',
				'created'=>'Created'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		$criteria=new CDbCriteria();

		$criteria->compare('id', $this->id);
		$criteria->compare('user_id', $this->user_id);
		$criteria->compare('CallNo', $this->CallNo);
		$criteria->compare('CallsheetId', $this->CallsheetId);
		$criteria->compare('CalledNo', $this->CalledNo);
		$criteria->compare('CallID', $this->CallID);
		$criteria->compare('CallType', $this->CallType);
		$criteria->compare('Agent', $this->Agent);
		$criteria->compare('Exten', $this->Exten);
		$criteria->compare('CallState', $this->CallState);
		$criteria->compare('State', $this->State);
		$criteria->compare('RealState', $this->RealState);

		return new CActiveDataProvider($this, array(
				'criteria'=>$criteria
		));
	}
}
