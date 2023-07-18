<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 2/5/15
 * Time: 15:27
 */

class DayTimeCommand extends LoggerExtCommand{

    public function actionInitRecord($orderId){
        $dayTimeRecord = new DayTimeRecord();
        $dayTimeRecord->order_id = $orderId;
        $dayTimeRecord->created = date('Y-m-d');
        $dayTimeRecord->status = 0;
        $dayTimeRecord->save();
    }


    public function actionChargeFromFile($fileName, $debug = 1 ){
        if (empty($fileName)) {
            return;
        }
        $fileContent = file($fileName);
        foreach ($fileContent as $line => $content) {
            $orderId = trim($content);
            $this->actionCharge($orderId, $debug);
        }
    }

    public function actionChargeFromDb($date, $orderId = 0, $debug = 1){
        if(empty($date)){
            $date = date('Ym', strtotime('-1 day'));
        }
        $list = DayTimeRecord::model()->getChargeList($date, $orderId);
        $now = date('Ym');
        $totalCount = count($list);
        $failCount  = 0;
        $successCount = 0;
        foreach($list as $record){
            $orderId = $record['order_id'];
            $ret = $this->actionCharge($orderId, $debug);
            if($ret){
                $record['status'] = 1;
                $record['settle_date'] = $now;
                $record['meta'] = '金额:'.$ret;
                $successCount += 1;
                if(!$record->save()){
                    EdjLog::info('update status error ---- '.$orderId);
                }
            }else{
                $failCount += 1;
                EdjLog::info('charge driver  error ---- '.$orderId);

            }
        }
        echo 'total|'.$totalCount.'|success|'.$successCount.'|fail|'.$failCount;
        echo "\n";
    }



    public function actionCharge($orderId, $debug = 1){
        $order  = Order::model()->getOrdersById($orderId);
        $format = 'charge driver --- |order_id|%s|driver_id|%s|cast|%s|';
        if($order){
            $driverId = $order['driver_id'];
            $cityId   = $order['city_id'];
            $charged  = $this->getCast($orderId, time(), EmployeeAccount::TYPE_FORFEIT, 66);
            if($charged != 0){
                EdjLog::info('charges --- '.$orderId);
                return true;
            }
            $cast      = $this->getCast($orderId, $order['created'], EmployeeAccount::TYPE_INFOMATION, EmployeeAccount::CHANNEL_DRIVER_DAYTIME_EXTRA_SUBSIDY);
            $companySettle = new DayTimeSubsidy($driverId, $cityId);
            $companySettle->setCast($cast);
            $companySettle->setChannel(EmployeeAccount::CHANNEL_DRIVER_DAYTIME_FORFEIT);
            $companySettle->setComment('日间业务作弊罚款: 订单号:'.$orderId);
            $companySettle->setOrderId($orderId);
            if(!$debug){
                if($companySettle->settlement()){
                    //add log
                    return $cast;
                }else{
                    EdjLog::info('fail --- '.sprintf($format, $orderId,$driverId, $cast));
                }
            }else{
                EdjLog::info('debug --- '.sprintf($format, $orderId,$driverId, $cast));
            }
        }
        return false;
    }


    private function getCast($orderId, $time, $type, $channel){
        //获取上一个月
        $orderMonth = date('Ym', $time);
        $cast_current = $this->getEmployeeAccount($orderMonth, $orderId, $type, $channel);
        //获取下一个月
        $nextMonth  = date('Ym', strtotime('+1 month', $time));
        $cast_next  = $this->getEmployeeAccount($nextMonth, $orderId, $type, $channel);
        return $cast_current + $cast_next;
    }

    private function getEmployeeAccount($tableName, $orderId, $type, $channel){
        $table = 't_employee_account_'.$tableName;

        $sqlFormat = 'select sum(cast) as cast from %s where order_id = %s and type = %s and channel = %s';
        $sql = sprintf($sqlFormat, $table, $orderId, $type, $channel);

        $account = Yii::app()->db_finance->createCommand($sql)->queryRow();

        if($account){
            return  isset($account['cast']) ? $account['cast']: 0;
        }
        return 0;
    }

}
