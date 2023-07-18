<?php
/**
 *  工单详细页面
 * @author wanglonghuan
 * @date 2013/12/24
 */
class ViewAction extends CAction
{
    public function run()
    {
        $model = SupportTicket::model()->findByPk($_GET['id']);
        $msg_models = SupportTicketMsg::model()->findAll("support_ticket_id=:st_id",array('st_id' => $model->id));
	$fee = SupportTicketFee::model()->getSupportTicketFeeByTicketId($model->id);
        //$params = array('st_id'=>$model->id, 'action' => SupportTicketLog::LOG_ACTION_CLOSE);
        //$log_model = SupportTicketLog::model()->find("support_ticket_id=:st_id and action=:action",$params);
        $this->controller->render('ticket_view',array(
            'model' => $model,
            'msg_models' => $msg_models,
	    'fee' => $fee,
            //'log_model' => $log_model,
        ));
    }
}
