<?php

 class DriverPush {
    private static $_models;


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
    * push unread notice count to driver
    *
    */
    public function pushUnreadNotice($driverId,$cityId){
	 	$notice = array(
	        'driver_id' => $driverId,
	        'city_id' => $cityId,
	    );
	    $count = NewNotice::model()->getDriverUnreadcount($notice);
	    $params = array(
	    	'type' => '0',
	    	'count' => $count
	    	);
	    $content = PushMsgFactory::model()->orgPushMsg($params,PushMsgFactory::TYPE_UNREAD_MSG_DRIVER_COUNT);
	    $client = GetuiClient::model()->getDriverInfo($driverId);
	    if(empty($client)){
			echo 'push dirver=['.$driverId.'] can not find token'.PHP_EOL;
	    	return false;
	    }
	    // print_r($content);
	    // echo 'client_id='.$client['client_id'];
	    EPush::set_message_type($content, "unread_notice");
	    $result = EPush::model('driver')->send($client['client_id'],$content); 
	    
	    if ($result['result']!='ok') {
	    	echo 'push dirver=['.$driverId.'] unread notice faild'.PHP_EOL;
	    	print_r($result);
	    	return false;
	    }else{
	    	echo 'push dirver=['.$driverId.'] unread notice success'.PHP_EOL;
	    	return true;		    	
	    }
    }

 	/**
    * push unread complain count to driver
    *
    */
    public function pushUnreadComplain($driverId){
	 	$count = CustomerComplain::model()->getUnreadComplainCount($driverId);
	    $params = array(
	                'type' => '1',
	                'count' => $count
                );
	    $content = PushMsgFactory::model()->orgPushMsg($params,PushMsgFactory::TYPE_UNREAD_MSG_DRIVER_COUNT);
	    $client = GetuiClient::model()->getDriverInfo($driverId);
	    if(empty($client)){
			echo 'push dirver=['.$driverId.'] can not find token'.PHP_EOL;
	    	return false;
	    }
	   	// print_r($content);
	   	
	    EPush::set_message_type($content, "unread_complain");
	    $result = EPush::model('driver')->send($client['client_id'],$content);                      
	    if ($result['result']!='ok') {
	    	echo 'push dirver=['.$driverId.'] unread complain faild'.PHP_EOL;
	    	print_r($result);
	    	return false;
	    }else{
	    	echo 'push dirver=['.$driverId.'] unread complain success'.PHP_EOL;
	    	return true;		    	
	    }
    }

    /*
	客户账户余额变更通知司机
    */
	public function pushCustomAccountChange($driver_id,$balance,$order_id){
		$client=GetuiClient::model()->getDriverInfo($driver_id);
		if(empty($client)){
			EdjLog::info('push driver=['.$driver_id.'] can not find token');
			return false;
		}
		$params=array(
				'content'=>array(
						'order_id'=>$order_id,
						'balance'=>$balance,
						)
			);	
		$content=PushMsgFactory::model()->orgPushMsg($params,PushMsgFactory::TYPE_CUSTOM_ACCOUNT_CHANGE);
		$result=EPush::model('driver')->send($client['client_id'],$content);
		if(empty($result['result'])||empty($result['status'])||$result['result']!='ok'||$result['status']!='successed_online'){
			EdjLog::info("push driver $driver_id customer_account_change for order_id $order_id fail,result is ".serialize($result));
			return false;
		}
		else{
			EdjLog::info("push driver $driver_id customer_account_change for order_id $order_id ok,result is ".serialize($result));
			return true;
		}
	}


	/**
	* 司机余额变更通知司机
	* @param $driver_id司机id,$balance司机当前余额，$type余额变更类型，需要调用者定义，默认1
	*
	*/
	public function pushDriverAccountChange($driverId,$balance,$type = 1){
		 $params = array(
	                'balance' => $balance,
	                'change_type' => $type
                );
	    $content = PushMsgFactory::model()->orgPushMsg($params,PushMsgFactory::TYPE_DRIVER_BALANCE_CHANGE);
	    $client = GetuiClient::model()->getDriverInfo($driverId);
	    if(empty($client)){
			echo 'push dirver=['.$driverId.'] can not find token'.PHP_EOL;
	    	return false;
	    }
	   	// print_r($content);
	    $result = EPush::model('driver')->send($client['client_id'],$content);                      
	    if ($result['result']!='ok') {
	    	echo 'push dirver=['.$driverId.'] balance change faild'.PHP_EOL;
	    	print_r($result);
	    	return false;
	    }else{
	    	echo 'push dirver=['.$driverId.'] balance change success'.PHP_EOL;
	    	return true;		    	
	    }
	}

	/*
		多人订单时推送哪些单该收取现金
	*/
	public function pushCustomPayChange($driver_id,$balance,$order_id,$is_cash_only){
		$client=GetuiClient::model()->getDriverInfo($driver_id);
		if(empty($client)){
			EdjLog::info("push driver $driver_id token not find");
			return false;
		}
		$params=array(
			'content'=>array(
				'order_id'=>$order_id,
				'balance'=>$balance,
				'is_must_cash_only'=>$is_cash_only?'1':'0',
				)
			);
		$content=PushMsgFactory::model()->orgPushMsg($params,PushMsgFactory::TYPE_CUSTOM_PAY_CHANGE);
		$result=EPush::model('driver')->send($client['client_id'],$content);
		if(empty($result['result'])||empty($result['status'])||$result['result']!='ok'||$result['status']!='successed_online'){
			EdjLog::info("push custoem_pay_change for driver $driver_id order_id $order_id fail");
			return false;
		}
		else{
			EdjLog::info("push custome_pay_change for driver $driver_id order_id $order_id ok,balance $balance is_cash_only $is_cash_only");
			return true;
		}
	}

	 /**
	  * 司机端下发红包 push
	  *
	  * @param $driverId
	  * @param array $envelope
	  * @return bool
	  */
	 public function pushDriverEnvelope($driverId, $envelope = array()){
		 $type = PushMsgFactory::DRIVER_PUSH_TYPE_ENVELOPE;
		 $titleFormat = '师傅您好,e代驾回馈司机,奖励%s一个';
		 $name = isset($envelope['name']) ? $envelope['name'] : '红包';
		 $content = array();
		 $content['title'] = sprintf($titleFormat, $name);
		 $content['name']  = $name;
		 $content['type']  = $envelope['type'];
		 $content['sn']    = $envelope['sn'];
		 $content['bonus'] = isset($envelope['balance']) ? $envelope['balance'] : 0;
		 $content['order_id'] = isset($envelope['order_id']) ? $envelope['order_id'] : 0;
		 return FinanceUtils::sendPush($driverId, $type, $content);
	 }
}
