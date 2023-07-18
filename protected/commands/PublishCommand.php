<?php
/**
 *
 * 司机处罚
 * 逻辑是
1.由肖波给出两份文件 a:表示全部的司机(作弊的/嫌疑的) b:表示作弊的司机 b 是 a 里 司机的子集
2.团望这边根据 文件 a里面提供的订单,去校验司机应该扣除的款项,进行扣款 然后根据 b 的司机去扣分
1)如果司机工号在 a 不在 b 那么只生成投诉
2)如果司机工号在 b , 那么扣分并且投诉
3.扣钱和扣分之后,发送一条短信给司机
 *
 *
 * User: tuan
 * Date: 15/3/6
 * Time: 14:55
 */
Yii::import('application.models.pay.subsidy.*');
class PublishCommand extends  LoggerExtCommand{

    private $publish_type_list = array(
        EmployeeAccount::TYPE_BONUS_CASH,
        EmployeeAccount::TYPE_BONUS_RETUEN,
    );

    private $smsDate = '';

    private  $format = '司机师傅，在%s期间，发现您有以下订单有作弊行为：（%s）
            公司将收回订单中的优惠券或者补贴%s。如需要申诉，请于收到该短信后的2日内，使用司机客户端中反馈进行申诉。具体使用详情请参见客户端公告《针对节前作弊司机处理公告》。';

    /**
     * 进行处罚
     * ../yiic publish publish --fileName=/data2/tuan/publish/money_income.txt --debug=1 --scoreFileName=/data2/tuan/publish/money_score.txt
     *
     * @param string $fileName
     * @param int $debug 如果为 true, 可以通过查看 log 进行判断
     * @param $scoreFileName
     */
    public function actionPublish($fileName = '', $debug = 1, $scoreFileName, $smsDate){
        $this->smsDate = $smsDate;
        $content 	= file_get_contents($fileName);
        $contentArr = preg_split('/[\r\n]+/', $content);
        $list = array();
        foreach($contentArr as $item){
            $arr = preg_split('/[\s]+/', $item);
            $driverId = isset($arr[0]) ? trim($arr[0]) : 0;
            $orderId  = isset($arr[1]) ? trim($arr[1]) : 0;
            $cast  = isset($arr[2]) ? trim($arr[2]) : 0;
            $tmp = array(
                'driver_id' => $driverId,
                'order_id' => $orderId,
                'cast'     => $cast,
            );
            $list[] = $tmp;
        }
        $scoreList = array();
        $scoreContent = file_get_contents($scoreFileName);
        $scoreContentArr = preg_split('/[\r\n]+/', $scoreContent);
        foreach($scoreContentArr as $item){
            $arr = preg_split('/[\s]+/', $item);
            $driverId = isset($arr[0]) ? trim($arr[0]) : 0;
            $score  = isset($arr[1]) ? trim($arr[1]) : 0;
            $scoreList[$driverId] = $score;
        }
        if(empty($this->smsDate)){
            echo "empty  sms data";
            echo "\n";
            return;
        }
        $this->publish($list, $scoreList,  $debug);
    }

    public function actionBack($fileName = '', $debug = 1, $scoreFileName, $smsDate){
        $this->smsDate = $smsDate;
        $content 	= file_get_contents($fileName);
        $contentArr = preg_split('/[\r\n]+/', $content);
        $list = array();
        foreach($contentArr as $item){
            $arr = preg_split('/[\s]+/', $item);
            $driverId = isset($arr[0]) ? trim($arr[0]) : 0;
            $orderId  = isset($arr[1]) ? trim($arr[1]) : 0;
            $cast  = isset($arr[2]) ? trim($arr[2]) : 0;
            $tmp = array(
                'driver_id' => $driverId,
                'order_id' => $orderId,
                'cast'     => $cast,
            );
            $list[] = $tmp;
        }
        $scoreList = array();
        $scoreContent = file_get_contents($scoreFileName);
        $scoreContentArr = preg_split('/[\r\n]+/', $scoreContent);
        foreach($scoreContentArr as $item){
            $arr = preg_split('/[\s]+/', $item);
            $driverId = isset($arr[0]) ? trim($arr[0]) : 0;
            $score  = isset($arr[1]) ? trim($arr[1]) : 0;
            $scoreList[$driverId] = $score;
        }
        if(empty($this->smsDate)){
            echo "empty  sms data";
            echo "\n";
            return;
        }
        $ret1 = $this->doBackMoney($list, $debug);
        $this->sendBatchSMS($ret1, $scoreList, $debug);
//        $ret2 = $this->doBackScore($scoreList, $debug);
    }

    private function sendBatchSMS($moneyList, $scoreList, $debug){
        $smsFormat = "司机师傅，经证实您以下订单没有作弊（%s），公司已返还该订单的相关费用%s，请注意查看。";
        $scoreSms = '';
        foreach($moneyList as $k => $v){
//            if(isset($scoreList[$k])){
//                $scoreSms = '及代驾分';
//                unset($scoreList[$k]);
//            }
            $sms = sprintf($smsFormat, $v['order_str'], $scoreSms);
            $driver = Driver::getProfile($k);
            $phone = isset($driver['phone']) ? $driver['phone'] : 0 ;
            if($debug){
                echo "$phone".'-----'.$sms;
                continue;
            }else{
                FinanceUtils::sendSMS($phone, $sms);
            }
        }
//        $scoreSms =
        foreach($scoreList as $k => $v){

        }
    }

    private function doBackScore($scoreList, $debug){
        $tmp = array();
        foreach($scoreList as $item){
            $driverId = $item['driver_id'];
            $score = $item['score'];
            if(!$debug){
                // TODO ....
                $this->addScoreBack($driverId, $score, "申诉成功,补款", "");
            }
            $tmp[$driverId] = array(
                'driver_id' => $driverId,
                'score'     => $score,
            );
        }
        return $tmp;
    }

    private function addScoreBack($driverId,$addScore, $reason = '', $complaint){

    }

    private function doBackMoney($list,  $debug){
        $sumArr = array();
        foreach($list as $item){
            $driverId = $item['driver_id'];
            $orderId  = $item['order_id'];
            $cast     = $item['cast'];
            if(isset($sumArr[$driverId])){
                $tmp  = $sumArr[$driverId];
                $orderStr = isset($tmp["order_str"]) ? $tmp["order_str"] : '';
                $orderStr .= ','.$orderId;
                $sum = isset($tmp['sum']) ? $tmp['sum'] : 0;
                $sum += $cast * -1;
                $sumArr[$driverId]['driver_id'] = $driverId;
                $sumArr[$driverId]['order_str'] = $orderStr;
                $sumArr[$driverId]['sum']       = $sum;
            }else{
                $sumArr[$driverId]['driver_id'] = $driverId;
                $sumArr[$driverId]['order_str'] = $orderId;
                $sumArr[$driverId]['sum']       = $cast * -1;
            }
        }
        EdjLog::info(' summary back ---- '.json_encode($sumArr));
        if(!$debug){
            $this->income($sumArr, '申诉成功回款', EmployeeAccount::CHANNEL_FORFEIT_COMPLAINT_OK);
        }
        return $sumArr;
    }



    /**
     * @param $fileName
     * @param int $debug
     */
    public function  actionRepair($fileName, $debug = 1){
        $content 	= file_get_contents($fileName);
        $contentArr = preg_split('/[\r\n]+/', $content);
        $list = array();
        foreach($contentArr as $item){
            $arr = preg_split('/[\s]+/', $item);
            $driverId = isset($arr[0]) ? trim($arr[0]) : 0;
            $cast  = isset($arr[1]) ? trim($arr[1]) : 0;
            $tmp = array(
                'driver_id' => $driverId,
                'sum'     => $cast * -1,
            );
            $list[$driverId] = $tmp;
        }
        if($debug){
            echo json_encode($list);
        }else{
            $this->income($list, '作弊申诉退款');
        }
    }


    private function publish($list = array(), $scoreList, $debug = 1){
        // 回去司机工号 订单号
        $published = $this->buildPublishArray($list);
        //build sum log
        $sum = $this->buildSummary($published);
        //print log
        if($debug){
            $this->printDebugLog($published);
        }
        $this->printSummaryLog($sum);
        if(!$debug){
            //扣款
            $this->income($sum);
            //扣代驾分
            $this->decScore($sum, $scoreList);
            $this->sendSMS($sum, $scoreList);
        }
    }

    private function sendSMS($sumList, $scoreList){
        foreach($sumList as $k => $v){
            $driverId = $k;
            $orderStr = $v['order_str'];
            $score = isset($scoreList[$k]) ? $scoreList[$k] : 0;
            $this->send($driverId, $orderStr, $v['sum'], $score);
        }
    }

    private function send($driverId, $orderStr, $sum, $score){
        $format = $this->format;
        $scoreFormat = '，同时扣除您%s分代驾分';
        $scoreLog = '';
        if($score > 0){
            $scoreLog = sprintf($scoreFormat, $score);
        }
        $sms = sprintf($format, $this->smsDate, $orderStr, $scoreLog);
        $driver = Driver::getProfile($driverId);
        $phone = isset($driver['phone']) ? $driver['phone'] : 0 ;
        if(empty($phone)){
            EdjLog::info('send fail --- phone is empty '.$driverId);
        }else{
            FinanceUtils::sendSMS($phone, $sms);
        }
    }


    private function decScore($sum, $scoreList){
        foreach($sum as $k => $v){
            $reason = '作弊 --- '. $v['order_str'];
            $score = isset($scoreList[$k]) ? $scoreList[$k] : 0;
            $format = $this->format;
            $scoreFormat = '，同时扣除您%s分代驾分';
            $scoreLog = '';
            if($score > 0){
                $scoreLog = sprintf($scoreFormat, $score);
            }
            $sms = sprintf($format, $this->smsDate, $v['order_str'], $scoreLog);
            $this->addScore($k, $score, $reason, $sms); // 扣一分
        }
    }

    private function income($sum ,$comment = '', $channel = 0){
        foreach($sum as $k => $v){
            $driver = Driver::getProfile($k);
            $settle = new CompanySubsidySettlement($k, $driver['city_id']);
            $settle::$EMPLOYEE_ACCOUNT_TYPE = EmployeeAccount::TYPE_FORFEIT;
            if(!empty($channel)){
                $settle->setChannel($channel);
            }else{
                $settle->setChannel(22);
            }
            if($v['sum'] == 0 ){
                continue;
            }
            $settle->setCast($v['sum']);
            $settle->setOrderId(0);
            if(empty($comment)){
                $settle->setComment('作弊处罚 -- '.$v['order_str']);
            }else{
                $settle->setComment($comment.$v['order_str']);
            }
            $settle->settlement();
        }
    }

    private function printSummaryLog($sumList){
        foreach($sumList as $k => $item){
            $log = ' sum  log --- '.$k.'---'.json_encode($item);
            EdjLog::info($log);
        }
    }

    private function printDebugLog($publishedList){
        foreach($publishedList as $item){
            $log = ' debug log --- '.json_encode($item);
            if(!$item['status']){
                $log .= '--- un equal';
            }
            EdjLog::info($log);
        }
    }

    private function buildSummary($published = array()){
        $sum = array();
        foreach($published as $item){
            $driverId = $item['driver_id'];
            $tmp = array(
                'order_str' => '',
                'count'     => 0,
                'sum'       => 0,
            );
            if(isset($sum[$driverId])){
                $tmp = $sum[$driverId];
            }
            $tmp = $this->incDriverInfo($item, $tmp);
            $sum[$driverId] = $tmp;
        }
        return $sum;
    }

    private function incDriverInfo($publishItem, $tmp){
        $order_str = $tmp['order_str'];
        $orderId = $publishItem['order_id'];
        if(empty($order_str)){
            $order_str = $orderId;
        }else{
            $order_str .= ','.$orderId;
        }
        $tmp['count'] += 1;
        $tmp['sum']   += $publishItem['actual_cast'];
        $tmp['order_str'] = $order_str;
        return $tmp;
    }

    private function buildPublishArray($list = array()){
        $tmp = array();
        foreach($list as $item){
            $driverId = $item['driver_id'];
            $orderId  = $item['order_id'];
            $cast     = $item['cast'];

            if(empty($driverId) || empty($orderId)) {
                EdjLog::info('empty driver_id or order_id   ---  '.json_encode($item));
                continue;
            }
            //get publish cast
            $publishCast = $this->getDriverPublishCast($driverId, $orderId);
            $item['actual_cast'] = $publishCast;
            if($publishCast != $cast){
                $item['status'] = 0; // 0表示不相等
            }else{
                $item['status'] = 1; // 0表示不相等

            }
            $tmp[] = $item;
        }
        return $tmp;
    }


    private function getDriverPublishCast($driverId, $orderId){
        $order = Order::model()->getOrdersById($orderId);
        $endTime = isset($order['end_time']) ? $order['end_time'] : 0;
        $date = $endTime;
        if(empty($date) || ($endTime < $order['start_time'])){
            $date = $order['created'];
        }
        $orderDate = date('Ym', $date);
        $sum_now = $this->sumCast($driverId, $orderId, $orderDate);
        $orderDate = date('Ym', strtotime('+1 month', strtotime($orderDate)));
        $sum_second = $this->sumCast($driverId, $orderId, $orderDate);
        return $sum_now + $sum_second;
    }

    private function sumCast($driverId, $orderId, $date){
        $table = 't_employee_account_'.$date;

        $sqlFormat = 'select sum(cast) as cast from %s where order_id = %s and user = "%s" and channel in(7, 8,9, 10, 63)';
        $sql = sprintf($sqlFormat, $table, $orderId, $driverId);

        $account = Yii::app()->db_finance->createCommand($sql)->queryRow();

        if($account){
            return  isset($account['cast']) ? $account['cast']: 0;
        }
        return 0;
    }

    /**
     * @param $driverId
     * @param $addScore
     * @param string $reason 这个会记入扣分明细
     * @param $complaint 这个会展示投诉详情
     */
    public function addScore($driverId,$addScore, $reason = '', $complaint){
        echo json_encode(func_get_args());
        $driver = Driver::getProfile($driverId);
        $cityId = isset($driver['city_id']) ? $driver['city_id'] : 0;
        $complainType = 247; //作弊司机, 会扣分并生成投诉
        if($addScore == 0 ){
            $complainType = 248;//嫌疑司机, 只生成投诉
        }
        $ret = DriverExt::model()->deductScore($driverId,$cityId, $addScore, $reason , $complainType, 0, $complaint);
        echo 'add score -- result'.serialize($ret);
        echo "\n";
    }


}