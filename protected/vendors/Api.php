<?php

class Api {
	public $host = 'http://api.edaijia.cn/rest/?';
	public $decode_json = true;
	
	private static $_models = array ();
	
	private $timestamp;
	private $ver;
	private $key;
	
	public static function model($className = __CLASS__) {
		$model = null;
		if (isset(self::$_models[$className]))
			$model = self::$_models[$className];
		else {
			$model = self::$_models[$className] = new $className(null);
		}
		return $model;
	}
	
	public function setKey($key) {
		$this->key = $key;
		return $this;
	}
	
	public function __construct($key) {
		$this->timestamp = date('Y-m-d H:i');
		$this->ver = 3;
		$this->key = $key;
	}
	
	public function customer_account_recharge($fee = 0, $token = 0, $type, $phone, $bonus) {
		$params = array (
			'fee'=>$fee, 
			'token'=>$token, 
			'type'=>$type, 
			'phone'=>$phone, 
			'bonus'=>$bonus);
		return self::get('customer.account.recharge', $params);
	}
	
	public function customer_order_queue($user) {
		$params = array (
			'user'=>$user);
		return self::get('customer.order.queue', $params);
	}
	
	public function customer_position($city_id) {
		$params = array (
			'city_id'=>$city_id);
		return self::get('customer.position', $params);
	}
	
	public function customer_positionofcallcenter($city_id) {
		$params = array (
			'city_id'=>$city_id);
		return self::get('customer.positionofcallcenter', $params);
	}
	
	public function callcenter_calllog() {
		$params = $_REQUEST['json'];
		return self::get('callcenter.calllog', $params);
	}
	
	public function driver_get($driver_id) {
		$params = array (
			'driverID'=>$driver_id);
		return self::get('driver.get', $params);
	}
	
	/**
	 * 
	 * 查找周边的司机
	 * @param array $params
	 */
	public function driver_nearby($params) {
		return self::get('driver.nearby', $params);
	}
	
	/**
	 * 
	 * 把GPS位置转换为百度坐标和街道地址
	 * @param string $lng
	 * @param string $lat
	 */
	public function gps_location($lng, $lat) {
		$params = array (
			'lat'=>$lat, 
			'lng'=>$lng);
		return self::get('gps.location', $params);
	}


    public function sp_send_sms($mobile, $content, $channel)
    {
        $params = array(
            'sms' => $content,
            'phone' => $mobile,
            'channel' => $channel);
        return self::get('open.go.sms', $params);

    }
	
	public function get($url, $params, $second = 30) {
		$params['appkey'] = $this->key;
		$params['ver'] = $this->ver;
		$params['timestamp'] = $this->timestamp;
		$params['method'] = $url;

		$sig = Api::createSigV2($params, $this->key);
		$params['sig'] = $sig;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $this->host);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$data = curl_exec($ch);
		curl_close($ch);
		
		if ($this->decode_json) {
			return json_decode($data, true);
		} else {
			return $data;
		}
	}
	
	public static function createSig($params) {
		ksort($params);
		
		$query_string = '';
		
		foreach($params as $k=>$v) {
			$query_string .= $k.$v;
		}
		
		$sig = md5($query_string.Yii::app()->params['api_password']);
		
		return $sig;
	}
	
	public static function createSigV2($params, $appkey) {
		ksort($params);
		$query_string = '';
		
		foreach($params as $k=>$v) {
			$query_string .= $k.$v;
		}
		$key = ApiKey::key($appkey);
		
		//检查API是否有效
		if ($key['enable']==1) {
			return md5($query_string.$key['secret']);
		} else {
			echo json_encode(array (
				'code'=>9999, 
				'message'=>'API无效'));
			Yii::app()->end();
		}
		
		$sig = md5($query_string.$key['secret']);
		return $sig;
	}
}