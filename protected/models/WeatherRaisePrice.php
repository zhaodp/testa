 <?php

/**
 * This is the model class for table "{{weather_raise_price}}".
 *
 * The followings are the available columns in table '{{weather_raise_price}}':
 * @property integer $id
 * @property integer $city_id
 * @property integer $add_price
 * @property integer $start_time
 * @property integer $end_time
 * @property integer $status
 * @property integer $app_message
 * @property integer $offer_message
 * @property integer $reason
 * @property integer $operator
 * @property string $create_time
 * @property string $update_time
 */
class WeatherRaisePrice extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{weather_raise_price}}';
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
            array('city_id, add_price, start_time, end_time', 'required'),
            array('city_id, add_price, status', 'numerical', 'integerOnly'=>true),
            array('operator,start_time, end_time', 'length', 'max'=>20),
            array('app_message, offer_message','length','max'=>70),
            array('reason','safe'),
            array('create_time,update_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, operator, city_id, add_price, start_time, end_time, status, create_time, update_time', 'safe', 'on'=>'search'),
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
            'city_id' => 'City',
            'add_price' => 'add_price',
            'start_time' => 'start_time',
            'end_time' => 'end_time',
            'app_message' => 'app_message',
            'offer_message' => 'offer_message',
            'status' => 'Status',
            'reason' => 'reason',
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
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('add_price',$this->add_price);
        $criteria->compare('start_time',$this->start_time);
        $criteria->compare('end_time',$this->end_time);
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
    *   @param $operator操作人 $city_id 城市id  $恶劣天气日期
    *
    */
    public function addRaisePrice($params){
        $model = new self();
        $data= array(
            'operator'=>$params['operator'],
            'city_id'=>$params['city_id'],
            'add_price'=>$params['add_price'],
            'start_time' => $params['start_time'],
            'end_time' => $params['end_time'],
            'app_message' => $params['app_message'],
            'offer_message' => $params['offer_message'],
            'reason' => $params['reason'],
            'status'=>0,
            'create_time'=>date('Y-m-d H:i:s'),
            'update_time'=>$params['update_time'],
            );
        $model->attributes = $data;
        if($model->save()) {
            return $model->id;
        } else {
            return false;
        }
        //return $model->insert(false);
    }

    /**
    * 获取所有恶劣天气加价 
    *
    */
    public function getAllAddPrice(){
        $sql = "SELECT * FROM t_weather_raise_price WHERE 1=1";
        $sql .= " AND status=0 ";
        $command = Yii::app()->dbreport->createCommand($sql);
        $result = $command->queryAll();
        
        return $result;
    }

    /**
    * 获取一条恶劣天气加价 
    *
    */
    public function getCityAddPrice($city_id,$timestamp){
        $sql = "SELECT * FROM t_weather_raise_price WHERE status=0";
        if (!empty($city_id)) {
            $sql .= " AND city_id = :city_id";
        }
        if(!empty($timestamp)){
          $sql .= " AND start_time <= :now AND end_time >= :now ";
        }
        $sql .= " ORDER BY id DESC LIMIT 1";
        $command = Yii::app()->dbreport->createCommand($sql);
        if (!empty($city_id)) {
           $command->bindParam(":city_id" , $city_id);
        }
        if (!empty($timestamp)){
            $command->bindParam(":now", $timestamp);
        }
        $result = $command->queryRow();
        
        return $result;
    }

    /**
    * 提前获取一条恶劣天气加价 
    * 日期传:2015-03-26
    *
    */
    public function preGetCityAddPrice($city_id,$timestamp=""){
        if(empty($timestamp)){
            $timestamp = date('Y-m-d');
        }
        if(strlen($timestamp)>10){
          return false;
        }
        $start_time = $timestamp." 00:00:00";
        $end_time   = $timestamp." 23:59:59";

        $sql = "SELECT * FROM t_weather_raise_price WHERE status=0";
        if (!empty($city_id)) {
            $sql .= " AND city_id = :city_id";
        }
        if(!empty($timestamp)){
          $sql .= " AND start_time >= :start_time AND end_time <= :end_time";
        }
        $sql .= " ORDER BY id DESC LIMIT 1";
        $command = Yii::app()->dbreport->createCommand($sql);
        if (!empty($city_id)) {
           $command->bindParam(":city_id" , $city_id);
        }
        if (!empty($timestamp)){
            $command->bindParam(":start_time", $start_time);
            $command->bindParam(":end_time", $end_time);
        }
        $result = $command->queryAll();
        
        return $result;
    }
}
