<?php
Yii::import("application.models.customer.*");
//消息中心相关命令
class messageCommand extends LoggerExtCommand{
	//优惠券过期通知，默认通知还有7天过使用期限的优惠券用户
	public function actionBonusNotice($notice_before_time){
		if(empty($notice_before_time)){
			$notice_before_time=7*24*60*60;
		}
		$nowUnixTime=time();
		$start_time=date('Y-m-d H:i:s',$nowUnixTime);
		$end_time=date('Y-m-d H:i:s',$nowUnixTime+$notice_before_time);
		$criteria = new CDbCriteria();
		$criteria->select = 'id,name';
		$arrBonusCode = BonusCode::model()->findAll($criteria);
		$arrBonusName=array();
		foreach($arrBonusCode as $row){
			$arrBonusName[$row['id']]=$row['name'];
		}
		unset($arrBonusCode);
		$sql=sprintf("select bonus_type_id,customer_phone,end_date from t_customer_bonus where order_id=0 and end_date>'%s' and end_date<'%s'",$start_time,$end_time);
		$arrBonus=Yii::app()->db_finance->createCommand($sql)->queryAll();
		foreach($arrBonus as $row){
			$bonus_id=$row['bonus_type_id'];
			if(empty($arrBonusName[$bonus_id])){
				EdjLog::info("$bonus_id not in bonus code array");
				continue;
			}
			$end_unix_time=strtotime($row['end_date']);
			$year=date('Y',$end_unix_time);
			$month=date('m',$end_unix_time);
			$day=date('d',$end_unix_time);
			$content="您的".$arrBonusName[$bonus_id]."即将到期，点击查看";
			CustomerMessage::model()->addCouponMsg($row['customer_phone'],$content); 
		}
	}

	/**
	*	收集昨天下单客户手机,发送洗车短信推广
	*	每天执行一次,早上8点执行,超时2个小时
	*/
	public function actionCollectWashMsg(){
		$msg='过节太忙，没时间洗车？e代驾高端接送洗车服务，让您省时，省力，更省心。体验价1元：http://t.cn/RwL3rwZ';
        $yesterday = date('Ymd',strtotime("-1 day"));
		//获取开通洗车城市
		$washCitys=CityConfig::model()->getAllWashCity();
		foreach ($washCitys as $city) {
			$city_id = $city['city_id'];
			$max=0;
			while (true) {
				try{
		            $criteria=new CDbCriteria;
			        $criteria->select='order_id,phone';
			        $criteria->condition='order_id>:max and order_date =:yesterday and status=1 and city_id=:city_id';
			        $criteria->limit=500;
			        $criteria->params=array(':max'=>$max,':yesterday'=>$yesterday,'city_id'=>$city_id);
			        $order_list= Order::model()->findAll($criteria);
		            if ($order_list) {
		                foreach ($order_list as $order) {
		                    $max = $order['order_id'];                    
		                    $phone= $order['phone'];
		                    //放到队列
		                    $data = array(
						        'phone' => $phone,
						        'content' => $msg,
						        'type' => MessageSend::WASH_TYPE
						  	);
						    $task=array(
						        'method'=>'push_message_send',
						        'params'=>$data,
						    );
						    Queue::model()->putin($task,'message');
						    EdjLog::info('order_id='.$max.'已经放入队列,t_message_send');
		                }
		            }else{
		                break;
		            }
	            }catch(Exception $e){
	            	EdjLog::info("产生异常".json_encode($e).'---continue');
	            	continue;
	            }
        	}
		}
	}

	/**
	*	放入队列,读取需要发送的短信内容
	*	每分钟执行一次
	*/
	public function actionSendMsg(){
		$criteria=new CDbCriteria;
        $criteria->select='id,phone,content,type,channel';
        $criteria->condition='status=0';
        $criteria->limit=1000;
        $msg_list= MessageSend::model()->findAll($criteria);
        foreach ($msg_list as $msg) {
        	$phone = $msg['phone'];
        	$content = $msg['content'];
        	$type = $msg['type'];
        	$channel = $msg['channel'];
        	$id = $msg['id'];
        	//放入队列
	        $data = array(
	        			'id'=> $id,
				        'phone' => $phone,
				        'content' => $content,
				        'type' => $type,
				        'channel' => $channel
				  	);
		    //添加
		    $task=array(
		        'method'=>'send_message_list',
		        'params'=>$data,
		    );
		    //更新数据已经发送
	        MessageSend::model()->updateByPk($id,array(
	                'status'=>MessageSend::HAS_SEND)
	        );
		   	EdjLog::info('id='.$msg['id'].' 已经放入队列,待发送');
		    Queue::model()->putin($task,'message');
        }
        
	}
}
?>

