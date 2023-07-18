<?php
/**
 * 录入处理备注
 * User: zengzhihai
 * Date: 13-7-10
 * Time: 下午10:55
 * To change this template use File | Settings | File Templates.
 */

class ComplainRemarkAction extends CAction{
    public function run(){
    	
        if($_GET){
            if(isset($_GET['cid'])){
                $complain_id=$_GET['cid'];
                $re=$_GET['re'];
                
                $this->controller->renderPartial('remark',array('re'=>$re,'cid'=>$complain_id));
            }
        }
        if($_POST){
            if(isset($_POST['cid'])){
                $complain_id=$_POST['cid'];
                $re=$_POST['re'];
                $model=CustomerComplain::model()->findByPk($complain_id);
                if($model){
                    if($model->update()){
                        //添加处理日志
                        $complainLog=new CustomerComplainLog();
                        $complainLog->complain_id=$complain_id;
                        $complainLog->process_type=$complainLog::PROCESS_ONE;   //品鉴处理
                        $complainLog->operator=Yii::app()->user->id;
                        $complainLog->result=$complainLog::PROCESS_ONE_ONE;
                        $complainLog->create_time=date('Y-m-d H:i:s',time());
                        $complainLog->mark=trim($_POST['mark']);
                        if($complainLog->insert()){
                            $this->controller->redirect($re);
                        }
                    }
                }

            }
        }
    }
}