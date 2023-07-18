<?php

/**
 * This is the model class for table "{{account_record}}".
 *
 * The followings are the available columns in table '{{account_record}}':
 * @property integer $id
 * @property integer $value
 * @property integer $total
 * @property integer $record_id
 * @property string $insert_time
 * @property string $user
 * @property string $comment
 */
class Account extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Account the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{account_record}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'user', 
				'required'), 
			array (
				'value, total, record_id', 
				'numerical', 
				'integerOnly'=>true), 
			array (
				'user', 
				'length', 
				'max'=>50), 
			array (
				'comment', 
				'length', 
				'max'=>255), 
			array (
				'insert_time', 
				'safe'), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, value, total, record_id, insert_time, user, comment', 
				'safe', 
				'on'=>'search'));
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
			'value'=>'Value', 
			'total'=>'Total', 
			'record_id'=>'Record', 
			'insert_time'=>'Insert Time', 
			'user'=>'User', 
			'comment'=>'Comment');
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
		$criteria->compare('value', $this->value);
		$criteria->compare('total', $this->total);
		$criteria->compare('record_id', $this->record_id);
		$criteria->compare('insert_time', $this->insert_time, true);
		$criteria->compare('user', $this->user, true);
		$criteria->compare('comment', $this->comment, true);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria));
	}
	
	public function balanceForward($data,$city_id = 0){
		$where = '1=1';
		$params = array();
		if($city_id != 0){
			$where .=" and left(user,2) = :city_prefix";
			$params[':city_prefix'] = Dict::item('city_prefix', $city_id);
		}
		if($data['settle_date'] != 0){
			$month = date('Ym', strtotime($data['settle_date']));
			$where .=" and settle_date < :settle_date";
			$params[':settle_date'] = $month;
			$employee_account =  Yii::app()->db_finance->createCommand()
							->select('sum(cast)-sum(if(type=0,cast,0))  as cast')
							->from('t_employee_account')
							->where($where,$params)
							->queryRow();
		}else{
			$employee_account =  Yii::app()->db_finance->createCommand()
							->select('sum(cast)-sum(if(type=0,cast,0))  as cast')
							->from('t_employee_account')
							->where($where,$params)
							->queryRow();
		}
		
		return $employee_account['cast'];
	}
}
