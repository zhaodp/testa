<?php
/**
 *  添加工单 操作用户 wanglonghuan 2013.12.23
 */
class AddAction extends CAction
{
    public function run()
    {
        $tuser_model =new TicketUser();
        if(isset($_POST['TicketUser'])){
            if($tuser_model->checkUserExist($_POST['TicketUser']['user'])){
                //echo '<script type="text/javascript">confirm("用户已经存在，不能重新创建！");</script>';
                $this->controller->redirect(array("/crm/groupUserAdd",'error_msg'=>"用户名已经存在，不能重新创建！"));
            }

            if(!isset($_POST['is_admin'])){
                $params = array(
                    'group'=>$_POST['TicketUser']['group']
                );
                $sql = "SELECT max(`cursor_sort`) cursor_sort FROM `t_ticket_user` `t` WHERE `t`.`group`=:group LIMIT 1;";
                $sort = $tuser_model->findBySql($sql,$params);

                if(empty($sort->cursor_sort)){
                    $tuser_model->cursor_sort = 1;
                }else{
                    $tuser_model->cursor_sort = $sort->cursor_sort+1;
                }
                $tuser_model->is_admin = 0;
                $tuser_model->group = $_POST['TicketUser']['group'];
            }else{
                $tuser_model->is_admin = 1;
            }
            $tuser_model->user = $_POST['TicketUser']['user'];
            $tuser_model->create_time = date("Y-m-d H:i:s",time());
            $tuser_model->create_user = Yii::app()->user->name;
            $tuser_model->city_id = $_POST['TicketUser']['city_id'];
            $tuser_model->status = 1;
            $tuser_model->save();

            echo '<script type="text/javascript">alert("创建成功！");</script>';
            $this->controller->redirect(array("crm/groupUserList"));
        }
        $this->controller->render('ticket_group_add',array(
            'model' => $tuser_model,
        ));
    }
}