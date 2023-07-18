<?php
Yii::import("application.models.redis.*");
/**
 * 结束代驾的时候,上传订单的信息,包括扣款信息以及其他后续要的信息
 * User: jack
 * Date: 2015/3/9
 * Time: 10:31
 *
 * 结算成功：
代驾服务结束，费用共计80元已结算成功。起步价39元（含1小时、10公里），行驶5小时35分钟，50公里；优惠券抵扣20元，账户余额支付60元。
继续支付：
代驾服务结束，请点击进行支付 >> 费用总计80元，优惠券抵扣20元，账户余额扣款20元，仍需支付40元。
 */
EdjLog::info('---online payment params------ '.json_encode($params));
//接受参数
if( isset($params['token'])
    && isset($params['order_id'])
    && isset($params['income'])){

    $token  = $params['token'];
    $driver_id = DriverToken::model()->getDriverIdByToken($token);
    if(!$driver_id){
        $ret = array (
            'code'=>1,
            'message'=>'请重新登录');
        echo json_encode($ret);return;
    }
    $order_id = isset($params['order_id']) ? $params['order_id'] : 0;
    if (strlen($params['order_id']) > 11 && is_numeric($params['order_id'])) {
        //获取数据库中的order_id
        $order_id = ROrder::model()->getOrder($params['order_id'] , 'order_id');
        if (empty($order_id)) {
            $ret = array('code' => 2 , 'message' => '订单异常,请稍后');
            echo json_encode($ret);return ;
        }
    }
    $wait_time=isset($params['waiting_time'])?$params['waiting_time']:0;
    $wait_time = FinanceUtils::convertTimeString(ceil($wait_time));//等候时间 那边传的就是分钟

    $cost_type = isset($params['cost_type']) ? $params['cost_type'] : 0;
    $beyond_time_cost = isset($params['beyond_time_cost']) ? $params['beyond_time_cost'] : 0; //日间订单超过基础时间收取的费用
    $beyond_distance_cost = isset($params['beyond_distance_cost']) ? $params['beyond_distance_cost'] : 0; //日间订单超过基础距离收取的费用
    $modify_name = isset($params['modify_name']) ? $params['modify_name'] : '';//调整费名称
    $modify_price = isset($params['modify_price']) ? $params['modify_price'] : 0;//调整费
    $modify_type = isset($params['modify_type']) ? $params['modify_type'] : 0;//调整费类型1加钱2减钱(整形)
    $cash_card_balance = isset($params['cash_card_balance']) ? $params['cash_card_balance'] : 0;//贵宾卡金额 点结束代驾的时候这个值为0 重新点贵宾卡的时候才有有值
    $order_type = isset($params['order_type']) ? $params['order_type'] : 0;//订单类型 1：普通单 2：日间单 3：洗车单
    $income = isset($params['income']) ? $params['income'] : 0;//订单总金额
    $start_price = isset($params['start_price']) ? $params['start_price'] : 0;//起步价 日间单才有
    $night_subsidy = isset($params['night_subsidy']) ? $params['night_subsidy'] : 0;//夜间补贴 日间单才有
    $distance_fee = isset($params['distance_fee']) ? $params['distance_fee'] : 0;//里程费 普通单的时候有
    $wait_price = isset($params['wait_price']) ? $params['wait_price'] : 0;//等候费 普通单的时候有
    $remote_subsidy = isset($params['remote_subsidy']) ? $params['remote_subsidy'] : 0;//远程补贴 普通单的时候有
    $remote_tip = isset($params['remote_tip']) ? $params['remote_tip'] : 0;//远程小费 普通单的时候有
    $beyond_time = isset($params['beyond_time']) ? $params['beyond_time'] : 0;//日间单超过的的时间
    $beyond_time = FinanceUtils::convertTimeString(ceil($beyond_time));//日间单超过的的时间 那边传的就是分钟
    $beyond_distance = isset($params['beyond_distance']) ? $params['beyond_distance'] : 0;//日间单超过的距离
    $startPrice_desc = isset($params['startPrice_desc']) ? $params['startPrice_desc'] : '';//起步价描述
    $force_cash = isset($params['force_cash']) ? $params['force_cash'] : 0;//是否仅收现金 1：仅收现金 0：非仅收现金
    $distance = isset($params['distance']) ? $params['distance'] : 0;//总里程
    $startTime = isset($params['start_time']) ? $params['start_time'] : 0;
    $endTime = isset($params['end_time']) ? $params['end_time'] : 0;
    $endTime = round($params['end_time'] / 1000);// 司机端上传上来的是毫秒级的
    $startTime = round($params['start_time'] / 1000);
    $cast = 0;//待充值的金额
    $balance = 0;//账户支付的金额
    $bonus = 0;//优惠券金额
    $coupon = 0;//实体卡金额
    $customer_phone = 0;
    $push_message_format = '';//提示信息
    if($endTime < $startTime){
        $endTime = $startTime;//司机端在某些极端情况会出现没有 end_time 的时候,这里进行兼容
    }
    $driver_time = FinanceUtils::convertTimeString(($endTime - $startTime)/60);//行驶时间
    //根据orderid得到该订单的信息
    $order = Order::model()->getOrderById($order_id);

    /** 以下为具体逻辑处理 */
        //2.司机点击"结束代驾"按钮上传数据
        $bonusMoney = 0;
        $customer_phone = $order['phone'];
        //得到该用户当前订单时刻的账户优惠金额 如果不是vip则存在优惠券
        $isVip = CustomerMain::model()->isVip($customer_phone);
        if(!$isVip){
            $bonusMoney = BBonus::model()->getBonusMoneyByOrderId($order_id, $customer_phone);
        }
        $pay_money = 0;
        $modify_fee = 0;//调整费
        $push_message_format .="您有1单代驾服务结束，";
        $income = $income + $remote_subsidy + $remote_tip;//张强那边传过来的总金额没有算上这两项所以这里总金额应该直接加上远程补贴和小费
        if($bonusMoney >= $income ){
            //优惠券就已经足够支付
            $push_message_format.= "费用总计".(int)$income."元，将使用优惠券".(int)$bonusMoney."元进行结算。";
        }
        if($modify_type == 2 ){
            $modify_fee = $modify_price;
        }
        $favorable_fee = $bonusMoney + $cash_card_balance;//优惠包括：优惠券+贵宾卡+(调整费张强那边计算我这边不参与计算)
        if($favorable_fee < $income){
            $pay_money = $income - $favorable_fee;//还需要从账户余额支付的金额
            $online_fee = BUpmpPayOrder::model()->validateOnlineOrder($customer_phone, $pay_money);//需要在线支付的金额
            $balance = $pay_money - $online_fee;//客户账户支付的金额
            $balance = ($balance < 0) ? 0 : $balance;
            $cast = $online_fee;
            if($online_fee > 0){//不足支付订单费用
                $push_message_format .= "请进行支付：费用总计".(int)$income."元，";
                if($bonusMoney > 0){
                    $push_message_format .= "优惠券抵扣".$bonusMoney."元、";
                }
                if($balance > 0){
                    $push_message_format .= "账户余额扣款".(int)$balance."元，";
                }
                $push_message_format .= "应支付".(int)$cast."元。";
            }else if($online_fee == 0){//足够支付订单费用
                $push_message_format .= "费用总计".(int)$income."元，将使用";
                if($bonusMoney > 0){
                    $push_message_format .= "优惠券".$bonusMoney."元、";
                }
                if($balance > 0){
                    $push_message_format .= "账户余额".(int)$balance."元";
                }
//                if($modify_type == 2 ){//调整费为扣款的时候  调整费不参与计算
//                    $push_message_format .= $modify_name."-".$modify_price."元";
//                }
                $push_message_format .= "进行结算。";
            }
        }
        if($cash_card_balance > 0){
            $push_message_format = "您将使用e代驾贵宾卡抵扣".(int)$cash_card_balance."元，如果贵宾卡金额不符，请与司机确认。";
        }
        $ret = array();
        if($order_type == 1){
            //普通单展示:里程费+等候费+调整费
            if($distance_fee > 0) $ret[] = array('key' => '里程费','value' => '('.$distance.'公里)'.(int)$distance_fee."元",);
            if($wait_price > 0)   $ret[] = array('key' => '等候费','value' =>'('.$wait_time.')'.(int)$wait_price."元",);
            if($remote_subsidy > 0)   $ret[] = array('key' => '远程补贴','value' =>(int)$remote_subsidy."元",);
            if($remote_tip > 0)   $ret[] = array('key' => '远程小费','value' =>(int)$remote_tip."元",);
            if($modify_price > 0) $ret[] = array('key' => $modify_name,'value' =>(int)$modify_price."元",);
        }else if($order_type == 2){
            //日间单展示:起步价费+时间费加费+里程附加费+调整费+夜间补贴费
            if($start_price > 0) $ret[] = array('key' => '起步价','value' =>'('.$startPrice_desc.')'.$start_price."元",);
            if($beyond_distance_cost > 0) $ret[] = array('key' => '里程附加费','value' =>'('.$beyond_distance.'公里)'.$beyond_distance_cost."元",);
            if($beyond_time_cost > 0) $ret[] = array('key' => '时间附加费','value' =>'('.$beyond_time.')'.$beyond_time_cost."元",);
            if($night_subsidy > 0) $ret[] = array('key' => '夜间补贴','value' =>(int)$night_subsidy."元",);
            if($modify_price > 0) $ret[] = array('key' => $modify_name,'value' =>(int)$modify_price."元",);
        }
        $ret2 = array();
        $ret2[] = array('key' => '账户支付','value' =>(int)$balance."元",);
        if($bonusMoney > 0) $ret2[] = array('key' => '优惠券金额','value' =>(int)$bonusMoney."元",);
        if($cash_card_balance > 0) $ret2[] = array('key' => '贵宾卡金额','value' =>(int)$cash_card_balance."元",);
        $redis_params = array();
        $redis_params['collection_fee'] = $ret;
        $redis_params['settle_fee'] = $ret2;
        $redis_params['income'] = (int)$income."元";
        $redis_params['cast'] = ceil($cast);
        $redis_params['account_pay_money'] = $pay_money;//放入r里面便于随时调用fee接口的计算
        $redis_params['customer_phone'] = $customer_phone;
        $cache_key = 'ONLINE_PAY'.$order_id;
        Yii::app()->cache->set($cache_key, $redis_params, 86400);//将信息存放redis一个小时
        $order = Order::model()->getOrdersById($order_id);
        $order_numer = isset($order['order_number']) ? $order['order_number'] : 0;//客户端需要长的order_number
        EdjLog::info('---pay online push client params------ '.$customer_phone.'-order_ID-'.$order_id.'redis_params--'.json_encode($redis_params).'--order_number:'.$order_numer.':---content:---'.$push_message_format);
        if(empty($order_numer) || !is_numeric($order_numer)){
            $order_numer = $order_id;//如果长的orderNumber为空或不是纯数字则传短的orderId
        }
        EdjLog::info("---PUSH-Client11--forceCash: $force_cash :==isVIP: $isVip : ==cast: $cast :=== ");
        if(!$force_cash){
            //不是仅收现金的时候 或者 非vip 或者是vip单不需要在线支付 才会给客户端发push
            if(!$isVip || ($isVip && $cast == 0)){
                $ret = ClientPush::model()->pushMsgForOnlinePayment($customer_phone,$push_message_format,$income,$order_numer,AppleMsgFactory::TYPE_MSG_ONLINEPAY);
                EdjLog::info("---PUSH-Client33--forceCash: $force_cash :==isVIP: $isVip : ==cast: $cast :===ret: $ret ==");
            }
        }

        $ret = array(
            'code' => 0,
            'message' => '成功');
        echo json_encode($ret);
        return;
}else{
    $ret = array(
        'code' => 2,
        'message' => '参数不正确');
    echo json_encode($ret);
    return;
}