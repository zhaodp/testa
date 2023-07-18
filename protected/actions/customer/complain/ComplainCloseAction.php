<?php
/**
 * 直接关闭投诉
 * User: Bidong
 * Date: 13-6-19
 * Time: 下午1:39
 * To change this template use File | Settings | File Templates.
 */

class ComplainCloseAction extends CAction{
    public function run(){

        if($_POST){

            if(isset($_POST['sub_type']) && $_POST['sub_type']=='-1'){
                echo "<meta charset='utf-8'/>";
                echo "<script type='text/javascript'>alert('请选择投诉分类！');history.back();</script>";
                Yii::app()->end();
            }
            if(empty($_POST['mark'])){
                echo "<meta charset='utf-8'/>";
                echo "<script type='text/javascript' charset='utf-8'>alert('请填写处理意见');history.back();</script>";
                Yii::app()->end();
            }

            if(!empty($_POST['cid']) && isset($_POST['sub_type']) && !empty($_POST['mark'])){
                $complain_id=$_POST['cid'];
                $re=$_POST['re'];
                $model=CustomerComplain::model()->findByPk($complain_id);
                if($model){
                    if($model->status == CustomerComplain::STATUS_EFFECT){
                        //已经生效的不能关闭，否则无法撤销
                        echo "<meta charset='utf-8'/>";
                        echo "<script type='text/javascript' charset='utf-8'>alert('已经生效的投诉，不能关闭');history.back();</script>";
                        Yii::app()->end();
                    }
                    if($model->status == CustomerComplain::STATUS_END){
                        //已经生效的不能关闭，否则无法撤销
                        echo "<meta charset='utf-8'/>";
                        echo "<script type='text/javascript' charset='utf-8'>alert('已经关闭的投诉，不能关闭');history.back();</script>";
                        Yii::app()->end();
                    }


                    $model->attributes = $_POST['CustomerComplain'];
                    $model->status=CustomerComplain::STATUS_END;    //关闭状态
                    $model->sp_process=CustomerComplain::SP_PROCESS_S;    //排除投诉
                    $model->complain_type=intval($_POST['sub_type']);
                    $model->operator=Yii::app()->user->id;
                    $model->update_time=date('Y-m-d H:i:s',time());
//                    $model->pnode = CnodeLog::FINISH;//关闭投诉的时候处理节点直接变为 完结状态

                    if($model->update()){
                        //添加处理日志
                        CnodeLog::model()->pushCnodeLog($complain_id,CnodeLog::CLOSE_COMPLAIN,"关闭投诉");
                        if ($_POST['closing']==1) {
                            CnodeLog::model()->pushCnodeLog($complain_id,CnodeLog::FINISH,'完结投诉');
                        }
                        $complainLog=new CustomerComplainLog();
                        $complainLog->complain_id=$complain_id;
                        $complainLog->process_type=$complainLog::PROCESS_ONE;   //司管处理
                        $complainLog->operator=Yii::app()->user->id;
                        $complainLog->result=CustomerComplain::SP_PROCESS_S;    //排除投诉
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