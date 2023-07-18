<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 15/4/28
 * Time: 20:47
 */

abstract class IAsyn {
    /** @var  订单 */
    protected $order ;

    function __construct($order)
    {
        $this->order = $order;
    }


    /**
     * 更新用户订单数据
     *
     */
    protected function updateCustomerDriver(){
        $order = $this->getOrder();
        $orderId = $this->getOrderId();
        $customer_data = array(
                                    'phone' => $order['phone'],
                                    'complate' => 1,
                                    'user_id' => $order['user_id'],
                                    'order_id'=>$orderId
        );
        //TODO ... add reflect class
        //TODO ...  customer service
        $task = array(
            'method' => 'update_customer_order',
            'params' => $customer_data,
        );
        Queue::model()->putin($task, 'settlement');
    }

    /**
     * 更新历史订单
     */
    protected function loadCustomerOrder(){
        $order = $this->getOrder();
        $data = array('phone' => $order['phone'], 'order_id' => $order['order_id']);
        //添加task队列更新历史订单缓存
        $task = array(
            'method' => 'load_customer_order',
            'params' => $data,
        );
        Queue::model()->putin($task, 'test');
    }

    /**
     * 成长体系操作
     * TODO ... rename
     */
    public function eMoney(){
        $order = $this->order;
        //现金消费加同等数量e币, 针对用户
        if ($order['price'] > 0) {
            $real_phone = $order['phone'];
            $vipInfo = VipService::service()->getVipInfoByVipCard($order['vipcard']);
            if($vipInfo){
                $real_phone = isset($vipInfo['phone']) ? $vipInfo['phone'] : '';
            }
            if (!empty($real_phone)) {
                //TODO ... need e 币 service
                CustomerMain::model()->addWealthForCash($real_phone, $order['price']);
            }
        }
        // TODO ...  driver account service
        $data = array('phone' => $order['phone'], 'order_id' => $order['order_id']);
        $task = array(
            'method' => 'group_order_e',
            'params' => $data,
        );
        Queue::model()->putin($task, 'orderstate');
    }

    /**
     * 代下单系统的操作
     *
     */
    public function partner(){
        $orderId = $this->getOrderId();
        /**
         * 第三方合作，司机报单后操作
         */
        $order_channel = Order::model()->getOrderChannel($orderId);
        if ($order_channel) {
            $partner = PartnerFactory::factory($order_channel);
            if ($partner instanceof AbstractPartner) {
                if (method_exists($partner, 'completeOrderHandler')) {
                    $partner->completeOrderHandler($orderId);
                }
            }
        }
    }


    protected function getOrderId(){
        $order = $this->getOrder();
        $orderId = 0;
        if($order){
            $orderId = isset($order['order_id']) ? $order['order_id'] : 0;
        }
        return $orderId;
    }


    /**
     * @return 订单
     */
    protected function getOrder()
    {
        return $this->order;
    }

    /**
     * @param 订单 $order
     */
    protected function setOrder($order)
    {
        $this->order = $order;
    }




}