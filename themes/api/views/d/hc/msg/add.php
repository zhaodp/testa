<?php
/**
 * 添加工单
 * @params token,type,content
 * @return echo array('code'=>0,'message'=>'添加成功')
 * @modify  wanglonghuan 2013-12-25
 */
$params['content'] = (empty($params['content']) || $params['content']=="" ) ? '' : $params['content']; //内容
$params['type'] = (empty($params['type']) || $params['type']<=0 ) ? 0: $params['type']; //分类id
$params['order_id'] = (empty($params['order_id']) || $params['order_id']<=0 ) ? 0: $params['order_id'];//订单id
$params['device'] 	= empty($params['device'])  ? '' : trim($params['device']);  // 设备
$params['os'] 		= empty($params['os']) 		? '' : trim($params['os']);     //操作系统版本
$params['version'] = empty($params['version']) ? '' : trim($params['version']); //App 版本
$params['complaint_type'] = (empty($params['complaint_type']) || $params['complaint_type']<=0 ) ? 0: $params['complaint_type'];//投诉对象类型
$params['complaint_target'] = empty($params['complaint_target']) ? '': $params['complaint_target'];//投诉对象 电话、工号
//申诉id
$params['complaint_id'] = (empty($params['complaint_id']) || $params['complaint_id']<=0 ) ? 0: $params['complaint_id'];

$task_params = array();
$driver = DriverStatus::model()->getByToken($params['token']);
if ( empty($driver) ||  $driver->token===null || $driver->token!==$params['token'] ) {
    $ret=array(
        'code'=>1,
        'message'=>'请重新登录'
    );
    echo json_encode($ret);
    return;
}
if($params['content'] == "" || $params['type'] ==""){
    $ret=array(
        'code'=>2,
        'message'=>'参数错误，内容或者分类参数错误。'
    );
    echo json_encode($ret);
    return;
}
if(isset($driver->city_id) && empty($driver->city_id)){
    $ret=array(
        'code'=>2,
        'message'=>'司机信息 错误 city_id error',
    );
    echo json_encode($ret);
    return;
}
//投诉类
if($params['type'] == TicketUser::TICKET_CATEGORY_COMPLAINT){
    if(empty($params['complaint_type'])){
        $ret=array(
            'code'=>2,
            'message'=>'投诉参数错误 请检查complaint_type',
        );
        echo json_encode($ret);
        return;
    }
    $complaint_driver = null;
    if(empty($params['complaint_target']) && $params['complaint_type'] != 3){
        $ret=array(
            'code'=>2,
            'message'=>'参数错误 投诉对象不能为空',
        );
        echo json_encode($ret);
        return;
    }else{
        if($params['complaint_type'] == 1){
            $complaint_driver = Driver::model()->getDriverByPhone($params['complaint_target']);
            if(!$complaint_driver){
                $complaint_driver = Driver::getProfile($params['complaint_target']);
            }
            if(!$complaint_driver){
                $ret=array(
                    'code'=>2,
                    'message'=>'参数错误 输入司机工号或电话错误！',
                );
                echo json_encode($ret);
                return;
            }

            if($complaint_driver->user == $driver->driver_id){
                $ret=array(
                    'code'=>2,
                    'message'=>'师傅您不能投诉您自己！',
                );
                echo json_encode($ret);
                return;
            }
            $task_params['complaint_driver_user'] = $complaint_driver->user;
            $task_params['complaint_driver_phone'] = $complaint_driver->phone;
        }
    }
}


//订单类 如果不传订单id 返回参数错误
if($params['type'] == TicketUser::TICKET_CATEGORY_ORDER && $params['order_id'] == 0){
    $ret=array(
        'code'=>2,
        'message'=>'参数错误，订单id不能为空！',
    );
    echo json_encode($ret);
    return;
}
//添加 工单使用队列

    $task_params['driver_id']=$driver->driver_id;
    $task_params['phone'] =$driver->phone;
    $task_params['type'] = $params['type'];
    $task_params['content'] = $params['content'];
    $task_params['city_id']=$driver->city_id;
    $task_params['order_id']=$params['order_id'];
    $task_params['device'] = $params['device'];
    $task_params['os'] = $params['os'];
    $task_params['version'] = $params['version'];
    $task_params['complaint_type']=$params['complaint_type'];
    $task_params['complaint_target']=$params['complaint_target'];
    $task_params['complaint_id']=$params['complaint_id'];

$task=array(
    'method'=>'create_support_ticket',
    'params'=>$task_params,
);
Queue::model()->putin($task,'support');

$ret=array(
            'code'=>0,
            'message'=>'已成功填加至创建列表，请稍后查看。',
    );
echo json_encode($ret);
?>