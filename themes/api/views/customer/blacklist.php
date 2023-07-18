<?php
/**
 * 获取黑名单列表
 * @param array $params
 * @return array 
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-05-02
 */
$token = isset($params['token']) ? $params['token'] : '';
//验证token
$validate = DriverToken::model()->validateToken($token);
if ($validate) {
	
	//需要调优，走缓存，调优SQL，读从库。add by sunhongjing
	$blackcustomers = Customer::model()->getBlackCustomerInfo();
	//兼容2.4.0之后版本，没有黑名单，返回FALSE,设置成空字符串 aiguoxin 2014.09.10
	$driver = DriverStatus::model()->getByToken($token);
	if ($driver===null||$driver->token===null||$driver->token!==$token) {
	    $ret=array('code'=>1 , 'message'=>'token失效');
	    echo json_encode($ret);return;
	}
	$app_ver = DriverStatus::model()->app_ver($driver->driver_id);
	if($app_ver && $app_ver>='2.4.0'){
        if(empty($blackcustomers)){
        	$blackcustomers = '';
        }
    }

	$ret = array(
	    'code' => 0,
	    'blackcustomers' => $blackcustomers,
	);
} else {
	$ret = array (
        'code'=>1,
        'message'=>'token失效'
    );
}
echo json_encode($ret);
