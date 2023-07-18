<?php
/**
 *  工单 转处理人  指派处理人  wanglonghuan 2014.1.9
 * @param ticket_id，child_sel，group_sel,city_id
 * @param echo json array(code, msg)
 */
class AssignAction extends CAction
{
    public function run()
    {
        $ret = array(
            'code' => 0,
            'msg' =>''
        );
        $groupUserMap = array();
        $ticket_id = $_POST['ticket_id'];
        if(isset($_POST['child_sel']) && !empty($_POST['child_sel'])){
            //指定到具体人
            $groupUserMap['group'] = $_POST['group_sel'];
            $groupUserMap['user'] = $_POST['child_sel'];
        }else{
            //根据部门 选下一个 司管择需要根据城市id分配 处理人
            $groupUserMap = TicketUser::model()->getFollowUser(0,$_POST['group_sel'],$_POST['city_id']);
        }
	SupportTicket::model()->updateByPk($ticket_id,array(
                'type'=>$_POST['type_id'],
                'class'=>$_POST['ticket_class'])
		);
	
	//设置补偿信息
            if(isset($_POST['ope']) && $_POST['ope'] == 'yes'){
		$fee =  SupportTicketFee::model()->getSupportTicketFeeByTicketId($_POST['ticket_id']);
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
                        $fee->support_ticket_id = $_POST['ticket_id'];
                        $fee->information_fee = $_POST['information_fee'];
                        $fee->insurance_fee = $_POST['insurance_fee'];
                        $fee->fine_fee = $_POST['fine_fee'];
                        $fee->other_fee = $_POST['other_fee'];
                        $fee->create_user = Yii::app()->user->name;
                        $fee->create_time = date('Y-m-d H:i:s',time());
                        $fee->status = 1;
                        $fee->save();
                }
            }	

        if(SupportTicket::model()->changeOperactionUser($groupUserMap['group'],$groupUserMap['user'],$ticket_id)){
            //操作日志
            SupportTicketLog::model()->SupportTicketAddLog($ticket_id,Yii::app()->user->name,SupportTicketLog::LOG_ACTION_TOOP,"");
            $params = array(
                'ticket_id'=>$ticket_id,
                'message'=>$_POST['content'],
                'reply_user'=>Yii::app()->user->name,
            );
            //回复
            SupportTicketMsg::model()->createSupportTicketMsg($params,0,$_POST['reply_type']); //user_type:公司，reply_type:内部沟通
            $ret = array(
                'code' => 1,
                'msg' =>'指派成功',
            );
        }else{
            $ret = array(
                'code' => 0,
                'msg' =>'指派失败',
            );
        }
        echo json_encode($ret);
    }
}
