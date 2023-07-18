<?php
/**
 * I车会.
 * User: ztyzjn
 * Date: 13-11-1
 * Time: 下午1:28
 * To change this template use File | Settings | File Templates.
 */

class CarClub extends AbstractPartner{

    private $gatewayUrl = "http://icar.1166.com.cn/weixin/";

    private $bonus_code_list = array(
        '39' => '60869',
        '59' => '15393',
        '79' => '82194',
        '99' => '18341',
    );

    //private $gatewayUrl = "http://www.d.edaijia.cn/v2/index.php?r=driver/driverAjax&act=";

    public function getChannel(){
        return '03002';
    }

    /**
     * 执行第三方（i车会）接口方法
     * @param $api_name  API名称
     * @param $params  参数
     * @return array API执行结果
     */
    public function execute($api_name, $params) {
        $url = $this->gatewayUrl.$api_name;
        $result_json = $this->get($url, $params);
        $result = json_decode($result_json, true);
        $api_log = new PartnerAccessLog();
        $api_log->insertData($this->channel, $api_name, $params, $result);
        return $result;
    }

    /**
     * I车会鉴权
     * @param $phone 客户电话
     * @param $usenum 预约人数
     * @return array
     * @返回值说明 :
     *    1、 鉴权成功 array( "status"=>0, result=> array("verify"=>0, "ablenum"=>3))
     *    2、 ip地址鉴权失败 array("status"=>"-1")
     *    3、 不是I车会会员 array("status"=>"0", array("result"=>array("verify"=>"-1", "ablenum"=>"0")))
     *    4、 是爱车会会员，但服务次数已尽 array("status"=>0, "result"=>array("verify"=>"0", "ablenum"=>"0"))
     */
    public function verify(array $params) {
        $phone = $params['phone'];
        $usenum = $params['usenum'];
        $api_name = 'DJVerify';
        $keysArr = array(
            'phone' => $phone,
            'usenum' => $usenum,
        );
        $result = $this->execute($api_name, $keysArr);
        return $result;
    }

    /**
     * 获得合作者信息
     * @return mixed
     */
    public function getPartnerInfo(){

    }

    /**
     * 在生成OrderQueue之前的操作, 将orderQueue表Channel变成I车会渠道号
     * @return bool
     */
    public function beforeOrderQueueSave(OrderQueue &$model){
        if ($model->channel == $this->channel) {
            $model->channel = $this->channel;
            $model->number = 1;
            return true;
        } else {
            return false;
        }
    }

    /**
     * 生成OrderQueue之后操作 此处根据不同的预约时间绑定不同的优惠券
     * @return bool
     */
    public function afterOrderQueueSave($order_queue_id){
        $order_queue_model = OrderQueue::model()->findByPk($order_queue_id);
        if ($order_queue_model && $order_queue_model->channel == $this->channel) {
            $booking_time = strtotime($order_queue_model->booking_time); //datetime
            $base_price = Common::conventional_price($order_queue_model->city_id, $booking_time);
            //TODO 此处绑定优惠券
            $bonus_sn = isset($this->bonus_code_list[$base_price]) ? $this->bonus_code_list[$base_price] : null;
            if ($bonus_sn) {
                $bonus_result = BonusLibrary::model()->merchantsBind($bonus_sn, $order_queue_model->phone);
                $api_name = 'OrdersSync';
                $params = array(
                    'phone' => $order_queue_model->phone,
                    'plantime' => date('YmdHis', strtotime($order_queue_model->booking_time)),
                    'planfrom' => urlencode($order_queue_model->address),
                );
                return $this->execute($api_name, $params);
            }

        }
        return false;
    }

    /**
     * 取消派单后执行动作，取消优惠券
     * @param $params
     * @return bool
     */
    public function cancelOrderQueue($params) {
        if (isset($params['booking_time'])) {
            $base_price = Common::conventional_price($params['city_id'], strtotime($params['booking_time']));
            $bonus_sn = isset($this->bonus_code_list[$base_price]) ? $this->bonus_code_list[$base_price] : null;
            if ($bonus_sn) {
                $result = BonusLibrary::model()->cancelBonus($bonus_sn,$params['phone']);
                return $result;
            }
        }
        return false;
    }

    /**
     * 司机接单后执行 此处调用爱车会订单同步接口
     * @param 订单ID
     * @return bool
     */
    public function afterOrderSave($order_id){
        /*
        $order_channel = Order::model()->getOrderChannel($order_id);
        if ($order_channel && ($order_channel == $this->channel)) {
            $model =  Order::model()->find('order_id=:order_id', array(':order_id'=>$order_id));
            $api_name = 'OrdersSync';
            $params = array(
                'phone' => $model->phone,
                'id' => intval($model->order_id),
                'plantime' => date('YmdHis', $model->booking_time),
                'planfrom' => urlencode($model->location_start),
                'driverid' => trim($model->driver_id)
            );
            return $this->execute($api_name, $params);
        } else {
            return false;
        }
        */
        return true;
    }

    /**
     * 报单后执行
     * @param Order $model
     * @return bool
     */
    public function completeOrder($order_id){
        return $this->synchronization($order_id);
    }

    /**
     * 销单后执行
     * @param Order $model
     * @return bool
     */
    public function cancelOrder($order_id){
        return $this->synchronization($order_id, 'cancel');
    }

    /**
     * 订单同步执行方法
     * @param $order_id
     * @return array|bool
     */
    private function synchronization($order_id, $type="complete") {
        $order_channel = Order::model()->getOrderChannel($order_id);
        if ($order_channel && ($order_channel == $this->channel)) {
            $model =  Order::model()->find('order_id=:order_id', array(':order_id'=>$order_id));
            $basicfee = Common::conventional_price($model->city_id, $model->booking_time);
            if (in_array(intval($model->status), array(1,4))) {
                $ext_data = OrderExt::model()->getPrimary($order_id);
                $waiting_time = $ext_data['wait_time'];
                $service = 0;
            } else {
                $waiting_time = 0;
                $service = intval($model->cancel_type) > 0 ? intval($model->cancel_type) : 5;
            }
            $api_name = 'OverSync';
            $params = array(
                'phone' => $model->phone,
                'id' => intval($model->order_id),
                'driverid' => $model->driver_id,
                'service' => $service,
                'realfrom' => urlencode($model->location_start),
                'realtime' => date('YmdHis', $model->start_time),
                'to' => urlencode($model->location_end),
                'fee' => $model->price,
                'basicfee' => $basicfee,
                'waittime' => $waiting_time,
                'distance' => ($model->distance)*1000,
            );
            if ($type == 'cancel') {
                $bonus_sn = $this->getBonusSn($model->city_id, $model->booking_time);
                if ($bonus_sn) {
                    BonusLibrary::model()->cancelBonus($bonus_sn, $model->phone, $order_id);
                }
            }
            return $this->execute($api_name, $params);
        } else {
            return false;
        }
    }

    private function getBonusSn($city_id, $booking_time) {
        $base_price = Common::conventional_price($city_id, $booking_time);
        $bonus_sn = isset($this->bonus_code_list[$base_price]) ? $this->bonus_code_list[$base_price] : null;
        return $bonus_sn;
    }

}