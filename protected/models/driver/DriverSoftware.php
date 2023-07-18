 <?php

/**
 * This is the model class for table "{{driver_software}}".
 *
 * The followings are the available columns in table '{{driver_software}}':
 * @property integer $id
 * @property string $driver_id
 * @property string $package
 * @property string $software_name
 * @property string $create_time
 * @property string $update_time
 */
class DriverSoftware extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_software}}';
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
            array('driver_id', 'length', 'max'=>10),
            array('package, software_name', 'length', 'max'=>255),
            array('create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, driver_id, package, software_name, create_time, update_time', 'safe', 'on'=>'search'),
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
            'package' => 'Package',
            'software_name' => 'Software Name',
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
        $criteria->compare('driver_id',$this->driver_id,true);
        $criteria->compare('package',$this->package,true);
        $criteria->compare('software_name',$this->software_name,true);
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
     * @return DriverSoftware the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


    /**
    *   add driver software
    *
    */
    public function addDriverSoftware($driver_id,$name,$package){
        $driverSoftware = new DriverSoftware();
        $attr = array(
            'driver_id'=>$driver_id,
            'software_name'=>$name,
            'package'=>$package,
            'create_time'=>date('Y-m-d H:i:s'),
        );
        $driverSoftware->attributes = $attr;
        $res = $driverSoftware->insert();
        return $res;
    }
}