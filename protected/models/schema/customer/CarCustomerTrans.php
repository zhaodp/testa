<?php

/**
 * This is the model class for table "{{customer_trans}}".
 * 用户充值消费记录表
 * The followings are the available columns in table '{{customer_trans}}':
 * @property integer $id
 * @property integer $user_id
 * @property string $trans_order_id
 * @property string $trans_card
 * @property integer $trans_type
 * @property string $amount
 * @property string $balance
 * @property integer $source
 * @property string $operator
 * @property string $create_time
 * @property string $remark
 */
class CarCustomerTrans extends FinanceActiveRecord
{
    public $customerPhone;      //用户手机(搜索时用到的属性)


    //交易类型（0.默认、1.账户消费、2.现金充值、3.优惠券充值、4.扣款、5.补偿、6.冲账、7.线上支付、8.充值奖励）
    const TRANS_TYPE_D = 0;
    const TRANS_TYPE_F = 1;
    const TRANS_TYPE_S = 2;
    const TRANS_TYPE_T = 3;
    const TRANS_TYPE_FR = 4;
    const TRANS_TYPE_FV = 5;
    const TRANS_TYPE_FX = 6;
    const TRANS_TYPE_PAY = 7;
    const TRANS_TYPE_RE = 8;
    const TRANS_TYPE_ALIPAY = 9;
    const TRANS_TYPE_ALIPAY_RECHARGE = 10;
    const TRANS_TYPE_BALANCE_TRANSFER = 11;
    const TRANS_TYPE_CARD=12;

    const TRANS_TYPE_PP_RECHARGE=28;//pp钱包充值
    const TRANS_TYPE_PP_PAY=29;//pp钱包支付
    const TRANS_TYPE_WX_RECHARGE=65;//微信充值
    const TRANS_TYPE_WX_PAY=66;//微信支付
	static $trans_type = array(
		self::TRANS_TYPE_D => '全部',
		self::TRANS_TYPE_F => '订单消费',
		self::TRANS_TYPE_S => '银联充值',
		self::TRANS_TYPE_T => '优惠券充值',
		self::TRANS_TYPE_FR => '扣款',
		self::TRANS_TYPE_FV => '客户补贴',
		self::TRANS_TYPE_FX => '冲账',
		self::TRANS_TYPE_PAY => '线上支付',
		self::TRANS_TYPE_RE => '充值奖励',
		self::TRANS_TYPE_ALIPAY => '支付宝支付',
		self::TRANS_TYPE_ALIPAY_RECHARGE => '支付宝充值',
		self::TRANS_TYPE_BALANCE_TRANSFER => '余额转移',
		self::TRANS_TYPE_PP_RECHARGE => 'pp钱包充值',
		self::TRANS_TYPE_PP_PAY => 'pp钱包支付',
		self::TRANS_TYPE_WX_RECHARGE => '微信充值',
		self::TRANS_TYPE_WX_PAY => '微信支付',
        self::TRANS_TYPE_CARD=>'充值卡充值',
	);


    //SOURCE 交易来源（0.默认、1.银联充值、2.系统扣除、3.系统奖励）
    const TRANS_SOURCE_D = 0;
    const TRANS_SOURCE_F = 1;
    const TRANS_SOURCE_S = 2;
    const TRANS_SOURCE_RE = 3;
    const TRANS_SOURCE_ALIPAY = 4;
    const TRANS_SOURCE_ALIPAY_RECHARGE = 5;
    const TRANS_SOURCE_CARD_PAY = 6;
    const TRANS_SOURCE_PP_RECHARGE=28;//pp钱包充值
    const TRANS_SOURCE_PP_PAY=29;//pp钱包支付
    const TRANS_SOURCE_WX_RECHARGE=65;//微信充值
    const TRANS_SOURCE_WX_PAY=66;//微信支付
	/** 远程叫单补贴 */
    const TRANS_SOURCE_REMOTE_ORDER = 51;
	/** 日间业务服务费 */
	const TRANS_SOURCE_DAYTIME_TIME_COST = 52;
	/** 日间业务夜间补贴 */
	const TRANS_SOURCE_DAYTIME_SUBSIDY   = 53;
	/** 日间业务返程补贴 */
	const TRANS_SOURCE_DATETIME_SUBSIDY_BACK = 54;
    /** 一口价业务 */
	const TRANS_SOURCE_UNIT = 55;
	static $trans_source = array(
		self::TRANS_SOURCE_D => '全部',
		self::TRANS_SOURCE_F => '银联充值',
		self::TRANS_SOURCE_S => '系统扣除',
		self::TRANS_SOURCE_RE => '系统奖励',
		self::TRANS_SOURCE_ALIPAY => '支付宝支付',
		self::TRANS_SOURCE_ALIPAY_RECHARGE => '支付宝充值',
        self::TRANS_SOURCE_CARD_PAY=>'充值卡充值',
		self::TRANS_TYPE_BALANCE_TRANSFER => '余额转移',
		self::TRANS_SOURCE_PP_RECHARGE => 'pp钱包充值充值',
		self::TRANS_SOURCE_PP_PAY => 'pp钱包支付',
		self::TRANS_SOURCE_WX_RECHARGE => '微信充值',
		self::TRANS_SOURCE_WX_PAY => '微信支付',
		self::TRANS_SOURCE_REMOTE_ORDER => '远程叫单补贴',

		self::TRANS_SOURCE_DAYTIME_TIME_COST => '日间业务服务费',
		self::TRANS_SOURCE_DAYTIME_SUBSIDY => '日间业务夜间补贴',
		self::TRANS_SOURCE_DATETIME_SUBSIDY_BACK => '日间业务返程补贴',
		self::TRANS_SOURCE_UNIT => '一口价业务服务费',
	);

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{customer_trans}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id, user_id, operator, create_time', 'required'),
            array('id, user_id, trans_type, source', 'numerical', 'integerOnly' => true),
            array('trans_order_id, trans_card, operator', 'length', 'max' => 45),
            array('amount, balance', 'length', 'max' => 10),
            array('remark', 'length', 'max' => 50),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, user_id, trans_order_id, trans_card, trans_type, amount, balance, source, operator, create_time, remark, customerPhone', 'safe', 'on' => 'search'),
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
            'customerMain'=>array(      //客户信息
                self::BELONGS_TO,
                'CustomerMain',
                'user_id',
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'user_id' => '用户ID',
            'trans_order_id' => '交易订单号（订单号、交易流水号）',
            'trans_card' => '交易卡号(VIP卡号、优惠券码)',
            'trans_type' => '交易类型（消费、现金充值、优惠券充值、扣款、补偿、冲账）',
            'amount' => '交易金额',
            'balance' => '当前余额',
            'source' => '交易来源（充值、订单）',
            'operator' => '操作人',
            'create_time' => '创建时间',
            'remark' => '备注',
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
    public function search($extCriteria = NULL)
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $tableAlias = $this->getTableAlias();
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('trans_order_id', $this->trans_order_id, true);
        $criteria->compare('trans_card', $this->trans_card, true);
        $criteria->compare('trans_type', $this->trans_type);
        $criteria->compare('amount', $this->amount, true);
        $criteria->compare('balance', $this->balance, true);
        $criteria->compare('source', $this->source);
        $criteria->compare($tableAlias.'.operator', $this->operator);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('remark', $this->remark, true);

        if (null !== $extCriteria) {
            $criteria->mergeWith($extCriteria);
        }
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

	/**
	 * 根据orderId 返回用户最近的一条交易记录
	 *
	 * @param $orderId
	 * @return mixed
	 */
	public function getCustomerTrans($orderId, $transType = 0, $source=0){
		$criteria = new CDbCriteria;
		$criteria->compare('trans_order_id', $orderId);
		if(!empty($transType)){
			$criteria->compare('trans_type', $transType);
		}
		if(!empty($source)){
			$criteria->compare('source', $source);
		}
		$criteria->order = 'create_time desc';
		return self::model()->find($criteria);
	}

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CustomerTrans the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 统计时间内的各种消费情况
     * @param $start_date format: 2014-10-10 00:00:00
     * @param $end_date format: 2014-10-11 00:00:00
     * @author duke
     * @return mixed
     */
    public function statConsume($start_date,$end_date){
        $where = "create_time between :order_date_start and :order_date_end";
        $params = array(':order_date_start' => $start_date, ':order_date_end' => $end_date);

        $res = Yii::app()->db_finance->createCommand()
            ->select("sum(amount) as amount,trans_type")
            ->from($this->tableName())
            ->where($where, $params)
            ->group('trans_type')
            ->queryAll();
        return $res;
    }
}
