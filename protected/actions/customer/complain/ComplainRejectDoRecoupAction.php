<?php
/**
 * 财务拒绝品监补偿
 * User: aiguoxin
 * Date: 14-7-10
 * Time: 下午11:33
 */

class ComplainRejectDoRecoupAction extends CAction
{
    public function run()
    {

        if ($_POST && !empty($_POST['cid'])) {
            $process_time = date('Y-m-d H:i:s', time());
            $operator = Yii::app()->user->id;
            $complain_id = $_POST['cid'];
            $complain = CustomerComplain::model()->findByPk($complain_id);

            if ($complain) {
                $process_result = CustomerComplain::STATUS_REJECT;
                $complain->status = CustomerComplain::STATUS_REJECT;

                $complain->sp_process = $process_result; //品监处理结果

                // $complain->operator = $operator;
                // $complain->update_time = $process_time; 去掉，新需求：只记录品监处理的
                if ($complain->save()) {
                    //更改补偿记录状态
                    $pk = $_POST['id'];
                    $recoup = CustomerComplainRecoup::model()->findByPk($pk);
                    $recoup->operator = $operator;
                    $recoup->update_time = $process_time;
                    $recoup->status = CustomerComplainRecoup::STATUS_REJECT;
                    $recoup->mark=$operator.'已驳回';

                    $recoup->save();
                    //记录日志
                    $data=array(
                        'driver_id'=>$recoup->driver_id,
                        'driver_cash'=>$recoup->amount_driver,
                        'mark'=>$operator.'已驳回',
                        );
                    $this->addProcessLog($complain_id,$process_result,$data);
                    CnodeLog::model()->pushCnodeLog($complain_id,CnodeLog::REJECT_COMPLAIN,$operator.'已驳回');//驳回投诉添加到最新日志
                    $this->controller->redirect($_POST['re']);
                }
            }
        }
    }

    //添加处理日志
    protected function addProcessLog($complain_id, $process_result, $data)
    {
        $complainLog = new CustomerComplainLog();
        $recoup_user = $recoup_amount = '';

       
        $recoup_user = $data['driver_id']; //绑定手机号
        $recoup_amount = $data['driver_cash'];
        $complainLog->recoup_amount = $recoup_amount;
        $complainLog->recoup_user = $recoup_user;
        $complainLog->result = $process_result;
        $complainLog->mark = $data['mark'];
        $complainLog->complain_id = $complain_id;
        $complainLog->process_type = $complainLog::PROCESS_THREE; //财务处理
        $complainLog->operator = Yii::app()->user->id;
        $complainLog->create_time = date('Y-m-d H:i:s', time());
        $complainLog->recoup_type = $process_result; //补偿方式
        $complainLog->payer = 1;

        $complainLog->insert();


    }

}
