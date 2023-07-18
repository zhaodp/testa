<?php
if (!defined('WEB_ROOT'))
	exit('not allow access');

define('THEMES_CURRENT', 'classic');

/** cache **/
define('CACHE_ROOT', WEB_ROOT.'cache/');
define('CONF_ROOT', WEB_ROOT.'protected/config/');
define('DATA_CURRENT', WEB_ROOT.'cache/data/');
define('DATA_CACHE', WEB_ROOT.'cache/cache/');
define('DATA_RUNTIME', WEB_ROOT.'cache/runtime/');
define('DATA_ASSETS', WEB_ROOT.'cache/assets/');
define('IMAGE_ASSETS', WEB_ROOT.'images/');

/** sp_url **/
//. 
$host = $_SERVER['HTTP_HOST'];
if ($host!='www.edaijia.cn') {
	define('SP_HOST', $_SERVER['HTTP_HOST']);
} else {
	define('SP_HOST', $_SERVER['HTTP_HOST'] . '/v2');
}
define('SP_URL_HOME', 'http://'.SP_HOST.'/');
define('SP_URL_STO', SP_URL_HOME.'sto/'.THEMES_CURRENT.'/');
define('SP_URL_CSS', SP_URL_STO.'c/');
define('SP_URL_IMG', SP_URL_STO.'i/');
define('SP_URL_JS', SP_URL_STO.'j/');
define('SP_URL_HOJO', SP_URL_STO.'hojo/');
define('SP_URL_ASSETS', SP_URL_HOME.'cache/assets/');

define('SP_URL_DRIVER_IMG', 'http://'. $_SERVER['HTTP_HOST'] .'/images/');
