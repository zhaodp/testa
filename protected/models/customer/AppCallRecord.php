<?php

/**
 * This is the model class for table "{{app_call_record}}".
 *
 * The followings are the available columns in table '{{app_call_record}}':
 * @property integer $id
 * @property string $udid
 * @property string $phone
 * @property string $driverID
 * @property string $device
 * @property string $os
 * @property string $version
 * @property string $longitude
 * @property string $latitude
 * @property integer $call_time
 * @property integer $created
 */
class AppCallRecord extends CActiveRecord
{
	//默认电话号码
	const DEFAUL_PHONE = '11111111111';
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AppCallRecord the static model class
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
		return '{{app_call_record}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('udid, phone, driverID, device, os, version, longitude, latitude, call_time, created', 'required'),
			array('call_time, created', 'numerical', 'integerOnly'=>true),
			array('udid, phone, longitude, latitude,macaddress', 'length', 'max'=>255),
			array('driverID, os, version', 'length', 'max'=>64),
			array('device', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, udid, phone, driverID, device, os, version, longitude, latitude, call_time, created', 'safe', 'on'=>'search'),
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
			'udid' => 'Udid',
			'macaddress' => 'MacAddress',
			'phone' => 'Phone',
			'driverID' => '司机工号',
			'device' => '设备',
			'os' => '平台',
			'version' => '版本',
			'longitude' => 'Longitude',
			'latitude' => 'Latitude',
			'call_time' => '呼叫时间',
			'created' => 'Created',
		);
	}
	
	public static function initRecord($udid, $macaddress, $phone = 0, $driverID, $device, $os, $version, $longitude, $latitude, $callTime){
		$appCallRecord = new AppCallRecord();
		
		$callTime = strtotime($callTime);

		$attr = array(
			'udid' => $udid,
			'macaddress'=> $macaddress,
			'phone' => $phone,
			'driverID' => $driverID,
			'device' => $device,
			'os' => $os,
			'version' => $version,
			'longitude' => $longitude,
			'latitude' => $latitude,
			'call_time' => $callTime,
			'created' => time(),
		);
		
		$appCallRecord->attributes = $attr;
		
		if ($appCallRecord->insert()){
			return true;
		} else {
			return FALSE;
		}
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($sort = NULL, $callTime = NULL)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('udid',$this->udid,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('driverID',$this->driverID,true);
		$criteria->compare('device',$this->device,true);
		$criteria->compare('os',$this->os,true);
		$criteria->compare('version',$this->version,true);
		$criteria->compare('longitude',$this->longitude,true);
		$criteria->compare('latitude',$this->latitude,true);
                if($callTime !== NULL){
                    $criteria->addBetweenCondition('call_time', $callTime[0], $callTime[1]);
                }
		$criteria->compare('created',$this->created);

                if($sort !== NULL){
                    $criteria->order = $sort;
                }
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
                    'pagination'=>array(
                        'pageSize'=>50,
                    ),
		));
	}

	public function getCountByTime($timeStart, $timeEnd){
		return self::model()->count("call_time between :timeStart and :timeEnd",
			array(':timeStart'=>$timeStart,
			':timeEnd' => $timeEnd));
	}

	public function getByOffsetAndLimit($timeStart, $timeEnd, $offset, $limit){
		if($limit > 10000){
			$limit = 10000;
		}
		$condition 	= "call_time between :timeStart and :timeEnd order by call_time asc limit :offset , :limit";
		$params 	= array(
			':timeStart'	=> $timeStart,
			':timeEnd' 		=> $timeEnd,
			':offset'		=> $offset,
			':limit'		=> $limit,
		);
		return self::model()->findAll($condition, $params);
	}

	public function getByTimeAndOS($timeStart, $timeEnd, $os){

		$condition 	= 'os like \'%'.$os.'%\'' .' and call_time between :timeStart and :timeEnd order by call_time desc';
		$params 	= array(
			':timeStart'	=> $timeStart,
			':timeEnd' 		=> $timeEnd,
		);
		return self::model()->findAll($condition, $params);
	}
	
	/**
	*获取各渠道呼入的订单总数和有效订单数和完成报单数
	*
	**/
	public function getEffectiveData($osArr, $begin_time, $end_time){
	    if(empty($osArr)){
		return array();
	    }
	    $where = 'os in (';
            foreach(array_keys($osArr) as $os){
                $where .= '\''.$os.'\''.',';
            }
	    $where = trim($where, ',').')';
	    $where .= ' and call_time>=:begin_time and call_time<=:end_time';
	    $command = Yii::app()->db_readonly->createCommand();
	    $data = $command->select('os,count(1) as sum,sum(case when order_id>0  then 1 else 0 end) as effective_order,sum(case when order_status=1  then 1 else 0 end) as success_order')
                                ->from('{{app_call_record}}')
                                ->where($where)
                                ->group('os')
                                ->queryAll(true,array('begin_time' => $begin_time, 'end_time' => $end_time));
            return $data;
	}

}
