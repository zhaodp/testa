<?php

// change the following paths if necessary
$yiic=dirname(__FILE__).'/../libs/framework/yiic.php';
$config=dirname(__FILE__).'/config/console.php';
global $cronId;//sys数据库中的cronId
$cronId=0;
if(!isset($_SERVER['argc'])||!isset($_SERVER['argv'])){
    die('This script must be run from the command line and args should be given.');
}
$argc=&$_SERVER['argc'];
$argv=&$_SERVER['argv'];
if($argc>=1 && substr($argv[$argc-1],0,strlen("--cronId=")) == "--cronId="){
    $cronId = (int)substr($argv[$argc-1],strlen("--cronId="));
    unset($_SERVER['argv'][$argc-1]);
    --$_SERVER['argc'];
}

spl_autoload_register(function($class){
    //$path = str_replace('\\', '/', substr($class, 1));
    $path = str_replace('\\', '/', $class);
    if (file_exists(dirname(__FILE__).'/vendors/' . $path . '.php')) {
        require_once(dirname(__FILE__).'/vendors/' . $path . '.php');
    }
});

require_once($yiic);
