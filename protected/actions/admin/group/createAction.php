<?php
/**
 * 创建小组 action
 * @author duke 2014-06-18
 */
class CreateAction extends CAction
{

    public function run()
    {
        if(!isset($_GET['dep_id']) || !$_GET['dep_id']){
            $this->controller->alertWindow('缺少必须参数');
        }

        $model = new AdminDepartment();

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        if ( !empty($_POST) && isset($_POST['AdminDepartment']) ) {

            $data = $_POST['AdminDepartment'];

            $model->attributes = $data;
            //$model->parent_id = $_POST['dep_id'];

            $model->create_time = date("Y-m-d H:i:s");
            //print_r($model->attributes);

            if(  $model->save() ){
                //创建一个空得组管理员角色组
                $dep_id = $model->primaryKey;
                $role_name = $data['name'].'管理员角色组';
                $role_arr = array(
                    'department_id' => $dep_id,
                    'name'=>$role_name,
                    'type'=>AdminRole::TYPE_GROUP,
                    'status'=>AdminRole::STATUS_NORMAL,
                    'create_time'=>date('Y-m-d H:i:s')
                );
                $role_id = AdminRole::model()->createRole($role_arr);

                //保存角色
                if($role_id){
                    $role_new = array();
                    if(isset($_POST['AdminRole']['role'])){
                        $role_new = $_POST['AdminRole']['role'];
                        unset($_POST['AdminRole']['role']);
                    }
                    if(!empty($role_new)){
                        $role_id = $role_id;
                        $status = ($data['status'] == AdminDepartment::STATUS_NORMAL) ? AdminRole2action::STATUS_NORMAL:AdminRole2action::STATUS_FORBIDEN;
                        $role2action_mod = AdminRole2action::model()->saveRole2Action($role_id,$role_new,$status);
                    }
                }
                if (!empty($_GET['dialog'])) {
                    echo CHtml::script("window.parent.$('#dep_dialog').dialog('close');window.parent.$('cru-frame').attr('src','');window.parent.$.fn.yiiGridView.update('{$_GET['grid_id']}');");
                    Yii::app()->end();
                }
            }
        }
        $dep_id = $_GET['dep_id'];

        $dep_info = AdminDepartment::model()->getInfoByid($dep_id);
        if($dep_info['parent_id']){
            $type = AdminRole::TYPE_GROUP;
        }else $type = AdminRole::TYPE_DEPART;
        //print_r($dep_info);echo 'bbbb';
        $mod = AdminRole::model();
        $department_role = $mod->getDepartmentRoles($dep_id,$type); //部门对应的最大权限 角色组
        //var_dump($department_role);die;
        $default_role_id = $department_role['id'];
        //echo $default_role_id;
        $actions = AdminRole2action::model()->getActionByRole($default_role_id);

        $this->controller->layout = '//layouts/main_no_nav';
        echo $this->controller->render('group_form',
            array (
                'model'=>$model,
                'action_info'=>$actions,
                'can_edit' =>true,
            ));
    }
}