<?php

/**
 * 获取订单服务，包括：<br/>
 * 1、拉取订单信息，检查是否接单成功<br/>
 * 2、获取当前所有进行中的订单
 * 
 * @author diwenchen 2015-05-04 13:48:59
 */
class PollOrderService extends BaseFlowService
{
    private static $instance;

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new PollOrderService();
        }
        return self::$instance;
    }

    /**
     * API入口
     * 拉取订单信息，检查是否接单成功,只需要读取redis的订单信息里是否有了driver_id即可
     */
    public function pollUntreatedOrder($params) {
        //验证参数
        $token = isset($params['token']) ? trim($params['token']) : '';
        $booking_id = isset($params['booking_id']) ? $params['booking_id'] : '';
        $booking_type = isset($params['booking_type']) ? $params['booking_type'] : '';
        $polling_start = isset($params['polling_start']) ? $params['polling_start'] : '';
        $polling_count = isset($params['polling_count']) ? $params['polling_count'] : '';
        $gps_type = isset($params['gps_type']) ? $params['gps_type'] : 'google';
        if (empty($booking_id) || empty($polling_start) || empty($polling_count)) {
            return array('code' => 2 , 'data' => '' , 'message' => '参数有误');
        }
        
        //验证token
        $validate = CustomerToken::model()->validateToken($token);
        if (!$validate) {
            return array('code' => 1 , 'data' => '' , 'message' => '验证失败');
        }
        
        $data = array();
        $data['eta'] = '';
        $data['next'] = 10;
        if ($polling_count != 1) {
            $data['next'] = 5;
        }
        
        switch ($booking_type) {
            case CustomerApiOrder::QUEUE_CHANNEL_SINGLE_DRIVER:
                $data['timeout'] = CustomerApiOrder::POLLING_SECOND_DRIVER;
                break;
            case CustomerApiOrder::QUEUE_CHANNEL_SINGLE_CHANGE:
                $data['timeout'] = CustomerApiOrder::POLLING_SECOND_CHANGE;
                break;
            case CustomerApiOrder::QUEUE_CHANNEL_BOOKING:
                $data['timeout'] = CustomerApiOrder::POLLING_SECOND_BOOKING;
                break;
            case CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER:
                $data['timeout'] = CustomerApiOrder::POLLING_SECOND_BOOKING;
                break;
            case CustomerApiOrder::QUEUE_CHANNEL_FIFTEEN_MIN_BOOKING:
                $data['timeout'] = CustomerApiOrder::POLLING_SECOND_FIFTEEN_MIN_BOOKING;
                break;
            case CustomerApiOrder::QUEUE_CHANNEL_400_OR_OTHER: //400订单
                $data['timeout'] = CustomerApiOrder::POLLING_SECOND_400_ORDER;
                break;
            default:
                $data['timeout'] = CustomerApiOrder::POLLING_SECOND_DRIVER;
                break;
        }
        //$data['timeout'] = 60;
        $data['polling_count'] = $polling_count;
        $data['text'] = '发送订单中...';
        //获取redis中订单信息
        //增加司机端拒绝返回参数要修改
        
        $order = CustomerApiOrder::model()->getOrderByBookingID($validate['phone'] , $booking_id , $data['timeout']);
        if ($order['driver_id'] == Push::DEFAULT_DRIVER_INFO) {
            $order['driver_id'] = '';
        }
        $data['driver_id'] = $order['driver_id'];
        $data['order_id'] = $order['order_id'];
        $data['polling_state'] = $order['polling_state'];
        //下边的返回值需要有改动
        //$data['number'] = 6;
        
        //返回数据
        return array('code' => 0 , 'data' => $data , 'message' => '获取成功');
    }
    
    /**
     * API入口
     * 获取正在进行中的订单，只需要从redis中获取我的司机。
     */
    public function pollUnderwayOrder($params){
        //接收token
        $token = isset($params['token']) ? $params['token'] : '';
        $gps_type = isset($params['gps_type']) ? $params['gps_type'] : 'google';
        $polling_count = isset($params['polling_count']) ? $params['polling_count'] : 1;
        $app_ver = isset($params['app_ver']) ? $params['app_ver'] : '0.0.0';
        
        //验证token
        $validate = CustomerToken::model()->validateToken($token);
        if (!$validate) {
            return array('code' => 1 , 'data' => '' , 'message' => '验证失败');
        }
        
        //获取当前订单数据
        $phone = isset($validate['phone']) ? $validate['phone'] :'';
        $result = CustomerApiOrder::model()->getMyDrivers($phone , $gps_type, $app_ver);
        $data = array(
                        'drivers' => $result['drivers'],
                        'polling_state' => $result['polling_state'],
                        'polling_count' => $polling_count,
                        'next' => 10,
        );
        
        if ($polling_count >= 50) {
            $data['polling_state'] = 2;
        };
        
        return array('code' => 0 , 'data' => $data , 'message' => '成功',);
    }
}
