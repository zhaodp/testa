<?php

class DriverExamStudy
{

    CONST CACHE_KEY = 'EXAM_KEY';

    CONST ALLOW_TIMES = 10;
    CONST ALLOW_TEST_TIIMES = 20;


    CONST TYPE_DRIVER_EXAM = 2; //被投诉司机考核
    CONST TYPE_DRIVER_PRISE_EXAM = 3; //司机端有奖答题

    CONST CATE_MUST_ID = 14;

    public function getExamCate($driver_id, $type = self::TYPE_DRIVER_PRISE_EXAM){
        if($type == self::TYPE_DRIVER_EXAM){
            $pool = Yii::app()->params['driver_study_list'];
            if(in_array($driver_id,$pool)){
                return array(self::CATE_MUST_ID =>'安全类');
            }
            else return false;
        }
        else{
            return QuestionNew::getCategory();
        }
    }


    public function checkDriverNeedExam($driver_id, $type){
        if($type == self::TYPE_DRIVER_EXAM){
            $driver_pool = Yii::app()->params['driver_study_list'];
            if(in_array($driver_id,$driver_pool)){
                $cate_id = self::CATE_MUST_ID;
                $needexam = DriverExamnewOnline::model()->checkNeedExam($driver_id , $type , $cate_id);
                return $needexam;
            }
        }
        if($type == self::TYPE_DRIVER_PRISE_EXAM) {
            $data = DriverExamnewOnline::model()->checkUserNeedExam($driver_id , $type);
            return $data;
        }
    }

    //http://h5.d.edaijia.cn/recruit/exam-app.html?driverId=123&type=1&cateId=1
    public function getExamUrl($driver_id, $testenv = false){
        $need = $this->checkDriverNeedExam($driver_id,self::TYPE_DRIVER_EXAM);
        $count = $need ? 1 : 0;

        if(!$testenv){
            return array(
                'url'=>'http://h5.edaijia.cn/recruit/exam-app.html?driverId='.$driver_id.'&type=2',
                'num'=>$count
            );
        }else {
            return array(
                'url'=>'http://h5.d.edaijia.cn/recruit/exam-app.html?driverId='.$driver_id.'&type=2',
                'num'=>$count
            );
        }
    }
}