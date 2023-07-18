<?php

/**
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
class Order extends OrderActiveRecord
{
    /**
     * 正常订单
     */
    const ORDER_READY = 0;
    /**
     * 完成报单的订单
     */
    const ORDER_COMPLATE = 1;
    /**
     * 销单待审核
     */
    const ORDER_COMFIRM = 2;
    /**
     * 已销单
     */
    const ORDER_CANCEL = 3;
    /**
     * 销单审核不通过
     */
    const ORDER_NOT_COMFIRM = 4;

    //增加取消状态码 BY AndyCong 2013-11-21
    const ORDER_DRIVER_REJECT_CANCEL = 5; //司机拒绝生成的取消订单
    const ORDER_CUSTOMER_CANCEL = 6; //用户取消
    const ORDER_NO_DISPATCH_CANCEL = 7; //未派出取消
    const ORDER_DRIVER_REJECT_NO_DISPATCH = 8; //未派出取消（司机拒单）

    /**
     * 订单来源：里程收费
     */
    const SOURCE_CLIENT = 0; //客户呼叫
    const SOURCE_CALLCENTER = 1; //呼叫中心
    const SOURCE_CLIENT_INPUT = 2; //客户呼叫补单
    const SOURCE_CALLCENTER_INPUT = 3; //呼叫中心补单
    public static $distance_sources = array(
        self::SOURCE_CLIENT,
        self::SOURCE_CALLCENTER,
        self::SOURCE_CLIENT_INPUT,
        self::SOURCE_CALLCENTER_INPUT,
    );

    /**
     * 订单来源：被司机端占用的source,服务器端不要使用
     */
    const SOURCE_DRIVER_CALLORDER = 10;

    /**
     * 订单来源：weixin
     * Please check with driver client
     */
    const SOURCE_WEIXIN = 21;

    /**
     * 订单来源：计时收费(日间业务)
     */
    const SOURCE_DAYTIME_CLIENT = 30; //客户呼叫
    const SOURCE_DAYTIME_CALLCENTER = 31; //呼叫中心
    const SOURCE_DAYTIME_CLIENT_INPUT = 32; //客户呼叫补单
    const SOURCE_DAYTIME_CALLCENTER_INPUT = 33; //呼叫中心补单
    public static $daytime_sources = array(
        self::SOURCE_DAYTIME_CLIENT,
        self::SOURCE_DAYTIME_CALLCENTER,
        self::SOURCE_DAYTIME_CLIENT_INPUT,
        self::SOURCE_DAYTIME_CALLCENTER_INPUT
    );

    /**
     * 订单来源：一口价(洗车)
     */
    const SOURCE_WASHCAR_CLIENT = 40; //客户呼叫
    const SOURCE_WASHCAR_CALLCENTER = 41; //呼叫中心
    const SOURCE_WASHCAR_CLIENT_INPUT = 42; //客户呼叫补单
    const SOURCE_WASHCAR_CALLCENTER_INPUT = 43; //呼叫中心补单
    public static $washcar_sources = array(
        self::SOURCE_WASHCAR_CLIENT,
        self::SOURCE_WASHCAR_CALLCENTER,
        self::SOURCE_WASHCAR_CLIENT_INPUT,
        self::SOURCE_WASHCAR_CALLCENTER_INPUT
    );

    //APP呼叫 source 集合
    public static $client_source = array(
        self::SOURCE_CLIENT,
        self::SOURCE_DAYTIME_CLIENT,
        self::SOURCE_WASHCAR_CLIENT,
    );

    //APP呼叫补单 source 集合
    public static $client_input_source = array(
        self::SOURCE_CLIENT_INPUT,
        self::SOURCE_DAYTIME_CLIENT_INPUT,
        self::SOURCE_WASHCAR_CLIENT_INPUT,
    );

    //400订单 source 集合
    public static $callcenter_sources = array(
        self::SOURCE_CALLCENTER,
        self::SOURCE_DAYTIME_CALLCENTER,
        self::SOURCE_WASHCAR_CALLCENTER,
    );

    //400补单 source 集合
    public static $callcenter_input_sources = array(
        self::SOURCE_CALLCENTER_INPUT,
        self::SOURCE_DAYTIME_CALLCENTER_INPUT,
        self::SOURCE_WASHCAR_CALLCENTER_INPUT,
    );

    //订单ID前缀
    const ORDER_ID_PRE = 'order_';

    //报单订单缓存KEY
    const SUBMIT_ORDER_CACHE_KEY = 'submit_order_cache_';

    const NOW_TABLE = 2;//当前使用表
    const CURRENT_YEAR_TABLE = 3;//当年表
    const PRE_YEAR_TABLE = 4;//去年表

    public $hash_phone;
    public $mask_phone;
    public $bonus_code;
    public $is_api = false;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Order the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{order}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
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
                 start_time, end_time, distance, charge, location_start,cancel_code,
                 location_end, income, cast, price, cost_type, description, created',
                'safe',
                'on' => 'search'));
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
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
    public function attributeLabels()
    {
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
            'cancel_code' => '取消code',
            'status' => '状态',
            'created' => '派单时间');
    }

    /**
     * md5 客户电话号码,隐藏客户电话的中间4位
     * @editor AndyCong 2013-04-16 加黑名单短信通知
     */
    public function afterFind()
    {
        $this->hash_phone = md5($this->phone);
        $this->mask_phone = substr_replace($this->phone, '****', 3, 4);
    }

    public function afterSave()
    {
        if ($this->getIsNewRecord()) {
            $isBlackCustomer = $this->checkBlackCustomer(trim($this->phone)); //校验是否为黑名单,是黑名单发警告短信 Modify BY AndyCong
            if (!$isBlackCustomer) {
                /**
                 * 检查订单用户是否VIP或者绑定了优惠券
                 * TODO：VIP用户直接充值
                 * 发送订单通知短信
                 */

                $isVip = VipService::service()->isVip($this->phone);
                if ($isVip) {
                    $vipInfo = VipService::service()->getVipInfo($this->phone);
                    $vipName = isset($vipInfo['name']) ? $vipInfo['name'] : 'vip';
                    $balance = isset($vipInfo['amount']) ? $vipInfo['amount'] : 0;
                    $vipType  = isset($vipInfo['type']) ? $vipInfo['type'] : '';
                    $vipCard = isset($vipInfo['vipid']) ? $vipInfo['vipid'] : 'vip';
                    switch ($vipType) {
                        case Vip::TYPE_CREDIT:
                            $message = sprintf('刚才呼入的（%s）为VIP客户：%s 卡号:%s 账户余额:%s，不足部分请收取现金。在您报单时,系统将自动从VIP账户划转代驾费到您账户，并短信通知客户扣款金额。', $this->phone, $vipName, $vipCard, $balance);
                            break;
                        case Vip::TYPE_FIXED:
                            $message = sprintf('刚才呼入的（%s）为预付费客户：%s 卡号:%s 账户余额:%s，不足部分请收取现金。在您报单时,系统将自动从客户账户划转代驾费到您账户，并短信通知客户扣款金额。', $this->phone, $vipName, $vipCard, $balance);
                            break;
                        case Vip::TYPE_COMPENSATE:
                            $message = sprintf('刚才呼入的（%s）为补偿客户：%s 卡号:%s 账户余额:%s，不足部分请收取现金。在您报单时,系统将自动从客户账户划转代驾费到您账户，并短信通知客户扣款金额。', $this->phone, $vipName, $vipCard, $balance);
                            break;
                        default:
                            $message = sprintf('刚才呼入的（%s）为补偿客户：%s 卡号:%s 账户余额:%s，不足部分请收取现金。在您报单时,系统将自动从客户账户划转代驾费到您账户，并短信通知客户扣款金额。', $this->phone, $vipName, $vipCard, $balance);
                            break;
                    }
                    Sms::SendSMS($this->driver_phone, $message, Sms::CHANNEL_SOAP);
                } else {
                    $bonus = BonusLibrary::model()->BonusOccupancy($this->phone, $this->order_id, $this->source);

                    if ($bonus) {
//                        $message = sprintf('刚才呼入的客户(%s)为优惠券客户,账户余额:%s元，不足部分请收取现金。报单时，系统将自动从客户账户划转%s元到您的信息费账户。', $this->phone, $bonus['money'], $bonus['money']);
                        $message = MessageText::getFormatContent(MessageText::DRIVER_CUSTOMER_BONUS, $this->phone, $bonus['balance']);
                        Sms::SendSMS($this->driver_phone, $message, Sms::CHANNEL_SOAP);

                        $message = sprintf('您的本次代驾服务可享受%s元优惠,感谢您选择e代驾,祝您愉快！', $bonus['balance']);
                        Sms::SendSMS($this->phone, $message, Sms::CHANNEL_SOAP);
                    }
                }
            } else {
                $message = "司机师傅你好，刚和你联系的客户（" . $this->phone . "）已被公司列入黑名单，请谨慎和他联系，如有疑问请联系客服。";
                Sms::SendSMS($this->driver_phone, $message);
            }
        }
        parent::afterSave();
    }

    public static function getOrderInfo($orderID)
    {
        $criteria = new CDbCriteria();
        $criteria->select = "order_id, phone, contact_phone, name, driver_id, vipcard, driver, city_id,
							FROM_UNIXTIME(booking_time, '%y-%m-%d %H:%i') as booking_time,
							FROM_UNIXTIME(start_time, '%y-%m-%d %H:%i') as start_time,
							FROM_UNIXTIME(end_time, '%y-%m-%d %H:%i') as end_time,
							location_start, location_end, distance, income, call_time";
        $criteria->compare('order_id', $orderID);
        $order = self::model()->find($criteria);
        return $order;
    }

    public function getOrderById($order_id)
    {
        $order = Order::getDbMasterConnection()->createCommand()
            ->select('*')
            ->from('t_order')
            ->where('order_id=:order_id', array(':order_id' => $order_id))
            ->queryRow();
        return $order;
    }


    public function getOrdersById($order_id)
    {
        $order = Order::getDbReadonlyConnection()->createCommand()
            ->select('*')
            ->from('t_order')
            ->where('order_id=:order_id', array(':order_id' => $order_id))
            ->queryRow();
        return $order;
    }

    /**
     *从归档表中查询订单
     *
     **/
    public function getOrderFromArchivesById($order_id, $num)
    {
        if ($num == 2) {
            $table = 't_order';
        } else if ($num == 3) {
            $table = 't_order_' . date('Y', time());
        } else if ($num == 4) {
            $table = 't_order_' . date('Y', strtotime('-1 years'));
        }
        $order = Order::getDbReadonlyConnection()->createCommand()
            ->select('*')
            ->from($table)
            ->where('order_id=:order_id', array(':order_id' => $order_id))
            ->queryRow();
        return $order;
    }

    /**
     *
     * 获取各城市指定时间段的订单状态统计
     */
    public function getGroupByStatus($begin_time, $end_time)
    {
        $sql = 'SELECT city_id, status, count( DISTINCT order_id ) sum,sum(case when driver_id="BJ00000" then 1 else 0 end) as push_failed_num
				FROM t_order
				WHERE booking_time
				BETWEEN :begin_time AND :end_time
				GROUP BY city_id, status;';

        return Order::getDbReadonlyConnection()->createCommand($sql)->queryAll(true, array(
            ':begin_time' => $begin_time,
            ':end_time' => $end_time));
    }

    /**
     * 某个时间段内接单的司机数量
     * @param <int> $begin_time     时间戳
     * @param <int> $end_time       时间戳
     * @return <int>
     * @author liuxiaobo
     * @since 2014-2-18
     */
    public function getCountForHasOrderDriver($begin_time, $end_time)
    {
        $command = Yii::app()->dborder_readonly->createCommand()
            ->select('COUNT(DISTINCT driver_id)')
            ->from(Order::model()->tableName())
            ->where('booking_time >= :begin_time AND booking_time < :end_time');
        $query = $command->queryScalar(array(':begin_time' => $begin_time, ':end_time' => $end_time));
        return $query;
    }

    public static function getAPPOrderCountByCustomerPhone($phone)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('phone', $phone);
        $criteria->compare('status', Order::ORDER_COMPLATE);
        $criteria->compare('source', Order::SOURCE_CLIENT);
        $orderCount = 0;
        $orderCount = self::model()->count($criteria);

        return $orderCount;
    }

    public static function getOrderCountByCustomerPhone($phone)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('phone', $phone);
        $criteria->compare('status', Order::ORDER_COMPLATE);
        $orderCount = 0;
        $orderCount = self::model()->count($criteria);

        return $orderCount;
    }

    public function getOrderCountByDriver($driver_id, $currentMonth)
    {
        $criteria = new CDbCriteria();
        $criteria->select = "COUNT(order_id) as order_id";

        $criteria->addCondition("driver_id=:driver_id AND FROM_UNIXTIME(booking_time,'%Y%m')=:booking_time");
        $criteria->params = array(
            ':driver_id' => $driver_id,
            ':booking_time' => $currentMonth);
        $criteria->addInCondition('status', array(
            Order::ORDER_COMPLATE,
            Order::ORDER_NOT_COMFIRM));
        $criteria->group = 'driver_id';

        $order = Order::model()->find($criteria);
        $orderCount = 0;
        if ($order) {
            $orderCount = $order->order_id;
        }
        return $orderCount;
    }

    /**
     * 根据客户手机号码获取该客户所有订单总额
     * @param <string> $phone
     * @return <int>
     */
    public function getOrdersAmountByCustomerPhone($phone)
    {
        $cache = Yii::app()->cache;
        $key = 'OrdersAmountByCustomerPhone_' . $phone;
        if ($cacheValue = $cache->get($key)) {
            return $cacheValue ? $cacheValue : 0;
        }
        $orderPrice = Yii::app()->dborder_readonly->createCommand()
            ->select('sum(price)')
            ->from(Order::model()->tableName())
            ->where('phone = :phone AND (status = ' . Order::ORDER_COMPLATE . ' OR status = ' . Order::ORDER_NOT_COMFIRM . ')', array(':phone' => $phone))
            ->queryScalar();
        $cache->set($key, $orderPrice, 60 * 10); //保存10分钟的有效期
        return $orderPrice ? $orderPrice : 0;
    }

    public function getSettleOrderCountByDriver($driver_id, $currentMonth)
    {
        $sql = 'SELECT order_count FROM t_driver_monthly_order WHERE user=:user AND current_month=:current_month';
        $command = Yii::app()->db->createCommand($sql);
        $command->bindParam(":user", $driver_id);
        $command->bindParam(":current_month", $currentMonth);
        $settleCount = $command->queryScalar();
        return $settleCount;
    }

    public function doSettleOrderCountByDriver($driver_id, $currentMonth, $orderCount)
    {
        $sql = 'INSERT INTO t_driver_monthly_order(user, current_month, order_count, created, operator) VALUES(:user, :current_month, :order_count, :created, :operator)';
        $command = Yii::app()->db->createCommand($sql);
        $created = time();
        $operator = Yii::app()->user->getId();
        $command->bindParam(":user", $driver_id);
        $command->bindParam(":current_month", $currentMonth);
        $command->bindParam(":order_count", $orderCount);
        $command->bindParam(":created", $created);
        $command->bindParam(":operator", $operator);
        $command->execute();
    }

    public static function getByCustomerPhoneID($phone, $orderID)
    {
        $criteria = new CDbCriteria();
        $criteria->select = "order_id, driver_id, vipcard, driver, FROM_UNIXTIME(booking_time, '%Y-%m-%d %H:%i') as booking_time,FROM_UNIXTIME(start_time, '%Y-%m-%d %H:%i') as start_time, FROM_UNIXTIME(end_time, '%Y-%m-%d %H:%i') as end_time, location_start, location_end, distance, income";
        $criteria->compare('phone', $phone);
        $criteria->compare('order_id', $orderID);

        $order = self::model()->find($criteria);

        return $order->attributes;
    }

    public static function getListByCustomerPhone($pageNo, $pageSize, $phone)
    {
        $criteria = new CDbCriteria();
        $criteria->select = "order_id, vipcard, FROM_UNIXTIME(booking_time, '%Y-%m-%d %H:%i') as booking_time, location_start, location_end, distance, income";
        $criteria->compare('phone', $phone);
        $criteria->addInCondition('status', array(
            Order::ORDER_COMPLATE,
            Order::ORDER_NOT_COMFIRM));
        $count = self::model()->count($criteria);
        $criteria->offset = $pageNo * $pageSize;
        $criteria->limit = $pageSize;
        $criteria->order = 'booking_time desc';
        $orders = self::model()->findAll($criteria);

        $ret = array();
        if ($orders) {
            foreach ($orders as $order) {
                $ret[] = array(
                    'order_id' => $order->order_id,
                    'vipcard' => $order->vipcard,
                    'booking_time' => $order->booking_time,
                    'location_start' => $order->location_start,
                    'location_end' => $order->location_end,
                    'distance' => $order->distance,
                    'income' => $order->income);
            }
            $ret['total'] = $count;
        }

        return $ret;
    }

    public function beforeSave()
    {

        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                //排除公司司机的电话号码
                $ret = Driver::getDriverByPhone($this->phone);
                if ($ret) {
                    $this->addError('phone', '此电话为司机备用电话');
                    return false;
                }

                //排除固定的电话号码
                if (CustomerWhiteList::model()->in_whitelist($this->phone)) {
                    $this->addError('phone', '此电话在白名单');
                    return false;
                }

                if (!$this->is_api) {
                    //每个客户/司机/天 相隔时间不超过2小时的通话记录只算一次;
                    //检查最近2小时是否有同一客户的订单（呼叫中心和直接呼叫）
                    $criteria = new CDbCriteria();

                    $criteria->condition = 'phone=:phone and imei=:imei';
                    $criteria->params = array(
                        ':phone' => $this->phone,
                        ':imei' => $this->imei);
                    $criteria->addBetweenCondition('created', $this->created - 7200, time());
                    $order = $this->find($criteria);

                    if (!$order) {
                        //再找有没有同一客户重复时间的订单
                        $order = $this->find('phone=:phone and imei=:imei and booking_time=:booking_time', array(
                            ':phone' => $this->phone,
                            ':imei' => $this->imei,
                            ':booking_time' => $this->booking_time));
                        if (!$order) {
                            $this->order_number = '';
                            $customer = CustomerService::service()->getCustomerInfo($this->phone,1);
                            if ($customer) {
                                $this->user_id = $customer->id;
                                $this->name = $customer->name;
                            } else {
                                $this->user_id = 0;
                                $this->name = '';
                            }
                            $this->vipcard = '';
                            $this->car_id = '';
                            $this->reach_time = '';
                            $this->reach_distance = '';
                            $this->start_time = 0;
                            $this->end_time = 0;
                            $this->distance = 0;
                            $this->charge = 0;
                            $this->location_start = '';
                            $this->location_end = '';
                            $this->income = 0;
                            $this->cast = 0;
                            $this->cancel_desc = '';
                            return true;
                        } else {
                            $this->addError('phone', '此电话是客户同时间电话');
                        }
                    } else {
                        //print_r($order->attributes);
                        $this->addError('phone', '此电话在最近两小时内有生成订单');
                    }
                    return false;
                } else {
                    return true;
                }
            } else {

                if (!$this->name || !$this->order_number || !$this->location_start || !$this->location_end || !$this->distance || !$this->income || !$this->start_time || !$this->end_time) {
                    return false;
                }

                //检查工单号是否已经使用
                $ret = $this->find('order_number=:order_number and order_id != :order_id', array(
                    ':order_number' => $this->order_number,
                    ':order_id' => $this->order_id));

                if ($ret) {
                    Yii::app()->clientScript->registerScript('alert', 'alert("此单号 ' . $this->order_number . ' 已经被使用，请检查。");');
                    return false;
                }

                if ($this->vipcard) {
                    //检查VIP卡号是否与电话号码匹配
                    $vip = VipService::service()->getVipInfo($this->phone);
                    if ($vip) {
                        if ($vip['vipid'] != $this->vipcard) {
                            Yii::app()->clientScript->registerScript('alert', 'alert("此电话号码与VIP卡号不匹配，请与客户核实。");');
                            return false;
                        }
                    } else {
                        Yii::app()->clientScript->registerScript('alert', 'alert("此号码 ' . $this->phone . ' 不是VIP。");');
                        return false;
                    }
                }
            }
            //检查订单是否已经录入了发票信息
            $invoice = new OrderInvoice();
            if ($this->invoice)
                $invoice->attributes = $this->invoice->attributes;

            if ($invoice->title || $invoice->telephone || $invoice->content || $invoice->contact || $invoice->address || $invoice->zipcode) {
                if ($invoice->title && $invoice->telephone && $invoice->content && $invoice->contact && $invoice->address && $invoice->zipcode) {
                    $criteria = new CDbCriteria();
                    $criteria->compare('order_id', $this->order_id);
                    $result = $invoice->find($criteria);
                    if (!$result) {
                        $invoice->order_id = $this->order_id;
                        $invoice->insert();
                    } else {
                        $invoice->updateByPk($this->order_id, $this->invoice->attributes);
                    }
                } else {
                    Yii::app()->clientScript->registerScript('alert', 'alert("请填写完整的发票信息。");');
                    return false;
                }
            }
            return true;
        }
    }

    /**
     *
     * 扣除每单的信息费用
     * 10点以前：5元.
     * 10点到11点：10元
     * 11点到12点：15元
     * 12点以后：20元
     * @param array $order
     */
    public function order_infomatin_fee($order)
    {
        $hour = date('H', $order['call_time']);
        $minutes = date('i', $order['call_time']);
        //换算为分钟后计算
        $min_sum = $hour * 60;
        $cast = 5;

        if ($min_sum >= 60 * 9 and $min_sum < 60 * 22) {
            $cast = 5;
        } elseif ($min_sum >= 60 * 22 and $min_sum < 60 * 23) {
            $cast = 10;
        } elseif ($min_sum >= 60 * 23 and $min_sum < 60 * 24) {
            $cast = 15;
        } elseif ($min_sum >= 1 and $min_sum < 60 * 9) {
            $cast = 20;
        }

        $account = new EmployeeAccount();
        $account->attributes = array(
            'type' => EmployeeAccount::TYPE_ORDER_INFOMATION,
            'user' => $order['driver_id'],
            'order_id' => $order['order_id'],
            'comment' => '信息费 单号：' . $order['order_number'],
            'cast' => $cast,
            'created' => $order['created']);
        $account->addAccount();
    }

    /**
     * 根据司机工号和状态返回订单数,这个方法是之前利民写的，方法名就沿用了。
     *
     * @author sunhongjing 2013-12-09
     * @param unknown_type $params 两个参数，
     */
    public function getDriverOrderCountWithStatus($driver_id, $status = 0)
    {
        $total = 0;
        if (empty($driver_id)) {
            return $total;
        }
        $status = empty($status) ? 0 : $status;
        //changed by aiguoxin


        $total=0;
        $total_num = Order::getDbMasterConnection()->createCommand()
                ->select('COUNT(order_id) AS `total_num`')
                ->from('t_order')
                ->where('driver_id=:driver_id and status=:status', array(
                    ':driver_id' => $driver_id, ':status' => $status))
                ->queryAll();
        if (!empty($total_num)) {
            $total = empty($total_num[0]['total_num']) ? 0 : $total_num[0]['total_num'];
        }
        return $total;
    }
    /**
     * 查询未报单的订单
     *
     * @author sunhongjing 2013-12-09
     * @param unknown_type $params 两个参数，
     */
    public function getDriverOrderCount($driver_id)
    {
        $total = 0;
        if (empty($driver_id)) {
            return $total;
        }



        $orderList = Order::getDbMasterConnection()->createCommand()
            ->select('order_id,order_number')
            ->from('t_order')
            ->where('driver_id=:driver_id and status=:status', array(
                ':driver_id' => $driver_id, ':status' => 0))
            ->queryAll();
        if (empty($orderList)){
            EdjLog::info('whytest5:driver_id='.$driver_id.',empty='.$total,'console');
            return $total;
        }
        $total = count($orderList);
        EdjLog::info('whytest6:driver_id='.$driver_id.',total='.$total,'console');
        foreach ($orderList as $list) {
            $submitRedisTag = '';
            if (empty($list['order_id'])) {
                continue;
            }
            $order_id = $list['order_id'];
            $submitRedisTag .= $order_id;
            if (RSubmitOrder::model()->getOrderId($order_id)) {
                $total = $total - 1;
                EdjLog::info('whytest7:driver_id='.$driver_id.',total='.$total,'console');
                continue;
            }
            if (empty($list['order_number'])) {
                $order_number = RSubmitOrder::model()->getOrderNumberByOrderId($list['order_id']);
            } else {
                $order_number = $list['order_number'];
            }
            if (!empty($order_number)) {
                $submitRedisTag .= $order_number;
                if (RSubmitOrder::model()->getOrderId($order_number)) {
                    $total = $total - 1;
                    continue;
                }
            }
            if (RSubmitOrder::model()->getOrderId($submitRedisTag)) {
                $total = $total - 1;
                EdjLog::info('whytest8:driver_id='.$driver_id.',total='.$total,'console');
            }
        }
        EdjLog::info('whytest9:driver_id='.$driver_id.',total='.$total,'console');
        return $total;
    }
    public function getDriverOrderListByType($params)
    {
        $orderLists = array();
        $orderList = Order::getDbReadonlyConnection()->createCommand()
            ->select("order_id, order_number, name, phone , contact_phone as customer_phone , source, booking_time , FROM_UNIXTIME(booking_time, '%Y-%m-%d %H:%i') as booking_date, location_start, cancel_desc, channel, city_id, cost_type")
            ->from('t_order')
            ->where('driver_id=:driver_id and status=:status', array(
                ':driver_id' => $params['driver_id'], ':status' => $params['status']))
            ->order('booking_time DESC')
            ->limit($params['pageSize'])
            ->offset($params['offset'])
            ->queryAll();
        foreach ($orderList as $list) {
            $favorable = $this->getOrderFavorable($list['phone'], $list['booking_time'], $list['source'], $list['order_id']);

            $list['phone'] = substr_replace($list['phone'], "****", 3, 4);
            $list['customer_phone'] = empty($list['customer_phone']) ? $list['phone'] : substr_replace($list['customer_phone'], "****", 3, 4);

            $list['favorable'] = $favorable['code'];
            if (empty($list['name'])) {
                if (empty($favorable['name'])) {
                    $customer = CustomerService::service()->getCustomerInfo($list['phone'],1);
                    if (!empty($customer)) {
                        $list['name'] = $customer->name;
                    } else {
                        $list['name'] = '';
                    }
                } else {
                    $list['name'] = $favorable['name'];
                }
            }
            //订单小费 补贴
            $orderExt = OrderExt::model()->getPrimary($list['order_id']);
            if ($orderExt) {
                $tips = FinanceCastHelper::getOrderFeeByExt($orderExt);
                $list['fee'] = $tips;
                $subsidyMoney = FinanceCastHelper::getSubsidy($list, $orderExt);
                $list['subsidy_money'] = $subsidyMoney;
                $list['isRemote'] = FinanceUtils::isRemoteOrder($list, $orderExt);
            } else {
                $list['fee'] = 0.00;
                $list['subsidy_money'] = 0.00;
                $list['isRemote'] = 0;
            }
            $total = FinanceCastHelper::getOrderTotalMoney($list, $orderExt, array(), true);//司机的总价需要增加修饰金额
            $list['fee_total'] = $total;
            $list['income'] = $total;
            $orderLists[] = $list;
        }

        return $orderLists;
    }

    public function getOngoingOrder($driver_id, $sources = null)
    {
        if (is_null($sources)) {
            return $this->find(array(
                'condition' => 'driver_id = :driver_id and status = 0',
                'params' => array(':driver_id' => $driver_id),
                'order' => 'booking_time DESC',
                'limit' => 1
            ));
        }

        if (is_array($sources)) {
            return $this->find(array(
                'condition' => 'driver_id = :driver_id and source in (:sources) and status = 0',
                'params' => array(':driver_id' => $driver_id, ':sources' => implode(',', $sources)),
                'order' => 'booking_time DESC',
                'limit' => 1
            ));
        }

        return $this->find(array(
            'condition' => 'driver_id = :driver_id and source = :sources and status = 0',
            'params' => array(':driver_id' => $driver_id, ':sources' => $sources),
            'order' => 'booking_time DESC',
            'limit' => 1
        ));
    }

    /**
     * 获取该订单的优惠信息
     * @param $phone
     * @param $booking_time
     * @param int $source
     * @param int $order_id
     * @return array
     * 参数code  0.普通用户  1.VIP用户   2.优惠劵用户  4.既有优惠劵，个人帐户里面又有钱   8.个人帐户有钱
     * @auther mengtianxue
     */
    public function getOrderFavorable($phone, $booking_time, $source = 1, $order_id = 0)
    {
        $favorable = array('code' => 0, 'card' => '', 'name' => '', 'money' => 0, 'user_money' => 0); //普通用户

        //增加手机号的验证,add by sunhongjing 2013-09-07
        $phone = trim($phone);
        if (empty($phone)) {
            return $favorable;
        }

        if (in_array($source, Order::$washcar_sources)) {
            return $favorable;
        }

        //如果有vip先使用vip，如果没有vip看是否又优惠劵  --孟天学
        $isVip = VipService::service()->isVip($phone);
        if ($isVip) {
            $vip = VipService::service()->getVipInfo($phone);
            $favorable['code'] = 1; //VIP用户
            $favorable['card'] = $vip['vipid'];
            $favorable['name'] = $vip['name'];
            $balance = isset($vip['amount']) ? $vip['amount'] : 0;
            if ($vip) {
                if ($balance > 0) {
                    $favorable['money'] =$balance;
                } else {
                    $favorable['money'] = 0;
                }
            } else {
                $favorable['money'] = 0;
            }
        } else {
            $bonus = CustomerBonus::model()->getBonusUsed($phone, $order_id);
            if ($bonus) {
                $status = true;
                if (!empty($source) && $source == Order::SOURCE_CLIENT) {
                    //app呼叫  有呼入记录 返回true 否则false
                    $status = CallHistory::model()->getIncomeCallHistory($phone, $booking_time);
                }

                if ($status) {
                    $favorable['code'] = 2; //优惠劵用户

                    if ($bonus['count'] == 1) {
                        $card = $bonus['bonus_sn'];
                    } else {
                        $card = '有' . $bonus['count'] . '张优惠劵，共优惠' . $bonus['balance'] . '元';
                    }

                    $favorable['card'] = $card;
                    $favorable['name'] = '';
                    $favorable['money'] = $bonus['balance'];
                }
            }

            //个人帐户金额
            $user_info = NormalAccountService::getUserAmount($phone);

            if ($user_info['amount'] > 0) {
                $favorable['user_money'] = $user_info['amount'];
                if ($favorable['code'] == 2) {
                    $favorable['code'] = 4; //既有优惠劵，个人帐户里面又有钱
                } else {
                    $favorable['code'] = 8; //个人帐户里面有钱
                }
            }
        }
        return $favorable;
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search($criteria)
    {

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

    //报单信息
    public function report($orderModel, $order, $type = FALSE)
    {
        $back = FALSE;
        if ($orderModel && $order) {
            //当前order_id
            $id = $orderModel->order_id;

            //等候时间
            $wait_time = $order['wait_time'];
            unset($order['wait_time']);

            //记录客户姓名
            $customer = CustomerService::service()->getCustomerInfo($orderModel->phone,1);
            if (empty($customer)) {
                $attr = array(
                    'name' => $order['name'],
                    'city_id' => $order['city_id'],
                    'phone' => $orderModel->phone,
                    'create_time' => date('Y-m-d h:i:s', time()));
                $customer = new CustomerMain();
                $customer->attributes = $attr;
                $customer->insert();
                $orderModel->user_id = $customer->id;
            }
            $order['user_id'] = $orderModel->user_id;
            $order['status'] = Order::ORDER_COMPLATE;
            $order['cast'] = 0;
            //记录车排号
            $car_id = CustomerCar::model()->getCustomerCar($order['car_number'], $orderModel->user_id);

            if (!$car_id) {
                $car_attr = array(
                    'user_id' => $orderModel->user_id,
                    'number' => strtoupper($order['car_number']),
                    'create_time' => date('Y-m-d H:i:s')
                );
                $car = new CustomerCar();
                $car->attributes = $car_attr;
                $car->insert();
                $car_id = $car->car_id;
            }
            $order['car_id'] = $car_id;

//				if($order['city_id'] == 1 && $total_money > 39){
//					$order['cast'] = round($total_money * 0.2);
//					$order['cast'] = Common::cast($model->booking_time, $total_money, $order['distance']);
//				}else{
//					$order['cast'] = 0;
//				}

            $mark = $order['log'] . "[手动]";
            unset($order['bonus_code']);
            unset($order['log']);
            unset($order['car_number']);

            $orderModel->attributes = $order;

            if ($orderModel->updateByPk($id, $orderModel->attributes)) {

                //记录日志
                $description = '报单';
                if ($type)
                    $operator = $orderModel->driver_id;
                else
                    $operator = Yii::app()->user->getId();
                $log = new OrderLog();
                $log_attr = $log->attributes;
                $log_attr['order_id'] = $id;
                $log_attr['description'] = $description;
                $log_attr['operator'] = strtoupper($operator);
                $log_attr['created'] = time();

                $log->attributes = $log_attr;
                $log->insert();

//					if (isset($_POST['isComplaint'])&&isset($_POST['isComplaint'])) {
//						//保存投诉信息
//						$tmp_model = $model->attributes;
//						$complaintArr=array();
//						$complaintArr['order_id'] = $id;
//						$complaintArr['driver_user'] = $tmp_model['driver_id'];
//						$complaintArr['customer_name'] = $tmp_model['name'];
//						$complaintArr['city'] = $tmp_model['city_id'];
//						$complaintArr['customer_phone'] = $tmp_model['phone'];
//						$complaintArr['order_type'] = 1;//1为报单
//						$complaintArr['complaint_type'] = $_POST['status'];
//						$complaintArr['complaint_content'] = $_POST['complaint'];
//						$complaintArr['driver_time'] = $tmp_model['start_time'];//只记录出发时间
//						$complaintArr['complaint_status'] = 0;
//						DriverComplaint::model()->saveDriverComplaint($complaintArr);
//						unset($complaintArr);
//						//---end
//					}

                $modelExt = OrderExt::model()->find('order_id = :order_id', array(':order_id' => $id));
                $OrderExt = array();
                $OrderExt['order_id'] = $id;
                $OrderExt['wait_time'] = $wait_time;
                $OrderExt['created'] = date('Y-m-d H:i:s');
                $OrderExt['mark'] = $mark;

                //添加实物劵金额  --mengtianxue 修改时间：2014-03-20
                //兼容原来有过报单行为的,原来的实体卡给予保留
                if (isset($order['coupon_money']) && !empty($order['coupon_money'])) {
                    $OrderExt['coupon_money'] = $order['coupon_money'];
                }
//                $OrderExt['invoiced'] = isset($order['invoiced']) ? intval($order['invoiced']) : 0;
                //$OrderExt['coupon'] = $order['coupon'];

                $driver_ready_time = isset($params['ready_time']) ? intval($params['ready_time']) : 0;
                $driver_ready_distance = isset($params['ready_distance']) ? floatval($params['ready_distance']) : 0.00;
                $driver_subsidy_money = 0.00;
                $customer_subsidy_money = 0.00;

                //上报 ready_time 或 ready_distance存在异常 不参与补贴活动
                if ($driver_ready_time != 0 || ($driver_ready_distance != 0.00 && $driver_ready_distance != 0)) {

                    if ($this->checkActivityOrder($orderModel->source, $orderModel->channel)) {
                        $subsidy = Common::orderSubsidy($orderModel->city_id, $driver_ready_time, $driver_ready_distance, $order['income']);

                        $driver_subsidy_money = $subsidy['driver_subsidy_money'];
                        $customer_subsidy_money = $subsidy['customer_subsidy_money'];

                        //if($driver_subsidy_money > 0) {   //司机提前到达
                        //    $m = intval($driver_ready_time/60);
                        //    $s = intval($driver_ready_time%60);
                        //    $phone = $orderModel->phone;
                        //    $content = $orderModel->driver_id."师傅已在".$m."分".$s."秒内到达，祝您愉快，如司机到达时间不实，请联系我们，客服4006913939。";
                        //    Sms::SendSMS($phone , $content);
                        //}

                        //if($customer_subsidy_money > 0) { //客户免单
                        //    $phone = $orderModel->phone;
                        //    $content = $orderModel->driver_id."师傅达到时间超过9分45秒，此单免单。若师傅收取了您的现金，请联系我们。若您为vip或有余额用户，钱款会稍后退回。客服4006913939。";
                        //    Sms::SendSMS($phone , $content);
                        //}
                    }

                }

                if (empty($modelExt)) {
                    $modelExt = new OrderExt();
                    $modelExt->attributes = $OrderExt;
                    $modelExt->invoiced = isset($order['invoiced']) ? intval($order['invoiced']) : 0;

                    //增加活动补贴字段 BY AndyCong 2014-04-03
                    $modelExt->driver_ready_time = $driver_ready_time;
                    $modelExt->driver_ready_distance = $driver_ready_distance;
                    $modelExt->driver_subsidy_money = $driver_subsidy_money;
                    $modelExt->customer_subsidy_money = $customer_subsidy_money;

                    if (!$modelExt->insert()) {
                        EdjLog::info('order::report save order_ext error|' . json_encode($modelExt->getErrors()));
                    }
                } else {
                    $modelExt->attributes = $OrderExt;
                    EdjLog::info('whytest=invoiced update  before1:' . $order['invoiced'], 'console');
                    $modelExt->invoiced = isset($order['invoiced']) ? intval($order['invoiced']) : 0;

                    //增加活动补贴字段 BY AndyCong 2014-04-03
                    $modelExt->driver_ready_time = $driver_ready_time;
                    $modelExt->driver_ready_distance = $driver_ready_distance;
                    $modelExt->driver_subsidy_money = $driver_subsidy_money;
                    $modelExt->customer_subsidy_money = $customer_subsidy_money;
                    EdjLog::info('whytest=invoiced update  before0:' . $modelExt->invoiced, 'console');
                    if (!$modelExt->update()) {
                        EdjLog::info('order::report update order_ext error|' . json_encode($modelExt->getErrors()));
                    }
                }
            }
            $subsidyHour = isset($order['subsidy_hour']) ? $order['subsidy_hour'] : 0;//日间单2.5.4版本为1去掉每小时补贴10元
            SettleService::settle($id, $orderModel, $order, $subsidyHour);
            try {
                RemoteDispatchBonusActivity::model()->checkBonus($orderModel);
            } catch (Exception $e) {
                EdjLog::warning('check RemoteDispatchBonusActivity bonus failed, message:' . $e->getMessage(), 'console');
            }

            $back = TRUE;
        }
        return $back;
    }

    /**
     *  校验是否可以生成订单(日间模式,一口价)
     * @param source int 订单来源
     * @param city   int 下单城市
     * @param calltime unixtime 下单时间
     * @param channel 订单类型(可选)
     */
    public static function CheckSpecialOrderSource($source, $city, $calltime, $channel = '')
    {
        if ($source == self::SOURCE_DAYTIME_CLIENT
            || $source == self::SOURCE_DAYTIME_CLIENT_INPUT
            || $source == self::SOURCE_DAYTIME_CALLCENTER
            || $source == self::SOURCE_DAYTIME_CALLCENTER_INPUT
        ) {


            $daytime_price = Common::getPriceListByCity($city);
            $daytime_type = CityConfigService::dayTimeStatus($city);
            if (!$daytime_type && !isset($daytime_price[$daytime_type])) {
                return array('flag' => false,
                    'code' => ApiErrorCode::ORDER_CITY_ERROR);
            }

            $detail_price = $daytime_price[$daytime_type];
            $start_time = strtotime(date('Y-m-d', $calltime) . ' ' . $detail_price['start_time']);
            $end_time = strtotime(date('Y-m-d', $calltime) . ' ' . $detail_price['end_time']);
            if ($calltime < $start_time || $calltime > $end_time) {
                return array('flag' => false,
                    'code' => ApiErrorCode::ORDER_TIME_ERROR);
            }

            return array('flag' => true, 'code' => 0);
        } elseif ($source == self::SOURCE_WASHCAR_CLIENT) {
            //一口价 代驾洗车业务
            $fixed_type = CityConfigService::washCarStatus($city);
            if (!$fixed_type) {
                return array('flag' => false,
                    'code' => ApiErrorCode::ORDER_CITY_ERROR);
            }

            if (!empty($channel) &&
                $channel == CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER
            ) {
                return array('flag' => false,
                    'code' => ApiErrorCode::ORDER_CHANNEL_ERROR);
            }

            return array('flag' => true, 'code' => 0);
        }

        //never be here
        return array('flag' => false, 'code' => 2);
    }

    /**
     *  v2后台订单来源说明
     */
    public static function SourceToString($source, $want_array = false)
    {
        $sarray = array(
            '' => '全部',

            self::SOURCE_CLIENT => '客户呼叫',
            self::SOURCE_CALLCENTER => '呼叫中心',
            self::SOURCE_CLIENT_INPUT => '客户呼叫补单',
            self::SOURCE_CALLCENTER_INPUT => '呼叫中心补单',

            self::SOURCE_DAYTIME_CLIENT => '客户呼叫(日间)',
            self::SOURCE_DAYTIME_CALLCENTER => '呼叫中心(日间)',
            self::SOURCE_DAYTIME_CLIENT_INPUT => '客户呼叫补单(日间)',
            self::SOURCE_DAYTIME_CALLCENTER_INPUT => '呼叫中心补单(日间)',

            self::SOURCE_WASHCAR_CLIENT => '客户呼叫(洗车)',
            self::SOURCE_WASHCAR_CALLCENTER => '呼叫中心(洗车)',
            self::SOURCE_WASHCAR_CLIENT_INPUT => '客户呼叫补单(洗车)',
            self::SOURCE_WASHCAR_CALLCENTER_INPUT => '呼叫中心补单(洗车)',

            self::SOURCE_WEIXIN => '微信'
        );

        if ($want_array) {
            return $sarray;
        } else {
            return isset($sarray[$source])
                ? $sarray[$source] : '';
        }
    }

    /**
     *  订单Description说明
     */
    public static function SourceToDescription($source)
    {
        $sarray = array(

            self::SOURCE_CLIENT => '直呼APP',
            self::SOURCE_CALLCENTER => '呼叫中心',
            self::SOURCE_CLIENT_INPUT => '客户端补单',
            self::SOURCE_CALLCENTER_INPUT => '客户端补单',

            self::SOURCE_DAYTIME_CLIENT => '直呼APP',
            self::SOURCE_DAYTIME_CALLCENTER => '呼叫中心',
            self::SOURCE_DAYTIME_CLIENT_INPUT => '客户端补单',
            self::SOURCE_DAYTIME_CALLCENTER_INPUT => '客户端补单',

            self::SOURCE_WASHCAR_CLIENT => '直呼APP',
            self::SOURCE_WASHCAR_CALLCENTER => '呼叫中心',
            self::SOURCE_WASHCAR_CLIENT_INPUT => '客户端补单',
            self::SOURCE_WASHCAR_CALLCENTER_INPUT => '客户端补单',
        );

        return isset($sarray[$source]) ? $sarray[$source] : '直呼APP';
    }

    /**
     *  订单source司机端历史订单对应文案
     */
    public static function SourceToDriverSubmitListString($source)
    {
        $sarray = array(

            self::SOURCE_CLIENT => '客户直接呼叫',
            self::SOURCE_CALLCENTER => '呼叫中心派单',
            self::SOURCE_CLIENT_INPUT => '客户直接呼叫补单',
            self::SOURCE_CALLCENTER_INPUT => '呼叫中心派单补单',

            self::SOURCE_DAYTIME_CLIENT => '客户直接呼叫',
            self::SOURCE_DAYTIME_CALLCENTER => '呼叫中心派单',
            self::SOURCE_DAYTIME_CLIENT_INPUT => '客户直接呼叫补单',
            self::SOURCE_DAYTIME_CALLCENTER_INPUT => '呼叫中心派单补单',

            self::SOURCE_WASHCAR_CLIENT => '客户直接呼叫',
            self::SOURCE_WASHCAR_CALLCENTER => '呼叫中心派单',
            self::SOURCE_WASHCAR_CLIENT_INPUT => '客户直接呼叫补单',
            self::SOURCE_WASHCAR_CALLCENTER_INPUT => '呼叫中心派单补单',
        );

        return isset($sarray[$source]) ? $sarray[$source] : '客户直接呼叫';
    }

    public function getOrderInfomation($order_id, $driver_id)
    {
        return Order::model()->find('order_id=:order_id and driver_id=:dirver_id', array(
            ':order_id' => $order_id,
            ':dirver_id' => $driver_id));
    }

    public function getOrderByOrderNumber($order_number, $driver_id)
    {
        return Order::model()->find('order_number=:order_number and driver_id=:dirver_id', array(
            ':order_number' => $order_number,
            ':dirver_id' => $driver_id));
    }

    //add by aiguoxin
    public function getOrderByOrderNumberOnly($order_number)
    {
        return Order::model()->find('order_number=:order_number', array(
            ':order_number' => $order_number));
    }


    /**
     * 校验客户手机号是否为黑名单
     *
     * @param string $phone
     * @return boolean
     * @author AndyCong<congming@edaijia.cn> 2013-04-16
     */
    public function checkBlackCustomer($phone = null)
    {
        if ($phone == null) {
            return false;
        }
        $sql = "SELECT COUNT(id) AS cnt FROM t_customer_blacklist WHERE phone=:phone";
        $command = Yii::app()->db_readonly->createCommand($sql);
        $command->bindParam(":phone", $phone);
        $result = $command->queryRow();
        if ($result['cnt'] > 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 订单处理
     * @param $params
     */
    public function order_operate($params)
    {

        //强制更新msg LOG  状态为己接收
        MessageLog::model()->updateByPk($params['push_msg_id'], array('flag' => 3));

        $queue = OrderQueue::model()->find('id=:id and flag=:flag ', array(':id' => $params['queue_id'], ':flag' => OrderQueue::QUEUE_WAIT_COMFIRM));

        if ($queue) {

            $where = array(
                'phone' => strval($queue->phone),
                'booking_time' => strtotime($queue->booking_time),
                'driver_id' => $params['driver_id'],
            );
            //当前己成订单数
            $order_count = Order::model()->count('phone=:phone  and booking_time =:booking_time and driver_id <> :driver_id ', $where);
            if ($order_count > $queue->number) {
                return false;
            }
            unset($where);
            //当前己建立订单关系数
            $order_queue_count = OrderQueueMap::model()->count('queue_id =:queue_id', array(":queue_id" => $queue->id));
            if ($order_queue_count > $queue->number) {
                return false;
            }


            //建立订单
            $order_arr = array(
                'queue_id' => $queue->id,
                'driver_id' => $params['driver_id'],
                'name' => $queue->name,
                'phone' => $queue->phone,
                'address' => $queue->address,
                'booking_time' => $queue->booking_time,
                'city_id' => $queue->city_id,
                'type' => $queue->type,
                'created' => $queue->created,
            );

            $order = $this->setGenOrder($order_arr);

            if ($order['code'] == 1) {
                return false;
            }
            unset($order_arr);

            //建立关系
            $order_queue_relations = $this->setOrderQueueRelations(
                $order['order_id'],
                $params['queue_id'],
                $params['driver_id'],
                $params['confirm_time']);

            if ($order_queue_relations['code'] == 1) {
                return false;
            }

            //设置发送消息
            $msg = $this->setPushOrderMsg($params['driver_id'], $params['queue_id']);

            if ($msg['code'] == 1) {
                return false;
            }
            //推送消息
            $message_arr = array(
                'type' => IGtPush::TYPE_ORDER_DETAIL,
                'content' => $msg['msg'],
                'level' => 3, //级别
                'driver_id' => $params['driver_id'],
                'queue_id' => $params['queue_id'],
                'order_id' => $order['order_id'],
                'created' => date('Y-m-d H:i:s', time()),
            );

            //发送失败记录请求次数
            $key = trim($params['queue_id']) . "_" . trim($params['driver_id']);
            DriverStatus::model()->orderCount($key, true);
            $push_message_flag = PushMessage::model()->organize_message_push($message_arr);
            $count = DriverStatus::model()->orderCount($key);
            if ($push_message_flag || $count >= Yii::app()->params['GETUI_PUSH_NUM']) {
                //更新派单时间 BY AndyCong<congming@edaijia.cn> 2013-05-21
                OrderQueue::model()->updateByPk($queue->id, array('update_time' => date('Y-m-d H:i:s', time())));
                //更新派单时间 BY AndyCong<congming@edaijia.cn> 2013-05-21 END
                //获取当前己派送的司机总数
                $count = OrderQueueMap::model()->count('queue_id =:queue_id', array(":queue_id" => $queue->id));
                if ($queue->number == $count) {
                    if ($queue->number > 1) {
                        //发送组员信息
                        $leader = self::sendLeaderSmsGroupMemberMsg($queue->id);
                        if (!empty($leader)) {
                            //推送消息
                            $message = array(
                                'type' => IGtPush::TYPE_MSG_LEADER,
                                'content' => $leader['msg'],
                                'level' => 3, //级别
                                'driver_id' => $leader['driver_id'],
                                'queue_id' => $queue->id,
                                'created' => date('Y-m-d H:i:s', time()),
                            );
                            QueueProcess::push_message($message);
                        }
                    }
                    //派送完成后
                    //获取备注信息
                    $queue_arr = array(
                        'queue_id' => $queue->id,
                        'comments' => $queue->comments,
                    );
                    //获取备注信息 END

                    $comments = $this->getQueueComments($queue_arr);
                    return OrderQueue::model()->updateByPk($queue->id, array('flag' => OrderQueue::QUEUE_SUCCESS, 'dispatch_agent' => '自动派单', 'comments' => $comments, 'dispatch_time' => date('Y-m-d H:i:s', time())));

                }
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 通过QUEUE ID 生成订单
     * @author zhanglimin 2013-05-06
     * @param $queue_id
     * @param $driver_id
     * @return array|mixed|null
     */
    public function setGenOrder($order_arr = array())
    {

        $client_name = isset($order_arr['name']) ? $order_arr['name'] : '';
        $client_phone = isset($order_arr['phone']) ? $order_arr['phone'] : '';
        $client_address = isset($order_arr['address']) ? $order_arr['address'] : '';
        $client_time = isset($order_arr['booking_time']) ? $order_arr['booking_time'] : '';
        $city_id = isset($order_arr['city_id']) ? $order_arr['city_id'] : '';
        $source = isset($order_arr['type']) ? $order_arr['type'] : '';
        $driver_id = isset($order_arr['driver_id']) ? $order_arr['driver_id'] : '';
        $created = isset($order_arr['created']) ? $order_arr['created'] : '';

        if (empty($client_name) || empty($client_phone) || empty($client_address) || empty($client_time)
            || empty($city_id) || empty($driver_id) || empty($created)
        ) {

            $ret = array('code' => 1,);
            return $ret;
        }

        $params = array(
            ':phone' => strval($client_phone),
            ':source' => $source,
            ':driver_id' => $driver_id,
            ':booking_time' => strtotime($client_time),
            // ':booking_time'=>strtotime($client_time)-300,
            // ':end_booking_time'=>strtotime($client_time)+300
        );

        //$ret = Order::model()->find('phone=:phone and source=:source and driver_id=:driver_id and booking_time >=:booking_time and booking_time<=:end_booking_time', $params);
        $ret = Order::model()->find('phone=:phone and source=:source and driver_id=:driver_id and booking_time =:booking_time ', $params);

        if (!$ret) {
            $call_time = strtotime($created);
            $driver_detail = DriverStatus::model()->get($driver_id);
            if (!empty($driver_detail)) {
                $employee_name = $driver_detail->info['name'];
                $imei = $driver_detail->info['imei'];
                $employee_phone = strval($driver_detail->phone);
            } else {
                $employee_name = $imei = $employee_phone = "";
            }

            $source_text = Order::SourceToDescription($source);

            $sql = 'insert into t_order (name,phone,source,driver,city_id,driver_id,driver_phone,
							imei,call_time,order_date,booking_time,location_start,description,created)
							values ("%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s",%s)';
            $sql = sprintf($sql, $client_name, $client_phone, $source, $employee_name, $city_id, $driver_id, $employee_phone, $imei, $call_time, date('Ymd', time()), strtotime($client_time), $client_address, $source_text, time());

            Order::getDbMasterConnection()->createCommand($sql)->execute();

            $order_id = Order::getDbMasterConnection()->getLastInsertID(); //获取最新插入的order_id

            if (!empty($order_id)) {

                //日志
                $log_arr = array(
                    'order_id' => $order_id,
                    'operator' => $driver_id,
                );
                OrderLog::model()->insertLog($log_arr);

                $ret = array(
                    'code' => 0,
                    'order_id' => $order_id,
                );
                return $ret;
            } else {
                $ret = array('code' => 1,);
                return $ret;
            }
        } else {
            $ret = array('code' => 0, 'order_id' => $ret->order_id,);
            return $ret;
        }
    }


    /**
     * 建立订单与队列的关系
     * @author zhanglimin 2013-05-06
     * @param string $order_id
     * @param string $queue_id
     * @param string $driver_id
     * @param string $confirm_time
     * @return array
     */
    public function setOrderQueueRelations($order_id = '', $queue_id = '', $driver_id = '', $confirm_time = '')
    {
        if (empty($order_id) || empty($queue_id) || empty($driver_id) || empty($confirm_time)) {
            $ret = array(
                'code' => 1,
            );
            return $ret;
        }
        $params = array(
            'order_id' => $order_id,
            'queue_id' => $queue_id,
            'driver_id' => $driver_id,
        );

        $ret = OrderQueueMap::model()->find('order_id=:order_id and queue_id=:queue_id and driver_id=:driver_id', $params);
        if (empty($ret)) {
            $model = new OrderQueueMap();
            $attributes = array(
                'order_id' => $order_id,
                'queue_id' => $queue_id,
                'driver_id' => $driver_id,
                'confirm_time' => $confirm_time,
            );
            $model->attributes = $attributes;
            $result = $model->save();
            if ($result) {
                //直接更新分配司机数量 BY AndyCong<congming@edaijia.cn>2013-05-22
                Order::model()->updateDispatchNumber($queue_id);
                //直接更新分配司机数量 BY AndyCong<congming@edaijia.cn>2013-05-22 END

                //获取司机信息写入缓存
//            	OrderQueue::model()->setDispatchDriverCache($queue_id , $driver_id);
                //获取司机信息写入缓存 END
                $ret = array(
                    'code' => 0,
                );
                return $ret;
            } else {
                $ret = array(
                    'code' => 1,
                );
                return $ret;
            }
        } else {
            $ret = array(
                'code' => 0,
            );
            return $ret;
        }
    }

    /**
     * 设置发送信息
     * @param string $driver_id
     * @param string $queue_id
     * @return array
     * @author zhanglimin<zhangliming@edaijia.cn>
     *         2013-05-06
     * @editor AndyCong<congming@edaijia.cn>
     *         2013-05-11
     */
    public function setPushOrderMsg($driver_id = "", $queue_id = "")
    {
        if (empty($driver_id) || empty($queue_id)) {
            return array('code' => 1);
        }
        $queue = OrderQueue::model()->find(' id=:id', array(':id' => $queue_id));
        if (empty($queue)) {
            return array('code' => 1);
        }
        $data = array(
            'address' => $queue->address,
            'customer_name' => $queue->name,
            'phone' => '',
            'contact_phone' => '',
            'booking_time' => $queue->booking_time,
            'number' => $queue->number,
            'vipcard' => '',
            'role' => '',
            'leader_phone' => '',
            'bonus' => '',
        );
        $vip = '';
        if ($queue->contact_phone && $queue->contact_phone != $queue->phone) {
            $data['phone'] = substr($queue->phone, 0, 3) . "****" . substr($queue->phone, -4);
            $data['contact_phone'] = $queue->contact_phone;
        } else {
            $data['phone'] = $queue->phone;
        }
        if ($queue->number > 1) {
            //预约多人
            $leader = self::checkGroupLeader($queue->id);
            if (empty($leader)) {
                return array('code' => 1);
            }
            //TODO 以下这块代码到时候要封装优化下，ClientController 那块也有这么一陀
            $vip = VipService::service()->getVipInfo($queue->phone);
            if ($vip) {
                $balance = isset($vip['amount']) ? $vip['amount'] : 0;
                $vipCard = $vip['vipid'];
                $data['vipcard'] = '卡号：' . $vipCard . '，余额：' . $balance . '元,不足部分请收取现金';
            } else {
//                $customerBonus=CustomerBonus::existsUnusedCustomerBonus($queue->phone);
                $customerBonus = array();
                if ($customerBonus) {
                    if ($customerBonus['is_limited'] != BonusType::BONUS_TYPE_LIMIT_APP && $customerBonus['is_limited'] != BonusType::BONUS_TYPE_LIMIT_APP_FRESH) {
                        $data['bonus'] = ' 优惠金额：' . $customerBonus['money'] . '元';
                    }
                }
            }
            if ($leader == $driver_id) {
                $data['role'] = '组长';
                $data['leader_phone'] = '';
            } else {
                $data['role'] = '组员';
                //获取组长的姓名与手机
                $leaderInfo = DriverStatus::model()->get($leader);
                $data['leader_phone'] = $leaderInfo->phone;
            }
        } else {
            //预约单人
            $isVip = VipService::service()->isVip($queue->phone);
            if ($isVip) {
                $vip = VipService::service()->getVipInfo($queue->phone);
                if ($vip) {
                    $balance = isset($vip['amount']) ? $vip['amount'] : 0;
                    $vipCard = $vip['vipid'];
                    $data['vipcard'] = '卡号：' . $vipCard . '，余额：' . $balance . '元,不足部分请收取现金';
                } else {
                    $data['vipcard'] = 'VIP卡欠费禁用，请收取现金';
                }
            } else {
//                $customerBonus = CustomerBonus::existsUnusedCustomerBonus($queue->phone);
                $customerBonus = array();
                if ($customerBonus) {
                    if ($customerBonus['is_limited'] != BonusType::BONUS_TYPE_LIMIT_APP && $customerBonus['is_limited'] != BonusType::BONUS_TYPE_LIMIT_APP_FRESH) {
                        $data['bonus'] = ' 优惠金额：' . $customerBonus['money'] . '元';
                    }
                }
            }
        }
        return array(
            'code' => 0,
            'msg' => $data,
        );

    }

    /**
     * 设置短信内容
     * @param string $driver_id
     * @param string $queue_id
     * @return array
     */
    public function setMsgSms($driver_id = "", $queue_id = "")
    {
        if (empty($driver_id) || empty($queue_id)) {
            return array('code' => 1);
        }
        $queue = OrderQueue::model()->find(' id=:id', array(':id' => $queue_id));
        if (empty($queue)) {
            return array('code' => 1);
        }
        $client_main_phone = $client_phone = $queue->phone;
        $contact_phone = $queue->contact_phone;
        $client_address = $queue->address;
        $client_name = $queue->name;
        $client_time = $queue->booking_time;
        $current_cart = $queue->number;
        $contact_content = '';
        $bonusInfo = "";
        $vipcard = "";
        $vip = "";
        if ($queue->number > 1) {
            //预约多人
            $leader = self::checkGroupLeader($queue->id);
            if (empty($leader)) {
                return array('code' => 1);
            }
            //TODO 以下这块代码到时候要封装优化下，ClientController 那块也有这么一陀

            $leader_message = '%s(%s%s)预约%s名代驾,%s地址:%s,时间:%s,您为组长,另%s名师傅会联系您。';
            $driver_message = '%s预约%s名代驾,地址:%s,时间:%s,请联系组长%s师傅(%s)。';
            $leader_vip_message = '%s(%s,%s)预约%s名代驾,%s地址:%s,时间:%s,您为组长,另%s名师傅会联系您。';
            $driver_vip_message = '%s(%s)预约%s名代驾,地址:%s,时间:%s,请联系组长%s师傅(%s)。';

            if (!empty($contact_phone) && ($client_phone != $contact_phone)) {
                $contact_content = '联系人:' . $contact_phone . ',';
                $client_main_phone = substr_replace($client_phone, "*****", 3, 5);
            }

            $vip = VipService::service()->getVipInfo($queue->phone);
            if ($vip) {
                $balance = isset($vip['amount']) ? $vip['amount'] : 0;
                $vipCard = $vip['vipid'];
                $data['vipcard'] = '卡号：' . $vipCard . '，余额：' . $balance . '元,不足部分请收取现金';
            } else {
                $customerBonus = CustomerBonus::getCustomerBounsAmount($client_phone, $queue->type);
                if ($customerBonus > 0) {
                    $bonusInfo = ' 优惠金额：' . $customerBonus . '元';
                }
            }

            if ($leader == $driver_id) {
                //是组长 联系客户
                if ($vip) {
                    $msg = sprintf($leader_vip_message, $client_name, $client_main_phone, $vipcard, $current_cart, $contact_content, $client_address, date('m-d H:i', strtotime($client_time)), $current_cart - 1);
                } else {
                    $msg = sprintf($leader_message, $client_name, $client_main_phone, $bonusInfo, $current_cart, $contact_content, $client_address, date('m-d H:i', strtotime($client_time)), $current_cart - 1);
                }
            } else {
                //获取组长的姓名与手机
                $leaderInfo = DriverStatus::model()->get($leader);
                $leader_name = $leaderInfo->info['name'];
                $leader_phone = $leaderInfo->phone;
                //是组员 联系组长
                if ($vip) {
                    $msg = sprintf($driver_vip_message, $client_name, $vipcard, $current_cart, $client_address, date('m-d H:i', strtotime($client_time)), mb_substr($leader_name, 0, 1, 'utf-8'), $leader_phone);
                } else {
                    $msg = sprintf($driver_message, $client_name, $current_cart, $client_address, date('m-d H:i', strtotime($client_time)), mb_substr($leader_name, 0, 1, 'utf-8'), $leader_phone);
                }
            }
        } else {
            //预约单人
            $contact_content = '';
            if (!empty($contact_phone) && ($client_phone != $contact_phone)) {
                $contact_content = '联系人:' . $contact_phone . ',';
            }

            //预约单人
            $isVip = VipService::service()->isVip($queue->phone);
            if ($isVip) {
                $vip = VipService::service()->getVipInfo($queue->phone);
                if ($vip) {
                    $balance = isset($vip['amount']) ? $vip['amount'] : 0;
                    $vipCard = $vip['vipid'];
                    $data['vipcard'] = '卡号：' . $vipCard . '，余额：' . $balance . '元,不足部分请收取现金';
                } else {
                    $data['vipcard'] = 'VIP卡欠费禁用，请收取现金';
                }
                $dirver_message = '%s(%s,%s)预约代驾,%s地址:%s,时间:%s。';
                if (empty($contact_content)) {
                    $msg = sprintf($dirver_message, $client_name, $client_phone, $vipcard, $contact_content, $client_address, date('m-d H:i', strtotime($client_time)));
                } else {
                    $msg = sprintf($dirver_message, $client_name, substr_replace($client_phone, "*****", 3, 5), $vipcard, $contact_content, $client_address, date('m-d H:i', strtotime($client_time)));
                }
            } else {
                $customerBonus = CustomerBonus::getCustomerBounsAmount($client_phone, $queue->type);
                if ($customerBonus > 0) {
                    $bonusInfo = ' 优惠金额：' . $customerBonus . '元';
                }

                $dirver_message = '%s(%s%s)预约代驾,%s地址:%s,时间:%s。';
                if (empty($contact_content)) {
                    $msg = sprintf($dirver_message, $client_name, $client_phone, $bonusInfo, $contact_content, $client_address, date('m-d H:i', strtotime($client_time)));
                } else {
                    $msg = sprintf($dirver_message, $client_name, substr_replace($client_phone, "*****", 3, 5), $bonusInfo, $contact_content, $client_address, date('m-d H:i', strtotime($client_time)));
                }
            }
        }
        return array(
            'code' => 0,
            'msg' => $msg
        );
    }


    /**
     * 谁是组长
     */
    private function checkGroupLeader($queue_id)
    {
        $leader = OrderQueueMap::model()->getLeader($queue_id);
        if (empty($leader)) {
            return array();
        }
        return $leader;
    }

    /**
     * 把组员的信息发给组长
     * @author zhanglimin 2013-05-11
     * @param int $queue_id
     * @return array
     */
    public function sendLeaderSmsGroupMemberMsg($queue_id = 0)
    {
        if ($queue_id == 0) {
            return array();
        }
        //TODO 先走主库吧，到时候量大的话，在改
        $orderQueueMapList = OrderQueueMap::model()->findAllByAttributes(
            array('queue_id' => $queue_id),
            array('select' => 'driver_id', 'order' => 'confirm_time ASC')
        );

        if (!empty($orderQueueMapList)) {
            $message = '工号:%s 手机:%s  ';
            $msg = "组员联系信息:";
            $leader = "";
            foreach ($orderQueueMapList as $k => $val) {
                if ($k == 0) {
                    $leader = $val->driver_id;
                } else {
                    $driver = DriverStatus::model()->get($val->driver_id);
                    $phone = $driver->phone;
                    $msg .= sprintf($message, $val->driver_id, $phone);
                }
            }
            return array(
                'driver_id' => $leader,
                'msg' => $msg,
            );
        } else {
            return array();
        }

    }

    /**
     * 通过电话号获取订单列表  构造成OrderQueue记录
     * @param string $phone
     * @param int $offset
     * @param int $pageSize
     * @return array $data
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-05-16
     */
    public function getOrderByPhone($phone, $offset = 0, $pageSize = 20, $token)
    {
        if (empty($phone)) {
            return false;
        }
        //加缓存 一分钟
        $cache_key = 'orderlist_' . $token . '_' . $offset;
        $data = Yii::app()->cache->get($cache_key);
        //加缓存 一分钟 END
        if (!$data) {


            //获取订单总数
            $count = Order::getDbReadonlyConnection()->createCommand()
                ->select('COUNT(order_id) AS cnt')
                ->from('t_order')
                ->where('phone=:phone', array(':phone' => strval($phone)))
                ->queryRow();
            //获取订单总数 END

            //取订单
            $result = Order::getDbReadonlyConnection()->createCommand()
                ->select('order_id , status as flag , location_start  AS order_street_name, created AS order_time , 1 AS order_drivers_count , (booking_time-created) AS order_timedelta_from_now_on , phone AS order_customer_phone , contact_phone AS order_contact_phone , unix_timestamp(booking_time) AS booking_time , income AS price , distance')
                ->from('t_order')
                ->where('phone=:phone', array(':phone' => strval($phone)))
                ->order('order_id DESC')
                ->limit($pageSize)
                ->offset($offset)
                ->queryAll();
            //取订单 END

            foreach ($result as $key => $val) {
                //获取司机工号
                if ($val['flag'] == self::ORDER_READY) {
                    $result[$key]['order_status'] = OrderQueue::QUEUE_TYPE_ACCEPTED;
                } elseif ($val['flag'] == self::ORDER_COMPLATE) {
                    $result[$key]['order_status'] = OrderQueue::QUEUE_TYPE_FINISHED;
                } elseif ($val['flag'] == self::ORDER_COMFIRM) {
                    $result[$key]['order_status'] = OrderQueue::QUEUE_TYPE_CANCELED;
                } elseif ($val['flag'] == self::ORDER_CANCEL) {
                    $result[$key]['order_status'] = OrderQueue::QUEUE_TYPE_CANCELED;
                } elseif ($val['flag'] == self::ORDER_NOT_COMFIRM) {
                    $result[$key]['order_status'] = OrderQueue::QUEUE_TYPE_FINISHED;
                }
                if (empty($result[$key]['order_contact_phone'])) {
                    $result[$key]['order_contact_phone'] = $result[$key]['order_customer_phone'];
                }

                $result[$key]['order_contact_phone'] = substr_replace($result[$key]['order_contact_phone'], '****', 3, 4);
                $result[$key]['order_customer_phone'] = substr_replace($result[$key]['order_customer_phone'], '****', 3, 4);

                if (empty($val['price'])) {
                    $result[$key]['price'] = 0;
                }
                if (empty($val['distance'])) {
                    $result[$key]['distance'] = 0;
                }
                $result[$key]['order_drivers'] = OrderQueue::model()->getDriverIDByQueueID($val['order_id']);
                $result[$key]['order_id'] = Order::ORDER_ID_PRE . $val['order_id'];
                unset($result[$key]['flag']);
                unset($result[$key]['booking_time']);
            }
            $data = array(
                'orderList' => $result,
                'orderCount' => $count['cnt'],
            );
            Yii::app()->cache->set($cache_key, $data, 60);
        }
        return $data;
    }

    /**
     * 获取订单详情
     * @param string $order_id
     * @param string $phone
     * @param string $gps_type
     * @return array $result
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-05-16
     */
    public function getOrderByIdPhone($order_id, $phone, $gps_type)
    {
        if (empty($order_id) || empty($phone)) {
            return false;
        }
        $result = array();
        //取订单
        $result = Order::getDbReadonlyConnection()->createCommand()
            ->select('order_id , status AS flag , location_start  AS order_street_name, created AS order_time , 1 AS order_drivers_count , (booking_time-created) AS order_timedelta_from_now_on , phone AS order_customer_phone , contact_phone AS order_contact_phone , FROM_UNIXTIME(booking_time , "%Y-%m-%d %H:%i:%s") AS booking_time')
            ->from('t_order')
            ->where('order_id=:order_id AND phone=:phone', array(':order_id' => $order_id, ':phone' => strval($phone)))
            ->queryRow();
        //取订单 END

        if (!empty($result)) {
            if ($result['flag'] == self::ORDER_READY) {
                $result['order_status'] = 'accepted';
            } elseif ($result['flag'] == self::ORDER_COMPLATE) {
                $result['order_status'] = 'finished';
            } elseif ($result['flag'] == self::ORDER_COMFIRM) {
                $result['order_status'] = 'canceled';
            } elseif ($result['flag'] == self::ORDER_CANCEL) {
                $result['order_status'] = 'canceled';
            } elseif ($result['flag'] == self::ORDER_NOT_COMFIRM) {
                $result['order_status'] = 'finished';
            }
            if (empty($result['order_contact_phone'])) {
                $result['order_contact_phone'] = $result['order_customer_phone'];
            }

            $result['order_drivers'] = OrderQueue::model()->getDriverIDByQueueID($order_id, true, $gps_type);
            $result['order_id'] = Order::ORDER_ID_PRE . $result['order_id'];
            unset($result['booking_time']);
        }
        return $result;
    }

    /**
     * 报单
     * @author mengtianxue 2013-05-26
     * @param array $params
     * @return bool
     */
    public function submit_order($params)
    {
//    	echo "\n order submit start \n";
        EdjLog::info('order_submit_order --- '.json_encode($params));
        $order_id = $params['order_id'];
        if (is_numeric($order_id)) {
            $order = Order::model()->getOrderInfomation($params['order_id'], $params['driver_id']);
        } else {
            $order = Order::model()->getOrderByOrderNumber($params['order_number'], $params['driver_id']);
        }

        //订单是否存在
        if (empty($order)) {
            return true;
        }

        $distance = isset($params['distance']) ? intval($params['distance']) : 999999;
        if ($distance >= 10000) {
            return true;
        }

        //如果订单已经处理
        $finished_status = array(
            Order::ORDER_COMPLATE,
            Order::ORDER_CANCEL,
            Order::ORDER_COMFIRM,
        );
//        echo "\n validate status \n";
        if (!in_array($order->status, $finished_status)) {

            //普通用户校验vip否 BY AndyCong 2014-02-17
            if ($params['cost_type'] == 0) {
                echo "\n check vip \n";
                $CustomerInfo = RCustomerInfo::model()->getByPhone($order->phone);
                $is_vip = isset($CustomerInfo->vip) ? $CustomerInfo->vip : 0;
//	        	echo "\n check vip \n";
                if ($is_vip) {
                    $vipInfo =  VipService::service()->getVipPhoneInfo($order->phone);  //获取vip信息
                    if (!empty($vipInfo)) {
                        $params['cost_type'] = 1;
                        $params['card'] = $vipInfo['vipcard'];
                        $params['price'] = ($vipInfo['total_balance'] >= $params['income']) ? 0 : $params['income'] - $vipInfo['total_balance'];

                        //给司机推送提醒消息 走队列
                        $task = array(
                            'method' => 'push_vip_notice',
                            'params' => array(
                                'driver_id' => $params['driver_id'],
                                'phone' => $order->phone,
                                'balance' => $vipInfo['total_balance'],
                                'order_id' => $params['order_id'],
                            ),
                        );
                        Queue::model()->putin($task, 'pushmsg');
                    }
                }
            }
            //普通用户校验vip否 BY AndyCong 2014-02-17 END
//        	
            $orderSave = Order::model()->orderSave($order, $params);
            if (!$orderSave) {
                return FALSE;
            }
//            echo "\n submit end \n";
        } else { //如果已经报单则返回true BY AndyCong 2013-07-08
            return true;
        }

        //保存扩展
        // first check if arrive-in-time activity applied to the order
        $orderActivity = ArriveInTimeActivityRedis::model()->getOrderActivity($order->order_id);
        $params['driver_ready_time'] = $this->computeDriverReadyTime($orderActivity, $params);
        $params['driver_ready_distance'] = isset($params['ready_distance']) ? $params['ready_distance'] : 0.00;
        $orderExt = Order::model()->OrderExt($params);
        if (!$orderExt) {
            return FALSE;
        }
        $this->sendFailedActivitySms($orderActivity, $params, $order);

        //保存位置
        $orderPostionArr = array();
        $orderPostionArr['flag'] = 3;
        $orderPostionArr['order_id'] = $params['order_id'];
        $orderPostionArr['lat'] = $params['lat'];
        $orderPostionArr['lng'] = $params['lng'];
        $orderPostionArr['gps_type'] = $params['gps_type'];
        $orderPostionArr['log_time'] = $params['log_time'];
        $orderPostion = OrderPosition::model()->insertInfo($orderPostionArr);
        if (!empty($orderPostion) && !$orderPostion['ret']) {
            return FALSE;
        }
        SettleService::settle($params['order_id'], $order, $params);

        // for RemoteDispatchBonusActivity
        try {
            RemoteDispatchBonusActivity::model()->checkBonus($order);
        } catch (Exception $e) {
            EdjLog::warning('check RemoteDispatchBonusActivity bonus failed, message:' . $e->getMessage(), 'console');
        }

        return TRUE;
    }

    private function computeDriverReadyTime($orderActivity, $params)
    {
        // if 'arrive-in-time' activity applied to the order,
        // set the 'driver_ready_time' recorded on the server side instead of passed by App
        if (!empty($orderActivity) && isset($orderActivity['arrive_time'])) {
            return $orderActivity['arrive_time'];
        }
        if (isset($params['ready_time'])) {
            return $params['ready_time'];
        }
        return 0;
    }

    private function sendFailedActivitySms($orderActivity, $params, $order)
    {
        if (empty($orderActivity)) {
            return;
        }

        // notify customer again if sms sent failed on START step for one of following cases:
        // 1. in_time entry existed and driver not in time, but customer sms sent failed
        // 2. in_time entry not existed due to network issue which means sms not sent at all
        $needNotify = false;
        if (isset($orderActivity['in_time'])) {
            if (!$orderActivity['in_time'] && empty($orderActivity['customer_sms_success'])) {
                $needNotify = true;
            }
        } else if (!empty($params['driver_ready_time'])
            && !ArriveInTimeActivity::model()->isInTime($params['driver_ready_time'], $order->city_id)
        ) {
            $needNotify = true;
        }

        if ($needNotify) {
            EdjLog::info('notify customer again', 'console');
            ArriveInTimeActivity::model()->notifyCustomerOnOrderSubmit($order->city_id, $order->phone, $order->driver_id);
        }
    }

    /**
     * 修改报单信息
     * @author mengtianxue 2013-05-26
     * @param array $params
     * @return bool
     */
    public function orderSave($order, $params)
    {
        $result = FALSE;
        $order_param = array();
        $order_param['status'] = Order::ORDER_COMPLATE;
        $order_param['cast'] = 0;
        $order_param['name'] = trim($params['name']);
        $order_param['location_end'] = $params['location_end'];
        $order_param['distance'] = trim($params['distance']);
        $order_param['cost_type'] = trim($params['cost_type']);
        $car_type = isset($params['car_type']) ? trim($params['car_type']) : '';

        if (!empty($params['car_number'])) {
            $customer_info = $this->customerSave($params['name'], $order->phone, trim($params['car_number']), $car_type, $order->city_id);
            $order_param['user_id'] = $customer_info['user_id'];
            $order_param['car_id'] = $customer_info['car_id'];
        }

        if ($order_param['cost_type'] == 1) {
            $order_param['vipcard'] = $params['card'];
        }
        $order_param['income'] = empty($params['income']) ? 0 : $params['income'];
        $order_param['price'] = empty($params['price']) ? 0 : $params['price'];
        if (isset($params['start_time'])) {
            $order_param['start_time'] = strtotime($params['start_time']);
        }
        $order_param['end_time'] = empty($params['end_time']) ? 0 : strtotime($params['end_time']);
//        $order_param['city_id'] = Common::getcity(substr(trim($params['driver_id']), 0, 2));

        if (!empty($params['driver_id'])) {
            $order_param['driver_id'] = $params['driver_id'];
        }

        $order->attributes = $order_param;
        if ($order->updateByPk($params['order_id'], $order->attributes)) {

            //记录日志
            if (Order::model()->OrderLog($params)) {
                $result = TRUE;
            }
        }
        return $result;
    }


    /**
     * 保存customer信息
     * @param $order_id
     * @param $car_number
     * @return int
     * author mengtianxue 2013-06-05
     */
    public function customerSave($name, $phone, $car_number, $car_type = '', $city_id = 0)
    {

        //记录客户姓名
        $customer_info = CustomerService::service()->getCustomerInfo($phone,1);

        if (empty($customer_info)) {
            $attr = array(
                'name' => $name,
                'phone' => $phone,
                'city_id' => $city_id,
                'create_time' => date('Y-m-d h:i:s', time()));
            $customerMain = new CustomerMain();
            $customerMain->attributes = $attr;
            $customerMain->insert();
            $user_id = $customerMain->id;
        } else {
            $user_id = $customer_info->id;

        }

        //记录车排号
        $car_info = CustomerCar::model()->getCustomerCar($car_number, $user_id);
        if (empty($car_info)) {
            $car_attr = array(
                'user_id' => $user_id,
                'number' => $car_number,
                'brand' => $car_type,
                'create_time' => date('Y-m-d H:i:s')
            );
            $car = new CustomerCar();
            $car->attributes = $car_attr;
            $car->insert();
            $car_id = $car->car_id;
        } else {
            $car_id = $car_info['car_id'];
        }
        //返回信息
        $result = array('user_id' => $user_id, 'car_id' => $car_id);
        return $result;
    }


    /**
     * 再完成之前的处理 把关于客户的信息先录入数据库
     * @param $order_id
     * @param $name
     * @param $phone
     * @param $car_number
     * @return bool
     * author mengtianxue
     */
    public function orderBeforeSave($data)
    {
        $order_id = $data['order_id'];
        $name = $data['name'];
        $phone = $data['phone'];
        $car_number = $data['car_number'];
        if ($car_number != 0) {
            $order = new Order();
            $customer_info = $this->customerSave($name, $phone, $car_number);
            $order_param = array();
            $order_param['user_id'] = $customer_info['user_id'];
            $order_param['car_id'] = $customer_info['car_id'];
            if ($order->updateByPk($order_id, $order_param)) {
                return true;
            }
            return false;
        } else {
            return true;
        }
    }


    /**
     * 保存扩展信息
     * @author mengtianxue 2013-05-26
     * @param array $params
     * @return bool
     */
    public function OrderExt($params)
    {
        $driver_receive_time = isset($params['driver_receive_time']) ? intval($params['driver_receive_time']) : -1;
        $order_id = $params['order_id'];
        $model = new OrderExt();
        $modelExt = $model->find('order_id = :order_id', array(':order_id' => $order_id));
        $OrderExt = array();
        $OrderExt['order_id'] = $order_id;
        $OrderExt['wait_time'] = $params['waiting_time'];
        $OrderExt['tip'] = $params['tip'];
        $OrderExt['car_cost'] = $params['car_cost'];
        $OrderExt['other_cost'] = $params['other_cost'];
        $OrderExt['total'] = $params['tip'] + $params['car_cost'] + $params['other_cost'];
        $OrderExt['mark'] = $params['cost_mark'];
        $OrderExt['created'] = date('Y-m-d H:i:s');
        $OrderExt['driver_receive_time'] = $driver_receive_time;

        //添加实物劵金额  --mengtianxue 修改时间：2014-03-20
        if (isset($params['coupon_money']) && !empty($params['coupon_money'])) {
            $OrderExt['coupon_money'] = $params['coupon_money'];
        }

//        $OrderExt['mark'] = $params['log'];
        if ($modelExt) {
            //$OrderExt['mark'] = $modelExt->mark . '@' . $params['log'];
            $modelExt->attributes = $OrderExt;

            //增加等候时间与优惠券张数 BY AndyCong 2013-11-05
            if (!empty($params['stop_wait_time'])) {
                $modelExt->stop_wait_time = $params['stop_wait_time'];
            }

            if (!empty($params['coupon'])) {
                $modelExt->coupon = $params['coupon'];
            }

            if (!empty($params['invoiced'])) {
                $modelExt->invoiced = $params['invoiced'];
            }
            //增加等候时间与优惠券张数 BY AndyCong 2013-11-05 END

            //增加活动补贴字段 BY AndyCong 2014-04-03
            $modelExt->driver_ready_time = isset($params['driver_ready_time']) ? $params['driver_ready_time'] : 0;
            $modelExt->driver_ready_distance = isset($params['driver_ready_distance']) ? $params['driver_ready_distance'] : 0.00;
            $modelExt->driver_subsidy_money = isset($params['driver_subsidy_money']) ? $params['driver_subsidy_money'] : 0.00;
            $modelExt->customer_subsidy_money = isset($params['customer_subsidy_money']) ? $params['customer_subsidy_money'] : 0.00;
            $modelExt->driver_receive_time = $driver_receive_time;
            if ($modelExt->update()) {
                return true;
            }
        } else {
            $model->attributes = $OrderExt;

            //增加等候时间与优惠券张数 BY AndyCong 2013-11-05
            if (!empty($params['stop_wait_time'])) {
                $model->stop_wait_time = $params['stop_wait_time'];
            }

            if (!empty($params['coupon'])) {
                $model->coupon = $params['coupon'];
            }

            if (!empty($params['invoiced'])) {
                $model->invoiced = $params['invoiced'];
            }
            //增加等候时间与优惠券张数 BY AndyCong 2013-11-05 END

            //增加活动补贴字段 BY AndyCong 2014-04-03
            $model->driver_ready_time = isset($params['driver_ready_time']) ? $params['driver_ready_time'] : 0;
            $model->driver_ready_distance = isset($params['driver_ready_distance']) ? $params['driver_ready_distance'] : 0.00;
            $model->driver_subsidy_money = isset($params['driver_subsidy_money']) ? $params['driver_subsidy_money'] : 0.00;
            $model->customer_subsidy_money = isset($params['customer_subsidy_money']) ? $params['customer_subsidy_money'] : 0.00;

            if ($model->insert()) {
                return TRUE;
            }
        }
        return FALSE;
    }


    /**
     * 记录日志
     * @author mengtianxue 2013-05-26
     * @param array $params
     * @return bool
     */
    public function OrderLog($params)
    {

        //报单log
        $description = empty($params['log']) ? '报单' : $params['log'];
        //操作人
        $operator = $params['driver_id'];

        $log = new OrderLog();
        $log_attr = $log->attributes;
        $log_attr['order_id'] = $params['order_id'];
        $log_attr['description'] = $description;
        $log_attr['operator'] = strtoupper($operator);
        $log_attr['created'] = time();
        $log->attributes = $log_attr;
        if ($log->insert()) {
            return true;
        }
        return FALSE;
    }

    /**
     * 组织queue备注信息
     * @param array $data
     * @return string $comments
     */
    public function getQueueComments($data = array())
    {
        $comments = '';
        if (empty($data)) {
            return $comments;
        }
        $drivers = OrderQueueMap::model()->findAll('queue_id=:queue_id', array(':queue_id' => $data['queue_id']));
        foreach ($drivers as $driver) {
            $driver_info = Driver::getProfile($driver->driver_id);
            $comments .= sprintf('%s %s', $driver->driver_id, $driver_info->phone) . "<br/>";
        }
        if ($data['comments']) {
            $comments = $data['comments'] . '<br/>' . $comments;
        } else {
            $comments = $data['comments'] . $comments;
        }
        return $comments;
    }

    /**
     * 更新派单人数
     * @author zhanglimin 2013-06-19
     * @param $queue_id
     * @return mixed
     */
    public function updateDispatchNumber($queue_id)
    {
        $sql = "UPDATE `t_order_queue` SET `dispatch_number` = `dispatch_number`+1 WHERE id = :id";
        // Yii::app()->db change into OrderQueue::getDbMasterConnection()
        return OrderQueue::getDbMasterConnection()->createCommand($sql)->execute(array(
            ':id' => $queue_id,
        ));
    }

    /**
     * 获取报单订单列表
     * @param array $condition
     * @return array $data
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-06-24
     */
    public function getSubmitOrderList($condition = array())
    {
        if (empty($condition)) {
            return '';
        }
        //对代码优化
        $whereStr = 'driver_id=:driver_id and status IN(:order_complate , :order_not_confirm)';
        $whereArr = array(':driver_id' => $condition['driver_id'],
            ':order_complate' => self::ORDER_COMPLATE,
            ':order_not_confirm' => self::ORDER_NOT_COMFIRM
        );

        $key_fix = $condition['driver_id'] . '_' . $condition['offset'] . '_' . $condition['pageSize'];

        //如果传了order_Id，那么就取大于这个id的记录，其他条件不变。add by sunhongjing 2013-07-10
        if (!empty($condition['order_id'])) {
            $whereStr = ' order_id>:order_id and ' . $whereStr;
            $whereArr[':order_id'] = $condition['order_id'];
            $key_fix = 'new' . $condition['order_id'] . $key_fix;
        }

        $cache_key = self::SUBMIT_ORDER_CACHE_KEY . 'list_' . $key_fix;
        $data = Yii::app()->cache->get($cache_key);
        if (!$data) {
            $data = Order::getDbReadonlyConnection()->createCommand()
                ->select("order_id, source, FROM_UNIXTIME(booking_time, '%Y-%m-%d %H:%i') as booking_time, income , location_start, city_id, source as order_source, channel")
                ->from('t_order')
                ->where($whereStr, $whereArr)
                ->order('order_id DESC')
                ->limit($condition['pageSize'])
                ->offset($condition['offset'])
                ->queryAll();
            $tmp = array();
            if ($data) {
                foreach ($data as $order) {
                    $order['source'] = Order::SourceToDriverSubmitListString($order['source']);
                    $orderExt = OrderExt::model()->getPrimary($order['order_id']);
                    $total = FinanceCastHelper::getOrderTotalMoney($order, $orderExt, array(), true);
                    $order['income'] = $total;
                    $order['fee_total'] = $total;
                    $tmp[] = $order;
                }
            }
            Yii::app()->cache->set($cache_key, $tmp, 180);
            $data = $tmp;
        }
        return $data;
    }

    /**
     * 获取报单订单详情
     * @param array $condition
     * @return array $data
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-06-24
     */
    public function getSubmitOrderDetail($condition = array())
    {
        if (empty($condition)) {
            return '';
        }
        $cache_key = self::SUBMIT_ORDER_CACHE_KEY . 'detail_' . $condition['driver_id'] . '_' . $condition['order_id'];
        $data = Yii::app()->cache->get($cache_key);
        if (!$data) {
            //获取订单
            $data = Order::getDbReadonlyConnection()->createCommand()
                ->select("order_id , name , phone , source as order_source , location_start , location_end , booking_time as booking_date , FROM_UNIXTIME(booking_time, '%Y-%m-%d %H:%i') as booking_time , FROM_UNIXTIME(start_time, '%Y-%m-%d %H:%i') as start_time , FROM_UNIXTIME(end_time, '%Y-%m-%d %H:%i') as reach_time , distance , income , car_id , price , vipcard , cost_type, channel, city_id")
                ->from('t_order')
                ->where('order_id=:order_id and driver_id=:driver_id', array(':order_id' => $condition['order_id'], ':driver_id' => $condition['driver_id']))
                ->queryRow();
            if (empty($data)) {
                return '';
            }

            $data['source'] = Order::SourceToDriverSubmitListString($data['order_source']);

            //获取车牌号
            $car_number = CustomerCar::model()->getCarNumber($data['car_id']);
            if ($car_number) {
                $data['car_number'] = $car_number;
            } else {
                $data['car_number'] = '';
            }
            //获取车牌号 END

            //获取扩展信息
            $data['coupon_num'] = 0; //返回贵宾卡张数 By AndyCong 2013-12-20
            $orderExt = OrderExt::model()->getPrimary($condition['order_id']);
            $cash_card_balance = 0;   //兼容老版本
            if ($orderExt) {
                $data['wait_time'] = $orderExt['wait_time'];
                $data['other_cost'] = intval($orderExt['other_cost']);
                if (!empty($orderExt['mark'])) {
                    $data['remark'] = $orderExt['mark'];
                }

                //返回实体卡上报金额和绑定金额  --mengtianxue  2014-03-20
                $data['cash_card_balance'] = !empty($orderExt['coupon_money']) ? (string)$orderExt['coupon_money'] : '0';
                $data['cash_card_back_balance'] = !empty($orderExt['coupon_income']) ? (string)$orderExt['coupon_income'] : '0';

                //返回贵宾卡张数 By AndyCong 2013-12-20
                if (!empty($orderExt['coupon'])) {
                    $data['coupon_num'] = $orderExt['coupon'];
                    if ($orderExt['coupon_money'] == 0.00) {
                        $cash_card_balance = $orderExt['coupon_money'];
                        $data['cash_card_balance'] = $data['coupon_num'] * 39;
                    }
                }
                //返回贵宾卡张数 By AndyCong 2013-12-20 END
                $tips = FinanceCastHelper::getOrderFeeByExt($orderExt);
                $data['fee'] = $tips;

            } else {
                $data['wait_time'] = 0;
                $data['other_cost'] = 0;
            }
            //获取扩展信息 END

            //获取已返现的贵宾卡张数
            $data['coupon_back_num'] = 0;
            $coupon_back_num = CustomerBonus::model()->getBonusUseCount($data['phone'], $condition['order_id'], 2);//2为实体卡
            if ($coupon_back_num) {
                $data['coupon_back_num'] = isset($coupon_back_num['count']) ? intval($coupon_back_num['count']) : 0;
                if ($cash_card_balance == 0) {
//                    $data['cash_card_back_balance'] = $data['coupon_back_num'] * 39;
                    $data['cash_card_back_balance'] = $coupon_back_num['money'];
                }
            }
            //获取已返现的贵宾卡张数 END
            $orderDetail = FinanceCastHelper::getOrderFeeDetail($condition['order_id'], false);
            $total = FinanceCastHelper::getOrderTotalMoney($data, $orderExt, $orderDetail, true);
            $data['income'] = $total;
            switch ($data['cost_type']) {
                case 1:
                    $balanceCast = $total - $data['price'] - $data['cash_card_balance'];
                    if (0 < $balanceCast) {
                        $data['favorable'] = 'VIP账户支付' . $balanceCast . '元';
                    }
                    break;
                case 2:
//                    $data['favorable'] = '优惠金额' . ($data['income'] - $data['price'] - $data['coupon_back_num'] * 39) . '元';
                    break;
                case 4:
//                    $data['favorable'] = '抵扣金额' . ($data['income'] - $data['price']) . '元，';
//                    $data['favorable'] = '帐户支付' . ($data['income'] - $data['price']) . '元，';
                    break;
                case 8:
                    if (empty($data['cash_card_balance'])) {
                        $money_coupon = $data['coupon_num'] * 39;
                    } else {
                        $money_coupon = $data['cash_card_balance'];
                    }
                    $money = $total - $data['price'] - $money_coupon;
                    if ($money > 0) {
                        $data['favorable'] = '账户支付' . $money . '元';
                    }
                    break;
            }
            //获得补贴
            $subsidyMoney = FinanceCastHelper::getSubsidy($data, $orderExt);
            $data['subsidy_money'] = $subsidyMoney;
            $data['isRemote'] = FinanceUtils::isRemoteOrder($data, $orderExt);
            //投诉否
            $complaint = DriverComplaint::model()->getByOrderID($condition['order_id']);
            if (!empty($complaint)) {
                $data['complaint'] = 1;
            } else {
                $data['complaint'] = 0;
            }
            //投诉否 END

            $data['phone'] = substr_replace($data['phone'], '****', 3, 4);
            $data = array_merge($data, $orderDetail);
            unset($data['booking_date']);
            unset($data['car_id']);
            unset($data['price']);
            unset($data['vipcard']);
            unset($data['cost_type']);
            Yii::app()->cache->set($cache_key, $data, 180);
        }
        return $data;
    }

    /**
     * 获取报单 VIP和优惠券信息
     * @param int $order_id
     * @param string $phone
     * @param string $booking_time
     * @param int $source
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-07-01
     */
    public function getSubmitOrderFavorable($order_id, $phone, $booking_time, $source = 1)
    {
        $favorable = array('code' => 0);
        $vipPhone = VipPhone::model()->getPrimary($phone);
        if ($vipPhone) {
            $Favorable['code'] = 1;
            $vip_trade = VipTrade::model()->getByOrderID($order_id);
            if ($vip_trade) {
                $favorable['money'] = $vip_trade['amount'];
            } else {
                $favorable['money'] = 0;
            }
        } else {
            $customerBonus = CustomerBonus::bonusExistsUser($phone, $booking_time);
            if (!empty($customerBonus)) {
                if (($customerBonus['is_limited'] == BonusType::BONUS_TYPE_LIMIT_APP || $customerBonus['is_limited'] == BonusType::BONUS_TYPE_LIMIT_APP_FRESH)
                    && ($source == Order::SOURCE_CALLCENTER || $source == Order::SOURCE_CALLCENTER_INPUT)
                ) {
                } else {
                    $favorable['code'] = 2;
                    $favorable['money'] = $customerBonus['money'];
                }
            }
        }
        return $favorable;
    }


    /**
     * 获取订单详情
     * @author zhanglimin
     * @param array $condition
     * @return array
     */
    public function getOrderDetail($condition = array())
    {
        $ret = array();
        if (empty($condition)) {
            return $ret;
        }
        $order = Order::model()->findByPk($condition['order_id']);
        if (empty($order)) {
            return $ret;
        }


        $data = array(
            'cost_type' => 0,
            'vipcard' => '',
            'bonus' => '',
            'card' => '',
            'balance' => 0,
            'order_id' => $condition['order_id'],
            'order_number' => $order->order_number,
        );

//        $favorable = Order::model()->getOrderFavorable($order->phone, $order->booking_time, $order->source, $order->order_id);
//        switch ($favorable['code']) {
//            case 1:
//                $vipcard = '余额：' . $favorable['money'] . '元,不足部分请收取现金';
//                $data['cost_type'] = $favorable['code'];
//                $data['vipcard'] = $vipcard;
//                $data['card'] = $favorable['card'];
//                $data['balance'] = $favorable['money'];
//                break;
//            case 2:
//                $bonus = '优惠金额：' . $favorable['money'] . '元';
//                $data['cost_type'] = $favorable['code'];
//                $data['bonus'] = $bonus;
//                $data['card'] = $favorable['card'];
//                $data['balance'] = $favorable['money'];
//                break;
//            default:
//                break;
//        }

        $favorable = Order::model()->getOrderFavorable($order->phone, $order->booking_time, $order->source, $order->order_id);
        if ($favorable['code'] > 0) {
            $data['card'] = $favorable['card'];
            $data['balance'] = $favorable['money'] + $favorable['user_money'];
            $data['cost_type'] = (string)$favorable['code'];
            switch ($favorable['code']) {
                case 1:
                    $data['vipcard'] = '余额：' . $favorable['money'] . '元,不足部分请收取现金';
                    break;
                case 2:
                    $data['bonus'] = ' 优惠金额：' . $favorable['money'] . '元';
                    break;
                case 4:
                    $data['bonus'] = ' 优惠金额：' . $favorable['money'] . '元,个人帐户余额' . $favorable['user_money'] . '元,不足部分请收取现金';
                    break;
                case 8:
                    $data['bonus'] = ' 个人帐户余额：' . $favorable['user_money'] . '元,不足部分请收取现金';
                    break;
            }
        }

        return $data;
    }


    /**
     *
     * @author zhanglimin 2013-08-02
     * 销单或投诉时给客户发短信文字信息
     * @param array $params
     * $params = array(
     *   'phone', //客户电话
     *   'isComplaint', //是否投拆
     *   'cancel_type', //销单类型,
     *   'complaint_type', //投诉类型,
     *   'driver_id', //司机工号,
     *   'driver_name', //司机姓名,
     *   'driver_phone', //司机电话,
     * );
     * @return bool
     */
    public function sendSmsCancelComplaintMessage($params = array())
    {
        if (empty($params)) {
            return false;
        }

        $ret = false;

        $msg = "";

        if ($params['isComplaint'] == 0) {
            //销单
            switch ($params['cancel_type']) {
                case 14:
                    //已接其他订单
                    $msg = "非常抱歉！由于%s %s（%s）已接受其他预约订单未能为您服，您可以选择另外的司机师傅或拨打服务热线：4006913939。如该司机已为您服务请回复“代驾费用”，感谢您的支持! ";
                    break;
                case 15:
                    //客人单方面取消
                    $msg = "您取消了%s %s（%s）的代驾服务！如再需要代驾服务，您可随时呼叫司机师傅或拨打服务热线：4006913939。如非您主动取消该次服务，请回复“取消原因“，感谢您的支持! ";
                    break;
                case 17 :
                    //距离过远，无法满足客户约定时间到达
                    $msg = "非常抱歉！%s %s（%s）因距离您过远未能为您服务，您可选择另外较近的司机师傅或拨打服务热线：4006913939。该司机已为您服务请回复“代驾费用”，感谢您的支持! ";
                    break;
                case 34:
                    //其它
                    $msg = "非常抱歉！%s %s（%s）未能为您服务，您可以选择另外司机师傅或拨打服务热线：4006913939。如该司机已为您服务请回复“代驾费用”，感谢您的支持! ";
                    break;
                case 31:
                    //无法联系客人
                    $msg = "非常抱歉！%s %s（%s）因无法联络您未能提供服务，如再需要代驾服务，您可随时呼叫司机师傅或拨打服务热线：4006913939。如该司机已为您服务请回复“代驾费用”，感谢您的支持! ";
                    break;
                case 32:
                    //等候超过30分钟，客人单方面取消
                    $msg = "%s %s（%s）因您的取消预约未能提供服务，如再需要代驾服务，您可随时呼叫司机师傅或拨打服务热线：4006913939。如该司机已为您服务请回复“代驾费用”，感谢您的支持! ";
                    break;
                case 33:
                    //客人叫多名司机为自己服务
                    $msg = "%s %s（%s）因您的取消预约未能提供服务，如再需要代驾服务，您可随时呼叫司机师傅或拨打服务热线：4006913939。如该司机已为您服务请回复“代驾费用”，感谢您的支持! ";
                    break;
                default :
                    $msg = "";
                    break;
            }
        } else {
            //投拆
            switch ($params['complaint_type']) {
                case 11 :
                    //拒付费用
                    $msg = "%s %s（%s）因您拒付代驾费用对您进行了投诉！司机很辛苦，赏两个小钱吧。多次被投诉可能会被禁用代驾服务噢！咨询热线：4006913939 ";
                    break;
                case 12 :
                    //少付费用投诉
                    $msg = "%s %s（%s）因您少付代驾费用对您进行了投诉！司机很辛苦，别克扣这点小钱啦。多次被投诉可能会被禁用代驾服务噢！咨询热线：4006913939 ";
                    break;
                case 13:
                    //在计价器跳字前被迫到达
                    $msg = "%s %s（%s）因您中断代驾服务对您进行了投诉！绳命如此精彩，酒后驾车如同自杀啊，钱不钱的都是小事，您说呢？多次被投诉可能会被禁用代驾服务噢！咨询热线：4006913939 ";
                    break;
                case 31:
                    //无法联系客人
                    $msg = "由于司机%s %s（%s）无法联络到您导致服务取消并引发司机对您的投诉！如要取消服务，请及时通知司机师傅，避免因使司机白跑、白等。多次投诉会被禁用服务！咨询热线：4006913939 ";
                    break;
                case 32:
                    //等候超过30分钟，客人单方面取消
                    $msg = "%s %s（%s）因您的超时（等候大于30分钟）取消预约未能提供服务，引发了司机对您的投诉！如要取消服务请在30分钟内通知司机师傅，避免司机白跑、白等。多次投诉会被禁用代驾服务噢！咨询热线：4006913939 ";
                    break;
                case 33:
                    //客人叫多名司机为自己服务
                    $msg = "%s %s（%s）因您“同时呼叫多位师傅”而未能提供服务，引发了司机对您的投诉！如要取消服务请在30分钟内通知司机师傅，避免司机白跑、白等。多次投诉会被禁用代驾服务噢！咨询热线：4006913939 ";
                    break;
                default :
                    $msg = "";
                    break;
            }
        }

        if (!empty($msg)) {
            $msg = sprintf($msg, $params['driver_id'], $params['driver_name'], $params['driver_phone']);
            $ret = Sms::SendSMS($params['phone'], $msg, Sms::CHANNEL_SOAP);
        }
        return $ret;

    }


    /**
     * 统计当日运营情况
     * @author bidong 2013-08-13
     * @param $city_id
     * @param Timestamp $start_time
     * @param Timestamp $end_time
     * @return array
     */
    public function countOrderTrendDetails($city_id, $start_time, $end_time)
    {

        $cityArr = $have_order_driver = array();
        $total_order = $complete_order = $cancel_order = $callcenter_order = $app_order = $mobile_order = $tel_order = $new_user_order = $old_user_order = 0;

        $param = array(':stime' => $start_time, ':etime' => $end_time, ':city_id' => $city_id);
        //订单统计start 当日总订单数
        $readonlyCommand = Order::getDbReadonlyConnection()->createCommand();
        $today_orders = $readonlyCommand->select('*')
            ->from('{{order}}')
            ->where("( booking_time BETWEEN :stime AND :etime) AND city_id=:city_id")
            ->queryAll(true, $param);
        $readonlyCommand->reset();
        foreach ($today_orders as $orders) {
            $phone = trim($orders['phone']);
            $driver_id = $orders['driver_id'];
            $len = strlen($phone);

            //报单
            if (intval($orders['status']) == 1 || intval($orders['status']) == 4) {
                $complete_order++;
            }
            //销单
            if (intval($orders['status']) == 2 || intval($orders['status']) == 3) {
                $cancel_order++;
            }

            //已接单司机
            if (!in_array($driver_id, $have_order_driver)) {
                $have_order_driver[] = $driver_id;
            }
            //呼叫中心订单
            if (intval($orders['source']) == 1 || intval($orders['source']) == 3) {
                $callcenter_order++;
            }
            //app 订单
            if (intval($orders['source']) == 0 || intval($orders['source']) == 2) {
                $app_order++;
            }
            //固定电话、手机订单
            if ($len < 11) {
                $tel_order++;
            } else {
                $mobile_order++;
            }

            $readonlyCommand = Yii::app()->db_readonly->createCommand();
            $customerOrders = $readonlyCommand->select('count(1)')
                ->from('{{customer_main}}')
                ->where("phone=:phone")
                ->queryScalar(array(':phone' => $phone));
            //老用户订单
            if (intval($customerOrders) > 0) {
                $old_user_order++;
            }
            //新用户订单
            if (intval($customerOrders) == 0) {
                $new_user_order++;
            }
            $total_order++;
        }
        //订单统计end

        //t_driver_position 统计
        //select distinct user_id from t_driver_position where status =0 and created between '2013-08-01 07:00:00' and '2013-08-01 12:00:00' ;
        //已上线司机
        $online_driver = $online_no_order = $idle_drivers = $on_service_driver = $service_driver = array();

        $param2 = array(
            ':city_id' => $city_id,
            ':stime' => date('Y-m-d H:i:s', $start_time),
            ':etime' => date('Y-m-d H:i:s', $end_time),
            ':status' => DriverPosition::POSITION_GETOFF);

        $positionData = Yii::app()->db_readonly->createCommand()->select('dp.status,d.city_id,d.user')
            ->from('{{driver_position}} dp')->join('{{driver}} d', 'dp.user_id = d.id')
            ->where('d.city_id=:city_id and dp.created between :stime and :etime and dp.status !=:status')
            ->queryAll(true, $param2);

        //缓存在线司机，用于排重统计
        $online_driver_cache = array();

        $cache_time = DailyTrendDetails::model()->formatDate($start_time);
        $online_driver_cachekey = 'online_drivers_' . $city_id . '_' . $cache_time;
        $cacheData = Yii::app()->cache->get($online_driver_cachekey);
        if (!empty($cacheData))
            $online_driver_cache = unserialize($cacheData);

        foreach ($positionData as $position) {

            $driver_id = $position['user'];
            $status = $position['status'];
            if (intval($status) == DriverPosition::POSITION_WORK || intval($position['status']) == DriverPosition::POSITION_IDLE) {
                if (!in_array($driver_id, $online_driver_cache)) {
                    $online_driver_cache[] = $driver_id; //上线司机
                }
            }
            if (intval($status) == DriverPosition::POSITION_WORK) {
                $on_service_driver[] = $driver_id; //服务中的司机
                $cnt = Yii::app()->dborder_readonly->createCommand()
                    ->select('count(1)')
                    ->from('{{order}}')
                    ->where('driver_id=:driver_id and status=:status')
                    ->queryScalar(array(':driver_id' => $driver_id, ':status' => Order::ORDER_READY));
                if (intval($cnt) > 0) {
                    //代驾中司机	当前时刻，状态为服务中且有订单未报的司机数
                    $service_driver[] = $driver_id;
                }
            }
            if (intval($status) == DriverPosition::POSITION_IDLE) {
                $idle_drivers[] = $driver_id; //空闲的司机
            }

        }

        Yii::app()->cache->set($online_driver_cachekey, serialize($online_driver_cache), 172800);
        $online_driverArr = Yii::app()->cache->get($online_driver_cachekey);
        $online_driver = unserialize($online_driverArr);
        //缓存处理结束

        //上线未接单司机  $have_order_driver  $online_driver
        if (!empty($have_order_driver) && is_array($have_order_driver) && is_array($online_driver)) {
            $online_no_order = array_diff($have_order_driver, $online_driver);
        }


        $cityArr = array(
            'total_order' => $total_order,
            'complete_order' => $complete_order, //报单
            'cancel_order' => $cancel_order, //销单
            'callcenter_order' => $callcenter_order, //呼叫中心订单
            'app_order' => $app_order, //APP终端订单
            'mobile_order' => $mobile_order, //移动手机订单
            'tel_order' => $tel_order, //固定电话订单
            'new_user_order' => $new_user_order, //新用户订单
            'old_user_order' => $old_user_order, //老用户订单

            'have_order_driver' => count($have_order_driver), //已接单司机
            'online_driver' => count($online_driver), //已上线司机数
            'online_no_order' => count($online_no_order), //上线未接单司机
            'service_driver' => count($service_driver), //代驾中司机
            'on_service_driver' => count($on_service_driver), //服务中的司机
            'idle_drivers' => count($idle_drivers), //空闲的

        );

        return $cityArr;

    }

    /**
     * 修正昨日统计数据，报单、消单
     * @param $city_id
     * @param $start_time
     * @param $end_time
     * @return array
     * @author bidong 2013-08-16
     */
    public function countOrderTrendCollect($city_id, $start_time, $end_time)
    {

        $param = array(':stime' => $start_time, ':etime' => $end_time, ':city_id' => $city_id);

        $sql = 'SELECT count(*) as total_order,
                    sum(source=1 || source=3) as callcenter_order,
	                sum(source=0 || source=2) as app_order,
                    sum(status=1 || status=4) as complete_order,
                    sum(status=2 || status=3) as cancel_order,
                    sum(status=5) as driver_cancel_order,
                    sum(status=6) as customer_cancel_order,
                    sum(status=7) as dispatch_cancel_order,
                    sum(status=8) as driver_deny,
                    count(distinct driver_id) as have_order_driver
              FROM {{order}} WHERE (booking_time BETWEEN :stime AND :etime) AND city_id=:city_id';


        $result = Order::getDbReadonlyConnection()->createCommand($sql)->queryRow(true, $param);
        $ret = array();
        if ($result) {
            $ret = array(
                'total_order' => $result['total_order'],
                'callcenter_order' => empty($result['callcenter_order']) ? 0 : $result['callcenter_order'],
                'app_order' => empty($result['app_order']) ? 0 : $result['app_order'],
                'complete_order' => empty($result['complete_order']) ? 0 : $result['complete_order'],
                'cancel_order' => empty($result['cancel_order']) ? 0 : $result['cancel_order'],
                'have_order_driver' => empty($result['have_order_driver']) ? 0 : $result['have_order_driver'],
                'driver_cancel_order' => empty($result['driver_cancel_order']) ? 0 : $result['driver_cancel_order'],
                'customer_cancel_order' => empty($result['customer_cancel_order']) ? 0 : $result['customer_cancel_order'],
                'dispatch_cancel_order' => empty($result['dispatch_cancel_order']) ? 0 : $result['dispatch_cancel_order'],
                'driver_deny' => empty($result['driver_deny']) ? 0 : $result['driver_deny']
            );
        }

        return $ret;
    }

    /**
     * 获取最近两天的订单
     * @param $driver_id
     * @return array
     * author mengtianxue
     */
    public function getOrderNearTwoDays($driver_id)
    {
        $start_time = strtotime("-2 day");
        $end_time = time();
        $criteria = new CDbCriteria();
        $criteria->select = "*";
        $criteria->addCondition('driver_id = :driver_id and call_time between :start_time and :end_time');
        $criteria->params = array(':driver_id' => $driver_id, ':start_time' => $start_time, ':end_time' => $end_time);
        $criteria->order = "order_id desc";
        $criteria->limit = "10";
        $order_list = Order::model()->findAll($criteria);
        return $order_list;
    }

    /**
     * 订单汇总
     * @param $driver_id
     * @param $start_time
     * @param $end_time
     * @return mixed
     * author mengtianxue
     */
    public function getOrderSummary($driver_id, $start_time, $end_time)
    {
        $where = 'driver_id = :driver_id';
        $params = array(':driver_id' => $driver_id);
        if (!empty($start_time)) {
            $where .= ' and booking_time >= :call_start_time';
            $params[':call_start_time'] = strtotime($start_time);
        }

        if (!empty($start_time)) {
            $where .= ' and booking_time <= :call_end_time';
            $params[':call_end_time'] = strtotime($end_time);
        }

        $order_info = Order::getDbReadonlyConnection()->createCommand()
            ->select('driver_id, driver, count(1) as order_count,sum(IF((status = 0), 1, 0)) AS order_entry, sum(IF((status = 1), 1, 0)) AS order_declaration,sum(IF((status != 1 && status != 0), 1, 0)) AS order_single')
            ->from('t_order')
            ->where($where, $params)
            ->queryRow();
        return $order_info;
    }

    /**
     * 根据渠道号和时间获取商家的订单数、销单数、报单数
     * @param $channel
     * @param $call_time
     * @param $booking_time
     * @author daiyihui
     * @return CSqlDataProvider|null
     */
    public function getPartnerOrderStats($channel, $call_time, $booking_time)
    {
        if (!empty($channel) && !empty($call_time) && !empty($booking_time)) {
            if (strlen($channel) === 5) {
                $channel = '= ' . $channel;
            } elseif ($count = count(explode(',', $channel)) > 0) {
                $channel = 'in (' . $channel . ')';
            } else {
                return false;
            }
            $sql = 'select channel, COUNT(1) as total,SUM(IF((`status` IN(1,4)) , 1 , 0))  as count_complete, SUM(IF((`status` in(2,3)) , 1 , 0)) as count_cancel, SUM(IF((`status` in(1, 4)), income, 0)) as count_fee  from t_order where channel ' . $channel . ' and booking_time >= :call_time and booking_time <= :booking_time group by channel ';
            $count = Order::getDbReadonlyConnection()->createCommand($sql)->bindParam(':call_time', $call_time)->bindParam(':booking_time', $booking_time)->query()->count();
            $sql_provider = new CSqlDataProvider($sql, array(
                'keyField' => 'total', //必须指定一个作为主键
                'totalItemCount' => $count, //分页必须指定总记录数
                'pagination' => array(
                    'pageSize' => 10,
                ),
                'params' => array(
                    ':call_time' => $call_time,
                    ':booking_time' => $booking_time,
                ),
            ));
            return $sql_provider;
        } else
            return false;
    }

    public function getOrderTotal($channel_id)
    {
        if (!empty($channel_id)) {
            $sql = 'SELECT COUNT(*) FROM t_order where channel = :channel AND status IN(1,4)';
            $count = Order::getDbReadonlyConnection()->createCommand($sql)->bindParam(':channel', $channel_id, PDO::PARAM_STR)->queryScalar();
            return $count;
        } else {
            return 0;
        }
    }

    /**
     * 订单收费详情
     * @param $order_id
     * @return array
     * author mengtianxue
     */
    public function getOrderPriceInfo($order_id)
    {
        $return = array();
        $order = $this->getOrdersById($order_id);
        if ($order) {
            $city_id = $order['city_id'];
            $old = strtotime('2013-09-01');
            if ($order['booking_time'] > $old) {
                $startTime = $order['start_time'];
            } else {
                $startTime = $order['booking_time'];
            }
            //等候时间和等候费用
            $wait_time = 0;
            $wait_price = 0;
            $stop_wait_time = 0;//开车中等候时间
            $stop_wait_price = 0;
            $arrive_time = 0;//到达等候时间
            $arrive_price = 0;
            $order_ext = OrderExt::model()->getPrimary($order_id);
            if ($order_ext) {
                $wait_time = $order_ext['wait_time'];
                $wait_price = floor($wait_time / 30) * 20;
                $stop_wait_time = $order_ext['stop_wait_time'];
                $stop_wait_price = floor($stop_wait_time / 30) * 20;
                $arrive_time = $wait_time - $stop_wait_time;
                $arrive_price = floor($arrive_time / 30) * 20;
            }

            //公里数
            $distance = $order['distance'];

            //事实收入
            $income = CityConfig::model()->calculatorFee($city_id, $distance, $startTime, $wait_time); //Common::calculator($city_id, $distance, $startTime, $wait_time);

            //基础信息
            $base = Common::base_price($city_id, $startTime);
            $base_km = $base['km']; //初始公里数

            $km = 0;
            if ($distance > $base_km) {
                $km = $distance - $base_km;
            }

            $base_price = $base['price'];
            $premium_price = $income - $base_price - $wait_price;

            $return['base_km'] = $base_km; //基础公里数
            $return['step'] = $base['Step']; //加公里数
            $return['base_price'] = $base_price; //基础价格
            $return['premium_km'] = $km; //超出公里数
            $return['premium_price'] = $premium_price < 0 ? 0 : $premium_price; //超出部分收费
            $return['wait_time'] = $wait_time; //等待时间
            $return['wait_price'] = $wait_price; //等待费用
            $return['stop_wait_time'] = $stop_wait_time; //开车中等待费用
            $return['stop_wait_price'] = $stop_wait_price;
            $return['arrive_time'] = $arrive_time;
            $return['arrive_price'] = $arrive_price;
        }
        return $return;
    }


    public function vipListPriceInfo($order_id)
    {
        $vip_price_info = '';
        $price_info = $this->getOrderPriceInfo($order_id);
        if ($price_info) {
            $vip_price_info .= '起步价格(包含' . $price_info['base_km'] . '公里):' . $price_info['base_price'] . "元<br/>";
            if ($price_info['premium_km'] > 0) {
                $vip_price_info .= '里程加价(超出' . $price_info['premium_km'] . '公里):' . $price_info['premium_price'] . "元<br/>";
            }

            if ($price_info['wait_time'] > 0) {
                $vip_price_info .= '等候加价(等候' . $price_info['wait_time'] . '分钟):' . $price_info['wait_price'] . "元";
            }

        }
        return $vip_price_info;
    }

    /**
     * 从只读（从库）中获得订单信息
     * @param $order_id
     * @return mixed
     */
    public function getOrderInfoByReadDb($order_id)
    {
        $order_info = Order::getDbReadonlyConnection()->createCommand()
            ->select('*')
            ->from('t_order')
            ->where('order_id=:order_id', array(':order_id' => $order_id))
            ->queryRow();
        return $order_info;
    }

    /**
     * 获得订单渠道
     * @param $order_id
     * @return mixed
     */
    public function getOrderChannel($order_id)
    {
        $key = 'ORDER_CHANNEL_' . $order_id;
        $channel = Yii::app()->cache->get($key);
        if (!$channel) {
            $channel = Order::getDbReadonlyConnection()->createCommand()
                ->select('channel')
                ->from('t_order')
                ->where('order_id=:order_id', array(':order_id' => $order_id))
                ->queryScalar();
            $channel = is_null($channel) || empty($channel) ? 0 : $channel;
            Yii::app()->cache->set($key, $channel, 86400);
        }
        return $channel;
    }

    /**
     * 获取订单轨迹信息
     * @param <int> $orderId
     * @return <array>
     */
    public function getOrderPath($orderId = 0)
    {
        $result = array();
        $order = Order::model()->findByPk($orderId);
        if ($order) {
            $order_id = $order->order_id;
            $order_no = $order->order_number;
            /** 测试数据
             * $booking_date = date("Y-m-d H:i:s",1386785188);
             * $track = Tools::getOrderTrack($order_id = '2799290', $order_no = 'BJ81301386828658', $booking_date);
             **/
            $booking_date = date("Y-m-d H:i:s", $order->booking_time);
            $track = Tools::getOrderTrack($order_id, $order_no, $booking_date);
            $result = $track ? $track : $result;
        }
        return $result;
    }

    /**
     * 获取订单轨迹信息(从缓存中取数据)
     * @param <int> $orderId
     * @return <array>
     */
    public function getOrderPathFromCache($orderId = 0)
    {
        $result = array();
        //如果有缓存就从缓存中取(已经完成的订单会被存到缓存中)
        $cache = OrderCache::model()->getOrderPath($orderId);
        if ($cache) {
            return $cache;
        }
        $order = Order::model()->findByPk($orderId);
        if ($order) {
            $result = $this->getOrderPath($orderId);
            $onWay = in_array($order['status'], $this->getOnWayStatus());
            //非进行中的订单存到缓存中
            if (!$onWay && !empty($result)) {
                $toCache = OrderCache::model()->setOrderPath($orderId, $result);
            }
        }
        return $result;
    }

    /**
     * 根据订单id获取派单时间
     * @param <int> $orderId
     * @return <array>    结果 或 false
     * @author liuxiaobo
     */
    public function getDispatchInfoByOrderId($orderId = 0)
    {
        $command = OrderQueue::getDbReadonlyConnection()->createCommand();
        $t = OrderQueueMap::model()->tableName();
        $qt = OrderQueue::model()->tableName();
        $queueMapQuery = $command->select('t.dispatch_time,t.confirm_time,t.queue_id,t.driver_id,qt.*')
            ->from($t . ' t')
            ->join($qt . ' qt', 'qt.`id`=`t`.`queue_id`')
            ->where('order_id=:order_id');
        $queueMap = $queueMapQuery->queryRow(TRUE, array(':order_id' => $orderId));

        return $queueMap;
    }

    /**
     * 根据订单id和flag，获取订单位置信息
     * @param <int> $orderId
     * @return <array>    结果 或 false
     * @author liuxiaobo
     */
    public function getOrderPositionByOrderId($orderId = 0, $flag = 1)
    {
        $command = OrderPosition::getDbReadonlyConnection()->createCommand();
        $command->select('*')
            ->from(OrderPosition::model()->tableName())
            ->where('order_id=:order_id AND flag=:flag');
        $ret = $command->queryRow(TRUE, array(':order_id' => $orderId, ':flag' => $flag));
        return $ret;
    }

    /**
     * 根据订单id，获取订单取消信息
     * @param <int> $orderId
     * @return <array>    结果 或 false
     * @author liuxiaobo
     */
    public function getOrderCancelByOrderId($orderId = 0)
    {
        $command = Yii::app()->db_readonly->createCommand();
        $cancel_states = array(
            OrderProcess::PROCESS_SYS_CANCEL,
            OrderProcess::PROCESS_USER_CANCEL,
            OrderProcess::PROCESS_USER_DESTROY,
            OrderProcess::PROCESS_DRIVER_DESTROY,
        );

        $processes = OrderProcess::model()->getOrderProcessesById($orderId);
        $ret = array();
        foreach ($processes as $process) {
            if (in_array($process['state'], $cancel_states)) {
                $ret = $process;
            }
        }
        return $ret;
    }

    /**
     * 根据司机id，获取当月销单数据信息
     * @param  $driver_idId
     * @return <array>    结果 或 false
     * @author qiujianping@edaijia-staff.cn 2014-09-19
     */
    public function getDriverMonCancelOrders($driver_id = 'BJ00000')
    {
        $start_time = date("Y-m-01 07:00:00", time());
        $sql = "select FROM_UNIXTIME(created) as created, order_id from t_order where driver_id=:driver_id and status in(2,3) and FROM_UNIXTIME(created) >= :start_time and channel!='01004' and (channel!=0 and source!=1) order by created desc";
        $ret = Order::getDbReadonlyConnection()->createCommand($sql)->bindParam(':driver_id', $driver_id)->bindParam(':start_time', $start_time)->queryAll();
        return $ret;
    }

    /**
     * 根据订单id获取该订单的时间线
     * @param <int> $orderId
     * @return <array>
     * @author liuxiaobo
     */
    public function getOrderTimeLine($orderId = 0)
    {
        $result = array();
        $order = $this->getOrderById($orderId);
        $createTime = $order;
        $dispatchInfo = $this->getDispatchInfoByOrderId($orderId);
        $recevingOrderInfo = $this->getOrderPositionByOrderId($orderId, 1); //接单
        $arrivePlaceInfo = $this->getOrderPositionByOrderId($orderId, 20); //到达客户指定位置
        $drivingStartInfo = $this->getOrderPositionByOrderId($orderId, 2); //开车
        $drivingEndInfo = $this->getOrderPositionByOrderId($orderId, 29); //开车结束
        $submitOrderInfo = $this->getOrderPositionByOrderId($orderId, 3); //报单
        $cancelOrderInfo = $this->getOrderCancelByOrderId($orderId); //取消订单
        $result['order'] = $order; //订单信息
        $result['create'] = $createTime; //创建订单时的信息
        $result['dispatch'] = $dispatchInfo; //派单时的信息
        $result['recevingOrder'] = $recevingOrderInfo; //接单时的信息
        $result['arrivePlace'] = $arrivePlaceInfo; //到达客户指定位置时的信息
        $result['drivingStart'] = $drivingStartInfo; //开车时的信息
        $result['drivingEnd'] = $drivingEndInfo; //开车结束时的信息
        $result['submitOrder'] = $submitOrderInfo; //报单时的信息
        $result['cancelOrder'] = $cancelOrderInfo; //取消订单
        return $result;
    }

    /**
     * 根据订单id获取该订单的时间线(从缓存中获取)
     * @param <int> $orderId
     * @return <array>
     * @author liuxiaobo
     */
    public function getOrderTimeLineFromCache($orderId = 0)
    {
        $result = array();
        //如果有缓存就从缓存中取(已经完成的订单会被存到缓存中)
        $cache = OrderCache::model()->getTimeLine($orderId);
        if ($cache) {
            return $cache;
        }
        $order = $this->getOrderById($orderId);
        if ($order) {
            $result = $this->getOrderTimeLine($orderId);
            $onWay = in_array($order['status'], $this->getOnWayStatus());
            //非进行中的订单存到缓存中
            if (!$onWay && !empty($result)) {
                $toCache = OrderCache::model()->setTimeLine($orderId, $result);
            }
        }
        return $result;
    }

    /**
     * 返回订单状态是进行中的状态
     * @return type
     */
    public function getOnWayStatus()
    {
        return array(0);
    }

    /**
     * 拉取订单信息(目前只拉取400手工派单没收到推送的订单)
     * @param string $driver_id
     * @return string
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-23
     */
    public function pullOrderInfo($driver_id)
    {
        if (empty($driver_id)) {
            return '';
        }
        $start_time = time() - 600;
        $end_time = time() + 600;
        $sql_order = "select order_id,order_number from t_order where driver_id = :driver_id and created between '" . $start_time . "' and '" . $end_time . "' and source = 1 and status = 0";

        $order = Order::getDbReadonlyConnection()->createCommand($sql_order)->queryRow(true, array(':driver_id' => trim($driver_id)));
        if (empty($order)) {
            return '';
        }

        $map = OrderQueueMap::model()->findByAttributes(
            array('order_id' => $order['order_id'], 'driver_id' => trim($driver_id)),
            array('select' => 'queue_id, order_id')
        );
        if (empty($map)) {
            return '';
        }

        $msg = Push::model()->setPushOrderMsg($map->queue_id, $driver_id, $order['order_id']);
        if (0 == $msg['code']) {
            $msg['msg']['order_id'] = $order['order_id'];

            //如果是组员验证组长是不是自己
            if ($msg['msg']['role'] == '组员' || !empty($msg['msg']['leader_phone'])) {
                $driver = DriverStatus::model()->get($driver_id);
                if ($driver->phone == $msg['msg']['leader_phone']) {
                    $msg['msg']['role'] = '组长';
                    $msg['msg']['leader_phone'] = '';
                }
            }

            //推送新订单详情格式
            $data = array(
                'type' => GetuiPush::TYPE_ORDER_NEW_DETAIL,
                'content' => $msg['msg'],
                'queue_id' => $map->queue_id,
                'push_msg_id' => 1,
                'timestamp' => time(),
            );
            return $data;
        }
        return '';
    }


    public function confirmOrder($status_value = 0)
    {
        $status = '';
        switch ($status_value) {
            case Order::ORDER_READY :
                $status = '未报单';
                break;
            case Order::ORDER_COMPLATE :
                $status = '已报单';
                break;
            case Order::ORDER_COMFIRM :

                $status = '待审核';
                break;
            case Order::ORDER_CANCEL :
                $status = '已销单';
                break;
            case Order::ORDER_NOT_COMFIRM :
                $status = '拒绝销单';
                break;
            case Order::ORDER_DRIVER_REJECT_CANCEL :
                $status = '司机拒绝生成的取消订单';
                break;
            case Order::ORDER_CUSTOMER_CANCEL :
                $status = '用户取消';
                break;
            case Order::ORDER_NO_DISPATCH_CANCEL :
                $status = '未派出取消';
                break;
            case Order::ORDER_DRIVER_REJECT_NO_DISPATCH :
                $status = '未派出取消（司机拒单）';
                break;
        }

        return $status;

        //(($data->cancel_desc!="")?$data->cancel_desc:CHtml::link("销单", array("order/cancel", "id"=>$data->order_id))):$data->cancel_desc
    }

    /**
     *
     *add by aiguoxin
     *get orderNum and Income by driverID and finish time > 7:00
     */
    public function getOrderNumAndIncome($driver_id)
    {
        $yesterday = date("Y-m-d 07:00:00", strtotime("-1 day"));
        $time = date("Y-m-d 07:00:00");
        $timeStart = strtotime($yesterday);
        $timeEnd = strtotime($time);
        $hour = date('H');
        //如果当前时间大于上午7点,那么就计算上午七点到现在的收入情况 否则 计算 当天上午七点到昨天上午七点的收取情况
        if ($hour >= 7) {
            $timeStart = $timeEnd;
            $timeEnd = time();
        }
        $ret = FinanceCastHelper::getDriverIncome($driver_id, $timeStart, $timeEnd);
        $order = array();
        $order['order_count_today'] = $ret['total_count'];
        $order['order_income_today'] = $ret['total_income'];
        return $order;
    }


    /**
     *  获取司指定日期的收入
     *  从日期当天7点到第二天7点计算
     */
    public function getIncomeByDay($driver_id, $date)
    {
        $start_time = date("Y-m-d 07:00:00", strtotime($date));
        $begin_date_time = strtotime($start_time); //获取当天7点的时间戳
        $end_date_time = $begin_date_time + 60 * 60 * 24; //第二天7点时间戳
        $sql = "select sum(income) from t_order where driver_id=:driver_id and status=1 and created >= :begin_date_time and created < :end_date_time";
        $income = Order::getDbReadonlyConnection()->createCommand($sql)->bindParam(':driver_id', $driver_id)->bindParam(':begin_date_time', $begin_date_time)->bindParam(':end_date_time', $end_date_time)->queryScalar();
        return $income;
    }


    /**
     *返回参与活动的订单类型 必须来自客户端  而且  渠道必须为:选司机下单 或者 一键下单 或者选司机下单
     *
     **/
    public function checkActivityOrder($source, $channel)
    {
        return in_array($source, Order::$client_source) && ($channel == CustomerApiOrder::QUEUE_CHANNEL_BOOKING || $channel == CustomerApiOrder::QUEUE_CHANNEL_SINGLE_CHANGE || $channel == CustomerApiOrder::QUEUE_CHANNEL_SINGLE_DRIVER);
    }

    /**
     * 根据订单的时间 和司机 id 返回订单信息
     *
     * add by liutuanwang 2014-7-01 用于用户直播统计
     *
     * @param $timeStart
     * @param $timeEnd
     * @param $driverId
     */
    public function getByAppCallTimeAndDriverId($timeStart, $timeEnd, $driverId)
    {
        $condition = 'driver_id = :driverId and status in(1,2) and created between :timeStart and :timeEnd';
        $params = array(
            ':timeStart' => $timeStart,
            ':timeEnd' => $timeEnd,
            ':driverId' => $driverId,
        );
        return self::model()->find($condition, $params);
    }

    /**
     * 根据订单的时间 和司机 电话 返回订单信息
     *
     * add by liutuanwang 2014-7-06 用于第三方话务中心
     *
     * @param $timeStart
     * @param $timeEnd
     * @param $driverPhone
     */
    public function getByAppCallTimeAndDriverNumber($timeStart, $timeEnd, $driverPhone)
    {
        $condition = ' driver_phone = :driverPhone and status in(1,4) and source IN(0,2) and call_time between :timeStart and :timeEnd ORDER BY call_time ASC ';
        $params = array(
            ':timeStart' => $timeStart,
            ':timeEnd' => $timeEnd,
            ':driverPhone' => $driverPhone,
        );
        return self::model()->find($condition, $params);
    }

    /**
     *
     *   add by aiguoxin
     *   获取司机某个时间段总的订单数
     */
    public function getOrderCountByTime($driver_id, $start_time, $end_time)
    {
        $sql = "select count(1) from t_order where driver_id=:driver_id and created >= UNIX_TIMESTAMP(:start_time) and created <= UNIX_TIMESTAMP(:end_time)";
        $command = Order::getDbReadonlyConnection()->createCommand($sql);
        $command->bindParam(':driver_id', $driver_id);
        $command->bindParam(':start_time', $start_time);
        $command->bindParam(':end_time', $end_time);
        $count = $command->queryScalar();
        return $count;
    }

    /**
     *
     *   add by jiajingtao
     *   获取司机某个时间段成功的订单数，所行驶的里程（公里）
     */
    public function getOrderNumDistanceByTime($driver_id, $start_time, $end_time)
    {
        $sql = "select count(1) as ordernum, sum(distance)  as totaldistance from t_order where driver_id=:driver_id and created >= UNIX_TIMESTAMP(:start_time) and created <= UNIX_TIMESTAMP(:end_time) and status in(1,4)";
        $command = Order::getDbReadonlyConnection()->createCommand($sql);
        $command->bindParam(':driver_id', $driver_id);
        $command->bindParam(':start_time', $start_time);
        $command->bindParam(':end_time', $end_time);
        //$count = $command->queryScalar();
        $data = $command->queryRow();
        return $data;
    }

    /**
     * 获取某个时间端之内销单总数
     * @param $driver_id
     * @param $start_time
     * @param $end_time
     * @return
     */
    public function getCancelOrderCountByTime($driver_id, $start_time, $end_time)
    {
        $sql = "select count(1) from t_order where driver_id=:driver_id and created >= UNIX_TIMESTAMP(:start_time)
        and created <= UNIX_TIMESTAMP(:end_time) and status in(2,4)";
        $command = Order::getDbReadonlyConnection()->createCommand($sql);
        $command->bindParam(':driver_id', $driver_id);
        $command->bindParam(':start_time', $start_time);
        $command->bindParam(':end_time', $end_time);
        $count = $command->queryScalar();
        return $count;
    }

    /**
     * 返回某个用户某个时间点之前所有完成的订单
     *
     * @param $timestamp
     * @param $customerPhone
     * @return mixed
     */
    public function getTotalCountByTimeAndCustomerPhone($timestamp, $customerPhone)
    {
        $condition = ' phone = :phone and status IN(1 , 4) and  booking_time < :timestamp ';
        $params = array(
            ':timestamp' => $timestamp,
            ':phone' => $customerPhone,
        );
        return self::model()->count($condition, $params);
    }

    /**
     *  当前城市、当前月份的所有状态订单数
     *  customer/complain/ComplainKpiAction.php:ComplainKpiAction
     **/
    public function getCityMonthAllStatusOrders($criteria)
    {
        $command = Order::getDbReadonlyConnection()->createCommand();
        $order_count = $command->select('count(order_id)')
            ->from('t_order')
            ->where($criteria['condition'], $criteria['params'])
            ->queryScalar();
        $command->reset();

        return $order_count;
    }

    /**
     *  获取司机月份报单数
     *
     **/
    public function getCountByDriverAndMonth($driver_id, $stat_month)
    {
        $sql = "select count(1) from t_order where driver_id=:driver_id and status=1 and FROM_UNIXTIME(booking_time,'%Y-%m')=:stat_month";
        $count = Order::getDbReadonlyConnection()->createCommand($sql)->queryScalar(array(':driver_id' => trim($driver_id), ':stat_month' => $stat_month));
        return $count;
    }

    /**
     * 通过status和booking_time获取订单
     * 参数: status, booking_time(start, end)
     * commands/checkOrderCommand.php:actionCheck
     **/
    public function getOrders_Status_BookingId($status, $start, $end)
    {
        $sql = "select order_id,income,price,cast from t_order where status=$status and booking_time>=$start and booking_time<$end";
        return Order::getDbReadonlyConnection()
            ->createCommand($sql)->queryAll();
    }

    /**
     * 手机查询订单的城市
     * 参数: phone
     * commands/customerCommand.php:actionSetCustomerCity
     **/
    public function getCityByPhone($phone)
    {
        $sql = "SELECT city_id FROM t_order WHERE phone=" . $phone;
        $command = Order::getDbReadonlyConnection()->createCommand($sql);
        return $command->queryScalar();
    }

    /**
     * 成单的或销单审核不通过统计
     *
     * commands/dataCommand.php:actionDailyDriverOrderReport
     **/
    public function statDriverFailOrderCount($params)
    {
        $connection = Order::getDbReadonlyConnection();
        return
            $connection->createCommand()
                ->select("driver as name,
	              driver_id,
		      count(order_id) as order_count,
                      (SUM(IF((source IN (:source_callcenter)), 1, 0))+SUM(IF((source IN (:source_callcenter_input)), 1, 0))) as app_count,
		      (SUM(IF((source IN (:source_client_input)), 1, 0))+SUM(IF((source IN (:source_client)), 1, 0))) as callcenter_count, 
                      sum(income) as income")
                ->from('t_order')
                ->where('driver_id = :driver_id and status IN (:order_conplate,:not_comfirm) and (booking_time BETWEEN :dateBegin AND :dateEnd)')
                ->queryRow(true, $params);
    }

    /**
     * 通过传的条件和状态获取order的统计
     *
     * commands/dataCommand.php:getStatusOrderCount
     */
    public function getStatusOrderCount($params, $status, $ex_select = '')
    {
        $connection = Order::getDbReadonlyConnection();
        if (is_array($status)) {
            $params[':status'] = implode(',', $status);
            $where = 'driver_id = :driver_id and status = status IN ( :status ) and (booking_time BETWEEN :dateBegin AND :dateEnd)';
        } else {
            $params[':status'] = $status;
            $where = 'driver_id = :driver_id and status = :status and (booking_time BETWEEN :dateBegin AND :dateEnd)';
        }
        return $connection->createCommand()
            ->select('count(order_id) as order_count')
            ->from('t_order')
            ->where($where)
            ->queryRow(true, $params);
    }

    /**
     * 400手动派单创建订单
     * controllers/ClientController.php:actionSendsms
     * controllers/ClientController.php:actionGroupsms
     **/
    public function create400ManualOrder($params)
    {
        Order::getDbMasterConnection()
            ->createCommand()
            ->insert('t_order', $params);
        return Order::getDbMasterConnection()->getLastInsertID();
    }

    /**
     * 通过created和driver_id获取单个订单
     * 参数: $params (driver_id, created)
     * Customer/appCallRecord/RecordIndexAction.php:findOrder
     * controllers/CustomerController.php:getAppCallOrders
     **/
    public function getOrder_DriverID_Created($params, $ex_where)
    {
        $command = Order::getDbReadonlyConnection()->createCommand();
        return $command->select('order_id,status')
            ->from('{{order}}')
            ->where("driver_id=:driver_id and created between :created_start and :created_end " . $ex_where)
            ->order('order_id desc')
            ->queryRow(true, $params);
    }

    public function getOrders_DriverID_Created($params, $ex_where)
    {
        $command = Order::getDbReadonlyConnection()->createCommand();
        return $command->select('order_id,status')
            ->from('{{order}}')
            ->where("driver_id=:driver_id and created between :created_start and :created_end " . $ex_where)
            ->order('order_id desc')
            ->queryAll(true, $params);
    }

    public function getInvoiceOrdersByPhone($phone, $invoiced, $bookink_year, $isvip = false)
    {
        if ($bookink_year == Order::NOW_TABLE) {
            $t_order_table = 't_order';
        } else if ($bookink_year == Order::CURRENT_YEAR_TABLE) {
            $t_order_table = 't_order_' . date('Y', time());
        } else if ($bookink_year == Order::PRE_YEAR_TABLE) {
            $t_order_table = 't_order_' . date('Y', strtotime('-1 years'));
        } else {
            $t_order_table = 't_order';
        }
        $sql = "select t1.order_id as id,t1.booking_time as create_time,t1.order_id,t1.price as amount,cost_type as trans_type, 2 as table_name," . $bookink_year . " as booking_year from " . $t_order_table . " t1 left join t_order_ext t2 on t1.order_id = t2.order_id where t2.invoiced= " . $invoiced . " and t1.price>0 and t1.status=1";
        if (!$isvip) {
            $sql .= " and  t1.vipcard = '' and t1.phone ='" . $phone . "'";
        } else {
            $sql .= " and  t1.vipcard != '' and t1.phone in" . $phone;
        }
        /*$invoice_date = date('m-d', time());
        if ($invoice_date <= '01-31') {*/
        $last_year = date('Y-01-01 00:00:00', strtotime('-1 years'));
        $sql .= ' and t1.booking_time>=' . strtotime($last_year);
        /* } else {
             $last_year = date('Y-01-01 00:00:00', time());
             $sql .= ' and t1.booking_time>=' . strtotime($last_year);
         }*/
        $orders = Order::getDbReadonlyConnection()->createCommand($sql)->queryAll();
        return $orders;
    }

    public function getInvoicedOrders($invoiceId, $bookink_year)
    {
        if ($bookink_year == Order::NOW_TABLE) {
            $t_order_table = 't_order';
        } else if ($bookink_year == Order::CURRENT_YEAR_TABLE) {
            $t_order_table = 't_order_' . date('Y', time());
        } else if ($bookink_year == Order::PRE_YEAR_TABLE) {
            $t_order_table = 't_order_' . date('Y', strtotime('-1 years'));
        } else {
            $t_order_table = 't_order';
        }
        $sql = 'select t1.order_id as id,t1.booking_time as create_time,t1.order_id,t1.price as amount,cost_type as trans_type,2 as table_name,' . $bookink_year . ' as booking_year from ' . $t_order_table . ' t1 left join t_order_ext t2 on t1.order_id = t2.order_id where t2.invoice_id=' . $invoiceId;
        $orders = Order::getDbReadonlyConnection()->createCommand($sql)->queryAll();
        return $orders;
    }

    public function getOrdersByDriverId($driver_id)
    {
        $orders = Order::getDbReadonlyConnection()->createCommand()
            ->select('order_id,order_number,created')
            ->from('t_order')
            ->where('driver_id=:driver_id', array(':driver_id' => $driver_id))
            ->order('created desc')
            ->queryAll();
        return $orders;
    }

    /**
     *获取微信来源呼入的订单总数和有效订单数和完成报单数
     *
     **/
    public function getStaticsBySource($source, $begin_time, $end_time)
    {
        $where = 'source=:source and call_time>=:begin_time and call_time<=:end_time';
        $command = Order::getDbReadonlyConnection()->createCommand();
        $data = $command->select('count(1) as sum,sum(case when status=1  then 1 else 0 end) as success_order')
            ->from('{{order}}')
            ->where($where)
            ->queryAll(true, array('source' => $source, 'begin_time' => $begin_time, 'end_time' => $end_time));
        return $data;
    }

    /**
     * 通过order_id或order_number获取订单
     * @param $order_id
     * @return mixed
     */
    public function queryOrder($order_id)
    {
        if (strlen($order_id) > 11) {
            return Order::getDbReadonlyConnection()->createCommand()
                ->select('*')
                ->from('t_order')
                ->where('order_number = :order_number', array(':order_number' => $order_id))
                ->queryRow();
        }
        return Order::getDbReadonlyConnection()->createCommand()
            ->select('*')
            ->from('t_order')
            ->where('order_id = :order_id', array(':order_id' => $order_id))
            ->queryRow();
    }

    /*
     *
     */
    public function isCancelOrder($source)
    {
        $arr_source = array(
            self::SOURCE_CLIENT_INPUT,
            self::SOURCE_CALLCENTER_INPUT,
            self::SOURCE_DAYTIME_CLIENT_INPUT,
            self::SOURCE_DAYTIME_CALLCENTER_INPUT,
            self::SOURCE_WASHCAR_CLIENT_INPUT,
            self::SOURCE_WASHCAR_CALLCENTER_INPUT);

        return in_array($source, $arr_source);
    }

    /**
     *  获取订单数目
     *  参数:订单ID(IN操作), 司机ID
     *  commands/bonusCommand.php:getBindSelfByOneDay
     **/
    public function getCount_OrderIn_DriverID($order_ids, $driver_id)
    {
        $sql = "SELECT COUNT(1) FROM t_order WHERE order_id IN (" . join(',', $order_ids) . ") AND driver_id='{$driver_id}'";
        return Order::getDbReadonlyConnection()
            ->createCommand($sql)->queryScalar();
    }


    /**
     *  获取订单信息
     *  参数:订单ID(IN操作)
     **/
    public static function getStatus($order_ids)
    {
        $sql = "SELECT phone,status from t_order where order_id IN (" . join(',', $order_ids) . ")";
        return Order::getDbReadonlyConnection()
            ->createCommand($sql)->queryAll();
    }


    public static function createOrderType($source, $channel, $from = null)
    {
        $source = sprintf("%03d", $source);

        if (empty($from)) {
            if (!empty($channel)) {
                $type = $source . sprintf("%010d", $channel);
            } else {
                $type = $source . '0000000000';
            }
        } else {
            $type = $source . sprintf("%010d", $from);
        }

        if (strlen($type) != 13) {
            EdjLog::warning("order type($type) length is invalid(should be 13). source is $source, channel is $channel, from is $from");
        }

        return $type;
    }


    /**
     * 获取最大的订单id
     */
    public function getCurrentMaxOrderID()
    {
        $sql = 'SELECT order_id  from `t_order` order by order_id desc limit 1';
        return Order::getDbReadonlyConnection()->createCommand($sql)->queryScalar();
    }

    /**
     * 获取发送问卷的订单列表
     * @param $param
     * @param string $sourceOrderId
     * @return mixed
     */
    public function  getSendInvestRecord($param, $sourceOrderId = '', $sendToVip = 0)
    {
        if ($sourceOrderId != '') {
            $sql = $sql = 'select phone, contact_phone,order_id,city_id  from t_order where order_id=' . $sourceOrderId;
        } else {
            // 查询报单订单
            $sql = 'select phone, contact_phone,order_id,city_id from t_order where  status=' . Order::ORDER_COMPLATE;
            if ($sendToVip == 0) {
                $sql.= ' and vipcard<=0 ';
            }
            if ($param['city_id'] > 0) {
                $sql .= '  and city_id= ' . $param['city_id'];
            }
            if ($param['currentMaxOrderId'] > 0) {
                $sql .= '  and order_id<= ' . $param['currentMaxOrderId'];
            }

            $sql .= ' order by order_id desc ';

            $sql .= '  limit ' . $param['offset'];
            $sql .= ' ,' . $param['limit'];
        }

        return Order::getDbReadonlyConnection()->createCommand($sql)->queryAll();
    }
    
    /**
     * 根据number获取order 
     * @param unknown $number
     * @return unknown
     */
    public function getOrderByNumber($number) {
        $order = Order::getDbMasterConnection()->createCommand()
        ->select('*')
        ->from('t_order')
        ->where('order_number=:order_number', array(':order_number' => $number))
        ->queryRow();
        return $order;
    }
    
}
