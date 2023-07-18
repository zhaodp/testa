<?php
/**
 * 获取一个角色的权限 -user_id
 * @author duke 2014-06-17
 */
class getInfoByidAction extends CAction
{

    public function run()
    {

        $groups = array();
        $id = $_GET['id'] ? $_GET['id'] : '';
        if(!$id){
            throw new exception('缺少必须参数');
        }
        $mod_role2action = AdminRole::model();
        //echo'<pre>';
        $role_action = $mod_role2action->getRoleDetail($id);
        //print_r($role_action);die;
        //print_r($role_action);
//        echo '<pre>';
//        print_r($role_action);

        //print_r($my_group_mods_list);

        $this->controller->layout = '//layouts/main_no_nav';
        echo $this->controller->render('action_info_user', array ('info'=>$role_action));
    }
}