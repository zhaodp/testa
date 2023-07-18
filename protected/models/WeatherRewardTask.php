 <?php

/**
 * This is the model class for table "{{weather_reward_task}}".
 *
 * The followings are the available columns in table '{{weather_reward_task}}':
 * @property integer $id
 * @property integer $operator
 * @property string $weather_day
 * @property integer $city_id
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 */
class WeatherRewardTask extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{weather_reward_task}}';
    }

    /**
     * @return CDbConnection database connection
     */
    public function getDbConnection()
    {
        return Yii::app()->dbreport;
    }


    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('update_time', 'required'),
            array('city_id, status', 'numerical', 'integerOnly'=>true),
            array('weather_day,operator', 'length', 'max'=>20),
            array('create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, operator, weather_day, city_id, status, create_time, update_time', 'safe', 'on'=>'search'),
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
            'operator' => 'Operator',
            'weather_day' => 'Weather Day',
            'city_id' => 'City',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
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

        $criteria->compare('id',$this->id);
        $criteria->compare('operator',$this->operator);
        $criteria->compare('weather_day',$this->weather_day,true);
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('status',$this->status);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('update_time',$this->update_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return WeatherRewardTask the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


    /**
    *   @param $operator操作人 $city_id 城市id  $weather_day恶劣天气日期
    *
    */
    public function addTask($operator,$city_id,$weather_day){
        $model = new WeatherRewardTask();
        $data= array(
            'operator'=>$operator,
            'city_id'=>$city_id,
            'weather_day'=>$weather_day,
            'status'=>0,
            'create_time'=>date('Y-m-d H:i:s'),
            );
        $model->attributes = $data;
        return $model->insert(false);
    }

    /**
    *   获取待执行的任务，获取今天之前及未执行的任务
    *
    */
    public function getTask(){
        $today = date("Y-m-d");
        $result = WeatherRewardTask::model()->findAll('status=:status and weather_day<:today', 
            array(':status'=>0,':today'=>$today));
        return $result;
    }

    /**
    *  判断同一天同一个城市，不能重复
    *
    */
    public function findTask($city_id,$weather_day){
        $result = WeatherRewardTask::model()->findAll('city_id=:city_id and weather_day=:weather_day', 
            array(':city_id'=>$city_id,':weather_day'=>$weather_day));
        $res = count($result);
        return $res;
    }
}