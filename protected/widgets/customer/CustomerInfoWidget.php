<?php
/**
 * 客户信息
 * @author  liuxiaobo
 */

class CustomerInfoWidget extends CWidget
{
    public $phone;          //客户手机


    public function run()
    {
        $phone = $this->phone;
        $data = array(
            'phone'=>$phone,
            'orderCount'=>0,
            'orderPrice'=>0,
            'complainCount'=>0,
        );
        
        $model = CustomerMain::model()->findByAttributes(array('phone'=>$phone));
        if($model){
            $data['orderCount'] = Order::model()->getOrderCountByCustomerPhone($phone);
            $data['orderPrice'] = Order::model()->getOrdersAmountByCustomerPhone($phone);
            $data['complainCount'] = CustomerComplain::model()->getCountByCustomerPhone($phone);
        }
        $this->render('info', array(
            'data' => $data,
        ));
    }
}