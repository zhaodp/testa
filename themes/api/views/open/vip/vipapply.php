<?php
/**
 * 客户端API：申请vip
 * @return json
 * @author cuiluzhe 2014-06-16
 */
//接收并验证参数
$name = isset($params['name']) ? $params['name'] : '';
$phone = isset($params['phone']) ? $params['phone'] : '';
$type = isset($params['type']) ? $params['type'] : '';
$company_name = isset($params['company_name']) ? $params['company_name'] : '';
$mail = isset($params['mail']) ? $params['mail'] : '';
$city_id = isset($params['city_id']) ? $params['city_id'] : '0';
$book_money = isset($params['book_money']) ? $params['book_money'] : '0';
$mark = isset($params['mark']) ? $params['mark'] : '';

$key = isset($params['key']) ? $params['key'] : '';//redis缓存的key
$verifyCode = isset($params['verifyCode']) ? $params['verifyCode'] : '';//验证码

if( empty($name) || empty($phone) || $city_id=='0' ){
	$ret = array('code' => 2 , 'data' => '' , 'message' => '参数有误');
        echo json_encode($ret);return ;
}
if( !empty($key) && !empty($verifyCode) ){
	$cache_code = RVerifyCode::model()->get($key);
	if(!$cache_code || $verifyCode != $cache_code ){
    		$ret = array('code' => 2 , 'data' => '' , 'message' => '请输入正确的验证码');
    		echo json_encode($ret);return ;
	}
}

$model = new VipApply();
$model->name=$name;
$model->phone=$phone;
$model->type=$type;
$model->company_name=$company_name;
$model->mail=$mail;
$model->city_id=$city_id;
$model->book_money=$book_money;
$model->mark=$mark;
$model->create_time=date("Y-m-d H:i:s", time());

$i=$model->save(false);
if ($i){
	$ret = array('code' => 0 , 'data' => '' , 'message' => '恭喜您，申请成功！');
        echo json_encode($ret);return ;
}else{
	$ret = array('code' => 1 , 'data' => '' , 'message' => '申请失败，请重新申请');
        echo json_encode($ret);return ;
}
