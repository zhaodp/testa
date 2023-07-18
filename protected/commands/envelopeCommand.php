<?php
Yii::import("application.models.envelope.*");

class envelopeCommand extends LoggerExtCommand
{

    /**
     * 司机在线时长
     * php yiic.php envelope DriveHotTimeList
     */
    public function actionDriveHotTimeList($start, $end, $on_line)
    {
        echo serialize(DriverOnlineLog::model()->getDriverOnlineList($start, $end, $on_line, array()));
    }

    /**
     * 司机红包列表
     * php yiic.php envelope update
     */
    public function actionList($drive_id)
    {
        echo serialize(EnvelopeExtend::model()->driveEnvelopeList($drive_id));
    }


    /**
     * 2次推送
     * php yiic.php envelope SecondList
     */
    public function actionSecondList($status,$offset,$limit)
    {
        echo serialize(EnvelopeExtend::model()->getPushSecondList($status,$offset,$limit));
    }

    /**
     * 司机更新红包状态
     * php yiic.php envelope update
     */
    public function actionUpdate()
    {
        echo serialize(EnvelopeExtend::model()->updateEnvelopeStatus(52,1));
    }

    /**
     * 待同步红包列表
     * php yiic.php envelope EnvelopeList
     */
    public function actionEnvelopeList()
    {
        echo serialize(EnvelopeExtend::model()->getPushList(0));
    }

    /**
     * 司机报单符合规则的发送红包
     * php yiic.php envelope DriveOrderList --order_id=1 --driver_id=BJ0703 --day=1421732975
     */
    public function actionDriveOrderList($order_id,$driver_id,$day)
    {
        $arr=array();
        $arr['order_id']=$order_id;
        $arr['driver_id']=$driver_id;
        $arr['day']=$day;
        QueueProcess::model()->envelopeOrder($arr);
//        try{
//            $envelope = EnvelopeInfo::model()->getEvenlopeList();
//            $extend = new EnvelopeExtend();
//            $map = new EnvelopeMap();
//            foreach ($envelope as $lope) {
//                $date_start=$lope['start_date'];
//                $date_end=$lope['end_date'];
//                $arr = array('dictname' => 'envelope_type', 'code' => $lope['envelope_type']);
//                EdjLog::info('红包id:' . $lope['id'] . ';红包类型:' . $lope['envelope_type']);
//                $num = Dict::getEnvelopeTypeNub($arr);
//                $allNum = 0;
//                $syncNum = 0;
//                if ($num > 0) {
//                    $end_date=date('Y-m-d H:m:s');
//                    if($date_end>$end_date){
//                        $arr_drive = $extend->getDriveList($lope['id'],date('Y-m-d'),date('Y-m-d').' 23:59:59');
//                    }else{
//
//                    }
//                    $arr_drive = $extend->getDriveList($lope['id'],$date_start,$date_end);
//                    $city_arr = $map->getCityListByEnvelopeId($lope['id']);
//                    foreach($city_arr as $city){
//                        $drive = EnvelopeAcount::model()->getOrderList($num, $arr_drive, $city,$date_start,$date_end);
//                        EdjLog::info('红包id:' . $lope['id'] . '符合条件司机记录:' . count($drive));
//                        $dr = array();
//                        $allNum = count($drive);
//                        foreach ($drive as $drive_info) {
//                            $dr['drive_id'] = $drive_info['driver_id'];
//                            $dr['city_id'] = $drive_info['city_id'];
//                            $dr['envelope_id'] = $lope['id'];
//                            $dr['envelope_type']=$lope['envelope_type'];
//                            $dr['amount'] = EnvelopeInfo::getEvenlopeNum($lope['envelope_role']);
//                            $dr['create_date'] = date('Y-m-d H:m:s');
//                            $dr['last_changed_date'] = date('Y-m-d H:m:s');
//                            $dr['order_id']=$drive_info['order_id'];
//                            if ($extend->envelopeInsert($dr)) {
//                                $syncNum++;
//                            } else {
//                                EdjLog::info('driver_id:' . $drive_info['driver_id'] . ';envelope_id:' . $lope['id'] . ' sync error!');
//                            }
//                        }
//                    }
//                } else {
//
//                    $city_arr = $map->getCityListByEnvelopeId($lope['id']);
//                    $drive = EnvelopeAcount::model()->getOrderList($num, array(), $city_arr);
//                    EdjLog::info('红包id:' . $lope['id'] . '符合条件司机记录:' . count($drive));
//                    $dr = array();
//                    $allNum = count($drive);
//                    $dr_num = 0;
//                    foreach ($drive as $drive_info) {
//                        $dr['drive_id'] = $drive_info['driver_id'];
//                        $dr['city_id'] = $drive_info['city_id'];
//                        $dr['envelope_id'] = $lope['id'];
//                        $dr['envelope_type']=$lope['envelope_type'];
//                        $dr['amount'] = EnvelopeInfo::getEvenlopeNum($lope['envelope_role']);
//                        $dr['create_date'] = date('Y-m-d H:m:s');
//                        $dr['last_changed_date'] = date('Y-m-d H:m:s');
//                        $dr['order_id']=$drive_info['order_id'];
//                        $dr_num = EnvelopeExtend::model()->getDriveEnvelopeNum($dr['drive_id'], $lope['id']);
//                        if ($dr_num > -1 && $dr_num < $drive_info['acount']) {
//                            for ($i = 0; $i < intval($drive_info['acount']) - $dr_num; $i++) {
//                                if ($extend->envelopeInsert($dr)) {
//                                    $syncNum++;
//                                } else {
//                                    EdjLog::info('driver_id:' . $drive_info['driver_id'] . ';envelope_id:' . $lope['id'] . ' sync error!');
//                                }
//                            }
//                        }
//                    }
//                }
//                EdjLog::info('envelope_id:' . $lope['id'] . ' sync 共' . $allNum . '条记录;同步成功' . $syncNum . '条记录!');
//            }
//        }catch (Exception $e){
//            EdjLog::info($e->getMessage());
//        }
    }


    /**
     * 司机高峰在线时长红包
     * php yiic.php envelope DriveHostTimeList
     */
    public function actionDriveHostTimeList()
    {
//
//        try{
//            $envelope = EnvelopeInfo::model()->getEvenlopeHoteTimeList();
//            $extend = new EnvelopeExtend();
//            $map = new EnvelopeMap();
//            $date = date('Y-m-d 8:00:00', strtotime("-1 day"));
//            $today = date('Y-m-d 8:00:00');
//            foreach ($envelope as $lope) {
//                $arr = array('dictname' => 'envelope_time_type', 'code' => $lope['envelope_type']);
//                EdjLog::info('红包id:' . $lope['id'] . ';红包类型:' . $lope['envelope_type']);
//                //在线时长
//                $num = Dict::getEnvelopeTypeNub($arr);
//                $allNum = 0;
//                $syncNum = 0;
//                if ($num >= 0) {
//                    $num = $num * 60;
//                    $arr_drive = $extend->getDriveList($lope['id']);
//                    $drive = DriverOnlineLog::model()->getDriverOnlineList($date, $today, $num, $arr_drive);
//                    EdjLog::info('红包id:' . $lope['id'] . '符合条件司机记录:' . count($drive));
//                    $dr = array();
//                    $city_arr = $map->getCityListByEnvelopeId($lope['id']);
//                    $allNum = count($drive);
//                    foreach ($drive as $drive_info) {
//                        $city_id = Driver::model()->getDriveCityById($drive_info['driver_id']);
//                        if (in_array($city_id, $city_arr)) {
//                            $dr['drive_id'] = $drive_info['driver_id'];
//                            $dr['city_id'] = $city_id;
//                            $dr['envelope_id'] = $lope['id'];
//                            $dr['envelope_type']=$lope['envelope_type'];
//                            $dr['amount'] = EnvelopeInfo::getEvenlopeNum($lope['envelope_role']);
//                            $dr['create_date'] = date('Y-m-d H:m:s');
//                            $dr['last_changed_date'] = date('Y-m-d H:m:s');
//                            $dr['order_id'] = 0;
//                            if ($extend->envelopeInsert($dr)) {
//                                $syncNum++;
//                            } else {
//                                EdjLog::info('driver_id:' . $drive_info['driver_id'] . ';envelope_id:' . $lope['id'] . ' sync error!');
//                            }
//                        }
//                    }
//                } else {
//                    EdjLog::info('actionDriveOrderList sync $num=0;');
//                }
//                EdjLog::info('envelope_id:' . $lope['id'] . ' sync 共' . $allNum . '条记录;同步成功' . $syncNum . '条记录!');
//            }
//        }catch (Exception $e){
//            EdjLog::info($e->getMessage());
//        }
    }

    //php yiic.php envelope Num
    public  function  actionNum(){
       echo EnvelopeExtend::model()->getDriveEnvelopeNum('BJ9058', 24);
    }


    /**
     * test
     * php yiic.php envelope Test
     */
    public function actionTest()
    {
        $arr=array();
        $arr['order_id']=4;
        $arr['driver_id']='BJ0703';
        $this->envelopeOrder($arr);
    }
    public function envelopeOrder($params){
        $orderId = isset($params['order_id']) ? $params['order_id'] : 0;
        $driverId =isset($params['driver_id']) ? $params['driver_id'] : '';
        $day=isset($params['day']) ? $params['day'] : date('Ymd');
        if(!empty($orderId) && !empty($driverId)){
            $city_id=Driver::model()->getDriveCityById($driverId);
            $res = EnvelopeAcount::model()->saveInfo($orderId,$driverId,$day,$city_id);
            if(!$res){
                EdjLog::error("add e money error order_id $orderId, driver_id: $driverId ");
            }
        }
    }


    /**
     * 司机更新红包领取状态
     * php yiic.php envelope UpdateReceive
     */
    public function actionUpdateReceive($id,$driver_id)
    {
        echo serialize(EnvelopeExtend::model()->updateEnvelopeReceiveStatus($id,$driver_id,1));
    }



    /**
     * 司机红包队列
     * php yiic.php envelope InsertOrderLog
     */
    public function actionInsertOrderLog($order_id,$driver_id,$source,$start_time)
    {
        $result=FinanceUtils::orderLog($order_id,$driver_id,$source,$start_time);
        echo serialize($result);
    }


    /**
     * 司机红包队列
     * php yiic.php envelope InsertEnvelope
     */
    public function actionInsertEnvelope()
    {
        $res = EnvelopeAcount::model()->saveInfo(1,11,20150104,0);
        echo $res;
    }
}


