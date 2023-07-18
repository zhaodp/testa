<?php

/**
 * This is the model class for table "{{daily_order_report}}".
 *
 * The followings are the available columns in table '{{daily_order_report}}':
 * @property string $id
 * @property string $driver_id
 * @property string $record_date
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
class DailyOrderReport extends ReportActiveRecord
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
//	public function getDbConnection()
//	{
//		return Yii::app()->dbreport;
//	}

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
            array('driver_id, record_date, year, month, day, city_id, i_orders, c_orders, e_orders, online, online_data, created', 'required'),
            array('i_orders, c_orders, e_orders, online, income, mark, p_online, p_continuous, c_complain, d_complain, high_opinion, bad_review, p_active', 'numerical', 'integerOnly'=>true),
            array('driver_id, year, month, day, city_id', 'length', 'max'=>10),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, driver_id, record_date, year, month, day, city_id, i_orders, c_orders, e_orders, online, online_data, income, mark, p_online, p_continuous, c_complain, d_complain, high_opinion, bad_review, created, p_active', 'safe', 'on'=>'search'),
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
            'driver_id' => '司机工号',
            'record_date' => '日期',
            'year' => 'Year',
            'month' => 'Month',
            'day' => 'Day',
            'city_id' => '城市',
            'i_orders' => '销单',
            'c_orders' => '报单（包含补单）',
            'e_orders' => '补单数',
            'online' => '是否在线',
            'online_data' => 'Online Data',
            'income' => '收入',
            'mark' => '司机状态',
            'p_online' => '峰值时段是否在线',
            'p_continuous' => '峰值时段是否连续在线',
            'c_complain' => '被投诉数',
            'd_complain' => '投诉数',
            'high_opinion' => '好评数',
            'bad_review' => '差评数',
            'created' => 'Created',
            'p_active' => 'P Active',
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

        $criteria->compare('id',$this->id,true);
        $criteria->compare('driver_id',$this->driver_id,true);
        $criteria->compare('record_date',$this->record_date,true);
        $criteria->compare('year',$this->year,true);
        $criteria->compare('month',$this->month,true);
        $criteria->compare('day',$this->day,true);
        $criteria->compare('city_id',$this->city_id,true);
        $criteria->compare('i_orders',$this->i_orders);
        $criteria->compare('c_orders',$this->c_orders);
        $criteria->compare('e_orders',$this->e_orders);
        $criteria->compare('online',$this->online);
        $criteria->compare('online_data',$this->online_data,true);
        $criteria->compare('income',$this->income);
        $criteria->compare('mark',$this->mark);
        $criteria->compare('p_online',$this->p_online);
        $criteria->compare('p_continuous',$this->p_continuous);
        $criteria->compare('c_complain',$this->c_complain);
        $criteria->compare('d_complain',$this->d_complain);
        $criteria->compare('high_opinion',$this->high_opinion);
        $criteria->compare('bad_review',$this->bad_review);
        $criteria->compare('created',$this->created,true);
        $criteria->compare('p_active',$this->p_active);

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
     * @param $record_date
     * @param $data
     * @return bool
     */
    public function insertDataToOrder($record_date, $data) {
        $insert_data = $data;
        $insert_data['record_date'] = $record_date;
        $date = $this->getYMDByDate($record_date);
        $insert_data['year'] = $date['year'];
        $insert_data['month'] = $date['month'];
        $insert_data['day'] = $date['day'];
        $insert_data['online_data'] = json_encode($data['online']);
        $online_data = $this->checkOnline($data['online']);
        $insert_data['online'] = intval($online_data['online']);
        $insert_data['p_online'] = intval($online_data['p_online']);
        $insert_data['p_continuous'] = intval($online_data['p_continuous']);
        $insert_data['p_active'] = intval($online_data['p_active']);
        $date_start = $record_date.' 00:00:00';
        $date_end = $record_date.' 23:59:59';
        $date_start_ts = strtotime($date_start);
        $date_end_ts = strtotime($date_end);
        $insert_data['c_complain'] = CustomerComplain::model()->count('driver_id=:driver_id and service_time>=:date_start and service_time<:date_end', array(':driver_id'=>$data['driver_id'], ':date_start'=>$date_start, ':date_end'=>$date_end));
        $insert_data['d_complain'] = DriverComplaint::model()->count('driver_user=:driver_user and driver_time>=:date_start_ts and driver_time<:date_end_ts', array(':driver_user'=>$data['driver_id'], ':date_start_ts'=>$date_start_ts, ':date_end_ts'=>$date_end_ts));
        $comment_sms_model = new CommentSms();
        $insert_data['high_opinion'] = $comment_sms_model->getHighOpinionCount($data['driver_id'], $record_date);
        $insert_data['bad_review'] = $comment_sms_model->getBadReview($data['driver_id'], $record_date);
        $insert_data['created'] = date('Y-m-d', time());
        $model = self::model()->find('driver_id=:driver_id and record_date=:record_date', array(':driver_id'=>$insert_data['driver_id'], ':record_date'=>$record_date));
        if (!$model) {
            $model = new DailyOrderReport();
        }
        $model->attributes = $insert_data;
        return $model->save();
        /*
        $sql = 'INSERT INTO t_daily_order_report (driver_id, record_date, year, month, day, city_id, i_orders, c_orders, e_orders, online, online_data, income, mark, p_online, p_continuous, c_complain, d_complain, high_opinion, bad_review, created) VALUES(:driver_id, :record_date, :year, :month, :day, :city_id, :i_orders, :c_orders, :e_orders, :online, :online_data, :income, :mark, :p_online, :p_continuous, :c_complain, :d_complain, :high_opinion, :bad_review, :created)';
        $command = Yii::app()->dbreport->createCommand($sql);

        $command->bindParam(":driver_id", $insert_data['driver_id']);
        $command->bindParam(":record_date", $insert_data['record_date']);
        $command->bindParam(":year", $insert_data['year']);
        $command->bindParam(":month", $insert_data['month']);
        $command->bindParam(":day", $insert_data['day']);
        $command->bindParam(":city_id", $insert_data['city_id']);
        $command->bindParam(":i_orders", $insert_data['i_orders']);
        $command->bindParam(":c_orders", $insert_data['c_orders']);
        $command->bindParam(":e_orders", $insert_data['e_orders']);
        $command->bindParam(":online", $insert_data['online']);
        $command->bindParam(":online_data", $insert_data['online_data']);
        $command->bindParam(":income", $insert_data['income']);
        $command->bindParam(":mark", $insert_data['mark']);
        $command->bindParam(":p_online", $insert_data['p_online']);
        $command->bindParam(":p_continuous", $insert_data['p_continuous']);
        $command->bindParam(":c_complain", $insert_data['c_complain']);
        $command->bindParam(":d_complain", $insert_data['d_complain']);
        $command->bindParam(":high_opinion", $insert_data['high_opinion']);
        $command->bindParam(":bad_review", $insert_data['bad_review']);
        $command->bindParam(":created", date('Y-m-d', time()));
        $result = $command->execute();
        $command->reset();
        return $result;
        */
    }

    /**
     * 根据一天内各时间的登录信息判断该司机当天是否登录
     * @param $online_data
     * @return bool
     */
    public function checkOnline($online_data) {
        $is_online = 0;
        $p_online = 0;
        $p_continuous = 0;
        $p_active = 0;
        foreach ($online_data as $v) {
            if ($v>0) {
                $is_online = 1;
                break;
            }
        }

        if ($online_data[12]>0 && $online_data[13]>0 && $online_data[14]>0 && $online_data[15]>0) {
            $p_continuous = 1;
        }

        if ($online_data[12]>0 || $online_data[13]>0 || $online_data[14]>0 || $online_data[15]>0) {
            $p_online = 1;
        }

        for ($k=12; $k<=14; $k++) {
            $s = $k;
            $e = $k+1;
            if ($online_data[$s] > 0 && $online_data[$e] > 0) {
                $p_active = 1;
                break;
            }
        }

        return array(
            'online'=>$is_online,
            'p_online' => $p_online,
            'p_continuous' => $p_continuous,
            'p_active' => $p_active
        );
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
            "(SUM(i_orders) + SUM(c_orders)) AS accept, ". //接单数()
            "SUM(IF(c_orders>0, 1, 0)) AS accept_days,". //接单的天数
            "SUM(p_online) AS p_online,". //峰值时刻是不是在线
            "SUM(p_continuous) AS p_continuous,". //峰值时刻是否连续上线
            "SUM(c_complain) AS c_complain,". //被投诉数
            "SUM(d_complain) AS d_complain,". //投诉客户数
            "SUM(high_opinion) AS high_opinion,". //好评数
            "SUM(bad_review) AS bad_review,". //差评数
            "SUM(IF(mark=0, 1, 0)) AS normal_days,". //正常天数
            "SUM(p_active) AS p_active" //峰值时段连续两小时上线
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
        $city_id = intval($city_id);
        $select_str = $city_id ? "city_id={$city_id} AND " : "";
        $command->where($select_str."`record_date`>=:date_start AND record_date<=:date_end", array(
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
        $city_id = intval($city_id);
        $command = $this->getSearchCommand();
        $select_str = $city_id ? "city_id={$city_id} AND " : "";
        $command->where($select_str."year=:year AND month=:month",array(
            ':year' => $year,
            ':month' => $month,
        ));
        $data = $command->queryAll();
        return $data;
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
        $search_str = is_numeric($city_id) && $city_id>0 ? " AND city_id=".$city_id : '';
        $command->where("`record_date`=:date".$search_str, array(':date'=>$date));
        $num = $command->queryScalar();
        return $num ? intval($num) : 0;
    }

    /**
     * 获得某城市某天的上线司机数/峰值时段上线数/峰会时段连续上线数/
     *    上线司机数：为当天全部上线司机数(只要当天有上报数据就算上线)
     *    峰值时段上线数 : 19：00-23：00 只要有上报数据就计为1
     *    峰值时段连续上线数 : 19：00-23：00 每个小时都有上报数才计为1
     * @param $date
     * @param $city_id
     * @return array(
     *                  online  上线司机数
     *                  p_online 峰值时段上线数
     *                  p_continuous 峰值时段连续上线数
     *                  p_active 峰会时段内任意两小时连续上线数
     *              )
     */
    public function getCityOnlineData($date, $city_id=0) {
        $command = Yii::app()->dbreport->createCommand();
        $command->select('
            SUM(online) as online,
            SUM(p_online) as p_online,
            SUM(p_continuous) as p_continuous,
            SUM(p_active) as p_active
        ');
        $command->from('t_daily_order_report');
        $search_str = is_numeric($city_id) && $city_id ? " AND city_id=".$city_id : '';
        $command->where("`record_date`=:date".$search_str, array(':date'=>$date));
        $data = $command->queryRow();
        return $data;
    }

    /**
     * 获得某城市某天峰值时段连续上线的司机数
     * @param $date
     * @param int $city_id
     * @return int
     */
    public function getPeakContinuousCount($date, $city_id=0) {
        $command = Yii::app()->dbreport->createCommand();
        $command->select('SUM(p_continuous)');
        $command->from('t_daily_order_report');
        $search_str = is_numeric($city_id) && $city_id>0 ? " AND city_id=".$city_id : '';
        $command->where("`record_date`=:record_date".$search_str, array(':record_date'=>$date));
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
        $search_str = is_numeric($city_id) && $city_id>0 ? " AND city_id=".$city_id : '';
        $command->where('`record_date`=:date AND (c_orders>0 OR i_orders>0)'.$search_str, array(':date'=>$date));
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
        $search_str = is_numeric($city_id) && $city_id>0 ? " AND city_id=".$city_id : '';
        $command->where('`record_date`=:date AND (online=0 AND mark=0)'.$search_str, array(':date'=>$date));
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

    /**
     * 获得司机总的上线天数、峰值时段上线天数、峰值时段连续上线天数
     * @param $driver_id
     * @return mixed
     */
    public function getOnlineDaysInfo($driver_id) {
        $command = Yii::app()->dbreport->createCommand();
        $command->select('SUM(online) AS online, SUM(p_online) AS p_online, SUM(p_continuous) AS p_continuous');
        $command->from('t_daily_order_report');
        $command->where('driver_id=:driver_id', array(':driver_id'=>$driver_id));
        $data = $command->queryRow();
        return $data;
    }

    public function getDriverNormalDays($driver_id) {
        $command = Yii::app()->dbreport->createCommand();
        $command->select('COUNT(*)');
        $command->from('t_daily_order_report');
        $command->where('mark=:mark and driver_id=:driver_id', array(':mark'=>0, ':driver_id'=>$driver_id));
        $data = $command->queryScalar();
        return intval($data);
    }

    /**
     * 计算指定时间段内的自然周起始及终止日期
     */
    public static function getWeekByDate($start, $end) {
        $w = date('w', strtotime($start));
        if ($w == 0) {
            $start_ts = strtotime($start);
        } else {
            $surplus = 7-$w;
            $start_ts = strtotime($start)+$surplus*86400;
        }
        $end_ts = strtotime($end);
        $week_arr = array();
        while($start_ts < $end_ts) {
            $tmp['0'] = date('Ymd', $start_ts);
            $tmp['1'] = date('Ymd', $start_ts+6*86400);
            if (strtotime($tmp['1']) > $end_ts) {
                break;
            }
            $week_arr[] = $tmp;
            $start_ts = $start_ts+7*86400;
        }
        return $week_arr;
    }

    public static function getLastMonthFirstDay($date, $month_num) {
        //当月第一天
        $firstday = date("Y-m-01",strtotime($date));
        for($i=0; $i<$month_num; $i++) {
            $time=strtotime($firstday);
            $firstday=date('Y-m-01',strtotime(date('Y',$time).'-'.(date('m',$time)-1).'-01'));
        }
        return $firstday;
    }

    public function getDriverWeekInfo($driver_id) {
        $current_date = date('Y-m-d', time());
        $start = self::getLastMonthFirstDay($current_date, 3);
        $date_line = self::getWeekByDate($start, $current_date);
        krsort($date_line);
        $data = array();
        if (is_array($date_line) && count($date_line)) {
            foreach($date_line as $v) {
                $_key = $v[0]. '～' . $v[1];
                $_date_start = $v[0];
                $_date_end = $v[1];
                $_tmp_data = $this->getDriverDataByDate($driver_id, $_date_start, $_date_end);
                $_tmp_data['record_date'] = $_key;
                $data[] = $_tmp_data;
            }
        }
        return $data;
    }

    /**
     * 某司机在时段内的订单、消单、在线天数、投诉、被投诉数
     * @param $driver_id
     * @param $date_start
     * @param $date_end
     * @return array
     */
    public function getDriverDataByDate($driver_id, $date_start, $date_end) {
        $command = Yii::app()->dbreport->createCommand();
        $command->select('
            SUM(i_orders) AS i_orders,
            SUM(c_orders) AS c_orders,
            SUM(e_orders) AS e_orders,
            SUM(online) AS online,
            SUM(p_online) AS p_online,
            SUM(p_continuous) AS p_continuous,
            SUM(c_complain) AS c_complain,
            SUM(d_complain) AS d_complain,
            SUM(high_opinion) AS high_opinion,
            SUM(bad_review) AS bad_review
        ');
        $command->from('t_daily_order_report');
        $command->where('driver_id=:driver_id AND record_date>=:date_start AND record_date<=:date_end', array(':driver_id'=>$driver_id, ':date_start'=>$date_start, ':date_end'=>$date_end));
        $data = $command->queryRow();
        $new_data = array();
        if (is_array($data) && count($data)) {
            foreach($data as $k=>$v) {
                $new_data[$k] = intval($v);
            }
        }
        return $new_data;
    }
}