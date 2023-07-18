<?php
/**
 * 客户端 绑定优惠券
 * @author aiguoxin
 * @return json,成功信息，异常返回错误代码
 *
 */


$bonus_sn = isset($params['bonus_sn']) ? trim($params['bonus_sn']) : '';
$phone = isset($params['phone']) ? trim($params['phone']) : '';
$pwd = isset($params['pwd']) ? trim($params['pwd']) : 0;
$callback=isset($_GET["callback"])?$_GET["callback"]:"";


if (empty($bonus_sn) && empty($phone)) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误'
    );
    $json_str=json_encode($ret);
    if(isset($callback)&&!empty($callback)){
        $json_str=$callback.'('.$json_str.')';
    }
    echo $json_str;Yii::app()->end();
}

$is_phone = Common::checkPhone($phone);
if( ! $is_phone ){
    $ret = array('code' => 2, 'message' => '手机号有误，请更正');
    echo json_encode($ret);
    return;
}

// $validate = CustomerToken::model()->validateToken($token);
// if (!$validate) {
//     $ret = array(
//         'code' => 1,
//         'message' => '验证失败',
//     );
//     echo json_encode($ret);
//     return;
// }

$ret = BonusLibrary::model()->BonusBinding($bonus_sn, $phone, $pwd);
$json_str=json_encode($ret);
if(isset($callback)&&!empty($callback)){
    $json_str=$callback.'('.$json_str.')';
}
echo $json_str;Yii::app()->end();