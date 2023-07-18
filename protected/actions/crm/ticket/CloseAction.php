<?php
/**
 *  关闭订单 ajax wanglonghuan 2013.12.23
 */
class CloseAction extends CAction
{
    public function run()
    {   
        $ret = array();
        $user_name = Yii::app()->user->name;
        if(isset($_POST['st_id'])){
            $date = date("Y-m-d H:i:s",time());
            $ticket_model = SupportTicket::model()->findByPk($_POST['st_id']);
            $params = array(
		'type'=>$_POST['type_id'],
                'class'=>$_POST['ticket_class'],
                'status' => SupportTicket::ST_STATUS_CLOSE,
                'operation_user'=>$user_name,
                'close_time' => $date,
                'last_reply_user'=>Yii::app()->user->name,
                'last_reply_time'=>$date,
            );
            $ticket_model->saveAttributes($params);
	    //设置补偿信息
	    if(isset($_POST['ope']) && $_POST['ope'] == 'yes'){
		$fee =  SupportTicketFee::model()->getSupportTicketFeeByTicketId($ticket_model->id);
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
				'status'=> 1,
            		);
			$fee->saveAttributes($params2);
			
		}else{
		        $fee = new SupportTicketFee();
                	$fee->support_ticket_id = $_POST['st_id'];
                	$fee->information_fee = $_POST['information_fee'];
                	$fee->insurance_fee = $_POST['insurance_fee'];
                	$fee->fine_fee = $_POST['fine_fee'];
                	$fee->other_fee = $_POST['other_fee'];
                	$fee->create_user = $user_name;
                	$fee->create_time = date('Y-m-d H:i:s',time());
                	$fee->status = 1;
                	$fee->save();
		}
	    }else{
		 $fee = SupportTicketFee::model()->getSupportTicketFeeByTicketId($ticket_model->id);
		 if(isset($fee)){
		    $fee->delete();
		 }
	    }

            $log_model = new SupportTicketLog();
            $log_model->support_ticket_id = $_POST['st_id'];
            $log_model->op_content = $user_name ."关闭了 工单：" . $_POST['st_id'];
            $log_model->create_time = $date;
            $log_model->action = SupportTicketLog::LOG_ACTION_CLOSE;
            $log_model->operater = $user_name;
            $log_model->save();
            $msg_model = new SupportTicketMsg();
            $params = array(
                'ticket_id' => $_POST['st_id'],
                'message' => $_POST['content'],
                'reply_user' => $user_name,
            );
            $msg_model->createSupportTicketMsg($params,0,SupportTicketMsg::REPLY_TYPE_TO_DRIVER);
            $ret['code'] =  1;
            $ret['msg'] = "操作成功!";
        }else{
            $ret['code'] =  0;
            $ret['msg'] = "参数错误.";
        }
        echo json_encode($ret);
    }
}
