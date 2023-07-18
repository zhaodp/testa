<?php
/**
 * 创建部门 action
 * @author duke 2014-06-18
 */
class CreateAction extends CAction
{

    public function run()
    {
        if(Yii::app()->user->admin_level != AdminUserNew::LEVEL_ADMIN){
            $this->alertWindow('没有权限');
        }
        $model = new AdminDepartment();

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        if ( !empty($_POST) && isset($_POST['AdminDepartment']) ) {

            $data = $_POST['AdminDepartment'];

            $model->attributes = $data;

            $model->create_time = date("Y-m-d H:i:s");
            //print_r($model->attributes);

            if(  $model->save() ){
                //创建一个空得管理严角色组
                $dep_id = $model->primaryKey;
                $role_name = $data['name'].'管理员角色组';
                $role_arr = array(
                    'department_id' => $dep_id,
                    'name'=>$role_name,
                    'type'=>AdminRole::TYPE_DEPART,
                    'status'=>AdminRole::STATUS_NORMAL,
                    'create_time'=>date('Y-m-d H:i:s')
                );
                $role_id = AdminRole::model()->createRole($role_arr);

                if (!empty($_GET['dialog'])) {
                    echo CHtml::script("window.parent.$('#dep_dialog').dialog('close');window.parent.$('cru-frame').attr('src','');window.parent.$.fn.yiiGridView.update('{$_GET['grid_id']}');");
                    Yii::app()->end();
                }
            }
        }

        $this->controller->layout = '//layouts/main_no_nav';
        echo $this->controller->render('dep_form', array ('model'=>$model));
    }
}