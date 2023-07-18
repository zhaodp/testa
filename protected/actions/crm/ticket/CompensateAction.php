<?php
/**
 *  确认处理 ajax cuiluzhe 2014.07.16
 */
class CompensateAction extends CAction
{
    public function run()
    {   
        $ret = array();
        $user_name = Yii::app()->user->name;
        if(isset($_POST['st_id'])){
            $date = date("Y-m-d H:i:s",time());
            $fee = SupportTicketFee::model()->getSupportTicketFeeByTicketId($_POST['st_id']);
            $params = array(
                'status' => 2,
                'deal_user'=>$user_name,
                'deal_time' => $date,
            );
            $fee->saveAttributes($params);
	    $total = $fee->information_fee + $fee->insurance_fee + $fee->fine_fee + $fee->other_fee;
	    $supportTicket = SupportTicket::model()->findByPk($_POST['st_id']);
	    $comment='';
	    if($fee->information_fee>0){
		$comment .= '补偿司机信息费:'.$fee->information_fee.'元 ';
	    }else if($fee->information_fee<0){
		$comment .= '扣司机信息费:'.(-$fee->information_fee).'元 ';
	    }
	    if($fee->insurance_fee>0){
                $comment .= '补偿司机保险费:'.$fee->insurance_fee.'元 ';
            }else if($fee->insurance_fee<0){
                $comment .= '扣司机保险费:'.(-$fee->insurance_fee).'元 ';
            }
	    if($fee->fine_fee>0){
                $comment .= '补偿司机罚金:'.$fee->fine_fee.'元 ';
            }else if($fee->fine_fee<0){
                $comment .= '扣司机罚金:'.(-$fee->fine_fee).'元 ';
            }
	    if($fee->other_fee>0){
                $comment .= '补偿司机其他费用:'.$fee->other_fee.'元 ';
            }else if($fee->other_fee<0){
                $comment .= '扣司机其他费:'.(-$fee->other_fee).'元 ';
            }
	    if($total != 0){
		if($total>1000){
		    Helper::alert('金额最多1000元');
                    Yii::app()->end();
		}
		$params['cast']=$total;
                $params['comment']=$comment;
		$params['channel']=EmployeeAccount::CHANNEL_TICKET_COMPENSATE;//工单补偿
		$params['city_id']=$supportTicket->city_id;
		$params['order_id']=$supportTicket->order_id;
		$params['user']=strtoupper($supportTicket->driver_id);
                OrderSettlement::model()->driverRecharge($params);
	    }
            $ret['code'] =  1;
            $ret['msg'] = "操作成功!";
        }else{
            $ret['code'] =  0;
            $ret['msg'] = "参数错误.";
        }
        echo json_encode($ret);
    }
}
