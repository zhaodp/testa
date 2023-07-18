<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 14-1-6
 * Time: 下午10:56
 * auther mengtianxue
 */

class ROrderHistory extends RZList
{
    protected static $_models = array();

    const DELETE_ALL = 0;
    const DELETE_BATCH = 1;
    //订单详情List
    private $_ORDER_HISTORY = "ORDER_HISTORY_LIST_";

    //用户历史订单id
    private $_CUSTOMER_ORDER_ID_LIST = "CUSTOMER_ORDER_ID_LIST_";

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
     * 将订单信息写入redis
     * @param $order_id
     * @param $data
     * @return bool
     * @auther mengtianxue
     */
    public function addOrders($order_id, $data = array())
    {
        if (is_array($data)) {
            $key = $this->_ORDER_HISTORY . $order_id;
            $ret = $this->mSetOrder($key, $data);
            return $ret;
        }
    }


    /**
     * 将订单ID 写入用户 orderId list
     * @param $user_id
     * @param $phone
     * @param $order_id
     * @return bool|int
     * @author bidong 2014-1-16
     */
    public function addOrderId($user_id, $phone, $order_id)
    {
        $ret = false;
        if ($user_id && $order_id && $phone) {
            $key = $this->_CUSTOMER_ORDER_ID_LIST . $phone;
            $ret = parent::addList($key, $user_id, $order_id);
        }

        return $ret;
    }

    /**
     * get all order ids
     * @param $phone
     *
     * @return orderid array
     * @author qiujianping@edaijia-staff.cn
     */
    public function getHistoryOrderIds($phone, $startScore = 0, $step = 10)
    {
        $key = $this->_CUSTOMER_ORDER_ID_LIST . $phone;
        $endScore = $startScore + $step - 1;
        $order_list = parent::getListByScore($key, $startScore, $endScore);
	return $order_list;
    }

    /**
     * get history order count
     * @param $phone
     *
     * @return the order count
     * @author qiujianping@edaijia-staff.cn
     */
    public function getHistoryOrderNum($phone)
    {
        $key = $this->_CUSTOMER_ORDER_ID_LIST . $phone;
        $count = parent::getListCount($key);
	return $count;
    }

    /**
     * 删除列表订单
     * @param $phone
     * @param $order_id
     * @return mixed
     * @auther mengtianxue
     */
    public function delOrderId($phone, $order_id)
    {
        $key = $this->_CUSTOMER_ORDER_ID_LIST . $phone;
        $del_order = parent::delItem($key, $order_id);
        return $del_order;
    }

    /**
     * 删除订单列表
     * @param $phone
     * @return mixed
     * @auther mengtianxue
     */
    public function delOrderHistory($phone){
        $key = $this->_CUSTOMER_ORDER_ID_LIST . $phone;
        $del = parent::del($key);
        return $del;
    }


    /**
     * 更新订单信息
     * @param $order_id 订单ID
     * @param $field 字段
     * @param $val  值
     * @return bool
     * @auther mengtianxue
     */
    public function updateOrder($order_id, $field, $val)
    {
        $key = $this->_ORDER_HISTORY . $order_id;
        $ret = $this->redis->hSet($key, $field, $val);
        return $ret;
    }

    /**
     * 保存订单信息
     * @param $key
     * @param $data
     * @author bidong
     * @return mixed
     */
    protected function mSetOrder($key, $data)
    {
        if (!empty($data) && is_array($data)) {
            $order_arr = array();
            $attr = Order::model()->getAttributes();
            foreach ($data as $k => $v) {
                if (array_key_exists($k, $attr)) {
                    $order_arr[$k] = $v;
                }
            }
            $ret = $this->redis->hMSet($key, $order_arr);
            return $ret;
        } else {
            return false;
        }
    }

    /**
     * 获取订单信息
     * @param $order_id
     * @param string $field
     * @return string
     * @auther mengtianxue
     */
    public function getOrder($order_id, $field = '')
    {
        $key = $this->_ORDER_HISTORY . $order_id;
        if (!empty($field)) {
            $result = $this->redis->hGet($key, $field);
        } else {
            $result = $this->redis->hGetAll($key);
        }
        return $result;
    }

    /**
     * 获取订单列表
     * @param $phone
     * @param $startScore
     * @param $step
     * @return array
     * @auther bidong 2014-1-19
     */
    public function getOrderList($phone, $startScore = 0, $step = 10)
    {
        $key = $this->_CUSTOMER_ORDER_ID_LIST . $phone;
        $data = array(
            'orderList' => array(),
            'orderCount' => 0,
        );

        $count = parent::getListCount($key);
        $num = $count - $startScore;
        $endScore = $startScore + $step - 1;
        $order_list = parent::getListByScore($key, $startScore, $endScore);
        if(!empty($order_list)){
            foreach ($order_list as $order_id) {
                $orderInfo = $this->getOrder($order_id);
				$orderExt  = OrderExt::model()->getPrimary($order_id);
				$cityId = $orderInfo['city_id'];
				if($orderExt){
					$readyDistance = isset($orderExt['linear_ready_distance'])? $orderExt['linear_ready_distance'] : 0;
					$readyTime = isset($orderExt['driver_ready_time']) ? $orderExt['driver_ready_time'] : 0;
				}
                $list['id'] = $num;
                $list['location_start'] = Helper::getShortAddress($orderInfo['location_start'], $orderInfo['city_id']);
                $list['location_end'] = Helper::getShortAddress($orderInfo['location_end'], $orderInfo['city_id']);
                $list['city_id'] = $orderInfo['city_id'];
                $list['order_id'] = $orderInfo['order_id'];
//				$list['income'] = FinanceCastHelper::getOrderIncome($orderInfo, $orderExt);
				$list['start_time'] = $orderInfo['start_time'];
	            $list['income']  = FinanceCastHelper::getOrderTotalMoney($orderInfo, $orderExt, null);
                $data['orderList'][] = $list;
                $num--;
            }
            $data['orderCount'] = $count;
        }

        return $data;
    }

	
     /**
   *获取phone对应redis中订单数目
   *
   **/
   public function getOrderCount($phone){
        if(!empty($phone)){
		$key = $this->_CUSTOMER_ORDER_ID_LIST . $phone;
		$count = parent::getListCount($key);
		return $count;
        }
	return 0;
   }

	
    /**
     * 初始化订单信息
     * @param $customer_phone
     * reload_flag=false 表示不重载，true表示需要从数据库重载
     * @auther mengtianxue
     */
    public function loadCustomerOrder($customer_phone, $order_id = '',$reload_flag=false)
    {
        //切换从库
        //判断缓存中是否存在order_id list,没有缓存，全部加载
        $orderId_list_key = $this->_CUSTOMER_ORDER_ID_LIST . $customer_phone;
        if (!$reload_flag && $this->redis->exists($orderId_list_key) && empty($order_id)) {
            return;
        }

        $criteria = new CDbCriteria();
        $criteria->select = "*";
        $criteria->addCondition('phone = :phone');
        $criteria->addCondition('status = :status');
        $criteria->params = array(
            ':phone' => $customer_phone,
            ':status' => Order::ORDER_COMPLATE
        );
        if (!empty($order_id)) {
            $criteria->addCondition('order_id = :order_id');
            $criteria->params[':order_id'] = $order_id;
        }
        $criteria->order = "order_id asc";
        $OrderObj = Order::model()->findAll($criteria);

        if ($OrderObj) {
            foreach ($OrderObj as $order) {
                $order_id = $order->attributes['order_id'];
		$order_source = $order->attributes['source'];
		if(isset($order_source)
		    && in_array($order_source, Order::$washcar_sources)) {
		    continue;
		}

                //判断是否已经删除
                $is_delete = $this->is_del_order($order_id);
                if (!$is_delete) {
                    //计算有序集合中最后一个元素的score值
                    //$score = parent::getMaxScore($orderId_list_key);
		    //改用order_id作为score
		    $score = $order_id;

                    //载入订单ID list
                    $result = parent::addList($orderId_list_key, $score, $order_id);
                    if ($result) {
                        //载入订单详情LIST
                        $result = $this->addOrders($order_id, $order->attributes);
                    }
                }
            }
        }
    }

    /**
     * 判断订单是否已经删除
     * @param $order_id
     * @return bool
     * @author bidong 2014-1-20
     */
    private function is_del_order($order_id)
    {
        Yii::import('application.models.schema.customer.CarCustomerDelOrder');
        $flag = false;
        $table = CarCustomerDelOrder::model()->tableName();

        $order = Yii::app()->db_readonly->createCommand()
            ->select('order_id')
            ->from($table)
            ->where('order_id=:order_id', array(':order_id' => $order_id))
            ->queryRow();

        if ($order) {
            $flag = true;
        }
        return $flag;
    }
}
