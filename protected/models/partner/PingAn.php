<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-11-11
 * Time: 下午3:50
 * To change this template use File | Settings | File Templates.
 */

class PingAn extends AbstractPartner {

    //webservice 地址
    private $webservice = "http://webt.healtha.net.cn:3000/TXN/txnWebPort?wsdl";

    //平安分配参数
    private $MID = '505556675380288';

    //平安分配参数
    private $TID = '80288001';

    //项目编号（平安分配）
    private $items = '40';

    //密钥
    private $key = array(
        'sysLoginId' => 'f7b96a92c556d577',
        'sysPwd' => '86b21af7cdef3dc6',
        'vatanges' => 'f7b96a92c556d577',
        'return' =>  '234ade3f4a3dc6ba' //返回值
    );

    /**
     * 错误信息
     * @var array
     */
    protected $error_msg = array (
        1 => '卡号或密码错误',
        3 => '卡状态异常,请查询发卡方',
        4 => '余票不足',
	    5 => '接收数据异常',
        6 => '商户信息错误',
        7 => '消费异常'
    );

    //优惠券
    private $bonus_code_list = array(
        '39' => '60869',
    );

    /**
     * 获得渠道号
     * @return string
     */
    public function getChannel(){
        return '03004';
    }

    /**
     * 获得合作者信息
     * @return mixed
     */
    public function getPartnerInfo(){

    }

    /**
     * 获得平安保险优惠券号码
     * @return string
     */
    private function getBonusSn() {
        return $this->bonus_code_list['39'];
    }

    /**
     * 执行第三方（平安保险）接口并保存log
     * @param $api_name  API名称
     * @param $params  参数
     * @return array API执行结果
     */
    public function execute($api_name, $params) {
        $client = new SoapClient($this->webservice);
        $ret = $client->$api_name($params);
        $api_log = new PartnerAccessLog();
        $return = $ret->return ? $ret->return : false;
        $api_log->insertData($this->channel, $api_name, $params, $return);
        return $return;
    }

    /**
     * 获得平安接口参数sysLoginId
     * @return string
     */
    private function getSysLoginId() {
        $key = $this->key['sysLoginId'];
        $input = $this->MID.$this->TID;
        $sysLoginId = $this->encrypt($input, $key);
        return $sysLoginId;
    }

    /**
     * 获得平安接口参数 sysPwd
     * @param $id_card
     * @param $password
     * @return string
     */
    private function getSysPwd($id_card, $password) {
        $key = $this->key['sysPwd'];
        $input = $id_card.$password;
        $sysPwd = $this->encrypt($input, $key);
        return $sysPwd;
    }


    /**
     * 平安保险鉴权
     * @param $id_card 卡号
     * @param $password 密码
     * @return array
     * @返回值说明 : array 'status' => 成功1 不成功0
     *                   'msg' => 不成功返回错误信息， 成功显示可用次数
     */
    public function verify(array $params) {
        $id_card = $params['id_card'];
        $password = $params['password'];
        $api_name = 'searchGetPro';
        $keysArr = array(
            'arg0' => $this->getSysLoginId(), //sysLoginId
            'arg1' => $this->getSysPwd($id_card, $password), //sysPwd
            'arg2' => 'S',   //type
            'arg3' => $this->items, //items
        );
        //sign
        $keysArr['arg4'] = md5($keysArr['arg0'].'_'.$keysArr['arg1'].'_'.$keysArr['arg2'].'_'.$keysArr['arg3'].'_hsarkgw');
        $result = $this->execute($api_name, $keysArr);
        if (isset($this->error_msg[$result])) {
            $return = array(
                'status' => 0,
                'msg' => $this->error_msg[$result],
            );
        } else {
            $num_tmp = explode('&', $result);
            $num_tmp = $this->decrypt($num_tmp[0], $this->key['return']);
            $num = str_replace('9@', '', $num_tmp);
            $return = array(
                'status' => 1,
                'msg' => $num
            );
        }
        return $return;
    }

    public function getPartnerOrderId($id_card, $password, $order_id=null, $num=1) {
        $api_name = 'TxnMessage';
        $vatanges = $this->encrypt($num.'@'.$order_id, $this->key['vatanges']);
        $params = array(
            'arg0' => $this->getSysLoginId(),
            'arg1' => $this->getSysPwd($id_card, $password),
            'arg2' => $vatanges,
            'arg3' => 'T',
            'arg4' => $this->items,
            'arg5' => time(), //mobtim
        );
        $sign_tmp = $params;
        unset($sign_tmp['arg4']);
        $params['arg6'] = implode('_', $sign_tmp);
        $params['arg6'] = md5($params['arg6'].'_hsarkgw');
        $result = $this->execute($api_name, $params);
        if (isset($this->error_msg[$result])) {
            $return = array(
                'status' => 0,
                'msg' => $this->error_msg[$result],
            );
        } else {
            $num_tmp = explode('&', $result);
            $num_tmp = $this->decrypt($num_tmp[0], $this->key['return']);
            $msg = explode('@', $num_tmp);
            unset($msg[0]);
            $return = array(
                'status' => 1,
                'msg' => $msg
            );
        }
        return $return;
    }

    /**
     * 在生成OrderQueue之前的操作, 将orderQueue表Channel变成平安渠道号
     * @return bool
     */
    public function beforeOrderQueueSave(OrderQueue &$model){
        $model->channel = $this->getChannel();
    }

    /**
     * 生成OrderQueue之后操作 此处绑定优惠券及记录平安客户的账号密码与order_queue_id的关联，为平安TxnMessage接口提供数据
     * @return bool
     */
    public function afterOrderQueueSave($order_queue_id){
        $id_card = isset($_REQUEST['ping_an']['id_card']) ? trim($_REQUEST['ping_an']['id_card']) : null;
        $password = $_REQUEST['ping_an']['password'] ? $_REQUEST['ping_an']['password'] : null;
        $order_queue_model = OrderQueue::model()->findByPk($order_queue_id);
        if ($order_queue_model && $order_queue_model->channel == $this->channel) {
            $queue_record = new PartnerQueueRecord();
            $queue_record->insertData($order_queue_id, $id_card, $password, $this->getChannel());
            $bonus_sn = $this->getBonusSn();
            for ($i=0; $i<$order_queue_model->number; $i++) {
                $bonus_result[$i] = BonusLibrary::model()->merchantsBind($bonus_sn, $order_queue_model->phone);
            }
            return $bonus_result;
        } else {
            return false;
        }
    }

    /**
     * 取消派单后执行动作，取消优惠券
     * @param $params Order_queue 全部属性
     * @return bool
     */
    public function cancelOrderQueue($params) {
        $bonus_sn = $this->getBonusSn();
        $result = BonusLibrary::model()->cancelBonus($bonus_sn,$params['phone']);
        return $result;
    }

    /**
     * 司机接单后执行
     * @param 订单ID
     * @return bool
     */
    public function afterOrderSave($order_id){
        $queue_model = OrderQueueMap::model()->getByOrderID($order_id);
        if ($queue_model) {
            $order_queue_id = $queue_model->queue_id;
            $customer = PartnerQueueRecord::model()->getCustomerByQueueId($order_queue_id);
            if ($customer) {
                $id_card = $customer['id_card'];
                $password = $customer['password'];
                $result = $this->getPartnerOrderId($id_card, $password, $order_id);
                if ($result['status']) {
                    $TxnMessage = $result['msg'];
                    $partner_order_id = $TxnMessage[1];
                    $insert_result = PartnerPinganOrder::model()->insertData($order_id, $partner_order_id, $id_card);
                    return $insert_result;
                } else {
                    return false;
                }

            }
        }
        return false;
    }

    /**
     * 报单后执行
     * @param Order $model
     * @return bool
     */
    public function completeOrder($order_id){
        return $this->synchronization($order_id, 'TAS');
    }

    /**
     * 销单后执行
     * @param Order $model
     * @return bool
     */
    public function cancelOrder($order_id){
        return $this->synchronization($order_id, 'TAC');
    }

    /**
     * 订单同步执行方法
     * @param $order_id
     * @return array|bool
     */
    private function synchronization($order_id, $type="TAS") {
        $order_channel = Order::model()->getOrderChannel($order_id);
        if ($order_channel && ($order_channel == $this->channel)) {
            $ping_an_order = PartnerPinganOrder::model()->find('order_id=:order_id', array(':order_id'=>$order_id));
            if ($ping_an_order) {
                return $this->OrderValid($order_id, $ping_an_order->partner_order_id, $type, 1);
            }
        } else {
            return false;
        }
    }

    /**
     * 调用平安OrderValid接口
     * @param $order_id
     * @param $partner_order_id
     * @param string $type
     * @param int $num
     * @return array|bool
     */
    public function OrderValid($order_id, $partner_order_id, $type='TAS', $num=1) {
        $input = $partner_order_id.'@'.$order_id.'@'.$num;
        $vatanges = $this->encrypt($input, $this->key['vatanges']);
        $order_info = Order::model()->getOrderInfoByReadDb($order_id);
        if (is_array($order_info) && count($order_info)) {
            //取消优惠券，该操作放在此处有点不合适，导致整个方法不够单纯，但可以少查一次ORDER表
            if ($type == 'TAC') {
                $bonus_sn = $this->getBonusSn();
                $result = BonusLibrary::model()->cancelBonus($bonus_sn, $order_info['phone']);
            }
            $params = array(
                'arg0' => $this->getSysLoginId(),
                'arg1' => $vatanges,
                'arg2' => $type,
                'arg3' => $this->items,
                'arg4' => $order_info['order_date'],
                'arg5' => time(),
            );
            $params['arg6'] = md5($params['arg0'].'_'.$params['arg1'].'_'.$params['arg2'].'_'.$params['arg3'].'_'.$params['arg4'].'_'.$params['arg5'].'_hsarkgw');
            $api_name = 'orderValid';
            return $this->execute($api_name, $params);
        } else {
            return false;
        }
    }


    /**
     * 平安保险DES加密
     * @param $input 需要加密内容
     * @param $key   密钥
     * @return string
     */
    public function encrypt($input, $key) {
		$size = mcrypt_get_block_size('des', 'ecb');
		$input = $this->pkcs5_pad($input, $size);
		$td = mcrypt_module_open('des', '', 'ecb', '');
		$iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		@mcrypt_generic_init($td, $key, $iv);
		$data = mcrypt_generic($td, $input);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		$data = bin2hex($data);
		return $data;
	}

    /**
     * 平安保险解密
     * @param $encrypted 解密内容
     * @param $key       密钥
     * @return bool|string
     */
    public function decrypt($encrypted, $key) {
		$encrypted = $this->hex2bin($encrypted);
		$td = mcrypt_module_open('des','','ecb','');
		$iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		$ks = mcrypt_enc_get_key_size($td);
		@mcrypt_generic_init($td, $key, $iv);
		$decrypted = mdecrypt_generic($td, $encrypted);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		$y=$this->pkcs5_unpad($decrypted);
		return $y;
	}

	public function pkcs5_pad ($text, $blocksize) {
		$pad = $blocksize - (strlen($text) % $blocksize);
		return $text . str_repeat(chr($pad), $pad);
	}

    public function pkcs5_unpad($text) {
		$pad = ord($text{strlen($text)-1});
		if ($pad > strlen($text))
			return false;
		if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
			return false;
		return substr($text, 0, -1 * $pad);
	}

    public function hex2bin($data) {
        $len = strlen($data);
        return pack("H" . $len, $data);
    }
}