<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 15/1/14
 * Time: 15:22
 */

class CustomerSettlement extends Settlement{

    private $phone;

    private $type;

    private $source ;

    private $comment;

    private $userId;

    private $cast;

    private $vip;

    private $customer;

    private $orderId;

    function __construct($phone)
    {
        $this->phone = $phone;
    }


    public function init()
    {
        $customerPhone = $this->getPhone();
        $isVip = VipService::service()->isVip($customerPhone);
        if ($isVip) {
            $vipPhone = VipService::service()->getVipInfo($customerPhone, false);
            if($vipPhone){
                $this->setVip($vipPhone);
            }
        } else {
            $customerAccount = NormalAccountService::forceGetUserAmount($customerPhone);
            $this->setCustomer(array(
                'id'	=> $customerAccount['user_id'],
                'amount'=> $customerAccount['amount'],
            ));
        }
    }

    /**
     * 结账,必须重写
     */
    public function settlement()
    {
        $this->init();
        if(empty($vip)){
             return $this->orderUser();
        }
        return false;
    }

    private function orderVip(){

    }

    private function orderUser(){
        //checkout
        $customer = $this->getCustomer();
        $userId = $customer['id'];
        $customerBalance = $customer['amount'];
        $delta = $this->getCast();
        if (0 != $delta) {
            $orderId = $this->getOrderId();
            $customerTransAttributes = array(
                'user_id' => $userId,
                'trans_order_id' => $orderId,
                'trans_type' => $this->getType(),
                'amount' => $delta,
                'balance' => $customerBalance + $delta,
                'source' => $this->getSource(),
                'remark' => $this->getComment(),
            );
            $customerAccountAttributes = array(
                'user_id' => $userId,
                'amount' => $delta,
            );
            $ret = $this->chargeNormal($customerTransAttributes, $customerAccountAttributes);
            if(!$ret){
            }
            return $ret;
        }else{
            return true;
        }
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getCast()
    {
        return $this->cast;
    }

    /**
     * @param mixed $cast
     */
    public function setCast($cast)
    {
        $this->cast = $cast;
    }

    /**
     * @return mixed
     */
    public function getVip()
    {
        return $this->vip;
    }

    /**
     * @param mixed $vip
     */
    public function setVip($vip)
    {
        $this->vip = $vip;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param mixed $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }



}