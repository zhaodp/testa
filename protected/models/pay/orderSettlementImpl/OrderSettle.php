<?php
/**
 * 订单结账的基类
 *
 * User: tuan
 * Date: 14/10/27
 * Time: 14:54
 */

class OrderSettle  extends Settlement{

	private $mailTitle = '[请关注]财务订单结算报警';
	/** 订单相关报警邮件内容格式  */
	private static $MAIL_CONTENT_FORMAT = 'content|%s|order_id|%s|driver_id|%s|user|%s|income|%s|price|%s|';

	/** 结束可以结账的订单状态集合 */
	protected static $ORDER_END_STATUS = array(
		Order::ORDER_COMPLATE,
		Order::ORDER_NOT_COMFIRM,
	);

	/** 订单 */
	private $order;
	/** 订单额外的信息 */
	private $orderExt;
	/** 优惠金额   */
	private $favorable;

	/** 回调接口列表 */
	private $callList = array();

	public function __construct($order, $orderExt, $favorable){
		$this->setOrder($order);
		$this->setOrderExt($orderExt);
		$this->setFavorable($favorable);
//		$this->setCallList($callList);
	}

	protected function init(){

	}

	protected function isVip(){
		$vipcard = $this->getVipCard();
		if(empty($vipcard) && 1 == $this->getCostType()){
		}
		return !empty($vipcard);
	}

	/**
	 * 报警
	 *
	 * @param $message
	 */
	protected function alarm($message){
		try {
			if (empty($message)) {
				$message = $this->mailTitle;
			}
			$content = sprintf(self::$MAIL_CONTENT_FORMAT, $message, $this->getOrderId(), $this->getDriverId(),
				$this->getUserPhone(), $this->getIncome(), $this->getPrice());
			FinanceUtils::sendFinanceAlarm($this->mailTitle, $content);
		} catch (Exception $e) {
		}
	}

	/**
	 * 回调
	 */
	protected  function callBack(){
		$callList = $this->getCallList();
		if(empty($callList)){
			return;
		}
		foreach($callList as $iFinance){
			try{
				if($iFinance instanceof IFinance){
					$iFinance->callback();
				}
			}catch (Exception $e){
				EdjLog::error('call back error'.$e->getMessage());
			}
		}
	}
	protected function getFee(){
		$orderExt = $this->getOrderExt();
		return FinanceCastHelper::getOrderFeeByExt($orderExt);
	}

	protected function getIncome(){
		$order = $this->getOrder();
		return $order['income'];
	}

	protected function getPrice(){
		$order = $this->getOrder();
		return $order['price'];
	}

	protected function getCostType(){
		$order = $this->getOrder();
		return $order['cost_type'];
	}

	protected function getOrderId(){
		$order = $this->getOrder();
		return $order['order_id'];
	}

	protected function getUserPhone(){
		$order = $this->getOrder();
		return $order['phone'];
	}

	protected function getVipCard(){
		$order = $this->getOrder();
		return $order['vipcard'];
	}

	protected function getOrderCreated(){
		$order = $this->getOrder();
		return $order['created'];
	}

	protected function getCityId(){
		$order = $this->getOrder();
		return $order['city_id'];
	}

	protected function getDriverId(){
		$order = $this->getOrder();
		return $order['driver_id'];
	}


	/**
	 * 或者实体卡金额
	 *
	 * @return int
	 */
	protected  function getCoupon(){
		$order = $this->getOrder();
		$coupon = 0;
		//实体卡金额
		$coupon_money = 0;
		$order_ext = $this->getOrderExt();
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
	 * 订单校验器
	 *
	 * @throws Exception 订单为空 或者 订单状态为结束跑出异常
	 */
	protected function orderValidator(){
		$order = $this->getOrder();
		if(is_null($order)){
			throw new Exception('order can not be null!');
		}
		$status = $order['status'];
		if(!in_array($status, self::$ORDER_END_STATUS)){
			throw new Exception('order must finished!');
		}
	}

	/**
	 * 结账,必须重写
	 */
	protected function settlement()
	{
		return array(
			'code'	=> FinanceConstants::CODE_FAIL,
		);
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
	 * @param mixed $orderExt
	 */
	public function setOrderExt($orderExt)
	{
		$this->orderExt = $orderExt;
	}

	/**
	 * @return mixed
	 */
	public function getOrderExt()
	{
		return $this->orderExt;
	}

	/**
	 * @param mixed $callList
	 */
	public function setCallList($callList)
	{
		$this->callList = $callList;
	}

	/**
	 * @return mixed
	 */
	public function getCallList()
	{
		return $this->callList;
	}

	/**
	 * @param mixed $favorable
	 */
	public function setFavorable($favorable)
	{
		$this->favorable = $favorable;
	}

	/**
	 * @return mixed
	 */
	public function getFavorable()
	{
		if(is_null($this->favorable)){
			return 0.00;
		}
		return $this->favorable;
	}

}