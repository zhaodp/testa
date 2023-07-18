<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 15/4/29
 * Time: 17:29
 */

class DriverAccountService {

    public static function orderCharge($cast, $order, $comment, $channel = 0){
        $orderId = $order['order_id'];
        if(empty($comment)){
            $comment = $orderId;
        }
        $defaultType = EmployeeAccount::TYPE_ORDER_VIP;
        $defaultChannel = EmployeeAccount::TYPE_ORDER_VIP;
        $isVip = CustomerMain::model()->isVip($order['phone']);
        if(!$isVip){
            $defaultType = EmployeeAccount::TYPE_ORDER_NORMAL;
            $defaultChannel = EmployeeAccount::TYPE_ORDER_NORMAL;
        }
        $type = $defaultType;
        if(empty($channel)){
            $channel = $defaultChannel;
        }
        // 订单划账给司机账户
        $params = array(
            'type' => $type,
            'channel' => $channel,
            'city_id' => $order['city_id'],
            'user' => $order['driver_id'],
            'order_id' => $order['order_id'],
            'comment' => $comment,
            'cast' => $cast,
            'order_date' => $order['created']
        );
        return self::orderChargeV2($params);
    }

    public static function orderChargeV2($employeeAccountAttributes){
        return DriverAccount::model()->chargeDriver($employeeAccountAttributes);
    }

    public static function getSettledFee($order){
        return DriverAccount::model()->getOrderfee($order);
    }

    /**
     * 返回司机的余额
     *
     * @param $driverId
     * @return mixed
     */
    public static function getDriverBalance($driverId){
        return DriverAccount::model()->getDriverBalance($driverId);
    }

    /**
     * @param $driverId
     */
    public static function reloadRedisDriverBalance($driverId){
        return DriverAccount::model()->ReloadDriverAmountRedis($driverId);
    }

    public static function getLastEmployeeAccountId($orderId){
        return DriverAccount::model()->getLastEmployeeAccountId($orderId);
    }

    public static function getDriverAmount($driverId){
        return DriverAccount::model()->getDriverAmount($driverId);
    }

}