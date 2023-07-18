<?php
/**
 * 用户信息redis存储维护
 * User: Bidong
 * Date: 13-5-29
 * Time: 上午11:37
 * To change this template use File | Settings | File Templates.
 */

class CustomerStatus extends CRedis{

    public $host='redis01n.edaijia.cn'; //10.132.17.218
    public $port=6379;
    public $password='k74FkBwb7252FsbNk2M7';
    protected static $_models=array();


	private static $black_list_key='CUSTOMER_BLACK_LIST';

    public static function model($className=__CLASS__) {
        $model=null;
        if (isset(self::$_models[$className]))
            $model=self::$_models[$className];
        else {
            $model=self::$_models[$className]=new $className(null);
        }
        return $model;
    }

    public function getBlackList(){
        return $this->redis->hkeys(CustomerStatus::$black_list_key);
    }

    public function is_black($phone)
    {
        return $this->redis->hexists(CustomerStatus::$black_list_key, $phone);
    }

    public function add_black($phone)
    {
        return $this->redis->hset(CustomerStatus::$black_list_key, $phone, '1');
    }


    public function rm_black($phone)
    {
        return $this->redis->hdel(CustomerStatus::$black_list_key, $phone);
    }

    public function reload_black($phones)
    {
        // Atomic operations can not be guaranteed in this place, but who care?
        $this->redis->del(CustomerStatus::$black_list_key);
        foreach ($phones as $p) {
            $this->redis->hset(CustomerStatus::$black_list_key, $p, '1');
        }
    }


    public  function set($name,$customer_token ,$value) {
        if ($value!==null) {
            if (is_array($value)) {
                $value=json_encode($value);
            }
            $cacheKey='';
            switch ($name) {
                case 'token' :
                    //保存token
                    $cacheKey='CUSTOMER_TOKEN_'.$customer_token;
                    break;
                case 'profile' :
                    //用户信息
                    $cacheKey='CUSTOMER_PROFILE_'.$customer_token;
                    break;
            }
            $this->redis->set($cacheKey, $value);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 返回REDIS的信息
     * @param string $name
     */
    public function get($name,$customer_token) {
        if ($name!==null) {
            $cacheKey='';
            switch ($name) {
                case 'token' :
                    $cacheKey='CUSTOMER_TOKEN_'.$customer_token;
                    break;
                case 'profile' :
                    //用户信息
                    $cacheKey='CUSTOMER_PROFILE_'.$customer_token;
                    break;
            }
            if($cacheKey){
                if (!$this->redis->exists($cacheKey)) {
                    //没有取到，说明已经登出
                    return false;
                }else{
                    return $this->redis->get($cacheKey);
                }
            }
        }
    }

    /**
     * 删除客户REDIS
     */
    public function delete($name,$customer_token) {
        $cacheKey='';
        switch ($name) {
            case 'token' :
                $cacheKey='CUSTOMER_TOKEN_'.$customer_token;
                break;
            case 'profile' :
                //用户信息
                $cacheKey='CUSTOMER_PROFILE_'.$customer_token;
                break;
        }
        if($cacheKey){
            if ($this->redis->exists($cacheKey)) {
               return $this->redis->delete($cacheKey);
            }else{
                return false;
            }
        }
    }

    public function reload($customer_token=null) {

    }

    //异步提交数
    public function upLoad($customer_token){

    }

    /**
     * LOAD 客户的redis信息
     * @author bidong
     */
    private function load($customer_token) {

    }


    /**
     * 客户端注册缓存
     * @param $phone
     * @param string $udid
     * @return null
     */
    public function getRegisterUidiCache($phone ,$udid=""){

        $key = "REGISTER_UDID_CACHE_".$phone;

        if ($udid) {
        	$this->redis->del($key);
            $this->redis->set($key, $udid);
            $this->redis->expire($key, 60*60*2);
        }
        return $this->redis->get($key);


    }

}