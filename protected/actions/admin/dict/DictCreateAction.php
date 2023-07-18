<?php
/**
 * Created by JetBrains PhpStorm.
 * author: mtx
 * Date: 13-9-2
 * Time: 下午5:48
 */
class DictCreateAction extends CAction
{

    public function run()
    {
        $this->controller->layout = '//layouts/main_no_nav';
        $Dict = new Dict;

        // Uncomment the following line if AJAX validation is needed
        //$this->performAjaxValidation($model);

        if (isset($_POST['Dict'])) {

            $Dict->attributes = $_POST['Dict'];

            if ($Dict->save()){
                $this->controller->redirect(array('adminuserNew/dict'));
            }
        }

        $this->controller->render('dict_create', array(
            'model' => $Dict,
        ));
    }
}