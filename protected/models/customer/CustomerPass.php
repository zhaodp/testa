<?php

/**
 * This is the model class for table "{{customer_pass}}".
 *
 * The followings are the available columns in table '{{customer_pass}}':
 * @property integer $id
 * @property string $phone
 * @property string $passwd
 * @property string $expired
 */
class CustomerPass extends CActiveRecord
{
	/**
	 * 密码验证成功
	 */
	const CUSTOMERPASS_PASS = 0;
	/**
	 * 未进行预登录
	 */
	const CUSTOMERPASS_UNPERLOGIN = 1;
	/**
	 * 密码校验失败
	 */
	const CUSTOMERPASS_PASS_ERROR = 2;
	/**
	 * 密码已过期
	 */
	const CUSTOMERPASS_PASS_EXPIRED = 3;
	
	/**
	 * 密码有效期
	 */
	const PASSEXPIRED = 600;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CustomerPass the static model class
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
		return '{{customer_pass}}';
	}

	 /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('phone, passwd, expired, update_time, create_time', 'required'),
            array('expired, send_times', 'numerical', 'integerOnly'=>true),
            array('phone, passwd', 'length', 'max'=>32),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, phone, passwd, expired, send_times, update_time, create_time', 'safe', 'on'=>'search'),
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
            'phone' => 'Phone',
            'passwd' => 'Passwd',
            'expired' => 'Expired',
            'send_times' => 'Send Times',
            'update_time' => 'Update Time',
            'create_time' => 'Create Time',
        );
    }
	
	/**
	 * 
	 * 预登录成功的客户生成验证用pwd，并发送短信给客户
	 * @param string $phone
	 * @param string $udid
	 * 
	 * 修改短信发送内容，兼容发送验证码
	 * @author 李白阳
	 * @version 2013-04-19
	 * @param strint $type
	 */
	public function createPerLoginPasswd($phone, $udid, $macaddress, $device_type) {
		$attributes = array();
		$attributes['phone'] = trim($phone);
		$expired = time() + 60;
		$attributes['expired'] = $expired;
		$passwd = rand(1000, 9999);
		$attributes['passwd'] = $passwd;
		
		//一天最多发十次
		$requestNum = Yii::app()->db_readonly->createCommand()
				->select("count(1) as count")
				->from('t_customer_pass')
				->where('phone = :phone and FROM_UNIXTIME(expired, "%Y-%m-%d") = :expired',
							array(':phone' => $phone, ':expired' => date('Y-m-d')))
				->queryScalar();
		if ($requestNum <= 10){
			//查询 60秒内有没有重发
			$lastRequest = Yii::app()->db_readonly->createCommand()
				->select("count(1) as count")
				->from('t_customer_pass')
				->where('phone = :phone and expired > :expired',
							array(':phone' => $phone, ':expired' => time()))
				->queryScalar();
		
			if ($lastRequest == 0){
				$customerpass = new CustomerPass();
				$attributes['create_time'] = date('Y-m-d H:i:s');
				$customerpass->attributes = $attributes;
				if ($customerpass->save()) {
					$message = '您的预登录密码为：%s，预登录密码将在十分钟后失效。同一手机号码一天可申请三次预登录密码。';
					$content = sprintf($message, $passwd);
					
					$sms_ret = Sms::SendSMS($phone, $content);
					if ( $sms_ret ){
						//记录用户信息
						CustomerService::service()->initCustomer($phone);
					
						//记录设备号
						$attributes['mac_address'] = $macaddress;
						$attributes['udid'] = $udid;
						$attributes['device_type'] = $device_type;
						CustomerToken::model()->initCustomerToken($attributes);
						$ret = array (
								'code'=>0, 
								'message'=>'密码已成功发送。');
					}
				} else {
					$ret = array (
							'code'=>1, 
							'message'=>'系统延迟，请稍后再试。');
				}
			} else {
				$ret = array (
						'code'=>1,
						'message'=>'一分钟之内只能请求一次预登录密码。');
			}
		}else{
			$ret = array (
						'code'=>1,
						'message'=>'一天之内只能请求十次预登录密码。');
		}
		return $ret;
	}
	
	/**
	 * 
	 * 验证登录客户pwd
	 * @param string $phone
	 * @param string $passwd
	 * 
	 */
	public function validatePerLoginPasswd($phone, $passwd) {
		$customerpass = Yii::app()->db->createCommand()
								->select("*")
								->from("t_customer_pass")
								->where("phone = :phone and expired > :expired",
											array(':phone'=>$phone, ':expired' => time()))
								->queryRow();
		
		if (!$customerpass){
			return self::CUSTOMERPASS_UNPERLOGIN;
		} else {
			if (trim($customerpass['passwd']) != trim($passwd)){
				return self::CUSTOMERPASS_PASS_ERROR;
			} else {
				if ($customerpass['expired'] < time()){
					return self::CUSTOMERPASS_PASS_EXPIRED;
				} else {
					return self::CUSTOMERPASS_PASS;
				}
			}
		}
		
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
        $criteria->compare('phone',$this->phone,true);
        $criteria->compare('passwd',$this->passwd,true);
        $criteria->compare('expired',$this->expired);
        $criteria->compare('send_times',$this->send_times);
        $criteria->compare('update_time',$this->update_time,true);
        $criteria->compare('create_time',$this->create_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
	
	
	/**
	 * 获取用户当天最后一条记录
	 * Enter description here ...
	 * @param unknown_type $phone
	 */
	public function getCustomerPassLast($phone){
		return Yii::app()->db_readonly->createCommand()
						->select("send_times, update_time")
						->from("t_customer_pass")
						->where("phone = :phone and update_time >= :update_time",
                                array(":phone" => $phone, ":update_time" => date("Y-m-d 00:00:00")))
						->order("create_time desc")
						->queryRow();
	}
	
	
	/**
	 * 验证登陆密码
	 * @param string $phone
	 * @param string $passwd
	 * @return string $token
	 * @author 
	 */
	public static function validatePass($phone , $passwd) {
		$sql = "SELECT * FROM t_customer_pass WHERE phone = :phone AND expired > :time";
		$command = Yii::app()->db_readonly->createCommand($sql);
		$time = time();
		$command->bindParam(":phone" , $phone);
		$command->bindParam(":time" , $time);
		$result = $command->queryRow();
		$token = null;
		if (!empty($result)) {
			if (trim($passwd) == trim($result['passwd'])) {
				$token = CustomerToken::createAuthtoken($phone);
				return $token;
			}
		} else {
			return $token;
		}
	}
}