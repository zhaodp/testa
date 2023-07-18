<?php
/**
 * 司机报名微信--获取报名司机二维码
 * @author duke
 * @version 2015-03-30
 */
$open_id = isset($params['open_id']) ? trim($params['open_id']) : '';
$ret = array('code' => 1 , 'data'=>'', 'message' => '访问错误');

if(empty($open_id)){
    $ret = array('code' => 1 , 'data'=>'', 'message' => '暂未生成二维码');
    $json_str = json_encode($ret);
    echo $json_str;Yii::app()->end();
}

$user_info = DriverRecruitment::model()->getInfoByOpenid($open_id);
if(!$user_info){
    $ret = array('code' => 35 , 'data'=>'', 'message' => '您尚未报名，请先报名考试');
    $json_str = json_encode($ret);
    echo $json_str;Yii::app()->end();
}

$qrcode = $user_info['qr_code'];
if(isset($qrcode) && $qrcode){
    $data = array('qrcode'=>$qrcode);
    $ret['data']=$data;
    $ret['code']=0;
    $ret['message'] = 'ok';
}

echo json_encode($ret);
Yii::app()->end();
