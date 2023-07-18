<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends LoggerExtController {
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout = '//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu = array ();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs = array ();
	
	/**
	 * 调用edaijia api server的key
	 * @var string
	 */
	public $api_key;
	
	/**
	 * render输出的格式定义，支持html,json
	 * @var string
	 */
	public $format;
        
    /**
     * 全局搜索
     */
    public $q;

      /**访问Action的当前时戳  单位:毫秒
       */
      public $visit_time;

        public function init() {
		parent::init();
		
            //初始化API key
            $this->api_key = Yii::app()->params['edj_api_key'];
            $is_guest  = Yii::app()->user->getIsGuest();
            //if($is_guest  ||  (Yii::app()->user->type == AdminUserNew::USER_TYPE_DRIVER)){
            if($is_guest){
                //$host = $_SERVER['HTTP_HOST'];
                //if ($host=='www.edaijia.cn'||$host=='www.edaijia.cc') {
                    if (Yii::app()->isMobile->isMobileBrowser()==true) {
                        Yii::app()->theme = 'mobile';
                    }
                //}
            }

	}

	public function setSsidTime($ssid, $time=0){
		$host = explode('.', trim($_SERVER['HTTP_HOST']));
		$count = count($host);
		$domain = '.' . $host[$count-2] . '.' . $host[$count-1];
		setcookie($ssid,session_id(),$time,"/",$domain);
		//设置菜单的当前链接位置
		setcookie('labelId','0.1',0,"/",$domain);
	}

	/**
	 * 设置ssid
	 */
	public function setSsid($action = null){
		$ssid = 'ssid';

		if(Yii::app()->user->getIsGuest()){

			if(!empty($_COOKIE[$ssid])){
				$this->setSsidTime($ssid, time()-3600);
			}
		}else{
			if(empty($_COOKIE[$ssid]) || $_COOKIE[$ssid] != session_id()){
				$this->setSsidTime($ssid);
			}
		}
		// 如果default left 未登录则返回json
		if($action && Yii::app()->user->getIsGuest()){
			$controller = Yii::app()->controller->getId();
			$method = $action->getId();
			if($controller == 'default' && $method == 'left'){
				$callback = empty($_GET['callback'])?'':$_GET['callback'];
				if(!empty($callback)){
					echo "$callback(".json_encode('').")";
				}
				exit;
			}
		}
	}
	
	public function beforeAction($action) {
		$this->setSsid($action);
		//return parent::beforeAction($action);
		//init the start time
		$this->visit_time=$this->getMillisecond();
		$controller_id = $this->getUniqueId();
		$action_id = $this->defaultAction;

        if ($action!==NULL) {
            $action_id = $action->getId();
        }
        //////////////
        $is_guest  = Yii::app()->user->getIsGuest();
        //Yii::app()->user->logout(true);die;
        //登录用户只能在一个位置登录
        if(!$is_guest && !in_array($controller_id,array('order','client')) &&  !in_array($action_id , array('dispatch','ajax')) ){

            $new_unique_id =  session_id(); //$_COOKIE['PHPSESSID']; echo
            $nowkey = Yii::app()->user->user_id.'now_ssid';
            $oldkey = Yii::app()->user->user_id.'old_ssid';

            $now_ssid = Yii::app()->cache->get($nowkey);
            $old_ssid = Yii::app()->cache->get($oldkey);
            //$old_unique_id = yii::app()->cache->set($key, $value, $expire);

            if($now_ssid && ($now_ssid !== $new_unique_id)){
                if($old_ssid && $old_ssid == $new_unique_id){
                    Yii::app()->user->logout(true); //如果用户session_id 变更了则需要重新登录
		    return parent::beforeAction($action);
                }
                Yii::app()->cache->set($nowkey,$new_unique_id,24*3600);
                Yii::app()->cache->set($oldkey,$now_ssid,24*3600);

            }else{
                $now_ssid = Yii::app()->cache->set($nowkey,$new_unique_id,24*3600);
            }
        }

        /////////////



		if( defined ( "CURRENT_ENV" ) && 'dev' == CURRENT_ENV ){
			$is_allow = true;
		}else{
            //echo $controller_id.'----'.$action_id;die;
            $action_mod = new AdminActions();
			$is_allow = $action_mod->havepermission($controller_id, $action_id);
            //var_dump($is_allow);die;
		}



		//echo $controller_id;echo  $action_id;echo $is_allow;die();
		if ($is_allow == true) {
		  // Add check if the user is kicked
		  $user_name = Yii::app()->user->getUserName();
		  $kick_key = 'ECENTER_KICK_KEY_'.$user_name;
		  $kicked = Yii::app()->cache->get($kick_key);

		  if(!empty($kicked)) {
		    // Destory the session
		    Yii::app()->cache->delete($kick_key);
		    Yii::app()->session->clear();
		    Yii::app()->session->destroy();
		  }
			return parent::beforeAction($action);
		}

		$url = $this->createAbsoluteUrl('site/login', array (
			'controller'=>$controller_id, 
			'action'=>$action_id));
		$this->redirect($url);
	
	//		$this->render('//site/error', array (
	//		'code'=>-9000, 
	//		'message'=>"未被授权访问此功能，请联系管理员。$controller_id, $action_id"));
	//		throw new Exception("未被授权访问此功能，请联系管理员。$controller_id, $action_id", -9001, NULL);
	}
	
	/**
	 * 对www.edaijia.cn/v2 的请求纪录访问日志
	 * 
	 * @author sunhongjing 2013-05-30
	 * @param unknown_type $action
	 */
	public function afterAction($action){
        $server = Yii::app()->request->getServerName();
	    $url = Yii::app()->request->getUrl();
	    //对某些方法不纪录访问日志
	    $controller = Yii::app()->controller->getId();  // controller
	    $method = $action->getId(); // action
            $controller_id = $this->getUniqueId();
            $action_id = $this->defaultAction;
            if ($action!==NULL) {
              $action_id = $action->getId();
            }
	     
	    //print performance log
	    $this->perLog($controller_id."/".$action_id);
            	
		
            //////////////
            $is_guest  = Yii::app()->user->getIsGuest();
            //Yii::app()->user->logout(true);die;
            //登录用户只能在一个位置登录
	    /*{{{*/
            if(!$is_guest){
              $user_id = Yii::app()->user->user_id;
              if(!is_numeric($user_id)){
                $user_id = 0;
              }
              $url = Yii::app()->request->getUrl();
              $url_length = strlen($url);
              if($url_length>=255){
                $url = substr($url,0,254); 
              }

              $params = array (
                'user_id' => $user_id, 
                'username'=>Yii::app()->user->getUserName(),
                'ip'	=> Common::getClientRealIp(),
                'agent' => Yii::app()->request->getUserAgent(), 
                'status'=> 2, 
                'url'   => $url, 
                'created'=>date(Yii::app()->params['formatDateTime'], time())
              );
              //纪录访问日志
              //访问人的ip,user_id,访问url,访问时间
              $task=array(
                'method'=>'admin_opt_log',
                'params'=>$params
              );
              Queue::model()->dumplog($task);
            }
	    /*}}}*/
        
        return parent::afterAction($action);
    }
	
	/**
	 * 重载父类过滤方法
	 * @author sunhongjing 2013-05-12
	 * @return array
	 */
	public function filters()
	{
		//只针对www.edaijia.cn域名增加兼职登陆限制	
		$server = Yii::app()->request->getServerName();
		if( 0 == strcasecmp($server, 'www.edaijia.cn') ){
			return array(
				//'checkPartTimeUserIp',
			);
		}else{
			return array();
		}	
	}
 
	/**
	 * 用户过滤器方法，对兼职用户的访问ip进行过滤
	 * 
	 * @param unknown_type $filterChain  action object
	 * @author sunhongjing 2013-05-11
	 */
	public function FilterCheckPartTimeUserIp($filterChain){	
		
		//1得到当前登录用户信息,判断是否兼职，如果兼职，只允许在固定ip登陆 add by sunhongjing 2013-05-11
		//如果是guest访问，那么放过让beforeAction去处理，如果是登陆用户，就直接判断了
		if( ! Yii::app()->user->getIsGuest()  ){		
			$userType = Yii::app()->user->getUserType() ;
			//var_dump($userType);
			//公司员工 $userType = 2
			if( 2 == $userType ){			
				//0为兼职，1为全职，-1为非登陆用户
				$callCenterUserType = Yii::app()->user->getCallCenterUserType();
				//var_dump($callCenterUserType);
				if( 0==$callCenterUserType ){
					//58.30.38.132  124.204.239.0/24 124.204.203.40
					$user_host_ip = Yii::app()->request->getUserHostAddress();
					if( '127.0.0.1'!=$user_host_ip && '58.30.38.132'!=$user_host_ip 
						&& '124.204.239' != substr($user_host_ip,0,strrpos($user_host_ip,'.') ) 
						&& '124.204.203' != substr($user_host_ip,0,strrpos($user_host_ip,'.') )
						){ 
						Yii::app()->user->logout(true);
echo <<<eof
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta charset="UTF-8" />
</head>
<body>
<div>抱歉，您当前的位置禁止登陆。</div>
</body>
</html>
eof;
exit; 
					}		
				}
			}
		}
		
		$filterChain->run();
	
	}
	
	
	public function render($view, $data = NULL, $return = false) {
		if ($this->format=='json') {
			$this->layout = false;
			header('Content-Type: application/json; charset=utf-8');
			
			$ret = parent::render($view, $data, true);
			if (isset($_REQUEST['callback'])) {
				$callback = $_REQUEST['callback'];
				echo $callback.'('.$ret.')';
			} else {
				echo $ret;
			}
			Yii::app()->end();
		} else {
			parent::render($view, $data, $return);
		}
	}

    public function alertWindow($msg,$url=''){
        echo "<meta charset='utf-8'/>";
        echo "<script type='text/javascript'>alert('{$msg}');";
        if($url) echo 'window.location="'.$url.'"';
        else echo "history.back();";
        echo "</script>";
        Yii::app()->end();
    }




  function getMillisecond(){                                                                                                                   
        list($s1, $s2) = explode(' ', microtime()); 
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000); 
  }

  
  function perLog($uri){
	$spendTime=$this->getMillisecond() - $this->visit_time;
	$log="|performance|".$uri."|".$spendTime;
        if($spendTime>1000){
          $log=$log."|SLOW";
        }
         EdjLog::warning($log);

  }

  function getParam($name, $default = '', $method = 'get') {
      return Common::getParam($name, $default, $method);
  }

  function outputJson($data = NULL, $msg = 'success',  $code = 0){
      Common::outputJson($data, $msg, $code);
  }

  function outPutError($code, $msg = NULL){
      $this->outputJson(NULL, $msg, $code);
  }

    public function responseAjax($code = 1,$msg='',$data=array()){
        echo json_encode(array('code'=>$code,'message'=>$msg,'data'=>$data));
        Yii::app()->end();
    }

}
