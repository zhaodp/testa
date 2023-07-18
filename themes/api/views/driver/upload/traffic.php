<?php
/**
 * 流量统计API
 * @param user	司机工号
 * @param total_rx	当日接收流量(Mb)
 * @param total_tx	当日发送流量(Mb)
 * @param rx	司机客户端接收流量(Mb)
 * @param tx	司机客户端发送流量(Mb)
 * @param device	手机型号
 * @param app_ver	软件版本号
 * @param created	日期
 * User: zhanglimin
 * Date: 13-6-24
 * Time: 下午2:16
 */
$driver_id = isset($params['user']) ? $params['user'] : "";
$e_receive_total = isset($params['rx']) ? $params['rx'] : "";
$e_send_total = isset($params['tx']) ? $params['tx'] : "";
$phone_receive_total = isset($params['total_rx']) ? $params['total_rx'] : "";
$phone_send_total = isset($params['total_tx']) ? $params['total_tx'] : "";

$device = isset($params['device']) ? $params['device'] : "";
$app_ver = isset($params['app_ver']) ? $params['app_ver'] : "";
$in_date = isset($params['created']) ? $params['created'] : "";

if(empty($driver_id) ||
   empty($e_receive_total) ||
   empty($e_send_total) ||
   empty($phone_receive_total)||
   empty($phone_send_total) ||
   empty($device) ||
   empty($app_ver) ||
   empty($in_date)
){
    $ret=array(
        'code'=>2,
        'message'=>'参数不正确'
    );
    echo json_encode($ret);
    return ;
}

$driver = DriverStatus::model()->get($driver_id);
if(!$driver){
    $ret=array(
        'code'=>2,
        'message'=>'工号不存在'
    );
    echo json_encode($ret);
    return ;
}

$task=array(
    'method'=>'driver_app_traffic',
    'params'=>array(
        'driver_id' => strtoupper(trim($driver_id)),
        'e_receive_total' => $e_receive_total,
        'e_send_total' => $e_send_total,
        'phone_receive_total' => $phone_receive_total,
        'phone_send_total' => $phone_send_total,
        'device' => $device,
        'app_ver' => $app_ver,
        'in_date' => $in_date,
    )
);
//Queue::model()->task($task);
//Queue::model()->putin($params=null,$queue_type=null)第二个参数是队列名称,可接受值见Queue::$queue_type_list;
Queue::model()->putin($task,'task');



$ret=array(
    'code'=>0,
    'message'=>'成功!'
);
echo json_encode($ret);
return;
