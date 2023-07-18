<?php
/**
 * Created by PhpStorm.
 * User: aiguoxin
 * Date: 15/3/25
 * Time: 下午7:41
 */
$token = empty($params['token']) ? '' : trim($params['token']);
$idCard = empty($params['idCard']) ? '' : trim($params['idCard']);
$type = empty($params['type']) ? 0 : trim($params['type']);

if(empty($token)){
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

if(empty($idCard)){
    $ret['message'] = '司机身份证号不能为空';
    echo JsonResponse::fail($ret);
    return;
}

$tmp_path  = "/tmp/";//接收文件目录
$target_path = $tmp_path.($_FILES['file']['name']);
$target_path = iconv("UTF-8","gb2312", $target_path);

//只有报名后的才能拍照
$driverRecruitment = DriverRecruitment::model()->getDriverByIDCard($idCard);
if(empty($driverRecruitment)){
    $ret['message'] = '请先报名，再拍照';
    echo JsonResponse::fail($ret);
    return;
}

//只有签约后的司机才能拍照
$driver = Driver::model()->getDriverByIdCard($idCard);
if(empty($driver)){
    $ret['message'] = '请先签约，再拍照';
    echo JsonResponse::fail($ret);
    return;
}

$driver_id = $driver['user'];
$city = $driver['city_id'];

//根据type，进行不同命名
$file_name = $_FILES['file']['tmp_name'];
//http://wiki.edaijia.cn/dwiki/doku.php?id=driver.manager.imageupload
$base_path = $driverRecruitment['city_id'];
switch($type){
    case 1:
        $file_name=$driver_id.'.jpg';break; //司机头像
    case 2: //驾驶证正面
        $file_name = $driverRecruitment['id'] . '_pic1.jpg';
        $pic = 'pic1';
        break;
    case 3: //驾驶证反面
        $file_name = $driverRecruitment['id'] . '_pic2.jpg';
        $pic = 'pic2';
        break;
    case 4: //身份证正面
        $file_name = $driverRecruitment['id'] . '_pic3.jpg';
        $pic = 'pic3';
        break;
    case 5: //身份证反面
        $file_name = $driverRecruitment['id'] . '_pic4.jpg';
        $pic = 'pic4';
        break;
    case 6: //暂住证或居住证 担保资料
        $base_path = 'driver_card/' . $driverRecruitment['city_id'];
        $file_name = Driver::createDriverCardPicName($driver['car_card']);
        break;
    default:
        $file_name=$driver_id.'.jpg';break; //司机头像
}

$target_path_encode = $tmp_path.$file_name;
$target_path_encode = iconv("UTF-8","gb2312", $target_path_encode);

if(move_uploaded_file($_FILES['file']['tmp_name'], $target_path_encode)) {
    $target_path = $target_path_encode;
    if(1 == $type){
        //上传到又拍云,不同类型的照片，传到不同目录下，需要和之前保持一致
        $upload_model = new UpyunUpload('edriver');
        $is_upload = $upload_model->driverPicUpload($city, $driver_id, $target_path_encode);
        $new_pic_url = $is_upload ? Driver::getUploadPictureUrl($driver_id, $city) : '';
        //更新数据库 todo 放入队列处理
        $driver_model = Driver::getProfile($driver_id);
        $driver_model->picture = $new_pic_url;
        $driver_model->save();
    //}else if(6 == $type){
    }else{
        $upload_model = new UpyunUpload('edriver');
        $is_upload = $upload_model->uploadFile($target_path, $base_path, $file_name);
        if($is_upload){
            $info = config_upyun::get_config_params('edriver');
            $yupai_base_url = $info['up_base_url'];
            if(6 == $type){
                //$new_pic_url = Driver::createDriverCardPictureUrl($driver['car_card'], $driver['city_id'], Driver::PICTURE_MIDDLE);
                $new_pic_url = sprintf("%sdriver_card/%s/%s", $yupai_base_url, $driver['city_id'], $file_name);
            }else{
                //$new_pic_url = Driver::createPicPictureUrl($pic ,$driverRecruitment['id'],$driver['id_card'], $driver['city_id'], Driver::PICTURE_MIDDLE,true);
                $new_pic_url = sprintf("%s%s/%s", $yupai_base_url, $driver['city_id'], $file_name);
            }
        }
    }

    if($new_pic_url){
        $upload_model->updateDriverPicCache($file_name, $base_path);
        $ret = array(
            'code' => 0,
            'picUrl'=>$new_pic_url,
            'message' => "The file ".( $_FILES['file']['name'])." has been uploaded,path=".$target_path,
        );
        echo json_encode($ret);
    }else{
        $ret = array(
            'code' => 2,
            'message' => '上传到又拍云失败',
        );
        echo json_encode($ret);
    }
}else{
    $ret = array(
        'code' => 2,
        'message' => "There was an error uploading the file, please try again! Error Code: ".$_FILES['file']['error'],
    );
    echo json_encode($ret);
}
