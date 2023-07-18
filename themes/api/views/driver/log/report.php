<?php
/**
 * Provided a way for driver to upload message and save in push
 *
 * @author qiujianping@edaijia-staff.cn
 * @version 2014-04-24
 */

$token = isset($params['token']) ? $params['token'] : '';
$msg = isset($params['content']) ? $params['content'] : '';
$app_ver = isset($params['app_ver']) ? $params['app_ver'] : '';
$model = isset($params['model']) ? $params['model'] : '';
$os = isset($params['os']) ? $params['os'] : '';
$type = isset($params['type']) ? $params['type'] : -1;

if (empty($token)) {
    EdjLog::info('Error | Empty token');
    $ret = array('code' => 1 , 'message' =>'Empty token');
    echo json_encode($ret);
    return ;
}

// Get the type string
$type_str = 'DEFAULT';
switch($type) {
    case 1:
	$type_str = 'BaiduMap';
	break;
    case 2:
	$type_str = 'GeTuiPush';
	//收到回执后,清除redis中push信息缓存
        //司机端上传的信息无法用json_decode解析,用正则匹配
        if(preg_match('#push_distinct_id:(\d+)\}#', $msg, $matches)) {
            if(!empty($matches) && !empty($matches[1])) {
                ROrder::model()->delMessage($matches[1]);
            }
        }
	break;
    case 3:
	$type_str = 'Anr';
	break;
    case 4:
	$type_str = 'edaijia';
	break;
    case 5:
	$type_str = 'Network';
	break;
    case 6:
	$type_str = 'DriverStatus';
	break;
    case 7:
	$type_str = 'UUPay';
	break;
    default:
	break;
}

//验证token
$driver = DriverStatus::model()->getByToken($token);
if ($driver) {
    EdjLog::info($driver->driver_id.'|'.$type_str.'|'.$model.'|'
	    .$os.'|'.$app_ver.'|'.$msg);
    $ret = array('code' => 0 , 'message' =>'Success');
} else {
    EdjLog::info('Invalid token|'.$token.'|'.$msg);
    $ret = array('code' => 1 , 'message' =>'Invalid token');
}

echo json_encode($ret);
return ;
