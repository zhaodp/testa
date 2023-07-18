 <?php

/**
 * This is the model class for table "{{month_deduct_city}}".
 *
 * The followings are the available columns in table '{{month_deduct_city}}':
 * @property integer $id
 * @property integer $city_id
 * @property string $operator
 * @property string $update_time
 */
class MonthDeductCity extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{month_deduct_city}}';
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
            array('city_id', 'numerical', 'integerOnly'=>true),
            array('operator', 'length', 'max'=>20),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, city_id, operator, update_time', 'safe', 'on'=>'search'),
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
            'operator' => 'Operator',
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
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('operator',$this->operator,true);
        $criteria->compare('update_time',$this->update_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return MonthDeductCity the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
    *   获取所有配置的城市id数组
    *
    */
    public function getAllSettingCitys(){
        $allSettingCitys = $this->findAll();
        $cityArray=array();
        foreach ($allSettingCitys as $citys) {
            array_push($cityArray, $citys['city_id']);
        }
        return $cityArray;
    }

    /**
    *   先删除所有城市配置，再添加
    *
    */
    public function addScoreCitys($citys){
        $this->deleteAll();
        //添加
        foreach ($citys as $city) {
            $model = new MonthDeductCity();
            $model->city_id = $city;
            $model->operator = Yii::app()->user->id;
            $model->save(false);
        }
    }
}