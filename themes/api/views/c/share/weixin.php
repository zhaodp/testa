<?php
/**
 * 客户端API：c.share.weixin 微信分享二维码获取,如果登陆，则返回带该用户信息的二维码，没登陆就返回公共的二维码。一期先都返回公共二维码
 * 调用url:
 * 
 * @author sunhongjing 2013-10-10
 * 
 * @param token
 * @return json,二维码图片地址，异常返回错误代码
 * 
 * @example 
 */
$ret = array(
    'code' => 0,
    'data' => array('url' => 'http://d.edaijia.cn/share/weixin2.png','content' => '微信扫一扫，代驾不难找'), //二维码url
    'timestamp' => date("Y-m-d H:i:s"),
);
echo json_encode($ret);
return;
//二维码暂时不需要  －－mengtianxue  2013-10-28

//验证参数格式
$token = isset($params['token']) ? $params['token'] : '';

if(empty($token)){
	$ret = array(
	    'code'=>0,
		'data'=>array('url'=>'http://d.edaijia.cn/share/weixin2.png','content' => '微信扫一扫，代驾不难找'),//二维码url
		'timestamp'=>date("Y-m-d H:i:s"),
	);
	echo json_encode($ret);
	return;
}

//验证token
$validate = CustomerToken::model()->validateToken($token);
if ($validate){
	$ret = array(
	    'code'=>0,
		'data'=>array('url'=>'http://d.edaijia.cn/share/weixin2.png','content' => '微信扫一扫，代驾不难找'),//二维码url
		'timestamp'=>date("Y-m-d H:i:s"),
	);

} else {
	$ret = array(
	    'code'=>0,
		'data'=>array('url'=>'http://d.edaijia.cn/share/weixin2.png','content' => '微信扫一扫，代驾不难找'),//二维码url
		'timestamp'=>date("Y-m-d H:i:s"),
	);
}
echo json_encode($ret);return;




