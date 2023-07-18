<?php

/**
 * This is the model class for table "{{month_order_report}}".
 *
 * The followings are the available columns in table '{{month_order_report}}':
 * @property string $id
 * @property string $driver_id
 * @property string $driver_name
 * @property string $city_id
 * @property integer $date
 * @property string $cancel
 * @property string $complete
 * @property string $additional
 * @property string $online
 * @property string $income
 * @property string $accept
 * @property string $accept_days
 * @property string $updated
 * @property string $created
 */
class MonthOrderReport extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return MonthOrderReport the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return CDbConnection database connection
	 */
	public function getDbConnection()
	{
		return Yii::app()->dbreport;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{month_order_report}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('driver_id, driver_name, city_id, date', 'required'),
			array('date', 'numerical', 'integerOnly'=>true),
			array('driver_id, driver_name', 'length', 'max'=>20),
			array('city_id, cancel, complete, additional, online, income, accept, accept_days, updated, created', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, driver_id, driver_name, city_id, date, cancel, complete, additional, online, income, accept, accept_days, updated, created', 'safe', 'on'=>'search'),
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
			'driver_name' => 'Driver Name',
			'city_id' => 'City',
			'date' => 'Date',
			'cancel' => 'Cancel',
			'complete' => 'Complete',
			'additional' => 'Additional',
			'online' => 'Online',
			'income' => 'Income',
			'accept' => 'Accept',
			'accept_days' => 'Accept Days',
			'updated' => 'Updated',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('driver_id',$this->driver_id,true);
		$criteria->compare('driver_name',$this->driver_name,true);
		$criteria->compare('city_id',$this->city_id,true);
		$criteria->compare('date',$this->date);
		$criteria->compare('cancel',$this->cancel,true);
		$criteria->compare('complete',$this->complete,true);
		$criteria->compare('additional',$this->additional,true);
		$criteria->compare('online',$this->online,true);
		$criteria->compare('income',$this->income,true);
		$criteria->compare('accept',$this->accept,true);
		$criteria->compare('accept_days',$this->accept_days,true);
		$criteria->compare('updated',$this->updated,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function getModelByDriverDate($driver_id, $date) {
        $model = self::model()->findByAttributes(array(
            'driver_id' => $driver_id,
            'date' => $date,
        ));
        return $model;
    }

    public function beforeSave() {
        if ($this->isNewRecord)
            $this->created = time();
        $this->updated = time();
        return parent::beforeSave();
    }

    public function getList($month_date, $city_id) {
        $command = Yii::app()->dbreport->createCommand();
        $command->select('*');
        $command->from('t_month_order_report');
        $search_str = $city_id ? ' AND city_id='.$city_id : '';
        $command->where('date=:month_date'.$search_str, array(':month_date'=>$month_date));
        $data = $command->queryAll();
        return $data;
    }

}