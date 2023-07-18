<?php
//add by aiguoxin 
/**
 * get city download url by token
 *
 */
//获取App版本号
$app_version_code= isset($params['app_version_code']) ? trim($params['app_version_code']) : '';
$api_version = 1;
if (!empty($app_version_code)){
	$api_version = 2;
}
$desc= "";
$deprecated_int = 1;
$latest_int = 2;
$ret = Yii::app()->params['appVersion'];
$ret2 = array(
		"update_type" => "0",
		"update_time" =>  date('YYYY-mm-dd',time()),
		"url" => "url",
		"desc" => $desc,
		"code" => "1"
);

$token = isset($params['token']) ? trim($params['token']) : '';
if(!empty($token)){
    $driver = DriverStatus::model()->getByToken($token);
    if($driver){
	//find city
	$city_id = $driver->city_id;
	$version=DriverCityVersion::model()->getVesionByCity($city_id);
	if($version){
		$cityVersion = DriverClientVersion::model ()->findByPk ( $version ['version_id'] );
				if ($cityVersion) {
					EdjLog::info('test=01','console');
					$ret ['appVersionDriver'] ['latest'] = $cityVersion ['latest'];
					$ret ['appVersionDriver'] ['url'] = $cityVersion ['url'];
					$ret ['appVersionDriver'] ['beta_latest'] = $cityVersion ['beta_latest'];
					$ret ['appVersionDriver'] ['beta_url'] = $cityVersion ['beta_url'];
					$ret ['appVersionDriver'] ['deprecated'] = $cityVersion ['deprecated'];
					$ret ['appVersionDriver'] ['updatetime'] = $cityVersion ['updatetime'];
					$desc= $cityVersion ['up_desc'];
					$deprecated_int = $cityVersion ['deprecated_int'];
					$latest_int = $cityVersion ['latest_int'];
				} else { // 使用默认配置
				$version = DriverClientVersion::model ()->findByPk ( 1 ); // 默认第一个
				if ($version) {
					EdjLog::info('test=02','console');
					$ret ['appVersionDriver'] ['latest'] = $version ['latest'];
					$ret ['appVersionDriver'] ['url'] = $version ['url'];
					$ret ['appVersionDriver'] ['beta_latest'] = $version ['beta_latest'];
					$ret ['appVersionDriver'] ['beta_url'] = $version ['beta_url'];
					$ret ['appVersionDriver'] ['deprecated'] = $version ['deprecated'];
					$ret ['appVersionDriver'] ['updatetime'] = $version ['updatetime'];
					$deprecated_int = $version ['deprecated_int'];
					$latest_int = $version ['latest_int'];
				}
	          }
	          
    }
  }
  if ($api_version == 2) {
  	if ($latest_int<=$app_version_code){
  		$ret2["update_type"] = 0;//不用升级
  	}
  	if ( $latest_int> $app_version_code) {
  		$ret2["update_type"] = 1;//建议升级
  	}
  	if ($app_version_code<= $deprecated_int) {
  		$ret2["update_type"] = 2;//强制更新
  	}
  	$ret2["update_time"] = $ret ['appVersionDriver'] ['updatetime'] ;
  	$ret2["url"] = $ret ['appVersionDriver'] ['url'];
  	$ret2["code"] =  "0";
  	$ret2["desc"] = $desc;
  	echo json_encode($ret2);
  	return;
  }
}


echo json_encode($ret);
