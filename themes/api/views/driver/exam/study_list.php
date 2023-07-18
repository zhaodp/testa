<?php
/**
 * 司机端 考核培训 类型列表
 * @author duke
 * @version 2015-03-31
 */
$driver_id = isset($params['driver_id']) ? trim($params['driver_id']) : '';
$type = isset($params['types']) ? (int)$params['types'] : '';

$ret = array('code' => 1 , 'message' => 'null','data'=>'');

if(empty($driver_id) || !$type){
    $ret = array('code' => 1 , 'data'=>'', 'message' => '访问错误');
    $json_str = json_encode($ret);
    echo $json_str;Yii::app()->end();
}

$mod = new DriverExamStudy();
$data = $mod->getExamCate($driver_id,$type);
$need_exam = DriverExamnewOnline::model()->checkUserNeedExam($driver_id, DriverExamStudy::TYPE_DRIVER_PRISE_EXAM);
$return_data = array();
if($data){
    foreach($data as $cate_id   => $cate_name){
        $tmp['cate_id'] = $cate_id;
        $tmp['cate_name'] = $cate_name;
        $tmp['prise'] = (isset($need_exam[$cate_id]) && $need_exam[$cate_id]) ? 1 :0;
        $return_data[] = $tmp;
    }
}


if($return_data){
    $ret['code']=0;
    $ret['message']='ok';
    $ret['data'] = $return_data;
}

echo json_encode($ret);
Yii::app()->end();




