<?php
echo json_encode(array ());return;
/**
 * 这个接口需要客户端配合检查是否还在使用，如果使用中，则需要优化。add by sunhongjing  at 2013-05-19
 * 
 * 获取指定时间段的客户呼叫位置及时间分布
 * @param start 开始时间
 * @param end   结束时间
 * $param city_id 城市ID
 * @author liyuqing 2012-10-16
 * 
 * 
 */
//$start = $params['start'];
//$end = $params['end'];
$start = date('Y-m-d 07:00:00',time()-86400*2);
$end = date('Y-m-d 07:00:00',time());

$city_id = $params['city_id'];

$sql = 'SELECT device, longitude,latitude,call_time 
		FROM `t_app_call_record` c, `t_driver` d
		where c.driverID = d.user and d.city_id=:city_id and call_time between unix_timestamp(:start) and unix_timestamp(:end)
		Group by macaddress,driverID
		ORDER BY c.`id`';

$params = array (
	':start'=>$start, 
	':end'=>$end, 
	':city_id'=>$city_id);
$positions = Yii::app()->db->createCommand($sql)->queryAll(true, $params);

if ($positions) {
	echo json_encode($positions);
} else {
	echo json_encode(array ());
}