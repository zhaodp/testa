<?php
class CcicAction extends CAction {

    // TODO 添加大客户电话, 格式为：'18611648791' => true
    private static $BIG_CUSTOMER_PHONE_MAP = array();

    private static $CUSTOMER_TYPE = array('big' => 1,
                                          'existing' => 2,
                                          'new' => 3);

    public function run() {
        $token = isset($this->controller->_params['token'])?$this->controller->_params['token']:'';
                
        // token md5(edaijia_ccic_172.16.11.11) edac5b44e15fa346985117bc18a3bc72
        if(empty($token) || $token != 'edac5b44e15fa346985117bc18a3bc72') {
            echo json_encode(array('code' => '2', 'message' => 'error token'));
            return;
        }

        $ret = array();
        $method = isset($this->controller->_params['method'])?$this->controller->_params['method']:'';
        switch ($method) {
            case 'tel' :
                $phone = empty($this->controller->_params['tel']) ? '' : trim($this->controller->_params['tel']);
                $ret = array('customer' => $this->getCustomerType($phone));
                break;
            default :
                $ret = array('code' => '2', 'message' => 'error method');
                break;
        }

        echo json_encode($ret);
        return;
    }

    private function getCustomerType($phone){
        // 先判断是否为大客户
        if (array_key_exists($phone, self::$BIG_CUSTOMER_PHONE_MAP)) {
            return self::$CUSTOMER_TYPE['big'];
        }

        // 按照电话号码最后一位查找数据库表
        $lastNumber = substr($phone, strlen($phone)-1, 1);
        $phoneExisted = Yii::app()->db_readonly->createCommand()->select('phone')->from("user_list_f_$lastNumber")->where('phone=:phone', array(':phone'=>$phone))->queryRow();
        if ($phoneExisted) {
            return self::$CUSTOMER_TYPE['existing'];
        }
    	return self::$CUSTOMER_TYPE['new'];
    }
}
