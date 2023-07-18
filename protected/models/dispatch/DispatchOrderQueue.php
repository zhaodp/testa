<?php
/**
 * 取派单
 * User: zhanglimin
 * Date: 13-6-19
 * Time: 下午3:19
 */
class DispatchOrderQueue{

    //设置排序权重
    private $_weight_sort = array(
        'vip' => 10,
    );

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
     * 获取等待派的单
     * @param $booking_time
     * @param int $limit
     * @return array
     */
    public function getWaitFormQueue($booking_time, $limit = 200 , $p = ''){
    	
        $ret = array();
        $result = $this->_getOrderQueue($booking_time , $limit , $p);
        if(empty($result)){
            return $ret;
        }
        foreach($result as $val){
            $tmp['number'] = $val['number'] - $val['dispatch_number'];
            if($tmp['number'] == 0 ){
                //如己派完则跳出
                continue;
            }else{
                $order = Push::model()->getDispatchOrder($val['id']);
                if(!empty($order)){
                    $tmp['queue_id'] = $val['id'];
                    $tmp['dispatch_number'] = $val['dispatch_number'];
                    $tmp['address'] = $val['address'];
                    $tmp['booking_time'] = $val['booking_time'];
                    $tmp['phone'] = $val['phone'];
                    $tmp['city_id'] = $val['city_id'];

                    $ret[$val['id']] = $tmp;
                    $ret[$val['id']]['push_order'] = $order;
                }else{
                    //没有要派的订单，跳出
                    continue;
                }

            }
        }
        //将订单组合成一维数组
        $ret = $this->_orderToArray($ret);
        //排序规则
        $ret = $this->_ruleSort($ret);
        return $ret;
    }
    
    /**
     * 获取vip等待派的单
     * @param $booking_time
     * @param int $limit
     * @return array
     */
    public function getVipWaitFormQueue($booking_time, $limit = 200 , $p = ''){
    	
        $ret = array();
        $result = $this->_getVipOrderQueue($booking_time , $limit , $p);
        if(empty($result)){
            return $ret;
        }
        foreach($result as $val){
            $tmp['number'] = $val['number'] - $val['dispatch_number'];
            if($tmp['number'] == 0 ){
                //如己派完则跳出
                continue;
            }else{
                $order = Push::model()->getDispatchOrder($val['id']);
                if(!empty($order)){
                    $tmp['queue_id'] = $val['id'];
                    $tmp['dispatch_number'] = $val['dispatch_number'];
                    $tmp['address'] = $val['address'];
                    $tmp['booking_time'] = $val['booking_time'];
                    $tmp['phone'] = $val['phone'];

                    $ret[$val['id']] = $tmp;
                    $ret[$val['id']]['push_order'] = $order;
                }else{
                    //没有要派的订单，跳出
                    continue;
                }

            }
        }
        //将订单组合成一维数组
        $ret = $this->_orderToArray($ret);
        //排序规则
        $ret = $this->_ruleSort($ret);
        return $ret;
    }
    
    /**
     * 获取一键下单等待派的单
     * @param $booking_time
     * @param int $limit
     * @return array
     */
    public function getWaitFormQueueByChannel($booking_time,$limit = 200,$p=''){
        $ret = array();
        $result = $this->_getOrderQueueByChannel($booking_time,$limit,$p);
        if(empty($result)){
            return $ret;
        }
        $time = time();
        foreach($result as $val){
        	//之前会多生成订单 一键下单的单子job在 下单30s后再派单---下单的时候就已经派了一次
        	/*$time_plus = abs($time-strtotime($val['created']));
        	if ($time_plus < 30) {
        		continue;
        	}
		*/
        	
            $tmp['number'] = $val['number'] - $val['dispatch_number'];
            if($tmp['number'] == 0 ){
                //如己派完则跳出
                continue;
            }else{
                $order = Push::model()->getDispatchOrder($val['id']);
                if(!empty($order)){
                    $tmp['queue_id'] = $val['id'];
                    $tmp['dispatch_number'] = $val['dispatch_number'];
                    $tmp['address'] = $val['address'];
                    $tmp['booking_time'] = $val['booking_time'];
                    $tmp['phone'] = $val['phone'];
                    $tmp['city_id'] = $val['city_id'];

                    $ret[$val['id']] = $tmp;
                    $ret[$val['id']]['push_order'] = $order;
                }else{
                    //没有要派的订单，跳出
                    continue;
                }

            }
        }
        //将订单组合成一维数组
        $ret = $this->_orderToArray($ret);
        //排序规则
        $ret = $this->_ruleSort($ret);
        return $ret;
    }
    
    
    
    /**
     * 要派的vip单
     * @param $booking_time
     * @param int $limit
     * @param int $p = 1,2,3,4,5(可以不传,不传取所有)
     * @return array
     */
    private  function _getVipOrderQueue($booking_time , $limit = 100 , $p = '') {
    	$begin_time = date('Y-m-d H:i:s');
    	
    	$orderby_string = ( time() % 2 ) ? 'id asc' : 'id desc';
    	
        $ret = array();
        //获取状态为1的queue list
        if (empty($p)) {
        	// Yii::app()->db change into OrderQueue::getDbMasterConnection()
        	$ret = OrderQueue::getDbMasterConnection()->createCommand()
                  ->select('id,city_id,number,dispatch_number,address,booking_time,lng,lat,phone,created')
                  ->from('t_order_queue')
                  ->where('flag=1 and booking_time between :begin_time and :end_time and is_vip=1 and channel not in("01001" , "01002" , "01003" ) ' , array(
                      ':begin_time' => $begin_time,
                      ':end_time' => $booking_time,
                  ))
                  ->order($orderby_string)
                  ->limit($limit)
                  ->queryAll();
        } else {
        	$p = abs(intval($p) - 1);
        	// Yii::app()->db change into OrderQueue::getDbMasterConnection()
        	$ret = OrderQueue::getDbMasterConnection()->createCommand()
                  ->select('id,city_id,number,dispatch_number,address,booking_time,lng,lat,phone,created')
                  ->from('t_order_queue')
                  ->where('flag=1 and mod(id,5)=:p and booking_time between :begin_time and :end_time and is_vip=1 and channel not in("01001" , "01002" , "01003") ' , array(
                      ':p' => $p,
                      ':begin_time' => $begin_time,
                      ':end_time' => $booking_time,
                  ))
                  ->order($orderby_string)
                  ->limit($limit)
                  ->queryAll();
        }
        if(empty($ret)){
            return $ret;
        }
        return $ret;
    }
    

    /**
     * 要派的单
     * @param $booking_time
     * @param int $limit
     * @param int $p = 1,2,3,4,5(可以不传,不传取所有)
     * @return array
     */
    private  function _getOrderQueue($booking_time , $limit = 100 , $p = '') {
    	$begin_time = date('Y-m-d H:i:s');
    	
    	$orderby_string = ( time() % 2 ) ? 'id asc' : 'id desc';
    	//$orderby_string =  'id asc';
    	
        $ret = array();
        //获取状态为1的queue list
        if (empty($p)) {
        	$sql = 'select id,city_id,number,dispatch_number,address,booking_time,lng,lat,phone,created,channel,is_vip from t_order_queue where flag=1 and booking_time between :begin_time and :end_time and channel not in("01001" , "01002" , "01003" , "01006", "01007") and phone not in("13611126764" , "18911883373") order by '.$orderby_string.' limit '.$limit;
        	$ret = OrderQueue::getDbMasterConnection()->createCommand($sql)->queryAll(true , array(':begin_time'=>$begin_time,':end_time'=>trim($booking_time)));
        } else {
        	
        	$p = abs(intval($p) - 1);
        	$sql = 'select id,city_id,number,dispatch_number,address,booking_time,lng,lat,phone,created,channel,is_vip from t_order_queue where flag=1 and mod(id,5)=:p and booking_time between :begin_time and :end_time and channel not in("01001" , "01002" , "01003", "01006", "01007") and phone not in("13611126764" , "18911883373") order by '.$orderby_string.' limit '.$limit;
        	$ret = OrderQueue::getDbMasterConnection()->createCommand($sql)->queryAll(true , array(':p'=>$p,':begin_time'=>$begin_time,':end_time'=>trim($booking_time)));
        }
        if(empty($ret)){
            return $ret;
        }
        return $ret;
    }
    
    /**
     * 要派的单
     * @param $booking_time
     * @param int $limit
     * @return array
     */
    private  function _getOrderQueueByChannel($booking_time , $limit=200,$p='') {
        $ret = array();
        
        $begin_time = date('Y-m-d H:i:s');
    	
    	$orderby_string = ( time() % 2 ) ? 'id asc' : 'id desc';
    	
        $ret = array();
        //获取状态为1的queue list
        if (empty($p)) {
        	// Yii::app()->db change into OrderQueue::getDbMasterConnection()
        	$ret = OrderQueue::getDbMasterConnection()->createCommand()
                  ->select('id,city_id,number,dispatch_number,address,booking_time,lng,lat,phone,created')
                  ->from('t_order_queue')
                  ->where('flag=1 and booking_time between :begin_time and :end_time and channel in("01002" , "01003" , "01006", "01007") and phone not in("13611126764" , "18911883373")' , array(
                      ':begin_time' => $begin_time,
                      ':end_time' => $booking_time,
                  ))
                  ->order($orderby_string)
                  ->limit($limit)
                  ->queryAll();
        } else {
        	$p = abs(intval($p) - 1);
        	// Yii::app()->db change into OrderQueue::getDbMasterConnection()
        	$ret = OrderQueue::getDbMasterConnection()->createCommand()
                  ->select('id,city_id,number,dispatch_number,address,booking_time,lng,lat,phone,created')
                  ->from('t_order_queue')
                  ->where('flag=1 and mod(id,5)=:p and booking_time between :begin_time and :end_time and channel in( "01002" , "01003" , "01006", "01007") and phone not in("13611126764" , "18911883373")' , array(
                      ':p' => $p,
                      ':begin_time' => $begin_time,
                      ':end_time' => $booking_time,
                  ))
                  ->order($orderby_string)
                  ->limit($limit)
                  ->queryAll();
        }
        if(empty($ret)){
            return $ret;
        }
        return $ret;
        
    }

    /**
     * 排序规则
     * @param array $data
     * @return array
     */
    private function _ruleSort($data = array()){
        if(empty($data)){
            return array();
        }

        //VIP排序
        $result = $this->_vipSort($data);


        //设置权重属性
        $result = $this->_setWeightAttr($result);
        //通过权重来排序
        $result = Common::array_sort($result);

        return $result;

    }

    /**
     * VIP排序
     * @param array $data
     * @return array
     */
    private function _vipSort($data = array()){
        if(empty($data)){
            return array();
        }
        $result = array();
        foreach($data as $key=>$val){
            $result[$key] = $val;
            $is_vip = VipPhone::model()->getPrimary($val['phone']);
            $weight = empty($is_vip) ? 0 :  $this->_weight_sort['vip'];
            //设置权重属性
            if(isset($result[$key]['weight'])){
                $result[$key]['weight'] +=  $weight ;
            }else{
                $result[$key]['weight'] =  $weight ;
            }
        }
        return $result;
    }

    /**
     * 设置权重
     * @param array $data
     * @return array
     */
    private function _setWeightAttr($data  = array()){
        if(empty($data)){
            return array();
        }
        foreach($data as &$val){
            $val['weight'] = $val['weight'].strtotime($val['booking_time']);
        }
        return $data;

    }

    /**
     * 将订单组合成一维数组
     * @param $data
     * @return array
     */
    private function _orderToArray($data){
        if(empty($data)){
            return array();
        }
        $list = array();
        foreach($data as $rows){
           if(!empty($rows['push_order'])){
               foreach($rows['push_order'] as $order){
                   $list[] = array(
                     'queue_id'=>$order['queue_id'],
                     'order_id'=>$order['order_id'],
                     'map_id'=>$order['map_id'],
                     'address'=>$rows['address'],
                     'booking_time'=>$rows['booking_time'],
                     'phone'=>$rows['phone'],
                     'city_id' => isset($rows['city_id']) ? $rows['city_id'] : null,
                   );
               }
           }
        }
        return $list;
    }

    /**
     * 派单失败的直接将order_queue状态置为完成
     * @author AndyCong<congming@edaijia-staff.cn>
     * @return boolean
     */
    public function dispatchFailedProcess() {
    	//取消order_queue
    	$expired_time = date('Y-m-d H:i:s' , time()-2*60*60);
    	$sql = " select id from t_order_queue  where flag = 1 and (channel = '01001' or phone = '13611126764') and booking_time <= :expired_time order by id desc limit 200";
    	// Yii::app()->db_readonly change into OrderQueue::getDbReadonlyConnection()
    	$queues  = OrderQueue::getDbReadonlyConnection()->createCommand($sql)->queryAll(true , array(':expired_time' => $expired_time));

    	if (!empty($queues)) {
    		foreach ($queues as $queue) {
    			echo "\r\n--- cancel queue_id is ".$queue['id']." ---\r\n";
    			// Yii::app()->db change into OrderQueue::getDbMasterConnection()
    			OrderQueue::getDbMasterConnection()->createCommand()->update('t_order_queue', array('flag' => OrderQueue::QUEUE_CANCEL), 'id=:id',array(':id'=>$queue['id']));
    		}
    	}
    	
    	//取消order
    	for ($i=0; $i < 20 ; $i++) {
	    	$booking_time = time()-2*60*60;
	    	$sql = "select order_id,phone from t_order where status = :status and driver_id = :driver_id and booking_time < :booking_time order by order_id desc limit 200";
	    	$orders = Order::getDbReadonlyConnection()->createCommand($sql)->queryAll(true , array(
	    	    ':status' => Order::ORDER_READY,
	    	    ':driver_id' => Push::DEFAULT_DRIVER_INFO,
	    	    ':booking_time' => $booking_time,
	    	));
	    	if (!empty($orders)) {
	    		foreach ($orders as $order) {
	    			echo "\r\n--- cancel order_id is ".$order['order_id']." ---\r\n";
	    			Order::getDbMasterConnection()->createCommand()->update('t_order', array('status' => Order::ORDER_NO_DISPATCH_CANCEL), 'order_id=:order_id',array(':order_id'=>$order['order_id']));
	    			BonusLibrary::model()->BonusUsed($order['phone'], $order['order_id'] , 0, 2);
	    		}
	    	} else {
	    		break;
	    	}
    	}
    	return true;
    }
    
    /**
     * 获取等待派的单（自动派单临时优化）
     * @param $booking_time
     * @param int $limit
     * @return array
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-13
     */
    public function getDispatchOrders($booking_time, $limit = 200 , $p = ''){
    	//获取Queue数据
        $ret = array();
        $result = $this->_getOrderQueue($booking_time , $limit , $p);
        if(empty($result)){
            return $ret;
        }
        
        foreach($result as $val){
        	//验证是否需要弹回
        	$change_flag = $this->_isChangeOrderFlag($val['booking_time'] , $val['created'] , $val['id']);
        	if($change_flag['flag']){
		    	QueueApiOrder::model()->queue_lock($val['id']);
		        echo "\n D|长时间未派出弹回|".$val['booking_time']."|".$val['id']." \n";
		        continue;
		    }
		    
		    //计算要派订单个数
            $tmp['number'] = $val['number'] - $val['dispatch_number'];
            if($tmp['number'] == 0 ){  //如己派完则跳出
                continue;
            }
            
            //获取要派订单
            $order = Push::model()->getDispatchOrder($val['id']);
            if (empty($order)) { //没有要派订单就退出
            	continue;
            }
            $tmp['queue_id'] = $val['id'];
            $tmp['dispatch_number'] = $val['dispatch_number'];
            $tmp['address'] = $val['address'];
            $tmp['booking_time'] = $val['booking_time'];
            $tmp['phone'] = $val['phone'];
            //后添加字段
            $tmp['city_id'] = $val['city_id'];
            $tmp['channel'] = $val['channel'];
            $tmp['is_vip'] = $val['is_vip'];
            $tmp['lng'] = $val['lng'];
            $tmp['lat'] = $val['lat'];

            $ret[$val['id']] = $tmp;
            $ret[$val['id']]['push_order'] = $order;
        }
        //将订单组合成一维数组
        $ret = $this->_orderAddInArray($ret);
        //排序规则
        $ret = $this->_setSort($ret);
        return $ret;
    }
    
    /**
     * 验证订单是否需要弹回（临时优化自动派单）
     * @param string $booking_time
     * @param string $created
     * @param int $queue_id
     * @return array $ret
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-13
     */
    private function _isChangeOrderFlag($booking_time,$created , $queue_id){
        $ret= array(
          'flag' => false,
          'msg'=>"",
        );
        //预约时间超过30分钟 提前20分钟弹回
        if( strtotime($booking_time)  - strtotime($created)  > 2400 ){
            if(( strtotime($booking_time) - time() ) < 1200 ){
            	$task = array(
            	    'method' => 'set_queue_handle',
            	    'params' => array(
            	        'queue_id' => $queue_id,
            	    ),
            	);
            	Queue::model()->putin($task , 'dalorder');
                $ret['flag'] = true;
                $ret['msg'] = "订单时间小于20分钟,己变了手动派单";
            }
        }else{ //预约时间小于30分钟 提前10分钟弹回
            if(( strtotime($booking_time) - time() ) < 600 ){
                $task = array(
            	    'method' => 'set_queue_handle',
            	    'params' => array(
            	        'queue_id' => $queue_id,
            	    ),
            	);
            	Queue::model()->putin($task , 'dalorder');
                $ret['flag'] = true;
                $ret['msg'] = "订单时间小于10分钟,己变了手动派单";
            }
        }
        return $ret;
    }
    
    /**
     * 将订单组合成一维数组
     * @param $data
     * @return array
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-13
     */
    private function _orderAddInArray($data){
        $list = array();
        foreach($data as $rows){
           foreach($rows['push_order'] as $order){
               $list[] = array(
                 'queue_id'=>$order['queue_id'],
                 'order_id'=>$order['order_id'],
                 'map_id'=>$order['map_id'],
                 'address'=>$rows['address'],
                 'booking_time'=>$rows['booking_time'],
                 'phone'=>$rows['phone'],
                 //后添加字段
                 'city_id'=>$rows['city_id'],
                 'channel'=>$rows['channel'],
                 'is_vip'=>$rows['is_vip'],
                 'lng'=>$rows['lng'],
                 'lat'=>$rows['lat'],
               );
           }
        }
        return $list;
    }
    
    /**
     * 排序规则（临时派单优化）
     * @param array $data
     * @return array
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-13
     */
    private function _setSort($data = array()){
        //VIP排序
        $result = $this->_vipPriority($data);
        //设置权重属性
        $result = $this->_setWeightAttr($result);
        //通过权重来排序
        $result = Common::array_sort($result);
        return $result;
    }
    
    /**
     * VIP排序（临时派单优化）
     * @param array $data
     * @return array
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-13
     */
    private function _vipPriority($data = array()){
        $result = array();
        foreach($data as $key=>$val){
            $result[$key] = $val;
            $weight = ($val['is_vip'] == 1) ? $this->_weight_sort['vip'] : 0 ;
            //设置权重属性
            if(isset($result[$key]['weight'])){
                $result[$key]['weight'] +=  $weight ;
            }else{
                $result[$key]['weight'] =  $weight ;
            }
        }
        return $result;
    }
}
