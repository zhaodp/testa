<?php

/**
 * This is the model class for table "{{driver_daily_order}}".
 *
 * The followings are the available columns in table '{{driver_daily_order}}':
 * @property integer $id
 * @property string $name
 * @property string $driver_id
 * @property integer $city_id
 * @property integer $order_count
 * @property integer $call_order_count
 * @property integer $phone_order_count
 * @property integer $income
 * @property integer monthly
 * @property string $created
 */
class DriverDailyOrder extends CActiveRecord
{
	public function init() {
		self::$db = Yii::app()->dbstat;
		parent::init();
	}
	/**
	 * 日排行
	 */
	const TYPE_DAILY = 0;
	/**
	 * 月排行
	 */
	const TYPE_MONTHLY = 1;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return RankDayList the static model class
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
		return '{{driver_daily_order}}';
	}
	
	public function save(){
		self::init();
		parent::save();
	}
	
	public function find(){
		self::init();
		parent::find();
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created', 'required'),
			array('city_id, order_count, call_order_count, phone_order_count, income', 'numerical', 'integerOnly'=>true),
			array('name, driver_id', 'length', 'max'=>20),
			array('monthly', 'length', 'max'=>11),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, driver_id, city_id, order_count, call_order_count, phone_order_count, income, created', 'safe', 'on'=>'search'),
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
			'name' => '司机姓名',
			'driver_id' => '司机工号',
			'city_id' => '城市',
			'order_count' => '总接单量',
			'call_order_count' => '呼叫中心派单量',
			'phone_order_count' => '客户直接呼叫量',
			'income' => '收入',
			'mon' => 'mon',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('driver_id',$this->driver_id,true);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('order_count',$this->order_count);
		$criteria->compare('call_order_count',$this->call_order_count);
		$criteria->compare('phone_order_count',$this->phone_order_count);
		$criteria->compare('income',$this->income);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * 获取排行调用数据
	 * Enter description here ...
	 * @param unknown_type $city_id
	 * @param unknown_type $type
	 */
	public function getDriverRankData($city_id,$type){
		$data = array();
		switch ($type){
			case 0:
				$created = date('Y-m-d',strtotime("-2 day"));
				$criteria = new CDbCriteria();
				$params = array();
				if($city_id != 0){
					$criteria->condition = 'city_id = :city_id';
					$params[':city_id'] = $city_id;
				}
				$criteria->addCondition('created = :created');
				$params[':created'] = $created;
				$criteria->order = 'income desc';
				$criteria->params = $params;
				$data['criteria'] = $criteria;
				//获取日排行汇总
				$driverRankCount = $this->getDriverDailyRankCount($city_id,$created);
				$data['driverRankCount'] = $driverRankCount;
				return $data;
				break;
			case 1:
				$date = date('Ym',strtotime("last month"));
				$criteria = new CDbCriteria();
				$criteria->select = 'name,driver_id,sum(order_count) as order_count,sum(call_order_count) as call_order_count,sum(phone_order_count) as phone_order_count,sum(income) as income,count(1) as created';
				$params = array();
				if($city_id != 0){
					$criteria->condition = 'city_id = :city_id';
					$params[':city_id'] = $city_id;
				}
				$criteria->addCondition('monthly = :monthly');
				$params[':monthly'] = $date;
				$criteria->group='driver_id';
				$criteria->order = 'income desc';
				$criteria->params = $params;
				$data['criteria'] = $criteria;
				//获取月排行汇总
				$driverRankCount = $this->getDriverMonthlyRankCount($city_id);
				$data['driverRankCount'] = $driverRankCount;
				return $data;
				break;
		}
	}
	
	/**
	 * 获取日排行信息统计
	 * Enter description here ...
	 * @param unknown_type $city_id
	 */
	public function getDriverDailyRankCount($city_id,$date=NULL){
		if(empty($date))
			$date = date('Y-m-d',strtotime("-2 day"));
		$DriverDailyOrder = new DriverDailyOrder();
		$criteria = new CDbCriteria();
		$criteria->select = 'sum(order_count) as order_count,count(1) as driver_id,created,city_id';
		$criteria->condition = 'city_id =:city_id';
		$criteria->addCondition('created = :created');
		$criteria->params = array(':city_id' => $city_id,
								':created' =>$date);
		$Rank_count = $DriverDailyOrder->find($criteria);
		if($Rank_count->order_count){
			$rank_str ='<h2>昨日（%s 15:00 -- %s 15:00）订单数据汇总</h2>
					<h3>
						%s订单总数:<font color="red">%s</font>单，
						接单司机人数：<font color="red">%s</font>, 
						平均接单数：<font color="red">%s</font>单/人
					</h3>';
			$dateBegin = date('m-d',strtotime($date));
			$dateEnd = date('m-d',strtotime($date)+86400);
			$city_id = Dict::item('city', $Rank_count->city_id);
			$order_count = $Rank_count->order_count;
			$driver_order_count = $Rank_count->driver_id;
			$driver_order_avg = sprintf("%.1f",$order_count/$driver_order_count);
			$rank_str = sprintf($rank_str,$dateBegin,$dateEnd,$city_id,$order_count,$driver_order_count,$driver_order_avg);
			return $rank_str;
		}
		else
			return '';
	}
	
	/**
	 * 获取月排行
	 */
	public function getDriverMonthlyRankCount($city_id = 1){
		$monthly = date('Ym',strtotime("-1 month"));
		$DriverDailyOrder = new DriverDailyOrder();
		$criteria = new CDbCriteria();
		$criteria->select = 'sum(order_count) as order_count,count(distinct driver_id) as driver_id,monthly,city_id,count(1) as call_order_count';
		$criteria->condition = 'city_id =:city_id';
		$criteria->addCondition('monthly = :monthly');
		$criteria->params = array(':city_id' => $city_id,
								':monthly' =>$monthly);
		$Rank_count = $DriverDailyOrder->find($criteria);
		if($Rank_count->order_count){
			$rank_str ='<h2>上月（%s -- %s）订单数据汇总</h2>
					<h3>
						%s订单总数:<font color="red">%s</font>单，
						接单司机人数：<font color="red">%s</font>, 
						平均上线天数： <font color="red">%s</font>天/人,
						平均接单数：<font color="red">%s</font>单/人
					</h3>';
			$dateBegin = date('m-01',strtotime("-1 month"));
			$dateEnd = date('m-d',strtotime(date('Y-m-01',time()))-86400);
			$city_id = Dict::item('city', $Rank_count->city_id);
			$order_count = $Rank_count->order_count;
			$driver_daily_count = $Rank_count->driver_id;
			$driver_daily_avg = sprintf("%.1f",$Rank_count->call_order_count/$driver_daily_count);
			$driver_order_avg = sprintf("%.1f",$order_count/$driver_daily_count/$driver_daily_avg);
			$rank_str = sprintf($rank_str,$dateBegin,$dateEnd,$city_id,$order_count,$driver_daily_count,$driver_daily_avg,$driver_order_avg);
			return $rank_str;
		}
		else
			return $Rank_count->order_count;
	}
	
	
}