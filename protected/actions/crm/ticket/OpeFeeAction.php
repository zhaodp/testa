<?php
/**
 *  工单补扣款列表
 * @author cuiluzhe
 * @date 2014/07/15
 */
class OpeFeeAction extends CAction
{
    public function run()
    {
        if(!TicketUser::model()->checkUserExist(Yii::app()->user->name))
        {
            throw new CHttpException(401, '您没有工单权限，请联系后台人员添加工单权限！');
        }
	$view = '0';
	if(isset($_GET['view'])){
		$view = $_GET['view'];
	}
	$model = SupportTicket::model()->findByPk($_GET['id']);
	$msg_models = SupportTicketMsg::model()->findAll("support_ticket_id=:st_id",array('st_id' => $model->id));
        $fee = SupportTicketFee::model()->getSupportTicketFeeByTicketId($model->id);
        $this->controller->render('ticket_fee_ope',array(
            'model' => $model,
	    'msg_models' => $msg_models,
            'fee' => $fee,
	    'view'=> $view,
        ));
    }
}
