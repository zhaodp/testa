<?php
/**
 * 获取当前时间价格
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-07-23
 */

//开通城市列表
$open_city = RCityList::model()->getOpenCityList();

//接收并验证参数
$lng = isset($params['lng']) ? $params['lng'] : '';
$lat = isset($params['lat']) ? $params['lat'] : '';
$gps_type = isset($params['gps_type']) ? $params['gps_type'] : 'wgs84';
if (empty($lng) || empty($lat)) {
	$ret = array('code' => 1 , 'message' => '参数有误');
	echo json_encode($ret);return ;
}


/**
 * 
 * 修改价格表信息,把结束日期减一分钟
 * @author sunhongjing 2013-05-08
 * @param unknown_type $d
 */
function getLastFeeHour($d)
{
	$ret = $d;
	$hour = @explode(':',$d);
	if(!empty($hour)){
		if(!empty($hour[0]) && '00' != $hour[0] ){
			$ret = sprintf("%02s:59", $hour[0]-1);
		}else{
			$ret = '23:59';
		}
	}
	return  $ret;
    
}

$gps_location = array(
    'longitude' => $lng,
    'latitude' => $lat,
);
$gps = GPS::model()->convert($gps_location , $gps_type);
$city_name = GPS::model()->getCityByBaiduGPS($gps['baidu_lng'] , $gps['baidu_lat']);

//判定城市是否开通
if (!in_array(trim($city_name) , $open_city)) {
	$ret = array('code' => 1 , 'message' => '当前城市暂未开通代驾服务');
	echo json_encode($ret);return ;
}

$citys = Dict::items('city');
$city_id = 0;
foreach($citys as $key=>$value) {
	if ($value == $city_name){
		$city_id = $key;
		break;
	}
}

$price = RCityList::model()->getFeeForGD($city_id);
if ($city_id==0||!($price)) {
	$city_id = 1;
}

$thirdFeeHour = $price['thirdFeeHour'];
$priceListAll = array (); 
if ($price['minFeeHour'].'-'.$price['firstFeeHour']=='00:00-00:00') {
	array_push($priceListAll, array (
		'part'=>'00:00-24:00', 
		'price'=>$price['minFee']));
} else {
	$part_min = $price['minFeeHour'];
    $part_first = $price['firstFeeHour'];
    if ($part_first < $part_min && 0 == $part_first) {
		$part_first = '24:00';
	}
	array_push($priceListAll, array (
		'part'=>$part_min.'-'.$part_first, 
		'price'=>$price['minFee']));
}

if (isset($price['secondFeeHour'])) {
	if ($price['firstFeeHour'].'-'.$price['secondFeeHour']!='00:00-00:00') {
		$part_first = $price['firstFeeHour'];
		$part_two = $price['secondFeeHour'];
		if ($part_two < $part_first && 0 == $part_two) {
			$part_two = '24:00';
		}
		array_push($priceListAll, array (
			'part'=>$part_first.'-'.$part_two, 
			'price'=>$price['firstFee']));
	}
	
	if (isset($price['thirdFeeHour'])) {
		$part_two = $price['secondFeeHour'];
		$part_three = $price['thirdFeeHour'];
		if ($part_three < $part_two && 0 == $part_three) {
			$part_three = '24:00';
		}
		if ($price['secondFeeHour'].'-'.$price['thirdFeeHour']!='00:00-00:00') {
			array_push($priceListAll, array (
				'part'=>$part_two.'-'.$part_three, 
				'price'=>$price['secondFee']));
		}
		
		if (isset($price['thirdFeeHour'])) {
			if ($price['thirdFeeHour'].'-'.$price['minFeeHour']!='00:00-00:00') {
				$part_three = $price['thirdFeeHour'];
				$part_four = $price['minFeeHour'];
				if ($part_four < $part_three && 0 == $part_four) {
					$part_four = '24:00';
				}
				array_push($priceListAll, array (
					'part'=>$part_three.'-'.$part_four, 
					'price'=>$price['thirdFee']));
			}
		}
	}

}
$part = '00:00-24:00';
$price = 39;
$hour = date('H:i');
foreach ($priceListAll as $partPrice) {
	$arr = explode('-' , $partPrice['part']);
	if ($arr[0] >= '22:00' && $arr[1] < '22:00') {
		$tmp = '24:00';
	} else {
		$tmp = $arr[1];
	}
	
	if ($hour >= "00:00" && $hour <= '07:00' && $thirdFeeHour !== '00:00') {
		$hour = '23:50';
	}
	
	if ($hour >= $arr[0] && $hour <= $tmp) {
		$part = $arr[0].'-'.$arr[1];
		$price = $partPrice['price'];
	}
}
$ret = array('code' => 0 , 'part' => $part , 'price' => $price , 'message' => '获取成功');
echo json_encode($ret);
return ;