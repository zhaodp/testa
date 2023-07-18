<?php
//通过GPS位置获取App价格表
$lng = $params['longitude'];
$lat = $params['latitude'];
$cityName = isset($params['cityName']) ? $params['cityName'] : '';
$expireAt = Yii::app()->params['appContent']['expireAt'];
$priceContent = Yii::app()->params['appContent']['priceContent'];


if ($cityName=='') {
	//查询百度地图返回城市名称
	$cityName = GPS::model()->getCityByBaiduGPS($lng,$lat);
}



$cityId = CityConfig::getIdByName($cityName);
$daytimeType = RCityList::model()->getCityById($cityId,'daytime_price');
if ($daytimeType) {
    $isDayTime = true;
} else {
    $isDayTime = false;
}
$price = RCityList::model()->getFee($cityId, $isDayTime, true);



$priceListAll = array (
    array (
        'title'=>sprintf($priceContent['title']['zh'], $cityName)),
    array (
        'title'=>$priceContent['period']['zh'],
        'detail'=>sprintf($priceContent['pricingStart']['zh'], $price['distince'])
    )
);

foreach($price['part_price'] as $v){
    $priceListAll[]=array('title'=>$v['start_time'].'-'.$v['end_time'],'detail'=>$v['price'].'元');
}

if($isDayTime){
    $no1 = $priceContent['memo']['4']['zh'];
    $n  = Yii::app()->params['daytime_price_new'][$daytimeType];
    $a = $n['basic_time']/60;
    $no1 = sprintf($no1, $n['start_time'], $n['end_time'], $n['price'],  $a, $n['basic_distance'], $n['beyond_time_unit'], $n['beyond_time_price'],
        $n['beyond_distance_unit'],$n['beyond_distance_price'],$n['beyond_time_unit'],$n['beyond_time_unit'],$n['beyond_distance_unit'],$n['beyond_distance_unit']);
    //白天%s~%s时段%s元起步（含$s小时、$s公里）， 超出部分每增加$s分钟$s元，代驾距离每增加$s公里加收$s元。超出部分时间不足$s分钟按$s分钟计算，里程不足$s公里按$s公里计算。夜间
    $first_info = $no1.$priceContent['memo']['1']['zh'];
}else{
    $first_info = $priceContent['memo']['1']['zh'];
}

$ret = array (
    'code'=>0,
    'priceList'=>$priceListAll,
    'memo'=>array (
        'memo'=>$priceContent['memo']['memo']['zh'],
        '1'=>'1、'.$first_info,
        '2'=>'2、'.sprintf($priceContent['memo']['2']['zh'], $price['distince'], $price['next_distince'], $price['next_price'], $price['next_distince'], $price['next_distince']),
        '3'=>'3、'.sprintf($priceContent['memo']['3']['zh'], $price['before_waiting_time'], $price['before_waiting_price'],$price['before_waiting_time']),
        'total'=>3),
    'expireAt'=>$expireAt,
    'city'=>$cityName,
    'message'=>'');


echo json_encode($ret);