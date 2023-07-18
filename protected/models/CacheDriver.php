<?php
/**
 * 司机信息的memcache缓存
 * 由队列执行进程更新缓存，API仅做写入队列和缓存读取 
 * @author dayuer
 *
 */
class CacheDriver extends CComponent {
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

	public function get($driver_id) {
		$cache_key=Yii::app()->params['CACHE_KEY_DRIVER_INFO'].$driver_id;
		$json=Yii::app()->cache->get($cache_key);
		
		if ($json) {
			$driver_info=json_decode($json, true);
		} else {
			$driver_info=self::load($driver_id);
		}
		return $driver_info;
	}
	
	public function reset($driver_id){
		$cache_key=Yii::app()->params['CACHE_KEY_DRIVER_INFO'].$driver_id;
		Yii::app()->cache->delete($cache_key);
	}
	
	public function reload(){
		$offset=0;
		$pagesize=500;
		echo "starting \n";
		while(true) {
			$criteria=new CDbCriteria(array(
					'select'=>'user',
					'offset'=>$offset,
					'limit'=>$pagesize
			));
			$drivers=Driver::model()->findAll($criteria);
			if ($drivers) {
				foreach($drivers as $driver) {
					echo $driver['user'] ."\n";
					self::reset($driver['user']);
					self::load($driver['user']);
				}
			} else {
				break;
			}
			$offset+=$pagesize;
			echo $offset."\n";
		}
	}

	/**
	 * 更新司机信息的全部或者部分信息
	 * @param string $user_id
	 * @param array $attrib
	 */
	public function set($driver_id, $attr) {
		$cache_key=Yii::app()->params['CACHE_KEY_DRIVER_INFO'].$driver_id;
		$json=Yii::app()->cache->get($cache_key);
		if ($json) {
			$driver_info=json_decode($json, true);
		} else {
			$driver_info=self::load($driver_id);
		}
		
		foreach($attr as $k=>$v) {
			if (is_array($v)) {
				foreach($v as $kk=>$vv) {
					$driver_info[$k][$kk]=$vv;
				}
			} else {
				$driver_info[$k]=$v;
			}
		}
		
		Yii::app()->cache->set($cache_key, json_encode($driver_info), 86400);
		return $driver_info;
	}

	private function load($driver_id) {
		$cache_key=Yii::app()->params['CACHE_KEY_DRIVER_INFO'].$driver_id;
		$driver_info=array();
		
		$driver=Driver::model()->getProfile($driver_id);
		if ($driver) {
			$driver_info['sys']['id']=$driver->id;
			$driver_info['sys']['imei']=$driver->imei;
			$driver_info['sys']['city_id']=$driver->city_id;
			$driver_info['sys']['mark']=$driver->mark;
			$driver_info['sys']['driver_id']=$driver->user;
			$driver_info['service']['level']=$driver->level;
			$driver_info['service']['goback']=$driver->goback;//结伴返程标识 add by sunhongjing
			$driver_info['info']['name']=$driver->name;
			$driver_info['info']['picture']=$driver->picture;
			$driver_info['info']['gender']=($driver->gender) ? Dict::item('gender', $driver->gender) : '1';
			$driver_info['info']['phone']=$driver->phone;
			$driver_info['info']['id_card']=$driver->id_card;
			$driver_info['info']['domicile']=$driver->domicile;
			$driver_info['info']['car_card']=$driver->car_card;
			$driver_info['info']['year']=$driver->year;
			$driver_info['info']['address']=$driver->address;
			$driver_info['info']['ext_phone']=$driver->ext_phone;
			$driver_info['info']['license_date']=$driver->license_date;
			
			$driverExt=DriverExt::model()->getExt($driver_id);
			$driver_info['service']['service_times']=$driverExt->service_times;
			$driver_info['service']['high_opinion_times']=$driverExt->high_opinion_times;
			$driver_info['service']['low_opinion_times']=$driverExt->low_opinion_times;
			
			$position=DriverPosition::model()->getDriverPosition($driver->id);
			if ($position) {
				$driver_info['sys']['status']=$position->status;
				$driver_info['gps']['longitude']=$position->longitude;
				$driver_info['gps']['latitude']=$position->latitude;
				$driver_info['gps']['google_lng']=$position->google_lng;
				$driver_info['gps']['google_lat']=$position->google_lat;
				$driver_info['gps']['baidu_lng']=$position->baidu_lng;
				$driver_info['gps']['baidu_lat']=$position->baidu_lat;
				$street = GPS::model()->getStreetByBaiduGPS($position->baidu_lng, $position->baidu_lat, 3); //3 所有GPS信息
				$driver_info['gps']['street']=$street['name'];
				$driver_info['gps']['created']=$position->created;
			}
			
			$driverPhone=DriverPhone::model()->getDriverPhone($driver_id);
			if ($driverPhone) {
				$driver_info['sys']['simcard']=$driverPhone->simcard;
				$driver_info['sys']['is_bind']=$driverPhone->is_bind;
			}
			
 			$driverToken = DriverToken::model()->findByPk($driver_id);
			if ($driverToken) {
				$driver_info['login']['authtoken']=$driverToken->authtoken;
				$driver_info['login']['created']=$driverToken->created;
			}
		}
		Yii::app()->cache->set($cache_key, json_encode($driver_info), 86400);
		return $driver_info;
	}
}