<?php

//根据城市名字获取城市id
$city_id = 0;
$cities = Dict::items('city');
foreach($cities as $code => $name){
	$values=MarketingActivityRedis::model()->get($code);
	if(isset($values)){
		foreach($values as $value){
			$v=unserialize($value);
			var_dump($v->title);
		}

	}
}
