<?php

class FilterDriverManager  {

    protected static $_models=array();

    private $strategyAry = array(); 

    private static function model($className = __CLASS__) {
        $model = null;
        if (isset(self::$_models[$className] ))
            $model = self::$_models[$className];
        else {
            $model = self::$_models[$className] = new $className(null);
        }
        return $model;
    }

    public function filter($city_id, $drivers,$lng, $lat, $range,$type,$order_id,$driver_app_ver=null){
        EdjLog::info('FilterDriverManager.filter city_id|'.$city_id.'driver count|'.count($drivers).'lng|'.$lng.'lat|'.$lat.'range|'.$range.'drivers.......'.json_encode($drivers));		
        if(empty($drivers)) {
            return $drivers;
        }

        if(empty($this->strategyAry))
        {
            return $drivers;
        }

        $nearby_drivers = $drivers;
        foreach($this->strategyAry as $strategy){
            if(empty($nearby_drivers))
            {
                break;
            }
            $nearby_drivers = $strategy->filter($city_id, $nearby_drivers,$lng, $lat,$range,$type,$order_id,$driver_app_ver);
        }
        if(!empty($nearby_drivers))
        {
            $nearby_drivers = FilterDriverBaseStrategy::arraySortByKey($nearby_drivers,'distance_sort');
        
        }

        EdjLog::info('FilterDriverManager.filter out city_id:'.$city_id.'driver count|'.count($nearby_drivers).'drivers.....'.json_encode($nearby_drivers));		
        
        return $nearby_drivers;	
    }

    public  function addStrategy($strategy){
        $this->strategyAry[] = $strategy; 
    }
} 
