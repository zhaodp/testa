<?php
/**
 * 角色管理列表动作action
 * @author duke 2014-06-17
 */
class adminAction extends EAction
{

    public function run()
    {
        $user_level = Yii::app()->user->admin_level;
        //$user_level = 1;
        //echo Yii::app()->user->department;die;
        $show_edit_button = false;
        $dep_name = $parent_dep_info = $parent_name ='';
        $dep_mod = AdminDepartment::model();
        if($user_level == AdminUserNew::LEVEL_ADMIN ){
            $show_edit_button = true;
            $need_dep_id = true;
            if(isset($_REQUEST['dep_id']) && $_REQUEST['dep_id']){
                $dep_info = $dep_mod->getInfoByid($_REQUEST['dep_id']);
                $dep_name = isset($dep_info['name']) ? $dep_info['name'] : '';
                if($dep_info['parent_id'] != 0 && isset($_GET['parent_id']) && $dep_info['parent_id'] == $_GET['parent_id']){
                    $parent_dep_info = $dep_mod->getInfoByid($dep_info['parent_id']);
                }
                $dep_id = $_REQUEST['dep_id'];
            }
        }
        elseif($user_level == AdminUserNew::LEVEL_DEPARTMENT_ADMIN){
            $need_dep_id = true;
            if(isset($_REQUEST['dep_id']) && $_REQUEST['dep_id']){
                $dep_info = $dep_mod->getInfoByid($_REQUEST['dep_id']);
                $dep_name = isset($dep_info['name']) ? $dep_info['name'] : '';
                if( isset($_GET['parent_id']) ){
                    if($dep_info['parent_id'] != 0 && Yii::app()->user->department == $_GET['parent_id'] && $dep_info['parent_id'] == $_GET['parent_id']){
                        $parent_dep_info = $dep_mod->getInfoByid($dep_info['parent_id']);
                    }
                    else{
                        $this->alertWindow('小组信息不正确 不能访问。');
                    }
                }
                else{
                    $dep_id = Yii::app()->user->department ;
                }
                //$dep_id = $_REQUEST['dep_id'];
            }
        }
        else if($user_level == AdminUserNew::LEVEL_GROUP_ADMIN){
            $need_dep_id = true;
            $dep_info = $dep_mod->getInfoByid($_REQUEST['dep_id']);
            //print_r($dep_info);
            if($dep_info['parent_id'] != 0 && isset($_GET['parent_id']) && $dep_info['parent_id'] == $_GET['parent_id']){
                $parent_dep_info = $dep_mod->getInfoByid($_GET['parent_id']);
            }
            $user_info = AdminUserNew::model()->findByPk(Yii::app()->user->user_id);
            if(!$user_info->group_id) $this->alertWindow('您没有被指定小组，请联系部门管理员');
            $dep_id = $user_info->group_id;
            //print_r($parent_dep_info);
            $dep_name = isset($dep_info['name']) ? $dep_info['name'] : '';
        }
        if($need_dep_id && (!isset($_REQUEST['dep_id']) || !$_REQUEST['dep_id'])){
            $this->alertWindow('缺少必须参数');
        }
        $dep_id = isset($dep_id) ? $dep_id : $_REQUEST['dep_id'];

        $model = new AdminRole('search');
        $model->unsetAttributes(); // clear any default values
        //print_r($_GET);
        if (isset($_GET['AdminRole'])){
            $model->attributes = $_GET['AdminRole'];
        }
        $model->setAttribute('department_id',$dep_id);

        //查看是否部门的默认管理员角色组状态正常 不正常则不像是建立角色组按钮
        $type = $parent_dep_info ? AdminRole::TYPE_GROUP: AdminRole::TYPE_DEPART;
        $department_admin_role = AdminRole::model()->getDepartmentRoles($dep_id,$type);
        //var_dump($department_admin_role);
        $show_create_button = $department_admin_role ? true : false;
//print_r($parent_dep_info);


//var_dump($dep_name); $dep_name;

        $this->controller->render('role_admin',
            array (
                'model'=>$model,
                'dep_id'=>$dep_id,
                'show_create_button'=>$show_create_button,
                'show_edit_button'=> $show_edit_button,
                'dep_name'=>$dep_name,
                'parent_info'=> $parent_dep_info,
            )
        );
    }
}