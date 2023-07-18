<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 14-2-17
 * Time: 下午2:13
 * auther mengtianxue
 */

Yii::import("application.controllers.CustomersController");
class InfoAction extends CAction
{
    public function run()
    {
        $id = '';
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = trim($_GET['id']);
        }

        $model = new CarCustomerMain();
        $params_main = array('id' => $id);
        $model->attributes = $params_main;
        $customer = BCustomers::model()->getCustomerInfo($params_main);

        $criteria = new CDbCriteria();
        $criteria->compare('user_id', $id);
        $criteria->order = 'call_time desc';

        $dataProvider =  new CActiveDataProvider('Order', array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 15)));


        $this->controller->render('user/info',
            array(
                'model' => $model,
                'customer' => $customer['data'],
                'customer_account' => $customer['user_account'],
                'dataProvider' => $dataProvider
            )
        );
    }

} 