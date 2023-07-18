<?php

/**
 * This is the model class for table "{{driver_interview_info}}".
 *
 * The followings are the available columns in table '{{driver_interview_info}}':
 * @property string $id
 * @property string $interview_date
 * @property integer $interview_time
 * @property string $serial_number
 * @property integer $city_id
 */
class DriverInterviewInfo extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverInterviewInfo the static model class
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
		return '{{driver_interview_info}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('interview_date, interview_time, serial_number, city_id, created', 'required'),
			array('interview_time, city_id', 'numerical', 'integerOnly'=>true),
			array('serial_number', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, interview_date, interview_time, serial_number, city_id, created', 'safe', 'on'=>'search'),
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
			'interview_date' => 'Interview Date',
			'interview_time' => 'Interview Time',
			'serial_number' => 'Serial Number',
			'city_id' => 'City',
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
		$criteria->compare('interview_date',$this->interview_date,true);
		$criteria->compare('interview_time',$this->interview_time);
		$criteria->compare('serial_number',$this->serial_number,true);
		$criteria->compare('city_id',$this->city_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * 以下为自己封装方法
     */

    public function insertData($interview_date, $interview_time, $serial_number, $city_id) {
        $model = new DriverInterviewInfo();
        $model->interview_date = $interview_date;
        $model->interview_time = $interview_time;
        $model->serial_number = $serial_number;
        $model->city_id = $city_id;
        $model->created = date('Y-m-d H:i:s', time());
        return $model->save();
    }

    /**
     * 查看某城市某天某时段内预约面试的总人数
     * @param $city_id
     * @param $date
     * @param $time
     */
    public function getNumberOfPeople($city_id, $date, $time=null) {
        $command = Yii::app()->db_readonly->createCommand();
        $str = '';
        if (!is_null($time)) {
            $str =  ' and interview_time='.$time;
        }
        $data = $command->select('count(*)')->from('t_driver_interview_info')
                        ->where('city_id=:city_id and interview_date=:date'.$str, array(':city_id'=>$city_id,':date'=>$date))
                        ->queryScalar();
        return intval($data);
    }

    public function getNumberOfPeopleByTimeList($city_id, $date, $time_list) {
        $command = Yii::app()->db_readonly->createCommand();
        $time_info = false;
        $data = $command->select('interview_time, count(*) as c')->from('t_driver_interview_info')
            ->where("city_id=:city_id and interview_date=:date ", array(':city_id'=>$city_id,':date'=>$date))
            ->group('interview_time')
            ->queryAll();
        if (is_array($data) && count($data)) {
            foreach ($data as $v) {
                $time_info[$v['interview_time']] = $v['c'];
            }
        }
        foreach($time_list as $t) {
            if (!isset($time_info[$t])) {
                $time_info[$t] = 0;
            }
        }
        return $time_info;
    }

    public function getBookingInfoBySerialNumber($id) {
        $command = Yii::app()->db_readonly->createCommand();
        $data = $command->select('id, interview_date, interview_time')->from('t_driver_interview_info')
            ->where("serial_number=:id", array(':id'=>$id))
            ->order('id desc')
            ->queryAll();
        return $data;
    }

    public function getLastBookingBySerialNumber($id) {
        $data = $this->getBookingInfoBySerialNumber($id);
        if ($data) {
            return $data[0];
        } else {
            return false;
        }
    }

    /***
     * 获得某天预约面试的报名流水号列表
     * @param $city_id
     * @param $date
     * @return array|bool
     */
    public function getInterviewByDate($city_id, $date) {
        $command = Yii::app()->db_readonly->createCommand();
        $data = $command->select('*')->from('t_driver_interview_info')
            ->where("city_id=:city_id and interview_date=:date", array(':city_id'=>$city_id, ':date'=>$date))
            ->order('id desc')
            ->queryAll();
        if (is_array($data) && count($data)) {
            $driver_list = array();
            foreach ($data as $v) {
                $driver_list[] = $v['serial_number'];
            }
            return $driver_list;
        } else {
            return false;
        }
    }
}