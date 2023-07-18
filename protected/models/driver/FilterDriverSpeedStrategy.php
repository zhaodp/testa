
<?php

class FilterDriverSpeedStrategy extends FilterDriverBaseStrategy  {

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

	public function filter($city_id, $drivers,$lng, $lat, $range,$type,$order_id,$driver_app_ver=null) {

		EdjLog::info ( 'FilterDriverSpeedStrategy.filter city_id|' . $city_id .'driver count|' .count($drivers).'lng|'.$lng.'lat|'.$lat.'range|'.$range );	
		if(empty($drivers) || is_null($city_id)){
			return $drivers;
		}	
		$speed_switch = $this->getSpeedSwitch($city_id);
		$speed_enabled = false;

		if(!empty($speed_switch))
			$speed_enabled = true;

		EdjLog::info ( 'FilterDriverSpeedStrategy.filter city_id|' . $city_id .'speed_enabled|'.$speed_enabled, 'console' );	
		if($speed_enabled)
		{
			$drivers_out = array();
			//fetch the speed list 
			foreach($drivers as $item) {
				$speed_factor = 1;
				$speed_factor_in = $this->getSpeedFactorByDriverId($item['driver_id']);
				if(!is_null($speed_factor_in))
				{
					$speed_factor = $speed_factor_in;
				}
				EdjLog::info ( 'FilterDriverSpeedStrategy.filter driver_id|' . $item['driver_id'] .'speed_factor|'.$speed_factor, 'console' );
				echo 'FilterDriverSpeedStrategy.filter driver_id|' . $item['driver_id'] .'speed_factor|'.$speed_factor.'orig_distance_sort|'.$item['distance_sort']."\n"; 			
				EdjLog::info( 'FilterDriverSpeedStrategy.filter driver_id|' . $item['driver_id'] .'speed_factor|'.$speed_factor.'orig_distance_sort|'.$item['distance_sort'],'console'); 		
	
				$item['distance_sort'] = $item['distance_sort']/$speed_factor;

				echo 'FilterDriverSpeedStrategy.filter driver_id|' . $item['driver_id'] .'speed_factor|'.$speed_factor.'new_distance_sort|'.$item['distance_sort'].'distance'.$item['distance']."\n"; 
				EdjLog::info( 'FilterDriverSpeedStrategy.filter driver_id|' . $item['driver_id'] .'speed_factor|'.$speed_factor.'new_distance_sort|'.$item['distance_sort'].'distance'.$item['distance'],'console'); 				
            	
				$drivers_out[] = $item;
			}

			foreach($drivers_out as $driver)	
				echo "driver:" .  serialize($driver) . "\n";
			//	$near_drivers=self::arraySortByKey($drivers, 'distance');
			//	$near_drivers = array_slice($near_drivers,0,$filter_count);	
			return $drivers_out;
		}

		return $drivers;
	}
	private function getSpeedSwitch($city_id)
	{	
		return FilterDriverRedis::model()->getSpeedSwitchByCityId($city_id);
	}

	private function getSpeedFactorByDriverId($driver_id){
		$factor =  FilterDriverRedis::model()->getSpeedFactorByDriverId($driver_id);
        if(is_null($factor) || $factor<1 || $factor>1.4)
            $factor = 1;
        return $factor;

	}

}

