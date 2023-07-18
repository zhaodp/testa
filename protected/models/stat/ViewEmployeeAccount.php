<?php

/**
 * This is the model class for table "{{view_employee_account}}".
 *
 * The followings are the available columns in table '{{view_employee_account}}':
 * @property string $user
 * @property double $t0
 * @property double $t1
 * @property double $t2
 * @property double $t3
 * @property double $t4
 * @property double $t5
 * @property string $comment
 * @property integer $created
 */
class ViewEmployeeAccount extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return V_EmployeeAccount the static model class
	 */
	public static function model($className = __CLASS__) {
		self::$db = Yii::app()->db_readonly;
		$result =  parent::model($className);
		self::$db = Yii::app()->db;
		return $result;
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{view_employee_account}}';
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
				'created, city_id, type, order_id', 
				'numerical', 
				'integerOnly'=>true), 
			array (
				't0, t1, t2, t3, t4, t5, t6', 
				'numerical'), 
			array (
				'user', 
				'length', 
				'max'=>10), 
			array (
				'comment', 
				'length', 
				'max'=>255), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'user, t0, t1, t2, t3, t4, t5, t6, type, order_id, city_id, comment, created', 
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
			'user'=>'工号', 
			'city_id'=>'城市', 
			'type'=>'收费类型',
			'order_id'=>'订单流水号', 
			't0'=>Dict::item('account_type', 0), 
			't1'=>Dict::item('account_type', 1), 
			't2'=>Dict::item('account_type', 2), 
			't3'=>Dict::item('account_type', 3), 
			't4'=>Dict::item('account_type', 4), 
			't5'=>Dict::item('account_type', 5), 
			't6'=>Dict::item('account_type', 6), 
			'comment'=>'备注', 
			'created'=>'报单日期');
	}
	
	public function getAccountHistory($params){
		$where = '';
		$settle_month = 201205;
		if (empty($params)){ 
			$where .= " AND 1<> 1";
		} else {
			if ($params['created'] >= date('Ym') || $params['created'] < 201205) {
				$settle_month = 201205;
			} else {
				$settle_month = $params['created'];
			}
			if (trim($params['user']) == '') $where .= " AND 1<> 1";
			if ($params['city_id'] > 0) $where .= " AND d.city_id=:city_id";
			if ($params['type'] > 0) $where .= " AND ea.type=:type";
			if (trim($params['user']) != '') $where .= " AND ea.user=:user";
			if (intval($params['order_id']) > 0) $where .= " AND ea.order_id=:order_id";
		}
		$sql = "SELECT 
					`ea`.`user` AS `id`,
					`ea`.`user` AS `user`,
					`d`.`name` AS `name`,
					`d`.`city_id` AS `city_id`,
					`ea`.`type` AS `type`,
					`ea`.`cast` AS `cast`,
					`ea`.`order_id` AS `order_id`,
					`ea`.`comment` AS `comment`,
					`ea`.`created` AS `created` 
				FROM 
					(`t_employee_account_$settle_month` `ea` JOIN `t_driver` `d`) 
				WHERE 
					(`ea`.`user` = `d`.`user`) 
					$where
				ORDER BY 
					`ea`.`created` DESC";

		$command = Yii::app()->db_readonly->createCommand($sql);
		if (!empty($params)){
			if ($params['city_id'] > 0) $command->bindParam(":city_id", $params['city_id']);
			if ($params['type'] > 0) $command->bindParam(":type", $params['type']);
			if (trim($params['user']) != '') $command->bindParam(":user", $params['user']);
			if (intval($params['order_id']) > 0) $command->bindParam(":order_id", $params['order_id']);
		}
		$records = $command->queryAll();
		
		return $records;
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		$criteria = new CDbCriteria();
		if ($this->city_id==0) {
			$this->city_id = null;
		} elseif (Yii::app()->user->city!=0) {
			$this->city_id = Yii::app()->user->city;
		}
		
		$criteria->compare('user', $this->user, true);
		if ($this->type > 0)
			$criteria->compare('type', $this->type);
		$criteria->compare('city_id', $this->city_id);
		$criteria->compare('order_id', $this->order_id);	
		
		if (isset($this->created) && $this->created > 0)
		{
			$criteria->addCondition('created>='. $this->created . ' and created <' . ($this->created + 86400)); 
		}
		
		$criteria->order='created desc';
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria));
	}
}
