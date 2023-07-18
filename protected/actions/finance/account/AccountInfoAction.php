<?php
/**
 * 账单详情
 * User: mtx
 * Date: 13-12-4
 * Time: 下午5:25
 * auther mengtianxue
 */

Yii::import("application.controllers.FinanceController");
Yii::import("application.models.driver.BAccount");

class AccountInfoAction extends CAction
{

    public function run()
    {

        $params = isset($_GET['CarEmployeeAccount']) ? $_GET['CarEmployeeAccount'] : array();
        $params['month'] = empty($params['month']) ? date('Ym') : trim($params['month']);
        $params['channel'] = empty($params['channel']) ? 1 : $params['channel'];
        $params['city_id'] =  empty($params['city_id']) ? '' : $params['city_id'];
        $params['operator'] = empty($params['operator']) ? '' : trim($params['operator']);
        $params['created'] = empty($params['created']) ? '' : $params['created'];

        //实例化
        $model = BAccount::model()->tableInstantiate($params['month']);

        $model->attributes = $params;
        //获取数据
        $data = BAccount::model()->getAccountBill($params);

        //获取司机的总额
        $accountCount = BAccount::model()->getAccountCount($params);

        $this->controller->render('account/account_info',
            array('model' => $model,
                'data' => $data,
                'accountCount' => $accountCount,
                'params' => $params
            )
        );
    }

} 