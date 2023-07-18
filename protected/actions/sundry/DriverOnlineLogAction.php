<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-12-29
 * Time: 下午11:45
 * auther mengtianxue
 */
Yii::import("application.controllers.SundryController");
Yii::import('application.models.schema.system.sundry.DriverOnlineLog');
class DriverOnlineLogAction extends CAction
{

    public function run()
    {
        $model = new DriverOnlineLog('search');
        $model->unsetAttributes();
        if (isset($_GET['DriverOnlineLog'])) {
            $model->attributes = $_GET['DriverOnlineLog'];
        }

        $this->controller->render('driver_online_log',
            array('model' => $model)
        );
    }

}