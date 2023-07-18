<?php

/**
 * This is the model class for table "{{daily_driver_order_report}}".
 *
 * The followings are the available columns in table '{{daily_driver_order_report}}':
 * @property integer $id
 * @property string $name
 * @property string $driver_id
 * @property integer $city_id
 * @property string $city_name
 * @property integer $order_count
 * @property integer $app_count
 * @property integer $callcenter_count
 * @property integer $cancel_count
 * @property integer $init_count
 * @property integer $income
 * @property integer $current_month
 * @property integer $current_day
 * @property integer $created
 */
class DailyDriverOrderReport extends CActiveRecord
{
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
	 * @return DailyDriverOrderReport the static model class
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
		return '{{daily_driver_order_report}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
		array('order_count, app_count, callcenter_count, cancel_count, init_count, income, current_month, current_day', 'required'),
		array('city_id, order_count, app_count, callcenter_count, cancel_count, init_count, income, current_month, current_day, created', 'numerical', 'integerOnly'=>true),
		array('name, driver_id, city_name', 'length', 'max'=>20),
		// The following rule is used by search().
		// Please remove those attributes that should not be searched.
		array('id, name, driver_id, city_id, city_name, order_count, app_count, callcenter_count, cancel_count, init_count, income, current_month, current_day, created', 'safe', 'on'=>'search'),
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
			'name' => '司机名字',
			'driver_id' => '司机工号',
			'city_id' => '城市ID',
			'city_name' => '城市名字',
			'order_count' => '总接单量',
			'app_count' => '呼叫中心派单量',
			'callcenter_count' => '客户直接呼叫量',
			'cancel_count' => '销单量',
			'init_count' => '未报单',
			'income' => '收入',
			'current_month' => '当前月',
			'current_day' => '当天前',
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
		$criteria->compare('city_name',$this->city_name,true);
		$criteria->compare('order_count',$this->order_count);
		$criteria->compare('app_count',$this->app_count);
		$criteria->compare('callcenter_count',$this->callcenter_count);
		$criteria->compare('cancel_count',$this->cancel_count);
		$criteria->compare('init_count',$this->init_count);
		$criteria->compare('income',$this->income);
		$criteria->compare('current_month',$this->current_month);
		$criteria->compare('current_day',$this->current_day);
		$criteria->compare('created',$this->created);

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
	public function getDriverRankData($city_id, $type, $pages = 10){
		$dateSeparation = date('H',time());
		$data = array();
		switch ($type){
			case self::TYPE_DAILY:
				if($dateSeparation>16)
					$current_day = date('Ymd',strtotime("-2 day"));
				else
					$current_day = date('Ymd',strtotime("-3 day"));
				$criteria = new CDbCriteria();
				$params = array();
				if($city_id != 0){
					$criteria->condition = 'city_id = :city_id';
					$params[':city_id'] = $city_id;
				}
				$criteria->addCondition('current_day = :current_day');
				$criteria->addCondition('order_count > 0');
				$params[':current_day'] = $current_day;
				$criteria->order = 'income desc';
				$criteria->params = $params;
				$dataDailyOrderRank = new CActiveDataProvider($this, array (
						'criteria'=>$criteria,
						'pagination'=>array (
							'pageSize'=>$pages)
						));
				$data['dataDailyOrderRank'] = $dataDailyOrderRank;
				
				//获取日排行汇总
				$driverRankCount = $this->getDriverDailyRankCount($city_id,$current_day);
				$data['driverRankCount'] = $driverRankCount;
				return $data;
				break;
			case self::TYPE_MONTHLY:
				$date = date('Ym',strtotime("last month"));
				$criteria = new CDbCriteria();
				$criteria->select = 'name,driver_id,sum(order_count) as order_count,sum(app_count) as app_count,sum(callcenter_count) as callcenter_count,sum(income) as income,count(1) as created';
				$params = array();
				if($city_id != 0){
					$criteria->condition = 'city_id = :city_id';
					$params[':city_id'] = $city_id;
				}
				$criteria->addCondition('current_month = :current_month');
				$criteria->addCondition('order_count > 0');
				$params[':current_month'] = $date;
				$criteria->group='driver_id';
				$criteria->order = 'income desc';
				$criteria->params = $params;
				$dataDailyOrderRank = new CActiveDataProvider($this, array (
						'criteria'=>$criteria,
						'pagination'=>array (
							'pageSize'=>$pages)
						));
				$data['dataDailyOrderRank'] = $dataDailyOrderRank;
				
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
	public function getDriverDailyRankCount($city_id,$date = NULL){
		$dateSeparation = date('H',time());
		if(empty($date)){
			if($dateSeparation>16)
				$date = date('Ymd',strtotime("-2 day"));
			else
				$date = date('Ymd',strtotime("-3 day"));
		}
		$criteria = new CDbCriteria();
		$criteria->select = 'sum(order_count) as order_count,count(1) as driver_id,created,city_id,city_name';
		$params = array();
		if($city_id != 0){
			$criteria->condition = 'city_id =:city_id';
			$params[':city_id'] = $city_id;
		}
		$criteria->addCondition('current_day = :current_day');
		$criteria->addCondition('order_count > 0');
		$params[':current_day'] = $date;
		$criteria->params = $params;
		$Rank_count = self::model()->find($criteria);
		if($Rank_count->order_count){
			$rank_str ='<h2>%s（%s 15:00 -- %s 15:00）订单数据汇总</h2>
					<h3>
						%s订单总数:<font color="red">%s</font>单，
						接单司机人数：<font color="red">%s</font>, 
						平均接单数：<font color="red">%s</font>单/人
					</h3>';
			$displayDate = $dateSeparation > 16 ? "昨日" : "前日";
			$dateBegin = date('m-d',strtotime($date));
			$dateEnd = date('m-d',strtotime($date)+86400);
			$city_id = empty($city_id) ? "全部" : $Rank_count->city_name;
			$order_count = $Rank_count->order_count;
			$driver_order_count = $Rank_count->driver_id;
			$driver_order_avg = sprintf("%.1f",$order_count/$driver_order_count);
			$rank_str = sprintf($rank_str,$displayDate,$dateBegin,$dateEnd,$city_id,$order_count,$driver_order_count,$driver_order_avg);
			return $rank_str;
		}
		else
		return '';
	}

	/**
	 * 获取月排行
	 */
	public function getDriverMonthlyRankCount($city_id){
		$current_month = date('Ym',strtotime("-1 month"));
		$criteria = new CDbCriteria();
		$criteria->select = 'sum(order_count) as order_count,count(distinct driver_id) as driver_id,current_month,city_id,city_name,count(1) as app_count';
		$params = array();
		if($city_id != 0){
			$criteria->condition = 'city_id =:city_id';
			$params[':city_id'] = $city_id;
		}
		$criteria->addCondition('current_month = :current_month');
		$criteria->addCondition('order_count > 0');
		$params[':current_month'] = $current_month;
		$criteria->params = $params;
		$Rank_count = self::model()->find($criteria);
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
			$city_id = empty($city_id) ? "全部" : $Rank_count->city_name;
			$order_count = $Rank_count->order_count;
			$driver_daily_count = $Rank_count->driver_id;
			$driver_daily_avg = sprintf("%.1f",$Rank_count->app_count/$driver_daily_count);
			$driver_order_avg = sprintf("%.1f",$order_count/$driver_daily_count/$driver_daily_avg);
			$rank_str = sprintf($rank_str,$dateBegin,$dateEnd,$city_id,$order_count,$driver_daily_count,$driver_daily_avg,$driver_order_avg);
			return $rank_str;
		}
		else
		return '';
	}
}