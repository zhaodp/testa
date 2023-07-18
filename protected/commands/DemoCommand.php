<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 15/1/12
 * Time: 11:22
 */
Yii::import('application.models.schema.customer.*');
class DemoCommand extends LoggerExtCommand{

    public function actionT($phone){
        $params =
            array('open_id'=>0,'phone'=>$phone,'act_name'=>'weixin_59');

        QueueProcess::model()->weixin39_binding_bonus($params);
    }

    public function actionDetail($appkey){
        echo serialize(RApiKey::model()->key($appkey));
        RApiKey::model()->reloadKeys();
    }

    public function actionBind($phone, $bonusSn){
        $ret = (FinanceWrapper::bindBonusBySn($phone, $bonusSn));
        echo serialize($ret);
    }

    public function actionConfig(){
        $ret = FinanceUtils::getModifyFeeConfig(2, 1, null);
        print_r($ret);
    }

    public function actionTT(){
        $criteria = new CDbCriteria();
        $criteria->addBewteenCondition('created', strtotime('-1 day'), time());
        $criteria->compare('status', 1);
        $criteria->compare('source', 30);
        $criteria->commare('city_id', 1);
        $criteria->addContidon('distance > 30');
        $orderList = Order::model()->findAll($criteria);
        foreach($orderList as $order ){
            $orderId = $order['order_id'];
            $list = $this->getEmployeeAccountList($orderId);
            if(empty($list)){
                echo $orderId.'----'.$order['distance'];
                echo "\n";
            }
        }

    }

    private function getEmployeeAccountList($orderId){
        $tableName = '201501';
        EmployeeAccount::$table_name = $tableName;
        $criteria = new CDbCriteria();
        $criteria->compare('channel', 58);
        $criteria->compare('order_id', $orderId);
        return EmployeeAccount::model()->findAll($criteria);

    }


    public function actionOrderList($driverId){

        $order = array();
        $order['driver_id'] = $driverId;
        $order['status'] = 0;
        $order['pageSize'] = 10;
        $order['offset'] = 0;
        $orderList = Order::model()->getDriverOrderListByType($order);
        print_r($orderList);
    }

    public function actionModifySettle($orderId){
        OrderSettlement::model()->modifyFeeSettle($orderId);
    }

    public function actionMockOrderSnapshoot($orderId){
        $orderSnapshoot = OrderSnapshoot::model()->getLatestSnapshoot($orderId);
        if(!$orderSnapshoot){
            $orderSnapshoot = new OrderSnapshoot();
        }
        $orderSnapshoot['order_id'] = $orderId;
        $tmp = array();
        $tmp['modify_fee'] = array(
            'type'  => 1,
            'name' => 'tuan test',
            'amount' => 1,
        );
        $orderSnapshoot['meta'] = json_encode($tmp);
        $orderSnapshoot->save();
    }

    /**
     * 展示用来测试类里面定义的方法
     */
    public function actionClassTest($driverId){
        $timeStart = date("Y-m-d 07:00:00",strtotime("-1 day"));
        $timeEnd   = date("Y-m-d 07:00:00");
        $hour = date('H');
        if($hour >= 7){
            $timeStart = $timeEnd;
            $timeEnd   = date('Y-m-d H:i:s');
        }else{

        }
        $newRet = FinanceCastHelper::getDriverIncome($driverId, strtotime($timeStart), strtotime($timeEnd));
        echo json_encode($newRet);
        echo "\n";
    }

    public function actionCustomerSettlement($phone, $cast){
        $ret = FinanceWrapper::chargeUser($phone, $cast, '众筹微信', 'xxxxx');
        if($ret){
            echo "succ \n";
        }else{
            echo "fail \n";

        }
    }

    /**
     *
     *
     * @param $phone
     * @param $views
     * php yiic Demo ViewTest --views=open.coupon.activityBind
     */
    public function actionViewTest($views)
    {
        $params = array(
            'phone' => '15210268972',
            'appkey'  => '10000001',
            'acount' => 111,
            'code' => 659
        );
        $this->rendorView($views, $params);
    }



    private function rendorView($views, $params){
        $action = str_replace('.', '/', $views);
        require_once('/sp_edaijia/www/v2/themes/api/views/'.$action.'.php');
        echo "\n";
    }
}