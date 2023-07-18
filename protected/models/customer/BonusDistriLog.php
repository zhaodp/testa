<?php

/**
 * This is the model class for table "{{bonus_distri_log}}".
 *
 * The followings are the available columns in table '{{bonus_distri_log}}':
 * @property string $id
 * @property integer $channel
 * @property string $distri_by
 * @property string $creation
 * @property int $city_id
 */
class BonusDistriLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{bonus_distri_log}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('channel,city_id', 'numerical', 'integerOnly'=>true),
			array('distri_by', 'length', 'max'=>50),
			array('creation', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, channel, distri_by, creation, city_id', 'safe', 'on'=>'search'),
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
			'channel' => 'Channel',
			'distri_by' => 'Distri By',
			'creation' => 'Creation',
            'city_id' => 'City Id',
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
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('channel',$this->channel);
        $criteria->compare('city_id',$this->city_id);
		$criteria->compare('distri_by',$this->distri_by,true);
		$criteria->compare('creation',$this->creation,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return BonusDistriLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


    /**
     * 获取城市分配人列表
     * @param int $city_id
     * @return array
     * @auther zhangxiaoyin
     */
    public static function getCityDistri($city_id)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'disTri_by';
        if ($city_id != 0) {
            $criteria->addCondition('city_id=:city_id');
            $criteria->params[':city_id'] = $city_id;
        }

        self::$db = Yii::app()->db_readonly;
        $channel = self::model()->findAll($criteria);
        self::$db = Yii::app()->db;

        $result = array();

        foreach ($channel as $value) {
            array_push($result, $value['disTri_by']);
        }

        return $result;
    }

    /**
     * 获取分配人分配渠道列表
     * @param string $disTri_by
     * @return array
     * @auther zhangxiaoyin
     */
    public static function getChannelByDistriBy($disTri_by)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'channel';

        $criteria->addCondition('disTri_by=:disTri_by');
        $criteria->addCondition('channel is not null and channel!=0');
        $criteria->params[':disTri_by'] = $disTri_by;
        self::$db = Yii::app()->db_readonly;
        $channel = self::model()->findAll($criteria);
        self::$db = Yii::app()->db;

        $result = array();

        foreach ($channel as $value) {
            array_push($result, $value['channel']);
        }

        return $result;
    }


    /**
     * 获取分配人分配渠道列表
     * @param string $disTri_by
     * @return array
     * @auther zhangxiaoyin
     */
    public static function getChannelDistriCount($channel,$dateStart,$dateEnd)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'channel,COUNT(*) as distri_by';

        $criteria->addCondition('channel>0');
        $criteria->addInCondition('channel',$channel);
        if($dateStart!=''&&$dateEnd!=''){
            $criteria->addBetweenCondition("creation", $dateStart, $dateEnd);
        }
        $criteria->group ='channel';
        self::$db = Yii::app()->db_readonly;
        $channel = self::model()->findAll($criteria);
        self::$db = Yii::app()->db;

        $result = array();

        foreach ($channel as $value) {
            $result[$value['channel']]=$value['distri_by'];
        }

        return $result;
    }


    /**
     * 获取分配人分配渠道列表
     * @param string $disTri_by
     * @return array
     * @auther zhangxiaoyin
     */
    public static function getChannelDistriByCity($city_id)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'DISTINCT(distri_by)';

        $criteria->addCondition('city_id=:city_id');
        $criteria->params[':city_id']=$city_id;
        $criteria->addCondition('channel>0');
        self::$db = Yii::app()->db_readonly;
        $channel = self::model()->findAll($criteria);
        self::$db = Yii::app()->db;

        $result = array();

        foreach ($channel as $value) {
            $result[$value['distri_by']]=$value['distri_by'];
        }

        return $result;
    }

    /**
     * 获取渠道被分配数量
     * @param array $log_id
     * @return array
     * @auther mengtianxue
     */
    public static function getChannelDistri($log_id)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'id,creation';

        $criteria->addInCondition('id', $log_id);
        self::$db = Yii::app()->db_readonly;
        $channel = self::model()->findAll($criteria);
        self::$db = Yii::app()->db;

        $result = array();

        foreach ($channel as $value) {
            $result[$value['id']] = $value['creation'];
        }

        return $result;
    }
}
