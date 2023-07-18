<?php
/**
 * 创建工单 400页面
 * @author wanglonghuan
 * @date 2013/12/24
 */
class AddAction extends CAction
{
    public function run()
    {
        if (isset($_REQUEST['phone'])) {
            $phone = $_REQUEST['phone'];
        }else{
            echo '缺少参数 电话！：phone';
            exit;
        }

        if (isset($_REQUEST['callid'])) {
            $callid = $_REQUEST['callid'];
        }else{
            $callid = '';
        }

        $order_list = array();
        $consult_params = array('phone' => $phone,'callid' => $callid);

        //司机信息
        $driver = Driver::model()->getDriverByPhone($phone);
        $driver_info = array();
        $status = '';
        if ($driver) {
            $consult_params['driver_id'] = $driver->user;
            $consult_params['name'] = $driver->name;
            $driver_info = Driver::model()->driverExtendData($driver['user']);

            $order_list = Order::model()->getOrderNearTwoDays($driver['user']);
            //师傅当前状态
            $status = Driver::model()->getDriverStatus($driver->user);
        }

        //初始化页面信息
        $knowledge_list = array();
        $cat = Dict::items("knowledge_cat");
        $cat["0"] = "其他问题";
        foreach ($cat as $k => $v) {
            $knowledge_list[$k]['name'] = $v;
            $knowledge_list[$k]['list'] = Knowledge::model()->getKnowledgeByCat($k);
        }

//        if(!TicketUser::model()->checkUserExist(Yii::app()->user->name)){
//            echo '您没有在工单处理人列表，请联系后台权限管理人员，添加至工单处理人列表。';
//            exit;
//        }
        //新建
        if(!empty($_POST['content']) && isset($_POST['is_finish']) && isset($_POST['type'])){
            //$transaction = Yii::app()->db->beginTransaction(); //开启实务
            //try{
                $model = new SupportTicket();
                $model->type = $_POST['type'];
                $model->content = $_POST['content'];
                $model->source = 1;
                $model->create_user = Yii::app()->user->name;
                $model->phone_number  = $phone;
                $model->create_time = date('Y-m-d H:i:s',time());
                $model->driver_id = $driver->user;
                $model->deadline = date('Y-m-d H:i:s',strtotime("+2 days"));
                $model->city_id = $driver->city_id;

                //根据分类 获取 处理部门 下个跟单人。
                $groupUserInfo = TicketUser::getFollowUser($_POST['type']);
                if(!$groupUserInfo){
                    $groupUserInfo['group']  = 0;
                    $groupUserInfo['user'] = '-';
                }
                $model->group = $groupUserInfo['group'];
                //直接提已解决工单
                if($_POST['is_finish'] == '1'){
                    $model->status = SupportTicket::ST_STATUS_CLOSE;
                    $model->close_time = date('Y-m-d H:i:s',time());
                    $model->operation_user = Yii::app()->user->name;
                    $model->follow_user = Yii::app()->user->name;
                    $group = TicketUser::model()->getGroup(Yii::app()->user->name);
                    $model->group = $group == 0 ? 7 : $group;
                }else{
                    //提交相关部门
                    $model->status = SupportTicket::ST_STATUS_PROCESSING;   //处理中
                    $model->operation_user = $groupUserInfo['user'];
                    $model->follow_user = $groupUserInfo['user'];
                }
                //投诉对象类型，投诉对象 电话/工号
                $complaint_type = $_POST['complaint_type'];
                $complaint_target = $_POST['complaint_target'];
                $customer_complain_id = $_POST['complaint_id'];
                $model->complaint_type = $complaint_type;
                $model->complaint_target = $complaint_target;
                $model->customer_complain_id = $customer_complain_id;
                $model->save();



                if($_POST['is_finish'] == '1'){
                    $log_model = new SupportTicketLog();
                    $log_model->support_ticket_id = $model->id;
                    $log_model->op_content = Yii::app()->user->name . "提交 并 关闭了工单：".$model->id."。\n"
                        ."解决方法：".$_POST['finish_content'];
                    $log_model->create_time = date('Y-m-d H:i:s',time());
                    $log_model->action = SupportTicketLog::LOG_ACTION_CLOSE;
                    $log_model->operater = Yii::app()->user->name;
                    $log_model->save();
                    $msg_model = new SupportTicketMsg();
                    $msg_model->support_ticket_id = $model->id;
                    $msg_model->message = $_POST['finish_content'];
                    $msg_model->create_time = date('Y-m-d H:i:s',time());
                    $msg_model->reply_user = Yii::app()->user->name;
                    $msg_model->save();
                }

                echo '<script type="text/javascript">alert("提交成功！");window.close();</script>';
            //    $transaction->commit();
            //}catch (Exception $e){
            //   $transaction->rollback(); //如果系统异常，实务回滚
            //   throw new CHttpException(500,$e->getMessage());
            //}
        }
        //搜索后的结果
        $dataProvider = Knowledge::model()->search_index($_GET);
        $this->controller->render('ticket_add',array(
            'dataProvider' => $dataProvider,
            'driver' => $driver,
            'driver_info' => $driver_info,
            'order_list' => $order_list,
            'status' => $status,
            'knowledge_list' => $knowledge_list,
        ));
    }
}