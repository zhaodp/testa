<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bidong
 * Date: 13-6-15
 * Time: 下午9:06
 * To change this template use File | Settings | File Templates.
 */

class ComplainOrderAction extends CAction {

    public function  run(){

        $complainModel=new CustomerComplain();
        $orderModel = new Order('search');
        $orderModel->unsetAttributes();
        $criteria = new CDbCriteria();

        if($_GET){
            $attArr=Order::model()->attributeLabels();
            $params=array();
            foreach($_GET as $k=>$v){
                if(array_key_exists($k,$attArr) && !empty($v) && $k!='driver_id'){
                    $criteria->addCondition($k.'=:'.$k);
                    $params[':'.$k]=$v;
                    $complainModel->$k=$v;
                }
            }

            if(isset($_GET['service_time']) && $_GET['service_time']){
                $complainModel->service_time=trim($_GET['service_time']);
                $criteria->addCondition('booking_time >=:service_time');
                $params[':service_time'] =strtotime($complainModel->service_time);
            }
            if(isset($_GET['complain_id'])){
                $complainModel->id=$_GET['complain_id'];
            }
            $criteria->params=$params;

        }
        $orderModel= $orderModel->search($criteria);
        $this->controller->render('orderlist',array(
            'model'=>$complainModel,
            'ordermodel'=>$orderModel,
        ));
        Yii::app()->end();
    }



}