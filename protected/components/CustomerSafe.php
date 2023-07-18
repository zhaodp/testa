<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CustomerSafe
 *
 * @author syang
 */
define('VERSION', '4.0');
define('NOISE_LEVEL1', 'p9TzBav8pT5egPO04SOb7SMcWf2MtF');
define('NOISE_LEVEL2', 'EqYKiaWy6qBRvX0EUoUXC6O3xEeRhz');
class CustomerSafe {

    
    public static function generateSign($queryString, $secretToken) {
		$raw = $queryString.$secretToken.NOISE_LEVEL2;
		return str_rot13(self::encode($raw));
    }
    
    public static function generateAuthSign($udid, $appKey, $appSecret, $timestamp) {
		$raw = $udid.$appKey.$appSecret.$timestamp.NOISE_LEVEL2;
		return self::encode($raw);
    }
    
	//生成访问Token
    public static function generateAccessToken($udid, $appKey, $appSecret) {
		$rand = 'S'.rand(0, 999999999999).time();
		return md5(sha1(sha1(sha1(sha1($appKey).$appSecret).$udid).$rand));
    }
	
	//生成加密Token
	public static function generateSecretToken($udid, $appKey, $appSecret) {
		$rand = 'S'.time().rand(0, 999999999999);
		return sha1(md5(md5(md5(md5($udid).$appKey).$appSecret).$rand));
	}
    
    private static function encode($str) {
		$str = strval($str);
		$m1 = md5($str);
		$m2 = sha1($str);
		return sha1($m1.$str.$m2.NOISE_LEVEL1);
    }
    
}

//echo CustomerSafe::generateSign("a", "b")."\n\n";
//echo CustomerSafe::generateAuthSign("1", "2", "3","4")."\n\n";
?>
