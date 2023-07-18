<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 14/12/15
 * Time: 20:14
 */

class VipSendCommand extends LoggerExtCommand {

    private $tradeList = array();

    private $vipCard;

    private $timeStart;

    private $timeEnd;

    private $tradeDate;

    private $debug = true;

    /**
     *  ../yiic vipSend vipRun --dateStart='2014-12-15' --dateEnd='2014-12-16' --vipCard=18601343239 --tradeDate=xxxx
     *
     * @param $dateStart
     * @param $dateEnd
     * @param $vipCard
     * @param $tradeDate
     * @param bool $debug
     */
    public function actionVipRun($dateStart, $dateEnd, $vipCard, $tradeDate, $debug = true){
        $this->timeStart = strtotime($dateStart);
        $this->timeEnd   = strtotime($dateEnd);
        $this->vipCard   = $vipCard;
        $this->debug     = $debug;
        $this->tradeDate = $tradeDate;
        //delete report
        $created = $tradeDate;
        $sqlDelete = 'DELETE FROM t_vip_trade_log_report WHERE created = :created AND  vipcard =:vipcard';//删除当天的
        $commandDelete = Yii::app()->dbstat->createCommand($sqlDelete);
        $commandDelete->bindParam(':created', $created);
        $commandDelete->bindParam(':vipcard', $vipCard);
        $commandDelete->execute();
        $commandDelete->reset();
        //delete report url
        $sqlDelete = 'DELETE FROM t_vip_trade_log_report_url WHERE created = :created AND  vipcard =:vipcard';//删除当天的
        $commandDelete = Yii::app()->dbstat->createCommand($sqlDelete);
        $commandDelete->bindParam(':created', $created);
        $commandDelete->bindParam(':vipcard', $vipCard);
        $commandDelete->execute();
        $commandDelete->reset();
        $this->start();
    }

    private function start(){
        //1.get vip trade list
        $this->getVipTradeList();
        if(empty($this->getTradeList())){
            EdjLog::info('end ---- find nothing');
        }
        //2.gen vip_trade_log_report
        $this->genTradeLogReport();
        //3.gen vip_trade_log_report_url
        $this->genTradeLogReportUrl();
        //4.send sms
        $this->sendSms();
    }

    private function sendSms(){
        //get report url list
        $created = $this->tradeDate;
        $vipTradeUrl = Yii::app()->dbstat->createCommand()
            ->select('*')
            ->from('t_vip_trade_log_report_url')
            ->where('created = :created AND  vipcard =:vipcard',array(':created'=>$created, ':vipcard' => $this->vipCard))
            ->queryAll();
        //
        $successCount  = 0;
        $failCount     = 0;
        $totalCount    = count($vipTradeUrl);
        foreach($vipTradeUrl as $vipList){
            $status = false;
            $vip = Vip::model()->find('id = :id',array(':id'=>$vipList['vipcard']));
            if($vipList['order_count'] == 1 || $vip->send_type == Vip::SEND_TYPE_SMS){
                $vipTrade = Yii::app()->dbstat->createCommand()
                    ->select('*')
                    ->from('t_vip_trade_log_report')
                    ->where('created = :created and vipcard = :vipcard',array(':created'=>$created,':vipcard'=>$vipList['vipcard']))
                    ->queryAll();
                $totalCount = $totalCount + count($vipTrade) - 1;//need add total count
                foreach($vipTrade as $send_list){
                    $message = '尊敬VIP客户，您好，电话'.$send_list['phone'].'，预约时间'.date('Y-m-d H:i', $send_list['booking_time']).'使用代驾，从'.$send_list['location_start'].'到'.$send_list['location_end'].'，'.$send_list['distance'].'公里，等候时间'.$send_list['waiting_time'].'分钟，等候金额'.$send_list['waiting_amount'].'总计金额'.$send_list['amount'].'元,当前余额'.$vipList['balance'];
                    $status = $this->send($vip['phone'], $message);
                }
            }else {
                $dateStart = date('Y年m月d日', $this->timeStart);
                $dateEnd = date('Y年m月d日', $this->timeEnd);
                if($dateStart == $dateEnd){
                    $date  = $dateStart;
                }else{
                    $date  = $dateStart.'到'.$dateEnd;
                }
                $message = 'VIP客户'.$vip->name.'您好!您的账户于'.$date.'使用'.$vipList['order_count'].'次代驾,消费'.-$vipList['consumpte'].'元,余额'.$vipList['balance'].'元.消费详情请点击>> http://wap.edaijia.cn/info.html?'.$vipList['url'];//.' 监督电话:4006913939';
                $status = $this->send($vip['phone'], $message);
            }
            if($status){
                $successCount += 1;
            }else{
                $failCount    += 1;
            }
        }
        $format = 'send end ---- vipcard|%s|start|%s|end|%s|total_count|%s|success_count|%s|fail_count|%s';
        EdjLog::log(sprintf($format, $this->vipCard, date('Y-m-d H:i:s', $this->timeStart), date('Y-m-d H:i:s', $this->timeEnd), $totalCount, $successCount, $failCount));
    }

    private function send($phone, $message){
        if($this->debug){
            $phone = '18610994686';
        }
        $status = Sms::SendSMS($phone, $message);
        EdjLog::info('send sms ---- return is '.$status.'---- phone is '.$phone);
        return $status;
    }

    private function genTradeLogReportUrl(){
        $created = $this->tradeDate;
        $reportList = Yii::app()->dbstat->createCommand()
            ->select('vipcard,sum(amount) as amount,count(1) as count')
            ->from('t_vip_trade_log_report')
            ->where('created = :created',array(':created'=>$created))
            ->group('vipcard')
            ->queryAll();
        if(empty($reportList)){
            EdjLog::info('fail -- find no report list');
        }

        foreach($reportList as $report ){
            $urlAttributes = $this->buildReportUrlAttributes($report);
            $ret = Yii::app()->dbstat->createCommand()->insert('t_vip_trade_log_report_url', $urlAttributes);
            if(!$ret){
                EdjLog::info('fail --- report id --- '.$report['id']);
            }
        }
    }

    private function buildReportUrlAttributes($report){
        $vipCard  = $report['vipcard'];
        $created = $this->tradeDate;
        $longUrl =  $vipCard.$created;
        $shortUrl = $this->shortUrl($longUrl);
        $vip      = Vip::model()->getPrimary($vipCard);
        if(empty($vip)){
            return array();
        }
        $balance  = $vip['balance'];
        $urlAttributes = array();
        $urlAttributes['vipcard']  = $vipCard;
        $urlAttributes['order_count'] = $report['count'];
        $urlAttributes['consumpte']   = $report['amount'];
        $urlAttributes['balance']     = $balance;
        $urlAttributes['created']     = $created;
        $urlAttributes['url']         = $shortUrl;
        return $urlAttributes;
    }


    private function genTradeLogReport(){
        $tradeList = $this->getTradeList();
        foreach($tradeList as $trade ){
            $attributes = $this->buildReportAttributes($trade);
            if(empty($attributes)){
                continue;//TODO ... add log here
            }
            $ret = Yii::app()->dbstat->createCommand()->insert('t_vip_trade_log_report', $attributes);
            if(!$ret){
                EdjLog::info('fail --- trade id --- '.$trade['id']);
            }
        }
    }

    private function buildReportAttributes($vipTrade){
        $orderId = $vipTrade['order_id'];
        $order   = Order::model()->getOrdersById($orderId);
        if(empty($order)){
            return array();// return a default array
        }
        $reportAttributes = array();
        $vipCard = $vipTrade['vipcard'];
        $phone   = $order['phone'];
        $vipPhone = VipPhone::model()->getPrimary($phone);
        $name = empty($vipPhone['name']) ? $order['name'] : $vipPhone['name'];
        $booking_time = $order['booking_time'];
        $location_start = $order['location_start'];
        $location_end = $order['location_end'];
        $distance = $order['distance'];
        $driver_id = $order['driver_id'];
        $waiting_time = OrderExt::getOrderExm($orderId);
        $waiting_amount = floor($waiting_time/30)*20;
        $reportAttributes['vipcard']   = $vipCard;
        $reportAttributes['phone']   = $phone;
        $reportAttributes['booking_time']   = $booking_time;
        $reportAttributes['location_start']   = $location_start;
        $reportAttributes['location_end']   = $location_end;
        $reportAttributes['distance']   = $distance;
        $reportAttributes['driver_id']   = $driver_id;
        $reportAttributes['waiting_time']      = $waiting_time;
        $reportAttributes['waiting_amount']      = $waiting_amount;
        $reportAttributes['type']      = $vipTrade['type'];
        $reportAttributes['amount']      = $vipTrade['amount'];
        $reportAttributes['created']      = $this->tradeDate;
        $reportAttributes['name']      = $name;
        return $reportAttributes;
    }

    private function getVipTradeList(){
        $vipCard   = $this->vipCard;
        $timeStart = $this->timeStart;
        $timeEnd   = $this->timeEnd;
        $params = array(
            'start_time' => $timeStart,
            'end_time'	 => $timeEnd,
        );

        if(!empty($vipCard)){
            $params['vipcard'] = $vipCard;
        }

        $list = VipTrade::model()->getVipTradeList($params, VipTrade::TYPE_ORDER);
        $format = 'vip-trade ---- vipcard|%s|start|%s|end|%s|trade_size|%s';

        $log = sprintf($format, $vipCard, date('Y-m-d H:i:s', $timeStart), date('Y-m-d H:i:s', $timeEnd), count($list));
        EdjLog::info($log);
        $this->tradeList = $list;
    }

    /**
     * @return array
     */
    public function getTradeList()
    {
        return $this->tradeList;
    }

    /**
     * @param array $tradeList
     */
    public function setTradeList($tradeList)
    {
        $this->tradeList = $tradeList;
    }


    /**
     * @return mixed
     */
    public function getVipCard()
    {
        return $this->vipCard;
    }

    /**
     * @param mixed $vipCard
     */
    public function setVipCard($vipCard)
    {
        $this->vipCard = $vipCard;
    }

    /**
     * @return mixed
     */
    public function getTimeStart()
    {
        return $this->timeStart;
    }

    /**
     * @param mixed $timeStart
     */
    public function setTimeStart($timeStart)
    {
        $this->timeStart = $timeStart;
    }

    /**
     * @return mixed
     */
    public function getTimeEnd()
    {
        return $this->timeEnd;
    }

    /**
     * @param mixed $timeEnd
     */
    public function setTimeEnd($timeEnd)
    {
        $this->timeEnd = $timeEnd;
    }

    private function shortUrl( $long_url )
    {
        $key = 'edaijai';
        $base32 = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";

        // 利用md5算法方式生成hash值
        $hex = hash('md5', $long_url.$key);
        $hexLen = strlen($hex);
        $subHexLen = $hexLen / 8;

        $output = array();
        for( $i = 0; $i < $subHexLen; $i++ )
        {
            // 将这32位分成四份，每一份8个字符，将其视作16进制串与0x3fffffff(30位1)与操作
            $subHex = substr($hex, $i*8, 8);
            $idx = 0x3FFFFFFF & (1 * ('0x' . $subHex));

            // 这30位分成6段, 每5个一组，算出其整数值，然后映射到我们准备的62个字符
            $out = '';
            for( $j = 0; $j < 6; $j++ )
            {
                $val = 0x0000003D & $idx;
                $out .= $base32[$val];
                $idx = $idx >> 5;
            }
            $output[$i] = $out;
        }

        return $output[0];
    }


}