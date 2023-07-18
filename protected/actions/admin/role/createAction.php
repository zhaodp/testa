<?php
/**
 * 用户角色管理 -user_id
 * @author duke 2014-06-17
 */
class createAction extends EAction
{

    public function run()
    {

        if((!isset($_GET['dep_id']) || !$_GET['dep_id']) && !isset($_POST['AdminRole']['department_id'])){
            throw new CHttpException(404,'缺少必须参数');
        }

        //得到当前登录用户信息
        $currentUserInfo =  Yii::app()->user->getCurrentUserInfo();


        $dep_id = $_GET['dep_id'];

        //print_r($actions);die;

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
        $dep_info = AdminDepartment::model()->getInfoByid($dep_id);
        if($dep_info['parent_id']){
            $type = AdminRole::TYPE_GROUP;
        }else $type = AdminRole::TYPE_DEPART;
        //print_r($dep_info);echo 'bbbb';
        $mod = AdminRole::model();
        $department_role = $mod->getDepartmentRoles($dep_id,$type); //部门对应的最大权限 角色组
        //var_dump($department_role);die;
        $default_role_id = $department_role['id'];
        $actions = AdminRole2action::model()->getActionByRole($default_role_id);


        $department_name = AdminDepartment::model()->getDepName($dep_id);

        //print_r($new_action);die;

        //$mod->setAttribute('department_id',$dep_id);


        //$action_list = AdminUser2role::model()->getactionById($user_id);

        //print_r($my_group_mods_list);
        $this->controller->layout = '//layouts/main_no_nav';

        $this->controller->render('role_create', array (
                'model'=>$mod,
                'action_info'=>$actions,
                'dep_name' => $department_name,
                'currentUserInfo'=>$currentUserInfo,
                'can_edit'=> true,
            )
        );
//        echo $this->controller->render('action_info_user', array ('info'=>$action_list));
    }
}