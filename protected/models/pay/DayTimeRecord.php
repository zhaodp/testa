<?php

/**
 * This is the model class for table "t_day_time_record".
 *
 * The followings are the available columns in table 't_day_time_record':
 * @property integer $id
 * @property integer $status
 * @property string $settle_date
 * @property string $created
 * @property string $order_id
 * @property string $meta
 */
class DayTimeRecord extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 't_day_time_record';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('settle_date', 'length', 'max'=>10),
			array('order_id', 'length', 'max'=>32),
			array('meta', 'length', 'max'=>1024),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, status, settle_date, created, order_id, meta', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * 扣钱列表
	 *
	 * @param $startTime
	 * @param $endTime
	 * @return mixed
	 */
	public function getChargeList($date, $orderId = 0){
		$criteria = new CDbCriteria();
		$criteria->addCondition('status = 0');
		if(!empty($orderId)){
			$criteria->compare('order_id', $orderId);
		}else{
			$criteria->compare('created', $date);
		}
		return self::model()->findAll($criteria);
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
			'id' => 'Id',
			'status' => 'Status',
			'settle_date' => 'Settle Date',
			'created' => 'Created',
			'order_id' => 'Order',
			'meta' => 'Meta',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);

		$criteria->compare('status',$this->status);

		$criteria->compare('settle_date',$this->settle_date,true);

		$criteria->compare('created',$this->created,true);

		$criteria->compare('order_id',$this->order_id,true);

		$criteria->compare('meta',$this->meta,true);

		return new CActiveDataProvider('DayTimeRecord', array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @return DayTimeRecord the static model class
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
}