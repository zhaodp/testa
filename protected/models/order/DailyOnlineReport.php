<?php

/**
 * This is the model class for table "{{daily_online_report}}".
 *
 * The followings are the available columns in table '{{daily_online_report}}':
 * @property integer $id
 * @property string $driver_id
 * @property string $date
 * @property integer $one
 * @property integer $two
 * @property integer $three
 * @property integer $four
 * @property integer $five
 * @property integer $six
 * @property integer $seven
 * @property integer $eight
 * @property integer $nine
 * @property integer $ten
 * @property integer $eleven
 * @property integer $twelve
 * @property integer $thirteen
 * @property integer $fourteen
 * @property integer $fifteen
 * @property integer $sixteen
 * @property integer $seventeen
 * @property integer $eighteen
 * @property integer $nineteen
 * @property integer $twenty
 * @property integer $twentyone
 * @property integer $twentytwo
 * @property integer $twentythree
 * @property integer $twentyfour
 * @property string $year
 * @property string $month
 * @property string $day
 * @property string $created
 */
class DailyOnlineReport extends CActiveRecord
{

    public static $time_quantum = array(
        '1' => 'one',
        '2' => 'two',
        '3' => 'three',
        '4' => 'four',
        '5' => 'five',
        '6' => 'six',
        '7' => 'seven',
        '8' => 'eight',
        '9' => 'nine',
        '10' => 'ten',
        '11' => 'eleven',
        '12' => 'twelve',
        '13' => 'thirteen',
        '14' => 'fourteen',
        '15' => 'fifteen',
        '16' => 'sixteen',
        '17' => 'seventeen',
        '18' => 'eighteen',
        '19' => 'nineteen',
        '20' => 'twenty',
        '21' => 'twentyone',
        '22' => 'twentytwo',
        '23' => 'twentythree',
        '24' => 'twentyfour',
    );

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DailyOnlineReport the static model class
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
		return '{{daily_online_report}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('driver_id, date, one, two, three, four, five, six, seven, eight, nine, ten, eleven, twelve, thirteen, fourteen, fifteen, sixteen, seventeen, eighteen, nineteen, twenty, twentyone, twentytwo, twentythree, twentyfour, year, month, day, created', 'required'),
			array('one, two, three, four, five, six, seven, eight, nine, ten, eleven, twelve, thirteen, fourteen, fifteen, sixteen, seventeen, eighteen, nineteen, twenty, twentyone, twentytwo, twentythree, twentyfour', 'numerical', 'integerOnly'=>true),
			array('driver_id, year, month, day, created', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, driver_id, date, one, two, three, four, five, six, seven, eight, nine, ten, eleven, twelve, thirteen, fourteen, fifteen, sixteen, seventeen, eighteen, nineteen, twenty, twentyone, twentytwo, twentythree, twentyfour, year, month, day, created', 'safe', 'on'=>'search'),
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
			'one' => 'One',
			'two' => 'Two',
			'three' => 'Three',
			'four' => 'Four',
			'five' => 'Five',
			'six' => 'Six',
			'seven' => 'Seven',
			'eight' => 'Eight',
			'nine' => 'Nine',
			'ten' => 'Ten',
			'eleven' => 'Eleven',
			'twelve' => 'Twelve',
			'thirteen' => 'Thirteen',
			'fourteen' => 'Fourteen',
			'fifteen' => 'Fifteen',
			'sixteen' => 'Sixteen',
			'seventeen' => 'Seventeen',
			'eighteen' => 'Eighteen',
			'nineteen' => 'Nineteen',
			'twenty' => 'Twenty',
			'twentyone' => 'Twentyone',
			'twentytwo' => 'Twentytwo',
			'twentythree' => 'Twentythree',
			'twentyfour' => 'Twentyfour',
			'year' => 'Year',
			'month' => 'Month',
			'day' => 'Day',
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
		$criteria->compare('one',$this->one);
		$criteria->compare('two',$this->two);
		$criteria->compare('three',$this->three);
		$criteria->compare('four',$this->four);
		$criteria->compare('five',$this->five);
		$criteria->compare('six',$this->six);
		$criteria->compare('seven',$this->seven);
		$criteria->compare('eight',$this->eight);
		$criteria->compare('nine',$this->nine);
		$criteria->compare('ten',$this->ten);
		$criteria->compare('eleven',$this->eleven);
		$criteria->compare('twelve',$this->twelve);
		$criteria->compare('thirteen',$this->thirteen);
		$criteria->compare('fourteen',$this->fourteen);
		$criteria->compare('fifteen',$this->fifteen);
		$criteria->compare('sixteen',$this->sixteen);
		$criteria->compare('seventeen',$this->seventeen);
		$criteria->compare('eighteen',$this->eighteen);
		$criteria->compare('nineteen',$this->nineteen);
		$criteria->compare('twenty',$this->twenty);
		$criteria->compare('twentyone',$this->twentyone);
		$criteria->compare('twentytwo',$this->twentytwo);
		$criteria->compare('twentythree',$this->twentythree);
		$criteria->compare('twentyfour',$this->twentyfour);
		$criteria->compare('year',$this->year);
		$criteria->compare('month',$this->month);
		$criteria->compare('day',$this->day);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * 插入数据
     * @param $current_date
     * @param $driver_id
     * @param $online_data
     * @return bool
     */
    public function insertData($current_date, $driver_id, $online_data) {
        $date = $this->getYMDByDate($current_date);
        if (is_array($online_data) && count($online_data)) {
            $_model = new DailyOnlineReport();
            $time_part = 7;
            foreach($online_data as $key=>$is_online) {
                $time = $time_part+$key;
                $field_name = DailyOnlineReport::$time_quantum[$time];
                if ($time>=24) {
                    $time_part = -17;
                }
                $_model->$field_name = intval($is_online);

            }
            $_model->date = $current_date;
            $_model->driver_id = $driver_id;
            $_model->year = $date['year'];
            $_model->month = $date['month'];
            $_model->day = $date['day'];
            $_model->created = time();
            return $_model->save();
        } else {
            return false;
        }
    }

    public function getYMDByDate($date) {
        $ts = strtotime($date);
        $d['year'] = date('Y', $ts);
        $d['month'] = date('m', $ts);
        $d['day'] = date('d', $ts);
        return $d;
    }

    /**
     * 查询给出的司机列表在时间段内的上线开数
     * @param $driver_list
     * @param $year
     * @param $month
     * @param $time_list
     * @return mixed
     */
    public function getDriverPeakDays($driver_list, $year, $month, $time_list) {
        $str = implode('>0 OR ', $time_list);
        $str .= '>0';
        $driver_str = implode("','", $driver_list);
        $sql = "SELECT count(*) as c , driver_id FROM t_daily_online_report WHERE YEAR = {$year} AND MONTH = {$month} AND (";
        $sql .= $str;
        $sql .= ") and driver_id IN (";
        $sql .= "'".$driver_str."'";
        $sql .= ") GROUP By driver_id";
        $command = Yii::app()->dbreport->createCommand($sql);
        $data = $command->queryAll();
        return $data;
    }

    /**
     * 获得某司机某月的上线情况
     * @param $driver_id
     * @param $year
     * @param $month
     * @return mixed
     */
    public function getDriverOnlineInfo($driver_id, $year, $month) {
        $sql = "SELECT * FROM t_daily_online_report WHERE driver_id='{$driver_id}' AND year={$year} AND month={$month} ORDER BY driver_id";
        $command = Yii::app()->dbreport->createCommand($sql);
        $data = $command->queryAll();
        return $data;
    }
}