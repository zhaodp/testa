<?php

/**
 * This is the model class for table "{{daily_order_report}}".
 *
 * The followings are the available columns in table '{{daily_order_report}}':
 * @property string $id
 * @property string $driver_id
 * @property string $date
 * @property string $year
 * @property string $month
 * @property string $day
 * @property string $city_id
 * @property integer $i_orders
 * @property integer $c_orders
 * @property integer $e_orders
 * @property integer $online
 * @property string $online_data
 * @property integer $mark
 * @property string $created
 */
class DailyOrderReport extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DailyOrderReport the static model class
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
		return '{{daily_order_report}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('driver_id, date, year, month, day, city_id, i_orders, c_orders, e_orders, online, income, online_data, created', 'required'),
			array('i_orders, c_orders, e_orders, online, mark', 'numerical', 'integerOnly'=>true),
			array('driver_id, year, month, day, city_id, created', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, driver_id, date, year, month, day, city_id, i_orders, c_orders, e_orders, online, income, online_data, mark, created', 'safe', 'on'=>'search'),
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
			'driver_id' => 'Driver',
			'date' => 'Date',
			'year' => 'Year',
			'month' => 'Month',
			'day' => 'Day',
			'city_id' => 'City',
			'i_orders' => 'I Orders',
			'c_orders' => 'C Orders',
			'e_orders' => 'E Orders',
			'online' => 'Online',
            'income' => 'Income',
			'online_data' => 'Online Data',
			'mark' => 'Mark',
			'created' => 'Created',
		);
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
		$criteria->compare('driver_id',$this->driver_id);
		$criteria->compare('date',$this->date);
		$criteria->compare('year',$this->year);
		$criteria->compare('month',$this->month);
		$criteria->compare('day',$this->day);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('i_orders',$this->i_orders);
		$criteria->compare('c_orders',$this->c_orders);
		$criteria->compare('e_orders',$this->e_orders);
		$criteria->compare('online',$this->online);
        $criteria->compare('income',$this->income);
		$criteria->compare('online_data',$this->online_data);
		$criteria->compare('mark',$this->mark);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * 通过日期截取该日期的年份、月份、日信息
     * @param $date 如：2012-06-28
     * @return array
     */
    public function getYMDByDate($date) {
        $ts = strtotime($date);
        $d['year'] = date('Y', $ts);
        $d['month'] = date('m', $ts);
        $d['day'] = date('d', $ts);
        return $d;
    }

    /**
     * 向t_driver_order_report表中插入数据，
     * 数据来源;http://db03.edaijia.cn/data/stat_driver_online/2013-06-25.data
     * @param $current_date
     * @param $data
     * @return bool
     */
    public function insertDataToOrder($current_date, $data) {
        $insert_data = $data;
        $insert_data['date'] = $current_date;
        $date = $this->getYMDByDate($current_date);
        $insert_data['year'] = $date['year'];
        $insert_data['month'] = $date['month'];
        $insert_data['day'] = $date['day'];
        $insert_data['online_data'] = json_encode($data['online']);
        $insert_data['online'] = intval($this->checkOnline($data['online']));
        $insert_data['created'] = time();
        $update_model = DailyOrderReport::model()->find('driver_id=:driver_id AND date = :current_date', array(':driver_id'=>$data['driver_id'], ':current_date'=>$current_date));
        if (!$update_model) {
            $model = new DailyOrderReport();
            $model->attributes = $insert_data;
            return $model->save(false);
        }  else {
            $update_model->attributes = $insert_data;
            return $update_model->save(false);
        }

    }

    /**
     * 根据一天内各时间的登录信息判断该司机当天是否登录
     * @param $online_data
     * @return bool
     */
    private function checkOnline($online_data) {
        $is_online = false;
        foreach ($online_data as $v) {
            if ($v) {
                $is_online = true;
                break;
            }
        }
        return $is_online;
    }

    /**
     * 生成COMMAND
     * @return mixed
     */
    protected function getSearchCommand() {
        $command = Yii::app()->dbreport->createCommand();
        $command->select(
            "driver_id, ".
            "city_id, ".
            "SUM(i_orders) AS cancel, ". //销单数
            "SUM(c_orders) AS complete, ". //报单数
            "SUM(e_orders) AS additional, ". //补充
            "SUM(online) AS online, ". //上线天数
            "SUM(income) AS income_total, ". //总收入
            "(e_orders + c_orders) AS accept, ". //接单数
            "SUM(IF(c_orders>0, 1, 0)) AS accept_days" //接单的天数
        );
        $command->from('t_daily_order_report');
        $command->group('driver_id');
        return $command;
    }

    /**
     * 根据时间段和城市获得销单数/报单数/补单数/上线天数/接单数/接单天数
     * @param $date_start 开始时间
     * @param $date_end   结束时间
     * @param $city_id    城市ID
     * @return mixed
     */
    public function buildReportByDateCity($date_start, $date_end, $city_id) {
        $command = $this->getSearchCommand();
        $select_str = $city_id ? "city_id={$city_id} AND " : "";
        $command->where($select_str."date>=:date_start AND date<=:date_end", array(
            ':date_start' => $date_start,
            ':date_end' => $date_end,
        ));
        return $command->queryAll();
    }

    /**
     * 获得某月某城市的数据
     * @param $year
     * @param $month
     * @param $city_id
     * @return mixed
     */
    public function buildReportByMonthCity($year, $month, $city_id=0) {
        $command = $this->getSearchCommand();
        $select_str = $city_id ? "city_id={$city_id} AND " : "";
        $command->where($select_str."year=:year AND month=:month",array(
            ':year' => $year,
            ':month' => $month,
        ));
        return $command->queryAll();
    }

    /**
     * 获得某年某月共有多少天,如果查询的是当前月则返回本月已经过去的天数
     * @param $year
     * @param $month
     * @return int
     */
    public static function get_day($year, $month)
    {
        if( in_array($month , array( 1 , 3 , 5 , 7 , 8 , '01', '03', '05', '07' , '08', 10 , 12)))
        {
            $text = 31;
        }
        elseif( $month == 2 )
        {
            if ( $year%400 == 0  || ($year%4 == 0 && $year%100 !== 0) )        //判断是否是闰年
            {
                $text = 29;
            }
            else{
                $text = 28;
            }
        }
        else{
            $text = 30;
        }
        $current_year = date('Y',time());
        $current_month = intval(date('m', time()));
        if ($current_year==$year && $current_month==intval($month)) {
            $text = date('d', time());
        }
        return $text;
    }

    /**
     * 获得司机姓名
     * @param $driver_id
     * @return array|mixed|null|string
     */
    public static function getDriverName($driver_id) {
        $key = "GET_DRIVER_NAME_".$driver_id;
        //$driver_name = Yii::app()->cache->get($key);
        $driver_name = false;
        if (!$driver_name) {
            $driver = Driver::getProfile($driver_id);
            $driver_name = $driver ? $driver->name : 'null';
            //Yii::app()->cache->set($key, $driver_name);
        }
        return $driver_name;
    }

    /**
     * 获得某城市某天的上线司机数(上线司机数：为当天全部上线司机数)
     * @param $date
     * @param $city_id
     * @return mixed
     */
    public function getDriverOnlineCount($date, $city_id=0) {
        $command = Yii::app()->dbreport->createCommand();
        $command->select('SUM(online)');
        $command->from('t_daily_order_report');
        $search_str = $city_id ? " AND city_id=".$city_id : '';
        $command->where('date=:date'.$search_str, array(':date'=>$date));
        $num = $command->queryScalar();
        return $num ? intval($num) : 0;
    }

    /**
     * 获得接单司机数 (有报单 有销单)
     * @param date  $date
     * @param int   $city_id
     * @return mixed
     */
    public function getDriverAcceptCount($date, $city_id=0) {
        $command = Yii::app()->dbreport->createCommand();
        $command->select('count(*)');
        $command->from('t_daily_order_report');
        $search_str = $city_id ? " AND city_id=".$city_id : '';
        $command->where('date=:date AND (c_orders>0 OR i_orders>0)'.$search_str, array(':date'=>$date));
        $num =  $command->queryScalar();
        return $num ? intval($num) : 0;
    }

    /**
     * 获得未上线司机数 （未上线司机数：为除去“非正常状态的司机”之外未上线的司机数。）
     * @param     $date
     * @param int $city_id
     * @return mixed
     */
    public function getDriverNotOnlineCount($date, $city_id=0) {
        $command = Yii::app()->dbreport->createCommand();
        $command->select('count(*)');
        $command->from('t_daily_order_report');
        $search_str = $city_id ? " AND city_id=".$city_id : '';
        $command->where('date=:date AND (online=0 OR mark=0)'.$search_str, array(':date'=>$date));
        $data = $command->queryScalar();
        return $data ? intval($data) : 0;
    }

    public static function getDateLine($start_date, $end_date) {
        $cdate = strtotime($start_date);
        $now = strtotime($end_date);
        $start = $cdate;
        $cdateArr = array();
        while ($start <= $now) {
            $cdateArr[] = date("Y-m-d", $start);
            $start = $start + 86400;
        }
        return $cdateArr;
    }
}