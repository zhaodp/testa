<?php

$province_id = isset($params['province_id']) ? trim($params['province_id']) : '';
$callback = isset($params['callback']) ? $params['callback'] : '';
if(empty( $province_id)){
    $criteria = new CDbCriteria();
    $criteria->select = 'id,name';
    $criteria->addCondition( 'status=1');
    $province = CityProvince::model()->findAll($criteria);
    $result = array();
    foreach($province as $p) {
        $attr = $p->getAttributes();
        unset($attr['status']);
        $result[] = $attr;
    }
    if($province){
        $ret = array('code' => 0 , 'message' => '获取成功','provincelist' => $result);
    } else {
        $ret = array('code' => 1 , 'message' => '获取失败');
    }


} else {
    $criteria = new CDbCriteria();
    $criteria->select = 'city_id,city_name';
    $criteria->addCondition( 'status=1 and province_id='.$province_id);
    $citylist = CityConfig::model()->findAll($criteria);
    $result = array();
    foreach($citylist as $c) {
        $attr = array();
        $attr['city_id'] = $c['city_id'];
        $attr['city_name'] = $c['city_name'];
        $result[] = $attr;
    }
    if($citylist){
        $ret = array('code' => 0 , 'message' => '获取成功','citylist' => $result);
    } else {
        $ret = array('code' => 1 , 'message' => '获取失败');
    }
}

$json_str = json_encode($ret);
if (isset($callback) && !empty($callback)) {
    $json_str = $callback . '(' .$json_str . ')';
}
echo $json_str;
Yii::app()->end();
