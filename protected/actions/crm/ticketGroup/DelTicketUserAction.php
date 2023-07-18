<?php
/**
 *  工单 删除用户 wanglonghuan 2014-1-9
 *  @params user_name
 */
class DelTicketUserAction extends CAction
{
    public function run()
    {
        $ret = array('code'=>0,'msg'=>'删除失败');
        $user_id= $_POST['userid'];
        $res = TicketUser::model()->removeUser($user_id);
        if($res){
            $ret = array('code'=>1,'msg'=>'删除成功');
        }
        echo json_encode($ret);
    }
}