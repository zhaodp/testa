<?php

/**
 * This is the model class for table "{{driver_batch}}".
 *
 * The followings are the available columns in table '{{driver_batch}}':
 * @property integer $id
 * @property integer $driver_batch
 * @property integer $city_id
 * @property integer $type
 * @property integer $status
 * @property integer $created
 */
class DriverBatch extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverBatch the static model class
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
		return '{{driver_batch}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('driver_batch, city_id', 'required'),
			array('driver_batch, city_id, type, status, created, entrynum, entrycount', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, driver_batch, city_id, type, status, created, entrynum, entrycount', 'safe', 'on'=>'search'),
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
			'id' => '序号',
			'driver_batch' => '批次',
			'city_id' => '城市',
			'type' => '类型',
			'status' => '状态',
			'created' => '创建时间',
			'entrycount' => '批次总数',
			'entrynum' => '已签约',
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
		$criteria->compare('driver_batch',$this->driver_batch);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('type',$this->type);
		$criteria->compare('status',$this->status);
		$criteria->compare('created',$this->created);
		$criteria->compare('entrynum',$this->entrynum);
		$criteria->order = 'id desc';
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function updataStatus($data){
		$driverbatch = self::model()->find('driver_batch = :batch',
						array(':batch'=>$data['batch']));
						
		$attributes = $driverbatch->attributes;
		$attributes['status'] = $data['status'];
		$driverbatch->attributes = $attributes;
		$driverbatch->save();
	}
	
	public function updataEntryCount($data){
		$driverbatch = self::model()->find('driver_batch = :batch',
						array(':batch'=>$data['batch']));
						
		$attributes = $driverbatch->attributes;
		$attributes['entrycount'] = $attributes['entrycount']+$data['num'];
		$driverbatch->attributes = $attributes;
		$driverbatch->save();
	}
	
	public function updataEntrynum($data){
		$driverbatch = self::model()->find('driver_batch = :batch',
						array(':batch'=>$data['batch']));
						
		$attributes = $driverbatch->attributes;
		$attributes['entrynum'] =$attributes['entrynum'] + $data['num'];
		$driverbatch->attributes = $attributes;
		$driverbatch->save();
	}
	
	public function batchLog($data){
		
	}
	
}