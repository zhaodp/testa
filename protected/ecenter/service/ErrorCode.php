<?php
class ErrorCode {
	const SUCCESS = 1;
	const ERROR = 0;
	const ERROR_PARAMS = -1;
	const ERROR_SIGN = -2;
	const ERROR_NOT_LOGIN = -3;
	const ERROR_AUTH = -4;

	const APP_NOT_EXIST = -5;
    const ERROR_PWD = -6;
    const ERROR_NO_SECURE_KEY = -7;

	public static $DESC = array(
		self::SUCCESS => '成功',
		self::ERROR => '未知错误',
		self::ERROR_PARAMS => '参数错误',
		self::ERROR_SIGN => '签名错误',
		self::ERROR_NOT_LOGIN => '未登录',
		self::ERROR_AUTH => '没有权限，请联系管理员',

		self::APP_NOT_EXIST => '不存在该应用系统',
        self::ERROR_PWD => '用户名或密码错误',
        self::ERROR_NO_SECURE_KEY => '没有绑定双因子',
	);

	public static function getDesc($code){
		return self::$DESC[$code];
	}
}
