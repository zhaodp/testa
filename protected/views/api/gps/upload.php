<?php
$args_params = explode(',', $location);

$model = Lbs::checkLocation($args_params[0], $args_params[1], $args_params[2], $args_params[3]);

if (!$model) {
	$gps = explode(',', $gps);
	$ret = Lbs::addLocation($args_params[0], $args_params[1], $args_params[2], $args_params[3], $gps[0], $gps[1]);
	if($ret){
		echo 1;
	}
}else{
	echo 0;
}
