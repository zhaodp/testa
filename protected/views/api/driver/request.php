<?php
/**
 * 获取周边最近的5名空闲司机
 * @var unknown_type
 */

$uuid = $params['uuid'];
$lng = $params['longitude'];
$lat = $params['latitude'];

$url_pre = "http://www.edaijia.cn:22322/driver?";
$params = array (
	'uuid'=>$uuid, 
	'longitude'=>$lng, 
	'latitude'=>$lat);

$pargma = "uuid=$uuid&longitude=$lng&latitude=$lat";
$md5sum = md5($pargma."zAcU!(^$26&8B*#g9hz");
$params['sig'] = $md5sum;

$url = Yii::app()->createUrl('', $params);
$url = preg_replace('%^/.*\?%', $url_pre, $url);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$content = curl_exec($curl);
curl_close($curl);

libxml_disable_entity_loader(true);
$drivers = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA|LIBXML_NOBLANKS);

if ($drivers->info) {
	$new_drivers = array('info'=>$drivers->info);
} else {
	$new_drivers = array ();
	//获取司机的代驾次数，大中小照片路径
	foreach($drivers->driver as $item) {
		$driver = Driver::getProfileByImei($item->id);
		if ($driver) {
			if (preg_match('%pic%', $item->picture)) {
				$item->picture_small = $item->picture;
				$item->picture_middle = $item->picture;
				$item->picture_large = $item->picture;
			} else {
				if (preg_match('%img\.edaijia\.cn%', $item['picture'])) {
					$item->picture_small = str_replace('middle', 'small', $item['picture']);
					$item->picture_middle = $item['picture'];
					$item->picture_large = str_replace('middle', 'normal', $item['picture']);
				} else {
					$item->picture_small = sprintf('%s%s/%s/small.jpg', SP_URL_DRIVER_IMG, $driver->city_id, $driver->user);
					$item->picture_middle = sprintf('%s%s/%s/middle.jpg', SP_URL_DRIVER_IMG, $driver->city_id, $driver->user);
					$item->picture_large = sprintf('%s%s/%s/normal.jpg', SP_URL_DRIVER_IMG, $driver->city_id, $driver->user);
				}
			}
			$item->order_count = Driver::getDriverOrder($driver->user);
			$item->comment_count = Driver::getDriverComments($driver->user);

			unset($item->picture);
			unset($item->priceDetail);
			
			$new_drivers[] = $item;
		}
	}
}
$json = json_encode($new_drivers);
echo $json;
