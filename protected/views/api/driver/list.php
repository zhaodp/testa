<?php
$imeis = $params['imeis'];

$criteria = new CDbCriteria();
$criteria->select = 'user,state';
$criteria->addInCondition('imei', explode(',', $imeis));

$driver = array();
$model = Employee::model()->findAll($criteria);

foreach($model as $item) {
	$driver[] = array (
		'driver_id'=>$item->user, 
		'order_count'=>Driver::getDriverOrder($item->user), 
		'state'=>$item->state);
}
$json = json_encode($driver);
echo $json;
