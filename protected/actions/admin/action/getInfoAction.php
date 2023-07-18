<?php
/**
 * 获取用户权限 -user_id
 * @author duke 2014-06-17
 */
class getInfoAction extends CAction
{

    public function run()
    {

        $groups = array();
        $user_id = $_GET['user_id'] ? $_GET['user_id'] : '';
        if(!$user_id){
            throw new exception('缺少必须参数');
        }

        $action_list = AdminUser2role::model()->getactionById($user_id);

        //print_r($my_group_mods_list);

        $this->controller->layout = '//layouts/main_no_nav';
        echo $this->controller->render('action_info_user', array ('info'=>$action_list));
    }
}