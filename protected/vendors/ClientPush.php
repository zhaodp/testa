<?php
// <!-- aiguoxin -->
class ClientPush{
	private static $_models;
    const ANDROID=1;
    const IPHONE=0;
	/**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Order the static model class
     */
    public static function model($className=__CLASS__) {
        $model=null;
        if (isset(self::$_models[$className]))
            $model=self::$_models[$className];
        else {
            $model=self::$_models[$className]=new $className(null);
        }
        return $model;
    }

    /**
    *   优惠券推送
    *   @param $customerPhone:客户号码，$message:发送内容，$messageid:消息id,$type:消息内容，$suggestionid:消息对应的具体内容id
    */
    public function pushMsg($customerPhone,$message='',$messageid=0,$type=AppleMsgFactory::TYPE_MSG_COUPON,$suggestionid=0){
        // echo $customerPhone.'start to put coupon push ...'.PHP_EOL;
        $data = array(
            'customerPhone' => $customerPhone,
            'messageid'=>$messageid,
            'message'=>$message,
            'type'=>$type,
            'suggestionid'=>$suggestionid,
            );
         //添加task队列更新数据库
        $task=array(
            'method'=>'common_client_push',
            'params'=>$data,
        );
        Queue::model()->putin($task,'clientpush');
        // echo $customerPhone." put into clinetpush queue...ok".PHP_EOL;
        return true;
    }
    /**
    *   在线支付客户支付状态push推送到客户端
    */
    public function pushMsgForOnlinePayment($customerPhone,$push_message_format,$income,$order_id,$type=AppleMsgFactory::TYPE_MSG_ONLINEPAY,$messageid=0,$suggestionid=0){
        $data = array(
            'customerPhone' =>$customerPhone,
            'message'=>$push_message_format,
            'income'=>$income,
            'order_id'=>$order_id,
            'type'=>$type,
            'messageid'=>$messageid,
            'suggestionid'=>$suggestionid,
        );
         //添加task队列更新数据库
        $task=array(
            'method'=>'online_pay_push',
            'params'=>$data,
        );
        Queue::model()->putin($task,'clientpush');
        // echo $customerPhone." put into clinetpush queue...ok".PHP_EOL;
        return true;
    }

    /**
    *   活动推送
    *
    */
    public function pushMsgForAct($customerPhone,$title,$content,$url,$version,$platform){
        echo $customerPhone.'start to put act push ...'.PHP_EOL;
        EdjLog::info('开始处理活动'.$content.',phone='.$customerPhone);
        
        $data = array(
            'customerPhone' => $customerPhone,
            'url'=>$url,
            'title'=>$title,
            'message'=>$content,
            'type'=>AppleMsgFactory::TYPE_MSG_ACTIVE,
            'version'=>$version,
            'platform'=>$platform,
            );
         //添加task队列更新数据库
        $task=array(
            'method'=>'act_client_push',
            'params'=>$data,
        );
        Queue::model()->putin($task,'clientpush');
        echo $customerPhone." put into clinetpush queue...ok".PHP_EOL;
        return true;
    }



    /**
    *
    *driver accept order 
    */
    public function pushMsgForDriverAcceptOrder($customerPhone,$driverId,$bookingId,$orderId,$driverName){
        echo $customerPhone." start to put into clinetpush queue...".PHP_EOL;
        $data = array(
            'customerPhone' => $customerPhone,
            'driverId' => $driverId,
            'bookingId' => $bookingId,
            'orderId'=>$orderId,
            'driverName'=>$driverName,
            'type'=>AppleMsgFactory::TYPE_MSG_DRIVER_RECEIVE_ORDER,
        );
        //添加task队列更新数据库
        $task=array(
            'method'=>'customer_client_push',
            'params'=>$data,
        );
        Queue::model()->putin($task,'clientpush');
        echo $customerPhone." put into clinetpush queue...ok".PHP_EOL;
        return true;
    }

    public function pushMsgForDriverAcceptOrderByQueue($params){
        echo 'driver accept order by queue...';
        $customerPhone=$params['customerPhone'];
        $driverId=$params['driverId'];
        $bookingId=$params['bookingId'];
        $orderId=$params['orderId'];
        $driverName=$params['driverName'];

        $message=$driverName.'师傅已接单';
        //排除洗车
        if(!$this->isOrderCanSend($orderId)){
            echo 'xiche order!'.PHP_EOL;
            return false;
        }

        //find user client_id
        if(empty($customerPhone)){
            echo 'phone is empty!'.PHP_EOL;
            return false;
        }
        $customer_client = CustomerClient::model()->getByPhoneAndLast($customerPhone);
        if(empty($customer_client)){
            echo "phone=".$customerPhone.',customer_client is empty'.PHP_EOL;
            return false;
        }
        $customer=CustomerMain::model()->getCustomer($customerPhone);
        if(empty($customer)){
            echo "phone=".$customerPhone.',customer_main is empty'.PHP_EOL;
            return false;
        }
        // 3.3.0
        if(!$this->canSend($customer['app_ver'])){
            return false;
        }
        $client_id = $customer_client['client_id'];
        $type = $customer_client['type'];
        if($type == self::ANDROID){//use getui
            echo 'phone='.$customerPhone.' use getui'.PHP_EOL;
            $params = array(
            'content' => $message,
            'orderId' => $orderId,
            'driverId' => $driverId,
            'bookingId' => $bookingId
            );
            return $this->useGetui($params,$client_id,AppleMsgFactory::TYPE_MSG_DRIVER_RECEIVE_ORDER, 'customer_client_push_order');
        }elseif ($type == self::IPHONE) {//use apple push
            echo 'phone='.$customerPhone.' use iphone'.PHP_EOL;
            $params = array(
            'message' => $message,
            'orderId' => $orderId,
            'badge' => 0,
            'sound' => 'ping1',
            'driverId' =>$driverId,
            'bookingId' => $bookingId
            );
            return $this->useApplePush($params,$customerPhone,$client_id,AppleMsgFactory::TYPE_MSG_DRIVER_RECEIVE_ORDER, 'apple_order_message');
        }

        return true;
    }
    /**
    *
    *driver reject order 
    */
    public function pushMsgForDriverRejectOrder($customerPhone,$driverId,$bookingId,$orderId,$driverName){
        $data = array(
            'customerPhone' => $customerPhone,
            'driverId' => $driverId,
            'bookingId' => $bookingId,
            'driverName'=>$driverName,
            'orderId' => $orderId,
            'type'=>AppleMsgFactory::TYPE_MSG_DRIVER_REJECT_ORDER,
        );
        //添加task队列更新数据库
        $task=array(
            'method'=>'customer_client_push',
            'params'=>$data,
        );
        Queue::model()->putin($task,'clientpush');
        return true;
    }

    public function pushMsgForDriverRejectOrderByQueue($params){
        $customerPhone=$params['customerPhone'];
        $driverId=$params['driverId'];
        $bookingId=$params['bookingId'];
        $driverName=$params['driverName'];
        $orderId=$params['orderId'];

        $message=$driverName.'师傅暂时无法为您服务';
        //排除洗车
        if(!$this->isOrderCanSend($orderId)){
            echo 'xiche order!'.PHP_EOL;
            return false;
        }
        //find user client_id
        if(empty($customerPhone)){
            echo 'phone is empty!'.PHP_EOL;
            return false;
        }
        $customer_client = CustomerClient::model()->getByPhoneAndLast($customerPhone);
        if(empty($customer_client)){
            echo "phone=".$customerPhone.',customer_client is empty'.PHP_EOL;
            return false;
        }
        $customer=CustomerMain::model()->getCustomer($customerPhone);
        if(empty($customer)){
            echo "phone=".$customerPhone.',customer_main is empty'.PHP_EOL;
            return false;
        }
        // 3.3.0
        if(!$this->canSend($customer['app_ver'])){
            return false;
        }
        $client_id = $customer_client['client_id'];
        $type = $customer_client['type'];
        if($type == self::ANDROID){//use getui
            echo 'phone='.$customerPhone.' use getui'.PHP_EOL;
            $params = array(
            'content' => $message,
            'driverId' => $driverId,
            'bookingId' => $bookingId
            );
            return $this->useGetui($params,$client_id,AppleMsgFactory::TYPE_MSG_DRIVER_REJECT_ORDER, 'customer_client_push_order');
        }elseif ($type == self::IPHONE) {//use apple push
            echo 'phone='.$customerPhone.' use iphone'.PHP_EOL;
            $params = array(
            'message' => $message,
            'badge' => 0,
            'sound' => 'ping1',
            'driverId' =>$driverId,
            'bookingId' => $bookingId
            );
            return $this->useApplePush($params,$customerPhone,$client_id,AppleMsgFactory::TYPE_MSG_DRIVER_REJECT_ORDER, 'apple_order_message');
        }

        return true;
    }

    /**
    *
    *driver cancel order 
    */
    public function pushMsgForDriverCancelOrder($customerPhone,$driverId,$bookingId,$orderId,$driverName,$cancelType){
    	$data = array(
            'customerPhone' => $customerPhone,
            'driverId' => $driverId,
            'bookingId' => $bookingId,
            'orderId'=>$orderId,
            'driverName'=>$driverName,
            'cancelType'=>$cancelType,
            'type'=>AppleMsgFactory::TYPE_MSG_DRIVER_CANCEL_ORDER,
        );
        //添加task队列更新数据库
        $task=array(
            'method'=>'customer_client_push',
            'params'=>$data,
        );
        Queue::model()->putin($task,'clientpush');
        return true;
    }

    public function pushMsgForDriverCancelOrderByQueue($params){
        $customerPhone=$params['customerPhone'];
        $driverId=$params['driverId'];
        $bookingId=$params['bookingId'];
        $orderId=$params['orderId'];
        $driverName=$params['driverName'];
        $cancelType=$params['cancelType'];

        $message=$driverName.'师傅已取消订单';
        //排除洗车
        if(!$this->isOrderCanSend($orderId)){
            echo 'xiche order!'.PHP_EOL;
            return false;
        }
        //find user client_id
        if(empty($customerPhone)){
            echo 'phone is empty!'.PHP_EOL;
            return false;
        }
        $customer_client = CustomerClient::model()->getByPhoneAndLast($customerPhone);
        if(empty($customer_client)){
            echo "phone=".$customerPhone.',customer_client is empty'.PHP_EOL;
            return false;
        }
        $customer=CustomerMain::model()->getCustomer($customerPhone);
        if(empty($customer)){
            echo "phone=".$customerPhone.',customer_main is empty'.PHP_EOL;
            return false;
        }
        // 3.3.0
        if(!$this->canSend($customer['app_ver'])){
            return false;
        }
        $client_id = $customer_client['client_id'];
        $type = $customer_client['type'];
        if($type == self::ANDROID){//use getui
            echo 'phone='.$customerPhone.' use getui'.PHP_EOL;
            $params = array(
            'content' => $message,
            'driverId' => $driverId,
            'bookingId' => $bookingId,
            'orderId'=>$orderId,
            'driverName'=>$driverName,
            'cancelType'=>$cancelType
            );
            return $this->useGetui($params,$client_id,AppleMsgFactory::TYPE_MSG_DRIVER_CANCEL_ORDER, 'customer_client_push_order');
        }elseif ($type == self::IPHONE) {//use apple push
            echo 'phone='.$customerPhone.' use iphone'.PHP_EOL;
            $params = array(
            'message' => $message,
            'badge' => 0,
            'sound' => 'ping1',
            'driverId' =>$driverId,
            'bookingId' => $bookingId,
            'orderId'=>$orderId,
            'driverName'=>$driverName
            );
            return $this->useApplePush($params,$customerPhone,$client_id,AppleMsgFactory::TYPE_MSG_DRIVER_CANCEL_ORDER, 'apple_order_message');
        }

        return true;
    }

    /**
    *
    *driver reach the order place 
    */
    public function orgPushMsgForDriverReachOrder($customerPhone,$driverId,$bookingId,$orderId,$driverName){
       $data = array(
            'customerPhone' => $customerPhone,
            'driverId' => $driverId,
            'bookingId' => $bookingId,
            'orderId'=>$orderId,
            'driverName'=>$driverName,
            'type'=>AppleMsgFactory::TYPE_MSG_DRIVER_REACH_TARGET,
        );
        //添加task队列更新数据库
        $task=array(
            'method'=>'customer_client_push',
            'params'=>$data,
        );
        Queue::model()->putin($task,'clientpush');
        return true;
    }

    public function orgPushMsgForDriverReachOrderByQueue($params){
        $customerPhone=$params['customerPhone'];
        $driverId=$params['driverId'];
        $bookingId=$params['bookingId'];
        $orderId=$params['orderId'];
        $driverName=$params['driverName'];

        $message=$driverName.'师傅已就位';
        //排除洗车
        if(!$this->isOrderCanSend($orderId)){
            echo 'xiche order!'.PHP_EOL;
            return false;
        }
        //find user client_id
        if(empty($customerPhone)){
            echo 'phone is empty!'.PHP_EOL;
            return false;
        }
        $customer_client = CustomerClient::model()->getByPhoneAndLast($customerPhone);
        if(empty($customer_client)){
            echo "phone=".$customerPhone.',customer_client is empty'.PHP_EOL;
            return false;
        }
        $customer=CustomerMain::model()->getCustomer($customerPhone);
        if(empty($customer)){
            echo "phone=".$customerPhone.',customer_main is empty'.PHP_EOL;
            return false;
        }
        // 3.3.0
        if(!$this->canSend($customer['app_ver'])){
            return false;
        }
        $client_id = $customer_client['client_id'];
        $type = $customer_client['type'];
        if($type == self::ANDROID){//use getui
            echo 'phone='.$customerPhone.' use getui'.PHP_EOL;
            $params = array(
            'content' => $message,
            'driverId' => $driverId,
            'bookingId' => $bookingId,
            'orderId'=>$orderId
            );
            return $this->useGetui($params,$client_id,AppleMsgFactory::TYPE_MSG_DRIVER_REACH_TARGET, 'customer_client_push_order');
        }elseif ($type == self::IPHONE) {//use apple push
            $params = array(
            'message' => $message,
            'badge' => 0,
            'sound' => 'ping1',
            'driverId' =>$driverId,
            'bookingId' => $bookingId,
            'orderId'=>$orderId
            );
            return $this->useApplePush($params,$customerPhone,$client_id,AppleMsgFactory::TYPE_MSG_DRIVER_REACH_TARGET, 'apple_order_message');
        }

        return true;
    }

     /**
    *
    *use getui
    */
    public function useGetui($params,$client_id,$type, $msg_type = 'default_client_push'){
        EdjLog::info('clent push use getui ....'.$client_id);

        $content = PushMsgFactory::model()->orgPushMsg($params,$type);
        
        //add by yangzhi 2015-04-03 增加个推消息类型参数
        if (!isset($msg_type) || empty($msg_type)) {
        	$msg_type = 'default_client_push';
        }
        
        EPush::set_message_type($content, $msg_type);
        
        $result = EPush::model('customer')->send($client_id,$content,EPush::LEVEL_HIGN,1800); 
        EdjLog::info('result='.serialize($result));
        if ($result['result']!='ok') {
            return false;
        } 
        return true;
    }

    /**
    *
    *use apple push
    **/
    public function useApplePush($params,$customerPhone,$client_id,$type, $message_type = 'default_apple_message'){
        EdjLog::info('clien push use apple push ....'.$customerPhone);
        //open apple push
        //ios桌面icon加未读消息数
        $app_version=CustomerMain::model()->getAppversion($customerPhone);
        
        $unread_msg_num = CustomerMessage::model()->getUnReadMsgNum($customerPhone,$app_version);//未读消息数
        $params['badge']=intval($unread_msg_num);
        
        $message=AppleMsgFactory::model()->orgPushMsg($params,$type);
        $body = json_encode($message,JSON_UNESCAPED_UNICODE);
		
        //changed by yangzhi 2015-04-03
        $this->addAppleMessage($customerPhone, $client_id, $body, $message_type);
        
        //save to apple push table 
        //$result = AppleMessage::model()->addAppleMessage($customerPhone,$client_id,$body);
        
        //fix bug , not save db,direct to send to apple
        // $applePush = new ApplePush;
        // $applePush->getSockConnet();
        // $result = $applePush->push($token,$body);
        // $applePush->closeConnections();
        //return $result == 1;
        return true;
    }

    /**
     * add by yangzhi 2015-04-03 直接将苹果消息存入到对列, 不再存储到数据库
     * @param string $phone
     * @param string $client_id
     * @param string $content
     * @param string $message_type
     */
    public function addAppleMessage($phone, $client_id, $content, $message_type = null) {
    	$apple_message = array(
    	    'method' => 'send_apple_message',
    	    'params' => array(
    	          'phone' => $phone,
    	          'device_token' => $client_id,
    	          'message_time' => time(),
    	          'push_distinct_id' => $this->uniqId(),
    	          'content' => $content
    	    )
    	);
    	
    	if (isset($message_type) && 'apple_order_message' == $message_type) {
    		Queue::model()->putin($apple_message, 'apple_order_message');
    	} else {
    		Queue::model()->putin($apple_message, 'apple_notice_message');
    	}
    }
    
    private function uniqId() {
    	$id = Tools::getUniqId('nomal');
    	$length = strlen($id);
    	if ($length > 9) {
    		return substr($id, $length - 9);
    	} else {
    		return $id;
    	}
    }
    
    /**
    *
    * can't be lower than 3.30
    */
    private function canSend($app_ver){
        if($app_ver < "3.3.0"){
            echo 'version='.$app_ver.' can not send'.PHP_EOL;
            return false;
        }
        echo 'version='.$app_ver.' can send'.PHP_EOL;
        return true;
    }

    public function canSendByVersion($app_ver,$version){
        if($app_ver < $version){
            echo 'version='.$app_ver.' can not send'.PHP_EOL;
            return false;
        }
        echo 'version='.$app_ver.' can send'.PHP_EOL;
        return true;
    }

    /**
     * 三周年客户回馈活动push
     * @param $client
     * @param $phone
     * @param $push_msg
     * @param $url
     * @return bool
     */
    public function pushMsgForCustomerFeedback($client, $phone, $push_msg, $url, $app_ver){
        echo $phone.'start to put feedback push ...'.PHP_EOL;
        EdjLog::info('开始处理'.$phone);
        $data = array(
            'phone' => $phone,
            'url'=>$url,
            'push_msg'=>$push_msg,
            'type'=>AppleMsgFactory::TYPE_MSG_ACTIVE,
	        'client_id'=>$client['client_id'],
	        'type'=>$client['type'],
            'app_ver'=>$app_ver,
        );
        $task=array(
            'method'=>'feedback_client_push',
            'params'=>$data,
        );
        Queue::model()->putin($task,'clientpush');
        echo $phone." put into clinetpush queue...ok".PHP_EOL;
        return true;
    }
    /**
     * 司机接单/开车时推送分享给客户
     * @param $order_id
     * @param $trigger_time
     */
    public function pushShareForCustomer($order_id, $trigger_time){
        $data = array(
            'order_id' => $order_id,
            'trigger_time' => $trigger_time,
        );
        $task=array(
            'method'=>'push_share_to_customer',
            'params'=>$data,
        );
        Queue::model()->putin($task,'clientpush');
        return true;
    }

    /**
    *   洗车订单不推送   
    *
    */
    public function isOrderCanSend($order_id){
        $order = Order::model()->queryOrder($order_id);
        if(!$order || empty($order)){
            EdjLog::info('id为'.$order_id.'的订单不存在');
            return false;
        }
        if($order['source'] == Order::SOURCE_WASHCAR_CLIENT){//洗车订单
            EdjLog::info('id为'.$order_id.'的订单为洗车订单');
            return false;
        }
        return true;
    }

    /*
     * 用户提醒 push,5.4.1需求
     */
    public function userNotifyPush($customerPhone,$params){
        $customer_client = CustomerClient::model()->getByPhoneAndLast($customerPhone);
        if(empty($customer_client)){
            EdjLog::info("phone=".$customerPhone.',customer_client is empty');
            return false;
        }
        $client_id = $customer_client['client_id'];
        $type = $customer_client['type'];
        if($type == self::ANDROID){
            $this->useGetui($params,$client_id,AppleMsgFactory::TYPE_MSG_USERNOTIFY);
            EdjLog::info("ANDROID phone=".$customerPhone.',send msg ok,msg:'.json_encode($params).PHP_EOL);
        }elseif ($type == self::IPHONE) {//use apple push
            $this->useApplePush($params,$customerPhone,$client_id,AppleMsgFactory::TYPE_MSG_USERNOTIFY);
            EdjLog::info("IPHONE phone=".$customerPhone.',send msg ok,msg:'.json_encode($params).PHP_EOL);
        }
    }
}
