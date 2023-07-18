<?php

/**
 * This is the model class for table "{{employee}}".
 *
 * The followings are the available columns in table '{{employee}}':
 * @property string $imei
 * @property string $name
 * @property string $picture
 * @property string $phone
 * @property string $id_card
 * @property string $domicile
 * @property string $car_card
 * @property integer $year
 * @property integer $level
 * @property integer $state
 * @property integer $price
 * @property string $price_detail
 * @property integer $mcc
 * @property integer $mnc
 * @property integer $lac
 * @property integer $ci
 * @property string $report_time
 * @property string $longitude
 * @property string $latitude
 * @property string $update_time
 * @property integer $mark
 * @property string $password
 * @property string $user
 * @property integer $city_id
 * @property string $ext_phone
 */
class Employee extends CActiveRecord {
	/**
	 * 空闲状态
	 */
	const EMPLOYEE_IDLE = 0;
	/**
	 * 工作状态
	 */
	const EMPLOYEE_WORK = 1;
	/**
	 * 下班状态
	 */
	const EMPLOYEE_GETOFF = 2;
	
	/**
	 * 有效司机
	 */
	const MARK_ENABLE = 0;
	/**
	 * 已屏蔽的司机
	 */
	const MARK_DISNABLE = 1;
	/**
	 * 已换手机的司机
	 */
	const MARK_CHANGE = 2;
	/**
	 * 已解约的司机
	 */
	const MARK_LEAVE = 3;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Employee the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{employee}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'imei', 
				'required'), 
			array (
				'year, level, state, price, mcc, mnc, lac, ci, mark, city_id', 
				'numerical', 
				'integerOnly'=>true), 
			array (
				'imei, name, domicile, longitude, latitude, password, user, ext_phone', 
				'length', 
				'max'=>255), 
			array (
				'picture, price_detail', 
				'length', 
				'max'=>1024), 
			array (
				'phone, id_card', 
				'length', 
				'max'=>20), 
			array (
				'report_time, update_time', 
				'length', 
				'max'=>20), 
			array (
				'car_card', 
				'length', 
				'max'=>50), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'imei, name, picture, phone, id_card, domicile, car_card, year, level, state, price, price_detail, mcc, mnc, lac, ci, report_time, longitude, latitude, update_time, mark, password, user, city_id, ext_phone', 
				'safe', 
				'on'=>'search'));
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array (
			'order'=>array (
				self::HAS_MANY, 
				'Order', 
				'imei'), 
			'reportedOrderCount'=>array (
				self::STAT, 
				'Order', 
				'imei', 
				'condition'=>'t.status IN (1, 4)'), 
			'unreportOrderCount'=>array (
				self::STAT, 
				'Order', 
				'imei', 
				'condition'=>'t.status = 0'), 
			'commentCount'=>array (
				self::STAT, 
				'Comments', 
				'employee_id'), 
			'ext'=>array (
				self::HAS_ONE, 
				'DriverExt', 
				'driver_id'));
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'imei'=>'imei', 
			'name'=>'姓名', 
			'picture'=>'照片', 
			'phone'=>'电话', 
			'id_card'=>'身份证', 
			'domicile'=>'籍贯', 
			'car_card'=>'驾驶证', 
			'year'=>'驾龄', 
			'level'=>'星级', 
			'state'=>'状态', 
			'price'=>'价格', 
			'price_detail'=>'价格明细', 
			'mcc'=>'Mcc', 
			'mnc'=>'Mnc', 
			'lac'=>'Lac', 
			'ci'=>'Ci', 
			'report_time'=>'上报时间', 
			'longitude'=>'Longitude', 
			'latitude'=>'Latitude', 
			'update_time'=>'更新时间', 
			'mark'=>'屏蔽', 
			'password'=>'Password', 
			'user'=>'工号', 
			'city_id'=>'城市', 
			'ext_phone'=>'备用电话');
	}
	
	public static function getProfile($name) {
		$criteria = new CDbCriteria();
		$criteria->select = '*';
		$criteria->condition = 'user=:name and mark < 2';
		$criteria->order = 'mark';
		$criteria->params = array (
			':name'=>$name);
		return self::model()->find($criteria);
	}
	
	/**
	 * 用司机工号取IEMI
	 */
	public static function getImei($id) {
		$criteria = new CDbCriteria();
		$criteria->select = 'imei';
		$criteria->condition = 'user=:id and mark=0';
		$criteria->params = array (
			':id'=>$id);
		
		$ret = self::model()->find($criteria);
		if ($ret) {
			return $ret->imei;
		}
		return null;
	}
	
	/**
	 * 确认输入的IEMI有效性
	 * @author libaiyang 2013-05-06 修改签约问题
	 */
	public static function getActiveImei($imei) {
		$criteria = new CDbCriteria();
		$criteria->select = 'imei, phone';
		$criteria->condition = "user='' and city_id=0 and year=0 and (mark=:markDisable or mark =:markChange) and imei=:imei";
		$criteria->params = array (
			':markDisable'=>self::MARK_DISNABLE, 
			':markChange'=>self::MARK_CHANGE, 
			':imei'=>$imei);
		
		$ret = self::model()->find($criteria);
		if ($ret) {
			return $ret->phone;
		} else {
			return '0';
		}
	}
	
	/**
	 * 获得可用IEMI
	 * @author libaiyang 2013-05-06 修改签约问题
	 */
	public static function getActiveImeis($imei) {
		$criteria = new CDbCriteria();
		$criteria->select = 'imei';
		//$criteria->limit = 3;
		$criteria->condition = "user='' and city_id=0 and year=0 and mark in (:markDisable, :markChange) or imei=:imei";
		$criteria->params = array (
			':markDisable'=>self::MARK_DISNABLE, 
			':markChange'=>self::MARK_CHANGE, 
			':imei'=>$imei);
		
		$ret = self::model()->findAll($criteria);
		
		if ($ret) {
			$retarr = array ();
			foreach($ret as $value)
				$retarr[] = $value->imei;
			return $retarr;
		}
		return null;
	}
	
	/**
	 * 手机注册imei和电话
	 */
	public static function register($imei, $phone) {
		$employee = self::model()->find('imei=:imei', array (
			':imei'=>$imei));
		
		if (!$employee) {
			$e = new Employee();
			$e->attributes = array (
				'imei'=>$imei, 
				'phone'=>$phone);
			if ($e->save()) {
				return 1;
			}
		}
		return 0;
	}
	
	public function updateChangedIMEI($current_imei){
		$employee = Employee::model()->find('imei=:imei', array (
			':imei'=>$current_imei));
        if ($employee) {
            $attr = array (
                'user'=>'',
                'name'=>'',
                'picture'=>'',
                'phone'=>'',
                'id_card'=>'',
                'domicile'=>'',
                'car_card'=>'',
                'ext_phone'=>'',
                'password'=>'6688',
                'year'=>0,
                'level'=>0,
                'city_id'=>0,
                'mark'=>Employee::MARK_CHANGE); //修改司机状态为替换
            return $employee->updateByPk($current_imei, $attr);
        } else {
            return false;
        }
	}
	
	/**
	 * 检查在空闲的超过10分钟未上报数据的司机
	 */
	public function checkStatus() {
		$drivers = self::model()->findAll('unix_timestamp(now()) - unix_timestamp(update_time) >= 1200 and mark=0 and state =0 ');
		return $drivers;
	}
	
	public function UpdateStatus($attributes) {
		self::model()->update($attributes);
	}
	
	/**
	 * 检查电话号码是不是司机的号码
	 */
//	public function checkDriverPhone($phone) {
//		$sql = 'select count(*) from t_employee where phone=:phone or ext_phone=:phone';
//		return self::model()->countBySql($sql, array (
//			':phone'=>$phone));
//	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 * 
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		

		$criteria = new CDbCriteria();
		
		$criteria->compare('imei', $this->imei, true);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('picture', $this->picture, true);
		$criteria->compare('phone', $this->phone, true);
		$criteria->compare('id_card', $this->id_card, true);
		$criteria->compare('domicile', $this->domicile, true);
		$criteria->compare('car_card', $this->car_card, true);
		$criteria->compare('year', $this->year);
		$criteria->compare('level', $this->level);
		$criteria->compare('state', $this->state);
		$criteria->compare('report_time', $this->report_time, true);
		$criteria->compare('longitude', $this->longitude, true);
		$criteria->compare('latitude', $this->latitude, true);
		$criteria->compare('update_time', $this->update_time, true);
		$criteria->compare('mark', $this->mark);
		$criteria->compare('password', $this->password, true);
		$criteria->compare('user', $this->user, true);
		$criteria->compare('city_id', $this->city_id);
		$criteria->compare('ext_phone', $this->ext_phone, true);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria));
	}
}
