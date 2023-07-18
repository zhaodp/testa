<?php
/**
 *
 * 用来封装对于司机账户的操作
 *
 * 需要注意 type 对于不同的值的影响
 * User: tuan
 * Date: 15/1/13
 * Time: 20:50
 */
class DriverSettlement extends Settlement{

    private $cast = 0;

    private $type = EmployeeAccount::TYPE_INFOMATION;

    private $channel = null;

    private $comment = null;

    private $orderId = 0;

    private $cityId = null;

    private $driverId = null ;

    function __construct($cityId, $channel, $driverId)
    {
        $this->cityId = $cityId;
        $this->channel = $channel;
        $this->driverId = $driverId;
    }


    /**
     * 结账
     */
    public function settlement()
    {
        if($this->validator()){
            $this->orderDriver();
        }else{
            return false;
        }
    }

    private function validator(){
        return $this->getChannel() != null
            && $this->getComment() != null
            && $this->getDriverId() != null;
    }

    private function orderDriver(){
        $cast = $this->getCast();
        $employeeAccountAttributes = array();
        $employeeAccountAttributes['type'] = $this->getType();
        $employeeAccountAttributes['channel'] = $this->getChannel();
        $employeeAccountAttributes['city_id'] = $this->getCityId();
        $employeeAccountAttributes['user'] = $this->getDriverId();
        $employeeAccountAttributes['order_id'] = $this->getOrderId();
        $employeeAccountAttributes['order_date'] = time();
        $employeeAccountAttributes['cast'] = $cast;
        $employeeAccountAttributes['comment'] = $this->getComment();
        $ret = $this->chargeDriver($employeeAccountAttributes);
        if($ret){
        }else{
            $this->alarm();
        }
        return $ret;
    }

    private function alarm(){

    }

    /**
     * @return int
     */
    public function getCast()
    {
        return $this->cast;
    }

    /**
     * @param int $cast
     */
    public function setCast($cast)
    {
        $this->cast = $cast;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return null
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param null $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    /**
     * @return null
     */
    public function getComment()
    {
        $orderId = $this->getOrderId();
        if(!empty($orderId)){
            return $this->comment.' 订单号:'.$orderId;
        }
        return $this->comment;
    }

    /**
     * @param null $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return null
     */
    public function getCityId()
    {
        return $this->cityId;
    }

    /**
     * @param null $cityId
     */
    public function setCityId($cityId)
    {
        $this->cityId = $cityId;
    }

    /**
     * @return null
     */
    public function getDriverId()
    {
        return $this->driverId;
    }

    /**
     * @param null $driverId
     */
    public function setDriverId($driverId)
    {
        $this->driverId = $driverId;
    }


}