<?php
/**
 * 订单历史评论
 * User: mtx
 * Date: 14-1-2
 * Time: 下午4:15
 * auther mengtianxue
 */

class ROrderComment extends CRedis
{
	public $host = 'redis02n.edaijia.cn'; //10.161.174.78 redis02n.edaijia.cn
    public $port = 6379;
    public $password = 'k74FkBwb7252FsbNk2M7';
    private $_prefix = 'ORDER_COMMENT_';
    private $_order_id = null;


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

    public function __set($name, $value)
    {
        if ($this->_order_id !== null) {
            $this->_attributes[$name] = $value;
            $key = $this->_order_key . $this->_order_id;
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $this->redis->hSet($key, $name, $value);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 写入redis
     * @param $order_id
     * @param $data
     * @return bool
     * @auther mengtianxue
     */
    public function setComment($order_id, $data = array())
    {
        $this->_order_id = $order_id;
        $key = $this->_prefix . $order_id;
        $this->redis->hMset($key, $data);
        return true;
    }

    /**
     * 获取订单是否评价
     * @param $order_id
     * @return null
     * @auther mengtianxue
     */
    public function getComment($order_id)
    {
        if ($order_id !== null) {
            $this->_order_id = $order_id;
            $key = $this->_prefix . $order_id;
            if ($this->redis->exists($key)) {
                $this->_attributes = array_merge($this->_attributes, $this->redis->hGetAll($key) );
                return $this;
            }
        }
        return false;
    }

    /**
     * 获取订单是否评价
     * @param $order_id
     * @return null
     * @auther mengtianxue
     */
    public function getCommentbyId($order_id)
    {
        if ($order_id !== null) {
            $this->_order_id = $order_id;
            $key = $this->_prefix . $order_id;
            if ($this->redis->exists($key)) {
                $this->_attributes = array_merge($this->_attributes, $this->redis->hGetAll($key) );
                return $this;
            }
        }
        return false;
    }

    /**
     * 删除订单评论记录
     * @param $order_id
     * @return bool
     * @auther mengtianxue
     */
    public function delete($order_id)
    {
        if (!empty($order_id)) {
            $key = $this->_prefix . $order_id;
            if ($this->redis->exists($key)) {
                $this->redis->delete($key);
                return true;
            }
        }
        return false;
    }

}