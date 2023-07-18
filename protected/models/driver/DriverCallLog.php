<?php

/**
 * This is the model class for table "{{driver_call_log}}".
 *
 * The followings are the available columns in table '{{driver_call_log}}':
 * @property integer $id
 * @property string $driver_id
 * @property string $imei
 * @property string $simcard
 * @property string $phone
 * @property integer $type
 * @property string $longitude
 * @property string $latitude
 * @property integer $callTime
 * @property integer $endTime
 * @property integer $talkTime
 * @property integer $status
 * @property integer $created
 */
class DriverCallLog extends ReportActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverCallLog the static model class
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
		return '{{driver_call_log}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('driver_id, imei, simcard, phone, type, longitude, latitude, callTime, endTime, talkTime, status, created', 'required'),
			array('type, callTime, endTime, talkTime, status, created', 'numerical', 'integerOnly'=>true),
			array('driver_id', 'length', 'max'=>12),
			array('imei, simcard, phone, longitude, latitude', 'length', 'max'=>32),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, driver_id, imei, simcard, phone, type, longitude, latitude, callTime, endTime, talkTime, status, created', 'safe', 'on'=>'search'),
		);
	}

	public function insertCallLog($record = array()){
		//检查是否有重复通话记录已经上传
		$attributes=array(
				'callTime'=>$record['callTime'],
				'endTime'=>$record['endTime'],
				'phone'=>$record['phone'],
				'driver_id'=>$record['driver_id']
		);

		$ret = self::model()->findByAttributes($attributes);
		if($ret){
			echo '通话记录已经上传';
			return;
		}
		$driverCallLog = new DriverCallLog();
		$record['created'] = time();
		$driverCallLog->attributes = $record;
		$driverCallLog->save();
		//呼叫时司机状态为空闲则生成订单
		if($record['status']==0){

            //测试通过电话生成订单  开始
            //$driver_test_ids  = Common::getTestDriverIds();
            $driver_test_ids  = Common::getAutoTestDriverIds();
            $current_driver_id = strtoupper($record['driver_id']);
            if( in_array( $current_driver_id , $driver_test_ids ) ){

                //只针对于呼入,呼出,调试，先注释掉判断呼入呼出的判断。 add by sunhongjing
                //if( in_array( $record['type'] , array(0,1) ) ){
                    
                    /**
                     * 根据通话记录生成订单
                     * @author zhanglimin  2013-05-29
                     */
                    /* 注释掉使用新版的
                     * $task=array(
                        'method'=>'new_gen_order',
                        'params'=>array(
                            'driver_id'=>$record['driver_id'] ,
                            'phone'=>$record['phone'],
                            'call_time'=> $record['callTime'],
                            'type'=>$record['type'],
                        ),
                    );
                    Queue::model()->task($task);
                    */
                //}

            }else{
                $record['created'] = $record['callTime'];
                CallHistory::model()->insertCallHistory($record);
            }
            //测试通过电话生成订单  结束
		}else{
			echo '非空闲状态不生成订单。\n';
			return true;
		}
	}
	
	/**
	 * 读取指定时间段内的呼叫4次不同司机的电话号码
	 *
	 */
	public function getCrankCalls($section='600')
	{
		return true;
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
			'imei' => 'Imei',
			'simcard' => 'Simcard',
			'phone' => 'Phone',
			'type' => 'Type',
			'longitude' => 'Longitude',
			'latitude' => 'Latitude',
			'callTime' => 'Call Time',
			'endTime' => 'End Time',
			'talkTime' => 'Talk Time',
			'status' => 'Status',
			'created' => 'Created',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('driver_id',$this->driver_id);
		$criteria->compare('imei',$this->imei);
		$criteria->compare('simcard',$this->simcard);
		$criteria->compare('phone',$this->phone);
		$criteria->compare('type',$this->type);
		$criteria->compare('longitude',$this->longitude);
		$criteria->compare('latitude',$this->latitude);
		$criteria->compare('callTime',$this->callTime);
		$criteria->compare('endTime',$this->endTime);
		$criteria->compare('talkTime',$this->talkTime);
		$criteria->compare('status',$this->status);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
