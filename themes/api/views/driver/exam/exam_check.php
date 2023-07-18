<?php
/**
 * 司机报名微信--在线考试提交答案
 * @author duke
 * @version 2015-03-30
 */
$open_id = isset($params['open_id']) ? trim($params['open_id']) : '';
$exam_id = isset($params['exam_id']) ? trim($params['exam_id']) : '';
$answer = isset($_POST['answer']) ? trim($_POST['answer']) : '';
$callback = isset($params['callback']) ? trim($params['callback']) : '';

$ret = array('code' => 1 , 'data'=>'', 'message' => '访问错误');

if(!$open_id || !$exam_id || !$answer){

    $json_str = json_encode($ret);
    echo $json_str;Yii::app()->end();
}

$user_answer = json_decode($answer,1);
$ret = DriverExamnewOnline::model()->checkAnswer($exam_id,$open_id, DriverExamnewOnline::TYPE_ONLINE,$user_answer);
if($ret['code'] == 0 ){ //考试通过 表示一下报名表的状态
    $user_info = DriverRecruitment::model()->getInfoByOpenid($open_id);
    if(!$user_info){
        $ret = array('code' => 35 , 'data'=>'', 'message' => '您尚未报名，请先报名考试');
        $json_str = json_encode($ret);
        echo $json_str;Yii::app()->end();
    }
    //$user_city_id = $user_info['city_id'];
    $signup_id = $user_info['id'];
    if( $ret['data']['status'] == 1){
        $res = DriverRecruitment::model()->updateByPk($signup_id,array('exam'=>DriverRecruitment::STATUS_ONLINE_EXAM_PASS));
        if($res){
            EdjLog::info('save driver recruitment field signup id:'.$signup_id);
        }
    }else{
        $res = DriverRecruitment::model()->updateByPk($signup_id,array('exam'=>DriverRecruitment::STATUS_ONLINE_EXAM_FAILED));
    }
}

echo $callback ? $callback.'('.json_encode($ret).')' : json_encode($ret);
Yii::app()->end();


