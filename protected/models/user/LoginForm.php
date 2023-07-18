<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends EFormModel {
	public $username;
	public $password;
	public $rememberMe;
	
	private $_identity;
    public $verifyCode;
	public $verifyCodeNew;

    private $login_sms_code_key = 'loginSmsCodeActive';
	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules() {
        $name = isset($_POST['LoginForm']['username']) && $_POST['LoginForm']['username']  ? $_POST['LoginForm']['username'] :'';
        $isSuperAdmin = false;
        if($name){
            $res = AdminUserNew::model()->checkIsSupperAdmin($name);
            if($res['code'] == 1) {
                $isSuperAdmin = true;
            }
        }



		$array = array (
			// username and password are required
			array (
				'username, password',
				'required'
			), 
			// rememberMe needs to be a boolean
			array (
				'rememberMe', 
				'boolean'
			), 
			// password needs to be authenticated
			array (
				'password', 
				'authenticate'
			),
            //array('verifyCode', 'captcha', 'allowEmpty' => !CCaptcha::checkRequirements(), 'on' => 'admin'),
            //array('verifyCode', 'captcha', 'allowEmpty' => true),
		);
        if($isSuperAdmin){
            $array[] = array('verifyCodeNew', 'checkTfaCode');
        }
        else{
            $array[] = array('verifyCode', 'captcha', 'allowEmpty' => !CCaptcha::checkRequirements(), 'on' => 'admin');
            $array[] = array('verifyCode', 'captcha', 'allowEmpty' => true);
        }
        return $array;
	}
	
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels() {
		return array (
			'rememberMe'=>'记住密码'
		);
	}
	
	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute, $params) {
		if (!$this->hasErrors()) {
			$this->_identity = new UserIdentity($this->username, $this->password);
			if (!$this->_identity->authenticate())
				$this->addError('password', '用户名或密码错误');
		}
	}
	
	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login() {
		if ($this->_identity===null) {
			$this->_identity = new UserIdentity($this->username, $this->password);
			$this->_identity->authenticate();
		}



		if ($this->_identity->errorCode===UserIdentity::ERROR_NONE) {
			$duration = $this->rememberMe ? 3600*24*30 : 0; // 30 days
			Yii::app()->user->login($this->_identity, $duration);
			return true;
		} else
			return false;
	}

    public function checkTfaCode($attribute,$params){
        if (!$this->hasErrors()) {
            $user_info = AdminUserNew::model()->find('name = :name',array(':name'=>$this->username));
            if(!$user_info || !$user_info->secure_key) {
                $this->addError('verifyCode', '请绑定验证器');
            }
            $tfa = new TFA();
            $secure_key = $tfa->decrypt($user_info->secure_key);
            $res = GoogleAuthenticator::checkCode($secure_key, $this->verifyCodeNew);
            if (!$res){
                EdjLog::error('双因子认证返回错误 id:'.$user_info->id.' name:'.$user_info->name.' secure_key: '. $user_info->secure_key. ' decrypted key: '. $secure_key);
                $this->addError('verifyCode', '验证码错误');
            }
        }
    }


    public function getSmsCode($username,$password){
        $userInfo = AdminUserNew::model()->find('name=:name and status=:status', array (
            ':name'=>$username,':status'=>AdminUserNew::STATUS_NORMAL));
        if($userInfo){
            if(md5($password) == $userInfo->password){
                $phone  = $userInfo->phone;
                $count_key = 'LoginCountKey'.date('Ymd').$phone;
                $redis_mod  = RedisHAProxy::model();
                $count = $redis_mod->get($count_key);

                if($count > 10){
                    return $this->returnMsg(3,'每个用户每天限制发送10次');
                }else {
                    $sms_code = rand(10000,99999);
                    $set_res = $redis_mod->set($this->login_sms_code_key.$phone,$sms_code,310);
                    if($set_res){


                        $msg = '验证码为：'.$sms_code.' 您正在登录E代驾后台系统，任何人向您索取均为欺诈,请保证您的账号安全。';
                        $params = array(
                            'content'=>$msg,
                            'phone'=>$phone
                        );
                        //添加task队列向数据中添加
                        $task = array(
                            'method'=>'v2loginsms',
                            'params'=>$params
                        );

                        $res = Queue::model()->putin($task,'v2loginsms');
//
//                            $res = Sms::SendSMS($phone, $msg);
//                            $res = 1;
                        if($res){
                            //记录每天第一次次数
                            if(!$count) {
                                $counts = 1;
                                $redis_mod->set($count_key,$counts,86400);
                            }
                            $count += 1;
                            $redis_mod->set($count_key,$count,86400);
                            return $this->returnMsg(1,'发送完毕',$phone); //上线前把smscode delete
                        }
                        else{
                            return $this->returnMsg(7,'发送失败，请及时联系技术部 错误码:7');
                        }
                    }
                    else{
                        return $this->returnMsg(6,'发送失败，请及时联系技术部 错误码:6');
                    }


                }

            }
            else{
                return $this->returnMsg(3,'用户不存在或密码错误');
            }

        }else{
            return $this->returnMsg(2,'用户不存在或密码错误');
        }

    }

    public function checkSmsCode($phone,$smscode){
        $redis_mod  = RedisHAProxy::model();
        $syscode = $redis_mod->get($this->login_sms_code_key.$phone);
        if($syscode){
            if($syscode == $smscode){
                $redis_mod->del($this->login_sms_code_key.$phone);
                return $this->returnMsg(1,'验证一致');
            }
            else{
                return $this->returnMsg(2,'您的验证码不正确');
            }
        }else{
            return $this->returnMsg(3,'验证码失效请重新获取');
        }
    }
}
