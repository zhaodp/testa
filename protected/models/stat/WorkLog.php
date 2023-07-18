<?php

/**
 * This is the model class for table "{{work_list}}".
 *
 * The followings are the available columns in table '{{work_list}}':
 * @property integer $id
 * @property integer $wid
 * @property string $name
 * @property string $phone
 * @property string $call_time
 * @property string $booking_time
 * @property string $reach_time
 * @property integer $reach_distance
 * @property string $start_time
 * @property string $end_time
 * @property string $start_location
 * @property string $end_location
 * @property integer $distance
 * @property integer $charge
 * @property integer $tip
 * @property string $car_type
 * @property string $car_stative
 * @property string $insert_time
 * @property integer $type
 * @property string $employee_id
 * @property string $user
 * @property string $car_number
 * @property string $vip_id
 */
class WorkLog extends ReportActiveRecord
{
	//工作日开始的小时
	public $start_hour = 7;
	
	public $begin_date;
	public $end_date;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return WorkList the static model class
	 */
	public static function model($className = __CLASS__)
	{
		self::$db = Yii::app()->dbreport;
		$result= parent::model($className);
		return $result;
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{work_log}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array();
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>'ID', 
			'city_id'=>'城市', 
			'state'=>'状态', 
			'total'=>'数量', 
			'snap_time'=>'记录时间');
	}
	
	/**
	 * 获取司机在线人数
	 *
	 * @param int $city_id
	 * @return array $work_log
	 * @author 
	 * @editor AndyCong<congming@edaijia.cn> 2013-05-20
	 *         时间段数组有问题，当某一时间段离线司机不存在，则数据图形往左偏移
	 */
	public function getCityWorklog($city_id)
	{
		$cache_key = 'CityWorklog_'.$city_id.md5($this->begin_date.$this->end_date);
		$ret = Yii::app()->cache->get($cache_key);
		
		if ($ret)
		{
			return json_decode($ret, true);
		}
		
		$freeDriver = $busyDriver = $offlineDriver = $totalDriver = "";
		$timeLine = "";
		$lastTime = '';
		$timeLineArray = array();
		$freeDriverArray = array();
		$busyDriverArray = array();
		$offlineDriverArray = array();
		$currentTime = '';
		
		$dataProvider = $this->search($city_id)->getData();
		
		foreach($dataProvider as $key=>$data)
		{
			switch ($data->attributes['state'])
			{
				case 0 :
					$freeDriverArray[] = $data->attributes['total'];
					break;
				case 1 :
					$busyDriverArray[] = $data->attributes['total'];
					break;
				case 2 :
					$offlineDriverArray[] = $data->attributes['total'];
					break;
			}
			
			//时间段数组重组 modify by AndyCong 2013-05-20
			if ($currentTime!=$data->attributes['snap_time'])
			{
				$timeLineArray[] = $data->attributes['snap_time'];
				$currentTime = $data->attributes['snap_time'];
			}
			//时间段数组重组 modify by AndyCong 2013-05-20 END
			
		}
		
		$total_driver = array();
		foreach($freeDriverArray as $k=>$v)
		{
			$busy = isset($busyDriverArray[$k]) ? (int) $busyDriverArray[$k] : 0;
			$free = isset($freeDriverArray[$k]) ? (int) $freeDriverArray[$k] : 0;
			$total_driver[$k] = $busy+$free;
		}
		//print_r($total_driver);
		//die();
		

		$i = 0;
		foreach($timeLineArray as $datetime)
		{
			if (array_key_exists($i, $total_driver))
			{
				$total_driver[$i] += 1;
				$totalDriver .= $total_driver[$i].",";
			} else
			{
				$totalDriver .= "1,";
			}
			
			if (array_key_exists($i, $freeDriverArray))
			{
				$freeDriverArray[$i] += 1;
				$freeDriver .= $freeDriverArray[$i].",";
			} else
			{
				$freeDriver .= "1,";
			}
			
			if (array_key_exists($i, $busyDriverArray))
			{
				$busyDriverArray[$i] += 1;
				$busyDriver .= $busyDriverArray[$i].",";
			} else
			{
				$busyDriver .= "1,";
			}
			
			if (array_key_exists($i, $offlineDriverArray))
			{
				$offlineDriverArray[$i] += 1;
				$offlineDriver .= $offlineDriverArray[$i].",";
			} else
			{
				$offlineDriver .= "1,";
			}
			
			if ($lastTime=='')
			{
				$lastTime = $datetime;
				$timeLine .= date('H', $lastTime);
			} else
			{
				if ($datetime>=$lastTime+3600)
				{
					$timeLine .= date('H', $datetime).",";
					$lastTime = $datetime;
				} else
				{
					$timeLine .= ',';
				}
			}
			$i++;
		}
		
		$timeLine = "'".str_replace(",", "','", $timeLine)."'";
		
		$work_log = array(
			'totalDriver'=>$totalDriver, 
			'freeDriver'=>$freeDriver, 
			'busyDriver'=>$busyDriver, 
			'offlineDriver'=>$offlineDriver, 
			'timeLine'=>$timeLine, 
			'lastTime'=>$lastTime, 
			'currentTime'=>$currentTime = '');
		Yii::app()->cache->set($cache_key, json_encode($work_log), 60);
		
		return $work_log;
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($city_id)
	{
		/*
		$sql = <<<SQLSTRING
		SELECT 
			state, 
			GROUP_CONCAT(total ORDER BY snap_time), 
			GROUP_CONCAT(FROM_UNIXTIME(snap_time, '%m-%d %H:%i') ORDER BY snap_time) 
		FROM 
			t_work_log 
		WHERE 
			city_id = 1 
			AND FROM_UNIXTIME(snap_time, '%Y-%m-%d %H:%i') BETWEEN '2012-04-25 09:00' AND '2012-04-26 09:00' 
			AND FROM_UNIXTIME(snap_time, '%i') in (5, 0)
		GROUP BY
			state
		*/
		if (!$this->begin_date&&!$this->end_date)
		{
			$this->begin_date = date('Y-m-d 0'.$this->start_hour.':00:00', time()-$this->start_hour*3600);
			$this->end_date = date(Yii::app()->params['formatDateTime'], time());
		}
		
		$criteria = new CDbCriteria();
		
		$criteria->compare('city_id', $city_id);
		$criteria->select = "state, total, snap_time ";
		$criteria->addCondition("minute IN (5, 0)");
		$criteria->addBetweenCondition("snap_time", strtotime($this->begin_date), strtotime($this->end_date));
		$criteria->limit = 1200;
		$criteria->order = "snap_time";
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria, 
			'pagination'=>array(
				'pageSize'=>1200)));
	
	}

    /**
     * 获取某城市某天服务中司机上线数的峰值和时间
     * @param $date
     * @param $city_id
     * @return array|bool|mixed|null
     */
    public function getBusyPeakSnapTime($date, $city_id) {
        $criteria = new CDbCriteria();
        if ($city_id) {
		    $criteria->compare('city_id', $city_id);
        }
		$criteria->select = "snap_time";
		$criteria->addCondition("minute IN (5, 0)");
        $criteria->addCondition("state = 1");
		$criteria->addBetweenCondition("snap_time", strtotime($date), intval(strtotime($date)+86400));
        $criteria->order = 'total desc';
        $criteria->limit = 1;
        $model = self::model()->find($criteria);
        if ($model) {
            return $model->snap_time;
        } else {
            return false;
        }
    }

    /**
     * 获得某该服务中、空闲、不在线的数量
     * @param $snap_time
     * @param $city_id
     * @return array|bool
     */
    public function getPeakStatTotal($snap_time, $city_id) {
        $criteria = new CDbCriteria();
        if ($city_id) {
		    $criteria->compare('city_id', $city_id);
        }
		$criteria->addCondition("snap_time=".$snap_time);
        $criteria->addCondition("state IN (0,1,2)");
        $model = self::model()->findAll($criteria);
        if ($model) {
            $data = array();
            foreach($model as $v) {
                $_tmp['snap_time'] = $v['snap_time'];
                $_tmp['date'] = date('Y-m-d', $_tmp['snap_time']);
                $_tmp['total'] = $v['total'];
                $_tmp['state'] = $v['state'];
                $_tmp['city_id'] = $v['city_id'];
                $data[] = $_tmp;
            }
            return $data;
        } else {
            return false;
        }
    }

    public function getPeakByDate($date, $city_id) {
        $snap_time = $this->getBusyPeakSnapTime($date, $city_id);
        if ($snap_time) {
            return $this->getPeakStatTotal($snap_time, $city_id);
        } else {
            return false;
        }
    }

    public function getPeakChartsData($date, $city_id) {
        $data = $this->getPeakByDate($date, $city_id);
        $_tmp = array();
        if (is_array($data) && count($data)) {
            $busy = 0;
            foreach($data as $v) {
                if ($v['state'] == 0) {
                    $_tmp['free'] = intval($v['total']);
                    $busy = $busy+$v['total'];
                } elseif ($v['state'] == 1) {
                    //$_tmp['busy'] = intval($v['total']);
                    $busy = $busy+$v['total'];
                }
                $_tmp['busy'] = $busy;
            }
            return $_tmp;
        } else {
            return array(
                'free' => 0,
                'busy' => 0,
            );
        }

    }
}
