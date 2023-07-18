<?php

/**
 * 活动缓存
 * clz
 */
class RActivity extends CRedis
{
   /*public $host = 'activityredis.edaijia.cn'; //activityredis.edaijia.cn(10.168.248.208)
    public $port = 22121;*/
   public $host = 'activity-redis02.edaijia-inc.cn'; //10.161.174.78 redis02n.edaijia.cn
    public $port = 22121;
    //public $password = 'k74FkBwb7252FsbNk2M7';
    protected static $_models = array();

    private $expire_time = 5184000;//缓存2个月

    const ACT_SUCCESS = 1;//领取成功
    const ACT_BINDING = 2;//已领取过
    const ACT_EXPIRED = 3;//活动结束

    private $verify_code = 'code';//验证码的key
    private $open_id_key = 'open_id';//open_id的key
    private $phone_key = 'phone';//手机号的key
    private $lock_key = '_lock';//锁的key
    private $left_num_key = '_left_num';//剩余数量

    private $activity_config_key_prefix  = 'activity_config_';

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
     * @param $phone 手机号
     * @param $code 验证码
     * @param $act_name 活动名称
     * @return bool
     */
    public function setCode($act_name, $phone, $code)
    {
        $key = $act_name.'_'.$this->verify_code.'_'.$phone;
        $ret = $this->redis->set($key, $code);
        if(!$ret){
            return false;
        }
        $this->redis->expire($key, 300);//设置缓存300秒
        return true;
    }

    /**
     * 获取验证码
     * @param $phone
     * @return bool|int
     */
    public function getCode($act_name, $phone)
    {
        $key = $act_name.'_'.$this->verify_code.'_'.$phone;
        $code = $this->redis->get($key);
        if(!$code){
            return false;
        }
        return $code;
    }

    /**
     * 获取微信39元立减活动剩余优惠劵数目
     * @param $act_name
     * @return bool
     */
    public function getLeftNum($act_name)
    {
        $key = $act_name.$this->left_num_key;
        $num = $this->redis->get($key);
        if(!$num){
           /* $init_num = 5;//初始化优惠劵数目
            $binding_num = Bonus39Log::model()->count('status=:status', array(':status'=>1));
            if(!$binding_num){
                $binding_num = 0;
            }
            $left_num = $init_num-$binding_num;
            $this->redis->set($key, $left_num);
            return $left_num;*/
            return 0;
        }
        return $num;
    }

    /**
     * 判断open_id或者手机号是否参加过活动
     * @param $act_name
     * @param $key  open_id或者phone
     * @return mixed
     */
    public function existsBind($act_name, $key, $type)
    {
        if($type == 'open_id'){
            $key = $act_name.'_'.$this->open_id_key.'_'.$key;
        }else{//phone
            $key = $act_name.'_'.$this->phone_key.'_'.$key;
        }
        $ret = $this->redis->exists($key);
        return $ret;
    }

    /**
     * 将缓存中的数目减一
     * @param $act_name
     * @return mixed
     */
    public function getFromCache($act_name)
    {
        $key = $act_name.$this->left_num_key;
        return $this->redis->decr($key);
    }

    /**
     * 将绑定信息存到缓存
     * @param $act_name
     * @param $open_id
     * @param $phone
     * @return mixed
     */
    public function setBindPhone($act_name,$type,$open_id,$phone)
    {
        if($type == 'open_id'){
            $key = $act_name.'_'.$this->open_id_key.'_'.$open_id;
            $ret = $this->redis->set($key,$phone);
        }else{//phone
            $key = $act_name.'_'.$this->phone_key.'_'.$phone;
            $ret = $this->redis->set($key,$phone);
        }
        return $ret;
    }

    /**
     * 获取手机号
     * @param $act_name
     * @param $open_id
     * @return mixed
     */
    public function getBindPhone($act_name, $open_id)
    {
       // $key = $act_name.'_'.$open_id;
        $key = $act_name.'_'.$this->open_id_key.'_'.$open_id;
        $ret = $this->redis->get($key);
        return $ret;
    }

    public function setLock($act_name, $phone)
    {
        $key = $act_name.$this->lock_key.'_'.$phone;
        $ret = $this->redis->setnx($key,1);
        return $ret;
    }

    public function resetActivity($actName, $initNum, $phone){
        $leftNumkey = $actName.$this->left_num_key;
        $this->redis->set($leftNumkey, $initNum);
        $inKey = $actName.$this->lock_key.'_'.$phone;

        $this->redis->del($inKey);
    }

    /**
     * @param $act_name
     * @param $phone
     * @return mixed
     * 判断是否参加过活动
     */
    public function isAttend($act_name, $phone)
    {
        $key = $act_name.$this->lock_key.'_'.$phone;
        $ret = $this->redis->get($key);
        if($ret){
            return true;
        }
        return false;
    }

    /**
     * @param $act_name
     * @param $phone
     * @return mixed
     * 立减39元活动记录给这个手机号发送过短信
     */
    public function phoneHasSend($act_name,$phone){
        $key = $act_name.'_record_'.$phone;
        $ret = $this->redis->set($key,1);
        return $ret;
    }

    /**
     * @param $act_name
     * @param $phone
     * @return mixed
     * 判断是否给这个手机号发过短信
     */
    public function existsPhoneSendRecord($act_name, $phone)
    {
        $key = $act_name.'_record_'.$phone;
        $ret = $this->redis->exists($key);
        return $ret;
    }

    /**
     * @param
     */
    public function getSendNum($type){
        /*if($type == 1){
            $key = 'weixin_39_act';
        }else if ($type == 2){
            $key = 'weixin_39_h5';
        }else if ($type == 3){
            $key = 'weixin_59_my';
        }else if($type == 4){
            $key = 'weixin_59_other';
        }*/
        $key = $type;
        return $this->redis->incr($key);
    }

    /**
     * 清理该模板活动redis缓存
     * @param $act_name
     * @param $phone(以防没存到db)
     * @param $init_num(以优惠劵初始数量，当活动没这个限制的时候可以设置大一点)
     */
    public function delCache($act_name,$phone,$init_num){
        $log = new Bonus39Log();
        $logs = Bonus39Log::model()->findAll('act_name=:act_name',array(':act_name'=>$act_name));
        if(!$logs){
            echo $act_name.' 缓存为空'.PHP_EOL;
        }else{
            foreach($logs as $log){
                if(!empty($log['phone'])){
                    $key = $act_name.'_'.$this->phone_key.'_'.$log['phone'];
                    $this->redis->del($key);
                    $key = $act_name.$this->lock_key.'_'.$phone;
                    $this->redis->del($key);
                    $key = $act_name.'_record_'.$phone;
                    $this->redis->del($key);
                    echo $act_name.'删除了phone='.$phone.' 的缓存'.PHP_EOL;
                }
                if(!empty($log['open_id'])){
                    $key = $act_name.'_'.$this->open_id_key.'_'.$log['open_id'];
                    $this->redis->del($key);
                    echo $act_name.'删除了open_id='.$log['open_id'].' 的缓存'.PHP_EOL;
                }
            }
        }
        if(!empty($phone)){
            $key = $act_name.'_'.$this->phone_key.'_'.$log['phone'];
            $this->redis->del($key);
            $key = $act_name.$this->lock_key.'_'.$phone;
            $this->redis->del($key);
            $key = $act_name.'_record_'.$phone;
            $this->redis->del($key);
            echo $act_name.' 删除了phone='.$phone.' 的缓存'.PHP_EOL;
        }
        echo $act_name.'开始设置初始化优惠劵数目'.PHP_EOL;
        $key = $act_name.$this->left_num_key;
        $this->redis->set($key,$init_num);
        echo $act_name.'结束设置初始化优惠劵数目'.PHP_EOL;

        echo $act_name.'开始设置活动开关'.PHP_EOL;
        $this->redis->set($act_name,1);
        echo $act_name.'结束设置活动开关'.PHP_EOL;
    }

    /**
     * 设置活动打开
     * @param $key
     * @return mixed
     */
    public function getOpenLock($key){
        return $this->redis->exists($key);
    }

    /**
     * 关闭活动
     * @param $key
     * @return mixed
     */
    public function removeOpenLock($key){
        return $this->redis->del($key);
    }

    /**
     *
     * 从 redis 拿配置
     *
     * @param $activityName
     * @return mixed
     */
    public function getActivityConfig($activityName){
        $key = $this->activityConfigKeyGen($activityName);
        return $this->redis->get($key);
    }

    /**
     * 设置 redis 的配置
     *
     * @param $activityName
     * @param array $config
     */
    public function setActivityConfig($activityName, $config = array()){
        $key = $this->activityConfigKeyGen($activityName);
        $this->redis->set($key, json_encode($config));
    }

    /**
     *
     * 删掉 redis 里面的配置
     *
     *
     * @param $activityName
     */
    public function delActivityConfig($activityName){
        $this->redis->del($this->activityConfigKeyGen($activityName));
    }

    /**
     * 生成活动配置的 key
     *
     * @param $activityName
     * @return string
     */
    public function activityConfigKeyGen($activityName)
    {
        return $this->activity_config_key_prefix . $activityName;
    }
    /*
     * 缓存活动
     */
    public function addActivity($key,$expireTime){
        $ret = $this->redis->set($key,1);
        if(!$ret){
            return false;
        }
        $this->redis->expire($key, $expireTime);//设置缓存300秒
        return true;
    }

    public function existActivity($key){
        $ret = $this->redis->exists($key);
        return $ret;
    }
    /**
     * 设置活动初始优惠劵数目
     * @param $act_name
     * @param $num
     */
    public function setLeftBonusNum($act_name, $num){
        EdjLog::info($act_name.'开始设置初始化优惠劵数目');
        $key = $act_name.$this->left_num_key;
        $ret = $this->redis->set($key,$num);
        if($ret){
            EdjLog::info($act_name.'将优惠劵数目初始化为'.$num);
        }else{
            EdjLog::info($act_name.'设置初始化优惠劵数目失败');
        }
        return $ret;
    }

    public function delAct($act_name){
        EdjLog::info($act_name.'开始删除redis中的活动');
        $key = 'act_english_name_'.$act_name;
        $ret = $this->redis->del($key);
        EdjLog::info($act_name.'结束删除redis中的活动');
    }

}
