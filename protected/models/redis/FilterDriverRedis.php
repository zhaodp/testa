<?php

class FilterDriverRedis extends CRedis {

	protected static $_models=array();
	public $host = 'redishaproxy.edaijia-inc.cn';

	public $port = 22121;

	private static $SPEED_CITY_PREFIX = 'filter_driver_by_speed_city_';

	private static $DRIVER_ARRIVE_SPEED_PREFIX = 'DRIVER_ARRIVE_SPEED_';

	private static $DRIVER_ARRIVE_SPEED_FACTOR_PREFIX = 'DRIVER_ARRIVE_SPEED_ACTOR_';
	public static function model($className = __CLASS__) {
		$model = null;
		if (isset ( self::$_models [$className] ))
			$model = self::$_models [$className];
		else {
			$model = self::$_models [$className] = new $className ( null );
		}
		return $model;
	}

	public function getSpeedSwitchByCityId($city_id) {
		if($this->redis->exists(self::$SPEED_CITY_PREFIX . $city_id)) {
			$speed_switch = $this->redis->get(self::$SPEED_CITY_PREFIX . $city_id);
			return $speed_switch;
		}
		else {
			return null;
		}
	}

	public function setSpeedSwitch($city_id, $speed_switch) {
		$this->redis->set(self::$SPEED_CITY_PREFIX . $city_id, $speed_switch);
	}

	public function getSpeedFactorByDriverId($driver_id){

		$speed_factor = null;
		if($this->redis->exists(self::$DRIVER_ARRIVE_SPEED_FACTOR_PREFIX . $driver_id)) {
			$speed_factor = $this->redis->get(self::$DRIVER_ARRIVE_SPEED_FACTOR_PREFIX . $driver_id);
		}

		return $speed_factor;
	}

	public function setSpeedFactorByDriverId($driver_id, $speed_factor){
		$this->redis->set(self::$DRIVER_ARRIVE_SPEED_FACTOR_PREFIX . $driver_id, $speed_factor);
	}

	public function getSpeedByDriverId($driver_id)
	{
		$speed = null;
		if($this->redis->exists(self::$DRIVER_ARRIVE_SPEED_PREFIX . $driver_id)){
			$speed = $this->redis->get(self::$DRIVER_ARRIVE_SPEED_PREFIX . $driver_id);
		}
		return $speed;
	}

}
