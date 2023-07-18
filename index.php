<?php
if ( isset($_REQUEST['from']) && 'wuba' == $_REQUEST['from']) die('{"msg":"ok"');

date_default_timezone_set('Asia/Chongqing');
define('WEB_ROOT', dirname(__FILE__).'/');
define('LIB_ROOT', WEB_ROOT.'libs/');

$yii = LIB_ROOT.'framework/yii.php';

//根据域名选择不同的配置文件
$server = explode('.', implode('.', array_reverse(explode(':', rtrim($_SERVER['HTTP_HOST'], '.')))));
$config = array ();
$define = false;

for($j = count($server); $j>0; $j--) {
	if ($define==false) {
		$define_file = WEB_ROOT.'protected/config/define.'.implode('.', array_slice($server, -$j)).'.php';
		if (file_exists($define_file)) {
			require_once ($define_file);
		} else {
			require_once (WEB_ROOT.'protected/config/define.php');
		}
		$define = true;
	}
	
	$file = WEB_ROOT.'protected/config/config.'.implode('.', array_slice($server, -$j)).'.php';
	if (file_exists($file)) {
		
		$config = require ($file);
		break;
	}
}

if (empty($config)) {
	$config = require (WEB_ROOT.'protected/config/main.php');
}

//$config = WEB_ROOT.'protected/config/main.php';

spl_autoload_register(function($class){
    //$path = str_replace('\\', '/', substr($class, 1));
    $path = str_replace('\\', '/', $class);
    if (file_exists(WEB_ROOT.'protected/vendors/'.$path.'.php')) {
        require_once(WEB_ROOT.'protected/vendors/'.$path.'.php');
    }
});

$ssid = 'ssid';
$host = explode('.', trim($_SERVER['HTTP_HOST']));
#ecenter-xxxx-svc是k8s里的域名,在k8s内的服务调用api可以用这样的域名
$ssid_host_map = array('api' => true, 'ecenter-test-svc' => true, 'ecenter-stage-svc' => true, 'ecenter-prod-svc' => true);
if(isset($ssid_host_map[$host[0]]) && $ssid_host_map[$host[0]]){
	$ssid = empty($_REQUEST[$ssid])?'':$_REQUEST[$ssid];
	if(!empty($ssid)){
		session_id($ssid);
	}
}

require_once ($yii);
Yii::createWebApplication($config)->run();
