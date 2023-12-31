<?php
/**
 * 时间距离费用计算器
 *
 * User: wq
 * Date: 15/3/3
 * Time: 13:12
 * 2.5.4版本 日间单计费调整为：29元起步（含1小时、行驶里程10公里）
2.超出部分：每半小时15元，里程每公里收费1元
 */
class TimeDistanceCostCalculator extends Calculator{

    /** @var 订单类型   */
    private $orderType ;
    /** @var  服务时间 秒 */
    private $time;
    /** @var  城市 id */
    private $cityId;
    /** @var  订单实际距离  */
    private $distance;

    function __construct($cityId, $orderType, $time,$distance)
    {
        $this->cityId = $cityId;
        $this->orderType = $orderType;
        $this->time = $time;
        $this->distance = $distance;
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
//        1.29元起步（含1小时、行驶里程10公里）2.超出部分：每半小时15元，里程每公里收费1元
        $fee = 0.00;
        $retArr = array();
        if(FinanceConstants::ORDER_TYPE_DAYTIME == $this->orderType){
            //load config
            $cityId = $this->cityId;
            $time   = $this->time;
            $distance   = $this->distance;
            $daytime_type = CityConfigService::dayTimeStatus($cityId);
            if($daytime_type && isset(Yii::app()->params['daytime_price_new'][$daytime_type])){
                $day_time_data = Yii::app()->params['daytime_price_new'][$daytime_type];
                EdjLog::info('new config --- '.json_encode($day_time_data));
                $unitPrice  = $day_time_data['price']; // 起步价格 29
                $basic_time  = $day_time_data['basic_time']; // 基础时间60
                $basic_distance  = $day_time_data['basic_distance']; // 基础距离10
                $beyond_time_unit  = $day_time_data['beyond_time_unit']; // 超出时间 30
                $beyond_distance_unit  = $day_time_data['beyond_distance_unit']; // 超出距离
                $beyond_time_price  = $day_time_data['beyond_time_price']; // 超出时间补贴的money 15
                $beyond_distance_price  = $day_time_data['beyond_distance_price']; // 超出距离补贴的money
                $halfCount = 0;
                $beyondTimeCost = 0;
                $beyondDistanceCost = 0;

                $minute  = ceil($time / 60); // 先换算为分钟
                $beyondTime = $minute - $basic_time;//超出的时间
                if($beyondTime > 0){
                    if($beyondTime < $beyond_time_unit){
                        $halfCount = 1;
                    }else{
                        $halfCount = ceil($beyondTime / $beyond_time_unit);
                    }
                }
                $beyondTimeCost = $beyond_time_price * $halfCount;//超过时间加收的钱

                $beyondDistance = $distance - $basic_distance;//超出的距离
                if($beyondDistance > 0){
                    $halfDdistance = ceil($beyondDistance/$beyond_distance_unit);
                    $beyondDistanceCost = ceil($beyond_distance_price * $halfDdistance);//超过距离加收的钱
                }

                $fee = $unitPrice + $beyondTimeCost + $beyondDistanceCost;//基础价格+超出时间加收钱+超出距离加收钱
                $retArr['beyondTime'] = $beyondTime;//为了快照里面能够取到该些数据
                $retArr['beyondTimeCost'] = $beyondTimeCost;//为了快照里面能够取到该些数据
                $retArr['beyondDistance'] = $beyondDistance;
                $retArr['beyondDistanceCost'] = $beyondDistanceCost;
                $retArr['totalFee'] = $fee;
            }
        }
        return $retArr;
    }


} 