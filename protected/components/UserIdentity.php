<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {

    private $userlimit_key = 'DRIVER_LOGIN_TIMES';

	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate() {
		$user = '';
		$password = '';

		/**
		 * 检查登录用户的类型
		 * 1、检查登录账号是不是司机
		 * 2、检查是不是后台管理人员
		 */
		
		$employee = Driver::model()->find('user=:user and mark=0', array (
			':user'=>$this->username));

		if ($employee) { 
             //20160415 禁止司机登录
             return false;
			$this->setState('type', AdminUserNew::USER_TYPE_DRIVER);
            $this->setState('user_id', $this->username);
			//$this->setState('roles', array(AdminGroup::model()->getID('drivers')));
			$this->setState('city', $employee->city_id);
			$password = md5($employee->password);
		} else {
			$admin = AdminUserNew::model()->find('name=:name and status=:status', array (
				':name'=>$this->username,':status'=>AdminUserNew::STATUS_NORMAL));
			if ($admin) {

				$this->setState('type', AdminUserNew::USER_TYPE_ADMIN);
				//$this->setState('roles', explode(',', $admin->roles));
				//add by sunhongjing 2013-03-22
				$this->setState('user_id', $admin->id);
				$this->setState('name', $admin->name);
				$this->setState('phone', $admin->phone);
				$this->setState('access_end', $admin->access_end);
				$this->setState('access_begin', $admin->access_begin);
				$this->setState('expiration_time', $admin->expiration_time);
				$this->setState('admin_level', $admin->level);
				$this->setState('status', $admin->status);
				$this->setState('admin_user_type', $admin->type);
                $this->setState('department', $admin->department_id);
				
				$this->setState('city', $admin->city_id);
				$this->setState('first_login', $admin->first_login);
				$agent = AdminAgent::model()->getAgent($admin->id);
				if($agent){
					$this->setState('agent', $agent->attributes);
				}
				$password = $admin->password;
			}
		}

		if($employee){
            return false;
            if ($password != md5($this->password)){
                $login_limit = $this->limitDriver($employee->user,true); //第二个参数 ： 登录次数会+1；
                if($login_limit == true){
                    $this->errorCode = 3;
                }
                $this->errorCode = self::ERROR_PASSWORD_INVALID;
            }
            else {
                $login_time = $this->getLimitDriver($employee->user); //只查看登录次数，如果大于5次 即使密码正确也不允许登录
                if($login_time){
                    $this->errorCode = 4;
                } else {
                    $this->cleanDriverTimes($employee->user);//如果司机登录正确 清空前面记录的错误登录次数（如果有的话）
                    $this->errorCode = self::ERROR_NONE;
                }
            }
        }
        else{
            if ($password!=md5($this->password)){
                $this->errorCode = self::ERROR_PASSWORD_INVALID;
            }
            else{
                $this->errorCode = self::ERROR_NONE;
            }
        }

		//统一到afterAction里纪录了。这里不纪录了
		//AdminLogs::addLogs($this->username,$this->errorCode);
		return !$this->errorCode;
	}


    /**
     * 限制司机错误登录次数，如一天内超过5次则返回true 否则记录登录次数，返回false
     * @param $driver_id
     * @return bool
     */
    private function limitDriver($driver_id){
        $key = $this->userlimit_key.$driver_id.date('Ymd');
        $cache = Yii::app()->cache->get($key);
        if($cache){
            if($cache > 5){
                $cache ++;
                Yii::app()->cache->set($key,$cache,86400);
                if($cache == 21){
                    Mail::sendMail(array('ops@edaijia-inc.cn','dongkun@edaijia-inc.cn','dengxiaoming@edaijia-inc.cn'),'司机今天登录次数超过50次 driver_id:'.$driver_id,'司机错误登录报警');
                    //Mail::sendMail(array('dongkun@edaijia-inc.cn'),'司机今天登录次数超过50次 driver_id:'.$driver_id,'司机错误登录报警');
                }
                return true;
            }
            else {
                $cache ++;
                Yii::app()->cache->set($key,$cache,86400);
            }
        }
        else {
            Yii::app()->cache->set($key,1, 86400);
        }
        return false;
    }

    /**
     * 限制司机错误登录次数，如一天内超过5次则返回true 否则记录登录次数，返回false
     * @param $driver_id
     * @return bool
     */
    private function getLimitDriver($driver_id){
        $key = $this->userlimit_key.$driver_id.date('Ymd');
        $cache = Yii::app()->cache->get($key);
        if($cache && $cache > 5){
            return true;
        }
        return false;
    }

    /**
     * 司机登录正确：清空登录次数
     * @param $driver_id
     * @return bool
     */
    private function cleanDriverTimes($driver_id){
        $key = $this->userlimit_key.$driver_id.date('Ymd');
        $cache = Yii::app()->cache->get($key);
        if($cache){
            Yii::app()->cache->set($key, 0, 86400);
        }
        return true;
    }
}
