<?php
// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');


// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

$test_lock = dirname(__FILE__).'/test.lock';
$dev_lock = dirname(__FILE__).'/dev.lock';
if (is_file($test_lock)) {
    require_once 'config_db_test.php';
}else if (is_file($dev_lock)) {
    require_once 'config_db_dev.php';
}else {
    require_once 'publish/config_db.php';
}
$params = require_once 'config_params.php';
$import = require_once 'config_import.php';

return array (
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..', 
    'theme'=>THEMES_CURRENT, 
    'name'=>'e代驾admin', 
    'language'=>'zh_cn', 
    
    //preloading 'log' component
        'preload'=>array (
            'log', 'logext'), 
    

    // autoloading model and component classes
    'import'=>$import, 
    
    'modules'=>array ()// uncomment the following to enable the Gii tool
    //        'gii'=>array (
    //            'class'=>'system.gii.GiiModule', 
    //            'password'=>'111111', 
    // If removed, Gii defaults to localhost only. Edit carefully to taste.
    //            'ipFilters'=>array (
    //                '127.0.0.1', 
    //                '::1'))
    , 
    
    // application components
    'components'=>array(
		'excel'=>array(
	      'class'=>'application.vendors.phpexcel.PHPExcel',
		      ),
        'assetManager'=>array(
            'class'=>'application.components.AssetManager',
        ),
        'user'=>array (
            // enable cookie-based authentication
            'allowAutoLogin'=>true, 
            'class'=>'WebUser'
        ), 
        'thumb'=>array (
            'class'=>'ext.phpthumb.EasyPhpThumb'
        ), 
        'isMobile'=>array (
            'class'=>'application.components.XDetectMobileBrowser'
        ), 
        'session'=>array (
            'class'=> 'CCacheHttpSession',
            'timeout'=>2700
        ), 
        // uncomment the following to enable URLs in path-format
        //        'urlManager'=>array (
        //            'urlFormat'=>'path', 
        //            'showScriptName'=>false, 
        //            'rules'=>array (
        //                '<action:index>'=>'site/<action>', 
        //                '<action:login>'=>'site/<action>', 
        //                '<action:logout>'=>'site/<action>', 
        //                'api/<func:[\s\S]+>'=>'api/index/func/<func>', 
        //                '<controller:\w+>/<id:\d+>'=>'<controller>/view', 
        //                '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>', 
        //                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>'
        //            ) //array('api', 'pattern'=>'api/<func:\*+>', 'verb'=>'GET'),
        //        ), 
        'db'=>$db,
        'dbadmin'=>$dbadmin,
        'dbadmin_readonly'=>$dbadmin_readonly,
        'dbstat'=>$dbstat, 
        'dbstat_proxy'=>$dbstat_proxy, 
        'db_readonly'=>$db_readonly, 
        'dbstat_readonly'=>$dbstat_readonly, 
        'dbreport' => $dbreport,
        'dbreport_readonly'=>$dbreport_readonly,
        'dbsys'=>$dbsys,
        'db_rds' => $db_rds,
        'dborder'=>$dborder,
        'dborder_readonly'=>$dborder_readonly,
        'db_finance'=> $db_finance,
        'db_activity'=>$db_activity,
        'db_pomo'=> $db_pomo,
        'mongodb'=>$mongodb,
        'db_third'=>$db_third,
        'errorHandler'=>array (
            // use 'site/error' action to display errors
            'errorAction'=>'site/error'
        ), 
        'assetManager'=>array (
            'basePath'=>DATA_ASSETS, 
            'baseUrl'=>SP_URL_ASSETS), 
        'cache'=>array (
            'class'=>'application.components.CProxyCache', 
            //'class'=>'CMemCache', 
            'useMemcached'=>true, 
            'keyPrefix'=>'edaijia_admin', 
            'servers'=>array (
                array (
                    'host'=>'cache.edaijia.cn', 
                    'port'=>11211, 
                    'weight'=>100
                )
            )
        ), 
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array (
                //log error
                array (
                    'class'=>'FileDailyLogRoute',
                    'levels'=>'error, warning',
                    'logFile'=>'v2_php_error.log',
                    'keepDays'=>7,
                    'logPath'=>'/data/logs/v2',
                ),
            )
        ),
	'logext'=>array(
            'class'=>'LoggerExt',
            'isTrace'=>true,
            'controllers'=>array(
                'api'=>array(
                    'logFileUseParam'=>true,  //enable param used in logfile name
                    'params'=>'method',
                    'logFilePattern'=>'edaijia_api.log',
                    'paramPattern'=>'%n_%v',
                    'joinCharacter'=>'.',
                    'logPathPattern'=>'v2',
                ),
                '*'=>array(
		    'logPathPattern'=>'v2',
                    'logFilePattern'=>'edaijia_controller.log'
                )
            ),
            'route'=>array(
                'class'=>'FileDailyLogRoute',
                'levels'=>'info,warning,error', //log level
                'keepDays'=>7,  // keep log file at 7 days
                'logPath'=>'/data/logs',
		'logPattern'=>'%c.%a'	
            ),
        )
    ), 
    // uncomment the following to show log messages on web pages
    /*
                array(
                    'class'=>'CWebLogRoute',
                ),
                */
    
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params'=>$params);

