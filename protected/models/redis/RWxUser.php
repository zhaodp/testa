<?php
/**
 * 管理customer的redis存储
 *
 * @author aiguoxin
 *
 *
 */
class RWxUser extends CRedis
{

    /*public $host='redis01n.edaijia.cn'; //10.132.17.218
    public $port=6379;
    public $password='k74FkBwb7252FsbNk2M7';*/
    public $host = 'activityredis.edaijia.cn'; //activityredis.edaijia.cn(10.168.248.208)
    public $port = 22121;

    protected static $_models = array();
    const WX_ACT_USER  = "WX_ACT_USER_KEY_"; //记录帮助次数
    const WX_ACT_USER_HELPER_LIST  = "WX_ACT_USER_HELPER_LIST_"; //记录帮助人列表
    const WX_USER_INFO = "WX_USER_INFO_KEY_"; //记录分享者个人信息
    const WX_HELP ='WX_HELP_KEY_';
    const WX_DRAW = 'WX_DRAW_KEY_';
    const WX_ALL_USER = 'WX_ALL_USER_KEY';
    

    const EXPIRE_TIME = 86400;


   public static function model($className = __CLASS__) {
        $model = null;
        if (isset ( self::$_models [$className] ))
            $model = self::$_models [$className];
        else {
            $model = self::$_models [$className] = new $className ( null );
        }
        return $model;
    }

    public function __set($name, $value)
    {
        if ($this->_customer_id !== null) {
            $this->_attributes[$name] = $value;
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $this->redis->hSet($this->_customer_id, $name, $value);
            return true;
        } else {
            return false;
        }
    }

    

    /**
    *   判断键值是否存在，存在返回0,不存在返回1
    *
    */
    public function keyExist($key,$value){
        return $this->redis->setnx($key,$value);
    }

    /*
    *   原子递增
    *
    */
    public function automicIncr($key){
        $this->keyExist($key,0);//防止key不存在
        return $this->redis->incr($key);
    }

    /*
    *   原子递减
    *
    */
    public function automicDecr($key){
       return $this->redis->decr($key);
    }

    /**
    *   获取键值
    *
    */
    public function getByKey($key){
        $value=$this->redis->get($key);
        if($value){
            $value = json_decode($value, true);
        }
        return $value;
    }

    /**
    *   设置值
    *
    */
    public function setKey($key,$value){
        $value = json_encode($value);
        $this->redis->set($key, $value);
        return true;
    }

    /**
    *   加入用户
    */
    public function addUser($act_name,$openid,$head_url,$nickname){
        $value=array(
            'openid'=>$openid,
            'headurl'=>$head_url,
            'nickname'=>$nickname,
            );
        return $this->redis->hset($act_name.'_'.self::WX_ALL_USER, $openid, json_encode($value));
    }

    /**
    *   获取用户信息
    *
    */
    public function getUser($act_name,$openid){
        $value = $this->redis->hget($act_name.'_'.self::WX_ALL_USER, $openid);
        if($value){
            $value = json_decode($value);
        }
        return $value;
    }

    /**
    *   获取微信用户信息
    */
    public function getUserInfo($act_name,$wx_user){
        EdjLog::info('user='.$wx_user.'--------------');

        $key = $act_name.'_'.self::WX_USER_INFO.$wx_user;
        $user_info = $this->getByKey($key);
        if(empty($user_info)){
            $user_info = WxFreeUser::model()->getUserInfo($act_name,$wx_user);
            if($user_info){
                $data=array(
                'wx_user'=>$user_info['wx_user'],
                'money'=>$user_info['money'],
                'is_draw'=>$user_info['is_draw'],
                'head_url'=>$user_info['head_url'],
                'nickname'=>$user_info['nickname'],
                'help_num'=>$user_info['help_num']
                    );
                //放到redis
                $this->setKey($key,$data);
                return $data;
            }
        }
        return $user_info;
    }

    /**
    *   获取用户帮助列表
    *
    */
    public function getHelpList($act_name,$wx_user){
        $key = $act_name.'_'.self::WX_ACT_USER_HELPER_LIST.$wx_user;
        $list = $this->getByKey($key);
        if(empty($list)){
            $list = WxFreeLog::model()->getHelpList($act_name,$wx_user);
            $this->setKey($key,$list);
        }
        return $list;
    }

    /**
    *   强制刷新帮助列表
    *
    */
    public function reloadHelpList($act_name,$wx_user){
        $key = $act_name.'_'.self::WX_ACT_USER_HELPER_LIST.$wx_user;
        $list = WxFreeLog::model()->getHelpList($act_name,$wx_user);
        $this->setKey($key,$list);
    }

    /**
    *   强制刷新用户信息
    *
    */
    public function reloadUserInfo($act_name,$wx_user){
        $key = $act_name.'_'.self::WX_USER_INFO.$wx_user;
        $user_info = WxFreeUser::model()->getUserInfo($act_name,$wx_user);
        if($user_info){
            $data=array(
                'wx_user'=>$user_info['wx_user'],
                'money'=>$user_info['money'],
                'is_draw'=>$user_info['is_draw'],
                'head_url'=>$user_info['head_url'],
                'nickname'=>$user_info['nickname'],
                'help_num'=>$user_info['help_num']
                    );
            //放到redis
            $this->setKey($key,$data);
          }
    }

}
