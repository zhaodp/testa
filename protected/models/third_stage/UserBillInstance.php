<?php

/**
 * This is the model class for table "user_bill_instance".
 *
 * The followings are the available columns in table 'user_bill_instance':
 * @property integer $id
 * @property integer $userId
 * @property string $startDate
 * @property string $meta
 */
class UserBillInstance extends ThirdStageActiveRecord
{
    public function getLatestActiveInstance($userId, $date = ''){
        if(empty($date)){
            $date = date('Y-m-d');
        }
        $criteria = new CDbCriteria();
        $criteria->compare('userId', $userId);
        $criteria->addCondition('startDate <= :date');
        $criteria->params[':date'] = $date;
        $criteria->order = 'id desc ';
        $criteria->limit = '1';
        return self::model()->find($criteria);
    }


    public function getLatestInstance($userId){
        $criteria = new CDbCriteria();
        $criteria->compare('userId', $userId);
        $criteria->order = 'id desc ';
        $criteria->limit = '1';
        return self::model()->find($criteria);
    }

    public function createInstance($userId, $date, $meta){
        $model = new UserBillInstance();
        $model->userId = $userId;
        $model->startDate = $date;
        $model->meta      = json_encode($meta);
        if($model->save()){
            return true;
        }else{
            EdjLog::info(json_encode($model->getErrors()));
        }
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_bill_instance';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userId, startDate, meta', 'required'),
			array('userId', 'numerical', 'integerOnly'=>true),
			array('meta', 'length', 'max'=>2048),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, userId, startDate, meta', 'safe', 'on'=>'search'),
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
			'startDate' => 'Start Date',
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

		$criteria->compare('userId',$this->userId);

		$criteria->compare('startDate',$this->startDate,true);

		$criteria->compare('meta',$this->meta,true);

		return new CActiveDataProvider('UserBillInstance', array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @return UserBillInstance the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}