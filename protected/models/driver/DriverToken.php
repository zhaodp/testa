<?php

/**
 * @property integer $id
 * @property string $driver_id
 * @property string $authtoken
 * @property string $created
 */
class DriverToken extends CActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{driver_token}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
				array(
						'driver_id, authtoken, created',
						'required'
				),
				array(
						'driver_id',
						'length',
						'max'=>10
				),
				array(
						'authtoken',
						'length',
						'max'=>32
				),
				// The following rule is used by search().
				// Please remove those attributes that should not be searched.
				array(
						'driver_id, authtoken',
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
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
				'driver_id'=>'Driver',
				'authtoken'=>'Authtoken',
				'created'=>'Created'
		);
	}

    /**
     * @param $driver_id
     * 生成司管app的token验证
     */
    public function createManagerToken($driver_id){
        $created=time();
        $secret = 'driver_manager_app';
        $token = md5($driver_id.$secret.$created);
        $driver=DriverStatus::model()->get($driver_id);
        if($driver){
            //存入redis
            DriverStatus::model()->setDriverManagerToken($token,$driver_id);
            //存入db
            $task=array(
                'method'=>'driver_manager_token',
                'params'=>array(
                    'driver_id'=>$driver_id,
                    'token'=>$token,
                    'create_time'=>date(Yii::app()->params['formatDateTime'], $created)
                )
            );
            EdjLog::info('driverId='.$driver_id.'开始放入队列,生成driver_manager_token记录...');
            Queue::model()->putin($task, 'default');
            return $token;
        }
        return null;
    }

	/**
	 * 
	 * 登录成功的司机生成验证用token
	 * @param string $driver_id
	 * @param string $imei
	 * @param string $sim
	 */
	public function createAuthtoken($driver_id, $imei, $sim) {
		$created=time();
		$token=md5($driver_id.$imei.$sim.$created);

		$driver=DriverStatus::model()->get($driver_id);
		if ($driver) {
			//校验手机信息是否匹配
			if ($driver->validatePhone($driver_id, $imei, $sim)) {
				$driver->token=$token;
				$driver->heartbeat=$created;
				
				//添加task队列，入库
				$task=array(
						'method'=>'driver_token',
						'params'=>array(
								'driver_id'=>$driver_id,
								'authtoken'=>$token,
								'created'=>date(Yii::app()->params['formatDateTime'], $created)
						)
				);
				Queue::model()->dumplog($task);
				return $token;
			}
			else{
				EdjLog::info("validate phone fail driver_id $driver_id imei $imei sim $sim");
			}
		}
		else{
			EdjLog::info("get driver $driver_id fail");
		}
		return null;
	}

	/**
	 * 检查登录Token是否有效
	 * @param string $token
	 * @return boolean
	 */
	public function validateToken($token) {
		$driver = DriverStatus::model()->getByToken($token);
		if ($driver&&$driver->token!=null) {
			return true;
		}
		return false;
	}

	/**
	 * 通过Token获取司机工号
	 * @param string $token
	 * @return NULL
	 */
	public function getDriverIdByToken($token) {
		$driver = DriverStatus::model()->getByToken($token);
		
		if ($driver) {
			return $driver->driver_id;
		}
		return null;
	}

	/**
	 * 通过司机工号检查登录Token是否存在，存在则返回token
	 * @param string $driver_id
	 * @return boolean
	 */
	public function validateTokenByUser($driver_id) {
		$driver = DriverStatus::model()->get($driver_id);
		if ($driver) {
			return $driver->token;
		}
		return false;
	}
}
