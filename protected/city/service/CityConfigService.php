<?php
/**
 * 城市管理的服务接口类,提供对于城市管理, 信息费结算的基本封装方法
 *
 * User: tuan
 * Date: 15/5/5
 * Time: 13:58
 */

class CityConfigService {

    /** 日间业务 */
    const  DAY_TIME_PRICE_INDEX =  'daytime_price';
    /** 日间业务信息费收取标准 */
    const  DAY_TIME_CAST_INDEX  = 'daytime_cast';
    /** 洗车业务 */
    const WASH_CAR_PRICE_INDEX  = 'wash_car_price';

    /**
     * 计算订单费用
     *
     * @param $city_id
     * @param $distance
     * @param $booking_time
     * @param $wait_time
     * @return mixed
     */
    public static function calculatorFee($city_id,$distance,$booking_time, $wait_time){
        return CityConfig::model()->calculatorFee($city_id, $distance, $booking_time, $wait_time);
    }

    /**
     * 城市日间业务的配置
     *
     * @param $cityId
     * @return mixed
     */
    public static function dayTimeStatus($cityId){
        return self::getCityConfig($cityId, self::DAY_TIME_PRICE_INDEX);
    }

    /**
     * 日间业务信息费收取标准
     *
     * @param $cityId
     * @return mixed
     */
    public static function dayTimeCast($cityId){
        return self::getCityConfig($cityId, self::DAY_TIME_CAST_INDEX);
    }

    /**
     * 洗车订单的配置
     *
     * @param $cityId
     * @return mixed
     */
    public static function washCarStatus($cityId){
        return self::getCityConfig($cityId, self::WASH_CAR_PRICE_INDEX);
    }

    /**
     * 加载城市的配置
     *
     * @param $cityId
     * @param string $filedName
     * @return mixed
     */
    public static function getCityConfig($cityId, $filedName = ''){
        return RCityList::model()->getCityById($cityId, $filedName);
    }

}