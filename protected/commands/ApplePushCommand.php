<?php
class ApplePushCommand extends LoggerExtCommand {
    
    /**
     * 基于数据库的苹果推送.
     * 
     * @param string $isTest
     */
    public function actionNewDBAppleConsumer($isTest = false) {
        echo 'start to push message to apple...' . PHP_EOL;
        // set log
        ini_set ( 'error_reporting', E_ERROR );
        ini_set ( 'display_errors', 'Off' );
        // 启动一个进程，随机执行5-10分钟。防止长时间不完。fix bug
        $timestamp = time ();
        $quit_time = rand ( 5, 10 ) * 59;
        $i = 1;
        
        $applePush = new ApplePushEx ();
        // $applePush->connect($isTest);
        $before_yesterday = date ( "Y-m-d H:i:s", strtotime ( "-2 day" ) );
        
        while ( true ) {
            if ($i > 100 || (time () - $timestamp > $quit_time)) {
                echo "-----Auto push apple messge over max times {$i} or over define process time: runed {$quit_time}s------\n";
                break;
            } else {
                $max = 0;
                $sql = "SELECT id,phone,token,message_json FROM t_apple_message
                    WHERE status in(0,3) AND create_time>:before_yesterday AND id>:max AND rank>send_num ORDER BY id LIMIT 5000";
                $command = Yii::app ()->db_readonly->createCommand ( $sql );
                $command->bindParam ( ":before_yesterday", $before_yesterday );
                $command->bindParam ( ":max", $max );
                $message_list = $command->queryAll ();
                if ($message_list) {
                    foreach ( $message_list as $message ) {
                        AppleMessage::model ()->updateStatusById ( $message ['id'], AppleMessage::CONSUMED );
                        $max = ($max > $message ['id']) ? $max : $message ['id'];
                        $phone = $message ['phone'];
                        
                        /*
                         * 去掉Redis检查，不然APNS导致的重试就没法实施了——曾坤 2015/4/9
                         * if(DriverStatus::model()->single_get(self::UNCOMMENTED_ORDER_KEY.$message['id'])!=null){
                         * continue;
                         * }
                         */
                        
                        echo 'time' . date ( "Y-m-d H:i:s" ) . 'phone=' . $phone . PHP_EOL;
                        $customer_client = CustomerClient::model ()->getByPhoneAndLast ( $phone );
                        if (empty ( $customer_client )) {
                            $reason = 'phone=' . $phone . ' customer_client can not be found!';
                            echo $reason;
                            AppleMessage::model ()->updateStatusById ( $message ['id'], AppleMessage::FAILED, $reason );
                            continue;
                        }
                        $token = $customer_client ['client_id'];
                        echo 'token=' . $token . PHP_EOL;
                        
                        // fix bug 发送前，判断下是否可以发送
                        $appleMsg = AppleMessage::model ()->getMessageById ( $message ['id'] );
                        // fix bug 防止循环过快，多次读取重复发送，每次发送前，判断下状态
                        if ($appleMsg ['status'] == AppleMessage::SUCCESS_TO_APNS || $appleMsg ['status'] == AppleMessage::SUCCESS_TO_CLIENT) {
                            echo 'update status ok' . PHP_EOL;
                            continue;
                        }
                        
                        if (empty ( $appleMsg ) || $appleMsg ['send_num'] > $appleMsg ['rank']) {
                            $reason = 'phone=' . $phone . ' send_num > rank,can not send';
                            echo $reason . PHP_EOL;
                            AppleMessage::model ()->updateStatusById ( $message ['id'], AppleMessage::FAILED, $reason . $token );
                            continue;
                        }
                        
                        $last_error = $applePush->pushEx ( $token, $message ['message_json'], $message ['id'] );
                        usleep ( 10000 );
                        
                        if ($last_error === false) {
                            echo 'APNS network error, phone = ', $phone, ' token = ', $token, PHP_EOL;
                            AppleMessage::model ()->updateStatusById ( $message ['id'], AppleMessage::FAILED, 'network error' );
                            break;
                        } elseif (is_array ( $last_error ) && isset ( $last_error ['command'], $last_error ['statusCode'], $last_error ['identifier'] )) {
                            
                            echo 'APNS reset connection, identifier = ', $last_error ['identifier'], PHP_EOL;
                            AppleMessage::model ()->updateStatusById ( $last_error ['identifier'], AppleMessage::UNINTELLIGIBLE_MESSAGE, 'unintelligible message, should not send again' );
                            
                            // 发送给APNS的某个notification出了错，从这个notification往后的所有
                            // inotification是没有push成功的,我们修改一下这些message的状态,
                            // 然后退出job，重来 —— 曾坤 2015/4/3
                            foreach ( $message_list as $m ) {
                                if ($m ['id'] > $last_error ['identifier']) {
                                    usleep ( 10000 );
                                    
                                    AppleMessage::model ()->updateStatusById ( $m ['id'], AppleMessage::FAILED, 'notification dropped by APNS, send again' );
                                }
                            }
                            
                            break;
                        }
                        
                        AppleMessage::model ()->updateStatusById ( $message ['id'], AppleMessage::SUCCESS_TO_APNS );
                        // DriverStatus::model()->single_set(self::UNCOMMENTED_ORDER_KEY.$message['id'], 1, 86400);
                    }
                    
                    // 发送完一批之后等待3秒，看看APNS会不会通知错误，这段代码和
                    // 上面循环里面的那段代码出现了重复，但貌似没有好的解决方案，
                    // 最好是把和APNS通信的代码和从数据取notification的代码分开
                    // 和APNS的代码保持和Apple的长连接——曾坤2015/4/8
                    sleep ( 3 );
                    
                    $last_error = $applePush->getLastError ();
                    
                    if ($last_error === false) {
                    } elseif (is_array ( $last_error ) && isset ( $last_error ['command'], $last_error ['statusCode'], $last_error ['identifier'] )) {
                        
                        echo 'APNS reset connection, identifier = ', $last_error ['identifier'], PHP_EOL;
                        AppleMessage::model ()->updateStatusById ( $last_error ['identifier'], AppleMessage::UNINTELLIGIBLE_MESSAGE, 'unintelligible message, should not send again' );
                        
                        foreach ( $message_list as $m ) {
                            if ($m ['id'] > $last_error ['identifier']) {
                                usleep ( 10000 );
                                
                                AppleMessage::model ()->updateStatusById ( $m ['id'], AppleMessage::FAILED, 'notification dropped by APNS, send again' );
                            }
                        }
                    }
                }
                
                sleep ( 2 ); // fix bug, can not read db all the time
                $i ++;
            }
        }
        // $applePush->closeConnections();
        echo 'finish to push message to apple...' . PHP_EOL;
    }
    
    /**
     * 基于对列的苹果推送.
     *
     * @param string $queue_name            
     */
    public function actionNewAppleConsumer($queue_name) {
        if (! isset ( $queue_name )) {
            $queue_name = "apple_order_message";
        }
        
        EdjLog::info('start to push message read form '.$queue_name.' to apple...', 'console');
        
        $tmp_queue_name = $queue_name . '_' . Tools::getUniqId ( 'nomal' );
        
        EdjLog::info('create temp queue with name '.$tmp_queue_name.' for apple message queue '.$queue_name, 'console');
        
        $apple = new ApplePushEx ();
        
        $quit_time = time () + rand ( 20, 30 ) * 59;

        static $MAX_EMPTY_COUNTER = 180;
        $empty_counter = 0;
        
        while ( time () < $quit_time ) {
            $queue_item = Queue::model ()->getit ( $queue_name );
            if (empty ( $queue_item )) {
                // 如果持续从队列里取不到待发送的push，那么我们break出这个循环，
                // 从而给我们一个检查是否APNS已经重置了连接的机会——曾坤 2015/4/17
                if (++$empty_counter == $MAX_EMPTY_COUNTER) {
                    EdjLog::info("could not get msg for ".$MAX_EMPTY_COUNTER." times, break!", 'console');
                    break;
                }
                sleep ( 1 );
                continue;
            }

            if (isset($queue_item['params'])) {
                $msg = $queue_item['params'];
            } else {
                EdjLog::info('Bad message format '.json_encode($queue_item), 'console');
                continue;
            }

            EdjLog::info('Apple Push start '.json_encode($msg), 'console');
            
            $empty_counter = 0;
            
            if (! isset ( $msg ['device_token'], $msg ['content'], $msg ['push_distinct_id'] )) {
                continue;
            }
            
            if ($this->checkDeviceToken ( $msg ['device_token'] ) !== true) {
                EdjLog::info("Bad device token : ".$msg ['device_token'], 'console');
                continue;
            }
            
            if (($ret = $apple->push ( $msg ['device_token'], $msg ['content'], $msg ['push_distinct_id'] )) !== true) {
                Queue::model ()->putin ( $queue_item, $queue_name );

                if (isset ( $ret ['identifier'] )) {
                    EdjLog::info('APNS reset connection, msg id = '.$ret ['identifier'], 'console');
                    $this->restoreApplePushMessage ( $queue_name, $tmp_queue_name, $ret ['identifier'] );
                    sleep(3);
                }
                
                continue;
            }
            EdjLog::info("Apple Push finish with token ".$msg ['device_token']." id ".$msg ['push_distinct_id'], 'console');
            
            usleep ( 10000 );
            
            $this->backupApplePushMessage ( $tmp_queue_name, $queue_item );
        }
        
        // 进行善后操作
        // 发送完一批之后等待3秒，看看APNS会不会通知错误，这段代码和
        // 上面循环里面的那段代码出现了重复，但貌似没有好的解决方案
        // 最好是把和APNS通信的代码和从数据库取notification的代码分开
        // 和APNS的代码保持和Apple的长连接——曾坤2015/4/8
        sleep ( 3 );
        
        $last_error = $apple->getLastError ();
        if (isset ( $last_error ['identifier'] )) {
            EdjLog::info('APNS reset connection, msg id = '.$last_error ['identifier'], 'console');
            $this->restoreApplePushMessage ( $queue_name, $tmp_queue_name, $last_error ['identifier'] );
        }

        RedisHAProxy::model ()->redis->delete ( $tmp_queue_name );
    }
    
    /**
     *
     * @param string $queue_name            
     * @param string $tmp_queue_name            
     * @param string $message_id            
     */
    private function restoreApplePushMessage($queue_name, $tmp_queue_name, $message_id) {
        while ( RedisHAProxy::model ()->redis->lLen ( $tmp_queue_name ) ) {
            $msg = RedisHAProxy::model ()->redis->lPop ( $tmp_queue_name );
            $msg = json_decode ( $msg, true );
            if ($msg['params']['push_distinct_id'] != $message_id) {
                EdjLog::info('restore message id '.$msg['params']['push_distinct_id'], 'console');
                Queue::model ()->putin ( $msg, $queue_name );
            } else {
                break;
            }
        }
    }
    
    // 保留发送给APNS的最后5000条notice，如果APNS收到错误的notice继而断开我们的连接，
    // 那么我们就从这个队列里去找那个错误的notice id后面的notice，把这些notice重新
    // 放回apple_notice_message队列去重新发送——曾坤 2015/4/7
    private function backupApplePushMessage($tmp_queue_name, $msg) {
        EdjLog::info('backup message id:'.$msg['params']['push_distinct_id'], 'console');
        RedisHAProxy::model()->redis->lPush ( $tmp_queue_name, json_encode ( $msg ) );

        while ( RedisHAProxy::model ()->redis->lLen ( $tmp_queue_name ) > 5000 ) {
            RedisHAProxy::model ()->redis->rPop ( $tmp_queue_name );
        }

        // 12个小时的过期时间
        RedisHAProxy::model ()->redis->expire ( $tmp_queue_name, 43200 );
    }
    
    /**
     * @yangzhi 完善Apple Feedback回调, 记录失败的device_token
     */
    public function actionAppleFeedback() {
        $apple = new ApplePushEx ();
        $feedback = $apple->sendFeedbackRequest ();
        $badAppleDeviceToken = RBadAppleDeviceToken::model ();
        
        foreach ( $feedback as $f ) {
            // 保存有问题的token到redis
            if (! isset ( $f )) {
                continue;
            }
            
            $token = $f ['devtoken'];
            
            if (isset ( $token )) {
                EdjLog::info("bad apple device token : $token", 'console');
                // 暂时先不记录feedback回来的token——曾坤 2015/4/17
                //$badAppleDeviceToken->addNewBadDeviceToken ( $token );
            }
        }
    }
    
    // 检查redis里面有没有token
    private function checkDeviceToken($token) {
        $badAppleDeviceToken = RBadAppleDeviceToken::model ();
        return $badAppleDeviceToken->isBadDeviceToken ( $token ) !== true;
    }
}
