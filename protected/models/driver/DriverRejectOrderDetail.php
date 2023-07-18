<?php
/**
 * 司机拒单详情
 * 
 * @author qiujianping 2014-08-25
 * 
 * This is the model class for table "{{driver_reject_order_detail}}".
 *
 * The followings are the available columns in table '{{driver_reject_order_detail}}':
 * @property string $id
 * @property string $driver_id
 * @property integer $order_id
 * @property interger $fail_type
 * @property string $description
 * @property interger $created
 */
class DriverRejectOrderDetail extends CActiveRecord
{
	const DISPATCH_FAIL_DRIVER_NO_RESPONSE = 2;
	const DISPATCH_FAIL_DRIVER_REJECT = 3;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrderProcess the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return CDbConnection database connection
	 */
	public function getDbConnection()
	{
	    return Yii::app()->dbreport;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{driver_reject_order_detail}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('driver_id, order_id, fail_type, description, created', 'required'),
			array('order_id, faile_type, created', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, driver_id, order_id, fail_type, created', 'safe', 'on'=>'search'),
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
			'order_id' => 'Order',
			'driver_id' => 'Driver',
			'fail_type' => 'ErrorCode',
			'description' => 'Description',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('driver_id',$this->driver_id);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('fail_type',$this->fail_type,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	/**
	 * @author qiujianping@edaijia-staff.cn 2014-08-28
	 * 
	 * Insert a reject log to database
	 *
	 * @params order_id
	 * @params driver_id
	 * @state reject_type
	 */
	public function createNewRejectDetail($params) {
	    $detail_data['order_id'] = $params['order_id'];
	    $detail_data['driver_id'] = $params['driver_id'];
	    $detail_data['fail_type'] = $params['fail_type'];
	    $detail_data['created'] = $params['created'];
	    $detail_data['description'] = OrderProcess::model()
		->getFailTypeDescription($params['fail_type']);
	    return Yii::app()->dbreport->createCommand()
		->insert("t_driver_reject_order_detail",$detail_data);
	}

	/**
	 * @author qiujianping@edaijia-staff.cn 2014-09-19
	 * 
	 * @param the driverid to be get 
	 * @return The reject detail
	 */
	public function getMonRejectOrdersByDriverId($driver_id = 'BJ00000') {
	      $ret = array();

	      $start_time = date("Y-m-01 07:00:00", time());

	      $command = Yii::app()->dbreport->createCommand();
	      $command->select('*')
		  ->from(DriverRejectOrderDetail::model()->tableName())
		  ->where('driver_id=:driver_id and created>:start_time')
	          ->order('created desc');

	      $ret = array();
	      $ret = $command->queryAll(true, array(':driver_id' => $driver_id,
			  'start_time' => $start_time));
	      return $ret;
	  }

  /**
   * @author qiujianping@edaijia-staff.cn 2014-04-04
   * 
   * Return the process of a specified order
   *
   * @param the order_id to be get 
   * @return All the processes of the order
   */
  public function getRejectDetailByDriverId($driver_id = 'BJ00000') {
      $ret = array();

      $command = Yii::app()->dbreport->createCommand();
      $command->select('*')
	  ->from(DriverRejectOrderDetail::model()->tableName())
	  ->where('driver_id=:driver_id');

      $ret = array();
      $ret = $command->queryAll(true, array(':driver_id' => $driver_id));
      return $ret;
  }


  /**
  *	@author aiguoxin@edaijia-inc.cn
  * Return distinct reject order count
  * @param driver_id start_time, end_time
  * @return distinct reject order count 
  */
  public function getOrderNumByDriverIdAndTime($driver_id, $start_time, $end_time){
      
      $count = Yii::app()->dbreport->createCommand()
                ->select('count(order_id)')
                ->from(DriverRejectOrderDetail::model()->tableName())
                ->where('driver_id=:driver_id and created >=:start_time and created <=:end_time',
                 	array(':driver_id' => $driver_id,':start_time'=>$start_time,'end_time'=>$end_time))
                ->queryScalar();
      return $count;

  }

} // End file
