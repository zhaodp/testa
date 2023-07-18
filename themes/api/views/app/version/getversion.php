<?php
//add by cuiluzhe
/**
 * get app download url by type
 *
 */

$type = isset($params['type']) ? trim($params['type']) : '0';

$app_sources = Yii::app()->params['app_source'];

if(!array_key_exists($type,$app_sources)){
    $type = '0';
}
$result = $app_sources[$type];
if(!isset($result) || empty($result)){
    $result = $app_sources['0'];
}
$ret = array('code'=>0,'url'=>$result['url'],'title'=>$result['title']);
echo json_encode($ret);
return;

