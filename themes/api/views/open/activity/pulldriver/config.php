<?php
/**
 * 司机拉新活动 获取活动配置,初始化页面
 */
$config = DriverPullNewConfig::model()->getConfig();
if(!$config){
    $ret = array ('code'=>2, 'message'=>'不存在活动配置信息');
    echo json_encode($ret);
    return;
}
$today = date('Y-m-d',time());
if($config['begin_time']>$today){
    $ret = array ('code'=>2, 'message'=>'活动暂没开始,请耐心等待');
    echo json_encode($ret);
    return;
}
if($config['end_time']<$today){
    $ret = array ('code'=>2, 'message'=>'活动已经结束');
    echo json_encode($ret);
    return;
}
$citys = explode(',', $config['city_id']);
$tmp = RCityList::model()->getOpenCityList();
$city_name = '';
foreach ($tmp as $key => $value) {
    if(in_array($key, $citys)){
        $city_name .= $value;
        $city_name .= ',';
    }
}
if(!empty($city_name)){
    $city_name = rtrim($city_name, ',');
}
$data = array();
$data['end_time'] = $config['end_time'];
$data['city_name'] = $city_name;
$ret = array ('code'=>000, 'data'=>$data, 'message'=>'获取成功');
echo json_encode($ret);return;