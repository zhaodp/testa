<?php
class DriverPositionSSDB
{
    private static $SSDB_MAX_COUNT_URL = 'http://ssdb01.edaijia-inc.cn:9001/driver-position/max-per-get';
    
    private static $SSDB_TIMESTAMP_URL_TEMPLATE = 'http://ssdb01.edaijia-inc.cn:9001/driver-position/query?driver=%s&begin=%d&end=%d';
    
    private static $SSDB_POSITIONS_URL_TEMPLATE = 'http://ssdb01.edaijia-inc.cn:9001/driver-position/get?driver=%s';
    
    private function getOrderPositions($order_id, $driver_id, $start_date, $end_date) {
        if((time() - $start_date) <= RDriverPosition::EXPIRE_TIME_MONTH) {
            $start_time = microtime(true);
            $positions = $this->getPositionsFromCache($order_id, $driver_id);
            if(!empty($positions)) {
                $time = microtime(true) - $start_time;
                $count = count($positions);
                return array(
                    'time'      => $time,
                    'positions' => $positions,
                    'source'    => 'redis',
                    'count'     => $count,
                    'speed'     => $count / $time
                );
            }
        }
        return $this->getPositions($driver_id, $start_date, $end_date);
    }
    
    public function getPositions($driver_id, $start_date, $end_date) {
        $start_time = microtime(true);
        $max_count_per_request = $this->getMaxPositionCountPerRequest();
        
        $timestamps_url = sprintf(self::$SSDB_TIMESTAMP_URL_TEMPLATE, $driver_id, $start_date * 1000, $end_date * 1000);
        $timestamps = $this->send($timestamps_url, 'post');
        $total_count = count($timestamps);
        
        if($total_count < 1) {
            return array(
                'time'      => 'N/A',
                'positions' => array(),
                'source'    => 'N/A',
                'count'     => 0,
                'speed'     => 'N/A'
            );
        }
        
        $offset = 0;
        $positions_url = sprintf(self::$SSDB_POSITIONS_URL_TEMPLATE, $driver_id);
        $positions = array();
        
        while(true) {
            $sub_timestamps = array_slice($timestamps, $offset, $max_count_per_request);
            $timestamp_string = implode(",", $sub_timestamps);
            $sub_positions = $this->send($positions_url, 'post', $timestamp_string);
            
            if(!empty($sub_positions)) {
                foreach($sub_positions as $position) {
                    if(empty($position)) {
                        continue;
                    }
                    
                    $position = json_decode($position, true);
                    if(isset($position['longitude'], $position['latitude'])) {
                        $positions[] = array(
                            'lng'     => $position['longitude'],
                            'lat'     => $position['latitude'],
                            'created' => $position['created'],
                            'state'   => $position['state']
                        );
                    }
                }
            }
            
            $offset += $max_count_per_request;
            if($offset >= $total_count) {
                break;
            }
        }
        
        $time = microtime(true) - $start_time;
        $count = count($positions);
        
        return array(
            'time'      => $time,
            'positions' => $positions,
            'source'    => 'ssdb',
            'count'     => $count,
            'speed'     => $count / $time
        );
    }
    
    private function getMaxPositionCountPerRequest() {
        $count = $this->send(self::$SSDB_MAX_COUNT_URL);
        return (!empty($count) && is_numeric($count)) ? $count : 100; 
    }
    
    private function send($url, $method = 'get', $post_params = null) {
        $ch = curl_init (); // 初始化curl
        curl_setopt($ch, CURLOPT_URL, $url); // 设置链接
        curl_setopt($ch, CURLOPT_TIMEOUT, 1); // 请求超时设置
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 设置是否返回信息
        
        EdjLog::info("sending request to $url", 'console');
        
        if($method === 'post') {
            curl_setopt($ch, CURLOPT_POST, 1); // 设置为POST方式
            
            if(!empty($post_params)) {
                if(is_array($post_params)) {
                    $post_params = json_encode($post_params);
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params);
                EdjLog::info("post param is $post_params", 'console');
            }
        }
        
        $retry = 3;
        while ($retry > 0) {
            $response = curl_exec($ch);
            $http_error_code = curl_errno($ch);
            if ($http_error_code != 0) { // http请求异常
                EdjLog::warning("request failed, error code is $http_error_code , message is " . curl_error($ch), 'console');
                $retry--;
                continue;
            }
        
            $response = json_decode($response, true);
            if(empty($response['success']) || !$response['success']) {
                EdjLog::warning("response not success, message is " . $response['reason'], 'console');
                $retry--;
                continue;
            }
            
            break;
        }
        curl_close($ch);
        
        if(!empty($response) && isset($response['data'])) {
            return $response['data'];
        }
        return false;
    }
    
    private function getCity($driver_id) {
        $city_id = DriverStatus::model()->getItem($driver_id,'city_id');
        if($city_id != 0) {
            $city_name = Dict::item('city', $city_id);
        } else {
            $city_name = '未知';
        }
        
        return array(
            'city'    => $city_name,
            'city_id' => $city_id
        );
    }
	
}