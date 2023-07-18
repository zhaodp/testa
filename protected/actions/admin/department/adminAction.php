<?php
/**
 * 部门管理
 * @author duke 2014-06-20
 */
class adminAction extends EAction
{

    public function run()
    {
        if(Yii::app()->user->admin_level != AdminUserNew::LEVEL_ADMIN){
            $this->alertWindow('没有权限');
        }
        $model =  AdminDepartment::model();

        $model->unsetAttributes(); // clear any default values
        $search_params = isset($_GET['AdminDepartment']) ? array_filter($_GET['AdminDepartment']) : '';
        if (($search_params))
            $model->attributes = $search_params;

        $model->setAttribute('parent_id',AdminDepartment::IS_DEP)  ;
//print_r($model);die;
        $this->controller->render('dep_admin',
            array (
            'model'=>$model)
        );
    }
}