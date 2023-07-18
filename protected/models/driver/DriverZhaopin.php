<?php

/**
 * This is the model class for table "{{driver_zhaopin}}".
 *
 * The followings are the available columns in table '{{driver_zhaopin}}':
 * @property integer $id
 * @property string $name
 * @property string $mobile
 * @property integer $city_id
 * @property integer $district_id
 * @property string $work_type
 * @property string $gender
 * @property integer $age
 * @property string $id_card
 * @property string $domicile
 * @property string $assure
 * @property string $marry
 * @property integer $political_status
 * @property integer $edu
 * @property string $pro
 * @property integer $driver_type
 * @property string $driver_card
 * @property integer $driver_year
 * @property string $driver_cars
 * @property string $contact
 * @property string $contact_phone
 * @property string $contact_relate
 * @property string $experience
 * @property integer $status
 * @property string $recycle
 * @property string $recycle_reason
 * @property string $ip
 * @property integer $ttime
 * @property integer $etime
 * @property integer $htime
 * @property integer $rtime
 * @property integer $ctime
 * @property integer $batch
 * @property integer $driver_id
 * @property integer $imei
 * @property integer $driver_phone
 * @property integer $noentry
 * @property integer $complete
 */

class DriverZhaopin extends CActiveRecord {
	public $zhaopin_status = array (
		'1'=>'已报名', 
		'2'=>'已通知培训', 
		'3'=>'已培训考核', 
		'4'=>'已签约',
		'5'=>'已激活');
	
	public $zhaopin_src = array(
		'0' => 'e代驾',
		'1' => '前程无忧',
		'2' => '智联',
		'3' => '58同城',
		'4' => '赶集网',
		'5' => '微博'		
	);
	public $verifyCode; //为User Model 设置一个新的属性		
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverZhaopin the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{driver_zhaopin}}';
	}
	
	/**
	 * @retur
	 * n array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'name, mobile, city_id, district_id, work_type, gender, age, id_card, domicile, assure, marry, political_status, edu, pro, driver_type, driver_card, driver_year, driver_cars, contact, contact_phone, contact_relate', 
				'required'), 
			array (
				'experience', 
				'length', 
				'max'=>1024), 
			array (
				'city_id, district_id, age, political_status, edu, driver_type, driver_year, status, ttime, etime, htime, rtime, ctime,batch', 
				'numerical', 
				'integerOnly'=>true), 
			array(
				'age', 
				'numerical',
				'integerOnly'=>true,
				'min'=>23,
				'max'=>55),
			array (
				'name, pro, contact', 
				'length', 
				'max'=>50), 
			array (
				'mobile,driver_phone', 
				'length', 
				'max'=>11), 
			array (
				'complete', 
				'length', 
				'max'=>4),
			array (
				'driver_id', 
				'length', 
				'max'=>10), 
			array (
				'work_type, gender, assure, marry, recycle, exam, noentry', 
				'length', 
				'max'=>1), 
			array (
				'id_card, driver_card, driver_cars, contact_phone, contact_relate', 
				'length', 
				'max'=>20), 
			array (
				'address, domicile, recycle_reason,imei', 
				'length', 
				'max'=>255), 
			array (
				'ip,src', 
				'length', 
				'max'=>15), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, name, mobile, city_id, src, district_id, work_type, gender, age, id_card, domicile, assure, marry, political_status, edu, pro, driver_type, driver_card, driver_year, driver_cars, contact, contact_phone, contact_relate, experience, status, recycle, recycle_reason, ip, ttime, etime, htime, rtime,ctime,batch,driver_id,driver_phone,imei,noentry,complete', 
				'safe', 
				'on'=>'search'), 
			array (
				'verifyCode', 
				'captcha', 
				'on'=>'insert', 
				'allowEmpty'=>!CCaptcha::checkRequirements(), 
				'message'=>'请输入正确的验证码'));
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array ();
	}
	
	public function checkData() {
		if ($this->findByAttributes(array (
			'id_card'=>$this->id_card))) {
			$this->addError('id_card', '此身份证号已经报过名');
			
			return false;
		}
		
		if (!$this->checkData2())
			return false;
		
		return true;
	}
	
	public function checkData2() {
		
		if ($this->city_id==0) {
			$this->addError('city_id', '请选择居住城市');
			return false;
		}
		if ($this->district_id==0) {
			$this->addError('district_id', '请选择居住区域');
			return false;
		}
		
		if ($this->age<18||$this->age>60) {
			$this->addError('age', '年龄必须在18岁到60岁之间');
			return false;
		}
		if ( (time() - $this->driver_year) < 157680000) {
			$this->addError('driver_year', '驾龄必须在5年以上');
			return false;
		}
		
		return true;
	}
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'id'=>'ID', 
			'name'=>'姓名', 
			'mobile'=>'手机号', 
			'gender'=>'性别', 
			'address'=>'居住详细地址',
			'age'=>'年龄', 
			'id_card'=>'身份证号', 
			'domicile'=>'户口所在地', 
			'assure'=>'是否需要担保', 
			'marry'=>'婚姻状况', 
			'political_status'=>'政治面貌', 
			'edu'=>'学历', 
			'pro'=>'专业', 
			'city_id'=>'居住城市', 
			'district_id'=>'居住区域', 
			'work_type'=>'工作方式', 
			'driver_type'=>'准驾车型', 
			'driver_card'=>'驾照号码', 
			'driver_year'=>'驾照申领日期', 
			'driver_cars'=>'熟练驾驶车型', 
			'contact'=>'紧急联系人姓名', 
			'contact_phone'=>'电话', 
			'contact_relate'=>'关系', 
			'experience'=>'代驾经验', 
			'status'=>'状态', 
			'recycle'=>'是否回收', 
			'recycle_reason'=>'回收原因', 
			'ip'=>'IP', 
			'ttime'=>'通知培训时间', 
			'etime'=>'已培训考核时间', 
			'htime'=>'签约时间',
			'rtime'=>'回收时间', 
			'ctime'=>'报名时间', 
			'batch'=>'批次', 
			'driver_phone'=>'司机工作号码', 
			'imei'=>'IMEI', 
			'driver_id'=>'司机工号',
			'noentry'=>'未签约原因',
			'complete'=>'是否完整');
	}
	
	public function setZhaopinSuccessStatus($id_card)
	{
		$criteria = new CDbCriteria();
		$criteria->addCondition("id_card='$id_card'");		
		$model = DriverZhaopin::Model()->find($criteria);
		if (!empty($model))
		{
			$dataZhaopin = $model->attributes;
			$status = $dataZhaopin['status'];
			$dataZhaopin['status'] = 4;
			$dataZhaopin['htime'] = time();
			$model->attributes = $dataZhaopin;
			$ret = $model->update();
		}
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
		$criteria->compare('name', $this->name, true);
		$criteria->compare('mobile', $this->mobile, true);
		
		if ($this->city_id==0) {
			$this->city_id = null;
		} elseif (Yii::app()->user->city!=0) {
			$this->city_id = Yii::app()->user->city;
		}
		
		$criteria->compare('city_id', $this->city_id);
		$criteria->compare('district_id', $this->district_id);
		$criteria->compare('work_type', $this->work_type, true);
		$criteria->compare('gender', $this->gender, true);
		$criteria->compare('age', $this->age);
		$criteria->compare('id_card', $this->id_card, true);
		$criteria->compare('domicile', $this->domicile, true);
		$criteria->compare('assure', $this->assure, true);
		$criteria->compare('political_status', $this->political_status);
		$criteria->compare('edu', $this->edu);
		$criteria->compare('pro', $this->pro, true);
		$criteria->compare('driver_type', $this->driver_type);
		$criteria->compare('driver_card', $this->driver_card, true);
		$criteria->compare('driver_year', $this->driver_year);
		$criteria->compare('driver_cars', $this->driver_cars, true);
		$criteria->compare('status', $this->status);
		$criteria->compare('recycle', $this->recycle, true);
		$criteria->compare('ttime', $this->ttime);
		$criteria->compare('etime', $this->etime);
		$criteria->compare('htime', $this->htime);
		$criteria->compare('rtime', $this->rtime);
		$criteria->compare('ctime', $this->ctime);
		$criteria->compare('batch', $this->batch);
		$criteria->compare('imei', $this->imei);
		$criteria->compare('driver_id', $this->driver_id);
		$criteria->compare('driver_phone', $this->driver_phone);
		$criteria->compare('noentry', $this->noentry);
		$criteria->compare('complete', $this->complete);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria));
	}
	
	public function searchData($data){
		$criteria = new CDbCriteria();
		$pages = $data ? $data['num'] : 50;
		$criteria->compare('city_id', Yii::app()->user->city);
//		$criteria->compare('status', 2);
		$criteria->compare('status', 1);
		$criteria->compare('recycle', 0);
		if($data&&$data['src']!=''){
			$criteria->compare('src', $data['src']);	
		}
		$criteria->order = 'id asc';
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>$pages)
		));
	}
	
	public function batchAdmin($data){
		$criteria = new CDbCriteria();
		$params = array();
		if(!empty($data['batch'])){
			$criteria->addCondition('batch = :batch');
			$params[':batch'] = $data['batch'];
		}
		if(!empty($data['noentry'])){
			$criteria->addCondition('noentry = :noentry');
			$params[':noentry'] = $data['noentry'];
		}
		if(!empty($data['name'])){
			$criteria->addCondition('name = :name');
			$params[':name'] = $data['name'];
		}
		if(!empty($data['driver_id'])){
			$criteria->addCondition('driver_id = :driver_id');
			$params[':driver_id'] = $data['driver_id'];
		}
		if(!empty($data['status'])){
			$criteria->addCondition('status = :status');
			$params[':status'] = $data['status'];
		}
		$criteria->params = $params;
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>100)
		));
	}
	
	
	public function sendSMS($data){
		$return = 0;
		if($data){
			$quest_id = explode(',', $data['id']);
			$zhaopin = new DriverZhaopin();
   	   		$criteria = new CDbCriteria();
   	   		$criteria->addInCondition('id', $quest_id);
   	   		$zhaopinList = $zhaopin->findAll($criteria);
   	   		
   	   		if($zhaopinList){
   	   			$num = 0;
   	   			$sms_message = $data['sms_content'];
	   	   		foreach($zhaopinList as $list){
	   	   			$phone = $list->mobile;
		   	   		Sms::SendSMS($phone, $sms_message);
					$zhaopin->updateAll(array('status'=>'2','batch'=>$data['batch'],'ttime'=>time()),'id = :id',array(':id'=>$list->id));
					$this->insertLog($list->id_card);
					$num ++;
				}
				if($num >0){
					$return = 1;
					$data['num'] = $num;
					$data['status'] = 1;
					DriverBatch::model()->updataEntryCount($data);
					DriverBatch::model()->updataStatus($data);
				}	
   	   		}
		}
		return $return;
	}
	
	public function driverZhaopinRecycle($batch){
		$Reduction = 0;
   	   	$recycle = 0;
		$zhaopin = new DriverZhaopin();
   	   	$zhaopinList = $zhaopin->findAll('batch = :batch and status = 2 and complete = 0',array(':batch'=>$batch));
   	   	if($zhaopinList){
   	   		foreach($zhaopinList as $list){
   	   			$phone = $list->mobile;
				if($this->smsLog($phone) < 2){
					$zhaopin->updateAll(array('status'=>'1','batch'=>0,'ttime'=>0),'id = :id',array(':id'=>$list->id));
					$this->insertLog($list->id_card);
					$Reduction++;
				}else{
			   	   	$zhaopin->updateAll(array('recycle'=>'1','recycle_reason'=>'通知两次都没有来培训','rtime'=>time()),'id = :id',array(':id'=>$list->id));
			   	   	$this->insertLog($list->id_card);
			   	   	$recycle++;
			   	}
   	   		}
   	   		$count = $Reduction+$recycle;
   	   		if($count >0){
   	   			DriverBatch::model()->updataEntryCount(array('batch'=>$batch,'num'=>-$count));
   	   		}
   	   	}
   	   	return array('Reduction'=>$Reduction,'recycle'=>$recycle);
	}
	
	public function smsLog($phone){
		$connection = Yii::app()->dbreport;
		$sql = "SELECT count(1) FROM t_sms_log WHERE receiver = $phone";
		
		$log = $connection->createCommand($sql)
					->queryScalar();
		return $log == FALSE ? 0 : $log;
	}
	
	/**
	 * 导出数据
	 */
	public function exportDriverZhaopin($batch){
	   $filename=$batch.'.csv';
	   header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
	   Header('Accept-Ranges: bytes');
	   header('Pragma: public');
	   header('Expires: 0');
	   header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	   header('Content-Disposition: attachment; filename="'.$filename.'"');
	   header('Content-Transfer-Encoding: binary');
	   $driverArr = array();
	   $driverArr['id'] = mb_convert_encoding('流水号','gb2312','UTF-8');
	   $driverArr['driver_name'] = mb_convert_encoding('姓名','gb2312','UTF-8');
	   $driverArr['phone'] = mb_convert_encoding('手机号','gb2312','UTF-8');
	   $driverArr['card'] = mb_convert_encoding('身份证号','gb2312','UTF-8');
	   $driverArr['driver_card'] = mb_convert_encoding('驾照号码','gb2312','UTF-8');
	   $driverArr['huji'] = mb_convert_encoding('户籍','gb2312','UTF-8'); 
	   $driverArr['addr'] = mb_convert_encoding('现住址','gb2312','UTF-8');
	   $driverArr['date'] = mb_convert_encoding('初领证日期','gb2312','UTF-8');
   	   echo implode(',', $driverArr)."\n";
   	   if($batch){
   	   		$zhaopin = new DriverZhaopin();
   	   		$criteria = new CDbCriteria();
   	   		$criteria->addCondition('batch = :batch');
   	   		$criteria->params = array(':batch'=>$batch);
   	   		$criteria->order = 'id asc';
   	   		$zhaopinList = $zhaopin->findAll($criteria);
   	   		
   	   		foreach($zhaopinList as $list){
   	   			$id_card = '\''.$list['id_card'];
   	   			echo $list['id'].',';
   	   			echo mb_convert_encoding($list['name'],'gb2312','UTF-8').',';
   	   			echo $list['mobile'].',';
   	   			echo $id_card.',';
   	   			echo '\''.mb_convert_encoding($list['driver_card'],'gb2312','UTF-8').',';
   	   			echo mb_convert_encoding($list['domicile'],'gb2312','UTF-8').',';
   	   			echo mb_convert_encoding($list['address'],'gb2312','UTF-8').',';
   	   			echo date('Y-m-d',$list['driver_year'])."\n";
   	   		}
   	   		DriverBatch::model()->updataStatus(array('batch'=>$batch,'status'=>2));
   	   }
	}
	
	/**
	 * 导入数据
	 */
	public function importDriverZhaopin($data){
		$model = DriverBackup::model()->find('user = :user' ,array(':user'=>$data['user']));
		
		if($model){
			if($model->user != $data['user'] || $model->phone != $data['phone']|| $model->imei != $data['imei']){
				$this->saveDriverBackup($model, $data);
			}
		}else{
			if($this->insertDriverBackup($data)){
				$DriverZhaopinPrefile = DriverZhaopin::model()->find('id_card = :id_card',array(':id_card'=>$data['id_card']));
				if($DriverZhaopinPrefile){
						$this->saveDriverZhaopin($DriverZhaopinPrefile, $data);
				}else{
					$this->insertDriverZhaopin($data);
					DriverBatch::model()->updataEntryCount(array('batch'=>$data['batch'],'num'=>1));
				}
			}
		}
	}
	
	public function insertDriverBackup($data){
		$model = new DriverBackup();
		$driverBackup = $model->attributes;
		$driverBackup['user'] = $data['user'];
		$driverBackup['name'] = $data['name'];
		$driverBackup['phone'] = $data['phone'];
		$driverBackup['ext_phone'] = $data['ext_phone'];
		$driverBackup['level'] = $data['level'];
		$driverBackup['status'] = $data['status'];
		$driverBackup['activate'] = $data['activate'];
		$driverBackup['entry_time'] = $data['entry_time'];
		$driverBackup['imei'] = $data['imei'];
		$driverBackup['address'] = $data['address'];
		$driverBackup['domicile'] = $data['domicile'];
		$driverBackup['license_time'] = $data['license_time'];
		$driverBackup['id_card'] = $data['id_card'];
		$model->attributes = $driverBackup;
		return $model->insert();
	}
	
	public function saveDriverBackup($model, $data){
		$driverBackup = $model->attributes;
		$driverBackup['user'] = $data['user'];
		$driverBackup['name'] = $data['name'];
		$driverBackup['phone'] = $data['phone'];
		$driverBackup['ext_phone'] = $data['ext_phone'];
		$driverBackup['level'] = $data['level'];
		$driverBackup['status'] = $data['status'];
		$driverBackup['activate'] = $data['activate'];
		$driverBackup['entry_time'] = $data['entry_time'];
		$driverBackup['imei'] = $data['imei'];
		$driverBackup['address'] = $data['address'];
		$driverBackup['domicile'] = $data['domicile'];
		$driverBackup['license_time'] = $data['license_time'];
		$driverBackup['id_card'] = $data['id_card'];
		$model->attributes = $driverBackup;
		$model->update();
	}
	
	public function saveDriverZhaopin($model,$data){
		$driverZhaopinInfo = $model->attributes;
		$city_id = Yii::app()->user->city;
		$prefix = Dict::item("city_prefix", $city_id);
		if(substr($data['user'],0,2) != $prefix){
			$driver_id = $prefix.$data['user'];
		}else{
			$driver_id = $data['user'];
		}
		$driverZhaopinInfo['name'] = $data['name'];
		$driverZhaopinInfo['driver_id'] = $driver_id;
		$driverZhaopinInfo['driver_phone'] = $data['phone'];
		$driverZhaopinInfo['imei'] = $data['imei'];
		$driverZhaopinInfo['status'] = $data['status'];
		$driverZhaopinInfo['complete'] = ($data['activate'] == '是') ? 0 : 1;
		$driverZhaopinInfo['mobile'] = $data['ext_phone'];
		$driverZhaopinInfo['id_card'] = $data['id_card'];
		$driverZhaopinInfo['driver_card'] = $data['id_card'];
		$driverZhaopinInfo['domicile'] = $data['domicile'];
		$driverZhaopinInfo['address'] = $data['address'];
		$driverZhaopinInfo['driver_year'] = $data['license_time'];
		$driverZhaopinInfo['etime'] = $data['status'] == 3 ? time() : 0;
		$driverZhaopinInfo['city_id'] = $city_id;
		$driverZhaopinInfo['batch'] = $data['batch'];
		$driverZhaopinInfo['ctime'] = time();
		$driverZhaopinInfo['ttime'] = time();
		$model->attributes = $driverZhaopinInfo;
		$model->update();
	}
	
	public function insertDriverZhaopin($data){
		$model = new DriverZhaopin();
		$driverZhaopinInfo = $model->attributes;
		$city_id = Yii::app()->user->city;
		$prefix = Dict::item("city_prefix", $city_id);
		if(substr($data['user'],0,2) != $prefix){
			$driver_id = $prefix.$data['user'];
		}else{
			$driver_id = $data['user'];
		}
		$driverZhaopinInfo['name'] = $data['name'];
		$driverZhaopinInfo['driver_id'] = $driver_id;
		$driverZhaopinInfo['driver_phone'] = $data['phone'];
		$driverZhaopinInfo['imei'] = $data['imei'];
		$driverZhaopinInfo['status'] = $data['status'];
		$driverZhaopinInfo['complete'] =  ($data['activate'] == '是') ? 0 : 1;
		$driverZhaopinInfo['mobile'] = $data['ext_phone'];
		$driverZhaopinInfo['id_card'] = $data['id_card'];
		$driverZhaopinInfo['driver_card'] = $data['id_card'];
		$driverZhaopinInfo['domicile'] = $data['domicile'];
		$driverZhaopinInfo['address'] = $data['address'];
		$driverZhaopinInfo['driver_year'] = $data['license_time'];
		$driverZhaopinInfo['etime'] = $data['status'] == 3 ? time() : 0;
		$driverZhaopinInfo['city_id'] = $city_id;
		$driverZhaopinInfo['batch'] = $data['batch'];
		$driverZhaopinInfo['ctime'] = time();
		$driverZhaopinInfo['ttime'] = time();
		$model->attributes = $driverZhaopinInfo;
		$model->insert();
	}
	
	/**
	 * 批量签约
	 */
	public function batchEntry($data){
		$return = 0; //失败
		$driverZhaopin = new DriverZhaopin();
		$criteria = new CDbCriteria();
   	   	$criteria->params = array(':batch'=>$data['batch']);
		if(isset($data['id'])){
   	   		$quest_id = explode(',', $data['id']);
   	   		$criteria->addInCondition('id', $quest_id);
   	   	}
   	   	$criteria->addCondition('status = 3');
   	   	$criteria->addCondition('batch = :batch');
   	   	$driverZhaopinList = $driverZhaopin->findAll($criteria);
   	   
		if($driverZhaopinList){
			$num = 0;
			foreach($driverZhaopinList as $list){
				if($list->imei && $list->driver_id){
					if(Employee::getActiveImei($list->imei)!='0'){
						if(!Driver::getProfile($list->driver_id)){
								$driver = new Driver();
								$driver_info = $driver->attributes;
								$year = date('Y-m-d') - date('Y-m-d',$list->driver_year);
								$driver_info['name'] = $list->name;
								$driver_info['domicile'] = $list->domicile;
								$driver_info['address'] = $list->address;
								$driver_info['id_card'] = $list->id_card;
								$driver_info['car_card'] = $list->driver_card;
								$driver_info['ext_phone'] = $list->mobile;
								$driver_info['year'] = $year;
								$driver_info['user'] = $list->driver_id;
								$driver_info['phone'] = $list->driver_phone;
								$driver_info['imei'] = $list->imei;
								$driver_info['city_id'] = $list->city_id;
								$driver_info['password'] = substr($list->id_card,-6,6);
								$driver->attributes = $driver_info;
								if ($driver->save()){
									if($list->noentry != 0){
										$driverZhaopin->updateAll(array('htime'=>time(),'status'=>4,'noentry'=>0),'status = 3 and id = :id',array(':id'=>$list->id));
									}else{
										$driverZhaopin->updateAll(array('htime'=>time(),'status'=>4),'status = 3 and id = :id',array(':id'=>$list->id));
									}
									DriverExt::model()->updateLicenseDate($list->driver_id,$list->driver_year);
									$this->insertLog($list->id_card);
									$num ++;
								}
						}else{
							$driverZhaopin->updateAll(array('noentry'=>2),'status = 3 and id = :id',array(':id'=>$list->id));
							$this->insertLog($list->id_card);
						}
					}else{
						$driverZhaopin->updateAll(array('noentry'=>1),'status = 3 and id = :id',array(':id'=>$list->id));
						$this->insertLog($list->id_card);
					}
				}
			}
			if($num>0){
				$return = $num; //成功
				DriverBatch::model()->updataEntrynum(array('num'=>$num,'batch'=>$data['batch']));
			}
			else
				$return = -1; //没有要修改的
		}
		echo $return;
	}
	
	/**
	 * 批量激活
	 */
	public function batchActivation($data){
		$return = 0;
		$driverZhaopin = new DriverZhaopin();
		$criteria = new CDbCriteria();
		$criteria->params = array(':batch'=>$data['batch']);
		if(isset($data['id'])){
   	   		$quest_id = explode(',', $data['id']);
   	   		$criteria->addInCondition('id', $quest_id);
   	   	}
   	   	$criteria->addCondition('status = 4');
   	   	$criteria->addCondition('complete = 0');
   	   	$criteria->addCondition('batch = :batch');
   	   	$driverZhaopinList = $driverZhaopin->findAll($criteria);
		if($driverZhaopinList){
			foreach($driverZhaopinList as $list){
				$id = $list->driver_id;
				$mark =Driver::MARK_ENABLE;
				$type = DriverLog::LOG_MARK_ENABLE;
				$reason = '新签约';
				if(Driver::block($id, $mark, $type, $reason)){
					$driverZhaopin->updateAll(array('status'=>5),'status = 4 and id = :id',array(':id'=>$list->id));
				}
//				$this->insertLog($list->id);
			}
			$return = 1;
		}
		return $return;
	}
	
	
	public function batchInfo($batch){
		$connection = Yii::app()->db;
		$sql = 'select SUM(IF((status = 4), 1, 0)) as entry,
				SUM(IF((status = 3), 1, 0)) as train,
				SUM(IF((status = 2), 1, 0)) as come 
				from t_driver_zhaopin where batch = :batch';
		$command = $connection->createCommand($sql);
		$command->params = array(':batch'=>$batch);
		$batchInfo = $command->queryRow();
		return $batchInfo;
	}
	
	/**
	 * 记录操作记录
	 */
	public function insertLog($id_card) {
		
		$operator = isset(Yii::app()->user) ? strtoupper(Yii::app()->user->getId()) : '系统自动操作';
		
		$zhaopin = new DriverZhaopin();
		$log = $zhaopin::model()->find('id_card=:id_card', array (
			':id_card'=>$id_card));
		$data = $log->attributes;
		$data['operator'] = $operator;
		$data['created'] = time();
		$connection = Yii::app()->dbstat
				->createCommand()
				->insert('t_driver_zhaopin_log', $data);
	}
}
