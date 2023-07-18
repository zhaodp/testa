<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ZhangTingyi
 * Date: 13-11-1
 * Time: 上午11:02
 * To change this template use File | Settings | File Templates.
 */

abstract class AbstractPartner {

    public $channel = null;

    private $errors = array();

    public function __construct(){
        $this->channel = $this->getChannel();
    }

    private function setError($error) {
        return array_push($this->errors, $error);
    }

    public function getErrors() {
        return $this->errors;
    }

    /***
     * 获得渠道ID
     * @return string
     */
    abstract public function getChannel();

    /**
     * 第三方鉴权操作
     * @param array $params
     * @return mixed
     */
    abstract public function verify(array $params);

    /**
     * 获得合作者信息
     * @return mixed
     */
    abstract public function getPartnerInfo();

    /**
     * 在生成OrderQueue之前的操作  使用场景：绑定优惠券，绑定VIP等
     * @return bool
     */
    abstract public function beforeOrderQueueSave(OrderQueue &$model);

    /**
     * 生成OrderQueue之后操作  使用场景：记录log、调用第三方API反馈是否派单
     * @return bool
     */
    abstract public function afterOrderQueueSave($order_queue_id);

    /**
     * 取销派单（指还未生成订单，只改变OrderQueue中的Flag状态）
     * @param $params
     * @return mixed
     */
    abstract public function cancelOrderQueue($params);

    /**
     * 司机接单后执行
     * @param $order_id
     * @return bool
     */
    abstract public function afterOrderSave($order_id);

    /**
     * 报单后执行
     * @param $order_id
     * @return bool
     */
    abstract public function completeOrder($order_id);

    /**
     * 销单后执行
     * @param order_id
     * @return bool
     */
    abstract public function cancelOrder($order_id);

    public function beforeOrderQueueSaveHandler(OrderQueue &$model) {
        try {
            return $this->beforeOrderQueueSave($model);
        }catch(Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * 生成OrderQueue之后操作
     * @param $order_queue_id
     * @return bool
     */
    public function afterOrderQueueSaveHandler($order_queue_id){
        try {
            return $this->afterOrderQueueSave($order_queue_id);
        }catch(Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * 司机接单后执行
     * @param Order $model
     * @return bool
     */
    public function afterOrderSaveHandler($order_id){
        try {
            /*
            $task = array(
                'method' => 'partner_order_create_sync',
                'params' => array(
                    'order_id' => $order_id,
                ),
            );
            */
            return $this->afterOrderSave($order_id);
            //Queue::model()->putin($task, 'default');
        }catch(Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * 报单后执行
     * @param Order $model
     * @return bool
     */
    public function completeOrderHandler($order_id){
        try {
            /*
            $task = array(
                'method' => 'partner_order_complete_sync',
                'params' => array(
                    'order_id' => $order_id,
                ),
            );
            */
            //return Queue::model()->putin($task, 'default');
            return $this->completeOrder($order_id);
        }catch(Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * 销单后执行
     * @param Order $model
     * @return bool
     */
    public function cancelOrderHandler($order_id) {
        try {
            /*
             $task = array(
                'method' => 'partner_order_cancel_sync',
                'params' => array(
                    'order_id' => $order_id,
                ),
            );
            return Queue::model()->putin($task, 'default');
            */
            return $this->cancelOrder($order_id);
        }catch(Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    public function cancelOrderQueueHandler($params) {
        try{
            return $this->cancelOrderQueue($params);
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * combineURL
     * 拼接url
     * @param string $baseURL   基于的url
     * @param array  $keysArr   参数列表数组
     * @return string           返回拼接的url
     */
    public function combineURL($baseURL,$keysArr){
        $combined = strpos($baseURL,"?")==false ? $baseURL."?" : $baseURL."&";
        $valueArr = array();

        foreach($keysArr as $key => $val){
            $valueArr[] = "$key=$val";
        }

        $keyStr = implode("&",$valueArr);
        $combined .= ($keyStr);
        return $combined;
    }

    /**
     * get_contents
     * 服务器通过get请求获得内容
     * @param string $url       请求的url,拼接后的
     * @return string           请求返回的内容
     */
    public function get_contents($url){
        /*
        if (ini_get("allow_url_fopen") == "1") {
            $response = file_get_contents($url);
        }else{
        */
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_FAILONERROR, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_URL, $url);
            $response =  curl_exec($ch);

        curl_close($ch);
        //}
        //-------请求为空
        if(empty($response)){
            $this->setError(curl_errno($ch));
        }
        return $response;
    }

    /**
     * get
     * get方式请求资源
     * @param string $url     基于的baseUrl
     * @param array $keysArr  参数列表数组
     * @return string         返回的资源内容
     */
    public function get($url, $keysArr){
        $combined = $this->combineURL($url, $keysArr);
        return $this->get_contents($combined);
    }

    /**
     * post
     * post方式请求资源
     * @param string $url       基于的baseUrl
     * @param array $keysArr    请求的参数列表
     * @param int $flag         标志位
     * @return string           返回的资源内容
     */
    public function post($url, $keysArr, $flag = 0){
        $ch = curl_init();
        if(! $flag) curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $keysArr);
        curl_setopt($ch, CURLOPT_URL, $url);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }


}
