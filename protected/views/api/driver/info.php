<?php
$imei = $params['imei'];

//$cache_key = 'driver_info_cache_'.$imei;
$cache_key = Yii::app()->params['CACHE_KEY_DRIVER_INFO'].$imei;
$json = Yii::app()->cache->get($cache_key);
if (!$json) {
	$criteria = new CDbCriteria();
	$criteria->select = 'user,state';
	$criteria->condition = 'imei=:imei';
	$criteria->params = array (
		':imei'=>$imei);
	
	$model = Employee::model()->find($criteria);
	if ($model) {
		$json = json_encode(array (
			'driver_id'=>$model->user, 
			'order_count'=>Driver::getDriverOrder($model->user), 
			'state'=>$model->state));
		Yii::app()->cache->set($cache_key, $json, 600);
	} else {
		$json = null;
	}
}
echo $json;
