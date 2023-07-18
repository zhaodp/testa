<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 14-1-9
 * Time: 下午5:32
 * auther mengtianxue
 */
Yii::import("application.controllers.CustomersController");

class UpdateStatusAction extends CAction
{
    public function run()
    {
        EdjLog::info(serialize($_REQUEST));
	$id=$_REQUEST['id'];
        $order_id=$_REQUEST['order_id'];
        $order=CarCustomerTrans::model()->findByPk($id);
        if(!empty($order)&&$order_id==$order->trans_order_id){
            $order->invoiced=1;
            if($order->update()){
                echo json_encode(array('success'=>1,'msg'=>'已开发票','order_id'=>$order_id));
            }
            else{
                EdjLog::info("order id $order_id update invoice status fail");
                echo json_encode(array('success'=>0,'msg'=>'未开发票，标记已开','order_id'=>$order_id));
            }
        }
        else{
            EdjLog::info("order id $order_id not exists");
            echo json_encode(array('success'=>0,'msg'=>'未开发票，标记已开','order_id'=>$order_id));
        }
        Yii::app()->end();
    }
} 
