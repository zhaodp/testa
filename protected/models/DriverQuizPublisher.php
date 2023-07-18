<?php
class DriverQuizPublisher {
    public static function publish($params) {
        EdjLog::info ( 'DriverQuizPublisher publishing, params ' . json_encode ( $params ) );
        if (isset ( $params ['driver_id'] )) {
            $driver_id = $params ['driver_id'];
            $driver = DriverStatus::model ()->get ( $driver_id );
            $driverPosition = DriverPosition::model()->getDriverPosition($driver->id);
            $appVer = $driverPosition->app_ver;
            EdjLog::info ( 'Driver app ver '.$appVer.', params ' . json_encode ( $params ) );
            if(empty($appVer) || $appVer<"2.6.0.0"){
                EdjLog::info ( 'Driver app ver invalid,app ver:'.$appVer.', params ' . json_encode ( $params ) );
                return;
            }
            if ($driver) {
                if ($driver->status != DriverStatus::STATUS_DRIVING) {
                    $unlock = QueueDispatchDriver::model()->isUnLock($driver_id);
                    if($unlock){
                        $des = new DriverExamStudy();
                        $examUrl = $des->getExamUrl($driver_id);
                        if(isset($examUrl ['url'] )){
                            $message = array (
                                'type' => 'driver_quiz',
                                'content' => array (
                                    'code' => '11',
                                    'message' => '请先做题',
                                    'title' => 'e代驾试题',
                                    'url' => $examUrl ['url']
                                )
                            );
                            self::send ( $driver, $message );
                            //推送答题信息。并强制下班 20150415
                            DriverGPS::model()->status($driver_id, DriverStatus::STATUS_OFFDUTY);
                        }else{
                            EdjLog::info ( 'Driver questions url empty, params ' . json_encode ( $params ) );
                        }
                    }else{
                        EdjLog::info ( 'Driver locked, params ' . json_encode ( $params ) );
                    }
                } else {
                    EdjLog::info ( 'Driver running, params ' . json_encode ( $params ) );
                }
            }
        }
    }
    
    /**
     *
     * @param string $driver            
     * @param int $level
     *            --- 推送级别 1(低级别),2（中级）,3(高级)
     * @param int $offline_time
     *            --- 离线时间（默认3600秒）
     * @return boolean
     * @version 2015-04-03 13:48
     */
    public static function send($driver, $message, $level = 1, $offline_time = 3600) {
        $phone = $driver->phone;
        $extPhone = $driver->info["ext_phone"];
        // 获取client_id
        $client_id = $driver->client_id;
        if(!empty($phone)){
            EdjLog::info ( "Sms send driver ok,client_id:".$client_id.",phone:".$phone."." );
            Sms::SendSMS($phone,"您好！e代驾温馨提醒：您有新的培训测验，请登录司机端参加测验，通过测验才能开始工作哦。");
        }else{
            EdjLog::info ( "Sms send driver fail,phone empty,client_id:".$client_id );
        }
        if(!empty($extPhone)){
            EdjLog::info ( "Sms send driver ok,client_id:".$client_id.",extPhone:".$extPhone."." );
            Sms::SendSMS($extPhone,"您好！e代驾温馨提醒：您有新的培训测验，请登录司机端参加测验，通过测验才能开始工作哦。");
        }else{
            EdjLog::info ( "Sms send driver fail,extPhone empty,client_id:".$client_id );
        }
        // 推送
        EPush::set_message_type($message, "driver_quiz");
        $result = EPush::model ( 'driver' )->send ( $client_id, $message, $level, $offline_time );
        if ($result ['result'] == 'ok') {
            EdjLog::info ( 'Send driver '.$client_id.' ok, params ' . json_encode($message) );
            return true;
        } else {
            EdjLog::info ( 'Send driver '.$client_id.' fail, params ' .json_encode( $message) );
            return false;
        }
    }
    public static function addQueue($params) {
        EdjLog::info ( 'DriverQuizPublisher - add task to queue, params are ' . json_encode ( $params ) );
        $task = array (
                        'method' => 'publishDriverQuiz',
                        'params' => $params 
        );
        Queue::model ()->putin ( $task, 'driver_quiz_publisher' );
    }
}
?>