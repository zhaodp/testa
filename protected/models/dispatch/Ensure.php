<?php
/**
 * 推送保障（自动二期优化）
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-07-11
 */
class Ensure {
	private static $_models;
	
	//短信前缀设定
	const SMS_PRE = 'order';
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
    
    public function PushMoveSms($queue_id = 0 , $driver_id = '' , $type = IGtPush::TYPE_ORDER_DETAIL) {
    	if (0 == $queue_id || empty($driver_id)) {
    		return false;
    	}
    	$result = false;
    	switch ($type) {
    		case IGtPush::TYPE_ORDER_DETAIL:
    			$result = $this->_sendOrderDetail($queue_id , $driver_id);
    			break;
    		case IGtPush::TYPE_MSG_LEADER:
    			$result = $this->_sendMsgLeader($queue_id , $driver_id);
    			break;
    		default:
    			break;
    	}
    	return $result;
    }
    
    /**
     * 发送订单详情短信
     * @param int $queue_id
     * @param string $driver_id
     * @return boolean $result
     */
    private function _sendOrderDetail($queue_id , $driver_id) {
    	//短信发送
		$sms_ret = $this->_sendOrderInfoSms($queue_id , $driver_id);
		if ( $sms_ret ) {
			$queue = OrderQueue::model()->findByPk($queue_id);
			if ($queue) {
				//判定人数（单人 、 多人：组长 组员）
				$count = OrderQueueMap::model()->count("queue_id = :queue_id" , array(":queue_id"=>$queue_id));
				if (1 != $queue->number && $queue->number == $count) {
					$send_leader_sms = Push::model()->setLeaderMsg($queue_id);
					if ( $send_leader_sms ) {
						//获取评论
						$condition = array('queue_id' => $queue_id , 'comments' => $queue->comments);
						$comments = Push::model()->getQueueComments($condition);
						
						//更新状态
						OrderQueue::model()->updateByPk($queue_id,array('flag'=>OrderQueue::QUEUE_SUCCESS , 'comments' => $comments ,'dispatch_agent' => '自动派单' , 'dispatch_time'=>date('Y-m-d H:i:s' , time())));
						return true;
					}
				}
			} else {
				return false;
			}
			$result = $this->_updateMsgFlag($queue_id , $driver_id , IGtPush::TYPE_ORDER_DETAIL);
			if ($result) {
				return true;
			}

		}
		return false;
    }
    
    private function _sendOrderInfoSms($queue_id , $driver_id) {
    	$sms_ret = false;
    	$tab = 't_message_log_'.date('Ym');
		$message_log = Yii::app()->dbreport->createCommand()
		                  ->select('content')
		                  ->from($tab)
		                  ->where('queue_id = :queue_id and driver_id = :driver_id and type=:type' , array(
		                      ':queue_id' => $queue_id,
		                      ':driver_id' => $driver_id,
		                      ':type' => IGtPush::TYPE_ORDER_DETAIL,
		                  ))->queryRow();
		if (!empty($message_log)) {
			$msg = json_decode($message_log['content']);
			$message = array(
			    'type'=>IGtPush::TYPE_ORDER_DETAIL,
				'content'=>$msg,
				'timestamp'=>time()
			);
			$message = json_encode($message);
			$message = self::SMS_PRE .$message;
			
			//获取司机电话
			$driver = DriverStatus::model()->get($driver_id);
			$phone=$driver->phone;
			$sms_ret = Sms::SendSMS($phone, $message);
		}
		return $sms_ret;
    }
    
    /**
     * 发送组长消息短信
     * @param int $queue_id
     * @param string $driver_id
     * @return boolean
     */
    private function _sendMsgLeader($queue_id , $driver_id) {
    	$queue = OrderQueue::model()->findByPk($queue_id);
		if ($queue) {
			$leader = Push::model()->setLeaderMsg($queue_id);
			if (!empty($leader)) {
				$driver_leader = DriverStatus::model()->get($leader['driver_id']);
				$leader_phone = $driver_leader->phone;
				$leader_msg = $leader['msg'];
				$send_result = Sms::SendSMS($leader_phone, $leader_msg);
				if ($send_result) {
					$result_msg_up = $this->_updateMsgFlag($queue_id , $driver_id , IGtPush::TYPE_MSG_LEADER);
					OrderQueue::model()->updateByPk($queue_id,array('flag'=>OrderQueue::QUEUE_SUCCESS , 'dispatch_agent' => '自动派单' , 'dispatch_time'=>date('Y-m-d H:i:s' , time())));
					//更新message状态
		            return true;
				}
			}
		} 
		return false;
    }
    
    /**
     * 更新msg状态
     * @param int $queue_id
     * @param string $driver_id
     * @param string $type
     * @return boolean $result;
     */
    private function _updateMsgFlag($queue_id , $driver_id , $type){
    	$tab = 't_message_log_'.date('Ym');
    	$sql="UPDATE ".$tab." SET `flag`=2 WHERE `queue_id` = :queue_id AND driver_id = :driver_id AND type=:type ";
		$result = Yii::app()->dbreport->createCommand($sql)->execute(array(
		    ":queue_id" => $queue_id,
		    ":driver_id" => $driver_id,
		    ":type" => $type,
		));
		return $result;
    }
}
