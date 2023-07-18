<?php
/**
 * 管理customer的redis存储
 *
 * @author sunhongjing at 2014-01-02
 *
 *
 */
class RCustomerInfo extends CRedis
{

    public $host = 'redis02n.edaijia.cn'; //10.161.174.78 redis02n.edaijia.cn

    public $port = 6379;
    public $password = 'k74FkBwb7252FsbNk2M7';

    protected static $_models = array();

    protected $_customer_id = null;

    private $_customer_token_key = "CUSTOMER_TOKEN_KEY_"; //用户token key

    private $_customer_key = "CUSTOMER_KEY_"; //用户信息key
    private $_prelogin_customer = "PRELOGIN_CUSTOMER_";

    // For check is the customer is new or old
    private $_customer_first_order_key = "CUSTOMER_FIRST_ORDER_KEY_";

    const EXPIRE_TIME = 86400;


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
     * 生成验证码
     * @return int
     */
    private function createCode()
    {
        $code = rand(1000, 9999); //生成验证码
        return $code;
    }

    /**
     * 获取登陆验证码
     * @param $phone
     * @return object
     * @auther mengtianxue
     */
    public function getCustomerSmsPasswd($phone,$business=CustomerToken::EDJ_TOKEN_FROM)
    {
        $key = $this->_prelogin_customer . md5($phone.$business);
        if ($this->redis->exists($key)) {
            $pass = $this->redis->get($key);
            $pass = json_decode($pass);
            return (object)$pass;
        }
        return null;
    }

    /**
     * 设置登陆验证码
     * @param $phone
     * @param array $data
     * @return object
     * @auther mengtianxue
     */
    public function setCustomerSmsPasswd($phone, $data = array())
    {   
        $business = isset($data['business']) ? $data['business'] :CustomerToken::EDJ_TOKEN_FROM;
        $key = $this->_prelogin_customer . md5($phone.$business);
        $num = 1;
        $code = $this->createCode();
        $code = isset($data['code']) ? $data['code'] : $code;
        $min_time = isset($data['min_time']) ? $data['min_time'] : (time() + 60);
        $out_time = isset($data['out_time']) ? $data['out_time'] : (time() + 600);
        if (isset($data['date_time']) && $data['date_time'] == date('Y-m-d')) {
            $date_time = $data['date_time'];

            if (isset($data['num'])) {
                $num = $data['num'] + 1;
            }
        } else {
            $date_time = date('Y-m-d');
        }

        $pass = array();
        $pass['code'] = $code;
        $pass['date_time'] = $date_time;
        $pass['min_time'] = $min_time;
        $pass['out_time'] = $out_time;
        $pass['num'] = $num;
        $this->redis->set($key, json_encode($pass));
        $this->redis->expire($key, 86400);
        return (object)$pass;
    }

    /**
     * 删除缓存
     * @param $phone
     */
    public function deletePassCodeCache($phone)
    {
        $cache_key = $this->_prelogin_customer . md5($phone);
        if ($this->redis->exists($cache_key)) {
            $this->redis->delete($cache_key);
        }
    }

    /**
     * 获取用户token值
     * @param $phone
     * @return string
     */
    public function getTokenValue($phone)
    {
        $token = md5(time() . $phone);
        $token = md5($token . $phone . time());
        return $token;
    }

    /**
     * 设置用户token
     * @param $token
     * @param string $id
     * @return mixed
     */
    public function setTokenCache($token, $id = "")
    {
        if (empty($id)) {
            return null;
        }
        $cache_key = $this->_customer_token_key . $token;
        if (!$this->redis->exists($cache_key)) {
            $this->redis->set($cache_key, $id);
        }
        return null;
    }

    /**
     * 清除用户token
     * @param $token
     * @return null
     */
    public function delete($token)
    {
        $flag = false;
        $cache_key = $this->_customer_token_key . $token;
        if ($this->redis->exists($cache_key)) {
            $this->redis->delete($cache_key);
            $flag = true;
        }
        return $flag;
    }


    /**
     * 返回客户的信息
     * @param string $customer_id
     */
    public function get($customer_id)
    {
        if ($customer_id !== null) {
            $key = $this->_customer_key . $customer_id;
            if (!$this->redis->exists($key)) {
                $load = $this->load($customer_id);
                if (empty($load)) {
                    return null;
                }
            }
            $this->_customer_id = $key;
            $this->_attributes = array_merge($this->_attributes, $this->redis->hGetAll($key));

            return $this;
        }
    }

    /**
     * 新增客户缓存信息
     * @param $customer_id
     * @param $phone
     * @return $this|null
     */
    public function add($customer_id, $phone)
    {
        $key = $this->_customer_key . $customer_id;
        if (!$this->redis->exists($key)) {
            //先初始化一下
            $this->redis->hSet($key, 'id', $customer_id);
            $this->redis->hSet($key, 'name', '');
            $this->redis->set('customer:phone:' . $phone, $customer_id);
        }
        return $this->get($customer_id);
    }

    /**
     * 初始化客户的redis信息
     * @param $customer_id
     */
    public function load($customer_id)
    {
        $customer = CustomerMain::model()->findByPk($customer_id);
        if ($customer) {
            $this->_customer_id = $customer_id;
            $id = empty($customer->id) ? 0 : $customer->id;
            $name = empty($customer->name) ? '' : $customer->name;
            $gender = empty($customer->gender) ? 0 : $customer->gender;
            $phone = empty($customer->phone) ? '' : $customer->phone;
            $backup_phone = empty($customer->backup_phone) ? '' : $customer->backup_phone;
            $address = empty($customer->address) ? '' : $customer->address;
            $car_num = empty($customer->car_num) ? '' : $customer->car_num;
            $city_id = empty($customer->address) ? 0 : $customer->city_id;
            $create_time = empty($customer->address) ? '' : $customer->create_time;
            $app_ver = empty($customer->app_ver) ? '' : $customer->app_ver;
            $type = empty($customer->type) ? '' : $customer->type;

            //客户基础信息
            $this->redis->hSet($this->_customer_key . $this->_customer_id, 'id', $id);
            $this->redis->hSet($this->_customer_key . $this->_customer_id, 'name', $name);
            $this->redis->hSet($this->_customer_key . $this->_customer_id, 'gender', $gender);
            $this->redis->hSet($this->_customer_key . $this->_customer_id, 'phone', $phone);
            $this->redis->hSet($this->_customer_key . $this->_customer_id, 'backup_phone', $backup_phone);
            $this->redis->hSet($this->_customer_key . $this->_customer_id, 'address', $address);
            $this->redis->hSet($this->_customer_key . $this->_customer_id, 'car_num', $car_num);
            $this->redis->hSet($this->_customer_key . $this->_customer_id, 'city_id', $city_id);
            $this->redis->hSet($this->_customer_key . $this->_customer_id, 'create_time', $create_time);
            $this->redis->hSet($this->_customer_key . $this->_customer_id, 'app_ver', $app_ver);
            $this->redis->hSet($this->_customer_key . $this->_customer_id, 'type', $type);

            //用户是否是vip
            $vip_phone = VipPhone::model()->getPrimary($customer->phone);
	    $vip=false;
	    if($vip_phone&&isset($vip_phone['vipid'])){
		$vip=Vip::model()->getPrimary($vip_phone['vipid']);
	    }
            $is_vip = 0;
            if ($vip_phone&&$vip) {
                $is_vip = 1;
            }
            $this->redis->hSet($this->_customer_key . $this->_customer_id, 'vip', $is_vip);

            $this->redis->set('customer:phone:' . $customer->phone, $customer->id);
            return $this;
        }
        return null;
    }


    /**
     * 重载全部的客户信息
     * @param null $driver_id
     */
    public function loadAll()
    {
        $offset = 0;
        $pagesize = 500;
        echo "starting \n";
        while (true) {
            $criteria = new CDbCriteria(array(
                'select' => 'id',
                'offset' => $offset,
                'limit' => $pagesize
            ));
            $customers = CustomerMain::model()->findAll($criteria);
            if ($customers) {
                foreach ($customers as $customer) {
                    $this->load($customer['id']);
                    echo $customer['id'] . "\n";
                }
            } else {
                break;
            }
            $offset += $pagesize;
            echo $offset . "\n";
        }
    }


    /**
     * 用token查询客户信息
     * @param string $token
     */
    public function getByToken($token)
    {
        if (empty($token)) {
            return null;
        }
        $token_key = $this->_customer_token_key . $token;
        if ($this->redis->exists($token_key)) {
            $id = $this->redis->get($token_key);
            return $this->get($id);
        }
        return null;
    }


    /**
     * 查找电话号对应的客户信息
     * @param string $phone
     * @return boolean
     */
    public function getByPhone($phone)
    {
        if (empty($phone)) {
            return null;
        }
        $id = $this->redis->get('customer:phone:' . $phone);
        if (!empty($id)) {
            return $this->get($id);
        }
        return null;
    }


    public function RPustList($key,$value){
        $this->redis->lPush($key,$value);
    }

    public function getLRand($key, $start, $end){
        var_dump($this->redis->lRange($key, $start, $end));
    }

    public function getLLen($key){
        var_dump($this->redis->llen($key));
    }

    /**
     * Check if the order is the first one for the customer
     */
    public function checkCustomerFirstOrder($phone, $order_id, $update = false) {
	// Check if there are history orders
	if($update) {
	    $customer_order_report = 
		CustomerOrderReport::model()->getCustomerOrder(
			array('phone' => $phone));
	    if($customer_order_report) {
		// Old customer
		return false;
	    }
	}

	$key =  $this->_customer_first_order_key.$phone;
	// Check if there is an order in current orderlist
        if ($this->redis->exists($key)) {
            $first_order_id = $this->redis->get($key);
	    if($first_order_id ==  $order_id) {
		return true;
	    }
        } else {
	    if($update) {
		$this->redis->set($key, $order_id);
		$this->redis->expire($key, self::EXPIRE_TIME);
		return true;
	    }
	}
	return false;
    }

    /*
     *
     *删除用户主要信息的缓存
     *
     */
    public function deleteCustomerMain($phone){
	$customer_id=$this->redis->get('customer:phone:' . $phone);
	if(empty($customer_id)){
		EdjLog::info("customer_id for phone $phone is empty");
	}
	$this->redis->delete($this->_customer_key . $customer_id);	
        $this->redis->delete('customer:phone:'.$phone);
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
        return $this->redis->incr($key);
    }

    /**
    *   获取键值
    *
    */
    public function getByKey($key){
        return $this->redis->get($key);
    }

}
