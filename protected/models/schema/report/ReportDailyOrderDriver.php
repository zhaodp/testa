<?php

/**
 * This is the model class for table "{{daily_order_driver}}".
 *
 * The followings are the available columns in table '{{daily_order_driver}}':
 * @property string $id
 * @property string $order_id
 * @property string $order_number
 * @property integer $source
 * @property integer $city_id
 * @property integer $call_type
 * @property integer $call_time
 * @property string $order_date
 * @property integer $booking_time
 * @property integer $reach_time
 * @property integer $reach_distance
 * @property integer $start_time
 * @property integer $end_time
 * @property integer $time_part
 * @property integer $current_month
 * @property integer $current_day
 * @property integer $year
 * @property integer $month
 * @property integer $day
 * @property integer $distance
 * @property integer $charge
 * @property string $location_start
 * @property string $location_end
 * @property integer $income
 * @property integer $cast
 * @property integer $coupon
 * @property integer $status
 * @property integer $user_id
 * @property string $customer_name
 * @property integer $customer_type
 * @property string $phone
 * @property string $vipcard
 * @property integer $is_new_user
 * @property integer $is_active
 * @property string $driver
 * @property integer $driver_id
 * @property string $driver_user
 * @property string $driver_phone
 * @property string $driver_imei
 * @property string $driver_picture
 * @property integer $is_new_driver
 * @property integer $is_left
 * @property integer $created
 */
class ReportDailyOrderDriver extends ReportActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{daily_order_driver}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('source, city_id, call_type, call_time, booking_time, reach_time, reach_distance, start_time, end_time, time_part, current_month, current_day, year, month, day, distance, charge, income, cast, coupon, status, user_id, customer_type, is_new_user, is_active, driver_id, is_new_driver, is_left, created', 'numerical', 'integerOnly'=>true),
			array('order_id, order_number, phone, driver_user, driver_phone, driver_imei', 'length', 'max'=>20),
			array('order_date', 'length', 'max'=>8),
			array('location_start, location_end, customer_name, driver', 'length', 'max'=>30),
			array('vipcard', 'length', 'max'=>15),
			array('driver_picture', 'length', 'max'=>200),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, order_id, order_number, source, city_id, call_type, call_time, order_date, booking_time, reach_time, reach_distance, start_time, end_time, time_part, current_month, current_day, year, month, day, distance, charge, location_start, location_end, income, cast, coupon, status, user_id, customer_name, customer_type, phone, vipcard, is_new_user, is_active, driver, driver_id, driver_user, driver_phone, driver_imei, driver_picture, is_new_driver, is_left, created', 'safe', 'on'=>'search'),
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
			'id' => 'id(主键)',
			'order_id' => '订单ID',
			'order_number' => '订单号码',
			'source' => '渠道',
			'city_id' => '城市ID',
			'call_type' => '呼叫类型',
			'call_time' => '呼叫时间',
			'order_date' => '下单日期',
			'booking_time' => '预约时间',
			'reach_time' => '到达时间',
			'reach_distance' => '到达距离',
			'start_time' => '开始时间',
			'end_time' => '结束时间',
			'time_part' => '时间段',
			'current_month' => '当前月份',
			'current_day' => '当前日期',
			'year' => '年',
			'month' => '月',
			'day' => '日',
			'distance' => '驾驶里程',
			'charge' => '价格',
			'location_start' => '起始位置',
			'location_end' => '终止位置',
			'income' => '收入',
			'cast' => '费用',
			'coupon' => '代金券金额',
			'status' => '状态',
			'user_id' => '用户ID',
			'customer_name' => '用户名',
			'customer_type' => '用户类型',
			'phone' => '电话号',
			'vipcard' => 'vip卡',
			'is_new_user' => '0:老用户、1:新用户',
			'is_active' => '0:非活跃用户、1:活跃用户',
			'driver' => '司机用户名',
			'driver_id' => '司机ID',
			'driver_user' => '司机工号',
			'driver_phone' => '司机手机号',
			'driver_imei' => '司机imei号',
			'driver_picture' => '司机图片',
			'is_new_driver' => '0:老用户、1:新用户',
			'is_left' => '0:未解约、1:已解约',
			'created' => '创建时间',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('order_id',$this->order_id,true);
		$criteria->compare('order_number',$this->order_number,true);
		$criteria->compare('source',$this->source);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('call_type',$this->call_type);
		$criteria->compare('call_time',$this->call_time);
		$criteria->compare('order_date',$this->order_date,true);
		$criteria->compare('booking_time',$this->booking_time);
		$criteria->compare('reach_time',$this->reach_time);
		$criteria->compare('reach_distance',$this->reach_distance);
		$criteria->compare('start_time',$this->start_time);
		$criteria->compare('end_time',$this->end_time);
		$criteria->compare('time_part',$this->time_part);
		$criteria->compare('current_month',$this->current_month);
		$criteria->compare('current_day',$this->current_day);
		$criteria->compare('year',$this->year);
		$criteria->compare('month',$this->month);
		$criteria->compare('day',$this->day);
		$criteria->compare('distance',$this->distance);
		$criteria->compare('charge',$this->charge);
		$criteria->compare('location_start',$this->location_start,true);
		$criteria->compare('location_end',$this->location_end,true);
		$criteria->compare('income',$this->income);
		$criteria->compare('cast',$this->cast);
		$criteria->compare('coupon',$this->coupon);
		$criteria->compare('status',$this->status);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('customer_name',$this->customer_name,true);
		$criteria->compare('customer_type',$this->customer_type);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('vipcard',$this->vipcard,true);
		$criteria->compare('is_new_user',$this->is_new_user);
		$criteria->compare('is_active',$this->is_active);
		$criteria->compare('driver',$this->driver,true);
		$criteria->compare('driver_id',$this->driver_id);
		$criteria->compare('driver_user',$this->driver_user,true);
		$criteria->compare('driver_phone',$this->driver_phone,true);
		$criteria->compare('driver_imei',$this->driver_imei,true);
		$criteria->compare('driver_picture',$this->driver_picture,true);
		$criteria->compare('is_new_driver',$this->is_new_driver);
		$criteria->compare('is_left',$this->is_left);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * @return CDbConnection the database connection used for this class
	 */
	public function getDbConnection()
	{
		return Yii::app()->dbreport;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DailyOrderDriver the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function getOldestOrder($city_id){
        $criteria = new CDbCriteria();
        $criteria->select = 'call_time';
        $criteria->addCondition('city_id = :city_id and call_time > :call_time');
        $criteria->params[':city_id']=$city_id;
        $criteria->params[':call_time']=0;
        $criteria->order = 'call_time desc';
        $criteria->limit = 1;
        $res = $this->find($criteria);
        if(is_object($res)){
            return $res->call_time;
        }else {
            return false;
        }
    }
}
