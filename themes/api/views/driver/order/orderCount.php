<?php
/**
 * 司机端API：driver.order.orderCount 返回订单数
 *
 * @author
 * @param token
 * @param
 *
 * @return json 成功返回成功信息，异常返回错误代码，需要附带返回结果的例子
 * @example
 */
	//验证参数格式
	$token  = isset($params['token']) ? trim($params['token']) : '';
	$status = isset($params['type']) ? trim($params['type']) : '0';

	if ('' == $token) {
		$ret=array(
	        'code'=>2,
	        'message'=>'参数有误!'
	    );
	    echo json_encode($ret);
	    return;
	}
	//验证参数 END

	//验证token
	$driver = DriverStatus::model()->getByToken($token);
	if (empty($driver) ||  $driver->token===null||$driver->token!==$token) {
	    $ret=array(
	        'code'=>1,
	        'message'=>'请重新登录'
	    );
	    echo json_encode($ret);
	    return;
	}
	//验证token END
	
	$driver_id = $driver->driver_id;
	$status 	= ( empty($status) || !in_array( $status, array('0','1','2','3','4','5','6','7') ) ) ? 0 : $status;
    EdjLog::info('whytest2:driver_id='.$driver_id.'status='.$status,'console');
    if ($status != 0) {
        $orderCount = Order::model()->getDriverOrderCountWithStatus($driver_id, $status);
        EdjLog::info('whytest3:driver_id='.$driver_id.',orderCount='.$orderCount,'console');
    } else{
        $orderCount = Order::model()->getDriverOrderCount($driver_id);
        EdjLog::info('whytest4:driver_id='.$driver_id.',orderCount='.$orderCount,'console');
    }

    $ret = array (
        'code'=>0,
        'total'=>$orderCount,
        'message'=>'读取成功'
    );

    echo json_encode($ret);
    return;
