<?php

/**
 * This is the model class for table "t_activity_config".
 *
 * The followings are the available columns in table 't_activity_config':
 * @property integer $id
 * @property string $act_name
 * @property string $config
 */
class ActivityConfig extends CActiveRecord
{

    /**
     *
     * 初始化一个配置实例
     *
     * @param $activityName
     * @param $bonusSn
     * @param $sms
     * @param $endTime
     * @return bool
     */
    public function initInstance($activityName, $bonusSn, $sms, $endTime){
        $model = new ActivityConfig();
        $model->act_name = $activityName;
        $config = array();
        $config['bonus_sn'] = $bonusSn;
        $config['sms']  = $sms;
        $config['end_time'] = $endTime;
        $model->config = json_encode($config);
        if($model->save()){
            return true;
        }else{
            EdjLog::info('save activity config instance fail ---- '.json_encode($model->getErrors()));
        }
        return false;
    }

    /**
     *
     * 返回一个活动配置
     *
     * @param $activityName
     * @return array|mixed
     */
    public function getInstance($activityName){
        $config = RActivity::model()->getActivityConfig($activityName);
        if($config){
            return json_decode($config, true);
        }else{
            $config = $this->getActivityFromDb($activityName);
            if(!empty($config)){
                RActivity::model()->setActivityConfig($activityName, $config);
                return $config;
            }else{
                return $this->getInstance($activityName);//递归 ... be care
            }
        }
    }

    private function getActivityFromDb($activityName){
        $criteria = new CDbCriteria();
        $criteria->compare('act_name', $activityName);
        $criteria->select = 'config';
        $config = self::model()->find($criteria);
        if($config){
            return json_decode($config['config'], true);
        }else{
            return array();
        }
    }



	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 't_activity_config';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('act_name, config', 'required'),
			array('act_name', 'length', 'max'=>40),
			array('config', 'length', 'max'=>1024),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, act_name, config', 'safe', 'on'=>'search'),
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
			'act_name' => 'Act Name',
			'config' => 'Config',
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

		$criteria->compare('act_name',$this->act_name,true);

		$criteria->compare('config',$this->config,true);

		return new CActiveDataProvider('ActivityConfig', array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @return ActivityConfig the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function getDbConnection() {
        return Yii::app()->db_activity;
    }
}