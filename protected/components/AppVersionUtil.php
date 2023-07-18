<?php

class AppVersionUtil {
	

	public static function getAndroidAppInfo($type=0){
		$params=Yii::app()->params['app_source'];
		if(!isset($params[$type])){
			//type不存在，则默认为0
			$type=0;	
		}
		
		return $params[$type];
	}
}
