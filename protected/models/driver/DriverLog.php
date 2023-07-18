<?php

/**
 * This is the model class for table "{{driver_log}}".
 *
 * The followings are the available columns in table '{{driver_log}}':
 * @property integer $id
 * @property integer $type
 * @property string $imei
 * @property string $driver_id
 * @property string $last_record
 * @property string $description
 * @property string $operator
 * @property integer $created
 */
class DriverLog extends CActiveRecord {
	/**
	 * 更新
	 */
	const LOG_NORMAL = 1;
	/**
	 * 激活
	 */
	const LOG_MARK_ENABLE = 2;
	/**
	 * 投诉屏蔽
	 */
	const LOG_MARK_DISABLE_COMPLAINTS = 3;
	/**
	 * 费用不足屏蔽
	 */
	const LOG_MARK_DISABLE_FEE = 4;
	/**
	 * 差评屏蔽
	 */
	const LOG_MARK_DISABLE_POOR = 5;
	/**
	 * 延时报单屏蔽
	 */
	const LOG_MARK_DISABLE_THRICE_DELAY = 6;
	/**
	 * 延时对单屏蔽
	 */
	const LOG_MARK_DISABLE_MONTHLY_DELAY = 7;
	/**
	 * 解约
	 */
	const LOG_MARK_LEAVE = 8;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverLog the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{driver_log}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'imei, driver_id, last_record, type, description, operator, created', 
				'required'), 
			array (
				'created, type', 
				'numerical', 
				'integerOnly'=>true), 
			array (
				'description, imei', 
				'length', 
				'max'=>255), 
			array (
				'operator, driver_id', 
				'length', 
				'max'=>20), 
			array (
				'created', 
				'length', 
				'max'=>65515), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'imei, driver_id, type, last_record, description, operator, created', 
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
	 * 获取司机被屏蔽的流程
	 * @return 
	 */
	public function getMarkLog($driver_id) {
		self::$db = Yii::app()->db_readonly;
		$criteria = new CDbCriteria();
		//$criteria->select = '';
		$criteria->condition = "driver_id=:driver_id and type<>:type";
		$criteria->params = array (
			':driver_id'=>$driver_id, 
			':type'=>self::LOG_NORMAL);
		$criteria->order = 'created DESC';
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria));
	
		//				
	//		$ret = self::model()->findAll($criteria);
	//		
	//		return $ret;
	}
	/**
	 * 获取司机最后一次被屏蔽的原因
	 * @return 
	 */
	public function getLastMarkLog($driver_id) {
		$criteria = new CDbCriteria();
		//$criteria->select = '';
		$criteria->condition = "driver_id=:driver_id and type<>:type";
		$criteria->params = array (
			':driver_id'=>$driver_id, 
			':type'=>self::LOG_NORMAL);
		$criteria->order = 'created DESC';
		$ret = self::model()->find($criteria);
		
		return $ret;
	}
	/**
	 * 记录操作记录
	 */
	public function insertLog($imei, $type, $driver_id, $last_record, $description) {
		$operator = isset(Yii::app()->user) ? strtoupper(Yii::app()->user->getId()) : '系统自动操作';
		
		$log = new DriverLog();
		$attributes = array (
			'imei'=>$imei, 
			'driver_id'=>$driver_id, 
			'type'=>$type, 
			'last_record'=>$last_record, 
			'description'=>$description, 
			'operator'=>$operator, 
			'created'=>time());
		$log->attributes = $attributes;
		$log->insert();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'id'=>'ID', 
			'type'=>'日志类型', 
			'imei'=>'IMEI', 
			'driver_id'=>'司机编号', 
			'last_record'=>'更新前的记录', 
			'description'=>'说明', 
			'operator'=>'操作人', 
			'created'=>'操作时间');
	}
	
	public function afterFind() {
		if (parent::afterFind()) {
			$this->last_record = CJson::decode($this->last_record, true);
		}
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
		$criteria->compare('type', $this->type);
		$criteria->compare('imei', $this->imei);
		$criteria->compare('driver_id', $this->driver_id);
		$criteria->compare('description', $this->description);
		$criteria->compare('operator', $this->operator);
		$criteria->compare('created', $this->created);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria));
	}
}