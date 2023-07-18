<?php
/**
 * 司机实时经纬坐标处理逻辑
 */
class DriverPositionService
{
    private $redis = null;
    private static $instance;

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new DriverPositionService();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->redis = RedisCache02::model()->getRedis();
    }

    function __get($name) {
        return $this->$name;
    }

    function __set($name, $value){
        $this->$name = $value;
    }

    /*---------------------------司机实时坐标逻辑-----------------------------------------*/

    /**
     * @param $driver DriverStatus
     * @param $param array
     * @return 0 成功,
     *         -1 坐标错误,
     */
    public function driverUploadPosition($driver, $params) {
        $driver_id = $driver->driver_id;
        $gps_type  = (isset($params['gps_type'])&&!empty($params['gps_type'])) ? trim($params['gps_type']) : 'wgs84';
        $lng       = isset($params['longitude']) ? $params['longitude'] : '1';
        $lat       = isset($params['latitude']) ? $params['latitude'] : '1';
        $log_time  = isset($params['log_time']) ? $params['log_time'] : date('YmdHis');
        $gps_time  = isset($params['gps_timestamp']) ? $params['gps_timestamp'] : time();

        // 坐标不正确
        if(intval($lng) + intval($lat) <= 10) {
            EdjLog::info("Error lng and lat|$lng|$lat|$driver_id|$gps_time");
            return -1;
        }

        $driver->last_upload_position = time();

        $position['gps_timestamp'] = $gps_time;
        $position['longitude']     = $lng;
        $position['latitude']      = $lat;
        $position['driver_id']     = $driver_id;
        $position['gps_type']      = $gps_type;
        $position['log_time']      = $log_time;

        //添加到更新司机当前位置的队列
        $task=array(
            'class' => "DriverPositionService",
            'method'=>'driverUploadPositionJob',
            'params'=>$position
        );
        Queue::model()->putin($task,'current');
    }

    /*
     * QueueProcess : driver_current_pos
     *
     */
    public function driverUploadPositionJob($params) {
        $driver_id=$params['driver_id'];
        $driver=DriverStatus::model()->get($driver_id);
        $app_ver=DriverStatus::model()->app_ver($driver_id);
        if ($driver) {
            //进入唯一列表，进行时间判断
            $params = array(
                'obj'=>$driver,
                'id'=>$driver->id,
                'driver_id'=>$driver_id,
                'service_type'=>$driver->service_type ? $driver->service_type : Driver::SERVICE_TYPE_FOR_DAIJIA,
                'app_ver'=>$app_ver,
                'gps_position'=>array(
                    'longitude'=>$params['longitude'],
                    'latitude'=>$params['latitude']
                ),
                'gps_type'=>$params['gps_type'],
                'gps_timestamp'=>$params['gps_timestamp']
            );
            EdjLog::info('driver_id='.$driver_id.',service_type='.$params['service_type'].'app_ver='.$app_ver.'进入唯一列表更新位置job ok');

            //坐标存入唯一列表 redis set
            $this->positionUniqueList('LAST_POSITION', $driver_id, $params);
        }
        else{
            EdjLog::info("get status for driver $driver_id fail");
        }
    }

    /**
     * 存储位置唯一列表
     * @param string $prefix  前缀
     * @param string $pk      主键
     * @param array $params  参数
     */
    public function positionUniqueList($prefix, $pk, $params, $useIndex=true) {
        $hashKey = $prefix."_".$pk;
        if ($useIndex==true)
            $sKey = $this->getPositionIndexName($prefix, $pk);
        else
            $sKey = $prefix."_LIST";

        $last_gps_timestamp = intval($this->redis->hget($hashKey, 'gps_timestamp'));
        $gps_timestamp = $params['gps_timestamp'];

        //增加判断缓存中记录的时间比当前时间大60秒，也更新 add by sunhongjing 2013-09-12
        EdjLog::info("gps_timestamp=".$gps_timestamp."|last_gps_timestamp="
                .$last_gps_timestamp."|time=".time());
        if ( $gps_timestamp > $last_gps_timestamp     //上传时间大于 上次上传时间
                || $last_gps_timestamp <= 0           //上次上传时间小于 0 可能是新师傅
                || ($last_gps_timestamp - time() )>60 //上次上传时间大于当前时间，可能是gps时间不准
                ) {

            // 更新redis司机信息中坐标
            $gps=GPS::model()->convert_only($params['gps_position'], $params['gps_type']);
            $gps['street'] = '';
            $driver = $params['obj'];
            $driver->position = $gps;


            $params['gps'] = $gps;
            //放到唯 一list
            $this->redis->hset($hashKey, 'gps_timestamp', $params['gps_timestamp']);
            $this->redis->hset($hashKey, 'data', json_encode($params));
            $this->redis->sadd($sKey, $pk);
        }else {
            //记录抛弃日志
            $log_format = "MISSPOSITION|%s|%s|%s|%s";
            $driver_id = isset($params['driver_id']) ? $params['driver_id'] : "";
            $gps_time = date("Y-m-d H:i:s", $gps_timestamp);
            $last_gps_time = date("Y-m-d H:i:s", $last_gps_timestamp);
            $log = sprintf($log_format, $driver_id, $gps_timestamp, $gps_time, $last_gps_timestamp, $last_gps_time);
            EdjLog::info($log);

            /*
            //添加task队列
            $task=array(
                'method'=>'driver_position_miss',
                'params'=>array(
                    'driver_id'=> $params['driver_id'],
                    'gps_timestamp' => $params['gps_timestamp'],
                    'gps_time'=> date("Y-m-d H:i:s", $params['gps_timestamp']),
                    'last_gps_timestamp' => $last_gps_timestamp,
                    'last_gps_time'=>date("Y-m-d H:i:s", $last_gps_timestamp),
                )
            );
            Queue::model()->putin($task, 'position_miss');
            */
        }
    }

    /**
     * 处理位置唯一列表
     * @param string $index 区域索引名称
     * queueCommand : actionPositionUniqueList
     */
    public function processPositionUniqueList($index) {

        $prefix = 'LAST_POSITION';
        $sKey = $prefix."_".strtoupper($index);

        while(true) {
            $lists = $this->getPositionUniqueIndex($sKey);
            if (!is_array($lists) || count($lists) == 0) {
                $rand = rand(20, 100);
                usleep(intval($rand));
                continue;
            }

            EdjLog::info('['.date("Y-m-d H:i:s")."] begin process");

            foreach($lists as $list) {
                $hashKey = $prefix."_".$list;
                $data = $this->getPositionData($hashKey, 'data');
                $params = false;
                if ($data) $params = @json_decode($data, true);

                if (isset($params) && isset($params['gps'])) {
                    //更新mongo
                    DriverGPS::model()->update($params['id'], $params['driver_id'], array(
                        'lng'=>$params['gps']['baidu_lng'],
                        'lat'=>$params['gps']['baidu_lat']
                    ),$params['service_type'],$params['app_ver']);

                    EdjLog::info('UPDATE DriverGPS '. $params['driver_id'] .' LNG: '. $params['gps']['baidu_lng'] .' LAT: '. $params['gps']['baidu_lat'] . ' SERVICE_TYPE: ' .$params['service_type']. ' APP_VER: ' .$params['app_ver']);

                    //添加task队列,回写库
                    $task=array(
                        'class' => __CLASS__,
                        'method'=>'saveDriverPositionIntoDB',
                        'params'=>array(
                            'driver_id'=>$params['driver_id'],
                            'gps'=>$params['gps'],
                            'log_time'=>date("YmdHis", $params['gps_timestamp']),
                        ),
                    );
                    Queue::model()->putin($task,'default');
                }
            }

            EdjLog::info('['.date("Y-m-d H:i:s")."] end process");
        }
    }

    /**
     * 司机位置放db
     * @author sunhongjing 2013-07-11
     *
     * @param unknown_type $params
     * @return bool
     */
    public function saveDriverPositionIntoDB($params) {
        if( empty($params) || empty($params['driver_id']) || empty($params['gps']) ){
            return false;
        }
        $driver_id = $params['driver_id'];
        $gps       = $params['gps'];

        $ret = DriverPosition::model()->updatePosition($driver_id, $gps, null, $params['log_time']);
        return true;
    }

    /**
     * 取得位置 索引
     * @author syang on 2013/8/29
     * @param string $sKey 索引KEY
     * @return mixed  FALASE or array
     */
    public function getPositionUniqueIndex($sKey) {
        $lists = @$this->redis->smembers($sKey);
        if (is_array($lists)) {
            @$this->redis->del($sKey);
        }
        return $lists;
    }

    /**
     * 取得位置数据
     * @author syang on 2013/8/29
     * @param string $hashKey hash Key
     * @param string $field   字段名
     */
    public function getPositionData($hashKey, $field) {
        return $this->redis->hget($hashKey, $field);
    }

    /**
     * 按区域，划分索引
     * @param unknown $prefix
     * @param unknown $pk
     * @return string
     */
    private function getPositionIndexName($prefix, $pk) {
        $indexs = array(
            'HUA_BEI'=>array( //北京， 天津
                'BJ', 'TJ',
            ),
            'HUA_DONG'=>array( //上海，南京，杭州，济南
                'SH', 'NJ', 'HZ', 'JN'
            ),
            'HUA_NAN'=>array( //长沙，广州， 深圳
                'CS', 'GZ', 'SZ'
            ),
            /*
            'HUA_ZHONG'=>array( //郑州， 武汉
                'ZZ', 'WH',
            ),
            'XI_NAN'=>array( //重庆， 城都
                'CQ', 'CD'
            ),
            'XI_BEI'=>array( //西安
                'XA'
            ),*/
        );

        $p_prefix = strtoupper(substr($pk, 0, 2));

        $indexName = "";
        foreach($indexs as $name => $index) {
            if (in_array($p_prefix, $index)) $indexName = $prefix."_".$name;
        }

        if ($indexName == "") $indexName = $prefix."_QI_TA";

        return $indexName;
    }
}
