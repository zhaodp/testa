<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-12-9
 * Time: 下午3:23
 * auther mengtianxue
 */
Yii::import("application.controllers.FinanceController");
Yii::import("application.models.customer.BReportDailyVipReport");
Yii::import('application.models.schema.report.ReportFsVipRp');

class VipMonthListAction extends CAction
{
    public function run()
    {
        $date_time = isset($_GET['date_time']) ? $_GET['date_time'] : date('Y');
        $data = BReportDailyVipReport::model()->getMonthDailyList($date_time);
        $this->controller->render('report/vip_month_list',
            array('data' => $data)
        );
    }
}