<?php
/**
 * 
 * 三周年客户回馈活动
 * @author cuiluzhe 2014-12-10
 *
 */
class RCustomerFeedback extends CRedis {
       public $host='redis03n.edaijia.cn';
       public $port=6379;
       public $password='k74FkBwb7252FsbNk2M7';
       protected static $_models=array();

       public $max_id = 'feedback_maxid_';//处理到的客户id

       const EXPIRE_TIME = 2592000;//30天

       public static function model($className=__CLASS__) {
              $model=null;
              if (isset(self::$_models[$className]))
                     $model=self::$_models[$className];	
              else {	
                     $model=self::$_models[$className]=new $className(null);
              }	
              return $model;
       }	

	public function setMaxId($max){
	    $this->redis->set($this->max_id , $max);
        $this->redis->expire($this->max_id, self::EXPIRE_TIME);
        return true;
	}
	
	public function getMaxId(){
        $max = $this->redis->get($this->max_id);
        return !$max ? 0 : $max;
     }

}


