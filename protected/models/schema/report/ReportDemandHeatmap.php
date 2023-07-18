<?php
/**
 * Created by PhpStorm.
 * User: aiguoxin
 * Date: 14-05-19
 * Time: 下午4:30
 */
class ReportDemandHeatmap extends ReportActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return DailyAccountReport the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return CDbConnection database connection
     */
    public function getDbConnection()
    {
        return Yii::app()->dbstat_readonly;
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{report_demand_heatmap}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('weekdaytime, lng, lat, score, idle_driver, point', 'required'),
            array('weekdaytime, lng, lat, score, idle_driver, point', 'safe', 'on' => 'search'),
        );
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
            'weekdaytime' => '日期参数：周+时间',
            'lng' => '经度',
            'lat' => '纬度',
            'score' => '历史订单比值',
            'idle_driver' => '空闲司机数',
            'point' => 'score/空闲司机数',
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

        $criteria = new CDbCriteria;

        $criteria->weekdaytime('weekdaytime', $this->weekdaytime);
        $criteria->compare('lng', $this->lng);
        $criteria->compare('lat', $this->lat);
        $criteria->compare('score', $this->score);
        $criteria->compare('idle_driver', $this->idle_driver);
        $criteria->compare('point', $this->point);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /*
    *   add by aiguoxin
    *   get block point
    *
    */
    public function getBlockPoint($left_lng,$left_lat,$right_lng,$right_lat){
        //compute time 
        $week = date('w');
        $hour = date('H');
        $minute = date('i');
        if($minute < 15){
            $minute = "00";
        }elseif ($minute >=15 && $minute < 45) {
            $minute = 30;
        }elseif ($minute >= 45) {
            $minute = "00";
            if($hour == 23){
                $hour = "00";
                if ($week == 7) {
                    $week = 1;
                } else {
                    $week++;
                }
            }else{
                $hour++;
            }
        }
        if($hour<10){
            $hour="0".$hour;
        }
        $weekdaytime=$week.$hour.$minute;
        $max_lng = $left_lng > $right_lng ? $left_lng : $right_lng;
        $min_lng = $left_lng < $right_lng ? $left_lng : $right_lng;
        $max_lat = $left_lat > $right_lat ? $left_lat : $right_lat;
        $min_lat = $left_lat < $right_lat ? $left_lat : $right_lat;


        //test
        // $weekdaytime=22100;

        $pointList = Yii::app()->dbstat_readonly->createCommand()
            ->select("lng,lat,point as count")
            ->from('t_report_demand_heatmap')
            ->where('weekdaytime=:weekdaytime and (lng between :min_lng and :max_lng) and (lat between :min_lat and :max_lat)', array(
                ':max_lng' => $max_lng, ':max_lat' => $max_lat,':min_lng'=>$min_lng,'min_lat'=>$min_lat,':weekdaytime'=>$weekdaytime))
            ->queryAll();
        
        return  $pointList;
    }
}