<?php
/**
 * 司机状态service
 * Created by PhpStorm.
 * User: zhaoyingshuang
 * Date: 15-4-30
 * Time: 下午3:16
 */

class DriverStatusService {
    private static $instance;

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new DriverStatusService();
        }
        return self::$instance;
    }

    function __get($name) {
        return $this->$name;
    }

    function __set($name, $value){
        $this->$name = $value;
    }

    /*---------------------------司机实时状态逻辑-----------------------------------------*/

    /**
     * @param $driver DriverStatus
     * @param $param array
     * @return 0 成功,
     *         -1 时间戳错误,
     *         -2 状态无变化,
     */
    public function driverUploadStatus($driver, $params) {
        $driver_id = $driver->driver_id;
        $status    = isset($params['status']) ? $params['status'] : '2';//没传就默认下班
        $app_ver   = isset($params['app_ver']) ? $params['app_ver'] : '';
        $force_update = isset($params['force_update']) ? $params['force_update'] : '';
        $gps_time = isset($params['gps_timestamp']) ? $params['gps_timestamp'] : time();

        //司机端 v2.2.6 新增字段 提高时间精度
        $milli_timestamp = isset($params['milli_timestamp']) ? $params['milli_timestamp']/1000 : $gps_time;

        if($status == $driver->status && $force_update != 1) {
            EdjLog::info('UploadStatusNoChange'.'|'.$driver_id);
            return -2;
        }

        $last_upload_status = @$driver->last_upload_status;
        if(floatval($last_upload_status) <= $milli_timestamp
            && $milli_timestamp < floatval(time() + 3600) ) {

            $driver->last_upload_status = $milli_timestamp;
            $task=array(
                'class' => __CLASS__,
                'method'=>'driverUploadStatusJob',
                'params'=>array(
                    'id'          => $driver->id,
                    'driver_id'   => $driver_id,
                    'status'      => $status,
                    'time'        => time(),
                    'last_status' => $driver->status,//记录上次状态
                    'app_ver'     => $app_ver,
                ),
            );
            Queue::model()->putin($task,'current_status');

            // 更新redis状态
            $driver->status = $status;
        }
        else {
            EdjLog::error('UploadStatusTimeError'.'|'.$driver_id.'|'.$milli_timestamp.'|'.$last_upload_status);
            return -1;
        }
    }

    /*
     * QueueProcess : driver_current_status
     *
     */
    public function driverUploadStatusJob($params) {
        //更新状态空闲校验司机是否已被屏蔽
        if($params['status'] == 0) {
            $driver = DriverStatus::model()->get($params['driver_id']);
            if($driver->mark != 0) {
                $driver->status = 2;  //强制将司机下班
                if($driver->mark == 1  && $driver->block_at == 1) {
                    $driver_id = $driver->driver_id;
                    $phone     = $driver->phone;
                    $content   = "尊敬的".$driver_id."师傅，您已登录客户端，目前信息费不足，无法收到系统订单，请在客户端在线充值，充值后即可以收到系统派单。";
                    Sms::SendSMS($phone , $content);
                }
                return true;
            }
        }

        //更新mongo 司机状态
        DriverGPS::model()->status($params['driver_id'], $params['status']);

        //添加task队列,回写库
        $app_ver = isset($params['app_ver']) ? $params['app_ver'] : '';
        $task=array(
            'class' => __CLASS__,
            'method'=>'saveDriverStatusIntoDB',
            'params'=>array(
                'id'=>$params['id'],
                'status'=> $params['status'],
                'app_ver' => $app_ver,
            ),
        );
        Queue::model()->putin($task,'status');

        //记录司机在线时间，改成队列
        $online_task=array(
            'method'=>'driver_online_log',
            'params'=>$params,
        );
        Queue::model()->putin($online_task,'dumplog');
    }

    /**
     * 司机状态放db
     * @author sunhongjing 2013-07-11
     *
     * @param unknown_type $params
     * @return bool
     */
    public function saveDriverStatusIntoDB($params)
    {
        if( empty($params) || empty($params['id']) || !isset($params['status']) ){
            return false;
        }
        $id      = $params['id'];
        $status  = $params['status'];
        $app_ver = isset($params['app_ver']) ? $params['app_ver'] : '';

        $ret = DriverPosition::model()->updateStatus($id, $status, $app_ver);

        return true;
    }

    public function getByToken($token) {
        return DriverStatus::model()->getByToken($token);
    }

    public function batch_get($driver_ids) {
        return DriverStatus::model()->batch_get($driver_ids);
    }

    public function getItem($driver_id, $name) {
        return DriverStatus::model()->getItem($driver_id, $name);
    }

    public function getByPhone($phone) {
        return DriverStatus::model()->getByPhone($phone);
    }

    public function get($driver_id) {
        return DriverStatus::model()->get($driver_id);
    }

    public function app_ver($driver_id) {
        return DriverStatus::model()->app_ver($driver_id);
    }

    public function info($driver_id) {
        return DriverStatus::model()->info($driver_id);
    }

    public function position($driver_id) {
        return DriverStatus::model()->position($driver_id);
    }

    public function service($driver_id) {
        return DriverStatus::model()->service($driver_id);
    }

    public function getCitySetting($city_id, $name) {
        return DriverStatus::model()->getCitySetting($city_id, $name);
    }


    public function getDriverManagerToken($token) {
        return DriverStatus::model()->getDriverManagerToken($token);
    }

    public function single_get($key) {
        return DriverStatus::model()->single_get($key);
    }

    public function single_set($key, $value, $expire) {
        return DriverStatus::model()->single_set($key, $value, $expire);
    }

    public function set_newpush_client($driver_id, $client_id) {
        return DriverStatus::model()->set_newpush_client($driver_id, $client_id);
    }

    public function set_client_id_app_ver($driver_id, $client_id, $app_ver) {
        return DriverStatus::model()->set_client_id_app_ver($driver_id, $client_id, $app_ver);
    }

    public function getCrownVal($city_id) {
        return DriverStatus::model()->getCrownVal($city_id);
    }

    public function putBlacklistPool($phone, $data) {
        return DriverStatus::model()->putBlacklistPool($phone, $data);
    }

    public function getByimei($imei) {
        return DriverStatus::model()->getByimei($imei);
    }
}
