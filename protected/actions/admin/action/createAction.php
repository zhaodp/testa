<?php
/**
 * 创建权限 action
 * @author duke 2014-06-18
 */
class CreateAction extends CAction
{

    public function run()
    {
        $model = new AdminActions();

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        if ( !empty($_POST) && isset($_POST['AdminActions']) ) {
//print_r($_POST);die;
            $dep_id = isset($_POST['AdminActions']['dep_id']) ? $_POST['AdminActions']['dep_id'] : 0;
            unset($_POST['AdminActions']['dep_id']);
            $data = $_POST['AdminActions'];

            $model->attributes = $data;

            $model->create_time = date("Y-m-d H:i:s");
            //print_r($model->attributes);

            if( $model->validate() && $model->save() ){
                $action_id = $model->primaryKey;
                $dep_id && AdminRole2action::model()->saveInfoByDep($action_id,$dep_id,$_POST['AdminActions']['status']);
                if (!empty($_GET['dialog'])) {
                    echo CHtml::script("window.parent.$('#define_dialog').dialog('close');window.parent.$('cru-frame').attr('src','');window.parent.$.fn.yiiGridView.update('{$_GET['grid_id']}');");
                    Yii::app()->end();
                }
            }
        }

        $apps = AdminApp::model()->getAll();
        $department = AdminDepartment::model()->getDep2Role();


        $this->controller->layout = '//layouts/main_no_nav';
        echo $this->controller->render('action_form', array ('model'=>$model,'apps'=>$apps,'department'=>$department));
    }
}
