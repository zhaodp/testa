<?php
/**
 * 应用系统更新
 * @author like 2015-05-19
 */
class updateAction extends CAction
{

    public function run($id)
    {

        $model = AdminApp::model()->findByPk($id);

        if ( !empty($_POST) && isset($_POST['AdminApp']) ) {
            $data = $_POST['AdminApp'];
            $model->attributes = $data;

            if( $model->validate() && $model->save() ){
                //更改权限，清空所有用户缓存
                Menu::model()->removeAllCache();
                if (!empty($_GET['dialog'])) {
                    echo CHtml::script("window.parent.$('#define_dialog').dialog('close');window.parent.$('cru-frame').attr('src','');window.parent.$.fn.yiiGridView.update('{$_GET['grid_id']}');");
                    Yii::app()->end();
                }
            }
        }

        $this->controller->layout = '//layouts/main_no_nav';
        echo $this->controller->render('app_form', array ('model'=>$model));

    }
}
?>
