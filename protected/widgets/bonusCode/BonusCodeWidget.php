<?php

/**
 * 客户绑定优惠券信息
 * @author  liuxiaobo
 */
class BonusCodeWidget extends CWidget {

    public $phone;          //客户手机

    public function run() {
        $phone = $this->phone;

        $order = CustomerBonus::model();
        $criteria = new CDbCriteria();
        $criteria->addCondition('customer_phone = :phone');
        $criteria->params[':phone'] = $phone;
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
        $statusStr.=$driverUser ? CHtml::link($driverUser->name, array("commentSms/admin", "driver_id" => $driverUser->user)) : "";
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
