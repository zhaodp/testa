<?php
$dbdache = array (
		'class'=>'system.db.CDbConnection',
		'connectionString'=>'mysql:host=localhost;dbname=db_kkpinche',
		'schemaCachingDuration'=>600,
		'emulatePrepare'=>true,
		'autoConnect'=>false,
		'username'=>'sp_master',
		'password'=>'ZdHxqVGYwtWyZUaR',
		'charset'=>'utf8',
		'tablePrefix'=>'t_');

$dbdache_readonly = array (
		'class'=>'system.db.CDbConnection',
		'connectionString'=>'mysql:host=localhost;dbname=db_kkpinche',
		'schemaCachingDuration'=>600,
		'emulatePrepare'=>true,
		'autoConnect'=>false,
		'username'=>'sp_readonly',
		'password'=>'cyumnC2rbAFMvmSP',
		'charset'=>'utf8',
		'tablePrefix'=>'t_');