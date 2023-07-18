<?php
/**
 * @author qiujianping@edaijia-inc.cn 2014-05-07
 * 
 * Api used to get inpire data   
 * Params need include the token
 */

$token = isset($params['token']) && !empty($params['token']) ? trim($params['token']) : "";

$driver = DriverStatus::model()->getByToken($token);

if ($driver == null || $driver->token === null || $driver->token !== $token) {
    $ret = array(
        'code' => 1,
        'message' => '请重新登录'
    );
    echo json_encode($ret);
    return;
}

$inspire_datas = DriverInspireData::model()->getInspireDataByDriverId($driver->driver_id);
$avg_inspire_datas = DriverInspireData::model()->getAvgInspireDataByCityId(0);

//add by aiguoxin
$reject_data = DriverInspireData::model()->getRejectRateByDriverId($driver->driver_id);
$cancal_data = DriverInspireData::model()->getCancelRateByDriverId($driver->driver_id);
//add by aiguoxin for get comments total
$comments = CommentSms::model()->getList(0, 10, $driver->driver_id);
$total = 0;
if($comments){
	$total = $comments['total'];
}

if(!empty($inspire_datas) && !empty($avg_inspire_datas) && !empty($reject_data) && !empty($cancal_data)) {
    $online_time_avg = $avg_inspire_datas['online_time']>$inspire_datas['online_time'] ? 0: 1;
    $receive_order_count_avg = 
	($avg_inspire_datas['receive_order_count']/100)>$inspire_datas['receive_order_count'] ? 0: 1;
    $receive_time_avg = 
	$avg_inspire_datas['accept_time']>$inspire_datas['accept_time'] ? 0: 1;
    $arrive_on_time_rate_avg = 
	$avg_inspire_datas['ready_on_time_rate']>$inspire_datas['ready_on_time_rate'] ? 0: 1;
    $ret = array(
	    'code' => 0,
	    'abnormal_data_monthly' => array(
		'refuse_order_rate' => $inspire_datas['reject_rate'],
		'refuse_order_rank' => $reject_data['ranking'],
		'cancel_order_rate' => $inspire_datas['cancel_rate'],
		'cancel_order_rank' => $cancal_data['ranking'],
		'non_praise_count' => $inspire_datas['non_praise_count'],
		),
	    'work_data_weekly' => array(
		'online_time' => $inspire_datas['online_time'],
		'online_time_average' => $online_time_avg,
		'receive_order_count' => $inspire_datas['receive_order_count'],
		'receive_order_count_average' => $receive_order_count_avg,
		'receive_time' => $inspire_datas['accept_time'],
		'receive_time_average' => $receive_time_avg,
		'arrive_on_time_rate' => $inspire_datas['ready_on_time_rate'],
		'arrive_on_time_rate_average' => $arrive_on_time_rate_avg,
		),
	    'work_data_monthly' => array(
		'online_time' => round($inspire_datas['mon_online_time']/60),
		'complete_order_count' => $inspire_datas['mon_complete_order_count'],
		'receive_order_count' => $inspire_datas['mon_receive_order_count'],
		'receive_time' => $inspire_datas['mon_accept_time'],
		'arrive_on_time_rate' => $inspire_datas['mon_ready_on_time_rate'],
		),
	    'evaluate_distribution' => array(
		'five_star_count' => $inspire_datas['five_star_count'],
		'four_star_count' => $inspire_datas['four_star_count'],
		'three_star_count' => $inspire_datas['three_star_count'],
		'two_star_count' => $inspire_datas['two_star_count'],
		'one_star_count' => $inspire_datas['one_star_count'],
		),
	    'total_comments' => $total,
	    'message' => '成功'
	    );
} else {
    $ret = array(
	    'code' => 0,
	    'abnormal_data_monthly' => array(
		'refuse_order_rate' => 0,
		'refuse_order_rank' => 0,
		'cancel_order_rate' => 0,
		'cancel_order_rank' => 0,
		'non_praise_count' => 0,
		),
	    'work_data_weekly' => array(
		'online_time' => 0,
		'online_time_average' => 0,
		'receive_order_count' => 0,
		'receive_order_count_average' => 0,
		'receive_time' => 0,
		'receive_time_average' => 0,
		'arrive_on_time_rate' => 0,
		'arrive_on_time_rate_average' => 0,
		),
	    'work_data_monthly' => array(
		'online_time' => 0,
		'complete_order_count' => 0,
		'receive_order_count' => 0,
		'receive_time' => 0,
		'arrive_on_time_rate' => 0,
		),
	    'evaluate_distribution' => array(
		'five_star_count' => 0,
		'four_star_count' => 0,
		'three_star_count' => 0,
		'two_star_count' => 0,
		'one_star_count' => 0,
		),
	    'total_comments' => 0,
	    'message' => '成功'
	    );
}
echo json_encode($ret);
return;
