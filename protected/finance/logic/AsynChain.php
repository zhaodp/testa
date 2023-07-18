<?php
/**
 * 提供异步的操作逻辑
 *
 * User: tuan
 * Date: 15/4/28
 * Time: 20:44
 */

class AsynChain extends IAsyn{


    function __construct($order)
    {
        $this->order = $order;
    }

    public function afterSettle(){
        $this->updateCustomerDriver();
        $this->loadCustomerOrder();
        $this->eMoney();
        $this->partner();
    }
}