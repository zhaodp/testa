<?php
$dbdache = array (
		'class'=>'system.db.CDbConnection',
		'connectionString'=>'mysql:host=10.241.221.106;dbname=db_dache',
		'schemaCachingDuration'=>600,
		'emulatePrepare'=>true,
		'autoConnect'=>false,
		'username'=>'sp_dache',
		'password'=>'sY4759Ck4p53mPc',
		'charset'=>'utf8',
		'tablePrefix'=>'t_');

$dbdache_readonly = array (
		'class'=>'system.db.CDbConnection',
		'connectionString'=>'mysql:host=10.241.221.106;dbname=db_dache',
		'schemaCachingDuration'=>600,
		'emulatePrepare'=>true,
		'autoConnect'=>false,
		'username'=>'sp_dache_read',
		'password'=>'sY4759Ck4p53mPc',
		'charset'=>'utf8',
		'tablePrefix'=>'t_');