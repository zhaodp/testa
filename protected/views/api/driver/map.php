<?php
$city_id = $params['city_id'];

$cache_key = 'driver_city_cache_'.$city_id;
$json = Yii::app()->cache->get($cache_key);
if (!$json) {
	$criteria = new CDbCriteria();
	$criteria->select = 'longitude,latitude';
	$criteria->condition = 'city_id=:city_id and state=0';
	$criteria->params = array (
		':city_id'=>$city_id);
	
	$model = Employee::model()->findAll($criteria);
	
	foreach($model as $item) {
		$data[] = array (
			'longitude'=>$item->longitude, 
			'latitude'=>$item->latitude);
	}
	
	$json = json_encode($data);
	Yii::app()->cache->set($cache_key, $json, 600);
}

echo $json;
