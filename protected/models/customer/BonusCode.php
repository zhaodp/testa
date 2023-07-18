<?php

/**
 * This is the model class for table "{{bonus_code}}".
 *
 * The followings are the available columns in table '{{bonus_code}}':
 * @property string $id
 * @property string $name
 * @property string $rename
 * @property integer $money
 * @property integer $channel
 * @property integer $type
 * @property integer $sn_type
 * @property string $sn_start
 * @property string $sn_end
 * @property integer $issued
 * @property integer $user_limited
 * @property integer $repeat_limited
 * @property integer $channel_limited
 * @property string $effective_date
 * @property string $binding_deadline
 * @property string $end_date
 * @property integer $end_day
 * @property string $coupon_rules
 * @property string $sms
 * @property integer $status
 * @property string $create_by
 * @property string $created
 * @property string $update_by
 * @property string $updated
 * @property string $remark
 * @property string $Introducte
 * @property integer $area_id
 * @property integer $isconsumer
 * @property integer $back_type
 */
class BonusCode extends FinanceActiveRecord
{
    //未审核
    const STATUS_AUDIT = 0;
    //开始使用日间优惠券的客户端版本
    const APP_VER = '5.3.0';
    //已审核
    const STATUS_APPROVED = 1;

    //审核未通过
    const STATUS_NOT_AUDIT = 2;

    //已过期
    const STATUS_EXPIRED = 3;


    //限app使用
    const CHANNEL_LIMITED_APP = 1;

    //不限使用类型
    const CHANNEL_LIMITED_NO_APP = 2;

    /** 实体卡/贵宾卡 */
    const SN_TYPE_COUNPON = 2;
    /** 区域码 */
    const SN_TYPE_RANGE_CODE = 0;
    /** 固定码 */
    const SN_TYPE_FIXED_CODE = 1;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return BonusCode the static model class
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
        return '{{bonus_code}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, money, channel, effective_date, binding_deadline, coupon_rules', 'required'),
            array('money, channel, type, sn_type, user_limited, repeat_limited, channel_limited, end_day, status, area_id, isconsumer, back_type, ismerchants,bonus_use_limit,is_bonus_sn', 'numerical', 'integerOnly' => true),
            array('name, rename', 'length', 'max' => 60),
            array('sn_start, issued, sn_end, channel', 'length', 'max' => 20),
            array('coupon_rules', 'length', 'max' => 1000),
            array('sms', 'length', 'max' => 140),
            array('create_by, update_by', 'length', 'max' => 32),
            array('remark, Introducte', 'length', 'max' => 200),
            array('end_date', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, money, channel, type, sn_type, sn_start, sn_end, issued, user_limited, repeat_limited, channel_limited, effective_date, binding_deadline, end_date, end_day, coupon_rules, sms, status, create_by, created, update_by, updated, remark, area_id, isconsumer, back_type, ismerchants, Introducte, rename,bonus_use_limit,is_bonus_sn', 'safe', 'on' => 'search'),
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
            'name' => '优惠劵名称',
            'rename' => '优惠劵名称(仅供APP展示使用)',
            'money' => '金额',
            'channel' => '发放渠道',
            'type' => '类型',
            'sn_type' => '优惠劵类型',
            'sn_start' => '起始号码',
            'sn_end' => '截止号码',
            'issued' => '生成数量',
            'user_limited' => '用户限制',
            'repeat_limited' => '重复限制',
            'channel_limited' => '渠道限制',
            'effective_date' => '生效日期',
            'binding_deadline' => '绑定截止日期',
            'end_date' => '使用截止时间',
            'end_day' => '使用有效天数',
            'coupon_rules' => '优惠劵位数',
            'sms' => '自定义短信内容',
            'status' => '状态',
            'create_by' => '创建人',
            'created' => '创建时间',
            'update_by' => '更新人',
            'updated' => '更新时间',
            'remark' => '备注',
            'Introducte' => '简洁描述(仅供APP展示使用)',
            'area_id' => '区域ID',
            'isconsumer' => '是否可消费',
            'back_type' => '是否全额返还',
            'ismerchants' => '商家专用',
            'bonus_use_limit' => '使用限制',
            'is_bonus_sn' => '是否使用优惠券'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search($order = NULL)
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('money', $this->money);
        $criteria->compare('channel', $this->channel);
        $criteria->compare('type', $this->type);
        $criteria->compare('sn_type', $this->sn_type);
        $criteria->compare('sn_start', $this->sn_start, true);
        $criteria->compare('sn_end', $this->sn_end, true);
        $criteria->compare('issued', $this->issued);
        $criteria->compare('user_limited', $this->user_limited);
        $criteria->compare('repeat_limited', $this->repeat_limited);
        $criteria->compare('channel_limited', $this->channel_limited);
        $criteria->compare('effective_date', $this->effective_date, true);
        $criteria->compare('binding_deadline', $this->binding_deadline, true);
        $criteria->compare('end_date', $this->end_date, true);
        $criteria->compare('end_day', $this->end_day, true);
        $criteria->compare('coupon_rules', $this->coupon_rules, true);
        $criteria->compare('sms', $this->sms, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('create_by', $this->create_by, true);
        $criteria->compare('created', $this->created, true);
        $criteria->compare('update_by', $this->update_by, true);
        $criteria->compare('updated', $this->updated, true);
        $criteria->compare('remark', $this->remark, true);
        $criteria->compare('area_id', $this->area_id);
        $criteria->compare('isconsumer', $this->isconsumer);
        $criteria->compare('back_type', $this->back_type);
        $criteria->compare('ismerchants', $this->ismerchants);
        $criteria->compare('bonus_use_limit', $this->bonus_use_limit);
        $criteria->compare('is_bonus_sn', $this->is_bonus_sn);
        if ($order !== NULL) {
            $criteria->order = $order;
        }

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function getBonusMerchantsByIdArray($idArray){
         $criteria = new CDbCriteria;
        $criteria->addInCondition('id', $idArray);
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
         ));
    }

    /**
     * 保存之前要更新的字段
     * @return bool
     * author mengtianxue
     */
    public function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->create_by = Yii::app()->user->getId();
                $this->created = date("Y-m-d H:i:s");
            }
            $this->updated = date("Y-m-d H:i:s");
            $this->update_by = Yii::app()->user->getId();
            return true;
        }
        return parent::beforeSave();
    }


    /**
     * 根据id获取优惠劵信息
     * @param $id
     * @param $status
     * @param $sn_type
     * @param $condition
     * @return mixed
     * author mengtianxue
     */
    public function getBonusCodeById($id, $status = null, $sn_type = null, $condition = 'and')
    {
        $criteria = new CDbCriteria();
        $criteria->compare('id', $id);
        if (!is_null($status)) {
            $criteria->compare('status', $status);
        }
        if (!is_null($sn_type)) {
            if ($condition == 'and') {
                $criteria->compare('sn_type', $sn_type);
            } else {
                $criteria->addCondition('sn_type != :sn_type');
                $criteria->params[':sn_type'] = $sn_type;
            }
        }
        return self::model()->find($criteria);
    }


    /**
     * 获取某个字段的值
     * @param $id
     * @param $field_name
     * @return string
     * @auther mengtianxue
     */
    public function getFieldValue($id, $field_name)
    {
        $bonus_code = $this->getBonusCodeById($id, self::STATUS_APPROVED);
        if ($bonus_code) {
            return $bonus_code[$field_name];
        }
        return '';
    }

    /**
     * 修改优惠劵的状态（0.待审核、1。已审核、2.审核不通过、3.删除）
     * @param $id
     * @param $status
     * @return bool
     * author mengtianxue
     */
    public function updateStatus($id, $status)
    {
        $update = $this->model()->updateByPk($id, array('status' => $status));
        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 根据bonusId获取短信内容
     * @param <int> $id
     * @return <string> 短信内容
     * created by liuxiaobo
     */
    public function getSmsById($id)
    {
        $model = self::model()->getBonusCodeById($id);
        if ($model) {
            return $model['sms'];
        }
        return '';
    }

    /**
     * 根据bonusId获取城市信息
     * @param <int> $id
     * @return <string> 城市信息
     * created by liuxiaobo
     */
    public function getCityById($id)
    {
        $citys = array();
        $models = BonusCodeCity::model()->getBonusCodeCityID($id);
        foreach ($models as $model) {
            $citys[] = Dict::item('city', $model['city_id']);
        }
        if (empty($citys)) {
            $citys = array('不限城市');
        }
        return $citys;
    }

    /**
     * 根据bonusId获取使用信息
     * @param <int> $id
     * @return <string> 使用信息
     * created by liuxiaobo
     */
    public function getUseInfoById($id)
    {
        $info = '';
        $model = self::model()->getBonusCodeById($id);
        if ($model['end_day'] > 0) {
            $info = '绑定后' . $model['end_day'] . '天';
        } else {
            $info = $model['end_date'];
        }
        return $info;
    }

    /**
     * 优惠劵使用时限制验证
     * @param $id
     * @param $order
     * @return bool
     * author mengtianxue
     */
    public function bonusUsedVerified($id, $order)
    {
        //检查传入的id不为空
        if (!empty($id)) {

            //检查优惠劵是否存在
            $bonusCode = $this->getBonusCodeById($id, self::STATUS_APPROVED);
            if ($bonusCode) {

                //vip用户限制
                $vip_phone = VipPhone::model()->getPrimary($order['phone']);
                if (empty($vip_phone)) {
                    return false;
                }

                //验证是否能在当前城市使用
                $city_arr = BonusCodeCity::model()->getBonusCodeCityID($bonusCode['id'], $order['city_id']);
                if (empty($city_arr)) {
                    return false;
                }

                //验证使用渠道限制
                $channel_limited = $order['source'];
                if ($bonusCode['channel_limited'] == self::CHANNEL_LIMITED_APP && in_array($channel_limited, Order::$client_source)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 优惠劵绑定时验证
     * @param $id
     * @param $phone
     * @param $bonus_sn
     * @return bool
     * author mengtianxue
     */
    public function bonusBindingVerified($id, $phone, $bonus_sn)
    {
        if (!empty($id)) {
            //检查优惠劵是否存在
            $bonusCode = $this->getBonusCodeById($id, self::STATUS_APPROVED);
            if ($bonusCode) {
                //vip用户限制
                $vip_phone = VipPhone::model()->getPrimary($phone);
                if (empty($vip_phone)) {
                    return false;
                }

                //用户限制验证
                $is_user_limited = $this->is_user_limited($bonusCode['user_limited'], $phone);
                //重复使用限制验证
                $is_repeat_limited = $this->is_repeat_limited($bonusCode['repeat_limited'], $phone, $bonus_sn);

                if ($is_user_limited && $is_repeat_limited) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 用户限制
     * @param $user_limited
     * @param $phone
     * @return bool
     * author mengtianxue
     */
    public function is_user_limited($user_limited, $phone)
    {
        //查看用户是否已有成单数据
        $isNewCustomer = CustomerOrderReport::model()->isNewCustomer($phone);

        $back = false;
        switch ($user_limited) {
            case 0: //不限
                $back = true;
                break;
            case 1: //限老用户使用
                if (!$isNewCustomer) {
                    $back = true;
                }
                break;
            case 2: //限新用户使用
                if ($isNewCustomer) {
                    $back = true;
                }
                break;
        }

        return $back;
    }

    /**
     * 重复使用限制验证
     * @param $repeat_limited
     * @param $phone
     * @param $bonus_sn
     * @return bool
     * author mengtianxue
     */
    public function is_repeat_limited($repeat_limited, $phone, $bonus_sn, $repeat_bind = 1)
    {
        $back = false;
        if ($repeat_limited == 2) {
            $bonus = CustomerBonus::model()->find('bonus_sn = :bonus_sn',
                array(':bonus_sn' => $bonus_sn));
            if (empty($bonus)) {
                $back = true;
            }
        } else { //1.可重复使用
            if ($repeat_bind == 1) {
                return true;
            } else {
                $bonus = CustomerBonus::model()->find('bonus_sn = :bonus_sn and customer_phone = :customer_phone',
                    array(':bonus_sn' => $bonus_sn, ':customer_phone' => $phone));
                if (empty($bonus)) {
                    $back = true;
                }
            }
        }
        return $back;
    }

    /**
     * @param $type_id
     * @return array|CActiveRecord|mixed|null
     * author mengtianxue
     */
    public function getBonusCode($type_id)
    {
        $bonusCode = BonusCode::model()->find('id = :type_id', array(
            ':type_id' => $type_id
        ));
        return $bonusCode;
    }

    /**
     * 获取最大的area_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getMaxAreaID()
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'max(area_id) as area_id';
        $bonusCode = self::model()->find($criteria);
        return $bonusCode->area_id;
    }

    /**
     * 根据渠道，获取优惠劵ID
     * @param $channel
     * @param $money
     * @return array
     * @auther mengtianxue
     */
    public function getBonusIDByChannle($channel, $money = 0)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('channel', $channel);
        $criteria->compare('status', self::STATUS_APPROVED);
        $criteria->addCondition('binding_deadline > :binding_deadline');
        $criteria->params[':binding_deadline'] = date('Y-m-d H:i:s');
        if (!empty($money)) {
            $criteria->compare('money', $money);
        }
        $bonus_code = self::model()->findAll($criteria);
        $bonus_id=array();
        if ($bonus_code) {
            foreach ($bonus_code as $bonus) {
                $bonus_id[] = $bonus['id'];
            }
        }
        return $bonus_id;
    }


    /**
     * 根据优惠码ID，获取优惠劵使用类型
     * @param $bonus_ids
     * @return array
     * @auther mengtianxue
     */
    public static function getBonusTypeByIDs($bonus_ids)
    {
        $criteria = new CDbCriteria;
        $criteria->addInCondition("id", $bonus_ids);
        $criteria->select = 'id,channel_limited,name';

        self::$db = Yii::app()->db_finance;
        $result = self::model()->findAll($criteria);
        return $result;
    }


    /**
     * 根据优惠码ID，获取优惠劵名称
     * @param $bonus_ids
     * @return array
     * @auther mengtianxue
     */
    public static function getBonusNameByIDs($bonus_ids)
    {
        $criteria = new CDbCriteria;
        $criteria->addInCondition("id", $bonus_ids);
        $criteria->select = 'id,name';

        self::$db = Yii::app()->db_finance;
        $result = self::model()->findAll($criteria);
        return $result;
    }


    /**
     * 根据优惠劵名称获取优惠码ID
     * @param $bonus_name
     * @return array
     * @auther mengtianxue
     */
    public static function getBonusIDByName($bonus_name)
    {

        $criteria = new CDbCriteria;
        $criteria->select = 'id';

        $criteria->compare('name', $bonus_name, true);

        self::$db = Yii::app()->db_finance;
        $result = self::model()->findAll($criteria);

        $bonus_ids = array();
        foreach ($result as $value) {
            array_push($bonus_ids, $value['id']);
        }
        return $bonus_ids;
    }

    /**
     * 根据优惠劵名称获取优惠码ID
     * @param $bonus_name
     * @return array
     * @auther mengtianxue
     */
    public static function getBonusByIds($bonus_ids)
    {

        $criteria = new CDbCriteria;
        $criteria->select = 'id,end_date';
        $criteria->addInCondition("id", $bonus_ids);
        $result = self::model()->findAll($criteria);

        $bonus_ids = array();
        foreach ($result as $value) {
            $bonus_ids[$value['id']]= $value['end_date'];
        }
        return $bonus_ids;
    }
}
