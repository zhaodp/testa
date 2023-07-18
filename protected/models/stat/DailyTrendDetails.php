<?php

/**
 * This is the model class for table "{{daily_trend_details}}".
 * 订单趋势明细
 * The followings are the available columns in table '{{daily_trend_details}}':
 * @property integer $id
 * @property integer $city_id
 * @property integer $order_count
 * @property integer $complete_order
 * @property integer $cancel_order
 * @property integer $callcenter_order_count
 * @property integer $app_order_count
 * @property integer $mobile_order_count
 * @property integer $tel_order_count
 * @property integer $new_user_order
 * @property integer $old_user_order
 * @property integer $have_order_driver
 * @property integer $online_driver
 * @property integer $online_no_order
 * @property integer $on_service_driver
 * @property integer $service_driver
 * @property integer $idle_drivers
 * @property string $progress_time
 * @property string $create_time
 */
class DailyTrendDetails extends ReportActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return DailyTrendDetails the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return CDbConnection database connection
     */
//    public function getDbConnection()
//    {
//        return Yii::app()->dbreport;
//    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{daily_trend_details}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_id, progress_time, create_time', 'required'),
            array('city_id, order_count, complete_order, cancel_order, callcenter_order_count, app_order_count, mobile_order_count, tel_order_count, new_user_order, old_user_order, have_order_driver, online_driver, online_no_order, on_service_driver, service_driver, idle_drivers', 'numerical', 'integerOnly'=>true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, city_id, order_count, complete_order, cancel_order, callcenter_order_count, app_order_count, mobile_order_count, tel_order_count, new_user_order, old_user_order, have_order_driver, online_driver, online_no_order, on_service_driver, service_driver, idle_drivers, progress_time, create_time', 'safe', 'on'=>'search'),
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
            'city_id' => 'City',
            'order_count' => 'Order Count',
            'complete_order' => 'Complete Order',
            'cancel_order' => 'cancel Order',
            'callcenter_order_count' => 'Callcenter Order Count',
            'app_order_count' => 'App Order Count',
            'mobile_order_count' => 'Mobile Order Count',
            'tel_order_count' => 'Tel Order Count',
            'new_user_order' => 'New User Order',
            'old_user_order' => 'Old User Order',
            'have_order_driver' => 'Have Order Driver',
            'online_driver' => 'Online Driver',
            'online_no_order' => 'Online No Order',
            'on_service_driver' => 'On Service Driver',
            'service_driver' => 'Service Driver',
            'idle_drivers' => 'Idle Drivers',
            'progress_time' => 'Progress Time',
            'create_time' => 'Create Time',
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
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('order_count',$this->order_count);
        $criteria->compare('complete_order',$this->complete_order);
        $criteria->compare('cancel_order',$this->cancel_order);
        $criteria->compare('callcenter_order_count',$this->callcenter_order_count);
        $criteria->compare('app_order_count',$this->app_order_count);
        $criteria->compare('mobile_order_count',$this->mobile_order_count);
        $criteria->compare('tel_order_count',$this->tel_order_count);
        $criteria->compare('new_user_order',$this->new_user_order);
        $criteria->compare('old_user_order',$this->old_user_order);
        $criteria->compare('have_order_driver',$this->have_order_driver);
        $criteria->compare('online_driver',$this->online_driver);
        $criteria->compare('online_no_order',$this->online_no_order);
        $criteria->compare('on_service_driver',$this->on_service_driver);
        $criteria->compare('service_driver',$this->service_driver);
        $criteria->compare('idle_drivers',$this->idle_drivers);
        $criteria->compare('progress_time',$this->progress_time,true);
        $criteria->compare('create_time',$this->create_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }


    /**
     * 添加单个城市10分钟统计明细
     * @param $data
     * @param $process_time
     * @author bidong 2013-8-14
     */
    public function addTrendDetail($data,$process_time){

        if(!empty($data)){
            foreach($data as $k=>$v){

                $model=new DailyTrendDetails();

                $model->city_id=$k;
                $model->order_count=$v['total_order'];
                $model->complete_order=$v['complete_order'];
                $model->cancel_order=$v['cancel_order'];
                $model->callcenter_order_count=$v['callcenter_order'];
                $model->app_order_count=$v['app_order'];
                $model->mobile_order_count=$v['mobile_order'];
                $model->tel_order_count=$v['tel_order'];
                $model->new_user_order= $v['new_user_order'];
                $model->old_user_order=$v['old_user_order'];

                $model->have_order_driver=$v['have_order_driver'];
                $model->online_driver=$v['online_driver'];
                $model->online_no_order=$v['online_no_order'];
                $model->on_service_driver=$v['on_service_driver'];
                $model->service_driver=$v['service_driver'];
                $model->idle_drivers=$v['idle_drivers'];
                $model->progress_time=date('Y-m-d H:i:s',$process_time);
                $model->create_time=date('Y-m-d H:i:s');

                $result= $model->insert();
                if($result){

                  $count_date=$this->formatDate($process_time);
                  DailyTrendCollect::model()->addTrendCollect($model,$count_date);
                }
            }
        }

    }

    /**
     * 返回统计日期
     * @param Timestamp $dateTime
     * @return bool|string
     */
    public  function formatDate($dateTime){
       //判断时间是否7点前后
        $count_date='';
        $hour = date("H",$dateTime);
        $hour=intval($hour);
        if($hour>=7){
            $count_date=date("Y-m-d",$dateTime);
        }
        if($hour<7){
            $count_date=date("Y-m-d",$dateTime-86400);
        }

        return $count_date;
    }
} 