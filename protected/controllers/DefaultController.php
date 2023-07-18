<?php

class DefaultController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column_frame';

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView()
    {
	$appv2 = AdminApp::model()->findByPk(2);
	$url = $appv2->url . '/index.php?r=account/summary';
	Yii::app()->request->redirect($url, true);
	//新的页面
        $this->render('default_view', array(
        ));
    }

    public function actionTop(){
	$this->layout = '//layouts/column_frame_top';
        $this->render('default_view_top', array(
        ));
    }

    public function actionLeft(){
	$this->layout = '//layouts/column_frame_left';
        $this->render('default_view_left', array(
        ));
    }
}
