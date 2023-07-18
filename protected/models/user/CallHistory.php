<?php

/**
 * This is the model class for table "{{call_history}}".
 *
 * The followings are the available columns in table '{{call_history}}':
 * @property integer $id
 * @property string $imei
 * @property integer $type
 * @property string $phone
 * @property integer $duration
 * @property integer $gap
 * @property string $insert_time
 * @property integer $mnc
 * @property integer $mcc
 * @property integer $lac
 * @property integer $ci
 * @property string $simcard
 */
class CallHistory extends CActiveRecord {
	public $call_time;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CallHistory the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{call_history}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'imei, phone',
				'required'
			), 
			array (
				'type, duration, gap, insert_time, mnc, mcc, lac, ci', 
				'numerical', 
				'integerOnly'=>true
			), 
			array (
				'sig,simcard', 
				'length', 
				'max'=>32
			), 
			array (
				'imei', 
				'length', 
				'max'=>100
			), 
			array (
				'phone', 
				'length', 
				'max'=>21
			), 
			array (
				'driver_id', 
				'length', 
				'max'=>10
			), 
			array (
				'insert_time', 
				'safe'
			), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, driver_id,imei, type, phone, duration, gap, insert_time, mnc, mcc, lac, ci, sig,simcard',
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
		return array (
			'driver'=>array (
				self::BELONGS_TO, 
				'Employee', 
				'imei'
			),
		);
	}

	public function getDuplicateCallHistory($sig, $phone = '13800138000'){
		$connect = Yii::app()->db_readonly;
		$callHistory = $connect->createCommand()
						->select('imei, type, phone')
						->from('t_call_history')
						->where('sig=:sig and phone=:phone', array(':sig'=>$sig, ':phone'=>$phone))
						->queryRow();
		if ($callHistory){
			return $callHistory;
		} else {
			return null;
		}
	}
	/**
	 * 查询2小时内的呼入记录 有返回true 无返回false
	 * Enter description here ...
	 * @param unknown_type $sig
	 * @param unknown_type $phone
	 * @param unknown_type $call_time
	 */
	public function getIncomeCallHistory($phone,$call_time){
		$status = FALSE;
		$end_time = date('Y-m-d H:i:s',$call_time + 10);
		$start_time = date('Y-m-d H:i:s',$call_time - 7200);
		$connect = Yii::app()->db_readonly->createCommand()
						->select('imei, type, phone')
						->from('t_call_history')
						->where('phone=:phone and type = 0 and insert_time between :start_time and :end_time', 
								array(':phone'=>$phone, ':start_time' =>$start_time, ':end_time'=>$end_time))
						->query()
						->count();
		if($connect > 0)
			$status =  TRUE;
		return $status;
	}
	
	
	public function insertCallHistory($params = array()){
		//gap 时间戳和上传时间的时间差
		//duration 通话时长
		
		if (!empty($params)){
			$sigArray = array(
						'imei'=>$params['imei'],
						'simcard'=>$params['simcard'],
						'phone'=>$params['phone'],
						'type'=>$params['type'],
						'callTime'=>$params['callTime'],
						'endTime'=>$params['endTime'],
						'talkTime'=>$params['talkTime'],
						);
			$sig = Api::createSig($sigArray);
			
			$gap = time() - $params['endTime'];
				
			if ($gap < 0) $gap = 0;
			
			$attributes = array(
					'imei'=>$params['imei'], 
					'driver_id'=>$params['driver_id'], 
					'type'=>$params['type'], 
					'phone'=>$params['phone'], 
					'duration'=>$params['talkTime'], 
					'gap'=>$gap
					);
			$model = new CallHistory();
			unset($model->attributes);
			$model->attributes = $attributes;
			$model->sig = $sig;
			$model->insert_time = date(Yii::app()->params['formatDateTime'], time());
			$model->simcard = isset($params['simcard']) ? $params['simcard'] : '';
			$model->insert();
		}
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'id'=>'ID', 
			'driver_id'=>'司机工号', 
			'imei'=>'IMEI号', 
			'type'=>'呼叫类型', 
			'phone'=>'司机电话', 
			'cPhone'=>'客户电话', 
			'duration'=>'Duration', 
			'gap'=>'Gap', 
			'insert_time'=>'Insert Time', 
			'mnc'=>'Mnc', 
			'mcc'=>'Mcc', 
			'lac'=>'Lac', 
			'ci'=>'Ci', 
			'user'=>'司机工号', 
			's_time'=>'开始时间', 
			'e_time'=>'结束时间',
			'simcard' => 'Simcard',
		);
	}
	
	public function afterFind(){
		$this->call_time = strtotime($this->insert_time) - $this->gap;
        $this->phone     =  Common::parseCustomerPhone($this->phone);
	}
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		

		$criteria = new CDbCriteria();
		
		$criteria->compare('id', $this->id);
		$criteria->compare('driver_id', $this->driver_id);
		$criteria->compare('imei', $this->imei);
		$criteria->compare('type', $this->type);
		$criteria->compare('phone', $this->phone);
		$criteria->compare('duration', $this->duration);
		$criteria->compare('gap', $this->gap);
		$criteria->compare('insert_time', $this->insert_time);
		$criteria->compare('sig', $this->sig);
		$criteria->compare('simcard', $this->simcard);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria
		));
	}
}