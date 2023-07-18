<?php
/**
 * 驳回补偿 ajax cuiluzhe 2014.07.16
 */
class RejectAction extends CAction
{
    public function run()
    {   
        $ret = array();
        $user_name = Yii::app()->user->name;
        if(isset($_POST['st_id'])){
	    $st_id = $_POST['st_id'];
            $date = date("Y-m-d H:i:s",time());
            $fee = SupportTicketFee::model()->getSupportTicketFeeByTicketId($st_id);
            $params = array(
                'status' => 3,
                'deal_user'=>$user_name,
                'deal_time' => $date,
            );
            $fee->saveAttributes($params);
	    
	    $supportTicket = SupportTicket::model()->findByPk($st_id);
	    $params2 = array(
                'status' => SupportTicket::ST_STATUS_REJECT,
                'operation_user'=>$user_name,
            );
            $supportTicket->saveAttributes($params2);
            $ret['code'] =  1;
            $ret['msg'] = "操作成功!";
        }else{
            $ret['code'] =  0;
            $ret['msg'] = "参数错误.";
        }
        echo json_encode($ret);
    }
}
