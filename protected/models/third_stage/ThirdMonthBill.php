<?php

/**
 * This is the model class for table "date_bill".
 *
 * The followings are the available columns in table 'date_bill':
 * @property integer $id
 * @property integer $userId
 * @property integer $type
 * @property double $cast
 * @property integer $status
 * @property string $month
 * @property integer $created
 */
class ThirdMonthBill extends ThirdStageActiveRecord
{



    /** 不分成 */
    const BILL_TYPE_FRIEND = 0;
    /** 按报单数分成 */
    const BILL_TYPE_ORDER_SHARE = 1;
    /** 按新客报单数分成 */
    const BILL_TYPE_INVITE_ORDER = 2;
    /** 按流水金额分成 */
    const BILL_TYPE_INCOME_ORDER = 4;
    /** 按老客报单数分成 */
    const BILL_TYPE_OLD_ORDER  = 3;


    /**
     *
     * @param $timeStart
     * @param $timeEnd
     * @return CActiveRecord[]
     */
    public function queryBills($channel){
        $id = ThirdUser::model()->getUserIdByChannel($channel);
        $criteria  = new CDbCriteria();
//        $criteria->compare('month', $month);
        $criteria->compare('userId', $id);
        $criteria->order = 'month desc';
        $list = self::model()->findAll($criteria);
        return $this->convertBillToArrayDateProvider($list, $channel);
    }

    /**
     * @param $userId
     * @param $type
     * @param $cast
     * @param $month
     * @param int $created
     * @return bool
     */
    public function createInstance($userId, $type, $cast, $month, $created = 0){
        $model = new ThirdMonthBill();
        $model->userId = $userId;
        $model->type = $type;
        $model->cast = $cast;
        $model->month = $month;
//        $model->status = 0;
        if(0 == $created){
            $created = date('Y-m-d H:i:s');
        }
        $model->created = $created;

        $status = $model->save();
        if(!$status){
            EdjLog::info(json_encode($model->getErrors()));
        }
        return $status;
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'bill';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userId, type, cast, month, created', 'required'),
			array('userId, type', 'numerical', 'integerOnly'=>true),
			array('cast', 'numerical'),
			array('month', 'length', 'max'=>12),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, userId, type, cast, month, created', 'safe', 'on'=>'search'),
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
			'id' => 'Id',
			'userId' => 'User',
			'type' => 'Type',
			'cast' => 'Cast',
			'month' => 'month',
			'created' => 'Created',
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

		$criteria->compare('userId',$this->userId);

		$criteria->compare('type',$this->type);

		$criteria->compare('cast',$this->cast);

		$criteria->compare('status',$this->status);

		$criteria->compare('month',$this->month,true);

		$criteria->compare('created',$this->created);

		return new CActiveDataProvider('ThirdMonthBill', array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @return ThirdMonthBill the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}