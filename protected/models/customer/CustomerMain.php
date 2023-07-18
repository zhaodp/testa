<?php

/**
 * This is the model class for table "{{customer_main}}".
 *
 * The followings are the available columns in table '{{customer_main}}':
 * @property integer $id
 * @property string $name
 * @property integer $gender
 * @property string $birthday
 * @property string $phone
 * @property string $email
 * @property string $imei
 * @property string $backup_phone
 * @property integer $city_id
 * @property string $credit
 * @property string $activity
 * @property string $amount
 * @property string $coupon
 * @property integer $type
 * @property string $company
 * @property string $vip_card
 * @property integer $vip_main
 * @property string $vip_card_balance
 * @property string $invoice_title
 * @property string $invoice_remark
 * @property integer $bill_receive_mode
 * @property integer $status
 * @property integer $is_verify
 * @property string $channel
 * @property string $remark
 * @property string $operator
 * @property string $create_time
 * @property string $update_time
 * @property string $last_login
 */
class CustomerMain extends CActiveRecord
{
    /**
     * 个人用户
     */
    const TYPE_INDIVIDUAL = 1;

    /**
     * 企业用户
     */
    const TYPE_ENTERPRISE = 2;

    /**
     * 用户来源400
     */
    const CHANNEL_400 = 1;

    /**
     * 用户来源APP
     */
    const CHANNEL_APP = 2;

    /**
     * 用户来源推广
     */
    const CHANNEL_POPULARIZE = 3;

    /**
     * 用户状态（正常）
     */
    const STATUS_NORMAL = 1;

    /**
     * 用户状态（屏蔽）
     */
    const STATUS_SHIELD = 2;

    /**
     * 用户状态（删除）
     */
    const STATUS_DELETE = 3;

    /**
     * 用户性别（男）
     */
    const GENDER_MAN = 1;

    /**
     * 用户性别（女）
     */
    const GENDER_WOMAN = 2;

    /**
     * 账单接收方式 （短信）
     */
    const BILL_RECEIVE_SMS = 1;

    /**
     * 账单接收方式 （邮箱）
     */
    const BILL_RECEIVE_MAIL = 2;

    /**
     * 用户初始状态
     */
    const STATUS_INITIAL = 0;

    /**
     * 用户已验证
     */
    const STATUS_CHECKED = 1;

    //APPLE审核客户端测试账户，段永超手机号
    const APPLE_TEST_ACCOUNT = '18500459981';
    const APPLE_TEST_MSG = '8521';

    public static $type_dict = array(
        self::TYPE_INDIVIDUAL => '个人用户',
        self::TYPE_ENTERPRISE => '企业用户'
    );

    public static $channel_dict = array(
        self::CHANNEL_400 => '400',
        self::CHANNEL_APP => 'APP',
        self::CHANNEL_POPULARIZE => '推广',
    );

    public static $status_dict = array(
        self::STATUS_NORMAL => '正常',
        self::STATUS_SHIELD => '屏蔽',
        self::STATUS_DELETE => '删除',
    );

    public static $gender_dict = array(
        self::GENDER_MAN => '男',
        self::GENDER_WOMAN => '女',
    );

    public static $bill_receive = array(
        self::BILL_RECEIVE_SMS => '短信',
        self::BILL_RECEIVE_MAIL => '邮件',
    );

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CustomerMain the static model class
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
        return '{{customer_main}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('phone, city_id, create_time', 'required'),
            array('gender, city_id, type, bill_receive_mode, status, is_verify, vip_main', 'numerical', 'integerOnly' => true),
            array('name, email, company, vip_card, invoice_title, invoice_remark, operator', 'length', 'max' => 50),
            array('wealth', 'length', 'max' => 8),
            array('phone, backup_phone', 'length', 'max' => 32),
            array('imei, app_ver', 'length', 'max' => 20),
            array('car_num', 'length', 'max' => 20),
            array('credit, activity, amount, coupon', 'length', 'max' => 10),
            array('channel', 'length', 'max' => 30),
            array('remark', 'length', 'max' => 100),
            array('address', 'length', 'max' => 100),
            array('birthday, update_time, last_login', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, gender, birthday, phone, email, imei, backup_phone, city_id, credit, activity, amount, coupon, type, company, vip_card, invoice_title, invoice_remark, bill_receive_mode, status, is_verify, channel, remark, address, operator, create_time, update_time, last_login, car_num', 'safe', 'on' => 'search'),
            array('email', 'email'),
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
            'id' => 'ID',
            'name' => '姓名',
            'gender' => '性别',
            'birthday' => '生日',
            'phone' => '手机号',
            'email' => '邮箱',
            'imei' => 'Imei',
            'backup_phone' => '备用电话',
            'city_id' => '城市',
            'credit' => '信用等级',
            'activity' => '活跃度',
            'amount' => '帐户余额',
            'coupon' => '优惠券',
            'car_num' => '车牌号',
            'type' => '类型',
            'company' => '企业名称',
            'vip_card' => 'vip卡号',
            'vip_main' => '是否VIP主账户', //1表示vip主账户，0表示默认子账户
            'vip_card_balance' => 'VIP卡余额',
            'invoice_title' => '发票抬头',
            'invoice_remark' => '发票备注',
            'bill_receive_mode' => '账单接受方式', //0 默认 1 短信 2 邮件 3短信+邮件
            'status' => '用户状态',
            'is_verify' => '用户是否已认证', //0.默认 1.已认证
            'channel' => '用户来源',
            'remark' => '备注',
            'address' => '常用代驾目的地',
            'operator' => '操作人',
            'wealth' => '财富e币',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'app_ver' => '客户端版本',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        if ($this->name)
            $criteria->compare('name', $this->name, true);
        if ($this->gender > 0)
            $criteria->compare('gender', $this->gender);
        $criteria->compare('birthday', $this->birthday, true);
        if ($this->phone)
            $criteria->compare('phone', trim($this->phone), true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('imei', $this->imei, true);
        $criteria->compare('backup_phone', $this->backup_phone, true);
        if ($this->city_id != 0)
            $criteria->compare('city_id', $this->city_id);
        $criteria->compare('credit', $this->credit, true);
        $criteria->compare('activity', $this->activity, true);
        $criteria->compare('amount', $this->amount, true);
        $criteria->compare('coupon', $this->coupon, true);
        if ($this->type > 0)
            $criteria->compare('type', $this->type);
        $criteria->compare('company', $this->company, true);
        if ($this->vip_card)
            $criteria->compare('vip_card', $this->vip_card, true);
        $criteria->compare('invoice_title', $this->invoice_title, true);
        $criteria->compare('invoice_remark', $this->invoice_remark, true);
        $criteria->compare('bill_receive_mode', $this->bill_receive_mode);
        if ($this->status)
            $criteria->compare('status', $this->status);
        $criteria->compare('is_verify', $this->is_verify);
        if ($this->channel)
            $criteria->compare('channel', $this->channel);
        $criteria->compare('remark', $this->remark, true);
        $criteria->compare('operator', $this->operator, true);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('update_time', $this->update_time, true);
        $criteria->order = 'id DESC';
        return new CActiveDataProvider($this,
            array(
                'pagination' => array(
                    'pageSize' => 30
                ),
                'criteria' => $criteria,
            ));
    }

    /**
     * 初始化客户电话
     * @param string $phone 手机号码
     */
    public function initCustomer($phone)
    {
        $phone = trim($phone);
        $customer = $this->getCustomer($phone);
        if (!$customer) {
            $customer = new CustomerMain();
            $city_id = Helper::PhoneLocation($phone);
            $customer->attributes = array(
                'phone' => $phone,
                'city_id' => $city_id,
                'operator' => 'system',
                'create_time' => date('Y-m-d H:i:s')
            );
            $customer->insert();
            RCustomerInfo::model()->load($customer->id);
        }

        return $customer;
    }

    /**
     * 判断是不是vip
     *
     * @param $phone  客户手机号
     * @return bool
     */
    public function isVip($phone)
    {
        $ret = VipService::service()->isVip($phone);
        return is_array($ret);
    }

    /**
     * 用户的信息
     * @param string $phone 手机号码
     */
    public function getCustomer($phone)
    {
        $customer = self::model()->find('phone=:phone', array(':phone' => $phone));
        return $customer;
    }

    /**
     * 用户的信息
     * @param integer $id
     * @param string $phone 手机号码
     */
    public function getCustomerByAttr($phone)
    {
        $customer = self::model()->find('phone=:phone', array(':phone' => $phone));
        return $customer;
    }

    /**
     * 更新用户的称谓
     * @param string $phone 手机号码
     * @param string $name 称谓
     */
    public function updateCustomerName($phone, $name)
    {
        $customer = $this->getCustomer($phone);
        $customer->name = $name;
        if ($customer->update()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 更新用户的余额
     * @param string $phone 手机号码
     * @param string $balance 余额
     */
    public function updateCustomerBalance($phone, $balance)
    {
        $customer = $this->getCustomer($phone);
        $customer->balance += $balance;
        if ($customer->update()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @author aiguoxin
     *   更新客户城市，按城市推送push需要用到
     */
    public function updateCustomerCity($phone, $city_id)
    {
        $customer = $this->getCustomer($phone);
        $customer->city_id = $city_id;
        if ($customer->update()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 用户的手机身份验证token
     * @param string $passwd
     * @param string $phone 手机号码
     */
    public function authenticate($phone, $passwd, $udid, $macaddress)
    {
        //验证密码
        $validate = CustomerPass::model()->validatePerLoginPasswd($phone, $passwd);
        if ($validate == CustomerPass::CUSTOMERPASS_PASS) {

            //获取AuthToken
            $token = CustomerToken::model()->createAuthtoken($phone, $udid, $macaddress);

            //修改customerMain 最后登陆时间
            $this->updateLastLoginAndStatus($phone);

            //返回信息
            $ret = array(
                'code' => 0,
                'token' => $token,
                'message' => '登录成功');
        } else {
            switch ($validate) {
                case CustomerPass::CUSTOMERPASS_UNPERLOGIN:
                    $message = '未进行预登录';
                    break;
                case CustomerPass::CUSTOMERPASS_PASS_ERROR:
                    $message = '密码错误';
                    break;
                case CustomerPass::CUSTOMERPASS_PASS_EXPIRED:
                    $message = '预登录密码过期';
                    break;
            }
            $ret = array(
                'code' => 1,
                'error' => $validate,
                'message' => $message);
        }

        return $ret;
    }

    /**
     * 登陆的时候  修改last_login和初次登录修改status
     * Enter description here ...
     * @param unknown_type $phone
     */
    public function updateLastLoginAndStatus($phone)
    {
        $customer = $this->getCustomer($phone);
        if (!empty($customer)) {
            $customer->last_login = date('Y-m-d H:i:s');
            if ($customer->is_verify == self::STATUS_INITIAL)
                $customer->is_verify = self::STATUS_CHECKED;

            if ($customer->update()) {
                RCustomerInfo::model()->load($customer->id);
                return true;
            }
        } else {
            $this->initCustomer($phone);
            return true;
        }
        return false;
    }

    public function beforeSave()
    {
        $this->update_time = date('Y-m-d H:i:s', time());
        if ($this->getIsNewRecord()) {
            $this->status = self::STATUS_NORMAL;
            $this->create_time = date('Y-m-d H:i:s', time());
        }
        return parent::beforeSave();
    }


    /**
     * @param $phone
     * @return mixed
     * author mengtianxue
     */
    public function getCustomerInfo($phone)
    {
//        $cache_key = 'customer_info_' . md5($phone);
//        $customer_info = Yii::app()->cache->get($cache_key);
//        $customer_info = json_decode($customer_info, true);
        $customer_info = RCustomerInfo::model()->getByPhone($phone);
        if (!$customer_info) {
            $customer = Yii::app()->db_readonly->createCommand()
                ->select("*")
                ->from('{{customer_main}}')
                ->where('phone = :phone', array(':phone' => $phone))
                ->queryRow();
            if ($customer) {
                $customer_info = RCustomerInfo::model()->load($customer['id']);
                $customer_info = RCustomerInfo::model()->get($customer['id']);
            }
//            else {
//                $customer_info = array('phone' => $phone);
//            }
//            Yii::app()->cache->set($cache_key, json_encode($customer_info), 86400);
        }
        if (!$customer_info) {
            EdjLog::info("customer info for phone $phone is empty");
            return false;
        }
        return (object)$customer_info;
    }

    public function forceGetCustomerInfo($phone)
    {
        $customer = Yii::app()->db_readonly->createCommand()
            ->select("*")
            ->from('{{customer_main}}')
            ->where('phone = :phone', array(':phone' => $phone))
            ->queryRow();
        if ($customer) {
            RCustomerInfo::model()->load($customer['id']);
            return (object)RCustomerInfo::model()->get($customer['id']);
        } else {
            $customer = $this->initCustomer($phone);//如果没有账户,给创建一个
            if ($customer) {
                RCustomerInfo::model()->load($customer['id']);
                return (object)RCustomerInfo::model()->get($customer['id']);
            }
            EdjLog::info("customer info for phone $phone empty");
            return false;
        }
    }

    /**
     * @param $params
     * @return bool
     * author mengtianxue
     */
    public function updateCustomerInfo($params)
    {
        $phone = $params['phone'];
//        $cache_key = 'customer_info_' . md5($phone);
//        $oldInfo = Yii::app()->cache->get($cache_key);
//        $customer_info = json_decode($oldInfo, true);

        $customer_info = RCustomerInfo::model()->getByPhone($phone);

        if ($customer_info->name == $params['name'] &&
            $customer_info->gender == $params['gender'] &&
            $customer_info->backup_phone == $params['backup_phone'] &&
            $customer_info->address == $params['address'] &&
            $customer_info->car_num == $params['car_num']
        ) {
            return true;
        }

        $ret = $this->updateAll($params, 'phone = :phone', array(':phone' => $phone));
        if ($ret) {
//            Yii::app()->cache->delete($cache_key);
            RCustomerInfo::model()->load($customer_info->id);
            return true;
        }
        return false;
    }

    public function getAppversion($phone)
    {
        $sql = sprintf("select app_ver from t_customer_main where phone='%s' limit 1", $phone);
        return Yii::app()->db_readonly->createCommand($sql)->queryScalar();
    }

    //add by aiguoxin
    public function updateAppversion($phone, $appversion)
    {
        $res = 0;
        //先从redis中获取，如果版本没有变化，直接返回
        $customer_info = $this->getCustomerInfo($phone);
        if ($customer_info && $customer_info->app_ver != $appversion) {
            $sql = "UPDATE `t_customer_main` SET `app_ver` = :appversion WHERE phone = :phone";
            $res = Yii::app()->db->createCommand($sql)->execute(array(
                ':phone' => $phone,
                ':appversion' => $appversion,
            ));
            if ($res) {//更新成功
                //更新redis
                $customer_info->app_ver = $appversion;
            }
        }
        return $res;
    }

    public function getWealth($phone)
    {
        $wealth = 0;
        $wealthObject = Yii::app()->db_readonly->createCommand()
            ->select("wealth")
            ->from('{{customer_main}}')
            ->where('phone = :phone', array(':phone' => $phone))
            ->queryScalar();
        if ($wealthObject) {
            $wealth = $wealthObject;
        }
        //保留整数
        $wealth = round($wealth);
        return $wealth;
    }


    /**
     *   使用e币支付，扣除
     * @param phone :用户电话 wealth:扣除的e币
     */
    public function payForWealth($phone, $wealth)
    {
        $result = array();
        $state = 0;
        $message = '支付成功';
        $result = array(
            'state' => $state,
            'message' => $message,
        );
        $user_wealth = $this->getWealth($phone);
        if ($user_wealth < $wealth) {
            $result['state'] = 1;
            $result['message'] = '剩余e币不足';
            return $result;
        }
        $sql = "UPDATE `t_customer_main` SET `wealth` = wealth-:wealth WHERE phone=:phone and wealth >= :wealth ";
        $res = Yii::app()->db->createCommand($sql)->execute(array(
            ':phone' => $phone,
            ':wealth' => $wealth,
        ));
        if ($res != 1) {
            $result['state'] = 1;
            $result['message'] = '剩余e币不足';
            return $result;
        }
        //记录消费日志
        CustomerWealthLog::model()->addWealthLog($phone, CustomerWealthLog::BILL_TYPE, -$wealth);

        return $result;
    }

    /**
     *   增加e币
     *   type类型，见CustomerWealthLog
     */
    public function addWealth($phone, $type, $wealth)
    {
        //增加e币
        $sql = "UPDATE `t_customer_main` SET `wealth` = wealth+:wealth WHERE phone=:phone";
        $res = Yii::app()->db->createCommand($sql)->execute(array(
            ':phone' => $phone,
            ':wealth' => $wealth,
        ));
        if ($res != 1) {
            return false;
        }
        //添加日志
        $res = CustomerWealthLog::model()->addWealthLog($phone, $type, $wealth);
        if ($res != 1) {
            return false;
        }
        return true;
    }

    /**
     *   现金消费
     */
    public function addWealthForCash($phone, $wealth)
    {
        return $this->addWealth($phone, CustomerWealthLog::CASH_TYPE, $wealth);
    }

    /**
     *   充值
     */
    public function addWealthForRecharge($phone, $wealth)
    {
        return $this->addWealth($phone, CustomerWealthLog::RECHARGE_TYPE, $wealth);
    }

    public static function getInfoByPhone($phone)
    {
        $criteria = new CDbCriteria;
        $criteria->addCondition("phone", $phone);
        return self::model()->find($criteria);
    }
}
