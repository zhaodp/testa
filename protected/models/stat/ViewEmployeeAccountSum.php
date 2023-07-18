<?php

/**
 * This is the model class for table "{{view_employee_account_sum}}".
 *
 * The followings are the available columns in table '{{view_employee_account_sum}}':
 * @property string $user
 * @property string $name
 * @property integer $city_id
 * @property integer $mark
 * @property integet total_min;
 * @property integet total_max;
 * @property double $type
 * @property double $cast
 */
class ViewEmployeeAccountSum extends CActiveRecord {
	public $total_max;
	
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
		return '{{view_employee_account_sum}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'name', 
				'required'), 
			array (
				'city_id,mark', 
				'numerical', 
				'integerOnly'=>true), 
			array (
				'type,cast,total_max,total_min,total', 
				'numerical'), 
			array (
				'user', 
				'length', 
				'max'=>10), 
			array (
				'name', 
				'length', 
				'max'=>255), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'user, name, city_id, type, mark, total_max,total_min,total', 
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
			'name'=>'姓名', 
			'city_id'=>'城市', 
			'type'=>'交易类型', 
			'cast'=>'交易金额');
	}
	
	public function getAccountMonthlySumByDriver($settle_month = 201205, $driver_id = 'BJ9000'){
		if ($settle_month >= date('Ym') || $settle_month < '201205') $settle_month = 201205;
		$sql = "SELECT 
						0 AS id,
						`ea`.`user` AS `user`,
						`d`.`name` AS `name`,
						`d`.`city_id` AS `city_id`,
						SUM(IF((`ea`.`type` = 0),`ea`.`cast`,0)) AS `t0`,
						SUM(IF((`ea`.`type` = 1),`ea`.`cast`,0)) AS `t1`,
						SUM(IF((`ea`.`type` = 2),`ea`.`cast`,0)) AS `t2`,
						SUM(IF((`ea`.`type` = 3),`ea`.`cast`,0)) AS `t3`,
						SUM(IF((`ea`.`type` = 4),`ea`.`cast`,0)) AS `t4`,
						SUM(IF((`ea`.`type` = 5),`ea`.`cast`,0)) AS `t5`,
						SUM(IF((`ea`.`type` = 6),`ea`.`cast`,0)) AS `t6`,
						SUM(IF((`ea`.`type` = 7),`ea`.`cast`,0)) AS `t7`,
						SUM(IF((`ea`.`type` = 8),`ea`.`cast`,0)) AS `t8`,
						SUM(IF((`ea`.`type` = 9),`ea`.`cast`,0)) AS `t9`,
						SUM(IF((`ea`.`type` = 10),`ea`.`cast`,0)) AS `t10`,
						(SUM(`ea`.`cast`) - SUM(if((`ea`.`type` = 0),`ea`.`cast`,0))) AS `total` 
					FROM 
						(`t_employee_account_$settle_month` `ea` join `t_driver` `d`) 
					WHERE 
						((`ea`.`user` = `d`.`user`) AND (`d`.`user`=:driver_id)) 
					GROUP BY 
						`ea`.`user`";
			$command = Yii::app()->db_readonly->createCommand($sql);
			$command->bindParam(":driver_id", $driver_id);
			$records = $command->queryAll();
			
			return $records;
	}
	
	public function getAccountMonthlySumByCityId($city_id = 0) {
		$where = '';
		if ($city_id>0)
			$where = 'WHERE city_id = '.intval($city_id);
		
		$sql = "SELECT 
					SUM(t0 + t3 - t1 - t2 - t4) AS x0,
					SUM(IF((t1 = -5), t1, 0)) AS x1, 
					SUM(IF((t1 = -10), t1, 0)) AS x2, 
					SUM(IF((t1 = -15), t1, 0)) AS x3, 
					SUM(IF((t1 = -20), t1, 0)) AS x4, 
					SUM(t2) AS x5, 
					SUM(t3) AS x6, 
					SUM(t4) AS x7, 
					SUM(t5) AS x8,
					SUM(t6) AS x9,
					SUM(t7 + t8 + t9 + t10) AS x10,					 
					COUNT(DISTINCT user) AS userCount,
					FROM_UNIXTIME(created, '%Y-%m') AS current_month,
					FROM_UNIXTIME(created, '%Y%m') AS current_month_short
				FROM  `t_view_employee_account_group` 
				$where
				GROUP BY current_month";
		
		$command = Yii::app()->db_readonly->createCommand($sql);
		$records = $command->queryAll();
		
		foreach ($records as $key => $record){
			if ($record['current_month_short'] < date('Ym')){
				$month_record = EmployeeAccount::model()->getMonthlyEmployeeAccountGroup($record['current_month_short'], $city_id);
				$record['x0'] = $record['x0'] + $month_record['x0'];
				$record['x1'] = $record['x1'] + $month_record['x1'];
				$record['x2'] = $record['x2'] + $month_record['x2'];
				$record['x3'] = $record['x3'] + $month_record['x3'];
				$record['x4'] = $record['x4'] + $month_record['x4'];
				$record['x5'] = $record['x5'] + $month_record['x5'];
				$record['x6'] = $record['x6'] + $month_record['x6'];
				$record['x7'] = $record['x7'] + $month_record['x7'];
				$record['x8'] = $record['x8'] + $month_record['x8'];
				$record['x9'] = $record['x9'] + $month_record['x9'];
				$record['x10'] = $record['x10'] + $month_record['x10'];
				$records[$key] = $record;
			}
		}
		
		return $records;
	}
	
	public function getAccountMonthlyAvgByCityId($city_id = 0) {
		$where = '';
		if ($city_id>0)
			$where = 'WHERE city_id = '.intval($city_id);
		
		$sql = "SELECT 
					AVG(x1/userCount) AS avgCount, 
					AVG(x2/userCount) AS avgIncome,
					current_month, 
					current_month_short
				FROM (
						SELECT 
							SUM(IF((t1 = -5), t1, 0)) / -5  + SUM(IF((t1 = -10), t1, 0)) / -10 + SUM(IF((t1 = -15), t1, 0)) / -15 + SUM(IF((t1 = -20), t1, 0)) /-20 AS x1, 
							SUM(t0) AS x2,
							COUNT(DISTINCT user) AS userCount,
							FROM_UNIXTIME(created, '%Y-%m-%d') AS current_day, 
							FROM_UNIXTIME(created, '%Y-%m') AS current_month,
							FROM_UNIXTIME(created, '%Y%m') AS current_month_short
						FROM  `t_view_employee_account_group` 
						$where
						GROUP BY current_day
					) AS monthly 
				GROUP BY current_month";
		
		$command = Yii::app()->db_readonly->createCommand($sql);
		
		$monthdaily = $command->queryAll();
		
		foreach ($monthdaily as $key=>$value){
			if ($value['current_month_short'] < date('Ym')){
				$month_value = EmployeeAccount::model()->getMonthlyEmployeeAccountGroupDailyAvg($value['current_month_short'], $city_id);
				$value['avgCount'] = $value['avgCount'] + $month_value['avgCount'];
				$value['avgIncome'] = $value['avgIncome'] + $month_value['avgIncome'];
				$monthdaily[$key] = $value;
			}
		}
		
		return $monthdaily;
	}
	
	public function getBalanceTotalByCityId($city_id = 0) {
		$model = new ViewEmployeeAccountSum();
		
		$criteria = new CDbCriteria();
		$criteria->select = 'sum(total) as t0';
		if ($city_id>0) {
			$criteria->condition = 'city_id=:city_id';
			$criteria->params = array (
				':city_id'=>$city_id);
		}
		
		return $model->find($criteria);
	}
	/**
	 * 通过司机工号搜索司机当月信息
	 * Enter description here ...
	 * @param unknown_type $user
	 */
	public function getAccountMonthlySumByDriverId($driver_id){
		$settle = Yii::app()->db_readonly->createCommand()
							->select('*')
							->from('t_view_employee_account_sum')
							->where('user=:user', array (':user'=>$driver_id))
							->queryRow();
		if(!empty($settle)){
			$settle['id'] = 1;
			$settle['settle_date'] = '0';
		}
		return $settle;
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		$criteria = new CDbCriteria();
		
		//		if (isset($_GET['Account']['total_max'])) {
		//			$model->total_max = $_GET['Account']['total_max'];
		//		} else {
		//			$model->total_max = 0;
		//		}
		

		if ($this->city_id==0) {
			$this->city_id = null;
		} elseif (Yii::app()->user->city!=0) {
			$this->city_id = Yii::app()->user->city;
		}
		
		$criteria->compare('user', $this->user, true);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('city_id', $this->city_id);
		
		if (!$this->mark) {
			$criteria->addCondition('mark=0');
		} else {
			$criteria->addCondition('mark=1');
		}
		
		if ($this->total_max) {
			$criteria->addCondition('total<'.$this->total_max);
		}
		
		return new CActiveDataProvider($this, array (
			'pagination'=>array (
				'pageSize'=>50), 
			'criteria'=>$criteria));
	}
}
