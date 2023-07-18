<?php
Yii::import("application.ecenter.service.*");
class ApiBaseController extends LoggerExtController {

	public function beforeAction($action){
		return parent::beforeAction($action);
	}

	public function actions() {
		return array(
		);
	}

	public function baseVerify() {
		//参数校验
		$params = $this->initParams();
		if(empty($params)){
			$this->result(ErrorCode::ERROR_PARAMS);
		}
		$appid = $params['appid'];
        	$model = AdminApp::model()->findByPk($appid);
		if(!isset($model->key)){
			$this->result(ErrorCode::APP_NOT_EXIST);
		}
		$verify = EcenterSignService::verifySign($params,$model->key);
		if(!$verify){
			$this->result(ErrorCode::ERROR_SIGN);
		}
	}

	/**
	 * 同一时刻只有一个
	 */
	protected function kickUser(){
		if(!Yii::app()->user->getIsGuest()){
			$new_unique_id = session_id();
			$nowkey = Yii::app()->user->user_id.'now_ssid';
			$oldkey = Yii::app()->user->user_id.'old_ssid';

			$now_ssid = Yii::app()->cache->get($nowkey);
			$old_ssid = Yii::app()->cache->get($oldkey);

			if($now_ssid && ($now_ssid !== $new_unique_id)){
				if($old_ssid && $old_ssid == $new_unique_id){
				    Yii::app()->user->logout(true); //如果用户session_id 变更了则需要重新登录
				}else{
					Yii::app()->cache->set($nowkey,$new_unique_id,24*3600);
					Yii::app()->cache->set($oldkey,$now_ssid,24*3600);
				}

			}else{
				Yii::app()->cache->set($nowkey,$new_unique_id,24*3600);
			}
		}
	}

	private function initParams(){
		$params = array();
		foreach($_REQUEST as $k=>$v){
			// 如果是r则删除，因为 index.php?r=resource/verify 干扰
			if($k != 'r'){
				$params[$k] = $v;
			}
		}
		if(empty($params['appid']) || 
			empty($params['sign']) 
		){
			return false;
		}
		return $params;
	}

	protected function result($code){
		$result = array();
		$result['code'] = $code;
		$result['message'] = ErrorCode::getDesc($code);

		//$result['user'] = $yiiUser;
		//$result['session'] = $_SESSION;
		
		if($code === ErrorCode::SUCCESS){
			if(!Yii::app()->user->getIsGuest()){
				$yiiUser = Yii::app()->user;
				$user = array();
				$user['type'] = $yiiUser->type; 
				$user['user_id'] = $yiiUser->user_id;
				$user['name'] = $yiiUser->name;
				$user['phone'] = $yiiUser->phone;
				$user['access_begin'] = $yiiUser->access_begin;
				$user['access_end'] = $yiiUser->access_end;
				$user['expiration_time'] = $yiiUser->expiration_time;
				$user['admin_level'] = $yiiUser->admin_level;
				$user['status'] = $yiiUser->status;
				$user['admin_user_type'] = $yiiUser->admin_user_type;
				$user['department'] = $yiiUser->department;
				$user['city'] = $yiiUser->city;
				$user['first_login'] = $yiiUser->first_login;
				if(isset($yiiUser->agent)){
					$user['agent'] = $yiiUser->agent;
				}

				$city_list = UserCity::model()->getUserCityList($yiiUser->user_id);
				if (empty($city_list)) {
					$user['city_list'] = [];
				} else {
					$user['city_list'] = $city_list;
				}

				$result['user'] = $user;
			}
		}

		echo json_encode($result);
		exit;
	}
}
