<?php
/**
 * @author qiujianping@edaijia-staff.cn 2014-05-07
 *  The model for driver inspire data. The data contains the data
 * for last 30 days and last 7 days. The datas are calcualted by 
 * daiyly job in driverCommand.
 * 
 * This is the model class for table "{{driver_inspire_data}}".
 *
 * The followings are the available columns in 
 * table '{{driver_inspire_data}}':
 *
 * @property integer $id
 * @property string $driver_id
 * @property integer $city_id
 * @property integer $online_time
 * @property integer $mon_online_time
 * @property integer $complete_order_count
 * @property integer $mon_complete_order_count
 * @property float $reject_rate
 * @property float $cancel_rate
 * @property integer $non_praise_count
 * @property integer $accept_time
 * @property float $ready_time
 * @property float $ready_on_time_rate
 * @property int $ready_on_time_count
 * @property integer $mon_accept_time
 * @property float $mon_ready_time
 * @property float $mon_ready_on_time_rate
 * @property int $mon_ready_on_time_count
 * @property int $mon_record_ready_time_count
 * @property int $mon_order_count
 * @property int $lastday_cancel_count
 * @property int $lastday_reject_count
 * @property integer $five_start_count
 * @property integer $four_start_count
 * @property integer $three_start_count
 * @property integer $two_start_count
 * @property integer $one_start_count
 * @property integer $total_comments
 */
class DriverInspireData extends CActiveRecord
{
    // Type for cancel rate
    const TYPE_CANCEL_RATE = 1;

    // Type for reject rate
    const TYPE_REJECT_RATE = 2;
    /** 
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return OrderProcess the static model class
     */
    public static function model($className=__CLASS__)
    {
	return parent::model($className);
    }

    /**
     * @return CDbConnection database connection
     */
    public function getDbConnection()
    {
	return Yii::app()->dbreport;
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
	return '{{driver_inspire_data}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
	// NOTE: you should only define rules for those 
	// attributes that will receive user inputs.
	return array(
		array('driver_id, city_id , online_time, mon_online_time,complete_order_count, mon_complete_order_count, receive_order_count, mon_receive_order_count, reject_rate, cancel_rate, non_praise_count, accept_time, mon_accept_time, ready_time, mon_ready_time, ready_on_time_rate, mon_ready_on_time_rate, ready_on_time_rate, mon_ready_on_time_rate, record_ready_time_count, mon_record_ready_time_count, five_start_count, four_start_count, three_start_count, two_start_count, one_start_count, total_comments, mon_order_count, lastday_reject_count, lastday_cancel_count', 'required'),
		array('city_id, online_time, complete_order, bad_comments, accept_time', 'numerical', 'integerOnly'=>true),
		array('id, driver_id, city_id', 'safe', 'on'=>'search'),
		);
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
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
		'driver_id' => 'Driver_ID',
		'city_id' => '城市',
		'online_time' => '7日在线时长',
		'complete_order_count' => '7日报单数',
		'receive_order_count' => '7日接单数',
		'mon_complete_order_count' => '当月报单数',
		'mon_receive_order_count' => '当月接单数',
		'reject_rate' => '最近一个月拒单率',
		'cancel_rate' => '最近一个月销单率',
		'non_praise_count' => '最近一个月5星以下评论数',
		'accept_time' => '7日平均接单时间',
		'ready_time' => '7日平均就位时间',
		'ready_on_time_rate' => '7日及时就位率',
		'ready_on_time_count' => '7日及时就位数',
		'record_ready_time_count' => '7日有记录就位时间的订单数',
		'mon_accept_time' => '当月平均接单时间',
		'mon_ready_time' => '当月平均就位时间',
		'mon_ready_on_time_rate' => '当月及时就位率',
		'mon_ready_on_time_count' => '当月及时就位数',
		'mon_record_ready_time_count' => '当月有记录就位时间的订单数',
		'five_start_count' => '5星评价数',
		'four_start_count' => '4星评价数',
		'three_start_count' => '3星评价数',
		'two_start_count' => '2星评价数',
		'one_start_count' => '1星评价数',
		'total_comments' => '总评论数',
		'mon_order_count' => '本月接单数',
		'lastday_cancel_count' => '昨日销单数',
		'lastday_reject_count' => '昨日拒单数',
		);
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
	// Warning: Please modify the following code to remove
	// attributes that should not be searched.
		
	$criteria=new CDbCriteria;
	$criteria->compare('id',$this->id,true);
	$criteria->compare('driver_id',$this->driver_id);
	$criteria->compare('city_id',$this->city_id);

	return new CActiveDataProvider($this, array(
		    'criteria'=>$criteria,
		    ));
    }

  /**
   * @author qiujianping@edaijia-staff.cn 2014-05-07
   * 
   * Return the driver inpire data by driver id 
   *
   * @param driver_id The driver_id
   * @return The inspire datas of the driver
   */
  public function getInspireDataByDriverId($driver_id = 'BJ00000') {
    $command = Yii::app()->dbreport->createCommand();
    $command->select('*')
            ->from(DriverInspireData::model()->tableName())
            ->where('driver_id=:driver_id');
    $ret = array();
    $ret = $command->queryRow(true, array(':driver_id' => $driver_id));
    return $ret;
  }

  /**
   * @author qiujianping@edaijia-staff.cn 2014-05-28
   * 
   * Return the driver Reject rate by driver id 
   *
   * @param driver_id The driver_id
   * @return The reject rate and the drivers's ranking 
   */
  public function getRejectRateByDriverId($driver_id = 'BJ00000') {
    $command = Yii::app()->dbreport->createCommand();
    $command->select('reject_rate, city_id')
            ->from(DriverInspireData::model()->tableName())
            ->where('driver_id=:driver_id');
    $ret = array(
	    'reject_rate' => 0,
	    'ranking' => 0,
	    );

    $datas = array();
    $datas = $command->queryRow(true, array(':driver_id' => $driver_id));
    if(!empty($datas)) {
	$reject_rate = $datas['reject_rate'];
	$sql="SELECT count(driver_id) as all_count, sum(if(reject_rate>=:reject_rate,1,0)) as smaller_count from t_driver_inspire_data where city_id=:city_id and driver_id != 'BJ00000'";
	$command = Yii::app()->dbreport->createCommand($sql);
	$command->bindParam(":reject_rate",$reject_rate);
	$command->bindParam(":city_id", $datas['city_id']);

	$datas = $command->queryRow();
	
	$ranking = 0;
	if(!empty($datas) && $datas['all_count'] != 0) {
	    $ranking =  $datas['smaller_count']/$datas['all_count'];
	}
	$ret = array(
		'reject_rate' => $reject_rate,
		'ranking' => $ranking,
		);
    }
    return $ret;
  }

  /**
   * @author qiujianping@edaijia-staff.cn 2014-05-28
   * 
   * Return the driver Cancle rate by driver id 
   *
   * @param driver_id The driver_id
   * @return The cancel rate and the drivers's ranking 
   */
  public function getCancelRateByDriverId($driver_id = 'BJ00000') {
    $command = Yii::app()->dbreport->createCommand();
    $command->select('cancel_rate, city_id, mon_order_count')
            ->from(DriverInspireData::model()->tableName())
            ->where('driver_id=:driver_id');
    $ret = array(
	    'cancel_rate' => 0,
	    'ranking' => 0,
	    );

    $datas = array();
    $datas = $command->queryRow(true, array(':driver_id' => $driver_id));
    if(!empty($datas)) {
	$cancel_rate = $datas['cancel_rate'];
	$ranking = 0;
	if($datas['mon_order_count'] > 10) {
	    $sql="SELECT count(driver_id) as all_count, sum(if(cancel_rate>=:cancel_rate,1,0)) as smaller_count from t_driver_inspire_data where city_id=:city_id and driver_id != 'BJ00000'";
	    $command = Yii::app()->dbreport->createCommand($sql);
	    $command->bindParam(":cancel_rate",$cancel_rate);
	    $command->bindParam(":city_id", $datas['city_id']);

	    $datas = $command->queryRow();
	    if(!empty($datas) && $datas['all_count'] != 0) {
		$ranking =  $datas['smaller_count']/$datas['all_count'];
	    }
	}
	
	$ret = array(
		'cancel_rate' => $cancel_rate,
		'ranking' => $ranking,
		);
    }
    return $ret;
  }

  /**
   * @author qiujianping@edaijia-staff.cn 2014-08-05
   * 
   * Return the driver's last day reject order count
   *
   * @param driver_id The driver_id
   * @return The driver's last day reject order count 
   */
  public function getLastdayRejectCountByDriverId($driver_id = 'BJ00000') {
    $command = Yii::app()->dbreport->createCommand();
    $command->select('city_id, lastday_reject_count')
            ->from(DriverInspireData::model()->tableName())
            ->where('driver_id=:driver_id');

    $datas = $command->queryRow(true, array(':driver_id' => $driver_id));
    $ret = 0;
    if(!empty($datas)) {
	$ret = $datas['lastday_reject_count'];
    }
    return $ret;
  }

  /**
   * @author qiujianping@edaijia-staff.cn 2014-09-19
   * 
   * Return the driver's this month's receive order count
   *
   * @param driver_id The driver_id
   * @return This month's driver receive order count 
   */
  public function getMonReceiveOrderCountByDriverId($driver_id = 'BJ00000') {
    $command = Yii::app()->dbreport->createCommand();
    $command->select('city_id, mon_receive_order__count')
            ->from(DriverInspireData::model()->tableName())
            ->where('driver_id=:driver_id');

    $datas = $command->queryRow(true, array(':driver_id' => $driver_id));
    $ret = 0;
    if(!empty($datas)) {
	$ret = $datas['mon_receive_order_count'];
    }
    return $ret;
  }

  /**
   * @author qiujianping@edaijia-staff.cn 2014-06-17
   * 
   * Return the driverids for rate on the top
   * in the city
   *
   * @param city_id The city_id
   * @param rate The rate to get
   * @param type The type of rate to get. 1 for cancel rate and 2 for reject
   *        rate
   *
   * @return The driver ids 
   */
  private function _getTopRateDriverByCityId($city_id = 0, 
	  $rate = 10, $type = DriverInspireData::TYPE_CANCEL_RATE) {
    $ret = array();

    // Get all count
    // We get all count first for there maybe many datas with cancel_rate == 0
    $command = Yii::app()->dbreport->createCommand();
    if($type == DriverInspireData::TYPE_REJECT_RATE) {
	$command->select('count(*) as all_count')
	    ->from(DriverInspireData::model()->tableName())
	    ->where('city_id=:city_id and driver_id !=:driver_id');
    } else {
	$command->select('count(*) as all_count')
	    ->from(DriverInspireData::model()->tableName())
	    ->where('city_id=:city_id and driver_id !=:driver_id and mon_order_count >10');
    }
    $datas = $command->queryRow(true, array(':city_id' => $city_id,':driver_id' => 'BJ00000'));
    $all_count = 0;
    if(!empty($datas)) {
	$all_count = $datas['all_count'];
    }

    // Get rate datas
    $command = Yii::app()->dbreport->createCommand();
    $rate_type = 'cancel_rate';
    if($type == DriverInspireData::TYPE_REJECT_RATE) {
	$rate_type = 'reject_rate';
	$command->select("{$rate_type}, driver_id, mon_order_count")
            ->from(DriverInspireData::model()->tableName())
            ->where("city_id=:city_id and driver_id !=:driver_id and {$rate_type}>0")
            ->order("$rate_type desc");
    } else {
	// default to be cancel rate
	$rate_type = 'cancel_rate';
	$command->select("{$rate_type}, driver_id, mon_order_count")
            ->from(DriverInspireData::model()->tableName())
            ->where("city_id=:city_id and driver_id !=:driver_id and {$rate_type}>0 and mon_order_count>10")
            ->order("$rate_type desc");
    }

    $datas = $command->queryAll(true, array(':city_id' => $city_id,':driver_id' => 'BJ00000'));

    if($all_count == 0) {
	$all_count = 1;
    }

    $last_rate = 0;
    $i = 0;
    $tmp_count = 0;
    $percentage = 0;
    $tmp_datas =  array();
    $mon_order_counts = array();
    foreach($datas as $data){
	if($data["{$rate_type}"] == $last_rate) {
	    $tmp_datas[] = $data['driver_id'];
	    $mon_order_counts[$data['driver_id']] = $data['mon_order_count'];
	    $tmp_count++;
	    $last_rate = $data["{$rate_type}"];
	    continue;
	}

	// else
	if($tmp_count > 0) {
	    $i = $i + $tmp_count;
	    $percentage = ($i*100)/$all_count;
	    if(($percentage >= $rate && $percentage <= 10)
		    || $percentage < $rate) {
		foreach($tmp_datas as $tmp_data) {
		    // Cancel rate should have receive order more than 10
		    $ret[] = $tmp_data;

		}
		$tmp_datas = array();
		$mon_order_counts = array();
		$tmp_count = 0;
	    }

	    if($percentage > $rate) {
		break;
	    }
	}

	$tmp_datas[] = $data['driver_id'];
	$mon_order_counts[$data['driver_id']] = $data['mon_order_count'];
	$tmp_count++;

	$last_rate = $data["{$rate_type}"];
    }

    // In case of the last part
    if($tmp_count > 0) {
	$i = $i + $tmp_count;
	$percentage = ($i*100)/$all_count;
	if(($percentage >= $rate && $percentage <= 10)
		|| $percentage < $rate) {
	    foreach($tmp_datas as $tmp_data) {
		$ret[] = $tmp_data;
	    }
	    $tmp_datas = array();
	    $tmp_count = 0;
	}
    }

    return $ret;
  }

  /**
   * @author qiujianping@edaijia-staff.cn 2014-06-17
   * 
   * Return the driverids for reject rate on the top
   * in the city
   *
   * @param city_id The city_id
   * @param rate The rate to get
   *
   * @return The driver ids 
   */
  public function getTopRejectDriverByCityId($city_id = 0, $rate = 10) {
      $ret = $this->_getTopRateDriverByCityId($city_id, 
	      $rate, DriverInspireData::TYPE_REJECT_RATE); 
      return $ret;
  }

  /**
   * @author qiujianping@edaijia-staff.cn 2014-06-17
   * 
   * Return the driverids for cancel rate on the top
   * in the city
   *
   * @param city_id The city_id
   * @param rate The rate to get
   *
   * @return The driver ids 
   */
  public function getTopCancelDriverByCityId($city_id = 0, $rate = 10) {
      $ret = $this->_getTopRateDriverByCityId($city_id, 
	      $rate, DriverInspireData::TYPE_CANCEL_RATE); 
      return $ret;
  }

  /**
   * @author qiujianping@edaijia-staff.cn 2014-05-07
   * 
   * Return avg inpire data by city_id 
   *
   * @param city_id The city_id
   * @return The inspire datas of the city
   */
  public function getAvgInspireDataByCityId($city_id = 0) {
    $command = Yii::app()->dbreport->createCommand();
    $command->select('*')
            ->from(DriverInspireData::model()->tableName())
            ->where('driver_id=:driver_id and city_id=:city_id');
    $ret = array();
    $default_driver = "BJ00000";
    $ret = $command->queryRow(true, array(':driver_id' => $default_driver, ':city_id' => $city_id));
    return $ret;
  }

} // End class
