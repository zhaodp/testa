<?php
/**
 * 司机报名h5---根据城市获取居住区域
 * @author luzhe
 * @version 2014-08-05
 */
$city_id = isset($params['city_id']) ? trim($params['city_id']) : '';
$callback=isset($params['callback'])?$params['callback']:'';

if(empty($city_id)){
    $ret = array('code' => 1 , 'message' => '参数有误');
    $json_str = json_encode($ret);
    if(isset($callback)&&!empty($callback)){
        $json_str=$callback.'('.$json_str.')';
    }
    echo $json_str;Yii::app()->end();
} 
$districts = District::model()->findAll('city_id=:city_id', array(':city_id' => $city_id));
if(empty($districts)){
    $ret = array('code' => 1 , 'message' => '您所居住的城市区域即将开通，请耐心等待');
    $json_str = json_encode($ret);
    if(isset($callback)&&!empty($callback)){
        $json_str=$callback.'('.$json_str.')';
    } 
    echo $json_str;Yii::app()->end();
}
$districts = CHtml::listData($districts,'id','name');
ksort($districts);
$ret = array('code' => 0 , 'message' => '获取成功','districts' => $districts);
$json_str = json_encode($ret);
if(isset($callback)&&!empty($callback)){
   $json_str=$callback.'('.$json_str.')';
}
echo $json_str;Yii::app()->end();
