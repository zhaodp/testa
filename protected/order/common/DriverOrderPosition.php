<?php
/**
 * Created by PhpStorm.
 * User: wanghongyuan
 * Date: 2015/4/29
 * Time: 20:59
 */

class DriverOrderPosition {


    public $order_state='已接单';
    public $wait_time='';
    public $cancel_type='';
    public $order_all_states;
    public $order_state_code = OrderProcess::ORDER_PROCESS_ACCEPT;
    public $role = '';
    public $order_id = '';
    public $accept_time;
    public $drive_time;
    public $expect_ready_time = 0;
    public $expect_ready_time_cost = 0;

//传入
    public $token;
    public $booking_id;
    public $driver_id;
    public $gps_type;
    public $app_ver;
    public  $booking_type;
//远程单信息
    public $remote_order_data=array();
    function __construct($orders)
    {
        $this->$token = isset($params['token']) ? $params['token'] : '';
        $this->booking_id = isset($params['booking_id']) ? $params['booking_id'] : '';
        $this->driver_id = isset($params['driver_id']) ? $params['driver_id'] : '';
        $this->order_id = isset($params['order_id']) ? $params['order_id'] : '';
        $this->gps_type = isset($params['gps_type']) ? $params['gps_type'] : 'google';
        $this->app_ver = isset($params['app_ver']) ? $params['app_ver'] : '0.0.0';
    }


    public function setByOrders($orders){
//默认角色通过预约人数判定，有疑问，如果用户代叫司机的话，也可以下多个单
        if (count($orders) > 1) {
            $role = '组长';
        } else {
            $role = '组员';
        }
        $order_states = array(
            0=> array(
                'order_state_code' => OrderProcess::ORDER_PROCESS_ACCEPT,
                'order_state_timestamp' => time(),
                'order_state_content' => '司机已接单'
            ),
        );
        $this->order_all_states= $order_states;
        $this->role= $role;//疑问
    }

}