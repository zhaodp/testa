<?php

Yii::import('application.models.customer.CustomerComplain');
class ComplainCommand extends CConsoleCommand {

    public function actionWorker() {
		echo "is ok?";
    }

    /**
     * 投诉自动派工处理
     */
    public function actionAutoDispatch()
    {
        //查询未派工的投诉
        $complains = CustomerComplain::model()->getUnDispatchComplain();
        if ($complains) {
            foreach ($complains as $k=>$v) {
                //查询投诉任务人其他未结案并已经分配任务人的投诉
                $user = CustomerComplain::model()->getOtherComplainUser($v['phone'], $v['id']);
                if (!$user) {//有投诉，将此投诉分配给相关任务人
                    $user = CustomerComplainGroupUser::model()->getTaskUser($v['complain_type']);//获取任务人
                } else {
                    echo date('Y-m-d H:i:s')." dispatch complain[".$v['id']."] to relate user[".$user['user_id']."]\r\n";
                }

                if ($user) {
                    //分配任务人
                    CustomerComplain::model()->setComplainUser($v['id'],$user['group_id'],$user['user_id']);
                    echo date('Y-m-d H:i:s')." dispatch complain[".$v['id']."] to user[".$user['user_id']."]\r\n";
                } else {
                    echo date('Y-m-d H:i:s')." no user to dispatch complain[".$v['id']."]\r\n";
                }
            }
        } else {
            echo date('Y-m-d H:i:s')." no complain need dispatch\r\n";
        }
        Yii::app()->end();
    }
}
