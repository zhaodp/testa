<?php
/**
 * Created by PhpStorm.
 * User: liufugang
 * Date: 2015/5/7
 * Time: 14:22
 */

class DriverQuizLogic {
    private static $instance;

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new DriverQuizLogic();
        }
        return self::$instance;
    }

    /**
     * @param $driver
     */
    public function driverNeedQuiz($driver){
        $appVer =  DriverStatus::model()->app_ver($driver->driver_id);
        if(!empty($appVer) && $appVer>="2.6.0.0"){
            $unlock = QueueDispatchDriver::model()->isUnLock($driver->driver_id);
            if($unlock){
                $des = new DriverExamStudy();
                $examUrl = $des->getExamUrl($driver->driver_id);
                if(isset($examUrl ['num'] )&&isset($examUrl ['url'] )){
                    if($examUrl ['num']>0){
                        $ret=array(
                            'code'=>11,
                            'message'=>'请先做题',
                            'title'=>'e代驾试题',
                            'url'=>$examUrl ['url']
                        );
                        return $ret;
                    }
                }
            }
        }
    }

}