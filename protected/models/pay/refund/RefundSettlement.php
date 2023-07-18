<?php
/**
 * 司机账户退单
 *
 * User: tuan
 * Date: 14/11/24
 * Time: 10:07
 */
class RefundSettlement extends Settlement{

	/** 司机流水  */
	private $employeeAccountList = array();
	/** order  */
	private $order ;

	function __construct($order)
	{
		$this->order = $order;
	}

	public function init(){
		//获得流水记录
		$list = $this->getAccountList();
		$this->setEmployeeAccountList($list);
	}


	/**
	 * 结账,必须重写
	 */
	public function settlement(){
		$this->init();
		//1.check
		$order = $this->getOrder();
		$list  = $this->getEmployeeAccountList();
		if(empty($order) || empty($list)){
			return;
		}
		$orderId = $order['order_id'];
		//2.refund driver
		$this->orderDriver();
		//3.insert refund log
		$this->insertRefundLog();
		//4.refund bonus
		$this->refundBonus();
	}

	/**
	 * 回退优惠券
	 */
	private function refundBonus(){
		$order = $this->getOrder();
		//不区分 vip 和普通用户
		CustomerBonus::model()->refundCustomerBonus($order['phone'], $order);
	}

	private function insertRefundLog(){
		$order = $this->getOrder();
		$orderId = $order['order_id'];
		$refundLog = RefundLog::model()->findByPk($orderId);
		EmployeeAccount::$table_name = date('Ym', time());
		$employeeAccountID = EmployeeAccount::model()->getLastEmployeeAccountId($orderId);
		if($refundLog){
			$refundLog['refund_count'] += 1;
			$refundLog['employee_account_id'] = $employeeAccountID;
		}else{
			$refundLog = new RefundLog();
			$refundLog->order_id = $orderId;
			$refundLog->refund_count = 1;
			$refundLog['employee_account_id'] = $employeeAccountID;
		}
		$refundLog->create_time = time();
		if($refundLog->save()){
			EdjLog::info('refund log save success '.$orderId.'|'.$refundLog->employee_account_id);
		}else{
			EdjLog::info('refund log save fail '.$orderId.'|'.$refundLog->employee_account_id);
		}
	}

	private function orderDriver(){
		$list = $this->getEmployeeAccountList();
		foreach($list as $record){
			$type = $record['type'];
			// 把退单金额还给用户
			$params = array(
				'type' => $type,
				'channel' => $record['channel'],
				'city_id' => $record['city_id'],
				'user' => $record['user'],
				'order_id' => $record['order_id'],
				'comment' => '重结退还:' . $record['comment'],
				'cast' => $this->refundCast($record),
				'order_date' => $this->getOrder()['created'],
			);
			$ret = $this->chargeDriver($params);
			if($ret){//退司机款成功,才退用户的款
				//order customer
				$this->orderCustomer($record);
			}else{
			}
		}
	}

	private function orderCustomer($employeeAccount){
		if(empty($employeeAccount)){
			return true;
		}
		$this->orderVip($employeeAccount);
		$this->orderNormal($employeeAccount);
	}

	private function orderNormal($employeeAccount){
		$normalUserAccountChannel = array(
			EmployeeAccount::TYPE_ORDER_NORMAL,
			EmployeeAccount::CHANNEL_REMOTE_ORDER_NORMAL,
			EmployeeAccount::CHANNEL_REMOTE_ORDER_NORMAL_TIPS,

			EmployeeAccount::CHANNEL_DAYTIME_ORDER_TIME_COST,
			EmployeeAccount::CHANNEL_DAYTIME_ORDER_SUBSIDY,
			EmployeeAccount::CHANNEL_DAYTIME_ORDER_SUBSIDY_BACK,
			EmployeeAccount::CHANNEL_ORDER_UNIT,
		);
		$channel = isset($employeeAccount['channel']) ? $employeeAccount['channel'] : 0;
		if(!in_array($channel, $normalUserAccountChannel)){
			return true;
		}
		//charge normal
		$order = $this->getOrder();
		$userId = isset($order['user_id']) ? $order['user_id'] : 0;
		$phone = $order['phone'];
		if(empty($userId)){
			$customer	= CustomerService::service()->getCustomerInfo($phone,1);
			$userId		= $customer->id;
		}
        $customerAccount = NormalAccountService::getUserAmount($phone);
		$customerBalance = isset($customerAccount['amount']) ? $customerAccount['amount'] : 0;
		$delta = $employeeAccount['cast'];
		$comment = $employeeAccount['comment'];
		$customerTransAttributes = array(
			'user_id' => $userId,
			'trans_order_id' => $order['order_id'],
			'trans_type' => CarCustomerTrans::TRANS_TYPE_F,
			'amount' => $delta,
			'balance' => $customerBalance + $delta,
			'source' => CarCustomerTrans::TRANS_SOURCE_S,
			'remark' => '退单重结'.$comment,
		);
		$customerAccountAttributes = array(
			'user_id' => $userId,
			'amount' => $delta,
		);
		$this->chargeNormal($customerTransAttributes, $customerAccountAttributes);
	}

	private function orderVip($employeeAccount){
		$vipAccountChannel = array(
			EmployeeAccount::TYPE_ORDER_VIP,
			EmployeeAccount::CHANNEL_REMOTE_ORDER_VIP,
			EmployeeAccount::CHANNEL_REMOTE_ORDER_VIP_TIPS,

			EmployeeAccount::CHANNEL_DAYTIME_ORDER_TIME_COST,
			EmployeeAccount::CHANNEL_DAYTIME_ORDER_SUBSIDY,
			EmployeeAccount::CHANNEL_DAYTIME_ORDER_SUBSIDY_BACK,
			EmployeeAccount::CHANNEL_ORDER_UNIT,

		);
		$channel = isset($employeeAccount['channel']) ? $employeeAccount['channel'] : 0;
		if(!in_array($channel, $vipAccountChannel)){
			return true;
		}
		$order = $this->getOrder();
		$vipCard = isset($order['vipcard']) ? $order['vipcard'] : 0;
		if(empty($vipCard)){
			$phone = $order['phone'];
			$vipPhone = VipPhone::model()->getPrimary($phone);
			if($vipPhone){
				$vipCard = $vipPhone['vipid'];
			}
		}
        $vip = VipService::service()->getVipInfoByVipCard($vipCard, false);
        $vipBalance = isset($vip['amount']) ? $vip['amount'] : 0;
		$delta = $employeeAccount['cast'];
		$comment = $employeeAccount['comment'];
		$vipTradeAttributes = array(
			'vipcard' => $vipCard,
			'order_id' => $order['order_id'],
			'type' => VipTrade::TYPE_INCOME,
			'source' => VipTrade::TRANS_SOURCE_D,
			'amount' => $delta,
			'comment' => '退单重结'.$comment,
			'balance' => $vipBalance + $delta,
			'order_date' => $order['created'],
		);

		$vipBalanceAttributes = array(
			'vipCard' => $vipCard,
			'delta' => $delta,
		);
		$this->chargeVip($vipTradeAttributes, $vipBalanceAttributes);
	}

	/**
	 * @param mixed $employeeAccountList
	 */
	public function setEmployeeAccountList($employeeAccountList)
	{
		$this->employeeAccountList = $employeeAccountList;
	}

	/**
	 * @return mixed
	 */
	public function getEmployeeAccountList()
	{
		return $this->employeeAccountList;
	}

	/**
	 * @param mixed $order
	 */
	public function setOrder($order)
	{
		$this->order = $order;
	}

	/**
	 * @return mixed
	 */
	public function getOrder()
	{
		return $this->order;
	}

	/**
	 * 格式化退还的金额
	 *
	 * @param $record
	 * @return mixed
	 */
	private function  refundCast($record)
	{
		$cast = $record['cast'];
		// 坑
		switch ($record['type']) {
			case EmployeeAccount::TYPE_ORDER_VIP :
			case EmployeeAccount::TYPE_ORDER_CASH :
			case EmployeeAccount::TYPE_INFOMATION :
			case EmployeeAccount::TYPE_BONUS_CASH :
			case EmployeeAccount::TYPE_BONUS_RETUEN :
			case EmployeeAccount::TYPE_DRIVER_BONUS_RETUEN :
			case EmployeeAccount::TYPE_BONUS_FIXED :
			case EmployeeAccount::TYPE_ORDER_NORMAL :
			case EmployeeAccount::TYPE_ACTIVE :
				$cast = $cast * -1;
				break;
			case EmployeeAccount::TYPE_ORDER_INFOMATION :
			case EmployeeAccount::TYPE_ORDER_INVOICE :
			case EmployeeAccount::TYPE_FORFEIT :
			case EmployeeAccount::TYPE_ORDER_INSURANCE :
				break;
		}
		return $cast;
	}

	private function getAccountList(){
		$order = $this->getOrder();
		$orderId = $order['order_id'];
		$refundLog = RefundLog::model()->findByPk($orderId);
		$boardId = isset($refundLog['employee_account_id']) ? $refundLog['employee_account_id'] : 0;
		$orderTime = isset($order['end_time']) ? $order['end_time'] : $order['created'];
		$list = $this->getEmployeeAccountByOrder($orderId, $orderTime, $boardId);
		return $list;
	}

	/**
	 * 返回司机的能退的流水
	 *
	 * @param $orderId
	 * @param $orderTime
	 * @param $boardId
	 * @return array
	 */
	private function getEmployeeAccountByOrder($orderId, $orderTime, $boardId)
	{
		$currentTime = time();
		$currentMonth = date('Ym', $currentTime);
		$orderMonth = date('Ym', $orderTime);
		$currentList = array();
		//如果当前月份不是和订单月份不是同一份月份,那么看看当前月份有没有流水记录,有就只退当前月份的,这个时候需要注意 boardID 的值
		if($currentMonth != $orderMonth){
			$currentList = $this->getEmployeeAccountListByOrderIdAndMonth($orderId, $currentMonth, $boardId);
		}
		$nextList = $this->getEmployeeAccountListByOrderIdAndMonth($orderId, $orderMonth, $boardId);
		if (empty($currentList)) {
			return $nextList;
		}else{
			return $currentList;
		}
	}

	private  function getEmployeeAccountListByOrderIdAndMonth($orderId, $month, $boardId){
		$where = 'order_id = :orderId and id > :boardId';
		$params = array(
			':orderId'      => $orderId,
			':boardId'      => $boardId,
		);
		return Yii::app()->db_finance->createCommand()
			->select('*')
			->from('t_employee_account_'.$month)
			->where($where,$params)
			->order('id asc')
			->queryAll();
	}
} 
