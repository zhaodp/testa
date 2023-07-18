 <?php

class pushsCommand extends LoggerExtCommand
{
    const UNCOMMENTED_ORDER_KEY='unCommented_order_';
  /**
     * send message to client(order >= yesterday 7  && order <= today 7)
     * @author aiguoxin 2014-04-08
     */
    public function actionPushUncommentedOrderMsg()
    {
        set_time_limit(0);
        $title = 'send unCommented order message to customer';
        echo Common::jobBegin($title);

        $yesterday = date("Y-m-d 07:00:00",strtotime("-1 day"));
        $today = date("Y-m-d 07:00:00");
        //collecion order data -> add order log
        echo 'start to collection after '.$yesterday.' success order data...'."\r\n";
        $max = 0;
        $pagesize = 500;

        $begin_time = strtotime($yesterday);
        $end_time = strtotime($today);

        $criteria = new CDbCriteria();
        $criteria->select = "order_id,driver_id,phone,call_time";
        $criteria->condition = "order_id>:max and source in(0,2,30,32) and status='1' and call_time between :begin_time and :end_time";
        $criteria->group = 'phone,driver_id';
        $criteria->order = 'order_id asc';
        $criteria->limit = $pagesize;

        while (true) {
            $criteria->params = array(
                                ':max' => $max,
                                ':begin_time' => $begin_time,
                                ':end_time' => $end_time,
                            );
            $orders = Order::model()->findAll($criteria);
            if($orders){
                foreach ($orders as $order) {
                    $max = ( $max > $order['order_id'] ) ? $max : $order['order_id'];
                    $call_time=$order->call_time;
                    $order_id=$order->order_id;
                    $phone = $order->phone;
                    //改用队列
                    $data = array(
                        'call_time' => $call_time,
                        'order_id' => $order_id,
                        'phone' => $phone,
                    );
                    $task=array(
                        'method'=>'collect_order_push_data',
                        'params'=>$data,
                    );
                    echo 'order='.$order_id.'未评价订单收集加入队列'.PHP_EOL;
                    Queue::model()->putin($task,'message');

                }
                sleep(60);//休眠1分钟，要不然队列处理不过来，会报警
            }else{
                break;
            }
        }
        echo 'collection after '.$yesterday.' success order data finish'."\r\n";

        // $this->actionSendUncommentedOrderMsgForAndriod();
        // $this->actionSendUncommentedOrderMsgForApple();        
        echo Common::jobEnd($title);
    }

    /**
    ** send message to apple user by phone
    */
    public function actionSend2Apple($client_id,$isTest=false){
        $applePush = new ApplePush;
        $applePush->getSockConnet($isTest);
        $message="您还有未评价的订单，快去给他们提提建议吧";
        $orderNum = 1;
        $params = array(
            'message' => $message,
            'messageid'=>0,
            'orderId' => 1,
            'badge' => 1,
            'type' => '2',
            'sound' => 'ping1',
            'orderNum' => 1
            );
        $body=AppleMsgFactory::model()->orgPushMsgForUncommentedOrder($params);
        $applePush->push($client_id,$body);
        $applePush->closeConnections();
    }

    /**
    ** send message to apple user by getui for andriod
    */
    public function actionSend2Andriod($client_id){
        $message="您还有未评价的订单，快去给他们提提建议吧";
        $orderNum = 1;
        $params = array(
            'content' => $message,
            'orderId' => 1,
            'orderNum' => 1
            );
        $content = PushMsgFactory::model()->orgPushMsg($params,PushMsgFactory::TYPE_MSG_CUSTOMER);
        $result = EPush::model('customer')->send($client_id,$content,2,12*3600);  
        if ($result['result']=='ok') {
            echo 'send ok';
        }else{
            echo "send fail";
            print_r($result);
        }
    }

    /**
    * push msg added by aiguoxin for andriod
    */
    public function actionSendUncommentedOrderMsgForAndriod(){
        $title = 'send unCommented order message to customer';
        echo Common::jobBegin($title);
        $before_yesterday = date("Y-m-d H:i:s",strtotime("-2 day"));
        $today = date("Y-m-d");
        echo 'start to handle after '.$before_yesterday.' unCommented order data...'."\r\n";
        $min=PHP_INT_MAX;
        $message = "您还有未评价的订单，快去给他们提提建议吧";//推送消息
        $dis_message = '请您对我们的服务进行评价';//消息列表显示信息
        while (true) {
            // echo 'order_date='.$before_yesterday.',comment_status='.$comment_status.',min='.$min."\r\n";
            $sql = "SELECT id,order_id,notice_phone FROM t_order_comment_log 
            WHERE order_date>:order_date AND comment_status = 0 AND notice_status in(0) AND id<:min ORDER BY id DESC LIMIT 5000";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(":order_date", $before_yesterday);
            $command->bindParam(":min",$min);
            $order_id_list = $command->queryAll();
            // echo 'unCommented order  count='.count($order_id_list)."\r\n";

            if ($order_id_list) {
                foreach ($order_id_list as $order) {
                    // echo "start to comment id=".$order['id'].PHP_EOL;
                    OrderCommentLog::model()->updateNoticeStatusConsumed($order['id']);
                    
                    $min = ( $min < $order['id'] ) ? $min : $order['id'];
                    $order_id=$order['order_id'];
                    $id = $order['id'];
                    $phone = $order['notice_phone'];
                    //放入队列
                    $data = array(
                        'today' => $today,
                        'before_yesterday' =>$before_yesterday,
                        'message'=>$message,
                        'dis_message'=>$dis_message,
                        'id'=>$id,
                        'order_id'=>$order_id,
                        'phone'=>$phone,

                    );
                    $task=array(
                        'method'=>'send_order_push',
                        'params'=>$data,
                    );
                    echo 'order='.$order_id.'未评价订单推送加入队列'.PHP_EOL;
                    Queue::model()->putin($task,'message');
                }
                sleep(60);//休眠1分钟，要不然队列处理不过来，会报警

            } else {
                break;
            }
        }
        echo 'handle after '.$before_yesterday.' unCommented order data finish'."\r\n";
    }

     /**
    * push msg added by aiguoxin for apple
    * 作废，apple的未评价推送放到android一块 add by aiguoxin
    */
    public function actionSendUncommentedOrderMsgForApple(){
        $title = 'send unCommented order message to customer';
        echo Common::jobBegin($title);
        $before_yesterday = date("Y-m-d H:i:s",strtotime("-2 day"));
        $before_yesterday_format = date("Ymd",strtotime("-2 day"));
        $today = date("Y-m-d");
        echo 'start to handle after '.$before_yesterday.' unCommented order data...'."\r\n";
        $min=PHP_INT_MAX;
        $reason="";
        $message = "您还有未评价的订单，快去给他们提提建议吧";
        while (true) {
            // echo 'order_date='.$before_yesterday.',comment_status='.$comment_status.',min='.$min."\r\n";
            $sql = "SELECT id,order_id,notice_phone FROM t_order_comment_log 
            WHERE order_date>:order_date AND comment_status = 0 AND notice_status in(0,3) AND id<:min ORDER BY id DESC LIMIT 5000";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(":order_date", $before_yesterday);
            $command->bindParam(":min",$min);
            $order_id_list = $command->queryAll();
            if ($order_id_list) {
                foreach ($order_id_list as $order) {
                    // echo "start to comment id=".$order['id'].PHP_EOL;
                    OrderCommentLog::model()->updateNoticeStatusConsumed($order['id']);
                    $min = ( $min < $order['id'] ) ? $min : $order['id'];
                    //remove the order which commented in send notice period
                    $comment_status = CommentSms::model()->getCommandSmsByOrderId($order['order_id']);
                    if (!empty($comment_status)) {
                        echo "[id=".$order['id'].']has been commentd'.PHP_EOL;
                        $reason = 'order='.$order['order_id'].' has been commented just now!';
                        OrderCommentLog::model()->updateNoticeStatusFail($order['order_id'],$reason);
                        continue 1;
                    }

                    $phone = $order['notice_phone'];
                    if(empty($phone)){
                        echo "[id=".$order['id'].'] phone is not exist'.PHP_EOL;
                        $reason = 'phone='.$phone.' is not exist!';
                        OrderCommentLog::model()->updateNoticeStatusFail($order['order_id'],$reason);
                        continue 1;
                    }
                    if(!OrderCommentLog::model()->canNotice($phone, $today,$order['id'])){
                        echo "[id=".$order['id'].']phone has been sent once'.PHP_EOL;
                        $reason = 'phone='.$phone.' today has benn sent once!';
                        OrderCommentLog::model()->updateNoticeStatusFail($order['order_id'],$reason);
                        continue 1;
                    }

                    //find user client_id
                    $customer_client = CustomerClient::model()->getByPhoneAndLast($phone);
                    if(empty($customer_client)){
                        echo "[id=".$order['id'].'] customer_client can not be found'.PHP_EOL;
                        $reason = 'phone='.$phone.' customer_client can not be found!';
                        OrderCommentLog::model()->updateNoticeStatusFail($order['order_id'],$reason);
                        continue 1;
                    }
                    $client_id = $customer_client['client_id'];

                    //find user uncommented order number
                    $num = OrderCommentLog::model()->getUncommentedOrderNum($phone,$before_yesterday);
                    if($customer_client['type'] == 0){
                    //ios use APNS
                        echo "[id=".$order['id']."]use iphone service\r\n";
                    //see http://wiki.edaijia.cn/dwiki/doku.php?id=push_%E5%8D%8F%E8%AE%AE%E5%AE%9A%E4%B9%89
                        $params = array(
                            'message' => $message,
                            'orderId' => $order['order_id'],
                            'badge' => 1,
                            'type' => '2',
                            'sound' => 'ping1',
                            'orderNum' =>$num
                            );
                            //open apple push
                            $body=AppleMsgFactory::model()->orgPushMsgForUncommentedOrder($params);
                            //save to apple push table
                            AppleMessage::model()->addAppleMessage($phone,$client_id,$body);
                            //fix bug 1703
                            OrderCommentLog::model()->updateNoticeStatusOk($order['order_id'],$phone);

                    }
                }
            } else {
                break;
            }
        }
        echo 'handle after '.$before_yesterday.' unCommented order data finish'."\r\n";
    }

    /**
    *
    *apple messge consumer
    */
    public function actionAppleConsumer($isTest=false){
        echo 'start to push message to apple...'.PHP_EOL;
        //set log 
        ini_set('error_reporting', E_ERROR);
        ini_set('display_errors', 'Off');
        //启动一个进程，随机执行5-10分钟。防止长时间不完。fix bug
        $timestamp=time();
        $quit_time=rand(5,10)*59;
        $i = 1;

        $applePush = new ApplePush;
        $applePush->getSockConnet($isTest);
        $before_yesterday = date("Y-m-d H:i:s",strtotime("-2 day"));

        while (true) {
            if ($i>100 || (time() - $timestamp > $quit_time ) ) {
                echo "-----Auto push apple messge over max times {$i} or over define process time: runed {$quit_time}s------\n";
                break;
            } else{
                $max=0;
                $sql = "SELECT id,phone,token,message_json FROM t_apple_message
                WHERE status in(0,3) AND create_time>:before_yesterday AND id>:max AND rank>send_num LIMIT 5000";
                $command = Yii::app()->db_readonly->createCommand($sql);
                $command->bindParam(":before_yesterday", $before_yesterday);
                $command->bindParam(":max",$max);
                $message_list = $command->queryAll();
                if ($message_list) {
                    foreach ($message_list as $message) {
                        AppleMessage::model()->updateStatusById($message['id'],AppleMessage::CONSUMED);
                        $max = ( $max > $message['id'] ) ? $max : $message['id'];
                        $phone=$message['phone'];
                        //一天之发送一条
                        if(DriverStatus::model()->single_get(self::UNCOMMENTED_ORDER_KEY.$message['id'])!=null){
                            continue;
                        }
                        echo 'time'.date("Y-m-d H:i:s").'phone='.$phone.PHP_EOL;
                        $customer_client = CustomerClient::model()->getByPhoneAndLast($phone);
                        if(empty($customer_client)){
                            $reason = 'phone='.$phone.' customer_client can not be found!';
                            echo $reason;
                            AppleMessage::model()->updateStatusById($message['id'],AppleMessage::FAILED,$reason);
                            continue 1; 
                        }
                        $token = $customer_client['client_id'];
                        echo 'token='.$token.PHP_EOL;                     

                        //fix bug 发送前，判断下是否可以发送
                        $appleMsg = AppleMessage::model()->getMessageById($message['id']);
                        //fix bug 防止循环过快，多次读取重复发送，每次发送前，判断下状态
                        if($appleMsg['status'] == AppleMessage::SUCCESS_TO_APNS 
                            || $appleMsg['status'] == AppleMessage::SUCCESS_TO_CLIENT){
                            echo 'update status ok'.PHP_EOL;
                            continue 1;
                        }

                        if(empty($appleMsg) || $appleMsg['send_num'] > $appleMsg['rank']){
                            $reason = 'phone='.$phone.' send_num > rank,can not send';
                            echo $reason.PHP_EOL;
                            AppleMessage::model()->updateStatusById($message['id'],AppleMessage::FAILED,$reason.$token);
                            continue 1;
                        }

                        $result = $applePush->push($token,$message['message_json']);
                        echo 'result='.$result.PHP_EOL;
                        if($result == '2'){
                            $applePush->getSockConnet();
                            $reason = 'phone='.$phone.' use ApplePush failed!reason=fwrite error';
                            echo $reason.PHP_EOL;
                            AppleMessage::model()->updateStatusById($message['id'],AppleMessage::FAILED,$reason.$token);
                        }elseif ($result == '3') {
                            $applePush->getSockConnet();
                            $reason = 'phone='.$phone.' use ApplePush failed!reason=connect lost';
                            echo $reason.PHP_EOL;
                            AppleMessage::model()->updateStatusById($message['id'],AppleMessage::FAILED,$reason,$token);
                        }elseif ($result == '4') {
                            $applePush->getSockConnet();
                            $reason = "error".PHP_EOL;
                            echo $reason.PHP_EOL;
                            AppleMessage::model()->updateStatusById($message['id'],AppleMessage::FAILED,$reason,$token);
                        }elseif ($result == '1') {
                            //send ok, update send status
                            echo 'update status ok'.PHP_EOL;
                            AppleMessage::model()->updateStatusById($message['id'],AppleMessage::SUCCESS_TO_APNS,'',$token);
                            //添加缓存
                            DriverStatus::model()->single_set(self::UNCOMMENTED_ORDER_KEY.$message['id'],1,24*3600);
                        }
                    }
                }
                sleep(2); //fix bug, can not read db all the time
                $i++;
            }
            
        }
        $applePush->closeConnections();
        echo 'finish to push message to apple...'.PHP_EOL;
    }

    private function applePushOrderMessage()
    {
        $this->applePushMessage('order');
    }

    private function applePushNoticeMessage()
    {
        $this->applePushMessage('notice');
    }

    private function applePushMessage($category)
    {
        $queue_name = $this->getApplePushQueueName($category);
        $apple = new ApplePushEx();

        $quit_time = time() + rand(5, 10) * 59;
        while (time() < $quit_time) {
            $msg = Queue::model()->getit($queue_name);
            if (empty($msg)) {
                break;
            }

            if (!isset($msg['device_token'], $msg['content'], $msg['id'])) {
               continue; 
            }

            if (($ret = $apple->pushEx($msg['device_token'], $msg['content'], $message['id'])) !== true) {
                if (isset($ret['identifier'])) {
                    $this->restoreApplePushMessage($category, $ret['identifier']);
                }
                break;
            } 

            $this->backupApplePushMessage($category, $msg);

            usleep(10000);
        }
    }

    private function restoreApplePushMessage($category, $message_id)
    {
        $backup_queue_name = $this->getApplePushBackupQueueName($category);
        $queue_name = $this->getApplePushQueueName($category);

        while (Queue::model()->redis->lLen($backup_queue_name)) {
            $msg = Queue::model()->redis->lPop($backup_queue_name);
            if ($msg['push_distinct_id'] != $message_id) {
                Queue:model()->putin(json_encode($msg), $queue_name);
            } else {
                break;
            }
        }
    }

    private function getApplePushQueueName($category)
    {
        switch ($category) {
            case 'order':
                return 'apple_order_message';
            case 'notice':
                return 'apple_notice_message';
            default:
                return '';
        }
    }

    private function getApplePushBackupQueueName($category)
    {
        switch ($category) {
            case 'order':
                return 'queue_backup_apple_order_message';
            case 'notice':
                return 'queue_backup_apple_notice_message';
            default:
                return '';
        }
    }

    // 保留发送给APNS的最后500条notice，如果APNS收到错误的notice继而断开我们的连接，
    // 那么我们就从这个队列里去找那个错误的notice id后面的notice，把这些notice重新
    // 放回apple_notice_message队列去重新发送——曾坤 2015/4/7
    private function backupApplePushMessage($category, $msg)
    {
        $backup_queue_size = 500;

        $backup_queue_name = $this->getApplePushBackupQueueName($category);

        // 这里因为要控制队列的长途，所以new了一个Queue，而没有用Queue::model()
        // 而且，putin参数假定队列的名字是不带queue_前缀的，所以用了substr来去掉
        // 前面的queue_——曾坤 2015/4/7
        $queue = new Queue();
        $queue->queue_max_length = array($backup_queue_name => $backup_queue_size);
        $queue->putin(json_encode($msg), substr($backup_queue_name, 6));
    }

    public function actionApplePush($category = 'order')
    {
        switch ($category) {
            case 'order':
                $this->applePushOrderMessage();
                break;
            case 'notice':
                $this->applePushNoticeMessage();
                break;
            default:
                break;
        }
    }

    /**
    * get apple feedback
    */
    public function actionAppleFeedback(){
        $applePush = new ApplePush;
        $result = $applePush->send_feedback_request();
        foreach($result as $val){   
             echo 'handle token='.$val['devtoken'].PHP_EOL;
             AppleMessage::model()->updateStatusByToken($val['devtoken']);
        }
    }


    public function actionTest($customerPhone){
        
        //优惠券    
        CustomerMessage::model()->addCouponMsg($customerPhone,'优惠券测试');

        //发票申请
        //CustomerMessage::model()->addBillMsg($customerPhone);

        //用户反馈
        $messageid=1;
        CustomerMessage::model()->addFeedBackMsg($messageid,'用户反馈测试');


    }

    public function actionAGX(){
        $res = DriverExt::model()->deductScore('BJ9010',1,1,'test');
        print_r($res);
    }

    /**
    *   给定号码、优惠码
    */
    public function actionMessage(){
        $message='e代驾&酒仙网联手送现金活动，恭喜您获得酒仙网20元红包（移动端全场通用）。红包序列号为%s，请登录酒仙网移动端激活使用';
        $phone_array=array();
        $code_array=array();
        //收集手机号
        $handle = @fopen("/opt/phone.txt", "r");
        if ($handle) {
            while (!feof($handle)) {
                $phone = fgets($handle, 4096);
                array_push($phone_array, $phone);
                
            }
            fclose($handle);
        }
        //收集优惠码
        $handle = @fopen("/opt/code.txt", "r");
        if ($handle) {
            while (!feof($handle)) {
                $code = fgets($handle, 4096);
                array_push($code_array, $code);
            }
            fclose($handle);
        }

        for($i=0;$i<count($phone_array);$i++){
            $phone=$phone_array[$i];
            $i_message=sprintf($message, $code_array[$i]);
            $res = Sms::SendSMS($phone, $i_message);
            print_r($res);
            echo 'phone='.$phone.'发送完成'.PHP_EOL;
        }
    }

    /**
    * 恢复短信回评数据到t_comment_sms
    *
    */
    public function actionReplyRecover(){
        $handle = @fopen("/opt/reply.txt", "r");
        $phone='';
        $level=0;
        if ($handle) {
            while (!feof($handle)) {
                $line = fgets($handle, 4096);
                $strarr = explode(",",$line);
                foreach($strarr as $newstr){
                    $strarr = explode("=", $newstr);
                    if(count($strarr) < 2){
                        echo 'line='.$line.'长度小于2过滤'.PHP_EOL;
                        continue;
                    }
                    if($strarr[0] == 'SrcMobile'){
                        $phone=$strarr[1];
                    }else if($strarr[0] == 'Content'){
                        $level=$strarr[1];
                    }
                }
                echo 'phone='.$phone.',lever='.$level.PHP_EOL;
                //获取最近的t_sms_send,最近邀请评价短信
                $smsSendList = Yii::app()->db_readonly->createCommand()
                                    ->select('*')
                                    ->from('{{sms_send}}')
                                    ->where("receiver = :phone and created>'2014-09-18'", array(':phone' => $phone))
                                    ->order('id')->queryAll();
                if(empty($smsSendList)){
                    echo 'phone='.$phone.'没有找到最近评价列表'.PHP_EOL; 
                    continue;
                }
                foreach ($smsSendList as $smsSend) {
                    if(empty($smsSend)){
                        echo 'phone='.$phone.'没有找到最近评价邀请'.PHP_EOL; 
                        continue;
                    }
                    $order_id=$smsSend['order_id'];
                    $sms_type=$smsSend['sms_type'];
                    $created=$smsSend['created'];
                    $driver_id=$smsSend['driver_id'];
                    $order_status=$smsSend['order_status'];
                    $imei=$smsSend['imei'];
                    //判断此订单是否已经评价，已经评价则过滤
                    $commentSMS=CommentSms::model()->getCommandSmsByOrderId($order_id);
                    if($commentSMS){
                        echo 'phone='.$phone.',order_id='.$order_id.'订单已经评价过，过滤'.PHP_EOL; 
                        continue;
                    }
                    //content获取
                    $content='';
                    switch ($level) {
                        case 1:
                            $content='非常不满意';
                            break;
                        case 2:
                            $content='不满意';                     
                            break;           
                        case 3:
                            $content='一般';
                            break;
                        case 4:
                            $content='满意';
                            break;
                        case 5:
                            $content='非常满意';
                            break;            
                        default:
                            # code...
                            break;
                    }
                    $attributes = array(
                                'sender' => $phone,
                                'driver_id' => $driver_id,
                                'imei' => $imei,
                                'level' => $level,
                                'content' =>$content,
                                'raw_content' => $content,
                                'confirm' => 1,
                                'order_status' => $order_status,
                                'created' => $created,
                                'order_id' => $order_id,
                                'sms_type' => $sms_type, //评价
                                'status' => 0); //默认未处理
                    $result=  Yii::app()->db->createCommand()->insert('t_comment_sms', $attributes);
                    print_r($attributes);
                    echo 'phone='.$phone.',order_id='.$order_id.'恢复成功'.PHP_EOL; 
                }
            }
            fclose($handle);
        }
    }

    /**
    *   司机调查问卷
    *
    */
    public function actionDriverInvest(){

        $msg='【e代驾调查问卷】师傅您好，公司希望收集下司机师傅是否有意愿使用苹果版司机端。苹果版司机端的主要优点是操作顺畅、系统稳定、定位精准、里程计算准确。请师傅们反馈如下：有较强烈使用意愿请回复“1”；无所谓或不确定请回复“2”；不想使用请回复“3”。';
        $max=0;
        while (true) {
            $sql = "SELECT id,user,city_id,phone,ext_phone FROM t_driver WHERE id>:max and mark != 3 LIMIT 1000";
            //test
            // $sql = "SELECT id,user,city_id,phone,ext_phone FROM t_driver WHERE user in('BJ9010','BJ9017','BJ9036','BJ9035','BJ9005')";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(":max",$max);
            $driver_list = $command->queryAll();
            if ($driver_list) {
                foreach ($driver_list as $driver) {
                    $flag = false;
                    $max = $driver['id'];
                    $driver_id = $driver['user'];
                    
                    $i_phone = ($driver['ext_phone']) ? $driver['ext_phone'] : $driver['phone'];
                    $i_phone='18910254159';
                    //使用缓存，每个号码发送一次
                    if(DriverStatus::model()->single_get('invest_driver_'.$i_phone)  != null){
                        echo 'phone='.$i_phone.',driver='.$driver_id.',has sent once,continue'.PHP_EOL; 
                        continue;
                    }
                    $res = Sms::SendForOrderComment($i_phone, $msg, 123);
                    if($res){
                        DriverStatus::model()->single_set('invest_driver_'.$i_phone,1,24*3600*7);
                        echo 'phone='.$i_phone.',driver='.$driver_id.',send ok'.PHP_EOL; 
                    }else{
                        echo 'phone='.$i_phone.',driver='.$driver_id.',send failed'.PHP_EOL; 
                    }
                }
            }else{
                break;
            }
        }
    }


    /**
    *   给昨天下单客户发洗车短信
    *
    */
    public function actionOrderMsg(){
        $msg='找个e代驾师傅帮您去洗车，代驾+洗车费才19元。首单仅需1元，下载e代驾洗车APP立即体验：http://t.cn/RwL3rwZ';
        $max=0;
        $yesterday = date('Ymd',strtotime("-1 day"));
        while (true) {
            $sql = "SELECT order_id,phone FROM t_order WHERE order_id>:max and order_date=:yesterday and city_id in(1,2,3,4,5,6,14) and status=1 LIMIT 1000";
            $command = Yii::app()->dborder_readonly->createCommand($sql);
            $command->bindParam(":max",$max);
            $command->bindParam(":yesterday",$yesterday);
            $order_list = $command->queryAll();
            if ($order_list) {
                foreach ($order_list as $order) {
                    $max = $order['order_id'];                    
                    $i_phone= $order['phone'];
                    //使用缓存，每个号码发送一次
                    if(DriverStatus::model()->single_get('message_type_1_'.$i_phone)  != null){
                        continue;
                    }
                    $res = Sms::SendForActive($i_phone, $msg);
                    if($res){
                        DriverStatus::model()->single_set('message_type_1_'.$i_phone,1,24*3600*7);
                        echo 'phone='.$i_phone.PHP_EOL;
                    }else{
                        echo 'phone='.$i_phone.' send failed'.PHP_EOL; 
                    }
                }
            }else{
                break;
            }
        }
    }


    /**
    *   给所有用户发送短信
    *
    */
    public function actionUserMsg(){
        $msg='e代驾祝您春节快乐！春节将至，由于部分司机回家过年，可能在节日期间出现司机短缺的情况，请您提前规划行程，给您带来不便敬请谅解！';
        $max=0;
        while (true) {
            $sql = "SELECT id,phone FROM t_chun_user WHERE id>:max LIMIT 10000";
            $command = Yii::app()->db_activity->createCommand($sql);
            $command->bindParam(":max",$max);
            $user_list = $command->queryAll();
            if ($user_list) {
                foreach ($user_list as $user) {
                    $max = $user['id'];                    
                    $i_phone= $user['phone'];
                    $is_phone = Common::checkPhone($i_phone);
                    if(!$is_phone ){
                        continue;
                    }
                    //使用缓存，每个号码发送一次
                    if(DriverStatus::model()->single_get('user_spring_'.$i_phone)  != null){
                        continue;
                    }
                    $res = Sms::SendForActive($i_phone, $msg);
                    if($res){
                        DriverStatus::model()->single_set('user_spring_'.$i_phone,1,24*3600*7);
                        echo 'phone='.$i_phone.PHP_EOL;
                    }else{
                        echo 'phone='.$i_phone.' send failed'.PHP_EOL; 
                    }
                }
            }else{
                break;
            }
        }
    }

    /**
     * C类和city_id=177的城市日间业务下线
     */
    public function actionDownDayTimeOrder(){
        $cityList = RCityList::model()->getOpenCityList();
        foreach ($cityList as $key => $value) {
            $city_id = $key;
            $cityType= RCityList::model()->getOpenCityByID($city_id,'city_level');
            $cityType= $cityType ? trim(substr($cityType, 0,1)) : '';
            if($cityType == 'C' || $city_id== 177){
                //更新t_city_config
                $res = CityConfig::model()->downDayTimeCity($city_id);
                echo 'res='.$res.',city='.$city_id.'日间业务下线成功'.PHP_EOL;
            }
        }
        RCityList::model()->loadCity();//重新加载信
        echo '重新加载城市到redis成功'.PHP_EOL;

    }

}
