<?php
/**
 * 修改部门信息
 * @author duke 2014-06-20
 */
class updateAction extends EAction
{

    public function run($id)
    {

        $model = AdminDepartment::model()->findByPk($id);
        $group_info = AdminRole::model()->getDepartmentRoles($id,AdminRole::TYPE_GROUP);
        if($group_info){
            $id_tmp = $group_info['id'];
        }
        else $id_tmp = '';
        $currentUserInfo =  Yii::app()->user->getCurrentUserInfo();
        if ( !empty($_POST) && isset($_POST['AdminDepartment']) ) {

            $data = $_POST['AdminDepartment'];
            //只有部门内的角色组都禁用了才可以禁用部门
            if($data['status'] == AdminDepartment::STATUS_FORBIDEN){
                $role_info = AdminRole::model()->getRolesByDepid($id);
                if(count($role_info) > 1){
                    $this->alertWindow('请先禁用小组内的角色组，然后才能禁用当前小组');
                }
            }

            $role_new = array();
            //print_r($_POST['AdminRole']['role']);die;
            if(isset($_POST['AdminRole']['role'])){
                $role_new = $_POST['AdminRole']['role'];
                unset($_POST['AdminRole']['role']);
            }

            $model->attributes = $data;
//print_r($data);die;

            //print_r($model->attributes);
            if( $model->save() ) {
                echo $currentUserInfo['admin_level'].'----'.$group_info['type'];
                if($currentUserInfo['admin_level'] != AdminUserNew::LEVEL_GROUP_ADMIN ){

                    $role_id = $id_tmp;
                    $status = ($data['status'] == AdminDepartment::STATUS_NORMAL ) ? AdminRole2action::STATUS_NORMAL : AdminRole2action::STATUS_FORBIDEN;
                    $role2action_mod = AdminRole2action::model()->saveRole2Action($role_id,$role_new,$status);
                }

                if (!empty($_GET['dialog'])) {
                    echo CHtml::script("window.parent.$('#dep_dialog').dialog('close');window.parent.$('cru-frame').attr('src','');window.parent.$.fn.yiiGridView.update('{$_GET['grid_id']}');");
                    Yii::app()->end();
                }
                //更改权限，清空所有用户缓存
                Menu::model()->removeAllCache();
            }
        }

        $actions = array();
        $role_now_action = array();
        $can_edit_depadmin_role = false;
        //得到当前登录用户信息

        //$currentUserInfo['admin_level'] = 1;

        //echo $id_tmp;die;
        if($id_tmp){
            $mod = AdminRole::model()->findByPk($id_tmp);
            if($currentUserInfo['admin_level'] == AdminUserNew::LEVEL_ADMIN ){
                    if($mod->type == AdminRole::TYPE_GROUP){
                        //echo 'aaaa';die;
                        $dep_info = AdminDepartment::model()->getInfoByid($id);
                        //print_r($dep_info);die;
                        $parent_dep_id = $dep_info['parent_id'];
                        $parent_dep_role_info  = $mod->getDepartmentRoles($parent_dep_id); //部门对应的最大权限 角色组
                        $default_role_id = $parent_dep_role_info['id'];
                    }
                $can_edit_depadmin_role = true;
            }
            else if($currentUserInfo['admin_level'] == AdminUserNew::LEVEL_DEPARTMENT_ADMIN){

                    if($mod->type == AdminRole::TYPE_GROUP){
                        $dep_info = AdminDepartment::model()->getInfoByid($id);
                        $parent_dep_id = $dep_info['parent_id'];
                        $parent_dep_role_info  = $mod->getDepartmentRoles($parent_dep_id); //部门对应的最大权限 角色组
                        $default_role_id = $parent_dep_role_info['id'];
                        $can_edit_depadmin_role = true;
                    }
            }
            $_GET['dep_id'] = $parent_dep_id;
            //echo $default_role_id;//die;
            $actions = $actions ? $actions : AdminRole2action::model()->getActionByRole($default_role_id);

            //print_r($actions);die;


            //$department_name = AdminDepartment::model()->getDepName($id);

            $role_now_action_info = AdminRole2action::model()->getActionByRole($id_tmp);
            //$role_now_action = array_keys($role_now_action_info);

            if($role_now_action_info ){
                foreach($role_now_action_info as $v){
                    foreach($v as  $vs){
                        $role_now_action[] = $vs['id'];
                    }
                }

            }
        }else{
            $dep_info = AdminDepartment::model()->getInfoByid($id);
            $_GET['dep_id'] = $dep_info['parent_id'];
        }
        //echo $id;
        //print_r($role_now_action);



        $this->controller->layout = '//layouts/main_no_nav';
        echo $this->controller->render('group_form',
            array (
                'model'=>$model,
                'role_now_action'=>$role_now_action,
                'action_info'=>$actions,
                'currentUserInfo'=>$currentUserInfo,
                'can_edit'=>$can_edit_depadmin_role,

            ));

    }


}
?>