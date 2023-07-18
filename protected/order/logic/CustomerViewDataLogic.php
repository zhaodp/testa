<?php
/**
 * 客户端展示数据Logic
 * TODO 需要接口随时添加,最终替换QueueApiOrder.php
 */

class CustomerViewDataLogic {

    private $redis = null;
    private static $instance;

    private $_prefix = 'PhoneBookingId_';

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new CustomerViewDataLogic();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->redis = Redis03::model()->getRedis();
    }

    function __get($name) {
        return $this->$name;
    }

    function __set($name, $value){
        $this->$name = $value;
    }

    /*
     * redis index
     */
    private function index($phone, $bookingId) {
        $index = $phone."_".$bookingId;
        return $this->_prefix.$index;
    }

    /**
     * 查询对应预约的全部数据
     * @param string $phone
     * @param string $bookingId
     * @return array
     */
    public function getAllFields($phone, $bookingId) {
        $index = $this->index($phone, $bookingId);
        $bookingData = $this->redis->hgetall($index);
        return $bookingData;
    }
}
