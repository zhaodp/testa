<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bidong
 * Date: 13-6-19
 * Time: 下午11:02
 * To change this template use File | Settings | File Templates.
 */

class ComplainDriverProcessAction extends CAction {
    public function  run(){
        $complain_id='';
        $urlReferrer='';

        if($_GET){
            if(isset($_GET['re']))
                $urlReferrer=$_GET['re'];
            if(isset($_GET['cid']))
                $complain_id=$_GET['cid'];

        }

        if($_POST){

            $dm_pro_result=intval($_POST['dm_process']);
            $process_time=date('Y-m-d H:i:s',time());
            $complain_id=$_POST['cid'];
            $mark=$_POST['mark'];

            $complain_model=CustomerComplain::model()->findByPk($complain_id);
            $complain_model->status=CustomerComplain::STATUS_DM;    //司管已处理
            $complain_model->dm_process=$dm_pro_result;
            $complain_model->update_time=$process_time;
            $complain_model->operator=Yii::app()->user->id;
            $driver_id=$complain_model->driver_id;
            if($complain_model->save()){
                //添加处理日志
                $complainLog=new CustomerComplainLog();
                $complainLog->complain_id=$complain_id;
                $complainLog->process_type=$complainLog::PROCESS_TWO;   //司管处理
                $complainLog->operator=Yii::app()->user->id;
                $complainLog->result=$dm_pro_result;
                $complainLog->create_time=$process_time;
                $complainLog->mark=trim($mark);
                if($complainLog->insert()){
                    //处罚司机
                    if($dm_pro_result!=CustomerComplain::DM_PROCESS_P){
                        $limitDays=array('5'=>1,'6'=>3,'7'=>7,'8'=>3600);     //处罚结果和日期对应
                        $days=$limitDays[$dm_pro_result];
                        $this->driverBlock($driver_id,$dm_pro_result,$days);
                    }
                    $this->controller->redirect(Yii::app()->createUrl($_POST['re']));
                }
            }
        }
        $this->controller->renderPartial('driver_process',array('re'=>$urlReferrer,'cid'=>$complain_id));

    }


    //屏蔽司机
    protected function driverBlock($driver_id,$result,$days){

        $mark_reason='';
        if($result!=CustomerComplain::DM_PROCESS_P){
            if($result==CustomerComplain::DM_PROCESS_S){
                //$driver_mark=Employee::MARK_LEAVE;
                $type=DriverLog::LOG_MARK_LEAVE;  //解约
                $mark_reason='因投诉解约';
                DriverPunish::model()->leave_driver($driver_id, $type, $mark_reason);
            }

            if($result!=CustomerComplain::DM_PROCESS_S ){
                $type=DriverLog::LOG_MARK_DISABLE_COMPLAINTS;  //投诉屏蔽
                //$driver_mark=Employee::MARK_DISNABLE;
                $mark_reason='因投诉屏蔽 '.$days.' 天';
                DriverPunish::model()->disable_driver($driver_id, $type, $mark_reason, $days );
            }

            //屏蔽司机
            //Driver::model()->mBlock($driver_id, $driver_mark, $type, $mark_reason,$days);

        }

    }
}