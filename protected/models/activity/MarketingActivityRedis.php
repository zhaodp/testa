<?php

class MarketingActivityRedis extends CRedis {

    public $host = 'redis01n.edaijia.cn'; //10.132.17.218
    public $port = 6379;
    public $password = 'k74FkBwb7252FsbNk2M7';
    protected static $_models = array();

    public static function model($className = __CLASS__) { 
        $model = null;
        if (isset(self::$_models[$className])){
            $model = self::$_models[$className];
        }else {
            $model = self::$_models[$className] = new $className(null);
        }
        return $model;
    }

	public function set($mactivity){
		//if (empty($mactivity['city_ids'])){
		//	return false;
		//}
		$city_ids = $mactivity['city_ids'];
		if($city_ids=='0'){//该活动面向全国城市 
			$cities = Dict::items('city');
                        foreach($cities as $code => $name){
				$city_id = $code;
				$ma = $mactivity;
                        	$ma->city_ids = $city_id;
                                $cache_key = 'mactivity_'.$city_id;
				$this->redis->lpush($cache_key, serialize($ma));
			}
			return true;
		}
		$city_array = explode(",",$city_ids);
		foreach($city_array as $city_id){
			$ma = $mactivity;
			$ma->city_ids = $city_id;
			$cache_key = 'mactivity_'.$city_id;
		        $this->redis->lpush($cache_key, serialize($ma));
		}
		return true;
	}

	/**
	*返回本城市的所有存放在缓存中的活动
	**/
	public function get($city_id){
		if(!isset($city_id)){
			return false;
	  	}
		$cache_key = 'mactivity_'.$city_id;
		$activity_list = $this->redis->lrange($cache_key,0,-1);
		if(isset($activity_list)){
			 return $activity_list;
		}
		return false;
	}	
	
	public function remove($mactivity){
		$city_ids=$mactivity->city_ids;
		if($city_ids=='0'){//该活动面向全国城市   
                        $cities = Dict::items('city');
                        foreach($cities as $code => $name){
                                $city_id = $code;
                                $cache_key = 'mactivity_'.$city_id;
                                $values=MarketingActivityRedis::model()->get($city_id);
                                $this->redis->del($cache_key);
                                foreach($values as $v){
                                        $value=unserialize($v);
                                        if($value->id!=$mactivity->id){                                                                 
                                          $this->redis->lpush($cache_key, serialize($value));                                                           
                                        }
                                }
                        }
                        return true;
                }

		$city_array = explode(",",$city_ids);
                foreach($city_array as $city_id){
                        $ma = $mactivity;
                        $cache_key = 'mactivity_'.$city_id;
			$values=MarketingActivityRedis::model()->get($city_id);
			$this->redis->del($cache_key);
			foreach($values as $v){  
                                $value=unserialize($v);
				if($value->id!=$mactivity->id){
					$this->redis->lpush($cache_key, serialize($value));
				}
                        }
                }
                return true;
	}

	public function clearCache(){
                for($i=0;$i<500;$i++){
			$cache_key = 'mactivity_'.$i;
			$this->redis->del($cache_key);

		}
        }
	
}

