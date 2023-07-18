<?php
/**
 * 智能计费
 * Created by JetBrains PhpStorm.
 * User: zhanglimin
 * Date: 13-5-30
 * Time: 下午3:32
 * To change this template use File | Settings | File Templates.
 */
class AutoOrder{

    private static $_models;
    
    const SOURCE_CLIENT_MSG = '直呼APP';
    const SOURCE_CALLCENTER_MSG = '呼叫中心';
    const SOURCE_CLIENT_INPUT_MSG = '客户端补单';
    const SOURCE_CALLCENTER_INPUT_MSG = '客户端补单';
    
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

    /**
     * 新版订单处理
     * @param $params
     */
    public function order_operate($params){

        //强制更新msg LOG  状态为己接收
        MessageLog::model()->updateByPk($params['push_msg_id'],array('flag'=>3));

        $queue = OrderQueue::model()->find('id=:id and flag=:flag ', array(':id'=>$params['queue_id'],':flag'=>OrderQueue::QUEUE_WAIT_COMFIRM));

        if($queue){

            $where = array(
                'phone'=>$queue->phone,
                'booking_time' =>strtotime($queue->booking_time),
                'driver_id'=>$params['driver_id'],
            );
            //当前己成订单数
            $order_count = Order::model()->count('phone=:phone  and booking_time =:booking_time and driver_id <> :driver_id ', $where);
            if($order_count > $queue->number){
                return false;
            }

            unset($where);
            //当前己建立订单关系数
            $order_queue_count = OrderQueueMap::model()->count('queue_id =:queue_id',array(":queue_id"=>$queue->id));
            if( $order_queue_count > $queue->number  ){
                return false;
            }

            //建立订单

            $order_arr = array(
                'queue_id' =>$queue->id,
                'driver_id' =>$params['driver_id'],
                'name'=>$queue->name,
                'phone'=>$queue->phone,
                'address'=>$queue->address,
                'booking_time'=>$queue->booking_time,
                'city_id'=>$queue->city_id,
                'type'=>$queue->type,
                'created'=>$queue->created,
            );

            $order = $this->setGenOrder($order_arr);

            if($order['code'] == 1){
                return false;
            }
            unset($order_arr);

            /*
             * 接单后记录接收位置
             */
            $arr = array(
                'order_id' => $order['order_id'],
                'flag' => OrderPosition::FLAG_ACCEPT,
                'gps_type'=>isset($params['gps_type']) ? $params['gps_type'] : 'wgs84',
                'lat'=>isset($params['lat']) ? $params['lat'] : 1,
                'lng'=>isset($params['lng']) ?$params['lng'] : 1,
                'log_time'=>isset($params['log_time']) ? $params['log_time'] : date("Y-m-d H:i:s"),
            );
            $ret = OrderPosition::model()->insertInfo($arr);
            unset($arr);

            //建立关系
            $order_queue_relations = $this->setOrderQueueRelations(
                $order['order_id'],
                $params['queue_id'],
                $params['driver_id'] ,
                $params['confirm_time']);

            if($order_queue_relations['code'] == 1){
                return false;
            }

            //设置发送消息
            $msg = $this->setPushOrderMsg( $params['driver_id'] ,$params['queue_id'] , $order['order_id']);
            $msg['msg']['order_id'] = $order['order_id'];
            if($msg['code'] == 1){
                return false;
            }
            //推送消息
            $message_arr=array(
                'type'=>GetuiPush::TYPE_ORDER_DETAIL,
                'content'=>$msg['msg'],
                'level'=>3,  //级别
                'driver_id'=>$params['driver_id'],
                'queue_id'=>$params['queue_id'],
                'created'=>date('Y-m-d H:i:s' , time()),
            );

            //发送失败记录请求次数
            $key = trim($params['queue_id'])."_".trim($params['driver_id']);
            DriverStatus::model()->orderCount($key , true);
            $push_message_flag = $this->organize_message_push($message_arr);
            $count = DriverStatus::model()->orderCount($key);
            if($push_message_flag || $count >= Yii::app()->params['GETUI_PUSH_NUM']){
                //更新派单时间 BY AndyCong<congming@edaijia.cn> 2013-05-21
                OrderQueue::model()->updateByPk($queue->id,array('update_time'=>date('Y-m-d H:i:s' , time())));
                //更新派单时间 BY AndyCong<congming@edaijia.cn> 2013-05-21 END
                //获取当前己派送的司机总数
                $count = OrderQueueMap::model()->count('queue_id =:queue_id',array(":queue_id"=>$queue->id));
                if($queue->number == $count){
                    if($queue->number > 1){
                        //发送组员信息
                        $leader = self::sendLeaderSmsGroupMemberMsg($queue->id);
                        if(!empty($leader)){
                            //推送消息
                            $message=array(
                                'type'=>GetuiPush::TYPE_MSG_LEADER,
                                'content'=>$leader['msg'],
                                'level'=>3,  //级别
                                'driver_id'=>$leader['driver_id'],
                                'queue_id'=>$queue->id,
                                'created'=>date('Y-m-d H:i:s' , time()),
                            );
                            QueueProcess::push_message($message);
                        }
                    }

                    //获取备注信息
                    $queue_arr = array(
                        'queue_id' => $queue->id,
                        'comments' => $queue->comments,
                    );
                    //获取备注信息 END

                    $comments = $this->getQueueComments($queue_arr);
                    return  OrderQueue::model()->updateByPk($queue->id,array('flag'=>OrderQueue::QUEUE_SUCCESS , 'dispatch_agent' => '自动派单' , 'comments' => $comments , 'dispatch_time'=>date('Y-m-d H:i:s' , time())));

                }
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }



    /**
     * 新版设置发送信息
     * @param string $driver_id
     * @param string $queue_id
     * @return array
     * @author zhanglimin<zhangliming@edaijia.cn>
     *         2013-05-06
     * @editor AndyCong<congming@edaijia.cn>
     *         2013-05-11
     */
    public  function setPushOrderMsg($driver_id ="", $queue_id = "" , $order_id = ''){

        if( empty($driver_id) || empty($queue_id) || empty($order_id)){
            return array('code'=>1);
        }
        $queue = OrderQueue::model()->find(' id=:id' , array(':id'=>$queue_id));
        if(empty($queue)){
            return array('code'=>1);
        }
        $data = array(
            'address' => $queue->address,
            'customer_name' => $queue->name,
            'phone' => '',
            'contact_phone' => '',
            'booking_time' => $queue->booking_time,
            'number' => $queue->number,
            'vipcard' => '',
            'role' => '',
            'leader_phone' => '',
            'bonus' => '',
            'card'=>'', //VIP或优惠卷卡号
            'balance'=>0, //VIP余额或优惠卷余额
            'source'=> $queue->type ,//订单来源
        );

        if ($queue->contact_phone && $queue->contact_phone != $queue->phone) {
            $data['phone'] = substr($queue->phone , 0 , 3)."****".substr($queue->phone , -4);
            $data['contact_phone'] = $queue->contact_phone;
        } else {
            $data['phone'] = $queue->phone;
        }

        $order_favorable = Order::model()->getOrderFavorable($queue->phone ,strtotime($queue->booking_time),$queue->type , $order_id);

        if($order_favorable['code'] == 1){
            //VIP
            $money_str = $order_favorable['money'];
            $data['vipcard'] = '余额：'.$money_str.'元,不足部分请收取现金';
            $data['card'] = $order_favorable['card'];
            $data['balance'] = $order_favorable['money'];
        }elseif($order_favorable['code'] == 2){
            //优惠劵
            $data['bonus']=' 优惠金额：'.$order_favorable['money'].'元';
            $data['card'] = $order_favorable['card'];
            $data['balance'] = $order_favorable['money'];
        }

        if($queue->number > 1){
            //预约多人
            $leader = self::checkGroupLeader($queue->id);
            if(empty($leader)){
                return array('code'=>1);
            }

            if($leader == $driver_id){
                $data['role'] = '组长';
                $data['leader_phone'] = '';
            }else{
                $data['role'] = '组员';
                //获取组长的姓名与手机
                $leaderInfo = DriverStatus::model()->get($leader);
                $data['leader_phone'] = $leaderInfo->phone;
            }
        }

        return array(
            'code'=>0,
            'msg' =>$data,
        );
    }

    /**
     * 通过QUEUE ID 生成订单
     * @author zhanglimin 2013-05-06
     * @param $queue_id
     * @param $driver_id
     * @return array|mixed|null
     * @editor AndyCong<congming@edaijia.cn>
     *         2013-07-03 优化（简化代码）
     */
    public function setGenOrder($order_arr = array()){
        $ret = array( 'code' => 1);
        $client_name = isset($order_arr['name']) ? $order_arr['name'] : '';
        $client_phone = isset($order_arr['phone']) ? $order_arr['phone'] : '';
        $client_address = isset($order_arr['address']) ? $order_arr['address'] : '';
        $client_time = isset($order_arr['booking_time']) ? $order_arr['booking_time'] : '';
        $city_id = isset($order_arr['city_id']) ? $order_arr['city_id'] : '';
        $source = isset($order_arr['type']) ? $order_arr['type'] : '';
        $driver_id = isset($order_arr['driver_id']) ?  $order_arr['driver_id'] : '';
        $created = isset($order_arr['created']) ? $order_arr['created'] : '';
        $call_time = isset($order_arr['call_time']) ? $order_arr['call_time'] : strtotime($created);
        $channel = isset($order_arr['channel']) ? $order_arr['channel'] : CustomerApiOrder::QUEUE_CHANNEL_CALLORDER; //增加渠道标识
	$call_type = isset($order_arr['call_type']) ? $order_arr['call_type'] : ''; //电话订单呼入呼出标识

        //验证参数
        if(empty($client_name) || empty($client_phone) || empty($client_address) || empty($client_time) || empty($driver_id) || empty($created)){
            return $ret;
        }

        $params = array (':phone'=>$client_phone,':source'=>$source,':driver_id'=>$driver_id,':booking_time'=>strtotime($client_time));
        $ret = Order::model()->find('phone=:phone and source=:source and driver_id=:driver_id and booking_time =:booking_time ', $params);
        if(!$ret){
            $driver_detail=DriverStatus::model()->get($driver_id);
            if(!empty($driver_detail)){
                $employee_name = $driver_detail->info['name'];
                $imei = $driver_detail->info['imei'];
                $employee_phone = $driver_detail->phone;
            }else{
                $employee_name = $imei = $employee_phone = "";
            }
            $source_text = Order::SourceToDescription($source);
            $order_number = isset($order_arr['order_number']) ? $order_arr['order_number'] : '';
            
            //插入订单  (优化防SQL注入)
            $order_date = date('Ymd', time());
            $booking_time = strtotime($client_time);
            $created = time();
            $attributes = array(
                'order_number' =>$order_number,
                'name' =>$client_name,
                'phone' =>$client_phone,
                'source' =>$source,
                'channel' =>$channel,  //增加渠道标识
                'driver' =>$employee_name,
                'city_id' =>$city_id,
                'driver_id' =>$driver_id,
                'driver_phone' =>$employee_phone,
                'imei' =>$imei,
                'call_time' =>$call_time,
                'order_date' =>$order_date,
                'booking_time' =>$booking_time,
                'location_start' =>$client_address,
                'description' =>$source_text,
                'created' =>$created,
            );
            Order::getDbMasterConnection()->createCommand()->insert('t_order' , $attributes);
            
            //获取最新插入的order_id
            $order_id = Order::getDbMasterConnection()->getLastInsertID(); 
            if(!empty($order_id)){
                //日志
                $log_arr = array('order_id' => $order_id,'operator' => $driver_id,);
                OrderLog::model()->insertLog($log_arr);
                if (!in_array($source, Order::$client_input_source)
		      && !in_array($source, Order::$callcenter_input_sources)
		      //一口价(洗车)暂时不支持 
		      && !in_array($source, Order::$washcar_sources)) {
                    if($channel == CustomerApiOrder::QUEUE_CHANNEL_CALLORDER && $call_type !== '') {
                        if($call_type == 0) {
			    //客户呼入电话可使用优惠劵
                            BonusLibrary::model()->BonusOccupancy($client_phone , $order_id , $source);
			}
		    }
		    else {
                        BonusLibrary::model()->BonusOccupancy($client_phone , $order_id , $source);
		    }
                }
                $ret = array('code' => 0 , 'order_id' => $order_id);
            }
        }else{
            $ret = array('code' => 0 , 'order_id' => $ret->order_id);
        }
        return $ret;
    }

    /**
     * 建立订单与队列的关系
     * @author zhanglimin 2013-05-06
     * @param string $order_id
     * @param string $queue_id
     * @param string $driver_id
     * @param string $confirm_time
     * @return array
     */
    public  function setOrderQueueRelations($order_id = '' , $queue_id = '' ,$driver_id='',$confirm_time=''){
        if(empty($order_id) || empty($queue_id) || empty($driver_id) || empty($confirm_time)){
            $ret = array(
                'code' => 1 ,
            );
            return $ret;
        }
        $params = array(
            'order_id' => $order_id,
            'queue_id' => $queue_id,
            'driver_id' => $driver_id,
        );

        $ret = OrderQueueMap::model()->find('order_id=:order_id and queue_id=:queue_id and driver_id=:driver_id', $params);
        if(empty($ret)){
            $model = new OrderQueueMap();
            $attributes = array(
                'order_id' => $order_id,
                'queue_id' => $queue_id,
                'driver_id' => $driver_id,
                'confirm_time' => $confirm_time,
                'dispatch_time' => $confirm_time,
                'flag' => 1,
            );
            $model->attributes = $attributes;
            $result = $model->save();
            if($result){
                //直接更新分配司机数量 BY AndyCong<congming@edaijia.cn>2013-05-22
                $sql = "UPDATE `t_order_queue` SET `dispatch_number` = `dispatch_number`+1 WHERE id = :id";
                // Yii::app()->db change into OrderQueue::getDbMasterConnection()
                OrderQueue::getDbMasterConnection()->createCommand($sql)->execute(array(
                    ':id' => $queue_id,
                ));

                $ret = array(
                    'code' => 0 ,
                );
                return $ret;
            }else{
                $ret = array(
                    'code' => 1 ,
                );
                return $ret;
            }
        }else{
            $ret = array(
                'code' => 0 ,
            );
            return $ret;
        }
    }


    /**
     * 谁是组长
     */
    private function checkGroupLeader($queue_id){
        $leader = OrderQueueMap::model()->getLeader($queue_id);
        if(empty($leader)) {
            return array();
        }
        return $leader;
    }



    /**
     * 把组员的信息发给组长
     * @author zhanglimin 2013-05-11
     * @param int $queue_id
     * @return array
     */
    public function sendLeaderSmsGroupMemberMsg($queue_id = 0 ){
        if($queue_id == 0 ){
            return array();
        }
        //TODO 先走主库吧，到时候量大的话，在改
        $orderQueueMapList = OrderQueueMap::model()->findAllByAttributes(
            array('queue_id' => $queue_id),
            array('select'   => 'driver_id', 'order' => 'confirm_time ASC')
        );
        
        if(!empty($orderQueueMapList)){
            $message = '工号:%s 手机:%s  ';
            $msg = "组员联系信息:";
            $leader = "";
            foreach($orderQueueMapList as $k=>$val){
                if($k == 0 ){
                    $leader = $val->driver_id;
                }else{
                    $driver = DriverStatus::model()->get($val->driver_id);
                    $phone = $driver->phone;
                    $msg .= sprintf($message, $val->driver_id, $phone) ;
                }
            }
            return array(
                'driver_id'=>$leader,
                'msg' => $msg,
            );
        }else{
            return array();
        }

    }



    /**
     * 组织queue备注信息
     * @param array $data
     * @return string $comments
     */
    public function getQueueComments($data = array()) {
        $comments = '';
        if (empty($data)) {
            return $comments;
        }
        $drivers = OrderQueueMap::model()->findAll('queue_id=:queue_id', array (':queue_id'=>$data['queue_id']));
        foreach($drivers as $driver) {
            $driver_info = Driver::getProfile($driver->driver_id);
            $comments .= sprintf('%s %s', $driver->driver_id, $driver_info->phone)."<br/>";
        }
        if ($data['comments']) {
            $comments = $data['comments'] .'<br/>'.$comments;
        } else {
            $comments = $data['comments'] . $comments;
        }
        return $comments;
    }
    
    
    /**
     * 发送订单消息
     * @param int $queue_id
     * @param string $driver_id
     * @param string $message
     * @return boolean
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-05-31
     */
	public function GetuiMoveToSms($queue_id = 0 , $driver_id = '' , $type = '') {
		$result = false;
		//判定类型 如果为组长直接发送组长信息
		if (GetuiPush::TYPE_MSG_LEADER == $type) {
			$queue = OrderQueue::model()->findByPk($queue_id);
			if ($queue) {
				$send_leader_sms = self::SendLeaderSms($queue_id);
				if ($send_leader_sms) {
					$result_msg_up = self::updateMsgFlag($queue_id , $driver_id , $type);
					OrderQueue::model()->updateByPk($queue_id,array('flag'=>OrderQueue::QUEUE_SUCCESS , 'dispatch_agent' => '自动派单' , 'dispatch_time'=>date('Y-m-d H:i:s' , time())));
					//更新message状态
		            $result = true;
				} 
			} 
			return $result;
		}
		//判定类型 如果为组长直接发送组长信息 END
		
		//发送订单信息短信 BY AndyCong 2013-06-27 优化封装
		$sms_ret = self::SendOrderInfoSms($queue_id , $driver_id);
		//发送订单信息短信 BY AndyCong 2013-06-27 优化封装 END
		
		if ( $sms_ret ) {
			//判定是不是最后一个组员 如果是最后一个组员 更新OrderQueue表中flag为4
			$queue = OrderQueue::model()->findByPk($queue_id);
			if ($queue) {
				$count = OrderQueueMap::model()->count("queue_id = :queue_id" , array(":queue_id"=>$queue_id));
				if (1 != $queue->number && $queue->number == $count) {
					$send_leader_sms = self::SendLeaderSms($queue_id);
					if ( $send_leader_sms ) {
						//获取评论
						$queue_arr = array(
						    'queue_id' => $queue_id,
						    'comments' => $queue->comments,
						);
						$comments = AutoOrder::model()->getQueueComments($queue_arr);
						//获取评论 END
						OrderQueue::model()->updateByPk($queue_id,array('flag'=>OrderQueue::QUEUE_SUCCESS , 'comments' => $comments ,'dispatch_agent' => '自动派单' , 'dispatch_time'=>date('Y-m-d H:i:s' , time())));
					} else {
						return $result;
					}
				}
			} else {
				return $result;
			}
			$result_msg_up = self::updateMsgFlag($queue_id , $driver_id , $type);
			if ($result_msg_up) {
				$result = true;
			}
			//判定是不是最后一个组员 如果是最后一个组员 更新OrderQueue表中flag为4 END
		}
		return $result;
	}
	
	/**
	 * 通过driver_id 、 queue_id发送短信
	 * @param int $queue_id
	 * @param string $driver_id
	 * @return boolean $sms_ret
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-06-27
	 */
	private function SendOrderInfoSms($queue_id , $driver_id) {
		$sms_ret = false;
		$message_log = Yii::app()->dbreport->createCommand()
		                  ->select('content')
		                  ->from('t_message_log')
		                  ->where('queue_id = :queue_id and driver_id = :driver_id and type=:type' , array(
		                      ':queue_id' => $queue_id,
		                      ':driver_id' => $driver_id,
		                      ':type' => GetuiPush::TYPE_ORDER_DETAIL,
		                  ))->queryRow();
		if (!empty($message_log)) {
			$msg = json_decode($message_log['content']);
			$message = array(
			    'type'=>GetuiPush::TYPE_ORDER_DETAIL,
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
	 * 发送leader短信
	 * @param int $queue_id
	 * @return boolean $result
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-05-31
	 */
	private function SendLeaderSms($queue_id) {
		$result = false;
		$leader = $this->sendLeaderSmsGroupMemberMsg($queue_id);
		if (!empty($leader)) {
			$driver_leader = DriverStatus::model()->get($leader['driver_id']);
			$leader_phone = $driver_leader->phone;
			$leader_msg = $leader['msg'];
			$result = Sms::SendSMS($leader_phone, $leader_msg);
		}
		return $result;
	}
	
	/**
	 * 更新msg状态
	 * @param int $queue_id
	 * @param string $driver_id
	 * @param string $type
	 * @return boolean $result
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-06-28
	 */
	private function updateMsgFlag($queue_id , $driver_id , $type) {
		if (empty($type)) {
			$type = GetuiPush::TYPE_ORDER_DETAIL;
		}
		$sql="UPDATE t_message_log SET `flag`=2 WHERE `queue_id` = :queue_id AND driver_id = :driver_id AND type=:type ";
		$result = Yii::app()->dbreport->createCommand($sql)->execute(array(
		    ":queue_id" => $queue_id,
		    ":driver_id" => $driver_id,
		    ":type" => $type,
		));
		return $result;
	}
	
	/**
	 * 该函数已废弃,请使用 Order::SourceToDescription 2014-12-03
	 * 获取下单来源文本
	 * @param int $source
	 * @return string $text
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-05-31
	 */
	public function GetSourceText($source) {
		switch ($source) {
			case Order::SOURCE_CLIENT:
				$text = self::SOURCE_CLIENT_MSG;
				break;
			case ORDER::SOURCE_CALLCENTER:
				$text = self::SOURCE_CALLCENTER_MSG;
				break;
			case Order::SOURCE_CLIENT_INPUT:
				$text = self::SOURCE_CLIENT_INPUT_MSG;
				break;
			case Order::SOURCE_CALLCENTER_INPUT:
				$text = self::SOURCE_CLIENT_INPUT_MSG;
				break;
			default:
				$text = self::SOURCE_CLIENT_MSG;
				break;
		}
        return $text;
	}
	
	/**
	 * 发送报单消息
	 * @param string $driver_id
	 * @param boolean $status
	 * @return boolean
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-05-26
	 */
	public function push_order_submit($driver_id , $order_id , $status = TRUE ) {
		if (empty($driver_id) || empty($order_id)) {
			return false;
		}
		if ($status) {
			$msg = '报单成功';
			$flag = 'finished';
		} else {
			$msg = '报单失败';
			$flag = 'failed';
		}
		$msg_info = array(
		    'order_id' => $order_id,
		    'message' => $msg,
		    'flag' => $flag,
		);
		$params = array(
		    'type' => GetuiPush::TYPE_ORDER_SUBMIT,
		    'driver_id' => $driver_id,
		    'level' => GetuiPush::LEVEL_HIGN,
		    'content' => $msg_info,
		    'created' => date("Y-m-d H:i:s" , time()),
		);
		$result = PushMessage::model()->organize_message_push($params);
		return $result;
	}
	
	/**
	 * 组织短信内容推送
	 * @param array $params
	 * @return boolean
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-05-13
	 */
	public function organize_message_push($params) {
		//判定类型
		$message=self::getMessageByType($params);
		//获取ClientID END
		$model=new MessageLog();
		$attributes = $params;
		$attributes['content'] = json_encode($params['content']);
		$model->attributes=$attributes;
		if ($model->save()) {
			$message['push_msg_id']=$model->push_msg_id;
			//其他包装推送参数
			$data=array(
					'client_id'=>$params['client_id'],
					'level'=>$params['level']
			);
			if (isset($params['offline_time'])) {
				$data['offline_time']=$params['offline_time'];
			}
			$data['message']=json_encode($message);
			if (isset($params['queue_id'])) {
				$data['queue_id']=$params['queue_id'];
			} else {
				$data['queue_id']=0;
			}
			$data['driver_id']=$params['driver_id'];
			//包装推送参数 END
			$result=GetuiPush::model($params['version'])->PushToSingle($data, $params['version']);
			if ($result['result']=='ok') {
			  return true;
			} else {
			  return false;
			}
			return $result;
		} else {
			return false;
		}
	}
	
	/**
	 * 通过类型获取推送消息体
	 * @param array $params
	 * @return array $message
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-04-28
	 */
	private function getMessageByType(&$params) {
		switch ($params['type']) {
			case GetuiPush::TYPE_ORDER : //订单 司机端
				$message=array(
						'type'=>GetuiPush::TYPE_ORDER,
						'queue_id'=>$params['queue_id'],
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case GetuiPush::TYPE_ORDER_DETAIL : //订单详情 司机端
				$message=array(
						'type'=>GetuiPush::TYPE_ORDER_DETAIL,
						'content'=>$params['content'],
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case GetuiPush::TYPE_MSG_LEADER : //消息  司机端
				$message=array(
						'type'=>'msg',
						'content'=>array(
								'message'=>$params['content'],
								'feedback' => 1,
						),
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case GetuiPush::TYPE_STATUS : //订单状态  司机端
				$message=array(
						'type'=>GetuiPush::TYPE_STATUS,
						'status'=>$params['status'],
						'content'=>$params['content'],
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case GetuiPush::TYPE_MSG_DRIVER : //消息  司机端
				$message=array(
						'type'=>'msg',
						'content'=>array(
								'message'=>$params['content'],
								'feedback' => 0,
						),
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case GetuiPush::TYPE_NOTICE_DRIVER : //公告 司机端
				$message=array(
						'type'=>'notice',
						'content'=>array(
								'message'=>$params['content']
						),
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case GetuiPush::TYPE_BLACK_CUSTOMER : //公告 客户端
				$message=array(
						'type'=>GetuiPush::TYPE_BLACK_CUSTOMER ,
						'content'=>array(
								'message'=>$params['content'],
								'phone'=>$params['phone'],
								'mark'=>$params['mark'],
						),
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case GetuiPush::TYPE_UPDATE_CONFIG : //配置 开关 司机端
				$message=array(
						'type'=>GetuiPush::TYPE_UPDATE_CONFIG ,
						'content'=>array(
								'message'=>'开关配置',
						),
						'timestamp'=>time()
				);
				foreach ($params['config'] as $key=>$val) {
					$message['content'][$key] = $val;
				}
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case GetuiPush::TYPE_CMD:
				$message=array(
						'type'=>GetuiPush::TYPE_CMD ,
						'content'=>array(
								'message'=>$params['message'],
						),
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case GetuiPush::TYPE_ORDER_SUBMIT:
			    $message=array(
						'type'=>GetuiPush::TYPE_ORDER_SUBMIT ,
						'content'=>$params['content'],
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			default :
				break;
		}
		$params['client_id'] = $client['client_id'];
		return $message;
	}

    /**
     * 查询当前司机有没有未报单
     * @author zhanglimin 2013-06-05
     * @param string $driver_id
     * @return bool
     */
    public function checkDriverOrderReady($driver_id){
        $criteria = new CDbCriteria();
        $criteria->select = "order_id,booking_time";
        $criteria->compare('driver_id', $driver_id);
        $criteria->compare('status',Order::ORDER_READY);
        $criteria->limit = 1;
        $criteria->order = 'booking_time desc';
        $orders = Order::model()->find($criteria);
        if($orders){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * 司机客户端一键预约
     * @param array $params
     * @param string $agent
     * @return boolean
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-05-27
     */
    public function call_order($params , $type = Order::SOURCE_CLIENT , $agent = OrderQueue::QUEUE_AGENT_CLIENT) {
    	if (empty($params)) {
    		return false;
    	}
        
    	//验证有无相同电话号和工号的呼叫中心的单子 如果有则去重（将order_number写入400手动派单的订单）
    	$is_repeat = Push::model()->validateIsRepeat($params);
    	if ($is_repeat) {
    		return $ret = array('code' => 0,'message' => '下单成功');
    	}
    	
		//一：生成t_order_queue 
		$order_queue = OrderQueue::model()->OrderQueueSave($params , $type , $agent);
		if (empty($order_queue)) {
			return $ret = array('code' => 2,'message' => '队列生成失败');
		}

	    //二：生成t_order	
	    $channel = CustomerApiOrder::QUEUE_CHANNEL_CALLORDER;
	    $order_arr = array(
            'queue_id' =>$order_queue['id'],
            'driver_id' =>$params['driver_id'],
            'name'=>$order_queue['name'],
            'phone'=>$order_queue['phone'],
            'address'=>$order_queue['address'],
            'booking_time'=>$order_queue['booking_time'],
            'city_id'=>$order_queue['city_id'],
            'type'=>$order_queue['type'],
            'created'=>$order_queue['created'],
            'call_time'=>$params['call_time'],
            'channel' => isset($order_queue['channel']) ? $order_queue['channel'] : $channel,
	    'call_type' => isset($params['call_type']) ? $params['call_type'] : '', //电话订单呼入呼出标识
        );
        if (isset($params['order_number'])) {
        	$order_arr['order_number'] = $params['order_number'];
        }
	    $order = $this->setGenOrder($order_arr);
	    if ($order['code'] == 1) {
	    	return $ret = array('code' => 2,'message' => '订单生成失败');
	    } else {
	    	$order_id = $order['order_id'];
	    }
	    
	    //将订单加入缓存 2013-11-27
	    $order_arr['order_id'] = $order_id;
	    $cache_params = Push::model()->getCacheParamsByQueueArr($order_queue , $order_arr);
	    if (!empty($cache_params)) {
	    	$task = array(
			    'method' => 'insert_orders_redis',
			    'params' => $cache_params
			);
			Queue::model()->putin($task , 'orderstate');
	    }
		//将订单加入缓存 2013-11-27 END

	    
	    if (!empty($order_arr['order_number'])) {
	    	CustomerApiOrder::model()->orderFavorableCache($order_arr['order_number']);
	    }
         
	    //三：生成t_order_queue_map
	    $confirm_time = date('Y-m-d H:i:s');	
	    $order_queue_map = $this->setOrderQueueRelations($order_id , $order_queue['id'] , $params['driver_id'] , $confirm_time);
	    if (!$order_queue_map || 1 == $order_queue_map['code']) {
	    	return $ret = array('code' => 2,'message' => '映射关系建立失败');
	    }
	    
	    //记录t_order_position
	    $arr = array(
             'order_id' => $order_id,
             'flag' => OrderPosition::FLAG_ACCEPT,
             'gps_type'=>isset($params['gps_type']) ? $params['gps_type'] : 'wgs84',
             'lat'=>isset($params['lat']) ? $params['lat'] : 1,
             'lng'=>isset($params['lng']) ?$params['lng'] : 1,
             'log_time'=>date("Y-m-d H:i:s"),
         );
         $ret = OrderPosition::model()->insertInfo($arr);
         unset($arr);

	 // Save the driver id and order id into redis
	 // For order trace
	 $convert_pos = isset($ret['position'])? $ret['position']:array();
	 RDriverPosition::model()->setCurrentOrder(
		 $params['driver_id'], 
		 $order_id, OrderProcess::PROCESS_ACCEPT,
		 $convert_pos);
	    
	    //四：推送消息
        $msg = Push::model()->setPushOrderMsg($order_queue['id'] , $params['driver_id'] , $order_id);
        if (!$msg || 1 == $msg['code']) {
        	return $ret = array('code' => 2,'message' => '组织消息失败');
        }
        
        $msg['msg']['order_id'] = $order_id;
        if (isset($params['order_number'])) {
            $msg['msg']['order_number'] = $params['order_number'];    	
        }
        $msg['msg']['source'] = CustomerApiOrder::CUSTOMER_BOOKING_CODE;
        
        $message_arr=array(
            'type'=>GetuiPush::TYPE_ORDER_DETAIL,
            'content'=>$msg['msg'],
            'level'=>3,  //级别
            'driver_id'=>$params['driver_id'],
            'queue_id'=>$order_queue['id'],
            'created'=>date('Y-m-d H:i:s' , time()),
        );
        
        //推送走分表
        $result = Push::model()->organizeMessagePush($message_arr);

	// Push accept message to customer
	$callid = $order_queue['callid'];
	$phone = $order_queue['phone'];
	$driver = DriverStatus::model()->get($params['driver_id']);

	EdjLog::Info($callid.'|'.$order_id.'|'.$phone.'|'.$params['driver_id'].'|Push accept msg|' , 'console');
	if(!empty($driver)){
	    ClientPush::model()->pushMsgForDriverAcceptOrder($phone, 
		    $params['driver_id'], $callid, $order_id, $driver->info['name']);
	}
        
        if (!$result) {
        	return $ret = array('code' => 2,'message' => '推送消息失败');
        } else {
        	return $ret = array('code' => 0,'message' => '下单成功');
        }
    }
    
    /**
	 * 获取队列（一键预约订单）列表
	 * @param string $phone
	 * @param int $offset
	 * @param int $pageSize
	 * @return array $data
	 * @author AndyCong<congming@edaijia.cn>
	 */
	public function getOrderQueueByPhone($phone = null , $offset = 0 , $pageSize = 20 , $token) {
		if ($phone == null) {
			return false;
		}
		//取个数
		// Yii::app()->db_readonly change into OrderQueue::getDbReadonlyConnection()
		$count = OrderQueue::getDbReadonlyConnection()->createCommand()
    	              ->select('COUNT(id) AS cnt')
    	              ->from('t_order_queue')
    	              ->where('phone=:phone',array(':phone'=>$phone))
    	              ->queryRow();
		//取个数 END
		
		//取订单
		// Yii::app()->db_readonly change into OrderQueue::getDbReadonlyConnection()
		$result = OrderQueue::getDbReadonlyConnection()->createCommand()
    	              ->select('id AS order_id , flag , number , phone , contact_phone , booking_time')
    	              ->from('t_order_queue')
    	              ->where('phone=:phone',array(':phone'=>$phone))
    	              ->order('id desc')
    	              ->limit($pageSize)
					  ->offset($offset)
    	              ->queryAll();
    	//取订单 END
		
		foreach ($result as $key=>$val) {
			$result[$key]['status'] = $this->_getOrderQueueStatus($val['flag'] , $val['booking_time']);
			
			if (empty($result[$key]['contact_phone'])) {
				$result[$key]['contact_phone'] = $result[$key]['phone'];
			}
			
			//派单中判定是否已完成
//			if ($result[$key]['order_status'] == OrderQueue::QUEUE_TYPE_ORDERED) {
//				$status = $this->getQueueFlag($val['order_id']);
//				if ($status) {
//					$result[$key]['order_status'] = OrderQueue::QUEUE_TYPE_FINISHED; 
//				}
//			}
			//派单中判定是否已完成 END
			
			unset($result[$key]['flag']);
			unset($result[$key]['phone']);
		}
		$data = array(
		    'orderList' => $result,
		    'orderCount' => $count['cnt'],
		);
		return $data;
	}
	
	/**
	 * 获取订单详情
	 * @param int $queue_id
	 * @param string $phone
	 * @return array $result
	 */
	public function getOrderQueueDetail($queue_id = 0 , $phone = '') {
		if (0 == $queue_id || empty($phone)) {
			return '';
		}
		
		//获取OrderQueue
		// Yii::app()->db_readonly change into OrderQueue::getDbReadonlyConnection()
		$result = OrderQueue::getDbReadonlyConnection()->createCommand()
			               ->select('id AS order_id , flag , address , phone , contact_phone , booking_time')
			               ->from("t_order_queue")
			               ->where('id = :id AND phone = :phone' , array(':id'=>$queue_id , ':phone' => $phone))
			               ->queryRow();
            
		if (!empty($result)) {
			$result['status'] = $this->_getOrderQueueStatus($result['flag'] , $result['booking_time']);
			
			if (empty($result['contact_phone'])) {
				$result['contact_phone'] = $result['phone'];
			}
			unset($result['flag']);
			
			//获取OrderQueueMap
            $relations = OrderQueueMap::model()->findAllByAttributes(
                array('queue_id' => $queue_id),
                array('select'   => 'order_id, driver_id')
            );
            
			$tmp = array();
			foreach ($relations as $key=>$val) {
			     $tmp[] = array('driver_id' => $val->driver_id, 'url' => 'http://www.test.com/'.$val->order_id);
			}
			$result['driverInfo'] = $tmp;
		}
		return $result;
	}
	
	/**
	 * 获取订单状态
	 * @param int $flag
	 * @param string $booking_time
	 * @return string $status
	 */
	private function _getOrderQueueStatus($flag , $booking_time) {
		switch ($flag) {
			case OrderQueue::QUEUE_WAIT:
				//判定是否过期
				$time = time();
				$current_time = date('Y-m-d H:i:s' , $time);
				$booking_time = strtotime($booking_time);
				$h = date('H' , $booking_time);
				if (intval($h) < 7) {
					$expired_time = date("Y-m-d" , $booking_time)." 07:00:00";
				} else {
					$expired_time = date("Y-m-d" , ($booking_time+86400))." 07:00:00";
				}
				if ($current_time > $expired_time) {
					$status = OrderQueue::QUEUE_TYPE_FAILURED;
				} else {
				    $status = OrderQueue::QUEUE_TYPE_ACCEPTED;
				}
				break;
			case OrderQueue::QUEUE_WAIT_COMFIRM:
				$status = OrderQueue::QUEUE_TYPE_ORDERING;
				break;
			case OrderQueue::QUEUE_READY:
				$status = OrderQueue::QUEUE_TYPE_ORDERING;
				break;
			case OrderQueue::QUEUE_CANCEL:
				$status = OrderQueue::QUEUE_TYPE_CANCELED;
				break;
			case OrderQueue::QUEUE_SUCCESS:
				$status = OrderQueue::QUEUE_TYPE_ORDERED;
				break;
			default:
				$status = OrderQueue::QUEUE_TYPE_ORDERING;
				break;
		}
		return $status;
	}
	
	public function PushTest($params = array()) {
		if (in_array($params['driver_id'] , array('BJ9012'))) {
			$arr = array(
			    'type'=>GetuiPush::TYPE_MSG_DRIVER,
				'content'=>$params['content'],
				'level'=>1,  //级别
				'driver_id'=>$params['driver_id'],
		        'created'=>date('Y-m-d H:i:s' , time()),
			);
			$this->organize_message_push($arr);
		}
	}
}
