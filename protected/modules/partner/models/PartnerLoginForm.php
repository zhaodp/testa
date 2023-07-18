<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class PartnerLoginForm extends CFormModel
{
	public $username;
	public $password;
	public $rememberMe;

	private $_identity;
    public $verifyCode;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('username, password, verifyCode', 'required'),
			// rememberMe needs to be a boolean
			array('rememberMe', 'boolean'),
			// password needs to be authenticated
			array('password', 'authenticate'),
            array('verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements(), 'message' => Yii::t ( 'PartnerLoginForm', '{attribute}输入错误' ))
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
  		return array(
            'username' => '用户名',
            'password' => '密码',
			'rememberMe'=>'记住我',
            'verifyCode' => '验证码',
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$this->_identity=new PartnerIdentity($this->username,$this->password);
			if(!$this->_identity->authenticate())
				$this->addError('password','用户名或密码错误');
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if($this->_identity===null)
		{
			$this->_identity = new PartnerIdentity($this->username,$this->password);

            $this->_identity->authenticate();
		}

		if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
		{
			$duration=$this->rememberMe ? 3600*24*30 : 0; // 30 days

            Yii::app()->partner->login($this->_identity,$duration);
			return true;
		}
		else
			return false;
	}
}
