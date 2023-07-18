<?php
/**
 * 账单详情
 * User: mtx
 * Date: 13-12-4
 * Time: 下午5:25
 * auther mengtianxue
 */

Yii::import("application.controllers.FinanceController");
Yii::import("application.models.customer.BReportDailyVipReport");
Yii::import("application.models.schema.report.ReportFsVipTradeInfo");

class VipInfoAction extends CAction
{

    public function run()
    {
        $params = isset($_GET['ReportFsVipTradeInfo']) ? $_GET['ReportFsVipTradeInfo'] : array();
        $params['channel'] = empty($params['channel']) ? 0 : $params['channel'];
        $params['city_id'] =  empty($params['city_id']) ? '' : $params['city_id'];
        $params['operator'] = empty($params['operator']) ? '' : trim($params['operator']);
        $params['daily_date'] = empty($params['daily_date']) ? '' : $params['daily_date'];

        //实例化
        $model = new ReportFsVipTradeInfo();

        //获取数据
        $data = BReportDailyVipReport::model()->getAccountBill($params);

        //获取司机的总额
        $accountCount = BReportDailyVipReport::model()->getAccountCount($params);

        $this->controller->render('report/vip_info',
            array('model' => $model,
                'data' => $data,
                'accountCount' => $accountCount,
                'params' => $params
            )
        );
    }

} 