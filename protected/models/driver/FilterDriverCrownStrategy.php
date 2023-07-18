<?php

class FilterDriverCrownStrategy extends FilterDriverBaseStrategy  {

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

	public function filter($city_id,$drivers, $lng, $lat, $range,$type,$order_id,$driver_app_ver=null) {
		
		if(empty($drivers)){
			return $drivers;
		}
        
        $isCrown = 0;
		foreach($drivers as $item) {
			$driver_lng=$item['lng'];
			$driver_lat=$item['lat'];
			$_distance =$item['distance'];
            $_distance_sort=$item['distance'];
            $isCrown = 0;
				if( !empty($item['driver_id']) ){
					$crown_driver = DriverRecommand::model()->validateRecommend($item['driver_id']);
					if(!empty($crown_driver)){
                        $isCrown = 1;
						$nowtime = date("His");
						$rushhours = "200000";
						$rushend = "230000";
						if(($nowtime >= $rushhours) && ($nowtime <= $rushend)){
							$reduce_meter = 500;  
						}else{
							$reduce_meter = 2000;
						}
						if($_distance<=2000){
							$crown_drivers[]=array(
									'id'=>$item['id'],
									'driver_id'=>$item['driver_id'],
									'distance'=>$_distance_sort,
									'status'=>isset($item['status'])? intval($item['status']) : -1,
									'lng' => $driver_lng,
									'lat' => $driver_lat
									);
						}
						$_distance_sort = $_distance_sort - $reduce_meter;
					}
					$near_drivers[]=array(
							'id'=>$item['id'],
							'driver_id'=>$item['driver_id'],
							'distance_sort'=>$_distance_sort,
                            'distance'=>$_distance,
							'status'=>isset($item['status'])? intval($item['status']) : -1,
							'lng' => $driver_lng,
							'lat' => $driver_lat,
                            'crown' => $isCrown,
                            'weight'=> $_distance
							);
				}
		}
		/*
		   if($crown_drivers){
		   $near_drivers=self::arraySortByKey($crown_drivers, 'distance');
		   $near_drivers=array_slice($near_drivers, 0, $count);
		   }else*/
			/* if ($near_drivers) {
			   $near_drivers=self::arraySortByKey($near_drivers, 'distance');
			   $near_drivers=array_slice($near_drivers, 0, $count);
		   }*/

		   return $near_drivers;

	}

}
