<?php
/**
 * 设置用户登录
 * User: zhanglimin
 * Date: 13-8-19
 * Time: 下午1:29
 */


class UserToken extends CRedis {
    public $host='redis01n.edaijia.cn'; //10.132.17.218
    public $port=6379;
    public $password='k74FkBwb7252FsbNk2M7';

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



    /**
     * 获取验证码
     */
    public function getSmsPassCode($phone=""){
        $ret = "";
        if(empty($phone)){
            return $ret;
        }
        $ret = $this->passCodeCache($phone);
        return $ret;
    }


    /**
     * 生成验证码
     * @return int
     */
    private function createCode(){
        $code = rand(1000, 9999);//生成验证码
        return $code;
    }


    /**
     * 设置验证码缓存 10分钟过期
     * @param $phone
     * @param $flag
     * @return mixed
     */
    private function passCodeCache($phone,$flag = true){

        $code = $this->createCode();

        $cache_key = 'prelogin_restaurant_'.md5($phone);

        if ($flag) {
            if (!$this->redis->exists($cache_key)) {
                $this->redis->set($cache_key, $code);
                $this->redis->expire($cache_key, 600);
            }
        }
        return $this->redis->get($cache_key);
    }

    /**
     * 删除缓存
     * @param $phone
     */
    public function deletePassCodeCache($phone){
        $cache_key = 'prelogin_restaurant_'.md5($phone);
        if ($this->redis->exists($cache_key)) {
            $this->redis->delete($cache_key);
        }
    }

    /**
     * 获取token
     * @param $phone
     * @return string
     */
    public function getToken($phone){
        $token = md5(time().$phone);
        $token = md5($token.$phone.time());
        return $token;
    }


}