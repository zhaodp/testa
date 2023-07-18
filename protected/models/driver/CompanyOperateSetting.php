<?php

/**
 * This is the model class for table "{{company_operate_setting}}".
 *
 * The followings are the available columns in table '{{company_operate_setting}}':
 * @property integer $id
 * @property integer $city_id
 * @property integer $type_id
 * @property integer $use_date
 * @property integer $basic_score
 * @property integer $grade
 * @property string $created
 */
class CompanyOperateSetting extends CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CompanyOperateSetting the static model class
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
		return '{{company_operate_setting}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, city_id, type_id, use_date, basic_score, grade', 'numerical', 'integerOnly'=>true),
			array('created', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, city_id, type_id, use_date, basic_score, grade, created', 'safe', 'on'=>'search'),
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
			'type_id' => 'Type',
			'use_date' => 'Use Date',
			'basic_score' => 'Basic Score',
			'grade' => 'Grade',
			'created' => 'Created',
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
		$criteria->compare('type_id',$this->type_id);
		$criteria->compare('use_date',$this->use_date);
		$criteria->compare('basic_score',$this->basic_score);
		$criteria->compare('grade',$this->grade);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function insertData($data) {
        $data['use_date'] = isset($data['use_date']) ? $data['use_date'] : date('Ym', time());
        $model = $model = self::model()->find('city_id=:city_id and use_date=:use_date and type_id=:type_id', array(':city_id'=>$data['city_id'], ':use_date'=>$data['use_date'], ':type_id'=>$data['type_id']));
        if (!$model) {
            $model = new CompanyOperateSetting();
        }
        $data['created'] = date('Y-m-d H:i:s', time());
        $model->attributes = $data;
        $result = $model->save();
        return $result;
    }

    public function afterSave(){
        $key = CompanyKpiCommon::getMemKey($this->city_id, $this->use_date, $this->type_id);
        Yii::app()->cache->set($key, $this->attributes);
        return parent::afterSave();
    }

    public function getSettingInfoByType($city_id, $use_date, $type_id) {
        $key = CompanyKpiCommon::getMemKey($city_id, $use_date, $type_id);
        $data = Yii::app()->cache->get($key);
        if (!$data) {
            $model = self::model()->find('city_id=:city_id and use_date=:use_date and type_id=:type_id', array(':city_id'=>$city_id, ':use_date'=>$use_date, ':type_id'=>$type_id));
            if ($model) {
                $data = $model->attributes;
                Yii::app()->cache->set($key, $data);
            }
        }
        return $data;
    }

    public function getSettingInfo($city_id, $use_date) {
        $type_id_list = CompanyKpiCommon::$operate_list;
        $data = array();
        foreach($type_id_list as $type_id=>$name) {
            $_tmp = $this->getSettingInfoByType($city_id, $use_date, $type_id);
            $_tmp['name'] = $name;
            $data[$type_id] = $_tmp;
        }
        return $data;
    }
}