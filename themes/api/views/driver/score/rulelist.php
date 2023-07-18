<?php
/**
 * 司机端代驾分规则 标准规则，每个城市相同  
 * @author aiguoxin
 * @version 2014-05-28
 * 
 */
//接收并验证参数
$token = isset($params['token']) ? trim($params['token']) : '';
$params['page_num'] = ( empty($params['page_num']) || $params['page_num']<=0 ) ? 1 : $params['page_num'];
$params['page_size'] = ( empty($params['page_size']) || $params['page_size']<=0 ) ? 20 : $params['page_size'];


if(empty($token)){
    $ret=array('code'=>2 , 'message'=>'参数不正确!');
    echo json_encode($ret);return;
}

//验证token
$driver = DriverStatus::model()->getByToken($token);
if ($driver===null||$driver->token===null||$driver->token!==$token) {
    $ret=array('code'=>1 , 'message'=>'token失效');
    echo json_encode($ret);return;
}


$total=CustomerComplainType::model()->getRuleCount();
$list=CustomerComplainType::model()->getRuleList($params['page_num'],$params['page_size']);


//返回成功信息
$ret=array('code'=>0 , 
	'message'=>'ok',
	'total'=>$total,
	'list'=>$list);
echo json_encode($ret);return;
