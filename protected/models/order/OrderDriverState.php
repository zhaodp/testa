<?php

/**
 * This is the model class for table "{{order_driver_state}}".
 *
 * The followings are the available columns in table '{{order_driver_state}}':
 * @property integer $order_id
 * @property string $state
 */
class OrderDriverState extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrderDriverState the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{order_driver_state}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'order_id, state', 
				'required'), 
			array (
				'order_id', 
				'numerical', 
				'integerOnly'=>true), 
			array (
				'state', 
				'length', 
				'max'=>2), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'order_id, state', 
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
			'order_id'=>'Order', 
			'state'=>'State');
	}
	
	public static function setState($order_id, $imei, $time) {
		$connect = Yii::app()->db_readonly;
		$table_perfix = 't_driver_position_';
		$stat_connect = Yii::app()->dbstat_readonly;
		$driver = $connect->createCommand()
				->select('id')
				->from('t_driver')
				->where('imei=:imei', array(':imei'=>$imei))
				->queryRow();
		$month = date('Ym', $time);
		
		$ret = self::model()->find('order_id=:order_id', array (
			':order_id'=>$order_id));
		
		if (!$ret) {
			$call_time = date(Yii::app()->params['formatDateTime'], $time);
			
			$start_time = $call_time;
			$end_time = date(Yii::app()->params['formatDateTime'], $time+600);
			
			$last_state = '';
			$prv_state = '';
//			$criteria = new CDbCriteria(array (
//				'select'=>'state,insert_time', 
//				'condition'=>'imei=:imei and insert_time between :start_time and :end_time', 
//				'order'=>'insert_time desc', 
//				'limit'=>1, 
//				'params'=>array (
//					':imei'=>$imei, 
//					':start_time'=>$start_time, 
//					':end_time'=>$end_time)));
//			
//			$last = EmployeeTrack::model()->find($criteria);
//			$last_state = (!$last) ? 2 : $last->state;
			$last = $stat_connect->createCommand()
				->select('state')
				->from($table_perfix.$month)
				->where(array('AND', 'user_id=:user_id', 'created BETWEEN :start_time AND :end_time'), 
						array(':user_id'=>$driver['id'], ':start_time'=>$start_time, ':end_time'=>$end_time))
				->order('created DESC')
				->limit(1)
				->queryRow();
			$last_state = (!$last) ? 2 : $last['state'];
			
			//查询接电话前10分钟的状态，如果没有，则为下班状态
			$start_time = date(Yii::app()->params['formatDateTime'], $time-600);
			$end_time = $call_time;
//			$criteria = new CDbCriteria(array (
//				'select'=>'state,insert_time', 
//				'condition'=>'imei=:imei and insert_time between :start_time and :end_time', 
//				'limit'=>1, 
//				'params'=>array (
//					':imei'=>$imei, 
//					':start_time'=>$start_time, 
//					':end_time'=>$end_time)));
//			
//			$prv = EmployeeTrack::model()->find($criteria);
//			$prv_state = (!$prv) ? 2 : $prv->state;
			
			$prv = $stat_connect->createCommand()
				->select('state')
				->from($table_perfix.$month)
				->where(array('AND', 'user_id=:user_id', 'created BETWEEN :start_time AND :end_time'), 
						array(':user_id'=>$driver['id'], ':start_time'=>$start_time, ':end_time'=>$end_time))
				->order('created DESC')
				->limit(1)
				->queryRow();
			$prv_state = (!$prv) ? 2 : $prv['state'];
			
			$state = new OrderDriverState();
			$state->attributes = array (
				'order_id'=>$order_id, 
				'state'=>$prv_state.$last_state);
			
			echo $prv_state.$last_state."\n";
			$state->insert();
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
		
		$criteria->compare('order_id', $this->order_id);
		$criteria->compare('state', $this->state);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria));
	}
}