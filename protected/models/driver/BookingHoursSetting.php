<?php

/**
 * This is the model class for table "{{booking_hours_setting}}".
 *
 * The followings are the available columns in table '{{booking_hours_setting}}':
 * @property integer $city_id
 * @property string $hour_1_start
 * @property string $hour_2_start
 * @property string $hour_3_start
 * @property string $hour_1_end
 * @property string $hour_2_end
 * @property string $hour_3_end
 */
class BookingHoursSetting extends CActiveRecord
{
    CONST HOUR_1_START_COLUMN = 'hour_1_start';
    CONST HOUR_2_START_COLUMN = 'hour_2_start';
    CONST HOUR_3_START_COLUMN = 'hour_3_start';
    CONST HOUR_1_END_COLUMN = 'hour_1_end';
    CONST HOUR_2_END_COLUMN = 'hour_2_end';
    CONST HOUR_3_END_COLUMN = 'hour_3_end';

    public static $columns = array(
        'start1' => self::HOUR_1_START_COLUMN,
        'end1' => self::HOUR_1_END_COLUMN,
        'start2' => self::HOUR_2_START_COLUMN,
        'end2' => self::HOUR_2_END_COLUMN,
        'start3' => self::HOUR_3_START_COLUMN,
        'end3' => self::HOUR_3_END_COLUMN,
    );

    public static $default_hours = array(
        self::HOUR_1_START_COLUMN => '10:00',
        self::HOUR_1_END_COLUMN => '12:00',
        self::HOUR_2_START_COLUMN => '14:00',
        self::HOUR_2_END_COLUMN => '16:00',
        self::HOUR_3_START_COLUMN => '16:01',
        self::HOUR_3_END_COLUMN => '18:00',
    );

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{booking_hours_setting}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_id', 'required'),
            array('city_id', 'numerical', 'integerOnly'=>true),
            array('hour_1_start, hour_2_start, hour_3_start, hour_1_end, hour_2_end, hour_3_end', 'length', 'max'=>11),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('city_id, hour_1_start, hour_2_start, hour_3_start, hour_1_end, hour_2_end, hour_3_end', 'safe', 'on'=>'search'),
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
            'city_id' => 'City',
            'hour_1_start' => '时段1开始时间',
            'hour_2_start' => '时段2开始时间',
            'hour_3_start' => '时段3开始时间',
            'hour_1_end' => '时段1结束时间',
            'hour_2_end' => '时段2结束时间',
            'hour_3_end' => '时段3结束时间',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('hour_1_start',$this->hour_1_start,true);
        $criteria->compare('hour_2_start',$this->hour_2_start,true);
        $criteria->compare('hour_3_start',$this->hour_3_start,true);
        $criteria->compare('hour_1_end',$this->hour_1_end,true);
        $criteria->compare('hour_2_end',$this->hour_2_end,true);
        $criteria->compare('hour_3_end',$this->hour_3_end,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return BookingHoursSetting the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function setHours($city_id,
        $hour_1_start = NULL,
        $hour_2_start = NULL,
        $hour_3_start = NULL,
        $hour_1_end = NULL,
        $hour_2_end = NULL,
        $hour_3_end = NULL){
            $model = self::model()->findByPk($city_id);
            if(empty($model)){
                $model = new BookingHoursSetting;
                $model->city_id = $city_id;
            }
            if(!is_null($hour_1_start)){
                $model->hour_1_start = $hour_1_start;
            }
            if(!is_null($hour_2_start)){
                $model->hour_2_start = $hour_2_start;
            }
            if(!is_null($hour_3_start)){
                $model->hour_3_start = $hour_3_start;
            }
            if(!is_null($hour_1_end)){
                $model->hour_1_end = $hour_1_end;
            }
            if(!is_null($hour_2_end)){
                $model->hour_2_end = $hour_2_end;
            }
            if(!is_null($hour_3_end)){
                $model->hour_3_end = $hour_3_end;
            }
            $result = $model->save();
            if(FALSE == $result){
                return $result;
            }else{
                return $model;
            }
        }
    /**
     * 取出城市所有时间段的配置
     */
    public function getHourStartEndByCity($city_id){
        $model = BookingHoursSetting::model()->findByPk($city_id);
        if(empty($model)){
            return self::$default_hours;
        }else{
            $hours = $model->attributes;
            unset($hours['city_id']);
            foreach($hours as $k => &$v){
                if(empty($v)){
                    $v = self::$default_hours[$k];
                }
            }
            return $hours;
        }
    }

    /**
     * 根据城市和时间段取起止时间
     */
    public function getHourStartEnd($city_id, $hour){
        $model = BookingHoursSetting::model()->findByPk($city_id);
        $start_col = "hour_{$hour}_start";
        $end_col = "hour_{$hour}_end";
        if(empty($model)){
            return array(
                self::$default_hours[$start_col],
                self::$default_hours[$end_col],
                );
        }
        $start = $model->$start_col;
        $end = $model->$end_col;
        return array($start, $end);
    }

    /**
     * 根据城市和时间段，返回 10:30 ~ 12:00
     */
    public function getHoursDesc($city_id, $hour){
        $hours_setting = BookingHoursSetting::model()->getHourStartEndByCity($city_id);
        $start_col = "hour_{$hour}_start";
        $end_col = "hour_{$hour}_end";
        return $hours_setting[$start_col] . ' ~ ' . $hours_setting[$end_col];
    }

    /**
     * 如果没有设定时间点，返回默认值
     */
    public function __get($name){
        if(in_array($name, self::$columns)){
            $value = parent::__get($name);
            if(empty($value)){
                return self::$default_hours[$name];
            }else{
                return $value;
            }
        }
        return parent::__get($name);
    }
}
