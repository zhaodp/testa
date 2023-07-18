<?php

class OrderStatusChangedPublisher {
    
    public static function publish($params) {
        
        EdjLog::info('OrderStatusChangedPublisher publishing, params are ' . json_encode($params), 'console');
        
        if(!isset($params['type'])) {
            $order_ext = OrderExt::model()->findByPk($params['orderId']);
            $params['type'] = $order_ext['type'];
        }
        
        if(!self::isFromH5OrWeixin($params['type'])) {
            EdjLog::info('order not from H5 or Weixin, type is ' . $params['type'] . ', skip it', 'console');
            return;
        }
        $params['from'] = substr($params['type'], 3, 10);
        
        if(!isset($params['orderId'])) {
            EdjLog::warning('no order id for order status change event publish, skip it', 'console');
            return;
        }
        
        if(!isset($params['bookingId'])) {
            $order_queue = self::getOrderQueue($params['orderId']);
            $params['bookingId'] = $order_queue['callid'];
        }
        
        $params['sig2'] = self::createSig($params);
        
        self::send($params);
    }
    
    private static function isFromH5OrWeixin($type) {
        $type = trim($type);
        return isset($type) && preg_match('/^\d{5}0[12]\d{6}$/', $type);
    }
    
    private static function getOrderQueue($order_id) {
        $map = OrderQueueMap::model()->find(array(
            'condition' => 'order_id = :order_id',
            'params'    => array(':order_id' => $order_id),
            'order'     => 'id ASC'
        ));
        
        return OrderQueue::getDbReadonlyConnection()->createCommand()
            ->select('*')
            ->from('t_order_queue')
            ->where('id = :id' , array(':id' => $map->queue_id))
            ->order('id ASC')
            ->queryRow();
    }
    
    private function send($params) {
        
        $url = Yii::app()->params['order_flow']['order_status_changed_publish_url'];
        $url .= ('?' . http_build_query($params));
        
        $ch = curl_init (); // 初始化curl
        curl_setopt($ch, CURLOPT_URL, $url); // 设置链接
        curl_setopt($ch, CURLOPT_TIMEOUT, 20); // 请求超时设置
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 设置是否返回信息
//         curl_setopt($ch, CURLOPT_POST, 1); // 设置为POST方式
//         curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_params)); // POST数据
        
        EdjLog::info("sending request to $url,  params are " . json_encode($params));
        
        $retry = 3;
        while ($retry > 0) {
            $response = curl_exec($ch);
            $http_error_code = curl_errno($ch);
            
            if($http_error_code != 0) {
                $retry--;
                EdjLog::warning("request http failed, error code is $http_error_code , message is " . curl_error($ch));
                continue;
            }
            
            break;
        }
        
        if($retry < 1) {
            EdjLog::warning('request overall failed after retries');
        }
        
        curl_close($ch);
    }
    
    public static function addQueue($params) {
        EdjLog::info('OrderStatusChangedPublisher - add task to queue, params are ' . json_encode($params), 'console');
        $task = array(
            'method' => 'publishOrderStatusChanged',
            'params' => $params,
        );
        Queue::model()->putin($task , 'order_status_changed_publisher');
    }
    
    private static function createSig($params) {
        ksort($params);
        $query_string = '';
        
        foreach($params as $k=>$v) {
            $query_string .= $k.$v;
        }
        
        return md5($query_string . 'nihs1323200$#$@#@luansd');
    }
}
