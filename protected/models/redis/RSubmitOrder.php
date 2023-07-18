<?php
/**
 * Created by vim.
 * User: lidingcai@edaijia-inc.cn
 * Date: 14-4-11
 * Time: 下午17:30
 */
class RSubmitOrder extends CRedis{
    public $host = 'redis03n.edaijia.cn'; 
    public $port = 6379;
    public $password = 'k74FkBwb7252FsbNk2M7';
    private $orderIdKeyPrefix='submit_order_id_';
    private $orderIdCashOnly='order_id_cash_only_';
    private $orderNumberKey='order_id_order_number_';

    public static function model($className = __CLASS__)
    {
                $model=null;
                if (isset(self::$_models[$className]))
                        $model=self::$_models[$className];
                else {
                        $model=self::$_models[$className]=new $className(null);
                }
                return $model;
    }

    /**
     * 增加orderId和orderNumber的关系
     * @param $orderId
     * @param $orderNumber
     */
   public function setOrderNumberByOrderId($orderId,$orderNumber){
       $key= $this->orderNumberKey.$orderId;
       $ret = $this->redis->setex($key, 60 * 24 * 7,$orderNumber);
       return $ret;
   }
    /**
    * 获得orderNumber通过orderid
   * @param $orderId
   */
    public function getOrderNumberByOrderId($orderId){
        $key= $this->orderNumberKey.$orderId;
        $orderNumber = $this->redis->get($key);
        return $orderNumber;
    }

    public function genOrderIdKey($orderId){
        return $this->orderIdKeyPrefix.$orderId;
    }

    public function setCashOnly($order_id){
	$key=$this->orderIdCashOnly.$order_id;
	$ret=$this->redis->set($key,1);
	$this->redis->expire($key,60*60*24*7);    
        return $ret;
    }
   
    public function getCashOnly($order_id){
	$key=$this->orderIdCashOnly.$order_id;
	$ret=$this->redis->get($key);
	return $ret;
    }
 
    //判断并且写入相应order_id入redis
    public function addOrderIdIfNotExist($orderId)
    {
        if (empty($orderId)) {
            return false;
        }
        $key = $this->genOrderIdKey($orderId);
        $ret = $this->redis->setnx($key, time());
        $this->redis->expire($key, 10 * 60);
        return $ret;
    }

    public function getOrderId($orderId){
        return $this->redis->get($this->genOrderIdKey($orderId));
    }
    //删除一个redis中的orderId,注意返回值是个整型，表示删除成功的key的数量
    public function delOrderId($orderId){	
	return $this->redis->del($this->genOrderIdKey($orderId));
    }
}
