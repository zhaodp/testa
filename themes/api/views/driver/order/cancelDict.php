<?php
//需要写清楚注释 add by sunhongjing at 2013-5-19
/*
 * modify  zhanglimin 2013-08-08  可别在变了。。。。。
 */


//按对象返回，防止数组索引丢失.
$cancelType= Dict::items('cancel_type');

unset($cancelType[0]); //去掉请选择

$complaintType = Dict::items('cancel_c_type');


//新加类型 销单
$orderCancelType = Dict::items('order_cancel_type');
ksort($orderCancelType);

//新加类型 投拆
$orderComplaintType = Dict::items('order_complaint_type');
ksort($orderComplaintType);

$order_cancel_type_step1 = array_slice($orderCancelType,0,1,true);

$order_complaint_type_step1 = $orderComplaintType;
unset($order_complaint_type_step1[31]);
unset($order_complaint_type_step1[99]);


$order_cancel_type_step2 = array_slice($orderCancelType,6,9,true);
$order_cancel_type_step2[11]  = $orderCancelType[11];
$order_complaint_type_step2 = array_slice($orderComplaintType,3,2,true);
$order_complaint_type_step2[12] = $orderComplaintType[12];
ksort($order_complaint_type_step2);

$order_complaint_type_step3 =  array_slice($orderComplaintType,5,2,true);

$ret = array (
    'code'		=> 0,
    'cancel'	=> (object)$cancelType,
    'complaint'	=> (object)$complaintType,
    'orderCancelTypeStep1' => (object)$order_cancel_type_step2,
    'orderCancelTypeStep2' => (object)$order_cancel_type_step1,

    'orderComplaintTypeStep1' => (object)$order_complaint_type_step2,
    'orderComplaintTypeStep2' => (object)$order_complaint_type_step1,
    'orderComplaintTypeStep3' => (object)$order_complaint_type_step3,

    'message'	=> '读取成功'
);

echo json_encode($ret);
return;