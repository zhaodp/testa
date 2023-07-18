<?php
$q = isset($params['query']) ? $params['query'] : '';
$r = isset($params['region']) ? $params['region'] : '';
$q=urlencode($q);
$r=urlencode($r);
$callback = isset($params['callback']) ? $params['callback'] : '';
$sUrl = "http://apis.map.qq.com/ws/place/v1/suggestion/?keyword=".$q."&region=".$r."&output=json&key=CVDBZ-JKLRJ-BZSF2-FKBII-SNGJ7-POB44";
$d = file_get_contents($sUrl);
print_r($d);
?>