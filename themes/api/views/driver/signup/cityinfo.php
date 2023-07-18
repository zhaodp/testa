<?php

$ip = Common::getClientRealIp();

$callback = isset($params['callback']) ? $params['callback'] : '';

try {
    $city = GPS::model()->getCityByIP($ip);
    if($city === FALSE ||
        strpos($city, '金华') !== FALSE || //如果城市返回金华或义乌，显示请选择
        strpos($city, '义乌') !== FALSE ||
        strpos($city, '苏州') !== FALSE ||
        strpos($city, '昆山') !== FALSE){
        $result['city_id'] = -1;
        $result['city_name'] = '请选择';
    }else{
        $city_name = mb_substr($city, 0, 2,'utf-8');
        $criteria = new CDbCriteria();
        $criteria->select = 'city_id,city_name';
        $criteria->addCondition('status=1 and  city_name like \'%' . $city_name . '%\'');
        $city = CityConfig::model()->find($criteria);

        $result = array();
        $result['city_id'] = $city['city_id'];
        $result['city_name'] = $city['city_name'];
    }
    $json_str = json_encode($result);
    if (isset($callback) && !empty($callback)) {
        $json_str = $callback . '(' . json_encode($result) . ')';
    }
    echo($json_str);

    Yii::app()->end();
} catch (ErrorException $e) {
    $result = array();
    $result['city_id'] = 1;
    $result['city_name'] = '北京';
    $json_str = json_encode($result);
    if (isset($callback) && !empty($callback)) {
        $json_str = $callback . '(' . json_encode($result) . ')';
    }
    echo($json_str);

    Yii::app()->end();
}
