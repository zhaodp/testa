<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-12-4
 * Time: 下午5:23
 * auther mengtianxue
 */
Yii::import("application.controllers.FinanceController");
Yii::import("application.models.driver.BDailyAccountReport");
Yii::import('application.models.schema.report.ReportFsAccountRp');

class DailyAccountAction extends CAction
{

    public function run()
    {
        $model = new ReportFsAccountRp();

        $params = isset($_GET['ReportFsAccountRp']) ? $_GET['ReportFsAccountRp'] : array();

        $params['account_date'] = empty($params['account_date']) ? date('Y-m-d') : $params['account_date'];
        $params['city_id'] = empty($params['city_id']) ? 0 : $params['city_id'];

        $model->attributes = $params;
        //获取当前数据
        $data = BDailyAccountReport::model()->getDailyAccountInfo($params['account_date'], $params['city_id']);

        $this->controller->render('report/daily_account',
            array('model' => $model,
                'data' => $data,
                'params' => $params
            )
        );

    }

} 