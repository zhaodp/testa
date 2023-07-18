<?php
date_default_timezone_set('Asia/Chongqing');
// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
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
    'name'=>'Console Application',
    'import'=>$import,
    // preloading 'log' component
    'preload'=>array (
        'log', 'logext'
    ),

    'behaviors' => array('ConsoleApplicationBehavior' => array('class' => 'ConsoleApplicationBehavior')),

    // application components
    'components'=>array (
        // uncomment the following to use a MySQL database
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
        'dborder'=>$dborder,
        'dborder_readonly'=>$dborder_readonly,
        'db_finance'=>$db_finance,
        'db_activity'=>$db_activity,
        'db_pomo'=>$db_pomo,
        'db_order_tmp'=>$db_order_tmp,
        'db_third'=>$db_third,
        //'db_rds' => $db_rds,
        'mongodb'=>$mongodb,
        'thumb'=>array (
            'class'=>'ext.phpthumb.EasyPhpThumb'
        ),
        'cache'=>array (
            'class'=>'CMemCache',
            'useMemcached'=>true,
            'keyPrefix'=>'edaijia',
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
                    'logFile'=>'job_php_error.log',
                    'keepDays'=>7,
                    'logPath'=>'/data/logs',
                ),
            )
        ),
        'logext'=>array(
            'class'=>'LoggerExt',
            'autoFlush'=>1,
            'autoDump'=>true,
            'isTrace'=>true,
            // for console
            'commands'=>array(
                'queue'=>array(
                    'logFileUseParam'=>true,
                    'params'=>'qname',
                    'logFilePattern'=>'console.%c.%a.%p.log',
                    'paramPattern'=>'%n_%v',
                    'joinCharacter'=>'.',
                    'logPathPattern'=>'%c',
                	'logInOne'=>true,
                ),
                '*'=>array(
                    'logFilePattern'=>'console.%c.%a.log',
                    'logPathPattern'=>'%c'
                )
            ),
            'route'=>array(
                'class'=>'FileDailyLogRoute',
                'levels'=>'info,warning,error', //log level
                'keepDays'=>7,  // keep log file at 7 days
                'logPath'=>'/data/logs',
	        	'logPattern'=>'application',
            	'logInOnePath'=>'out.log'
            ),
        ),

    ),
    'params'=>$params);
