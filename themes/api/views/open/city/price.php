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


$cityId=CityConfig::getIdByName($cityName);

$price = RCityList::model()->getFee($cityId,true,true);

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

$ret = array (
    'code'=>0,
    'priceList'=>$priceListAll,
    'memo'=>array (
        'memo'=>$priceContent['memo']['memo']['zh'],
        '1'=>$priceContent['memo']['1']['zh'],
        '2'=>sprintf($priceContent['memo']['2']['zh'], $price['distince'], $price['next_distince'], $price['next_price'], $price['next_distince'], $price['next_distince']),
        '3'=>sprintf($priceContent['memo']['3']['zh'], $price['before_waiting_time'], $price['before_waiting_price']),
        'total'=>3),
    'expireAt'=>$expireAt,
    'city'=>$cityName,
    'message'=>'');


echo json_encode($ret);
