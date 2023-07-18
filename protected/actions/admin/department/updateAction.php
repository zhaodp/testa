<?php
/**
 * 修改部门信息
 * @author duke 2014-06-20
 */
class updateAction extends EAction
{

    public function run($id)
    {
        if(Yii::app()->user->admin_level != AdminUserNew::LEVEL_ADMIN){
            $this->alertWindow('没有权限');
        }
        $model = AdminDepartment::model()->findByPk($id);

        if ( !empty($_POST) && isset($_POST['AdminDepartment']) ) {

            $data = $_POST['AdminDepartment'];
            //只有部门内的角色组都禁用了才可以禁用部门
            if($data['status'] == AdminDepartment::STATUS_FORBIDEN){
                $role_info = AdminRole::model()->getRolesByDepid($id);
                if(!empty($role_info)){
                    $this->alertWindow('请先禁用部门内的角色组，然后才能禁用部门');
                }
            }
            $change_name = false;
            if($data['name'] != $model->name){
                $change_name = true;
                $role_name_new = $data['name'].'管理员角色组';
            }

            $model->attributes = $data;
//print_r($data);die;

            //print_r($model->attributes);
            if( $model->save() ){
                if($change_name)
                $res = AdminRole::model()->updateAll(
                    array('name'=>$role_name_new),
                    'department_id = :dep_id and type = :type',
                    array(':dep_id' => $id,':type'=>AdminRole::TYPE_DEPART
                    )
                );

                if (!empty($_GET['dialog'])) {
                    echo CHtml::script("window.parent.$('#dep_dialog').dialog('close');window.parent.$('cru-frame').attr('src','');window.parent.$.fn.yiiGridView.update('{$_GET['grid_id']}');");
                    Yii::app()->end();
                }
                //更改权限，清空所有用户缓存
                Menu::model()->removeAllCache();
            }
        }





        $this->controller->layout = '//layouts/main_no_nav';
        echo $this->controller->render('dep_form', array ('model'=>$model));

    }


}
?>