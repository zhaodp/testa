 <?php

/**
 * This is the model class for table "{{driver_city_version}}".
 *
 * The followings are the available columns in table '{{driver_city_version}}':
 * @property integer $id
 * @property integer $city_id
 * @property string $city_name
 * @property integer $version_id
 * @property string $version_name
 * @property string $create_time
 * @property string $update_time
 */
class DriverCityVersion extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_city_version}}';
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
            array('city_id, version_id', 'numerical', 'integerOnly'=>true),
            array('city_name,operator', 'length', 'max'=>50),
            array('create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, city_id, city_name, operator,version_id, create_time, update_time', 'safe', 'on'=>'search'),
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
            'city_name' => 'City Name',
            'version_id' => 'Version',
            'operator'=>'operator',
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
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('city_name',$this->city_name,true);
        $criteria->compare('version_id',$this->version_id);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('update_time',$this->update_time,true);
        $criteria->compare('operator',$this->operator,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DriverCityVersion the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
    *  获取城市版本信息列表
    *
    */
    public function getList($city_name){
        $sql = "SELECT * FROM t_driver_city_version WHERE 1=1";
        if (!empty($city_name)) {
            $sql .= " AND city_name = :city_name";
        }
        $sql .= " ORDER BY id DESC";
        $command = Yii::app()->dbreport->createCommand($sql);
        if (!empty($city_name)) {
            $command->bindParam(":city_name" , $city_name);
        }
        $result = $command->queryAll();
        $dataProvider = new CArrayDataProvider($result, array (
            'id'=>'versionlist',
            'keyField'=>'city_name',
            'pagination'=>array (
                'pageSize'=>50)
            )
        );
        return $dataProvider;
    }

    /**
    *   获取所有配置城市ID
    *
    */
    public function getAllCityIds(){
        $sql = "SELECT * FROM t_driver_city_version";
        $command = Yii::app()->dbreport->createCommand($sql);
        $result = $command->queryAll();
        $citys = array();
        foreach ($result as $city_version) {
            array_push($citys, $city_version['city_id']);
        }
        return $citys;
    }

    public function getVesionByCity($city_id){
        $connection = Yii::app()->dbreport;
        $sql = "SELECT * FROM t_driver_city_version WHERE city_id='{$city_id}' order by update_time desc limit 1";
        $command = $connection->createCommand($sql);
        return $command->queryRow();
    }

    public function versionBeUsed($version_id){
        $connection = Yii::app()->dbreport;
        $sql = "SELECT count(*) FROM t_driver_city_version WHERE version_id='{$version_id}'";
        $command = $connection->createCommand($sql);
        return $command->queryScalar() > 0;
    }

}