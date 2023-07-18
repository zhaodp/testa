<?php
$params = json_decode('{"mcc":"460","mnc":"0","towers":[{"mcc":"460","lac":"4437","ci":"23157","ssi":"74","ta":"255"},{"mcc":"460","lac":"4437","ci":"12344","ssi":"88","ta":"255"},{"mcc":"460","lac":"4437","ci":"12346","ssi":"94","ta":"255"},{"mcc":"460","lac":"4437","ci":"1126","ssi":"97","ta":"255"},{"mcc":"460","lac":"4437","ci":"10932","ssi":"95","ta":"255"},{"mcc":"460","lac":"4437","ci":"50567","ssi":"97","ta":"255"},{"mcc":"460","lac":"4437","ci":"1125","ssi":"99","ta":"255"}]}',true);


$ret = lbs2gps($params);

print_r($ret);

function lbs2gps($params) {
	$mcc = $params['mcc'];
	$mnc = $params['mnc'];
	
	$call_towers = '';
	
	foreach($params['towers'] as $item) {
		$call_towers .= '{
			"cell_id":'.$item['ci'].',
			"location_area_code":'.$item['lac'].',
			"mobile_country_code":'.$item['mcc'].',
			"mobile_network_code":'.$mnc.',
			"signal_strength":'.$item['ssi'].',
			"timing_advance":'.$item['ta'].'
		},';
	}
	
	$call_towers = trim($call_towers, ',');
	
	$vars = '{
		"version": "1.1.0" ,
		"host": "maps.google.com",
		"access_token": "2:k7j3G6LaL6u_lafw:4iXOeOpTh1glSXe",
  	    "home_mobile_country_code": '.$mcc.',
		"home_mobile_network_code": '.$mnc.',
		"address_language": "zh_CN",
		"radio_type": "gsm",
		"request_address": true ,
		"cell_towers":['.$call_towers.']
	}';
	
	echo $vars;
	
	$rdata = curl_post('http://www.google.com/loc/json', $vars);
	$r_ary = json_decode($rdata, true);
	if ($r_ary) {
		return $r_ary['location'];
	}
	return null;
}

function curl_post($url, $vars, $second = 30) {
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
