<?php
/**
 * 订单相关缓存
 * @author liuxiaobo
 * @since 2013-12-17
 */
class OrderCache  extends CRedis{
    public $host='redis01n.edaijia.cn'; //10.132.17.218
    public $port=6379;
    public $password='k74FkBwb7252FsbNk2M7';
    const DIR_TIME_LINE = 'order:time_line:';
    const DIR_ORDER_PATH = 'order:order_path:';
    const DIR_ORDER_INFO = 'order:order_info:';
    const EXPIRE_ORDER_TIME_LINE = 2592000;     //30天
    const EXPIRE_ORDER_PATH = 2592000;
    const EXPIRE_ORDER_INFO = 259200; //3天
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
     * 获取订单的时间线
     * @param <int> $orderId
     * @param <datetime> $updateCodeTime    修改代码逻辑_上线_的时间，如果修改代码后需要更新缓存的时候就修改此参数
     * @return <array>  @link:Order::model()->getOrderTimeLine($orderId);
     * @author liuxiaobo
     */
    public function getTimeLine($orderId=0, $updateCodeTime='2014-01-08 17:07:01'){
        $key = self::DIR_TIME_LINE . $orderId;
        if($this->redis->ttl($key) < (strtotime($updateCodeTime) + self::EXPIRE_ORDER_TIME_LINE - time())){
            //判断缓存在更新代码后的有效性
            $this->deleteTimeLine($orderId);
        }
        $value = $this->redis->get($key);
        return json_decode($value,TRUE);
    }
    
    /**
     * 删除订单的时间线
     * @param <int> $orderId
     * @return <bool>
     * @author liuxiaobo
     */
    public function deleteTimeLine($orderId=0){
        $key = self::DIR_TIME_LINE . $orderId;
        return $this->redis->delete($key);
    }
    
    /**
     * 设置订单的时间线
     * @param <int> $orderId
     * @param <int> $timeLine   @link:Order::model()->getOrderTimeLine($orderId);
     * @return <bool>
     * @author liuxiaobo
     */
    public function setTimeLine($orderId=0, $timeLine=array()){
        $key = self::DIR_TIME_LINE . $orderId;
        $value = json_encode($timeLine);
        $expire = self::EXPIRE_ORDER_TIME_LINE;
        return $this->redis->setex($key, $expire, $value);
    }
    
    /**
     * 获取订单路线
     * @param <int> $orderId
     * @param <datetime> $updateCodeTime    修改代码逻辑_上线_的时间，如果修改代码后需要更新缓存的时候就修改此参数
     * @return <array>  @link:Order::model()->getOrderPath($orderId);
     * @author liuxiaobo
     */
    public function getOrderPath($orderId=0, $updateCodeTime='2014-01-08 17:07:01'){
        $key = self::DIR_TIME_LINE . $orderId;
        if($this->redis->ttl($key) < (strtotime($updateCodeTime) + self::EXPIRE_ORDER_PATH - time())){
            //判断缓存在更新代码后的有效性
            $this->deleteOrderPath($orderId);
        }
        $key = self::DIR_ORDER_PATH . $orderId;
        $value = $this->redis->get($key);
        return json_decode($value,TRUE);
    }
    
    /**
     * 删除订单路线
     * @param <int> $orderId
     * @return <bool>
     * @author liuxiaobo
     */
    public function deleteOrderPath($orderId=0){
        $key = self::DIR_ORDER_PATH . $orderId;
        return $this->redis->delete($key);
    }
    
    /**
     * 设置订单路线
     * @param <int> $orderId
     * @param <int> $orderPath   @link:Order::model()->getOrderPath($orderId);
     * @return <bool>
     * @author liuxiaobo
     */
    public function setOrderPath($orderId=0, $orderPath=array()){
        $key = self::DIR_ORDER_PATH . $orderId;
        $value = json_encode($orderPath);
        $expire = self::EXPIRE_ORDER_PATH;
        return $this->redis->setex($key, $expire, $value);
    }
    
    /**
     * 获取订单缓存信息
     * @param type $orderId
     * @return \OrderCache|boolean
     * @author liuxiaobo
     */
    public function getOrderInfo($orderId=0){
        $key = self::DIR_ORDER_INFO . $orderId;
        if($this->redis->exists($key) || $this->initOrderInfo($orderId)){
            $this->_attributes = array_merge($this->_attributes, $this->redis->hGetAll($key));
            return $this;
        }
        return FALSE;
    }
    
    /**
     * 重置订单信息缓存
     * @param type $orderId
     * @return boolean
     * @author liuxiaobo
     */
    public function initOrderInfo($orderId=0){
        $orderInfo = $this->getAttributesByNeed($orderId);
        if($orderInfo){
            foreach ($orderInfo as $attribute => $attributeVal){
                $this->setOrderInfoAttribute($orderId, $attribute, $attributeVal);
            }
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * 设置订单熟悉信息
     * @param type $orderId
     * @param type $attribute
     * @param type $attributeVal
     * @return type
     * @author liuxiaobo
     */
    public function setOrderInfoAttribute($orderId, $attribute, $attributeVal){
        $key = self::DIR_ORDER_INFO . $orderId;
        $this->redis->hSet($key, $attribute, $attributeVal);
        $this->redis->expire($key, self::EXPIRE_ORDER_INFO);
    }
    
    /**
     * 清除订单详细信息
     */
    public function delOrderInfoAttribute($orderId){
        $key = self::DIR_ORDER_INFO . $orderId;
        $this->redis->del($key);
    }

    /**
     * 获取需要缓存的订单信息数据
     * @param type $orderId
     * @return array
     * @author liuxiaobo
     */
    private function getAttributesByNeed($orderId=0){
        $result = array();
        $orderInfo = Order::model()->findByPk($orderId);
        if(!$orderInfo){
            return $result;
        }
        //未完成的订单不加入缓存
        if(in_array($orderInfo->status, Order::model()->getOnWayStatus())){
            return $result;
        }
        $result = array_merge($result, $orderInfo->attributes);
        $ext = $orderInfo->order_ext;
        $ext_attributes = array();
        foreach (array('wait_time','mark') as $attribute) {
            $ext_attributes[$attribute] = isset($ext->$attribute) ? $ext->$attribute : '';
        }
        //报单时间（利用了之前订单详情页的逻辑） 2014-1-2
        $submitTime = '';
        $criteria = new CDbCriteria();
        $criteria->addCondition('order_id=:order_id and operator=:driver_id and description = :description');
        $criteria->params = array(':order_id'=>$orderInfo->order_id, ':driver_id'=>$orderInfo->driver_id, ':description' => '报单');
        $criteria->order = 'id ASC';
        $orderLog = OrderLog::model()->find($criteria);
        if ($orderLog){
            $submitTime = $orderLog->created;
        }
        $ext_attributes['submit_time'] = $submitTime;
                        
        $result = array_merge($result, $ext_attributes);
        
        return $result;
    }
}
