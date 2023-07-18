<?php
$id_card = $this->getParam('id_card', FALSE, 'post');
$city_id = $this->getParam('city_id', FALSE, 'post');
$date = $this->getParam('date', FALSE, 'post');
$hour = $this->getParam('hour', FALSE, 'post');

//缺少参数
if(FALSE === ($id_card && $city_id && $date && $hour)){
    $this->renderError(__LINE__, '缺少必要的参数');
}
list($errno, $msg) = BookingExamDriver::model()->driverBook($city_id, $date, $id_card, $hour);
//预约成功，发送短信
if(0 == $errno){
    $city_config = CityConfig::model()->findByAttributes(
        array('city_id' => $city_id)
    );
    $address = $city_config->booking_sms;
    $city = $city_config->city_name;
    $date_str = date('n月j日', strtotime($date));
    list($start, $end) = BookingHoursSetting::model()->getHourStartEnd($city_id, $hour);
    $driver_recruitment = DriverRecruitment::model()->findByIDCard($id_card);
    $content = sprintf(BookingExamSetting::SMS, $city, $date_str . $start, $address);
    if(!empty($driver_recruitment->mobile)){
        Sms::SendSMS($driver_recruitment->mobile, $content);
    }
}
$this->outputJson(null, $msg, $errno);
