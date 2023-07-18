<?php
/**
 *  工单 操作用户 设为管理员 wanglonghuan 2014-1-9
 *  @params user_name
 */
class ManagerUserAction extends CAction
{
    public function run()
    {
        $username = urldecode($_GET['username']);
        $model = TicketUser::model()->find("`user`=:user",array('user'=>$username));

        $this->controller->render('manager_user',array(
            'model'=>$model,
        ));
    }
}