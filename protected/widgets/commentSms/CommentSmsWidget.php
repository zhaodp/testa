<?php

/**
 * 客户信息（可根据订单id 或 客户手机 搜索）
 * @author  liuxiaobo
 */
class CommentSmsWidget extends CWidget {

    public $phone;          //客户手机
    public $orderId;          //订单id

    public function run() {
        $phone = $this->phone;
        $orderId = $this->orderId;

        $order = CommentSms::model();
        $criteria = new CDbCriteria();
        if($phone){
            $criteria->addCondition('sender = :phone');
            $criteria->params[':phone'] = $phone;
        }
        if($orderId){
            $criteria->addCondition('order_id = :order_id');
            $criteria->params[':order_id'] = $orderId;
        }
        $criteria->order = 'id desc';
        $orders = new CActiveDataProvider($order, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 5
            )
        ));
//        print_r($orders);
        $this->render('info', array(
            'data' => $orders,
        ));
    }

    public function getDriverUser($data) {
        $p_url = $statusStr = '';
        $p_url = Yii::app()->createUrl('driver/archives', array('id' => $data->driver_id));
        $statusStr.='<a title="查看司机信息" href="' . $p_url . '" target="_blank" style="margin-bottom:10px;" >' . $data->driver_id . '</a><br/>';

        $driverUser = Driver::model()->getProfile($data->driver_id);
        $statusStr.=$driverUser ? CHtml::link($driverUser->name, array("commentSms/admin", "driver_id" => $driverUser->user), array('target'=>'_blank')) : "";
        return $statusStr;
    }

    public function getDriverCityId($data) {
        $driverUser = Driver::model()->getProfile($data->driver_id);
        return $driverUser ? Dict::item("city", $driverUser->city_id) : "";
    }

    public function getOrderStatus($data) {
        $str = '';
        if (is_object($data)) {
            switch ($data->order_status) {
                case 1:
                case 4:
                    $str = '报单';
                    break;
                case 2:
                case 3:
                    $str = '销单';
                    break;
            }
        }

        return $str;
    }

}
