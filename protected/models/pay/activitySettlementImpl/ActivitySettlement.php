<?php
/**
 * 活动结账
 *
 * User: tuan
 * Date: 9/28/14
 * Time: 16:17
 */
abstract class ActivitySettlement extends  Settlement{

	private $activity;

	private $order;

	private $orderExt;

	private $vip;

	private $customer;

	private $driver;

	private  $activityType = '';

	public function __construct($activity, $order, $orderExt){
		$this->setActivity($activity);
		$this->setOrder($order);
		$this->setOrderExt($orderExt);
		$this->setActivityType($activity->getActivityType());
	}

	/** 处理vip */
	abstract function orderVip();
	/** 处理normal customer */
	abstract  function orderNormal();
	/** 处理 driver */
	abstract  function orderDriver();

	/**
	 * 新客优惠活动结账
	 */
	protected  function settlement(){
			return false;
	}

	/**
	 * 活动是否开启
	 *
	 * @return bool
	 */
	protected function isActivityOn(){
		try{
			return CityBasedActivity::isActivityApplied($this->getOrder(), $this->getActivityType());
		}catch (Exception $e){
			EdjLog::error($e->getMessage());
			EdjLog::info('order ---'.serialize($this->getOrder()).' activity ----'.serialize($this->getActivity()));
			return false;
		}
	}

	protected  function init(){
		if($this->legalOrder()){
			$order = $this->getOrder();
			$customerPhone = $order['phone'];
			$vipCard  = $order['vipcard'];
			if(!empty($vipCard)){
                $vip = VipService::service()->getVipInfoByVipCard($vipCard, false);
                $this->setVip($vip);
			}else{
                $customerAccount = NormalAccountService::forceGetUserAmount($customerPhone);
				$this->setCustomer(array(
					'id'	=> $customerAccount['user_id'],
					'amount'=> $customerAccount['amount'],
				));
			}
			//set driver
			$this->setDriver($order['driver_id']);
		}
	}

	protected function legalActivity(){
		return (null != $this->getActivity())  && ($this->getActivity() instanceof CityBasedActivity);// TODO ... 需要是活动那边定义的活动的实例
	}

	protected function legalOrder(){
		return (null != $this->getOrder()) && is_array($this->getOrder());
	}

	protected function legalOrderExt(){
		return (null != $this->orderExt);
	}

	/**
	 * @param mixed $activity
	 */
	public function setActivity($activity)
	{
		$this->activity = $activity;
	}

	/**
	 * @return mixed
	 */
	public function getActivity()
	{
		return $this->activity;
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
	 * @param mixed $customer
	 */
	public function setCustomer($customer)
	{
		$this->customer = $customer;
	}

	/**
	 * @return mixed
	 */
	public function getCustomer()
	{
		return $this->customer;
	}

	/**
	 * @param mixed $driver
	 */
	public function setDriver($driver)
	{
		$this->driver = $driver;
	}

	/**
	 * @return mixed
	 */
	public function getDriver()
	{
		return $this->driver;
	}

	/**
	 * @param mixed $vip
	 */
	public function setVip($vip)
	{
		$this->vip = $vip;
	}

	/**
	 * @return mixed
	 */
	public function getVip()
	{
		return $this->vip;
	}

	/**
	 * @param mixed $activityType
	 */
	public function setActivityType($activityType)
	{
		$this->activityType = $activityType;
	}

	/**
	 * @return mixed
	 */
	public function getActivityType()
	{
		if($this->getActivity()){
			return $this->getActivity()->getActivityType();
		}
		return $this->activityType;
	}

}