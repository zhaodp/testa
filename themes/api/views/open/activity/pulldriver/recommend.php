<?php
/**
 * 司机拉新活动 帮他报名api
 */
$phone = isset($params['phone']) ? trim($params['phone']) : '';
$id_card = isset($params['id_card']) ? trim($params['id_card']) : '';
if(empty($phone) || empty($id_card)){
	$ret = array ('code'=>2,'message'=>'参数错误');
	echo json_encode($ret);return;
}
$log = new DriverPullNewRecommendLog();
$log->phone = $phone;
$log->id_card = $id_card;
$log->create_time = date('Y-m-d H:i:s',time());
$save_ret = $log->save();
if(!$save_ret){
    EdjLog::info('保存失败'.json_encode($log->getErrors()));
    $ret = array ('code'=>2, 'message'=>'推荐失败,请重新推荐下');
}else{
    $ret = array ('code'=>0, 'message'=>'推荐成功');
}
echo json_encode($ret);return;