<?php
/**
 * 客户端API：c.order.comment 给订单的服务司机评价评分
 *
 * @author
 *
 * @param token
 * @param 标示订单的唯一标示
 * @param
 * @param
 * @return json,成功或者失败信息
 *
 * @version 1.0
 * @see
 */
$token = $params['token'];
$order_id = isset($params['order_id']) ? trim($params['order_id']) : '';
$driver_id = isset($params['driver_id']) ? trim($params['driver_id']) : '';
$order_status = isset($params['status']) ? trim($params['status']) : 1;
$level = isset($params['level']) ? trim($params['level']) : 0;
$content = isset($params['content']) ? trim($params['content']) : '';
$reason = isset($params['reason']) ? trim($params['reason']) : '';

//参数有效性验证
if (empty($token) || empty($order_id) || empty($driver_id)) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误'
    );
    echo json_encode($ret);
    return;
}

if (empty($content)) {
    $ret = array(
        'code' => 2,
        'message' => '评价内容不能为空'
    );
    echo json_encode($ret);
    return;
}

//校验token
$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
    $ret = array(
        'code' => 1,
        'message' => '验证失败'
    );
    echo json_encode($ret);
    return;
}

//评价
$command_data = array();


////////////////

//评论为非五星 则进入投诉系统  duke add


$record_complaint = 1;  //是否转入投诉系统
switch($level){
    case 5:
        $record_complaint = 0;
        break;
    default:

        break;
}
//短信回评 汇入投诉系统
if($record_complaint){
    $customer_complian=new CustomerComplain();
    $customer_complian->name=$validate['phone'];
    $customer_complian->phone=$validate['phone'];
    $customer_complian->customer_phone=$validate['phone'];
    $customer_complian->driver_id=$driver_id;

    $customer_complian->order_id=$order_id;
    //$customer_complian->complain_type='';

    $customer_complian->source=3;//来源 app 客户评价
    $customer_complian->create_time=date('Y-m-d H:i:s');
    $customer_complian->update_time=date('Y-m-d H:i:s');
    $customer_complian->created =
    $customer_complian->operator='用户评价后转入';
    $customer_complian->status=1;
    $customer_complian->city_id=DriverStatus::model()->getItem($driver_id,'city_id');

    $customer_complian->cs_process=1;//客服创建
    $customer_complian->detail=$content;

    $customer_complian->insert();
    $command_data['status'] = 1;
    //push to driver add aiguoxin 2014-05-22
    $data = array(
        'driver_id' => $driver_id,
    );
    //添加task队列更新数据库
    $task=array(
        'method'=>'driver_complain_push',
        'params'=>$data,
    );
    Queue::model()->putin($task,'pushmsg');
}
//评论为非五星 则进入投诉系统 end   duke add

///////////////
$command_data['order_id'] = $order_id;
$command_data['level'] = $level;
$command_data['content'] = $content;
$command_data['reason'] = $reason;
$command_data['sender'] = $validate['phone'];
$command_data['driver_id'] = $driver_id;
$command_data['order_status'] = $order_status;

//为什么不走队列？ add by sunhongjing
$comment_sms = CommentSms::model()->addOrderCommand($command_data);



$ret = array('order_id' => $order_id);
switch($comment_sms){
    case 0:
        $ret['code'] = 0;
        $ret['message'] = '评价成功';
        break;
    case 1:
        $ret['code'] = 2;
        $ret['message'] = '添加失败';
        break;
    case 2;
        $ret['code'] = 2;
        $ret['message'] = '已经评论';
        break;
}
echo json_encode($ret);
return;
