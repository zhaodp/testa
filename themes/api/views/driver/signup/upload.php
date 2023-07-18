<?php
/**
 * 司机在线报名h5 --上传证件图片
 * @author luzhe
 * @version 2014-08-07
 */
$city_id     = isset($params['city_id']) ? trim($params['city_id']) : 0;
$id          = isset($params['id']) ? trim($params['id']) : 0;
$pic1_base64 = isset($params['pic1_base64']) ? trim($params['pic1_base64']) : '';//驾驶证主页
$pic2_base64 = isset($params['pic2_base64']) ? trim($params['pic2_base64']) : '';//驾驶证副页
$pic3_base64 = isset($params['pic3_base64']) ? trim($params['pic3_base64']) : '';//身份证正面
$pic4_base64 = isset($params['pic4_base64']) ? trim($params['pic4_base64']) : '';//身份证反面
$callback    = isset($params['callback'])?$params['callback']:'';

if(empty($city_id) || empty($id)){
    $ret = array('code' => 1 , 'message' => '参数有误');
    $json_str=json_encode($ret);
    if(isset($callback)&&!empty($callback)){
      $json_str=$callback.'('.$json_str.')';
    }
    echo $json_str;Yii::app()->end();
}
if(!empty($pic1_base64)){
    $to_upload = $pic1_base64;
    $img_name = 'pic1';
}else if(!empty($pic2_base64)){
    $to_upload = $pic2_base64;
    $img_name = 'pic2';
}else if(!empty($pic3_base64)){
    $to_upload = $pic3_base64;
    $img_name = 'pic3';
}else if(!empty($pic4_base64)){
    $to_upload = $pic4_base64;
    $img_name = 'pic4';
}

if(!isset($to_upload) || empty($to_upload)){
    $ret = array('code' => 1 , 'message' => '图片不能为空');
    $json_str=json_encode($ret);
    if(isset($callback)&&!empty($callback)){
      $json_str=$callback.'('.$json_str.')';
    }
    echo $json_str;Yii::app()->end();
}
//上传图片 将路径保存到数据库 返回信息
//$img_data = base64_decode(str_replace('data:image/png;base64,', '', $to_upload));
$to_upload = str_replace(' ', '+', $to_upload);//访问的时候字符串中的+会变成空格 故转换回来
$img_data = base64_decode(str_replace('data:image/png;base64,', '', $to_upload));

$file_dir = 'temp/'.$city_id . '/' . $id;
if (!file_exists(IMAGE_ASSETS . 'temp/')){
        mkdir(IMAGE_ASSETS . 'temp/');
}
if (!file_exists(IMAGE_ASSETS .'temp/'. $city_id . '/')){
	mkdir(IMAGE_ASSETS . 'temp/'.$city_id);
}
if (!file_exists(IMAGE_ASSETS . $file_dir.'/')){
	mkdir(IMAGE_ASSETS . $file_dir.'/');
}
$file_dir = IMAGE_ASSETS . $file_dir.'/'; 
$local_pic_address = $file_dir.'/'. $img_name . '.jpg';
$result = file_put_contents($local_pic_address, $img_data);
if(!$result){
    $ret = array('code' => 1 , 'message' => '图片上传失败');
    $json_str=json_encode($ret);
    if(isset($callback)&&!empty($callback)){
      $json_str=$callback.'('.$json_str.')';
    }      
    echo $json_str;Yii::app()->end();
}else{
     $upload_model = new UpyunUpload('edriver');

$yun_img_name = $id.'_'.$img_name;
$is_upload = $upload_model->driverPicUpload($city_id, $yun_img_name, $local_pic_address);
     
     //http://pic.edaijia.cn/1/32717_pic1.jpg_middle
     $picture = $is_upload ? Driver::getUploadPictureUrl($yun_img_name, $city_id) : '';
     $recruitmentModel = DriverRecruitment::model()->findByPk($id);
     if($recruitmentModel){
	if($img_name == 'pic1'){
		$params = array('pic1'=> $picture,
                         );
	}else if($img_name == 'pic2'){
		 $params = array('pic2'=> $picture,
                         );
	}else if($img_name == 'pic3'){
		 $params = array('pic3'=> $picture,
                         );
        }else{
		 $params = array('pic4'=> $picture,
                         );
        }
	$recruitmentModel->saveAttributes($params);
	$ret = array('code' => 0 , 'message' => '上传成功', 'pic' => $picture);
    	$json_str=json_encode($ret);
    	if(isset($callback)&&!empty($callback)){
      		$json_str=$callback.'('.$json_str.')';
    	}       
    	echo $json_str;Yii::app()->end();
     }
}
