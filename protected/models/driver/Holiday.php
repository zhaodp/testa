<?php

/**
 * This is the model class for table "{{holiday}}".
 *
 * The followings are the available columns in table '{{holiday}}':
 * @property integer $id
 * @property string $holiday
 * @property string $status
 * @property string $create_date
 */
class Holiday extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Holiday the static model class
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
		return '{{holiday}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('holiday, status,create_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, holiday, status, create_date', 'safe', 'on'=>'search'),
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
			'holiday' => '节假日',
			'status' => '状态',
			'create_date' => '创建日期',
		);
	}

	public function beforeSave() {
		if (parent::beforeSave()) {
			if ($this->isNewRecord) {
				$this->create_date = date('Y-m-d H:i:s');
				return true;
			}else{
				return true;
			}
		}
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
		$criteria->compare('holiday',$this->holiday,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('create_date',$this->create_date,true);
		$criteria->order='holiday desc';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getHolidayByDate($date){
		if($date==''){ return ;}
		$result = Holiday::model()->find("holiday = :holiday",array(':holiday'=>$date));
		
		if(!empty($result)){
			return $result->attributes;
		}else{
			return ;
		}
	}
}