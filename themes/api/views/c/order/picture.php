<?php
/**
 * 客户端API：c.order.picture 客户端派单页红包接口
 * 调用的url:
 * @author cuiluzhe 2014-12-24
 * @param $params    $params['_callback']
 * @return json
 */

$lng = isset($params['longitude']) ? $params['longitude'] : '';
$lat = isset($params['latitude']) ? $params['latitude'] : '';
$cityName = isset($params['cityName']) ? $params['cityName'] : '';

if(empty($lng) && empty($lat) && empty($cityName)){
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
if($city_id == 0){
    $ret = array('code' => 0 , 'message' => '城市名字错误');
}else{
    $act = DispatchOrderAct::model()->getDispatchOrderAct($city_id);
    if(!$act){
        $ret = array('code' => 0 , 'message' => '该城市现阶段不存在活动');
    }else{
        $ret = array('code' => 0 , 'act'=>$act, 'message' => '获取成功');
    }
}

//支持5.4.1需求，用户提醒
$userNotifyPush = new UserNotifyPush();
EdjLog::info('Invoke userNotifyApi,params:'.json_encode($params));
$orderInfo=$userNotifyPush->userNotifyApi($params);
if($orderInfo){
    $ret['order']=$orderInfo;
}else{
    EdjLog::info('Invoke userNotifyApi,return empty.'.json_encode($params));
}
echo json_encode($ret);
return;


