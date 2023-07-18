<?php
Yii::import("application.controllers.CustomersController");

class TransAction extends CAction
{
    public function run()
    {

        $params = array();
        $customer_id='';
        if($_GET){
            $getData=$_GET;
            $phone =isset($_GET['phone'])?htmlspecialchars(trim($_GET['phone'])):'';
            $params['trans_type']=isset($_GET['trans_type'])?trim($_GET['trans_type']):'';
            $params['trans_order_id']=isset($_GET['trans_order_id'])?htmlspecialchars(trim($_GET['trans_order_id'])):'';
            if(isset($_GET['start_time']) && !empty($_GET['start_time']))
                $params['start_time']=trim($_GET['start_time']);
            if(isset($_GET['end_time']) && !empty($_GET['start_time']))
                $params['end_time']=trim($_GET['end_time']);

            if(!empty($phone)){
                $customer = BCustomers::model()->getCustomerInfo(array('phone'=>$phone));
                $customer_id='--';
                if(isset($customer["data"]) && !empty($customer["data"])){
                    $customer_id=$customer["data"]->id;
                }
                $params['user_id'] = $customer_id;
            }
        }


        if(!isset($params['start_time']))
            $params['start_time']=$getData['start_time']=date('Y-m',time()).'-01';
        if(!isset($params['end_time']))
            $params['end_time']=$getData['end_time']=date('Y-m-d',time());

        $dataProvider = BCustomers::model()->getCustomerTradeList($params);
        $statisticsStr=BCustomers::model()->getUserTransStatistics($params['start_time'],$params['end_time'],$customer_id);

        $this->controller->render('user/trans_list',
            array(
                'model' => $getData,
                'dataProvider' => $dataProvider,
                'statistics'=>$statisticsStr
            ));
    }
}







