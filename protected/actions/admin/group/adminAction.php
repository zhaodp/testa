<?php
/**
 * 小组管理
 * @author duke 2014-06-20
 */
class adminAction extends EAction
{

    public function run()
    {
        $model =  AdminDepartment::model();
        $user_level = Yii::app()->user->admin_level;

        $dep_name = '';
        if($user_level == AdminUserNew::LEVEL_ADMIN || $user_level == AdminUserNew::LEVEL_DEPARTMENT_ADMIN){
            if($user_level == AdminUserNew::LEVEL_DEPARTMENT_ADMIN && Yii::app()->user->department != $_REQUEST['dep_id']){
                $this->alertWindow('没有权限');
            }
            //$show_edit_button = true;
            $need_dep_id = true;
            if($_REQUEST['dep_id']){
                $dep_name = AdminDepartment::model()->getInfoByid($_REQUEST['dep_id']);
                $dep_name = isset($dep_name['name']) ? $dep_name['name'] : '';
                $dep_id = $_REQUEST['dep_id'];
            }
        }
        else if($user_level == AdminUserNew::LEVEL_GROUP_ADMIN){
            $need_dep_id = false;
            $dep_id = Yii::app()->user->department;
            $dep_name = AdminDepartment::model()->getInfoByid($dep_id);
            $dep_name = isset($dep_name['name']) ? $dep_name['name'] : '';

        }
        if($need_dep_id && (!isset($_REQUEST['dep_id']) || !$_REQUEST['dep_id'])){
            $this->alertWindow('缺少参数 dep_id');
        }
        $dep_info = AdminDepartment::model()->findByPk($dep_id);
        if($dep_info->parent_id != 0){
            $this->alertWindow('小组内没有小组了。');
        }

        $model->unsetAttributes(); // clear any default values
        $search_params = isset($_GET['AdminDepartment']) ? array_filter($_GET['AdminDepartment']) : '';
        if (($search_params))
            $model->attributes = $search_params;

        $model->setAttribute('parent_id',$dep_id);
//print_r($model);die;
        $this->controller->render('group_admin',
            array (
            'model'=>$model,
            'dep_id'=>$dep_id,
            'dep_name'=>$dep_name)
        );
    }
}