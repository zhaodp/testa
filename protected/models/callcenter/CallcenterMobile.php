<?php

/**
 * This is the model class for table "{{callcenter_mobile}}".
 *
 * The followings are the available columns in table '{{callcenter_mobile}}':
 * @property string $short_url
 * @property integer $call_id
 * @property string $phone
 * @property integer $flag
 * @property string $updated
 * @property string $created
 */
class CallcenterMobile extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{callcenter_mobile}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array(
				'short_url, call_id, phone, flag, updated, created', 
				'required'), 
			array(
				'call_id, flag', 
				'numerical', 
				'integerOnly'=>true), 
			array(
				'short_url, phone', 
				'length', 
				'max'=>15), 
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array(
				'short_url, call_id, phone, flag, updated, created', 
				'safe', 
				'on'=>'search'));
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'call_log'=>array(
				self::BELONGS_TO, 
				'CallcenterLog', 
				'call_id'));
	}
	
	public function beforeSave()
	{
		$this->created = date(Yii::app()->params['formatDateTime'], time());
		return true;
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'short_url'=>'Short Url', 
			'call_id'=>'CallID', 
			'phone'=>'Phone', 
			'flag'=>'Flag', 
			'updated'=>'Updated', 
			'created'=>'Created');
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
		

		$criteria = new CDbCriteria();
		
		$criteria->compare('short_url', $this->short_url);
		$criteria->compare('call_id', $this->call_id);
		$criteria->compare('phone', $this->phone);
		$criteria->compare('flag', $this->flag);
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria));
	}
	
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CallcenterMobile the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}
