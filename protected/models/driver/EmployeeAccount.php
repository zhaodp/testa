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
class EmployeeAccount extends FinanceActiveRecord
{
    //为解决分标也能使用当前model 使用
//	private $_tableName = '{{employee_account}}';
//    private $_defaultTableName = 'employee_account';

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
	const TYPE_ACTIVE=11;//活动补贴
	/** 普通订单收入 */
	const TYPE_ORDER_NORMAL = 12;
	/** 余额转移 */
	const TYPE_BALANCE_TRANSFER = 13;

    public static $accountStatus= array(
        '0' => '订单现金收入',
        '1' => '订单信息费',
        '2'=>'订单发票扣税',
        '3'=>'VIP订单司机收入',
        '4'=>'罚金扣费',
        '5'=>'信息费充值',
        '6'=>'订单保险扣费',
        '7'=>'优惠券现金充值',
        '8'=>'优惠券返现',
        '9'=>'司机发卡优惠券返现',
        '10'=>'贵宾卡返现',//优惠券补账
        '11'=>'活动补贴',
        '12'=>'普通订单收入',
        '13'=>'余额转移');

	/** channel对应的流水文案  */
	public static $channelMessage = array(
		self::CHANNEL_REMOTE_ORDER_VIP => '远程订单补贴',
		self::CHANNEL_REMOTE_ORDER_NORMAL => '远程订单补贴',
		self::CHANNEL_REMOTE_ORDER_NORMAL_TIPS => '远程订单小费',
		self::CHANNEL_REMOTE_ORDER_VIP_TIPS => '远程订单小费',
		self::CHANNEL_REMOTE_ORDER_INFORMATION => '远程订单补贴信息费',
		self::CHANNEL_REMOTE_ORDER_TIMEOUT		=> '远程订单补贴(超时)',

        self::CHANNEL_DAYTIME_ORDER_TIME_COST   => '日间业务服务费',
        self::CHANNEL_DAYTIME_ORDER_SUBSIDY   => '日间业务夜间补贴',
        self::CHANNEL_DAYTIME_ORDER_SUBSIDY_BACK   => '日间业务返程补贴',
        self::CHANNEL_DRIVER_DAYTIME_EXTRA_SUBSIDY => '日间优惠补贴',

        self::CHANNEL_ORDER_UNIT   => '标准价格业务服务费',

        self::CHANNEL_DRIVER_INFORMATION_ENVELOPE => '红包奖励',
        self::CHANNEL_DRIVER_MODIFY_FEE             => '调整费补扣',
        self::CHANNEL_DRIVER_DAYTIME_FORFEIT        => '日间业务作弊罚款',
        self::CHANNEL_FORFEIT_COMPLAINT_OK          => '申诉成功回款',
        self::CHANNEL_DEVICE_FEE                    => '物料押金',
	);

    const TYPE_PP_RECHARGE=28;//pp钱包充值
    const TYPE_PP_PAY=29;//pp钱包支付

    //对应数据库中的channel字段
    const CHANNEL_ALIPAY=5;//支付宝支付
    const CHANNEL_UNIONPAY=25;//银联支付，为了向上兼容不得不用25这个值
    const CHANNEL_ALIRECHARGE=26;//支付宝充值
	const CHANNEL_NANJING_ACTIVE=27;//南京活动补贴
	const CHANNEL_HANGZHOU_ACTIVE=30;//杭州活动补贴
	

   const CHANNEL_TICKET_COMPENSATE=31;//工单补偿

    const CHANNEL_PP_RECHARGE=28;//pp钱包充值
    const CHANNEL_PP_PAY=29;//pp钱包支付
	/** VIP远程叫订单补贴 */
	const CHANNEL_REMOTE_ORDER_VIP = 50;
	/** 普通用户远程叫订单补贴 */
	const CHANNEL_REMOTE_ORDER_NORMAL = 51;

	/** 远程叫订单补贴信息费 */
	const CHANNEL_REMOTE_ORDER_INFORMATION = 52;

	/** VIP叫订单小费 */
	const CHANNEL_REMOTE_ORDER_VIP_TIPS = 53;
	/** 普通用户叫订单小费 */
	const CHANNEL_REMOTE_ORDER_NORMAL_TIPS = 54;
	/** 远程订单补贴 超时 */
	const CHANNEL_REMOTE_ORDER_TIMEOUT	   = 55;
	/** 日间业务服务费  */
	const CHANNEL_DAYTIME_ORDER_TIME_COST  = 56;
	/** 日间业务夜间补贴 */
	const CHANNEL_DAYTIME_ORDER_SUBSIDY  = 57;
	/** 日间业务返程补贴 */
	const CHANNEL_DAYTIME_ORDER_SUBSIDY_BACK  = 58;
    /** 一口价业务 */
	const CHANNEL_ORDER_UNIT  = 59;
    /** 用来记录司机日间订单总收入的channel */
    const CHANNEL_DRIVER_CASH_DAYTIME  = 60;
    /** 用来记录司机洗车总收入的channel */
    const CHANNEL_DRIVER_CASH_UNIT  = 61;
    /** 司机连续高峰在线,奖励作为信息费充入 */
    const CHANNEL_DRIVER_INFORMATION_SUBSIDY  = 62;
    /** 司机红包奖励作为信息费充入 */
    const CHANNEL_DRIVER_INFORMATION_ENVELOPE = 63;
    /** 日间业务 给司机服务时间的额外补贴 */
    const CHANNEL_DRIVER_DAYTIME_EXTRA_SUBSIDY = 64;
    /**  调整费 */
    const CHANNEL_DRIVER_MODIFY_FEE            = 65;
    /** 司机日间订单作弊罚款 */
    const CHANNEL_DRIVER_DAYTIME_FORFEIT               = 66;

    const CHANNEL_WX_RECHARGE=65;//微信充值

    const CHANNEL_WX_PAY=66;//微信支付
    /** 司机入职的衣服等物料押金 */
    const CHANNEL_DEVICE_FEE = 67;

    /** 申诉成功回款 */
    const CHANNEL_FORFEIT_COMPLAINT_OK = 70;

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
        return array(
            array(
                'user, type, order_id, cast, balance,  comment',
                'required'
            ),
            array(
                'city_id, channel, type, created, settle_date, order_date',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'cast, balance',
                'numerical'
            ),
            array(
                'user',
                'length',
                'max' => 10
            ),
            array('operator', 'length', 'max' => 20),
	    array('order_id','length','max'=>32),
            array(
                'comment',
                'length',
                'max' => 512
            ),
            array(
                'id, user,city_id, channel,  type, order_id, cast, balance, comment, created, settle_date, order_date, operator',
                'safe',
                'on' => 'search'
            )
        );
    }

    /**
     *
     * @return array
     * relational rules.
     */
    public function relations()
    {
        return array();
    }

    /**
     *
     * @return array
     * customized
     *
     *
     * attribute
     *
     *
     * labels
     *
     *
     * (name=>label)
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

	public function getMessageByChannel($channel){
		if(isset(self::$channelMessage[$channel])){
			return self::$channelMessage[$channel];
		}else{
			return '';
		}
	}

    /**
     * 增加一笔司机的账务
     * 定义帐务的收入支出标志
     */
    public function addAccount()
    {
        switch ($this->type) {
            case self::TYPE_ORDER_CASH :
            case self::TYPE_ORDER_VIP :
            case self::TYPE_INFOMATION :
            case self::TYPE_BONUS_CASH :
            case self::TYPE_BONUS_RETUEN :
            case self::TYPE_DRIVER_BONUS_RETUEN :
            case self::TYPE_BONUS_FIXED :
                break;
            case self::TYPE_ORDER_INFOMATION :
            case self::TYPE_ORDER_INVOICE :
            case self::TYPE_FORFEIT :
            case self::TYPE_ORDER_INSURANCE :
                $this->cast = $this->cast * -1;
                break;
        }
        return $this->save();
    }

    /**
     * 获取司机历史充值记录
     */
    public function getRechargeHistory($params)
    {
        $tablename = 't_employee_account_' . $params['created'];
        $from = Yii::app()->db_finance->createCommand()->select('*')->from($tablename);
        if ($params['user'] == '' && $params['comment'] == '') {
            $where = $from->where(array(
                'IN',
                'type',
                array(
                    EmployeeAccount::TYPE_INFOMATION,
                    EmployeeAccount::TYPE_BONUS_CASH,
                    EmployeeAccount::TYPE_BONUS_FIXED,
                    EmployeeAccount::TYPE_BONUS_RETUEN,
                    EmployeeAccount::TYPE_DRIVER_BONUS_RETUEN,
                    EmployeeAccount::TYPE_ORDER_VIP
                )
            ));
        }

        if ($params['user'] == '' && $params['comment'] != '') {
            $where = $from->where(array(
                'AND',
                array(
                    'LIKE',
                    'comment',
                    '%' . $params['comment'] . '%'
                ),
                array(
                    'IN',
                    'type',
                    array(
                        EmployeeAccount::TYPE_INFOMATION,
                        EmployeeAccount::TYPE_BONUS_CASH,
                        EmployeeAccount::TYPE_BONUS_FIXED,
                        EmployeeAccount::TYPE_BONUS_RETUEN,
                        EmployeeAccount::TYPE_DRIVER_BONUS_RETUEN,
                        EmployeeAccount::TYPE_ORDER_VIP
                    )
                )
            ));
        }

        if ($params['user'] != '' && $params['comment'] == '') {
            $where = $from->where(array(
                'AND',
                array(
                    'LIKE',
                    'user',
                    '%' . $params['user'] . '%'
                ),
                array(
                    'IN',
                    'type',
                    array(
                        EmployeeAccount::TYPE_INFOMATION,
                        EmployeeAccount::TYPE_BONUS_CASH,
                        EmployeeAccount::TYPE_BONUS_FIXED,
                        EmployeeAccount::TYPE_BONUS_RETUEN,
                        EmployeeAccount::TYPE_DRIVER_BONUS_RETUEN,
                        EmployeeAccount::TYPE_ORDER_VIP
                    )
                )
            ));
        }

        if ($params['user'] != '' && $params['comment'] != '') {
            $where = $from->where(array(
                'AND',
                array(
                    'LIKE',
                    'user',
                    '%' . $params['user'] . '%'
                ),
                array(
                    'LIKE',
                    'comment',
                    '%' . $params['comment'] . '%'
                ),
                array(
                    'IN',
                    'type',
                    array(
                        EmployeeAccount::TYPE_INFOMATION,
                        EmployeeAccount::TYPE_BONUS_CASH,
                        EmployeeAccount::TYPE_BONUS_FIXED,
                        EmployeeAccount::TYPE_BONUS_RETUEN,
                        EmployeeAccount::TYPE_DRIVER_BONUS_RETUEN,
                        EmployeeAccount::TYPE_ORDER_VIP
                    )
                )
            ));
        }

        $rechargeHistory = $where->order('created DESC')->queryAll();
        return $rechargeHistory;
    }

    /**
     * 获取司机历史账单
     *
     * @param 工号 $driver_id
     */
    public function getAccountSettle($driver_id)
    {
        $where = 'user=:user';
        $params = array(':user' => $driver_id);

        $settle = Yii::app()->db_finance->createCommand()
            ->select('*')
            ->from('t_employee_account_settle')
            ->where($where, $params)
            ->order('settle_date DESC')
            ->queryAll();
        return $settle;
    }

    /**
     * 获取司机历史账单
     *
     * @param 工号 $driver_id
     * @param 月份 $month
     */
    public function getAccountSettleMonth($driver_id, $month = null)
    {
        $where = 'user=:user';
        $params = array(':user' => $driver_id);

        if ($month !== null) {
            $where .= ' and settle_date=:month';
            $params[':month'] = $month;
        }

        $settle = Yii::app()->db_finance->createCommand()
            ->select('*')
            ->from('t_employee_account_settle')
            ->where($where, $params)
            ->order('settle_date DESC')
            ->queryRow();
        return $settle;
    }

    /**
     * 司机签约第一天接单时间
     */
    public function getEmployeeAccountEnjoyDate($driver_id)
    {
        $date = Yii::app()->db_finance->createCommand()->select('*')->from('t_employee_account')->where('user = :user', array(
            ':user' => $driver_id
        ))->order('created asc')->queryRow();
        return $date['created'];
    }

    /**
     * 获取司机历史结算单详情
     *
     * @param 工号 $driver_id
     * @param 月度 $month
     * @type  类型
     */
    public function getAccountSettleDetail($driver_id, $month, $type = null)
    {
        $days = date('t', strtotime($month));

        $current_month = date('Ym', strtotime($month));
        $tablename = 't_employee_account_' . $current_month;
        $detail = array();
        if ($type != null) {
            $detail = Yii::app()->db_finance->createCommand()
                ->select('*')
                ->from($tablename)
                ->where('user=:user AND type=:type', array(
                    ':user' => $driver_id, ':type' => $type))
                ->order('created DESC')
                ->queryAll();
        } else {
            for ($i = 1; $i <= $days; $i++) {
                $day = $month . "-" . $i;
                $settle_detail = $this->getEmployeeAccountDaily($day, $tablename, $driver_id);
                if (!empty($settle_detail['t1'])) {
                    $detail[$i] = $settle_detail;
                    $detail[$i]['id'] = $day;
                    $detail[$i]['driver_id'] = $driver_id;
                }
            }
        }
        return $detail;
    }

    /**
     * 获取司机历史结算单详情
     * @param 工号 $driver_id
     * @param 月度 $month
     * @param 时间 $day
     * @param 查找类型 $type
     */
    public function getAccountSettleDetailList($driver_id, $day, $type = null, $tablename = null)
    {
        $current_month = date('Ym', strtotime($day));
        $tablename = $tablename == null ? 't_employee_account_' . $current_month : $tablename;
        $startTime = strtotime($day);
        $endTime = $startTime + 86400;
        if (isset($type) && $type != null) {
            $settle = Yii::app()->db_finance->createCommand()
                ->select('*')
                ->from($tablename)
                ->where('user=:user and type = :type and created between :startTime and :endTime',
                    array(':user' => $driver_id, ':type' => $type, ':startTime' => $startTime, 'endTime' => $endTime))
                ->order("id desc")
                ->queryAll();
        } else {
            $settle = Yii::app()->db_finance->createCommand()
                ->select('*')
                ->from($tablename)
                ->where('user=:user and created between :startTime and :endTime',
                    array(':user' => $driver_id, ':startTime' => $startTime, 'endTime' => $endTime))
                ->order("id desc")
                ->queryAll();
        }
        return $settle;
    }

    public function getAccountSettleList($driver_id, $day, $type = null)
    {
        $tablename = 't_employee_account';
        $startTime = strtotime($day);
        $endTime = $startTime + 86400;
        $settle = array();
        if (isset($type)) {
            $settle = Yii::app()->db_finance->createCommand()->select('*')->from($tablename)->where('user=:user and type = :type and created between :startTime and :endTime', array(
                ':user' => $driver_id,
                ':type' => $type,
                ':startTime' => $startTime,
                'endTime' => $endTime
            ))->queryAll();
        } else {
            $settle = Yii::app()->db_finance->createCommand()
                ->select('sum(if(type=1,1,0)) as t,
											sum(if(type=0,cast,0)) AS t0,
											sum(if(type=1,cast,0)) AS t1,
											sum(if(type=2,cast,0)) AS t2,
											sum(if(type=3,cast,0)) AS t3,
											sum(if(type=4,cast,0)) AS t4,
											sum(if(type=5,cast,0)) AS t5,
											sum(if(type=6,cast,0)) AS t6,
											sum(if(type=7,cast,0)) AS t7,
											sum(if(type=8,cast,0)) AS t8,
											sum(if(type=9,cast,0)) AS t9,
											sum(if(type=10,cast,0)) AS t10,
											sum(cast)-sum(if(type=0,cast,0))  as total')
                ->from($tablename)
                ->where('user=:user and created between :startTime and :endTime',
                    array(':user' => $driver_id, ':startTime' => $startTime, 'endTime' => $endTime))
                ->queryRow();
        }
        return $settle;
    }

    /**
     * 获取司机历史结算单详情
     *
     * @param 工号 $driver_id
     * @param 月度 $month
     * @param 时间 $day
     * @param 查找类型 $type
     */
    public function getAccountSettleMonthList($driver_id, $month, $type = null, $tablename = null)
    {
        $current_month = date('Ym', strtotime($month));

        $tablename = $tablename == null ? 't_employee_account_' . $current_month : $tablename;
        if (isset($type)) {
            $settle = Yii::app()->db_finance->createCommand()
                ->select('*')
                ->from($tablename)
                ->where('user=:user and type = :type and comment !="信息费余额结转"', array(
                    ':user' => $driver_id,
                    ':type' => $type
                ))
                ->order("id desc")
                ->queryAll();

            if ($type == 5 && $tablename == 't_employee_account') {
                $settle_jz[0] = Yii::app()->db_finance->createCommand()
                    ->select('id,user,type,order_id,sum(cast) as cast,comment,is_settle,is_settle,created')
                    ->from($tablename)
                    ->where('user=:user and type = :type and comment ="信息费余额结转"', array(
                        ':user' => $driver_id,
                        ':type' => $type
                    ))
                    ->queryRow();
                $settle = array_merge($settle, $settle_jz);
            }
        } else {
            $settle = Yii::app()->db_finance->createCommand()
                ->select('*')
                ->from($tablename)
                ->where('user=:user and comment !="信息费余额结转"', array(
                    ':user' => $driver_id
                ))
                ->order("id desc")
                ->queryAll();
            if ($type == 5 && $tablename == 't_employee_account') {
                $settle_jz[0] = Yii::app()->db_finance->createCommand()
                    ->select('id,user,type,order_id,sum(cast) as cast,comment,is_settle,is_settle,created')
                    ->from($tablename)
                    ->where('user=:user and comment ="信息费余额结转"', array(
                        ':user' => $driver_id
                    ))
                    ->queryRow();
                $settle = array_merge($settle, $settle_jz);
            }
        }
        return $settle;
    }

    /**
     * 循环获取当天数据统计
     * Enter
     * description
     * here
     * .
     * ..
     *
     * @param unknown_type $day
     * @param unknown_type $tablename
     * @param unknown_type $driver_id
     */
    public function getEmployeeAccountDaily($day, $tablename, $driver_id)
    {
        $startTime = strtotime($day);
        $endTime = strtotime($day) + 86400;

        $settle = Yii::app()->db_finance->createCommand()->select('sum(if(type=1,1,0)) as t,
											sum(if(type=0,cast,0)) AS t0,
											sum(if(type=1,cast,0)) AS t1,
											sum(if(type=2,cast,0)) AS t2,
											sum(if(type=3,cast,0)) AS t3,
											sum(if(type=4,cast,0)) AS t4,
											sum(if(type=5,cast,0)) AS t5,
											sum(if(type=6,cast,0)) AS t6,
											sum(if(type=7,cast,0)) AS t7,
											sum(if(type=8,cast,0)) AS t8,
											sum(if(type=9,cast,0)) AS t9,
											sum(if(type=10,cast,0)) AS t10,
											sum(cast)-sum(if(type=0,cast,0))  as total')->from($tablename)->where('user=:user and created between :startTime and :endTime', array(
                ':user' => $driver_id,
                ':startTime' => $startTime,
                'endTime' => $endTime
            ))->queryRow();
        return $settle;
    }

    public function dailyList($tablename, $order_id)
    {
        $settle = Yii::app()->db_finance->createCommand()->select('sum(if(type=1,1,0)) as t,
											sum(if(type=0,cast,0)) AS t0,
											sum(if(type=1,cast,0)) AS t1,
											sum(if(type=2,cast,0)) AS t2,
											sum(if(type=3,cast,0)) AS t3,
											sum(if(type=4,cast,0)) AS t4,
											sum(if(type=5,cast,0)) AS t5,
											sum(if(type=6,cast,0)) AS t6,
											sum(if(type=7,cast,0)) AS t7,
											sum(if(type=8,cast,0)) AS t8,
											sum(if(type=9,cast,0)) AS t9,
											sum(if(type=10,cast,0)) AS t10,
											sum(cast)-sum(if(type=0,cast,0))  as total')->from($tablename)->where('order_id in (' . $order_id . ')')->queryRow();
        return $settle;
    }

    public function getEmployeeAccountDailyList()
    {
        $day = $_GET['month'];
        $driver_id = $_GET['driver_id'];
        $startTime = strtotime($day);
        $endTime = strtotime($day) + 86400;
        $orderList = Order::getDbReadonlyConnection()->createCommand()->select('order_id')->from('t_order')->where('driver_id=:driver_id and status in(:ORDER_COMPLATE,:ORDER_NOT_COMFIRM) and call_time between :startTime and :endTime', array(
            ':driver_id' => $driver_id,
            ':ORDER_COMPLATE' => Order::ORDER_COMPLATE,
            ':ORDER_NOT_COMFIRM' => Order::ORDER_NOT_COMFIRM,
            ':startTime' => $startTime,
            'endTime' => $endTime
        ))->queryAll();
        return $orderList;
    }

    /**
     *
     * 获取司机当月账单明细
     *
     * @param 月份 $month
     * @param 工号 $driver_id
     */
    public function getAccount($month, $driver_id)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'user,type,cast,comment,created';
        $criteria->condition = 'settle_date=:date and user=:user';
        $criteria->order = 'id,created';
        $criteria->params = array(
            ':date' => $month,
            ':user' => $driver_id
        );

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 100
            )
        ));
    }

    public function getAccountSummery($month, $driver_id)
    {
        $sql = 'SELECT type,cast FROM `t_employee_account_settle`
				WHERE `settle_date`=:date and `driver_id`=:driver_id';
        $summery = Yii::app()->db_finance->createCommand($sql)->queryAll(true, array(
            ':date' => $month,
            ':driver_id' => $driver_id
        ));

        $total = 0;
        if ($summery) {
            foreach ($summery as $k => $v) {
                $summery_tmp['type_' . $v['type']] = $v;
                if ($v['type'] != 0) {
                    $total += $v['cast'];
                }
            }
        }
        $summery_tmp['total'] = $total;
        return $summery_tmp;
    }

    public function getEmployeeAccountByOrderType($order_id, $type)
    {
        $order = Order::model()->getOrderById($order_id);
        if (!isset($order['status'])) {
            return 0;
        }
        // 订单状态不等于已报单和拒绝消单的，订单费返回0
        if ($order['status'] != Order::ORDER_COMPLATE && $order['status'] != Order::ORDER_NOT_COMFIRM) {
            return 0;
        }

        // 没有报单日志的
        $order_log = OrderLog::model()->find('order_id=:order_id and operator=:operator', array(
            ':order_id' => $order['order_id'],
            ':operator' => $order['driver_id']
        ));
        if (!$order_log) {
            return 0;
        }

        $order_account_month = date('Ym', $order_log->created);
        $account = self::getEmployeeAccountHistoryByOrderType('t_employee_account_' . $order_account_month, $order['order_id'], $type);
        if ($account == -1) {
            $account = self::getEmployeeAccountHistoryByOrderType('t_employee_account', $order['order_id'], $type);
        }
        $cast = isset($account['cast']) ? $account['cast'] : 0;
        return $cast;
    }

    private function getEmployeeAccountHistoryByOrderType($tableName, $order_id, $type)
    {
        try {
            $account = Yii::app()->db_finance->createCommand()->select('*')->from($tableName)->where('order_id=:order_id and type=:type', array(
                ':order_id' => $order_id,
                ':type' => $type
            ))->queryRow();
            if (empty($account)) {
                return -1;
            } else {
                return $account;
            }
        } catch (Exception $e) {
            return -1;
        }

    }

    /**
     * 查询订单是否结账，结账的信息费用
     *
     * @param object $order
     */
    public function getOrderfee($order)
    {
        if (!isset($order->status)) {
            return 0;
        }
        // 订单状态不等于已报单和拒绝消单的，订单费返回0
        if ($order->status != Order::ORDER_COMPLATE && $order->status != Order::ORDER_NOT_COMFIRM) {
            return 0;
        }

        // 没有报单日志的
        $order_log = OrderLog::model()->find('order_id=:order_id and operator=:operator', array(
            ':order_id' => $order->order_id,
            ':operator' => $order->driver_id
        ));
        if (!$order_log) {
            return 0;
        }

        $order_account_month = date('Ym', $order_log->created);
        $account = self::getEmployeeAccountHistoryByOrderType('t_employee_account_' . $order_account_month, $order->order_id, EmployeeAccount::TYPE_ORDER_INFOMATION);
        if ($account == -1) {
            $account = self::getEmployeeAccountHistoryByOrderType('t_employee_account', $order->order_id, EmployeeAccount::TYPE_ORDER_INFOMATION);
        }
        $cast = isset($account['cast']) ? $account['cast'] : 0;
        return $cast;
    }

    public function getDriverBalance($driverId)
    {
        $balance = Yii::app()->db_finance->createCommand()->select('SUM(cast) AS balance')->from('t_employee_account')->where('user=:user', array(
            ':user' => $driverId
        ))->group('user')->queryScalar();

        return $balance;
    }

    public function getDriverBonusIncomeByType($type)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'sum(cast) as cast';
        $criteria->addCondition('type=:type');
        $criteria->params[':type'] = $type;
        $criteria->addCondition('user=:driver_id');
        $criteria->params[':driver_id'] = Yii::app()->user->id;
        $employeeAccount = EmployeeAccount::model()->find($criteria);
        if ($employeeAccount)
            return $employeeAccount->cast;
        else
            return 0;
    }

    /**
     * 获取司机历史账单按类型累计
     *
     * @param 类型 $type
     */
    public function getAccountSettleTotalByType($type)
    {
        $amount = Yii::app()->db_finance->createCommand()->select('SUM(t' . $type . ')')->from('t_employee_account_settle')->where('user=:user', array(
            ':user' => Yii::app()->user->id
        ))->queryScalar();
        return $amount;
    }

    public function getBonusCastByOrder($order_id)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('order_id=:order_id');
        $criteria->params[':order_id'] = $order_id;
        $criteria->addCondition('user=:driver_id');
        $criteria->params[':driver_id'] = Yii::app()->user->id;
        $criteria->addCondition('type>:type');
        $criteria->params[':type'] = EmployeeAccount::TYPE_BONUS_CASH;
        $employeeAccount = EmployeeAccount::model()->find($criteria);
        if ($employeeAccount)
            return $employeeAccount->cast;
        else
            return 0;
    }

    public function driverRecharge($attributes)
    {
        EmployeeAccount::$table_name = date('Ym');
        $model = new EmployeeAccount();
        $dataAccount = array();
        $dataAccount['created'] = time();
        $dataAccount['type'] = EmployeeAccount::TYPE_INFOMATION;
        $dataAccount['comment'] = '信息费充值 操作人：' . Yii::app()->user->getID() . '，备注：' . $attributes['comment'];
        $dataAccount['user'] = $attributes['user'];
        $dataAccount['cast'] = $attributes['cast'];
        $model->attributes = $dataAccount;
        if ($model->insert()) {
            Driver::enableByFee($attributes['user']);

            $this->ReloadDriverAmountRedis($attributes['user']);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 根据$attributes获取两分钟以内有没有给该司机充值
     */
    public function getDriverRecharge($attributes)
    {
        $created = time() - 120;
        $employeeAccount = EmployeeAccount::model()
            ->find('created > :created and type = :type and user = :user and cast = :cast',
                array(':created' => $created, ':type' => EmployeeAccount::TYPE_INFOMATION, ':user' => $attributes['user'], ':cast' => $attributes['cast']));
        if ($employeeAccount) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getOrderCleaningTimeByOrderForBonus($order_id)
    {
        $employeeAccount = EmployeeAccount::model()->find('order_id=:order_id', array(
            ':order_id' => $order_id
        ));
        if ($employeeAccount) {
            return date("Y-m-d H:i", $employeeAccount->created);
        } else {
            return '';
        }
    }

    /**
     * 扣除订单信息费
     *
     * @param array $order
     */
    public function deal_order_infomation_fee($order)
    {

        $cast = CityConfig::model()->calculatorCast($order); //Common::cast($order);


        $account = new EmployeeAccount();
        $account->attributes = array(
            'type' => EmployeeAccount::TYPE_ORDER_INFOMATION,
            'user' => $order['driver_id'],
            'order_id' => $order['order_id'],
            'comment' => '信息费 单号：' . $order['order_number'],
            'cast' => $cast,
            'created' => $order['created']
        );
        $account->addAccount();

        // 修改order表中cast字段
        Order::model()->findByPk($order['order_id'])->updateByPk($order['order_id'], array(
            'cast' => $cast
        ));
    }

    /**
     * 扣除订单保险费用(仅北京)，每单2元
     *
     * @param array $order
     */
    public function deal_order_insurance($order, $cast = 2)
    {
        // 仅北京的司机扣除保险费
//		if ($order['city_id']==1) {
        $account = new EmployeeAccount();
        $account->attributes = array(
            'type' => EmployeeAccount::TYPE_ORDER_INSURANCE,
            'user' => $order['driver_id'],
            'order_id' => $order['order_id'],
            'comment' => '保险费 单号：' . $order['order_number'],
            'cast' => $cast,
            'created' => $order['created']
        );
        $account->addAccount();
//		}
    }

    /**
     * 普通订单的收入记录
     *
     * @param int $order_id
     */
    public function deal_order_normal($order)
    {
        $account = new EmployeeAccount();
        $account->attributes = array(
            'type' => EmployeeAccount::TYPE_ORDER_CASH,
            'user' => $order['driver_id'],
            'order_id' => $order['order_id'],
            'comment' => '现金收入 单号：' . $order['order_number'],
            'cast' => $order['income'],
            'created' => $order['created']
        );
        $account->addAccount();

        $model = Order::model()->findByPk($order['order_id']);
        if ($model->invoice) {
            $account = new EmployeeAccount();
            $account->attributes = array(
                'type' => EmployeeAccount::TYPE_ORDER_INVOICE,
                'user' => $order['driver_id'],
                'order_id' => $order['order_id'],
                'comment' => '发票扣税 单号：' . $order['order_number'],
                'cast' => $order['income'] * 0.06,
                'created' => $order['created']
            );
            $account->addAccount();
        }
    }

    /**
     * 没人调用了
     *
     * VIP订单，扣除VIP帐户余额，增加司机收入记录，发短信通知VIP扣款金额及余额
     *
     * @param int $order_id
     */
    public function deal_order_vip($order)
    {
        $vip = VipService::service()->getVipInfoByVipCard($order['vipcard'], false);
        if (!$vip) {
            return;
        }

        $order_ext = OrderExt::model()->getPrimary($order['order_id']);
        $tip = 0;
        if ($order_ext) {
            $tip = $order_ext['total'];
        }
        $income = $order['income'] - $order['price'];
        $total = $income + $tip;

        if ($income > 0) {
            // VIP客户消费明细
            $attributes = array(
                'vipcard' => $order['vipcard'],
                'order_id' => $order['order_id'],
                'type' => VipTrade::TYPE_ORDER,
                'amount' => $total,
                'comment' => 'VIP消费 单号：' . $order['order_number'],
                'balance' => $vip['amount'] - $total,
                'created' => $order['created']
            );
            $trade = new VipTrade();
            $amount = $trade->addTrade($attributes);


            // VIP订单划账给司机账户
            $account = new EmployeeAccount();
            $account->attributes = array(
                'type' => EmployeeAccount::TYPE_ORDER_VIP,
                'user' => $order['driver_id'],
                'order_id' => $order['order_id'],
                'comment' => 'VIP订单收入 单号：' . $order['order_number'],
                'cast' => $total,
                'created' => $order['created']
            );
            $account->addAccount();

            // 扣除发票税点
            // TODO：税点单独放到参数里
            $account = new EmployeeAccount();
            $account->attributes = array(
                'type' => EmployeeAccount::TYPE_ORDER_INVOICE,
                'user' => $order['driver_id'],
                'order_id' => $order['order_id'],
                'comment' => 'VIP订单发票税点扣除 单号：' . $order['order_number'],
                'cast' => ($order['income'] + $tip) * 0.06,
                'created' => $order['created']
            );
            $account->addAccount();

            if ($amount != null) {
				Vip::model()->updateBalance($vip['vipid'], $amount);
                VipAccountService:
            }
        }
    }

    /**
     * 普通订单的优惠券记录
     *
     * @param array $order
     */
    public function deal_order_bonus($order, $notify_sms = false)
    {
        // 如果用户有绑定优惠券，金额返信息费
        $un_used_bonus = CustomerBonus::existsUnusedCustomerBonus($order['phone']);
        if ($un_used_bonus) {
            if (($un_used_bonus['is_limited'] == BonusType::BONUS_TYPE_LIMIT_APP || $un_used_bonus['is_limited'] == BonusType::BONUS_TYPE_LIMIT_APP_FRESH) && ($order['source'] == Order::SOURCE_CALLCENTER || $order['source'] == Order::SOURCE_CALLCENTER_INPUT)) {

                $sms_message = "您昨天通过拨打400电话享受e代驾服务,您的账户中还有" . $un_used_bonus['money'] . "元代金券,仅限通过e代驾客户端找代驾使用,免费下载>> http://wap.edaijia.cn ";
                Sms::SendSMS($order['phone'], $sms_message);
                exit();
            }
        }

        if ($un_used_bonus) {
            CustomerBonus::customerBonusUse($order['phone'], $un_used_bonus['bonus'], $order['order_id'], $order['booking_time']);
        }

        $bonus = CustomerBonus::getOrderBonus($order['phone'], $order['order_id']);

        if ($bonus && $bonus['created'] < $order['call_time']) {
            echo '---' . $bonus['bonus'] . "\n";
            $arrStaticBonus = BonusType::model()->getStaticBonus();

            if (in_array($bonus['bonus_type_id'], $arrStaticBonus))
                $bonus_sn = $bonus['bonus'];
            else
                $bonus_sn = $bonus['bonus'] . $bonus['parity_bit'];

            EmployeeAccount::model()->deleteAll('order_id=:order_id and type=:type', array(
                ':order_id' => $order['order_id'],
                ':type' => EmployeeAccount::TYPE_BONUS_CASH
            ));

            $account = new EmployeeAccount();
            $account->attributes = array(
                'type' => EmployeeAccount::TYPE_BONUS_CASH,
                'user' => $order['driver_id'],
                'order_id' => $order['order_id'],
                'comment' => '优惠券充值 号码：' . $bonus_sn, // $bonus['bonus'].$bonus['parity_bit'],
                'cast' => $bonus['money'],
                'created' => $order['created']
            );

            $account->addAccount();

            // 给发卡司机返点//司机发卡优惠券
            if ($bonus['bonus_type_id'] == 8) {
                $city_id = substr($bonus_sn, 0, 2);
                $driver_id = substr($bonus_sn, 2, 4);
                $cityPrefix = Dict::items("bonus_city");
                $driver = array();

                if (isset($cityPrefix[$city_id])) {
                    $driver_id = $cityPrefix[$city_id] . $driver_id;

                    $model = Driver::model()->find('user=:driver_id', array(
                        ':driver_id' => $driver_id
                    ));
                    if ($model)
                        $driver['driver_id'] = $model->user;

                    $return_type = EmployeeAccount::TYPE_DRIVER_BONUS_RETUEN;
                    $return_cast = 20;
                }
            } else {
                $driver = DriverBonus::model()->getDriverID($bonus_sn); // $bonus['bonus'].$bonus['parity_bit']);
                $return_type = EmployeeAccount::TYPE_BONUS_RETUEN;
                $return_cast = 5;
            }

            if ($driver) {
                EmployeeAccount::model()->deleteAll('order_id=:order_id and type=:type', array(
                    ':order_id' => $order['order_id'],
                    ':type' => $return_type
                ));

                $account = new EmployeeAccount();
                $account->attributes = array(
                    'type' => $return_type, // EmployeeAccount::TYPE_BONUS_RETUEN,
                    'user' => $driver['driver_id'],
                    'order_id' => $order['order_id'],
                    'comment' => '优惠券返现 号码：' . $bonus_sn, // $bonus['bonus'].$bonus['parity_bit'],
                    'cast' => $return_cast, // 5,
                    'created' => $order['created']
                );
                $account->addAccount();
            }
            $bonus_name = BonusType::model()->getBonusName($bonus['bonus_type_id']);

            if ($notify_sms) {
                $cash = $order['income'] - $bonus['money'];
                $sms_message = MessageText::getFormatContent(MessageText::CUSTOMER_BONUS_USED, date("Y-m-d", $order['created']), $order['income'], $bonus_name, $bonus_sn, $bonus['money'], $cash);
                Sms::SendSMS($order['phone'], $sms_message);
            }
        }
    }

    /**
     *
     * 普通订单的优惠券补帐收入记录
     *
     * @param array $order
     */
    public function deal_order_bonus_fix($order_id)
    {
        $command = Yii::app()->db->createCommand();
        $command->select('*')->from('t_bonus_fixed_log')->where('order_id =:order_id', array(
            ':order_id' => $order_id
        ));

        $bonus_fix = $command->queryRow();

        if ($bonus_fix) {
            EmployeeAccount::model()->deleteAll('order_id=:order_id and type=:type', array(
                ':order_id' => $bonus_fix['order_id'],
                ':type' => EmployeeAccount::TYPE_BONUS_FIXED
            ));

            $account = new EmployeeAccount();
            $account->attributes = array(
                'type' => EmployeeAccount::TYPE_BONUS_FIXED,
                'user' => $bonus_fix['driver_id'],
                'order_id' => $bonus_fix['order_id'],
                'comment' => $bonus_fix['comment'],
                'cast' => $bonus_fix['cast'],
                'created' => $bonus_fix['created']
            );
            $account->addAccount();
        }
    }

    /**
     *
     *
     *
     *
     * 计算各个城市的计费标准
     * 标准城市：
     * 扣除每单的信息费用
     * 7点到22点：5元.
     * 22点到23点：10元
     * 23点到0点：15元
     * 0点到6点：20元
     * 重庆：
     * 每单10元
     *
     * @param int $city_id
     * @param int $calltime
     * 订单呼叫时间
     */
    private function city_fee($city_id, $calltime)
    {
        $cast = 10;

        switch ($city_id) {
            // 杭州
            case 4 :
                $hour = date('H', $calltime);
                $minutes = date('i', $calltime);
                // 换算为分钟后计算
                $min_sum = $hour * 60 + $minutes;

                if ($min_sum >= 60 * 7 and $min_sum < 60 * 22) {
                    $cast = 5;
                } else {
                    $cast = 10;
                }
                break;
            // 重庆
            case 7 :
                $cast = 10;
                break;
            default :
                $hour = date('H', $calltime);
                $minutes = date('i', $calltime);
                // 换算为分钟后计算
                $min_sum = $hour * 60 + $minutes;

                if ($min_sum >= 60 * 7 and $min_sum < 60 * 22) {
                    $cast = 5;
                } elseif ($min_sum >= 60 * 22 and $min_sum < 60 * 23) {
                    $cast = 10;
                } elseif ($min_sum >= 60 * 23 and $min_sum < 60 * 24) {
                    $cast = 15;
                } elseif ($min_sum >= 0 and $min_sum < 60 * 9) {
                    $cast = 20;
                }
                break;
        }
        return $cast;
    }

    public function insert_deal_error_log($order_id)
    {
        $deal_order_id = Yii::app()->dbstat_readonly->createCommand()->select('order_id')->from('t_order_deal_error_log')->where('order_id =:order_id', array(
            ':order_id' => $order_id
        ))->queryScalar();
        if (!$deal_order_id) {
            $attributes = array(
                'order_id' => $order_id,
                'type' => 0
            );
            @Yii::app()->dbstat->createCommand()->insert('t_order_deal_error_log', $attributes);
        }
    }

    public function update_deal_error_log($order_id)
    {
        @Yii::app()->dbstat->createCommand("UPDATE t_order_deal_error_log SET type = 1 WHERE order_id=:order_id")->bindParam(":order_id", $order_id)->execute();
    }

    public function search()
    {
        $criteria = new CDbCriteria();
        if ($this->type != 0) {
            $criteria->compare('type', $this->type);
        }
        $criteria->compare('user', $this->user);
        $criteria->compare('order_id', $this->order_id);

        $this->created = empty($this->created) ? strtotime(self::$table_name . '01') : strtotime($this->created);

        if (!empty($this->created) && empty($this->settle_date)) {
            $this->settle_date = strtotime(date('Y-m-31 23:59:59', $this->created));
        } else {
            $this->settle_date = empty($this->settle_date) ? strtotime(self::$table_name . '31235959') : strtotime($this->settle_date);
        }

        $criteria->addBetweenCondition('created', $this->created, $this->settle_date);
        $criteria->compare('comment', $this->comment, true);
        if ($this->id) {
            $criteria->compare('comment', $this->id, true);
        }
        $criteria->order = 'id desc';

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 30
            )
        ));
    }


    public function manual_search()
    {
        $criteria = new CDbCriteria();
        if (!empty($this->order_id)) {
            $criteria->compare('order_id', $this->order_id);
        } else {
            $criteria->condition = '1 != 1';
        }
        $criteria->order = 'id desc';

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 30
            )
        ));
    }

    public function getSearchTotalMoney($data)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'sum(cast) as cast';
        if (!empty($data['type'])) {
            $criteria->compare('type', $data['type']);
        }

        if (isset($data['user'])) {
            $criteria->compare('user', $data['user']);
        }
        if (!empty($data['order_id'])) {
            $criteria->compare('order_id', $data['order_id']);
        }
        $data['created'] = empty($data['created']) ? strtotime(date('Y-m-1 00:00:00')) : strtotime($data['created']);
        $data['settle_date'] = empty($data['settle_date']) ? strtotime(date('Y-m-31 23:59:59')) : strtotime($data['settle_date']);
        $criteria->addBetweenCondition('created', $data['created'], $data['settle_date']);

        if (isset($data['comment'])) {
            $criteria->compare('comment', $data['comment'], true);
        }

        if (isset($data['id'])) {
            $criteria->compare('comment', $data['id'], true);
        }
        return $this->find($criteria);
    }


    /**
     * 获取t_employee_account 汇总信息
     */
    public function getEmployeeAccount($city_id = 0)
    {
        $params = array();
//		当月的数据信息，去除测试用户
        $where = "is_settle = 0";

        if ($city_id != 0) {
            $where .= " and left(user,2) = :city_prefix";
            $params[':city_prefix'] = Dict::item('city_prefix', $city_id);
        }

        $where .= " and user not in (" . Common::getTestDriverID() . ") ";
//		$params[':testAccount'] = Common::TESTACCOUNT;
        return Yii::app()->db_finance->createCommand()->select('sum(if(type=0,cast,0)) as t0,
									sum(if(type=1,cast,0)) as t1,
									sum(if(type=2,cast,0)) as t2,
									sum(if(type=3,cast,0)) as t3,
									sum(if(type=4,cast,0)) as t4,
									sum(if(type=5,cast,0)) as t5,
									sum(if(type=6,cast,0)) as t6,
									sum(if(type=7,cast,0)) as t7,
									sum(if(type=8,cast,0)) as t8,
									sum(if(type=9,cast,0)) as t9,
									sum(if(type=10,cast,0)) as t10,
									sum(cast)-sum(if(type=0,cast,0)) as totle')
            ->from('t_employee_account')
            ->where($where, $params)
            ->queryRow();
    }

    /**
     * 根据司机工号获取司机的余额
     * @author mengtianxue 2013-05-24
     * @param unknown_type $user
     */
    public function getDriverBalances($user)
    {
        $driverBalance = DriverBalance::model()->getDriverBalance($user);
        if ($driverBalance) {
            $balance = number_format($driverBalance->balance, 2,".","");
        } else {
            $balance = 0.00;
        }
        return $balance;
    }

    /**
     * 司机昨天的收入
     * 收入(0)-保险费(6)-税费(2)-信息费(1)-延迟报单(4)
     * @author mengtianxue 2013-05-24
     * @param unknown_type $user
     */
    public function getDriverYesterdayIncome($user)
    {
        $income = 0.00;
        $endTime = strtotime(date('Y-m-d 00:00:00'));
        $startTime = $endTime - 86400;
        $table_name = "t_employee_account_" . date("Ym", $startTime);
        $yestday_income = Yii::app()->db_finance->createCommand()
            ->select("SUM( IF( (`type` = 0), `cast` , 0 )) as cast")
            ->from($table_name)
            ->where('user = :user and created between :startTime and :endTime',
                array(':user' => $user, ':startTime' => $startTime, ':endTime' => $endTime))
            ->queryScalar();
        if (!empty($yestday_income)) {
            $income = number_format($yestday_income, 2,".","");
        }
        return $income;
    }

    /**
     * 司机当月的收入
     * 收入(0)-保险费(6)-税费(2)-信息费(1)-延迟报单(4)
     * @author mengtianxue 2013-05-24
     * @param unknown_type $user
     */
    public function getDriverMonthIncome($user)
    {
        $income = 0.00;
        $table_name = "t_employee_account_" . date("Ym");
        $month_income = Yii::app()->db_finance->createCommand()
            ->select("SUM( IF( (`type` =0), `cast` , 0 ))  AS cast")
            ->from($table_name)
            ->where('user = :user', array(':user' => $user))
            ->queryScalar();
        if (!empty($month_income)) {
            $income = number_format($month_income, 2,".","");
        }
        return $income;
    }

    /**
     * 司机历史收入（除当月的）
     * 收入(t0)-保险费(t6)-税费(t2)-信息费(t1)-延迟报单(t4)
     * @author mengtianxue 2013-05-24
     * @param unknown_type $user
     */
    public function getDriverHistoryIncome($user)
    {
        $income = 0;
        $history_income = Yii::app()->db_finance->createCommand()
            ->select("SUM(t0) AS cast")
            ->from("t_employee_account_settle")
            ->where('user = :user', array(':user' => $user))
            ->queryScalar();
        if (!empty($history_income)) {
            $income = $history_income;
        }
        return $income;
    }

    /**
     * 通过司机工号获取司机当前的收入和余额信息
     * @author mengtianxue 2013-05-24
     * @param unknown_type $user
     */
    public function getDriverAmount($user)
    {
        $return = array();
        $return['balance'] = $this->getDriverBalances($user);;

        $return['yesterday'] = $this->getDriverYesterdayIncome($user);
        $return['month'] = $this->getDriverMonthIncome($user);
        $total = $this->getDriverHistoryIncome($user) + $return['month'];
        $return['total'] = number_format($total, 2,".","");
        return $return;
    }


    /**
     * 更新司机redis信息
     * @param $driver_id
     * @return bool
     * author mengtianxue
     */
    public function ReloadDriverAmountRedis($driver_id)
    {
        if ($driver_id) {
            $driver = DriverStatus::model()->get($driver_id);
            if ($driver) {
                $driverAmount = EmployeeAccount::model()->getDriverAmount($driver_id);
                $driver->account = $driverAmount;
                return true;
            }
        }
        return false;
    }

    /**
     * 获取历史优惠劵返现
     * @return mixed
     * author mengtianxue
     */
    public function getBonusHistoryReturn()
    {
        $history_income = Yii::app()->db_finance->createCommand()
            ->select("SUM(t7) AS t7,SUM(t8) AS t8,SUM(t9) AS t9,SUM(t10) AS t10")
            ->from("t_employee_account_settle")
            ->queryRow();
        return $history_income;
    }


    /**
     * 获取当月司机总收入
     * @param int $city_id
     * @return mixed
     * author mengtianxue
     */
    public function getMonthIncome($city_id = 0)
    {
        $where = "id > 0";
        $params = array();

        if ($city_id != 0) {
            $city_code = Common::getCityCode($city_id);
            $where .= " and left(user,2) = :city_code";
            $params[':city_code'] = $city_code;
        }
        $where .= " and user not in (" . Common::getTestDriverID() . ") ";
        $table_name = "t_employee_account_" . date("Ym");
        $month_income = Yii::app()->db_finance->createCommand()
            ->select("SUM( IF( (`type` = 0), 1, 0 ))  AS order_count,
                    SUM( IF( (`type` = 0), `cast` , 0 ))  AS cast_0,
                    SUM( IF( (`type` = 1), `cast` , 0 ))  AS cast_1,
                    SUM( IF( (`type` = 2), `cast` , 0 ))  AS cast_2,
                    SUM( IF( (`type` = 3), `cast` , 0 ))  AS cast_3,
                    SUM( IF( (`type` = 4), `cast` , 0 ))  AS cast_4,
                    SUM( IF( (`type` = 5), `cast` , 0 ))  AS cast_5,
                    SUM( IF( (`type` = 6), `cast` , 0 ))  AS cast_6,
                    SUM( IF( (`type` = 7), `cast` , 0 ))  AS cast_7,
                    SUM( IF( (`type` = 8), `cast` , 0 ))  AS cast_8,
                    SUM( IF( (`type` = 9), `cast` , 0 ))  AS cast_9,
                    SUM( IF( (`type` = 10), `cast` , 0 ))  AS cast_10")
            ->from($table_name)
            ->where($where, $params)
            ->queryRow();

        return $month_income;
    }


    public function getYesterdayIncome($city_id = 0)
    {
        $time = strtotime("-1 day");
        $table_name = "t_employee_account_" . date("Ym", $time);
        $order_date_start = strtotime(date("Y-m-d 00:00:00", $time));
        $order_date_end = $order_date_start + 86400;

        $where = "id > 0 and created between :order_date_start and :order_date_end";
        $params = array(':order_date_start' => $order_date_start, ':order_date_end' => $order_date_end);
        if ($city_id != 0) {
            $where .= " and city_id = :city_code";
            $params[':city_code'] = $city_id;
        }
        //print_r($params);
        $where .= " and user not in (" . Common::getTestDriverID() . ") ";

        $month_income = Yii::app()->db_finance->createCommand()
            ->select("SUM( IF( (`type` = 0), 1, 0 ))  AS order_count,
                    SUM( IF( (`type` = 0), `cast` , 0 ))  AS cast_0,
                    SUM( IF( (`type` = 1), `cast` , 0 ))  AS cast_1,
                    SUM( IF( (`type` = 2), `cast` , 0 ))  AS cast_2,
                    SUM( IF( (`type` = 3), `cast` , 0 ))  AS cast_3,
                    SUM( IF( (`type` = 4), `cast` , 0 ))  AS cast_4,
                    SUM( IF( (`type` = 5), `cast` , 0 ))  AS cast_5,
                    SUM( IF( (`type` = 6), `cast` , 0 ))  AS cast_6,
                    SUM( IF( (`type` = 7), `cast` , 0 ))  AS cast_7,
                    SUM( IF( (`type` = 8), `cast` , 0 ))  AS cast_8,
                    SUM( IF( (`type` = 9), `cast` , 0 ))  AS cast_9,
                    SUM( IF( (`type` = 10), `cast` , 0 ))  AS cast_10")
            ->from($table_name)
            ->where($where, $params)
            ->queryRow();
        return $month_income;
    }


    /**
     * 获取充值详情
     * @param $params
     * @return array
     * @auther mengtianxue
     */
    public function getIncomeListByChannel($params)
    {
        $min_id = isset($params['min_id']) ? $params['min_id'] : '0';
        $count = isset($params['count']) ? $params['count'] : '10';
        $channel = isset($params['channel']) ? $params['channel'] : '25';
        $driver_id = isset($params['driver_id']) ? $params['driver_id'] : 'BJ9000';
        $datetime = isset($params['datetime']) ? $params['datetime'] : date('Ym');
        $table_name = "t_employee_account_" . $datetime;

        //条件
        $where = 'id > 0 and user = :driver_id';
        $params_arr = array(':driver_id' => $driver_id);
        if (!empty($channel)) {
            $where .= " and channel=:channel";
            $params_arr[':channel'] = $channel;
        }

        if (!empty($min_id)) {
            $where .= " and id < :id";
            $params_arr[':id'] = $min_id;
        }

        $orderList = $this->getQueryAll($table_name, $where, $params_arr, $count);

        $num = count($orderList);
        $num_pro = $count - $num;
        if (count($orderList) < $count) {
            $datetime = $datetime . "01";
            $datetime = date('Ym', strtotime('-1 month', strtotime($datetime)));
            $table_name_pro = "t_employee_account_" . $datetime;
            $orderList_pro = $this->getQueryAll($table_name_pro, $where, $params_arr, $num_pro);
            $orderList = $orderList + $orderList_pro;
        }

        foreach ($orderList as $k => $v) {
            if (empty($orderList[$k]['balance'])) {
                $orderList[$k]['balance'] = '0.00';
            }
            $orderList[$k]['created'] = date('Y-m-d H:i:s', $v['created']);
        }


        return array(
            'datetime' => $datetime,
            'orderList' => $orderList,
        );
    }

    /**
     * 获取信息
     * @param $table_name
     * @param $where
     * @param $params
     * @param $count
     * @return mixed
     * @auther mengtianxue
     */
    public function getQueryAll($table_name, $where, $params, $count)
    {
        $back = Yii::app()->db_finance->createCommand()
            ->select("id, user, type, channel, order_id, cast, balance, created")
            ->from($table_name)
            ->where($where, $params)
            ->order('id DESC')
            ->limit($count)
            ->queryAll();
        return $back;
    }

	/**
	 * 获得最后一条流水号
	 *
	 * @param $orderId
	 * @return int
	 */
	public function  getLastEmployeeAccountId($orderId){
		$criteria = new CDbCriteria;
		$criteria->compare('order_id', $orderId);
		$criteria->order = ' id DESC';
		$criteria->limit = 1;
		$model = self::model()->find($criteria);
		if(empty($model)){
			return 0;
		}else{
			return $model->id;
		}
	}

    /**
    *   分页获取信息费账户明细
    *   aiguoxin
    */
    public function getInfoAccountList($driver_id,$page,$pageSize,$tabledate){
        $tablename = 't_employee_account_' . $tabledate;
        $limitStart = ($page-1)*$pageSize;

        $sql = "select `type`,`created`,`cast`,`balance`,`channel`
            from `$tablename`
            where user='{$driver_id}'
            and type!=0
            order by id desc
            limit ".$limitStart.",".$pageSize;
        $models = self::model()->findAllBySql($sql);
        $data = array();
        if(!empty($models)){
            foreach($models as $k=> $model){
                if($model->type < 0 || $model->type > 13){
                    continue;
                }
				$description = self::$accountStatus[$model->type];
				$channel = $model['channel'];
				if(array_key_exists($channel, self::$channelMessage)){
					$cast = $model['cast'];
					if((self::CHANNEL_REMOTE_ORDER_TIMEOUT != $channel) && (0 == $cast)){
						//为0的不展示,但是补贴超时除外
						continue;
					}
					$description = $this->getMessageByChannel($channel);
				}
                $data[] = array(
                    'money' => $model->cast,
                    'create_time' => $model->created,
                    'balance' => $model->balance,
                    'description'=> $description,
                    );
            }
        }
        return $data;
    }

    /**
    *   分页获取信息费账户收入
    *   aiguoxin
    */
    public function getAccountIncomeList($driver_id,$page,$pageSize,$tabledate){
        $tablename = 't_employee_account_' . $tabledate;
        $limitStart = ($page-1)*$pageSize;

        $sql = "select `type`,`created`,`cast`,`balance`
            from `$tablename`
            where user='{$driver_id}'
            and cast>0
            order by id desc
            limit ".$limitStart.",".$pageSize;
        $models = self::model()->findAllBySql($sql);
        $data = array();
        if(!empty($models)){
            foreach($models as $k=> $model){
                $data[] = array(
                    'money' => $model->cast,
                    'create_time' => $model->created,
                    'total' => $model->balance,
                    'name'=>self::$accountStatus[$model->type],
                    );
            }
        }
        return $data;
    }
}
