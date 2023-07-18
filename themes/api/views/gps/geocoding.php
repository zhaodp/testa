<?php
if(!isset($params['address'])){
	return null;
}

$address = $params['address'];
$result = GPS::model()->geocoding($address);

echo json_encode($result);