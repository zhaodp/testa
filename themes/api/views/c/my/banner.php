<?php
/**
 * 客户端API：c.my.banner 客户端用户中心底部banner
 * 调用的url:
 * @author duke 2015-01-13
 * @param $params
 * @return json
 */

$lng = isset($params['longitude']) ? $params['longitude'] : '';
$lat = isset($params['latitude']) ? $params['latitude'] : '';
$cityName = isset($params['cityName']) ? $params['cityName'] : '';

if(empty($lng) && empty($lat) ){
    $ret = array('code' => 0 , 'message' => '参数错误');
    echo json_encode($ret);
    return;
}
if ($cityName == '') {
    //查询百度地图返回城市名称
    $cityName = GPS::model()->getCityByBaiduGPS($lng,$lat);
}else{
    $city= explode("市",$cityName);
    if(count($city)>1){
        $cityName=$city[0];
    }
}
$citys = Dict::items('city');
$citys_flip = array_flip($citys);
$city_id = 0;
if(isset($citys_flip[$cityName])){
    $city_id = $citys_flip[$cityName];
}
if($city_id == 0) {
    $ret = array('code' => 45 , 'message' => '城市名字错误');
}else {
    $act = array(
//        'pic_url'=>'http://pic.edaijia.cn/client/banner_client_new.png',
//        'act_url'=>'http://wap.edaijia.cn'
    );//DispatchOrderAct::model()->getDispatchOrderAct($city_id);
    if(!$act){
        $ret = array('code' => 0 , 'message' => '该城市现阶段不存在活动');
    }else{
        $ret = array('code' => 0 , 'act'=>$act, 'message' => '获取成功');
    }
}
echo json_encode($ret);
return;