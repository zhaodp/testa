<?php
/**
 * 司机 实时坐标,状态,轨迹上传API入口服务
 */
class DriverRealTimeInfoApiService
{
    const DELAY = 5;

    private $redis = null;
    private static $instance;

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new DriverRealTimeInfoApiService();
        }
        return self::$instance;
    }

    function __get($name) {
        return $this->$name;
    }

    function __set($name, $value){
        $this->$name = $value;
    }


    /**
     *  司机端手动切换状态,上传坐标
     *  API : driver.upload.position
     */
    public function driverManualUpload($params) {
        $driver_id = isset($params['user']) ? $params['user'] : '';
        $status    = isset($params['status']) ? $params['status'] : '2';//没传就默认下班
        $token     = isset($params['token']) ? $params['token'] : '';

        if( empty($token) ){
            EdjLog::info('token is empty for driver '.$driver_id);
            $ret=array(
                    'code'=>2,
                    'delay'=> self::DELAY,
                    'message'=>'参数错误'
            );
            return $ret;
        }

        $driver = DriverStatus::model()->getByToken($token);

        if ($driver) {
            //liufugang/20150507/司机端做题支持:如果答题司机不答题,就永远工作.但司机要正常下班
            if($driver->status != DriverStatus::STATUS_DRIVING && $status != DriverStatus::STATUS_OFFDUTY){
                $ret = DriverQuizLogic::getInstance()->driverNeedQuiz($driver);
                if($ret){
                    return $ret;
                }
            }
            //add by aiguoxin 司机没有领取工服，不能共工作,判断条件，到时再修改
            if($status == DriverPosition::POSITION_IDLE){
                $canWork = DriverRecruitment::model()->canWork($driver_id);
                if($canWork && $canWork['code']){
                    $ret=array(
                        'code'=>3,
                        'message'=>$canWork['message']
                    );
                    return $ret;
                }
            }

            //小于间隔不处理,直接返回。
            $last_upload_position = @$driver->last_upload_position;
            if(abs(time() - $last_upload_position) < self::DELAY) {
                $ret=array(
                    'code'=>0,
                    'delay'=>self::DELAY,
                    'message'=>'上报频率高'
                );
                return $ret;
            }

            //更新坐标
            DriverPositionService::getInstance()->driverUploadPosition($driver, $params);

            //更新状态
            DriverStatusService::getInstance()->driverUploadStatus($driver, $params);

            $ret=array(
                'code'=>0,
                'delay'=>self::DELAY,
                'message'=>'上传成功'
            );
            return $ret;
        } else {
            EdjLog::info('driver '.$driver_id.' should relogin');
            $ret=array(
                'code'=>1,
                'delay'=>self::DELAY,
                'message'=>'请重新登录'
            );
            return $ret;
        }
    }

    /**
     *  司机端自动切换状态,上传坐标
     *  API : driver.upload.batchposition
     */
    public function driverAutoUpload($params) {
        $status = isset($params['status']) ? $params['status'] : '2';//没传就默认下班
        $token = isset($params['token']) ? $params['token'] : '';
        $positions = isset($params['positions']) ? $params['positions'] : '';

        if( empty($token) || empty($positions)){
            $ret=array(
                'code'=>2,
                'delay'=>self::DELAY,
                'message'=>'参数错误'
            );
            return $ret;
        }

        $driver = DriverStatus::model()->getByToken($token);
        if ($driver) { // Empty driver
            //解析url中引号
            $positions = htmlspecialchars_decode($positions, ENT_QUOTES);
            //解析 positions
            $params['positions'] = json_decode($positions, TRUE);

            //司机端自动上传参数中没有longitude和latitude字段,以positions中最后一组坐标作为lng和lat
            $params['longitude'] = 1.0;
            $params['latitude']  = 1.0;
            $last = end($params['positions']);
            if($last) {
                $params['longitude'] = $last['lng'];
                $params['latitude']  = $last['lat'];
                $params['gps_type']  = $last['gps_type'];
                $params['gps_time']  = $last['gps_time'];
            }

            //处理司机轨迹
            DriverTrackService::getInstance()->driverUploadTrack($driver, $params);

            //小于间隔不处理,直接返回。
            $last_upload_position = @$driver->last_upload_position;
            if(abs(time() - $last_upload_position) < self::DELAY) {
                $ret=array(
                    'code'=>0,
                    'delay'=>self::DELAY,
                    'message'=>'上报频率高'
                );
                return $ret;
            }

            //更新坐标
            DriverPositionService::getInstance()->driverUploadPosition($driver, $params);

            //更新状态
            DriverStatusService::getInstance()->driverUploadStatus($driver, $params);

            $ret=array(
                'code'=>0,
                'delay'=>self::DELAY,
                'message'=>'上传成功'
            );
        } else {  // empty driver
            $ret=array(
                'code'=>1,
                'delay'=>self::DELAY,
                'message'=>'请重新登录'
            );
        }

        return $ret;
    }
}
