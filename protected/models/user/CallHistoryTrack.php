<?php

/**
 * This is the model class for table "{{call_history_track}}".
 *
 * The followings are the available columns in table '{{call_history_track}}':
 * @property string $id
 * @property string $imei
 * @property integer $type
 * @property string $phone
 * @property integer $step
 * @property string $insert_time
 */
class CallHistoryTrack extends CActiveRecord
{

    const STEP_ONE = 1; //通过空闲验证
    const STEP_TWO = 2; //通过测试司机帐号验证
    const STEP_THREE = 3; //通过呼入/呼出验证
    const STEP_FOUR = 4;  //进入电话生成订单队列
    const STEP_FIVE = 5; //通过白名单不生成订单验证
    const STEP_SIX = 6; //通过排除公司司机的电话和备用电话验证
    const STEP_SEVEN = 7 ; //通过黑名单不生成订单验证
    const STEP_EIGHT = 8 ; //通过未报单验证
    const STEP_NINE = 9 ; //进入booking 方法


	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CallHistoryTrack the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{call_history_track}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('imei, phone, step, insert_time', 'required'),
			array('type, step', 'numerical', 'integerOnly'=>true),
			array('imei', 'length', 'max'=>100),
			array('phone', 'length', 'max'=>21),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, imei, type, phone, step, insert_time', 'safe', 'on'=>'search'),
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
			'imei' => 'Imei',
			'type' => 'Type',
			'phone' => 'Phone',
			'step' => 'Step',
			'insert_time' => 'Insert Time',
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
		$criteria->compare('type',$this->type);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('step',$this->step);
		$criteria->compare('insert_time',$this->insert_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * 插入上传电话追踪信息
     * @param array $params
     * @return bool
     */
    public function insertInfo($params = array()){
        if(empty($params)){
            return false;
        }
        $params['insert_time'] = date("Y-m-d H:i:s");
        $params['data'] = isset($params['data']) ? $params['data'] : '';
        $params['type'] = isset($params['type']) ? $params['type'] : 0;
        $params['call_time'] = date("Y-m-d H:i:s",$params['call_time']);
        return Yii::app()->db->createCommand()->insert('t_call_history_track',$params);
    }
}