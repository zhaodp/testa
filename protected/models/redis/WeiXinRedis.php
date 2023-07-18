<?php
/**
 * Created by vim.
 * User: lidingcai@edaijia-inc.cn
 * Date: 14-4-11
 * Time: 下午17:30
 */
//对于银联已经向我方发送notify通知的充值单号，将order_id缓存，以免银联或我方多次对其进行加钱操作
//设置缓存为2天，以免充值和定时脚本同时运行产生影响
class WeixinRedis extends CRedis{
    public $host = '10.132.38.173';
    public $port = 9600;
    public $password = 'k74FkBwb7252FsbNk2M7';
    private $uniqOrderIdKey='uniq_order_id';
    private $orderIdKeyPrefix='order_id_';
    private $settlePhoneKey='settle_phone_key_';
    private $settleVipCardKey='settle_vip_card_key_';

    public function getWeiXinToken(){
        return $this->redis->hget('WEIXIN_ACCESS_TOKEN', 'token');
    }

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

    public function genOrderIdKey($orderId){
        return $this->orderIdKeyPrefix.$orderId;
    }

    //生成唯一order_id
    public function incrOrderId(){
        return $this->redis->incr($this->uniqOrderIdKey);
    }

    //判断并且写入相应order_id入redis
    public function addOrderIdIfNotExist($orderId){
        if(empty($orderId)){
            return false;
        }
        $key=$this->genOrderIdKey($orderId);
        $ret=$this->redis->setnx($key,1);
        $this->redis->expire($key,2*60);
        return $ret;
    }

    //删除一个redis中的orderId,注意返回值是个整型，表示删除成功的key的数量
    public function delOrderId($orderId){
        return $this->redis->del($this->genOrderIdKey($orderId));
    }

    public function addSettlePhoneIfNotExist($phone){
        if(empty($phone)){
            return false;
        }
        $key=$this->settlePhoneKey.$phone;
        $ret=$this->redis->setnx($key,1);
        $this->redis->expire($key,2*60);
        return $ret;
    }
    public function delSettlePhone($phone){
        return $this->redis->del($this->settlePhoneKey.$phone);
    }

    public function addSettleVipCardIfNotExist($vipCard){
        if(empty($vipCard)){
            return false;
        }
        $key=$this->settleVipCardKey.$vipCard;
        $ret=$this->redis->setnx($key,1);
        $this->redis->expire($key,2*60);
        return $ret;
    }
    public function delSettleVipCard($vipCard){
        return $this->redis->del($this->settleVipCardKey.$vipCard);
    }

    /**
     * 给用户加一个这样的锁
     *
     * @param $phone
     * @param $token
     */
    public function  lockUserFeedBack($phone, $token){
        $key = 'feedback_'.$phone.'_'.$token;
        $ret = $this->redis->get($key);
        if($ret){
            if(1 < $ret){
                return array(
                    'code'  => 1,
                    'message' => '恭喜你,你已经领过了',
                );
            }
            return array(
                'code'  => 1,
                'message' => '不要点太快哦',
            );
        }else{
            $this->redis->set($key, 1);
        }
        return true;
    }

    /**
     * 操作完了 + 1;
     *
     * @param $phone
     * @param $token
     * @return mixed
     */
    public function incrUserFeedBack($phone, $token){
        $key = 'feedback_'.$phone.'_'.$token;
        return $this->redis->incr($key);
    }

    /**
     * 操作失败之后,给删除一下缓存
     *
     * @param $phone
     * @param $token
     * @return mixed
     */
    public function delUserFeedBack($phone, $token){
        $key = 'feedback_'.$phone.'_'.$token;
        return $this->redis->del($key);
    }

    /**
     *
     * 朝 redis 插入财务的锁
     *
     * @param $key
     * @param $value
     */
    public function addFinanceLock($key, $value, $expired){
        if(0 < $expired){
            $this->redis->set($key, $value, $expired);
        }else{
            $this->redis->set($key, $value);
        }
    }

    /**
     *
     * 获得财务在 redis 里面的锁
     *
     * @param $key
     */
    public function getFinanceLock($key){
        return $this->redis->get($key);
    }
}
