<?php
/**
 * 
 * API入口
 * @author dayuer
 *
 */

// modify base Controler by syang on 2013-6-18
class ApiController extends ApiBaseController
{
	public $layout = '//layouts/blank';
	public $_params;
	public $_ver;
	public $white_list=array('common.pay.ali.alinotify', 'open.ping');
	public function actions()
	{
		return array(
			'docs'=>'application.controllers.api.DocsAction',
//			合力400电话接口
			'hojo'=>'application.controllers.api.HojoAction', //电话记录对接接口
//			'ivr'=>'application.controllers.api.HojoIvrAction', //Ivr对接接口
			'ccic'=>'application.controllers.api.CcicAction',
			'public'=>'application.controllers.api.PublicAction', 
			'rest'=>'application.controllers.api.RestAction', 
			'v4'=>'application.controllers.api.V4Action', 
			'mtk'=>'application.controllers.api.MtkAction',
			'ppnotify'=>'application.controllers.api.PpnotifyAction',
			'wxnotify'=>'application.controllers.api.WxnotifyAction'
        );
	}
	
	public function init()
	{
		$this->format = 'json';
		$action = Yii::app()->request->pathInfo;
		if (Yii::app()->request->isPostRequest)
		{
			$this->_params = $_POST;
		} else
		{
			$this->_params = $_GET;
		}

		//@self::accessLog();
		
		switch ($action)
		{
			case 'docs' :
			case 'hojo' :
			case 'ccic' :
			case 'public' :
			case 'ppnotify':
			case 'wxnotify':
				return;
				break;
			default :
				break;
		}
		if(isset($this->_params['method'])&&in_array($this->_params['method'],$this->white_list,true)){
			$this->_params['appkey']='20000001';//appkey 没太大意义
			$this->_ver = isset($this->_params['ver']) ? $this->_params['ver'] : 1;
			return;
		}	
		$message = null;
		//排序
		ksort($this->_params);
		//$this->_params = $_REQUEST;
		
		//新版本V4，使用action判定，与之前版本隔离 提前处验证  by syang on 2013-6-22
		if ($action == 'v4') {
		    //校验参数及其签名
		    $sign = isset($this->_params['sign']) ? $this->_params['sign'] : null;
			
			$i = isset($this->_params['i']) ? $this->_params['i'] : 0;
			if ($i < 0  || $i > 3) {
				$message = array(
					'code'=>'-1001', 
					'message'=>'index error');
					$this->render('/app/error', array('message'=>$message));
				Yii::app()->end();
			}
		    
		    //时间戳
		    $timestamp = isset($this->_params['timestamp']) ? $this->_params['timestamp'] : null;
		
		    if ($sign==null)
		    {
				$message = array(
					'code'=>'-1001', 
					'message'=>'not found sign');
					$this->render('/app/error', array('message'=>$message));
				Yii::app()->end();
		    }
		    
		    //检查时间戳是否过期，允许15分钟的误差
		    $nowTimestamp = time();
		    $offsetTimestamp = abs($nowTimestamp-intval($timestamp));
		    if ($offsetTimestamp> 15*60) {
				$message = array('code'=>'-1001', 'message'=>'请检查操作系统时间以便正常使用');
				$this->render('/app/error', array('message'=>$message));
				Yii::app()->end();
		    }

		    unset($this->_params['sign']);
		    $queryString = http_build_query($this->_params);
		    if (isset($this->_params['access_token'])) {
				$accessToken = $this->_params['access_token'];
				$params = RAuthToken::model()->get($accessToken);
				if ($params===false) {
			    	$message = array('code'=>'-1001', 'message'=>'access_token expire ;');
			    	$this->render('/app/error', array('message'=>$message));
			    	Yii::app()->end();
				}
				
				//记算签名
				$generateSign = CustomerSafe::generateSign($queryString, $params['secretToken']);
				if ($sign!=$generateSign) {
			    	$message = array('code'=>'-1002', 'message'=>'sign err;');
			    	$this->render('/app/error', array('message'=>$message));
			    	Yii::app()->end();
				}
		    }
		    else if (
			    isset($this->_params['method']) && 
				$this->_params['method'] == 'customer.auth' &&
				    isset($this->_params['app_key']) && 
					    isset($this->_params['udid'])
			) {
				//授权请求
				$timestamp = $this->_params['timestamp'];
				$appKey = $this->_params['app_key'];
				$udid = $this->_params['udid'];

			
				$key = RApiKey::model()->key($this->_params['app_key']);
				//获取key的信息，
				if ( $key['enable']==1) {
					$appSecret = md5($key['secret']);
					//$uuid, $appKey, $appSecret, $timestamp
					$generateSign = CustomerSafe::generateAuthSign($udid, $appKey, $appSecret, $timestamp);
				
					if ($sign!=$generateSign) {
				    	$message = array('code'=>'-1002', 'message'=>'sign err;');
				    	$this->render('/app/error', array('message'=>$message));
				    	Yii::app()->end();
					}
					
			    	//生成access_token;
			    	$accessToken = CustomerSafe::generateAccessToken($udid, $appKey, $appSecret);
			    	$secretToken = CustomerSafe::generateSecretToken($udid, $appKey, $appSecret);

					RAuthToken::model()->save($accessToken, array(
						'udid'=>$udid,
						'appKey'=>$appKey,
						'appSecret'=>$appSecret,
						'secretToken'=>$secretToken,
						'timestamp'=>$timestamp,
						'accessToken'=>$accessToken
					));
					
					$message = array('code'=>'0', 'access_token'=>$accessToken, 'secret_token'=>$secretToken);
			    	echo json_encode($message);
			    	exit;
				}
		    }
		    else {
			    $message = array('code'=>'-1002', 'message'=>'request err;');
			    $this->render('/app/error', array('message'=>$message));
			    Yii::app()->end();
		    }
		    
		    
		}
				
		else { //之前的处理
			//new position & heartbeat by syang on 2013-8-1
			if (Yii::app()->request->isPostRequest && count($_POST) == 0)
			{
				//获得原始数据
				$raw_data = file_get_contents("php://input");
				if (strlen($raw_data) <= 40) {
					$message = array(
							'code'=>'-1201',
							'message'=>'raw data error');
					$this->render('/app/error', array('message'=>$message));
					Yii::app()->end();
				}
				
				//获取需要解密的数据
				$sha1_data = substr($raw_data, -23, 20);
				$len_data = substr($raw_data, 10, 4);
				$lens = unpack('Vlen', $len_data);
				$data_len = $lens['len'];
				$bin_data = substr($raw_data, 0, 10). substr($raw_data, 14, -23) . substr($raw_data, -3);
				
				if ($data_len != strlen($bin_data)) {
					$message = array(
							'code'=>'-1202',
							'message'=>'data error');
					$this->render('/app/error', array('message'=>$message));
					Yii::app()->end();
				}
				
				$data = @edaijia_decode_bin($bin_data);
				
				if (strlen($data) == 0 ) {
					$message = array(
							'code'=>'-1203',
							'message'=>'data error');
					$this->render('/app/error', array('message'=>$message));
					Yii::app()->end();
				}
				
				if (bin2hex($sha1_data) != sha1($data)) {
					$message = array(
							'code'=>'-1204',
							'message'=>'data error');
					$this->render('/app/error', array('message'=>$message));
					Yii::app()->end();
				}
				
				//开始处理
				//获得标识  
				$types = unpack('Vtype', $data);
				$params = array();
			
				if ($types['type']==1) {  //driver.upload.position
					$format = 'Vtype/Vdriver_no/Vstatus/Vlog_time/flongitude/flatitude/csecond/C16token/cfirst/c10gps_type/Vtimestamp';
					$params = @unpack($format, $data);
					
					//检查时间戳是否过期，允许15分钟的误差
					$nowTimestamp = time();
					$offsetTimestamp = abs($nowTimestamp-intval($params['timestamp']));
					if ($offsetTimestamp> 15*60) {
						$message = array('code'=>'-1001', 'message'=>'请检查操作系统时间以便正常使用');
						$this->render('/app/error', array('message'=>$message));
						Yii::app()->end();
					}
					
					$token = '';
					if (isset($params['token1']) && isset($params['token16'])) {
						for($i=1; $i<=16; $i++) {
							$token .= chr($params['token'.$i]);
							unset($params['token'.$i]);
						}
					}
					//处理gps_type
					$gps_type = '';
					if (isset($params['gps_type1']) && isset($params['gps_type10'])) {
						for($i=1; $i<=10; $i++) {
							if ($params['gps_type'.$i] > 23) {
								$gps_type .= chr($params['gps_type'.$i]);
							}
							unset($params['gps_type'.$i]);
						}
					}
				
					//处理driver_id
					$driver_id = "";
					if (isset($params['first']) && isset($params['second']) && isset($params['driver_no'])) {
						$driver_no = substr("00000000". $params['driver_no'], -4);
						$driver_id .= chr($params['first']) . chr($params['second']) . $driver_no;
						unset($params['first']);
						unset($params['second']);
						unset($params['driver_no']);
					}
					
					$params['log_time'] = date('YmdHis', $params['log_time']);
					$params['token'] = bin2hex($token);
					$params['gps_type'] = $gps_type;
					$params['driver_id'] = $driver_id;
					$params['method'] = "driver.upload.position";
					$this->_params = $params;
					
				}
				elseif ($types['type']==2) { //driver.define.heartbeat
					$format = 'Vtype/Vdriver_no/Vtimestamp/csecond/C16token/cfirst';
					$params = @unpack($format, $data);
					//检查时间戳是否过期，允许15分钟的误差
					$nowTimestamp = time();
					$offsetTimestamp = abs($nowTimestamp-intval($params['timestamp']));
					if ($offsetTimestamp> 15*60) {
						$message = array('code'=>'-1001', 'message'=>'请检查操作系统时间以便正常使用');
						$this->render('/app/error', array('message'=>$message));
						Yii::app()->end();
					}
					
					$token = '';
					if (isset($params['token1']) && isset($params['token16'])) {
						for($i=1; $i<=16; $i++) {
							$token .= chr($params['token'.$i]);
							unset($params['token'.$i]);
						}
					}
					//处理driver_id
					$driver_id = "";
					if (isset($params['first']) && isset($params['second']) && isset($params['driver_no'])) {
						$driver_no = substr("00000000". $params['driver_no'], -4);
						$driver_id .= chr($params['first']) . chr($params['second']) . $driver_no;
						unset($params['first']);
						unset($params['second']);
						unset($params['driver_no']);
					}
					$params['token'] = bin2hex($token);
					$params['driver_id'] = $driver_id;
					$params['method'] = "driver.define.heartbeat";
					$this->_params = $params;
				}
				else {
					$message = array('code'=>'-1205', 'message'=>'method error;');
					$this->render('/app/error', array('message'=>$message));
					Yii::app()->end();
				}
				//end new post
			} else {

			    //校验参数及其签名
			    $sig = isset($this->_params['sig']) ? $this->_params['sig'] : null;
			    //时间戳 格式为yyyy-MM-dd HH:mm:ss
			    $timestamp = isset($this->_params['timestamp']) ? $this->_params['timestamp'] : null;
	
			    if ($sig==null)
			    {
				    $message = array(
					    'code'=>'-1001', 
					    'message'=>'没有签名');
			    }
	
			    //检查版本号
			    $this->_ver = isset($this->_params['ver']) ? $this->_params['ver'] : 1;
	
	
			    unset($this->_params['sig']);
			    unset($this->_params['r']);
			    unset($this->_params['PHPSESSID']);
			    unset($this->_params['qqmail_alias']);
	
			    $query_string = '';
	
			    foreach($this->_params as $k=>$v)
			    {
				    if ($k!='gpsstring'&&$k!='callback'&&$k!='_')
				    {
					    $query_string .= $k.$v;
				    }
			    }
	
			    $system_sig = null;
	
			    switch ($this->_ver)
			    {
				    case 1 :
					    $system_sig = self::sig_v1($query_string);
					    break;
				    case 2 :
					    $system_sig = self::sig_v2($query_string);
					    break;
				    case 3 :
					    //检查时间戳是否过期，允许15分钟的误差
					    $timestamp = strtotime($timestamp);
					    $max_time = time()+15*60;
					    $min_time = time()-15*60;
					    if ($timestamp<=$max_time&&$timestamp>=$min_time)
					    {
						    $system_sig = self::sig_v2($query_string);
					    } else
					    {
						    $message = array('code'=>'-1001', 'message'=>'请检查操作系统时间以便正常使用');
						    $this->render('/app/error', array('message'=>$message));
						    Yii::app()->end();
					    }
					    break;		    
				    case 99 :
					    //不用签名的api调用，给手机端server使用
					    $system_sig = Yii::app()->params['mtk_sig'];
					    break;
				    default :
					    $message = array('code'=>'-1002', 'message'=>'ver not support;');
					    $this->render('/app/error', array('message'=>$message));
					    Yii::app()->end();
			    }
	
			    if ($sig!=$system_sig)
			    {
				    $message = array('code'=>'-1000', 'message'=>'sig error;');
				    //echo json_encode($message);
				    $this->render('/app/error', array('message'=>$message));
				    Yii::app()->end();
			    }
	
			    $_REQUEST['query'] = $query_string;
			    //self::accessLog($_REQUEST, $status);

	
			    if ($message)
			    {
				    $this->format =='json';
				    $this->render('/app/error', array('message'=>$message));
			    }
			}
		}
	}
	
	private function sig_v1($query_string)
	{
		return md5($query_string.Yii::app()->params['api_password']);
	}
	
	private function sig_v2($query_string)
	{
		$appkey = isset($this->_params['appkey']) ? $this->_params['appkey'] : '';
		
		// key from redis by syang on 2013-6-18
		$key = RApiKey::model()->key($appkey);
		//检查API是否有效
		if ($key['enable']==1)
		{
			return md5($query_string.$key['secret']);
		} else
		{
			echo json_encode(array(
				'code'=>9999, 
				'message'=>'API无效'));
			Yii::app()->end();
		}
	}
	
	public function actionIndex()
	{
		if (!isset($this->_params['func']))
		{
			Yii::app()->end();
		}
		
		$this->_params['format'] = 'json';
		$func = $this->_params['func'];
		$router = str_replace('.', '/', $func);
		Yii::app()->runController($router);
	}
	
	public function actionError()
	{
		//var_dump(Yii::app()->errorHandler->error);exit;
		if ($error = Yii::app()->errorHandler->error)
		{
			unset($error['traces']);
			$error['created'] = date(Yii::app()->params['formatDateTime'], time());
			//@Yii::app()->dbstat->createCommand()->insert('t_api_error_log', $error);
			echo json_encode(array(
				'message', 
				$error));
		}
	}
	
	public function actionDriver()
	{
		$message = array(
			'code'=>1012, 
			'message'=>'此接口已过期禁用');
		echo json_encode($message);
		Yii::app()->end();
	}
	
	/**
	 * 司机信息相关api
	 */
	//	public function actionDriver()
	//	{
	//		switch ($this->_params['ac'])
	//		{
	//			//手机注册
	//			case 'register' :
	//			//司机登录
	//			case 'login' :
	//			//单个司机信息
	//			case 'info' :
	//			//多个司机信息查询
	//			case 'list' :
	//			//司机运行轨迹
	//			case 'track' :
	//			//司机位置
	//			case 'map' :
	//			//获取指定GPS坐标位置周边的司机列表
	//			case 'request' :
	//				$this->render('driver/'.$this->_params['ac'], array(
	//					'params'=>$this->_params));
	//				break;
	//			default :
	//				break;
	//		}
	//	}
	

	//	public function actionCustomer()
	//	{
	//		switch ($this->_params['ac'])
	//		{
	//			//客户信息查询
	//			case 'info' :
	//				$this->render('customer/'.$this->_params['ac'], array(
	//					'params'=>$this->_params));
	//				break;
	//			default :
	//				break;
	//		}
	//	}
	

	//	public function actionGPS()
	//	{
	//		switch ($this->_params['ac'])
	//		{
	//			case 'upload' :
	//				$gps = $this->_params['gps'];
	//				$location = $this->_params['location'];
	//				$data = $this->render('gps/upload', array(
	//					'gps'=>$gps, 
	//					'location'=>$location, 
	//					true));
	//				break;
	//		}
	//	}
	

	private function accessLog()
	{
		//记录api访问日志
		$table_name = 't_api_log_'.date('Ym', time());
		
		$method = isset($this->_params['method']) ? $this->_params['method'] : '';
		$appkey = isset($this->_params['appkey']) ? $this->_params['appkey'] : '';
		$macaddress = isset($this->_params['macaddress']) ? $this->_params['macaddress'] : '';
		$from = isset($this->_params['from']) ? $this->_params['from'] : '';
		$ipaddress = Yii::app()->request->userHostAddress;
		$agent = isset(Yii::app()->request->userAgent) ? Yii::app()->request->userAgent : '';
		$timestamp = date(Yii::app()->params['formatDateTime'], time());
		$longitude = isset($this->_params['longitude']) ? $this->_params['longitude'] : '';
		$latitude = isset($this->_params['latitude']) ? $this->_params['latitude'] : '';
		
		//不记录20000001 系统内部key的日志
		if ($appkey=='20000001')
		{
			return;
		}
		
		//不记录MTK调用API系统的日志
		if ($agent=='TC_Http')
		{
			return;
		}
		
		$methods = array('driver.define.heartbeat');
		if(in_array($method, $methods)){
			return;
		}
		
		$attributes = array(
			'method'=>$method, 
			'appkey'=>$appkey, 
			'macaddress'=>$macaddress, 
			'longitude'=>$longitude, 
			'latitude'=>$latitude, 
			'ipaddress'=>$ipaddress, 
			'source'=>$from, 
			'agent'=>$agent, 
			'created'=>$timestamp);
		
		$task = array(
			'method'=>'log_api', 
			'params'=>array(
				'table_name'=>$table_name, 
				'attributes'=>$attributes));
		Queue::model()->dumplog($task);
		
	
//		@Yii::app()->dbstat->createCommand()->insert($table_name, $attributes);
	}
}
