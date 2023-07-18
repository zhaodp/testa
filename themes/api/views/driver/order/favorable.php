<?php
/**
 * 司机端获取交易优惠信息
 * @author zhongfuhai
 * @version 2015-05-05
 */
if(Yii::app()->params['order_architecture_refactor_on']){
    $result = OrderDealService::getInstance()->getDealInfo($params);
    echo json_encode($result);
    return;
}

/**
 * 获取优惠信息api
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-07-16
 */
//接收并验证参数
$phone = isset($params['phone']) ? $params['phone'] : '';
$booking_time = isset($params['booking_time']) ? $params['booking_time'] : '';
$order_number = isset($params['order_number']) ? $params['order_number'] : '';
$order_id = isset($params['order_id']) ? trim($params['order_id']) : '';
$token = isset($params['token']) ? $params['token'] : '';

//增加log 2014-03-20
EdjLog::info("phone:".$phone."|order_number:".$order_number."|order_id:".$order_id."|拉取优惠信息|begin", 'api' );

if (empty($phone) || empty($booking_time) || empty($order_number) || empty($token)) {

        //增加log 2014-03-20
        EdjLog::warning("phone:".$phone."|order_number:".$order_number."|order_id:".$order_id."|拉取优惠信息|参数错误", 'api' );

    $ret = array('code' => 2 , 'message' => '参数有误');
    echo json_encode($ret);return ;
}

//验证token
$driver = DriverStatus::model()->getByToken($token);
if ($driver) {

    //返回给司机信息初始化
    $ret = array('code' => 0 , 'message' => '获取成功');

    //走DB 先获取订单信息
    if(empty($order_id)) {
         // 如果order_id为空 用order_number获取订单
         $order = Order::model()->getOrderByOrderNumberOnly($order_number);
    } else {
        if (strlen($order_id) > 11 && is_numeric($order_id)) {
        // 选司机下单 order_id 为t_order表中的order_number
            $order = Order::model()->getOrderByOrderNumberOnly($order_id);
        } else {
            $order = Order::model()->getOrdersById($order_id);
    }
    }

    if(empty($order)) {  //订单不存在 等待下一次拉取

        //增加log 2014-03-20
        EdjLog::info("phone:".$phone."|order_number:".$order_number."|order_id:".$order_id."|拉取优惠信息|订单信息不存在", 'api' );

        echo json_encode($ret);return;
    }

    //优惠信息数据初始化
    $data = array(
        'cost_type' => 0 ,
        'vipcard' => '' ,
        'bonus' => '' ,
        'card' => '' ,
        'balance' => 0,
        'user_money' => 0,
        'order_id' => $order['order_id'],
        'order_number' => $order_number,
    );
    //选司机下单仍然返回unique_order_id
    if (strlen($order_id) > 11 && is_numeric($order_id)) {
        $data['order_id'] = $order_id;
    }

    //获取优惠信息
    $favorable = Order::model()->getOrderFavorable($order['phone'] , $order['booking_time'] , $order['source'] , $order['order_id']);
    if($favorable){
        $favorable['user_money'] = isset($favorable['user_money']) ? $favorable['user_money'] : 0;
        $favorable['money'] = isset($favorable['money']) ? $favorable['money'] : 0;
        $data['card'] = isset($favorable['card']) ? $favorable['card'] : '';

        $data['balance'] = $favorable['money'] + $favorable['user_money'];
        $data['user_money'] = $favorable['user_money'];
        $data['cost_type'] = (string)$favorable['code'];
        switch($favorable['code']){
            case 1:
                $data['vipcard'] = '余额：'.$favorable['money'].'元,不足部分请收取现金';
                break;
            case 2:
                $data['bonus']=' 优惠金额：'.$favorable['money'].'元';
                break;
            case 4:
                $data['bonus']=' 优惠金额：'.$favorable['money'].'元,个人帐户余额'.$favorable['user_money'].'元,不足部分请收取现金';
                break;
            case 8:
                $data['bonus']=' 个人帐户余额：'.$favorable['user_money'].'元,不足部分请收取现金';
                break;
        }
    }
    //返回订单小费,补贴金额
    $orderExt = OrderExt::model()->getPrimary($order_id);
    $tips = FinanceCastHelper::getOrderFeeByExt($orderExt);
    $subsidyMoney = FinanceCastHelper::getSubsidy($order, $orderExt);
    $data['fee'] = $tips;
    $data['subsidy_money'] = $subsidyMoney;

    $ret = array('code' => 0 , 'fav' => $data , 'message' => '获取成功');

    //增加log 2014-03-20
    EdjLog::info("phone:".$phone."|order_number:".$order_number."|order_id:".$order_id."|拉取优惠信息|".json_encode($data), 'api' );

} else { //token失效

    //增加log 2014-03-20
    EdjLog::warning("phone:".$phone."|order_number:".$order_number."|order_id:".$order_id."|拉取优惠信息|token失效" , 'api' );

    $ret = array('code' => 1 , 'message' => '请重新登录');

}
echo json_encode($ret);
return ;
