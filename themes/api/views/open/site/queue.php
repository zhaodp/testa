<?php
/**
 * 官网API：报名查询
 * @return json
 * @author cuiluzhe 2014-08-28
 */
$id_card = isset($params['id_card']) ? $params['id_card'] : '';

if( empty($id_card) ){
	$ret = array('code' => 2 ,'message' => '参数有误');
        echo json_encode($ret);return ;
}
$recruitment = new DriverRecruitment();
$driver_info = $recruitment->getDriverByIDCard($id_card);
if ($driver_info) {
    $status = Dict::items('recruitment_status');
    $driver_model = new Driver();
    $driver = $driver_model->getDriverByIdCard($id_card);
	
    $citys = Dict::items('city_prefix');
    $city_id = $driver_info['city_id'] == 0 ? 1 : $driver_info['city_id'];
    if (isset($citys[$city_id])) {
        $serial_number = sprintf("%s%05d", $citys[$city_id], $driver_info['id']);
    } else {
	$serial_number = $driver_info['id'];
    }
    $currentStatus = $driver_info['status'];//当前状态
    /***状态查询***/
    if(in_array($currentStatus,array(DriverRecruitment::STATUS_ENTRY_OK,DriverRecruitment::STATUS_SIGNED,5,6))){
        $currentStatus = DriverRecruitment::STATUS_ENTRY_OK;//已签约、已领装备、5激活、6财务确认，都属于已签约
    }else{
        $currentStatus = DriverRecruitment::STATUS_ENROLL;//默认已报名
    }
    $status_cn = DriverRecruitment::$status_dict[$currentStatus];

    //解约
    if(isset($driver['mark']) && Driver::MARK_LEAVE == $driver['mark']){
        $status_cn = '已解约';
    }

}else{
    $status_cn = '<p>您还没有报名，请先报名</p>';
    $ret = array('code' => 1, 'status_cn' => $status_cn, 'status' => 0);
    echo json_encode($ret);return ;
} 
$name = $driver_info['name'];
$mobile = $driver_info['mobile']; 
//$mobile = Common::parseCustomerPhone($mobile); 
$mobile = preg_replace("/(1\d{1,2})\d\d(\d{0,3})/","\$1****\$3",$mobile);
$status = isset($driver_info['status']) ? $driver_info['status'] : NULL;
$city = RCityList::model()->getCityByID($city_id);
$city_name = $city['city_name'];

$book_record = BookingExamDriver::model()->find('id_card = :id_card AND date >= :date',
    array(':id_card' => $id_card,
    ':date' => date('Ymd'),
)
        );

//有没有在当前日期后的预约
$next_order = TRUE;
if(empty($book_record)){
    $next_order = FALSE;
}


$ret = array(
    'code'          => 0,
    'serial_number' => $serial_number,
    'name'          => $name,
    'mobile'        => $mobile ,
    'status_cn'     => $status_cn,
    'status'        => $status,
    'city_id'       => $city_id,
    'city_name'     => $city_name,
    'next_order'    => $next_order,
);
echo json_encode($ret);return;?>
