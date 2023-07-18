<?php

/**
 * This is the model class for table "t_envelope_map".
 *
 * The followings are the available columns in table 't_envelope_map':
 * @property string $id
 * @property integer $city_id
 * @property string $envelope_id
 * @property integer $status
 * @property string $create_date
 * @property string $last_changed_date
 */
class EnvelopeMap extends FinanceActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{envelope_map}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('city_id, envelope_id', 'required'),
			array('city_id, status', 'numerical', 'integerOnly'=>true),
			array('envelope_id', 'length', 'max'=>20),
			array('create_date, last_changed_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, city_id, envelope_id, status, create_date, last_changed_date', 'safe', 'on'=>'search'),
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
			'city_id' => 'City',
			'envelope_id' => 'Envelope',
			'status' => 'Status',
			'create_date' => 'Create Date',
			'last_changed_date' => 'Last Changed Date',
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

		$criteria->compare('id',$this->id,true);

		$criteria->compare('city_id',$this->city_id);

		$criteria->compare('envelope_id',$this->envelope_id,true);

		$criteria->compare('status',$this->status);

		$criteria->compare('create_date',$this->create_date,true);

		$criteria->compare('last_changed_date',$this->last_changed_date,true);

		return new CActiveDataProvider('EnvelopeMap', array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @return EnvelopeMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


    /**获取红包城市列表
     * @param array $arr
     * @return mixed
     */
    public function getList($arr){
        $criteria=new CDbCriteria;
        $criteria->addInCondition('envelope_id',$arr);
        $criteria->addCondition('status=0');
        return  self::model()->findAll($criteria);
    }


    /**获取城市红包列表
     * @param array $arr
     * @return mixed
     */
    public function getEvenList($city_id){
        $criteria=new CDbCriteria;
        $criteria->select='DISTINCT(envelope_id) as envelope_id';
        $criteria->addCondition('city_id=:city_id');
        $criteria->params[':city_id']=$city_id;
        $data=self::model()->findAll($criteria);
        $result=array();
        foreach($data as $da){
            $result[]=$da->envelope_id;
        }
        return $result;
    }


    /**获取城市红包列表
     * @param array $arr
     * @return mixed
     */
    public function getCityListByEnvelopeId($envelope_id){
        $criteria=new CDbCriteria;
        $criteria->select='city_id';
        $criteria->addCondition('envelope_id=:envelope_id');
        $criteria->params[':envelope_id']=$envelope_id;
        $criteria->addCondition('status=0');
        $data=self::model()->findAll($criteria);
        $result=array();
        foreach($data as $da){
            $result[]=$da->city_id;
        }
        return $result;
    }

}