<?php
/**
 * 司机轨迹service
 * Created by PhpStorm.
 * User: zhaoyingshuang
 * Date: 15-4-30
 * Time: 下午3:21
 */

class DriverTrackService {
    private static $instance;

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new DriverTrackService();
        }
        return self::$instance;
    }

    function __get($name) {
        return $this->$name;
    }

    function __set($name, $value){
        $this->$name = $value;
    }

    /*
     *  司机端上传轨迹
     *
     */
    public function driverUploadTrack($driver, $params) {
        $log_time = isset($params['log_time']) ? $params['log_time'] : date('YmdHis');
        $de_json = isset($params['positions']) ? $params['positions'] : '';
        $driver_id = $driver->driver_id;;
        $status = isset($params['status']) ? $params['status'] : '2';//没传就默认下班

        $count_json = count($de_json);

        $lat =1.0;
        $lng =1.0;
        $gps_type = 'baidu';
        $gps_time = time();

        $positions_arr = array();
        $pos_status =  $status;

        $track_params['driver_id'] = $driver_id;
        $track_params['log_time'] = $log_time;

        for ($i = 0; $i < $count_json; $i++){
            if(count($positions_arr) > 0 && $curr_status != $de_json[$i]['status']) {
                //更新track
                $track_params['positions'] = $positions_arr;
                $track_params['status']    = $curr_status;
                $task=array(
                    'class' => __CLASS__,
                    'method'=>'driverUploadTrackJob',
                    'params'=>$track_params
                );
                Queue::model()->putin($task,'position');
                $positions_arr = array();
            }
            $curr_status = $de_json[$i]['status'];
            if($curr_status == 1) {
                $pos_status =  $curr_status;
            }

            $lat = $de_json[$i]['lat'];
            $lng = $de_json[$i]['lng'];
            $gps_type = $de_json[$i]['gps_type'];
            $gps_time = $de_json[$i]['gps_time'];
            $provider = isset($de_json[$i]['provider'])? $de_json[$i]['provider']:'';
            $accuracy = isset($de_json[$i]['accuracy'])? $de_json[$i]['accuracy']:'';
            $pos_milli_timestamp = isset($de_json[$i]['milliTimestamp'])? 
                $de_json[$i]['milliTimestamp']/1000:$gps_time;
            $positions_arr[] = array(
                'lat' => $lat,
                'lng' => $lng,
                'gps_type' => $gps_type,
                'gps_time' => $gps_time,
                'provider' => $provider,
                'accuracy' => $accuracy,
                'status' => $curr_status,
                'milli_timestamp' => $pos_milli_timestamp
            );
        }

        if(count($positions_arr) > 0) {
            $track_params['positions'] = $positions_arr;
            $track_params['status']    = $pos_status;
            $task=array(
                'class' => __CLASS__,
                'method'=>'driverUploadTrackJob',
                'params'=>$track_params
            );
            Queue::model()->putin($task,'position');
        }
    }

    /**
     * Batch update order postions
     * @param array $params
     * QueueProcess : driver_batch_position_track
     */
    public function driverUploadTrackJob($params) {
        // Save busy driver position for current order
        // Get order id first
        if(!isset($params['driver_id'])
            || !isset($params['positions'])
            || !isset($params['status'])) {
            return false;
        }

        if($params['status'] == 1) { // Busy
            // Get order id
            $order_info = RDriverPosition::model()->getCurrentOrder($params['driver_id']);
            if(empty($order_info)) {
                return false;
            }
            $order_id = $order_info['order_id'];
            if(empty($order_id)) {
                return false;
            }
            $order_state = $order_info['order_state'];

            // Save the position
            // Assure the type of GPS
            $last_lat = 1.0;
            $last_lng = 1.0;

            //batch filter positions
            //$upload_pos = $params['positions'];
            $datas = array('driver_id' => $params['driver_id'],
                'order_id' => $order_id,
                'positions' => $params['positions']);
            $upload_pos = RDriverPosition::model()->validPositions($datas);

            $insert_positions =  array();
            foreach($upload_pos as $loop_pos) {
            $gps_type = $loop_pos['gps_type'];
            $lat = $loop_pos['lat'];
            $lng = $loop_pos['lng'];
            $provider = $loop_pos['provider'];
            $accuracy = $loop_pos['accuracy'];
            $status = $loop_pos['status'];
            if($status != 1) {
                continue;
            }

            if($lat <= 1.0 || $lng <= 1.0) {
                continue;
            }

            /*
            if(!empty($accuracy) && $accuracy > 300) {
                continue;
            }
            */
            // add by lifulong@edaijia 2014.12.09

            /*
            $data = array('driver_id' => $params['driver_id'],
                'order_id' => $order_id,
                'position' => $loop_pos);
            if(!RDriverPosition::model()->validPosition($data)) {
                EdjLog::info("driver_batch_position_track:\tdriver_id".$params['driver_id']."\torder_id:".$order_id."\tlat:".$loop_pos['lat']."\tlng:".$loop_pos['lng']."\tnonvalid");
                continue;
            } else {
                EdjLog::info("driver_batch_position_track:\tdriver_id".$params['driver_id']."\torder_id:".$order_id."\tlat:".$loop_pos['lat']."\tlng:".$loop_pos['lng']."\tvalid");
            }
            */

            if($gps_type != 'baidu') {
                $gps=GPS::model()->convert_only(array(
                    'longitude'=>$lng,
                    'latitude'=>$lat,
                    ), $gps_type);
                $lat = $gps['baidu_lat'];
                $lng = $gps['baidu_lng'];
            }
            $insert_positions[] = array(
                'lat' => $lat,
                'lng' => $lng);
            $last_lat = $lat;
            $last_lng = $lng;
            }

            if(count($insert_positions) > 0) {
            // Save the position
            $datas = array('driver_id' => $params['driver_id'],
                'order_id' => $order_id,
                'order_state' => $order_state,
                'positions' => $insert_positions);
            RDriverPosition::model()->insertBatchPosition($datas);
            RDriverPosition::model()->updateCurrentOrderPos($order_id,
                array('lat' => $last_lat,
                    'lng' => $last_lng));
            }

        }
        // 什么也不做，延时存储db
        // call driver_position_track_ope
        //EdjLog::info("delay save");
        //var_dump($params);
    }
    /**
     * 添加
     * @param $params
     * dirver_id
     * imei
     * status
     * latitude
     * longitude
     * street
     */
    public function addTrack($params)
    {
        $driverTrack = new DriverTrack();
        $driverTrack->attributes = $params;
        return $driverTrack->insert();
    }
}
