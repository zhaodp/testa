<?php
/**
 * 司机报名h5---获取试题
 * @author luzhe
 * @version 2014-08-06
 */
$id_card = isset($params['id_card']) ? trim($params['id_card']) : '';
$callback=isset($params['callback'])?$params['callback']:'';

if(empty($id_card)){
    $ret = array('code' => 1 , 'message' => '请输入身份证号码');
    $json_str = json_encode($ret);
    if(isset($callback)&&!empty($callback)){
        $json_str=$callback.'('.$json_str.')';
    }
    echo $json_str;Yii::app()->end();
} 
 $isDirverOld = 0;//0 新司机，1 老司机
 $is_pass_exam = false;//未通过考试
$zhaopin = DriverRecruitment::model()->getDriverByIDCard($id_card);
if (!$zhaopin){
$ret = array('code' => 1 , 'message' => '您还没有报名,请先报名');
    $json_str = json_encode($ret);
    if(isset($callback)&&!empty($callback)){
        $json_str=$callback.'('.$json_str.')';
    }
    echo $json_str;Yii::app()->end();
}else{
if($zhaopin['exam'] == DriverRecruitment::STATUS_ONLINE_EXAM_PASS){
        $ret = array('code' => 1 , 'message' => '您已经完成考试');
        $json_str = json_encode($ret);
        if(isset($callback)&&!empty($callback)){
            $json_str=$callback.'('.$json_str.')';
        }
        echo $json_str;Yii::app()->end();
    }else{
        $city_id = $zhaopin['city_id'];
        //判断是否走新流程城市，是的话，提示预约 add by aiguoxin
        $open = DriverOrder::model()->checkOpenCity($city_id);
        if($open){
            $id_card=$zhaopin['id_card'];
            $city_name = RCityList::model()->getCityByID($city_id,'city_name');
            $book_record = BookingExamDriver::model()->find('id_card = :id_card AND date >= :date',
                array(':id_card' => $id_card,
                    ':date' => date('Ymd'),
                )
            );
            if($book_record){ //已经预约
                $hours=$book_record['hours'];
                $hours=BookingHoursSetting::model()->getHoursDesc($city_id,$hours);
                $date =date ( "Y年m月d日",strtotime($book_record['date']));
                $date=$date.' '.$hours;
                $ret = array('code'=>3,'message'=>'you are reserved','data'=>array('city_name'=>$city_name,'date'=>$date));
            }else{
                $ret = array('code'=>4,'message'=>'you are not reserved','id_card'=>$id_card,
                    'data'=>array('city_name'=>$city_name,'city_id'=>$city_id));
            }
            $json_str = json_encode($ret);
            if(isset($callback)&&!empty($callback)){
                $json_str=$callback.'('.$json_str.')';
            }
            echo $json_str;Yii::app()->end();
        }

        $arrExam = Question::model()->getQuestionList($id_card, $city_id, 0);
    if(!$arrExam){
    $ret = array('code' => 1 , 'message' => '获取数据错误');
            $json_str = json_encode($ret);
            if(isset($callback)&&!empty($callback)){
                $json_str=$callback.'('.$json_str.')';
            }
            echo $json_str;Yii::app()->end();
    }
    $exams = array();
    foreach($arrExam as $k => $v){
    //$dataArray[$k] = $v->attributes;
    $dataArray[$k]['id'] = $v->id;
    $dataArray[$k]['title'] = $v->title;
    $dataArray[$k]['contents'] = json_decode($v->contents);
    $dataArray[$k]['correct'] = $v->correct;
    $dataArray[$k]['track'] = $v->track;
    $dataArray[$k]['type'] = $v->type;
    }
    $exams = array_merge($exams,$dataArray);
        $ret = array('code' => 0 , 'message' => '获取成功', 'data' => $exams);
        $json_str = json_encode($ret);
        if(isset($callback)&&!empty($callback)){
            $json_str=$callback.'('.$json_str.')';
        }
        echo $json_str;Yii::app()->end();
    }
}
