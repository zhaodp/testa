<?php

/**
 * This is the model class for table "{{customer_bonus}}".
 *
 * The followings are the available columns in table '{{customer_bonus}}':
 * @property integer $id
 * @property integer $bonus_type_id
 * @property integer $channel
 * @property string $bonus_sn
 * @property integer $parity_bit
 * @property integer $order_id
 * @property integer $customer_id
 * @property string $customer_phone
 * @property string $create_by
 * @property integer $created
 * @property integer $updated
 */
class CustomerBonus extends FinanceActiveRecord
{
    /**
     * 客户端
     */
    const CHANNELAPP = 0;
    /**
     * 呼叫中心
     */
    const CHANNELCALLCENTER = 1;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CustomerBonus the static model class
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
        return '{{customer_bonus}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'parity_bit, create_by, created',
                'required'),
            array(
                'bonus_type_id, balance, use_money, money, back_type, channel, parity_bit, order_id, customer_id, created, used, updated, end_date, user_limited, channel_limited,bonus_use_limit',
                'numerical',
                'integerOnly' => true),
            array(
                'bonus_sn, channel',
                'length',
                'max' => 20),
            array(
                'sn_type',
                'length',
                'max' => 2),
            array(
                'customer_phone, create_by',
                'length',
                'max' => 32),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array(
                'id, bonus_type_id, balance, sn_type, use_money, money, back_type, channel, bonus_sn, parity_bit, order_id, customer_id, customer_phone, create_by, created, used, updated, end_date, user_limited, channel_limited',
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
            'bonus_type' => array(
                self::BELONGS_TO,
                'BonusCode',
                'bonus_type_id'
            ),
			
        );
    }

    public function getBindCount($bonus)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('bonus_sn=:bonus_sn');
        $criteria->params[':bonus_sn'] = $bonus;
        $count = CustomerBonus::model()->count($criteria);

        return $count;
    }

    public function getUsedCount($bonus)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('bonus_sn=:bonus_sn');
        $criteria->params[':bonus_sn'] = $bonus;
        $criteria->addCondition('order_id > 0');
        $count = CustomerBonus::model()->count($criteria);

        return $count;
    }

    public static function existsCustomerBonusByType($bonus_type, $phone)
    {
        return CustomerBonus::model()->exists('bonus_type_id=:bonus_type_id AND customer_phone=:customer_phone', array(
            ':bonus_type_id' => $bonus_type,
            ':customer_phone' => $phone));
    }

    public static function existsCustomerBonus($bonus)
    {
        return CustomerBonus::model()->exists('bonus_sn=:bonus_sn', array(
            ':bonus_sn' => $bonus));
    }

    public static function existsCustomerPhone($phone)
    {
        return CustomerBonus::model()->exists('order_id=0 AND customer_phone=:customer_phone', array(
            ':customer_phone' => $phone));
    }

    public static function existsCustomerBonusWithPhone($bonus, $phone)
    {
        return CustomerBonus::model()->exists('bonus_sn=:bonus_sn AND customer_phone=:customer_phone', array(
            ':bonus_sn' => $bonus,
            ':customer_phone' => $phone));
    }


    /**
     * 查询手机号是否有未使用的优惠劵
     * @param int $phone
     * @return array|null
     * author mengtianxue  添加注释
     */
    public static function existsUnusedCustomerBonus($phone = 0)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'order_id = 0 and customer_phone=:customer_phone and created < :created';
        $criteria->order = 'id desc';
        $criteria->params = array(
            ':customer_phone' => $phone,
            ':created' => time() - 600,
        );

        $customerBonus = CustomerBonus::model()->find($criteria);
        if (!$customerBonus) {
            return null;
        } else {
            $bonusType = CustomerBonus::getBonusCode($customerBonus->bonus_type_id);
            return array(
                'name' => $bonusType->name,
                'money' => $bonusType->money,
                'is_limited' => isset($bonusType->is_limited) ? $bonusType->is_limited : '',
                'bonus' => $customerBonus->bonus_sn,
                'parity_bit' => $customerBonus->parity_bit,
                'channel' => $customerBonus->channel,
                'phone' => $phone);
        }
    }


    public static function getBonusCode($type_id)
    {
        $bonusCode = BonusCode::model()->find('id = :type_id', array(
            ':type_id' => $type_id
        ));
        return $bonusCode;
    }

    /**
     * 查询小于绑定时间 手机号是否有未使用的优惠劵
     * @param $phone
     * @param $bonusTime
     * @return array|null
     * author mengtianxue
     */
    public static function bonusExistsUser($phone, $bonusTime)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'order_id = 0 and customer_phone=:customer_phone and created < :created';
        $criteria->order = 'id desc';
        $criteria->limit = 1;
        $criteria->params = array(
            ':customer_phone' => $phone,
            ':created' => $bonusTime
        );

        $customerBonus = CustomerBonus::model()->find($criteria);

        if (!$customerBonus) {
            return null;
        } else {
            $bonusType = BonusType::getBonusType($customerBonus->bonus_type_id);
            return array(
                'name' => $bonusType->name,
                'money' => $bonusType->money,
                'is_limited' => $bonusType->is_limited,
                'bonus' => $customerBonus->bonus_sn,
                'parity_bit' => $customerBonus->parity_bit,
                'channel' => $customerBonus->channel,
                'phone' => $phone);
        }
    }


    /**
     * 查找用户订单所使用的优惠号码及优惠金额
     * @param int $phone
     * @param int $order_id
     */
    public static function getOrderBonus($phone, $order_id)
    {
        $customerBonus = CustomerBonus::model()->find('order_id = :order_id and customer_phone=:customer_phone', array(
            ':order_id' => $order_id,
            ':customer_phone' => $phone));

        if (!$customerBonus) {
            return 0;
        } else {
            return array(
                'bonus_type_id' => $customerBonus->bonus_type_id,
                'money' => $customerBonus->balance,
                'bonus' => $customerBonus->bonus_sn,
                'parity_bit' => $customerBonus->parity_bit,
                'channel' => $customerBonus->channel,
                'created' => $customerBonus->created,
                'used' => $customerBonus->used,
                'sn_type' => $customerBonus->sn_type,
            );
        }
    }

    public function getCountBonusUsed($bonus_sn, $parity_bit = '0')
    {
        $count = CustomerBonus::model()->count('bonus_sn = :bonus_sn and parity_bit=:parity_bit', array(
            ':bonus_sn' => $bonus_sn,
            ':parity_bit' => $parity_bit));
        return $count;
    }

    /**
     *
     * 客户使用优惠券
     * @param int $phone
     * @param int $bonus
     * @param int $order_id
     */
    public static function customerBonusUse($phone = 0, $bonus = 0, $order_id = 0, $used = 0)
    {
        $customerBonus = CustomerBonus::model()->find('order_id = 0 and customer_phone=:customer_phone and bonus_sn=:bonus', array(
            ':customer_phone' => $phone,
            ':bonus' => $bonus));

        $order = Order::getOrderInfo($order_id);
        if ($order && $customerBonus) {
            if (($order->call_time) > ($customerBonus->created)) {
                if ($used == 0)
                    $used = time();

                $attr = array(
                    'order_id' => $order_id,
                    'used' => $used,
                    'updated' => time());
                $customerBonus->attributes = $attr;

                if ($customerBonus->save())
                    return true;
            }
        }
        return false;
    }

    public static function initCustomerBonus($bonus, $phone, $channel = 0)
    {
        $customer = CustomerService::service()->initCustomer($phone);
        $customerBonus = new CustomerBonus();

        $attr = array(
            'bonus_type_id' => $bonus['typeId'],
            'channel' => $channel,
            'bonus_sn' => $bonus['bonusString'],
            'parity_bit' => $bonus['parityBit'],
            'customer_id' => $customer->id,
            'customer_phone' => $phone,
            'create_by' => '用户录入',
            'created' => time());

        $customerBonus->attributes = $attr;
        if ($customerBonus->insert()) {
        }

        return true;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'bonus_type_id' => '优惠券名称',
            'channel' => 'Channel',
            'sn_type' => '类型',
            'bonus_sn' => '优惠券号码',
            'parity_bit' => 'Parity Bit',
            'balance' => '金额',
            'use_money' => '使用金额',
            'order_id' => '订单流水号',
            'customer_id' => 'Customer',
            'customer_phone' => '客户电话',
            'create_by' => 'Create By',
            'created' => '绑定时间',
            'used' => '消费时间',
            'updated' => '更新时间',
            'end_date' => '截止日期',
            'user_limited' => '用户限制',
            'channel_limited' => '渠道限制',
            'bonus_use_limit' => '使用限制'
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


        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id);
        $criteria->compare('bonus_type_id', $this->bonus_type_id);
        $criteria->compare('channel', $this->channel);

        /**
         * @author 李白阳 2013-04-19
         * 修改查找优惠劵条件
         */
        if ($this->bonus_sn) {
            $criteria->compare('bonus_sn', $this->bonus_sn);
        }
// 		if ($this->bonus_sn) {
// 			$arrBonus = BonusType::validCode($this->bonus_sn);
// 			if ($arrBonus['code'] == '1')
// 			{
// 				$criteria->compare('bonus_sn', $arrBonus['bonusString'], true);
// 			}
// 		}

        $criteria->compare('parity_bit', $this->parity_bit);
        $criteria->compare('order_id', $this->order_id);
        $criteria->compare('customer_id', $this->customer_id);
        $criteria->compare('customer_phone', $this->customer_phone, true);
        $criteria->compare('create_by', $this->create_by, true);
        $criteria->compare('created', $this->created);
        $criteria->compare('updated', $this->updated);
        $criteria->compare('bonus_use_limit', $this->bonus_use_limit);
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pagesize' => 30)));
    }

    public function getMinCreatedDate($bonus_type_id)
    {
        $date = null;
        $criteria = new CDbCriteria();
        $criteria->select = "min(created) as created";
        if ($bonus_type_id > 0)
            $criteria->addCondition('bonus_type_id=' . $bonus_type_id);

        $data = self::model()->find($criteria);
        if ($data && $data->created) {
            $date = date('Y-m-d', $data->created);
        }
        return $date;
    }

    /**
     * 通过司机工号获得bonus
     * @parame string $driver_id
     * @return string
     */
    public static function getBonusByDriver($driver_id)
    {
        $mem_key_prefix = "DRIVER_TO_BONUS_";
        $mem_key = $mem_key_prefix . $driver_id;
        $bonus = false;
        if (!$bonus) {
            $city_arr = self::getCityPrefix();
            $city_prefix = substr($driver_id, 0, 2);
            if ($city_prefix) {
                $city_id = array_search($city_prefix, $city_arr);
                $city_id = $city_id < 10 ? '0' . $city_id : $city_id;
                $bonus = str_replace($city_prefix, $city_id, $driver_id);
            }
        }
        return $bonus;
    }

    /**
     * 通过bonus获得司机信息（工号，姓名，城市），信息缓存3天
     * @parame string $bonus_sn
     * @return array
     */
    public static function getDriverByBonus($bonus_sn)
    {
        $mem_key_prefix = "BONUS_TO_DRIVER_";
        $mem_key = $mem_key_prefix . $bonus_sn;
        $driver_info = Yii::app()->cache->get($mem_key);

        if (!$driver_info) {
            $city_arr = self::getCityPrefix();
            $city_id = substr($bonus_sn, 0, 2);
            $driver_id = substr($bonus_sn, 2, 4);
            $city_prefix = isset($city_arr[$city_id]) ? $city_arr[$city_id] : 0;
            if (!$city_prefix) {
                return false;
            }
            $driver_id = $city_prefix . $driver_id;
            $driver = Driver::getProfile($driver_id);
            if ($driver) {
                $driver_info = array(
                    'id' => $driver->user,
                    'name' => $driver->name,
                    'city_id' => $driver->city_id,
                );
                Yii::app()->cache->set($mem_key, $driver_info, 86400 * 3);
            }
        }
        return $driver_info;
    }

    public static function getCityPrefix()
    {
        $DcityPrefix = Dict::model()->findAll(array(
            'condition' => 'dictname=:dictname',
            'params' => array(
                ':dictname' => 'bonus_city'
            )
        ));
        $city_arr = array();
        foreach ($DcityPrefix as $item) {
            $city_arr [$item->code] = $item->name;
        }
        return $city_arr;
    }

    /**
     * 获取手机号码绑定优惠劵的金额
     * @author mengtianxue 2013-05-22
     * @param $phone 客户电话
     * @param $type 优惠劵类型  1为400用户，2为app呼叫
     */
    public function getCustomerBounsAmount($phone, $type = 1)
    {
        $price = 0;
        //检查优惠劵是否使用
        $un_used_bonus = CustomerBonus::existsUnusedCustomerBonus($phone);
        if ($un_used_bonus) {
            //app用户 优惠劵是限app使用
            if (($un_used_bonus['is_limited'] == BonusType::BONUS_TYPE_LIMIT_APP || $un_used_bonus['is_limited'] == BonusType::BONUS_TYPE_LIMIT_APP_FRESH)
                && $type == 2
            ) {
                $price = $un_used_bonus['money'];
            }
            //400用户 优惠劵是非限app使用
            if (($un_used_bonus['is_limited'] != BonusType::BONUS_TYPE_LIMIT_APP || $un_used_bonus['is_limited'] != BonusType::BONUS_TYPE_LIMIT_APP_FRESH)
                && $type == 1
            ) {
                $price = $un_used_bonus['money'];
            }

        }
        return $price;
    }

    /**
     * 获取师傅新客邀请码绑定次数
     * @param $bonus_count
     * author mengtianxue
     */
    public function getCustomerBounsCount($bonus_sn)
    {
        $created = strtotime(date('Y-m-01'));
        $criteria = new CDbCriteria();
        $criteria->compare('bonus_sn', $bonus_sn);
        $criteria->compare('bonus_type_id', 8); // 8为根据司机工号生成的新客优惠码
        $criteria->addCondition('created > :created');
        $criteria->params[':created'] = $created;
        $success_order_info = self::model()->count($criteria);
        return $success_order_info;
    }


    /**
     * 判断是否有绑定新客邀请码
     * @param $phone
     * @return mixed
     * author mengtianxue
     */
    public function getNewInviteBonus($phone)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('customer_phone', $phone);
        $criteria->compare('bonus_type_id', 8);
        $bonus_info = self::model()->find($criteria);
        return $bonus_info;
    }

    /**
     * 修改优惠劵使用状态
     * @param $bonus_sn
     * @param $phone
     * @param null $used
     * @return bool
     * author mengtianxue
     */
    public function setCustomerBonusUsed($bonus_sn, $phone, $used = null)
    {
        $customerBonus = $this->find('bonus_sn = :bonus_sn and customer_phone = :phone',
            array(':bonus_sn' => $bonus_sn, ':phone' => $phone));
        $customer_bonus = $customerBonus->attributes;
        if ($used === null) {
            $customer_bonus['used'] = time();
        } else {
            $customerBonus['used'] = $used;
        }
        $customerBonus->attributes = $customer_bonus;

        if ($customerBonus->save()) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 优惠劵占用
     * 成单和销单时传order_id,成单时传订单id号，销单时传0
     * @param array $params bonus_sn优惠劵码必传   phone客户手机号必传 order_id订单id
     * @return bool
     * author mengtianxue
     */
    public function CustomerBonusOccupancy($params)
    {

        if (empty($params['bonus_sn']) && empty($params['phone'])) {
            return false;
        }

        $customerBonus = $this->find('bonus_sn = :bonus_sn and customer_phone = :customer_phone and order_id = 0',
            array(':bonus_sn' => $params['bonus_sn'], ':customer_phone' => $params['phone']));

        if ($customerBonus) {
            if (isset($params['order_id'])) {
                $customerBonus->order_id = $params['order_id'];
            } else {
                $customerBonus->order_id = 0;
            }

            $ret = $customerBonus->updateByPk($customerBonus->id, $customerBonus->attributes);
            if ($ret) {
                return $customerBonus;
            }
        }
        return false;
    }

    /**
     * 优惠劵绑定
     * @param $bonus
     * @param $phone
     * @return bool
     * author mengtianxue
     */
    public function AddCustomerBonus($bonus, $phone)
    {
        $customer_id = 0;
        try {//修复并发job绑定优惠劵时的问题
            $customer = CustomerService::service()->initCustomer($phone);
            if ($customer) {
                $customer_id = $customer->id;
            }
        } catch (Exception $e) {
            EdjLog::error($e->getMessage());
        }
        $sn_type = isset($bonus['sn_type']) ? $bonus['sn_type'] : 0;
        $customerBonus = new CustomerBonus();
        $attr = array(
            'bonus_type_id' => $bonus['id'],
            'channel' => $bonus['channel'],
            'sn_type' => $sn_type,
            'bonus_sn' => $bonus['bonus_sn'],
            'parity_bit' => $bonus['parityBit'],
            'customer_id' => $customer_id,
            'balance' => $bonus['money'],
            'money' => $bonus['money'],
            'back_type' => $bonus['back_type'],
            'customer_phone' => $phone,
            'create_by' => 'system',
            'end_date' => $bonus['end_date'],
            'user_limited' => $bonus['user_limited'],
            'channel_limited' => $bonus['channel_limited'],
        );

        if (isset($bonus['bonus_use_limit'])) {
            $attr['bonus_use_limit'] = $bonus['bonus_use_limit'];
        }

        if (isset($bonus['used'])) {
            $attr['used'] = $bonus['used'];
        }

        if (isset($bonus['order_id'])) {
            $attr['order_id'] = $bonus['order_id'];
        }

        if (isset($bonus['created'])) {
            $attr['created'] = $bonus['created'];
        } else {
            $attr['created'] = time();
        }
		$customerBonus->attributes = $attr;
		if ($customerBonus->insert()) {
			return $customerBonus->id;
		} else {
			return false;
		}		
		   
    }
	
	
	
	/**
     * 优惠劵绑定 同种优惠券时 新优惠券加入之前的设为过期
     * @param $bonus
     * @param $phone
     * @return bool
     * author mengtianxue
     */
    public function AddCustomerBonus2($bonus, $phone)
    {
        $customer_id = 0;
        try {//修复并发job绑定优惠劵时的问题
            $customer = CustomerService::service()->initCustomer($phone);
            if($customer){
                $customer_id = $customer->id;
            }
        }catch (Exception $e){
            EdjLog::error($e->getMessage());
        }
        $sn_type = isset($bonus['sn_type']) ? $bonus['sn_type'] : 0;
        $customerBonus = new CustomerBonus();
        $attr = array(
            'bonus_type_id' => $bonus['id'],
            'channel' => $bonus['channel'],
            'sn_type' => $sn_type,
            'bonus_sn' => $bonus['bonus_sn'],
            'parity_bit' => $bonus['parityBit'],
            'customer_id' => $customer_id,
            'balance' => $bonus['money'],
            'money' => $bonus['money'],
            'back_type' => $bonus['back_type'],
            'customer_phone' => $phone,
            'create_by' => Yii::app()->user->id,
            'end_date' => $bonus['end_date'],
            'user_limited' => $bonus['user_limited'],
            'channel_limited' => $bonus['channel_limited'],
        );

        if (isset($bonus['bonus_use_limit'])) {
            $attr['bonus_use_limit'] = $bonus['bonus_use_limit'];
        }

        if (isset($bonus['used'])) {
            $attr['used'] = $bonus['used'];
        }

        if (isset($bonus['order_id'])) {
            $attr['order_id'] = $bonus['order_id'];
        }

        if (isset($bonus['created'])) {
            $attr['created'] = $bonus['created'];
        } else {
            $attr['created'] = time();
        }

        $info = CustomerBonus::model()->find('bonus_type_id=:bonus_type_id and end_date >:current_date and customer_phone = :customer_phone and order_id=0 order by created desc',array(':bonus_type_id'=>$bonus['id'],':current_date'=>date("Y-m-d H:i:s"),':customer_phone'=>$phone));
		if(!empty($info)){
			$result = CustomerBonus::model()->updateByPk($info->attributes['id'],$attr,'bonus_type_id=:bonus_type_id and customer_phone = :customer_phone',array(':bonus_type_id'=>$bonus['id'],':customer_phone'=>$phone));
			if($result){
				$attr2 = array(
				'bonus_type_id' => $info['bonus_type_id'],
				'channel' => $info['channel'],
				'sn_type' => $info['sn_type'],
				'bonus_sn' => $info['bonus_sn'],
				'parity_bit' => $info['parity_bit'],
				'customer_id' => $info['customer_id'],
				'balance' => $info['balance'],
				'money' => $info['money'],
				'back_type' => $info['back_type'],
				'customer_phone' => $info['customer_phone'],
				'create_by' => $info['create_by'],
				'end_date' => $info['end_date'],
				'user_limited' => $info['user_limited'],
				'channel_limited' => $info['channel_limited'],
				
				);			
				$attr2['bonus_use_limit'] = $info['bonus_use_limit'];
				$attr2['used'] = $info['used'];
				$attr2['order_id'] = $info['order_id'];
				$attr2['created'] = $info['created'];
				
				$bonusLog = CustomerBonusLog::model()->addCustomerBonusLog($attr2);
				return $info->attributes['id'];
			} else {
				return false;
			}
		} else {
			$customerBonus->attributes = $attr;
			if ($customerBonus->insert()) {
				return $customerBonus->id;
			} else {
				return false;
			}		
		}   
    }


    /**
     * 检查有效的优惠劵
     * @param $phone 手机号
     * @param $status 呼叫类型
     * @param int $order_id $order_id为0的时候判断优惠劵是否可用， order_id不为0时，判断优惠劵是否已经被占用
     * @return bool|CActiveRecord
     * author mengtianxue
     */
    public function CheckCustomerBonusUsed($phone, $status, $order_id = 0)
    {
        //获取历史订单
        $customer_order_report = CustomerOrderReport::model()->getCustomerOrder(array('phone' => $phone));

        $params = array();
        $criteria = new CDbCriteria();
        $criteria->condition = 'id > 0';

        $criteria->addCondition('customer_phone = :customer_phone');
        $params[':customer_phone'] = $phone;

        $criteria->addCondition('used = 0');

        if ($status == 1 || $status == 3) {
            //400订单
            $criteria->addCondition('channel_limited = 2');
        }

        if ($customer_order_report) {
            //老客户
            $criteria->addCondition('user_limited != 2');
        } else {
            //新客户
            $criteria->addCondition('user_limited != 1');
        }

        if ($order_id == 0) {
            $criteria->addCondition('order_id = 0');
        } else {
            $criteria->addCondition('order_id = :order_id');
            $params[':order_id'] = $order_id;
        }

        $criteria->addCondition('created < :created');
        $params[':created'] = time() - 60;

        $criteria->addCondition('end_date > :end_date');
        $params[':end_date'] = date('Y-m-d H:i:s');

        $criteria->order = 'user_limited desc, balance desc, id desc';
        $criteria->params = $params;

        $customerBonus = CustomerBonus::model()->findAll($criteria);

        if ($customerBonus) {
            return $customerBonus;
        } else {
            return false;
        }
    }

    /**
     * 查看是否有已经标记的订单
     * @param $phone
     * @param $order_id
     * @return array|bool|CActiveRecord|mixed|null
     * author mengtianxue
     */
    public function getBonusUsed($phone, $order_id)
    {
        $customerBonus = Yii::app()->db_finance->createCommand()
            ->select("bonus_sn, balance, use_money, money, count(1) as count")
            ->from('{{customer_bonus}}')
            ->where("customer_phone = :customer_phone and order_id = :order_id and used = 0",
                array(':customer_phone' => $phone, ':order_id' => $order_id))
            ->queryRow();
        if (!empty($customerBonus) && $customerBonus['count'] > 0) {
            return $customerBonus;
        } else {
            return false;
        }
    }

    /**
     * 只有一个优惠劵
     * @param $phone
     * @param $order_id
     * @return array|bool|CActiveRecord|mixed|null
     * author mengtianxue
     */
    public function getBonusUse($phone, $order_id)
    {
        $customerBonus = $this->getSingleCustomerBonus($phone, $order_id);
        if (!empty($customerBonus)) {
            return $customerBonus;
        } else {
            return false;
        }
    }

    /**
     * 获取某个订单的绑定的优惠券
     *
     * @param $phone
     * @param $orderId
     * @param int $bonusSn
     * @param bool $used 如果为true 返回的就是使用了的优惠券
     * @return array|CActiveRecord|mixed|null
     */
    public function getSingleCustomerBonus($phone, $orderId = 0, $bonusSn = 0, $used = false)
    {
        $criteria = new CDbCriteria();
        if (!empty($phone)) {
            $criteria->compare('customer_phone', $phone);
        }
        if (!empty($orderId)) {
            $criteria->compare('order_id', $orderId);
        }
        if (!empty($bonusSn)) {
            $criteria->compare('bonus_sn', $bonusSn);
        }
        if ($used) {
            $criteria->addCondition('used > 0 ');
        } else {
            $criteria->addCondition('used = 0');
        }
        return self::model()->find($criteria);
    }


    /**
     * 绑定订单号到优惠券里面
     * @param $phone
     * @param $bonus_sn
     * @param $order_id
     * @return bool
     * @auther mengtianxue
     */
    public function couponUse($phone, $bonus_sn, $order_id)
    {
        $customerBonus = $this->getSingleCustomerBonus($phone, 0, $bonus_sn);
        if ($customerBonus) {
            $customer_bonus = $this->updateByPk($customerBonus['id'],
                array('order_id' => $order_id, 'used' => time()));
            if ($customer_bonus) {
                return $customerBonus;
            }
        }
        return false;
    }

    /**
     * 检查订单是否已经使用了优惠劵
     * @param $order_id
     * @param $type 0 是结账了的优惠劵才算是使用，否则占用了就算是使用
     * @return bool
     * author mengtianxue
     */
    public function checkedBonusUseByOrderID($order_id, $type = 0)
    {
        $needUsed = false;
        if ($type == 0) {
            $needUsed = true;
        }
        $customerBonus = $this->getSingleCustomerBonus(0, $order_id, 0, $needUsed);
        if (!empty($customerBonus)) {
            return $customerBonus;
        } else {
            return false;
        }
    }

    /**
     * 优惠劵统计
     * @param $phone
     * @param $bonus_sn
     * @param $start_time
     * @param $end_time
     * @return mixed
     * author mengtianxue
     */
    public function getBonusUsedSummary($phone, $bonus_sn = 0, $start_time = 0, $end_time = 0)
    {
        $where = 'customer_phone = :customer_phone';
        $params = array(':customer_phone' => $phone);
        if ($bonus_sn != 0) {
            $where .= ' and bonus_sn = :bonus_sn';
            $params[':bonus_sn'] = $bonus_sn;
        }

        if ($start_time != 0) {
            $where .= ' and used >= :start_time';
            $params[':start_time'] = $start_time;
        }

        if ($end_time != 0) {
            $where .= ' and used <= :end_time';
            $params[':end_time'] = $end_time;
        }

        $customerBonus = Yii::app()->db_finance->createCommand()
            ->select("customer_phone, count(1) as totle_num, sum(IF((order_id != 0),1,0)) as used_num")
            ->from('{{customer_bonus}}')
            ->where($where, $params)
            ->queryRow();
        return $customerBonus;
    }


    /**
     * 高德直接绑定优惠劵，用户可重复提交
     * @param $phone
     * @return bool
     * author mengtianxue
     */
    public function bonusOldCode($phone, $activityId)
    {
        $activity = BActivityBonus::model()->getActivityByKey($activityId);
        if (!$activity || !$activity->nowInActivity()) {
            return false;
        }
        $bonusSn = $activity->getBonusSn();
        $bonus_id = explode(',', $bonusSn);
        $addCustomerBonus = BonusLibrary::model()->ActivitiesBind($phone, $bonus_id);
        return true;
    }

    /**
     * 检查是否已经绑定优惠劵
     * @param $phone
     * @return bool
     * author mengtianxue
     */
    public function customerPhoneExists($phone)
    {
        return CustomerBonus::model()->exists('used = 0 AND customer_phone=:customer_phone', array(
            ':customer_phone' => $phone));
    }

    /**
     * 获取全部绑定的优惠劵
     * @param $type
     * @param $date
     * @return mixed
     * author mengtianxue
     */
    public function getBindingByID($date, $type = 0)
    {
        if ($type == 0) {
            $id = $date->id;
        } else {
            $id = $date;
        }

        $cache_key = 'Binding_' . md5($id);
        $binging = Yii::app()->cache->get($cache_key);
        $bonusCount = json_decode($binging, true);
        if (!$bonusCount) {
            $bonusCount = self::model()->countByAttributes(array('bonus_type_id' => $id));
            Yii::app()->cache->set($cache_key, json_encode($bonusCount), 3600);
        }
        return $bonusCount;
    }


    /**
     * 获取全部使用的优惠劵
     * @param $date
     * @param $type $type=0时未对象，否则未id
     * @return mixed
     * author mengtianxue
     */
    public function getUsedByID($date, $type = 0)
    {
        if ($type == 0) {
            $id = $date->id;
        } else {
            $id = $date;
        }

        $cache_key = 'bonus_used_' . md5($id);
        $binging = Yii::app()->cache->get($cache_key);
        $bonusCount = json_decode($binging, true);
        if (!$bonusCount) {
            $criteria = new CDbCriteria();
            $criteria->compare('bonus_type_id', $id);
            $criteria->addCondition('order_id != 0');
            $bonusCount = self::model()->count($criteria);
            Yii::app()->cache->set($cache_key, json_encode($bonusCount), 3600);
        }
        return $bonusCount;
    }

    /**
     * 获取优惠劵所有的分类
     * @param $id 规则分类id
     * @return array
     * author mengtianxue
     */
    public function getBonusCodeList($id = 0)
    {
        $list = array();
        $cache_key = 'bonus_code';
        $bonusCode = Yii::app()->cache->get($cache_key);
        if (!$bonusCode) {
            $data = BonusCode::model()->findAll();
            if (!empty($data)) {
                foreach ($data as $row) {
                    $list[$row->id] = $row->name;
                }
            }
            Yii::app()->cache->set($cache_key, json_encode($list), 600);
        } else {
            $list = json_decode($bonusCode, true);
        }
        if ($id != 0) {
            return $list[$id];
        } else {
            return $list;
        }


    }

    /**
     * 获取实体卡张数
     * @param $order_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getBonusNumByOrderID($order_id)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('order_id', $order_id);
        $criteria->compare('sn_type', BonusCode::SN_TYPE_COUNPON);
        $number = self::model()->count($criteria);
        return $number;
    }


    /**
     * 返回订单绑定的优惠券数量
     *
     * @param $phone
     * @param $order_id
     * @return array|bool|CActiveRecord|mixed|null 默认返回绑定实体卡的数量
     * author mengtianxue
     */
    public function getBonusUseCount($phone, $order_id, $snType = 2)
    {
        $customerBonus = Yii::app()->db_finance->createCommand()
            ->select("count(1) as count, sum(money) as money")
            ->from('{{customer_bonus}}')
            ->where("customer_phone = :customer_phone and order_id = :order_id and sn_type = :snType",
                array(':customer_phone' => $phone,
                    ':order_id' => $order_id,
                    ':snType' => $snType,
                ))
            ->queryRow();

        if (!empty($customerBonus)) {
            return $customerBonus;
        } else {
            return $customerBonus;
        }
    }


    /**
     * 获取一个订单使用的优惠劵
     * @param $phone
     * @param $order_id
     * @return array|bool|CActiveRecord|mixed|null
     * author mengtianxue
     */
    public function getBonusUseByOrderId($phone, $order_id)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('customer_phone', $phone);
        $criteria->compare('order_id', $order_id);
        $customerBonus = self::model()->findAll($criteria);
        if (!empty($customerBonus)) {
            return $customerBonus;
        } else {
            return false;
        }
    }


    public function checkedBonusByPhone($phone, $bonus_id)
    {
        $time = time() - 60;

        $params = array();
        $criteria = new CDbCriteria();

        $criteria->addCondition('customer_phone = :customer_phone');
        $params[':customer_phone'] = $phone;

        $criteria->addCondition('created > :created');
        $params[':created'] = $time;
        $criteria->params = $params;

        $criteria->addInCondition('bonus_type_id', $bonus_id);

        $customerBonus = CustomerBonus::model()->find($criteria);

        if ($customerBonus) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 广发银行 获取用户还有固定优惠码张数
     * @param $bonus_sn
     * @param $phone
     * @return mixed
     * @auther mengtianxue
     */
    public function getBonusNum($bonus_sn, $phone)
    {
        $number = self::model()->countByAttributes(
            array(
                'bonus_sn' => $bonus_sn,
                'customer_phone' => $phone,
                'order_id' => 0));
        return $number;
    }

    /**
     * 回滚优惠券的使用状态
     *
     * @param $order
     * @param $userPhone
     */
    public function  refundCustomerBonus($userPhone, $order)
    {
        $format = "回退优惠券状态|orderId|%s|userPhone|%s|result|%s";
        $orderId = $order['order_id'];
        //一个用户只能有一个电子优惠券,但是可以让司机给绑 N 个贵宾卡
        $customerBonusList = $this->getCustomerBonus($userPhone, $orderId, '');
        if (empty($customerBonusList)) {
            EdjLog::info(sprintf($format, $orderId, $userPhone, 'empty'));
        } else {
            foreach ($customerBonusList as $customerBonus) {
                //修改金额
                $params = array(
                    'used' => 0,
                    'use_money' => 0,
                    'money' => $customerBonus['money'] + $customerBonus['use_money'],
                );
                $ret = self::model()->updateAll($params,
                    'customer_phone = :customer_phone and order_id = :order_id',
                    array(
                        ':customer_phone' => $userPhone,
                        ':order_id' => $orderId,
                    )
                );
                //修改状态,只实体卡需要修改
                $snType = $customerBonus['sn_type'];
                $bonusSn = $customerBonus['bonus_sn'];
                if (BonusCode::SN_TYPE_COUNPON == $snType) {
                    // 实体卡由用户给予司机，不能删除绑定，不然会依然从客户余额里面扣除
                    //1.删除 t_customer_bonus 里面的绑定
                    $this->deleteBonusByOrderIdAndBonusSn($bonusSn, $orderId);
                    //2.修改 t_order_ext 里面的值
                    $bonusMoney = abs($customerBonus['money']);
                    OrderExt::model()->updateIncome($orderId, $bonusMoney * -1, -1);
                    //3.修改 t_bonus_library 里面的 status 为未绑定
                    BonusLibrary::model()->updateStatus($bonusSn, BonusLibrary::STATUS_NO_USE);
                }
                if ($ret) {
                    EdjLog::info(sprintf($format, $orderId, $userPhone, 'success'));
                } else {
                    EdjLog::info(sprintf($format, $orderId, $userPhone, 'fail'));
                }
            }
        }
        //更新 order_ext 表里面得 coupon_money,退单重结回到原始状态
        $orderExt = OrderExt::model()->findByPk($orderId);
        if ($orderExt && !empty($orderExt->coupon_money)) {
            $orderExt->coupon_money = 0;
            if (!$orderExt->update()) {
                EdjLog::info(' update order_ext error %s ', serialize($orderExt));
            }
        }
    }

    public function deleteBonusByOrderIdAndBonusSn($bonusSn, $orderId)
    {
        $customerBonusList = $this->getCustomerBonus('', $orderId, $bonusSn);
        if (empty($customerBonusList)) {
            return true;
        } else {
            foreach ($customerBonusList as $item) {
                $item->delete();
            }
        }
    }

    /**
     * 返回用户某个订单的优惠券
     *
     * @param string $phone
     * @param string $orderId
     * @param string $bonusSn
     * @return mixed
     */
    public function getCustomerBonus($phone = '0', $orderId = '0', $bonusSn = '')
    {
        $criteria = new CDbCriteria;
        $criteria->compare('bonus_sn', $bonusSn);
        $criteria->compare('order_id', $orderId);
        $criteria->compare('customer_phone', $phone);
        return self::model()->findAll($criteria);
    }


    /**
     * 获取一个优惠劵的使用金额
     * @param $bonus_type_id
     * author cuiluzhe
     */
    public function getUsedMoneyByID($data)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('bonus_type_id', $data->id);
        if ($data['back_type'] == 1) {//按消费金额返还
            $criteria->select = 'sum(use_money) as use_money';
            $customerBonus = self::model()->find($criteria);
            $use_money = empty($customerBonus) ? 0 : $customerBonus->use_money;
        } else {
            $criteria->select = 'sum(balance) as use_money';
            $criteria->addCondition('order_id > 0');
            $customerBonus = self::model()->find($criteria);
            $use_money = empty($customerBonus) ? 0 : $customerBonus->use_money;
        }
        if (isset($use_money)) {
            return $use_money;
        }
        return 0;
    }


    public static function getBonusBindList($ids)
    {
        $criteria = new CDbCriteria;
        $criteria->addInCondition("bonus_sn", $ids);
        $criteria->select = 'bonus_sn,order_id,updated';
        self::$db = Yii::app()->db_readonly;
        $result = self::model()->findAll($criteria);
        self::$db = Yii::app()->db_readonly;
        return $result;
    }

    /**
     * 获取一个优惠劵的余额
     * @param $bonus_type_id
     * author cuiluzhe
     */
    public function getMoneyByID($data)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('bonus_type_id', $data->id);
        $criteria->select = 'sum(money) as money';
        $customerBonus = self::model()->find($criteria);
        $money = empty($customerBonus) ? 0 : $customerBonus->money;
        if (isset($money)) {
            return $money;
        }
        return 0;
    }

    public function getTotalMoneyByIds($ids)
    {
        $total = 0;
        foreach ($ids as $id) {
            $data = BonusCode::model()->findByPk($id);
            if ($data && isset($data->id)) {
                $use_money = $this->getUsedMoneyByID($data);
                if (isset($use_money)) {
                    $total += $use_money;
                }
            }
        }
        return $total;
    }

    public function getCustomerBonusList($id, $begin, $end)
    {
        $criteria = new CDbCriteria;
        if (isset($id) && !empty($id)) {
            $criteria->addCondition("bonus_type_id=" . $id);
        }
        $criteria->addCondition("order_id>0");
        if (isset($begin) && !empty($begin)) {
            $begin_int = strtotime($begin);
            $criteria->addCondition("used >= " . $begin_int);
        }
        if (isset($end) && !empty($end)) {
            $end_int = strtotime($end);
            $criteria->addCondition("used <= " . $end_int);
        }
        return new CActiveDataProvider($this, array(
            'pagination' => array('pageSize' => 20),
            'criteria' => $criteria,
        ));
    }

    public function getCustomerBonusListForExport($id, $begin, $end)
    {
        $criteria = new CDbCriteria;
        if (isset($id) && !empty($id)) {
            $criteria->addCondition("bonus_type_id=" . $id);
        }
        $criteria->addCondition("order_id>0");
        if (isset($begin) && !empty($begin)) {
            $criteria->addCondition("used >= " . $begin);
        }
        if (isset($end) && !empty($end)) {
            $criteria->addCondition("used <= " . $end);
        }
        $criteria->order = 'used desc';
        return CustomerBonus::model()->findAll($criteria);
    }

    public function getCustomerBonusBindList($id, $begin, $end)
    {
        $criteria = new CDbCriteria;
        if (isset($id) && !empty($id)) {
            $criteria->addCondition("bonus_type_id=" . $id);
        }
        if (isset($begin) && !empty($begin)) {
            $begin_int = strtotime($begin);
            $criteria->addCondition("created >= " . $begin_int);
        }
        if (isset($end) && !empty($end)) {
            $end_int = strtotime($end);
            $criteria->addCondition("created <= " . $end_int);
        }
        return new CActiveDataProvider($this, array(
            'pagination' => array('pageSize' => 50),
            'criteria' => $criteria,
        ));
    }


    /**删除手机号对应的未使用的新客优惠码
     * @param $phone
     */
    public function deleteNewCustomerBonus($phone, $bonus_id)
    {
        $result = false;
        try {
            $criteria = new CDbCriteria;
            $criteria->addCondition("customer_phone=:customer_phone");
            $criteria->params[':customer_phone'] = $phone;
            $criteria->addCondition("bonus_type_id=:bonus_type_id");
            $criteria->params[':bonus_type_id'] = $bonus_id;
            $criteria->addCondition("order_id=0");
            $customerBonusList = self::model()->findAll($criteria);
            if (empty($customerBonusList)) {
                $result = true;
            } else {
                foreach ($customerBonusList as $item) {
                    $item->delete();
                }
                $result = true;
            }
        } catch (Exception $e) {
            EdjLog::error($e);
            $result = false;
        }
        return $result;
    }


    /**查询手机号对应的未使用的优惠码
     * @param $phone
     */
    public function selectCustomerBonusNum($card_sn, $club_sn, $date_start, $date_end)
    {
        $data = array();
        try {
            $criteria = new CDbCriteria;
            $criteria->select = 'DISTINCT(customer_phone)';
            $criteria->addBetweenCondition("used", $date_start, $date_end);
            $criteria->addInCondition("bonus_sn", array($card_sn, $club_sn));
            $criteria->addCondition("order_id!=0");
            $result = self::model()->findAll($criteria);
            if ($result) {
                foreach ($result as $da) {
                    $data[] = $da->customer_phone;
                }
            }

        } catch (Exception $e) {
            EdjLog::error($e->getMessage());
            $data = array();
        }
        return $data;
    }


    /**查询手机号对应的未使用的优惠码
     * @param $phone
     */
    public function selectPhoneList($card_sn, $club_sn, $phone)
    {
        $result = array();
        try {
            $criteria = new CDbCriteria;
            $criteria->select = 'customer_phone,SUM(CASE WHEN bonus_sn=' . $card_sn . ' and order_id=0 THEN 1 ELSE 0 END) as channel,
SUM(CASE WHEN bonus_sn=' . $club_sn . '  and order_id=0  THEN 1 ELSE 0 END) as use_money';
            $criteria->addInCondition("customer_phone", $phone);
            $criteria->addInCondition("bonus_sn", array($card_sn, $club_sn));
            $criteria->group = 'customer_phone';
            $result = self::model()->findAll($criteria);

        } catch (Exception $e) {
            EdjLog::error($e->getMessage());
            $result = array();
        }
        return $result;
    }

    /**
     * 给出phone和sn，返回order_id
     * @param $phone
     * @param $sn
     * @return string|null
     * author hesongtao
     */
    public static function phoneBonusCheckUser($phone, $sn)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'order_id';
        $criteria->condition = ' customer_phone=:customer_phone and bonus_sn=:bonus_sn';
        $criteria->order = 'id desc';
        $criteria->limit = 1;
        $criteria->params = array(
            ':customer_phone' => $phone,
            ':bonus_sn' => $sn
        );

        $customerBonus = CustomerBonus::model()->find($criteria);

        if (!$customerBonus) {
            return null;
        } else {
            return $customerBonus->order_id;
        }
    }


    /**
     * 检查手机号是否绑定过优惠劵
     * @param $phone
     * @return bool
     */
    public function checkBind($phone)
    {
        return CustomerBonus::model()->exists('customer_phone=:customer_phone', array(':customer_phone' => $phone));
    }

    /**
     * 根据bonus_id查询绑定记录
     * @param $bonus_type_id
     * @return array
     */
    public function getBonusByBonusTypeId($bonus_type_id, $date_start = '', $date_end = '')
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'DISTINCT bonus_sn';
        $criteria->addCondition("bonus_type_id=:bonus_type_id");
        $criteria->params[':bonus_type_id'] = $bonus_type_id;
        if (!empty($date_start) && !empty($date_end)) {
            $criteria->addCondition("created>=:date_start");
            $criteria->params[':date_start'] = $date_start;
            $criteria->addCondition("created<:date_end");
            $criteria->params[':date_end'] = $date_end;
        }


        $criteria->order = 'id DESC';
        return self::model()->findAll($criteria);
    }


    /**
     * 根据bonus_sn查询绑定记录
     * @param $bonus_sn
     * @return int
     */
    public function getBonusBindByOneDay($bonus_sn, $date_start, $date_end, $type = 1)
    {
        $criteria = new CDbCriteria;
        $criteria->addCondition("bonus_sn=:bonus_sn");
        $criteria->params[':bonus_sn'] = $bonus_sn;
        $criteria->addCondition("created>=:date_start");
        $criteria->params[':date_start'] = $date_start;
        $criteria->addCondition("created<:date_end");
        $criteria->params[':date_end'] = $date_end;

        if ($type == 1) {
            $criteria->addCondition("updated>=:date_start");
            $criteria->params[':date_start'] = $date_start;
            $criteria->addCondition("updated<:date_end");
            $criteria->params[':date_end'] = $date_end;
        }
        return self::model()->count($criteria);
    }
}
