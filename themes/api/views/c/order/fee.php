<?php
/**
 * User: jack
 * Date: 2015/3/13
 * 司机点击"结束代驾"或添加贵宾卡，客户端拉取客户订单和支付等信息
 */
EdjLog::info('---online payment client fetch------ '.json_encode($params));
$token = isset($params['token']) ? trim($params['token']) : '';
$order_id = isset($params['order_id']) ? trim($params['order_id']):'';

if(empty($order_id) || empty($token)) {
    $ret = array('code' => 2 , 'data' => '' , 'message' => '参数有误');
    echo json_encode($ret);return ;
}
if (strlen($params['order_id']) > 11 && is_numeric($params['order_id'])) {
    //获取数据库中的order_id
    $order_id = ROrder::model()->getOrder($params['order_id'] , 'order_id');
    if (empty($order_id)) {
        $ret = array('code' => 2 , 'message' => '订单异常,请稍后');
        echo json_encode($ret);return ;
    }
}

$cache_key = 'ONLINE_PAY'.$order_id;
$online_params = Yii::app()->cache->get($cache_key);
if(!empty($online_params)){
    $data = array();
    $collection_fee = isset($online_params['collection_fee']) ?  $online_params['collection_fee'] : array();
    $settle_fee = isset($online_params['settle_fee']) ?  $online_params['settle_fee'] : array();
    $income = isset($online_params['income']) ?  $online_params['income'] : 0;
    $cast = isset($online_params['cast']) ?  $online_params['cast'] : 0;
    $account_pay_money = isset($online_params['account_pay_money']) ?  $online_params['account_pay_money'] : 0;//这单用户除了优惠券和贵宾卡还需要从余额扣除的钱
    $customer_phone = isset($online_params['customer_phone']) ?  $online_params['customer_phone'] : 0;
    $online_fee = BUpmpPayOrder::model()->validateOnlineOrder($customer_phone, $account_pay_money);//重新调该方法查询用户账户计算需要在线支付的金额
    $balance = $account_pay_money - $online_fee;//客户账户支付的金额
    $settle_fee[0]['value'] = $balance."元";//更新账户需要支付的值
    $cast = $online_fee;

    $completed = 0;//是否已保单 （0为否， 1为是）
    $model = Order::model()->getOrderById($order_id);
    if($model['status'] == Order::ORDER_COMPLATE){
        $completed = 1;
    }

    $data['collection_fee'] = $collection_fee;
    $data['settle_fee'] = $settle_fee;
    $data['income'] = $income;
    $data['cast'] = $cast;
    $data['order_id'] = $order_id;
    $data['completed'] = $completed;

    $ret = array('code' => 0 ,
        'data' => $data ,
        'message' => '成功'
    );
    EdjLog::info('---payonlinefee return------ '.json_encode($ret));
    echo json_encode($ret);return ;
}else{
    $ret = array('code' => 0,
        'data' => array() ,
        'message' => '拉取订单信息失败'
    );
    echo json_encode($ret);return ;
}