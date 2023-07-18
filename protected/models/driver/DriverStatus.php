<?php
/**
 * 管理driver的redis存储
 * @var 2013-04-22
 * @update 2012-05-20
 * @author dayuer
 *
 * @property Redis $redis
 * @property integer $id
 * @property integer $status
 * @property integer $heartbeat
 * @property integer $city_id
 * @property integer $android
 * @property integer $mark
 * @property integer $goback
 * @property integer $idle_time 空闲时长
 * @property integer $recommand 皇冠司机
 * @property string $token
 * @property string $driver_id
 * @property string $udid
 * @property string $client_id
 * @property string $phone
 * @property array $account
 * @property array $info
 * @property array $position
 * @property array $service
 *
 * $driver = DriverStatus::model()->get($driver_id);
 * echo $driver->phone;
 * echo $driver->id;
 * var_dump($driver->service);
 *
 */
class DriverStatus extends CRedis {
    public $host='cache02n.edaijia.cn'; //10.132.17.218
    public $port=6379;
    public $password='k74FkBwb7252FsbNk2M7';
    protected static $_models=array();
    protected static $_online;
    protected static $_driver_id=null;
    private $_driver_online_key			  = "DRIVER_ONLINE_KEY_";
    private $_driver_city_setting_key     = "DRIVER_CITY_SETTING_KEY_";

    const STATUS_ONDUTY  = 0;  //上班
    const STATUS_DRIVING = 1;  //接单中
    const STATUS_OFFDUTY = 2;  //下班


    public static function model($className=__CLASS__) {
        $model=null;
        if (isset(self::$_models[$className]))
            $model=self::$_models[$className];
        else {
            $model=self::$_models[$className]=new $className(null);
        }
        return $model;
    }

    public function updateAccount($driver_id, $account){
        $account = json_encode($account);
        return $this->redis->hSet($driver_id, 'account', $account);
    }
    public function __set($name, $value) {
        if (self::$_driver_id!==null) {
            $this->_attributes[$name]=$value;
            if (is_array($value)) {
                $value=json_encode($value);
            }
            //特殊属性修改后的同步操作
            switch ($name) {
                case 'mark' :
                    if ($value!=0) {
                        self::offline(self::$_driver_id);
                    }
                    break;
                case 'status' :
                    if ($value==0||$value==1) {
                        self::online(self::$_driver_id);
                    } else {
                        self::offline(self::$_driver_id);
                    }
                    break;
                case 'position' :
                    $this->heartbeat=time();
                    break;
                case 'token' :
                    //保存token=>driver_id对应表,有效期1小时
                    //2014-10-09 qiujianping 修改有效期为2小时
                    $this->redis->set('DRIVER_TOKEN_'.$value, self::$_driver_id);
                    $this->redis->expire('DRIVER_TOKEN_'.$value, 7200);
                    break;
                case 'heartbeat' :
                    //修改token过期时间
                    //2014-10-09 qiujianping 修改有效期为2小时
                    $this->redis->expire('DRIVER_TOKEN_'.$this->token, 7200);
                    //$this->redis->hSet('onlines', self::$_driver_id, $value);

                    //司机onlines迁移到Redis HA Proxy
                    $index = 'index_'.substr(self::$_driver_id, -1).'_onlines';
                    RedisHAProxy::model()->redis->hset($index, self::$_driver_id, $value);

                    //每个心跳周期增加一次空闲计数，服务中则清零
                    if ($this->status==0) {
                        $this->redis->hIncrBy(self::$_driver_id, 'idle_time', 1);
                    } else {
                        $this->redis->hSet(self::$_driver_id, 'idle_time', 0);
                    }
                    break;
            }
            $this->redis->hSet(self::$_driver_id, $name, $value);
            return true;
        } else {
            return false;
        }
    }

    /**
     *	@author aiguoxin
     *	设置valve到key中
     */
    public function single_set($key,$value,$expire){
        $value = json_encode($value);
        $this->redis->set($key,$value,$expire);
        return true;
    }

    /**
     *	@author aiguoxin
     *	获取value
     */
    public function single_get($key){
        $value = $this->redis->get($key);
        if($value){
            $value = json_decode($value, true);
        }
        return $value;
    }

    /**
     * @author wangjian
     * redis pipeline 批量返回司机信息
     * @param array $driver_ids
     */
    public function batch_get($driver_ids) {
        $data = array();
        if(empty($driver_ids)) {
            return $data;
        }
        $driver_id_count = count($driver_ids);

        $params = array();
        foreach($driver_ids as $driver_id){
            array_push($params, array($driver_id));
        }
        $result = $this->pipeline('hGetAll', $params);

        if(count($result) != $driver_id_count) {
            EdjLog::warning('redispipeline获取结果出错|'.$driver_id_count.'|'.count($result).'|end' , 'console');
            return $data;
        }

        $need_to_load = array();
        for($i=0; $i<$driver_id_count; $i++) {
            $tmp = $driver_ids[$i];
            // 司机信息为空 重新加载
            if( empty($result[$i]) ) {
                EdjLog::warning('redis司机信息为空重新加载|'.$tmp.'|end' , 'console');
                self::load($tmp);
                $result[$i] = $this->redis->hGetAll($tmp);
            }
            $data[$tmp] = $result[$i];
            $data[$tmp]['service'] = @json_decode($data[$tmp]['service'], true);
            $data[$tmp]['position'] = @json_decode($data[$tmp]['position'], true);
            $data[$tmp]['info'] = @json_decode($data[$tmp]['info'], true);
            $data[$tmp]['account'] = @json_decode($data[$tmp]['account'], true);
            $data[$tmp]['recommand'] = @json_decode($data[$tmp]['recommand'], true);
        }

        return $data;
    }

    public function set_app_ver($driver_id, $app_ver)
    {
        $key_app_ver = 'DRIVER_APP_VERSION';
        $this->redis->hset($key_app_ver, $driver_id, $app_ver);

    }

    public function set_newpush_client($driver_id, $newpush_client_id)
    {
        $key = 'DRIVER_CLIENT_NEWPUSH_MAP';

        $rv = $this->redis->hget($driver_id, "client_id");
        // driver_id 不存在测试 bool(false)
        // client_id 不存在测试 bool(false)
        // 存在测试 返回client_id
        // 存在为空测试 string(0) ""
        //var_dump($rv);
        if ($rv === false) {
            $rv = '';
        }

        if ($rv != "") {
            $this->redis->hset($key, $rv, $newpush_client_id);
        }

    }

    public function newpush_client($getui_client_id)
    {
        $key = 'DRIVER_CLIENT_NEWPUSH_MAP';
        if ($getui_client_id == null) {
            return '';
        }

        $rv = $this->redis->hget($key, $getui_client_id);
        // DRIVER_CLIENT_NEWPUSH_MAP 不存在返回 bool(false)
        // DRIVER_CLIENT_NEWPUSH_MAP 对应client_id不存在返回 bool(false)
        //var_dump($rv);
        if ($rv === false) {
            $rv = '';

        }

        return $rv;

    }


    // 设置client_id对应的司机版本
    public function set_client_id_app_ver($driver_id, $client_id, $app_ver)
    {
        $key_app_ver = 'DRIVER_APP_VERSION';
        $key_client_app_ver = 'DRIVER_CLIENT_APP_VERSION';
        $this->redis->hset($key_app_ver, $driver_id, $app_ver);
        $this->redis->hset($key_client_app_ver, $client_id, $app_ver);

    }

    public function app_client_ver($client_id)
    {
        $key_client_app_ver = 'DRIVER_CLIENT_APP_VERSION';
        if ($client_id == null) {
            return '';
        }

        $rv = $this->redis->hget($key_client_app_ver, $client_id);
        //var_dump($rv);
        if ($rv === false) {
            $driver = GetuiClient::model()->getClientIdInfo($client_id);
            //var_dump($driver);

            if ($driver && isset($driver['driver_id'])) {
                //var_dump($driver['driver_id']);
                $rv = self::app_ver($driver['driver_id']);
                $this->redis->hset($key_client_app_ver, $client_id, $rv);
            } else {
                $rv = '';
            }

        }


        return $rv;
    }


    // 获取司机端版本
    public function app_ver($driver_id)
    {
        $key_app_ver = 'DRIVER_APP_VERSION';
        if ($driver_id == null) {
            return '';
        }
        //工号转大写，有的地方使用小写工号，导致获取的内容不一致
        $driver_id = strtoupper($driver_id);
        $rv = $this->redis->hget($key_app_ver, $driver_id);
        if ($rv === false) {
            $driver = DriverStatus::model()->get($driver_id);
            //var_dump($driver);
            if ($driver) {
                $driver_position = DriverPosition::model()->getDriverPosition($driver->id);
                //var_dump($driver_position);

                if ($driver_position) {

                    $app_ver = $driver_position->app_ver;
                    $this->redis->hset($key_app_ver, $driver_id, $app_ver);
                    $rv = ($app_ver == null ? "" : $app_ver);

                } else {
                    $rv = "";
                }
            } else {
                $rv = "";
            }

        }

        return $rv;
    }

    /**
     * 返回司机的信息
     * @param string $driver_id
     */
    public function get($driver_id) {
        if ($driver_id!==null) {
            if (!$this->redis->exists($driver_id)) {
                self::load($driver_id);
            }
            self::$_driver_id=$driver_id;

            $this->_attributes=array_merge($this->_attributes, $this->redis->hGetAll($driver_id));

            $this->_attributes['service']=@json_decode($this->_attributes['service'], true);
            $this->_attributes['position']=@json_decode($this->_attributes['position'], true);
            //auto refresh driver's driving age,zhongfuhai comment at 2015/4/16
            $this->_attributes['info']=$this->refreshDrivingYear(@json_decode($this->_attributes['info'], true));
            $this->_attributes['account']=@json_decode($this->_attributes['account'], true);
            $this->_attributes['recommand']=@json_decode($this->_attributes['recommand'], true);

            return $this;
        }
    }



    /**
     * 返回全部在线司机工号
     */
    public function onlines() {
        return $onlines=$this->redis->hGetAll('onlines');
    }

    /**
     * 司机id散列 存入Redis HA Proxy
     * 返回全部在线司机工号
     */
    public function onlines_redishaproxy() {
        $onlines = array();
        for($i=0; $i<10; $i++) {
            $online_index = 'index_'.$i.'_onlines';
            $onlines_tmp = RedisHAProxy::model()->redis->hGetAll($online_index);
            $onlines = array_merge($onlines, $onlines_tmp);
        }

        return $onlines;
    }


    public function rebuild() {
        $offset=0;
        $pagesize=500;
        echo "starting \n";
        while(true) {
            $drivers=Yii::app()->db_readonly->createCommand()->select('id, user as driver_id,status')->from('t_driver d, t_driver_position p')->where('d.id = p.user_id')->offset($offset)->limit($pagesize)->queryAll();

            if ($drivers) {
                foreach($drivers as $driver) {
                    if ($driver['status']==0||$driver['status']==1) {
                        self::online($driver['driver_id']);
                    } else {
                        self::offline($driver['driver_id']);
                    }
                }
            } else {
                break;
            }
            $offset+=$pagesize;
            echo $offset."\n";
        }
    }

    /**
     * 设置司机的返程标示
     * @param string $driver_id
     * @param int $flag
     */
    public function goback($driver_id, $flag) {
        $driver=self::get($driver_id);
        if ($driver) {
            if ($flag!==null) {
                $driver->goback=$flag;
            }
            return $driver->goback;
        }
        return null;
    }

    /**
     * 个推失败后订单计数发消息
     * @author zhanglimin 2013-05-10
     * @param $key  格式: queue_id_driver_id
     * @param bool $flag
     * @return mixed
     */
    public function orderCount($key, $flag=false) {
        $key='QUEUE_DRIVER_'.$key;
        if ($flag) {
            if ($this->redis->exists($key)) {
                $this->redis->incr($key);
            } else {
                //初始化为1,10分钟过期
                $this->redis->set($key, 1);
                $this->redis->expire($key, 600);
            }
        }
        return $this->redis->get($key);
    }

    /**
     * TODO 转移到单独的黑名单类
     * 将电话放入预备黑名单缓存池
     *
     * $data['type'];
     * $data['phone'];
     * $data['callTime']
     * $data['talkTime'];
     * $data['driver_id'];
     * $data['status'];
     *
     * @param unknown_type $data
     */
    public function putBlacklistPool($phone, $data) {
        $ret=false;
        if (empty($phone)||empty($data)) {
            return $ret;
        }

        //白名单不生成
        if(CustomerWhiteList::model()->in_whitelist($phone)) {
            return $ret;
        }

        //下班的电话不存
        if (2==$data['status']) {
            return $ret;
        }
        //不是北京的不存
        if (false===stripos($data['driver_id'], "BJ")) {
            return $ret;
        }
        //不是呼入，不存
        if (0!=$data['type']) {
            return $ret;
        }
        //黑车师傅的电话不存
        $black_car_phone = Common::getBlackCarPhones();
        if( in_array($phone,$black_car_phone) ){
            return $ret;
        }

        $value=$this->redis->hGet($phone, 'crank_call');

        $params=array();

        if ($value) {
            $value=json_decode($value);
            $value=(array)$value;

            $params=$value;
            $params[]=$data['driver_id'];
        } else {
            $params[]=$data['driver_id'];
        }

        $ret=$this->redis->hSet($phone, 'crank_call', json_encode($params));
        $this->redis->expire($phone, 600); //有效期10分钟


        //放黑名单，推送消息
        if (4<=count($params)) {
            $task=array(
                'method'=>'crank_call_to_blacklist',
                'params'=>array(
                    'driver_ids'=>$params,
                    'phone'=>$phone
                )
            );
            Queue::model()->task($task);
        }

        return count($params);
    }

    /**
     * TODO 转移到单独的黑名单类
     * @param unknown $phone
     * @return mixed
     */
    public function getBlacklist($phone) {
        $ret=$this->redis->hGet($phone, 'crank_call');
        return json_decode($ret);
    }

    /**
     * 删除司机的记录
     */
    public function delete($driver_id) {
        if ($this->redis->exists($driver_id)) {
            $this->redis->delete($driver_id);
        }
    }

    /**
     * 查找IMEI对应的司机信息
     * @param string $imei
     * @return array|NULL
     */
    public function getByimei($imei) {
        $driver_id=$this->redis->get('driver:imei:'.$imei);
        if ($driver_id) {
            return self::get($driver_id);
        } else {
            $driver_profile=Driver::getProfileByImei($imei);
            if ($driver_profile) {
                return self::get($driver_profile->user);
            }
        }
        return null;
    }

    /**
     * 用token查询司机信息
     * @param string $token
     */
    public function getByToken($token) {
        $token_key='DRIVER_TOKEN_'.$token;
        if ($this->redis->exists($token_key)) {
            $driver_id=$this->redis->get($token_key);
            return self::get($driver_id);
        }
        return null;
    }

    /**
     * 清除在线列表里心跳超期和状态为下班的司机(逻辑)
     **/
    public function kickoff($onlines) {
        foreach ( $onlines as $driver_id => $timestamp ) {
            $driver = self::get ( $driver_id );

            $time = time();
            $old_status = $driver->status;

            if ($old_status == 2 || ($time - $driver->heartbeat) > 300) {
                $status = 2;
                EdjLog::info ( 'KickOff|' . $driver_id . '|' . $old_status . '|' . $driver->heartbeat . '|' . ($time - $driver->heartbeat) );

                // 更新redis
                $driver->status = $status;

                // 更新mongo
                DriverGPS::model ()->status ( $driver_id, $status );

                // 更新数据库
                DriverPosition::model ()->updateStatus ( $driver->id, $status );
                self::offline ( $driver_id );

                // yangzhi 2014-12-23 非下班状态, 向手机端发送唤醒指令
                if ($old_status != 2) {
                    $getui_id = $driver->client_id;

                    if ($getui_id != null) {
                        $message = array(
                            'type' => 'driver_status_sync',
                            'content' => array(
                                'driver_id' => $driver_id,
                                'status' => $old_status
                            )
                        );

                        EdjLog::info ("push driver sync message: " . json_encode($message));

                        //add by yangzhi
                        EPush::set_message_type($message, "driver_status_sync");
                        EPush::model("driver")->send($getui_id, $message);
                    }
                }

                // aiguoxin 2014-08-25 计算司机在线时间,只处理超时
                if (($time - $driver->heartbeat) > 300) {
                    $data = array (
                        'driver_id' => $driver_id,
                        'off_line_time' => time ()
                    );

                    // 添加task队列更新数据库
                    $task = array (
                        'method' => 'compute_driver_online',
                        'params' => $data
                    );
                    Queue::model ()->putin ( $task, 'status' );
                }
            }
        }
    }

    /**
     * 清除在线列表里心跳超期和状态为下班的司机
     */
    public function clean() {
        $onlines=$this->redis->hGetAll('onlines');
        EdjLog::info('在线人数|'.count($onlines));
        self::kickoff($onlines);
    }

    /**
     * 按司机工号的尾号
     * 清除在线列表里心跳超期和状态为下班的司机
     */
    public function clean_redishaproxy($indexs=array()) {
        $onlines = array();
        $total = 0;

        if(!empty($indexs)) {
            EdjLog::info(__METHOD__."|clean by index|".join(',', $indexs));
        }
        else {
            EdjLog::info(__METHOD__."|clean all indexs");
        }

        for($i=0; $i<10; $i++) {
            $online_index = 'index_'.$i.'_onlines';

            //区分尾号处理
            if(!empty($indexs) && !in_array($i, $indexs)) {
                continue;
            }

            $onlines = RedisHAProxy::model()->redis->hGetAll($online_index);
            self::kickoff($onlines);
            EdjLog::info('尾号'.$i.'|在线人数|'.count($onlines));
            $total += count($onlines);
        }

        EdjLog::info('总在线人数|'.$total);
    }

    /**
     * 校验司机的手机是否匹配imei和simcard
     * @param string $driver_id
     * @param string $imei
     * @param string $sim
     */
    public function validatePhone($driver_id, $imei, $sim) {
        $driver=self::get($driver_id);
        if ($driver) {
            if ($imei==$driver->info['imei']&&$sim==$driver->info['simcard']) {
                return true;
            }
            else{
                EdjLog::info("fail to validate phone driver_id $driver_id imei $imei sim $sim driver_imei ".
                    $driver->info['imei']." driver_simcard ".$driver->info['simcard']." driver is ".serialize($driver->info));
            }
        }
        else{
            EdjLog::info("validate driver phone fail  driver_id $driver_id imei $imei sim $sim");
        }
        return false;
    }

    /**
     * 重载全部的司机信息或者只更新指定的司机信息
     * @param string $driver_id
     */
    public function reload($driver_id=null,$updateToken=true) {
        if ($driver_id!==null) {
            //self::delete($driver_id);
            EdjLog::info("DriverStatsu#reload#start to load redis..................".$driver_id);
            self::load($driver_id,$updateToken);
            EdjLog::info("DriverStatsu#reload#load redis ok..................".$driver_id);

        } else {
            $this->redis->flushAll();
            $offset=0;
            $pagesize=500;
            echo "starting \n";
            while(true) {
                $criteria=new CDbCriteria(array(
                    'select'=>'user',
                    'offset'=>$offset,
                    'limit'=>$pagesize
                ));
                $drivers=Driver::model()->findAll($criteria);
                if ($drivers) {
                    foreach($drivers as $driver) {
                        self::load($driver['user'],$updateToken);
                    }
                } else {
                    break;
                }
                $offset+=$pagesize;
                echo $offset."\n";
            }
        }
    }

    public function loadDriver($driver_id=null){
        if ($driver_id!==null) {
            self::load($driver_id);
            return true;
        }else{
            return false;
        }
    }


    /**
     * 初始化司机的redis信息
     */
    private function load($driver_id,$updateToken=true) {
        EdjLog::info("DriverStatsu#load#start to load redis..................".$driver_id);

        self::$_driver_id=$driver_id;
        $driver=Driver::model()->getProfile($driver_id);
        if ($driver) {
            //上次上报时间
            $this->redis->hSet($driver_id, 'last_upload_position', 0);
            $this->redis->hSet($driver_id, 'last_upload_status', 0);
            $this->redis->hSet($driver_id, 'last_heartbeat', time());
            //司机基础信息
            $this->redis->hSet($driver_id, 'id', $driver->id);
            $this->redis->hSet($driver_id, 'city_id', $driver->city_id);
            $this->redis->hSet($driver_id, 'driver_id', $driver->user);
            $this->redis->hSet($driver_id, 'mark', $driver->mark);
            $this->redis->hSet($driver_id, 'block_at', $driver->block_at);
            $this->redis->hSet($driver_id, 'block_mt', $driver->block_mt);
            $this->redis->hSet($driver_id, 'phone', $driver->phone);
            $this->redis->hSet($driver_id, 'heartbeat', time());
            $this->redis->hSet($driver_id, 'service_type', $driver->service_type);
            EdjLog::info('driver_id='.$driver_id.',service_type='.$driver->service_type.'加入redis成功');
            //结伴返程标识 add by zhanglimin
            $this->redis->hSet($driver_id, 'goback', $driver->goback);

            //增加对应表
            $this->redis->set('driver:imei:'.$driver->imei, $driver->user);
            $this->redis->set('driver:driver_id:'.$driver->user, $driver->id);

            //司机电话和备用电话载入 BY AndyCong
            $this->redis->set('driver:phone:'.$driver->phone, $driver->id);
            if ($driver->ext_phone) {
                $this->redis->set('driver:phone:'.$driver->ext_phone, $driver->id);
            }
            //司机电话和备用电话载入 BY AndyCong

            $driver_info['imei']=$driver->imei;
            $driver_info['level']=$driver->level;

            $driver_info['name']=$driver->name;
            $driver_info['picture']=$driver->picture;
            //处理司机的大中小图片地址
            /*
			if (preg_match('%pic%', $driver->picture)) {
				$picture_small=$driver->picture;
				$picture_middle=$driver->picture;
				$picture_large=$driver->picture;
			} else {
				if (preg_match('%img\.edaijia\.cn%', $driver->picture)) {
					$picture_small=str_replace('middle', 'small', $driver->picture);
					$picture_middle=$driver->picture;
					$picture_large=str_replace('middle', 'normal', $driver->picture);

				} else {
					$picture_small=$picture_middle=$picture_large='';
				}
			}
            */
            $picture_small = Driver::getPictureUrl($driver_id, $driver->city_id, Driver::PICTURE_SMALL);
            $picture_middle = Driver::getPictureUrl($driver_id, $driver->city_id, Driver::PICTURE_MIDDLE);
            $picture_large = Driver::getPictureUrl($driver_id, $driver->city_id, Driver::PICTURE_NORMAL);
            $driver_info['picture_small']=$picture_small;
            $driver_info['picture_middle']=$picture_middle;
            $driver_info['picture_large']=$picture_large;

            $driver_info['gender']=($driver->gender) ? Dict::item('gender', $driver->gender) : '1';
            $driver_info['id_card']=$driver->id_card;
            $driver_info['domicile']=$driver->domicile;
            $driver_info['car_card']=$driver->car_card;
            $driver_info['year']=$driver->year;
            $driver_info['address']=$driver->address;
            $driver_info['ext_phone']=$driver->ext_phone;
            $driver_info['license_date']=$driver->license_date;

            $driverPhone=DriverPhone::model()->getDriverPhone($driver_id);
            if ($driverPhone) {
                $driver_info['simcard']=$driverPhone->simcard;
                $this->redis->hSet($driver_id, 'android', $driverPhone->is_bind);
            }
            $this->info=$driver_info;

            $driverExt=DriverExt::model()->getExt($driver_id);
            $driver_service['service_times']=$driverExt->service_times;
            $driver_service['high_opinion_times']=$driverExt->high_opinion_times;
            $driver_service['low_opinion_times']=$driverExt->low_opinion_times;
            $this->service=$driver_service;

            $position=DriverPosition::model()->getDriverPosition($driver->id);

            if ($position) {
                $driver_gps['longitude']=$position->longitude;
                $driver_gps['latitude']=$position->latitude;
                $driver_gps['google_lng']=$position->google_lng;
                $driver_gps['google_lat']=$position->google_lat;
                $driver_gps['baidu_lng']=$position->baidu_lng;
                $driver_gps['baidu_lat']=$position->baidu_lat;
                $driver_gps['street']=GPS::model()->getStreetByBaiduGPS($position->baidu_lng, $position->baidu_lat);

                if ($driver->mark==0) {
                    $this->status=$position->status;
                } else {
                    $this->status=self::STATUS_OFFDUTY ;
                }
                $this->position=$driver_gps;
                $this->heartbeat=strtotime($position->created);
            }

            $token=DriverToken::model()->findByPk($driver_id);
            if ($token && $updateToken) {
                $this->redis->hSet($driver_id, 'token', $token->authtoken);
                $this->redis->hSet($driver_id, 'heartbeat', strtotime($token->created));
                $this->redis->set('DRIVER_TOKEN_'.$token->authtoken, $driver_id);
                $this->redis->expire('DRIVER_TOKEN_'.$token->authtoken, 7200);
            }

            //司机的账务信息
            $account=array(
                'balance'=>0, //余额
                'yesterday'=>0, //昨日收入
                'month'=>0, //当月收入
                'total'=>0
            );
            //获取收入和余额信息 add by zhanglimin 2013-05-25
            $driverAmount=EmployeeAccount::model()->getDriverAmount($driver_id);
            if ($driverAmount) {
                $this->account=$driverAmount;
            }

            //个推的client_id
            $getui=GetuiLog::model()->getDriverInfoByDriverID($driver_id);
            if ($getui) {
                $this->redis->hSet($driver_id, 'client_id', $getui['client_id']);
                $this->redis->hSet($driver_id, 'udid', $getui['udid']);
            }

            //皇冠司机信息 MOD BY AndyCong 2013-08-05
            $recommand = DriverRecommand::model()->validateRecommend($driver_id);
            $this->recommand = !empty($recommand) ? $recommand : null;
            EdjLog::info("DriverStatsu#reload#load redis ok..................".$driver_id);
        }
    }

    private function online($driver_id) {
        $driver=self::get($driver_id);
        if ($driver) {
            //$this->redis->hSet('onlines', $driver_id, $driver->heartbeat);

            //司机onlines迁移到Redis HA Proxy
            $index = 'index_'.substr($driver_id, -1).'_onlines';
            RedisHAProxy::model()->redis->hset($index, $driver_id, $driver->heartbeat);

            //同步mongo的状态
            //DriverGPS::model()->status($driver_id, $driver->status);
        }
    }

    private function offline($driver_id) {
        $driver=self::get($driver_id);
        if ($driver) {
            //$this->redis->hDel('onlines', $driver_id);

            //司机onlines迁移到Redis HA Proxy
            $index = 'index_'.substr($driver_id, -1).'_onlines';
            RedisHAProxy::model()->redis->hDel($index, $driver_id);

            //同步mongo的状态
            //DriverGPS::model()->status($driver_id, $driver->status);
        }
    }

    /**
     * 查找电话号对应的司机信息
     * @param string $phone
     * @return boolean
     * @author AndyCong<congming@edaijia.cn> 2013-07-09
     */
    public function getByPhone($phone) {
        $driver_id=$this->redis->get('driver:phone:'.$phone);
        if ($driver_id) {
            return $driver_id;
        }
        return false;
    }

    /**
     * 获取redis中司机信息
     * @param string $driver_id
     * @param string $name
     * @return boolean
     */
    public function getItem($driver_id, $name) {
        if(empty($driver_id) || empty($name)) {
            return false;
        }
        $item = $this->redis->hget($driver_id, $name);
        if ($item) {
            return $item;
        }
        return false;
    }

    /**
     * 获取相应field的值
     * @param string $key1
     * @param unknown_type $key2
     * @return unknown
     */
    public function setCrownVal($key, $value, $expire) {
        $value = json_encode($value);
        $this->redis->set($key, $value, $expire);
        return true;
    }

    /**
     * 获取相应field的值
     * @param string $key1
     * @param unknown_type $key2
     * @return unknown
     */
    public function getCrownVal($city_id) {
        $key = date("Y-m-d").'_CROWN_'.$city_id;
        $limit_num = $this->getCityMonthCrownLimit($city_id);
        $data = $this->redis->get($key);
        if (!empty($data) || $data==='0') {
            $data = json_decode($data , true);
        }else{
            $data = $limit_num;
            $this->setCrownVal($key,$data,24*3600);
        }
        return $data;
    }

    /**
     *	获取每个城市的限额,设置是在wealthcommand中每个月1号
     *
     */
    public function getCityMonthCrownLimit($city_id){
        $limit_num = 5;
        $key=date("Y-m").'_CROWN_'.$city_id;
        $data = $this->redis->get($key);
        if(!empty($data)){
            $data = json_decode($data , true);
            $limit_num = $data;
        }
        return $limit_num;
    }

    /**
     *	设置每个月城市皇冠限额
     *
     */
    public function setCityMonthCrownLimit($city_id,$value,$expire){
        $key=date("Y-m").'_CROWN_'.$city_id;
        $value = json_encode($value);
        $this->redis->set($key, $value, $expire);
        return true;
    }

    /**
     *	兑换后，递减
     */
    public function descCrownVal($city_id){
        $key = date("Y-m-d").'_CROWN_'.$city_id;
        if($this->getCrownVal($city_id) < 1){
            return false;
        }
        //递减
        $this->redis->decr($key);
        return true;
    }

    /**
     *	获取v2后台城市皇冠数量当月限额
     *
     */
    public function getV2Crown($city_id){
        $key = date("Y-m").'_V2_CROWN_'.$city_id;
        $limit_num = $this->getV2CityMonthCrownLimit($city_id);
        $data = $this->redis->get($key);
        if (!empty($data) || $data==='0') {
            $data = json_decode($data , true);
        }else{
            $data = $limit_num;
            $this->setCrownVal($key,$data,24*3600*31);
        }
        return $data;
    }

    /**
     *	获取每个城市的司管限额,设置是在wealthcommand中每个月1号
     *
     */
    public function getV2CityMonthCrownLimit($city_id){
        $limit_num = 5;
        $key=date("Y-m").'_V2_CROWN_'.$city_id;
        $data = $this->redis->get($key);
        if(!empty($data)){
            $data = json_decode($data , true);
            $limit_num = $data;
        }
        return $limit_num;
    }

    /**
     *	设置每个月城市司管限额
     *
     */
    public function setV2CityMonthCrownLimit($city_id,$value,$expire){
        $key=date("Y-m").'_V2_CROWN_'.$city_id;
        $value = json_encode($value);
        $this->redis->set($key, $value, $expire);
        return true;
    }

    /**
     *	奖励皇冠，数量递减
     */
    public function descV2Crown($city_id){
        $key = date("Y-m").'_V2_CROWN_'.$city_id;
        if($this->getV2Crown($city_id) < 1){
            return false;
        }
        //递减
        $this->redis->decr($key);
        return true;
    }

    /**
     *	原子增加奖励皇冠数量
     *
     */
    public function incrV2Crown($city_id, $inc=1){
        $key = date("Y-m").'_V2_CROWN_'.$city_id;
        //增加
        $this->redis->incrby($key,$inc);
        return true;
    }

    /**
     *	获取v2后台城市e币数量当月限额
     *
     */
    public function getV2Emoney($city_id){
        $key = date("Y-m").'_V2_Emoney_'.$city_id;
        $limit_num = Common::getCityV2ELimit($city_id);
        $data = $this->redis->get($key);
        if (!empty($data)) {
            $data = json_decode($data , true);
        }else{
            $data = $limit_num;
            $this->setCrownVal($key,$data,24*3600*31);
        }
        return $data;
    }

    /**
     *	e币，数量减少。如果是负数，则增加;正数则减少
     */
    public function changeV2Emoney($city_id,$emoney){
        $key = date("Y-m").'_V2_Emoney_'.$city_id;
        if($this->getV2Emoney($city_id) < 1){
            return false;
        }
        //递减
        if($emoney > 0){
            $this->redis->decrby($key,$emoney);
        }else if($emoney < 0){
            $this->redis->incrby($key,abs($emoney));
        }
        return true;
    }

    /**
     *	@author aiguoxin
     *	记录上线时间戳
     */
    public function setWorkTimeStamp($driver_id,$data){
        $key= $this->_driver_online_key.$driver_id;
        $data = json_encode($data);
        $this->redis->set($key,$data,24*3600);//保留一天
        return true;
    }

    /**
     *	@author aiguoxin
     *	获取上线时间戳
     */
    public function getWorkTimeStamp($driver_id){
        $key= $this->_driver_online_key.$driver_id;
        $data = $this->redis->get($key);
        if (!empty($data)) {
            $data = json_decode($data , true);
        }
        return $data;
    }


    /**
     * @param $token
     * @param $driver_id
     * @return bool
     * 设置司管登陆token
     */
    public function setDriverManagerToken($token,$driver_id){
        $this->redis->set($token,$driver_id);
        return true;
    }

    /**
     * @param $token
     * @return bool|string
     * 根据token获取司管信息
     */
    public function getDriverManagerToken($token){
        return $this->redis->get($token);
    }

    /**
     * @param $token
     * @return int
     * 删除token
     */
    public function delDriverManagerToken($token){
        return$this->redis->del($token);
    }


    /**
     *	@author aiguoxin
     *	记录司机城市所在的默认配置
     */
    public function setCitySetting($city_id,$name,$value){
        $key= $this->_driver_city_setting_key.$city_id;
        $this->redis->hset($key,$name,$value);//保留一天
        return true;
    }

    /**
     *	@author aiguoxin
     *	获取司机城市所在的默认配置
     */
    public function getCitySetting($city_id,$name){
        $key= $this->_driver_city_setting_key.$city_id;
        $data = $this->redis->hget($key,$name);
        if (!empty($data)) {
            $data = json_decode($data , true);
        }else{
            $data = 0;//默认为0
        }
        return $data;
    }

    public function isSupportRemoteOrder($driver_id) {
        $driver_version = $this->app_ver($driver_id);
        return $driver_version === '2.4.8' || !Helper::compareVersion('2.5.0', $driver_version);
    }


    /**
     * @author zhongfuhai
     * @param $driver_info
     * @return array
     */
    public function refreshDrivingYear($driver_info){
    	if($driver_info){
    		$recordDrivingYear = $driver_info['year'];
    		$licenseDate = date_parse($driver_info['license_date']);
    		$licenseYear = $licenseDate['year'];
    		$licenseMonth = $licenseDate['month'];
    
    		$currentYear = date("Y");
    		$currentMonth = date("m");
    		$currentDrivingYear = 0;
    
    		if($currentMonth>=$licenseMonth){
    			$currentDrivingYear = $currentYear - $licenseYear + 1;
    		}else{
    			$currentDrivingYear = $currentYear - $licenseYear;
    		}
    
    		if($currentDrivingYear!=$recordDrivingYear){
    			//if the driving year in array is wrong, update it
    			$driver_info['year'] = $currentDrivingYear;
    			$this->info=$driver_info;
    		}
    	}
    	 
    	return $driver_info;
    }
    
}
