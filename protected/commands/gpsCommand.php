<?php
class GpsCommand extends CConsoleCommand {
	/**
	 * 
	 * 从队列中获取需要解析的基站地址，发送到google请求
	 */
	public function actionDriverGps() {
		$longitude = $latitude = 0;
		$queue = Yii::app()->params['httpsqs']['location']['queue'];
		$host = Yii::app()->params['httpsqs']['location']['host'];
		$port = Yii::app()->params['httpsqs']['location']['port'];
		$password = Yii::app()->params['httpsqs']['location']['password'];
		
		$httpsqs = new Httpsqs($host, $port, $password);
		$s_count = 0;
		while (true) {
			$gps = array ();
			$json_location = $httpsqs->get($queue);
			//$json_location = '{"imei":"356993020520421","mcc":"460","mnc":"0","towers":[{"mcc":"460","lac":"4187","ci":"39357","ssi":100,"ta":255}]}';
			//$json_location = '{"imei":"353419036311764","mcc":"460","mnc":"0","towers":[{"mcc":"460","lac":"4604","ci":"21903","ssi":"62","ta":"255"},{"mcc":"460","lac":"4604","ci":"19368","ssi":"250","ta":"255"},{"mcc":"460","lac":"4607","ci":"2098","ssi":"250","ta":"255"},{"mcc":"460","lac":"0","ci":"0","ssi":"255","ta":"255"},{"mcc":"460","lac":"0","ci":"0","ssi":"255","ta":"255"},{"mcc":"460","lac":"0","ci":"0","ssi":"255","ta":"255"},{"mcc":"460","lac":"0","ci":"0","ssi":"255","ta":"255"}]}';
			if ($json_location=='HTTPSQS_GET_END') {
				while (true) {
					sleep(2);
					$json_location = $httpsqs->get($queue);
					if ($json_location!='HTTPSQS_GET_END')
						break;
				}
			}
			
			//echo $json_location;
			echo $s_count++."\n";
			
			//解析地址
			$params = json_decode($json_location, true);
			$imei = $params['imei'];
			foreach($params['towers'] as $tower) {
				if ($tower['ci']!=0&&$tower['lac']!=0) {
					$google_gps = Lbs2GPS::Location($tower);
					if (isset($google_gps['latitude'])) {
						$gps[] = array (
							'rssi'=>$tower['ssi'], 
							'latitude'=>doubleval($google_gps['latitude']), 
							'longitude'=>doubleval($google_gps['longitude']));
					}
				}
			}
			//print_r($gps);
			

			if ($gps) {
				//取离第一个基站距离500米的基站，如果没有，取第一个基站的座标位定位。
				if (count($gps)>2) {
					$main_tower = $gps[0];
					$points = array ();
					foreach($gps as $tower) {
						$d = Helper::Distance($main_tower['latitude'], $main_tower['longitude'], $tower['latitude'], $tower['longitude']);
						if ($d<500) {
							echo "new BMap.Point(".$tower['longitude'].",".$tower['latitude']."),\n";
							$points[] = $tower;
						}
					}
					
					if ($points) {
						$sum_longitude = 0;
						$sum_latitude = 0;
						foreach($points as $item) {
							$sum_longitude += $item['longitude'];
							$sum_latitude += $item['latitude'];
						}
						$latitude = $sum_latitude/count($points);
						$longitude = $sum_longitude/count($points);
					} else {
						$latitude = $main_tower['latitude'];
						$longitude = $main_tower['longitude'];
					}
				} else {
					foreach($gps as $item) {
						$longitude = $item['longitude'];
						$latitude = $item['latitude'];
					}
				}
				echo "new BMap.Point($longitude,$latitude),\n";
				
				if ($longitude!=0&&$latitude!=0) {
					//更新司机位置的三个坐标系
					$driver = Driver::getProfileByImei($imei);
					
					if ($driver) {
						$user_id = $driver->id;
						$gps_position = array (
							'longitude'=>$longitude, 
							'latitude'=>$latitude);
						DriverPosition::model()->updatePosition($user_id, $gps_position);
					}
				}
			}
		}
	}
	
	/**
	 * 
	 * 修正employee的座标错误
	 */
	public function actionUpdateEmployee() {
		$sql = 'update t_employee set state=2
				where unix_timestamp(now()) - unix_timestamp(update_time) > 600;';
		Yii::app()->db->createCommand($sql)->execute();
		
		$sql = 'update `t_driver_position` set status =2
				where unix_timestamp(now()) - unix_timestamp(`created`) > 600 and status<3;';		
		Yii::app()->db->createCommand($sql)->execute();
		
// 		$sql = 'delete from `t_driver_token`
// 				where unix_timestamp(now()) - unix_timestamp(`created`) > 600;';		
// 		Yii::app()->db->createCommand($sql)->execute();
		
// 		$sql = 'update t_employee e, t_driver d, t_driver_position dp
// 				set e.state = 2,e.update_time=now()
// 				WHERE e.user = d.user
// 				AND d.id = dp.user_id
// 				AND unix_timestamp( now( ) ) - unix_timestamp( dp.created ) >600
// 				AND e.state =0';
// 		Yii::app()->db->createCommand($sql)->execute();
		

		$sql = 'update `t_employee` e ,`t_driver_position` dp, `t_driver` d
				set e.state=dp.status, e.longitude = dp.baidu_lng, e.latitude = dp.baidu_lat
				where e.imei = d.imei and d.id = dp.user_id';
		Yii::app()->db->createCommand($sql)->execute();
		
		//更新空闲和忙碌的司机名单
//		$cache_key = 'idel_driver';
//		$sql = 'select d.id,dp.baidu_lng,dp.baidu_lat 
//				from  `t_driver_position` dp, `t_driver` d
//				where d.id = dp.user_id and dp.status in (0,3) and d.mark=0 and `longitude`!=\'\' and `latitude`!=\'\'';
//		$idel_driver = Yii::app()->db->createCommand($sql)->queryAll();
//		Yii::app()->cache->set($cache_key, $idel_driver, 120);
//		
//		$cache_key = 'busy_driver';
//		$sql = 'select d.id,dp.baidu_lng,dp.baidu_lat 
//				from  `t_driver_position` dp, `t_driver` d
//				where d.id = dp.user_id and dp.status =1 and d.mark=0 and `longitude`!=\'\' and `latitude`!=\'\'';
//		$busy_driver = Yii::app()->db->createCommand($sql)->queryAll();
//		Yii::app()->cache->set($cache_key, $busy_driver, 120);
	}
	
	//RSSI模型计算距离
	private function distance($rssi) {
		$A = 10;
		$n = 4;
		$d = pow(10, ($rssi+$A)/(10*$n));
		return $d;
	}
	
	public function actionLbs2GPS($offset = 0) {
		//导入新的未解析LBS坐标点
		while (true) {
			$criteria = new CDbCriteria(array (
				'condition'=>'longitude=0 and latitude=0', 
				'limit'=>50, 
				'offset'=>$offset, 
				'order'=>'id'));
			$_items = Lbs2gps::model()->findAll($criteria);
			if ($_items) {
				foreach($_items as $_item) {
					$vars = '{
							"version": "1.1.0" ,
							"host": "maps.google.com",
							"access_token": "2:k7j3G6LaL6u_lafw:4iXOeOpTh1glSXe",
							"home_mobile_country_code": '.$_item->mcc.',
							"home_mobile_network_code":0,
							"address_language": "zh_CN",
							"radio_type": "gsm",
							"request_address": true ,
							"cell_towers":[
							{
							"cell_id":'.$_item->ci.',
							"location_area_code":'.$_item->lac.',
							"mobile_country_code":'.$_item->mcc.',
							"mobile_network_code":0,
							"timing_advance":5555
							}
							]
							}';
					$rdata = self::curl_post('http://www.google.com/loc/json', $vars);
					$r_ary = json_decode($rdata, true);
					if (isset($r_ary['location'])) {
						//转换为百度坐标体系
						$baidu_data = self::google2baidu($r_ary['location']['longitude'], $r_ary['location']['latitude']);
						$attr = array (
							'longitude'=>$r_ary['location']['longitude']*10000, 
							'latitude'=>$r_ary['location']['latitude']*10000, 
							'baidu_lng'=>$baidu_data['x'], 
							'baidu_lat'=>$baidu_data['y'], 
							'update_time'=>time());
						
						$model = Lbs2gps::model()->findByPk($_item->id);
						$model->attributes = $attr;
						$model->update();
					}
					echo $_item->id."\n";
				}
			} else {
				break;
			}
			$offset += 50;
		}
	}
	
	public function actiontTest($mcc, $lac, $ci) {
		print_r(self::lbs2gps($mcc, $lac, $ci));
	}
	
	private function lbs2gps($mcc, $lac, $ci) {
		$vars = '{
				"version": "1.1.0" ,
				"host": "maps.google.com",
				"access_token": "2:k7j3G6LaL6u_lafw:4iXOeOpTh1glSXe",
				"home_mobile_country_code": '.$mcc.',
				"home_mobile_network_code":0,
				"address_language": "zh_CN",
				"radio_type": "gsm",
				"request_address": true ,
				"cell_towers":[{
					"cell_id":'.$ci.',
					"location_area_code":'.$lac.',
					"mobile_country_code":'.$mcc.',
					"mobile_network_code":0,
					"timing_advance":5555
				}]
				}';
		$rdata = self::curl_post('http://www.google.com/loc/json', $vars);
		$r_ary = json_decode($rdata, true);
		if ($r_ary) {
			return $r_ary['location'];
		}
		return null;
	}
	
	private function google2baidu($lng, $lat) {
		$snoopy = new Snoopy();
		$snoopy->fetch("http://api.map.baidu.com/ag/coord/convert?from=2&to=4&x=$lng&y=$lat");
		
		$data = json_decode($snoopy->results, true);
		return $data;
	}
	
	private function curl_post($url, $vars, $second = 30) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
}