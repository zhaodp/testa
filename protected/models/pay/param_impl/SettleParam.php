<?php
/**
 * 参数实现
 *
 * User: tuan
 * Date: 14/11/29
 * Time: 13:49
 */
Yii::import('application.models.pay.settlement.*');
class SettleParam  extends IParam{
	/** @var  参数名 */
	private $key;
	/** @var  参数的值 */
	private $value;
	/** @var  订单类型 */
	private $orderType;
	/** @var  是否是 vip */
	private $isVip;

	/** @var  财务这边的配置 */
	private $config;

	function __construct($key, $value, $orderType, $isVip)
	{
		$this->key = $key;
		$this->orderType = $orderType;
		$this->value = $value;
		$this->isVip = $isVip;
		$this->config = FinanceConfigUtil::getConfigByOrderType($orderType);
	}


	/** 是否是 vip */
	public function isVip()
	{
		return $this->isVip;
	}

	/** 用户流水表里面的 type */
	public function getUserType()
	{
		$config = $this->config[$this->key];
		return $this->isVip() ? $config['type_vip'] : $config['type_normal'];
	}

	/** 用户流水表里面的 source */
	public function getUserSource()
	{
		$config = $this->config[$this->key];
		return $this->isVip() ? $config['source_vip'] : $config['source_normal'];
	}

	/** 用户流水表里面的 comment */
	public function getUserComment()
	{
		$config = $this->config[$this->key];
		return $config['comment_user'];
	}

	/**  司机流水表里面的 type */
	public function getDriverType()
	{
		$config = $this->config[$this->key];
		return $this->isVip() ? $config['type_driver_vip'] : $config['type_driver_normal'];
	}

	/**  司机流水表里面的 channel */
	public function getDriverChannel()
	{
		$config = $this->config[$this->key];
		return $this->isVip() ? $config['source_driver_vip'] : $config['source_driver_normal'];
	}

	/**  司机流水表里面的 comment */
	public function getDriverComment()
	{
		$config = $this->config[$this->key];
		return $config['comment_driver'];
	}

	/** 该参数期望的值 */
	public function getExcepted()
	{
		return $this->value;
	}

} 