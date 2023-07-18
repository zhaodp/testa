<?php

/**
 * This is the model class for table "t_city_province".
 *
 * The followings are the available columns in table 't_city_province':
 * @property integer $id
 * @property string $name
 * @property integer $status
 */
class cityProvince extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 't_city_province';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, status', 'required'),
            array('status', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>32),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, status', 'safe', 'on'=>'search'),
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
            'id' => 'Id',
            'name' => 'Name',
            'status' => 'Status',
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
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);

        $criteria->compare('name',$this->name,true);

        $criteria->compare('status',$this->status);

        return new CActiveDataProvider('cityProvince', array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * @return cityProvince the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public static function getProvince(){
        $criteria = new CDbCriteria();
        $criteria->select = 'id,name';
        $criteria->addCondition( 'status=1');
        $province = CityProvince::model()->findAll($criteria);
        $result = array();
        foreach($province as $p) {
            $result[$p['id']] = $p['name'];
        }
        return $result ;
    }

}