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
class MonthOrderReport extends ReportActiveRecord
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
//	public function getDbConnection()
//	{
//		return Yii::app()->dbreport;
//	}

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
            array('driver_id, driver_name, city_id, current_month, updated, created', 'required'),
            array('current_month, p_online, p_continuous, c_complain, d_complain, high_opinion, bad_review, normal_days, p_active', 'numerical', 'integerOnly'=>true),
            array('driver_id, driver_name', 'length', 'max'=>20),
            array('city_id, cancel, complete, additional, online, income, accept, accept_days', 'length', 'max'=>10),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, driver_id, driver_name, city_id, current_month, cancel, complete, additional, online, income, accept, accept_days, updated, created, p_online, p_continuous, c_complain, d_complain, high_opinion, bad_review, normal_days, p_active', 'safe', 'on'=>'search'),
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
            'current_month' => 'Current Month',
            'cancel' => 'Cancel',
            'complete' => 'Complete',
            'additional' => 'Additional',
            'online' => 'Online',
            'income' => 'Income',
            'accept' => 'Accept',
            'accept_days' => 'Accept Days',
            'updated' => 'Updated',
            'created' => 'Created',
            'p_online' => 'P Online',
            'p_continuous' => 'P Continuous',
            'c_complain' => 'C Complain',
            'd_complain' => 'D Complain',
            'high_opinion' => 'High Opinion',
            'bad_review' => 'Bad Review',
            'normal_days' => 'Normal Days',
            'p_active' => 'P Active',
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
        $criteria->compare('current_month',$this->current_month);
        $criteria->compare('cancel',$this->cancel,true);
        $criteria->compare('complete',$this->complete,true);
        $criteria->compare('additional',$this->additional,true);
        $criteria->compare('online',$this->online,true);
        $criteria->compare('income',$this->income,true);
        $criteria->compare('accept',$this->accept,true);
        $criteria->compare('accept_days',$this->accept_days,true);
        $criteria->compare('updated',$this->updated,true);
        $criteria->compare('created',$this->created,true);
        $criteria->compare('p_online',$this->p_online);
        $criteria->compare('p_continuous',$this->p_continuous);
        $criteria->compare('c_complain',$this->c_complain);
        $criteria->compare('d_complain',$this->d_complain);
        $criteria->compare('high_opinion',$this->high_opinion);
        $criteria->compare('bad_review',$this->bad_review);
        $criteria->compare('normal_days',$this->normal_days);
        $criteria->compare('p_active',$this->p_active);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function getModelByDriverDate($driver_id, $date) {
        $model = self::model()->find("driver_id = '{$driver_id}' and current_month = {$date}");
        return $model;
    }

    public function beforeSave() {
        if ($this->isNewRecord)
            $this->created = date('Y-m-d H:i:s', time());
        $this->updated = date('Y-m-d H:i:s',time());
        return parent::beforeSave();
    }

    public function getList($month_date, $city_id, $driver_id = NULL) {
        //改成用原生sql BY AndyCong 2013-08-29
        $sql = "SELECT *,cancel/accept as cancel_rate FROM t_month_order_report WHERE current_month = :month_date";
        $paramsArr = array(':month_date' => $month_date);
        if ($city_id) {
            $sql .= ' AND city_id = :city_id';
            $paramsArr[':city_id'] = $city_id;
        }
        if ($driver_id !== NULL && trim($driver_id)) {
            $sql .= ' AND driver_id = :driver_id';
            $paramsArr[':driver_id'] = $driver_id;
        }
        $command = Yii::app()->dbreport->createCommand($sql);
        $command->params = $paramsArr;
        $data = $command->queryAll();
        return $data;
    }

    public function getDriverExtData($driver_id) {
        $command = Yii::app()->dbreport->createCommand();
        $command->select('
            sum(cancel) as cancel,
            sum(complete) as complete,
            sum(additional) as additional,
            sum(online) as online,
            sum(accept) as accept,
            sum(accept_days) as accept_days,
            sum(c_complain) as c_complain,
            sum(d_complain) as d_complain,
            sum(high_opinion) as high_opinion,
            sum(bad_review) as bad_review,
            sum(p_online) as p_online,
            sum(p_continuous) as p_continuous,
            sum(normal_days) as normal_days
        ');
        $command->from('t_month_order_report');
        $command->where('driver_id=:driver_id', array(':driver_id'=>$driver_id));
        $data = $command->queryRow();
        return $data;
    }

}