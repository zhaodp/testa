<?php
/**
 * 读取未考试数量
 * User: duke
 * Date: 15-04-10
 * Time: 上午11:59
 */
$token=isset($params['token'])&&!empty($params['token']) ? trim($params['token']) : "";
//add by aiguoxin type=0未读公告,type=1投诉
$type=isset($params['type'])&&!empty($params['type']) ? trim($params['type']) : "";

if ( empty($token) || !$type) {
    $ret=array(
        'code'=>2,
        'message'=>'参数不正确'
    );
    echo json_encode($ret);
    return;
}
$type_arr = json_decode($type);
$driver = DriverStatus::model()->getByToken($token);
if ( empty($driver) ||  $driver->token===null || $driver->token!==$token ) {
    $ret=array(
        'code'=>1,
        'message'=>'请重新登录'
    );
    echo json_encode($ret);
    return;
}
$city_id = $driver->city_id;
//$open_city_id = array(22); //厦门
$open_city_id = array(53645); //先不开
if(in_array($city_id,$open_city_id)){
    $mod = new DriverExamStudy();

    $need_exam_count = $mod->checkDriverNeedExam($driver->driver_id, DriverExamStudy::TYPE_DRIVER_PRISE_EXAM);
    $category = QuestionNew::model()->getCategory();
    $i = 0;
    foreach($category as $cate_id => $name){
        if(!in_array($cate_id,$need_exam_count)){
            $i++;
        }
    }
}
else{
    $i = -1;
}

foreach($type_arr as $type_id){
    if($type_id  == 1){
        $data[] = array('type'=>$type_id,'count'=>$i);
    }else{
        $data[] = array('type'=> $type_id,'count'=>0);
    }
}




$ret=array(
    'code'=>0,
    'message'=>'成功',
    'list'=>$data,
);
echo json_encode($ret);
return;