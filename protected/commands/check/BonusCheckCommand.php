<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 1/30/15
 * Time: 15:38
 */

class BonusCheckCommand extends LoggerExtCommand{

    public function actionRun($bonusSn){
        //get bonus_sn
        $list = $this->getSnList($bonusSn);
        foreach($list as $item){
            $phone = $item['customer_phone'];
            $created = $item['created'];
            $order = $this->getOrder($phone, $created);
            if($order){
                $orderId = $order['order_id'];
                $l     = $this->getSnList(0, $orderId);
                if(!$l){
                    EdjLog::info("get errrrrr".$order['source']."--".$order['channel']."--".$order['order_id']."--".$order['status']);
                }else{
                    EdjLog::info('use other --- '.$orderId);
                }
            }else{
                EdjLog::info("success");
            }
        }
    }

    private function getSnList($bonusSn = 0, $orderId = 0 ){
        $criteria = new CDbCriteria();

        if(!empty($orderId)){
            $criteria->addCondition('bonus_sn != :bonus');
            $criteria[':bonus']  = '9356294566';
            $criteria->compare("order_id", $orderId);
        }else{
            $criteria->compare('bonus_sn', $bonusSn);
            $criteria->addCondition("order_id = 0");
        }
        $criteria->select = "customer_phone, created";
        return CustomerBonus::model()->findAll($criteria);
    }

    private function getOrder($phone, $created ){
        $criteria = new CdbCriteria();
        $startTime = strtotime("2015-01-30 00:00:00");
        $endTime   = time();
        $criteria->addBetweenCondition('start_time', $startTime, $endTime);
        $criteria->addBetweenCondition('end_time', $startTime, $endTime);
        $criteria->compare("phone", $phone);
        $criteria->addCondition('created > :startTime');
        $criteria->params[':startTime'] = $created;
        $criteria->select = 'order_id,source, channel, status, income, price';

        return Order::model()->find($criteria);
    }

    public function actionOrder($fileName){
        $content 	= file_get_contents($fileName);
        $contentArr = preg_split('/[\r\n]+/', $content);
        $format = "%s --- %s -- %s";
        foreach($contentArr as $item){
            $arr = preg_split('/[:]+/', $item);
            $channel = $arr[0];
            if(0 == $channel){
                continue;
            }
            $orderId = $arr[1];
            $order = Order::model()->getOrderInfoByReadDb($orderId);
            if($order){
                $phone = $orderId['phone'];
                $device = $this->getDevice($phone);
                if($device){
                    EdjLog::info(sprintf($format, $phone, $orderId, $device['device_type']));
                }
            }
        }
    }

    private function getDevice($phone){
        $criteria = new CdbCriteria();
        $criteria->compare('phone', $phone);
        $criteria->compare('login_status', 1);
        $criteria->select = 'device_type';
        return CustomerToken::model()->find($criteria);
    }

}