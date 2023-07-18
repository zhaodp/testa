<?php
/**
 * 里程费计算器, 通过和里程费相关的参数, 城市 id,代驾里程, 预约时间,等待时间
 *
 * User: tuan
 * Date: 14/11/29
 * Time: 11:19
 */
class IncomeCalculator  extends  Calculator{

	/** @var  城市 id */
	private $cityId ;
	/** @var  代驾里程 */
	private $distance;
	/** @var  预约时间 */
	private $bookingTime;
	/** @var  等候时间 */
	private $waitTime;

	public function __construct($bookingTime, $cityId, $distance, $waitTime)
	{
		$this->bookingTime = $bookingTime;
		$this->cityId = $cityId;
		$this->distance = $distance;
		$this->waitTime = $waitTime;
	}


	/** 校验器 */
	public function validator()
	{
		//计算里程,城市 id ,需要去 CityConfig 里面 laod 配置,必须
		return !is_null($this->cityId) && !empty($cityId);
	}

	/**
	 * 返回能够返回该计算器的字符串
	 *
	 * @return string
	 */
	public function toString()
	{
		$format = 'city_id|%s|distance|%s|booking_time|%s|wait_time|%s';
		$excepted = 'not set';
		if($this->isSetExcepted()){
			$excepted = $this->excepted;
		}
		$str_parent = parent::toString();
		$str_this = sprintf($format, $this->cityId, $this->distance,
							$this->bookingTime, $this->waitTime);
		return $str_this.$str_parent;
	}

	/**
	 * 计算里程费
	 *
	 * @return int 如果有异常返回哦
	 */
	protected  function getFee()
	{
		$ret = 0;
		try {
			$ret = CityConfig::model()->calculatorFee($this->cityId,
				$this->distance, $this->bookingTime, $this->waitTime);
		} catch (Exception $e) {
			EdjLog::error(' calculator fee fail' . $e->getMessage());
		}
		return $ret;
	}
}