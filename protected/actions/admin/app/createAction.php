<?php
/**
 * 创建应用系统
 * @author like 2015-05-19
 */
class createAction extends CAction
{

    public function run()
    {
        $model = new AdminApp();

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        if ( !empty($_POST) && isset($_POST['AdminApp']) ) {
            $data = $_POST['AdminApp'];
            $model->attributes = $data;
	    $model->key = md5(time());
            $model->create_time = date("Y-m-d H:i:s");
            //print_r($model->attributes);

            if( $model->validate() && $model->save() ){
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
