<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : '';
$idCard = isset($_GET['idCard']) ?  trim($_GET['idCard']) : '';
$times = isset($_GET['times']) ? (int)$_GET['times'] : '';


if(!$times){
    $this->renderError(__LINE__, '请求错误');
}

if(!$id && !$idCard){
    $this->renderError(__LINE__, '请求错误');
}

//如果传了身份证号，根据身份证找到报名id
if($idCard){
    $driver = DriverRecruitment::model()->getDriverByIDCard($idCard);
    if($driver){
        $id = $driver['id'];
    }
}

$id = (int)$id;
$times = (int)$times;

$res = DriverRecruitment::model()->updateSendTimes($id,$times);
if($res){
    $this->outputJson();
}else{
    $this->outputJson('','error','1');
}



