<?php

/**
 * This is the model class for table "{{order_ext}}".
 *
 * The followings are the available columns in table '{{order_ext}}':
 * @property integer $order_id
 * @property integer $wait_time
 */
class OrderExt extends OrderActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{order_ext}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('order_id', 'required'),
            array('order_id, wait_time, stop_wait_time, invoiced, coupon, bad_weather_surcharge', 'numerical', 'integerOnly' => true),
            array('coupon_money, coupon_income, tip, car_cost, other_cost, total', 'length', 'max' => 10),
            array('created,driver_receive_time,driver_ready_time,driver_ready_distance,linear_ready_distance,driver_subsidy_money,customer_subsidy_money,fee,use_fee', 'safe'),
            array('mark', 'length', 'max' => 200),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('order_id, wait_time, stop_wait_time, invoiced, coupon, coupon_money, coupon_income, tip, car_cost, other_cost, total, created, mark,driver_ready_time,driver_ready_distance,linear_ready_distance,driver_subsidy_money,customer_subsidy_money', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'order_id' => 'Order',
            'wait_time' => 'Wait Time',
            'stop_wait_time' => 'Stop Wait Time',
            'invoiced' => 'Invoiced',
            'coupon' => 'Coupon',
            'coupon_money' => 'Coupon Money',
            'coupon_income' => 'Coupon Income',
            'tip' => 'Tip',
            'car_cost' => 'Car Cost',
            'other_cost' => 'Other Cost',
            'total' => 'Total',
            'created' => 'Created',
            'mark' => 'Mark',
            'driver_ready_time' => 'driver_ready_time',
            'driver_ready_distance' => 'driver_ready_distance',
            'driver_subsidy_money' => 'driver_subsidy_money',
            'customer_subsidy_money' => 'customer_subsidy_money',
            'fee' => 'Remote order fee',
            'use_fee' => 'If the fee should be used',
            'cash_only' => 'order is cash only',
            'linear_ready_distance' => 'linear_ready_distance',
            'bad_weather_surcharge' => 'bad weather surcharge fee',
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

        $criteria = new CDbCriteria;

        $criteria->compare('order_id', $this->order_id);
        $criteria->compare('wait_time', $this->wait_time);
        $criteria->compare('stop_wait_time', $this->stop_wait_time);
        $criteria->compare('invoiced', $this->invoiced);
        $criteria->compare('coupon', $this->coupon);
        $criteria->compare('coupon_money', $this->coupon_money, true);
        $criteria->compare('coupon_income', $this->coupon_income, true);
        $criteria->compare('tip', $this->tip, true);
        $criteria->compare('car_cost', $this->car_cost, true);
        $criteria->compare('other_cost', $this->other_cost, true);
        $criteria->compare('total', $this->total, true);
        $criteria->compare('created', $this->created, true);
        $criteria->compare('mark', $this->mark, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * 返回订单的实体卡金额
     *
     * @param $orderId
     * @return float
     */
    public function getCouponMoney($orderId)
    {
        $order_ext = $this->getPrimary($orderId);
        $coupon_money = 0.00;
        if ($order_ext) {
            if (empty($order_ext['coupon_money'])) {
                $coupon = $order_ext['coupon'];
                $coupon_money = $coupon * 39;
            } else {
                $coupon_money = $order_ext['coupon_money'];
            }
        }
        return $coupon_money;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OrderExt the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     *  获取orderExt 扩展信息,从读库读取
     * @author mengtianxue 2013-03-27
     * @param string $order_id
     * @return array
     */
    public function getPrimary($order_id)
    {
        $return = array();
        $order_ext = OrderExt::getDbReadonlyConnection()->createCommand()
            ->select("*")
            ->from("t_order_ext")
            ->where("order_id = :order_id", array(':order_id' => $order_id))
            ->queryRow();
        if ($order_ext) {
            $return = $order_ext;
        }
        return $return;
    }

    public function updateIncome($order_id, $money, $count = 1)
    {
        $counters = array('coupon' => $count, 'coupon_income' => $money);
        $where = 'order_id = :order_id';
        $param = array(':order_id' => $order_id);

        $ret = $this->updateCounters($counters, $where, $param);
        if ($ret) {
            return true;
        } else {
            return false;
        }
    }

    public static function getOrderExm($orderID)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('order_id', $orderID);
        $orderExm = self::model()->find($criteria);
        return empty($orderExm) ? 0 : $orderExm->wait_time;
    }

    /**
     * Get order fee
     * Array as params
     */
    public static function getFee($order_ext = array(), $order_id = 0)
    {
        $ret_fee = 0;
        $get_from_db = true;
        if (!empty($order_ext)) {
            if (isset($order_ext['use_fee']) && isset($order_ext['fee'])) {
                $get_from_db = false;
                if ($order_ext['use_fee'] == 1) {
                    $ret_fee = $order_ext['fee'];
                }
            }
        }

        if ($get_from_db) {
            $criteria = new CDbCriteria();
            $criteria->compare('order_id', $order_id);
            $order_exm = self::model()->find($criteria);
            if (!empty($order_exm)) {
                if ($order_exm->use_fee) {
                    $ret_fee = $order_exm->fee;
                }
            }
        }

        return $ret_fee;
    }

    /**
     *
     *   update driver receive order time
     *   add by aiguoxin
     **/
    public function updateDriverReceiveTime($order_id, $driver_receive_time)
    {
        $receive_time = array('driver_receive_time' => $driver_receive_time);
        $where = 'order_id = :order_id';
        $param = array(':order_id' => $order_id);

        $ret = $this->updateCounters($receive_time, $where, $param);
        if ($ret) {
            return true;
        } else {
            return false;
        }
    }


    public function dealOrderExt($id, $invoiceId)
    {
        $db = Order::getDbMasterConnection();
        $sql = "update t_order_ext set invoiced=1,invoice_id=:invoiceId where order_id =:id";
        $command = $db->createCommand($sql);
        $command->bindParam(":id", $id);
        $command->bindParam(":invoiceId", $invoiceId);
        $command->execute();
    }

    public static function WxOrderList($day_start, $day_end, $start, $limit)
    {
        $db = Order::getDbReadonlyConnection();
        $sql = "select order_id from t_order_ext where (created BETWEEN :dayStart and :dayEnd) and  type like '_____020%'  order by created desc limit :start,:limit";
        $command = $db->createCommand($sql);
        $command->bindParam(":dayStart", $day_start);
        $command->bindParam(":dayEnd", $day_end);
        $command->bindParam(":start", $start);
        $command->bindParam(":limit", $limit);
        return $command->queryAll();
    }
    

    public function getBadWeatherSurchargeByCity($city_id)
    {
       $bad_weather_surcharge = WeatherRaisePrice::model()->getCityAddPrice($city_id, date('Y-m-d H:i:s'));
       if (!empty($bad_weather_surcharge)
           && isset($bad_weather_surcharge['add_price'])
       ) {
           return $bad_weather_surcharge['add_price'];
       }

       return 0;
    }

    public function getBadWeatherSurchargeByOrderId($order_id)
    {
        if (empty($order_id)) {
            return false;
        }

        $order_ext = OrderExt::model()->findByPk($order_id);
        if (empty($order_ext)) {
            return false;
        }

        $bad_weather_surcharge = WeatherRaisePrice::model()->findByPk($order_ext['bad_weather_surcharge']);
        if (empty($bad_weather_surcharge)) {
            return 0;
        }

        static $ENABLED = 0;
        static $DISABLED = 1;

        if (isset($bad_weather_surcharge['add_price']) 
            && isset($bad_weather_surcharge['status'])
            && $bad_weather_surcharge['status'] == $ENABLED
        ) {
            return $bad_weather_surcharge['add_price'];
        } else {
            return 0;
        }
    }
}

