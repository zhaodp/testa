<?php
/**
 * 司机报名微信--检测是否可以进行考试
 * @author duke
 * @version 2015-03-31
 */
$open_id = isset($params['open_id']) ? trim($params['open_id']) : '';
$ret = array('code' => 1 , 'message' => '请找考官扫描二维码开始考试');

if(empty($open_id)){
    $ret = array('code' => 1 , 'data'=>'', 'message' => '访问错误');
    $json_str = json_encode($ret);
//    if(isset($callback)&&!empty($callback)){
//        $json_str=$callback.'('.$json_str.')';
//    }
    echo $json_str;Yii::app()->end();
}



$exam_mod = DriverExamnewOnline::model();
$now = time();
//先检查当前用户是否有未完成的试题 20分钟内
$exam_id_redis = $exam_mod->checkExistQuestion($open_id,DriverExamnewOnline::TYPE_ONLINE);
if($exam_id_redis){
    $ret['code']=0;
    $ret['message']='可以考试';
}

echo json_encode($ret);
Yii::app()->end();




