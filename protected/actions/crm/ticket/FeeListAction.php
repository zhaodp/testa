<?php
/**
 *  工单补扣款列表
 * @author cuiluzhe
 * @date 2014/07/15
 */
class FeeListAction extends CAction
{
    public function run()
    {
        $param = array();
        if(isset($_GET['support_ticket_id'])){
                $param['support_ticket_id'] = $_GET['support_ticket_id'];
        }
        if(isset($_GET['driver_id'])){
                $param['driver_id'] = $_GET['driver_id'];
        }   
        if(isset($_GET['city_id'])){
                $param['city_id'] = $_GET['city_id'];
        }   
        if(isset($_GET['status'])){
                $param['status'] = $_GET['status'];
        }     
        if(isset($_GET['create_user'])){
                $param['create_user'] = $_GET['create_user'];
        } 
        if(isset($_GET['deal_user'])){ 
                $param['deal_user'] = $_GET['deal_user'];
        } 
        if(isset($_GET['create_time_begin'])){
                $param['create_time_begin'] = $_GET['create_time_begin'];
        } 
        if(isset($_GET['create_time_end'])){
                $param['create_time_end'] = $_GET['create_time_end'];
        } 
        if(isset($_GET['feec_time_begin'])){
                $param['feec_time_begin'] = $_GET['feec_time_begin'];
        }
        if(isset($_GET['feec_time_end'])){
                $param['feec_time_end'] = $_GET['feec_time_end'];
        }
        if(isset($_GET['feed_time_begin'])){
                $param['feed_time_begin'] = $_GET['feed_time_begin'];
        }
        if(isset($_GET['feed_time_end'])){
                $param['feed_time_end'] = $_GET['feed_time_end'];
        }
        if(!TicketUser::model()->checkUserExist(Yii::app()->user->name))
        {
            throw new CHttpException(401, '您没有工单权限，请联系后台人员添加工单权限！');
        }
        $dataProvider = SupportTicketFee::model()->getSupportTicketFeeList($param);
        $this->controller->render('ticket_fee_list',array(
            'param' =>$param,
            'dataProvider' => $dataProvider,
        ));
    }
}
