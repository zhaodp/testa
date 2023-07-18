<?php
/**
 *  回复工单操作 ajax wanglonghuan 2013.12.23
 */
class ReplyAction extends CAction
{
    public function run()
    {
        $ret = array();
        if(isset($_POST['content']) && isset($_POST['model_id'])){
            $msg_model = new SupportTicketMsg();
            $log_model = new SupportTicketLog();
            $msg_model->support_ticket_id = $log_model->support_ticket_id = $_POST['model_id'];
            $msg_model->message = $_POST['content'];
            $msg_model->create_time =  $log_model->create_time = date("Y-m-d H:i:s",time());
            $msg_model->reply_user = Yii::app()->user->name;
            $msg_model->reply_user_type = 0;
            $msg_model->reply_type = $_POST['reply_type'];
            $msg_model->save();
            $log_model->op_content = Yii::app()->user->name . " 回复了工单：" . $_POST['model_id'];
            $log_model->action = SupportTicketLog::LOG_ACTION_REPLY;
            $log_model->operater = Yii::app()->user->name;
            $log_model->save();
            SupportTicket::model()->updateByPk($_POST['model_id'],array(
		'type'=>$_POST['type_id'],
		'class'=>$_POST['ticket_class'],
                'last_reply_user'=>Yii::app()->user->name,
                'new_msg' => 1,
                'last_reply_time'=>date("Y-m-d H:i:s",time())),'',array());

	   //设置补偿信息
	    $user_name = Yii::app()->user->name;
            if(isset($_POST['ope']) && $_POST['ope'] == 'yes'){
		$fee =  SupportTicketFee::model()->getSupportTicketFeeByTicketId($_POST['model_id']);
		if(isset($fee)){
                        $fee->information_fee = $_POST['information_fee'];
                        $fee->insurance_fee = $_POST['insurance_fee'];
                        $fee->fine_fee = $_POST['fine_fee'];
                        $fee->other_fee = $_POST['other_fee'];
                        $params2 = array(
                                'information_fee' => $_POST['information_fee'],
                                'insurance_fee'=> $_POST['insurance_fee'],
                                'fine_fee' => $_POST['fine_fee'],
                                'other_fee'=> $_POST['other_fee'],
                        );
                        $fee->saveAttributes($params2);

                }else{
                        $fee = new SupportTicketFee();
                        $fee->support_ticket_id = $_POST['model_id'];
                        $fee->information_fee = $_POST['information_fee'];
                        $fee->insurance_fee = $_POST['insurance_fee'];
                        $fee->fine_fee = $_POST['fine_fee'];
                        $fee->other_fee = $_POST['other_fee'];
                        $fee->create_user = $user_name;
                        $fee->create_time = date('Y-m-d H:i:s',time());
                        $fee->status = 1;
                        $fee->save();
                }
            }

            $ret['code'] =  1;
            $ret['msg'] = "回复成功!";
        }else{
            $ret['code'] =  0;
            $ret['msg'] = "参数错误.";
        }
        echo json_encode($ret);
    }
}
