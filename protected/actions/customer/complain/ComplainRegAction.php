<?php
/**
 * 定位订单
 * User: Bidong
 * Date: 13-6-19
 * Time: 下午1:39
 * To change this template use File | Settings | File Templates.
 */

class ComplainRegAction extends CAction{
    public function run(){

        if($_GET){
            if(isset($_GET['cid'])){
                $complain_id=$_GET['cid'];
                $model=CustomerComplain::model()->findByPk($complain_id);
                if($model){
                    $order_id=$_GET['oid'];
                    $orderModel=Order::model()->findByPk($order_id);
                    if($orderModel){
                        $model->driver_id=$orderModel->driver_id;
                        $model->driver_phone=$orderModel->driver_phone;
                        $model->service_time=date('Y-m-d H:i:s',$orderModel->booking_time);
                        $model->order_id=$order_id;
                        $model->city_id = DriverStatus::model()->getItem($orderModel->driver_id,'city_id');
                        // $model->update_time=date('Y-m-d H:i:s',time());
                        $model->sp_process=CustomerComplain::SP_PROCESS_O;   //品监已定位订单

                        if($model->save()){
                            $this->controller->redirect($_GET['re']);
                        }else{
                            $msg=$model->getErrors();
                            Yii::app()->clientScript->registerScript('alert', 'alert("'.$msg.'");');
                        }
                    }

                }else{
                    $this->controller->redirect(Yii::app()->createUrl($_GET['re']));
                }
            }else{
                $this->controller->redirect(Yii::app()->createUrl($_GET['re']));
            }

        }



    }
}