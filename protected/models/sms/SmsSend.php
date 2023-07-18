<?php

/**
 * This is the model class for table "{{sms_send}}".
 *
 * The followings are the available columns in table '{{sms_send}}':
 * @property integer $id
 * @property string $sender
 * @property string $receiver
 * @property string $message
 * @property integer $subcode
 * @property integer $sms_type
 * @property integer $sche_time
 * @property integer $status
 * @property integer $created
 * @property integer $order_id
 * @property string $driver_id
 * @property integer $order_status
 * @property string $imei
 */
class SmsSend extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SmsSend the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{sms_send}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'sender, receiver, message, sms_type, sche_time, status, created, order_id, driver_id, order_status, imei', 
				'required'
			), 
			array (
				'subcode, sms_type, sche_time, status, created', 
				'numerical', 
				'integerOnly'=>true
			), 
			array (
				'sender, receiver', 
				'length', 
				'max'=>20
			), 
			array (
				'message', 
				'length', 
				'max'=>256
			), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, sender, receiver, message, subcode, sms_type, sche_time, status, created, order_id, driver_id, order_status, imei', 
				'safe', 
				'on'=>'search'
			)
		);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array ();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'id'=>'ID', 
			'sender'=>'Sender', 
			'receiver'=>'Receiver', 
			'message'=>'Message', 
			'subcode'=>'Subcode', 
			'sms_type'=>'Type', 
			'sche_time'=>'Sche Time', 
			'status'=>'Status', 
			'created'=>'Created',
			'order_id'=>'订单ID',
			'driver_id'=>'司机工号', 
			'order_status'=>'订单状态', 
			'imei'=>'IMEI号'
		);
	}
	/**
     * 获取最后的短信附加码
	 * @author libaiyang 2013-04-27
	 *
	 */
	public static function getLastSubCode(){
		//Yii::app()->db_readonly->createCommand()->select('subcode')->from('t_sms_send')->order('id DESC')->limit(1)->queryScalar();
		//加入缓存，读取更新缓存数值 bidong 2013-05-23
        $last_subcode = 1;
        $cache_key='max_sms_subcode';
        $max_subcode= Yii::app()->cache->get($cache_key);
        if($max_subcode){
            $last_subcode=$max_subcode+1;
        }else{
            $last_subcode=Yii::app()->db_readonly->createCommand()->select('subcode')->from('t_sms_send')->order('id DESC')->limit(1)->queryScalar();
            $last_subcode++;
        }
        if($last_subcode >=Yii::app()->params['maxSmsEx']){
            $last_subcode=1;
		}

        Yii::app()->cache->set($cache_key,$last_subcode,86400);
		return $last_subcode;
	}
	/**
	 * @author libiayang 2013-04-27
	 * 保存短信发送数据
	 * @param array $data
	 */
	public function saveSmsLog($data){
        $ret=false;
		if(!empty($data)){
            if( empty($data['sender']) || empty($data['order_id']) || empty($data['driver_id']) || empty($data['message']) ){
                echo "订单资料不全\n";
                return $ret;
            }

			$data['subcode'] = self::getLastSubCode();
			$data['sche_time'] = '0000-00-00 00:00:00';
			$data['status'] = 0;
			$data['sms_type'] = $data['type'];
			$data['receiver'] = $data['sender'];
			$data['created'] = date('Y-m-d H:i:s');
			
			$model = new SmsSend();
			$model->attributes=$data;
            $model->send_num = isset($data['send_num']) ? intval($data['send_num']) : 1;
			$ret=$model->insert();
		}
        return $ret;
	}
	
	/**
	 * @author libaiyang
	 * 封装发送点评及询价短信方法
     * 改为只记录数据，再起一个JOB 循环发送
	 * @param array $data
	 * 增加最主要数据的判断
	 */
	public static function commentSmsEx($data){

		if(!empty($data)){
			if( $data['sender']==''||$data['order_id']==''||$data['driver_id']==''){
				echo "订单资料不全\n";
			}else{
				$smsLog = self::model()->saveSmsLog($data);
			}
		}
		return true;
	}
	/**
	 * 重新发送评价短信
	 */
	
	public static function sendSmsAgain($data){
		//重新发短信
		$message = '尊敬的客户您好，刚刚您对司机%s评价为差评%s分，请回复“1+原因“进行说明。如果刚刚评价有误，请回复”1至5+具体说明”重新评价。5最好，1最差。';
		$message = sprintf($message, $data['driver_id'], $data['level']);
		Sms::SendSMSEx($data['sender'], $message, $data['subcode']);
	}
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		

		$criteria = new CDbCriteria();
		
		$criteria->compare('id', $this->id);
		$criteria->compare('sender', $this->sender, true);
		$criteria->compare('receiver', $this->receiver);
		$criteria->compare('subcode', $this->subcode);
		$criteria->compare('sms_type', $this->sms_type);
		$criteria->compare('sche_time', $this->sche_time);
		$criteria->compare('status', $this->status);
		$criteria->compare('created', $this->created);
        $criteria->compare('driver_id', $this->driver_id);
        if (!isset($_GET['SmsSend'])) {
            $criteria->addCondition('unix_timestamp(created)+604800>=unix_timestamp()');
        }
        if(!empty($_GET['start_time'])&&!empty($_GET['end_time'])){
            $criteria->addBetweenCondition('date(created)', $_GET['start_time'], $_GET['end_time']);
        }

        $criteria->order='created desc';

		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>50,
            ),
		));
	}

    /**
     * 评价短信是否已发送
     * @param $phone
     * @param $driver_id
     * @param $order_id
     * @return bool
     */
    public function isSend($phone,$driver_id,$order_id){
        $flag=false;
        $count=0;
        $condition='receiver=:phone and driver_id=:driver_id and order_id=:order_id';
        $params=array(':phone'=>$phone,':driver_id'=>$driver_id,':order_id'=>$order_id);
        $count=self::model()->count($condition,$params);
        if($count>0){
            $flag=true;
        }

        return $flag;
    }

}