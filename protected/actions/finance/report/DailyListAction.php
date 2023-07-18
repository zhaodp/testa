<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-12-8
 * Time: 下午3:34
 * auther mengtianxue
 */
Yii::import("application.controllers.FinanceController");
Yii::import("application.models.driver.BDailyAccountReport");
Yii::import('application.models.schema.report.ReportFsAccountTag');

class DailyListAction extends CAction
{
    public function run()
    {
        $date_time = isset($_GET['date_time']) ? $_GET['date_time'] : date('Y-m');
        $data = BDailyAccountReport::model()->getDetailsList($date_time);
        $this->controller->render('report/daily_list',
            array('data' => $data,
            'date_time' => $date_time
            )
        );
    }
}