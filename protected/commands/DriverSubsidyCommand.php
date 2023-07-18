<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 15/4/15
 * Time: 15:59
 */

Yii::import('application.models.pay.subsidy.*');
class DriverSubsidyCommand extends LoggerExtCommand{

    public function actionSubsidy($driverId, $cityId, $cast, $orderId){

        $channel = 72;
        $this->income($driverId, $cityId, $cast, '调用未成单补贴', $channel, $orderId );
    }

    public function actionShenzhen($driverId, $orderId, $cast = 0, $debug = 1){
        $driver = Driver::model()->getDriver($driverId);
        $channel = 73;
        $comment = '深圳首单%s元的补贴 订单号:'.$orderId;
        $smsFormat = '尊敬的司机师傅，昨晚20点到23点首单39元的补贴已充入您的信息费中，请注意查收！';
        $logFormat = '|driverId|%s|orderId|%s|ext_phone|%s|ret|%s|';
        if($driver){
            $extPhone = isset($driver['ext_phone']) ? $driver['ext_phone'] : '';
            $cityId   = isset($driver['city_id']) ? $driver['city_id'] : 1;
            $ret = false;
            if(!$debug){
                $ret = $this->income($driverId, $cityId, $cast, $comment, $channel, $orderId);
                if($ret){
                    if(!empty($extPhone)){
                        $sms = sprintf($smsFormat, $cast);
                        FinanceUtils::sendSMS($extPhone, $sms);
                    }
                }
            }
            echo sprintf($logFormat, $driverId, $orderId, $extPhone, $ret);
            echo "\n";
        }
    }

    private function income($driverId, $cityId, $cast, $comment, $channel, $orderId){
        if(empty($driverId) || empty($cityId) || empty($comment)) {
            return array(
                'code' => 2,
                'message' => 'arguments error',
            );
        }
        if(empty($channel)){
            $channel = EmployeeAccount::TYPE_INFOMATION;
        }
        $balance = DriverBalance::model()->getBalance($cityId, $driverId);
        if($balance < $cast){
            return array(
                'code' => 2,
                'message' => 'driver have no enough money',
            );
        }
        $order = array(
            'order_id' => $orderId,
            'city_id'  => $cityId,
            'created'  => time(),
            'driver_id' => $driverId,
        );
        $settle = New CompanySubsidySettlement($driverId, $cityId);
        $settle->setChannel($channel);
        $settle->setComment($comment);
        $settle->setOrderId($orderId);
        $settle->setCast($cast);
        $ret = $settle->settlement();
        if(!$ret){
            FinanceUtils::sendFinanceAlarm('Finance Wrapper error', json_encode(func_get_args()));
        }
        return $ret;
    }
}