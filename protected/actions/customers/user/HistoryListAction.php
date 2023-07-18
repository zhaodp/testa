<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 14-2-20
 * Time: 上午11:12
 * auther mengtianxue
 */
Yii::import("application.controllers.CustomersController");

class HistoryListAction extends CAction
{
    public function run()
    {
        $id = $_GET['id'];
        $model = new CarCustomerMain();
        $params_main = array('id' => $id);
        $model->attributes = $params_main;
        $customer = BCustomers::model()->getCustomerInfo($params_main);

        $model = new CarCustomerAccount();
        $params = array('user_id' => $id);
        $dataProvider = BCustomers::model()->getCustomerTradeList($params);

        $this->controller->render('user/history_list',
            array(
                'model' => $model,
                'customer' => $customer['data'],
                'customer_account' => $customer['user_account'],
                'dataProvider' => $dataProvider,
            ));
    }
}