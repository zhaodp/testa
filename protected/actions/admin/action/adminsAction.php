<?php
/**
 * 用户组管理（角色管理）的添加动作Action
 * @author sunhongjing 2013-03-01
 */
class adminsAction extends CAction
{

    public function run()
    {
        $model =  AdminActions::model();

        $model->unsetAttributes(); // clear any default values
        $search_params = isset($_GET['AdminActions']) ? array_filter($_GET['AdminActions']) : '';
        if ($search_params) {
		$model->attributes = $search_params;
	}

        $this->controller->render('action_admin', array (
        'model'=>$model));
    }
}
