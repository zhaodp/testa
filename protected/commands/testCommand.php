<?php
class TestCommand extends CConsoleCommand {


	
	public function actionIosPush() {
		Yii::import("application.vendors.*");
		IosPush::model()->PushToSingle(array(
			'device_token' => '8d4db9a6144aa0dba12c9f5027b13b06ea1120e144f3db1f3405da8eeafddd03', // string
			'msg' => 'Hello 你好', //string
			'badge' => 3, //int default is 1
			'expiry' => 30 // int unit second , default is 30
		));

	}

    //php yiic test test
	public function actionTest() {
		echo  date('Y-m-d', strtotime("-" . 0 . " day"));;
	}
	
	public function actionDache() {
		$new = new DcRouteGPS();
		//$new->init();
		//$n = new DcDriverGPS();
		//$n->init();
		//exit;
	//	$a = $new->nearbyRoute(116.398449, 39.913979, 1, 2500);
		$a = $new->getRouteByCityId(1);
		var_dump($a);
		exit;
	}

	public function actionDispatch($f, $id='') {
		switch ($f) {
			case 'insert' :
				$ret = QueueDispatchDriver::model()->insert($id);
				break;
			case 'delete' :
				$ret = QueueDispatchDriver::model()->delete($id);
				break;
			case 'all' :
				$ret = QueueDispatchDriver::model()->showall($id);
				break;
			case 'clean' :
				$ret = QueueDispatchDriver::model()->clean($id);
				break;
		}
		print_r($ret);
	}

	/**
	 * driverstatus方法测试
	 */
	public function actionDriverStatus($f='heartbeat', $id=null) {
		if ($id!==null) {
			$driver=DriverStatus::model()->get($id);
		}
		switch ($f) {
			case 'position' :
				var_dump($driver->position);
				break;
			case 'rebuild' :
				DriverStatus::model()->rebuild();
				break;
			case 'heartbeat' :
				echo $driver->heartbeat;
				break;
			case 'reload' :
				if ($id!==null) {
					DriverStatus::model()->reload($id);
				} else {
					DriverStatus::model()->reload();
				}
				break;
			case 'clean' :
				DriverStatus::model()->clean();
				break;
			case 'get' :
				print_r($driver);
				break;
			case 'onlines' :
				$onlines=DriverStatus::model()->onlines();
				print_r($onlines);
				echo '在线人数：'.count($onlines);
				break;
		}
		
		//self::nearby_driver($onlines, '39.93177029981', '116.46811101799', $idel_driver, $busy_driver);
	}

	public function actionPush($f='push', $id=null) {
		switch ($f) {
			case 'push' :
				$client=GetuiClient::model()->getClientInfo($id);
				$message=array(
						'type'=>MessageLog::TYPE_STATUS,
						'content'=>array(
								'message'=>'测试push'
						),
						'timestamp'=>time()
				);
				$params['version']='driver';
				
				$params=array(
						'client_id'=>$client->client_id,
						'message'=>'',
						'level'=>IGtPush::LEVEL_HIGN
				);
				IGtPush::PushToSingle($params);
				break;
		}
	}

	public function actionGPS($f) {
		switch ($f) {
			case 'convert' :
				$gps=GPS::model()->convert(array(
						'longitude'=>116.52128176577,
						'latitude'=>39.908151251998
				), 'wgs84');
				print_r($gps);
				$gps=GPS::model()->convert(array(
						'longitude'=>116.52128176577,
						'latitude'=>39.908151251998
				), 'google');
				print_r($gps);
				break;
			case 'all' :
				GPS::model()->getAll();
		}
	}

	public function actionRedis() {
		$cache_keys=array(
				'DRIVER_TOKEN_',
				'GPS_BAIDU_STREET_',
				'GPS_WGS_2_GOOGLE_',
				'GPS_Google_2_BAIDU_',
				'GPS_WGS_2_BAIDU_'
		);
		foreach($cache_keys as $item) {
			for($i=0; $i<=16; $i++) {
				$cache_key=$item.dechex($i).'*';
				$keys=GPS::model()->redis->keys($cache_key);
				foreach($keys as $key) {
					GPS::model()->redis->del($key);
					echo $key."\n";
				}
			}
		}
		
		die();
		// 		DriverStatus::model()->reload('BJ9004');
		// 		$driver = DriverStatus::model()->get('BJ9004');
		// 		print_r($driver->position);
		$json='{"client_id":"127f2e9028e9d0fbcf5124221771feea","udid":"1099e9bb7ca64b1a725c7bfea5def657","version":"driver","city":"1","driver_id":"BJ0858","created":"2013-05-19 11:21:37"}';
		$params=json_decode($json, true);
		QueueProcess::model()->driver_push_register($params);
	}

	public function actionMongo($f='nearby') {
		switch ($f) {
			case 'index' :
				DriverGPS::model()->reindex();
				break;
			case 'sync' :
				DriverGPS::model()->sync();
				break;
			case 'init' :
				DriverGPS::model()->init();
				break;
			case 'status' :
				DriverGPS::model()->status('BJ9004', 1);
				break;
			case 'update' :
				DriverGPS::model()->update(9278,'BJ9000', array(
						'lng'=>116.453557,
						'lat'=>39.968712
				), 0);
				break;
			case 'nearby' :
				$drivers=DriverGPS::model()->find(array('city_id'=>1,'status'=>0));
				//$drivers=DriverGPS::model()->nearby(116.461889, 39.910907, 1, 5);
				print_r($drivers);
			//var_dump(iterator_to_array($drivers));
		}
	}

	public function actionCallLog() {
		$json='{"callTime":1367321023,"endTime":1367321029,"imei":"353721051592248","latitude":"39.967933","longitude":"116.445272","phone":"18911883373","status":"1","talkTime":"5","type":"0","simcard":"89860021010819587002","driver_id":"BJ9004"}';
		$params=json_decode($json, true);
		QueueProcess::model()->driver_calllog($params);
		$json='{"callTime":1367320894,"endTime":1367320898,"imei":"353721051592248","latitude":"39.967933","longitude":"116.445272","phone":"18911883373","status":"1","talkTime":"4","type":"2","simcard":"89860021010819587002","driver_id":"BJ9004"}';
		$params=json_decode($json, true);
		QueueProcess::model()->driver_calllog($params);
		$json='{"callTime":1367321021,"endTime":1367321026,"imei":"353721051592248","latitude":"39.967933","longitude":"116.445272","phone":"18911883373","status":"1","talkTime":"5","type":"0","simcard":"89860021010819587002","driver_id":"BJ9004"}';
		$params=json_decode($json, true);
		QueueProcess::model()->driver_calllog($params);
	}

	public function actionSms($phone='') {
		//短信通道测试
		$mobile[]='18701552183';
		$content='短信通道测试';
		if (!empty($phone)) {
			$mobile[]=$phone;
		}
		//$mobile=array('18911883373','15011553373','15544804577');
		foreach($mobile as $phone) {
			Sms::SendSMS($phone, '三三易九 '.$content, SMs::CHANNEL_GSMS);
			Sms::SendSMS($phone, '指联 '.$content, SMs::CHANNEL_ZLZX);
			Sms::SendSMS($phone, 'e达信 '.$content, SMs::CHANNEL_SOAP);
		}
	}

	public function actionCustomerBatch() {
		$customerVisit=new CustomerVisit();
		$list=$customerVisit->findAll();
		foreach($list as $l) {
			CustomerVisitAnswer::model()->updateAll(array(
					'batch_id'=>$l->batch_id
			), 'customer_id = :customer_id', array(
					':customer_id'=>$l->id
			));
			echo $l->id.' '.$l->batch_id."\n";
		}
	}

	/**
	 * 测试短信发送
	 * @author sunhongjing 2013-04-20
	 */
	public function actionTestSendSms() {
		$phone='13581855712';
		$content='测试1234';
        $subcode='1234';

		$send_ret=Sms::SendSMS($phone, $content, Sms::CHANNEL_MT,$subcode);
		print_r($phone);
        print_r($send_ret);
	}

    /**
     * 带附加码 短信测试
     */
    public function actionTestSendSmsEX() {
        $phone='13581855712';
        $content='测试附加码短信';
        $subcode='26';
        $send_ret=Sms::SendSMSEx($phone, $content,$subcode);
        print_r($send_ret);
    }

    /**
     * 获取缓存最大附加码
     * @param null $key
     */
    public function actionGetCacheSubcode($key=null){

        $cache_key='max_sms_subcode';
        if($key){
            $cache_key=$key;
        }
        $max_subcode= Yii::app()->cache->get($cache_key);
        print_r($max_subcode);
    }
	/**
	 * 发送命令  php protected/yiic sms senddriverssms
	 * 
	 * @author sunhongjing 2013-04-11
	 * 
	 * @return unknown_type
	 */
	public function actionTestSmsChannel() {
		
		//分公司经理电话
		$phones=array(
				//array('phone'=>'13811480665'),//冯欣
				//array('phone'=>'13996396764'),
				array(
						'phone'=>'18701552183'
				) //孙洪静电话
		);
		
		//$phones = Yii::app()->db->createCommand()->select('*')->from('t_tt')->queryAll();
		

		$content='测试上行通道';
		$i=1;
		foreach($phones as $item) {
			echo $i.'.'.$item['phone']."\n";
			//echo  $content . "\n";
			$ret=Sms::SendSMSEx($item['phone'], $content, '778');
			print_r($ret);
			$i++;
		}
	}

	/**
	 * 短信余额接口测试
	 * @author sunhongjing 2013-04-20
	 */
	public function actionTestSmsBalance() {
		$ret_zlzx=Sms::Balance(Sms::CHANNEL_ZLZX);
		if ($ret_zlzx) {
			$str_arr=explode(':', $ret_zlzx);
			$ret=Sms::SendSMS('18701552183', '指联在线短信余量'.$str_arr[1].'条,已低于20000条，请及时充值。', Sms::CHANNEL_ZLZX);
			print_r($ret);
		} else {
			echo "fail";
		}
	}

	/**
	 * 测试接收上行短信
	 * 
	 * @author sunhongjing 2012-03-27
	 */
	public function actionTestRecSms() {
		$ret=Sms::RecSMSEx();
		var_dump($ret);
	}

	public function actionCrossDatabaseQuery() {
		
		//		DriverDailyOrder::$db = Yii::app()->dbstat;  //在上面重新指定数据库时   可以通过  所有的表都指定到car 
		//		$model=new DriverDailyOrder();
		//		$model::$db = Yii::app()->dbstat; //在下面重新指定数据库时   找不到数据库
		

		//		$data = $model->getDriverRankData($city_id,$type);
		//		$this->render 页面报错： Base table or view not found: 1146 Table 'db_stat.t_admin_group' doesn't exist. The SQL statement executed was: SELECT * FROM `t_admin_group` `t` WHERE name=:name LIMIT 1 
		$city_id=1;
		$type=0; //0日排行，1月排行
		$model=new DriverDailyOrder();
		
		$created=date('Y-m-d', strtotime("-2 day"));
		$criteria=new CDbCriteria();
		$params=array();
		if ($city_id!=0) {
			$criteria->condition='city_id = :city_id';
			$params[':city_id']=$city_id;
		}
		$criteria->addCondition('created = :created');
		$params[':created']=$created;
		$criteria->order='income desc';
		$criteria->params=$params;
		//获取日排行汇总
		$driverRankCount=array();
		$dataProvider=new CActiveDataProvider($model, array(
				'criteria'=>$criteria,
				'pagination'=>array(
						'pageSize'=>10
				)
		));
		
		//		$this->render('ranking',array(
		//			'model'=>$model,
		//			'dataProvider'=>$dataProvider
		//		));
	}

	public function actionGetCallLog($order_id) {
		
		//$order_id = 421802;
		$order=Order::getDbReadonlyConnection()->createCommand()->select('*')->from('t_order')->where('order_id=:order_id', array(
				':order_id'=>$order_id
		))->queryRow();
		OrderDriverState::setState($order['order_id'], $order['imei'], $order['call_time']);
	}

	/**
	 * 
	 */
	public function actionIndex() {
		$start = 0;
		$offset = 500;
		
		while(true){
			echo $start . "\n";
			$sql = "SELECT FileServer, MonitorFilename
					FROM `t_callcenter_log_201307`
					WHERE MonitorFilename IS NOT NULL
					LIMIT $start , $offset";
			
			$urls = Yii::app()->db->createcommand($sql)->queryAll();
			if($urls){
				$ch = curl_init();
				foreach($urls as $item){
					$host = $item['FileServer'];
					$url = str_replace($item['FileServer'], 'http://edj-callcenter.qiniudn.com', $item['MonitorFilename']);

					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_URL, $url);
					//curl_setopt($ch, CURLOPT_POST, true);
					// 执行
					curl_exec($ch);
						
					// 检查是否有错误发生
					if(!curl_errno($ch)){
						$info = curl_getinfo($ch);
						echo $info['total_time'] . ' seconds' . "\n";
					}
				}
				curl_close($ch);
				$start += $offset;
			}else{
				break;
			}
		}
		
		die();
		
		$ch = curl_init($url);
		// 执行
		//curl_exec($ch);
		
		// 检查是否有错误发生
		if(!curl_errno($ch)){
			$info = curl_getinfo($ch);
			echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url'];
		}
		
		// Close handle
		curl_close($ch);
		die();
		
		$drivers = QueueDispatchDriver::model()->showall();
		print_r($drivers);
		die();
		for($i=0;$i<70;$i++){
			$result = QueueDispatchDriver::model()->insert('BJ9001');
			echo $result . "\n";
			echo time(). "\n";
			sleep(5);
		}
		
		die();
		$result = DriverGPS::model()->nearbyAll('116.453820' , '39.936809' , 2 , 5000);
		var_dump($result);exit;
	}

	public function actionCode($n) {
		//$start = 1000425;
		$start=9556;
		//$start = 2000003;
		$number=0;
		while(true) {
			if ($number>$n) {
				break;
			}
			$code=$start++;
			$last=Helper::CheckCode($code);
			if ($last) {
				echo $code.$last."\n";
				$number++;
			}
		}
	}

	public function actionResetCache() {
		$json=json_encode(array());
		Yii::app()->cache->set('SYSTEM_DICT_vipphone_status', $json, 0);
		Yii::app()->cache->set('SYSTEM_DICT_vip_status', $json, 0);
		Yii::app()->cache->set('SYSTEM_DICT_vip_trade_type', $json, 0);
		Yii::app()->cache->set('SYSTEM_DICT_vip_type', $json, 0);
	}

	/**
	 * 读取缓存
	 * php protected/yiic test getdictbyname --dictname=vipphone_status
	 */
	public function actionGetDictByName($dictname) {
		$models=Dict::model()->findAll(array(
				'condition'=>'dictname=:dictname',
				'params'=>array(
						':dictname'=>$dictname
				),
				'order'=>'postion'
		));
		$array=array();
		foreach($models as $model) {
			$array[$dictname][$model->code]=$model->name;
		}
		
		print_r($array);
		
		$dict=Dict::items($dictname);
		print_r($dict);
		
		$cache_key='SYSTEM_DICT_'.$dictname;
		$json=Yii::app()->cache->get($cache_key);
		echo $json;
	}

	public function actionGetCode($code) {
		$last=Helper::CheckCode($code);
		
		echo $code.$last."\n";
	}

	/**
	 * 读取缓存
	 * php protected/yiic test updatepositionbytable
	 */
	public function actionUpdatePositionByTable() {
		$connect=Yii::app()->dbstat_readonly;
		$command=$connect->createCommand()->select('*')->from('t_android_driver_upload_position_log')->where('after_insert = 0 and driver_id=:driver_id', array(
				':driver_id'=>'BJ9003'
		));
		$positions=$command->queryAll();
		$command->reset();
		foreach($positions as $position) {
			
			$position['towers']=json_decode($position['towers'], true);
			
			self::driver_position($position);
			$sql='UPDATE t_android_driver_upload_position_log SET after_insert = 1 WHERE id = '.$position['id'];
			
			//			$command = $connect->createCommand($sql)->execute();
			//			$command->reset();
			

			//die();
		}
	}

	/**
	 * 
	 * 检查保险费是否扣成功
	 */
	public function actionCast() {
		$group=AdminGroup::model()->getGroups();
		print_r($group);
	}

	public function actionDai() {
		$drivers=array();
		
		$sql='SELECT longitude ,latitude FROM `t_app_call_record` 
				where left(driverID,2) =\'BJ\' and  created  between unix_timestamp("2013-01-05") and unix_timestamp("2013-01-07")
				group by longitude ,latitude ';
		$pos=Yii::app()->db->createCommand($sql)->queryAll();
		
		$city='北京';
		foreach($pos as $po) {
			$lon=intval($po['longitude']*100000);
			$lat=intval($po['latitude']*100000);
			$url="http://dj.95081.com/c/invok/mapList?lon=$lon&lat=$lat&city=$city&currentpage=1&pagenum=100";
			
			$ch=curl_init();
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 0);
			$data=curl_exec($ch);
			curl_close($ch);
			
			//driverName driveYears mobile
			$items=CJSON::decode($data, true);
			if ($items) {
				foreach($items as $item) {
					if (isset($item['mobile'])&&!array_key_exists($item['mobile'], $drivers)&&$item['driveYears']>=5&&$item['userStatus']==0) {
						$params=array(
								'name'=>$item['driverName'],
								'mobile'=>$item['mobile'],
								'city_id'=>1,
								'district_id'=>1,
								'work_type'=>0,
								'id_card'=>$item['idCard'],
								'domicile'=>$item['province'],
								'driver_card'=>$item['runNum'],
								'driver_year'=>$item['driveYears'],
								'recycle'=>1,
								'ctime'=>time(),
								'experience'=>'95081'
						);
						try {
							Yii::app()->db->createCommand()->insert('t_driver_zhaopin', $params);
						} catch(Exception $e) {
						}
					}
				}
			}
		}
	}

	public function actionYun() {
		$drivers=array();
		
		$sql='SELECT longitude ,latitude FROM `t_app_call_record` 
				where left(driverID,2) =\'BJ\' and  created  between unix_timestamp("2013-01-05") and unix_timestamp("2013-01-07")
				group by longitude ,latitude';
		$pos=Yii::app()->db->createCommand($sql)->queryAll();
		
		$city='北京';
		foreach($pos as $po) {
			$lon=$po['longitude'];
			$lat=$po['latitude'];
			$url="http://cdsa.daijia.net/customer/u/nearbydriver/index?lat=$lat&lng=$lon&cityName=%E5%8C%97%E4%BA%AC%E5%B8%82&userId=uitisfd1b0b5090c41e897c6568fc7ac9773&mapType=2";
			
			$ch=curl_init();
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 0);
			$data=curl_exec($ch);
			curl_close($ch);
			
			$items=json_decode(json_decode($data), true);
			if ($items) {
				foreach($items['driverInfos'] as $item) {
					if (!array_key_exists($item['driverPhone'], $drivers)) {
						$drivers[$item['driverPhone']]=array(
								'name'=>$item['name'],
								'phone'=>$item['driverPhone']
						);
						echo $item['name'].','.$item['driverPhone']."\n";
						//$message = '师傅，诚邀您加入e代驾！e代驾日单量突破3000单，只要你来就有钱赚！云代驾师傅2月6日前签约，送手机，送工服，送现金200元，再免2月份信息费！登录报名网址：http://zhaopin.edaijia.cn';
						$message='师傅，e代驾年前最后一天签约培训！云盟司机2月6日前签约，送手机，送工服，送现金200元，再免2月份信息费！当天签约当天接单！登录报名网址：http://zhaopin.edaijia.cn';
						$content=str_replace('*', '', $item['name']).$message;
						Sms::SendSMS($item['driverPhone'], $content);
					}
				}
			}
		}
		
		//print_r($drivers);
	}

	public function actionAliOss() {
		RankMonList::$db=Yii::app()->dbstat;
		$model=new RankMonList('search');
		
		$model->unsetAttributes(); // clear any default values
		if (isset($_GET['RankMonList']))
			$model->atributes=$_GET['RankMonList'];
	}

	/**
	 * 
	 * 测试order model 
	 */
	public function actionOrder() {
		//测试订单生成
	}

	/**
	 * 
	 * 测试订单结算
	 * @param int $order_id
	 */
	public function actionOrderDeal($order_id) {
		//		$order_id = '334145';
		$command=Yii::app()->db->createCommand();
		$command->select('o.order_id,order_number,vipcard,phone,call_time,booking_time,location_start,location_end,distance,driver_id,city_id,income,source,l.created')->from('t_order o')->join('t_order_log l', 'o.order_id = l.order_id')->where('status in(1,4) and o.driver_id = l.operator and o.order_id=:order_id', array(
				':order_id'=>$order_id
		));
		$order=$command->queryRow();
		if ($order) {
			echo $order['order_id']."\n";
			
			$queueProcess=new QueueProcess();
			
			$queueProcess->deal_order($order, false);
		}
	}

	/**
	 * 
	 * 测试订单结算月份
	 * @param int $step
	 */
	public function actionOrderDealMonth($start=1, $step=1000) {
		//		$order_id = '334145';
		$command=Yii::app()->db->createCommand();
		$command->select('order_id,driver_id')->from('t_order o')->where('status in(1,4) and order_id > :start and order_id <= :end', array(
				':start'=>$start,
				':end'=>$start+$step
		));
		$orders=$command->queryAll();
		foreach($orders as $order) {
			$settle_table='';
			$order_id=$order['order_id'];
			echo $order_id;
			echo "\n";
			$tableName='t_employee_account';
			$account=Yii::app()->db_finance->createCommand()->select('*')->from($tableName)->where('order_id=:order_id and type=:type', array(
					':order_id'=>$order_id,
					':type'=>EmployeeAccount::TYPE_ORDER_INFOMATION
			))->queryRow();
			if ($account) {
				$settle_table=$tableName;
				echo $settle_table;
				echo "\n";
			} else {
				$tableName='t_employee_account_201301';
				$account=Yii::app()->db_finance->createCommand()->select('*')->from($tableName)->where('order_id=:order_id and type=:type', array(
						':order_id'=>$order_id,
						':type'=>EmployeeAccount::TYPE_ORDER_INFOMATION
				))->queryRow();
				
				if ($account) {
					$settle_table=$tableName;
					echo $settle_table;
					echo "\n";
				} else {
					$tableName='t_employee_account_201212';
					$account=Yii::app()->db_finance->createCommand()->select('*')->from($tableName)->where('order_id=:order_id and type=:type', array(
							':order_id'=>$order_id,
							':type'=>EmployeeAccount::TYPE_ORDER_INFOMATION
					))->queryRow();
					if ($account) {
						$settle_table=$tableName;
						echo $settle_table;
						echo "\n";
					} else {
						$tableName='t_employee_account_201211';
						$account=Yii::app()->db_finance->createCommand()->select('*')->from($tableName)->where('order_id=:order_id and type=:type', array(
								':order_id'=>$order_id,
								':type'=>EmployeeAccount::TYPE_ORDER_INFOMATION
						))->queryRow();
						if ($account) {
							$settle_table=$tableName;
							echo $settle_table;
							echo "\n";
						} else {
							$tableName='t_employee_account_201210';
							$account=Yii::app()->db_finance->createCommand()->select('*')->from($tableName)->where('order_id=:order_id and type=:type', array(
									':order_id'=>$order_id,
									':type'=>EmployeeAccount::TYPE_ORDER_INFOMATION
							))->queryRow();
							
							if ($account) {
								$settle_table=$tableName;
								echo $settle_table;
								echo "\n";
							} else {
								$tableName='t_employee_account_201209';
								$account=Yii::app()->db_finance->createCommand()->select('*')->from($tableName)->where('order_id=:order_id and type=:type', array(
										':order_id'=>$order_id,
										':type'=>EmployeeAccount::TYPE_ORDER_INFOMATION
								))->queryRow();
								
								if ($account) {
									$settle_table=$tableName;
									echo $settle_table;
									echo "\n";
								} else {
									$tableName='t_employee_account_201208';
									$account=Yii::app()->db_finance->createCommand()->select('*')->from($tableName)->where('order_id=:order_id and type=:type', array(
											':order_id'=>$order_id,
											':type'=>EmployeeAccount::TYPE_ORDER_INFOMATION
									))->queryRow();
									
									if ($account) {
										$settle_table=$tableName;
										echo $settle_table;
										echo "\n";
									} else {
										$tableName='t_employee_account_201207';
										$account=Yii::app()->db_finance->createCommand()->select('*')->from($tableName)->where('order_id=:order_id and type=:type', array(
												':order_id'=>$order_id,
												':type'=>EmployeeAccount::TYPE_ORDER_INFOMATION
										))->queryRow();
										
										if ($account) {
											$settle_table=$tableName;
											echo $settle_table;
											echo "\n";
										} else {
											$tableName='t_employee_account_201206';
											$account=Yii::app()->db_finance->createCommand()->select('*')->from($tableName)->where('order_id=:order_id and type=:type', array(
													':order_id'=>$order_id,
													':type'=>EmployeeAccount::TYPE_ORDER_INFOMATION
											))->queryRow();
											
											if ($account) {
												$settle_table=$tableName;
												echo $settle_table;
												echo "\n";
											} else {
												$tableName='t_employee_account_201205';
												$account=Yii::app()->db_finance->createCommand()->select('*')->from($tableName)->where('order_id=:order_id and type=:type', array(
														':order_id'=>$order_id,
														':type'=>EmployeeAccount::TYPE_ORDER_INFOMATION
												))->queryRow();
												if ($account) {
													$settle_table=$tableName;
													echo $settle_table;
													echo "\n";
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
			
			@Yii::app()->dbstat->createCommand("INSERT INTO t_deal_order_log(order_id, driver_id, settle_table) VALUE (:order_id, :driver_id, :settle_table)")->bindParam(":order_id", $order['order_id'])->bindParam(":driver_id", $order['driver_id'])->bindParam(":settle_table", $settle_table)->execute();
		}
	}

	/**
	 * 
	 * 迁移已报单数据到Order订单
	 */
	public function actionMatch() {
		$offset=0;
		while(true) {
			$criteria=new CDbCriteria(array(
					'limit'=>500,
					'offset'=>$offset,
					'order'=>'id'
			));
			//匹配call_time前后1小时内
			$works=WorkList::model()->findAll($criteria);
			if ($works) {
				foreach($works as $work) {
					$attributes=array(
							'order_number'=>$work->wid,
							'name'=>$work->name,
							'vipcard'=>$work->vip_id,
							'start_time'=>strtotime($work->start_time),
							'end_time'=>strtotime($work->end_time),
							'distance'=>$work->distance,
							'reach_time'=>strtotime($work->reach_time),
							'reach_distance'=>$work->reach_distance,
							'location_start'=>$work->start_location,
							'location_end'=>$work->end_location,
							'income'=>$work->charge+$work->tip,
							'cast'=>-5,
							'status'=>1
					);
					
					//查询phone是否为有效用户，否则添加一个新的用户
					$model=Customer::getCustomer($work->phone);
					if (!$model) {
						$attr=array(
								'name'=>$work->name,
								'phone'=>$work->phone,
								'insert_time'=>date('Y-m-d h:i:s', time())
						);
						$customer=new Customer();
						$customer->attributes=$attr;
						$customer->save();
						$attributes['user_id']=$customer->primaryKey;
					} else {
						$attr=array(
								'name'=>$work->name,
								'insert_time'=>date('Y-m-d h:i:s', time())
						);
						$model->updateByPk($model->id, $attr);
						$attributes['user_id']=$model->id;
					}
					
					$model=CustomerCar::model()->find('number=:number', array(
							':number'=>$work->car_number
					));
					
					if (!$model) {
						$car_attr=array(
								'user_id'=>$attributes['user_id'],
								'brand'=>$work->car_type,
								'number'=>$work->car_number,
								'status'=>$work->car_stative
						);
						$car=new CustomerCar();
						$car->attributes=$car_attr;
						$car->save();
						$attributes['car_id']=$car->primaryKey;
					} else {
						$attributes['car_id']=$model->car_id;
					}
					
					$book_time_a=strtotime($work->booking_time)-30*60;
					$book_time_b=strtotime($work->booking_time)+30*60;
					$cri=new CDbCriteria(array(
							'condition'=>'phone=:phone and driver_id=:driver_id',
							'params'=>array(
									':phone'=>$work->phone,
									':driver_id'=>$work->user
							)
					));
					$cri->addBetweenCondition('call_time', $book_time_a, $book_time_b);
					
					$order=Order::model()->find($cri);
					
					if ($order) {
						$order->updateByPk($order->order_id, $attributes);
					} else {
						$add_order=new Order();
						if ($work->wid!=0) {
							$ret=$add_order->model()->find('order_number='.$work->wid);
						} else {
							$ret=$add_order->model()->find('phone=:phone and driver_id=:driver_id and order_date=:order_date', array(
									':phone'=>$work->phone,
									':driver_id'=>$work->user,
									':order_date'=>date('Ymd', strtotime($work->booking_time))
							));
							echo $work->id."\n";
							print_r(array(
									':phone'=>$work->phone,
									':driver_id'=>$work->user,
									':order_date'=>date('Ymd', strtotime($work->booking_time))
							));
						}
						if (!$ret) {
							$employee=Employee::model()->find('user=:user', array(
									':user'=>$work->user
							));
							
							if ($employee) {
								$attributes['driver']=$employee->name;
								$attributes['driver_id']=$employee->user;
								$attributes['driver_phone']=$employee->phone;
								$attributes['imei']=$employee->imei;
							} else {
								$attributes['driver_id']=$work->user;
								$attributes['imei']=$work->employee_id;
								
								//echo $work->user . "\n";
							}
							
							$attributes['source']=0;
							$attributes['phone']=$work->phone;
							$attributes['call_time']=strtotime($work->booking_time);
							$attributes['order_date']=date('Ymd', strtotime($work->booking_time));
							$attributes['booking_time']=strtotime($work->booking_time);
							$attributes['start_time']=strtotime($work->start_time);
							$attributes['end_time']=strtotime($work->end_time);
							$attributes['distance']=$work->distance;
							$attributes['description']='数据升级V2';
							$attributes['created']=strtotime($work->insert_time);
							
							$add_order->attributes=$attributes;
							$add_order->insert();
						} else {
							$ret->updateByPk($ret->order_id, $attributes);
						}
					}
				}
			} else {
				break;
			}
			$offset+=500;
		}
	}

	public function actionImportExam() {
		$params=array();
		
		$driver_exams=new DriverExam();
		$driver_exam=$driver_exams->findAll();
		foreach($driver_exam as $item) {
			$addSQL="insert into t_question(type,title,question_type,city_id,track,contents,correct,status) 
				values(:type,:title,:question_type,:city_id,:track,:contents,:correct,:status)";
			$params[':type']=$item->type;
			$params[':title']=$item->title;
			$params[':question_type']=2;
			$params[':city_id']=0;
			$params[':track']=0;
			if ($item->c==''||$item->d=='') {
				$code_list=array(
						0=>$item->a,
						1=>$item->b
				);
			} else {
				$code_list=array(
						0=>$item->a,
						1=>$item->b,
						2=>$item->c,
						3=>$item->d
				);
			}
			
			$params[':contents']=json_encode($code_list);
			$params[':correct']=$item->correct;
			$params[':status']=$item->status;
			
			$command=Yii::app()->db->createCommand($addSQL);
			$command=$command->execute($params);
		}
		echo '导入成功';
	}

	/**
	 * 批量修改deal_information
	 * @param $date 是t_employee_account的后缀名称
	 * 			不传的时候默认是t_employee_account表
	 * php yiic.php test OrderDealInformation --date=201205
	 */
	public function actionOrderDealInformation($date=null) {
		$table=empty($date) ? 't_employee_account' : 't_employee_account'.'_'.$date;
		$command=Yii::app()->db_finance->createCommand();
		$orders=$command->select('order_id,cast')->from($table)->where('type = 1')->queryAll();
		
		foreach($orders as $list) {
			$this->updateDealInformationByOrderId($list);
			echo $list['order_id']."\n";
		}
	}

	public function updateDealInformationByOrderId($date) {
		$order=new Order();
		$order->updateAll(array(
				'cast'=>$date['cast']
		), 'order_id = :order_id', array(
				':order_id'=>$date['order_id']
		));
	}

	/**
	 * php yiic.php test CustomerBonus --driver_id=010768
	 * @param unknown_type $driver_id
	 */
	public function actionCustomerBonus($driver_id) {
		$list=Yii::app()->db->createCommand()->select('*')->from('t_customer_bonus')->where('order_id != 0 and bonus_sn = :bonus_sn', array(
				':bonus_sn'=>$driver_id
		))->order('used desc')->queryAll();
		foreach($list as $value) {
			$date=date('Ym', $value['used']);
			$table_name=$date>201301 ? 't_employee_account' : 't_employee_account_'.$date;
			$is_no=Yii::app()->db_finance->createCommand()->select('*')->from($table_name)->where("order_id = :order_id and comment like '%".$driver_id."%'", array(
					':order_id'=>$value['order_id']
			))->queryRow();
			$xinxi=!empty($is_no) ? '是' : '否';
			$phone='';
			//			if($xinxi == '是'){
			$order=Order::model()->getOrderById($value['order_id']);
			$phone=$order['phone'];
			//			}
			

			echo $value['order_id'].' '.$xinxi.' '.$table_name.' '.$phone.' '.$order['status']."\n";
		}
	}

	protected function settle($list) {
		if ($list['type']!=0) {
			//修改t_employee_account 结转信息
			$params=array();
			$month=$list['settle_date'];
			$id=$list['id'];
			$sql="update t_employee_account set cast = cast+:cast where user = :user and settle_date = :settle_date";
			$params[':cast']=$list['cast'];
			$params[':user']=$list['user'];
			$params[':settle_date']=$month;
			Yii::app()->db_finance->createCommand($sql)->execute($params);
			
			//修改t_employee_acount_settle 统计信息
			$params_settle=array();
			$type=$list['type'];
			$fieldName="t".$type;
			$settle_date=$list['settle_dates'];
			$sql_settle="update t_employee_account_settle set $fieldName = $fieldName+:cast, total = total+:total where user = :user and settle_date = :settle_date";
			$params_settle[':cast']=$list['cast'];
			$params_settle[':total']=$list['total'];
			$params_settle[':user']=$list['user'];
			$params_settle[':settle_date']=$settle_date;
			Yii::app()->db_finance->createCommand($sql_settle)->execute($params_settle);
		}
		
		// 把t_employee_account 表中的数据插入到对应的表中
		$sql_i="insert into `t_employee_account_$month` 
				select * from `t_employee_account`
				where id = $id";
		Yii::app()->db_finance->createCommand($sql_i)->execute();
		
		//删除t_employee_account 表中对应的数据
		$sql_d="delete from t_employee_account
				where  id = $id";
		Yii::app()->db_finance->createCommand($sql_d)->execute();
	}

	/**
	 * 
	 * 修改bug
	 * @param 日期 $date
	 */
	public function actionAccount($date=null) {
		if ($date) {
			$end_time=date('Y-m-d 15:00:00', strtotime($date));
			$begin_time=date('Y-m-d 15:00:00', strtotime($date)-86400);
		} else {
			$end_time=date('Y-m-d 15:00:00', time());
			$begin_time=date('Y-m-d 15:00:00', time()-86400);
		}
		$pagesize=500;
		$offset=0;
		
		while(true) {
			$command=Yii::app()->db->createCommand();
			$command->select('o.order_id,order_number,vipcard,phone,call_time,booking_time,location_start,location_end,distance,driver_id,city_id,income,price,l.created')->from('t_order o')->join('t_order_log l', 'o.order_id = l.order_id')->where('status in(1,4) and l.created between unix_timestamp(:begin_time) and unix_timestamp(:end_time) and l.operator =""', array(
					':begin_time'=>$begin_time,
					':end_time'=>$end_time
			))->group('order_id')->limit($pagesize, $offset);
			
			$rows=$command->queryAll();
			if ($rows) {
				foreach($rows as $item) {
					
					$order_params=array(
							':order_id'=>$item['order_id']
					);
					
					if (EmployeeAccount::model()->exists('order_id=:order_id', $order_params)) {
						EmployeeAccount::model()->deleteAll('order_id=:order_id', $order_params);
						echo 'delete '.$item['order_id']."\n";
					}
					
					echo $item['order_id']."\n";
					//每订单扣除信息费
					self::order_infomatin_fee($item);
					//扣除每单保险费(仅北京司机)
					self::order_insurance($item);
					
					if ($item['vipcard']) {
						self::order_vip($item);
					} else {
						//司机优惠券返点
						//self::order_bonus($item, true);
						self::order_normal($item);
					}
				}
				$offset+=$pagesize;
			} else {
				break;
			}
		}
	}

	/**
	 * 
	 * 生成每日15点前完成报单的订单对账单中优惠券返现
	 * @param 日期 $date
	 */
	public function actionAccountBonus($date=null) {
		if ($date) {
			$end_time=date('Y-m-d 15:00:00', strtotime($date));
			$begin_time=date('Y-m-d 15:00:00', strtotime($date)-86400);
		} else {
			$end_time=date('Y-m-d 15:00:00', time());
			$begin_time=date('Y-m-d 15:00:00', time()-86400);
		}
		
		$pagesize=50;
		$offset=0;
		
		while(true) {
			$command=Yii::app()->db->createCommand();
			$command->select('o.order_id,order_number,vipcard,phone,call_time,booking_time,location_start,location_end,distance,driver_id,income,source,l.created')->from('t_order o')->join('t_order_log l', 'o.order_id = l.order_id')->where('status in(1,4) and l.created between unix_timestamp(:begin_time) and unix_timestamp(:end_time) and l.operator = ""', array(
					':begin_time'=>$begin_time,
					':end_time'=>$end_time
			))->group('order_id')->limit($pagesize, $offset);
			
			$rows=$command->queryAll();
			
			if ($rows) {
				foreach($rows as $item) {
					echo $item['order_id']."\n";
					
					if (!$item['vipcard']) {
						self::order_bonus($item, true);
					}
				}
				$offset+=$pagesize;
			} else {
				break;
			}
		}
	}
	
	//修改t_order_log 的operator
	public function actionOrderLogOperator($date=NULL) {
		$command=Yii::app()->db->createCommand();
		$command->select('o.order_id,order_number,vipcard,phone,call_time,booking_time,location_start,location_end,distance,driver_id,city_id,income,price,l.created,l.id')->from('t_order o')->join('t_order_log l', 'o.order_id = l.order_id')->where('status in(1,4) and l.created between unix_timestamp(:begin_time) and unix_timestamp(:end_time) and l.operator =""', array(
				':begin_time'=>$begin_time,
				':end_time'=>$end_time
		))->group('order_id')->limit($pagesize, $offset);
		
		$rows=$command->queryAll();
		if ($rows) {
			foreach($rows as $item) {
				OrderLog::model()->updateByPk($item['id'], array(
						'operator'=>$item['driver_id']
				));
				echo $item['driver_id']."\n";
			}
		}
	}

	/**
	 * 骚扰电话加入黑名单
	 * 
	 * @author sunhongjing 2013-05-13
	 */
	public function actionCrankCall2Blacklist($phone) {
		$phone=empty($phone) ? '13466328021' : trim($phone);
		$ret=DriverStatus::model()->getBlacklist($phone);
		$params=array(
				'driver_ids'=>$ret,
				'phone'=>$phone
		);
		
		$ret=QueueProcess::model()->crank_call_to_blacklist($params);
		print_r($ret);
	}

	/**
	 * 骚扰电话放入队列
	 * 
	 * @author sunhongjing 2013-05-13
	 */
	public function actionPutCrankCallQue() {
		for($i=1; $i<50; $i++) {
			$data['type']=$i%2;
			$data['phone']='13466328021';
			$data['callTime']=time();
			$data['talkTime']='60';
			$data['driver_id']="BJ9001";
			$data['status']=$i%3;
			echo $data['phone']."---".$data['type']."---".$data['driver_id']."---".$data['status']."---".$data['callTime'];
			$ret=DriverStatus::model()->putBlacklistPool($data['phone'], $data);
			var_dump($ret);
		}
	}

	/**
	 * 推送消息测试
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-04-28
	 *
	 */
	public function actionPushMessageTest() {
		//推送订单
		$time=time();
		$booking_time=date('Y-m-d H:i:s', $time+1200);
		$queue_arr=array(
				'phone'=>'13611126764', //客户电话
				'contact_phone'=>'13611126764', //客户电话
				'city_id'=>1, //需要gps反推
				'callid'=>md5(time()), //callid 时间戳加密
				'name'=>'测试订单', //需要传进来
				'number'=>3, //司机数量
				'address'=>'建外soho', //地址                
				'comments'=>'测试订单，不要派单', //说明
				'booking_time'=>$booking_time, //传进来的时间+20分钟
				'flag'=>1, //派单状态
				'update_time'=>'0000-00-00 00:00:00', //更新时间
				'agent_id'=>'测试自动派单', //操作员 --- 
				'dispatch_agent'=>'', //下单的时间
				'dispatch_time'=>'0000-00-00 00:00:00', //下单的时间
				'created'=>date('Y-m-d H:i:s', $time) //下单的时间
		);
		$model=new OrderQueue();
		$model->attributes=$queue_arr;
		$model->save();
		$params=array(
				'type'=>'order',
				'content'=>'测试订单',
				'level'=>3,
				'driver_id'=>'BJ9002',
				'queue_id'=>$model->id,
				'customer_name'=>'丛铭',
				'customer_phone'=>'13611126764',
				'address'=>'测试地址',
				'booking_time'=>time(),
				'created'=>date('Y-m-d H:i:s', time())
		);
		//推送消息
		//		$params=array(
		//				'type'=>'msg_driver',  //类型 order订单  status订单状态 msg_driver司机客户端消息 msg_customer
		//				                       //     notice_driver公告司机端  notice_customer公告客户端
		//				'content'=>'测试消息内容',
		//				'level'=>3,  //级别
		//				'driver_id'=>'BJ9002',
		//              'created'=>date('Y-m-d H:i:s' , time()),
		//		);
		//		//推送公告
		//		$params=array(
		//				'type'=>'notice_driver',  //类型 order订单  status订单状态 msg_driver司机客户端消息 msg_customer
		//				                       //     notice_driver公告司机端  notice_customer公告客户端
		//				'content'=>'测试公告内容',
		//				'level'=>3,  //级别
		//				'driver_id'=>'BJ9002',
		//              'created'=>date('Y-m-d H:i:s' , time()),
		//		);
		QueueProcess::push_message($params);
	}
	
	/**
	* 通过订单表反查用户的城市(临时倒数据，仅执行一次)
	* author zhangtingyi
	*/
	public function actionSetCustomerCity() {
		$pagesize=100;
		$offset=0;
		while(true) {
			$sql = "SELECT id,phone FROM t_customer_main LIMIT {$offset},{$pagesize}";
			$command = Yii::app ()->db_readonly->createCommand($sql);
			$all_user = $command->queryAll();
			if (is_array($all_user) && count($all_user)) {
				foreach ($all_user as $val) {
					$phone = $val['phone'];
					$customer_id = $val['id'];
					//$sql = "SELECT city_id FROM t_order WHERE phone='".$phone."' order by order_date desc";
					$command = Yii::app ()->db_order_readonly->createCommand();
					$command->select('city_id');
					$command->from('t_order');
					$command->where('phone=:phone');
					$command->order('order_date desc');
					$command->bindParam(":phone", $phone);
					$city_id = $command->queryScalar();
					if ($city_id>0) {
						$customer_model = CustomerMain::model()->findByPk($customer_id);
						$customer_model->city_id = $city_id;
						echo 'customer:'.$customer_id."  city_id:".$city_id.' success:'.intval($customer_model->save())."\n";
					}
				}
				$offset+=$pagesize;
			} else {
				break;
			}
		}
	}


	/**
	 * 获取所有重复的customer
	 * Enter description here ...
	 * php yiic.php test UpdateCustomerUserID
	 */
	public function actionUpdateCustomerUserID() {
		$customerList=Yii::app()->db_readonly->createCommand()->select("phone, count(1) as cc")->from("t_customer")->where("phone <> ''")->group("phone")->having("cc > 1")->queryAll();
		echo count($customerList)."\n";
		foreach($customerList as $list) {
			$phone=$list['phone'];
			$id=$this->getCustomerID($phone);
			if ($id) {
				//修改order表中的user_id
				$this->updateOrderUserID($id, $phone);
				echo $phone.". ".$id."\n";
			}
		}
	}

	/**
	 * 获取customer_main新表里的id 
	 * Enter description here ...
	 * @param unknown_type $phone
	 */
	public function getCustomerID($phone) {
		return Yii::app()->db_readonly->createCommand()->select("id")->from("t_customer_main")->where("phone = :phone", array(
				':phone'=>$phone
		))->queryScalar();
	}

	/**
	 * 修改order的user_id
	 * Enter description here ...
	 * @param unknown_type $user_id
	 * @param unknown_type $phone
	 */
	public function updateOrderUserID($user_id, $phone) {
		$sql="update t_order set user_id = :user_id where phone = :phone";
		$command=Order::getDbMasterConnection()->createCommand($sql);
		$command->bindParam(':user_id', $user_id);
		$command->bindParam(':phone', $phone);
		$command->execute();
	}

	/**
	 * 修改customerCar的数据
	 * Enter description here ...
	 */
	public function actionUpdateCustomerCar() {
		$pagesize=100;
		$offset=0;
		
		while(true) {
			$command=Yii::app()->db_readonly->createCommand();
			$rows=$command->select('car_id,user_id')->from("t_customer_car")->where("car_id > 0")->limit($pagesize, $offset)->queryAll();
			if ($rows) {
				foreach($rows as $item) {
					$car_id=$item['car_id'];
					$user_id=0;
					$user_id=$this->getOrderUser_id($car_id);
					if (!empty($user_id)&&$item['user_id']!=$user_id) {
						$this->updateCustomerCar($user_id, $car_id);
					}
					echo $car_id." ".$user_id."\n";
				}
				$offset+=$pagesize;
			} else {
				break;
			}
		}
	}

	public function getOrderUser_id($car_id) {
		$command=Order::getDbReadonlyConnection()->createCommand();
		$cars=$command->select("user_id")->from("t_order")->where("order_id > 0 and car_id = :car_id", array(
				':car_id'=>$car_id
		))->queryAll();
		$user_id=0;
		foreach($cars as $car) {
			if ($car!=0) {
				$user_id=$car['user_id'];
				break;
			}
		}
		return $user_id;
	}

	public function updateCustomerCar($user_id, $car_id) {
		$sql="update t_customer_car set user_id = :user_id where car_id = :car_id";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(':user_id', $user_id);
		$command->bindParam(':car_id', $car_id);
		$command->execute();
	}

	/**
	 * 把t_customers 表中数据添加到t_customer_main表中
	 * Enter description here ...
	 */
	public function actionCustomers() {
		$pagesize=100;
		$offset=0;
		while(true) {
			$command=Yii::app()->db_readonly->createCommand();
			$rows=$command->select('*')->from("t_customers")->limit($pagesize, $offset)->queryAll();
			if ($rows) {
				foreach($rows as $item) {
					$phone=$item['phone'];
					//检查用户是否存在
					$id=$this->checkedCustomerMain($phone);
					if (empty($id)) {
						//添加用户
						$id=$this->addCustomerMain($phone);
					}
					//修改customer_bouns
					$this->updateCustomerBouns($phone, $id);
					echo $id."\n";
				}
				$offset+=$pagesize;
			} else {
				break;
			}
		}
	}

	public function checkedCustomerMain($phone) {
		$back='';
		$customer=Yii::app()->db_readonly->createCommand()->select("*")->from("t_customer_main")->where("phone = :phone", array(
				':phone'=>$phone
		))->queryRow();
		if ($customer)
			$back=$customer['id'];
		return $back;
	}

	public function addCustomerMain($phone) {
		$Model=new CustomerMain();
		$data=array();
		$data['phone']=$phone;
		$data['create_time']=date('Y-m-d H:i:s');
		$Model->attributes=$data;
		$Model->insert();
		return $Model->id;
	}

	public function updateCustomerBouns($customer_phone, $customer_id) {
		$sql="update t_customer_bonus set customer_id = :customer_id where customer_phone = :customer_phone";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(':customer_id', $customer_id);
		$command->bindParam(':customer_phone', $customer_phone);
		$command->execute();
	}

	/**
	 * 自动派单
	 */
	public function actionDispatchTest($params="BJ9001") {
		$time=date("Y-m-d H:i:s", time()+Yii::app()->params['dispatch']['time_interval']);
		
		//取要派的单
		//  while(true){
		

		$orders=OrderQueue::model()->getDriverNumber($time, 10);
		
		//根据订单地址查找周边司机
		if (!empty($orders)) {
			foreach($orders as $order) {
				//$drivers=DriverGPS::model()->nearby($order['lng'], $order['lat'], 0, $order['number']*4);
				

				$drivers=array();
				
				$exp=explode(",", $params);
				if (!empty($exp)) {
					foreach($exp as $k=>$driver_id) {
						$drivers[]=array(
								'id'=>$k,
								'driver_id'=>$driver_id,
								'distance'=>100
						);
					}
				} else {
					$drivers[]=array(
							'id'=>1,
							'driver_id'=>$params,
							'distance'=>100
					);
				}
				
				if (!empty($drivers)) {
					$drivers=array_slice($drivers, 0, $order['number']);
					foreach($drivers as $driver) {
						$flag=QueueDispatchDriver::model()->insert($driver['driver_id']);
						if ($flag) {
							echo "driver_id:".$driver['driver_id']."<br>";
							$task=array(
									'method'=>'dispatch_driver',
									'params'=>array(
											'queue_id'=>$order['queue_id'],
											'driver_id'=>$driver['driver_id']
									)
							);
							Queue::model()->task($task);
						}
					}
				}
			}
		} else {
			sleep(5);
		}
		
		// }
	}
	
	
	/**
	 * 测试定时短信发送，代扩展码的
	 * 
	 * @author sunhongjing 2013-05-27
	 * 
	 */
	public function actionTestPresendSms()
	{
		$mobile = '18701552183';
		$content = "测试带扩展码的定时发送";
		$subcode = "1";
		$presend = date('Y-m-d')." 17:00:00";
		//$ret = Sms::SendSMSEx($phone, $message, $subcode,$presend);
		//var_dump($ret);
		$soap = new SoapClient(Yii::app()->params['sms_soap']);
		$soap->decode_utf8 = true;
		$params = array (
			'sn'=>Yii::app()->params['sms_sn'], 
			'pwd'=>Yii::app()->params['sms_password'], 
			'mobile'=>$mobile, 
			'content'=>$content."【e代驾】", 
			'ext'=>$subcode);
		
		$params['stime'] = trim($presend);
		
		$result = $soap->__soapCall('MT', array (
			'parameters'=>$params));
		
		echo "REQUEST:\n" . $soap->__getLastRequest() . "\n";
		print_r($result);
		
	}

	/**
	 * 查看被锁定的司机---自动派单测试
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-05-21
	 */
	public function actionShowLockDriver() {
		$result=QueueDispatchDriver::model()->showall();
		var_dump($result);
	}

	/**
	 * 清空被锁定的司机---自动派单测试
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-05-21
	 */
	public function actionCleanLockDriver() {
		QueueDispatchDriver::model()->clean();
	}

	/**
	 * 获取司机信息缓存---自动派单测试
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-05-21
	 */
	public function actionQueueDispatchDriverCache($queue_id) {
		$result=OrderQueue::model()->getDispatchDriverCache($queue_id);
		var_dump($result);
	}

    /**
	 * 统计司机接单记录
	 * @author mengtianxue  2013-05-28
     * php yiic.php test CallReport --date=2013-05-22
	 */
    public function actionCallReport($date = null){
        
        if($date === null){
            $days = date("t", (time() - 86400));
            $date = date('Y-m', (time() - 86400));
        }else{
            $days = date("t", strtotime($date));
            $date = date('Y-m', strtotime($date));
        }
        $call_user_list = AdminUser::model()->getCallUserList();
        
        $day = '';
        for($d = 1; $d <= $days; $d++){
            for($h = 0; $h < 24; $h++){
                $day = $date."-".$d." ".$h.":00:00";
                foreach($call_user_list as $list){
                    $list['call_count'] = OrderQueue::model()->getCallTotal($day,$list['user_id']);
                    if( $list['call_count'] > 0){
                        $list['order_count'] = OrderQueue::model()->getCallCenterTotle($day,$list['name']);
                        $list['dispatch_count'] = OrderQueue::model()->getdispatchTotle($day,$list['name']);
                        $list['report_time'] = date('Y-m-d H:i:s', strtotime($day));
                        $list['created'] = date('Y-m-d H:i:s');
                        Yii::app()->dbreport->createCommand()->insert('t_call_phone_report',$list);
                        echo $list['name']."\n";
                    }
                }
            }
        }
    }

    
    
    public function actionOrderAuto2ManualTest($queue_id=1)
    {
    	$comments = '长时间未派出，撤回手动派单';
    	$ret = OrderQueue::model()->setOrder2ManualOpt($queue_id,$comments);
    	var_dump($ret);
    }
    
    

    public function actionOrderPosition($driver_id = 'BJ1161'){
        $gps_position=array(
                'latitude'=>39.903318,
                'longitude'=>116.449833
            );
        $gps=GPS::model()->convert($gps_position);
        var_dump($gps);
        exit;
        /*$arr = array(
            'order_id' => $order_id,
            'flag' => OrderPosition::FLAG_ACCEPT,
            'gps_type'=>'baidu',
            'lat'=>39.912297,
            'lng'=>116.480633,
            'log_time'=>date("Y-m-d H:i:s"),
        );

        $ret = OrderPosition::model()->insertInfo($arr);
        var_dump($ret);
        exit;*/

       /* $ret = Order::model()->setPushOrderMsg_test($driver_id ,231354);
        var_dump($ret);
        exit;*/

       /* $message_arr=array(
            'type'=>IGtPush::TYPE_ORDER_DETAIL,
            'content'=>'test',
            'level'=>3,  //级别
            'driver_id'=>'BJ9006',
            'queue_id'=>'123',
            'order_id'=>'123',
            'created'=>date('Y-m-d H:i:s' , time()),
            'card'=>'', //VIP或优惠卷卡号
            'balance'=>'', //VIP余额或优惠卷余额
            'source'=> '',//订单来源
        );
        $push_message_flag = QueueProcess::push_message($message_arr);
        var_dump($push_message_flag);
        exit;*/
    }


    /**
     * 通过电话记录自动成单
     */
    public function actionCallGenOrder(){
        $arr = array(
            'driver_id' => 'BJ9006' ,
            'phone'=>13381059701,
            'call_time' =>date("Y-m-d H:i:s",time()),
        );

        $driver = DriverStatus::model()->get($arr['driver_id']);
        
        $params = array(
            'driver_id'=>$driver->driver_id,
            'phone'=>$arr['phone'],
            'city_id'=>$driver->city_id,
            'name' => '先生',
            'call_time' => $arr['call_time'],
            'booking_time'=>$arr['call_time']+60*20,
            'address'=>$driver->position['street'],
            'lng'=>$driver->position['longitude'],
            'lat'=>$driver->position['latitude'],
        );
        print_r($params);
        $ret = OrderQueue::model()->booking($params);
        var_dump($ret);
        if(!$ret || $ret['code']==2){
           var_dump(false);
        }else{
            var_dump(true);
        }

        //$ret = QueueProcess::model()->new_gen_order($where);
        //var_dump($ret);
        exit;
       /* $params = array(
            'queue_id'=>123,
            'driver_id'=>'BJ9006',
            'type'=>'order',
            'push_msg_id'=>12313,
            'confirm_time'=>date("Y-m-d H:i:s"), //确认时间
            'gps_type'=>'baidu',
            'lng'=>116.999999,
            'lat'=>30.1213,
            'log_time'=>time(),
        );
        $ret = AutoOrder::model()->order_operate($params);
        var_dump($ret);
        exit;*/
    }


    public function actionOnlineOrder($push_msg_id = '' , $queue_id = '' , $driver_id = ''){
       $arr = array(
           'push_msg_id' => $push_msg_id ,
           'queue_id' =>$queue_id,
           'driver_id'=>$driver_id,
           'confirm_time'=>date("Y-m-d H:i:s") //确认时间
       );
        $ret = Order::model()->order_operate($arr);
        var_dump($ret);



    }

    /**
     * 发送点评短信
     * 每天15点30分发送（视回复数调整）
     *
     */
    public function actionDianping($date = null) {
        if ($date) {
            $end_time = date('Y-m-d 07:00:00', strtotime($date));
            $begin_time = date('Y-m-d 07:00:00', strtotime($date)-86400);
        } else {
            $end_time = date('Y-m-d 07:00:00', time());
            $begin_time = date('Y-m-d 07:00:00', time()-86400);
        }

        $offset = 0;
        $pagesize = 50;

        $criteria = new CDbCriteria();
        $criteria->condition = 'call_time between :begin_time and :end_time';
        $criteria->addcondition('status = 1');
        $criteria->limit = $pagesize;
        $criteria->group = 'phone,driver_id';
        $criteria->params = array (
            ':begin_time'=>strtotime($begin_time),
            ':end_time'=>strtotime($end_time));
        while (true) {
            $criteria->offset = $offset;
            $orders = Order::model()->findAll($criteria);
            if ($orders) {
                foreach($orders as $order) {
                    $flag = 0;
                    //排除公司司机的电话号码
                    $ret = Driver::getDriverByPhone($order->phone);
                    if ($ret) {
                        $flag++;
                    }
                    //排除固定的电话号码
                    $ret = in_array($order->phone, Yii::app()->params['whitelist']);
                    if ($ret==true) {
                        $flag++;
                    }
                    //过滤非手机号码,1开头的11位数字
                    if (!preg_match('%^1\d{10}%', $order->phone)) {
                        $flag++;
                    }

                    //电话号码有效 报完的订单 发送短信   @author mengtianxue  2013-05-13
                    if ($flag==0 && $order->status != 0) {
                        echo $order->phone."\n";
                        $order_status = '0'; //报单
                        $driver = Driver::getProfile($order->driver_id);
                        $content_price = "";
                        if ($order->status == 4 || $order->status == 1) {
                            $content = MessageText::getFormatContent(MessageText::NEW_CUSTOMER_ORDER_COMMENT, $driver->user);
                            //'请评价昨天的e代驾司机%s。如“上车未告知起始里程、恶意多收费、虚报里程、未出示工卡”，请回复"差评+内容"，感谢您使用！';

                            //发送价格确认短息内容
                            if(empty($order->vipcard)){
                                $content_price = '尊敬的客户，您昨日%s使用e代驾，现金支付%s元。 如与您实际现金支付不一致，可回复“实际支付的金额+说明”进行举报。例如“100 包含小费“';
                                $content_price = sprintf($content_price, date('H时i分', $order->booking_time), $order->price);
                            }else{
                                $content_price = '尊敬的客户，您昨日%s使用e代驾，代驾费%s元，其中支付现金%s元。 如与您实际支付不一致，可回复“实际金额+说明”进行举报。例如"100 包含小费"';
                                $content_price = sprintf($content_price, date('H时i分', $order->booking_time), $order->income - $order->price, $order->price);
                            }
                        } else {
                            $order_status = '1'; //销单
                            $content = MessageText::getFormatContent(MessageText::NEW_CUSTOMER_ORDER_CANCEL, $driver->user);
                            //'非常抱歉昨天的e代驾司机%s未能为您服务！是否此司机借故拒单？请给予回复"差评+内容"，感谢您的支持！';
                        }

                        $content = sprintf($content, $driver->name);
                        //type:评价短信0/短信询价1
                        $data = array(
                            'sender' => '18600882374',
                            'message' => $content,
                            'type' => 0,
                            'order_id' => $order->order_id,
                            'driver_id' => $driver->user,
                            'order_status'=> $order_status,
                            'imei'=>$driver->imei
                        );

                        //评价短信
                        SmsSend::commentSmsEx($data);
//                        echo $content."\n";

                        //发送价格核实短信  @author mengtianxue 2013-05-09
                        if(!empty($content_price)){
                            $data['type'] = 1;
                            $data['order_status'] = '0';
                            $data['message'] = $content_price;
                            SmsSend::commentSmsEx($data);
//                            echo $content_price."\n";
                        }
                    }
                }
                $offset += $pagesize;
            } else {
                break;
            }
        }
    }



    /**
	 * 个推消息推送失败转成短信发送订单信息
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-05-10
	 * @uses php yiic.php sms GetuiMoveSms
	 */
	public function actionGetuiMoveSms() {
		$start_time = date("Y-m-d H:i:s", time()-600);
		$end_time = date("Y-m-d H:i:s", time()-120);
		$flag=1;
		
		//TODO 分页
		$push_message_list=Yii::app()->dbreport->createCommand()
							->select('*')
							->from('t_message_log')
							->where('created BETWEEN :start_time AND :end_time AND flag = :flag AND type IN (:type_order_detail , :type_msg_leader)', array(
							        ':start_time' => $start_time,
							        ':end_time' => $end_time,
									':flag'=>$flag,
									':type_order_detail'=>IGtPush::TYPE_ORDER_DETAIL,
									':type_msg_leader'=>IGtPush::TYPE_MSG_LEADER,
							))->queryAll();
		$i=1;
		foreach($push_message_list as $item) {
			if (in_array($item['driver_id'] , array('BJ9001' , 'BJ9002' , 'BJ9003' , 'BJ9004' , 'BJ9005' , 'BJ9006' , 'BJ9008' , 'BJ9009' , 'BJ9012' , 'BJ1161' , 'BJ9010' , 'BJ9011'))) {
				echo 'push:' . $item['queue_id'] . ' ' . $item['driver_id'] . "\n";
				$result=AutoOrder::model()->GetuiMoveToSms($item['queue_id'], $item['driver_id'] , $item['type']);
				$i++;
			}
		}
	}

/**
 * 推送弹回测试
 */
public function actionOrderRejectLog(){
        $arr = array(
            'queue_id'=>2,
            'driver_id'=>'BJ9006',
            'created'=>date("Y-m-d H:i:s"), //确认时间
        );
        $flag = QueueProcess::model()->push_order_reject_log($arr);
        var_dump($flag);
}

    /**
     * 将测试工号在t_driver表中增加标识位
     */
    public function actionSetTestDriverId() {
        $test_driver_ids = Common::getTestDriverIds(0);
        if (is_array($test_driver_ids) && count($test_driver_ids)) {
            foreach ($test_driver_ids as $driver_id) {
                $_model = Driver::model()->find('user=:driver_id',array(':driver_id'=>$driver_id));
                if ($_model) {
                    $_model->is_test = 1;
                    echo $driver_id.'--'.intval($_model->save(false)). "\n";
                } else {
                    echo $driver_id.'--'.'0'. "\n";
                }
            }
        }
    }
    
    /**
     * 个推注册测试
     * @author AndyCong<congming@edaijia.cn>
     * @uses php yiic.php test DriverPushRegister
     */
    public function actionDriverPushRegister() {
    	for ($i = 100 ; $i < 1000 ; $i++) {
			$params = array(
	    	    'client_id' => '1234567890100000',
	    	    'udid' => 'abcdefghigklmn',
	    	    'version' => 'driver',
	    	    'city' => '2',
	    	    'driver_id' => 'BJ9'.$i,
	    	    'created' => date('Y-m-d H:i:s'),
	    	);
	    	$ret = QueueProcess::model()->driver_push_register($params);
    	}
    }
    
    /**
     * 新自动派单
     * @author zhanglimin
     */
    public function actionDispatchNew(){
        $message="%s|%s|%s|%s|%s\n";
        $error_message="%s|%s|%s|%s|%s|%s\n";
        $dispatch_time=date(Yii::app()->params['formatDateTime'], time());
        $time=date("Y-m-d H:i:s", time()+Yii::app()->params['dispatch']['time_interval']);
        $orders = DispatchOrderQueue::model()->getWaitFormQueue($time);
        if(!empty($orders)){
            foreach($orders as $order){
                $drivers = DispatchDriver::model()->getDrives($order['queue_id'],$order['order_id']);
                if(empty($drivers) || (isset($drivers['flag'])&& $drivers['flag']== false)|| empty($drivers['drivers']) ){
                    //输出错误信息。。。
                    echo sprintf($error_message, $dispatch_time, "queue_id:".$order['queue_id'], "order_id:".$order['order_id'], $drivers['msg'], $order['booking_time'], $order['address']);
                    continue;
                }
                foreach($drivers['drivers'] as $driver){
                    echo sprintf($message, $dispatch_time, $driver['driver_id'], "order_id:".$order['order_id'], $order['booking_time'], $order['address']);
                    $task=array(
                        'method'=>'dispatch_driver_new',
                        'params'=>array(
                            'queue_id'=>$order['queue_id'],
                            'order_id'=>$order['order_id'],
                            'driver_id'=>$driver['driver_id'],
                        )
                    );
                    Queue::model()->putin($task,'task');
                }
            }
        }
    }

    /**
     * 客户投诉回填城市
     * @author bidong
     */
    public function actionComplainBackfillCity(){

        $citys=Dict::items('city_prefix');
        $citys=array_flip($citys);

        $command=Yii::app()->db->createCommand();
        $noCityArr=$command->select('id,city_id,driver_id')->from('t_customer_complain')
            ->where('city_id =:city and driver_id !="" ',array('city'=>0))->queryAll();
        $command->reset();
        foreach($noCityArr as $item){
            if(strlen($item['driver_id'])==6){
                $pre=strtoupper(substr($item['driver_id'],0,2));
                $cityid=$citys[$pre];

                $command->update('t_customer_complain',
                    array('city_id'=>$cityid),
                    'id=:id', array(':id'=>$item['id']));
            }
        }
    }
    
    public function actionDriverReload($driver_id = '') {
    	$driver_id = !empty($driver_id) ? $driver_id : 'BJ9002';
    	DriverStatus::model()->reload($driver_id);
    }

    /**
     * 修复司机户籍
     */
    public function actionResetDomicile() {
        $f = fopen('/tmp/domicile.txt','r');
        while (!feof($f)) {
            $line = fgets($f);
            $arr = explode(',', $line);
            if (is_array($arr) && count($arr)) {
                $model = Driver::model()->find('user=:driver_id', array(':driver_id'=>trim($arr[0])));
                if ($model) {
                    $model->domicile = trim($arr[1]);
                    $model->register_city = trim($arr[2]);
                    echo trim($arr[0]). '--' . intval($model->save(false)). "\n";
                }
            }
        }
    }

    /*
     * ticket user change
     * 修改了表结构，我想去掉 map对应关系表 写到程序中控制分类，部门对应关系，
     */
    public function actionTicketChange()
    {
        Yii::import("application.models.helpcenter.*");
        $userModel = TicketUser::model()->findAll();

        foreach($userModel as $model)
        {

            $sql = "select * from t_ticket_group_map tm where tm.`user`=:tm_user";
            $tm_params = array('tm_user'=>$model->user);
            $res = Yii::app()->db->createCommand($sql)->queryRow(true,$tm_params);
            //var_dump($model->user);exit;
            if(!empty($res)){
                //接单 部门关系表有对应关系
                $updateSql = "update t_ticket_user set `group`=:group,`cursor_sort`=:cursor_sort where `user`=:user";
                $params = array('group' => $res['group'], 'cursor_sort' => $res['cursor_sort'], 'user' => $model->user);
                $updateRes = Yii::app()->db->createCommand($updateSql)->execute($params);
                echo '修改用户：' .$model->user . " 结果：" . $updateRes ."\n";
            }

        }
    }

	/**
	 *
	 * 用来测试账单回退
	 *
	 */
	public function actionRefund($orderId){
		$order = Order::model()->getOrderById($orderId);
		if(empty($orderId)){
			echo 'empty order '. "\n";
		}
		OrderSettlement::model()->delEmployeeAccount($order);
	}
    
    public function actionIn(){
        $ret=Yii::app()->db_readonly->createCommand()->select('order_id')->from('t_order')->where('order_id in ()')->queryAll();
        var_dump($ret);
    }
}
