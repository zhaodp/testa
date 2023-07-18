<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 14-4-10
 * Time: 上午10:22
 * auther mengtianxue
 */

Yii::import("application.controllers.ReportOrderController");
Yii::import('application.models.schema.report.ReportDailyOrderDriver');
class CancelAction extends CAction
{
    public function run()
    {
        $model = new ReportDailyOrderDriver();

        $driver_id = $_GET['driver_id'];
        $call_time = isset($_GET['start_time']) ? $_GET['start_time'] : 0;
        $booking_time = isset($_GET['end_time']) ? $_GET['end_time'] : 0;
        $order_info = Order::model()->getOrderSummary($driver_id, $call_time, $booking_time);

        $criteria = new CDbCriteria();
        $criteria->select = "*";
        $criteria->compare('driver_user', $driver_id);
        $criteria->addCondition("alert_level > 0");
        $criteria->addCondition("status > 0");

        if (!empty($call_time)) {
            $criteria->addCondition("booking_time >= :call_start_time");
            $criteria->params[':call_start_time'] = strtotime($call_time);
        }

        if (!empty($booking_time)) {
            $criteria->addCondition("booking_time <= :call_end_time");
            $criteria->params[':call_end_time'] = strtotime($booking_time);
        }
        
        $criteria->order = 'alert_level desc';
        $dataProvider = new CActiveDataProvider($model, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 100)));

        $this->controller->render('cancel', array(
            'dataProvider' => $dataProvider,
            'order_info' => $order_info
        ));
    }
}