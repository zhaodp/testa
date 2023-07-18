<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-12-29
 * Time: 下午11:45
 * auther mengtianxue
 */
Yii::import("application.controllers.SundryController");
Yii::import('application.models.schema.system.sundry.CarSmsLog');
class SmsLogAction extends CAction
{

    public function run()
    {
        $model = new CarSmsLog('search');
        $model->unsetAttributes();
        if (isset($_GET['CarSmsLog'])) {
            $model->attributes = $_GET['CarSmsLog'];
        }

        $this->controller->render('sms_log',
            array('model' => $model)
        );
    }

} 