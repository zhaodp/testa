<?php

/**
 * This is the model class for table "data_summary".
 *
 * The followings are the available columns in table 'data_summary':
 * @property integer $id
 * @property string $channel
 * @property integer $orderCount
 * @property integer $customerCount
 * @property integer $inviteCount
 * @property string $date
 */
class DataSummary extends ThirdStageActiveRecord
{


	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'data_summary';
	}

    public function createInstance($attributes = array()){
        $model = new DataSummary();
        $model->attributes = $attributes;
        if(!$model->save()){
            EdjLog::info(json_encode($model->getErrors()));
            return false;
        }
        return true;
    }

    public function queryDetail($channel, $startTime, $endTime, $type = 1){
        $lastStartTime = $this->getPreTime($startTime, $type);
        $lastEndTime   = $this->getPreTime($endTime, $type);
        //get pre summary
        $preSummary = $this->queryByTime($lastStartTime, $lastEndTime, true, $channel);
        $preMap     = $this->listToMap('channel', $preSummary);
        //get current time
        $currentSummary = $this->queryByTime($startTime, $endTime, true, $channel);

        $currentMap = $this->listToMap('channel', $currentSummary);

        $ret = array();
        foreach($currentMap as $k => $v){
            $pre = isset($preMap[$k]) ? $preMap[$k] : new DataSummary();
            $ret[] = $this->merge($pre, $v, $startTime, $endTime);
        }
        return $ret;
    }

    /**
     * 把一个二维数组,转为一个指定字段为索引的map
     *
     * @param $column
     * @param array $list
     * @return array 可能抛出undefined index异常
     */
    public  function listToMap($column, $list = array()){
        if(empty($list)){
            return array();
        }
        $ret = array();
        foreach($list as $item){
            $index = $item[$column];
            $ret[$index]  = $item;
        }
        return $ret;
    }

    /**
     *
     * 加上总的概要
     *
     * @param $startTime
     * @param $endTime
     * @param int $type
     * @return mixed
     */
    public function querySummary($channel, $startTime, $endTime, $type = 1){
        $lastStartTime = $this->getPreTime($startTime, $type);
        $lastEndTime   = $this->getPreTime($endTime, $type);
        //get pre summary
        $preSummary = $this->queryByTime($lastStartTime, $lastEndTime, false,  $channel);

        //get current time
        $currentSummary = $this->queryByTime($startTime, $endTime, false, $channel);
        //merge
        $ret = $this->merge($preSummary[0], $currentSummary[0], $startTime, $endTime);
        $ret['channel'] = 'all';
        return $ret;
    }

    private function merge($pre, $current, $startTime, $endTime){
        $ret = $current->attributes;
        if(empty($pre['orderCount'])){
            $orderRate = 100;
        }else{
            $orderRate = (($current['orderCount'] - $pre['orderCount']) / $pre['orderCount']) * 100 ;
        }
        $ret['order_rate'] = bcsub($orderRate, 0, 2);
        $ret['startTime']  = $startTime;
        $ret['endTime']  = $endTime;
        return $ret;
    }


    public function queryByTime($startTime, $endTime, $byChannel = false , $channel = ''){
        $dateStart = date('Y-m-d', $startTime);
        $dateEnd   = date('Y-m-d', $endTime);
        $criteria  = new CDbCriteria();
//        $criteria->addBetweenCondition('date', $dateStart, $dateEnd);
        $criteria->addCondition('date >= :dateStart');
        $criteria->addCondition('date < :dateEnd');
        $criteria->params[':dateStart'] = $dateStart;
        $criteria->params[':dateEnd'] = $dateEnd;
        $criteria->select = 'sum(orderCount) as orderCount, sum(customerCount) as customerCount, sum(inviteCount) as inviteCount';
        if($byChannel){
            $criteria->select = $criteria->select.', channel';
            $criteria->group = 'channel';
        }
        if(!empty($channel)){
            $criteria->select = $criteria->select.', channel';
            $criteria->compare('channel', $channel);
        }
        return self::model()->findAll($criteria);
    }

    private function getPreTime($time, $type){
        return CalendarUtils::getPreTime($time, $type);
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('channel, date', 'required'),
			array('orderCount, customerCount, inviteCount', 'numerical', 'integerOnly'=>true),
			array('channel', 'length', 'max'=>20),
			array('date', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, channel, orderCount, customerCount, inviteCount, date', 'safe', 'on'=>'search'),
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
			'channel' => '渠道',
			'orderCount' => '订单数',
			'customerCount' => '用户数',
			'inviteCount' => '新客数',
			'date' => '日期',
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

		$criteria->compare('channel',$this->channel,true);

		$criteria->compare('orderCount',$this->orderCount);

		$criteria->compare('customerCount',$this->customerCount);

		$criteria->compare('inviteCount',$this->inviteCount);

		$criteria->compare('date',$this->date,true);

		return new CActiveDataProvider('DataSummary', array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @return DataSummary the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}