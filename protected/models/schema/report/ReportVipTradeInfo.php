<?php

/**
 * This is the model class for table "{{vip_order}}".
 *
 * The followings are the available columns in table '{{vip_order}}':
 * @property integer $id
 * @property string $vipcard
 * @property string $name
 * @property string $phone
 * @property string $daily_date
 * @property string $driver_id
 * @property integer $order_id
 * @property integer $amount
 * @property integer $cast
 * @property integer $insurance
 * @property integer $balance
 * @property double $Invoice_money
 * @property string $created
 */
class ReportVipTradeInfo extends ReportActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{vip_trade_info}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('vipcard, phone, daily_date, driver_id, created', 'required'),
            array('order_id, income, amount, cast, insurance, balance', 'numerical', 'integerOnly' => true),
            array('Invoice_money', 'numerical'),
            array('vipcard', 'length', 'max' => 13),
            array('name, phone, driver_id', 'length', 'max' => 20),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, vipcard, name, phone, daily_date, driver_id, order_id, income, amount, cast, insurance, Invoice_money, balance, created', 'safe', 'on' => 'search'),
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
            'vipcard' => 'VIP卡号',
            'name' => '名字',
            'phone' => '手机号',
            'daily_date' => '使用时间',
            'driver_id' => '工号',
            'order_id' => '订单号',
            'income' => '收入',
            'amount' => 'VIP扣款',
            'cast' => '信息费',
            'insurance' => '保险费',
            'Invoice_money' => '发票扣税',
            'balance' => '司机所得',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('vipcard', $this->vipcard, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('phone', $this->phone, true);
        $criteria->compare('daily_date', $this->daily_date, true);
        $criteria->compare('driver_id', $this->driver_id, true);
        $criteria->compare('order_id', $this->order_id);
        $criteria->compare('income', $this->income);
        $criteria->compare('amount', $this->amount);
        $criteria->compare('cast', $this->cast);
        $criteria->compare('insurance', $this->insurance);
        $criteria->compare('Invoice_money', $this->Invoice_money);
        $criteria->compare('balance', $this->balance);
        $criteria->compare('created', $this->created, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
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
     * @return VipOrder the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}