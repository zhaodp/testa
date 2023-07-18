<?php
/**
 * ajax解屏蔽功能
 * Enter description here ...
 * @author zengzhihai
 *
 */
class UpdateStatusAction extends CAction
{
    public function run(){
        if(isset($_GET['id'])) $id=$_GET['id'];
        if(isset($_GET['phone'])) $phone=$_GET['phone'];
        if(isset($_POST['action'])&&$_POST['action']=='update'){
            $_id=$_POST['id'];
            $_mark=$_POST['mark'];
            $_phone=$_POST['phone'];
            CustomerMain::model()->updateByPk($_id,array('status'=>1));
            //通过手机号查找对应的投诉列表
            $sql3="select * from {{driver_complaint}} where customer_phone=:phone";
            $command3=Yii::app()->db_readonly->createCommand($sql3)->bindValue(':phone',$_phone);
            $complaintNews = $command3->queryAll();
            if($complaintNews){
                $DrivercomplainLog = new DriverComplaintLog();
                foreach($complaintNews as $key=>$val){
                    $DrivercomplainLog->complain_id=$val['id'];
                    $DrivercomplainLog->driver_id=$val['driver_user'];
                    $DrivercomplainLog->phone=$_phone;
                    $DrivercomplainLog->type=$val['order_type'].','.$val['complaint_type'];
                    $DrivercomplainLog->mark=$_mark;
                    $DrivercomplainLog->status=DriverComplaint::DM_PROCESS_1;
                    $DrivercomplainLog->operator=Yii::app()->user->id;
                    $DrivercomplainLog->create_time=time();
                    $DrivercomplainLog->save();
                }
            }
            echo json_encode(array('succ'=>1));
            exit();
        }
        $this->controller->renderPartial('info/update_status',array('id'=>$id,'phone'=>$phone));
    }



}