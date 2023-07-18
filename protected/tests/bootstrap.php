<?php

// change the following paths if necessary
$yiit=dirname(__FILE__).'/../../libs/framework/yiit.php';
$config=dirname(__FILE__).'/../config/test.php';

require_once($yiit);
require_once(dirname(__FILE__).'/WebTestCase.php');
require_once('vendor/autoload.php');
require_once('unit/BaseTest.php');
Yii::createWebApplication($config);
