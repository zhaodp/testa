<?php

/**
 * RMaliciousOrder 恶意下单处理 
 * @date: 2015-03-16
 * @time: 下午2:41
 * @auther yuchao 
 */
class RMaliciousOrder extends RedisHAProxy
{
    protected static $_models = array();

    //司机恶意数据
    private $driver = 'Mali_dr_';
    //客户恶意数据
    private $customer = 'Mali_cu_';

    //恶意判断时间, 7天
    private $ttltime = 604800; 
    //判断条件 3次
    private $times = 3;

    public static function model($className = __CLASS__)
    {
        $model = null;
        if (isset(self::$_models[$className]))
            $model = self::$_models[$className];
        else {
            $model = self::$_models[$className] = new $className(null);
        }
        return $model;
    }

    /**
     * 判断客户是不是恶意用户
     * @param $people_id
     * @return array
     * @auther yuchao 
     */
    public function isMaliCustomer($people_id)
    {
        $time = time();
        $key_id = $this->customer. $people_id;
        if (!$this->redis->exists($key_id)) {
            $this->redis->lpush($key_id, $time);
            return false;
        }else{
          $this->redis->lpush($key_id, $time);
          $newtimes = $this->adjustList($key_id, $time);
          if($newtimes>=$this->times){
            return true;
          } else {
            return false;
          }
        }
    }

    /**
     * 判断司机是不是恶意用户 
     * @param $driver_id
     * @return array 
     * @auther yuchao 
     */
    public function isMaliDriver($driver_id)
    {
        $time = time();
        $key_id = $this->driver. $driver_id;
        if (!$this->redis->exists($key_id)) {
            $this->redis->lpush($key_id, $time);
            return false;
        }else{
          $this->redis->lpush($key_id, $time);
          $newtimes = $this->adjustList($key_id, $time);
          if($newtimes>= $this->times){
            return true;
          } else {
            return false;
          }
        }
    }

    /**
     * 获取redis中存储的恶意次数;
     * @param $key_id
     * @return int
     * @author yuchao
     */
    public function getTimes($key_id)
    {
        $times = 0;//恶意次数
        if(!$this->redis->exists($key_id))
        {
          return $times;
        }else{
            $times = $this->redis->lsize($key_id);
            if(!empty($times)){
              return $times;
            }else{
              return false;
            }
        }
    
    }

    /**
     * 是恶意计数增加,计入redis代码
     * @param $code_id
     * @return array
     * @author yuchao
     */
    public function adjustList($key_id, $time="")
    {
        $times = 0;//恶意次数
        if(empty($time)){
            $time = time();
        }
        if(!$this->redis->exists($key_id))
        {
            $this->redis->lpush($key_id,$time);
            $times = $this->redis->lsize($key_id);

        }else{
            $listlength = $this->redis->lsize($key_id);
            if($listlength > 0){
              $lists = $this->redis->lrange($key_id,0,-1);
              $this->redis->del($key_id);
              foreach($lists as $timeitem){
                $difftime = $time - $timeitem;
                if($difftime <= $this->ttltime){
                  $this->redis->lpush($key_id, $timeitem);
                }
              }
              $expire_time = $this->ttltime;
              $this->redis->expire($key_id,intval($expire_time));
            }
            $times = $this->redis->lsize($key_id);
            if(!empty($times)){
              return $times;
            }else{
              return false;
            }
            
        }
        return $times;

    }


}
