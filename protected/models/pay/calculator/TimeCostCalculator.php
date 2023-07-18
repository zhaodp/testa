<?php
/**
 * 时间费用计算器
 *
 * User: tuan
 * Date: 14/11/29
 * Time: 13:12
 */
class TimeCostCalculator extends Calculator{

	/** @var 订单类型   */
	private $orderType ;
	/** @var  服务时间 秒 */
	private $time;
	/** @var  城市 id */
	private $cityId;

	function __construct($cityId, $orderType, $time)
	{
		$this->cityId = $cityId;
		$this->orderType = $orderType;
		$this->time = $time;
	}


	/** 校验器 */
	public function validator(){
		return true;// TODO ... 看坤哥那里需要哪些参数
	}

	public function toString()
	{
		$str_parent = parent::toString();
		$str_this = '';
		return parent::toString(); // TODO: Change the autogenerated stub
	}

	protected function getFee()
	{
		$fee = 0.00;
		if(FinanceConstants::ORDER_TYPE_DAYTIME == $this->orderType){
			//load config
			$cityId = $this->cityId;
			$time   = $this->time;
            $daytime_type = CityConfigService::dayTimeStatus($cityId);
			if($daytime_type && isset(Yii::app()->params['daytime_price'][$daytime_type])){
				$day_time_data = Yii::app()->params['daytime_price'][$daytime_type];
				EdjLog::info('config --- '.json_encode($day_time_data));
				$unitPrice  = $day_time_data['price']; // 单价
				$halfCount = 0;
				$minute  = ceil($time / 60); // 先换算为分钟
				if($minute < 60 ){//不足一个小时按照一个小时计算
					$halfCount = 1;
				}else{
					$halfCount = ceil($minute / 60);
				}
				$fee = $unitPrice * $halfCount;
			}
		}
		return $fee;
	}


} 