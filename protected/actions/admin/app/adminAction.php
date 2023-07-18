<?php
/**
 * 应用系统Action
 * @author like 2015-05-19
 */
class adminAction extends CAction
{

    public function run()
    {
        $model =  AdminApp::model();
        $model->unsetAttributes();
        $search_params = isset($_GET['AdminActions']) ? array_filter($_GET['AdminActions']) : '';
        if ($search_params) {
		$model->attributes = $search_params;
	}

	$this->controller->render('app_admin', array ('model'=>$model));
    }
}
