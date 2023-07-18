<?php
$db = array (
	'class'=>'system.db.CDbConnection', 
    'enableProfiling'=>true, //开启性能 显示SQL 执行时间 liuxiaobo add 2013-12-11
	'connectionString'=>'mysql:host=db.edaijia.cn;dbname=db_car', 
	'schemaCachingDuration'=>600, 
	'emulatePrepare'=>true,
    'enableParamLogging'=>true,
	'autoConnect'=>false, 
	'username'=>'edaijia', 
	'password'=>'XpMfGYWFbAvaYQyb', 
	'charset'=>'utf8', 
	'tablePrefix'=>'t_');
$dbadmin = array (
    'class'=>'system.db.CDbConnection',
    'enableProfiling'=>true, //开启性能 显示SQL 执行时间 zhanglimin add 2013-05-11
    'connectionString'=>'mysql:host=db.edaijia.cn;dbname=db_admin',
    'schemaCachingDuration'=>600,
    'emulatePrepare'=>true,
    'autoConnect'=>false,
    'username'=>'edaijia',
    'password'=>'XpMfGYWFbAvaYQyb',
    'charset'=>'utf8',
    'tablePrefix'=>'t_');
$dbstat = array (
	'class'=>'system.db.CDbConnection', 
	'connectionString'=>'mysql:host=db.edaijia.cn;dbname=db_stat', 
	'schemaCachingDuration'=>600, 
	'emulatePrepare'=>true,
    'enableParamLogging'=>true,
	'autoConnect'=>false, 
	'username'=>'edaijia', 
	'password'=>'XpMfGYWFbAvaYQyb', 
	'charset'=>'utf8', 
	'tablePrefix'=>'t_');
$dbreport = array (
	'class'=>'system.db.CDbConnection', 
	'connectionString'=>'mysql:host=db.edaijia.cn;dbname=db_report', 
	'schemaCachingDuration'=>600, 
	'emulatePrepare'=>true,
    'enableParamLogging'=>true,
	'autoConnect'=>false, 
	'username'=>'edaijia', 
	'password'=>'XpMfGYWFbAvaYQyb', 
	'charset'=>'utf8', 
	'tablePrefix'=>'t_');
$dbreport_readonly = array (
    'class'=>'system.db.CDbConnection',
    'connectionString'=>'mysql:host=db.edaijia.cn;dbname=db_report',
    'schemaCachingDuration'=>600,
    'emulatePrepare'=>true,
    'enableParamLogging'=>true,
    'autoConnect'=>false,
    'username'=>'edaijia',
    'password'=>'XpMfGYWFbAvaYQyb',
    'charset'=>'utf8',
    'tablePrefix'=>'t_');

$db_readonly = array (
	'class'=>'system.db.CDbConnection', 
    'enableProfiling'=>true, //开启性能 显示SQL 执行时间 liuxiaobo add 2013-12-11
	'connectionString'=>'mysql:host=db_readonly.edaijia.cn;dbname=db_car', 
	'schemaCachingDuration'=>600, 
	'emulatePrepare'=>true,
    'enableParamLogging'=>true,
	'autoConnect'=>false, 
	'username'=>'edaijia', 
	'password'=>'XpMfGYWFbAvaYQyb',  
	'charset'=>'utf8', 
	'tablePrefix'=>'t_');
$dbadmin_readonly = array (
    'class'=>'system.db.CDbConnection',
    'enableProfiling'=>true, //开启性能 显示SQL 执行时间 zhanglimin add 2013-05-11
    'connectionString'=>'mysql:host=db_readonly.edaijia.cn;dbname=db_admin',
    'schemaCachingDuration'=>600,
    'emulatePrepare'=>true,
    'autoConnect'=>false,
    'username'=>'edaijia',
    'password'=>'XpMfGYWFbAvaYQyb',
    'charset'=>'utf8',
    'tablePrefix'=>'t_');

$dbstat_readonly = array (
	'class'=>'system.db.CDbConnection', 
	'connectionString'=>'mysql:host=db_readonly.edaijia.cn;dbname=db_stat', 
	'schemaCachingDuration'=>600, 
	'emulatePrepare'=>true,
    'enableParamLogging'=>true,
	'autoConnect'=>false, 
	'username'=>'edaijia', 
	'password'=>'XpMfGYWFbAvaYQyb',  
	'charset'=>'utf8', 
	'tablePrefix'=>'t_');
$db_rds = array (
	'class'=>'system.db.CDbConnection', 
	'connectionString'=>'mysql:host=10.200.177.63;dbname=db1oli0697v5wc77', 
	'schemaCachingDuration'=>600, 
	'emulatePrepare'=>true,
    'enableParamLogging'=>true,
	'autoConnect'=>false, 
	'username'=>'db1oli0697v5wc77', 
	'password'=>'db1oli0697v5wc77', 
	'charset'=>'utf8', 
	'tablePrefix'=>'t_');
$mongodb = array (
	'class'=>'Mongo', 
	'connectionString'=>'mongodb://mongo.edaijia.cn:27017', 
	'dbName'=>'driver', 
	'fsyncFlag'=>false, 
	'safeFlag'=>true, 
	'upsertFlag'=>true);
$dbsys = array (
	'class'=>'system.db.CDbConnection', 
	'connectionString'=>'mysql:host=db.edaijia.cn;dbname=db_sys', 
	'schemaCachingDuration'=>600, 
	'emulatePrepare'=>true,
    'enableParamLogging'=>true,
	'autoConnect'=>false, 
	'username'=>'sp_sys', 
	'password'=>'fbUWUpkxGOk8rAj', 
	'charset'=>'utf8', 
	'tablePrefix'=>'sys_');

$dbdache = array (
    'class'=>'system.db.CDbConnection',
    'connectionString'=>'mysql:host=db.edaijia.cn;dbname=db_dache',
    'schemaCachingDuration'=>600,
    'emulatePrepare'=>true,
    'enableParamLogging'=>true,
    'autoConnect'=>false,
    'username'=>'sp_dache',
    'password'=>'39ZdWS5qF64N7cVf',
    'charset'=>'utf8',
    'tablePrefix'=>'t_');

$dbdache_readonly = array (
    'class'=>'system.db.CDbConnection',
    'connectionString'=>'mysql:host=db.edaijia.cn;dbname=db_dache',
    'schemaCachingDuration'=>600,
    'emulatePrepare'=>true,
    'enableParamLogging'=>true,
    'autoConnect'=>false,
    'username'=>'sp_dache_read',
    'password'=>'39ZdWS5qF64N7cVf',
    'charset'=>'utf8',
    'tablePrefix'=>'t_');

$dbstat_proxy = array (
    'class'=>'system.db.CDbConnection', 
    'connectionString'=>'mysql:host=10.165.5.175;port=1236;dbname=db_proxy_stat',
    'schemaCachingDuration'=>600, 
    'emulatePrepare'=>true, 
    'autoConnect'=>false, 
    'username'=>'db_proxy', 
    'password'=>'edaijiaproxyT1', 
    'charset'=>'utf8',
    'tablePrefix'=>'t_');

$dborder = array (
    'class'=>'system.db.CDbConnection',
    'connectionString'=>'mysql:host=db.edaijia.cn;dbname=db_order',
    'schemaCachingDuration'=>600,
    'emulatePrepare'=>true,
    'enableParamLogging'=>true,
    'autoConnect'=>false,
    'username'=>'edaijia',
    'password'=>'XpMfGYWFbAvaYQyb',
    'charset'=>'utf8',
    'tablePrefix'=>'t_');

$dborder_readonly = array (
    'class'=>'system.db.CDbConnection',
    'connectionString'=>'mysql:host=db.edaijia.cn;dbname=db_order',
    'schemaCachingDuration'=>600,
    'emulatePrepare'=>true,
    'enableParamLogging'=>true,
    'autoConnect'=>false,
    'username'=>'edaijia',
    'password'=>'XpMfGYWFbAvaYQyb',
    'charset'=>'utf8',
    'tablePrefix'=>'t_');
//财务切换开始使用过的数据库配置 TODO ... 上线前,再次确认
$db_finance = array (
	'class'=>'system.db.CDbConnection',
	'enableProfiling'=>true, //开启性能 显示SQL 执行时间 zhanglimin add 2013-05-11
	'connectionString'=>'mysql:host=db.edaijia.cn;dbname=db_finance',
	'schemaCachingDuration'=>0,
	'emulatePrepare'=>true,
	'autoConnect'=>false,
	'username'=>'edaijia',
	'password'=>'XpMfGYWFbAvaYQyb',
	'charset'=>'utf8',
	'tablePrefix'=>'t_');

$db_activity = array (
    'class'=>'system.db.CDbConnection',
    'enableProfiling'=>true, //开启性能 显示SQL 执行时间 zhanglimin add 2013-05-11
    'connectionString'=>'mysql:host=db.edaijia.cn;dbname=db_activity',
    'schemaCachingDuration'=>0,
    'emulatePrepare'=>true,
    'autoConnect'=>false,
    'username'=>'edaijia',
    'password'=>'XpMfGYWFbAvaYQyb',
    'charset'=>'utf8',
    'tablePrefix'=>'t_');

$db_pomo = array (
    'class'=>'system.db.CDbConnection',
    'enableProfiling'=>true, //开启性能 显示SQL 执行时间 zhanglimin add 2013-05-11
    'connectionString'=>'mysql:host=db.edaijia.cn;dbname=pomo',
        'schemaCachingDuration'=>0,
        'emulatePrepare'=>true,
        'autoConnect'=>false,
        'username'=>'edaijia',
        'password'=>'XpMfGYWFbAvaYQyb',
    'charset'=>'utf8',
    'tablePrefix'=>'t_');
$db_third = array (
    'class'=>'system.db.CDbConnection',
    'enableProfiling'=>true, //开启性能 显示SQL 执行时间 zhanglimin add 2013-05-11
    'connectionString'=>'mysql:host=db.edaijia.cn;dbname=db_third',
    'schemaCachingDuration'=>0,
    'emulatePrepare'=>true,
    'autoConnect'=>false,
    'username'=>'edaijia',
    'password'=>'XpMfGYWFbAvaYQyb',
    'charset'=>'utf8',
    'tablePrefix'=>'t_');
$db_order_tmp = array (
    'class'=>'system.db.CDbConnection',
    'connectionString'=>'mysql:host=db.edaijia.cn;dbname=db_order',
    'schemaCachingDuration'=>600,
    'emulatePrepare'=>true,
    'enableParamLogging'=>true,
    'autoConnect'=>false,
    'password'=>'XpMfGYWFbAvaYQyb',
    'charset'=>'utf8',
    'tablePrefix'=>'t_');

