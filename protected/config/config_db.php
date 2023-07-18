<?php
$db = array (
	'class'=>'system.db.CDbConnection', 
	'connectionString'=>'mysql:host=10.160.37.139;dbname=db_car', 
	'schemaCachingDuration'=>600, 
	'emulatePrepare'=>true, 
	'autoConnect'=>false, 
	'username'=>'sp_car_master', 
	'password'=>'uMTNwWqnqjt5CKPa', 
	'charset'=>'utf8', 
	'tablePrefix'=>'t_');
$dbadmin = array (
    'class'=>'system.db.CDbConnection',
    'connectionString'=>'mysql:host=10.160.37.139;dbname=db_admin',
    'schemaCachingDuration'=>600,
    'emulatePrepare'=>true,
    'autoConnect'=>false,
    'username'=>'sp_car_master',
    'password'=>'uMTNwWqnqjt5CKPa',
    'charset'=>'utf8',
    'tablePrefix'=>'t_');
$dbstat = array (
	'class'=>'system.db.CDbConnection', 
	'connectionString'=>'mysql:host=10.160.37.139;dbname=db_stat', 
	'schemaCachingDuration'=>600, 
	'emulatePrepare'=>true, 
	'autoConnect'=>false, 
	'username'=>'sp_stat_master', 
	'password'=>'XpMfGYWFbAvaYQyb', 
	'charset'=>'utf8', 
	'tablePrefix'=>'t_');
$dbreport = array (
	'class'=>'system.db.CDbConnection', 
	'connectionString'=>'mysql:host=10.160.37.139;dbname=db_report', 
	'schemaCachingDuration'=>600, 
	'emulatePrepare'=>true, 
	'autoConnect'=>false, 
	'username'=>'sp_report', 
	'password'=>'xMr6trenmTVvUQVm', 
	'charset'=>'utf8', 
	'tablePrefix'=>'t_');
$db_readonly = array (
	'class'=>'system.db.CDbConnection', 
	'connectionString'=>'mysql:host=10.242.174.65;dbname=db_car', 
	'schemaCachingDuration'=>600, 
	'emulatePrepare'=>true, 
	'autoConnect'=>false, 
	'username'=>'sp_car_readonly', 
	'password'=>'uMTNwWqnqjt5CKPa', 
	'charset'=>'utf8', 
	'tablePrefix'=>'t_');
$dbadmin_readonly = array (
    'class'=>'system.db.CDbConnection',
    'connectionString'=>'mysql:host=10.242.174.65;dbname=db_admin',
    'schemaCachingDuration'=>600,
    'emulatePrepare'=>true,
    'autoConnect'=>false,
    'username'=>'sp_car_readonly',
    'password'=>'uMTNwWqnqjt5CKPa',
    'charset'=>'utf8',
    'tablePrefix'=>'t_');
$dbstat_readonly = array (
	'class'=>'system.db.CDbConnection', 
	'connectionString'=>'mysql:host=10.242.174.65;dbname=db_stat', 
	'schemaCachingDuration'=>600, 
	'emulatePrepare'=>true, 
	'autoConnect'=>false, 
	'username'=>'sp_stat_readonly', 
	'password'=>'XpMfGYWFbAvaYQyb', 
	'charset'=>'utf8', 
	'tablePrefix'=>'t_');
$db_rds = array (
	'class'=>'system.db.CDbConnection', 
	'connectionString'=>'mysql:host=10.200.177.63;dbname=db1oli0697v5wc77', 
	'schemaCachingDuration'=>600, 
	'emulatePrepare'=>true, 
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
	'connectionString'=>'mysql:host=10.160.37.139;dbname=db_sys', 
	'schemaCachingDuration'=>600, 
	'emulatePrepare'=>true, 
	'autoConnect'=>false, 
	'username'=>'sp_sys', 
	'password'=>'1trb7qPaqnf29rW', 
	'charset'=>'utf8', 
	'tablePrefix'=>'sys_');

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
//财务切换开始使用过的数据库配置 TODO ... 上线前,再次确认
$db_finance = array (
	'class'=>'system.db.CDbConnection',
	'enableProfiling'=>true, //开启性能 显示SQL 执行时间 zhanglimin add 2013-05-11
	'connectionString'=>'mysql:host=10.132.22.171;dbname=finance',
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
    'username'=>'write',
    'password'=>'write',
    'charset'=>'utf8',
    'tablePrefix'=>'t_');

$db_pomo = array (
    'class'=>'system.db.CDbConnection',
    'enableProfiling'=>true, //开启性能 显示SQL 执行时间 zhanglimin add 2013-05-11
    'connectionString'=>'mysql:host=rdsz3iazjuyvfzz.mysql.rds.aliyuncs.com;dbname=pomo',
    'schemaCachingDuration'=>0,
    'emulatePrepare'=>true,
    'autoConnect'=>false,
    'username'=>'sp_car_master',
    'password'=>'uMTNwWqnqjt5CKPa',
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
    'charset'=>'utf8');
