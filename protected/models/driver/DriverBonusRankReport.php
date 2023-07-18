<?php

/**
 * This is the model class for table "{{driver_bonus_rank_report}}".
 *
 * The followings are the available columns in table '{{driver_bonus_rank_report}}':
 * @property integer $id
 * @property string $driver_id
 * @property string $name
 * @property integer $city_id
 * @property string $bonus_code
 * @property double $bonus
 * @property integer $bind_count
 * @property integer $used_count
 * @property integer $created
 */
class DriverBonusRankReport extends ReportActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverBonusRankReport the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return CDbConnection database connection
	 */
//	public function getDbConnection()
//	{
//		return Yii::app()->dbreport;
//	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{driver_bonus_rank_report}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, bonus', 'required'),
			array('city_id, bind_count, used_count, created', 'numerical', 'integerOnly'=>true),
			array('bonus', 'numerical'),
			array('driver_id', 'length', 'max'=>10),
			array('name', 'length', 'max'=>255),
			array('bonus_code', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, driver_id, name, city_id, bonus_code, bonus, bind_count, used_count, consumption_day,created, bind_self', 'safe', 'on'=>'search'),
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
			'driver_id' => 'Driver',
			'name' => 'Name',
			'city_id' => 'City',
			'bonus_code' => 'Bonus Code',
			'bonus' => 'Bonus',
			'bind_count' => 'Bind Count',
			'used_count' => 'Used Count',
            'consumption_day' => 'Consumption Day',
            'bind_self' => 'Bind Self',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('driver_id',$this->driver_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('bonus_code',$this->bonus_code,true);
		$criteria->compare('bonus',$this->bonus);
		$criteria->compare('bind_count',$this->bind_count);
		$criteria->compare('used_count',$this->used_count);
        $criteria->compare('consumption_day',$this->consumption_day);
        $criteria->compare('bind_self',$this->bind_self);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getSummaryCountByCondition($condition) {
		$table_name = 't_driver_bonus_rank_report';
		$sql = "select COUNT(distinct bonus_code)";
		$sql .= " from {$table_name}"; 
		if (isset($condition['city_id']) && $condition['city_id']) {
			if (!strpos($sql, 'where')) {
				$sql .= " where city_id={$condition['city_id']} ";
			} else {
				$sql .= " and city_id={$condition['city_id']} ";
			}
		}
		if (isset($condition['driver_id']) && $condition['driver_id']) {
			if (!strpos($sql, 'where')) {
				$sql .= " where driver_id='{$condition['driver_id']}' ";
			} else {
				$sql .= " and driver_id='{$condition['driver_id']}' ";
			}	
		}
		$command = Yii::app ()->dbreport->createCommand($sql);
		$count = $command->queryScalar();
		return $count;	
	}
	public function getSummaryDataByCondition($condition) {
		$table_name = 't_driver_bonus_rank_report';
		$sql = "select driver_id, name, bonus_code, city_id,SUM(bonus) as bonus_count, SUM(bind_count) as bind_count_count, SUM(used_count) AS used_count_count, SUM(consumption_day) as consumption_count, SUM(bind_self) as bind_self_count";
		$sql .= " from {$table_name}"; 
		if (isset($condition['city_id']) && $condition['city_id']) {
			if (!strpos($sql, 'where')) {
				$sql .= " where city_id={$condition['city_id']} ";
			} else {
				$sql .= " and city_id={$condition['city_id']} ";
			}
		}

		if (isset($condition['driver_id']) && trim($condition['driver_id'])!='') {
			if (!strpos($sql, 'where')) {
				$sql .= " where driver_id='{$condition['driver_id']}' ";
			} else {
				$sql .= " and driver_id='{$condition['driver_id']}' ";
			}
		}
		$sql .= " GROUP BY bonus_code ";
		$sql .= " ORDER BY bonus_count desc ";
		//$sql .= " LIMIT {$offset}, {$limit}";
		$command = Yii::app ()->dbreport->createCommand($sql);
		$data = $command->queryAll();
		return $data;
	}
	
	public function getDataProvider($data, $pageSize=20) {
		$dataProvider = new CArrayDataProvider($data , array (
			'id'=>'driver_bonus_rank',
			'sort'=>array(
			'attributes'=>array(
					'bind_count_count', 'used_count_count', 'bonus_count',
				),
			),
			'pagination'=>array (
				'pageSize'=>$pageSize),
		));
		return $dataProvider;
	}
	
	public function getUsedCountByDriver($driver_id) {
		$sql = "SELECT SUM(used_count) AS used_count FROM t_driver_bonus_rank_report WHERE driver_id='{$driver_id}'";
		$command = Yii::app ()->dbreport->createCommand($sql);
		$used_count = $command->queryScalar();
		return $used_count;
	}
	
	public function getUserSort($driver_id) {
		$driver_used_count = $this->getUsedCountByDriver($driver_id);
		$driver = Driver::getProfile($driver_id);
		$sql = "SELECT SUM(used_count) AS sort FROM t_driver_bonus_rank_report ";
		if ($driver) {
			if ($driver->city_id) {
				$sql .= "WHERE city_id=".$driver->city_id;
			}
		}
		$sql .= " GROUP BY driver_id HAVING sort>".intval($driver_used_count);
		$command = Yii::app ()->dbreport->createCommand($sql);
		$sort = $command->queryAll();
		if (is_array($sort)) {
			return count($sort)+1;
		} else {
			return 1;
		}		
	}
}