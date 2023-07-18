<?php

/**
 * This is the model class for table "{{order_position}}".
 *
 * The followings are the available columns in table '{{order_position}}':
 * @property integer $id
 * @property integer $order_id
 * @property integer $type
 * @property double $latitude
 * @property double $longitude
 * @property string $street
 * @property string $created
 */
class OrderPositionSSDB  {

    private static $SSDB_MAX_COUNT_URL = 'http://ssdb01.edaijia-inc.cn:9001/driver-position/max-per-get';
    private static $SSDB_TIMESTAMP_URL_TEMPLATE = 'http://ssdb01.edaijia-inc.cn:9001/driver-position/query?driver=%s&begin=%d&end=%d';
    private static $SSDB_POSITIONS_URL_TEMPLATE = 'http://ssdb01.edaijia-inc.cn:9001/driver-position/get?driver=%s';

    /**
     *
     * @param unknown $order_id
     * @param string $start
     */
    public static function getOrderPositionsFromSSDB($driver_id, $start_time,$end_time)
    {
        $ret = array();
        if (empty($start_time) ||  empty($end_time)) {
            return array();
        }

//        $start_time = microtime(true);
        $max_count_per_request = self::getMaxPositionCountPerRequest();
        EdjLog::info('max_count_per_request:'.$max_count_per_request,'console');
        $timestamps_url = sprintf(self::$SSDB_TIMESTAMP_URL_TEMPLATE, $driver_id, $start_time * 1000
            ,$end_time * 1000);
        $timestamps = self::send($timestamps_url, 'post');
        $total_count = count($timestamps);
        if (!$timestamps) {
            return $ret;
        }
        $offset = 0;
        $positions_url = sprintf(self::$SSDB_POSITIONS_URL_TEMPLATE,$driver_id);
        $positions = array();

        while (true) {
            $sub_timestamps = array_slice($timestamps, $offset, $max_count_per_request);
            $timestamp_string = implode(",", $sub_timestamps);
            $sub_positions = self::send($positions_url, 'post', $timestamp_string);
            if (!empty ($sub_positions)) {
                foreach ($sub_positions as $position) {
                    if (empty ($position)) {
                        continue;
                    }
                    $position = json_decode($position, true);
                    if (isset ($position ['longitude'], $position ['latitude'])) {
                        $positions [] = array(
                            'lng' => $position ['longitude'],
                            'lat' => $position ['latitude'],
                            'created' => strtotime($position ['created']),
                            'order_state' => $position ['state']
                        );
                    }
                }
            }

            $offset += $max_count_per_request;
            if ($offset >= $total_count) {
                break;
            }
        }
        return $positions;
    }

    /**
     * 获得每次最多坐标数
     * @return int|string
     */
    private static function getMaxPositionCountPerRequest()
    {
        $count = self::send(self::$SSDB_MAX_COUNT_URL);
        return (!empty($count) && is_numeric($count)) ? $count : 100;
    }

    private static function send($url, $method = 'get', $post_params = null)
    {
        $stime=microtime();
        $ch = curl_init(); // 初始化curl 144
        curl_setopt($ch, CURLOPT_URL, $url); // 设置链接 145
        curl_setopt($ch, CURLOPT_TIMEOUT, 1); // 请求超时设置 146
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 设置是否返回信息 147
        EdjLog::info("sending request to $url", 'console');
        if ($method === 'post') {
            curl_setopt($ch, CURLOPT_POST, 1); // 设置为POST方式 152
            if (!empty ($post_params)) {
                if (is_array($post_params)) {
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
            if ($http_error_code != 0) { // http请求异常 167
                EdjLog::warning("request failed, error code is $http_error_code , message is " . curl_error($ch), 'console');
                $retry--;
                continue;
            }

            $response = json_decode($response, true);
            if (empty ($response ['success']) || !$response ['success']) {
                EdjLog::warning("response not success, message is " . $response ['reason'], 'console');
                $retry--;
                continue;
            }

            break;
        }
        curl_close($ch);
        $etime = microtime();
        EdjLog::info('costTime:'.($etime-$stime),'console');
        EdjLog::info('response:'.json_encode($response),'console');
        if (!empty ($response) && isset ($response ['data'])) {
          //  EdjLog::info('response data:'.$response ['data'],'console');
            return $response ['data'];
        }
        return array();
    }

    
}
