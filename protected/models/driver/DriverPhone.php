<?php

/**
 * This is the model class for table "{{driver_phone}}".
 *
 * The followings are the available columns in table '{{driver_phone}}':
 * @property string $driver_id
 * @property string $imei
 * @property string $simcard
 * @property string $phone
 * @property integer $is_bind
 * @property integer $sort
 */
class DriverPhone extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverPhone the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	public $sort = 0;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{driver_phone}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('driver_id, imei, simcard', 'required'),
			array('is_bind', 'numerical', 'integerOnly'=>true),
			array('driver_id', 'length', 'max'=>10),
			array('imei, simcard', 'length', 'max'=>32),
			array('phone', 'length', 'max'=>15),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('driver_id, imei, simcard, phone, is_bind, device', 'safe', 'on'=>'search'),
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
	
	public function validateDriverPhone($imei, $sim, $driver_id) {
		$driverPhone = DriverPhone::model()->find('driver_id=:driver_id and imei=:imei and simcard=:sim', 
			array(':driver_id'=>$driver_id, ':imei'=>$imei, ':sim'=>$sim));
			
		if ($driverPhone){
			return $driverPhone;
		} else {
			return false;
		}
	}
	
	public function registerDriverPhone($imei, $sim , $phone, $device, $driver_app_ver=''){
        $ret=array();
		$driverPhone = DriverPhone::model()->find('imei=:imei and simcard=:sim', array(':imei'=>$imei, ':sim'=>$sim));
		if (!is_null($driverPhone)){
            /*
             * by zhanglimin at 2013-04-25
             * 对于已经注册的用户 当driver_id 首字母以V开头时，返回当前driver_id 否则返回空
             */
            $user = substr(strtoupper($driverPhone->driver_id),0,1) == "V" ? $driverPhone->driver_id : "";
			//add by aiguoxin 版本大于等于2.5.0直接返回司机工号，客户端填充
			$app_ver=DriverStatus::model()->app_ver($driverPhone->driver_id);
			if($driver_app_ver && $driver_app_ver>='2.5.0'){
				$user=$driverPhone->driver_id;
			}
			//更新司机版本号，保证服务器端是最新的，防止司机端降级，新功能不能用(永峰)
			if($driver_app_ver && ($app_ver!=$driver_app_ver)){//司机端和服务器版本不一致，更新服务器
				$driver=DriverStatus::model()->get($driverPhone->driver_id);
				if($driver){
					DriverStatus::model()->set_app_ver($driverPhone->driver_id,$driver_app_ver);
				}
			}
			//更新司机版本号
			$ret = array(
				'code'=>1,
                'user'=>$user,
				'message'=>'系统中已存在');
		} else {
            //获取最大的V 号，最大 V 号+1 作为新上报driver_id
			$criteria = new CDbCriteria();
			$criteria->select = "REPLACE( driver_id, 'V', '' ) AS driver_id";
			$criteria->condition = "LEFT(driver_id, 1) = 'V'";
			$criteria->order = 'LENGTH( driver_id ) DESC , driver_id DESC';
			
			$lastVisiter = DriverPhone::model()->find($criteria);
			if ($lastVisiter) {
				$visiterId = $lastVisiter->driver_id;
				$visiterId++;
				$visiterName = 'V' . $visiterId;
			} else {
				$visiterName = 'V1';
			}

            //保存数据到 driverPhone  bidong up 2013-7-4
			$newDriver = new DriverPhone();
            $newDriver->unsetAttributes();
            $newDriver->driver_id=$visiterName;
            $newDriver->imei=$imei;
            $newDriver->simcard=$sim;
            $newDriver->device = $device;
			if (!empty($phone)) {
                $newDriver->phone=$phone;
			}
            $succ=$newDriver->save();

			if ($succ){
				$employee = Employee::model()->find('imei=:imei', array(':imei'=>$imei));
				if (!$employee){
					$employee = new Employee();
					$attributes['imei'] = $imei;
					$employee->attributes = $attributes;
					$employee->insert();
				}
				$ret = array(
					'code'=>0,
					'user'=>$visiterName,
					'is_bind'=>0,
					'message'=>'注册成功');
			}else{
				$ret = array(
					'code'=>2, 
					'message'=>'系统注册失败');
			}
		}
		
		return $ret;
	}
	
	public function getDriverPhone($driver_id){
		$driverPhone = DriverPhone::model()->find('driver_id=:driver_id', 
			array(':driver_id'=>$driver_id));
			
		if (!$driverPhone){
			$driverPhone = new DriverPhone();
		}
		return $driverPhone;
	}
	
	public function getBindDriverList(){
		$drivers = Yii::app()->db_readonly->createCommand()
				->select('driver_id')
				->from('t_driver_phone')
				->where('is_bind = 1')
				//->text;
				->queryAll();
		$driverList = array();
		foreach ($drivers as $driver){
			$driverList[] = $driver['driver_id']; 
		}
		return $driverList;
	}
	
	public function existsBindDriver($driver_id){
		$driver = Yii::app()->db_readonly->createCommand()
				->select('driver_id')
				->from('t_driver_phone')
				->where(array('AND', 'is_bind = 1', 'driver_id=:driver_id'), array(':driver_id'=>$driver_id))
				->queryRow();
		if ($driver){
			return 'Andriod';
		} else {
			return 'MTK';
		}
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'driver_id' => '司机工号',
			'imei' => 'Imei',
			'simcard' => 'SIM卡号',
			'phone' => '手机号',
			'is_bind' => '是否绑定',
            'device' => '手机类型',
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
		
		$criteria = new CDbCriteria ();
		$criteria->select = "driver_id, phone, imei, simcard, is_bind,CASE WHEN LEFT(driver_id, 1) = 'V' THEN 1 ELSE 0 END AS sort";$criteria->select = "driver_id, phone, imei, simcard, is_bind,CASE WHEN LEFT(driver_id, 1) = 'V' THEN 1 ELSE 0 END AS sort";
		$user_city_id = Yii::app()->user->city;
        if (isset ( $_GET ['DriverPhone'] )) {
			$params = array ();
			if ($_GET ['DriverPhone'] ['driver_id']) {
				$criteria->addCondition ( 'driver_id = :driver_id' );
				$params [':driver_id'] = $_GET ['DriverPhone'] ['driver_id'];
			}
			if ($_GET ['DriverPhone'] ['phone']) {
				$criteria->addCondition ( 'phone = :phone' );
				$params [':phone'] = $_GET ['DriverPhone'] ['phone'];
			}
			if ($_GET ['DriverPhone'] ['imei']) {
				$criteria->addCondition('imei = :imei');
				$params[':imei'] = $_GET ['DriverPhone'] ['imei'];
			}
			if ($_GET ['DriverPhone'] ['simcard']) {
				$criteria->addCondition('simcard = :simcard');
				$params['simcard'] = $_GET ['DriverPhone'] ['simcard'];
			}
			if (! empty ( $params )) {
				$criteria->params = $params;
			}
		}
        if ($user_city_id != 0) {
            $city = Dict::items('city_prefix');
            $city_prefix = $city[$user_city_id];
            $criteria->addCondition("LEFT(driver_id, 1) = 'V' OR LEFT(driver_id, 2)='{$city_prefix}'");
        }
		$criteria->order = 'sort desc,is_bind desc';
		return new CActiveDataProvider ( $this, array (
			'criteria' => $criteria,
			'pagination'=>array (
				'pageSize'=>30
			)
		) );
	}
	
	/**
	 * ajaxDriverPhone 修改driverPhone信息
     * 不用了
	 */
	public function ajaxDriverPhone($data){
		$return = false;
		$model = new DriverPhone ();
		$driver = Driver::getProfile($data['driver_id']);
		$driverPhone = $model->find ( 'driver_id = :driver_id', 
												array (':driver_id' => $data['driver_id'] ) );
		if ($driver && !$driverPhone) {
			$return = $this->updateDriver($data);
		}else{
			if($driverPhone){
				$driverPhone_data = $driverPhone->attributes;
				$driverPhone_data['driver_id'] = 'P'.$driverPhone_data['driver_id'];
				$driverPhone_data['is_bind'] = 0;
				$driverPhone->attributes = $driverPhone_data;
				if($driverPhone->save()){
					$return = $this->updateDriver($data);
				}
			}
		}
		return $return;
	}
	
	public function updateDriver($data, $imei_old = null){
		$return = false;
		$model = new DriverPhone ();
        if ($imei_old['0'] == 'V') {
            //删除已存在的工号
            $model->deleteAll('driver_id = :driver_id', array(':driver_id' => $data['driver_id']));
        }

		$count = $model->updateAll ( array ('driver_id' => $data['driver_id'], 'phone' => $data['phone'],'is_bind' => 1 ), 
										'imei = :imei and simcard = :simcard', 
										array (':imei' => $data['imei'], ':simcard' => $data['simcard'] ));
		if ($count > 0) {
			$driver = Driver::getProfile($data['driver_id']);
			$current_imei = $driver->imei;
			//修改Driver imei 和 电话号码
			$driver_data = $driver->attributes;
			$driver_data['imei'] = $data['imei'];
			$driver_data['phone'] = $data['phone'];
			$driver->attributes = $driver_data;
			
			if($driver->save()) {
				Comments::model()->updateEmployeeID($current_imei, $data['imei']);
				$return = true; //修改成功
			}
		}
		return $return;
	}
	
	public function updateDriverPhone($data, $imei_old){
		$return = false;
		$model = new DriverPhone ();
        if($imei_old['0'] == 'V'){
            //删除已存在的工号
            $model->deleteAll('driver_id = :driver_id',array(':driver_id' => $data['driver_id']));
        }
        //修改当前信息
		$count = $model->updateAll ( array ('driver_id' => $data['driver_id'], 'phone' => $data['phone'],'is_bind' => 0 ),
										'imei = :imei and simcard = :simcard', 
										array (':imei' => $data['imei'], ':simcard' => $data['simcard'] ));
		if($count > 0){
			$return = true;
		}
		return $return;
	}

    /**
     * 判断imei是否被绑定
     * @author zhangtingyi
     * @param int imei
     * @return bool
     */
    public function checkBindByImei($imei) {
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('COUNT(*)');
        $command->from('t_driver_phone');
        $command->where('imei=:imei', array(':imei'=>$imei));
        $num = $command->queryScalar();
        return $num ? true : false;
    }

    /**
     * 通过V号获得IMEI
     * @param $v_code
     * @return mixed
     */
    public function getImeiByVCode($v_code) {
        $info = $command = Yii::app()->db_readonly->createCommand()
            ->select('imei')
            ->from('t_driver_phone')
            ->where('driver_id=:driver_id', array(':driver_id'=>$v_code))
            ->queryScalar();
        return $info;
    }

    public function getPhoneInfoByVCode($v_code) {
        $info = $command = Yii::app()->db_readonly->createCommand()
            ->select('*')
            ->from('t_driver_phone')
            ->where('driver_id=:driver_id', array(':driver_id'=>$v_code))
            ->queryRow();
        return $info;
    }

    /**
     * @param $vNum
     * @param $driverId
     * @param $driverPhone
     * 司机入职，用工号替换v号信息
     */
    public function replaceDriverInfo($vNum,$driverId,$phone){
        $driver_phone_model = new DriverPhone();
        //v号存在的，才完善对应信息
        if($vNum){
            //查看V号对应的IMEI
            $driverPhone = $driver_phone_model->find('driver_id=:driver_id', array(':driver_id' => strtoupper($vNum)));
            if ($driverPhone) {
                $data['imei'] = $driverPhone['imei'];
                //删除已有的工号，保证工号唯一性
                DriverPhone::model()->deleteAll('driver_id= :driver_id', array(':driver_id' => $driverId));
                $dataDriverPhone = $driverPhone->attributes;
                $dataDriverPhone['driver_id'] = $driverId; //用工号替换V号
                $dataDriverPhone['phone'] = $phone;
                $dataDriverPhone['is_bind'] = 1;
                $driverPhone->attributes = $dataDriverPhone;
                $driverPhone->save(false);
            }
        }
    }
}