<?php

/**
 * 读从库进行订单搜索,降低主库压力
 * OrderQueue.php调用地方过多,为避免切换读写造成问题,创建SearchOrderQueue

 * This is the model class for table "{{order_queue}}".
 *
 * The followings are the available columns in table '{{order_queue}}':
 * @property integer $id
 * @property integer $city_id
 * @property string $callid
 * @property string $name
 * @property string $phone
 * @property string $contact_phone
 * @property integer $number
 * @property string $address
 * @property string $booking_time
 * @property string $comments
 * @property string $agent_id
 * @property string $dispatch_agent
 * @property string $dispatch_time
 * @property integer $flag
 * @property string $update_time
 * @property string $created
 */
class SearchOrderQueue extends OrderActiveRecord {
	//等待派单
	const QUEUE_WAIT = 0;
	//已经分派，等待调度接单
	const QUEUE_WAIT_COMFIRM = 1;
	//派单失败，等待客服处理
	const QUEUE_READY = 2;
	//订单取消
	const QUEUE_CANCEL = 3;
	//订单派单成功
	const QUEUE_SUCCESS = 4;
	
	//一键预约添加类型  BY AndyCong
	const QUEUE_TYPE_ACCEPTED = 'accepted';
	const QUEUE_TYPE_FAILURED = 'failured';
	const QUEUE_TYPE_ORDERING = 'ordering';
	const QUEUE_TYPE_CANCELED = 'canceled';
	const QUEUE_TYPE_ORDERED = 'ordered';
	const QUEUE_TYPE_FINISHED = 'finished';
	//一键预约添加类型  BY AndyCong END
	
	//下单类型 BY AndyCong
	const QUEUE_AGENT_CLIENT = '直呼APP';
	const QUEUE_AGENT_CALLCENTER = '400接单';
	const QUEUE_AGENT_KEYBOOKING = '一键预约';
	const QUEUE_AGENT_DRIVERBOOKING = '司机代下单';
	const QUEUE_AGENT_WEIXIN = '微信';
	//下单类型 BY AndyCong END
	
	public $begin_booking_time;
	public $end_booking_time;

    public $booking_time_day;
    public $booking_time_time;
	
	public function init() {
		$this->begin_booking_time = date('Y-m-d 07:00:00', time()-7*60*60);
		$this->end_booking_time = date('Y-m-d H:i:s', time()+2*86400);
		
		parent::init();
	}
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrderQueue the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
        public function getDbConnection() {
            return Yii::app()->dborder_readonly;
        }

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{order_queue}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'city_id, callid, name, phone, number, address, booking_time, agent_id', 
				'required'), 
			array (
				'city_id, number,dispatch_number, flag , type,is_vip', 
				'numerical', 
				'integerOnly'=>true), 
			array (
				'callid', 
				'length', 
				'max'=>40), 
			array (
				'name, phone, contact_phone, dispatch_agent', 
				'length', 
				'max'=>20), 
			array (
				'address', 
				'length', 
				'max'=>100), 
			array (
				'comments', 
				'length', 
				'max'=>500), 
			array (
				'agent_id', 
				'length', 
				'max'=>10), 
			array (
				'dispatch_time, update_time,created,lng,lat,google_lng,google_lat,channel', 
				'safe'), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, city_id, callid, name, phone, contact_phone, number, dispatch_number , address, booking_time, comments, agent_id, dispatch_agent, dispatch_time, flag, update_time, created , type,is_vip', 
				'safe', 
				'on'=>'search'));
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array ();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'id'=>'ID', 
			'city_id'=>'城市', 
			'callid'=>'Callid', 
			'name'=>'客户姓名', 
			'phone'=>'客户电话', 
			'contact_phone'=>'联系人电话', 
			'number'=>'人数 ',
			'dispatch_number'=>'己派人数 ',
			'address'=>'地址', 
			'booking_time'=>'预约时间', 
			'comments'=>'备注', 
			'agent_id'=>'接单调度', 
			'dispatch_agent'=>'派单调度', 
			'dispatch_time'=>'派单时间', 
			'flag'=>'派单状态', 
			'update_time'=>'Update Time', 
			'created'=>'接单时间',
			'type'=>'类型');
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		$criteria = new CDbCriteria();
		
		$criteria->compare('id', $this->id);
		$criteria->compare('city_id', $this->city_id);
		$criteria->compare('callid', $this->callid );
		$criteria->compare('name', $this->name );
		$criteria->compare('phone', $this->phone );
		
		if( !empty($this->is_vip) ){
			$criteria->compare('is_vip', $this->is_vip );
		}
		
		//添加联系人查询 或的关系 BY AndyCong 2013-05-07
		if ($this->phone) {
			$criteria->addCondition("contact_phone='".$this->phone."'" , 'OR');
		}
		
		$criteria->compare('contact_phone',$this->contact_phone);
		$criteria->compare('number', $this->number);
		//$criteria->compare('dispatch_number', $this->dispatch_number);
		$criteria->compare('address', $this->address,true);
		$criteria->compare('comments', $this->comments);
		$criteria->compare('agent_id', $this->agent_id);
		$criteria->compare('dispatch_agent', $this->dispatch_agent);
		$criteria->compare('dispatch_time', $this->dispatch_time);

        $criteria->compare('flag', $this->flag);
        $user_id = isset(Yii::app()->user->user_id) ? Yii::app()->user->user_id : 1;
        if ($user_id != 130) {
			$criteria->addCondition("channel not in('01001' , '01002' , '01003' , '01004' , '01005' , '01006', '01007')" , 'AND');
		}

		//$criteria->compare('update_time', $this->update_time, true);
		$criteria->compare('booking_time', '>='.$this->begin_booking_time);
		

		//TODO:队列状态不等于等待派单或者查询全部状态时，只显示当天的队列
		if ($this->begin_booking_time&&$this->end_booking_time) {
			//$criteria->addBetweenCondition('booking_time', $this->begin_booking_time, $this->end_booking_time);
		}
		
		if ($this->flag==self::QUEUE_SUCCESS) {
			$criteria->order = 'dispatch_time desc';
		} else {
			$criteria->order = 'flag,booking_time,created,number desc';
		}
		
		//print_r($criteria);
		$rowData = self::model()->findAll($criteria);
		$data = new CArrayDataProvider($rowData, array(
			'id'	=> 'id',
			'pagination'=>array(
				'pageSize'=>10,
			),
		));
	
		return $data;
	}
}
