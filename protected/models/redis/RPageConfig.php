<?php
/**
 * 
 * 三周年客户回馈活动
 * @author cuiluzhe 2014-12-10
 *
 */
class RPageConfig extends CRedis {
       public $host='redis03n.edaijia.cn';
       public $port=6379;
       public $password='k74FkBwb7252FsbNk2M7';
       protected static $_models=array();

       public $shared = 'shared_';

       const EXPIRE_TIME = 604800;//有效期7天

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
     * 将分享过的订单放到缓存
     * @param $order_id
     * @return bool
     */
	public function setShared($order_id){
        $key = $this->shared.$order_id;
	    $this->redis->set($key , $order_id);
        $this->redis->expire($key, self::EXPIRE_TIME);
        return true;
	}

    /**
     * 判断这笔订单是否已经参与过分享
     * @param $order_id
     * @return bool
     */
	public function isShared($order_id){
        $key = $this->shared.$order_id;
        $order_id = $this->redis->get( $key);
        if($order_id){
            return true;
        }
        return false;
     }

}


