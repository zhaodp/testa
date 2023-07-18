<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 14-1-9
 * Time: 下午6:04
 * auther mengtianxue
 */
class TradeAction extends CAction {

    public function run(){
        $model = new CarCustomerTrans();
        $params = array();
        if (isset($_GET['CarCustomerTrans'])) {
            $params = $_GET['CarCustomerTrans'];
            $model->attributes = $params;
        }
        $dataProvider = BCustomers::model()->getCustomerTradeList($params);
        $this->controller->render('user/trade',array(
            'model' => $model,
            'dataProvider' => $dataProvider
        ));
        
    }
    
} 