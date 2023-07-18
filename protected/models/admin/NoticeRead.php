<?php

/**
 * This is the model class for table "{{notice_read}}".
 *
 * The followings are the available columns in table '{{notice_read}}':
 * @property integer $notice_id
 * @property string $driver_id
 * @property integer $created
 */
class NoticeRead extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return NoticeRead the static model class
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
		return '{{notice_read}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('notice_id, driver_id, created', 'required'),
			array('notice_id, created', 'numerical', 'integerOnly'=>true),
			array('driver_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('notice_id, driver_id, created', 'safe', 'on'=>'search'),
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
			'notice_id' => 'Notice',
			'driver_id' => 'Driver',
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

		$criteria->compare('notice_id',$this->notice_id);
		$criteria->compare('driver_id',$this->driver_id);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * 获取noticeRead
	 */
	public function getnoticeRead($date){
		if($date){
			return $this->find('driver_id=:driver_id AND notice_id=:notice_id', array (
						':driver_id'=>$date['driver_id'], 
						':notice_id'=>$date['notice_id']));
		}else{
			return '';
		} 
	}
	
	/**
	 * 保存阅读记录
	 */
	public function noticeReadSave($date){
		if($date['driver_id']){
			$noticeRead = new NoticeRead();
			if (!$noticeRead->getnoticeRead($date)) {
				$NoticeRead = array();
				$NoticeRead['driver_id'] = $date['driver_id'];
				$NoticeRead['notice_id'] = $date['notice_id'];
				$NoticeRead['created'] = time();
				$noticeRead->attributes = $NoticeRead;
				$noticeRead->save();
			}
		}
	}
}