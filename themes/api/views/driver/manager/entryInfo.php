<?php
/**
 * Created by PhpStorm.
 * User: aiguoxin
 * Date: 15/3/25
 * Time: 下午1:10
 */
$token = empty($params['token']) ? '' : trim($params['token']);
$driverIdCard = empty($params['driverIdCard']) ? '' : trim($params['driverIdCard']);

/****颜色常量定义*****/
$black=1;
$green=2;
$red=3;

if (empty($token)) {
    $ret['message'] = JsonResponse::validMsgFail;
    $ret['code'] = JsonResponse::EXPIRE_CODE;
    echo JsonResponse::fail($ret);
    return;
}

//验证是否登录
$driverManagerId = DriverStatus::model()->getDriverManagerToken($token);
if (empty($driverManagerId)) {
    $ret['message'] = JsonResponse::validMsgFail;
    $ret['code'] = JsonResponse::EXPIRE_CODE;
    echo JsonResponse::fail($ret);
    return;
}
$manager = Driver::getProfile($driverManagerId);
if(empty($manager)){
    $ret['message'] = JsonResponse::validMsgFail;
    echo JsonResponse::fail($ret);
    return;
}
$driver = Driver::model()->getDriverByIdCard($driverIdCard);
$manager_city = $manager['city_id'];

//根据身份证号获取验证信息
$recruitmentInfo = DriverRecruitment::model()->getDriverByIDCard($driverIdCard);
if (empty($recruitmentInfo)) {
    $ret['message'] = '该司机未报名，请先在线报名';
    echo JsonResponse::fail($ret);
    return;
}
$city_id = $recruitmentInfo['city_id'];
//司管和司机不在一个城市，不能操作司机
if($city_id != $manager_city){
     $ret['message'] = '没有权限操作其他城市的司机';
    echo JsonResponse::fail($ret);
    return;
}

$serialNum = DriverRecruitment::model()->getSerialNum($city_id,$recruitmentInfo['id']);
$name = $recruitmentInfo['name'];

//司机签约信息，未签约时候，信息从报名表获取t_driver_recruitment,已入职从t_driver表获取
$driverNum = $recruitmentInfo['id_driver_card'];
$driverTime = date("Y-m-d H:i:s",$recruitmentInfo['driver_year']);
$driverType = $recruitmentInfo['driver_type'];
$driverPhone = '';//入职工作手机

//司机头像是否上传
$isImageUploaded=false;
//司机是否入职
$isSigned=DriverRecruitment::model()->isSigned($driverIdCard);
$driverHeadUrl='http://pic.edaijia.cn/0/default_driver.jpg_small?ver=1428462348';

if(empty($isSigned)){
    $driverType=-1;//未签约,默认请选择
}

$driver=Driver::model()->getDriverByIdCard($driverIdCard);
if($driver){
    $headUrl = $driver['picture'];
    if($headUrl){
        $isImageUploaded=true;
        $driverHeadUrl =  $headUrl;
    }
    if(isset($driver['id_driver_card'])){
        $driverNum = $driver['id_driver_card'];
    }
    $driverTime = date("Y-m-d H:i:s",strtotime($driver['license_date']));
    $driverPhone = $driver['phone'];
}
//路考是否通过
$isAutoRoadPassed=false;
$autoTime='';
$isManualRoadPassed=false;
$manualTime='';


//1.先判断是否路考通过，通过的话，全部展示
$road=$recruitmentInfo['road_new'];
//兼容旧数据
if(!in_array($road,DriverRecruitment::$road_dict_source)){
    $road = DriverRecruitment::STATUS_ROAD_INIT;
}
$roadNavDesc=DriverRecruitment::$road_dict[$road];
$roadNavColor=$black;

$road_pass=DriverRoadExam::model()->isRoadPass($road);
if($road_pass){//路考通过
    $roadExam = DriverRoadExam::model()->getPassInfo($recruitmentInfo['id']);
    $isAutoRoadPassed=true;
    $isManualRoadPassed=true;
    if($roadExam) {
        $autoTime = $roadExam['a_road_time'];
        $manualTime = $roadExam['m_road_time'];
    }
    $roadNavColor=$green;
}else{//路考未考或没有通过,只获取当天数据
    $today = date("Y-m-d");
    $roadExam = DriverRoadExam::model()->getRoadExamInfo($today,$recruitmentInfo['id']);
    if($roadExam){
        $isAutoRoadPassed=in_array($roadExam['automatic'],DriverRoadExam::$rank)?TRUE:FALSE;
        $autoTime = $roadExam['a_road_time'];
        $isManualRoadPassed=in_array($roadExam['manuax'],DriverRoadExam::$rank)?TRUE:FALSE;
        $manualTime = $roadExam['m_road_time'];
        if($road == DriverRecruitment::STATUS_ROAD_FIELD_FAILED){
            $roadNavColor = $red;
        }
    }
}

//各种状态显示
$exam = $recruitmentInfo['exam'];
if(!in_array($exam,DriverRecruitment::$exam_dict_source)){
    $exam = DriverRecruitment::STATUS_ONLINE_EXAM_INIT;
}
$examNavDesc=DriverRecruitment::$exam_dict[$exam];
$examNavClick=true;
$examNavColor=$black;


if($exam == DriverRecruitment::STATUS_ONLINE_EXAM_PASS){
    $examNavColor=$green;
}elseif($exam == DriverRecruitment::STATUS_ONLINE_EXAM_FAILED){
    $examNavColor=$red;
}


$licencePicAUrl = Driver::createPicPictureUrl('pic1' ,$recruitmentInfo['id'],$manager['id_card'], $recruitmentInfo['city_id'], Driver::PICTURE_MIDDLE,true);
$licencePicBUrl = Driver::createPicPictureUrl('pic2' ,$recruitmentInfo['id'],$manager['id_card'], $recruitmentInfo['city_id'], Driver::PICTURE_MIDDLE,true);
$idCardPicAUrl = Driver::createPicPictureUrl('pic3' ,$recruitmentInfo['id'],$manager['id_card'], $recruitmentInfo['city_id'], Driver::PICTURE_MIDDLE,true);
$idCardPicBUrl = Driver::createPicPictureUrl('pic4' ,$recruitmentInfo['id'],$manager['id_card'], $recruitmentInfo['city_id'], Driver::PICTURE_MIDDLE,true);
if(empty($driver)){
    $creditPicUrl = '';
}else{
    $creditPicUrl = Driver::createDriverCardPictureUrl($driver['car_card'], $driver['city_id'], Driver::PICTURE_MIDDLE);
}

//获取在线考核次数和路考次数
$examAllTimes=0;
$examTodayTimes=0;
$roadAllTimes=0;
$roadTodayTimes=0;
$examArray=DriverExamnewOnline::model()->getExamTimes($driverIdCard);
if($examArray){
    $examAllTimes=$examArray['total'];
    $examTodayTimes=$examArray['today'];
}
$roadArray=DriverRoadExam::model()->getRoadTimes($driverIdCard);
if($roadArray){
    $roadAllTimes=$roadArray['total'];
    $roadTodayTimes=$roadArray['today'];
}

$ret = array(
    'serialNum' => $serialNum,
    'name' => $name,
    'idCard' => $driverIdCard,
    'driverHeadUrl'=>$driverHeadUrl,
    'isSigned'=>$isSigned,
    'isImageUploaded'=>$isImageUploaded,
    'isAutoRoadPassed'=>$isAutoRoadPassed,
    'autoTime'=>$autoTime,
    'isManualRoadPassed'=>$isManualRoadPassed,
    'manualTime'=>$manualTime,
    'driverNum'=>$driverNum,
    'driverTime'=>$driverTime,
    'driverType'=>$driverType,
    'driverPhone'=>$driverPhone,
    'roadNavClick'=>true,
    'roadNavDesc'=>$roadNavDesc,
    'roadNavColor'=>$roadNavColor,
    'photoNavDesc'=>'',//照片暂时不用状态
    'photoNavClick'=>true,
    'photoNavColor'=>$black,
    'signNavDesc'=>$isSigned?'已签约':'未签约',
    'signNavClick'=>true,
    'signNavColor'=>$isSigned?$green:$red,
    'examNavDesc'=>$examNavDesc,
    'examNavClick'=>$examNavClick,
    'examNavColor'=>$examNavColor,
    'licencePicAUrl' => $licencePicAUrl,
    'licencePicBUrl' => $licencePicBUrl,
    'idCardPicAUrl' => $idCardPicAUrl,
    'idCardPicBUrl' => $idCardPicBUrl,
    'creditPicUrl' => $creditPicUrl,
    'examAllTimes'=>$examAllTimes,
    'examTodayTimes'=>$examTodayTimes,
    'roadAllTimes'=>$roadAllTimes,
    'roadTodayTimes'=>$roadTodayTimes,
);

echo JsonResponse::success($ret);
return;
