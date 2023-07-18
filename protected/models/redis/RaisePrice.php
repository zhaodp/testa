<?php

/**
 * 恶劣天气加价调整策略 
 * Date: 15-3-17
 * Time: 下午2:41
 * auther yuchao 
 */
class RaisePrice extends RedisHAProxy
{
    protected static $_models = array();

    const BAD_WEATHER_SURCHARGE = 'bad_weather_surcharge_';

    public static function model($className = __CLASS__)
    {
        $model = null;
        if (isset(self::$_models[$className]))
            $model = self::$_models[$className];
        else {
            $model = self::$_models[$className] = new $className(null);
        }
        return $model;
    }

    public function getBadWeatherSurcharge($city_id)
    {
        $surcharge = $this->redis->get(RaisePrice::BAD_WEATHER_SURCHARGE.$city_id); 
        return !empty($surcharge) ? json_decode($surcharge) : false;
    }

    public function revokeBadWeatherSurcharge($city_id)
    {
        return $this->redis->delete(RaisePrice::BAD_WEATHER_SURCHARGE.$city_id); 
    }

    public function setBadWeatherSurcharge($city_id, $surcharge, $expire)
    {
        if (empty($city_id)
            || empty($surcharge)
            || empty($expire)
            || $expire <= 0
        ) {
            return false;
        }

        return $this->redis->set(
                                RaisePrice::BAD_WEATHER_SURCHARGE.$city_id,
                                is_array($surcharge) ? json_encode($surcharge) : $surcharge,
                                $expire
                            ); 
    }
}

