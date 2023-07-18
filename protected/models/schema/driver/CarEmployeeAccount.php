<?php

/**
 * This is the model class for table "{{employee_account}}".
 *
 * The followings are the available columns in table '{{employee_account}}':
 * @property integer $id
 * @property string $user
 * @property integer $type
 * @property integer $order_id
 * @property double $cast
 * @property string $comment
 * @property integer $created
 */
class CarEmployeeAccount extends FinanceActiveRecord
{
    public static $table_name = '';
    const TYPE_ORDER_CASH = 0; // 订单现金收入
    const TYPE_ORDER_INFOMATION = 1; // 订单信息费
    const TYPE_ORDER_INVOICE = 2; // 订单发票扣税
    const TYPE_ORDER_VIP = 3; // VIP订单司机收入
    const TYPE_FORFEIT = 4; // 罚金扣费
    const TYPE_INFOMATION = 5; // 信息费充值
    const TYPE_ORDER_INSURANCE = 6; // 订单保险扣费
    const TYPE_BONUS_CASH = 7; // 优惠券现金充值
    const TYPE_BONUS_RETUEN = 8; // 优惠券返现
    const TYPE_DRIVER_BONUS_RETUEN = 9; // 司机发卡优惠券返现
    const TYPE_BONUS_FIXED = 10; // 优惠券补账

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        if (self::$table_name != '') {
            return '{{employee_account_' . self::$table_name . '}}';
        } else {
            return '{{employee_account}}';
        }
    }

    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user, city_id, channel, type, order_id, cast, is_settle, settle_date', 'required'),
            array('city_id, channel, type, order_id, is_settle, created', 'numerical', 'integerOnly'=>true),
            array('cast', 'numerical'),
            array('user', 'length', 'max'=>10),
            array('comment', 'length', 'max'=>255),
            array('operator', 'length', 'max'=>20),
            array('settle_date', 'length', 'max'=>6),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, user, city_id, channel, type, order_id, cast, comment, is_settle, settle_date, created, operator', 'safe', 'on'=>'search'),
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
            'user' => '司机工号',
            'city_id' => '城市ID',
            'channel' => '扣款类型',
            'type' => '信息费类型',
            'order_id' => '订单流水号',
            'cast' => '费用',
            'balance' => '余额',
            'comment' => '备注',
            'settle_date' => '结账时间',
            'order_date' => '订单生成时间',
            'created' => '操作时间',
            'operator' => '操作人'
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
        $criteria->compare('user',$this->user,true);
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('channel',$this->channel);
        $criteria->compare('type',$this->type);
        $criteria->compare('order_id',$this->order_id);
        $criteria->compare('cast',$this->cast);
        $criteria->compare('comment',$this->comment,true);
        $criteria->compare('is_settle',$this->is_settle);
        $criteria->compare('settle_date',$this->settle_date,true);
        $criteria->compare('created',$this->created);
        $criteria->compare('operator',$this->operator);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination' => array(
                'pageSize' => 50
            ),
        ));
    }
}
