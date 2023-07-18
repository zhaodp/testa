<?php

/**
 * This is the model class for table "{{driver_queue}}".
 *
 * The followings are the available columns in table '{{driver_queue}}':
 * @property integer $id
 * @property integer $city_id
 * @property integer $worktype
 * @property string $name
 * @property string $phone
 * @property string $address
 * @property string $id_card
 * @property string $domicile
 * @property string $car_card
 * @property integer $mark
 * @property string $ip
 * @property string $created
 */
class DriverQueue extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverQueue the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{driver_queue}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'city_id, worktype, name, phone, address, id_card, domicile, car_card', 
				'required'), 
			array (
				'city_id, worktype, mark, phone', 
				'numerical', 
				'integerOnly'=>true), 
			array (
				'name', 
				'length', 
				'max'=>50), 
			array (
				'id_card,car_card', 
				'length', 
				'max'=>20), 
			array (
				'phone', 
				'length', 
				'max'=>11, 
				'min'=>11), 
			array (
				'address', 
				'length',
				'min'=>10,
				'max'=>200), 
			array (
				'domicile', 
				'length', 
				'max'=>30), 
			array (
				'ip', 
				'length', 
				'max'=>15), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, city_id, worktype, name, phone, address, id_card, domicile, car_card, mark, ip, created', 
				'safe', 
				'on'=>'search'));
	}
	
	public function beforeSave() {
		if (parent::beforeSave()) {
			$this->mark = 0;
			$this->ip = Yii::app()->request->userHostAddress;
			$this->created = date(Yii::app()->params['formatDateTime'], time());
		}
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
			'city_id'=>'工作城市', 
			'worktype'=>'全职/兼职', 
			'name'=>'姓名', 
			'phone'=>'手机号码', 
			'address'=>'现居住地址', 
			'id_card'=>'身份证号码', 
			'domicile'=>'籍贯', 
			'car_card'=>'驾驶证号码', 
			'mark'=>'Mark', 
			'ip'=>'Ip', 
			'created'=>'Created');
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
		$criteria->compare('city_id', $this->city_id);
		$criteria->compare('worktype', $this->worktype);
		$criteria->compare('name', $this->name);
		$criteria->compare('phone', $this->phone);
		$criteria->compare('address', $this->address);
		$criteria->compare('id_card', $this->id_card);
		$criteria->compare('domicile', $this->domicile);
		$criteria->compare('car_card', $this->car_card);
		$criteria->compare('mark', $this->mark);
		$criteria->compare('ip', $this->ip);
		$criteria->compare('created', $this->created);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria));
	}
}