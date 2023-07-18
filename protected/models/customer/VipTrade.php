<?php

/**
 * This is the model class for table "{{vip_trade}}".
 *
 * The followings are the available columns in table '{{vip_trade}}':
 * @property integer $id
 * @property string $vipcard
 * @property integer $order_id
 * @property integer $type
 * @property integer $amount
 * @property string $comment
 * @property integer $balance
 * @property integer $created
 */
class VipTrade extends FinanceActiveRecord
{
    //VIP费用项目
    const TYPE_INCOME = 0; //VIP直充
    const TYPE_ORDER = 1; //订单消费
    const TYPE_CARD_INCOME = 2; //充值卡客户充值
    const TYPE_SUBSIDY= 3; //客户补贴
    const TYPE_PAY= 4; //银联充值-线上支付
    const TYPE_ALIPAY=5;//支付宝支付
    const TYPE_ALIPAY_RECHARGE=6;//支付宝充值
    const TYPE_ALIPAY_BALANCE_TRANSFER=7;//余额转移
    const TYPE_PP_RECHARGE=28;//pp钱包充值
    const TYPE_PP_PAY=29;//pp钱包支付
    const TYPE_WX_RECHARGE=65;//微信充值
    const TYPE_WX_PAY=66;//微信支付
    static $trans_type = array(
        self::TYPE_INCOME => 'VIP直充',
        self::TYPE_ORDER => '订单消费',
        self::TYPE_CARD_INCOME => '充值卡客户充值',
        self::TYPE_SUBSIDY => '客户补贴',
        self::TYPE_PAY => '银联充值',
	self::TYPE_ALIPAY=>'支付宝支付',
	self::TYPE_ALIPAY_RECHARGE=>'支付宝充值',
	self::TYPE_ALIPAY_BALANCE_TRANSFER=>'余额转移',
        self::TYPE_PP_RECHARGE=>'pp钱包充值',
        self::TYPE_PP_PAY=>'pp钱包支付',
        self::TYPE_WX_RECHARGE=>'微信充值',
        self::TYPE_WX_PAY=>'微信支付'
    );

    //SOURCE 交易来源（0.默认、1.银联充值、2.系统扣除、3.系统奖励 4.支付宝支付，5，支付宝充值）
    const TRANS_SOURCE_D = 0;
    const TRANS_SOURCE_F = 1;
    const TRANS_SOURCE_S = 2;
    const TRANS_SOURCE_RE = 3;
    const TRANS_SOURCE_ALIPAY = 4;
    const TRANS_SOURCE_ALIPAY_RECHARGE=5;
    const TRANS_SOURCE_PP_RECHARGE=28;
    const TRANS_SOURCE_PP_PAY=29;
    const TRANS_SOURCE_WX_RECHARGE=65;
    const TRANS_SOURCE_WX_PAY=66;
	/** 日间业务服务费 */
	const TRANS_SOURCE_DAYTIME_TIME_COST = 40;
	/** 日间业务夜间补贴 */
	const TRANS_SOURCE_DAYTIME_SUBSIDY   = 41;
	/** 日间业务返程补贴 */
	const TRANS_SOURCE_DAYTIME_SUBSIDY_BACK = 42;
    /** 一口价业务 */
	const TRANS_SOURCE_UNIT = 43;



    static $trans_source = array(
        self::TRANS_SOURCE_D => '全部',
        self::TRANS_SOURCE_F => '银联充值',
        self::TRANS_SOURCE_S => '系统扣除',
        self::TRANS_SOURCE_RE => '系统奖励',
		self::TRANS_SOURCE_ALIPAY => '支付宝支付',
		self::TRANS_SOURCE_ALIPAY_RECHARGE=>'支付宝充值',
        self::TRANS_SOURCE_PP_RECHARGE=>'pp钱包充值',
        self::TRANS_SOURCE_PP_PAY=>'pp钱包支付',
        self::TRANS_SOURCE_WX_RECHARGE=>'微信充值',
        self::TRANS_SOURCE_WX_PAY=>'微信支付',

		self::TRANS_SOURCE_DAYTIME_TIME_COST => '日间业务服务费',
		self::TRANS_SOURCE_DAYTIME_SUBSIDY => '日间业务夜间补贴',
		self::TRANS_SOURCE_DAYTIME_SUBSIDY_BACK => '日间业务返程补贴',
		self::TRANS_SOURCE_UNIT => '一口价业务服务费',
    );
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return VipAccountLogic the static model class
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
        return '{{vip_trade}}';
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
                'vipcard, order_id, type, amount,source,balance, comment',
                'required'),
            array(
                'order_id, type, created',
                'numerical',
                'integerOnly' => true),
            array(
                'vipcard',
                'length',
                'max' => 13),
            array(
                'comment',
                'length',
                'max' => 512),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array(
                'id, vipcard, order_id, type, amount,comment, balance, created, source',
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
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'vipcard' => 'Vip 卡号',
            'order_id' => '订单ID',
            'type' => '交易类型',
            'amount' => '交易金额',
            'balance' => '余额',
            'source' => '交易来源',
            'comment' => '注释',
            'created' => '交易时间');
    }

    public function beforeSave()
    {
        if (parent::beforeSave()) {
            $this->created = time();
            return true;
        }
    }

    /**
     *
     * 增加一笔VIP账户记录，返回增减数额
     */
    public function addTrade($attributes)
    {
        $this->attributes = $attributes;
        if ($this->type == self::TYPE_ORDER) {
            $this->amount = $this->amount * -1;
        }
        if ($this->save()) {
            return $this->amount;
        }
        return null;
    }

    //start_time , end_time 都是unix时间
    public function VipTradeList($data, $type = null)
    {

        $criteria = new CDbCriteria();
        $criteria->compare('vipcard', $data['vipcard']);
		$payTypeArray = array(
            self::TYPE_INCOME,
			self::TYPE_PAY,
			self::TYPE_ALIPAY_RECHARGE,
			self::TYPE_ALIPAY,
			self::TYPE_SUBSIDY,
			self::TYPE_PP_PAY,
		);
        if ($type !== null) {
            if ($type == 1) {
				$criteria->addCondition('order_id != 0');
	            $criteria->addNotInCondition('type', $payTypeArray);
            } else {
	            $criteria->addInCondition('type', $payTypeArray);
            }
        }
        if (isset($data['start_time']) && isset($data['end_time'])) {
			$criteria->addBetweenCondition('created', $data['start_time'], $data['end_time']);
        }
		$criteria->compare('order_id', $this->order_id);
        $criteria->order = 'id desc';

        return new CActiveDataProvider('VipTrade', array(
            'pagination' => array(
                'pageSize' => 30
            ),
            'criteria' => $criteria));
    }

    //start_time , end_time 都是unix时间
	public function V2VipTradeList($data)
	{
		$criteria = new CDbCriteria();
		if (isset($data['vipcard'])) {
			$criteria->compare('vipcard', $data['vipcard']);
		}
		if (isset($data['start_time']) && isset($data['end_time'])) {
			$criteria->addBetweenCondition('created', $data['start_time'], $data['end_time']);
		}
		if (isset($data['vip_type'])) {
			$vip_type = $data['vip_type'];
			$vipIdList = Vip::model()->getVipIdListByType($vip_type);
			if(!empty($vipIdList)){
				$criteria->addInCondition('vipcard', $vipIdList);
			}
		}

		if (isset($data['order_id'])) {
			$criteria->compare('order_id', $data['order_id']);
		}
		if (isset($data['type'])) {
			$criteria->compare('type', $data['type']);
		}
		$criteria->order = 'id desc';

		return new CActiveDataProvider('VipTrade', array(
			'pagination' => array(
				'pageSize' => 30
			),
			'criteria' => $criteria));
	}

    public function getVipTradeList($data, $type = null){
        $criteria = new CDbCriteria();
	    if(!empty($data['vipcard'])){
            $criteria->compare('vipcard', $data['vipcard']);
	    }
        if ($type !== null) {
            if ($type == 1) {
                $criteria->addCondition('order_id != 0');
            } else {
                $criteria->addCondition('order_id = 0');
            }
        }

        if (isset($data['start_time'])) {
            $criteria->addCondition("created > " . $data['start_time']);
        }

        if (isset($data['end_time'])) {
            $criteria->addCondition("created < " . $data['end_time']);
        }

        $criteria->order = 'id desc';

        return VipTrade::model()->findAll($criteria);
    }


    /**
     * 得到VIP账户的交易详情
     *
     * @author sunhongjing 2013-02-25
     * @param $condition
     * @param $pageNo
     * @param $pageSize
     * @return array
     */
    public function AccountTradeList($condition, $pageNo = 1, $pageSize = 30)
    {

        $offset = $pageNo > 1 ? ($pageNo - 1) * $pageSize : 0;
        $limit = $pageSize;
		$criteria = new CDbCriteria();
		$criteria->compare('vipcard', $condition['vipcard']);
		$criteria->offset = $offset;
		$criteria->limit = $limit;
		$criteria->order = 'created DESC';
		$ret = self::model()->findAll($criteria);
        return $ret;
    }

    /**
     * 取得交易记录的条数
     * @param $condition
     * @return unknown_type
     */
    public function getAccountTradeCount($condition)
    {
		$ret = array();
		$criteria = new CDbCriteria();
		$criteria->compare('vipcard', $condition['vipcard']);
		$count = self::model()->count($criteria);
		$ret['total_rs'] = $count;
        return $ret;
    }

    public function VipTradePrintList($data, $type = null)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('vipcard', $data['vipcard']);
        if ($type !== null) {
            if ($type == 1) {
                $criteria->compare('type', $type);
            } else {
				$criteria->addCondition('type != :type');
				$criteria->params[':type'] = self::TYPE_ORDER;
            }
        }
        if (isset($data['start_time'])) {
            $criteria->addCondition("created > " . $data['start_time']);
        }
        if (isset($data['end_time'])) {
            $criteria->addCondition("created < " . $data['end_time']);
        }
        $criteria->compare('order_id', $this->order_id);
        $criteria->order = 'created desc';

        return new CActiveDataProvider($this, array(
            'pagination' => array(
                'pageSize' => 500
            ),
            'criteria' => $criteria));
    }

    /**
     * vip 数据按时间进行统计
     */
    public function vipStatistic($data, $type = -1)
    {
		$where = 'vipcard = :vipcard and (created between :start_time and :end_time)';
		$params = array(
			':vipcard' => $data['vipcard'],
			':start_time' => $data['start_time'],
			':end_time' => $data['end_time']);
		if(-1 != $type){
			$where .= ' and type = :type';
			$params[':type'] = $type;
		}
        $statistic = Yii::app()->db_finance->createCommand()
            ->select("type ,count(1) as count ,sum(amount) as amount")
            ->from('{{vip_trade}}')
            ->where($where, $params)
            ->group("type")
            ->queryAll();
		return $statistic;
    }


    /**
     * vip 数据按时间进行统计 全部数据统计
     * @param $data
     * @return mixed
     */

    public function vipStatisticAll($start_time,$end_time)
    {
        $statistic = Yii::app()->db_finance->createCommand()
            ->select("type , sum(amount) as amount")
            ->from("t_vip_trade")
            ->where('created between :start_time and :end_time',
                array( ':start_time' => $start_time, ':end_time' => $end_time))
            ->group("type")
            ->queryAll();
        return $statistic;
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
        if (($this->type) >= 0) {
            $criteria->compare('type', $this->type);
        }
        $criteria->compare('vipcard', $this->vipcard);
        $criteria->compare('order_id', $this->order_id);
        $criteria->order = 'created desc';


        return new CActiveDataProvider($this, array(
            'pagination' => array(
                'pageSize' => 30
            ),
            'criteria' => $criteria));
    }

    /**
     * 通过order_id获取Vip扣款金额
     * @param int $order_id
     * @return array $result
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-07-01
     */
    public function getByOrderID($order_id, $type = -1, $source = -1)
    {
		$criteria = new CDbCriteria();
		$criteria->compare('order_id', $order_id);
		if(-1 != $type){
			$criteria->compare('type', $type);
		}
		if(-1 != $source){
			$criteria->compare('source', $source);
		}
		$criteria->order = 'created desc';
		$criteria->select = 'amount ';
		$result = self::model()->find($criteria);
        return $result;
    }

    /**
     * 获取当月最后一条消费记录balance
     * @param $vipCard
     * @param $date
     * @return mixed
     * author mengtianxue
     */
    public function getVipMonthLastBalance($vipCard, $date)
    {
        $start_time = strtotime($date);
        $end_time = strtotime("1 month", $start_time);
		$criteria = new CDbCriteria();
		$criteria->compare('vipcard', $vipCard);
		$criteria->addBetweenCondition('created', $start_time, $end_time);
		$criteria->order = 'id desc';
		$result = self::model()->find($criteria);
        return $result;
    }

	/**
	 * vip 数据按时间进行统计
	 */
	public function getVipStaticData($data)
	{
		$result=Yii::app()->db_finance->createCommand()
			->select("sum(amount) as total_amount")
			->from("{{vip_trade}}")
			->where('type=:type and created>=:start_time and created<=:end_time',
				array(':type'=>$data['type'],':start_time'=>$data['start_time'],':end_time'=>$data['end_time']))
			->queryAll();
		return $result;
	}

	/*
	单独充值vip用户数量统计
	*/
    public function getVipPayCnt($data, $type = -1){
//        $result=Yii::app()->db_finance->createCommand()
//                ->select("count(distinct(vipcard)) as pay_cnt")
//                ->from("t_vip_trade")
//                ->where('(type=0 or type=2 or type=4) and created>=:start_time and created<=:end_time',
//                        array(':start_time'=>$data['start_time'],':end_time'=>$data['end_time']))
//                ->queryAll();
		$criteria = new CDbCriteria();
		if (-1 == $type) {
			$criteria->addInCondition('type', array(
				self::TYPE_INCOME, //0
				self::TYPE_CARD_INCOME, //2
				self::TYPE_PAY, //4
			));
		} else {
			$criteria->compare('type', $type);
		}
		$criteria->addBetweenCondition('created', $data['start_time'], $data['end_time']);
		$criteria->select = 'vipcard';
		$criteria->distinct = true;
		$ret = self::model()->findAll($criteria);
		$result = array();
		$result['pay_cnt']	 = count($ret);
        return $result;
    }

    /*
	各种类型的单独充值用户统计
    */
    public function getVipPayTypeCnt($data){
//        $result=Yii::app()->db_finance->createCommand()
//                ->select("count(distinct(vipcard)) as pay_cnt")
//                ->from("t_vip_trade")
//                ->where('type=:type and created>=:start_time and created<=:end_time',
//                        array(':type'=>$data['type'],':start_time'=>$data['start_time'],':end_time'=>$data['end_time']))
//                ->queryAll();
		return $this->getVipPayCnt($data, $data['type']);
    }

    /*
	单独消费的vip用户数量统计
    */
    public function getVipConsumeCnt($data){
		$result = array();
		$ret = $this->getVipPayCnt($data, self::TYPE_ORDER);
		$result['consume_cnt'] = $ret['pay_cnt'];
        return $result;
    }


	public function dealVipTrade($id, $invoiceId){
	   $db =  Yii::app()->db_finance;
	   $sql = "UPDATE t_vip_trade SET invoiced=1, invoice_id=:invoice_id where id =:id";
	   $command = $db->createCommand($sql);
	   $command->bindParam(":id", $id);
	   $command->bindParam(":invoice_id", $invoiceId);
	   $command->execute();
    }

	public function getInvoicedVipTrans($invoiceId)
	{
		$sql = 'select id,created as create_time,order_id,amount,type as trans_type,3 as table_name,5 as booking_year from t_vip_trade where invoice_id= :invoice_id';
		$trans = Yii::app()->db_finance->createCommand($sql)->queryAll(true, array(':invoice_id' => $invoiceId));
		return $trans;
	}

	/**
	 * 返回某个vip的流水记录
	 *
	 * @param $vipCard
	 * @param $phone
	 * @param $vipType
	 * @param $boardId
	 * @param int $count
	 * @param int $tradeSource
	 */
	public function getVipTradeListApi($vipCard, $phone, $vipType, $boardId, $count = 10, $tradeSource = 0){
		if(empty($vipCard)){
			return false;
		}
		$data = array();
		$tradeCriteria = new CDbCriteria();
		$tradeCriteria->compare('vipcard', $vipCard);
		if(!empty($boardId)){
			$tradeCriteria->addCondition(' id < :boardId');
			$tradeCriteria->params[':boardId'] = $boardId;
		}
		if(!empty($tradeSource)){
			$tradeCriteria->compare('source', $tradeSource);
		}
		$tradeCriteria->order = 'id DESC';
		$tradeCriteria->limit = $count;

		$tradeList = self::model()->findAll($tradeCriteria);
		if($tradeList){
			$tradeMap = $this->listToMap('order_id', $tradeList);
			$orderIdList = array_keys($tradeMap);
			$orderCriteria = new CDbCriteria();
            if($vipType==VipPhone::TYPE_VICE){
                $orderCriteria->compare('phone', $phone);
            }else{
                $orderCriteria->compare('vipcard', $vipCard);
            }
            $orderCriteria->addInCondition('order_id', $orderIdList);
            $orderCriteria->select = 'order_id, phone, location_start, location_end';
			$orderList = Order::model()->findAll($orderCriteria);
			$vip_info = array();
//			if($orderList){
				$orderMap = $this->listToMap('order_id', $orderList);
				foreach($tradeList as  $v){
					$tmp = $v->attributes;
                    $k = $v['order_id'];
					$order = isset($orderMap[$k]) ? $orderMap[$k] :
						array('phone' => $phone, 'location_start'=> '', 'location_end' => '');
					$tmp['location_start'] = $order['location_start'];
					$tmp['location_end']	 = $order['location_end'];
					$tmp['phone']			 = $order['phone'];
					$vip_info[] = $tmp;
				}
//			}
//			$vip_info = array_values($tradeMap);
			foreach($vip_info as  $attributes){
				$phone_t=$attributes['phone'];
				foreach($attributes as $k => $val){
					if($k == 'type'){
						$trans_type = VipTrade::$trans_type;
						$val = $trans_type[$val];
						unset($attributes[$k]);
						$k = 'trans_type';
					}
					if($k == 'source'){
						$trans_source = VipTrade::$trans_source;
						$val = $trans_source[$val];
					}
					if($k == 'vipcard'){
						unset($attributes[$k]);
						$k = 'user_id';
					}
					if($k == 'order_id'){
						unset($attributes[$k]);
						$k = 'trans_order_id';
					}
					if($k == 'comment'){
						unset($attributes[$k]);
						$k = 'remark';
					}
					if($k == 'created'){
						unset($attributes[$k]);
						$k = 'create_time';
						$val=date("Y-m-d H:i:s",$val);
					}
					$attributes[$k] = $val;
				}
				$description='';
				if($vipType>VipPhone::TYPE_VICE && $phone_t<>$phone && !is_null($phone_t) ){
					$description="副卡：".$attributes['phone'];
					$trans_type = "副卡".$attributes['trans_type'];
				}else{
					if(!empty($attributes['trans_order_id'])){
						$description="订单号：".$attributes['trans_order_id'];
					}
					$trans_type = $attributes['trans_type'];
				}
				if($vipType==VipPhone::TYPE_VICE){
					$attributes['balance'] = '';
				}
				$attributes['description'] = $description;
				$attributes['trans_type'] = $trans_type;
				$data[] = $attributes;
			}
		}
		return $data;
	}

	/**
	 * 把一个二维数组,转为一个指定字段为索引的map
	 *
	 * @param $column
	 * @param array $list
	 * @return array 可能抛出undefined index异常
	 */
	private  function listToMap($column, $list = array()){
		if(empty($list)){
			return array();
		}
		$ret = array();
		foreach($list as $item){
			$index = $item[$column];
			$ret[$index]  = $item;
		}
		return $ret;
	}

   public function getVipTrades($vipcard, $created){
	  $vipTrades = Yii::app()->dbstat_readonly->createCommand()
				->select('*')
				->from('t_vip_trade_log_report')
				->where('vipcard = :vipcard and created = :created', 
					array(':vipcard'=>$vipcard,':created'=>$created))
				->order('booking_time desc')->queryAll();
	  return $vipTrades;
   }

    public function getVipTrans($vipid,$invoiced){
          $sql = 'select id,created as create_time,order_id,amount,type as trans_type,3 as table_name,5 as booking_year from t_vip_trade where vipcard= :vipcard and invoiced= :invoiced and type in(0,4,5,6,28,29,65,66) and amount>0';
       /* $invoice_date = date('m-d', time());
        if ($invoice_date <= '01-31') {*/
            $last_year = date('Y-01-01 00:00:00', strtotime('-1 years'));
            $sql .= ' and created>=' . strtotime($last_year);
      /*  } else {
            $last_year = date('Y-01-01 00:00:00', time());
            $sql .= ' and created>=' . strtotime($last_year);
        }*/
        $trans = Yii::app()->db_finance->createCommand($sql)->queryAll(true, array(':vipcard' => $vipid, ':invoiced' => $invoiced));
        return $trans;
    }


}
