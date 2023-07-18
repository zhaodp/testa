<?php
/**
 *  工单 操作用户 设为管理员 wanglonghuan 2014-1-9
 *  @params user_name
 */
class SetAdminAction extends CAction
{
    public function run()
    {
        $username = $_POST['username'];
        $model = TicketUser::model()->find("`user`=:user",array('user'=>$username));
        $is_admin = 0;
        if($model->is_admin == 1){
            $is_admin = 0;
        }else{
            $is_admin = 1;
        }
        if($model->saveAttributes(array('is_admin' => $is_admin))){
            $ret['msg'] = "设置成功";
        }else{
            $ret['msg'] = "设置失败";
        }
        $ret['code'] = $is_admin;
        echo json_encode($ret);
    }
}