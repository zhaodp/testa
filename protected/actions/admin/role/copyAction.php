<?php
/**
 * 用户角色复制 -user_id
 * @author duke 2015-08-10
 */
class copyAction extends EAction
{

    public function run()
    {
        if((!isset($_GET['dep_id']) || !$_GET['dep_id'] || !isset($_GET['id']) || !$_GET['id']) ){
            throw new CHttpException(404,'缺少必须参数');
        }

        //得到当前登录用户信息
        $currentUserInfo =  Yii::app()->user->getCurrentUserInfo();
        $dep_id = $_GET['dep_id'];
        $id = $_GET['id'];
        $mod = AdminRole::model()->findByPk($id);
        if(isset($_POST['AdminRole'])){
            $mod = new AdminRole();
            //print_r($_POST);die;

            $_POST['AdminRole']['create_time'] = date('Y-m-d H:i:s');
            $role_new = array();
            if(isset($_POST['AdminRole']['role'])){
                $role_new = $_POST['AdminRole']['role'];
                unset($_POST['AdminRole']['role']);
            }

            $mod->attributes = $_POST['AdminRole'];



            if ($mod->save()){
                if(!empty($role_new)){
                    $role_id = $mod->primaryKey;
                    $role2action_mod = AdminRole2action::model()->saveRole2Action($role_id,$role_new,$_POST['AdminRole']['status']);
                }
                if (!empty($_GET['dialog'])) {
                    echo CHtml::script("window.parent.$('#role_add_dialog').dialog('close');window.parent.$('#roleadd_frame').attr('src','');window.parent.$.fn.yiiGridView.update('{$_GET['grid_id']}');");
                    Yii::app()->end();
                }
            }
        }
        $actions = array();
        $can_edit_depadmin_role = false;
        if($currentUserInfo['admin_level'] == AdminUserNew::LEVEL_ADMIN ){
            if( $mod->type == AdminRole::TYPE_DEPART){
                $actions = AdminActions::model()->getAllNormalAction();
            }else
                if($mod->type == AdminRole::TYPE_GROUP){
                    $dep_info = AdminDepartment::model()->getInfoByid($dep_id);
                    $parent_dep_id = $dep_info['parent_id'];
                    $parent_dep_role_info  = AdminRole::model()->getDepartmentRoles($parent_dep_id); //部门对应的最大权限 角色组
                    $default_role_id = $parent_dep_role_info['id'];
                }else {
                    //echo $dep_id;die;
                    $dep_info = AdminDepartment::model()->getInfoByid($dep_id);
                    $parent_dep_id = $dep_info['parent_id'];
                    $type_dep = ($parent_dep_id)  ? AdminRole::TYPE_GROUP : AdminRole::TYPE_DEPART;
                    $department_role = AdminRole::model()->getDepartmentRoles($dep_id,$type_dep); //部门对应的最大权限 角色组
                    $default_role_id = $department_role['id'];
                }
            $can_edit_depadmin_role = true;
        }
        else if($currentUserInfo['admin_level'] == AdminUserNew::LEVEL_DEPARTMENT_ADMIN){
            if($mod->type == AdminRole::TYPE_DEPART){
                $can_edit_depadmin_role = false;
                $default_role_id = 0;//$id; 部门管理员不能编辑部门默认角色组
            }else
                if($mod->type == AdminRole::TYPE_GROUP){
                    $dep_info = AdminDepartment::model()->getInfoByid($dep_id);
                    $parent_dep_id = $dep_info['parent_id'];
                    $parent_dep_role_info  = AdminRole::model()->getDepartmentRoles($parent_dep_id); //部门对应的最大权限 角色组
                    $default_role_id = $parent_dep_role_info['id'];
                    $can_edit_depadmin_role = true;
                }
                else{
                    $dep_info = AdminDepartment::model()->getInfoByid($dep_id);
                    $parent_dep_id = $dep_info['parent_id'];
                    $type_dep = ($parent_dep_id)  ? AdminRole::TYPE_GROUP : AdminRole::TYPE_DEPART;
                    $department_role = AdminRole::model()->getDepartmentRoles($dep_id,$type_dep); //部门对应的最大权限 角色组
                    $default_role_id = $department_role['id'];
                    $can_edit_depadmin_role = false;
                }
        }
        else { //小组管理员
            if($mod->type == AdminRole::TYPE_DEPART){
                $this->alertWindow('没有权限');
            }
            else if($mod->type == AdminRole::TYPE_GROUP){
                $can_edit_depadmin_role = true;
            }
            else {
                $department_role = AdminRole::model()->getDepartmentRoles($dep_id,AdminRole::TYPE_GROUP); //部门对应的最大权限 角色组
                $default_role_id = $department_role['id'];
                $can_edit_depadmin_role = true;
            }
        }
        $actions = $actions ? $actions : AdminRole2action::model()->getActionByRole($default_role_id);

        $department_name = AdminDepartment::model()->getDepName($dep_id);

        $role_now_action_info = AdminRole2action::model()->getActionByRole($id);
        //$role_now_action = array_keys($role_now_action_info);
        $role_now_action = array();
        if($role_now_action_info ){
            foreach($role_now_action_info as $v){
                foreach($v as  $vs){
                    $role_now_action[] = $vs['id'];
                }
            }

        }
        //print_r($role_now_action);die;

        //$mod->setAttribute('department_id',$dep_id);


        //$action_list = AdminUser2role::model()->getactionById($user_id);

        //print_r($my_group_mods_list);
        $this->controller->layout = '//layouts/main_no_nav';

        $this->controller->render('role_copy', array (
                'model'=>$mod,
                'action_info'=>$actions,
                'dep_name' => $department_name,
                'currentUserInfo'=>$currentUserInfo,
                'role_now_action' =>$role_now_action,
                'can_edit' =>$can_edit_depadmin_role,
            )
        );
//        echo $this->controller->render('action_info_user', array ('info'=>$action_list));
    }
}