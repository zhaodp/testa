<?php
/**
 * 用户特殊权限管理-查看电话和优惠券权限
 * @author duke 2014-06-17
 */
class adminAction extends CAction
{

    public function run()
    {

        if(!isset($_GET['user_id']) || !$_GET['user_id'] ){
            throw new CHttpException(404,'缺少必须参数');
        }

        $user_id = $_GET['user_id'];

        $mod = AdminSpecialAuth::model()->find('user_id = '.$user_id);
        if(!$mod) $mod = new AdminSpecialAuth();
        if(isset($_POST['AdminSpecialAuth'])){

            $mod->attributes = $_POST['AdminSpecialAuth'];
            $mod->save();
            if (!empty($_GET['dialog'])) {
                echo CHtml::script("window.parent.$('#mydialog').dialog('close');window.parent.$('view_info_frame').attr('src','');");
                Yii::app()->end();
            }
        }
        $user_info = AdminUserNew::model()->findByPk($user_id);

        $this->controller->layout = '//layouts/main_no_nav';

        $this->controller->render('special_form', array (
                'model'=>$mod,
                'user_info'=>$user_info,
            )
        );
//        echo $this->controller->render('action_info_user', array ('info'=>$action_list));
    }
}