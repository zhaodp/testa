<?php

/**
 * This is the model class for table "third_dict".
 *
 * The followings are the available columns in table 'third_dict':
 * @property integer $id
 * @property string $dictName
 * @property string $key
 * @property string $value
 */
class ThirdDict extends ThirdStageActiveRecord
{
    /** 应用版本号模式 */
    const DICT_NAME_ACCESS_MODEL = 'access_model';
    /** from 值对应的商户名称 */
    const DICT_NAME_FROM_NAME    = 'from_name';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'third_dict';
	}

    public function createInstance($dictName, $key, $value){
        $model = new ThirdDict();
        $model->dictName = $dictName;
        $model->key      = $key;
        $model->value    = $value;
        if(!$model->save()){
            EdjLog::info('save third_dict error ---- '.json_encode($model->getErrors()));
        }
    }

    /**
     * 回去一个值对应的 key
     *
     * @param $dictName
     * @param $value
     * @return int|mixed
     */
    public function getKey($dictName, $value){
        $criteria = new CDbCriteria();
        $criteria->compare('dictName', $dictName);
        $criteria->compare('value', $value);
        $model = self::model()->find($criteria);
        return isset($model['key']) ? $model['key'] : 0;
    }

    /**
     * 加载字典
     *
     * @param $dictName
     * @param string $key
     * @param string $value
     * @return array|CActiveRecord|mixed|null
     */
    public function getDict($dictName, $key = '', $value = ''){
        $criteria = new CDbCriteria();
        $criteria->compare('dictName', $dictName);
        if(!empty($key)){
            $criteria->compare('key', $key);
        }
        if(!empty($value)){
            $criteria->compare('value', $value);
        }
        $list = self::model()->findAll($criteria);
        $ret = array();
        if($list){
            foreach($list as $item){
                $ret[$item['key']] = $item['value'];
            }
        }
        return $ret;
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dictName, key, value', 'required'),
			array('dictName, key', 'length', 'max'=>20),
			array('value', 'length', 'max'=>30),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, dictName, key, value', 'safe', 'on'=>'search'),
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
			'dictName' => 'Dict Name',
			'key' => 'Key',
			'value' => 'Value',
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

		$criteria->compare('dictName',$this->dictName,true);

		$criteria->compare('key',$this->key,true);

		$criteria->compare('value',$this->value,true);

		return new CActiveDataProvider('ThirdDict', array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @return ThirdDict the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}