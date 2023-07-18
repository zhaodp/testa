<?php

/**
 * 读从库进行订单搜索,降低主库压力
 * Order.php调用地方过多,为避免切换读写造成问题,创建SearchOrder
 * using by search order, db is readonly.
 *
 * This is the model class for table "{{order}}".
 *
 * The followings are the available columns in table '{{order}}':
 * @property integer $order_id
 * @property string $order_number
 * @property integer $user_id
 * @property string $name
 * @property string $phone
 * @property string $hashphone
 * @property string $vipcard
 * @property integer $car_id
 * @property string $driver
 * @property string $driver_id
 * @property string $city_id
 * @property string $driver_phone
 * @property string $imei
 * @property integer $call_time
 * @property integer $call_type
 * @property string $order_date
 * @property integer $booking_time
 * @property integer $reach_time
 * @property integer $reach_distance
 * @property integer $start_time
 * @property integer $end_time
 * @property integer $distance
 * @property integer $charge
 * @property string $location_start
 * @property string $location_end
 * @property integer $income
 * @property integer $cast
 * @property string $description
 * @property string $cancel_desc
 * @property integer $cancel_type
 * @property integer $status
 * @property integer $created
 */
class SearchOrder extends OrderActiveRecord
{
    public $hash_phone;
    public $mask_phone;
    public $bonus_code;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Order the static model class
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
        return '{{order}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            //array('user_id, name, phone, driver, imei, call_time, booking_time, reach_time, reach_distance, start_time, end_time, distance, charge, location_start, location_end, income, cast', 'required'),
            array(
                'phone, driver, driver_id, imei, call_time, booking_time',
                'required'),
            array(
                'city_id, user_id, source, car_id, call_time, call_type, cancel_type, order_date, booking_time, reach_time, reach_distance, distance, charge, income, price, cost_type, created, status',
                'numerical',
                'integerOnly' => true),
            array(
                'start_time, end_time',
                'length',
                'max' => 11),
            array(
                'cancel_type, income',
                'length',
                'max' => 4),
            array('cast',
                'length',
                'max' => 10),
            array(
                'cost_type',
                'length',
                'max' => 2),
            array(
                'order_number, name, phone, driver_phone, imei, location_start, location_end',
                'length',
                'max' => 20),
            array(
                'driver',
                'length',
                'max' => 30),
            array(
                'vipcard',
                'length',
                'max' => 15),
            array(
                'description,cancel_desc',
                'length',
                'max' => 512),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array(
                'order_id, order_number, user_id, cancel_desc,name, phone, md5(phone) hash_phone,vipcard, driver,
                 driver_id, city_id, imei, call_time, call_type, cancel_type, booking_time, reach_time, reach_distance,
                 start_time, end_time, distance, charge, location_start,
                 location_end, income, cast, price, cost_type, description, created',
                'safe',
                'on' => 'search'));
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'driver_state' => array(
                self::BELONGS_TO,
                'OrderDriverState',
                'order_id'),
            'customer' => array(
                self::BELONGS_TO,
                'Customer',
                'phone'),
            'car' => array(
                self::BELONGS_TO,
                'CustomerCar',
                'car_id'),
            'invoice' => array(
                self::HAS_ONE,
                'OrderInvoice',
                'order_id'),
            'order_queue_map' => array(
                self::HAS_ONE,
                'OrderQueueMap',
                'order_id'),
            'order_ext' => array(
                self::HAS_ONE,
                'OrderExt',
                'order_id'),
            'order_bonus' => array(
                self::HAS_ONE,
                'CustomerBonus',
                'order_id'),
            'comment_sms' => array(
                self::HAS_ONE,
                'CommentSms',
                'order_id'),
            'customer_complain' => array(
                self::HAS_ONE,
                'CustomerComplain',
                'order_id'),
            'driver_complain' => array(
                self::HAS_ONE,
                'DriverComplaint',
                'order_id'),
            'order_log' => array(
                self::HAS_MANY,
                'OrderLog',
                'order_id'),
            'employee' => array(
                self::BELONGS_TO,
                'Employee',
                'driver_id',
                'join' => ',t_order d o',
                'on' => 'o.driver_id = user'));
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'order_id' => '流水号',
            'order_number' => '单号',
            'channel' => '合作商家',
            'source' => '订单来源',
            'user_id' => 'UserID',
            'name' => '客户名称',
            'phone' => '客户电话',
            'vipcard' => 'VIP卡号',
            'bonus_code' => '优惠码',
            'driver' => '司机',
            'driver_id' => '司机工号',
            'city_id' => '城市',
            'driver_phone' => '司机电话',
            'imei' => 'Imei',
            'call_time' => '呼叫时间',
            'call_type' => '呼叫类型',
            'order_date' => 'Order Date',
            'booking_time' => '预约时间',
            'reach_time' => '到达时间',
            'reach_distance' => '到达距离',
            'start_time' => '出发时间',
            'end_time' => '到达时间',
            'distance' => '里程',
            'charge' => '价格',
            'location_start' => '出发地',
            'location_end' => '到达地',
            'income' => '收费',
            'cast' => '扣款',
            'cost_type' => '扣款类型',
            'price' => '实收费用',
            'description' => '订单来源',
            'cancel_type' => '销单类别',
            'cancel_desc' => '销单原因',
            'status' => '状态',
            'created' => '派单时间');
    }

    /**
     * md5 客户电话号码,隐藏客户电话的中间4位
     * @editor AndyCong 2013-04-16 加黑名单短信通知
     */
    public function afterFind() {
        $this->hash_phone = md5($this->phone);
        $this->mask_phone = substr_replace($this->phone, '****', 3, 4);
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search($criteria) {

        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        if (!$criteria) {
            $criteria = new CDbCriteria();
        }

        //0为兼职，1为全职，-1为非登陆用户
        $callCenterUserType = Yii::app()->user->getCallCenterUserType();
        if ($callCenterUserType == 1) {
            //$phone = strval($this->phone);
            $phone = strval($this->phone);
            $criteria->compare('order_id', $this->order_id);
            $criteria->compare('order_number', $this->order_number);
            $criteria->compare('user_id', $this->user_id);
            $criteria->compare('name', $this->name);
            $criteria->compare('t.phone', $phone);
            $criteria->compare('md5(t.phone)', $this->hash_phone);
            $criteria->compare('vipcard', $this->vipcard);
            $criteria->compare('driver', $this->driver);
            $criteria->compare('driver_id', $this->driver_id);
            $criteria->compare('driver_phone', $this->driver_phone);
            //$criteria->compare('call_time', $this->call_time);
            //$criteria->compare('booking_time', $this->booking_time);
            //$criteria->compare('reach_time', $this->reach_time);
            //$criteria->compare('reach_distance', $this->reach_distance);
            //$criteria->compare('start_time', $this->start_time);
            //$criteria->compare('end_time', $this->end_time);
            //$criteria->compare('distance', $this->distance);
            //$criteria->compare('charge', $this->charge);
            //$criteria->compare('location_start', $this->location_start, true);
            //$criteria->compare('location_end', $this->location_end, true);
            $criteria->compare('income', $this->income);
            //$criteria->compare('cast', $this->cast);
            $criteria->compare('cancel_type', $this->cancel_type);
            $criteria->compare('source', $this->source);
            $criteria->compare('status', $this->status);
            if ($this->city_id != 0) {
                $criteria->compare('city_id', $this->city_id);
            }
            //$criteria->compare('created', $this->created);

            if (!empty($this->call_time)) {
                $criteria->addCondition("booking_time >= :call_start_time");
                $criteria->params[':call_start_time'] = strtotime($this->call_time);
            }

            if (!empty($this->booking_time)) {
                $criteria->addCondition("booking_time <= :call_end_time");
                $criteria->params[':call_end_time'] = strtotime($this->booking_time);
            }
        } else {
            $params = array();
            if (empty($this->phone) && empty($this->driver_id) && empty($this->vipcard)) {
                $criteria->addCondition('1 = 0');
            }

            if (!empty($this->phone)) {
                $criteria->addCondition('t.phone = :phone');
                $params[':phone'] = strval($this->phone);
            }

            if (!empty($this->driver_id)) {
                $criteria->addCondition('driver_id = :driver_id');
                $params[':driver_id'] = $this->driver_id;
            }

            if (!empty($this->vipcard)) {
                $criteria->addCondition('vipcard = :vipcard');
                $params[':vipcard'] = $this->vipcard;
            }

            if (!empty($this->call_time)) {
                $criteria->addCondition("booking_time >= :call_start_time");
                $params[':call_start_time'] = strtotime($this->call_time);
            }

            $criteria->params = $params;
        }

        $criteria->order = 'call_time desc';

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 15)));

    }
}
