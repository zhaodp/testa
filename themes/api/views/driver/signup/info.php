<?php
/**
 * 司机报名微信--获取报名司机信息
 * @author duke
 * @version 2015-03-30
 */
$open_id = isset($params['open_id']) ? trim($params['open_id']) : '';
$id_card = isset($params['id_card']) ? trim($params['id_card']) : '';
$ret = array('code' => 1 , 'data'=>'', 'message' => '访问错误');
$user_info = array();
if(empty($open_id) && empty($id_card)){
    $ret = array('code' => 1 , 'data'=>'', 'message' => '访问错误');
    $json_str = json_encode($ret);
    echo $json_str;Yii::app()->end();
}

if($open_id){
    $mod = DriverRecruitment::model();
    $user_info = $mod->getInfoByOpenid($open_id);
    if(!$user_info){
        $ret = array('code' => 35 , 'data'=>'', 'message' => '您尚未报名，请先报名考试');
        $json_str = json_encode($ret);
        echo $json_str;Yii::app()->end();
    }
    $user_info['city_name'] = Dict::item('city', $user_info['city_id']);
    $status_info = $mod->getDriverStateByIdCard($user_info['id_card']);
    $user_info['status'] = $status_info;


    $book_record = BookingExamDriver::model()->find('id_card = :id_card AND date >= :date',
        array(':id_card' => $user_info['id_card'],
            ':date' => date('Ymd'),
        )
    );

//有没有在当前日期后的预约
    $next_order = TRUE;
    if(empty($book_record)){
        $next_order = FALSE;
    }
    $user_info['next_order'] = $next_order;
}elseif($id_card){
    $user_infos = DriverRecruitment::model()->getDriverByIDCard($id_card);
    if($user_infos){
        $user_info = array(
            'name'=>$user_infos['name'],
            'id'=>$user_infos['id'],
            'city_id'=>$user_infos['city_id'],
            'city_name'=> Dict::item('city', $user_infos['city_id']),
            'qr_code'=>$user_infos['qr_code'],
            'phone'=>$user_infos['mobile'],
            'id_card'=>$user_infos['id_card']
        );
    }else {
        $ret = array('code' => 35 , 'data'=>'', 'message' => '您尚未报名，请先报名考试');
        $json_str = json_encode($ret);
        echo $json_str;Yii::app()->end();
    }
}

$ret['data'] = $user_info;
$ret['code'] = 0;
$ret['message'] = 'ok';


echo json_encode($ret);
Yii::app()->end();
