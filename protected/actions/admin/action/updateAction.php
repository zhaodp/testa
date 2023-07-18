<?php
/**
 * 用户组管理（角色管理）的添加动作Action
 * @author sunhongjing 2013-03-01
 */
class updateAction extends CAction
{

    public function run($id)
    {

        $model = AdminActions::model()->findByPk($id);

        if ( !empty($_POST) && isset($_POST['AdminActions']) ) {

            $dep_id = isset($_POST['AdminActions']['dep_id']) ? $_POST['AdminActions']['dep_id'] : array();
            unset($_POST['AdminActions']['dep_id']);
            $data = $_POST['AdminActions'];

            $model->attributes = $data;


            //print_r($model->attributes);
            if( $model->validate() && $model->save() ){
                $action_id = $id;
                AdminRole2action::model()->saveInfoByDep($action_id,$dep_id,$_POST['AdminActions']['status']);
                //更改权限，清空所有用户缓存
                Menu::model()->removeAllCache();
                if (!empty($_GET['dialog'])) {
                    echo CHtml::script("window.parent.$('#define_dialog').dialog('close');window.parent.$('cru-frame').attr('src','');window.parent.$.fn.yiiGridView.update('{$_GET['grid_id']}');");
                    Yii::app()->end();
                }
            }
        }



        $department = AdminDepartment::model()->getDep2Role();
        $alreday_had_dep = AdminRole2action::model()->getDepByAction($id);
        $alreday_had_dep = array_keys($alreday_had_dep);

        $this->controller->layout = '//layouts/main_no_nav';
        echo $this->controller->render('action_form', array ('model'=>$model,'department'=>$department,'alreday_had_dep'=>$alreday_had_dep));

    }
}
?>
