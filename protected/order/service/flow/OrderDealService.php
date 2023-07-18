<?php
/**
 * 处理司机订单交易信息
 * @author zhongfuhai
 *
 */
class OrderDealService
{
    private static $instance;
    
    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new OrderDealService();
        }
        return self::$instance;
    }
    
    function __get($name) {
        return $this->$name;
    }
    
    function __set($name, $value){
        $this->$name = $value;
    }
    
    /**
     * 司机端获取订单优惠信息
     * @param array $params
     * @return json $ret
     * @author zhongfuhai
     * @version 2015-05-05
     */
    public function getDealInfo($params){
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
            return $ret;
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
        
                return $ret;
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
            $favorable = $this->getOrderFavorable($order['phone'] , $order['booking_time'] , $order['source'] , $order['order_id']);
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
            return $ret;
        } else { //token失效
        
            //增加log 2014-03-20
            EdjLog::warning("phone:".$phone."|order_number:".$order_number."|order_id:".$order_id."|拉取优惠信息|token失效" , 'api' );
        
            $ret = array('code' => 1 , 'message' => '请重新登录');
            return $ret;
        }
    }
    
    /**
     * 获取该订单的优惠信息
     * @param $phone
     * @param $booking_time
     * @param int $source
     * @param int $order_id
     * @return array
     * 参数code  0.普通用户  1.VIP用户   2.优惠劵用户  4.既有优惠劵，个人帐户里面又有钱   8.个人帐户有钱
     * @auther zhongfuhai
     * @version 2015-05-05
     */
    public function getOrderFavorable($phone, $booking_time, $source = 1, $order_id = 0)
    {
        $favorable = array('code' => 0, 'card' => '', 'name' => '', 'money' => 0, 'user_money' => 0); //普通用户
    
        //增加手机号的验证,add by sunhongjing 2013-09-07
        $phone = trim($phone);
        if (empty($phone)) {
            return $favorable;
        }
    
        if (in_array($source, Order::$washcar_sources)) {
            return $favorable;
        }
    
        $vipPhone = VipService::service()->getVipInfo($phone);
        if ($vipPhone) {
            $favorable['code'] = 1; //VIP用户
            $favorable['card'] = $vipPhone['vipid'];
            $favorable['name'] = $vipPhone['name'];
            if(isset($vipPhone['amount']) && $vipPhone['amount'] > 0){
                $favorable['money'] = $vipPhone['amount'];
            }else{
                $favorable['money'] = 0;
            }
        } else {
            $bonus = BonusService::service()->getOrderBonus($phone, $order_id);
            if ($bonus) {
                $status = true;
                if (!empty($source) && $source == Order::SOURCE_CLIENT) {
                    //app呼叫  有呼入记录 返回true 否则false
                    $status = CallHistory::model()->getIncomeCallHistory($phone, $booking_time);
                }
    
                if ($status) {
                    $favorable['code'] = 2; //优惠劵用户
    
                    if ($bonus['count'] == 1) {
                        $card = $bonus['bonus_sn'];
                    } else {
                        $card = '有' . $bonus['count'] . '张优惠劵，共优惠' . $bonus['balance'] . '元';
                    }
    
                    $favorable['card'] = $card;
                    $favorable['name'] = '';
                    $favorable['money'] = $bonus['balance'];
                }
            }
    
            //个人帐户金额
            $user_info = NormalAccountService::getUserAmount($phone);
    
            if ($user_info['amount'] > 0) {
                $favorable['user_money'] = $user_info['amount'];
                if ($favorable['code'] == 2) {
                    $favorable['code'] = 4; //既有优惠劵，个人帐户里面又有钱
                } else {
                    $favorable['code'] = 8; //个人帐户里面有钱
                }
            }
        }
        return $favorable;
    }
    
    
}