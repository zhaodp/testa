<?php
class MemoryCache {
	/**
	 * 获取司机缓存
	 * @param string $driver_id 司机工号
	 * @return array driver info
	 */
	public function loadDriverCache($driver_id) {
		$cache_key = Yii::app()->params['CACHE_KEY_DRIVER_INFO'].$driver_id;
		
		$json = Yii::app()->cache->get($cache_key);
		
		if (!$json) {
			$this->initDriverCache($driver_id);
			
			$json = Yii::app()->cache->get($cache_key);
		}
		
		return json_decode($json, true);
	}
	
	/**
	 * 初始化司机缓存
	 * @param string $driver_id 司机工号
	 * 
	 */
	public function initDriverCache($driver_id) {
		$cache_key = Yii::app()->params['CACHE_KEY_DRIVER_INFO'].$driver_id;
		$driver = Driver::getProfile($driver_id);
		if ($driver) {
			$driverDetail['sys']['id'] = $driver->id;
			$driverDetail['sys']['imei'] = $driver->imei;
			$driverDetail['sys']['city_id'] = $driver->city_id;
			$driverDetail['sys']['mark'] = $driver->mark;
			$driverDetail['sys']['driver_id'] = $driver->user;
			
			$driverDetail['service']['level'] = $driver->level;
			
			$driverDetail['info']['name'] = $driver->name;
			$driverDetail['info']['picture'] = $driver->picture;
			$driverDetail['info']['gender'] = Dict::item('gender', $driver->gender);
			$driverDetail['info']['phone'] = $driver->phone;
			$driverDetail['info']['id_card'] = $driver->id_card;
			$driverDetail['info']['domicile'] = $driver->domicile;
			$driverDetail['info']['car_card'] = $driver->car_card;
			$driverDetail['info']['year'] = $driver->year;
			$driverDetail['info']['address'] = $driver->address;
			$driverDetail['info']['ext_phone'] = $driver->ext_phone;
			$driverDetail['info']['license_date'] = $driver->license_date;
			
			$driverExt = DriverExt::model()->getExt($driver_id);
			
			$driverDetail['service']['service_times'] = $driverExt->service_times;
			$driverDetail['service']['high_opinion_times'] = $driverExt->high_opinion_times;
			$driverDetail['service']['low_opinion_times'] = $driverExt->low_opinion_times;
			
			$driverPosition = DriverPosition::model()->getDriverPosition($driver->id);
			
			if ($driverPosition) {
				$driverDetail['sys']['status'] = $driverPosition->status;
				$driverDetail['gps']['longitude'] = $driverPosition->longitude;
				$driverDetail['gps']['latitude'] = $driverPosition->latitude;
				$driverDetail['gps']['google_lng'] = $driverPosition->google_lng;
				$driverDetail['gps']['google_lat'] = $driverPosition->google_lat;
				$driverDetail['gps']['baidu_lng'] = $driverPosition->baidu_lng;
				$driverDetail['gps']['baidu_lat'] = $driverPosition->baidu_lat;
				$driverDetail['gps']['street'] = '';
			}
			
			$driverPhone = DriverPhone::model()->getDriverPhone($driver_id);
			if ($driverPhone) {
				$driverDetail['sys']['simcard'] = $driverPhone->simcard;
				$driverDetail['sys']['is_bind'] = $driverPhone->is_bind;
			}
			
			$driverToken = DriverToken::model()->getTokenByDriver($driver_id);
			if ($driverToken) {
				$driverDetail['login']['authtoken'] = $driverToken->authtoken;
				$driverDetail['login']['created'] = $driverToken->created;
			}
		} else {
			$driverDetail['sys']['id'] = 0;
			$driverDetail['sys']['imei'] = 0;
			$driverDetail['sys']['city_id'] = 0;
			$driverDetail['sys']['mark'] = 3;
			$driverDetail['sys']['driver_id'] = $driver_id;
			$driverDetail['sys']['status'] = 3;
			$driverDetail['sys']['simcard'] = 0;
			$driverDetail['sys']['is_bind'] = 0;
			
			$driverDetail['info']['name'] = '';
			$driverDetail['info']['gender'] = '';
			$driverDetail['info']['picture'] = '';
			$driverDetail['info']['phone'] = '';
			$driverDetail['info']['id_card'] = '';
			$driverDetail['info']['domicile'] = '';
			$driverDetail['info']['car_card'] = '';
			$driverDetail['info']['year'] = 0;
			$driverDetail['info']['address'] = '';
			$driverDetail['info']['ext_phone'] = '';
			$driverDetail['info']['license_date'] = '';
			$driverDetail['service']['service_times'] = 0;
			$driverDetail['service']['high_opinion_times'] = 0;
			$driverDetail['service']['low_opinion_times'] = 0;
			$driverDetail['service']['level'] = 0;
			
			$driverDetail['gps']['longitude'] = 1;
			$driverDetail['gps']['latitude'] = 1;
			$driverDetail['gps']['google_lng'] = 1;
			$driverDetail['gps']['google_lat'] = 1;
			$driverDetail['gps']['baidu_lng'] = 1;
			$driverDetail['gps']['baidu_lat'] = 1;
			$driverDetail['gps']['street'] = '';
			
			$driverDetail['login']['authtoken'] = '';
			$driverDetail['login']['created'] = 0;
		}
		$json = json_encode($driverDetail);
		Yii::app()->cache->set($cache_key, $json, 3600);
	}
	
	public function setDriverCache($driver_id, $record = array()) {
		$cache_key = Yii::app()->params['CACHE_KEY_DRIVER_INFO'].$driver_id;
		
		$json = json_encode($record);
		
		Yii::app()->cache->set($cache_key, $json, 3600);
	}
}