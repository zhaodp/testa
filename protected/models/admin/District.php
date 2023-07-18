<?php

/**
 * This is the model class for table "{{district}}".
 *
 * The followings are the available columns in table '{{district}}':
 * @property integer $id
 * @property integer $city_id
 * @property string $name
 */
class District extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return District the static model class
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
		return '{{district}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('city_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, city_id, name', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
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
		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public static function getDistrictsList($city_id){
        $key = 'DISTRICTS_LIST_'.$city_id;
        $districts = Yii::app()->cache->get($key);
        if (!$districts) {
            $districts = District::model()->findAll('city_id=:city_id', array(':city_id' => $city_id));
            $districts = CHtml::listData($districts,'id','name');
            Yii::app()->cache->set($key, $districts);
        }
        return $districts;
    }

    public static function getDistrictsName($city_id, $district_id) {
        $list = self::getDistrictsList($city_id);
        return isset($list[$district_id]) ? $list[$district_id] : null;
    }
}