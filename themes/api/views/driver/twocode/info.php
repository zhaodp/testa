<?php
/**
 * get driver info for two code
 * @author aiguoxin
 * @version 2014-04-22
 * 
 */


//接收并验证参数
$driverId=$_GET["driverId"];
$callback=isset($_GET["callback"])?$_GET["callback"]:"";

if(empty($driverId)){
	$ret=array('code'=>2 , 'message'=>'缺少driverId参数');
	$json_str=json_encode($ret);
	if(isset($callback)&&!empty($callback)){
		$json_str=$callback.'('.$json_str.')';
	}
	echo $json_str;Yii::app()->end();
}

$driver = new Driver();
$model = $driver->find('user = :user',array(':user'=>$driverId));
if(empty($model)){
	$ret=array('code'=>2 , 'message'=>'没有找到司机信息');
	$json_str=json_encode($ret);
	if(isset($callback)&&!empty($callback)){
		$json_str=$callback.'('.$json_str.')';
	}
	echo $json_str;Yii::app()->end();
}

//find driver info
// $cardUrl=Driver::getIdCardPic($driverId);
$cardUrl=$model['picture'];
if(!strstr($cardUrl, "http://pic.edaijia.cn/")){
	$cardUrl='http://pic.edaijia.cn/0/default_driver.jpg_small?ver=1401872845';
}

//bonusNum
$bonus_city = Dict::items("bonus_city");
$city_prefix = Dict::items("city_prefix");
$prefix = $city_prefix[$model->city_id];
$bonus_code = array_search($prefix, $bonus_city);


$driverNum=substr($driverId,2);
$bonusNum=$bonus_code.$driverNum;

$info=array(
	'cardUrl'=>$cardUrl,
	'name'=>$model->name,
	'driverId'=>$driverId,
	'bonusNum'=>$bonusNum,
	);

//返回成功信息
$ret=array('code'=>0 , 'message'=>'ok', 'data'=>$info);
$json_str=json_encode($ret);
if(isset($callback)&&!empty($callback)){
	$json_str=$callback.'('.$json_str.')';
}
echo $json_str;Yii::app()->end();?>