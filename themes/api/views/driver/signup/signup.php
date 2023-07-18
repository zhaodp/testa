<?php
/**
 * 司机在线报名h5
 * @author luzhe
 * @version 2014-08-01
 */
$data['name'] = isset($params['name']) ? trim($params['name']) : '';
$data['mobile'] = isset($params['mobile']) ? trim($params['mobile']) : '';
$data['id_card'] = isset($params['id_card']) ? trim($params['id_card']) : '';
$data['driver_card'] = isset($params['id_card']) ? trim($params['id_card']) : '';
$data['id_driver_card'] = isset($params['id_driver_card']) ? trim($params['id_driver_card']) : '';
$data['driver_year_str'] = isset($params['driver_year']) ? $params['driver_year'] : '';
$data['gender'] = isset($params['gender']) ? trim($params['gender']) : '1';
$data['age'] = isset($params['age']) ? intval($params['age']) : 0;
$data['domicile'] = isset($params['domicile']) ? trim($params['domicile']) : '--';
$data['register_city'] = isset($params['register_city']) ? trim($params['register_city']) : '--';
$data['driver_type'] = isset($params['driver_type']) ? intval($params['driver_type']) : 0;
$data['driver_cars'] = isset($params['driver_cars']) ? trim($params['driver_cars']) : '';
$data['city_id'] = isset($params['city_id']) ? intval($params['city_id']) : 0;
$data['district_id'] = isset($params['district_id']) ? intval($params['district_id']) : 0;
$data['address'] = isset($params['address']) ? trim($params['address']) : '';
$data['src'] = isset($params['src']) ? intval($params['src']) : 0;

$data['recommender'] = isset($params['recommender']) ? trim($params['recommender']) : '';

$data['other_src'] = isset($params['other_src']) ? trim($params['other_src']) : '';
$data['contact'] = isset($params['contact']) ? trim($params['contact']) : '';
$data['contact_phone'] = isset($params['contact_phone']) ? trim($params['contact_phone']) : '';
$data['contact_relate'] = isset($params['contact_relate']) ? trim($params['contact_relate']) : '';
$data['experience'] = isset($params['experience']) ? trim($params['experience']) : '';
//官网司机报名 增加4个字段 分别为单位名称 单位联系人 联系人方式  合作相关信息单位名称
$data['company'] = isset($params['company']) ? trim($params['company']) : '';
$data['company_contact'] = isset($params['company_contact']) ? trim($params['company_contact']) : '';
$data['company_mobile'] = isset($params['company_mobile']) ? trim($params['company_mobile']) : '';
$data['join_company'] = isset($params['join_company']) ? trim($params['join_company']) : '';

$agree = isset($params['agree']) ? trim($params['agree']) : '';
$data['signup_src'] = isset($params['signup_src']) ? trim($params['signup_src']) : 0;
$callback = isset($params['callback']) ? $params['callback'] : '';

$key = isset($params['key']) ? $params['key'] : '';//redis缓存的key
$verifyCode = isset($params['verifyCode']) ? $params['verifyCode'] : '';//验证码

$act_type = isset($params['act_type']) ? $params['act_type'] : 0;//活动渠道




if (empty($data['name']) || empty($data['mobile']) || empty($data['id_card']) || empty($data['id_driver_card'])
    || empty($data['register_city']) || empty($data['driver_year_str'])
) {

    $ret = array('code' => 1, 'message' => '参数有误');
    $json_str = json_encode($ret);
    if (isset($callback) && !empty($callback)) {
        $json_str = $callback . '(' . $json_str . ')';
    }
    echo $json_str;
    Yii::app()->end();
}

$data['age'] = DriverRecruitment::model()->countAge($data['id_card']) ;
$data['gender'] = DriverRecruitment::model()->countSex($data['id_card']) ;
$data['domicile'] = DriverRecruitment::model()->countDomicile($data['id_card']) ;

if (!empty($key) && !empty($verifyCode)) {
    $cache_code = RVerifyCode::model()->get($key);
    if (!$cache_code || $verifyCode != $cache_code) {
        $ret = array('code' => 2, 'data' => '', 'message' => '请输入正确的验证码');
        echo json_encode($ret);
        return;
    }
}

if ($data['age'] < 18 || $data['age'] > 60) {
    $ret = array('code' => 1, 'message' => '年龄必须在18岁到60岁之间');
    $json_str = json_encode($ret);
    if (isset($callback) && !empty($callback)) {
        $json_str = $callback . '(' . $json_str . ')';
    }
    echo $json_str;
    Yii::app()->end();
}
$data['driver_year'] = strtotime(trim($data['driver_year_str']));
if ((time() - $data['driver_year']) < 157680000) {
    $ret = array('code' => 1, 'message' => '驾龄必须5年以上');
    $json_str = json_encode($ret);
    if (isset($callback) && !empty($callback)) {
        $json_str = $callback . '(' . $json_str . ')';
    }
    echo $json_str;
    Yii::app()->end();
}
/**
 * if ($data['src']==8){
 * if(empty($data['other_src'])){
 * $ret = array('code' => 1 , 'message' => '请输入来源渠道');
 * $json_str=json_encode($ret);
 * if(isset($callback)&&!empty($callback)){
 * $json_str=$callback.'('.$json_str.')';
 * }
 * echo $json_str;Yii::app()->end();
 * }
 * }
 * if ($data['src']==6){
 * if(empty($data['recommender'])){
 * $ret = array('code' => 1 , 'message' => '请输入朋友司机工号');
 * $json_str=json_encode($ret);
 * if(isset($callback)&&!empty($callback)){
 * $json_str=$callback.'('.$json_str.')';
 * }
 * echo $json_str;Yii::app()->end();
 * }
 * }
 **/

if (DriverRecruitment::model()->findByAttributes(array('id_card' => $data['id_card']))) {
    $ret = array('code' => 1, 'message' => '此身份证号已经报过名');
    $json_str = json_encode($ret);
    if (isset($callback) && !empty($callback)) {
        $json_str = $callback . '(' . $json_str . ')';
    }
    echo $json_str;
    Yii::app()->end();
}

if(-1 == $data['city_id'] || 0 == $data['city_id']){
    $ret = array('code' => 1, 'message' => '请选择居住地');
    $json_str = json_encode($ret);
    if (isset($callback) && !empty($callback)) {
        $json_str = $callback . '(' . $json_str . ')';
    }
    echo $json_str;
    Yii::app()->end();
}

$data['apply_time'] = time();
$data['name'] = strip_tags($data['name']);
$data['address'] = strip_tags($data['address']);

if (!empty($agree) && $agree == '1') {
    $model = new DriverRecruitment();
    $model->attributes = $data;
    $model->work_type = 1;
    $model->register_city = $data['register_city'];
    $model->signup_src = $data['signup_src'];
    $model->recommender = $data['recommender'];
    $model->act_type = $act_type;
    if ($model->save(false)) {
        //保存日志流水
        $insertArr = array();
        $insertArr['name'] = $data['name'];
        $insertArr['id_card'] = $data['id_card'];
        $insertArr['message'] = '参加报名';
        $insertArr['time'] = time();
        Yii::app()->db->createCommand()->insert('t_recruitment_log', $insertArr);
        //生成二维码 队列
        createQrcode($model->id,$data['id_card'],$data['mobile']);
        $ret = array(
            'code' => 0,
            'message' => '报名成功',
            'id' => $model->primaryKey,
            'city_id' => $model->city_id,
            'apmStatus' => DriverOrder::model()->checkOpenCity($model->city_id) ? 1 : 0,
        );
        $json_str = json_encode($ret);
        if (isset($callback) && !empty($callback)) {
            $json_str = $callback . '(' . $json_str . ')';
        }
        echo $json_str;
        Yii::app()->end();
    }
} else {
    $ret = array('code' => 1, 'message' => '请确认同意代驾协议');
    $json_str = json_encode($ret);
    if (isset($callback) && !empty($callback)) {
        $json_str = $callback . '(' . $json_str . ')';
    }
    echo $json_str;
    Yii::app()->end();
}

//异步生成司机二维码
function createQrcode($id, $id_card,$phone){
    $params = array(
        'driver_phone'=>$phone,
        'signup_id'=>$id,
        'id_card'=>$id_card
       );

    //
    $task = array(
        'method'=>'create_driver_qrcode',
        'params'=>$params,
    );
    Queue::model()->putin($task,'backtogether');
}
