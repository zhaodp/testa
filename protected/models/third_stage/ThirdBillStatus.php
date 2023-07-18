<?php

/**
 * This is the model class for table "bill_status".
 *
 * The followings are the available columns in table 'bill_status':
 * @property integer $id
 * @property integer $userId
 * @property string $billMonth
 * @property integer $status
 * @property integer $payStatus
 * @property string $created
 */
class ThirdBillStatus extends ThirdStageActiveRecord
{
    /** 未结账 */
    const STATUS_UN_SETTLE = 0;

    /** 已付款 */
    const STATUS_SETTLED = 1;


    public static $STATUS_MAP  = array(
        self::STATUS_UN_SETTLE => '未结账',
        self::STATUS_SETTLED   => '已付款',
    );

    public function getStatus($channel, $billMonth){
        $model = $this->queryMonthBill($channel, $billMonth);
        return isset($model['payStatus']) ? $model['payStatus'] : ThirdBillStatus::STATUS_UN_SETTLE;
    }

    public function updateStatus($channel, $billMonth, $status){
        $model = $this->queryMonthBill($channel, $billMonth);
        if($model){
            $model->payStatus = $status;
            $status = $model->save();
            if(!$status){
                EdjLog::info(json_encode($model->getErrors()));
            }
            return $status;
        }
        return false;
    }

    public function createInstance($channel, $billMonth){
        $id = ThirdUser::model()->getUserIdByChannel($channel);
        $model = new ThirdBillStatus();
        $model->userId  = $id;
        $model->billMonth = $billMonth;
        $model->status = 0;
        $model->payStatus = self::STATUS_UN_SETTLE;
        $model->created = date('Y-m-d H:i:s');
        if(!$model->save()){
            EdjLog::info(json_encode($model->getErrors()));

        }
    }

    /**
     *
     * 查询一个 渠道某月的账单
     *
     * @param $channel
     * @param $billMonth
     * @return array|CActiveRecord|mixed|null
     */
    public function queryMonthBill($channel, $billMonth){
        $id = ThirdUser::model()->getUserIdByChannel($channel);
        $criteria = new CDbCriteria();
        $criteria->compare('userId', $id);
        $criteria->compare('billMonth', $billMonth);
        return self::model()->find($criteria);
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'bill_status';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userId, billMonth, status, payStatus, created', 'required'),
			array('userId, status, payStatus', 'numerical', 'integerOnly'=>true),
			array('billMonth', 'length', 'max'=>8),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, userId, billMonth, status, payStatus, created', 'safe', 'on'=>'search'),
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
			'billMonth' => 'Bill Month',
			'status' => 'Status',
			'payStatus' => 'Pay Status',
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

		$criteria->compare('billMonth',$this->billMonth,true);

		$criteria->compare('status',$this->status);

		$criteria->compare('payStatus',$this->payStatus);

		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider('ThirdBillStatus', array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @return ThirdBillStatus the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}