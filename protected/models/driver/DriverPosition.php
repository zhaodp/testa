<?php

/**
 * This is the model class for table "{{driver_position}}".
 *
 * The followings are the available columns in table '{{driver_position}}':
 * @property integer $user_id
 * @property integer $status
 * @property string $longitude
 * @property string $latitude
 * @property string $google_lng
 * @property string $google_lat
 * @property string $baidu_lng
 * @property string $baidu_lat
 * @property string $created
 */
class DriverPosition extends CActiveRecord {
	/**
	 * 空闲状态
	 */
	const POSITION_IDLE = 0;
	/**
	 * 工作状态
	 */
	const POSITION_WORK = 1;
	/**
	 * 下班状态
	 */
	const POSITION_GETOFF = 2;
	/**
	 * 结伴状态
	 */
	const POSITION_TOGETHER = 3;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverPosition the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{driver_position}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array (
			array (
				'user_id, status, longitude, latitude, google_lng, google_lat, baidu_lng, baidu_lat, created', 
				'required'), 
			array (
				'longitude, status, latitude, google_lng, google_lat, baidu_lng, baidu_lat', 
				'length', 
				'max'=>15), 
			array (
				'user_id, status, longitude, latitude, google_lng, google_lat, baidu_lng, baidu_lat', 
				'safe', 
				'on'=>'search'));
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
				'driver'=>array(
						self::BELONGS_TO,
						'Driver',
						'user_id'
				)
		);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'user_id'=>'User ID', 
			'status'=>'Status', 
			'longitude'=>'Longitude', 
			'latitude'=>'Latitude', 
			'google_lng'=>'Google lng', 
			'google_lat'=>'Google Lat', 
			'baidu_lng'=>'Baidu lng', 
			'baidu_lat'=>'Baidu Lat', 
			'created'=>'Created');
	}
	
	public function getDriversPositionByCity($city_id = 1) {
		$sql = 'SELECT d.user, d.id, d.name, d.phone, dp.baidu_lng longitude,dp.baidu_lat latitude,dp.status state
				FROM `t_driver_position` dp
				JOIN t_driver d ON dp.user_id = d.id
				WHERE d.city_id ='. intval($city_id) .'
				AND d.mark =0 AND dp.baidu_lng >0 AND dp.baidu_lat>0
				AND dp.status in(0,1)';
		
		$drivers = Yii::app()->db_readonly->createCommand($sql)->queryAll();
		
		$addPoint = '';
		foreach($drivers as $driver) {
			$query_string = '';
			$driver_phone = $driver['phone'];
			$last_latitude = $driver['latitude'];
			$last_longitude = $driver['longitude'];
			$last_phone = $driver['phone'];
			$last_name = $driver['name'];
			$driver_name = $driver['name'].' '.$driver['user'];
			$addPoint .= sprintf('marker = addDriver(%s, %s, "%s",%s);'."\n", $last_latitude, $last_longitude, $driver_name, $driver['state']);
		}
		return $addPoint;
	}
	
	public function getDriverPositionTrackByTime($user_id = '574', $startDate = '2013-01-01 00:00:00', $endDate = '2013-01-01 00:00:00') {
		$data = array ();
		$timeStartDate = strtotime($startDate);
		$table_ext = date('Ym', $timeStartDate);
		
		$tablename = 't_driver_position_'.$table_ext;
		
		$positions = Yii::app()->dbstat_readonly->createCommand()
				->select('baidu_lng, longitude, baidu_lat, latitude, google_lat, google_lng, state, created')
				->from($tablename)
				->where('baidu_lat > 1 and baidu_lng > 1 and user_id=:user_id and created between :startDate and :endDate', array (
			':user_id'=>$user_id, 
			':startDate'=>$startDate, 
			':endDate'=>$endDate))->order('created ASC')->queryAll();
		
		$addPoint = '';
		$linePoint = '';
		foreach($positions as $position) {
			$latitude = $position['baidu_lat'];
			$longitude = $position['baidu_lng'];
			$state = $position['state'];
			$datetime = $position['created'];
			if ($latitude!=''&&$longitude!='') {
				$addPoint .= sprintf('marker = addDriver(%s, %s, "%s",%s);'."\n", $latitude, $longitude, $datetime, $state);
				$linePoint .= sprintf('new BMap.Point(%s, %s),'."\n", $longitude, $latitude);
			}
		
		//			$datetime = $position['created'];
		//			if ($position['latitude'] != '' && $position['longitude'] != ''){
		//				$addPoint .= sprintf('marker = addDriver(%s, %s, "%s",%s);'."\n", $position['latitude'], $position['longitude'], $datetime, 0);
		//				$linePoint .= sprintf('new BMap.Point(%s, %s),'."\n", $position['longitude'], $position['latitude']);
		//				
		//				$addPoint .= sprintf('marker = addDriver(%s, %s, "%s",%s);'."\n", $position['baidu_lat'], $position['baidu_lng'], $datetime, 1);
		//				$linePoint .= sprintf('new BMap.Point(%s, %s),'."\n", $position['baidu_lat'], $position['baidu_lng']);
		//				
		//				$addPoint .= sprintf('marker = addDriver(%s, %s, "%s",%s);'."\n", $position['google_lat'], $position['google_lng'], $datetime, 2);
		//				$linePoint .= sprintf('new BMap.Point(%s, %s),'."\n", $position['google_lng'], $position['google_lat']);
		//			}
		}
		
		$data['addPoint'] = $addPoint;
		
		$data['linePoint'] = $linePoint;
        
        $data['pointData'] = $positions;
		
		return $data;
	}
	
	public function getAllDriverStatus() {
		$sql = 'SELECT c.name city_name, state, count( * ) total
				FROM `t_employee` e ,`t_city` c
				WHERE e.city_id !=0 and e.city_id = c.id
				GROUP BY city_id, state';
		
		$items = Yii::app()->db_readonly->createCommand($sql)->QueryAll();
		
		return $items;
	}
	
	public function getDriverStatus($user = 'BJ9000', $driver_id = 0) {
		$driverPosition = DriverPosition::model()->getDriverPosition($driver_id);
		
		if ($driverPosition) {
			return $driverPosition->status;
		} else {
			return DriverPosition::POSITION_GETOFF;
		}
	}
	
	public function updateStatus($user_id, $status = 911, $app_ver='') {
		$attributes['user_id'] = $user_id;
		$attributes['created'] = date(Yii::app()->params['formatDateTime'], time());
		
		if ($status!=911)
			$attributes['status'] = $status;

		//在这里更新app_ver
		//heartbeat只更新redis不再写mysql,减少一次调用
		if(!empty($app_ver)) {
                    $attributes['app_ver'] = $app_ver;
		}
		
		$a = self::model()->updateByPk($user_id, $attributes);
                return $a;
	}
	
	public function insertStatDriverTrack($imei, $hash_key, $json_position) {
		$attributes = array (
			'imei'=>$imei, 
			'hash'=>$hash_key, 
			'location'=>$json_position, 
			'created'=>date(Yii::app()->params['formatDateTime'], time()));
		$table_name = 't_driver_tracks_'.date('Ym', time());
		Yii::app()->dbstat->createCommand()->insert($table_name, $attributes);
	}
	
	public function addTower2LBS($latitude, $longitude, $mcc, $mnc, $lac, $ci, $address) {
		if (!Lbs::checkLocation($mcc, $mnc, $lac, $ci)) {
			$attributes = array (
				'mcc'=>$mcc, 
				'mnc'=>$mnc, 
				'lac'=>$lac, 
				'ci'=>$ci, 
				'latitude'=>$latitude, 
				'longitude'=>$longitude, 
				'address'=>$address, 
				'update_time'=>date('Y-m-d H:i:s', time()));
			
			$model = new Lbs();
			$model->attributes = $attributes;
			@$model->insert();
		}
	}
	
	public function getDriverPosition($user_id) {
		//todo 临时切换
		self::$db = Yii::app()->db_readonly;
		$driverPosition = self::model()->find('user_id=:user_id', array (
			':user_id'=>$user_id));
		self::$db = Yii::app()->db;
//		if (!$driverPosition) {
//			$gps = array (
//				'longitude'=>'', 
//				'latitude'=>'');
//			self::updatePosition($user_id, $gps);
//			
//			$driverPosition = self::model()->find('user_id=:user_id', array (
//				':user_id'=>$user_id));
//		}
		return $driverPosition;
	}
	
	/**
	 * 更新司机的坐标位置
	 */
	public function updatePosition($driver_id, $gps, $status = null, $log_time = null) {
		if ($log_time==null){
			$log_time=date('YmdHis');
		}
		
		$driver=DriverStatus::model()->get($driver_id);
		
		$driver->position = $gps;
		
		$attributes = $gps;
		$attributes['user_id']= $driver->id;
		if ($status!==null) {
			$attributes['status']=$status;
			$driver->status = $status;
		}
		
		if ($log_time) {
			$attributes['created']=date(Yii::app()->params['formatDateTime'], strtotime($log_time));
		} else {
			$attributes['created']=date(Yii::app()->params['formatDateTime'], time());
		}
		
		
		//更新司机位置
		$ret = DriverPosition::findByPk($driver->id);
		
		if($ret) {
			self::model()->updateByPk($driver->id, $attributes);
			$driver_info['login'] = array('created'=>$attributes['created']);
		} else {
			$dp=new DriverPosition();
			$dp->attributes=$attributes;
			@$dp->insert();
		}
		
		unset($attributes['status']);
		unset($attributes['street']);

		if ($status!==null) {
			$attributes['state']=$status;
		} else {
			$attributes['state']=Driver::model()->getStatus($driver_id);
		}
		
		//$table_name='t_driver_position_'.date('Ym', time());
		//Yii::app()->dbstat->createCommand()->insert($table_name, $attributes);
	}
	
	public function search() {
		$criteria = new CDbCriteria();
		
		$criteria->compare('user_id', $this->user_id);
		$criteria->compare('status', $this->status);
		$criteria->compare('hash', $this->hash);
		$criteria->compare('longitude', $this->longitude);
		$criteria->compare('latitude', $this->latitude);
		$criteria->compare('google_lng', $this->google_lng);
		$criteria->compare('google_lat', $this->google_lat);
		$criteria->compare('baidu_lng', $this->baidu_lng);
		$criteria->compare('baidu_lat', $this->baidu_lat);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria));
	}
}
