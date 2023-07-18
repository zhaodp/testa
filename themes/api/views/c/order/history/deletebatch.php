<?php
/**
 * 客户端API：用户删除订单
 * @param token
 * @param order_id
 * @param pageNo
 * @param
 * @author bidong 2014-1-14
 * @return json
 */

//加缓存 一天
//$cache_key = 'ORDER_COMPLATE_' . $phone . '_' . $pageNo;
$token = isset($params['token'])? trim($params['token']):'';
$order_ids_str =isset($params['orderIds'])?trim($params['orderIds']):'';

// Delete all or delete some
$type=isset($params['type'])?trim($params['type']):NULL;

if(!isset($type) || empty($token)){
    $ret = array(
        'code' => 2,
        'message' => '参数缺失',
    );
    echo json_encode($ret);
    return;
}

if(empty($order_ids_str) && $type != ROrderHistory::DELETE_ALL) {
    $ret = array(
        'code' => 2,
        'message' => '参数缺失',
    );
    echo json_encode($ret);
    return;
}

$order_ids = array();

if(!empty($order_ids_str)) {
    $order_ids = explode(',',$order_ids_str);
}


//验证token 获取用户手机
$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
    $ret = array(
        'code' => 1,
        'message' => '验证失败',
    );
    echo json_encode($ret);
    return;
}
$phone = trim($validate['phone']);

$ret = array(
    'code' => 2,
    'message' => '删除失败',
);

Yii::import('application.models.schema.customer.CarCustomerDelOrder');
$table=CarCustomerDelOrder::model()->tableName();

if($type == ROrderHistory::DELETE_ALL) {
    // delete all
    // Get all order id
    while(true) {
	$order_ids = array();
	$order_ids = ROrderHistory::model()->getHistoryOrderIds($phone, 0); 
	$order_ids_count = count($order_ids);
	if($order_ids_count == 0){
	    break;
	}
	foreach($order_ids as $del_order_id){
            $command=Yii::app()->db->createCommand();
            $result=$command->insert($table, array(
                'phone'=>$phone,
                'order_id'=>$del_order_id,
                'create_time'=>date('Y-m-d H:i:s'),
            ));
	    $result= ROrderHistory::model()->delOrderId($phone, $del_order_id);
	}
    }
    //删除redis
    $result= ROrderHistory::model()->delOrderHistory($phone);
    $result = true;

} else if ($type == ROrderHistory::DELETE_BATCH) {
    // delete batch
    foreach($order_ids as $del_order_id){
	//逻辑删除数据
	$command = Yii::app()->db->createCommand();
	$result = $command->insert($table, array(
		    'phone'=>$phone,
		    'order_id'=>$del_order_id,
		    'create_time'=>date('Y-m-d H:i:s'),
		    ));

	//删除redis
	$result= ROrderHistory::model()->delOrderId($phone, $del_order_id);
    }
    $result = true;
} else{
    $result = true;
}

if($result){
    $ret = array(
        'code' => 0,
        'message' => '删除成功',
    );
}


echo json_encode($ret);
