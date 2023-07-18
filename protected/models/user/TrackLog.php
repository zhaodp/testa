<?php

/**
 * This is the model class for table "{{track_log}}".
 *
 * The followings are the available columns in table '{{track_log}}':
 * @property integer $id
 * @property string $imei
 * @property integer $state
 * @property string $longitude
 * @property string $latitude
 * @property integer $mark
 * @property string $report_time
 */
class TrackLog extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TrackLog the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{track_log}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'imei, longitude, latitude, report_time', 
				'required'
			), 
			array (
				'id, state, mark', 
				'numerical', 
				'integerOnly'=>true
			), 
			array (
				'imei', 
				'length', 
				'max'=>20
			), 
			array (
				'longitude, latitude', 
				'length', 
				'max'=>255
			), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, imei, state, longitude, latitude, mark, report_time', 
				'safe', 
				'on'=>'search'
			)
		);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array ();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'id'=>'ID', 
			'imei'=>'Imei', 
			'state'=>'State', 
			'longitude'=>'Longitude', 
			'latitude'=>'Latitude', 
			'mark'=>'Mark', 
			'report_time'=>'Report Time'
		);
	}
	
	public function beforeSave() {
		if (parent::beforeSave()) {
			$criteria = new CDbCriteria(array (
				'condition'=>'imei=:imei and report_time=:report_time', 
				'params'=>array (
					':imei'=>$this->imei, 
					':report_time'=>$this->report_time
				)
			));
			$item = TrackLog::model()->find($criteria);
			if (count($item)) {
				return false;
			} else {
				return true;
			}
		} else
			return false;
	
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		

		$criteria = new CDbCriteria();
		
		$criteria->compare('id', $this->id);
		$criteria->compare('imei', $this->imei, true);
		$criteria->compare('state', $this->state);
		$criteria->compare('longitude', $this->longitude, true);
		$criteria->compare('latitude', $this->latitude, true);
		$criteria->compare('mark', $this->mark);
		$criteria->compare('report_time', $this->report_time, true);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria
		));
	}
}