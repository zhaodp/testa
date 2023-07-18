<?php
class EcenterSignService {
	/**
	* 构造签名
	* @param array $params 待签名数据
	* @param string $secretKey 私钥
	* @param string $algo 签名算法
	* @return string 构造的签名
	*/
	public static function createSign(array $params, $secretKey, $algo = "md5"){
		ksort($params);
		reset($params);

		$signPars = self::initUrl($params);
		$sign = strtolower(hash_hmac($algo, $signPars, $secretKey));

		return $sign;
	}

	/**
	* 签名校验
	* @param array $params 待签名数据
	* @param string $secretKey 私钥
	* @param string $algo 签名算法
	* @return false:校验失败，true:校验通过
	*/
	public static function verifySign(array $params, $secretKey, $algo = "md5"){
		if(empty($params['sign'])) {
			return false;
		}

		$sign = $params['sign'];
		unset($params['sign']);

		ksort($params);
		reset($params);

		$signPars = self::initUrl($params);
		$signCheck = strtolower(hash_hmac($algo, $signPars, $secretKey));

		return $sign === $signCheck;
	}

	public static function initUrl($params){
		$url = '';
		while(list($k, $v) = each($params)){
		    if('' === $v) continue;
		    $url .= $k . '=' . $v . '&';
		}
		// 去掉最后一个 "&"
		$url = substr($url, 0, strlen($url)-1);

		return $url;
	}

}


