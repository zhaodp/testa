<?php

/**
 * This is the model class for table "{{driver_back_together}}".
 *
 * The followings are the available columns in table '{{driver_back_together}}':
 * @property integer $id
 * @property integer $initiator_id
 * @property string $together_id
 * @property string $longitude
 * @property string $latitude
 * @property string $createtime
 */
class DriverBackTogether extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverBackTogether the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{driver_back_together}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('initiator_id', 'required'),
			array('initiator_id', 'numerical', 'integerOnly'=>true),
			array('together_id, longitude, latitude', 'length', 'max'=>100),
			array('createtime', 'length', 'max'=>30),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, initiator_id, together_id, longitude, latitude, createtime', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'initiator_id' => 'Initiator',
			'together_id' => 'Together',
			'longitude' => 'Longitude',
			'latitude' => 'Latitude',
			'createtime' => 'Createtime',
		);
	}
	
	//将返程信息记录到数据库
	public function insertTogetherComment($initiator_id,$together_id,$lng,$lat){
		$model = new DriverBackTogether();
		$data = array();
		$data['initiator_id'] = $initiator_id;
		$data['together_id'] = $together_id;
		$data['createtime'] = time();
		$data['longitude'] = $lng;
		$data['latitude'] = $lat;
		$model->attributes = $data;
		$model->save();
	}
	
	public function findTogether($user_id){
		
		$togethers = null;
		
		$driverPosition = DriverPosition::model()->find('user_id=:user_id', array (':user_id'=>$user_id));
		if ($driverPosition) {
			$togethers = self::searchTogetherByDriverPosition($driverPosition->user_id,$driverPosition->longitude,$driverPosition->latitude);
			if(!empty($togethers)){
				$sendSmsMessage = self::sendSmsForTogether($user_id, $togethers,$driverPosition->longitude,$driverPosition->latitude);
			}		
		}
	}
	
	private function sendSmsForTogether($user_id, $together, $lng, $lat){
		if($user_id!=''&&!empty($together)){
			//发送短信
			$together_id = '';
			$driver = Driver::model()->getProfileById($user_id);
			if ($driver) {
				$driverPosition = DriverPosition::model()->find('user_id=:user_id', array (':user_id'=>$driver->id));
				$content = '周边待返程司机：';
				if ($driverPosition) {
					foreach($together as $item){
						if($item['phone']&&$item['id']){
							$content.=$item['driver_id'].$item['name'].'手机：'.$item['phone'].'距你'.$item['distance'].'。';
							$together_id.=$item['id'].'|';
							//self::updateStatus($item['id'],self::POSITION_IDLE);
						}
					}
					//设置发起人空闲
					//self::updateStatus($driver_id,self::POSITION_IDLE);
					$content.='请与对方联系，如不返程，请调至空闲。';
					if($together_id!=''){
						Sms::SendSMS($driver->phone, $content);
						//echo 'phone:' . $driver->phone . '。 content:' . $content . '<br>';
					}
					
					if($together[0]){
						$content = '周边待返程司机：';
						//第一位
						$content.=$driver->user.$driver->name.'手机：'.$driver->phone.'距你'.self::getPositionInfo(Helper::Distance($together[0]['latitude'], $together[0]['longitude'],$driverPosition->latitude, $driverPosition->longitude)).'。';
						if(!empty($together[1])){
							$content.=$together[1]['name'].$together[1]['driver_id'].'手机：'.$together[1]['phone'].'距你'.self::getPositionInfo(Helper::Distance($together[1]['latitude'], $together[1]['longitude'],$together[0]['latitude'], $together[0]['longitude'])).'。';
						}
						$content.='请与对方联系，如不返程，请调至空闲。';
						Sms::SendSMS($together[0]['phone'], $content);
						//echo 'phone:' . $together[0]['phone'] . '。 content:' . $content . '<br>';
					}
					
					
					if($together[1]){
						$content = '周边待返程司机：';
						//第二位
						$content.=$driver->user.$driver->name.'手机：'.$driver->phone.'距你'.self::getPositionInfo(Helper::Distance($together[0]['latitude'], $together[0]['longitude'],$driverPosition->latitude, $driverPosition->longitude)).'。';
						if(!empty($together[0])){
							$content.=$together[0]['name'].$together[0]['driver_id'].'手机：'.$together[0]['phone'].'距你'.self::getPositionInfo(Helper::Distance($together[1]['latitude'], $together[1]['longitude'],$together[0]['latitude'], $together[0]['longitude'])).'。';
						}
						$content.='请与对方联系，如不返程，请将调至空闲。';
						Sms::SendSMS($together[1]['phone'], $content);
						//echo 'phone:' . $together[1]['phone'] . '。 content:' . $content . '<br>';
					}
					
					
					//insert DB
					if($together_id!=''){
						DriverBackTogether::model()->insertTogetherComment($driver->id, $together_id, $lng, $lat);
					}
				}
				
				return $together_id;
			} else {
				return '';
			}
		}else{
			return '';
		}
	}
	
	private function getPositionInfo($distance){
		if ($distance<=100) {
			$distance = '100米内';
		} elseif ($distance>100&&$distance<=200) {
			$distance = '200米内';
		} elseif ($distance>200&&$distance<=300) {
			$distance = '300米内';
		} elseif ($distance>300&&$distance<=400) {
			$distance = '400米内';
		} elseif ($distance>400&&$distance<=500) {
			$distance = '500米内';
		} elseif ($distance>500&&$distance<=600) {
			$distance = '600米内';
		} elseif ($distance>600&&$distance<=700) {
			$distance = '700米内';
		} elseif ($distance>700&&$distance<=800) {
			$distance = '800米内';
		} elseif ($distance>800&&$distance<=900) {
			$distance = '900米内';
		} elseif ($distance>900&&$distance<=1000) {
			$distance = '1.0公里';
		} else {
			$distance = number_format(intval($distance)/1000, 1).'公里';
		}
		return $distance;
	}
	
	private function searchTogetherByDriverPosition($user_id,$lng,$lat){
		$lng = sprintf('%.6f', $lng);
		$lat = sprintf('%.6f', $lat);
		$longitude = $lng;
		$latitude = $lat;
		$idel_driver = null;
		
		$driver_key = Yii::app()->params['CACHE_ONLINE_DRIVERS'];
		$json = Yii::app()->cache->get($driver_key);
		
		//查询半小时之内的本人所有请求记录，并生成数组
		$min_time = time() - 3600;
		
		$allRequest = Yii::app()->db_readonly->createCommand()
						->select('together_id,createtime')
						->from('t_driver_back_together')
						->where(array('and', 'initiator_id=:initiator_id', 'createtime>:createtime'), array(':initiator_id'=>$user_id, ':createtime'=>$min_time))
						->order('id DESC')
						->queryAll();
		
		
		$str_allids = '';
		$arr_allids = array();
		if($allRequest){
			if (count($allRequest) >= 10) {
				//一小时之内最多请求10次
				return ;
			}
			foreach ($allRequest as $item)
			{
				$str_allids.=$item['together_id'];
			}
		
			//分解字符串为数组
			$arr_allids = array_unique(explode('|', $str_allids));
			array_pop($arr_allids);
		}
		
		if ($json) {
			$online_drivers = json_decode($json, true);
		
			$nearby_driver = self::nearby_driver_together($arr_allids, $user_id, $online_drivers, $latitude, $longitude);
		
			$idel_driver = self::driver_detail_together($nearby_driver);
		}
		if ($idel_driver) {
			$drivers = $idel_driver;
		}else {
			$drivers = null;
		}
		
		if (!$drivers) {
		
			return ;
		
		} else {
			$new_drivers = array ();
		
			//获取司机的小照片路径，计算距离的表示,隐藏身份证和驾驶证最后4位数
			foreach($drivers as $k=>$item) {
				if (isset($item['driver_id'])) {
						
					$distance = intval($item['distance']);
					//5公里以内空闲司机
						
					if ($distance<=100) {
						$distance = '100米内';
					} elseif ($distance>100&&$distance<=200) {
						$distance = '200米内';
					} elseif ($distance>200&&$distance<=300) {
						$distance = '300米内';
					} elseif ($distance>300&&$distance<=400) {
						$distance = '400米内';
					} elseif ($distance>400&&$distance<=500) {
						$distance = '500米内';
					} elseif ($distance>500&&$distance<=600) {
						$distance = '600米内';
					} elseif ($distance>600&&$distance<=700) {
						$distance = '700米内';
					} elseif ($distance>700&&$distance<=800) {
						$distance = '800米内';
					} elseif ($distance>800&&$distance<=900) {
						$distance = '900米内';
					} elseif ($distance>900&&$distance<=1000) {
						$distance = '1.0公里';
					} else {
						$distance = number_format(intval($distance)/1000, 1).'公里';
					}
						
					unset($item['distance']);
					$item['distance'] = $distance;
					$item['picture'] = $item['picture'];
					$new_drivers[] = $item;
				}
			}
		}
		return $new_drivers;
	}
	
	private function nearby_driver_together($arr_allids, $user_id, $drivers, $latitude, $longitude) {
		$idel_driver = array();
		foreach($drivers as $driver) {
			if (isset($driver['baidu_lat'])&&isset($driver['baidu_lng'])&&$user_id!=$driver['id']) {
				if(!in_array($driver['id'], $arr_allids)){
					$_distance = Helper::Distance($latitude, $longitude, $driver['baidu_lat'], $driver['baidu_lng']);
						
					if($driver['status']==DriverPosition::POSITION_TOGETHER){
					//if($driver['status']==DriverPosition::POSITION_IDLE){
						if ($_distance<=5000) {
							//检查是否有同等距离的司机，如果有，距离加1
							while (true) {
								if (array_key_exists($_distance, $idel_driver)) {
									$_distance++;
								}else{
									break;
								}
							}
							
							$idel_driver[$_distance] = array (
									'id'=>$driver['id'],
									'distance'=>$_distance);
						}
					}
				}
			}
		}
		
		return $idel_driver;
	}
	
	
	private function driver_detail_together($drivers, $gps_type = 'google', $max_driver_count = 2) {
		if($max_driver_count ==0){
			return null;
		}
	
		if ($drivers) {
			$drivers = self::arraySortByKey($drivers, 'distance', $max_driver_count);
			$driver_ids = '';
			foreach($drivers as $k=>$v) {
				$driver_ids .= $v['id'].',';
			}
			$driver_ids = trim($driver_ids, ',');
	
			$sql = 'SELECT d.* , dp.status, dp.google_lng, dp.google_lat, dp.baidu_lng, dp.baidu_lat
			FROM `t_driver_position` dp, `t_driver` d
			where d.id = dp.user_id and d.id in ('.$driver_ids.')
			order by field(id,'.$driver_ids.');';
	
			$ret = Yii::app()->db_readonly->createCommand($sql)->queryAll();
			foreach($ret as $item) {
				$id = $item['id'];
				$drivers[$id]['driver_id'] = $item['user'];
				$drivers[$id]['name'] = trim($item['name']);
				$drivers[$id]['picture'] = $item['picture'];
				$drivers[$id]['phone'] = $item['phone'];
				$drivers[$id]['state'] = $item['status'];
				if ($gps_type=='baidu') {
					$drivers[$id]['longitude'] = $item['baidu_lng'];
					$drivers[$id]['latitude'] = $item['baidu_lat'];
				} else {
					$drivers[$id]['longitude'] = $item['google_lng'];
					$drivers[$id]['latitude'] = $item['google_lat'];
				}
			}
		} else {
			$drivers = null;
		}
		return $drivers;
	}
	
	public function arraySortByKey(array $array, $key, $count = 5) {
		$asc = true;
		$result = array ();
		// 整理出准备排序的数组
		foreach($array as $k=>&$v) {
			$values[$k] = isset($v[$key]) ? $v[$key] : '';
		}
		unset($v);
		// 对需要排序键值进行排序
		$asc ? asort($values) : arsort($values);
		// 重新排列原有数组
		$i = 0;
		foreach($values as $k=>$v) {
			$i++;
			$result[$array[$k]['id']] = $array[$k];
			if ($i>=$count) {
				break;
			}
		}
		return $result;
	}
	
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('initiator_id',$this->initiator_id);
		$criteria->compare('together_id',$this->together_id,true);
		$criteria->compare('longitude',$this->longitude,true);
		$criteria->compare('latitude',$this->latitude,true);
		$criteria->compare('createtime',$this->createtime,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
