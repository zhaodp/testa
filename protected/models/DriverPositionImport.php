<?php
/**
 * 司机位置导入到ssdb
 * @author diwenchen
 */
class DriverPositionImport {
    public static function model($className = __CLASS__) {
        return parent::model ( $className );
    }
    public static function batchImportPostions($params) {
        // var_dump($params);
        if (! isset ( $params ['positions'] ) || ! isset ( $params ['driver_id'] )) {
            return;
        }
        
        $driver_id = $params ['driver_id'];
        $positions = $params ['positions'];
        
        foreach ( $positions as $pos ) {
            if (! isset ( $pos ['lat'] ) || ! isset ( $pos ['lng'] ) || ! isset ( $pos ['gps_type'] ) || ! isset ( $pos ['provider'] ) || ! isset ( $pos ['accuracy'] ) || ! isset ( $pos ['status'] ) || ! isset ( $pos ['milli_timestamp'] )) {
                // echo "NOT KEY\n";
                continue;
            }
            $param = array ();
            $param ['driver_id'] = $driver_id;
            $param ['status'] = $pos ["status"];
            $milli_timestamp = intval ( $pos ["milli_timestamp"] );
            $param ['milli_timestamp'] = $milli_timestamp;
            $param ['log_time'] = date ( "YmdHis", $milli_timestamp );
            $param ['longitude'] = $pos ["lng"];
            $param ['latitude'] = $pos ["lat"];
            $param ['gps_type'] = $pos ["gps_type"];
            
            self::singleImportPostion ( $param );
        }
    }
    public static function singleImportPostion($params) {
        $driver_id = $params ['driver_id'];
        $status = isset ( $params ['status'] ) ? $params ['status'] : 0;
        
        $params ['log_time'] = isset ( $params ['log_time'] ) ? $params ['log_time'] : date ( "YmdHis" );
        $log_time = Common::format_log_time ( $params ['log_time'] );
        
        $check_sum = intval ( $params ['longitude'] ) + intval ( $params ['latitude'] );
        if ($check_sum > 10) {
            
            $gps_position = array (
                            'longitude' => $params ['longitude'],
                            'latitude' => $params ['latitude'] 
            );
            
            $gps = GPS::model ()->convert_only ( $gps_position, $params ['gps_type'] );
            
            $attributes = $gps;
            $attributes ['user_id'] = $driver_id;
            $attributes ['created'] = $log_time;
            
            $attributes ['state'] = $status;
            
            $milli_timestamp = isset ( $params ['milli_timestamp'] ) ? $params ['milli_timestamp'] : strtotime ( $log_time );
            $start_time = Common::get_current_time ();
            self::httpPost ( $driver_id, $attributes, $milli_timestamp * 1000 );
            $run_time = Common::get_time_intv ( $start_time );
            EdjLog::info ( "driver_position_to_ssdb_time: $run_time" );
        }
        
        $format = "userid:%s \n";
        echo printf ( $format, $driver_id );
    }
    private function httpPost($driver_id, $params, $timeStamp) {
        $urlSting = "http://ssdb01:9001/driver-position/upload?driver=%s&timestamp=%d";
        $url = sprintf ( $urlSting, $driver_id, $timeStamp );
        $ch = curl_init (); // 初始化curl
        curl_setopt ( $ch, CURLOPT_URL, $url ); // 设置链接
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 5 ); // 请求超时设置
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 ); // 设置是否返回信息
        curl_setopt ( $ch, CURLOPT_POST, 1 ); // 设置为POST方式
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, json_encode ( $params ) ); // POST数据
        $retry = 3;
        while ( $retry -- ) {
            $response = curl_exec ( $ch ); // 接收返回信息
            if (curl_errno ( $ch )) { // 出错则显示错误信息
                continue;
            } else {
                EdjLog::warning ( "driver_position_to_ssdb success|" . $url . "|" . json_encode ( $params ) );
                break;
            }
        }
        if ($retry < 0) {
            $err = curl_error ( $ch );
            EdjLog::info ( "driver_position_to_ssdb fail|" . $url . "|" . json_encode ( $params ) . "|" . $err );
        }
        curl_close ( $ch );
    }
}
