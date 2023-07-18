<?php
class FilterDriverCrossRiverStrategy extends FilterDriverBaseStrategy { 

    protected static $_models=array();

    public static function model($className=__CLASS__) {
        $model=null;
        if (isset(self::$_models[$className]))
            $model=self::$_models[$className];
        else {
            $model=self::$_models[$className]=new $className(null);
        }
        return $model;
    }

    public function filter($city_id, $drivers, $lng, $lat,  $range,$type,$order_id,$driver_app_ver=null) {

        if(empty($drivers)) {

            EdjLog::info('FilterDriverCrossRiverStrategy in city ' . $city_id . 'no driver', 'console');
            return;
        }

        $driverNumBeforeFilter = count($drivers);
        $drivers = FilterDriverAcrossRiver::model()->filter($city_id, $drivers, $lng, $lat, $range);
        if($driverNumBeforeFilter > count($drivers)) {
            EdjLog::info('drivers filtered out by FilterDriverAcrossRiver in city ' . $city_id . ' for order:' . $order_id, 'console');
        }

        if(empty($drivers)) {
            EdjLog::info('no drivers after FilterDriverAcrossRiver in city ' . $city_id . ' for order:' . $order_id, 'console');
            $retryDriverBase = FilterDriverAcrossRiver::model()->getRetryDriverBase($city_id);
            if(!empty($retryDriverBase)) {
                if(isset($type) && in_array($type, Order::$washcar_sources)) {
                    $drivers = DriverGPS::model()->nearbyService($lng, $lat, 0, $retryDriverBase, $range, Driver::SERVICE_TYPE_FOR_XICHE, $driver_app_ver);
                } else{
                    $drivers = DriverGPS::model()->nearby($lng, $lat, 0 , $retryDriverBase, $range, $driver_app_ver,$city_id);
                }
                EdjLog::info('after FilterDriverAcrossRiver retry, nearby driver number is ' . count($drivers), 'console');
                $drivers = FilterDriverAcrossRiver::model()->filter($city_id, $drivers, $lng, $lat, $range);
                if(empty($drivers)) {
                    EdjLog::info('no drivers after FilterDriverAcrossRiver retry in city ' . $city_id . ' for order:' . $order_id, 'console');
                }
            }
        }

        return $drivers;

    }

}
