<?php
/**
 * 队列处理
 */
class QueueProcess {
    private static $_models;
    private $message;

    public static function model($className=__CLASS__) {
        $model=null;
        if (isset(self::$_models[$className]))
            $model=self::$_models[$className];
        else {
            $model=self::$_models[$className]=new $className(null);
        }
        return $model;
    }

    /**
     * 订单价格表log
     * @author sunhongjing 2014-01-26
     *
     * @param unknown_type $params
     * @editor AndyCong 2014-01-26
     */
    public function order_pricelist_tmp_log($params)
    {
    	$driver = DriverStatus::model()->getByToken($params['token']);
	if(!$driver||!is_object($driver)){
		EdjLog::info('driver is ill params '.serialize($params));
		return false;
	}
    	$driver_id = $driver->driver_id;
    	$method = isset($params['method']) ? $params['method'] : '';
    	$order_id = isset($params['order_id']) ? $params['order_id'] : '';
    	$city_id = isset($params['city_id']) ? $params['city_id'] : '';
    	$lng = isset($params['lng']) ? $params['lng'] : '';
    	$lat = isset($params['lat']) ? $params['lat'] : '';
    	$log_time = isset($params['log_time']) ? date('Y-m-d H:i:s' , $params['log_time']) : date('Y-m-d H:i:s');
    	echo "\n method:{$method}|order_id:{$order_id}|driver_id:{$driver_id}|city_id:{$city_id}|lat:{$lat}|lng:{$lng}|log_time:{$log_time}\n";
    	return true;
    }

    /**
     * 司机接单的log
     * @author sunhongjing 2014-01-15
     *
     * @param unknown_type $params
     * @editor AndyCong 2014-01-16
     */
    public function order_receive_tmp_log($params)
    {
    	$driver = DriverStatus::model()->getByToken($params['token']);
    	$push_msg_id = isset($params['push_msg_id']) ? $params['push_msg_id'] : '';
	if(!isset($driver)) {
		echo "[ERROR] Invalid driver token:".$params['token']."|push_msg_id:".$push_msg_id;
		return false;
        }
    	$driver_id = $driver->driver_id;
    	$log_time = isset($params['log_time']) ? date('Y-m-d H:i:s' , $params['log_time']) : date('Y-m-d H:i:s');
    	$flag = isset($params['flag']) ? $params['flag'] : 2;
    	echo "\n push_msg_id:".$push_msg_id."|driver_id:".$driver_id."|log_time:".$log_time."|flag:".$flag."\n";
    	return true;
    }

    /**
     * 客户端呼叫位置记录
     * @param array $params
     */
    public function app_calllog($params) {
        $udid=isset($params['udid']) ? $params['udid'] : '';
        $phone=isset($params['phone']) ? $params['phone'] : 0;
        $driver_id = isset($params['driverID']) ? $params['driverID'] : '';
        $macaddress=isset($params['macaddress']) ? $params['macaddress'] : '';
        $device=isset($params['device']) ? $params['device'] : '';
        $os=isset($params['os']) ? $params['os'] : '';
        $version=isset($params['version']) ? $params['version'] : '';
        $longitude=$params['longitude'];
        $latitude=$params['latitude'];
        $callTime=$params['callTime'];

        AppCallRecord::initRecord($udid, $macaddress, $phone, $driver_id, $device, $os, $version, $longitude, $latitude, $callTime);
        echo $driver_id.$callTime;
    }

    /**
     * 呼叫中心移动客服
     *
     * @param array $params
     */
    public function callcenter_mobile($params) {
        $format='id:%s, phone:%s, state:%s'."\n";
        echo sprintf($format, $params['id'], $params['phone'], $params['state']);
        $message=CallcenterLog::model()->process($params);
        echo 'callcenter_mobile:'.$message."\n";
    }

    /**
     * 输出催单日志，为了统计用
     *
     * @author sunhongjing 2014-01-11
     *
     * @param unknown_type $params
     * @return null
     */
    public function callcenter_urge_call($params)
    {
    	$queue_id 	= isset($params['queue_id']) ? $params['queue_id'] : '';
    	$times 		= isset($params['times']) ? $params['times'] : '';
    	$date 		= isset($params['date']) ? $params['date'] : date("Y-m-d H:i:s");
    	//array('queue_id'=>$queue_id,'times'=>$times,'date'=>date('Y-m-d H:i:s')),
    	$format="\n".'queue_id:%s, urge_times:%s, date:%s'."\n";
        echo sprintf($format, $queue_id, $times, $date);
    }

    /**
     * 司机状态更新
     *
     * @param array $params
     */
    public function status($params) {
        $imei=$params['imei'];
        $driver_id=$params['driver_id'];
        $status=$params['status'];
        $created=date(Yii::app()->params['formatDateTime'], time());

        $format="userid:%s, status:%s\n";
        echo sprintf($format, $driver_id, $status);

        $driver=DriverStatus::model()->get($driver_id);
        if ($driver) {
            $driver->status=$status;
        }

        $attributes=array(
            'user_id'=>$driver->id,
            'status'=>$status,
            'created'=>$created
        );

        DriverPosition::model()->updateByPk($driver->id, $attributes);

        //纪录轨迹
        $attributes=array(
            'imei'=>$imei,
            'state'=>$status,
            'insert_time'=>$created
        );
        $et=new EmployeeTrack();
        $et->attributes=$attributes;
        $et->insert();
    }


    /**
     * mtk手机的通话记录上传
     *
     * @param array $params
     */
    public function mtk_call($params) {
        $status=0;
        $format='imei:%s, phone:%s, type:%s, status:%s'."\n";

        // 		$ret=CallHistory::model()->exists('sig=:sig', array(
        // 				':sig'=>$params['sig']
        // 		));


        // 		if (!$ret) {
        // 		}


        $model=new CallHistory();
        unset($model->attributes);
        $model->attributes=$params;
        if ($model->insert()) {
            $status=1;
        } else {
            $status=-1;
        }

        echo sprintf($format, $params['imei'], $params['phone'], $params['type'], $status);
        echo 'mtk_call:'.json_encode($params)."\n";
    }

    /**
     * MTK手机的基站定位
     *
     * @param array $params
     */
    public function mtk_location($params) {
        $imei=$params['imei'];
        $towers=$params['towers'];

        // 检查IMEI是否已注册
        $driver_profile=Driver::getProfileByImei($imei);

        if ($driver_profile) {
            $gps=array();
            // $json_location = '{"imei":"356993020520421","mcc":"460","mnc":"0","towers":[{"mcc":"460","lac":"4187","ci":"39357","ssi":100,"ta":255}]}';
            // $json_location = '{"imei":"353419036311764","mcc":"460","mnc":"0","towers":[{"mcc":"460","lac":"4604","ci":"21903","ssi":"62","ta":"255"},{"mcc":"460","lac":"4604","ci":"19368","ssi":"250","ta":"255"},{"mcc":"460","lac":"4607","ci":"2098","ssi":"250","ta":"255"},{"mcc":"460","lac":"0","ci":"0","ssi":"255","ta":"255"},{"mcc":"460","lac":"0","ci":"0","ssi":"255","ta":"255"},{"mcc":"460","lac":"0","ci":"0","ssi":"255","ta":"255"},{"mcc":"460","lac":"0","ci":"0","ssi":"255","ta":"255"}]}';
            // 解析地址
            foreach($towers as $tower) {
                if ($tower['ci']!=0&&$tower['lac']!=0) {
                    $google_gps=Lbs2GPS::Location($tower);
                    if (isset($google_gps['latitude'])) {
                        $gps[]=array(
                            'rssi'=>$tower['ssi'],
                            'latitude'=>doubleval($google_gps['latitude']),
                            'longitude'=>doubleval($google_gps['longitude'])
                        );
                    }
                }
            }

            if ($gps) {
                // 取离第一个基站距离500米的基站，如果没有，取第一个基站的座标位定位。
                if (count($gps)>2) {
                    $main_tower=$gps[0];
                    $points=array();
                    foreach($gps as $tower) {
                        $d=Helper::Distance($main_tower['latitude'], $main_tower['longitude'], $tower['latitude'], $tower['longitude']);
                        if ($d<500) {
                            $points[]=$tower;
                        }
                    }

                    if ($points) {
                        $sum_longitude=0;
                        $sum_latitude=0;
                        foreach($points as $item) {
                            $sum_longitude+=$item['longitude'];
                            $sum_latitude+=$item['latitude'];
                        }
                        $latitude=$sum_latitude/count($points);
                        $longitude=$sum_longitude/count($points);
                    } else {
                        $latitude=$main_tower['latitude'];
                        $longitude=$main_tower['longitude'];
                    }
                } else {
                    foreach($gps as $item) {
                        $longitude=$item['longitude'];
                        $latitude=$item['latitude'];
                    }
                }

                if ($longitude!=0&&$latitude!=0) {
                    $driver_id=$driver_profile->user;
                    $gps_position=array(
                        'longitude'=>$longitude,
                        'latitude'=>$latitude
                    );
                    $gps=GPS::model()->convert($gps_position);
                    DriverPosition::model()->updatePosition($driver_id, $gps);

                    $format="userid:%s, location:%s,%s\n";
                    echo sprintf($format, $driver_id, $longitude, $latitude);
                } else {
                    echo 'can\'t location:'.$driver_profile->user."\n";
                }
            }
        } else {
            echo 'not register'."\n";
        }
    }

    /**
     * 司机客户端注册push client_id
     * @param array $params
     */
    public function driver_push_register($params) {
        $client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
        if (empty($client)) {
            //新建
            $client=new GetuiClient();
            $client->attributes=$params;
            if ($client->save()) {
                echo 'add new';
            }
        } else {
            //更新
            if (GetuiClient::model()->updateByPk($client['id'], $params)) {
                echo 'update';
            }
        }
        GetuiClient::model()->setCache($params['driver_id'], $params); //设置缓存
    }


    /**
     * 客户客户端注册push client_id
     * @author zhanglimin 2013-08-05
     * @param $params
     */
    public function customer_client_register($params){

        if( empty($params['client_id']) || empty($params['udid']) ){
            echo "client_id 或 udid为空，请检查\n";
            return;
        }

        $client = CustomerClient::model()->getInfo($params['udid']);
        $params['created'] = date("Y-m-d H:i:s");
        if(empty($client)){

        	echo Common::jobBegin("client is empty");
            //检查客户表是否有这个数据，得到phone,和id
            $info = CustomerToken::model()->getInfobyUdid($params['udid']);

            if(!empty($info)){
                $params['phone'] = empty($info['phone']) ? '' : $info['phone'];
            }

            echo Common::jobBegin("1234");
            //新建
            $client  = new CustomerClient();
            $client->attributes = $params;
            if($client->save()){
                echo Common::jobBegin("客户端注册新建开始");
                echo "add new \n";
                echo Common::jobBegin("客户端注册新建结束");
            }
        }else{
        	echo Common::jobBegin("client is exist");
            if(!isset($params['phone'])){
                return false;
            }
            //更新
            if (CustomerClient::model()->updateByPk($client['id'], $params)) {
                if(!empty($params['phone'])){
                    //缓存
                    CustomerStatus::model()->getRegisterUidiCache($params['phone'],$params['udid']);
                }
                echo Common::jobBegin("客户端注册修改开始");
                echo "update \n";
                echo Common::jobBegin("客户端注册修改结束");

            }
        }
        return true;
    }

    public function dc_driver_position2db($params)
    {
        if( empty($params) || empty($params['driver_id']) || !isset($params['status']) ){
            return false;
        }
        $driver_id = $params['driver_id'];
        unset($params['driver_id']);
        $params['update_time'] = date(Yii::app()->params['formatDateTime'], time());

        $ret = DcDriverPosition::model()->updateAll($params, 'driver_id=?', array($driver_id));
        print_r($ret);
        return true;
    }

    /**
     * 司机位置放db
     * @author sunhongjing 2013-07-11
     *
     * @param unknown_type $params
     * @return bool
     */
    public function driver_position2db($params)
    {
        if( empty($params) || empty($params['driver_id']) || empty($params['gps']) ){
            return false;
        }
        $driver_id 	= $params['driver_id'];
        $gps 		= $params['gps'];

        $ret = DriverPosition::model()->updatePosition($driver_id, $gps, null, $params['log_time']);
        print_r($ret);
        return true;
    }


    /**
     * 司机状态放db
     * @author sunhongjing 2013-07-11
     *
     * @param unknown_type $params
     * @return bool
     */
    public function driver_status2db($params)
    {
        if( empty($params) || empty($params['id']) || !isset($params['status']) ){
            return false;
        }
        $id 	= $params['id'];
        $status 	= $params['status'];
	$app_ver = isset($params['app_ver']) ? $params['app_ver'] : '';
        $ret = DriverPosition::model()->updateStatus($id, $status, $app_ver);
        return true;
    }

    /**
     * Batch update order postions
     *
     * @param array $params
     */
    public function driver_batch_position_track($params) {
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
     * 更新司机GPS位置信息记录到track表
     *
     * @param array $params
     */
    public function driver_position_track($params) {
	// Save busy driver position for current order
	// Get order id first
	if(!isset($params['driver_id']) || !isset($params['latitude'])
		|| !isset($params['longitude'])
		|| !isset($params['gps_type'])
		|| !isset($params['status'])) {
	    return false;
	}

	if($params['status'] == 1) { // Busy
	    $lat = $params['latitude'];
	    $lng = $params['longitude'];
	    if($lat == 1.0 || $lng == 1.0) {
		return false;
	    }

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
	    if($params['gps_type'] != 'baidu') {
		$gps=GPS::model()->convert_only(array(
			    'longitude'=>$lng,
			    'latitude'=>$lat,
			    ),
			$params['gps_type']);
		$lat = $gps['baidu_lat'];
		$lng = $gps['baidu_lng'];
	    }

	    $positions = array();
	    $positions[] = array(
		    'lat' => $lat,
		    'lng' => $lng);

	    // Save the position
	    $datas = array('driver_id' => $params['driver_id'],
		    'order_id' => $order_id,
		    'order_state' => $order_state,
		    'positions' => $positions);
	    RDriverPosition::model()->insertPosition($datas);
	}
        // 什么也不做，延时存储db
        // call driver_position_track_ope
        //EdjLog::info("delay save");
        //var_dump($params);
    }


    public function driver_position_track_ope($params) {
			DriverPositionImport::singleImportPostion($params);
    }



    // 2014/07/07 15:35:40 [5002] [info] [application] REDIS_QUEUE_OUT METHOD:driver_batch_position_track PARAMS:{"driver_id":"BJ9021","log_time":"1404718538","positions":[{"lat":"40.018963","lng":"116.480074","gps_type":"baidu","gps_time":1404718447486,"provider":"network","accuracy":73.582794189453,"status":1,"milli_timestamp":1404718447.486},{"lat":"40.018963","lng":"116.480074","gps_type":"baidu","gps_time":1404718488381,"provider":"network","accuracy":73.582794189453,"status":1,"milli_timestamp":1404718488.381}],"status":1}
    public function driver_batch_position_track_ope($params) {
			DriverPositionImport::batchImportPostions($params);
    }

	
    /**
     * 测试位置转化方法速度
     * Enter description here ...
     */
    public function test_pos_convert($params)
    {
        $check_sum=intval($params['longitude'])+intval($params['latitude']);
        if ($check_sum>10) {

            $gps_position=array(
                'longitude'=>$params['longitude'],
                'latitude'=>$params['latitude']
            );
            $begin = microtime(true);
            echo "\n-----begin convert----".$begin."---\n";

            $gps=GPS::model()->convert($gps_position, $params['gps_type']);
            print_r($gps);
            echo "\n-----end convert----".microtime(true)."----diff---". ($begin-microtime(true)) ."---\n";

        }else{
            echo "not need convert pos \n";
        }

    }


    /**
     * 更新司机GPS位置信息
     *
     * @param array $params
     */
    public function driver_position($params) {
        $driver_id=$params['driver_id'];
        $status=isset($params['status'])?$params['status']:0;
        $params['log_time'] = isset($params['log_time']) ? $params['log_time'] : date("YmdHis");
        $driver=DriverStatus::model()->get($driver_id);

        if ($driver) {
            $check_sum=intval($params['longitude'])+intval($params['latitude']);
            if ($check_sum>10) {

                $gps_position=array(
                    'longitude'=>$params['longitude'],
                    'latitude'=>$params['latitude']
                );

                $gps=GPS::model()->convert($gps_position, $params['gps_type']);

                //添加task队列,回写库
                $task=array(
                    'method'=>'driver_position2db',
                    'params'=>array(
                        'driver_id'=>$driver_id,
                        'status'=>$status,
                        'gps'=>$gps,
                        'log_time'=>$params['log_time'],
                    ),
                );
                Queue::model()->putin($task,'default');

            } else {
                //只更新状态
                $driver->status=$status;
                //更新mongo
                DriverGPS::model()->status($driver_id, $status);

                //添加task队列,回写库
                $task=array(
                    'method'=>'driver_status2db',
                    'params'=>array(
                        'id'=>$driver->id,
                        'status'=>$status,
                    ),
                );
                Queue::model()->putin($task,'status');

            }

            $format="userid:%s \n";
            echo printf($format, $driver_id);
        }
    }


    /**
     * 更新司机状态
     */
    public function driver_current_status($params) {

        //更新mongo
        //临时容错
        if (isset($params['stauts']))  $params['status'] = $params['stauts'];

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

        DriverGPS::model()->status($params['driver_id'], $params['status']);

        //添加task队列,回写库
	$app_ver = isset($params['app_ver']) ? $params['app_ver'] : '';
        $task=array(
            'method'=>'driver_status2db',
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
     * 更新司机GPS位置信息
     *
     * @param array $params
     */
    public function driver_current_pos($params) {
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
            echo 'driver_id='.$driver_id.',service_type='.$params['service_type'].',app_ver='.$app_ver.'进入唯一列表更新位置job ok';
            EdjLog::info('driver_id='.$driver_id.',service_type='.$params['service_type'].'app_ver='.$app_ver.'进入唯一列表更新位置job ok');
	    DriverPositionService::getInstance()->positionUniqueList('LAST_POSITION', $driver_id, $params);
        }
	else{
		echo "get status for driver $driver_id fail";
	}
        $format="userid:%s \n";
        echo printf($format, $driver_id);
    }

    /**
     * tracking 报单
     *
     * @param unknown_type $params
     */
    public function order_submit_tracking($params)
    {
    	echo "\n---";
        echo isset($params['order_id']) ? "---order_id:".$params['order_id'] : '';
        echo isset($params['order_number']) ? "---order_num:".$params['order_number'] : '';
        echo "---\n";
    }



    /**
     * 结伴返程
     * @author sunhongjing 2013-04-26
     * @param unknown_type $params
     */
    public function driver_goback_together($params) {
        $id=$params['id'];
        $goback=$params['goback'];

        //记录日志
        //go back log 记录日志

        $data = array(
            'driver_id' => $params['driver_id'],
            'goback' => $goback,
            'lng' => $params['lng'],
            'lat' => $params['lat'],
            'status' => $params['status'],
        );
        DriverGobackLog::model()->insertInfo($data);

        //更新司机的positon状态，临时增加，需要切换到redis

        return Driver::model()->updateByPk($id, array( 'goback' => $goback ));
    }


    /**
     * 同步用户信息，位置信息数据到拼车
     * @author duke 2015-05-20
     * @param unknown_type $params
     */
    public function pinche_synccustomer($params) {
        if(!isset($params['gps'])){
            $gps = DriverPosition::model()->getDriverPosition($params['driver_info']['id']);
            $params['gps']['lat'] = $gps->latitude;
            $params['gps']['lng'] = $gps->longitude;
        }

        $res = Pinche::model()->synccustomer($params['gps'] ,$params['driver_info'],$params['goback']);
        $res = PincheV2::model()->synccustomer($params['gps'] ,$params['driver_info'],$params['goback']);

    }

    /**
     * 骚扰电话放黑名单，推送
     * @author sunhongjing 2013-05-13
     * @param unknown_type $params
     */
    public function crank_call_to_blacklist($params) {
        if (empty($params['phone'])||empty($params['driver_ids'])) {
            return false;
        }
        $phone=trim($params['phone']);

        //首先判断是否是真实老用户，如果不是，就放黑名单
        //$real_customer = CustomerMain::model()->getCustomer($params['phone']);
        $real_customer_order_num=Order::model()->getOrderCountByCustomerPhone($phone);

        if (0<$real_customer_order_num) {
            return false;
        }

        $contents='';
        $drivers_number=count($params['driver_ids']);
        $now=date("Y-m-d H:i:s");

        $driver_group=array();

        //$driver_ids = array_unique($params['driver_ids']);
        $driver_ids=$params['driver_ids'];
        foreach($driver_ids as $driver) {
            $driver_group[$driver][]=$driver;
            $last_driver=$driver;
        }
        foreach($driver_group as $k=>$v) {
            $contents.=" [ $k ] ".count($v)."次；";

            $sms_data=array(
                'phone'=>$phone,
                'content'=>"该客户呼叫司机{$drivers_number}人次。包括:".$contents,
                'driver_id'=>$k,
                'created'=>$now, //确认时间
                'type'=>'call'
            );
            $ret=DriverUploadSms::model()->insertInfo($sms_data);
        }

        return true;

    }

    /**
     * 用户获取验证码
     * @author mengtianxue 2013-05-07
     * @param unknown_type $params
     */
    public function customer_prelogin_passwd($params) {
        $customerPassParams=array();
        $customerPass=new CustomerPass();
        $phone = $params['phone'];
        //查询当前号码的最后获取passwd
        $checkedCustomerPass = CustomerPass::model()->getCustomerPassLast($phone);

        //检查是否有记录，最后一条的记录 send_times+1不等于传递参数时，重写cache 否则，直接保存传递数据
        if($checkedCustomerPass && ($params['send_times'] != ($checkedCustomerPass['send_times'] + 1)) ){
            $params['send_times'] = $checkedCustomerPass['send_times'] + 1;
            $cache_key = 'prelogin_customer_'.md5($phone.$params['macaddress']);
            Yii::app()->cache->set($cache_key, json_encode($params), 86400);
        }
        $customerPassParams['send_times'] = $params['send_times'];
        $customerPassParams['phone'] = $phone;
        $customerPassParams['expired'] = $params['expired'];
        $customerPassParams['passwd'] = $params['passwd'];
        $customerPassParams['update_time'] = date('Y-m-d H:i:s', $params['update_time']);
        $customerPassParams['create_time'] = date('Y-m-d H:i:s');

        $customerPass->attributes = $customerPassParams;
        if ($customerPass->insert()) {
            //记录用户信息
            CustomerService::service()->initCustomer($params['phone']);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 用户登陆时要更改的信息
     * @author mengtianxue 2013-05-08
     * @param unknown_type $params
     */
    public function customer_login($params) {
        //检查用户是否在token表中存在
        $customerToken = CustomerToken::model()->checkCustomerTokenByBusiness($params);
        //存在修改用户信息 否则添加新记录
        if($customerToken){
            CustomerToken::model()->updateAuthTokenByBusiness($params);
        }else{
            CustomerToken::model()->addCustomerToken($params);
        }

        $phone=$params['phone'];
        $business=$params['business'];
	CustomerToken::model()->deleteAuthTokenWithoutOne($phone,
		$params['authtoken'],$business);

        //修改customerMain 最后登陆时间
        return CustomerService::service()->updateLastLoginAndStatus($phone);
    }

    /**
     * 登出时删除cache 和 修改Token 状态
     * Enter description here ...
     * @param unknown_type $params
     */
    public function customer_logout($params){
        $customerLogic = new CustomerLogic();
        $customerLogic->deleteCustomerTokenCache($params['phone'],$params['business']);
        $customerToken = CustomerToken::model()->deleteAuthToken($params['phone'],$params['business']);
        return true;
    }

    /**
     * 推送唤醒信息，检查司机是否在线
     *
     * @author sunhongjing 2014-01-02
     * @param unknown_type $params
     * @return bool
     */
    public function push_wakeup_msg($params)
    {
    	$client_id = $params['client_id'];
    	$flag	   = $params['flag'];
		$message=array(
				'type'=>'msg',
				'content'=>array(
						'message'=>'激活消息：如果您2分钟内收到该消息，说明您可以正常接单。师傅多多接单。',
						'category' => '1',
				),
				'timestamp'=>time()
		);

		//print_r($message);

		//$level = EPush::LEVEL_LOW=1; //普通级别
		//$level = EPush::LEVEL_MIDDLE=2; //中级级别
		//$level = EPush::LEVEL_HIGN=3; //最高级别

		EPush::set_message_type($message, "wakeup_message");
		$ret = EPush::model($flag)->send($client_id, $message, EPush::LEVEL_HIGN);
		print_r($ret);
		return true;
    }


    /**
     * 客户端心跳，更新客户端在线状态列表
     *
     * @param array $params
     */
    public function driver_heartbeat($params) {
        $id=$params['id'];
        $driver_id=$params['driver_id'];
        $timestamp=$params['timestamp'];
        $app_ver=$params['app_ver'];
        echo $driver_id.' time:'.$timestamp."\n";
        DriverPosition::model()->updateByPk($id, array(
                                                       'created'=>$timestamp,
                                                       'app_ver'=>$app_ver,
        ));
    }

    /**
     * android手机通话记录上传
     *
     * @param array $params
     */
    public function driver_calllog($params) {
        DriverCallLog::model()->insertCallLog($params);
        $format='driver:%s phone:%s type:%s'."\n";
        echo printf($format, $params['driver_id'], $params['phone'], $params['type']);
    }

    /**
     * 司管app登录token写入数据库
     * @param array $params
     */
    public function driver_manager_token($params) {
        $driver_id=$params['driver_id'];
        $token=$params['token'];
        $create_time=$params['create_time'];

        $tokenRecord=DriverManagerToken::model()->findAll('driver_id=:driver_id', array(':driver_id'=>$driver_id));
        if ($tokenRecord) {
            EdjLog::info('driverId='.$driver_id.'更新司管app登陆token...');
            $update_sql = "update t_driver_manager_token set `auth_token`=:token WHERE `driver_id` = :driver_id ";
            $change = Yii::app()->db->createCommand($update_sql);
            $change->bindParam(":token", $token);
            $change->bindParam(":driver_id", $driver_id);
            $change->execute();
        } else {
            EdjLog::info('driverId='.$driver_id.'保存司管app登陆token...');
            $tokenRecord=new DriverManagerToken();
            $tokenRecord->attributes=array(
                'driver_id'=>$driver_id,
                'auth_token'=>$token,
                'create_time'=>$create_time
            );
            $tokenRecord->save(false);
        }
    }

    /**
     * 客户端登录token写入数据库
     * @param array $params
     */
    public function driver_token($params) {
        $driver_id=$params['driver_id'];
        $authtoken=$params['authtoken'];
        $created=$params['created'];

        $token=DriverToken::model()->findByPk($driver_id);
        if ($token) {

        	//临时改下写法 add by sunhongjing 2013-11-20
        	$update_sql = "update t_driver_token set `authtoken`=:authtoken ,created=:created  WHERE `driver_id` = :driver_id ";
            $change = Yii::app()->db->createCommand($update_sql);
            $change->bindParam(":authtoken", $authtoken);
            $change->bindParam(":created", $created);
            $change->bindParam(":driver_id", $driver_id);
            $change->execute();
            $change->reset();

//            DriverToken::model()->updateByPk($driver_id, array(
//                'authtoken'=>$authtoken,
//                'created'=>$created
//            ));
        } else {
            $token=new DriverToken();
            $token->attributes=array(
                'driver_id'=>$driver_id,
                'authtoken'=>$authtoken,
                'created'=>$created
            );
            $token->save();
        }
    }

    /**
     * 合力金桥通话记录保存
     * @param array $params
     */
    public function callcenter_hojo($params){

        //把呼叫中心的坐席号码对应到系统账户
        if (isset($params['Exten'])) {
            $sql='select user_id from t_admin_agent where agent_num=:agent';
            $user_id=Yii::app()->db_readonly->createCommand($sql)->queryScalar(array(
                ':agent'=>$params['Exten']
            ));
            $params['user_id']=$user_id;
        }

        $log=new CallcenterLog();
        $log->attributes=$params;
        $log->CallsheetId=$params['CallSheetID'];


        if ($log->CallType && $log->insert()) {
            $call_id = $log->getPrimaryKey();
//            //振铃(20s以上)未接听，ivr放弃
//            if ($log->State=='leak') {
//                if ((strtotime($log->End)-strtotime($log->Ring))>20){
//                    $task=array(
//                        'method'=>'callcenter_mobile',
//                        'params'=>array(
//                            'id'=>$call_id,
//                            'state'=>$log->State,
//                            'phone'=>$log->CallNo
//                        )
//                    );
//                    Queue::model()->putin($task,'dumplog');
//                }
//            }else
            if ($log->CallType && $log->State == 'queueLeak') {
                $task = array(
                    'method' => 'callcenter_mobile',
                    'params' => array(
                        'id' => $call_id,
                        'state' => $log->State,
                        'phone' => $log->CallNo
                    )
                );
                Queue::model()->putin($task, 'dumplog');
            } elseif ($log->CallType && $log->State == 'notDeal') {
                $task = array(
                    'method' => 'callcenter_mobile',
                    'params' => array(
                        'id' => $call_id,
                        'state' => $log->State,
                        'phone' => $log->CallNo
                    )
                );
                Queue::model()->putin($task, 'dumplog');
            }
        }
    }

    /**
     * 记录API访问日志
     *
     * @param string $table_name
     * @param array $params
     */
    public function log_api($params) {
        $table_name=$params['table_name'];
        $attributes=$params['attributes'];
        @Yii::app()->db_rds->createCommand()->insert($table_name, $attributes);
    }


    public function testss($param){

    }

    /**
     * 订单结算
     *
     * @param array $params
     * @param bool $is_new
     */
    public function deal_order($params) {
        $order_id=$params['order_id'];
        $is_new=isset($params['is_new']) ? $params['is_new'] : true;

        $order=Order::model()->findByPk($order_id);
        if (!$order) {
            return;
        }

        $account=new EmployeeAccount();
        // EmployeeAccount::model()->insert_deal_error_log($order['order_id']);
        $order_params=array(
            ':order_id'=>$order_id
        );

        if ($account->exists('order_id=:order_id', $order_params)) {
            $account->deleteAll('order_id=:order_id', $order_params);
            echo 'delete '.$order_id."\n";
        }

        // 每订单扣除信息费
        $account->deal_order_infomation_fee($order);
        // 扣除每单保险费
        $account->deal_order_insurance($order);

        if ($order['vipcard']) {
            // 结算VIP订单
            EmployeeAccount::model()->deal_order_vip($order);
        } else {
            // 结算非VIP订单
            EmployeeAccount::model()->deal_order_normal($order);
            // 结算补帐的优惠券信息
            EmployeeAccount::model()->deal_order_bonus_fix($order_id);
            // 结算订单优惠券
            EmployeeAccount::model()->deal_order_bonus($order, $is_new);
        }
        echo 'deal_order:'.$order_id."ok\n";
        // EmployeeAccount::model()->update_deal_error_log($order['order_id']);
    }

    /**
     * 分配司机调用方法
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-05-11
     */
    public function dispatch_driver($params) {
        $queue_id=$params['queue_id'];
        $driver_id=$params['driver_id'];
        $queue=OrderQueue::model()->findByPk($queue_id);
        if ($queue) {
            $params=array(
                'type'=>'order',
                'content'=>'订单',
                'level'=>3,
                'driver_id'=>$driver_id,
                'queue_id'=>$queue_id,
                'customer_phone'=>$queue->contact_phone,
                'customer_name'=>$queue->name,
                'address'=>$queue->address,
                'booking_time'=>$queue->booking_time,
                'created'=>date('Y-m-d H:i:s', time())
            );
            $result=self::model()->push_message($params);
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 新分配司机调用方法
     * @param $params
     * @return mixed
     */
    public function dispatch_driver_new($params){
    	//记录log
        EdjLog::info($params['queue_id'].'|'.$params['order_id'].'|201系统已派单|'.$params['driver_id'].'|begin' , 'console');

        $ret = Push::model()->PushOrder($params['queue_id'] , $params['order_id']  , $params['driver_id']);

        if ($ret) {

        	//记录log
	        EdjLog::info($params['queue_id'].'|'.$params['order_id'].'|201系统已派单|'.$params['driver_id'].'|派单成功|end' , 'console');

        } else {

        	//记录log
	        EdjLog::warning($params['queue_id'].'|'.$params['order_id'].'|201系统已派单|'.$params['driver_id'].'|派单失败|end' , 'console');

        }

        return $ret;
    }

    /**
     * 推送消息处理
     * @param array $params
     *        $params维数组:type、driver_id/udid、message、level必须 version、offline_time非必须
     *                     queue_id、customer_name、customer_phone、address、booking_time在type=order时必须
     *                     status在type=status时必须
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-04-28
     */
    public function push_message($params) {
        //应该增加对$params的验证 ~ add comments by sunhongjing 2013-07-13
        $ret = PushMessage::model()->organize_message_push($params);
        print_r($ret);
        return $ret;
    }

    /**
     * 更新消息接收成功状态
     * @author zhanglimin 2013-04-28
     * @param $params
     * @return int
     */
    public function set_message_flag($params) {
        $push_msg_id=$params['push_msg_id'];
        $flag=$params['flag'];
        $ret = MessageLog::model()->updateByPk( $push_msg_id, array('flag'=>$flag) );

        if($ret){
            echo "------更新消息状态为{$flag}成功-------\n";
        }else{
            echo "------更新消息状态为{$flag}失败-------\n";
        }

        return $ret;
    }

    /**
     * 400派单处理订单
     * @author zhanglimin 2013-05-02
     * @param $params
     * @return bool
     */
    public function push_order_operate($params) {
        //应该增加对$params的验证 ~ add comments by sunhongjing 2013-07-13
        $ret = Order::model()->order_operate($params);
        print_r($ret);
        return $ret;
    }

    /**
     * 新版400派单处理订单
     * @author zhanglimin 2013-05-02
     * @param $params
     * @return bool
     */
    public function push_order_operate_new($params) {
        //应该增加对$params的验证 ~ add comments by sunhongjing 2013-07-13
        $ret = AutoOrder::model()->order_operate($params);
        print_r($ret);
        return $ret;
    }

    /**
     * 保存上传信息数据
     * @author zhanglimin 2013-05-13
     * @param $params
     * @return bool
     */
    public function push_upload_sms($params) {
        $params['status']=0;
        $model=new DriverUploadSms();
        $model->attributes=$params;
        $ret = $model->insert();
        echo $ret ? "------短信保存成功-----\n" : "----短信保存失败----\n";
        return true;
    }

    /**
     * 队列处理admin_opt_log
     *
     * @author sunhongjing 2013-05-30
     * @param unknown_type $params
     */
    public function admin_opt_log($params){

        $ret = AdminLogs::model()->addAdminOptLogs($params);
        echo $ret ? "------保存成功-----\n" : "----保存失败----\n";
        return $ret;
    }

    /**
     * 上报订单位置
     * @author zhanglimin 2013-05-24
     * @modified qiujianping@edaijia-staff.cn 2014-04-08
     *	Add assure part for order without driver in db
     *
     * @param $params
     * @return bool
     */
    public function push_order_position($params){
        //添加车牌号和客户处理 BY AndyCong 2013-06-05
//    	$order_params = array(
//    	    'order_id' => $params['order_id'],
//    	    'name' => $params['name'],
//    	    'phone' => $params['phone'],
//    	    'car_number' => $params['car_number'],
//    	);
//    	Order::model()->orderBeforeSave($order_params);
        //添加车牌号和客户处理 BY AndyCong 2013-06-05 END

        $ready_time = isset($params['ready_time']) ? $params['ready_time'] : 0;
        $ready_distance = isset($params['ready_distance']) ? $params['ready_distance'] : 0.00;
        $city_id = isset($params['city_id']) ? $params['city_id'] : 0;

        if(isset($params['ready_time'])) {
            unset($params['ready_time']);
        }
        if(isset($params['ready_distance'])) {
            unset($params['ready_distance']);
        }
        if(isset($params['city_id'])) {
            unset($params['city_id']);
        }

        unset($params['name']);
        unset($params['phone']);
        unset($params['car_number']);

	$driver_id = Push::DEFAULT_DRIVER_INFO;
	if(isset($params['driver_id'])) {
	  $driver_id = $params['driver_id'];
	  unset($params['driver_id']);
	}

	//查找订单
	// For orders (call order or driver booking)
	$inner_order_id = $params['order_id'];
	if(strlen($params['order_id']) > 11) {
	    $order = Order::getDbReadonlyConnection()->createCommand()
		->select('*')
		->from('t_order')
		->where('order_number = :order_number' , array(':order_number' => $params['order_id']))
		->queryRow();
	    if(!empty($order)) {
		$inner_order_id = $order['order_id'];
	    }
	}


    // changes from Guoxin introduce param 'driver_receive_time' which is no responding column in t_order_position
    // so need check and unset it
    if(isset($params['driver_receive_time'])) {
        unset($params['driver_receive_time']);
    }
    $result = OrderPosition::model()->insertInfo($params);
	$convert_pos = isset($result['position'])? $result['position']:array();
        //更新订单redis
        switch ($params['flag']) {
            case OrderPosition::FLAG_ACCEPT :
                $order_state = OrderProcess::ORDER_PROCESS_ACCEPT ;
		// Update the driver id and order id into redis
		RDriverPosition::model()->setCurrentOrder(
			$driver_id,
			$inner_order_id, OrderProcess::PROCESS_ACCEPT,
			$convert_pos);
                break;
            case OrderPosition::FLAG_ARRIVE:

            	//记录log
		        EdjLog::info($params['order_id'].'|302司机就位' , 'console');

                $order_state = OrderProcess::ORDER_PROCESS_READY ;
		// Update the driver id and order id into redis
		RDriverPosition::model()->updateCurrentOrder(
			$driver_id,
			$inner_order_id, OrderProcess::PROCESS_READY,
			$convert_pos);
                break;
            case OrderPosition::FLAG_START:

            	//记录log
		        EdjLog::info($params['order_id'].'|303司机开车' , 'console');

                $order_state = OrderProcess::ORDER_PROCESS_DRIVING ;
		// Update the driver id and order id into redis
		RDriverPosition::model()->updateCurrentOrder(
			$driver_id,
			$inner_order_id, OrderProcess::PROCESS_DRIVING,
			$convert_pos);
                break;
            case OrderPosition::FLAG_FINISH:
            	//记录log
		EdjLog::info($params['order_id'].'|501结束代驾' , 'console');

                $order_state = OrderProcess::PROCESS_DEST;

		// Update the driver id and order id into redis
		RDriverPosition::model()->updateCurrentOrder(
			$driver_id,
			$inner_order_id, OrderProcess::PROCESS_DEST,
			$convert_pos);
                break;
            default:
                $order_state = OrderProcess::ORDER_PROCESS_ACCEPT ;
                break;
        }
        CustomerApiOrder::model()->updateOrderRedisByOrderFlag(
	    $params['order_id'] , $order_state, $driver_id);
        //更新订单redis

        $this->safelyApplyNewCustomerFreeActivity($params['flag'], $params['order_id'], true);

        $this->safelyApplyArriveInTimeActivity($params['flag'], $inner_order_id, $ready_time);

        return $result['ret'];
    }

    /**
     * 司机报单
     * @author mengtianxue 2013-05-26
     * @modified qiujianping@edaijia-inc.cn 2014-04-08
     *   Add Check if the submitted order is bind with the driver,
     *  If not, bind it.
     * @param $params
     * @return bool
     */
    public function order_submit($params){
      // Check if the driver id is bind with the order
      $order = NULL;
      if(empty($params['order_id'])){
        $order = Order::getDbMasterConnection()->createCommand()
            ->select('*')
            ->from('t_order')
            ->where('order_number=:order_number', array(':order_number' => $params['order_number']))
            ->queryRow();
	if(empty($order)){
	  EdjLog::info('|'.$params['order_number'].
	      '|Cannot find order with order number' , 'console');
	  return false;
	}else{
	  $params['order_id'] = $order['order_id'];
	}
      } else {
	$order =  Order::model()->getOrderById($params['order_id']);
      }
      if(empty($order)){
	EdjLog::info('|'.$params['order_id'].'|Cannot find order with order id' , 'console');
	return false;
      }

      if($order['driver_id'] == Push::DEFAULT_DRIVER_INFO &&
	  $params['driver_id'] != Push::DEFAULT_DRIVER_INFO){
	// Rebind
        $map = OrderQueueMap::getDbReadonlyConnection()->createCommand()
            ->select('*')
            ->from('t_order_queue_map')
            ->where('order_id = :order_id' , array(':order_id' => $params['order_id']))
            ->order('id ASC')
            ->queryRow();
	if(!empty($map)) {
	  EdjLog::info('|'.$params['order_id'].'|'.$params['driver_id'].
	      '|Rebind order receveive actions' , 'console');
	  Push::model()->redoOrderReceive($map['queue_id'],
	      $params['order_id'], $params['driver_id']);
	}
      }else if($order['driver_id'] == Push::DEFAULT_DRIVER_INFO &&
	  $params['driver_id'] == Push::DEFAULT_DRIVER_INFO){
	EdjLog::info('|'.$params['order_id'].'|Cannot bind the driver, Both driver ids are default' , 'console');
      } else {
	// Normal case, ignore it
      }

      // End bind

        //记录log
	    EdjLog::info($params['order_id'].'|501司机保单|'.$params['driver_id'].'|begin' , 'console');

        $gps_position=array(
            'latitude'=>$params['lat'],
            'longitude'=>$params['lng']
        );

        $gps = GPS::model()->convert($gps_position, $params['gps_type']);
        $params['location_end'] = empty($gps) ? '' : $gps['street'];
        $params['end_time'] = isset($params['end_time']) ? $params['end_time'] : date('Y-m-d H:i:s');

        $return = Order::model()->submit_order($params);

        //选司机下单 推送虚拟的order_id
        if (isset($params['unique_order_id'])) {
        	AutoOrder::model()->push_order_submit($params['driver_id'], $params['unique_order_id'], $return);
        	DalOrder::model()->updateOldCacheData('' , $params['unique_order_id'] , '' , OrderProcess::ORDER_PROCESS_FINISH, '', $params['location_end']);

                //更新订单状态机 by wangjian 2014-03-26
		// 2014-03-26 BEGIN
		if ($return) {
    	          $real_order_id = ROrder::model()->getOrder($params['unique_order_id'] , 'order_id');
		  if (empty($real_order_id)) {
		      EdjLog::warning($params['order_id'].'|501司机自动报单|'.$params['driver_id'].'|状态机更新获取real_order_id失败|end' , 'console');
		      $real_order_id = $params['order_id'];
		  }
		  OrderProcess::model()->genNewOrderProcess(
		      array( 'queue_id'  => $real_order_id,
			'order_id'  => $real_order_id,
			'driver_id' => $params['driver_id'],
			'state'     => OrderProcess::PROCESS_AUTO_SUBMIT,
			'created'=>date('Y-m-d H:i:s' , time()),
			)
		      );
		}
		// 2014-03-26 END
        } else {
        	AutoOrder::model()->push_order_submit($params['driver_id'], $params['order_id'], $return);
        	CustomerApiOrder::model()->updateOrderRedisByOrderFlag(
                $params['order_id'] , OrderProcess::ORDER_PROCESS_FINISH, $params['driver_id'], '', 0,
                $params['location_end']
            );
        }
        
        if ($return) {
        	//记录log
		    EdjLog::info($params['order_id'].'|501司机保单|'.$params['driver_id'].'|报单成功|end' , 'console');
            $this->badWeatherChargeDrvier($order);
        } else {
        	//记录log
		    EdjLog::warning($params['order_id'].'|501司机保单|'.$params['driver_id'].'|报单失败|end' , 'console');
        }

        return $return;
    }
    /**
     * 恶劣天气加价 但是vip客户下单不能加价，所以加价费公司以信息费冲入司机账户
     * @param $order
     */
    public  function  badWeatherChargeDrvier($order){
        $fee = OrderExt::model()->getBadWeatherSurchargeByOrderId($order['order_id']);//是否恶劣天气加价的金额
        $phone = isset($order['phone']) ? $order['phone'] : '';
        if($fee){
            $isVip = VipService::service()->isVip($phone);
            if($isVip){
                //如果该单符合恶劣天气条件下单但是下单客户为vip客户，那么加价的金额以信息费冲入司机账户
                $smsComment = "师傅您好单号:".$order['order_id']."的订单满足恶劣天气加价要求，加价金额".$fee."元作为信息费已经充入您的信息费账户!";
                $channel = EmployeeAccount::CHANNEL_DRIVER_MODIFY_FEE;
                $comment = "恶劣天气加价但客户为vip下单加价金额作为信息费冲入司机账户 单号:".$order['order_id'].".";
                //TODO ...  need charge driver, 是一种充值的类型
                $status = DriverAccountService::orderCharge($fee, $order, $comment, $channel);
                if($status){
                    $driver_phone = $order['driver_phone'];
                    Sms::SendSMS($driver_phone,$smsComment);
                }
            }
        }
    }

    /**
     * 一键预约生成订单走队列
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-05-30
     */
    public function queue_booking($params) {
        $model = new OrderQueue();
        $model->attributes = $params;
        if ($model->save()) {
            echo "----保存数据成功----\n";
            return true;
        }else {
            echo "----保存数据失败----\n";
            return false;
        }
    }


    /**
     * 推送弹回记录日志
     * @author zhanglimin 2013-06-05
     * @param $params
     * @return bool
     */
    public function push_order_reject_log($params = array()){
    	//记录log
		EdjLog::info($params['queue_id'].'|'.$params['order_id'].'|405司机拒单|'.$params['driver_id'].'|begin' , 'console');
//        $arr = $params;
//        if (isset($params['order_id'])) {
//            unset($params['order_id']);
//        }
        //增加两个字段(order_id type) BY AndyCong  2013-12-26
        $model = new OrderRejectLog();
        $model->attributes = $params;
        CustomerApiOrder::model()->driverReject($params);
        if ($model->save()) {

            //记录log
            EdjLog::info($params['queue_id'].'|'.$params['order_id'].'|405司机拒单|'.$params['driver_id'].'|log记录成功|end' , 'console');

            return true;
        }else {

        	//记录log
        	EdjLog::info($params['queue_id'].'|'.$params['order_id'].'|405司机拒单|'.$params['driver_id'].'|log记录失败|end' , 'console');

            echo "----推送弹回日志记录失败----\n";
            return false;
        }
    }

    /**
     * 手机预注册队列处理
     * @author zhanglimin 2013-06-07
     * @param $params
     * @return bool
     */
    public function push_register_driver_phone($params){
        $imei = $params['imei'];
        $sim = $params['sim'];
        $phone = $params['phone'];
        $ret = DriverPhone::model()->registerDriverPhone($imei, $sim , $phone , "");
        if($ret['code']==0){
            echo "----手机预注册队列处理成功----\n";
            return true;
        }else{
            echo "----手机预注册队列处理失败----\n";
            return false;
        }
    }


    /**
     * 司机销单队列处理
     * @author zhanglimin 2013-06-07
     * @param $params
     * @return bool
     */
    public function push_order_cancel($params){

        $attr = array (
            'cancel_desc'=>$params['log'],
            'cancel_type'=>Common::convertCancelType($params['cancel_type']),
            'status'=>Order::ORDER_COMFIRM);

        if (Order::model()->updateByPk($params['order_id'], $attr)){

            if ($params['isComplaint'] == 1) {
                $complaintArr=array();
                $complaintArr['order_id'] = $params['order_id'];
                $complaintArr['driver_user'] = $params['driver_id'];
                $complaintArr['customer_name'] = $params['name'];
                $complaintArr['city'] = $params['city_id'];
                $complaintArr['customer_phone'] = $params['phone'];
                $complaintArr['order_type'] = Order::ORDER_COMFIRM;
                $complaintArr['complaint_type'] = Common::convertCancelType($params['complaint_type'],1);
                $complaintArr['complaint_content'] = $params['complaint'];
                $complaintArr['driver_time'] = $params['start_time'];//只记录出发时间
                $complaintArr['complaint_status'] = 0;
                DriverComplaint::model()->saveDriverComplaint($complaintArr);
            }
            $log_attr = array (
                'order_id'=>$params['order_id'],
                'description'=>$params['log'],
                'operator'=>$params['driver_id'],
            );
            OrderLog::model()->insertLog($log_attr);

            /**
             * 第三方合作，司机销单后操作
             * author : zhangtingyi 2013-11-04
             */
            $order_channel = Order::model()->getOrderChannel($params['order_id']);
            if ($order_channel) {
                $partner = PartnerFactory::factory($order_channel);
                if ($partner instanceof AbstractPartner) {
                    if (method_exists($partner,'cancelOrderHandler')) {
                        $partner->cancelOrderHandler($params['order_id']);
                    }
                }
            }

            //取消优惠劵使用
            BonusLibrary::model()->BonusUsed($params['phone'], $params['order_id'], 0, 2);

            $cancel_type = $params['cancel_type'] == 0 ? '' : Dict::item('qx_o_type', $params['cancel_type']);
            CustomerApiOrder::model()->updateOrderRedisByOrderFlag(
		$params['order_id'] , OrderProcess::ORDER_PROCESS_DRIVER_DESTORY ,
		$params['driver_id'], $cancel_type);
            
            OrderStatusChangedPublisher::addQueue(array(
                'orderId'   => $params['order_id'],
                'status'    => OrderProcess::ORDER_PROCESS_DRIVER_DESTORY,
                'message'   => '司机取消订单',
                'from'      => 'from',
                'driverId'  => $params['driver_id'],
                'phone'     => $params['phone']
            ));
            
            echo "----司机销单队列处理成功----\n";
            return true;
        }else{
            echo "----司机销单队列处理失败----\n";
            return false;
        }
    }


    /**
     * 新司机销单队列处理
     * @author zhanglimin 2013-07-31
     * @param $params
     * @return bool
     */
    public function push_new_order_cancel($params){
        $attr = array (
            'cancel_desc'=>$params['log'],
            'cancel_type'=>$params['cancel_type'],
            'status'=>Order::ORDER_COMFIRM);

        $cancelComplaintLog = array(
            'driver_id' => $params['driver_id'],
            'phone' => $params['phone'],
            'order_id' => $params['order_id'],
            'order_number' => $params['order_number'],
            'cid' => $params['cancel_type'],
            'cid_desc' => $params['log'],
            'content' => $params['content'],
            'isComplaint' => $params['isComplaint'],
            'source' => $params['source'],
        );

        if(!empty($params['order_id'])){
            if (Order::model()->updateByPk($params['order_id'], $attr)){


                if ($params['isComplaint'] == 1) {
                    $complaintArr=array();
                    $complaintArr['order_id'] = $params['order_id'];
                    $complaintArr['driver_user'] = $params['driver_id'];
                    $complaintArr['customer_name'] = $params['name'];
                    $complaintArr['city'] = $params['city_id'];
                    $complaintArr['customer_phone'] = $params['phone'];
                    $complaintArr['order_type'] = Order::ORDER_COMFIRM;
                    $complaintArr['complaint_type'] = $params['complaint_type'];
                    $complaintArr['complaint_content'] = $params['complaint'];
                    $complaintArr['driver_time'] = $params['start_time'];//只记录出发时间
                    $complaintArr['complaint_status'] = 0;
                    DriverComplaint::model()->saveDriverComplaint($complaintArr);
                }
                $log_attr = array (
                    'order_id'=>$params['order_id'],
                    'description'=>$params['log'],
                    'operator'=>$params['driver_id'],
                );
                OrderLog::model()->insertLog($log_attr);

                //记录销单或投诉Log开始
                CancelComplaintLog::model()->insertLog($cancelComplaintLog);
                //记录销单或投诉Log结束

                //更新redis BY AndyCong
                $cancel_type = $params['cancel_type'] == 0 ? '' : Dict::item('qx_o_type', $params['cancel_type']);
                CustomerApiOrder::model()->updateOrderRedisByOrderFlag($params['order_id'] ,
		    OrderProcess::ORDER_PROCESS_DRIVER_DESTORY , $params['driver_id'], $cancel_type);
                //更新redis BY AndyCong END

                /**
                 * 第三方合作，司机销单后操作
                 * author : zhangtingyi 2013-11-04
                 */
                $order_channel = Order::model()->getOrderChannel($params['order_id']);
                if ($order_channel) {
                    $partner = PartnerFactory::factory($order_channel);
                    if ($partner instanceof AbstractPartner) {
                        if (method_exists($partner,'cancelOrderHandler')) {
                            $partner->cancelOrderHandler($params['order_id']);
                        }
                    }
                }

                //取消优惠劵使用
                BonusLibrary::model()->BonusUsed($params['phone'], $params['order_id'], 0, 2);

                echo "----司机销单队列处理成功----\n";
                return true;
            }else{
                echo "----司机销单队列处理失败----\n";
                return false;
            }
        }else{

            //记录销单或投诉Log开始
            CancelComplaintLog::model()->insertLog($cancelComplaintLog);
            //记录销单或投诉Log结束
            echo "----司机销单队列处理成功----\n";
            return true;

        }



    }

    /**
     * 司机补单处理
     * @author zhanglimin 2013-06-07
     * @param $params
     * @return bool
     */
    public function push_order_create($params){
        //验证订单是否有补单成功过 BY AndyCong 2013-08-27
        $driver_id = isset($params['driver_id']) ? trim($params['driver_id']) : '';
        $order_number = isset($params['order_number']) ? trim($params['order_number']) : '';

        if (!empty($order_number)) {
            $order = Order::model()->find('order_number = :order_number and driver_id = :driver_id' , array(':order_number' => $order_number , 'driver_id' => $driver_id));
            if ($order) {
                return true;
            }
        }

        //验证订单是否有补单成功过 BY AndyCong 2013-08-27 END

        $model = new Order();
        //补录订单忽略检查每小时只能一个客户订单以及司机内部电话
        $builder = $model->getCommandBuilder();
        $table = $model->getMetaData()->tableSchema;
        $command = $builder->createInsertCommand($table, $params);
        if ($command->execute()) {
            $order_id = $builder->getLastInsertID($table);

            //保存order_queue
            echo "\n order_id is ".$order_id."\n";
            $data = array();
            $data['phone'] = $params['phone'];
            $data['city_id'] = $driver_id ? DriverStatus::model()->getItem($driver_id,'city_id') : 0;
            $data['address'] = '暂未获取';
            $data['booking_time'] = date('Y-m-d H:i:s' , $params['booking_time']);
            $data['dispatch_number'] = 1;
            $data['dispatch_time'] = date('Y-m-d H:i:s' , time());
            $data['flag'] = OrderQueue::QUEUE_SUCCESS;
            $queue = CustomerApiOrder::model()->save_order_queue($data , $params['source'] , '司机补单');

            if (!empty($queue)) {
                echo "\n queue_id is ".$queue['id']."\n";
                $confirm_time=date("Y-m-d H:i:s", time());
                $attributes=array(
                    'order_id'=>$order_id,
                    'queue_id'=>$queue['id'],
                    'driver_id'=>$driver_id,
                    'confirm_time'=>$confirm_time
                );
                @OrderQueueMap::getDbMasterConnection()->createCommand()->insert('t_order_queue_map', $attributes);

                // 记录订单状态机
                OrderProcess::model()->genNewOrderProcess(
	            array( 'queue_id'  => $queue['id'],
	                   'order_id'  => $order_id,
	        	   'driver_id' => $driver_id,
	        	   'state'     => OrderProcess::PROCESS_DRIVER_CREATE,
	        	   'created'   => date("Y-m-d H:i:s", time()),
	            )
	        );
            }

            CustomerApiOrder::model()->orderFavorableCache($order_number);
            //优惠劵的占用  补单不让使用优惠劵  金竹要求  11月13号
//            BonusLibrary::model()->BonusOccupancy($params['phone'], $order_id, $params['source']);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 发票信息处理
     * @author zhanglimin 2013-06-08
     * @param $params
     * @return bool
     */
    public function push_order_invoice($params){
        $orderInvoice = OrderInvoice::model()->find('order_id=:order_id', array(':order_id'=>$params['order_id']));
        if (!$orderInvoice) {
            $orderInvoice = new OrderInvoice();
            $invoice = array();
            $invoice['order_id'] = $params['order_id'];
            $invoice['title'] = '';
            $invoice['content'] = '';
            $invoice['contact'] = '';
            $invoice['telephone'] = '';
            $invoice['address'] = '';
            $invoice['zipcode'] = '';
            $orderInvoice->attributes = $invoice;
            $flag = $orderInvoice->save();
            return $flag;
        }else{
            return false;
        }

    }

    /**
     * 发票更新处理
     * @author zhanglimin 2013-06-08
     * @param $params
     * @return int
     */
    public function push_order_invoice_update($params){
        $orderInvoice = OrderInvoice::model()->find('order_id=:order_id', array(':order_id'=>$params['order_id']));
        if (!$orderInvoice)
            $orderInvoice = new OrderInvoice();

        $invoice = $orderInvoice->attributes;

        $invoice['order_id'] = $params['order_id'];
        $invoice['title'] = $params['invoice_title'];
        $invoice['content'] = $params['invoice_content'];
        $invoice['contact'] = $params['invoice_contact'];
        $invoice['telephone'] = $params['invoice_telephone'];
        $invoice['address'] = $params['invoice_address'];
        $invoice['zipcode'] = $params['invoice_zipcode'];

        $flag = $orderInvoice->updateByPk($params['order_id'], $invoice);

        return $flag;
    }

    /**
     * 读取未读公司公告处理
     * @author zhanglimin 2013-06-08
     * @param $params
     */
    public function push_driver_notice_read($params){
        $flag = NoticeRead::model()->noticeReadSave($params);
        return $flag;
    }

    /**
     * 读取新未读公司公告处理
     * @author zhanglimin 2013-08-28
     * @param $params
     */
    public function push_driver_new_notice_read($params){
        $flag = NewNoticeDriverRead::model()->newNoticeReadSave($params);
        return $flag;
    }

    /**
    *  update customer complain by driver read
    *  aiguoxin
    *
    */
    public function push_customer_complain_read($params){
        return CustomerComplain::model()->updateDriverRead($params['notice_id'],$params['driver_id']);
    }

    /**
     * 司机投诉客户保存
     * @param $params
     * @return bool
     * author mengtianxue
     */
    public function driver_complain($params)
    {
        $order = Order::model()->getOrderById($params['order_id']);
        if (!empty($order) && $order['driver_id'] == $params['driver_id']) {
            $complaintArr = array();
            $complaintArr['order_id'] = $order['order_id'];
            $complaintArr['driver_user'] = $order['driver_id'];
            $complaintArr['customer_name'] = $order['name'];
            $complaintArr['city'] = $order['city_id'];
            $complaintArr['customer_phone'] = $order['phone'];
            $complaintArr['order_type'] = $order['status']; //1为报单
            $complaintArr['complaint_type'] = Common::convertCancelType($params['type'],1);
            $complaintArr['complaint_content'] = $params['content'];
            $complaintArr['driver_time'] = $order['start_time']; //只记录出发时间
            $complaintArr['complaint_status'] = 0;
            $complaintArr['created'] = time();
            return DriverComplaint::model()->saveDriverComplaint($complaintArr);
        }else{
            echo "------未找到订单或参数错误------\n";
        }
        return false;
    }

    /**
     * 流量统计
     * @author zhanglmin 2013-06-24
     * @param $params
     */
    public function driver_app_traffic($params){
        $driverAppTraffic =  DriverAppTraffic::model()->find(' driver_id=:driver_id and in_date =:in_date',
            array(
                'driver_id'=>$params['driver_id'],
                'in_date'=>$params['in_date'],
            ));
        if(empty($driverAppTraffic)){
            $result = DriverAppTraffic::model()->insertInfo($params);
        }else{
            $result = DriverAppTraffic::model()->updateInfo($params);
        }
        return $result;
    }

    /**
     * 生成用户客户端auth_token
     * @param array $params
     * @return boolean $result
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-07-02
     */
    public function app_auth_token($params) {
        $model = new AppAuthToken();
        $model->attributes = $params;
        $result = $model->save();
        return $result;
    }

    /**
     * 记录位置上报，抛弃存入mongo的日志
     * @param array $params
     */
    public function driver_position_miss($params) {
        /*
                    'driver_id'=> $params['driver_id'],
                    'gps_timestamp' => $params['gps_timestamp'],
                    'gps_time'=> date("Y-m-d H:i:s", $params['gps_timestamp']),
                    'last_gps_timestamp' => $last_gps_timestamp,
                    'last_gps_time'=>date("Y-m-d H:i:s", $last_gps_timestamp),
         */
        $log_format = "\n%s|%s|%s|%s\n";

        $driver_id = isset($params['driver_id']) ? $params['driver_id'] : "";

        $gps_timestamp = isset($params['gps_timestamp']) ? $params['gps_timestamp'] : "";
        $gps_time = isset($params['gps_time']) ? $params['gps_time'] : "";
        $last_gps_timestamp = isset($params['last_gps_timestamp']) ? $params['last_gps_timestamp'] : "";
        $last_gps_time = isset($params['last_gps_time']) ? $params['last_gps_time'] : "";

        $log = sprintf($log_format, $driver_id, $gps_timestamp, $gps_time, $last_gps_timestamp, $last_gps_time);
        echo $log;
    }

    /**
     * 呼叫生成时间(新)
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-07-03
     */
    public function call_order($params = array()){
        //验证订单存在否
        $order = Order::model()->find('order_number = :order_number' , array(':order_number' => $params['order_number']));
        if ($order) {
            return false;
        }
        $time = time();
        $data = array(
            'driver_id'=>$params['driver_id'],
            'phone'=>$params['phone'],
            'city_id'=>$params['city_id'],
            'name' => '先生',
            'call_time' => $params['booking_time'],  //呼叫时间改成预约时间前20分钟
            'booking_time'=> $params['booking_time'],
            'address'=>$params['address'],
            'lng'=>$params['longitude'],
            'lat'=>$params['latitude'],
            'order_number' => $params['order_number'],
	    'call_type' => isset($params['call_type']) ? $params['call_type'] : '',
        );
        $ret = AutoOrder::model()->call_order($data);
        if(!$ret || $ret['code']==2){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 测试新队列处理方法
     * @author sunhongjing 2013-07-11
     *
     * @param unknown_type $params
     * @return bool
     */
    public function queue_worker_test($params = array()){
        //添加task队列
        print_r($params);
        return true;
    }


    /**
     * 上报deviceToken
     * @author zhanglimin 2013-07-12
     * @param $params
     */
    public function customer_device($params = array()){
        $flag =  CustomerToken::model()->setDeviceToken($params);
        return $flag;
    }

    /**
     * 400重构接单处理
     * @param array $params
     * @author AndyCong<congming@edaijia>
     * @version 2013-07-20
     */
    public function order_receive_operate($params) {
        $result = Push::model()->OrderReceiveOperate($params);
        $this->safelyApplyArriveInTimeActivity(OrderPosition::FLAG_ACCEPT, $params['order_id']);
        $this->safelyRemoteDispatchBonusActivity(OrderPosition::FLAG_ACCEPT, $params['order_id'], $params['driver_id']);

        return $result;
    }

    /**
     * 处理消息置状态
     * @param array $params
     * @author AndyCong<congming@edaijia>
     * @version 2013-07-23
     */
    public function update_msg_flag($params) {
        $ret = Push::model()->updateOrderDetailMsgFlag($params['push_msg_id'] , $params['flag']);
        return $ret;
    }

    /**
     * 自动派单监控处理
     * @param $params
     * @return bool
     */
    public function dispatch_drivers_log($params){
        AutoDispatchLog::model()->nearbyDriversSave($params['drivers'] , $params['driver'] , $params['queue_id'], $params['order_id']);
        return true;
    }

    public function update_driver_accept_log($params) {
        AutoDispatchLog::model()->acceptDriverStatusUpdate($params);
        return true;
    }

    public function  order_settle($params){
        $id = $params['id'];
	    $settleParams = isset($params['settle_params']) ? $params['settle_params'] : array();
		$order = Order::model()->getOrderById($id);
		$orderExt = OrderExt::model()->getPrimary($id);
		$isRemote = FinanceUtils::isRemoteOrder($order, $orderExt);
	    $orderType = FinanceUtils::getOrderType($order);
	    if(FinanceUtils::isParamSettle($orderType)){
			return OrderSettlement::model()->submitSettle($id, $settleParams);
	    }
		if($isRemote){
			OrderSettlement::model()->remoteOrderSettle($id);
		}else{
			OrderSettlement::model()->orderSettle($id);
		}
        return true;
    }


    /**
     * 通过音频建立订单并派送
     * @author zhanglimin
     * @param array $params
     * @version 2013-08-08
     * @return bool
     */
    public function customer_gen_order($params = array()){
        if(empty($params)){
            return false;
        }
        Push::model()->audioBooking($params);
        return true;
    }

    /**
     * 设置订单状态机，插入数据表
     *
     * @author sunhongjing 2013-10-17
     *
     * @param unknown_type $params
     * @return bool
     */
    public function set_order_process_state($params = array())
    {
      //验证参数
      if(empty($params)){
        echo "参数错误\n";
        return false;
      }

      if( empty($params['queue_id']) || empty($params['order_id']) || empty($params['state']) ){
        EdjLog::info("Invalid params|queue_id,order_id,statemaybe null");
        return false;
      }
      $ret = OrderProcess::model()->insertNewOrderStatus($params);
      if($ret) {
        EdjLog::info("Success|".$params['queue_id']."|".$params['order_id']."|".$params['state']);
      } else {
        EdjLog::info("Failed|".$params['queue_id']."|".$params['order_id']."|".$params['state']);
      }

      // Save reject detail
      if(isset($params['fail_type']) && ($params['fail_type'] == 2 ||
	      $params['fail_type'] == 3)) {
	  $ret = DriverRejectOrderDetail::model()->createNewRejectDetail($params);
      }
      return $ret;
    }

    /**
     * 仿滴滴打车接单处理
     * @param array $params
     * @author zhanglimin
     * @version 2013-08-08
     */
    public function order_audio_receive_operate($params) {
        $result = Push::model()->AudioOrderReceiveOperate($params);
        return $result;
    }

    /**
     * 把记smslog迁移出来，没验证，先凑合用吧
     *
     * @author sunhongjing 2013-08-09
     *
     * @param unknown_type $params
     */
    public function mark_sms_send_log($params){
        $ret = Yii::app()->dbreport->createCommand()->insert('t_sms_log', $params);
        echo $ret ? "------成功-----\n" : "----失败----\n";
        return $ret;
    }

    /**
     * 记录RestaurantToken表数据
     * @author zhanglimin 2013-08-19
     * @param $params
     * @return bool
     */
    public function restaurant_login($params) {
        $ret = array('flag'=>false,'name'=>'');
        $user = RestaurantToken::model()->checkPhone($params['phone']);
        if(!empty($user)){
            //检查用户是否在token表中存在
            $restaurantToken =  RestaurantToken::model()->find(" user_id=:user_id",array('user_id'=>$user['user_id']));
            unset($params['phone']);
            $params['created'] = date('Y-m-d H:i:s');
            if(empty($restaurantToken)) {
                $params['user_id'] = $user['user_id'];
                $restaurantToken = new RestaurantToken();
                $restaurantToken->attributes = $params;
                $restaurantToken->save();
            }else{
                $restaurantToken->authtoken = $params['authtoken'];
                $restaurantToken->created = $params['created'];
                $restaurantToken->update(array('authtoken','created'));
            }

            $userInfo = AdminUserNew::model()->findByPk(intval($user['user_id']));

            if(!empty($userInfo)){
                $ret = array('flag'=>true,'name'=>$userInfo->name);
            }
        }
        return $ret;
    }

    /**
     * 添加商铺数据
     * @author zhanglimin 2013-08-19
     * @param array $params
     * @return bool|string
     */
    public function restaurant_add($params = array()){
        if(empty($params)){
            return false;
        }

        $params['created'] = date("Y-m-d H:i:s");

        $flag = Restaurant::model()->insertInfo($params);

        return $flag;

    }

    /**
     * 更新手机SIM号并将多于的删除
     * @author zhanglimin 2013-08-19
     */
    public function update_driver_phone_sim($data = array()){
        if(empty($data)){
            return false;
        }
        $driverPhones = DriverPhone::model()->find('imei=:imei and simcard=:sim', array(':imei'=>$data['imei'], ':sim'=>$data['sim']));
        if(!empty($driverPhones)){

            $att = $driverPhones->attributes;

            echo "delete driver_phone old data:  driver_id:{$att['driver_id']},imei:{$att['imei']},simcard:{$att['simcard']},is_bind:{$att['is_bind']},phone:{$att['phone']},device:{$att['device']}\n";

            $sql="delete from {{driver_phone}} where driver_id=:driver_id";
            $command=Yii::app()->db->createCommand($sql);
            $command->bindParam(":driver_id",$att['driver_id']);
            $flag=$command->execute();
            if($flag){

                $newDriver = new DriverPhone();
                $newDriver->unsetAttributes();
                $newDriver->driver_id=$att['driver_id'];
                $newDriver->imei=$att['imei'];
                $newDriver->is_bind=$att['is_bind'];
                $newDriver->simcard=$data['sim_new'];
                $newDriver->device = $att['device'];
                $newDriver->phone=$att['phone'];
                if($newDriver->save()){
                    //更新缓存
                    $driverStatus = DriverStatus::model()->get($att['driver_id']);
                    $set = $driverStatus->info;
                    $set['simcard'] = $data['sim_new'];
                    $driverStatus->info = $set;
                    unset($set);
                }
                return true;
            }
        }


    }

    /**
     * 插入日志信息
     * @author zhanglimin 2013-08-23
     * @param array $params
     * @return bool
     */
    public function operation_log_insert($params = array()){
        if(empty($params)){
            return false;
        }
        return Yii::app()->db->createCommand()->insert('{{operation_log}}',$params);
    }


    /**
     * 语音即时发送播报日志 并推送到司机客户端
     * @author zhanglimin 2013-08-31
     * @param array $params
     * @return bool
     */
    public function notice_immediatepost($params = array()){
        if(empty($params)){
            return false;
        }

        $flag = Yii::app()->db->createCommand()->insert('{{audio_promptly_log}}',$params);
        if($flag){
            $pus_data = array(
                'notice_id' => AudioPromptlyLog::SET_AUDIO_PROMPT_VALUE,
                'content' => '',
                'url' => $params['audio_url'],
                'city_id' => $params['city_id'],
                'type' => IGtPush::TYPE_NOTICE_DRIVER_UPY,
                'category'=>'即时语音播报',
                'title'=>'即时语音',
                'created'=>date("m-d H:i"),
                'audio_time'=>$params['audio_second'], //语言时长
                //暂时测试工号的id
                //'drivers'=>Common::getTestDriverIds(1),

            );
            $v = PushMessage::model()->pushNoticeAudio($pus_data);
        }
        return $flag;

    }

    /**
     * 客户访问nearby时记录日志
     *
     * @author sunhongjing 2013-09-04
     * @param unknown_type $params
     * @return bool
     */
    public function customer_access_heatmap($params = array()){
        if(empty($params)){
            return false;
        }
        if( empty($params['udid']) ){
            return false;
        }
        $check_sum = intval($params['lng']) + intval($params['lat']);
        if ( $check_sum < 10 ){
            return false;
        }

        $model = new CustomerAccessLog();
        $model->attributes = $params;
        $result = $model->save();
        return $result;
    }

    /**
     * 添加语音公告
     * @author zhanglimin 2013-08-31
     * @param array $params
     * @return bool
     */
    public function notice_datepost($params = array()){
        if(empty($params)){
            return false;
        }
        $categorys = array_flip(NewNotice::$categorys);
        if(!isset($categorys[$params['category']])){
            return false;
        }

        $user_id = $params['opt_user_id'];
        unset($params['opt_user_id']);
        $userInfo = Yii::app()->db_readonly->createCommand()
            ->select('name')
            ->from('t_admin_user')
            ->where('user_id=:user_id', array(':user_id'=>$user_id))
            ->queryRow();
        if(empty($userInfo)){
            return false;
        }
        $date = date("Y-m-d H:i:s");
        $params['category'] = $categorys[$params['category']] ;
        $params['type'] = 1 ;
        $params['create_time'] = $date;
        $params['update_time'] = $date;
        $params['opt_user'] = $userInfo['name'];
        $params['update_user'] = $userInfo['name'];
        return NewNotice::model()->insertData($params);
    }

    /**
     *
     * 处理发布添加队列
     * @author 曾志海  2013-09-02
     * @param array $params
     * @return bool
     */
    public function punish_audio_news($params=array()){

            if($params['booking_push_flag']==NewNotice::STOP){
                if($params['type']==NewNotice::VOICE){
                    //音频类型的通过后直接推送
                    $data = array(
                        'notice_id'=>$params['id'],
                        'city_id'=>$params['city_id'],
                        'content'=>$params['content'],
                        'url'=>$params['audio_url'],
                        'category'=>$params['category'],
                        'title'=>$params['title'],
                        'created'=>$params['created'],//预约发布时间
                        'audio_time'=>$params['audio_time'], //语音时长
                    );
                    NewNotice::model()->pushAudioMsg($data);
                }
                //add by aiguoxin push unread notice
                EdjLog::info('push message to driver..............');
                NewNotice::model()->pushCommonMsg($params['city_id']);
            }
        return true;
    }

    /**
     * 记录拒不升级的用户
     * @author zhanglimin 2013-09-17
     * @param string $driver_id
     * @return bool
     */
    public function push_no_update_version($params){
        if(empty($params)){
            return false;
        }
        $data = array(
            'route'=>'driver/login',   //路由全称
            'mod_code'=>'1003' , //模块编号
            'opt_type'=>'insert',  //操作类型
            'data_log'=> trim(strtoupper($params['driver_id'])),  //保存数据
            'opt_user'=>'system', //当前操作人
        );

        $get_sql = 'select id from {{operation_log}} where opt_type=:opt_type and mod_code=:mod_code and data_log=:data_log';
        $command = Yii::app()->db_readonly->createCommand($get_sql);
        $ret= $command->queryRow(true,array(':opt_type'=>$data['opt_type'],':mod_code'=>$data['mod_code'],'data_log'=>$data['data_log']));

        if(empty($ret)){
            OperationLog::model()->insertLog($data);
        }
        return true;
    }


    /**
     * 选司机下单
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2013-10-16
     */
    public function api_customer_single($params) {
        if(CustomerApiOrder::model()->isBlockCustomerByConfig($params)) {
            return false;
        }
        
        $result = CustomerApiOrder::model()->single_order($params);
        echo $result ? "-派单成功-\n" : "-派单失败-\n";
        return $result;
    }

    /**
     * 新单人下单测试方法
     *
     * @author sunhongjing 2013-12-31
     * @param unknown_type $params
     * @return boolean
     */
    public function app_single_booking($params)
    {
        if(CustomerApiOrder::model()->isBlockCustomerByConfig($params)) {
            return false;
        }
        
        $result = DalOrder::model()->OrderSingleFactory($params);
    	if ($result) {
    		echo "\n success \n";
    	} else {
    		echo "\n\ fail n";
    	}
    }


    /**
     * 一键下单
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2013-1017
     */
    public function api_customer_multi($params) {
        if(CustomerApiOrder::model()->isBlockCustomerByConfig($params)) {
            return false;
        }
        
        $params['channel'] = isset($params['channel']) ? $params['channel'] : CustomerApiOrder::QUEUE_CHANNEL_BOOKING;
	$source = isset($params['source']) ? $params['source'] : Order::SOURCE_CLIENT;
	if(!empty($source) && $source == Order::SOURCE_WEIXIN) {
	    $queue = CustomerApiOrder::model()->save_order_queue($params,
		    Order::SOURCE_WEIXIN , OrderQueue::QUEUE_AGENT_WEIXIN);
	} else {
	    $queue = CustomerApiOrder::model()->save_order_queue($params,
		    $source, OrderQueue::QUEUE_AGENT_KEYBOOKING);
	}

        if (empty($queue)) {
            return false;
        }
        
        $queue_id = isset($queue['id']) ? $queue['id'] : '';
        //输出log
        EdjLog::info($queue_id.'|101等待派单','console');

        //增加优惠券 2014-03-19
        $bonus_sn = isset($params['bonus_sn']) ? $params['bonus_sn'] : '';
        if(!empty($bonus_sn)) {  //有优惠券 将优惠券加到缓存中
            $bonus_cache = QueueDispatchOrder::model()->queueBonusBind($queue_id , $bonus_sn);

            if($bonus_cache) {
                EdjLog::info($queue_id.'|bonus_sn:'.$bonus_sn.'缓存添加成功|101等待派单','console');
            } else {
                EdjLog::info($queue_id.'|bonus_sn:'.$bonus_sn.'缓存添加失败|101等待派单','console');
            }

        }

	// Add remote order fee for queue
        $fee = isset($params['fee']) ? $params['fee'] : '';
	if(!empty($fee)) {
	    $set_rst = QueueDispatchOrder::model()->
		setQueueRemoteOrderFee($queue_id, $fee);
	    if($set_rst) {
		EdjLog::info('Success|'.$queue_id.'|Fee:'.$fee.
			' Cache fee for remote order','console');
	    } else {
		EdjLog::info('Fail|'.$queue_id.'|Fee:'.$fee.
			' Cache fee for remote order','console');
	    }
	}

        $params['source'] = $source;
        $result = CustomerApiOrder::model()->multi_push($queue_id , $params);
        return $result;
    }
    
    /**
     * 将缓存信息加入到缓存
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2013-10-17
     */
    public function insert_order_cache($params) {
        CustomerApiOrder::model()->insert_order_cache($params);
        return true;
    }

    /**
     * 将缓存信息加入到缓存
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2013-11-27
     */
    public function insert_orders_redis($params) {
        CustomerApiOrder::model()->insert_orders_redis($params);
        return true;
    }

    /**
     * 取消OrderQueue
     * @param string $booking_id
     * @return boolean $result
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2013-10-18
     */
    public function api_queue_cancel($params) {
        $result = CustomerApiOrder::model()->cancelOrderQueue($params['phone'] , $params['booking_id']);
        return $result;
    }

    /**
     * 取消订单
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2013-10-18
     */
    public function api_order_cancel($params) {
        $cancel_queue = CustomerApiOrder::model()->cancelOrderQueue($params['phone'] , $params['booking_id']);
        $cancel_order = CustomerApiOrder::model()->cancelOrders($params['phone'] , $params['booking_id'],$params['reason_code'],$params['reason_detail']);
        return true;
    }

    /**
     * 更新订单redis
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2013-10-18
     */
    public function api_update_orders($params) {
        $result = CustomerApiOrder::model()->updateOrderRedis($params);
        return $result;
    }

    /**
     * 司机拒单处理
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2013-10-23
     */
    public function api_driver_reject_process($params) {
        $result = CustomerApiOrder::model()->driverRejectProcess($params);
        return $result;
    }

    /**
     * 司机补单(队列执行)
     * @params array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2013-10-29
     */
    public function driver_supplement_order($params) {
        $result = OrderQueue::model()->booking($params,
	    isset($params['source']) ? $params['source'] : Order::SOURCE_CLIENT_INPUT,
	    OrderQueue::QUEUE_AGENT_DRIVERBOOKING);
        return $result;
    }

    /**
     * 呼叫中心派单走推送（推送为新类型 order_new）
     * @param array $params
     * @return boolean $result
     */
    public function callcenter_handle_dispatch($params) {
        $queue_id = isset($params['queue_id']) ? $params['queue_id'] : '';
        $order_id = isset($params['order_id']) ? $params['order_id'] : '';
        $driver_id = isset($params['driver_id']) ? $params['driver_id'] : '';
        if (empty($queue_id) || empty($order_id) || empty($driver_id)) {
            return false;
        }

        $result = Push::model()->PushNewOrder($queue_id , $order_id , $driver_id);
        return $result;
    }

    /**
     * 司机消单（走新流程,没有订单生成订单）
     * @param array $params
     * @return boolean
     */
    public function driver_cancel_order($params) {
    	//记录log
        EdjLog::info($params['order_id'].'|504司机取消|'.$params['driver_id'].'|begin' , 'console');

        $order_id = isset($params['order_id']) ? $params['order_id'] : '';
        $order_number = isset($params['order_number']) ? $params['order_number'] : '';
        $order = Push::model()->getCancelOrder($order_id , $order_number);

        $params['cancel_type'] = Common::convertCancelType($params['cancel_type']);


        //订单不存在则生成一个取消订单
        if (empty($order)) {
//            Push::model()->genCancelOrder($params);
            return true;
        }

        //订单不是该司机或订单状态不为0则直接返回
        if(strtoupper($order->driver_id) != strtoupper($params['driver_id']) || $order->status != Order::ORDER_READY){
            return false;;
        }

        $attr = array (
            'cancel_desc'=>$params['log'],
            'cancel_type'=>$params['cancel_type'],
            'status'=>Order::ORDER_COMFIRM);

        $cancelComplaintLog = array(
            'driver_id'    => $params['driver_id'],
            'phone'        => $params['phone'],
            'order_id'     => $order->order_id,
            'order_number' => $params['order_number'],
            'cid'          => Common::convertCancelType($params['cancel_type']),
            'cid_desc'     => $params['log'],
            'content'      => $params['content'],
            'isComplaint'  => $params['isComplaint'],
            'source'       => $params['source'],
        );

        if (Order::model()->updateByPk($order->order_id, $attr)){


        	//记录log
        	EdjLog::info($params['order_id'].'|504司机取消|'.$params['driver_id'].'|订单取消成功|end' , 'console');

            //取消优惠劵使用
            //未处理订单中取消订单 参数params中phone格式为138****8888
	    $bonus_phone = $params['phone'];
            if( $bonus_phone === substr_replace($order->phone, "****", 3, 4) ) {
                $bonus_phone = $order->phone;
            }
            BonusLibrary::model()->BonusUsed( $bonus_phone, $order->order_id , 0 , 2);
            if ($params['isComplaint'] == 1) {
                $complaintArr=array();
                $complaintArr['order_id'] = $order->order_id;
                $complaintArr['driver_user'] = $params['driver_id'];
                $complaintArr['customer_name'] = $params['name'];
                $complaintArr['city'] = $order->city_id;
                $complaintArr['customer_phone'] = $params['phone'];
                $complaintArr['order_type'] = Order::ORDER_COMFIRM;
                $complaintArr['complaint_type'] = Common::convertCancelType($params['complaint_type'],1);
                $complaintArr['complaint_content'] = $params['complaint'];
                $complaintArr['driver_time'] = $order->start_time;//只记录出发时间
                $complaintArr['complaint_status'] = 0;
                DriverComplaint::model()->saveDriverComplaint($complaintArr);
            }
            //记录orderLog
            $log_attr = array (
                'order_id'    => $order->order_id,
                'description' => $params['log'],
                'operator'    => $params['driver_id'],
            );
            OrderLog::model()->insertLog($log_attr);

            //记录销单或投诉Log开始
            CancelComplaintLog::model()->insertLog($cancelComplaintLog);

            //更新redis BY AndyCong
            $cancel_type = $params['cancel_type'] == 0 ? '' : Dict::item('qx_o_type', $params['cancel_type']);

	    // cancel_type 可能是投诉类型
	    if(!$cancel_type) {
              $cancel_type = $params['cancel_type'] == 0 ? '' : Dict::item('ts_o_type', $params['cancel_type']);
	    }

            CustomerApiOrder::model()->updateOrderRedisByOrderFlag($order->order_id ,
		OrderProcess::ORDER_PROCESS_DRIVER_DESTORY , $params['driver_id'] ,$cancel_type);
        
            OrderStatusChangedPublisher::addQueue(array(
                'orderId'   => $params['order_id'],
                'status'    => OrderProcess::ORDER_PROCESS_DRIVER_DESTORY,
                'message'   => '司机取消订单',
                'driverId'  => $params['driver_id'],
                'phone'     => $params['phone']
            ));

            /**
             * 第三方合作，司机销单后操作
             * author : zhangtingyi 2013-11-04
             */
            $order_channel = Order::model()->getOrderChannel($order->order_id);
            if ($order_channel) {
                $partner = PartnerFactory::factory($order_channel);
                if ($partner instanceof AbstractPartner) {
                    if (method_exists($partner,'cancelOrderHandler')) {
                        $partner->cancelOrderHandler($order->order_id);
                    }
                }
            }

            //记录log
        	EdjLog::info($params['order_id'].'|504司机取消|'.$params['driver_id'].'|取消成功|end' , 'console');

            return true;
        }else{

        	//记录log
        	EdjLog::warning($params['order_id'].'|504司机取消|'.$params['driver_id'].'|司机取消失败|end' , 'console');

            return false;
        }
    }

    /**
     * 获取优惠信息
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2013-11-10
     */
    public function driver_get_order_favorable($params) {
        $order_id = isset($params['order_id']) ? $params['order_id'] : '';
        $order_number = isset($params['order_number']) ? $params['order_number'] : '';
        $booking_time = isset($params['booking_time']) ? $params['booking_time'] : '';

        $order = false;
        if (!empty($order_id)) {
            $order = Order::model()->findByPk($order_id);
        } elseif (!empty($order_number)) {
            $order = Order::model()->find('order_number = :order_number', array(':order_number' => $order_number));
        }

        if (!$order) {
            return false;
        }

        //把优惠信息先加入到memcache
        CustomerApiOrder::model()->orderFavorableCache($order_number);

        $map = OrderQueueMap::model()->find('order_id = :order_id' , array(':order_id' => $order->order_id));
        if (!$map) {
            return false;
        }

        $msg = Push::model()->setPushOrderMsg($map->queue_id , $order->driver_id , $order->order_id);
        if (0 == $msg['code']) {
            $msg['msg']['order_id'] = $order->order_id;
            $msg['msg']['is_new'] = 0;
            if (!empty($order_number)) {
                $msg['msg']['order_number'] = $order_number;
            }
            $data = array(
                'type' => GetuiPush::TYPE_ORDER_DETAIL,
                'content' => $msg['msg'],
                'level' => GetuiPush::LEVEL_HIGN,  //级别
                'driver_id' => $order->driver_id,
                'queue_id' => $map->queue_id,
                'created' => date('Y-m-d H:i:s' , time()),
            );

            //发送失败记录请求次数
            $result = Push::model()->organizeMessagePush($data);
            return $result;
        }
        return false;
    }

    /**
     * 更新order REDIS
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2013-11-15
     */
    public function update_order_redis($params) {
    	$order_id = isset($params['order_id']) ? $params['order_id'] : '';
    	$order_state = isset($params['order_state']) ? $params['order_state'] : '';
    	if (empty($order_id) || empty($order_state)) {
    		return false;
    	}
	$driver_id = Push::DEFAULT_DRIVER_INFO;
	if(isset($params['driver_id'])) {
	  $driver_id =  $params['driver_id'];
	}
	$order_process_flag = isset($params['order_process_flag']) ? $params['order_process_flag'] : 0;
    	$result = CustomerApiOrder::model()->updateOrderRedisByOrderFlag($order_id ,
	    $order_state, $driver_id, '', $order_process_flag);
    	return $result;
    }


	/**
     * 更新自增长id
     * @param array $params
     * @return boolean
     */
	public function unique_log($params) {
		if (empty($params)) {
			return false;
		}
//		$ret=UniqidSequence::model()->findByPk($params['style']);
//		if ($ret) {
//			if ($ret->max_id<$params['max_id']) {
//				return UniqidSequence::model()->updateByPk($params['style'], $params);
//			}
//		}
		return false;
	}

    /**
     * ------------------------------------------------------------------------------------------------------------——
     * |zhanglimin 2013-10-18                                                                                       |
     * |帅气的分隔线 下面全是返程项目的要执行的方法,非返程项目的请写在在此分隔线之上                                                |
     * ------------------------------------------------------------------------------------------------------------——
     */


    /**
     * 打车 dache 更新司机GPS位置信息
     *
     * @param array $params
     */
    public function dc_driver_current_pos($params) {
        var_dump($params);
        $driver_id=$params['driver_id'];
        $status=$params['status'];
        $route_id = $params['route_id'];
        $driver=DcDriverStatus::model()->get($driver_id);
        if ($driver) {
            $params = array(
                'obj' => $driver,
                'id' => $driver_id,
                'driver_id' => $driver_id,
                'status' => $status,
                'route_id'=>$params['route_id'],
                'gps_position'=>array(
                    'longitude'=>$params['lng'],
                    'latitude'=>$params['lat']
                ),
                'gps_type'=>$params['gps_type'],
                'log_time'=>$params['log_time'],
            );
            DriverPositionService::getInstance()->positionUniqueList('DC_LAST_POSITION', $driver_id, $params, false);
        }
    }

    /**
     * 返回车客户端与司机端注册
     * @author zhanglimin 2013-10-08
     * @param $params
     * @return bool
     */
    public function dc_client_register($params){

        if( empty($params['client_id']) ){
            echo "client_id ,请检查\n";
            return false;
        }
        $dc_client = DcClient::model()->find(' phone=:phone and version=:version',array('phone'=>$params['phone'],'version'=>$params['version']));
        if(empty($dc_client)){
            $params['created'] = date("Y-m-d H:i:s");
            $dc_client = new DcClient();
            $dc_client->attributes = $params;
            if($dc_client->save()){
                echo Common::jobBegin("返回车注册新建开始");
                echo "add new \n";
                echo Common::jobBegin("返回车注册新建结束");
            }
        }else{
            $dc_client->client_id = $params['client_id'];
            if($dc_client->update(array('client_id'))){
                echo Common::jobBegin("返回车注册修改开始");
                echo "update \n";
                echo Common::jobBegin("返回车注册修改结束");
            }
        }
        return true;

    }




    /**
     * 黑车客户端登录token写入数据库
     * @author zhanglimin 2013-10-10
     * @param array $params
     * @return bools
     */
    public function dc_driver_token($params) {
        $driver_id=$params['driver_id'];
        $authtoken=$params['authtoken'];
        $created=$params['created'];

        $token=DcDriverToken::model()->find('driver_id=:driver_id' , array(
            ':driver_id' => $driver_id));
        if ($token) {
            $flag = DcDriverToken::model()->updateByPk($token->id, array(
                'authtoken'=>$authtoken,
                'created' => $created,
            ));
        } else {
            $token=new DcDriverToken();
            $token->attributes=array(
                'driver_id'=>$driver_id,
                'authtoken'=>$authtoken,
                'created'=>$created
            );
            $flag =  $token->save();
        }
        return $flag;
    }


    /**
     * 黑车司机端登录时更新imei
     * @author zhanglimin 2013-10-10
     * @param $params
     * @return bool
     */
    public function dc_update_driver_imei($params){
        $flag =  DcDriver::model()->updateByPk($params['driver_id'], array(
            'imei'=>$params['imei'],
        ));
        return $flag;
    }


    /**
     * 更新黑车工作状态
     * @author zhanglimin 2013-10-10
     * @param $params
     * @return bool
     */
    public function dc_update_driver_status($params){
        $driver = DcDriverPosition::model()->find('driver_id =:driver_id',array('driver_id'=>$params['driver_id']));
        if($driver){
            $driver->status = $params['status'];
            $driver->update_time = $params['update_time'];
            $driver->route_id = $params['route_id'];
            $flag =  $driver->update (array('status','update_time'));
        }else{
            $driver = new DcDriverPosition();
            $driver->driver_id = $params['driver_id'];
            $driver->status = $params['status'];
            $driver->update_time = $params['update_time'];
            $driver->route_id = $params['route_id'];
            $flag = $driver->save();
        }
        return $flag;
    }

    /**
     * 返回车客户请求返程
     * @author zhanglimin 2013-10-11
     * @param $params
     */
    public function dc_customer_carpool($params){
        DcCustomerCarpool::model()->createGen($params);
        return true;
    }


    /**
     * 黑车司机接受订单
     * @author zhanglimin 2013-10-11
     * @param $params
     */
    public function dc_accept_order($params){
        $carPool = DcCustomerCarpool::model()->findByPk($params['carpool_id']);//获取客户请求返回的信息
        if($carPool){
            //订单属性
            $order_params = array(
                'customer_phone' => $carPool->phone,
                'driver_id'=>$params['driver_id'],
                'driver_phone'=>$params['driver_phone'],
                'status' => DcOrder::STATUS_NOT_CAR ,  //默认没上车
                'route_id' => $params['route_id'],
                'car_pool_id'=>$params['carpool_id'],
                'created' => date("Y-m-d H:i:s"), //订单建立时间
                'created_month' => date("Ym"),
            );

            $order_ext_arr = array(
                //请求时
                array(
                    'request_time'=>$carPool->request_time, //请求时间
                    'lng'=>$carPool->lng,
                    'lat'=>$carPool->lng,
                    'gps_type'=>$carPool->gps_type,
                    'address'=>$carPool->address,
                    'type'=>DcOrder::TYPE_REQUEST_ADDRESS,
                ),
                //接单时
                array(
                    'request_time'=>$params['created'], //接单时间
                    'lng'=>$params['lng'],
                    'lat'=>$params['lat'],
                    'gps_type'=>$params['gps_type'],
                    'address'=>$params['address'],
                    'type'=>DcOrder::TYPE_ACCEPT_ADDRESS,
                ),
            );
            DcOrder::model()->orderGen($order_params,$order_ext_arr);
        }
        return true;
    }

    /**
     * 设置订单状态为上车
     * @author zhanglimin 2013-10-11
     * @param $params
     * @return int
     */
    public function dc_set_order_status($params){
        $flag =  DcOrder::model()->updateByPk($params['order_id'], array(
            'status'=>$params['status'],
        ));
        return $flag;
    }

    /**
     * 设置评论信息
     * @author zhanglimin 2013-10-11
     * @param $params
     * @return mixed
     */
    public function dc_set_order_comment($params){
        $flag = DcOrderComment::model()->createGen($params);
        return $flag;
    }

    /**
     * 保存意见反馈
     * @param $dataFeedback
     * @return bool
     * author mengtianxue
     */
    public function feedBack($dataFeedback){
        $phone = $dataFeedback['email'];
        $feedback_count = CustomerFeedback::model()->getFeedBackCountByPhone($phone);
        if($feedback_count == 0){
            $model = new CustomerFeedback();
            $model->attributes = $dataFeedback;
            if ($model->save()){
                //add by aiguoxin 加入建议列表
                CustomerSuggestion::model()->initSuggestion($phone,$dataFeedback['content'],
                    CustomerSuggestion::TYPE_FEEDBACK,$model->attributes['id']);
                return true;
            }
        }
        return false;
    }

    /**
    *   @author aiguoxin
    *   回复已经反馈
    */
    public function replyFeedback($dataFeedback){
        $id = $dataFeedback['id'];
        $phone = $dataFeedback['phone'];
        $content = $dataFeedback['content'];

        //更改意见状态，客户端查看
        CustomerSuggestion::model()->updateStatus($id,CustomerSuggestion::STATUS_PROCESS);
        //更改v2后台成未回复状态
        $customerSuggestion = CustomerSuggestion::model()->findByPk($id);
        if($customerSuggestion){
            $type = $customerSuggestion['type'];
            $cid = $customerSuggestion['opinion_id'];
            if($type == CustomerSuggestion::TYPE_COMPLAIN){
                CustomerComplain::model()->updateReplyStatus($cid,CustomerFeedback::NO_REPLY_STATUS);
            }else{
                CustomerFeedback::model()->updateReplyStatus($cid,CustomerFeedback::NO_REPLY_STATUS);
            }
        }
        EdjLog::info('id='.$id.'更改意见处理状态成功');

    }

    /**
     * 批量绑定优惠劵
     * @param $params
     * @return bool
     * author mengtianxue
     */
    public function addCustomerBonusBatch($params){
        $num = isset($params['num']) ? $params['num'] : 1;
        $phone = $params['phone'];
        $bonus_sn = $params['bonus_sn'];
        $sms = $params['sms'];
        unset($params['sms']);

        $bonus_info = BonusCode::model()->getBonusCodeById($params['bonus_id']);
        unset($params['bonus_id']);
        $end_date = 0;
        if ($bonus_info['end_day'] != 0) {
            $end_date = date('Y-m-d H:i:s', strtotime($bonus_info['end_day'] . " day"));
        } else {
            $end_date = $bonus_info['end_date'];
        }
        if($bonus_info){
            $params = array(
                'id' => $bonus_info['id'],
                'sn_type' => $bonus_info['sn_type'],
                'channel' => $bonus_info['channel'],
                'bonus_sn' => $bonus_sn,
                'parityBit' => 0,
                'money' => $bonus_info['money'],
                'back_type' => $bonus_info['back_type'],
                'end_date' => $end_date,
                'user_limited' => $bonus_info['user_limited'],
                'channel_limited' => $bonus_info['channel_limited'],
                'bonus_use_limit' => $bonus_info['bonus_use_limit'],
            );

            for($i = 0; $i < $num; $i++){
                CustomerBonus::model()->AddCustomerBonus($params, $phone);
            }
            if ($num > 0 && !empty($sms)) {
                Sms::SendForBatchMsg($phone, $sms);
            }

            return true;
        }else{
            return false;
        }
    }

    /**
     * 设置返程车推送信息己接收成功
     * @author zhanglimin 2013-10-13
     * @param $params
     * @return bool|int
     */
    public function dc_update_msg_request_status($params){
        $flag = false;
        if(isset($params['push_msg_id']) && $params['push_msg_id']!=0){
            $flag =  DcMessageLog::model()->updateByPk($params['push_msg_id'], array(
                'flag'=>$params['flag'],
            ));
        }
        return $flag;
    }

    /**
     * ------------------------------------------------------------------------------------------------------------——
     * |zhanglimin 2013-10-18                                                                                       |
     * |帅气的分隔线 上面段内全是返程项目的要执行的方法,非返程项目的请写在在此分隔线之下或开始之上                                                |
     * ------------------------------------------------------------------------------------------------------------——
     */

    /**
     * @param $params
     * @return bool
     * author mengtianxue
     */
    public function del_order_list_cache($params){
        $phone = trim($params['phone']);
        if(!empty($phone)){
            CustomerApiOrder::model()->delOldOrderListCache($phone);
        }
        return true;
    }

    /**
     * 第三方订单 司机接单时操作，
     * @param $order_id
     */
    public function partner_order_create_sync($order_id) {
        $order_channel = Order::model()->getOrderChannel($order_id);
        $flag = false;
        if ($order_channel) {
            $partner = PartnerFactory::factory($order_channel);
            if ($partner && $partner instanceof AbstractPartner) {
                $result = $partner->afterOrderSave($order_id);
                $flag = $result;
            }
        }
        return $flag;
    }

    /**
     * 第三方订单 司机报单时操作
     * @param $order_id
     */
    public function partner_order_complete_sync($order_id) {
        $order_channel = Order::model()->getOrderChannel($order_id);
        $flag = false;
        if ($order_channel) {
            $partner = PartnerFactory::factory($order_channel);
            if ($partner && $partner instanceof AbstractPartner) {
                $result = $partner->completeOrder($order_id);
                $flag = $result;
            }
        }
        return $flag;
    }

    /**
     * 第三方订单 司机销单时操作
     * @param $order_id
     */
    public function partner_order_cancel_sync($order_id) {
        $order_channel = Order::model()->getOrderChannel($order_id);
        $flag = false;
        if ($order_channel) {
            $partner = PartnerFactory::factory($order_channel);
            if ($partner && $partner instanceof AbstractPartner) {
                $result = $partner->cancelOrder($order_id);
                $flag = $result;
            }
        }
        return $flag;
    }

    /**
     * 客人投诉司机
     * @param $params
     * @return bool
     * @auther mengtianxue
     */
    public function complain($params){
	$order_id = $params['order_id'];
        if(!empty($order_id)){
           $order = Order::model()->getOrderById($order_id);
	   //$order = Order::model()->getOrderByOrderNumberOnly($order_id);
           $complain = new CustomerComplain();
           $complain->name = $order['name'];
           $complain->phone = $order['phone'];
           $complain->customer_phone = $order['phone'];
           $complain->driver_id = $order['driver_id'];
           $complain->driver_phone = $order['driver_phone'];
           $complain->order_id = $order['order_id'];
           $complain->service_time = date('Y-m-d H:i:s', $order['booking_time']);
           $complain->source = 3;
           $complain->detail = $params['content'];
           $complain->city_id = DriverStatus::model()->getItem($order['driver_id'],'city_id');
           $complain->operator = $order['name'];
           $complain->created = '司机销单客户投诉';
           $complain->create_time = date('Y-m-d H:i:s',time());
	       $complain->status = 1;
           $res = $complain->insert();
           //推送push
           DriverPush::model()->pushUnreadComplain($order['driver_id']);
           if($res){
                 //加入意见列表，2.4.1之前版本不做处理
                $opinion_id = $complain->attributes['id'];
                $phone = $complain->phone;
                $title = $complain->detail;
                if($opinion_id){
                    CustomerSuggestion::model()->initSuggestion($phone,$title,CustomerSuggestion::TYPE_COMPLAIN,$opinion_id);
                }
                return true;
           }
           return false;
         }
         return false;
    }

    /**
     * 优惠劵生成脚本
     * @param $params
     * @return bool
     * @auther mengtianxue
     */
    public function bonusLibrary($params){
        $bonus_code_id = $params['id'];
        $model = BonusCode::model()->getBonusCodeById($bonus_code_id);
        $couponArr = $codeRules = array();
        $couponArr['bonus_id'] = $bonus_code_id;
        $couponArr['money'] = $model['money']; //优惠券金额
        $couponArr['sn_type'] = $model['sn_type']; //类型
        //$couponArr['channel'] = $model['channel']; //渠道
        $couponArr['effective_date'] = $model['effective_date']; //生效日期
        $couponArr['binding_deadline'] = $model['binding_deadline']; //绑定截止日期
        if (!empty($model['end_date'])) {
            $couponArr['end_date'] = $model['end_date'];
        }

        if ($model['sn_type'] == 1) {
            $couponArr['bonus_sn'] = $model['issued']; //固定优惠码
	    if(!BonusLibrary::model()->doFixedCouponInsert($couponArr)){
                $task = array(
                    'method' => 'bonusLibrary',
                    'params' => $params
                );
                Queue::model()->putin($task, 'task');
            }
        } else {
            $couponRules = CJSON::decode($model['coupon_rules']);
            if ($model['area_id'] != 0) {
                $area_id = str_pad($model['area_id'], 3, '0', STR_PAD_LEFT); //拼装区域优惠码前三位
            }
            $codeRules['area_id'] = $area_id; //区域码前三位
            $codeRules['issued'] = $model['issued']; //生成优惠码数量
            $codeRules['couponDigits'] = $couponRules['code_num'] - 3; //优惠码位数
            $codeRules['num'] = $couponRules['num']; //优惠码位数
            $codeRules['num_prdfix'] = $couponRules['num_prdfix']; //优惠码位数
            BonusLibrary::model()->doAreaCouponInsert($couponArr, $codeRules);
        }
        return true;
    }



    /**
     * 优惠劵生成脚本
     * @param $params
     * @return bool
     * @auther mengtianxue
     */
    public function bonus_no_sn($params){
        EdjLog::info('bonus_no_sn params:'.json_encode($params));
        $phone=isset($params['phone'])?$params['phone']:'';
        $id = isset($params['id'])? $params['id']:0;
        $acount=isset($params['acount'])?$params['acount']:0;
        $ms=isset($params['ms'])?$params['ms']:'';
        if($phone==''||$id==0||$acount==0){
            EdjLog::info('params error:'.json_encode($params));
        }else{
            BonusLibrary::model()->bindBonusActive($phone,$acount,$id,$ms);
        }

        return true;
    }

//    public function updateAccountSettle($params){
//        $settle = OrderSettlement::model()->updateAccountSettle($params);
//        return $settle;
//    }
    /**
     * ------------------------------------------------------------------------------------------------------------——
     * | wanglonghuan  2013-12-26                                                                          |
     * |分割线 以下接口为 工单系统接口|
     * ------------------------------------------------------------------------------------------------------------——
     */

    /**
     * 创建工单
     * wanglonghuan 2013-12-26
     * @params driver_id,type,content,phone,groupUserInfo
     */
    public function create_support_ticket($params)
    {

        //根据分类 获取 处理部门 下个跟单人。
        $groupUserInfo = TicketUser::model()->getFollowUser($params['type']);
        if(!$groupUserInfo){
            //没有跟单人时 先收下提交信息。
            $groupUserInfo = array('group' => '0', 'user' => '-');
        }
        $params['groupUserInfo'] = $groupUserInfo;

        $date = date('Y-m-d H:i:s',time());
        $support_ticket_id = SupportTicket::model()->createSupportTicket($params);
        if( $support_ticket_id ){
            echo $date . " \nSupport Ticket:create_support_ticket 创建成功\n";
        }else{
            echo $date . " \nSupport Ticket:create_support_ticket 保存失败\n";
        }
    }

    /**
     * 司机回复工单
     * wanglonghuan 2013-12-27
     * @params ticket_id,message,driver_id
     */
    public function reply_support_ticket($params)
    {
        $ticket_id = $params['ticket_id'];
        $driver_id = $params['reply_user'];
        $date = date("Y-m-d H:i:s",time());
        if(SupportTicketMsg::model()->createSupportTicketMsg($params,1,SupportTicketMsg::REPLY_TYPE_FROM_DRIVER)){
            echo "\n".$date . " Support Ticket:reply_support_ticket 回复消息保存成功。\n";
        }else{
            echo "\n".$date . " Support Ticket:reply_support_ticket 回复消息保存失败。\n";
        }
        if(SupportTicketLog::model()->SupportTicketAddLog($ticket_id,$driver_id,SupportTicketLog::LOG_ACTION_REPLY)){
            echo "\n".$date . " Support Ticket:reply_support_ticket 回复log保存成功。\n";
        }else{
            echo "\n".$date . " Support Ticket:reply_support_ticket 回复log保存失败。\n";
        }
        $res = SupportTicket::model()->updateByPk($ticket_id,array(
            'last_reply_user'=>$driver_id,
            'last_reply_time'=>date("Y-m-d H:i:s",time())),'',array());

        if($res){
            echo "\n".$date . " Support Ticket:reply_support_ticket 修改工单 成功。\n";
        }else{
            echo "\n".$date . " Support Ticket:reply_support_ticket 修改工单 失败。\n";
        }
    }

    /**
     * 关闭订单
     * wanglonghuan 2013-12-27
     * @params $ticket_id,$driver_id
     */
    public function close_support_ticket($params)
    {
        $ticket_id = $params['ticket_id'];
        $driver_id = $params['reply_user'];
        $date = date("Y-m-d H:i:s",time());
        $ticket_model = SupportTicket::model()->findByPk($ticket_id);
        $params = array(
            'status' => SupportTicket::ST_STATUS_CLOSE,
            'operation_user'=>$driver_id,
            'close_time' => $date,
        );
        $ticket_model->saveAttributes($params);
        if(SupportTicketLog::model()->SupportTicketAddLog($ticket_id,$driver_id,SupportTicketLog::LOG_ACTION_CLOSE)){
            echo "\n".$date . " Support Ticket:close_support_ticket 关闭成功。\n";
            return true;
        }else{
            echo "\n".$date . " Support Ticket:close_support_ticket 关闭失败。\n";
            return false;
        }
    }

    /**
     * ------------------------------------------------------------------------------------------------------------——
     * | AndyCong<congming@edaijia-staff.cn>
     * | 2014-01-05                                                                          |
     * | 分割线 以下为派单重构
     * ------------------------------------------------------------------------------------------------------------——
     */

    /**
     * 派单缓存数据写入数据库
     * @param array $params
     * @return boolean $result
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-05
     */
    public function dump_insert_order($params) {
    	if (empty($params['queue']['key']) || empty($params['queue']['data'])
		    || empty($params['order']['key']) || empty($params['order']['data'])
		    || empty($params['map']['key']) || empty($params['map']['data']) )
		{
    		return false;
    	}

    	$queue_id = DalOrder::model()->queueInsert($params['queue']['data'] , $params['queue']['key']);
    	//将queue_id 和 unique_queue_id关联
    	if ($queue_id) {
    		ROrder::model()->updateQueue($params['queue']['key'] , array('queue_id' => $queue_id));
    	}

    	$order_id = DalOrder::model()->orderInsert($params['order']['data'] , $params['order']['key']);
    	//将order_id 与 unique_order_id关联
    	if ($order_id) {
    		ROrder::model()->updateOrder($params['order']['key'] , array('order_id' => $order_id));
    		$phone = isset($params['order']['data']['phone']) ? $params['order']['data']['phone'] : '';
    		$source = isset($params['order']['data']['souce']) ? $params['order']['data']['souce'] : 0;
            $channel = isset($params['order']['data']['channel']) ? $params['order']['data']['channel'] : '';

            $bonus_sn = isset($params['order']['data']['bonus_sn']) ? $params['order']['data']['bonus_sn'] : ''; //优惠券码
            $is_use_bonus = isset($params['order']['data']['is_use_bonus']) ? intval($params['order']['data']['is_use_bonus']) : 1; //是否可使用优惠券

            if($is_use_bonus) {
                $driver_id = $params['real_driver_id'] ;
                $driver = Driver::model() ->getProfile ($driver_id);
                $driver_phone='BJ00000';
                if (!empty($driver['phone'])){
                    $driver_phone = $driver['phone'];
                }
                if(!empty($bonus_sn)) { //优先绑定选定优惠券
                    echo "\n single|order_id:".$order_id."|bonus_sn:".$bonus_sn." \n";
                    BonusLibrary::model()->BonusOccupancyBySn($phone , $bonus_sn , $order_id, 0 ,$driver_phone);
                } else {
                    echo "\n single|order_id:".$order_id."|没有获取到优惠券 \n";
                    BonusLibrary::model()->BonusOccupancy($phone , $order_id , $source , 1 ,0 ,0 ,$driver_phone);
                }

            }

    	}
    	$params['map']['data']['queue_id'] = $queue_id;
    	$params['map']['data']['order_id'] = $order_id;
    	$map_id   = DalOrder::model()->mapInsert($params['map']['data'] , $params['map']['key']);
    	//将map_id 与 unique_map_id关联
    	if ($map_id) {
    		ROrder::model()->updateMap($params['map']['key'] , array('map_id' => $map_id));
    	}

        // 判断此订单是否需要执行恶劣天气加价的逻辑——曾坤 2015/4/1
        $bad_weather_surcharge = WeatherRaisePrice::model()->getCityAddPrice(
            $params['queue']['data']['city_id'],
            date('Y-m-d H:i:s')
        );

		//FIXED:创建orderExt记录  2014-12-16
		$order_ext = new OrderExt();
		$order_ext->order_id = $order_id;
        if (!empty($bad_weather_surcharge) && isset($bad_weather_surcharge['id'])) {
            $order_ext->bad_weather_surcharge = $bad_weather_surcharge['id'];
        }
		$order_ext->type = Order::createOrderType($source, $channel);
		if(!$order_ext->save()) {
			EdjLog::info('Update order_ext error|OrderId:'.$order_ext->order_id.'|'.json_encode($order_ext->getErrors()));
		}

		//FIXED:计算客户与司机距离，存储到redis		2014-12-16
		//获取与客户见距离
		$customer_lng = $params['lng'];
		$customer_lat = $params['lat'];
		$driver_id = $params['real_driver_id'];
		$arrive_dist = '';
		if (intval($customer_lng) > 0 && intval($customer_lat) > 0) {
			$arrive_dist = Push::model()->_getRouteDistanceSpeed($driver_id , $customer_lng , $customer_lat);
		}
		$driver_dis_data[] = array();
		$driver_dis_data['dist'] = $arrive_dist;
		$driver_dis_data['is_remote'] = '0';

		// Save the driver dis_data to redis
		QueueDispatchOrder::model()->setOrderDriverDisData($order_id, $driver_id, $driver_dis_data);


	// LOG FOR SPEEDUP
	EdjLog::info('LOG FOR SPEEDUP|queue_id:'.$queue_id.'=>'.$params['queue']['key']
	    .'|order_id:'.$order_id.'=>'.$params['order']['key'].'|选司机下单id映射|end');

	// 更新订单状态机 by wangjian 2014-03-26
	// 2014-03-26 DEGIN
	$created = date('Y-m-d H:i:s', time());
	$created_in_cache = ROrder::model()->getQueue($params['queue']['key'], 'created');
	if ($created_in_cache) {
	    $created = $created_in_cache;
	}
	$real_driver_id = $params['order']['data']['driver_id'];
	if ($params['real_driver_id']) {
	  $real_driver_id = $params['real_driver_id'];
	}
        OrderProcess::model()->genNewOrderProcess(
	    array( 'queue_id'  => $queue_id,
	           'order_id'  => $order_id,
		   'driver_id' => $real_driver_id,
		   'state'     => OrderProcess::PROCESS_NEW,
		   'created'   => $created
	    )
	);
        OrderProcess::model()->genNewOrderProcess(
	    array( 'queue_id'  => $queue_id,
	           'order_id'  => $order_id,
		   'driver_id' => $real_driver_id,
		   'state'     => OrderProcess::PROCESS_START_DISPATCH,
		   'created'   => $created,
	    )
	);
	// 2014-03-26 END

    }

    /**
     * 更新订单司机信息 同时记录订单位置 和 状态机
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-05
     */
    public function dump_update_order($params) {
    	if (empty($params['order_info']) || empty($params['driver_info'])) {
    		return false;
    	}

    	//更新order中的司机信息
    	$order = Order::model()->updateAll($params['driver_info'] , 'order_number = :order_number' , array(
    	    ':order_number' => $params['order_info']['order_id'],
    	));
    	if ($order) {
    		echo "\n order update success \n";
    	} else {
    		echo "\n order update fail \n";
    	}

    	//获取数据库中的queue_id 和 order_id
    	$queue_id = ROrder::model()->getQueue($params['order_info']['queue_id'] , 'queue_id');
    	$order_id = ROrder::model()->getOrder($params['order_info']['order_id'] , 'order_id');
    	if (empty($queue_id) || empty($order_id)) {
    		echo "\n cache not exist \n";
    		return false;
    	}

    	//记录订单位置
    	$position_arr = array(
    	    'order_id' => $order_id,
    	    'gps_type' => $params['order_info']['gps_type'],
    	    'lng' => $params['order_info']['lng'],
    	    'lat' => $params['order_info']['lat'],
    	    'log_time' => isset($params['order_info']['log_time']) ? $params['order_info']['log_time'] : date('Y-m-d H:i:s'),
    	    'flag' => OrderPosition::FLAG_ACCEPT,
    	);
	$result = OrderPosition::model()->insertInfo($position_arr);
	$convert_pos = array();
	if (!empty($result) && isset($result['ret']) && $result['ret']) {
	    $convert_pos =  isset($result['position'])? $result['position']:array();
    	}

	//记录距离信息到数据库	2014-12-16
	$driver_id = $params['driver_info']['driver_id'];
	$driver_dis_data =
		QueueDispatchOrder::model()->getOrderDriverDisData($order_id, $driver_id);
	$order_ext = OrderExt::model()->find(
		'order_id = :order_id',
		array(':order_id' => $order_id));

	if(!empty($order_ext)) {
		$order_ext->use_fee = 0;
		$order_ext->linear_ready_distance = isset($driver_dis_data['dist'])? $driver_dis_data['dist']:0;

		if(!$order_ext->update()) {
			EdjLog::info('order::Accept update order_ext error|'.json_encode($modelExt->getErrors()));
		}
	} // Valid order ext

        // 更新订单状态机 by wangjian 2014-03-26
	// 2014-03-26 BEGIN
        OrderProcess::model()->genNewOrderProcess(
	    array( 'queue_id'  => $queue_id,
	           'order_id'  => $order_id,
		   'driver_id' => $params['driver_info']['driver_id'],
		   'state'     => OrderProcess::PROCESS_ACCEPT,
		   'created'   => date('Y-m-d H:i:s', time()),
	    )
	);

	// Save the driver id and order id into redis
	RDriverPosition::model()->setCurrentOrder(
		$params['driver_info']['driver_id'],
		$order_id, OrderProcess::PROCESS_ACCEPT,
		$convert_pos);
	// 2014-03-26 END
    	return true;
    }

    /**
     * 记录推送消息
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-05
     */
    public function dump_insert_messagelog($params) {
    	if (empty($params['key']) || empty($params['data']) )
		{
    		return false;
    	}
    	$unique_queue_id = isset($params['data']['queue_id']) ? $params['data']['queue_id'] : '';
    	$queue_id = ROrder::model()->getQueue($unique_queue_id , 'queue_id');
    	if (!empty($queue_id)) {
    		$params['data']['queue_id'] = $queue_id;
    		$unique_order_id = isset($params['data']['content']['order_id']) ? $params['data']['content']['order_id'] : '';
    		if (!empty($unique_order_id)) {
    			$order_id = ROrder::model()->getOrder($unique_order_id , 'order_id');
    			if (!empty($order_id)) {
    				$params['data']['content']['order_id'] = $order_id;
    			}
    		}
    	}
    	$push_msg_id = DalOrder::model()->messageInsert($params['data'] , $params['key']);
    	if ($push_msg_id) {
    		ROrder::model()->updateMessage($params['key'] , array('push_msg_id' => $push_msg_id));
    	}
    	return true;
    }

    /**
     * 推送订单（队列执行）
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-06
     * @ Modified:
     *	Author: qiujianping@edaijia-inc.cn
     *	Date: 2014-03-21
     *	Desc: Add log data
     */
    public function push_order($params) {
        if (empty($params['version'])
	    || (empty($params['client_id']) && empty($params['driver_id']))
	    || empty($params['message'])) {
    		return false;
    	}

        if(!empty($params['client_id'])) {
            $result = EPush::model($params['version'])->send($params['client_id'] , $params['message'] , 3 , 0);
	}
	else {
	    // 如果没有client_id,设置个推推送失败,用短信派单
	    $result = array('result' => 'failed', 'status' => 'failed');
	}

	// For log
	$queue_id='';
	if(isset($params['message']['queue_id'])){
	  $queue_id=$params['message']['queue_id'];
	}

	$order_num=' ';
	$order_id=' ';
	$phone=' ';
	if(isset($params['message']['content'])){
	  if(isset($params['message']['content']['order_number'])) {
	    $order_num=$params['message']['content']['order_number'];
	  }
	  if(isset($params['message']['content']['order_id'])) {
	    $order_id=$params['message']['content']['order_id'];
	  }
	  if(isset($params['message']['content']['phone'])) {
	    $phone=$params['message']['content']['phone'];
	  }
	}

	if ($result['result']=='ok' && $result['status'] == "successed_online") {
	  EdjLog::info($queue_id.'|'.$order_num.'|'.$order_id.'|'.$phone.'|'.$params['client_id'].
		  '|'.$result['taskId'].'|'.$result['status'].'|'.$result['result'].'|个推推送成功|end');
	} else {
	    if ($result['result']=='ok' && $result['status'] == "successed_offline") {
		EdjLog::info($queue_id.'|'.$order_num.'|'.$order_id.'|'.$phone.'|'.$params['client_id'].
			'|'.$result['taskId'].'|'.$result['status'].'|'.$result['result'].'|个推推送成功|end');
	    } else {
		EdjLog::info($queue_id.'|'.$order_num.'|'.$order_id.'|'.$phone.'|'.$params['client_id'].
			'|'.$result['result'].'|个推推送失败|end');
	    }

	    if(isset($params['driver_phone'])) {
	        //如果版本支持 短信下发push
	        $app_ver = '';
	        if(!empty($params['client_id'])) {
                    $app_ver = DriverStatus::model()->app_client_ver($params['client_id']);
	        } else if (!empty($params['driver_id'])){
                    $app_ver = DriverStatus::model()->app_ver($params['driver_id']);
		}

		if(!empty($app_ver) && !empty($params['message']['push_distinct_id'])
                    && !empty(Yii::app()->params['SmsPushLimitedVersion'])
		    && Helper::compareVersion($app_ver, Yii::app()->params['SmsPushLimitedVersion'])) {
	            if( EPush::sms_push($params['message']['push_distinct_id'],
			$params['driver_phone']) ) {

                        //短信下单不进行二次推送,清除redis中二次推送标签
                        ROrder::model()->single_push_can_retry($order_id);

		        EdjLog::info('SmsPushLog_single|'.$queue_id.'|'.$order_id
			    .'|'.$params['driver_phone'].'|'.$params['message']['push_distinct_id']);
                    }
		    return;
		}
	    }
	}
    }

    /**
     * 重构司机接单
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-05
     */
    public function dal_order_received($params) {
    	if (empty($params['queue_id']) || empty($params['order_id']) || empty($params['push_msg_id'])
    	    ||empty($params['gps_type']) || empty($params['lng'])|| empty($params['lat']) || empty($params['log_time']))
    	{
    		return false;
    	}

    	$result = DalOrder::model()->OrderReceiveFactory($params);
    	$this->safelyApplyArriveInTimeActivity(OrderPosition::FLAG_ACCEPT, $params['order_id']);
    	$this->safelyRemoteDispatchBonusActivity(OrderPosition::FLAG_ACCEPT, $params['order_id'], $params['driver_id']);

    	return $result;
    }

    /**
     * 上报订单状态及位置
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-05
     */
    public function upload_order_position($params) {
    	if (empty($params['order_id']) || empty($params['lng']) || empty($params['lat'])
    	    || empty($params['gps_type']) || empty($params['flag']) || empty($params['log_time']))
    	{
    	    return false;
    	}
    	$result = DalOrder::model()->uploadOrderPosition($params);

        $this->safelyApplyNewCustomerFreeActivity($params['flag'], $params['order_id']);

        $ready_time = isset($params['ready_time']) ? $params['ready_time'] : null;
        $this->safelyApplyArriveInTimeActivity($params['flag'], $params['order_id'], $ready_time);

    	return $result;
    }

    private function safelyApplyNewCustomerFreeActivity($flag, $order_id, $is_multi = false) {
        try {
            NewCustomerFreeActivity::model()->onOrderStatusChanged($flag, $order_id, $is_multi);
        } catch (Exception $e) {
            EdjLog::warning('apply new customer free activity failed, message:' . $e->getMessage() , 'console');
        }
    }

    private function safelyApplyArriveInTimeActivity($flag, $order_id, $ready_time = null) {
        try {
            ArriveInTimeActivity::model()->onOrderStatusChanged($flag, $order_id, $ready_time);
        } catch (Exception $e) {
            EdjLog::warning('apply arrive-in-time activity failed, message:' . $e->getMessage() , 'console');
        }
    }


    private function safelyRemoteDispatchBonusActivity($flag, $order_id, $driver_id) {
    	try {
    		RemoteDispatchBonusActivity::model()->onOrderStatusChanged($flag, $order_id, $driver_id);
    	} catch (Exception $e) {
    		EdjLog::warning('apply remote bonus activity failed, message:' . $e->getMessage() , 'console');
    	}
    }

    /**
     * @modified qiujianping@edaijia-staff.cn 2014-04-08
     * 	use driver id to check if the data base is updated
     *
     * 将订单状态位置写入db
     * @param array $params
     */
    public function insert_order_position($params) {
    	$order_id = ROrder::model()->getOrder($params['order_id'] , 'order_id');
    	if (!empty($order_id)) {
    		//记录订单位置
    		$flag = isset($params['flag']) ? $params['flag'] : OrderPosition::FLAG_ACCEPT;
    		$attributes = array(
	    	    'order_id' => $order_id,
	    	    'gps_type' => $params['gps_type'],
	    	    'lng' => $params['lng'],
	    	    'lat' => $params['lat'],
	    	    'log_time' => isset($params['log_time']) ? $params['log_time'] : date('Y-m-d H:i:s'),
	    	    'flag' => $flag,
	    	    'created' => date('Y-m-d H:i:s'),
	    	);
		$result = OrderPosition::model()->insertInfo($attributes);
		$convert_pos = array();
	    	if (!empty($result) && isset($result['ret']) && $result['ret']) {
	    		echo "\n insert order_position success \n";
			$convert_pos =  isset($result['position'])? $result['position']:array();
	    	} else {
	    		echo "\n insert order_position fail \n";
	    	}
		$driver_id = isset($params['driver_id']) ? $params['driver_id'] : Push::DEFAULT_DRIVER_INFO;

		if($flag == OrderPosition::FLAG_FINISH) {
		    // Update the driver id and order id into redis
		    RDriverPosition::model()->updateCurrentOrder(
			    $driver_id,
			    $order_id, OrderProcess::PROCESS_DEST,
			    $convert_pos);
		}

	    	//记录状态机
	    	$position_arr = array(
		    OrderPosition::FLAG_ACCEPT,
		    OrderPosition::FLAG_ARRIVE,
		    OrderPosition::FLAG_START,
		    OrderPosition::FLAG_SUBMIT,
	    	);

	    	if (!in_array($flag , $position_arr)) {
	    		return true;
	    	}
	    	switch ($flag) {
	    		case OrderPosition::FLAG_ACCEPT:
	    			$state = OrderProcess::ORDER_PROCESS_ACCEPT;
	    			break;
	    		case OrderPosition::FLAG_ARRIVE:
	    			$state = OrderProcess::ORDER_PROCESS_READY;
				// Update the driver id and order id into redis
				RDriverPosition::model()->updateCurrentOrder(
					$driver_id,
					$order_id, OrderProcess::PROCESS_READY,
					$convert_pos);
	    			break;
	    		case OrderPosition::FLAG_START:
	    			$state = OrderProcess::ORDER_PROCESS_DRIVING;
				// Update the driver id and order id into redis
				RDriverPosition::model()->updateCurrentOrder(
					$driver_id,
					$order_id, OrderProcess::PROCESS_DRIVING,
					$convert_pos);
	    			break;
	    		case OrderPosition::FLAG_SUBMIT:
	    			$state = OrderProcess::ORDER_PROCESS_FINISH;
	    			break;
	    		default:
	    			$state = OrderProcess::ORDER_PROCESS_ACCEPT;
	    			break;
	    	}
	    	$queue_id = $order_id;  //将order_id 给到 queue_id 否则需要将queue_id 查出来
                // 更新订单状态机 by wangjian 2014-03-26
		// 2014-03-26 BEGIN
		$driver_id_in_cache = ROrder::model()->getOrder($params['order_id'], 'driver_id');
		if ($driver_id_in_cache) {
		  if($driver_id_in_cache == Push::DEFAULT_DRIVER_INFO &&
		      $driver_id != Push::DEFAULT_DRIVER_INFO) {

		    //获取司机信息
		    $driver = DriverStatus::model()->get($driver_id);

		    // If the driver info is found
		    // Update the order in redis
		    if($driver) {
		      // update redis
		      $driver_info = array(
			  'driver_id' => $params['driver_id'],
			  'driver' => $driver->info['name'],
			  'driver_phone' => $driver->phone,
			  'imei' => $driver->info['imei'],
			  );
		      ROrder::model()->updateOrder($params['order_id'] , $driver_info);
		      $order = Order::model()->updateAll(
			  $driver_info ,
			  'order_number = :order_number' ,
			  array(
			    ':order_number' => $params['order_id'],
			    ));

		      // Update the db
		    }
		  }
		}

		$save_state = OrderProcess::transFromOldToNew($state);
                OrderProcess::model()->genNewOrderProcess(
        	    array( 'queue_id'  => $order_id,
        	           'order_id'  => $order_id,
        		   'driver_id' => $driver_id,
        		   'state'     => $save_state,
        		   'created'   => date('Y-m-d H:i:s', time()),
        	    )
        	);
		// 2014-03-26 END
		return true;
    	} else {
    		echo "\n insert order_position fail \n";
    		return false;
    	}
    }

    /**
     * 取消订单---DAL
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-06
     */
    public function dal_driver_cancel_order($params) {
    	if (empty($params['order_id']) || empty($params['driver_id'])) {
    		return false;
    	}

	//验证订单是否已报单
	//order_id 是unique_order_id,即order_number
        $order = Push::model()->getCancelOrder('', $params['order_id']);
	if(!empty($order) && $order->status != Order::ORDER_READY) {
	    //订单状态不是未报单,不能取消
            EdjLog::info("SingleOrder Cancel status Error|"
	        .$order->status."|".$order->id);
            return false;
	}


    	$result = DalOrder::model()->cancelOrder($params);

    	//给客户发短信
        $phone = ROrder::model()->getOrder($params['order_id'] , 'phone');
        $driver_id = ROrder::model()->getOrder($params['order_id'] , 'driver_id');
        $order_channel = ROrder::model()->getOrder($params['order_id'] , 'channel');
        if (!empty($phone) || !empty($driver_id)) {
        	$temp = SmsTemplate::model()->getContentBySubject('dianping_cancel_order' , array('$driver_id$'=>$driver_id.'师傅' , '昨天的e代驾司机' => ','));
			if (!empty($temp['content'])) {
				$message = $temp['content'];
                //判断订单是否是合作方订单，并且是否可以发短信给客户 author zhangtingyi
                $partner_common = new PartnerCommon();
                $is_forbid = $partner_common->checkForbidSmsByChannel($order_channel);
                if (!$is_forbid) {
		            Sms::SendForOrder($phone , $message);
                }
			}
        }

    	return $result;
    }

    /**
     * 取消订单---数据库操作
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-06
     */
    public function dump_cancel_order($params) {
    	$attr = array (
            'cancel_desc'=>$params['log'],
            'cancel_type'=>$params['cancel_type'],
            'status'=>Order::ORDER_COMFIRM);
        $result = Order::model()->updateAll($attr , 'order_number = :order_number' , array(':order_number' => $params['order_id']));
        if ($result) {
        	$order_id = ROrder::model()->getOrder($params['order_id'] , 'order_id');
        	$city_id = ROrder::model()->getOrder($params['order_id'] , 'city_id');
        	$phone = ROrder::model()->getOrder($params['order_id'] , 'phone');
        	BonusLibrary::model()->BonusUsed($phone , $order_id , 0 , 2);
        	if (empty($order_id) || empty($city_id)) {
	        	//取消优惠券
        		echo "\n order_id  or city_id is empty \n";
        		return false;
        	}
        	//记录销单或投诉Log开始
        	$cancelComplaintLog = array(
	            'driver_id'    => $params['driver_id'],
	            'phone'        => $params['phone'],
	            'order_id'     => $order_id,
	            'order_number' => $params['order_number'],
	            'cid'          => Common::convertCancelType($params['cancel_type']),
	            'cid_desc'     => $params['log'],
	            'content'      => $params['content'],
	            'isComplaint'  => $params['isComplaint'],
	            'source'       => $params['source'],
	        );
            CancelComplaintLog::model()->insertLog($cancelComplaintLog);

            if ($params['isComplaint'] == 1) {
                $complaintArr=array();
                $complaintArr['order_id'] = $order_id;
                $complaintArr['driver_user'] = $params['driver_id'];
                $complaintArr['customer_name'] = $params['name'];
                $complaintArr['city'] = $city_id;
                $complaintArr['customer_phone'] = $params['phone'];
                $complaintArr['order_type'] = Order::ORDER_COMFIRM;
                $complaintArr['complaint_type'] = Common::convertCancelType($params['complaint_type'],1);
                $complaintArr['complaint_content'] = $params['complaint'];
                $complaintArr['driver_time'] = date('Y-m-d H:i:s');//只记录出发时间
                $complaintArr['complaint_status'] = 0;
                DriverComplaint::model()->saveDriverComplaint($complaintArr);
            }
            //记录orderLog
            $log_attr = array (
                'order_id'    => $order_id,
                'description' => $params['log'],
                'operator'    => $params['driver_id'],
            );
            OrderLog::model()->insertLog($log_attr);
            echo "\n cancel order success \n";

            // 更新订单状态机 by wangjian 2014-03-26
	    // 2014-03-26 BEGIN
	    OrderProcess::model()->genNewOrderProcess(
	      array( 'queue_id'  => $order_id,
	             'order_id'  => $order_id,
		     'driver_id' => $params['driver_id'],
		     'state'     => OrderProcess::PROCESS_DRIVER_DESTROY,
                     'created'=>date('Y-m-d H:i:s' , time()),
	      )
	    );
	    // 2014-03-26 END

            return true;
        } else {
        	echo "\n cancel order fail \n";
        	return false;
        }
    }

    /**
     * 上报司机拒绝log
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-06
     */
    public function upload_driver_reject_log($params) {
    	if (empty($params['queue_id']) || empty($params['order_id']) || empty($params['driver_id'])
    	    || empty($params['type']) || empty($params['created']))
    	{
    		return false;
    	}
    	DalOrder::model()->driverRejectLog($params);
    	return true;
    }

    /**
     * 司机拒单处理
     * $params array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-07
     */
    public function driver_reject_order_process($params) {
    	if (empty($params['queue_id']) || empty($params['order_id']) || empty($params['driver_id'])
    	    || empty($params['type']) || empty($params['created']))
    	{
    		return false;
    	}
    	//取消为派出去订单
    	$attr = array(
    	    'status' => Order::ORDER_DRIVER_REJECT_NO_DISPATCH,
    	);
    	$result = Order::model()->updateAll($attr , 'order_number = :order_number and driver_id = :driver_id and channel = :channel' , array(
    	    ':order_number' => $params['order_id'],
    	    ':driver_id' => DalOrder::DEFAULT_DRIVER_INFO,
    	    ':channel' => DalOrder::QUEUE_CHANNEL_SINGLE_DRIVER,
    	));
    	$queue_id = ROrder::model()->getQueue($params['queue_id'] , 'queue_id');
    	$order_id = ROrder::model()->getOrder($params['order_id'] , 'order_id');
    	if ($result) {
    		$phone = ROrder::model()->getOrder($params['order_id'] , 'phone');
    		BonusLibrary::model()->BonusUsed($phone , $order_id, 0, 2);
    		echo "\n update order success \n";
    	} else {
    		echo "\n update order fail \n";
    	}

        $unique_queue_id = $params['queue_id'];
		$unique_order_id = $params['order_id'];
    	if (empty($order_id) || empty($queue_id)) {
    		echo "\n order_id or queue_id is empty \n";
    	} else {
    		$params['queue_id'] = $queue_id;
	    	$params['order_id'] = $order_id;
    	}
    	$model = new OrderRejectLog();
        $model->attributes = $params;
        if ($model->save()) {
            echo "\n insert reject_log success \n";
            // 更新订单状态机 by wangjian 2014-03-29
	    // 司机取消
	    // 2014-03-29 BEGIN
            OrderProcess::model()->genNewOrderProcess(
                array( 'queue_id'  => $queue_id,
                       'order_id'  => $order_id,
                       'driver_id' => $params['driver_id'],
                       'state'     => OrderProcess::PROCESS_DRIVER_CANCEL,
                       'created'   => date('Y-m-d H:i:s' , time()),
                )
            );
	    // 2014-03-29 END

            return true;
        }else {
        	$params['queue_id'] = $unique_queue_id;
	    	$params['order_id'] = $unique_order_id;
        	$task = array(
        	    'method' => 'driver_reject_order_process',
        	    'params' => $params,
        	);
        	Queue::model()->putin($task , 'dalorder');
            echo "\n insert reject_log fail \n";
            return false;
        }
    }

    /**
     * 用户取消处理
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-07
     */
    public function customer_cancel_order($params) {
    	if (empty($params['order_id']) || empty($params['queue_id']) || empty($params['status'])) {
            return false;
    	}

    	//取消订单
    	$attr = array(
    	    'status' => $params['status'],
            'cancel_code' => isset($params['cancel_code']) ? $params['cancel_code'] : '',
            'cancel_desc' => isset($params['cancel_desc']) ? $params['cancel_desc'] : '',
    	);
    	$cancel = Order::model()->updateAll($attr , 'order_number = :order_number and status not in(:order_comfirm , :order_cancel , :order_complate)' , array(
		    ':order_number' => $params['order_id'],
		    ':order_comfirm' => Order::ORDER_COMFIRM,
		    ':order_cancel' => Order::ORDER_CANCEL,
		    ':order_complate' => Order::ORDER_COMPLATE,
		));
		$order_id = ROrder::model()->getOrder($params['order_id'] , 'order_id');
		$queue_id = ROrder::model()->getQueue($params['queue_id'] , 'queue_id');
		if ($cancel) {
			//取消优惠券
			$phone = ROrder::model()->getOrder($params['order_id'] , 'phone');
			BonusLibrary::model()->BonusUsed($phone , $order_id , 0 , 2);
			echo "\n cancel order success \n";
		} else {
			echo "\n cancel order fail \n";
		}

		return true;
    }

    /**
     * 派单弹回走dalorder队列
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-14
     */
    public function set_queue_handle($params) {
    	if (empty($params['queue_id'])) {
    		return false;
    	}

    	$result = OrderQueue::model()->setOrder2ManualOpt($params['queue_id'] , '--长时间未派出，撤回手动派单--');
    	if ($result) {
    		echo "\n ".$params['queue_id']."|派单弹回成功 \n";
    	} else {
    		echo "\n ".$params['queue_id']."|派单弹回失败 \n";
    	}
    	return $result;
    }

    /**
     * 输出读缓存用开始结束时间
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-15
     */
    public function print_get_cache_time($params) {
    	$phone = isset($params['phone']) ? $params['phone'] : '';
    	$start_time = isset($params['start_time']) ? $params['start_time'] : microtime(true);
    	$end_time = isset($params['end_time']) ? $params['end_time'] : microtime(true);
    	if (empty($phone)) {
    		echo "参数有误!";
    		return false;
    	}
    	echo "\n phone:".$phone."|".$start_time."|".$end_time."| ".floatval($end_time-$start_time)."\n";
    	return true;
    }

    /***
     * @param $params
     * 初始化司机签约t_driver表
     */
    public function  init_driver_info($params){
        $data = $params;
        /***司机信息存入t_driver表**************/
        Driver::model()->insertDriverRecord($data);
        EdjLog::info('2.报名流水号id='.$data['id'].',id_card='.$data['id_card'].'入职，初始化t_driver表成功...');
    }

    /**
     * 司机签约时  初始化师傅信息费和优惠劵
     * @param $params
     * @auther mengtianxue
     */
    public function DriverInitialization($params){
        Yii::import('application.models.schema.driver.Material2Driver');
        Yii::import('application.models.schema.driver.MaterialMoneyLog');
        Yii::import('application.models.schema.driver.MaterialLog');
        Yii::import('application.models.schema.driver.MaterialMoney');
        $v_number = isset($params['v_number'])?$params['v_number']:'';
        $driver_id = isset($params['driver_id'])?$params['driver_id']:'';
        $driver_phone = isset($params['driver_phone'])?$params['driver_phone']:'';
        $recruitment_id = isset($params['recruitment_id'])?$params['recruitment_id']:'';
        $id_card = isset($params['id_card'])?$params['id_card']:'';
        $name = isset($params['name'])?$params['name']:'';

        /***1.用工号替换v号***********************/
        DriverPhone::model()->replaceDriverInfo($v_number,$driver_id,$driver_phone);
        EdjLog::info('1.报名流水号id='.$recruitment_id.',id_card='.$id_card.'入职，替换v号成功...');

        /***2.记录日志*********************/
        $insertArr = array();
        $insertArr['name'] = $name;
        $insertArr['id_card'] = $id_card;
        $insertArr['message'] = '签约成功';
        Driver::model()->insertDriverStatusLog($insertArr);
        EdjLog::info('2.报名流水号id='.$recruitment_id.',id_card='.$id_card.'入职，t_recruitment_log表记录日志成功...');


        /***3.更新t_driver_id_address表的状态********/
        $address = new DriverIdPool();
        $address->usedDriverId($driver_id);
        EdjLog::info('3.报名流水号id='.$recruitment_id.',id_card='.$id_card.'入职，更新工号t_driver_id_pool表成功...');

        /***4.初始化信息费和优惠券***************************/
        $settle = array('comment' => '新司机签约', 'user' => $params['driver_id'], 'city_id'=>$params['city_id'], 'channel'=>24, 'cast' => 0);
        OrderSettlement::model()->driverRecharge($settle, true);
        BonusLibrary::model()->addBonusLibrary($params['driver_id']);
        EdjLog::info('4.报名流水号id='.$recruitment_id.',id_card='.$id_card.'入职，初始化信息费和优惠券成功...');

        /***5.是否优惠城市，解除信息费屏蔽**************************/
        $discount = Common::driver_discount($params['driver_id']);
        $driver = Driver::getProfile($params['driver_id']);
        if ($driver) {
            if ($discount == 1) {
                $driver->block_at = 1;
            } else {
                $driver->block_at = 0;
            }
            $driver->save(false);
        }
        EdjLog::info('5.报名流水号id='.$recruitment_id.',id_card='.$id_card.'入职，判断是否优惠城市成功...');

        /***6.初始化t_driver_ext表*********************/
        DriverExt::model()->getExt($params['driver_id']);
        EdjLog::info('6.报名流水号id='.$recruitment_id.',id_card='.$id_card.'入职，初始化t_driver_ext表成功...');

        /***7.注册账号到论坛****************************/
        if(isset($params['hostname']) && ( strcasecmp($params['hostname'], 'www.edaijia.cn') == 0)){
            Helper::syncDriverToBbs($params['driver_id'],$params['password']);
        }
        Material2Driver::model()->changeDriverId($params['recruitment_id'],$params['driver_id']);
        MaterialMoney::model()->changeDriverId($params['recruitment_id'],$params['driver_id']);
        MaterialMoneyLog::model()->changeDriverId($params['recruitment_id'],$params['driver_id']);
        MaterialLog::model()->changeDriverId($params['recruitment_id'],$params['driver_id']);
        EdjLog::info('7.报名流水号id='.$recruitment_id.',id_card='.$id_card.'入职，初始化论坛账号成功...');

        /***8.生成订单，扣装备押金使用******************/
        $open = DriverOrder::model()->checkOpenCity($driver['city_id']);
        if($open){
            DriverOrder::model()->addDriverOrder($params['recruitment_id']);
            EdjLog::info('8.报名流水号id='.$recruitment_id.',id_card='.$id_card.'入职，生成装备订单成功...');
        }
        EdjLog::info('ok...报名流水号id='.$recruitment_id.',id_card='.$id_card.'入职，入职成功...ok');
    }

    /**
     * 导入司机签约信息
     * @param $params
     * @auther mengtianxue
     */
    public function ImportDriverBank($params){
        if(!empty($params)){
            DriverBankSign::model()->driverBankSave($params);
            echo $params['5']."\n";
        }
    }

    /**
     * 载入用户订单信息到redis
     * @param $params
     * @return bool
     * author mengtianxue bidong 2014-1-17
     */
    public function load_customer_order($params){
        $phone = trim($params['phone']);
        $order_id=trim($params['order_id']);
        if(!empty($phone) && !empty($order_id)){
            ROrderHistory::model()->loadCustomerOrder($phone,$order_id);
        }
        return true;
    }

    /**
     * 删除用户订单ID 缓存
     * @param $params
     * @author bidong 2014-1-16
     */
    public function customerDelOrder($params){
        Yii::import('application.models.schema.customer.CarCustomerDelOrder');
        Common::jobBegin('用户删除订单queue');
        if(!empty($params)){
            $order_id=$params['order_id'];
            $phone=$params['phone'];

            $table=CarCustomerDelOrder::model()->tableName();
            $command=Yii::app()->db->createCommand();
            $result=$command->insert($table, array(
                'phone'=>$phone,
                'order_id'=>$order_id,
                'create_time'=>date('Y-m-d H:i:s'),
            ));
            echo "\r\n 结果:";
            var_dump($result);

	    $result= ROrderHistory::model()->delOrderId($phone, $order_id);
        }else{
            echo "参数为空 \r\n";
        }

        Common::jobEnd('完成用户删除订单');
    }

    /**
     * 推送vip信息提醒
     * @param array $params
     * @return boolean
     * @author AndyCong<congnming@edaijia-staff.cn>
     * @version 2014-02-18
     */
    public function push_vip_notice($params) {
    	$order_id = isset($params['order_id']) ? intval($params['order_id']) : '';
    	$phone = isset($params['phone']) ? trim($params['phone']) : '';
    	$balance = isset($params['balance']) ? $params['balance'] : '';
    	$driver_id = isset($params['driver_id']) ? trim($params['driver_id']) : '';
    	if (empty($phone) || empty($balance) || empty($driver_id) || empty($order_id)) {
    		return false;
    	}

    	$msg = '您服务的'.$phone.'客户是vip,账户余额'.$balance."[e代驾]";
    	$result = PushMessage::model()->PushDriverMsg($msg , $driver_id , 3 , 0);    //高级  无离线时间
    	if ($result) {
    		echo "\n order_id:".$order_id."|phone:".$phone."|driver_id:".$driver_id."|push success \n";
    	} else {
    		echo "\n order_id:".$order_id."|phone:".$phone."|driver_id:".$driver_id."|push fail \n";
    	}
    	return $result;
    }

    /**
     * 第三方下单接口
     * @param array $params
     * @return boolean $result
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-02-24
     */
    public function api_third_book($params) {
        $params['channel'] = isset($params['channel']) ? $params['channel'] : CustomerApiOrder::QUEUE_CHANNEL_BOOKING;
        $parnter = "人保合作";
        if(isset($params['channel']) && $params['channel'] == '03035'){
            $parnter = "车音网";
        }
        $source = isset($params['source']) ? $params['source'] :Order::SOURCE_CALLCENTER;
        $queue = CustomerApiOrder::model()->save_order_queue($params , $source , $parnter);
        if (empty($queue)) {
            return false;
        }
        $queue_id = isset($queue['id']) ? $queue['id'] : '';
        $result = CustomerApiOrder::model()->multi_push($queue_id);

        $key = $queue['phone'].'_'.$queue['callid'];
        ROrder::model()->gather_order_key($queue['phone'] , $key);
        ROrder::model()->gather_order_key($queue['contact_phone'] , $key);
        return true;
    }

    /**
     * api访问sig串和加密穿做log输出
     * @param  array $params
     * @return bool
     * @AndyCong<congming@edaijia-staff.cn>
     * @version 2014-03-14
     */
    public function api_sig_log($params) {
        $query_string = isset($params['query_string']) ? $params['query_string'] : '';
        $system_sig   = isset($params['system_sig']) ? $params['system_sig'] : '';

        echo "\n ".$query_string."|".$system_sig." \n";
        return true;
    }

    /**
     * 添加用户订单统计脚本
     * @param $params
     * @auther mengtianxue
     */
    public function update_customer_order($params)
    {
        EdjLog::info("this is a temp flag |".time()."|".json_encode($params));
//        echo ("this is a temp flag |".time()."|".json_encode($params));
        if(isset($params['order_id'])){
            $order  = Order::model()->getOrderById($params['order_id']);
            $app_num = 0;
            $call_num = 0;
            $other_num = 0;
            if ($order['source'] == 0) {
                $app_num = 1;
            } elseif ($order['source'] == 1) {
                $call_num = 1;
            } else {
                $other_num = 1;
            }
            if ($order['booking_time'] == null || empty($order['booking_time'])) {
                $first_order_time = 0;
            } else {
                $first_order_time = $order['booking_time'];
            }
            $params['first_order_time'] = $first_order_time;
            $params['app_num'] = $app_num;
            $params['call_num'] = $call_num;
            $params['other_num'] = $other_num;

        }else{
            throw new Exception("QueueProcess -> update_customer_order -> order_id 是必须的");
        }
        $phone = $params['phone'];
        $customer_order_report = CustomerOrderReport::model()->getCustomerOrder(array('phone' => $phone));
        if ($customer_order_report) {
            $data = array('complate' => $params['complate'], 'app_num' => $app_num, 'call_num' => $call_num, 'other_num' => $other_num);
            CustomerOrderReport::model()->updateCustomerOrder($data, $phone);
        } else {
            CustomerOrderReport::model()->addCustomerOrder($params);
        }
        echo $phone . "\n";
    }


    /**
     * 广发银行贵宾   发送短信
     * @param $params
     * @return bool
     * @auther mengtianxue
     */
    public function bonus_number_sms($params){
        $phone = isset($params['phone']) ? $params['phone'] : '';
        $bonus_sn = isset($params['bonus_sn']) ? $params['bonus_sn'] : '';
        if(empty($phone) || empty($bonus_sn)){
            echo $phone. $bonus_sn."手机号和优惠码都不能为空";
            return false;
        }else{
            $num  = CustomerBonus::model()->getBonusNum($phone, $bonus_sn);
            if($num > 0){
                $message = '尊敬的广发银行贵宾，感谢您使用代驾服务。您还有'.$num.'次代驾服务特权，有效期至2014-12-31';
                Sms::SendSMS($phone, $message);
            }
        }
    }
    /**
     * 城市补贴活动
     * @param $params
     * @return bool
     * @auther zhangtongkai 2014-04-04
     */
    public function load_order_ext($params){
        OrderSettlement::model()->loadOrderExt($params);
        return true;
    }

    /**
    *   普通推送，目前包括优惠券、发票申请、用户反馈
    */
    public function common_client_push($params){
        $customerPhone=$params['customerPhone'];
        $messageid = isset($params['messageid'])?$params['messageid']:0;
        $type = $params['type'];
        $message=$params['message'];
        $suggestionid=$params['suggestionid'];
        //find user client_id
        if(empty($customerPhone)){
            echo 'phone is empty!'.PHP_EOL;
            EdjLog::info("phone is empty!");
            return false;
        }
        $customer_client = CustomerClient::model()->getByPhoneAndLast($customerPhone);
        if(empty($customer_client)){
            echo "phone=".$customerPhone.',customer_client is empty'.PHP_EOL;
            EdjLog::info("phone=".$customerPhone.",customer_client is empty");
            return false;
        }
        //$customer=CustomerMain::model()->getCustomer($customerPhone);
        $customer=CustomerService::service()->getCustomerInfo($customerPhone,1);
        if(empty($customer)){
            echo "phone=".$customerPhone.',customer_main is empty'.PHP_EOL;
            EdjLog::info("phone=".$customerPhone.",customer_main is empty");
            return false;
        }
        // 版本判断,优惠券
        if($type ==AppleMsgFactory::TYPE_MSG_COUPON &&
         !ClientPush::model()->canSendByVersion($customer['app_ver'],'5.1.0')){
            EdjLog::info("phone=".$customerPhone.",version < 5.1.0");
            return false;
        }
        // 版本判断,反馈、发票寄出
        if(($type == AppleMsgFactory::TYPE_MSG_FEEDBACK || $type == AppleMsgFactory::TYPE_MSG_BILL) &&
         !ClientPush::model()->canSendByVersion($customer['app_ver'],'5.2.0')){
            EdjLog::info("phone=".$customerPhone.",version < 5.2.0");
            return false;
        }
        $client_id = $customer_client['client_id'];
        $phone_type = $customer_client['type'];
        if($phone_type == ClientPush::ANDROID){//use getui
            echo 'phone='.$customerPhone.' use getui'.PHP_EOL;
            EdjLog::info("phone=".$customerPhone." use getui");
            $params = array(
                'content' => $message,
                'messageid' => $messageid,
                'suggestionid' => $suggestionid,
            );
            return ClientPush::model()->useGetui($params,$client_id,$type);
        }else if ($phone_type == ClientPush::IPHONE) {//use apple push
            EdjLog::info("phone=" . $customerPhone . " use iphone");
            $params = array(
                'message' => $message,
                'messageid' => $messageid,
                'suggestionid' => $suggestionid,
                'badge' => 0,
                'sound' => 'ping1',
            );
            return ClientPush::model()->useApplePush($params,$customerPhone,$client_id,$type);
        }

        return true;
    }
    /**
     *   在线支付推送
     */
    public function online_pay_push($params){
        $customerPhone=$params['customerPhone'];
        $messageid = isset($params['messageid'])?$params['messageid']:0;
        $type = $params['type'];
        $message=$params['message'];
        $suggestionid=$params['suggestionid'];
        //find user client_id
        if(empty($customerPhone)){
            echo 'phone is empty!'.PHP_EOL;
            EdjLog::info("phone is empty!");
            return false;
        }
        $customer_client = CustomerClient::model()->getByPhoneAndLast($customerPhone);
        if(empty($customer_client)){
            echo "phone=".$customerPhone.',customer_client is empty'.PHP_EOL;
            EdjLog::info("phone=".$customerPhone.",customer_client is empty");
            return false;
        }
        //$customer=CustomerMain::model()->getCustomer($customerPhone);
        $customer=CustomerService::service()->getCustomerInfo($customerPhone,1);
        if(empty($customer)){
            echo "phone=".$customerPhone.',customer_main is empty'.PHP_EOL;
            EdjLog::info("phone=".$customerPhone.",customer_main is empty");
            return false;
        }
        $client_id = $customer_client['client_id'];
        $phone_type = $customer_client['type'];
        $order_id = isset($params['order_id'])?$params['order_id']:0;
        $income = isset($params['income'])?$params['income']:0;
        if($phone_type == ClientPush::ANDROID){//use getui
            echo 'phone='.$customerPhone.' use getui'.PHP_EOL;
            EdjLog::info("phone=".$customerPhone." use getui");
            $params = array(
                'content' => $message,
                'messageid'=> $messageid,
                'suggestionid'=>$suggestionid,
                'order_id'=> $order_id,
                'income'=> $income,
            );
            return ClientPush::model()->useGetui($params,$client_id,$type);
        }elseif ($phone_type == ClientPush::IPHONE) {//use apple push
            EdjLog::info("phone=".$customerPhone." use iphone");
            $params = array(
                'message' => $message,
                'messageid' => $messageid,
                'suggestionid'=>$suggestionid,
                'badge' => 0,
                'sound' => 'ping1',
                'order_id'=> $order_id,
                'income'=> $income,
            );
            return ClientPush::model()->useApplePush($params,$customerPhone,$client_id,$type, 'apple_order_message');
        }
        return true;
    }

     /**
    *   活动推送
    */
    public function act_client_push($params){
        EdjLog::info('act_client_push start ....');
        $customerPhone=$params['customerPhone'];
        $url = $params['url'];
        $type = $params['type'];
        $title = $params['title'];
        $message=$params['message'];
        $version=$params['version'];
        $platform=$params['platform'];//平台，1.iso,2.andriod,3.所有

        //find user client_id
        if(empty($customerPhone)){
            echo 'phone is empty!'.PHP_EOL;
            EdjLog::info("phone is empty!");
            return false;
        }
        if(empty($title)){
            $title = 'e代驾';
        }
        $customer_client = CustomerClient::model()->getByPhoneAndLast($customerPhone);
        if(empty($customer_client)){
            echo "phone=".$customerPhone.',customer_client is empty'.PHP_EOL;
            EdjLog::info("phone=".$customerPhone.',customer_client is empty');
            return false;
        }
        //$customer=CustomerMain::model()->getCustomer($customerPhone);
        $customer=CustomerService::service()->getCustomerInfo($customerPhone,1);
        if(empty($customer)){
            echo "phone=".$customerPhone.',customer_main is empty'.PHP_EOL;
            EdjLog::info("phone=".$customerPhone.',customer_main is empty');
            return false;
        }
        // 版本判断
        if(!ClientPush::model()->canSendByVersion($customer['app_ver'],$version)){
            EdjLog::info("phone=".$customerPhone.',version < 5.1.0');
            return false;
        }
        $client_id = $customer_client['client_id'];
        $type = $customer_client['type'];
        if($type == ClientPush::ANDROID && ($platform == MarketingActivity::UNLIMITED_PLATFORM
        || $platform ==  MarketingActivity::ANDROID_PLATFORM)){//use getui
            //存入一条到消息中
            $messageid = CustomerMessage::model()->addMsg($customerPhone,CustomerMessage::TYPE_ACT,$title,$message,$url);
            if($messageid < 0){
                return false;
            }
            EdjLog::info('act_client_push use getui ....'.$customerPhone);
            echo 'phone='.$customerPhone.' use getui'.PHP_EOL;
            $params = array(
            'title' => $title,
            'content' => $message,
            'messageid' => $messageid,
            'url'=>$url,
            );
            return ClientPush::model()->useGetui($params,$client_id,AppleMsgFactory::TYPE_MSG_ACTIVE);
        }else if ($type == ClientPush::IPHONE && ($platform == MarketingActivity::UNLIMITED_PLATFORM
        || $platform ==  MarketingActivity::IOS_PLATFORM)) {//use apple push
            EdjLog::info('act_client_push use apple push ....'.$customerPhone);
            echo 'phone='.$customerPhone.' use iphone'.PHP_EOL;
            //存入一条到消息中
            $messageid = CustomerMessage::model()->addMsg($customerPhone,CustomerMessage::TYPE_ACT,$title,$message,$url);
            if($messageid < 0){
                return false;
            }
            $params = array(
            'message' => $message,
            'messageid' => $messageid,
            'title' => $title,
            'content' => $message,
            'url'=>$url,
            'badge' => 0,
            'sound' => 'ping1',
            );
            return ClientPush::model()->useApplePush($params,$customerPhone,$client_id,AppleMsgFactory::TYPE_MSG_ACTIVE);
        }

        return true;
    }

    /**
    *
    *   add by aiguoxin
    *   push msg to client
    */
    public function customer_client_push($params){
        echo 'start to push client-----'.PHP_EOL;
        print_r($params);
        switch ($params['type']) {
            case AppleMsgFactory::TYPE_MSG_DRIVER_RECEIVE_ORDER:
                echo 'use queue to push receive order'.PHP_EOL;
                ClientPush::model()->pushMsgForDriverAcceptOrderByQueue($params);
                break;
            case AppleMsgFactory::TYPE_MSG_DRIVER_REJECT_ORDER:
                echo 'use queue to push reject order'.PHP_EOL;
                ClientPush::model()->pushMsgForDriverRejectOrderByQueue($params);
                break;
            case AppleMsgFactory::TYPE_MSG_DRIVER_CANCEL_ORDER:
                echo 'use queue to push cancel order'.PHP_EOL;
                ClientPush::model()->pushMsgForDriverCancelOrderByQueue($params);
                break;
            case AppleMsgFactory::TYPE_MSG_DRIVER_REACH_TARGET:
                echo 'use queue to push reach target'.PHP_EOL;
                ClientPush::model()->orgPushMsgForDriverReachOrderByQueue($params);
                break;
            default:
                # code...
                break;
        }

    }

    /*
    *   add by aiguoxin
    *   nanjing active display for give coupon and insert
    */
    public function customer_active_display($params){
        //find
        // echo '--------start active display------------'.PHP_EOL;
        // $activeLog = CustomerActiveLog::model()->getActiveLog($params['phone'],$params['cityId'],$params['activeName']);
        // echo "-------------active--------------".PHP_EOL;
        // if(empty($activeLog)){// add new
        //     echo "-------------active--empty------------".PHP_EOL;
            $count = CustomerActiveLog::model()->addCustomerActiveLog($params['phone'],$params['state'],$params['cityId'],$params['activeName']);
            if($count > 0){
                echo "active set customerActiveLog ok".PHP_EOL;;
                //gift for coupon
                // if(intval($params['state']) == CustomerActiveLog::SHARE_FRIEND){
                //     $res=BonusLibrary::model()->BonusBinding($params['bonus_sn'], $params['phone'], $params['pwd']);
                //     echo '------------------- coupon----------------phone='.$params['phone'].PHP_EOL;
                //     print_r($res);
                // }
            }else{
                echo "active set CustomerActiveLog faild".PHP_EOL;;
            }
        // }else{ //update state
        //     echo "-------------active--not empty------------".PHP_EOL;
        //     $state = $activeLog['state'];
        //     if($state != CustomerActiveLog::SHARE_FRIEND){
        //         $count = CustomerActiveLog::model()->updateState($params['phone'],$params['cityId'],$params['activeName'],$params['state']);
        //         if($count >0 && intval($params['state']) == CustomerActiveLog::SHARE_FRIEND){
        //             $res = BonusLibrary::model()->BonusBinding($params['bonus_sn'], $params['phone'], $params['pwd']);
        //             echo '------------------- coupon----------------phone='.$params['phone'].PHP_EOL;
        //             print_r($res);
        //         }
        //     }
        // }
    }

    //add by aiguoxin
    public function driver_complain_push($params){
        DriverPush::model()->pushUnreadComplain($params['driver_id']);
        //加入意见列表，2.4.1之前版本不做处理
        $opinion_id = isset($params['opinion_id']) ? $params['opinion_id'] : 0;
        $phone = isset($params['phone']) ? $params['phone'] : '';
        $title = isset($params['title']) ? $params['title'] : '';
        if($opinion_id){
            CustomerSuggestion::model()->initSuggestion($phone,$title,CustomerSuggestion::TYPE_COMPLAIN,$opinion_id);
        }
    }

    //e币兑换商品
    public function e_shop_charge($params){

        $product = $params['product'];
        $productId = $product['id'];
        $driver_id = $params['driver_id'];
        $token = $params['token'];
        $city_id = $params['city_id'];
        EdjLog::info('driver_id='.$driver_id.',productId='.$productId.'开始兑换商品');

        //1.扣除城市商品数量
        $res = DriverStatus::model()->descCrownVal($city_id);
        if(!$res){
            EdjLog::info('driver_id='.$driver_id.',productId='.$productId.'兑换失败，当前城市剩余产品不够,res='.$res);
        }
        //2.扣除司机e币
        $res = DriverExt::model()->addWealth($driver_id,-$product['wealth']);
        if($res !=1){
            EdjLog::info('driver_id='.$driver_id.',productId='.$productId.'兑换失败，扣除司机e币失败');
        }

        //3.分配司机皇冠
        $begin_time = date("Y-m-d H:i:s");
        $end_time = date("Y-m-d H:i:s",strtotime("+1 day"));
        $model=new DriverRecommand;
        $model->type=DriverRecommand::CROWN_TYPE;
        $model->begin_time=$begin_time;
        $model->end_time=$end_time;
        $model->created=$begin_time;
        $model->reason='司机e币兑换';
        $model->operator='司机兑换';
        $model->driver_id=$driver_id;
        if($model->save()){

        $recommand_arr = array(
        'type' => 1,
        'begin_time' => date("Y-m-d H:i:s"),
        'end_time' => date("Y-m-d H:i:s",strtotime("+1 day")),
        'reason' => 'e币兑换',
        );
        $driver = DriverStatus::model()->getByToken($token);

        $driver->recommand = $recommand_arr;
        if (!$driver->recommand) {
            EdjLog::info('driver_id='.$driver_id.',productId='.$productId.'兑换失败，分配皇冠失败');

            }
        }
        //4.记录e币消费记录
        $res = DriverWealthLog::model()->addLog($driver_id,DriverWealthLog::CROWN_TYPE,-$product['wealth'],$city_id,date("Y-m-d H:i:s"));
        if($res != 1){
            EdjLog::info('driver_id='.$driver_id.',productId='.$productId.'兑换失败，记录司机e币消费记录失败');
        }
        EdjLog::info('driver_id='.$driver_id.',productId='.$productId.'兑换商品成功');

    }

    /**
    *   组员报单，组长加e币
    *
    */
    public function group_order_e($params){
        $order_id = $params['order_id'];
        $city_id = 0;
        $driver_id = ROrder::model()->getGroup($order_id,'leader'); //组长
        $group_driver =  ROrder::model()->getGroup($order_id,'driver_id'); //组员
        //不是组员单或是组长报单，不处理
        if($driver_id == null || $driver_id == $group_driver){
            EdjLog::info('order_id='.$order_id.'不是组员单，过滤掉');
            return;
        }
        $city_id = 0;
        $leader = DriverStatus::model()->get($driver_id); //组长所在城市
        if($leader){
            $city_id = $leader->city_id;
        }
        $total_wealth=DriverWealthLog::GROUP_WEALTH;
        DriverExt::model()->addWealth($driver_id,$total_wealth);
        DriverWealthLog::model()->addLog($driver_id,DriverWealthLog::GROUP_TYPE,$total_wealth,$city_id,date("Y-m-d H:i:s"));
        echo 'driver='.$driver_id.'group wealth success'.PHP_EOL;
        EdjLog::info('driver='.$driver_id.'group wealth success');
    }

    /**
    *   @author aiguoxin
    *   计算司机在线时间
    */
    public function compute_driver_online($params){
        $driver_id = $params['driver_id'];
        $off_line_time = $params['off_line_time'];
        $last_online=DriverStatus::model()->getWorkTimeStamp($driver_id);
        DriverStatus::model()->setWorkTimeStamp($driver_id,0);//设置上线时间为0,防止多次下线
        $online_time=($off_line_time-$last_online)*1000;//转毫秒

        echo 'driver='.$driver_id.'心跳超时，强制下线并记录在线时间'.PHP_EOL;
        EdjLog::info('driver='.$driver_id.'心跳超时，强制下线并记录在线时间');
        if($last_online >0 && $online_time>0){
            if($online_time > DriverOnlineLog::MAX_ONLINE_TIME){
                echo 'driver='.$driver_id.'在线时间时间超过一天，过滤，online='.$last_online.',current_time='.$off_line_time.PHP_EOL;
                EdjLog::info('driver='.$driver_id.'在线时间时间超过一天，过滤，online='.$last_online.',current_time='.$off_line_time);
            }else{
                $res=DriverOnlineLog::model()->addDriverOnlineLog($driver_id,$online_time);
                if($res){
                    echo 'driver='.$driver_id.'上线时间更新成功'.PHP_EOL;
                    EdjLog::info('driver='.$driver_id.'上线时间更新成功'.',online='.$last_online.',finish='.$off_line_time);

                }else{
                    echo 'driver='.$driver_id.'上线时间更新失败'.PHP_EOL;
                    EdjLog::info('driver='.$driver_id.'上线时间更新失败');
                }
            }
        }else{
            echo 'driver='.$driver_id.'上线时间有误，过滤,last_online='.$last_online.PHP_EOL;
            EdjLog::info('driver='.$driver_id.'上线时间有误，过滤,last_online='.$last_online.PHP_EOL);
        }
    }

	/**
	 * 发送报警邮件
	 *
	 * @param $params
	 */
	public function send_mail_alarm($params){
		$title 	 = $params['title'];
		$content = $params['content'];
		$toList  = $params['toList'];
		@Mail::sendMail($toList, $content, $title);
	}

	/**
	 * 写 vip 操作 log 进入数据库
	 *
	 * @param $params
	 */
	public function dump_insert_vip_log($params){
		try{
			Vip::model()->vipLog($params);
		}catch (Exception $e){
			EdjLog::error('dump vip log error'.$e->getMessage());
			$mailTitle = 'vip 操作异步写库操作异常';
			$content = $e->getMessage().' ------ 参数 ----'.json_encode($params);
			FinanceUtils::sendFinanceAlarm($mailTitle, $content);
		}
	}

	/**
	 * 新推送：异步推送消息.
	 * @author yangzhi
	 *
	 * @param array $params
	 */
	public function api_asyn_send_message_through_newpush($params) {
		$result = EPush::model()->asyn_send_message_through_newpush($params);
		return $result;
	}

	 /**
     * 三周年客户回馈活动
     * @param $data
     * @return bool
     */
	public function feedback_client_push($data){
		$client_id = $data['client_id'];
        $type = $data['type'];
		$customerPhone = $data['phone'];
		$push_msg = $data['push_msg'];
		$url = $data['url'];
        $app_ver = $data['app_ver'];
        //存入一条到消息中
        $messageid = CustomerMessage::model()->addMsg($customerPhone,CustomerMessage::TYPE_ACT,"e代驾",$push_msg,$url);
        if($messageid < 0){
            return false;
        }
        $app_min_ver = '5.1.0';
        if(!isset($app_ver) || empty($app_ver) ||$app_ver<$app_min_ver){
            EdjLog::info('phone='.$customerPhone.',app_ver='.$app_ver);
            echo 'phone='.$customerPhone.',app_ver='.$app_ver.PHP_EOL;
            return false;
        }
        if($type == ClientPush::ANDROID){//use getui
            EdjLog::info('feedback_client_push use getui ....'.$customerPhone);
            echo 'phone='.$customerPhone.' use getui'.PHP_EOL;
            $params = array(
                'content' => $push_msg,
                'messageid' => $messageid,
                'url'=>$url,
            );
            return ClientPush::model()->useGetui($params,$client_id,AppleMsgFactory::TYPE_MSG_ACTIVE);
        }else if ($type == ClientPush::IPHONE) {//use apple push
            EdjLog::info('feedback_client_push use apple push ....'.$customerPhone);
            echo 'phone='.$customerPhone.' use iphone'.PHP_EOL;
            $params = array(
                'message' => $push_msg,
                'messageid' => $messageid,
                'url'=>$url,
                'badge' => 0,
                'sound' => 'ping1',
            );
            return ClientPush::model()->useApplePush($params,$customerPhone,$client_id,AppleMsgFactory::TYPE_MSG_ACTIVE);
        }
        return true;
	}

    /**
	 * 异步保存订单额外信息
	 *
	 * @param $params
	 */
	public function saveOrderInfo($params){
		$orderId = isset($params['order_id']) ? $params['order_id'] : 0;
        $driver_id = isset($params['driver_id']) ? $params['driver_id'] : 0;
        //如果车牌不符合规则则扣除50e币  约定:deduct_Wealth为0不扣  为1扣除
        $deduct_Wealth = isset($params['deduct_wealth']) ? $params['deduct_wealth'] : 0 ;
        if($deduct_Wealth){
            DriverWealthLog::model()->deductWealth($driver_id,$orderId);
        }
		if(!empty($orderId)){
			$attributes = $params;
			$driverId = $params['driver_id'];
			OrderExtra::model()->saveOrderInfo($orderId, $driverId, $attributes);
			$order_detail = isset($params['order_detail']) ?  $params['order_detail'] : '';
			if(!empty($order_detail)){
				$res = DriverWealthLog::model()->addDayOrderWealth($driverId, $orderId);
				if(1 != $res){
					EdjLog::error("add e money error order_id $orderId, driver_id: $driverId ");
				}
			}
		}
	}

	/**
	 * 个推：异步推送消息.
	 * @author yangzhi
	 *
	 * @param array $params
	 */
	public function api_asyn_send_message_through_getui($params) {
		$result = EPush::model($params['version'])->asyn_send_message_through_getui($params);
		return $result;
	}

    /*
	 * 异步保存订单信息（司机端红包用）
	 *
	 * @param $params
	 */
    public function envelopeOrder($params){
        EdjLog::info(serialize($params));
        try{
            $orderId = isset($params['order_id']) ? $params['order_id'] : 0;
            $driverId =isset($params['driver_id']) ? $params['driver_id'] : '';
            $day=isset($params['day']) ? date('Y-m-d H:i',$params['day']) : '';
            if(!empty($orderId) && !empty($driverId) &&!empty($day)){
                $extend = new EnvelopeExtend();
                $city_id=Driver::model()->getDriveCityById($driverId);
                $res = EnvelopeAcount::model()->saveInfo($orderId,$driverId,$day,$city_id);
                if(!$res){
                    EdjLog::error("add e EnvelopeAcount error order_id $orderId, driver_id: $driverId ");
                }else{
                    //获取订单开启时进行中的红包
                    $envelope_list=EnvelopeInfo::model()->getEvenlopeList($day);
                    if(!empty($envelope_list)){
                        foreach($envelope_list as $envelope){
                            if(in_array($city_id,EnvelopeMap::model()->getCityListByEnvelopeId($envelope->id))){
                                $arr = array('dictname' => 'envelope_type', 'code' => $envelope['envelope_type']);
                                EdjLog::info('红包id:' . $envelope['id'] . ';红包类型:' . $envelope['envelope_type']);
                                $num = Dict::getEnvelopeTypeNub($arr);
                                $dr=array();
                                //每次报单发放红包
                                if($num==0){
                                    $dr['drive_id'] = $driverId;
                                    $dr['city_id'] = $city_id;
                                    $dr['envelope_id'] = $envelope['id'];
                                    $dr['envelope_type']=$envelope['envelope_type'];
                                    $dr['amount'] = EnvelopeInfo::getEvenlopeNum($envelope['envelope_role']);
                                    $dr['create_date'] = date('Y-m-d H:i:s');
                                    $dr['last_changed_date'] = date('Y-m-d H:i:s');
                                    $dr['order_id']=$orderId;
                                    $dr['day']=date('Y-m-d',$params['day']);
                                    if (!$extend->envelopeInsert($dr)) {
                                        EdjLog::info('driver_id:' . $driverId . ';envelope_id:' . $envelope['id'] . ' sync error!');
                                    }
                                }else{
                                    //多次报单发放红包
                                    //设置订单开启日
                                    $start_envelope=$envelope['start_date'];
                                    $end_envelope=$envelope['end_date'];

                                    if(EnvelopeAcount::model()->driverOrderNum($driverId,$start_envelope,$end_envelope)>$num){
                                        if(EnvelopeExtend::model()->driveEnvelope($driverId,$envelope['id'])==0){
                                            $dr['drive_id'] = $driverId;
                                            $dr['city_id'] = $city_id;
                                            $dr['envelope_id'] = $envelope['id'];
                                            $dr['envelope_type']=$envelope['envelope_type'];
                                            $dr['amount'] = EnvelopeInfo::getEvenlopeNum($envelope['envelope_role']);
                                            $dr['create_date'] = date('Y-m-d H:i:s');
                                            $dr['last_changed_date'] = date('Y-m-d H:i:s');
                                            $dr['order_id']=$orderId;
                                            $dr['day']=date('Y-m-d H:i',$params['day']);
                                            if (!$extend->envelopeInsert($dr)) {
                                                EdjLog::info('driver_id:' . $driverId . ';envelope_id:' . $envelope['id'] . ' sync error!');
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }catch (Exception $e){
            EdjLog::error($e);
        }

    }
    /**
     * 司机接单/开车推送分享链接给客户
     * @param $params
     * @return bool
     */
    public function push_share_to_customer($params){
        EdjLog::info('push_share_to_customer start ....');
        $order_id=$params['order_id'];
        $trigger_time = $params['trigger_time'];
        $order = Order::model()->queryOrder($order_id);
        if(!$order || empty($order)){
            EdjLog::info('id为'.$order_id.'的订单不存在');
            return false;
        }
        if($order['source'] == Order::SOURCE_WASHCAR_CLIENT){//洗车订单
            EdjLog::info('id为'.$order_id.'的订单为洗车订单');
            return false;
        }
        $share_activity = PageConfig::model()->getSharedInfoByOrderIdAndTriggerTime($order, $trigger_time);
        if(!$share_activity){
            EdjLog::info('不存在符合条件的活动');
            return false;
        }
        $message = $share_activity['title'];
        $encode_order_id = Common::encrypt((string)$order['order_id'], 'order@edai%jia~!');
        $url     = $share_activity['url'].'?order_id='.$encode_order_id.'&';
        EdjLog::info('share_message='.$message);
        EdjLog::info('share_url='.$url);
        $customerPhone = $order['phone'];
        //find user client_id
        if(empty($customerPhone)){
            echo 'phone is empty!'.PHP_EOL;
            EdjLog::info("phone is empty!");
            return false;
        }
        EdjLog::info('share_customerPhone='.$customerPhone);
        $customer_client = CustomerClient::model()->getByPhoneAndLast($customerPhone);
        if(empty($customer_client)){
            echo "phone=".$customerPhone.',customer_client is empty'.PHP_EOL;
            EdjLog::info("phone=".$customerPhone.',customer_client is empty');
            return false;
        }
        //$customer=CustomerMain::model()->getCustomer($customerPhone);
        $customer=CustomerService::service()->getCustomerInfo($customerPhone,1);
        if(empty($customer)){
            echo "phone=".$customerPhone.',customer_main is empty'.PHP_EOL;
            EdjLog::info("phone=".$customerPhone.',customer_main is empty');
            return false;
        }
        // 版本判断
        $version = '5.1.0';
        if(!ClientPush::model()->canSendByVersion($customer['app_ver'],$version)){
            EdjLog::info("phone=".$customerPhone.',version < 5.1.0');
            return false;
        }
        $client_id = $customer_client['client_id'];
        $type = $customer_client['type'];
        if($type == ClientPush::ANDROID){//use getui
            EdjLog::info('share_client_push use getui ....'.$customerPhone);
            echo 'phone='.$customerPhone.' use getui'.PHP_EOL;
            $params = array(
                'content' => $message,
                'url'=>$url,
                'order_id'=>$order['order_id'],
            );
            return ClientPush::model()->useGetui($params,$client_id,AppleMsgFactory::TYPE_MSG_SHARE);
        }else if ($type == ClientPush::IPHONE) {//use apple push
            EdjLog::info('share_client_push use apple push ....'.$customerPhone);
            echo 'phone='.$customerPhone.' use iphone'.PHP_EOL;
            $params = array(
                'message' => $message,
                'url'=>$url,
                'order_id'=>$order['order_id'],
                'badge' => 0,
                'sound' => 'ping1',
            );
            return ClientPush::model()->useApplePush($params, $customerPhone, $client_id, AppleMsgFactory::TYPE_MSG_SHARE);
        }
        return true;
    }

    /**
     * 更新春节司机送回家活动数据
     * @param $params
     * @return bool
     * @throws CDbException
     */
    public function update_festival_data($params){
        $comeon = new FestivalComeon();
        $comeon->phone = $params['phone'];
        $comeon->friend_phone = $params['friend_phone'];
        $comeon->create_time = date('Y-m-d H:i:s',time());
        $ret = $comeon->save();
        if(!$ret){
            EdjLog::info($params['friend_phone'].'为'.$params['phone'].'点击加油保存失败');
        }
        $count = FestivalCustomer::model()->updateCounters(array('come_on_times'=>1),'phone=:phone',array(':phone'=>$params['phone']));
        if($count<=0){
            EdjLog::info('更新'.$params['phone'].'加油次数失败');
        }
        if($ret && $count>0){
            EdjLog::info('festival:'.$params['friend_phone'].' come on for '.$params['phone'].' successful');
        }else{
            EdjLog::info('festival:'.$params['friend_phone'].' come on for '.$params['phone'].' falied');
        }
        $comeon_num = $params['comeon_num'];
        if($comeon_num == 20){//加油次数为20时绑定优惠劵
            $short_message = '尊敬的客户您好，您参加的e代驾春节返乡活动已收集“加油”20次，e代驾送您20元优惠券5张，e代驾已签署200万代驾责任险，请放心使用~';
            $bonus_code = '5818121535547';
            $binding_ret = FinanceWrapper::bindBonusGenerate($params['phone'] , $bonus_code , 5 , $short_message);
            if($binding_ret && $binding_ret['code'] == 0){
                EdjLog::info('为phone='.$params['phone'].'用户绑定优惠劵成功');
                $binding_time =  date("Y-m-d H:i:s", time());
                $update_ret = FestivalCustomer::model()->updateAll(array('binding_status'=> 1, 'binding_time'=> $binding_time), 'phone=:phone',array(':phone'=>$params['phone']));
                if($update_ret>0){
                    EdjLog::info('更新phone='.$params['phone'].'用户优惠劵绑定状态成功');
                }else{
                    EdjLog::info('更新phone='.$params['phone'].'用户优惠劵绑定状态失败');
                }
            }else{
                EdjLog::info('为phone='.$params['phone'].'用户绑定优惠劵失败');
            }
        }
        return true;
    }

    public function festival_send_message_to_customer($params){
        $content = '尊敬的客户您好:您已成功报名活动,我们会给您匹配司机,如果您要去的城市同路司机较少,则可能会花较长时间匹配,匹配结果最晚将于2月12日左右发送给您';
        $phone = $params['phone'];
        $ret_sms = Sms::SendSMS($phone, $content, Sms::CHANNEL_ZCYZ);
        if (empty($ret_sms)) {
            EdjLog::info('festival_send_message_to_customer:'.$phone.' falied');
            return false;
        }
        EdjLog::info('festival_send_message_to_customer:'.$phone.' successful');
        return true;
    }

    public function festival_send_message_to_driver($params){
        $content = '尊敬的师傅您好:您已成功报名活动,我们会给您匹配客户,如果您要去的城市同路客户较少,则可能会花较长时间匹配,匹配结果最晚将于2月12日左右发送给您';
        $driver_number = $params['driver'];
        $driver = Driver::model()->find('user=:user', array(':user'=>$driver_number));
        if(!$driver){
            EdjLog::info('工号为'.$driver_number.'的司机不存在');
            return false;
        }
        $ret_sms = Sms::SendSMS($driver['phone'], $content, Sms::CHANNEL_ZCYZ);
        if (empty($ret_sms)) {
            EdjLog::info('festival_send_message_to_driver:'.$driver['phone'].' falied');
            return false;
        }
        EdjLog::info('festival_send_message_to_driver:'.$driver['phone'].' successful');
        return true;
    }

    public function weixin39_binding_bonus($params){
        $activityName = $params['act_name'];
        $activityConfig = ActivityConfig::model()->getInstance($activityName);
        $short_message = $activityConfig['sms'];
        $bonus_code = $activityConfig['bonus_sn'];
        $binding_ret = FinanceWrapper::bindBonusGenerate($params['phone'] , $bonus_code , 1 , $short_message);
        $log = new Bonus39Log();
        $log->act_name = $params['act_name'];
        $log->open_id = $params['open_id'];
        $log->phone = $params['phone'];
        $log->create_time = date('Y-m-d H:i:s',time());
        if($binding_ret && $binding_ret['code'] == 0){
            EdjLog::info($params['act_name'].'活动为phone='.$params['phone'].'的用户绑定优惠劵成功');
            $log->status=1;
        }else{
            EdjLog::info($params['act_name'].'活动为phone='.$params['phone'].'的用户绑定优惠劵失败');
            $log->status=0;
            FinanceUtils::sendFinanceAlarm('活动绑定优惠券失败', json_encode($params));
        }
        $ret = $log->save();
        return true;
    }

    public function synchronize_elasticsearch($param)
    {
        if (empty($param) || !isset($param['es_source'], $param['es_action'])) {
            return false;
        }

        $class_name = $param['es_source'].'Synchronizer';
        $method_name = $param['es_action'];
        if (class_exists($class_name) && method_exists($class_name, $method_name)) {
            unset($param['es_source']);
            unset($param['es_action']);
            call_user_func(array($class_name, $method_name), $param);
        } else {
            EdjLog::error('synchronize method does not exist. class name '.$class_name.' method name '.$method_name);
        }
    }

    /*
     * 用户取消活动处理
     */
    public function customer_cancel_activity($params) {
        LackDriverSmsActivity::model()->run($params);
    }

    /*
     * API nearby 未找到司机活动处理
     */
    public function nearby_nodriver_activity($params) {
        if(!empty($params['token'])) {
            EdjLog::info(__METHOD__."|try to send sms|".$params['token']);
            $validate = CustomerToken::model()->validateToken($params['token']);
            if(!empty($validate['phone'])) {
                LackDriverSmsActivity::model()->run(
                    array('phone' => $validate['phone'])
                );
            }
        }
    }


    /**
     * 收集未评价订单push
     */
    public function collect_order_push_data($params){
        $call_time=$params['call_time'];
        $order_id=$params['order_id'];
        $phone = $params['phone'];

        $order_date = date("Y-m-d H:i:s",$call_time);
        //查找order的具体信息
        $comment_status_array = CommentSms::model()->getCommandSmsByOrderId($order_id);
        $comment_status_int = empty($comment_status_array) ? 0 : 1;
        $orderCommentLog = array(
            'order_id' => $order_id,
            'order_date' => $order_date,
            'phone' => $phone,
            'comment_status' => $comment_status_int
        );
        try{
            $res = OrderCommentLog::model()->getOrderCommentLog($order_id);
            if(empty($res)){
                OrderCommentLog::model()->addOrderCommentLog($orderCommentLog);
                echo "order_id=".$order_id." added \r\n";
            }
        }catch(Exception $e){
            echo "order_id=".$order_id." has been added \r\n";
            return;
        }
    }

    /**
     * 发送未评价订单发送push
     */
    public function send_order_push($params){
        $order_id=$params['order_id'];
        $id = $params['id'];
        $phone = $params['phone'];

        $today = $params['today'];
        $message = $params['message'];
        $dis_message = $params['dis_message'];
        $before_yesterday = date("Y-m-d H:i:s",strtotime("-2 day"));
        //remove the order which commented in send notice period
        $comment_status = CommentSms::model()->getCommandSmsByOrderId($order_id);
        if (!empty($comment_status)) {
            echo "[id=".$id.']has been commentd'.PHP_EOL;
            $reason = 'order='.$order_id.' has been commented just now!';
            OrderCommentLog::model()->updateNoticeStatusFail($order_id,$reason);
            return;
        }

        if(empty($phone)){
            // echo "[id=".$id.'] phone is not exist'.PHP_EOL;
            $reason = 'phone='.$phone.' is not exist!';
            OrderCommentLog::model()->updateNoticeStatusFail($order_id,$reason);
            return;
        }
        //一天之发送一条
        if(DriverStatus::model()->single_get('unCommented_order_'.$phone.$message)!=null){
            return;
        }
        if(!OrderCommentLog::model()->canNotice($phone, $today,$id)){
            echo "[id=".$id.']phone has been sent once'.PHP_EOL;
            $reason = 'phone='.$phone.' today has benn sent once!';
            OrderCommentLog::model()->updateNoticeStatusFail($order_id,$reason);
            return;
        }

        //find user client_id
        $customer_client = CustomerClient::model()->getByPhoneAndLast($phone);
        if(empty($customer_client)){
            //echo "[id=".id.'] customer_client can not be found'.PHP_EOL;
            $reason = 'phone='.$phone.' customer_client can not be found!';
            OrderCommentLog::model()->updateNoticeStatusFail($order_id,$reason);
            return;
        }
        $client_id = $customer_client['client_id'];

        //find user uncommented order number
        $num = OrderCommentLog::model()->getUncommentedOrderNum($phone,$before_yesterday);
        //Android use getui
        if($customer_client['type'] == 1){
            echo "[id=".$id."]use getui service\r\n";
            //添加缓存
            DriverStatus::model()->single_set('unCommented_order_'.$phone.$message,1,24*3600);
            //更新用户消息表，没有则添加，有则更新
            $messageid=CustomerMessage::model()->updateStateByType($phone,CustomerMessage::TYPE_ORDER,$dis_message);
            $params = array(
                'content' => $message,
                'orderId' => $order_id,
                'orderNum' => $num,
                'messageid'=>$messageid
            );
            $content = PushMsgFactory::model()->orgPushMsg($params,PushMsgFactory::TYPE_MSG_CUSTOMER);
            $result = EPush::model('customer')->send($client_id,$content,2,12*3600);
            if ($result['result']!='ok') {
                echo "[id=".$id.'] use getui service failed'.PHP_EOL;
                print_r($result);
                $reason = 'phone='.$phone.',use getui service failed!';
                OrderCommentLog::model()->updateNoticeStatusFail($order_id,$reason);
                return;
            }else{
                //send ok, update send status
                echo 'phone='.$phone.',use getui service ok';
                OrderCommentLog::model()->updateNoticeStatusOk($order_id,$phone);

            }

        }elseif($customer_client['type'] == 0){
            //ios use APNS
            echo "[id=".$id."]use iphone service\r\n";
            //see http://wiki.edaijia.cn/dwiki/doku.php?id=push_%E5%8D%8F%E8%AE%AE%E5%AE%9A%E4%B9%89
            //添加缓存
            DriverStatus::model()->single_set('unCommented_order_'.$phone.$message,1,24*3600);
            //更新用户消息表，没有则添加，有则更新
            $messageid=CustomerMessage::model()->updateStateByType($phone,CustomerMessage::TYPE_ORDER,$dis_message);
            $params = array(
                'message' => $message,
                'orderId' => $order_id,
                'badge' => 1,
                'type' => '2',
                'sound' => 'ping1',
                'orderNum' =>$num,
                'messageid' =>$messageid
            );
            //open apple push
            $body=AppleMsgFactory::model()->orgPushMsgForUncommentedOrder($params);
            
            //changed by yangzhi 2015-04-03
            ClientPush::model()->addAppleMessage($phone, $client_id, $body);
            
            //save to apple push table
            //AppleMessage::model()->addAppleMessage($phone,$client_id,$body);
            
            //fix bug 1703
            OrderCommentLog::model()->updateNoticeStatusOk($order_id,$phone);

        }
    }


    /**
     * 收集未评价订单发送评价短信
     *
     */
    public function collect_order_msg($params){
        $phone = $params['phone'];
        $driver_id=$params['driver_id'];
        $order_id=$params['order_id'];
        $channel=$params['channel'];
        $status=$params['status'];
        $start_time=$params['start_time'];
        $income=$params['income'];
        $imei=$params['imei'];

        $flag = 0;
        $sms_content='';
        //排除公司司机的电话号码
        $ret = Driver::getDriverByPhone($phone);
        if ($ret) {
            $flag++;
            echo '排除公司司机的电话号码'."\r\n";
        }
        //排除固定的电话号码
        if(CustomerWhiteList::model()->in_whitelist($phone)){
            $flag++;
            echo '排除固定的电话号码'."\r\n";
        }
        //过滤非手机号码,1开头的11位数字
        if (!preg_match('%^1\d{10}%', $phone)) {
            $flag++;
            echo '过滤非手机号码'."\r\n";
        }
        //排除已经发送过的订单
        $is_send= SmsSend::model()->isSend($phone,$driver_id,$order_id);
        if($is_send){
            $flag++;
            echo '排除已经发送过的订单'."\r\n";
        }
        //电信手机停止发送
//  关闭 bidong 2013-12-19
//                    $ctcc_Phone=Common::isCTCCPhone($order->phone);
//                    if($ctcc_Phone){
//                        $flag++;
//                    }
        //排除已经评价过的订单 bidong 2013-12-19
        $commentSMS=CommentSms::model()->getCommandSmsByOrderId($order_id);
        if(!empty($commentSMS)){
            $flag++;
            echo '排除已经评价过的订单'."\r\n";
        }
        //排除某些特定渠道订单，不发送订单 2013-12-26
        $partner_common = new PartnerCommon();
        $is_forbid = $partner_common->checkForbidSmsByChannel($channel);
        if($is_forbid){
            $flag++;
            echo '排除某些特定渠道'."\r\n";
        }
        //APP的订单不发送短信 (只发400订单的短信评价) 2014-1-3
        //change by aiguoxin 直接在sql过滤
        // if($order->source==0 || $order->source==2 ){
        //     $flag++;
        //     echo 'APP的订单不发送短信'."\r\n";
        // }
        //获取短信内容
        $temp=SmsTemplate::model()->getContentBySubjectExtOrder('dianping_complete_order_new', array(), $channel);
        $message_complete_order=$temp['content'];


        //电话号码有效 报完的订单 发送短信   @author mengtianxue  2013-05-13
        //组织短信内容，只发送评价和消单短信 @author bidong 2013-08-23
        if ($flag == 0 && $status!= 0 && !empty($message_complete_order)) {
            switch($status){
                case 1: //完成报单的订单
                case 4: //销单审核不通过
                    //开始时间
                    $time=date('H:i',$start_time);
                    //发送点评短信
                    $template_param = array('$driver_id$' => $driver_id,
                        '$time$' => $time,
                        '$price$' => $income);
                    $sms_content=SmsTemplate::model()->replaceContent($message_complete_order,$template_param);
                    break;
                case 2: //销单待审核
                case 3: //已销单
                    //发送消单短信  2013-11-08 暂停发送销单短信
                    //$sms_content=SmsTemplate::model()->replaceContent($message_cancel_order,array('$driver_id$'=>$order->driver_id));
                    break;

                default:

                    break;
            }
            $data = array(
                'sender' => $phone,
                'message' => $sms_content,
                'type' => 0,    //0.评价短信/1.价格核实
                'order_id' => $order_id,
                'driver_id' => $driver_id,
                'order_status' => $status,
                'imei' => $imei
            );
            if( $driver_id!=Push::DEFAULT_DRIVER_INFO && !empty($sms_content)){
                //评价短信。改为只记录数据，再起一个JOB 循环发送 bidong 2013-08-08
                SmsSend::model()->saveSmsLog($data);
            }
            echo 'receive:'.$data['sender'].' ---sms_type:'.$data['type'].' ----driver_id:'.$data['driver_id'].'-----order_id:'.$data['order_id']. "\n";

        }else{
            echo $flag."--order_id:".$order_id."--\n";
        }
    }

    /**
    *   待发短信收集
    *
    */
    public function push_message_send($params){
        $phone = isset($params['phone'])? $params['phone'] :'';
        $content = isset($params['content'])? $params['content'] :'';
        $type = isset($params['type'])? $params['type'] : 0;

        $is_phone = Common::checkPhone($phone);
        if(!$is_phone){
            return false;
        }
        if($type == MessageSend::WASH_TYPE){ //洗车类型推广短信,一个用户只发送一次
            $message_send = MessageSend::model()->findByTypeAndPhone($type,$phone);
            if($message_send){
                EdjLog::info('phone='.$phone.',type='.$type.'只能发送一次');
                return false;
            }
        }
        $res = MessageSend::model()->addMessageSend($phone,$content,$type);
        return $res;
    }

    /**
     * 移除过期已读公告
     */
    public function remove_expire_notice($params){
        $notice_id=$params['notice_id'];
        EdjLog::info('开始移除过期公告notice='.$notice_id);
        NoticeStatus::model()->delDeadlineCache($params);
        EdjLog::info('开始移除过期公告notice='.$notice_id.' ok');
    }

    /**
    *   放入队列消费,发送短信
    *
    */
    public function send_message_list($params){
        $phone = isset($params['phone'])? $params['phone'] :'';
        $content = isset($params['content'])? $params['content'] :'';
        $channel = isset($params['channel'])? $params['channel'] : 0;
        $id = isset($params['id'])? $params['id'] : 0;
        
        if($channel == MessageSend::CHANNEL_MARKET){ //营销类,失败不重发,也没有放入t_sms_log表
            Sms::SendForActive($phone,$content);
        }else{
            Sms::SendForBatchMsg($phone,$content); //优先用营销,失败尝试其他通道
        }
        EdjLog::info('phone='.$phone.',content='.$content.' has sent');
    }

    /**
     * 司机在线时间日志改成队列
     */
    public function driver_online_log($params){
        //aiguoxin 2014-08-25 如果司机上线，记录上线时间到redis;下线则计算在线时长
        $driver_id = $params['driver_id'];
        $last_status = isset($params['last_status']) ? $params['last_status'] : DriverPosition::POSITION_GETOFF;//上次工作状态
        $current_time = $params['time'];
        echo 'driver='.$driver_id.',status='.$params['status'].',last_status='.$last_status.PHP_EOL;
        EdjLog::info('driver='.$driver_id.',status='.$params['status'].',last_status='.$last_status);
        //记录上线时间
        if($last_status==DriverPosition::POSITION_GETOFF){//下班2
            if($params['status'] == DriverPosition::POSITION_IDLE  //当前状态空闲或工作
                || $params['status'] == DriverPosition::POSITION_WORK){
                DriverStatus::model()->setWorkTimeStamp($driver_id,$current_time);
                echo 'driver='.$driver_id.'开始工作'.PHP_EOL;
                EdjLog::info('driver='.$driver_id.'开始工作');
            }
        }

        if($params['status'] == DriverPosition::POSITION_GETOFF){//下线
            $last_online=DriverStatus::model()->getWorkTimeStamp($driver_id);
            DriverStatus::model()->setWorkTimeStamp($driver_id,0);//设置上线时间为0,防止多次下线
            echo 'driver='.$driver_id.'结束工作'.PHP_EOL;
            EdjLog::info('driver='.$driver_id.'结束工作');
            if($last_online > 0){
                $online_time = ($current_time-$last_online)*1000;//转毫秒
                if($online_time>0){
                    if($online_time > DriverOnlineLog::MAX_ONLINE_TIME){
                        echo 'driver='.$driver_id.'在线时间时间超过一天，过滤，online='.$last_online.',current_time='.$current_time.PHP_EOL;
                        EdjLog::info('driver='.$driver_id.'在线时间时间超过一天，过滤，online='.$last_online.',current_time='.$current_time);
                    }else{
                        $res=DriverOnlineLog::model()->addDriverOnlineLog($driver_id,$online_time,date("Y-m-d H:i:s",$params['time']));
                        if($res){
                            echo 'driver='.$driver_id.'上线时间更新成功'.',online='.$last_online.',finish='.$current_time.PHP_EOL;
                            EdjLog::info('driver='.$driver_id.'上线时间更新成功'.',online='.$last_online.',finish='.$current_time);

                        }else{
                            echo 'driver='.$driver_id.'上线时间更新失败'.PHP_EOL;
                            EdjLog::info('driver='.$driver_id.'上线时间更新失败');
                        }
                    }
                }else{
                    echo 'driver='.$driver_id.'上线时间有误，过滤'.PHP_EOL;
                    EdjLog::info('driver='.$driver_id.'上线时间有误，过滤');
                }
            }
        }
    }

    public function baidu_map_binding_bonus($params){
        $short_message = '恭喜您获得e代驾送您的29元白天，39元晚间代驾券，支付时直接减免；累了，醉了找个司机送您回家吧。立即使用http://t.cn/RwrxsJy';
        $bonus_code1 = '1855117325';
        $bonus_code2 = '5232737495';
        $binding_ret1 = FinanceWrapper::bindBonusGenerate($params['phone'] , $bonus_code1 , 1 , '');
        $binding_ret2 = FinanceWrapper::bindBonusGenerate($params['phone'] , $bonus_code2 , 1 , $short_message);
        $log = new Bonus39Log();
        $log->act_name = $params['act_name'];
        $log->open_id = $params['open_id'];
        $log->phone = $params['phone'];
        $log->create_time = date('Y-m-d H:i:s',time());
        if($binding_ret1 && $binding_ret1['code'] == 0 && $binding_ret2 && $binding_ret2['code'] == 0){
            EdjLog::info($params['act_name'].'活动为phone='.$params['phone'].'的用户绑定优惠劵成功');
            $log->status=1;
        }else{
            EdjLog::info($params['act_name'].'活动为phone='.$params['phone'].'的用户绑定优惠劵失败');
            $log->status=0;
            FinanceUtils::sendFinanceAlarm('活动绑定优惠券失败', json_encode($params));
        }
        $ret = $log->save();
        return true;
    }

	// 司机已就位之后，订单却被取消了，我们认为这种很有可能是一种恶意订单
	// 需要对其进行处理。考虑到恶意订单的判断逻辑会随着时间的迁移而发生变化
	// 所以，我在这里加上了_v1这个结尾以准备扩展——曾坤 2015/3/16
	public function malicious_cancelling_v1($params)
	{
        EdjLog::info('malicious_cancelling_v1 '.json_encode($params), 'console');

        if (!isset($params['initiator'], $params['driver'], $params['customer'], $params['city'])) {
            return;
        }

        // VIP用户默认他们是不会恶意销单的——曾坤 2015/3/19
        if (!empty(VipPhone::model()->getVipByphone($params['customer']))) {
            return;
        }

        $config = new Redis();
        $config->connect("redishaproxy.edaijia-inc.cn",22121); 
        $malicious_order_city_list = $config->get('malicious_order_city_list');
        if (!empty($malicious_order_city_list)) {
            $malicious_order_city_list = json_decode($malicious_order_city_list);
        } else {
            // 这一功能目前只在深圳上线，深圳的城市ID是6——曾坤 2015/3/19
            $malicious_order_city_list = array(6);
        }

        if (!in_array($params['city'], $malicious_order_city_list)) {
            return;
        } 


        // 推送违约处罚规则
        static $content = '尊敬的客户，为了您和他人都能及时叫到司机，请在不需要司机时提早通过客户端、400电话、微信销单，若由于您的原因导致司机就位后销单达到3次，我们将对账号进行屏蔽处理，敬请谅解。客服电话4006913939';
        Sms::SendForBatchMsg($params['customer'], $content);

        $user_id = '';
        if ($params['initiator'] == 'customer') {
            $user_id = $params['customer'];
        } else if ($params['initiator'] == 'driver') {
            $user_id = $params['driver'];
        }

        // 记录本次取消操作到数据库——曾坤 2015/3/20
        Yii::app()->dborder->createCommand()->insert(
            't_malicious_order',
             array(
                 'user_id' => $user_id
             )
        );

        // 把用户踢下线 
        $udid = CustomerToken::model()->getUdidbyPhone($params['customer']);
        EdjLog::info('malicious_cancelling_v1 udid '.json_encode($udid), 'console'); 
        if (!empty($udid)) {
            $customer_logic = new CustomerLogic();
            $token = $customer_logic->getTokenCacheByPhoneUdid($params['customer'], $udid['udid']);
            EdjLog::info('malicious_cancelling_v1 token '.json_encode($token), 'console'); 
            if (!empty($token)) {
                $customer_logic->clearCustomerTokenCache($token['authtoken']);
                $customer_logic->deleteCustomerTokenCache($params['customer'], 'edaijia');
                CustomerToken::model()->deleteAuthToken($params['customer'], 'edaijia');
            }
        }
    }

    /**
     * v2后台用户获取短信验证码队列 也可以用于后台其他异步发送短信功能 duke
     * @param $params
     * @return array|bool
     */
    public function v2loginsms($params){
        $phone = isset($params['phone']) ? $params['phone'] : '';
        $content = isset($params['content']) ? $params['content'] : '';
        if(!$phone || !$content) return false;
        echo 'phone:'.$phone.' -- content:'.$content."\n";
        return Sms::SendSMS( $phone, $content);
    }


    public function createDriverDocUrl($params){
        $id = $params['id'];
        $condition = json_decode($params['condition'],1);
        $url = DriverOrder::model()->upload($condition);
        if($url){
            $data = array(
                'url'=>$url,
                'status'=>DriverExportLog::STATUS_FINISH
            );
            $res = DriverExportLog::model()->updateByPk($id,$data);
            if(!$res){
                EdjLog::info(' update export log field url:'.$url);
            }
        }
        else{
            EdjLog::info('upload to upyun field condition:'.json_encode($condition).' id:'.$id);
        }
    }

    /*
     * 司机报名后，生成司机二维码
     * @param $params
     * @return bool
     */
    public function create_driver_qrcode($params){
        $id_card = $params['id_card'];
        $signup_id = $params['signup_id'];
        $driver_phone = $params['driver_phone']; //备用数据
        $mod = new Helper();
        $data_qr = json_encode(array('idCard'=>$id_card));
        $create_res = $mod->create_qrcode($data_qr,'driver/qrcode',$signup_id.rand(1234,52345).'.jpg');
        //var_dump($create_res);die;
        if($create_res){
            $recruit_mod = new DriverRecruitment();
            $res = $recruit_mod->findByPk($signup_id);
            $res->qr_code = $create_res;
            //print_r($res);
            $ret = $res->save(false);
            if(!$ret) {
                $ress = $recruit_mod->findByPk($signup_id);
                //print_r($ress);die;
                if(!$ress->qr_code){
                    EdjLog::info('保存司机二维码到数据库失败'.$create_res.'----'.$signup_id.'----'.$id_card);
                }
            }

        }else{
            //echo '生成司机二维码失败'.$create_res.'----'.$signup_id.'----'.$id_card;
            EdjLog::info('生成司机二维码失败'.$create_res.'----'.$signup_id.'----'.$id_card);
        }
        return true;
    }

    public function bad_weather_sms_notify($params)
    {
        if (!isset($params['phone'], $params['content'])) {
            return;
        }

        Sms::SendForOrder($params['phone'], $params['content']);
    }

    public function publishOrderStatusChanged($params) {
        OrderStatusChangedPublisher::publish($params);
    }

    /**
     * 此方法是解决Apple消息被错误的当作Queue消息来消费.
     * 
     * @param $params
     */
    public function send_apple_message($params)
    {
        EdjLog::error("Error send apple message through queue, please use command instead.");
    }


    public function immediatelyPush($params){
        UserNotifyPush::doImmediatelyPush($params);
    }
    public function publishDriverQuiz($params) {
        DriverQuizPublisher::publish($params);
    }
}
