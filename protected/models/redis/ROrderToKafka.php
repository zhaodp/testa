<?php
/*
 * 订单生产，往redis队列放。
 */
class ROrderToKafka extends CRedis {
	public $host='kafkaredis.edaijia-inc.cn'; //

	public $port=6379;
	public $password='k74FkBwb7252FsbNk2M7';
    public $queueName = "queue:orderStatus:data";

	protected static $_models=array();

	public static function model($className=__CLASS__) {
		$model=null;
		if (isset(self::$_models[$className]))
			$model=self::$_models[$className];
		else {
			$model=self::$_models[$className]=new $className(null);
		}
		return $model;
	}

	public function createOrderAddQueue($orderNum=0){
        if(empty($orderNum)){
            EdjLog::info("Create order to queue, order_number invalid.");
            return;
        }
        EdjLog::info("Create order to queue, order_number:".$orderNum);
        $bean = ROrder::model()->getOrder($orderNum);
        if(!$bean){
            EdjLog::info("Create order to queue, order_number:".$orderNum.", redis empty,query db.");
            $bean = Order::model()->queryOrder($orderNum);
        }
        if(!$bean){
            EdjLog::info("Create order to queue, order_number:".$orderNum.", redis and db empty,return.");
            return false;
        }
        $orderId = isset($bean["order_id"])?$bean["order_id"]:"";
        if(empty($orderId)){
            EdjLog::info("Create order to queue, order_number:".$orderNum.", order id invalid.");
            return;
        }
        $json = array();
        $json["id"] = $orderId;
        $json["redis_insert_time"] = floor(1000 * microtime(true));
        $json["order"]=$bean;
        EdjLog::info("Create order to queue:".json_encode($json));
        $this->addRQueue($json);
    }

    public function addRQueue($json=array()){
        $return  = $this->redis->rPush($this->queueName, json_encode($json));
    }

}
