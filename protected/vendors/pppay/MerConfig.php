<?php

/**
 * 配置信息
 * 
 */
class MerConfig {
	/**
	 * 商户编码		
	 */
	public static $MER_ID = '333';//"1000002153";
	/**
	 * 二级商户号
	 */
	const SUB_MER_ID = "";
	/**
	 * 版本	
	 * 
	 */
	const VERSION = "v1.0";
	/**
	 * 编码
	 */
	const EN_CODE = "UTF-8";
	/**
	 * 加密类型：1:DESede 2:AES
	 */
	const ENC_TYPE = 1;
	
	/**
	 * 签名类型：1:MD5 2:SHA1
	 */
	const SIGN_TYPE = 1;
	/**
	 * 是否压缩	 
	 * 
	 */
	const ZIP_TYPE = 0;
	/**
	 * 
	 */
	const KEY_INDEX = 1;
	/**
	 * 商户秘钥
	 */
	public static $KEY = '';//"qJ7nqKZbEf1rDtLvycjV1fJV0/VUyJmK";
	
	/**
	 *  API URL
	 */
	//const PAY_API_URL = "http://124.193.184.92/FS/api";
	/**
	 *  APP WAP URL
	 */
	//const PAY_APP_WAP_URL = "http://124.193.184.94/bfsmob/servlet/MerReqServlet";
	
	/**
	 *  APP WAP 信用支付 URL
	 */
	//const PAY_APP_WAP_CREDIT_URL = "http://124.193.184.92/FS/servlet/MerReqServlet";
	
}
?>
