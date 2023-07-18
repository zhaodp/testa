<?php
/**
 * This is the model class for table "{{booking_exam_setting}}".
 *
 * The followings are the available columns in table '{{driver_recruitment}}':
 * @property integer $id
 * @property integer $city_id
 * @property integer $date
 * @property integer $hours_1
 * @property integer $hours_2
 * @property integer $hours_3
 */
class BookingExamSetting extends CActiveRecord {


    CONST HOURS_1 = 1;
    CONST HOURS_2 = 2;
    CONST HOURS_3 = 3;

    //三个参数示例：
    //  1. 北京
    //  2. 3月22日10:00
    //  3. 浙江丽水市莲都区兴业大夏213室
    //CONST SMS = "师傅你好，你预约了e代驾%s分公司%s的路考，请你携带以下证件及物品准时参加路考：身份证、驾驶证、开通网银的银行卡、安卓智能手机，非本市户口还需提供暂住证或居住证。路考地点为%s";
    CONST SMS = "师傅您好，您预约了e代驾%s分公司%s的路考。%s";
    CONST CHANGE_HOUR_SMS_NOTIFY = "师傅您好，您预约的e代驾%s分公司的路考时间变更为%s%s。%s"; //北京，4月24日，12:10，司管配置内容

    public static $hours_desc = array(
        self::HOURS_1 => '10:00 - 12:00',
        self::HOURS_2 => '14:00 - 16:00',
        self::HOURS_3 => '16:00 - 18:00',
        );
    public static $hours_name = array(
        self::HOURS_1 => 'hours_1',
        self::HOURS_2 => 'hours_2',
        self::HOURS_3 => 'hours_3',
        );
    public static $hours_used = array(
        self::HOURS_1 => 'used_hours_1',
        self::HOURS_2 => 'used_hours_2',
        self::HOURS_3 => 'used_hours_3',
        );


	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{booking_exam_setting}}';
	}

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 取出某天的报名数据
     * @param int $day 20150101
     * @param int $city_id
     */
    public function getDayData($day, $city_id){
        $sql = "SELECT * FROM t_booking_exam_setting WHERE city_id = :city_id AND `date` = :day;";
        return self::model()->findBySql($sql, array(':city_id' => $city_id, ':day' => $day));
    }


    /**
     * 取出某个月的报名状态
     * @param $month 例子：201501
     */
    public function getMonthData($month, $city_id){
        $sql = "SELECT * FROM t_booking_exam_setting WHERE city_id = :city_id AND `date` LIKE :month;";
        $command = Yii::app()->db_readonly->createCommand($sql);
        $tmp = $month . '%';
        $command->bindParam(':month', $tmp);
        $command->bindParam(':city_id', $city_id);
        $result = $command->queryAll();
        if(empty($result)){
            $result = $this->initMonthData($month, $city_id);
        }
        return $result;
    }

    /**
     * 设定某一天的可报名数
     * @param int $day
     * @param int id_card
     * @param array $hours 格式： array(1 => 30, ...). 时段（1/2/3）=> 数量
     */
    public function setDayData($city_id, $day, Array $hours){
        $day_data = BookingExamSetting::model()->getDayData($day, $city_id);

        //该日期没有初始化
        if(empty($day_data)){
            return array(__LINE__, '该日期没有初始化');
        }

        //不可以设置过去的日期
        if($day_data < date('Ymd')){
            return array(__LINE__, '不能设置过去的时间');
        }
        foreach($hours as $hour => $num){
            //非法时段
            if(!isset(self::$hours_name[$hour])){
                return array(__LINE__, '非法时段');
            }
            //非法数据
            if(!is_numeric($num) || $num < 0){
                return array(__LINE__, '非法数据');
            }
            $column_name = self::$hours_name[$hour];
            $column_used_name = self::$hours_used[$hour];
            //设定的值比已经报名人数小，是否处理
            if($num < $day_data->$column_used_name){
                return array(__LINE__, '设定值比已经报名的人数小');
            }
            $day_data->$column_name = $num;
        }
        $day_data->save(FALSE);
        return $day_data;
    }


    /**
     * 初始化某月某城市数据。只能初始化当月和之后二个月的数据
     * @param int $month 格式：201501
     * @param int $city_id
     */
    public function initMonthData($month, $city_id){
        $first_day = $month . '01'; //拼成201501的格式

        $data = array();
        //month 不是月份格式
        if(FALSE == strtotime($first_day) || -1 == strtotime($first_day)){
            return __LINE__;
        }
        $last_day = date('Ymt', strtotime($first_day));
        $allowed_first_day = date('Ym01');
        $allowed_last_day = date('Ym01', strtotime('+4 months'));
        //时间超出可设定范围
        if($first_day < $allowed_first_day || $first_day > $allowed_last_day){
            return __LINE__;
        }

        for($day = $first_day; $day <= $last_day; $day++){
            $result = $this->initDayData($day, $city_id);
            $data[] = $result->attributes;
        }
        return $data;
    }

    public function initDayData($date, $city_id, $hours_1 = 0, $hours_2 = 0, $hours_3 = 0){
        $model = new BookingExamSetting();
        $model->date = $date;
        $model->city_id = $city_id;
        $model->hours_1 = $hours_1;
        $model->hours_2 = $hours_2;
        $model->hours_3 = $hours_3;
        try{
            $model->save();
        }catch(Exception $e){
            return FALSE;
        }
        return $model;
    }

    //取出某时间段的开始结束时间
    public function getHourStartEnd($city_id, $hour){
        $model = BookingHoursSetting::model()->findByPk($city_id);
        list($start, $end) = explode('-', self::$hours_desc[$hour]);
        return array(trim($start), trim($end));
    }

    /*
     * 取出明天开始所有的有效预约数
     */
    public function getNumOfUsedAfterNextDay($city_id){
        $after = date("Ymd");
        $sql = "SELECT SUM(used_hours_1) h1, SUM(used_hours_2) h2, SUM(used_hours_3) h3 FROM t_booking_exam_setting WHERE date > {$after} AND city_id = {$city_id}";
        return Yii::app()->db_readonly->createCommand($sql)->queryRow();
    }

}
