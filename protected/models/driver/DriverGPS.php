<?php
/**
 * 司机位置的Mongo封装
 * @author dayuer
 * 2013-05-08
 */
class DriverGPS {
	public $host='mongodb://mongo01n.edaijia.cn';
	public $port=27017;
	public $password='k74FkBwb7252FsbNk2M7';
	protected static $_models=array();
	private $_mongo;
	private $_coll;
	private $options=array(
			'safe'=>true,
			'upsert'=>true
	);

	public static function model($className=__CLASS__) {
		$model=null;
		if (isset(self::$_models[$className]))
			$model=self::$_models[$className];
		else {
			$model=self::$_models[$className]=new $className(null);
		}
		return $model;
	}

	public function __construct() {
		$this->_mongo=new Mongo($this->host.':'.$this->port);
		$this->_coll=$this->_mongo->driver->location;
	}

	public function __destruct() {
		if ($this->_mongo) {
			$this->_mongo->close();
		}
	}

	/**
	 * 初始化mongo库
	 */
	public function init() {
		$this->_mongo->dropDB('driver');
		$offset=0;
		$pagesize=100;
		echo "starting...\n";
		while(true) {
			$criteria=new CDbCriteria(array(
					'select'=>'user_id,status,baidu_lng,baidu_lat',
					'offset'=>$offset,
					'limit'=>$pagesize
			));
			$drivers=DriverPosition::model()->findAll($criteria);
			if ($drivers) {
				foreach($drivers as $driver) {
					if ($driver->driver) {
						$this->insert($driver['user_id'], array(
								'lng'=>$driver['baidu_lng'],
								'lat'=>$driver['baidu_lat']
						), $driver['status']);
					}
				}
			} else {
				break;
			}
			$offset+=$pagesize;
			echo $offset."\n";
		}
		
		$this->_coll->ensureIndex(array(
				'location'=>'2d',
				'status'=>1
		));
		
		$this->_coll->ensureIndex(array(
				'driver_id'=>1
		));
		
		$this->_coll->ensureIndex(array(
				'city_id'=>1,
				'status'=>1
		));
	}

	public function reindex() {
		$this->_coll->ensureIndex(array(
				'location'=>'2d',
				'status'=>1
		));
		
		$this->_coll->ensureIndex(array(
				'driver_id'=>1
		));
		
		$this->_coll->ensureIndex(array(
				'city_id'=>1,
				'status'=>1
		));
	}

	/**
	 * mongo插入新司机位置信息
	 * @param int 	$id
	 * @param array $position
	 * @param int 	$status
	 * 
	 * @author dayuer 2013-08-26
	 */
	public function insert($id, $position, $status) {
		$criteria=new CDbCriteria(array(
				'select'=>'id, user, city_id',
				'condition'=>'id=:id',
				'params'=>array(
						':id'=>$id
				)
		));
		$driver=Driver::model()->find($criteria);
		
		if ($driver) {
			$newposition=array(
					'_id'=>intval($id),
					'driver_id'=>$driver->user,
					'city_id'=>intval($driver->city_id),
					'status'=>intval($status),
					'location'=>array(
							'lng'=>doubleval($position['lng']),
							'lat'=>doubleval($position['lat'])
					),
					'update'=>date(Yii::app()->params['formatDateTime'], time())
			);
			
			$rs=$this->_coll->update(array(
					'_id'=>intval($id)
			), $newposition, $this->options);
		}
	}

	/**
	 * 更新司机的坐标信息
	 * @param int $id
	 * @param string $driver_id //稍后去掉
	 * @param array $position
	 * @param int $status
	 * 
	 * @modify 2013-08-29 dayuer
	 */
	public function update($id, $driver_id, $position, $service_type=Driver::SERVICE_TYPE_FOR_DAIJIA, $app_ver='0') {
		$driver=$this->_coll->findOne(array(
				'_id'=>intval($id)
		));
		
		if ($driver && isset($driver['city_id']) && isset($driver['driver_id']) ) {
                        $set = array();
			$set['location']=array(
					'lng'=>doubleval($position['lng']),
					'lat'=>doubleval($position['lat'])
			);
			$set['update']=date(Yii::app()->params['formatDateTime'], time());
			//增加支持业务类型列到mongo
			$set['service_type']=$service_type;
			$set['app_ver']=$app_ver;
			$rs=$this->_coll->update(array(
					'_id'=>intval($id)
			), array('$set' => $set), $this->options);
			echo 'driver_id='.$driver['driver_id'].',service_type='.$service_type.',app_ver='.$app_ver.'更新mongo ok';
			EdjLog::info('driver_id='.$driver['driver_id'].',service_type='.$service_type.',app_ver='.$app_ver.'更新mongo ok');
		} else {
			$this->_coll->remove(array(
					'_id'=>$id
			));
			//增加司机位置信息
			$this->insert($id, $position, 2);
                        EdjLog::info("DriverGPS update position no found|".$driver_id);
		}
	}

	public function get($driver_id) {
		$position=$this->_coll->findOne(array(
				'driver_id'=>$driver_id
		));
		if ( isset($position['status']) )
			return intval($position['status']);
		else
			return -1;
	}
	/**
	 * 更新司机的状态
	 * @param int $id
	 * @param int $status
	 */
	public function status($driver_id, $status) {
		$position=$this->_coll->findOne(array(
				'driver_id'=>$driver_id
		));
		
		if ($position && isset($position['driver_id']) ) {
		    $set = array();
		    $set['status']=intval($status);
		    $set['update']=date(Yii::app()->params['formatDateTime'], time());

		    $this->_coll->update(array(
		    		'driver_id'=>$driver_id
		    ), array('$set' => $set), $this->options);
		}
		else {
                    EdjLog::info("DriverGPS update status no found|".$driver_id);
		}
	}

	private function do_sync($drivers, $indexs=array()) {
		foreach($drivers as $driver) {
			if (isset($driver['_id'])&&is_object($driver['_id'])) {
				$this->_coll->remove(array(
						'_id'=>$driver['_id']
				));
			} else {
				if(isset($driver['driver_id'])){
                                        //区分尾号处理
				        $index = substr($driver['driver_id'], -1);
					if(!empty($indexs) && !in_array($index, $indexs)) {
					    continue;
					}

					$driver_info=DriverStatus::model()->get($driver['driver_id']);
					
					if (($driver_info->mark!=0 && $driver['status'] != 2) || $driver_info->status!=$driver['status']) {
					        EdjLog::info($driver['driver_id'].' change '.$driver['status'].'(mongo) to '.$driver_info->status."(redis)");
						if ($driver_info->mark==0) {
							$driver['status']=intval($driver_info->status);
						} else {
							$driver['status']=intval(2);
						}

						//同步坐标
						if(isset($driver_info->position['latitude'])
						    && isset($driver_info->position['longitude'])) {
						    $driver['location'] = array(
							'lng' => doubleval($driver_info->position['longitude']),
							'lat' => doubleval($driver_info->position['latitude'])
						    );
						}

						$driver['update']=date(Yii::app()->params['formatDateTime'], time());
						$this->_coll->update(array(
								'_id'=>$driver['_id']
						), $driver, $this->options);
						//更新数据库
						DriverPosition::model()->updateStatus($driver['_id'], $driver_info->status);
					}
				}
				
			}
		}
	}

    /**
     *  同步redis和mongo的司机状态
     */
    public function sync($indexs=array()) {
        $drivers = SearchDriverGPS::model()->get_all_drivers();

        if(!empty($indexs)) {
            EdjLog::info(__METHOD__."|sync by index|".join(',', $indexs));
        }
        else {
            EdjLog::info(__METHOD__."|sync all indexs");
        }
        $this->do_sync($drivers, $indexs);
    }

	public function getDriverList($city_id, $timeout=10) {
		$d = date("Y-m-d H:i:s", time()-$timeout*60);
		$params = array(
			'city_id'=>intval($city_id),
			'status'=>intval(0)
		);
		$all = $this->_coll->find($params);
		return $all;
	}

	
	public function getDriverDistance($driver_id, $lng, $lat, $gps_type) {
		$driver = $this->_coll->findOne(array('driver_id'=>$driver_id));
		
		$driver_lng=$driver['location']['lng'];
		$driver_lat=$driver['location']['lat'];
		
		if ($gps_type=="baidu") {
			$_distance=Helper::Distance($lat, $lng, $driver_lat, $driver_lng);
		}
		else {
			$gps=GPS::model()->convert(array(
					'longitude'=>$lng,
					'latitude'=>$lat,
					), $gps_type);
			
			$_distance=Helper::Distance($gps['baidu_lat'], $gps['baidu_lng'], $driver_lat, $driver_lng);
		}
		
		return $_distance;
	}
	
	/**
	 * 查找最近的司机,
	 * 
	 * nearbyAll($lng, $lat)  附近5000米内所有司机
	 * 
	 * @param float $lng
	 * @param float $lat
	 * @param int $stauts  -1
	 * @param int $max_distance 最远距离 5000
	 * @param int $count
	 * @return array
	 */
	public function nearbyAll($lng, $lat, $status=-1, $max_distance=5000, $count=0) {
		//取max_distance的两倍远作为mongod的maxDistance
        $mongo_max_distance=isset(Yii::app()->params['EarthRound'])?$max_distance*2*360.0/Yii::app()->params['EarthRound']:10.0;
        $near_condition=array(
				'location'=>array(
						'$near'=>array(
								doubleval($lng),
								doubleval($lat)
						),
                        '$maxDistance'=>doubleval($mongo_max_distance)
				),
				'status'=>$status
		);
		
		if ($status==-1)
			unset($near_condition['status']);
			
			//取所有
		if ($count==0) {
			$drivers=$this->_coll->find($near_condition);
		} else {
			$drivers=$this->_coll->find($near_condition)->limit($count*2);
		}
		
		$near_drivers=array();
		foreach($drivers as $item) {
			$driver_lng=$item['location']['lng'];
			$driver_lat=$item['location']['lat'];
			$_distance=Helper::Distance($lat, $lng, $driver_lat, $driver_lng);
			if ($_distance<=$max_distance) {
				$near_drivers[]=array(
						'id'=>$item['_id'],
						'driver_id'=>$item['driver_id'],
						'user'=>$item['driver_id'],
						'distance'=>$_distance,
						'status'=>isset($item['status'])? intval($item['status']) : -1
				);
			}
		}
		
		if ($near_drivers) {
			$near_drivers=self::arraySortByKey($near_drivers, 'distance');
			if ($count==0)
				return $near_drivers;
			$near_drivers=array_slice($near_drivers, 0, $count);
		}
		
		return $near_drivers;
	}

  /**
   * 查找最近的司机 按照status_list依次查找 达到count值返回 减少一次mongo连接
   * @param float $lng
   * @param float $lat
   * @param array $status_list 第一个元素为首选状态
   * @param int count
   * @param int $max_distance 最远距离
   * @return array
   */
  public function nearby_multi_status($lng, $lat, $status_list,
    $count=5, $max_distance=5000) {
    $near_drivers = array();
    //取max_distance的两倍远作为mongod的maxDistance
    $mongo_max_distance=isset(Yii::app()->params['EarthRound'])?$max_distance*2*360.0/Yii::app()->params['EarthRound']:10.0; 
    $near_condition=array(
      'location'=>array(
        '$near'=>array(doubleval($lng), doubleval($lat)),
        '$maxDistance'=>$mongo_max_distance
      ),
      'status' => array( '$in' => $status_list ),
    );
    //取一倍的司机数量
    $drivers=$this->_coll->find($near_condition)->limit($count);

    $first_count = 0;
    foreach($drivers as $item) {
      if (!isset($item['status']) || empty($item['driver_id'])) {
        continue;
      }

      $driver_lng=$item['location']['lng'];
      $driver_lat=$item['location']['lat'];
      $_distance=Helper::Distance($lat, $lng, $driver_lat, $driver_lng);
      if ($_distance <= $max_distance) {
        $near_drivers[$item['driver_id']] =array(
          'id'        => $item['_id'],
          'driver_id' => $item['driver_id'],
          'distance'  => $_distance,
          'status'    => intval($item['status']),
        );
        if ($status_list[0] == intval($item['status'])) {
	  $first_count++;
        }
      }
    }

    // 保证获取到至少一首选状态
    if ($first_count == 0) {
      $near_condition=array(
        'location'=>array(
          '$near'=>array(doubleval($lng), doubleval($lat)),
          '$maxDistance'=>$mongo_max_distance
        ),
        'status' => $status_list[0],
      );
      $drivers=$this->_coll->find($near_condition)->limit($count);
      foreach($drivers as $item) {
        if (!isset($item['status']) || empty($item['driver_id'])) {
          continue;
        }
	if (array_key_exists($item['driver_id'], $near_drivers)) {
          continue;
	}

        $driver_lng=$item['location']['lng'];
        $driver_lat=$item['location']['lat'];
        $_distance=Helper::Distance($lat, $lng, $driver_lat, $driver_lng);
        if ($_distance <= $max_distance) {
          $near_drivers[$item['driver_id']] =array(
            'id'        => $item['_id'],
            'driver_id' => $item['driver_id'],
            'distance'  => $_distance,
            'status'    => intval($item['status']),
          );
        }
      }
    }

    if (!empty($near_drivers)) {
      $near_drivers = array_values($near_drivers);
      $sort_func = function($a, $b) {
        if($a['status'] !== $b['status']) {
	  return intval($a['status']) < intval($b['status']) ? -1 : 1;
	}
	return $a['distance'] < $b['distance'] ? -1 : 1;
      };
      usort($near_drivers, $sort_func);
    }

    return $near_drivers;
  }

	/**
	 * 查找最近的司机
	 * @param float $lng
	 * @param float $lat
	 * @param int $stauts
	 * @param int $count
	 * @param int $max_distance 最远距离
	 * @return array
	 */
	public function nearby($lng, $lat, $stauts=0, $count=5, $max_distance=5000, $app_ver=null,$city_id=null) {
        //取max_distance的两倍远作为mongod的maxDistance
		$mongo_max_distance=isset(Yii::app()->params['EarthRound'])?$max_distance*2*360.0/Yii::app()->params['EarthRound']:10.0;
        $near_condition=array(
				'location'=>array(
						'$near'=>array(
								doubleval($lng),
								doubleval($lat)
						),
                        '$maxDistance'=>doubleval($mongo_max_distance)
				),
				'status'=>$stauts
		);
	    if($app_ver != null) {
                    $near_condition['app_ver'] = array("\$gte" => $app_ver);
		}
		
		//取一倍的司机数量
		$drivers=$this->_coll->find($near_condition)->limit($count*2);
		
		$near_drivers=array();
		foreach($drivers as $item) {
			$driver_lng=$item['location']['lng'];
			$driver_lat=$item['location']['lat'];
			$_distance=Helper::Distance($lat, $lng, $driver_lat, $driver_lng);
			if ($_distance<=$max_distance) {
				//modify by sunhongjing 2013-10-13,增加对driver_id的判断，因为有的时候driver_id会错误
				if( !empty($item['driver_id']) ){
					$near_drivers[]=array(
						'id'=>$item['_id'],
						'driver_id'=>$item['driver_id'],
						'distance'=>$_distance,
						'status'=>isset($item['status'])? intval($item['status']) : -1,
                        'lng' => $driver_lng,
                        'lat' => $driver_lat
					);
				}
			}
		}

	
		if ($near_drivers) {
			$near_drivers=self::arraySortByKey($near_drivers, 'distance');
			$near_drivers=array_slice($near_drivers, 0, $count);
		}
		
		return $near_drivers;
	}

	/**
	 * 查找最近的司机,根据服务查找司机,采用正则匹配字符串查询获取
	 * @param float $lng
	 * @param float $lat
	 * @param int $stauts
	 * @param int $count
	 * @param int $max_distance 最远距离
	 * @param string $service_type
	 * @return array
	 */
	public function nearbyService($lng, $lat, $stauts=0, $count=5, $max_distance=5000, $service_type='00000000000000000000000000000001',$app_ver=null) {
        //取max_distance的两倍远作为mongod的maxDistance
		$mongo_max_distance=isset(Yii::app()->params['EarthRound'])?$max_distance*2*360.0/Yii::app()->params['EarthRound']:10.0;
        //正则匹配，0全部替换成星号.
        $service_type = '/'.strtr($service_type,'0','.').'/';
        $service_type = new MongoRegex($service_type);
        $near_condition=array(
				'location'=>array(
						'$near'=>array(
								doubleval($lng),
								doubleval($lat)
						),
                        '$maxDistance'=>doubleval($mongo_max_distance)
				),
				'status'=>$stauts,
				'service_type'=>$service_type
		);
		
		if($app_ver != null) {
            $near_condition['app_ver'] = array("\$gte" => $app_ver);
        }

		//取一倍的司机数量
		$drivers=$this->_coll->find($near_condition)->limit($count*2);
		// echo 'lng='.$lng.',lat='.$lat.'service_type='.$service_type.'查找附近的5公里司机个数:'.count($drivers);
		EdjLog::info('lng='.$lng.',lat='.$lat.'service_type='.$service_type.'查找附近的5公里司机个数:'.count($drivers));
		$near_drivers=array();
		foreach($drivers as $item) {
			$driver_lng=$item['location']['lng'];
			$driver_lat=$item['location']['lat'];
			$_distance=Helper::Distance($lat, $lng, $driver_lat, $driver_lng);
			if ($_distance<=$max_distance) {
				//modify by sunhongjing 2013-10-13,增加对driver_id的判断，因为有的时候driver_id会错误
				if( !empty($item['driver_id']) ){
					$near_drivers[]=array(
						'id'=>$item['_id'],
						'driver_id'=>$item['driver_id'],
						'distance'=>$_distance,
						'status'=>isset($item['status'])? intval($item['status']) : -1,
                        'lng' => $driver_lng,
                        'lat' => $driver_lat
					);
				}
			}
		}
		
		if ($near_drivers) {
			$near_drivers=self::arraySortByKey($near_drivers, 'distance');
			$near_drivers=array_slice($near_drivers, 0, $count);
		}
		
		return $near_drivers;
	}
	
	/**
	 * 查找最近的司机
	 * @param float $lng
	 * @param float $lat
	 * @param int $stauts
	 * @param int $count
	 * @param int $max_distance 最远距离
	 * @return array
	 */
	public function nearby_client($lng, $lat, $stauts=0, $count=5, $max_distance=5000) {
		//取max_distance的两倍远作为mongod的maxDistance
        $mongo_max_distance=isset(Yii::app()->params['EarthRound'])?$max_distance*2*360.0/Yii::app()->params['EarthRound']:10.0;
        $near_condition=array(
				'location'=>array(
						'$near'=>array(
								doubleval($lng),
								doubleval($lat)
						),
                        '$maxDistance'=>doubleval($mongo_max_distance)
				),
				'status'=>$stauts
		);
		//取一倍的司机数量
		$drivers=$this->_coll->find($near_condition)->limit($count);
	
		$near_drivers=array();
		foreach($drivers as $item) {
			$driver_lng=$item['location']['lng'];
			$driver_lat=$item['location']['lat'];
			$_distance=Helper::Distance($lat, $lng, $driver_lat, $driver_lng);
			if ($_distance<=$max_distance) {
				
				//modify by sunhongjing 2013-10-13,增加对driver_id的判断，因为有的时候driver_id会错误
				if( !empty($item['driver_id']) ){
					$near_drivers[]=array(
						'lng'=>$driver_lng,
						'lat'=>$driver_lat,
						'id'=>$item['_id'],
						'driver_id'=>isset($item['driver_id']) ? $item['driver_id'] : '',
						'distance'=>$_distance,
						'status'=>isset($item['status'])? intval($item['status']) : -1
					);
				}
				
			}
		}
	
		if ($near_drivers) {
			$near_drivers=self::arraySortByKey($near_drivers, 'distance');
			$near_drivers=array_slice($near_drivers, 0, $count);
		}
	
		return $near_drivers;
	}
	/**
	 * 查找最近的司机,并打印日志
	 *
	 * @param string $city_id
	 * @param string $order_id
	 * @param float $lng
	 * @param float $lat
	 * @param int $stauts
	 * @param int $count
	 * @param int $max_distance
	 *        	最远距离
	 * @return array
	 */
	public function nearby_printLog($city_id, $order_id, $lng, $lat, $stauts = 0, $count = 5, $max_distance = 5000, $app_ver = null) {
		$near_drivers = array ();
		$near_drivers = self::nearby ( $lng, $lat, $stauts, $count, $max_distance ,$app_ver);
		$logInfo = "DriverGPS:nearby_printLog|".$city_id . "|" . $order_id . ":" . $lat . ":" . $lng;
	
		if ($near_drivers) {
			$drivers= array_slice ( $near_drivers, 0, 5 );
			foreach ( $drivers as $item ) {
				$logInfo = $logInfo . "|" . $item ['driver_id'] . ":" . $item ['status'] . ":" . $item ['lat'] . ":" . $item ['lng'] . ":" . $item ['distance'];
			}
		}
		EdjLog::info ( $logInfo, 'console' );
		return $near_drivers;
	}

	private function arraySortByKey(array $array, $key) {
        $asc=true;
		$result=array();
		// 整理出准备排序的数组
		foreach($array as $k=>&$v) {
			$values[$k]=isset($v[$key]) ? $v[$key] : '';
		}
		unset($v);
		// 对需要排序键值进行排序
		$asc ? asort($values) : arsort($values);
		// 重新排列原有数组
		$i=0;
		foreach($values as $k=>$v) {
			$i++;
			$result[$array[$k]['id']]=$array[$k];
		}
		
		return $result;
	}
}
