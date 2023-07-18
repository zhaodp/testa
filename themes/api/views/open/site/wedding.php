<?php
/**
 * 客户端API：婚庆代驾申请
 * @return json
 * @author cuiluzhe 2014-06-16
 */
//接收并验证参数
$name = isset($params['name']) ? $params['name'] : '';
$phone = isset($params['phone']) ? $params['phone'] : '';
$wedding_type = isset($params['wedding_type']) ? $params['wedding_type'] : '0';
$run_time = isset($params['run_time']) ? $params['run_time'] : '';
$city_id = isset($params['city_id']) ? $params['city_id'] : '0';
$hotels = isset($params['hotels']) ? $params['hotels'] : '';
$detail_site = isset($params['detail_site']) ? $params['detail_site'] : '';
$number = isset($params['number']) ? $params['number'] : '0';
$mark = isset($params['mark']) ? $params['mark'] : '';
$key = isset($params['key']) ? $params['key'] : '';//redis缓存的key
$verifyCode = isset($params['verifyCode']) ? $params['verifyCode'] : '';//验证码
if( empty($name) || empty($phone) || empty($run_time) || empty($city_id) || empty($detail_site) || empty($number) || empty($mark) || empty($key) || empty($verifyCode)){
	$ret = array('code' => 2 , 'data' => '' , 'message' => '参数有误');
        echo json_encode($ret);return ;
}
$cache_code = RVerifyCode::model()->get($key);
if(!$cache_code || $verifyCode != $cache_code ){
    $ret = array('code' => 2 , 'data' => '' , 'message' => '请输入正确的验证码');
    echo json_encode($ret);return ;
}

$model = new WeddingApply();
$model->name=$name;
$model->phone=$phone;
$model->wedding_type=$wedding_type;
$model->run_time=$run_time;
$model->city_id=$city_id;
$model->hotels=$hotels;
$model->detail_site=$detail_site;
$model->number=$number;
$model->mark=$mark;
$model->create_time=date("Y-m-d H:i:s", time());
$i=$model->save(false);
if ($i){
	$ret = array('code' => 0 , 'data' => '' , 'message' => '恭喜您，申请成功！');
        echo json_encode($ret);return ;
}else{
	$ret = array('code' => 1 , 'data' => '' , 'message' => '申请失败,请重新申请');
        echo json_encode($ret);return ;
}
