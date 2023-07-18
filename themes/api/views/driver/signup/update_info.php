<?php
$open_id = $this->getParam('open_id', NULL, 'all', TRUE);

$wechat_client = new Wechat();
$wechat_result = $wechat_client->getInfo($open_id);
if(0 != $wechat_result['code']){
    $this->renderError(__LINE__, 'wrong open id');
}
$id_card = $wechat_result['data']['idCardNum'];
$driver = DriverRecruitment::model()->findByIDCard($id_card);
if(empty($driver)){
    $this->renderError(__LINE__, '司机没有报名');
}

if(0 == strcasecmp($_SERVER['REQUEST_METHOD'], 'get')){
    $data = $driver->attributes;
    $need_attribute = array(
        'contact'        => 0,
        'contact_phone'  => 0,
        'contact_relate' => 0,
        'height'         => 0,
        'size'           => 0,
        'mail_phone'     => 0,
        'mail_name'      => 0,
        'mail_addr'      => 0,
        'mail_province'  => 0,
        'mail_district'  => 0,
        'mail_city'      => 0,
    );
    $this->outputJson(array_intersect_key($data, $need_attribute));
}
$contact = $this->getParam('contact', NULL,  'post', TRUE);
$contact_phone = $this->getParam('contact_phone', NULL, 'post', TRUE);
$contact_relate = $this->getParam('contact_relate', NULL, 'post', TRUE);
$height = $this->getParam('height', NULL, 'post', TRUE);
$size = $this->getParam('size', NULL, 'post', TRUE);
$mail_phone = $this->getParam('mail_phone', NULL, 'post', TRUE);
$mail_name = $this->getParam('mail_name', NULL, 'post', TRUE);
$mail_addr = $this->getParam('mail_addr', NULL, 'post', TRUE);
$mail_province = $this->getParam('mail_province', NULL, 'post', TRUE);
$mail_city = $this->getParam('mail_city', NULL, 'post', TRUE);
$mail_district = $this->getParam('mail_district', NULL, 'post', TRUE);

$driver['contact'] = $contact ;
$driver['contact_phone'] = $contact_phone ;
$driver['contact_relate'] = $contact_relate ;
$driver['height'] = $height ;
$driver['size'] = $size ;
$driver['mail_phone'] = $mail_phone ;
$driver['mail_name'] = $mail_name ;
$driver['mail_addr'] = $mail_addr ;
$driver['mail_province'] = $mail_province;
$driver['mail_city'] = $mail_city;
$driver['mail_district'] = $mail_district;
$res = $driver->save(false) ;
//需要补充完订单地址数据,已发货和签收的订单、离职后的状态，不用更改地址
//DriverOrder::model()->updateAddr($driver_id,$mail_province,$mail_city,$mail_district,$mail_name,$mail_phone);
$this->outputJson();

