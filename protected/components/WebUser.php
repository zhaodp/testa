<?php
class WebUser extends CWebUser {
	// Store model to not repeat query.
	private $_model;

	public function getUserType() {
		return isset(Yii::app()->user->type) ? Yii::app()->user->type : null;
	}

	public function getCallCenterUserType(){
		return isset(Yii::app()->user->admin_user_type) ? Yii::app()->user->admin_user_type : -1;
	}

//	public function getUserRoles() {
//		return Yii::app()->user->roles;
//	}

	public function getCity() {
		return isset(Yii::app()->user->city) ? Yii::app()->user->city : 0;
	}

	public function getAdminLevel()
	{
		return Yii::app()->user->admin_level;
	}

	public function getUserId()
	{
		return Yii::app()->user->user_id;
	}

	public function getUserName()
	{
		return Yii::app()->user->name;
	}

    public function getDepartment()
    {
        return isset(Yii::app()->user->department) ? Yii::app()->user->department : '';
    }


	public function getCurrentUserInfo(){
		$user['user_id'] = Yii::app()->user->user_id;
		$user['name'] = Yii::app()->user->name;
		$user['phone'] = Yii::app()->user->phone;
		$user['city'] = Yii::app()->user->city;
		$user['access_begin'] = isset(Yii::app()->user->access_begin) ? Yii::app()->user->access_begin : '';
		$user['access_end'] = isset(Yii::app()->user->access_end) ? Yii::app()->user->access_end : '';
		$user['expiration_time'] = isset(Yii::app()->user->expiration_time) ? Yii::app()->user->expiration_time : '';
		$user['admin_level'] = Yii::app()->user->admin_level;
		//$user['admin_level'] = 0;
		$user['type'] = Yii::app()->user->type;
		$user['user_type'] = Yii::app()->user->admin_user_type;
		$user['status'] = Yii::app()->user->status;
		//$user['roles'] = Yii::app()->user->roles;
        $user['department'] = isset(Yii::app()->user->department) ? Yii::app()->user->department : '';


		return $user;
	}




//	public function checkAccess($operation, $params = array()) {
//		print_r($operation);die();
//		//所有的guest组都可访问
//		if ($operation=='*') {
//			return true;
//		} elseif ($operation=='@'&&!Yii::app()->user->getIsGuest()) {
//			//所有的登录用户组都可以访问
//			return true;
//		}
//		//只限指定用户组访问
//		return isset(Yii::app()->user->roles)&&in_array($operation, Yii::app()->user->roles);
//	}

	public function login($identity, $duration = 0) {
		$ret = parent::login($identity, $duration);
		return $ret;
	}

//	public function logout($destroySession = true){
//		//$this->setState('roles', null);
//		print_r(Yii::app()->user->roles);die();
//		parent::logout();
//	}

	// Load user model.
	protected function loadUser($id, $type) {
		if ($this->_model===null) {
			if ($id!==null)
				switch ($type) {
					//司机
					case 1 :
						$this->_model = Employee::model()->find('user=:user', array (
							':user'=>Yii::app()->user->getId));
						break;
					case 2 :
						$this->_model = AdminUserNew::model()->find('name=:name', array (
							':name'=>md5(Yii::app()->user->getId)));
						break;
				}
		}
		return $this->_model;
	}
}
