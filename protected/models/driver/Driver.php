<?php

/**
 * This is the model class for table "{{driver}}".
 *
 * The followings are the available columns in table '{{driver}}':
 * @property integer $id
 * @property integer $city_id
 * @property string $user
 * @property string $name
 * @property integer $gender
 * @property string $picture
 * @property string $phone
 * @property string $domicile
 * @property string $id_card
 * @property string $car_card
 * @property integer $year
 * @property integer $level
 * @property integer $mark
 * @property integer $block_at
 * @property integer $block_mt
 * @property string $password
 * @property string $ext_phone
 * @property string $imei
 * @property string $license_date
 * @property string $rank
 * @property string $recommender
 * @property string $crated
 */
class Driver extends CActiveRecord
{

    /**
     * 有效司机
     */
    const MARK_ENABLE = 0;
    /**
     * 已屏蔽的司机
     */
    const MARK_DISNABLE = 1;
    /**
     * 已换手机的司机
     */
    const MARK_CHANGE = 2;

    /**
     * 解约的司机
     */
    const MARK_LEAVE = 3;

    //测试账号
    const IS_TEST_TRUE = 1;

    //普通账号
    const IS_TEST_FALSE = 0;

    //返程车
    const IS_TEST_RETRUN = 2;

    //司管
    const IS_DRIVER_MANAGER =1;//1是司管，0不是

    //司机支持的业务，目前有代驾、洗车,共32位...............................1
    const SERVICE_TYPE_FOR_DAIJIA = '00000000000000000000000000000001'; //代驾
    const SERVICE_TYPE_FOR_XICHE  = '00000000000000000000000000000010'; //洗车

    public static $assure_dict = array(
        0 => '担保待定',
        5 => '无需担保',
        6 => '担保金',
        7 => '担保人',
        8 => '未担保',
        9 => '暂住证',
    );

    public static $error_msg = array(
        '1001' => '参数错误',
        '1002' => '司机状态不符合要求或无该司机信息',
        '1003' => '该工号已经存在',
        '1004' => 'V号有误',
        '1005' => '该IMEI已经存在',
        '1006' => '该身份证号已经签约',
        '1007' => '数据写入失败',
        '1008' => '请输入工号',
        '1009' => '状态更新出错',
        '1010' => '日记记录出错',
        '1012' => '请先完善司机信息',
        '1011' => '手机号码格式有误',
        '1013' => '请输入V号'
    );

    const PICTURE_HOST = 'http://pic.edaijia.cn/';

    CONST PICTURE_SMALL = 'small'; //缩略图&水印 限定宽度(117px)，高度自适应 ，质量: 95 + 锐化

    CONST PICTURE_MIDDLE = 'middle'; //限定宽度(156px)，高度自适应 ，质量: 95 + 锐化

    CONST PICTURE_NORMAL = 'normal'; //限定宽度(544px)，高度自适应 ，质量: 95 + 锐化

    CONST PICTURE_BOX = 'box'; //限定宽度(300px)，限定高度(300px)，放大和裁剪图片 ，质量: 95 + 锐化

    CONST PICTURE_SBOX = 'sbox'; //限定宽度(80px)，限定高度(80px)，放大和裁剪图片 ，质量: 95 + 锐化

    CONST PICTURE_SMALLA = 'smalla'; //限定宽度(117px)，高度自适应 ，质量: 85 + 锐化

    public $is_android = false;

    /**
     * 看一个电话是否有对应的司机
     *
     * @param $phone
     * @return bool
     */
    public function isDriver($phone){
        $criteria = new CDbCriteria();
        $criteria->compare('phone', $phone);
        $criteria->compare('ext_phone', $phone, false, 'OR');
        $driver = self::model()->find($criteria);
        return $driver ? true : false;
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Driver the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'user, name, phone, id_card, car_card',
                'required'),
            array(
                'city_id, driver_manager,id_driver_card, year, mark, block_at, block_mt, gender,is_test',
                'numerical',
                'integerOnly' => true),
            array(
                'name, domicile, password, ext_phone, imei,address',
                'length',
                'max' => 255),
            array(
                'user,rank,recommender',
                'length',
                'max' => 10),
            array(
                'picture',
                'length',
                'max' => 1024),
            array(
                'phone, id_card, license_date',
                'length',
                'max' => 20),
            array(
                'car_card',
                'length',
                'max' => 50),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array(
                'id, city_id, driver_manager, user, name, phone, domicile, id_card, car_card, year, level, mark, block_at, block_mt, password, ext_phone, gender, license_date, rank, recommender, register_city, service_type',
                'safe',
                'on' => 'search'));
    }


    /**
     * 获取司机状态
     */
    public static function getMark($mark = ''){
        $mark_array = array(
            self::MARK_ENABLE=>'正常',
            self::MARK_DISNABLE => '屏蔽',
            self::MARK_LEAVE => '离职',
        );
        if($mark !== ''){
            if(isset($mark_array[$mark]))
                return $mark_array[$mark];
            else return false;

        }
        return $mark_array;
    }

    /**
     *
     * 屏蔽司机
     * @param 司机工号 $user
     * @param 屏蔽激活 $mark
     * @param 屏蔽类型 $type
     * @param 屏蔽原因 $reason
     * @param $is_auto 是否欠费屏蔽
     * @param $enable_auto 是否自动解除屏蔽
     */
    public function block($user, $mark, $type, $reason, $is_auto = false, $is_system= false, $enable_auto= false)
    {
        //修改司机信息
        $attr = array();
        $driver = Driver::getProfile($user);
        if ($driver === null)
            return -1;

        $attr['imei'] = $driver->imei;

        $block_at = $driver->block_at;
        $block_mt = $driver->block_mt;
        EdjLog::info('block...'.$user.',mark='.$mark.',is_auto='.$is_auto.',is_system='.$is_system.',block_at='.$block_at.',block_mt='.$block_mt);
        $status = ''; //屏蔽或解约需要更改司机状态为下班
        switch ($mark) {
            case Driver::MARK_DISNABLE : //屏蔽
                $message = '%s师傅，您已被屏蔽，原因：%s。';

                if ($is_auto) {
                    $block_at = 1;
                } else {
                    //changed by aiguoxin
                    if($is_system){
                        if ($block_mt == 0) {
                            $block_mt = 2;
                        }elseif ($block_mt == 1) {
                            $block_mt = 3;
                        }elseif ($block_mt == 2 || $block_mt == 3) {
                            //do nothing,keep no change
                        }
                    }else{
                        if($block_mt==0){
                            $block_mt = 1;
                        }elseif ($block_mt == 1 || $block_mt == 3) {
                            //do nothing,keep no change
                        }elseif ($block_mt == 2) {
                            $block_mt = 3;
                        }
                    }
                }

                $attr['mark'] = $mark;
                $status = 1;
                EdjLog::info('enable...'.$user.',mark='.$mark.',is_auto='.$is_auto.',is_system='.$is_system.',block_at='.$block_at.',block_mt='.$block_mt);
                break;
            case Driver::MARK_ENABLE : //激活
                $message = '%s师傅，您的状态已调整为正常，原因：%s，请重新登录司机端。';


                if ($is_auto) {
                    $block_at = 0;
                } else {
                    if($enable_auto){//如果是自动脚本解除屏蔽，则不区分手动屏蔽和扣分屏蔽
                        $block_mt = 0;
                    }else{
                        if($is_system){
                            if ($block_mt == 1) {
                                //do nothing,keep no change
                            }elseif ($block_mt == 2) {
                                $block_mt = 0;
                            }elseif ($block_mt == 3) {
                                $block_mt = 1;
                            }
                        }else{
                            if($block_mt==1){
                                $block_mt = 0;
                            }elseif ($block_mt == 2) {
                                //do nothing,keep no change
                            }elseif ($block_mt == 3) {
                                $block_mt = 2;
                            }
                        }
                    }
                }

                if ($block_at == $block_mt && $block_at == 0) {
                    $attr['mark'] = $mark;
                } else {
                    $attr['mark'] = $driver->mark;
                }
                EdjLog::info('enable...'.$user.',mark='.$mark.',is_auto='.$is_auto.',is_system='.$is_system.',block_at='.$block_at.',block_mt='.$block_mt);
                break;
            case Driver::MARK_LEAVE : //解约
                $message = '%s师傅，您已解约。';
                $attr['mark'] = $mark;
                $status = 1;
                break;
        }
        $attr['block_at'] = $block_at;
        $attr['block_mt'] = $block_mt;
        $last_mark = $driver->mark;

        $driver->attributes = $attr;
        if ($driver->save(false)) {
            //屏蔽后，主动清除司机登录token bidong 2013-08-14
            $driverStatus = DriverStatus::model()->get($driver->user);

            //兼容2.4.0之前版本，如果低于这个版本，非扣费屏蔽，需要重登录
            $app_ver = DriverStatus::model()->app_ver($driver->user);
            if($mark == Driver::MARK_DISNABLE && $block_mt != 0){
                if(empty($app_ver) || $app_ver<'2.4.0'){
                    $driverStatus->token = '';
                }
            }

            if($mark == Driver::MARK_DISNABLE && $block_mt == 1){
                $driverStatus->token = '';
            }
            //系统解除屏蔽,显赫说要重新登录
            if($is_system && $mark == Driver::MARK_ENABLE){
                $driverStatus->token = '';
            }

            $driverStatus->block_at = $block_at;
            $driverStatus->block_mt = $block_mt;
            $driverStatus->mark = $mark;
            if($block_mt !=0 || $block_at !=0){ //aiguoxin 2014-08-22 兼容司机解除手动屏蔽，但是还有系统屏蔽等情况
                $driverStatus->mark = Driver::MARK_DISNABLE;
            }

            if($status)  {
                $driverStatus->status = DriverPosition::POSITION_GETOFF;
                DriverPosition::model()->updateStatus($driver->user,DriverPosition::POSITION_GETOFF);
            }


            if ($mark == Driver::MARK_LEAVE) {
                Employee::model()->updateChangedIMEI($attr['mark']);
            }
            $ext = DriverExt::model()->getExt($user);
            $attrs = array(
                'mark_reason' => $reason);
            $ext->attributes = $attrs;
            if ($ext->save()) {

                $last_record = json_encode(array(
                    'mark' => $last_mark,
                    'mark_reason' => $ext->attributes['mark_reason']));
                //echo 'driver='.$type.'<br>';
                DriverLog::model()->insertLog($driver->imei, $type, $driver->user, $last_record, Dict::item('disableBy', $type));

                //屏蔽成功发送通知短信
                $ext_phone = '';
                if (trim($driver->ext_phone) != '') {
                    $ext_phone_array = explode(',', $driver->ext_phone);
                    $ext_phone = $ext_phone_array[0];
                }

                if($attr['mark'] != $last_mark) {
                    $i_phone = ($ext_phone) ? $ext_phone : $driver->phone;
                    $i_message = sprintf($message, $driver->user, $reason);
                    Sms::SendSMS($i_phone, $i_message);
                }
                return 1;
            }
        }
        return 0;
    }


    public static function enableByFee($driverId)
    {
        $employee = Driver::getProfile($driverId);

        if ($employee->mark == Employee::MARK_DISNABLE) {
            $lastMark = DriverLog::model()->getLastMarkLog($employee->user);
            if (isset($lastMark) && $lastMark->type == DriverLog::LOG_MARK_DISABLE_FEE) {
                $balance = DriverAccountService::getDriverBalance($employee->user);
                if ($balance > 200) {
                    $type = DriverLog::LOG_MARK_ENABLE;
                    $reason = '已充值';
                    Driver::model()->block($driverId, Employee::MARK_ENABLE, $type, $reason);
                }
            }
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'position' => array(
                self::BELONGS_TO,
                'DriverPosition',
                'id',
                'condition' => 'position.status in (0,1) and baidu_lng >0 and baidu_lat>0'
            ),
            'logs' => array(
                self::HAS_MANY,
                'DriverLog',
                'id'));
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'Id',
            'city_id' => '城市',
            'user' => '工号',
            'name' => '姓名',
            'gender' => '性别',
            'picture' => '头像',
            'phone' => '手机号码',
            'domicile' => '籍贯',
            'register_city' => '户口所在市',
            'address' => '居住详细地址',
            'id_card' => '身份证号码',
            'car_card' => '驾驶证号码',
            'id_driver_card' => '驾照档案编号',
            'year' => '驾龄',
            'level' => '星级',
            'mark' => '标记',
            'block_at' => '自动屏蔽激活',
            'block_mt' => '手动屏蔽激活',
            'password' => '密码',
            'ext_phone' => '备用电话',
            'imei' => 'IMEI',
            'is_andriod' => '手机类型',
            'license_date' => '驾照申领日期',
            'rank' => '司机等级',
            'recommender' => '推荐人编号',
            'assure' => '担保状态',
            'work_type' => '工作方式',
            'service_type' => '支持业务类型',
            'created' => '签约时间',
            'driver_manager'=>'是否司管，用于控制登陆司管app',
        );
    }


    public function beforeSave()
    {
        if (parent::beforeSave()) {
            $this->name = trim($this->name);
            $this->phone = trim($this->phone);
            $this->domicile = trim($this->domicile);

            if ($this->city_id == 0) {
                $this->city_id = Yii::app()->user->city;
            }
            $this->imei = trim($this->imei);
            $this->user = strtoupper(trim($this->user));
            $this->password = $this->password == '6688' ? strtoupper(substr(trim($this->id_card), -6)) : $this->password;

            $old_record = self::getProfile($this->user);
            if ($old_record) {
                if ($this->mark == $old_record->mark && $this->level == $old_record->level) {
                    $ext = DriverExt::model()->getExt($this->user);
                    if ($ext) {

                        $last_record = json_encode(array_merge($old_record->attributes, $ext->attributes));
                        DriverLog::model()->insertLog($this->imei, DriverLog::LOG_NORMAL, $this->user, $last_record, '更新司机信息');
                    }
                }
            } else {
                $this->created = date('Y-m-d H:i:s', time());
            }
        }
        return parent::beforeSave();
    }

    public function afterSave()
    {
        EdjLog::info("Driver#afterSave#start..................".$this->user);
        if ($this->getIsNewRecord()) {
            //$record = self::getProfile($this->user);
            $record = $ret = self::model()->find('user=:user', array(':user' => $this->user));
            if ($record) {
                $ext = DriverExt::model()->getExt($this->user);
                if ($ext) {
                    $last_record = json_encode(array_merge($record->attributes, $ext->attributes));

                    DriverLog::model()->insertLog($record->imei, DriverLog::LOG_NORMAL, $record->user, $last_record, '新建司机信息');
                }
            }
        }

        if (!empty($this->imei)){
            $attr = array(
                'imei' => trim($this->imei),
                'user' => $this->user,
                'name' => $this->name,
                'picture' => $this->picture,
                'phone' => $this->phone,
                'id_card' => $this->id_card,
                'domicile' => $this->domicile,
                'car_card' => $this->car_card,
                'year' => $this->year,
                'level' => $this->level,
                'mark' => $this->mark,
                'password' => $this->password,
                'city_id' => $this->city_id,
                'ext_phone' => $this->ext_phone);

            $employee = Employee::model()->find('imei=:imei', array(
                ':imei' => $this->imei));
            if ($employee) {
                $dataEmployee = $employee->attributes;
                foreach ($attr as $key => $value) {
                    $dataEmployee[$key] = $value;
                }

                $employee->attributes = $dataEmployee;
                $employee->save();
            }

        }
        EdjLog::info("Driver#afterSave#start to reload redis..................".$this->user);
        DriverStatus::model()->reload($this->user,false);//此处不更新token，否则屏蔽的时候，也会让司机token失效，重登录
        EdjLog::info("Driver#afterSave#reload redis ok..................".$this->user);

        return parent::afterSave();
    }

    public static function getDriverOrder($driver_id)
    {
        $ext = DriverExt::model()->getExt($driver_id);
        $orderCount = ($ext) ? $ext->service_times : 0;


        return $orderCount;
    }

    public static function getDriverComments($driver_id)
    {
        $ext = DriverExt::model()->getExt($driver_id);
        $commentsCount = ($ext) ? ($ext->high_opinion_times + $ext->low_opinion_times) : 0;
        return $commentsCount;
    }

    public static function getDriverReadyOrder($driver_id)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'order_id';
        $criteria->condition = 'driver_id=:driver_id and status=:status';
        $criteria->params = array(
            ':driver_id' => $driver_id,
            ':status' => Order::ORDER_READY);

        return Order::model()->count($criteria);
    }

    /**
     * 根据电话号码查询司机信息
     * @editor sunhongjing 2013-09-07 增加条件验证和排除解约司机
     * @param int $phone
     */
    public static function getDriverByPhone($phone)
    {
        if (empty($phone)) {
            return false;
        }
        $phone = trim($phone);
        $sql = 'select * from t_driver where phone =:phone and mark<>:leave order by mark asc';

        $ret= Driver::model()->findBySql($sql, array( ':phone' => $phone,  ':leave' => Driver::MARK_LEAVE));
        if(!empty($ret)){
            return $ret;
        }
        $sql = 'select * from t_driver where ext_phone=:ext_phone and mark<>:leave order by mark asc';

        $ret= Driver::model()->findBySql($sql, array( ':ext_phone' => $phone, ':leave' => Driver::MARK_LEAVE));
        if(!empty($ret)){
            return $ret;
        }
        return $ret;
    }

    /**
     * 获取司机的最新状态信息
     * @param int $user_id
     * @return string $status
     */
    public function getStatus($user_id)
    {
        $status = Yii::app()->db_readonly->createCommand()
            ->select('status')
            ->from(' t_driver_position')
            ->where('user_id=:user_id')
            ->queryScalar(array(':user_id' => $user_id));

        if ($status) {
            return $status;
        } else {
            return 2;
        }
    }

    public function getOrderList($driver_id)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'name, phone, source, location_start, location_end, call_time, booking_time, status';
        $criteria->condition = 'driver_id=:driver_id';
        $criteria->order = 'order_id desc';
        $criteria->params = array(
            ':driver_id' => $driver_id);
        $criteria->limit = 5;
        $criteria->offset = 0;

        $list = Order::model()->findAll($criteria);

        return $list;
    }

    /**
     *
     * 查询推荐司机列表
     */
    public function getRecommandList()
    {
        $sql = 'SELECT * FROM `t_driver_recommand`
				WHERE :today between `begin_time` and `end_time`;';

        $params = array(
            ':today' => date(Yii::app()->params['formatDateTime'], time()));

        $list = Yii::app()->db_readonly->createcommand($sql)->queryAll(true, $params);

        foreach ($list as $k => $v) {
            $list[$v['driver_id']] = $v;
            unset($list[$k]);
        }

        return $list;
    }

    /**
     *
     * 获取司机位置列表
     * @param int $city_id
     * @param string $mark
     * @param string $status
     */
    public function getStatusList($city_id, $mark)
    {
//		$sql = 'SELECT e.user as driver_id, d.id, d.name, d.phone, dp.baidu_lng longitude,dp.baidu_lat latitude,e.state
//				FROM `t_driver_position` dp
//				JOIN t_driver d ON dp.user_id = d.id
//				JOIN t_employee e ON d.imei = e.imei
//				WHERE d.city_id =:city_id
//				AND e.mark =:mark AND dp.longitude >0 and dp.latitude >0
//				AND e.state in(0,1) ORDER BY e.state desc';

        $sql = 'SELECT d.user as driver_id, d.id, d.name, d.phone, dp.baidu_lng longitude,dp.baidu_lat latitude,dp.status as state
				FROM `t_driver_position` dp
				JOIN t_driver d ON dp.user_id = d.id
				WHERE d.city_id =:city_id
				AND d.mark =:mark AND dp.longitude >0 and dp.latitude >0
				AND dp.status =0;';
        //AND dp.status in(0,1);';

        $params = array(
            ':city_id' => $city_id,
            ':mark' => $mark);

        $drivers = Yii::app()->db_readonly->createCommand($sql)->queryAll(true, $params);
        //$drivers = Yii::app()->db->createCommand($sql)->queryAll(true, $params);
        return $drivers;
    }

    /**
     * 通过身份证号查询司机信息
     * @param $id_card 身份证号
     * @return array
     */
    public function getDriverByIdCard($id_card)
    {
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('*');
        $command->from('t_driver');
        $command->where('id_card=:id_card', array(':id_card' => $id_card));
        $driver = $command->queryRow();
        return $driver;
    }

    /**
     *
     * 查询司机的被推荐信息
     * @param string $driver_id
     */
    public function getDriverRecommand($driver_id)
    {
        $sql = 'SELECT * FROM `t_driver_recommand`
				WHERE :today between `begin_time` and `end_time` AND driver_id = :driver_id;';

        $recommand = Yii::app()->db_readonly->createcommand($sql)->query(array(
            ':today' => date(Yii::app()->params['formatDateTime'], time()),
            ':driver_id' => $driver_id));

        return $recommand;
    }

    /**
     *
     * 查询司机列表
     * @param int $city_id
     * @param string $mark
     * @return boolean $result
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-05-13
     */
    public function getDriverList($city_id, $mark)
    {
        if (empty($city_id) || empty($mark)) {
            return false;
        }
        if (is_array($mark)) {
            $cond_mark = " AND mark IN(" . implode(',', $mark) . ")";
        } else {
            $cond_mark = " AND mark=" . $mark;
        }
        $result = Yii::app()->db_readonly->createCommand()
            ->select('user as driver_id,name,city_id')
            ->from('t_driver')
            ->where('city_id = :city_id ' . $cond_mark . '', array(
                ':city_id' => $city_id,
            ))->queryAll();
        return $result;
    }

    public function getProfileWithManagerCity($driver_id)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('user', $driver_id, true);
        if (Yii::app()->user->city != 0) {
            $criteria->compare('city_id', Yii::app()->user->city);
        }
        return Driver::model()->find($criteria);
    }

    /**
     * 校验密码
     */
    public function validatePassword($password)
    {
        return $password === $this->password;
    }

    /**
     *
     * 用工号查询司机信息
     * @param string $driver_id
     */
    public static function getProfile($driver_id)
    {
        self::$db = Yii::app()->db_readonly;
        $ret = self::model()->find('user=:driver_id', array(':driver_id' => $driver_id));
        self::$db = Yii::app()->db;
        //auto refresh driver's driving age,zhongfuhai comment at 2015/4/16
        $ret = self::refreshDrivingYear($ret);
        return $ret;
    }

    /**
     *
     * 用IMEI查询司机工号
     * @param string $imei
     */
    public static function getProfileByImei($imei)
    {
        self::$db = Yii::app()->db_readonly;
        $ret = self::model()->find('imei=:imei and mark=0', array(':imei' => $imei));
        self::$db = Yii::app()->db;
        return $ret;
    }

    public function getProfileById($id)
    {
        self::$db = Yii::app()->db_readonly;
        $ret = self::model()->find('id=:id', array(':id' => $id));
        self::$db = Yii::app()->db;
        return $ret;
    }

    /**
     *
     * 校验imei是否存在系统
     * @param string $imei
     */
    public static function validateImei($imei)
    {
        self::$db = Yii::app()->db_readonly;
        $driver = Driver::model()->find('imei=:imei and mark=0', array(':imei' => $imei));
        self::$db = Yii::app()->db;
        if (!$driver) {
            return false;
        }
        return true;
    }

    /**
     * 用司机工号取IEMI
     */
    public static function getImei($id)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'imei';
        $criteria->condition = 'user=:id';
        $criteria->params = array(
            ':id' => $id);
        self::$db = Yii::app()->db_readonly;
        $ret = self::model()->find($criteria);
        self::$db = Yii::app()->db;
        if ($ret) {
            return $ret->imei;
        }
        return null;
    }

    /**
     * 查找要扣款城市的司机
     * @param $city 城市id(string ,隔开)
     * @param $cast 最低金额
     * @return array
     * author mengtianxue
     */
    public function DriverLists($city, $cast)
    {
        if (is_array($city)) {
            $city_str = implode(",", $city);
        } else {
            $city_str = $city;
        }

        if (!empty($city_str)) {
            $drivers = Yii::app()->db_finance->createCommand()
                ->select('driver_id,name,city_id,balance')
                ->from('t_driver_balance')
                ->where("city_id in (" .$city_str . ") and balance < :cast ", array(':cast' => $cast))
                ->queryAll();
            $driver_ids = array();
            $driver_arr = array();
            if(empty($drivers)){
                return array();
            }
            foreach($drivers as $driver){
                $driver_ids[] = $driver['driver_id'];
                $driver_arr[$driver['driver_id']] = $driver;
            }
            $driver_list = Yii::app()->db_readonly->createCommand()
                ->select('user,id,phone,ext_phone,block_at,mark')
                ->from('t_driver')
                ->where("user in ('".implode("','",$driver_ids)."') and mark in (0,1) and is_test=0 and imei!=''")
                ->queryAll();
            $driver_result = array();
            foreach($driver_list as $driver){
                $driver_id = $driver['user'];
                unset($driver['user']);
                $driver_result[] = array_merge($driver_arr[$driver_id],$driver);
            }
            return $driver_result;
        }
        return array();

    }

    /**
     * 信息费低于限额的司机名单 (北京,上海，广州，深圳)
     */
    /*注释掉无效代码，谁再用团望打她
    public function getArrearage($cast)
    {
        $drivers = Yii::app()->db_readonly->createCommand()->select('v.user,v.name,v.city_id,d.phone,d.ext_phone,v.total')
            ->from('t_view_employee_account_sum v, t_driver d')->where('v.user= d.user and d.mark =0 and v.city_id in(1,3,5,6) and v.total<:cast', array(
                ':cast' => $cast))->queryAll();
        return $drivers;
    }
    */

    /**
     * 信息费低于限额的司机名单（杭州，重庆）
     */
    /*注释掉无效代码，谁再用团长打他
    public function getArrearage_area($cast)
    {
        $drivers = Yii::app()->db_readonly->createCommand()->select('v.user,v.name,v.city_id,d.phone,d.ext_phone,v.total')->from('t_view_employee_account_sum v, t_driver d')->where('v.user= d.user and d.mark =0 and v.city_id in(4,7) and v.total<:cast', array(
            ':cast' => $cast))->queryAll();
        return $drivers;
    }
    */

    /**
     * 信息费高于限额的司机名单 TODO：区分司机的屏蔽类型
     */
    /*uncommented by liutuanwang,not lidingcai
    public function getOverLimit($cast)
    {
        $drivers = Yii::app()->db_readonly->createCommand()->select('v.user,v.name,v.city_id,d.phone,d.ext_phone,v.total')->from('t_view_employee_account_sum v, t_driver d')->where('v.user= d.user and d.mark =0 and v.city_id in(1,3) and v.total<=:cast', array(
            ':cast' => $cast))->queryAll();
        return $drivers;
    }
    */

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.
        $params = array();
//		$rank = isset($_REQUEST['rank']) ? $_REQUEST['rank'] : '';

        $criteria = new CDbCriteria();
        if ($this->city_id != 0) {
            $criteria->compare('city_id', $this->city_id);
        }
        $criteria->compare('user', $this->user);
        $criteria->compare('name', $this->name);
        $criteria->compare('gender', $this->gender);
        $criteria->compare('phone', $this->phone);
        $criteria->compare('domicile', $this->domicile);
        $criteria->compare('address', $this->address);
        $criteria->compare('id_card', $this->id_card);
        $criteria->compare('register_city', $this->register_city);
        //$criteria->compare('car_card', $this->car_card, true);
        $criteria->compare('year', $this->year);
        $criteria->compare('driver_manager', $this->driver_manager);

        //$criteria->compare('level', $this->level);

        if ($this->level != '') {
            if ($this->level == 5) {
                $criteria->addCondition('level=5');
            } else {
                $maxLevel = $this->level + 1;
                $criteria->addCondition('level>=' . $this->level . ' AND level<' . $maxLevel);
            }
        }

        $criteria->compare('mark', $this->mark);
        $criteria->compare('imei', $this->imei);
        $criteria->compare('ext_phone', $this->ext_phone);
        $criteria->compare('license_date', $this->license_date);

        if ($this->car_card == 2) {
            $this->is_android = true;
            $android_drivers = DriverPhone::model()->getBindDriverList();
            $criteria->addInCondition('user', $android_drivers);
        } else if ($this->car_card == 1) {
            $this->is_android = false;
            $android_drivers = DriverPhone::model()->getBindDriverList();
            $criteria->addNotInCondition('user', $android_drivers);
        }

        if (!empty($this->rank)) {
            $criteria->compare('rank', $this->rank);
        }
        $criteria->compare('recommender', $this->recommender);
//		$criteria->params = $params;
        $criteria->order = 'id desc';

        return new CActiveDataProvider($this, array(
            'pagination' => array(
                'pageSize' => 50
            ),
            'criteria' => $criteria
        ));
    }

    public function searchByCondition(array $data, $pageSize = 50)
    {

        $criteria = new CDbCriteria();
        if ($data['city_id'] != 0) {
            $criteria->compare('city_id', $data['city_id']);
        }
        $criteria->compare('user', $data['user']);
        $criteria->compare('name', $data['name'], true);
        $criteria->compare('gender', $data['gender']);
        $criteria->compare('phone', $data['phone']);
        //$criteria->compare('domicile', $data['domicile'], true);
        //$criteria->compare('address', $data['address'], true);
        $criteria->compare('id_card', $data['id_card']);

        //$criteria->compare('car_card', $this->car_card, true);
        $criteria->compare('year', $data['year']);
        //$criteria->compare('level', $this->level);

        if ($data['level'] != '') {
            if ($data['level'] == 5) {
                $criteria->addCondition('level=5');
            } else {
                $maxLevel = $data['level'] + 1;
                $criteria->addCondition('level>=' . $data['level'] . ' AND level<' . $maxLevel);
            }
        }

        $criteria->compare('mark', $data['mark']);
        $criteria->compare('imei', $data['imei']);
        $criteria->compare('ext_phone', $data['ext_phone']);
        $criteria->compare('assure', $data['assure']);
        $criteria->compare('work_type', $data['work_type']);

        //$criteria->compare('license_date', $data['license_date'], true);

        if ($data['car_card'] == 2) {
            $data['is_android'] = true;
            $android_drivers = DriverPhone::model()->getBindDriverList();
            $criteria->addInCondition('user', $android_drivers);
        } else if ($data['car_card'] == 1) {
            $data['is_android'] = false;
            $android_drivers = DriverPhone::model()->getBindDriverList();
            $criteria->addNotInCondition('user', $android_drivers);
        }

        if (!empty($data['rank'])) {
            $criteria->compare('rank', $data['rank']);
        }
        if (!empty($data['created_start']) && !empty($data['created_end']) && $data['created_start'] <= $data['created_end']) {
            $criteria->addBetweenCondition('created', $data['created_start'], $data['created_end']);
        }
        //$criteria->compare('recommender', $data['recommender'], true);
//		$criteria->params = $params;
        $criteria->order = 'id desc';
        return new CActiveDataProvider($this, array(
            'pagination' => array(
                'pageSize' => $pageSize
            ),
            'criteria' => $criteria
        ));

    }

    /**
     * 司机签约唯一方法
     * @param     $id 报名表（t_driver_recruitment）主键
     * @param     $v_number   V号 （t_driver_phone）
     * @param     $driver_phone 司机工作电话
     * @param int $assure 担保信息（默认为未担保）
     * @return array
     * @throws Exception
     */
    public function driverEntry($id, $driver_id, $v_number, $driver_phone, $assure = 8)
    {
        try {
            $driver = DriverRecruitment::model()->findByPk($id);

            //查看报名ID是否存在
            if (!$driver) {
                throw new Exception('1001');
            }

            //开通城市，v号不必填
            $city_id = $driver['city_id'];
            $open = DriverOrder::model()->checkOpenCity($city_id);
            if(empty($open) && !$v_number){
                throw new Exception('1013');
            }

            //判断是否完善快递地址，只针对开通城市
            if($open){
                $complete = DriverRecruitment::model()->isComplete($id);
                if(empty($complete)){
                    throw new Exception('1012');
                }
            }

            //将报名表中的属性附到一个数组
            $data = $driver->attributes;

            //检测手机号格式是否正确
            if (!Common::checkPhone($driver_phone)) {
                throw new Exception('1011');
            }

            //查看司机状态是否是路考已经通过
            $signed = DriverRecruitment::model()->canSigned($driver->status,$city_id);
            if (empty($signed)) {
                throw new Exception('1002');
            }

            //查看该司机的身份证号是否存在
            $is_exist_id_card = $this->getDriverByIdCard($driver->id_card);
            if ($is_exist_id_card) {
                throw new Exception('1006');
            }

            //查看该工号是否已经签约
            if (Driver::getProfile($driver_id)) {
                throw new Exception('1003');
            }

            //判断V号是否存在
            if($v_number){
                $driverPhone = DriverPhone::model()->find('driver_id=:driver_id', array(':driver_id' => strtoupper($v_number)));
                if(empty($driverPhone)){
                    throw new Exception('1004');
                }
                $data['imei']=$driverPhone['imei'];//初始化t_driver需要，要不然司机签约后，无法登陆司机端
            }

            /***全部放入队列处理***/
            $hostname  = $_SERVER['HTTP_HOST'];
            $task = array(
                'method' => 'DriverInitialization',
                'params' => array(
                    'driver_id' => $driver_id,
                    'city_id' =>  $data['city_id'],
                    'password'=> substr($data['id_card'], -6, 6),
                    'hostname'=>$hostname,
                    'recruitment_id'=>$id,
                    'v_number'=>$v_number,
                    'driver_phone'=>$driver_phone,
                    'id_card'=>$data['id_card'],
                    'name'=>$data['name']
                ),
            );
            EdjLog::info('1.报名流水号id='.$id.',id_card='.$data['id_card'].'入职，初始化信息，成功进入队列处理...');
            Queue::model()->putin($task, 'default');

            /***初始化t_driver表***/
            $data['driver_id'] = $driver_id;
            $data['phone'] = $driver_phone;
            $task= array(
                'method' => 'init_driver_info',
                'params' => $data,
            );
            Queue::model()->putin($task, 'default');
            EdjLog::info('2.报名流水号id='.$id.',id_card='.$data['id_card'].'入职，初始化t_driver放入队列处理成功...');

            /***2.更新报名表信息***********/
            $driver->entrant_time = time();
            $driver->status = DriverRecruitment::STATUS_ENTRY_OK;
            $driver->driver_id = $driver_id;
            $driver->save(false);
            EdjLog::info('3.报名流水号id='.$id.',id_card='.$data['id_card'].'入职，更新t_driver_recruitment表成功...');


            return array('status' => true, 'data' => $data);
        } catch (Exception $e) {
            $error_code = $e->getMessage();
            $msg = self::$error_msg[$error_code];
            return array('status' => false, 'data' => $msg);
        }
    }

    /**
     * 向 t_driver 表中插入数据
     * @param $data
     * @return bool
     */
    public function insertDriverRecord($data)
    {
        $driver = new Driver();
        $driver_info = $driver->attributes;
        $year = date('Y-m-d') - date('Y-m-d', $data['driver_year']);
        $driver_info['name'] = $data['name'];
        $driver_info['domicile'] = $data['domicile'];
        $driver_info['register_city'] = $data['register_city'];
        $driver_info['gender'] = $data['gender'];
        $driver_info['address'] = $data['address'];
        $driver_info['id_card'] = $data['id_card'];
        $driver_info['car_card'] = $data['id_card'];
        $driver_info['ext_phone'] = $data['mobile'];
        $driver_info['year'] = $year;
        $driver_info['user'] = $data['driver_id'];
        $driver_info['phone'] = $data['phone'];
        $driver_info['imei'] = $data['imei'];
        $driver_info['city_id'] = $data['city_id'];
        $driver_info['password'] = substr($data['id_card'], -6, 6);
        $driver_info['license_date'] = date('Y-m-d', $data['driver_year']);
        $driver_info['rank'] = $data['rank'];
        $driver_info['recommender'] = $data['recommender'];
        $driver_info['work_type'] = $data['work_type'];
        $driver_info['picture'] = ''; //self::getPictureUrl($data['driver_id'], $data['city_id'], self::PICTURE_MIDDLE);
        $driver_info['block_mt'] = 1;
        $driver_info['created'] = date('Y-m-d H:i:s');

        //author zhangtingyi 将该方法拿到队列中执行
        /*
        $discount = Common::driver_discount($driver_info);
        if ($discount == 1) {
            $driver_info['block_at'] = 1; //bidong 2013-08-27
        } else {
            $driver_info['block_at'] = 0;
        }
        */

        $driver_info['block_at'] = 1;
        $driver->attributes = $driver_info;
        $driver->register_city = $driver_info['register_city'];
        $driver->assure = $data['assure'] ? $data['assure'] : 8;
        $driver->id_driver_card = $data['id_driver_card'];
        $result = $driver->save();

        $id = $driver->primaryKey;
        return $result;
    }

    /**
     * 获得司机头像地址
     * @param string $driver_id 司机工号
     * @param string $city_id 城市
     * @param string $size 尺寸 self::PICTURE_SMALL（小图117px） self::PICTURE_MIDDLE (中图156px) self::PICTURE_NORMAL(544px)
     * @return string
     */
    public static function getPictureUrl($driver_id, $city_id, $size = self::PICTURE_MIDDLE, $version = false)
    {

        $default = 'http://pic.edaijia.cn/0/default_driver.jpg_small';
        $img_big = 'http://pic.edaijia.cn/0/default_driver.jpg_normal';
        $driver_info = self::getProfile($driver_id);

        $pic = isset($driver_info->picture) ? $driver_info->picture : '';
        if($pic == '' || trim($pic) == 'default.png'){
            $image = ($size == self::PICTURE_SMALL) ?  $default : $img_big;
        }else{
            $pos = strpos($pic,'_');
            if($pos){
                $img_main = substr($pic,0,$pos+1);
                $image = $img_main.$size;
            }
            else{
                $image = $pic;
            }

        }


        if ($version) {
            $image = $image . '?ver=' . time();
        }
        return $image;
    }

    /**
     * 获得上传的司机头像地址 上传头像时使用 其他不是用
     * @param string $driver_id 司机工号
     * @param string $city_id 城市
     * @param string $size 尺寸 self::PICTURE_SMALL（小图117px） self::PICTURE_MIDDLE (中图156px) self::PICTURE_NORMAL(544px)
     * @return string
     */
    public static function getUploadPictureUrl($driver_id, $city_id, $size = self::PICTURE_MIDDLE, $version = false)
    {
        $url = self::PICTURE_HOST . $city_id . '/' . $driver_id . '.jpg_' . $size;
        if ($version) {
            $url = $url . '?ver=' . time();
        }
        return $url;
    }

    public function insertLog($id_card)
    {

        $operator = isset(Yii::app()->user) ? strtoupper(Yii::app()->user->getId()) : '系统自动操作';


        $log = DriverRecruitment::model()->find('id_card=:id_card', array(':id_card' => $id_card));
        if ($log) {
            $data = $log->attributes;
            $data['operator'] = $operator;
            $data['created'] = time();
            unset($data['interview']);
            unset($data['road']);
            unset($data['sort']);
            unset($data['register_city']);
            unset($data['id_driver_card']);
            $connection = Yii::app()->dbstat
                ->createCommand()
                ->insert('t_driver_recruitment_log', $data);
        }
    }

    //记录司机状态变化，生成流水
    public function insertDriverStatusLog($inserArr)
    {
        $data = array();
        $data['name'] = $inserArr['name'];
        $data['id_card'] = $inserArr['id_card'];
        $data['message'] = $inserArr['message'];
        $data['time'] = time();
        return $connection = Yii::app()->db
            ->createCommand()
            ->insert('t_recruitment_log', $data);
    }

    /**
     * @author libaiyang    2013-05-07
     * @param string $userName
     */
    public static function getDriverByName($userName)
    {

        $drivers = Yii::app()->db_readonly->createCommand()
            ->select('user')
            ->from('t_driver')
            ->where('name=:name', array(':name' => $userName))
            ->order('id DESC')
            ->queryAll();

        return $drivers;
    }

    /**
     * 判断司机是否已经签约(根据司机工号和imei)
     * @param $driver_id
     * @param $imei
     * @return bool
     */
    public function checkDriverEntry($driver_id, $imei)
    {
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('count(*)');
        $command->from('t_driver');
        $command->where('imei = :imei and user = :driver_id', array(':imei' => $imei, ':driver_id' => $driver_id));
        $num = $command->queryScalar();
        return $num ? true : false;
    }

    /**
     * 根据工号获取司机信息
     * @param $driver_id
     * @return mixed
     * author mengtianxue
     */
    public function getDriver($driver_id)
    {
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('*');
        $command->from('t_driver');
        $command->where('user = :driver_id', array(':driver_id' => $driver_id));
        $driver = $command->queryRow();
        return $driver;
    }


    /**
     * 根据工号(多个)获取司机信息
     * @param $driver_id
     * @return mixed
     * author duke
     */
    public function getDriverByIds($driver_ids)
    {
        $driver_ids = implode('","',$driver_ids);
        $driver_ids = '"'.$driver_ids.'"';
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('*');
        $command->from('t_driver');
        $command->where('user in( '.$driver_ids.')');
        $driver = $command->queryAll();
        return $driver;
    }

    /**
     * 根据城市获得未使用的最大的司机工号
     * @param $city_id
     * @return bool|string
     */
    public function getNewDriverId($city_id)
    {
        $new_num = 0;
        $driver_id = false;
        $city = Dict::items('city_prefix');
        $city_prefix = $city[$city_id];
        $city_length = strlen($city_prefix);
        //表中数据有城市和工号的前缀不相符的情况，还有要兼容司机换城市的情况
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('max(user)');
        $command->from('t_driver');
        $command->where('city_id=:city_id and  is_test=:is_test and user like "' . $city_prefix . '%" AND LEFT(user, ' . intval($city_length + 1) . ') != "' . $city_prefix . '9"', array(':is_test' => self::IS_TEST_FALSE, ':city_id' => $city_id));
        $max_user = $command->queryScalar(); //取出该城市最大工号
        $max_num = $max_user ? substr($max_user, 2) : '0';
        if (is_numeric($max_num) && $max_num >= 0) {
            $new_num = intval($max_num) + 1;
        }
        //任何分公司9000段不安排司机
        if ($new_num >= 9000 && $new_num <= 9999) {
            $new_num = 10000;
        }
        if (is_numeric($new_num)) {
            //循环一百次，防止生成工号已经被使用的情况发生
            for ($i = $new_num; $i < $new_num + 10000; $i++) {
                //工号中不能带4
                $i = str_replace(4, 5, $i);
                //生成工号不足4位前面用0补齐
                $i = sprintf("%04d", $i);
                $tmp_driver_id = $city_prefix . $i;
                //检查该工号是否有人使用，如果没有人使用跳出循环
                if (!self::getProfile($tmp_driver_id)) {
                    $driver_id = $tmp_driver_id;
                    break;
                }
            }
        }
        return $driver_id;
    }

    public function getDrivers($city_id, $mark)
    {
        if (3 == $mark || 4 == $mark) {
            $drivers = $this->getDriverByStatus($city_id, $mark);
        } else {
            $drivers = $this->getDriverByMark($city_id, $mark);
        }
        return $drivers;
    }

    /**
     * 通过mark获取司机工号
     * @param int $city_id
     * @param int $mark
     * @return array $result
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-06-08
     */
    public function getDriverByMark($city_id = 0, $mark)
    {
        $sql = "SELECT user,phone FROM t_driver WHERE 1=1";
        if ($city_id != 0) {
            $sql .= " AND city_id = :city_id";
        }
        if ($mark != 0) {
            $sql .= " AND mark = :mark";
        } else {
            $sql .= " AND mark IN(0 , 1)";
        }
        $command = Yii::app()->db_readonly->createCommand($sql);
        if ($city_id != 0) {
            $command->bindParam(":city_id", $city_id);
        }
        if ($mark != 0) {
            $mark = $mark - 1;
            $command->bindParam(":mark", $mark);
        }
        $result = $command->queryAll();
        return $result;
    }

    /**
     * 通过状态获取司机（需改成mongo...）
     * @param int $city_id
     * @param int $flag
     * @return array $result
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-06-08
     */
    public function getDriverByStatus($city_id = 0, $flag = 0)
    {
        if (0 == $flag) {
            return '';
        }
        if (3 == $flag) {
            $status = 0;
        } else {
            $status = 2;
        }
        $start_time = date('Y-m-d', strtotime('-5 day')) . ' 00:00:00';
        $end_time = date('Y-m-d H:i:s', time());
        $sql = "SELECT d.user,d.phone FROM t_driver_position p LEFT JOIN t_driver d ON p.user_id=d.id WHERE p.created BETWEEN :start_time AND :end_time AND p.status=:status";
        if (0 != $city_id) {
            $sql .= ' AND d.city_id=:city_id';
        }
        $command = Yii::app()->db_readonly->createCommand($sql);
        $command->bindParam(":status", $status);
        if (0 != $city_id) {
            $command->bindParam(":city_id", $city_id);
        }
        $command->bindParam(":start_time", $start_time);
        $command->bindParam(":end_time", $end_time);
        $result = $command->queryAll();
        return $result;
    }

    /**
     * 获得某天的司机签约数
     * @param null $date 2013-01-01
     * @return int
     */
    public function getDriverBydate($date = null, $city_id = 0)
    {
        if (!$date) {
            $date = date('Y-m-d', time());
        } else {
            $date = date('Y-m-d', strtotime($date));
        }
        $start = $date . ' 00:00:00';
        $end = $date . ' 23:59:59';
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('*');
        $command->from('t_driver');
        if ($city_id == 0) {
            $command->where('created>=:start and created<=:end', array(':start' => $start, ':end' => $end));
        } else {
            $command->where('city_id=:city_id and created>=:start and created<=:end', array(':city_id' => $city_id, ':start' => $start, ':end' => $end));
        }
        $data = $command->queryAll();
        return $data;
    }

    public function getDriverInfoByDriverId($id)
    {
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('*');
        $command->from('t_driver');
        $command->where('user=:id', array(':id' => $id));
        $driver_info = $command->queryRow();
        return $driver_info;
    }


    /***
     * 司机签约天数
     * @param $driver_id
     */
    public function getEntryTime($driver_id)
    {
        $driver_model = self::getProfile($driver_id);
        $created = $driver_model->created;
        return self::dateDiff($created);
    }

    /***
     * 求两个日期相差天数
     * @param $date_start
     * @param $date_end
     * @return float
     */
    public static function dateDiff($date_start, $date_end = null)
    {
        if (is_null($date_end)) {
            $date_end = date('Y-m-d', time());
        }
        $time_1 = strtotime($date_start);
        $time_2 = strtotime($date_end);
        $days = round(abs($time_2 - $time_1) / 3600 / 24);
        return $days;
    }

    /**
     * 司机扩展信息
     * @param $driver_id
     * @return mixed
     */
    public function driverExtendData($driver_id)
    {
        $ext = DriverExt::model()->find('driver_id=:driver_id', array(
                ':driver_id' => $driver_id)
        );
        $driver_info = $ext->attributes;

        $recharge = $this->getDriverRecharge($driver_id);
        //充值金额
        $driver_info['recharge'] = $recharge['recharge'];
        //给公司带来的收入
        $driver_info['deductions'] = $recharge['deductions'];

        //司机在线天数
        /*
        $daily_model = new DailyOrderReport();
        $online_info = $daily_model->getOnlineDaysInfo($driver_id);
        $driver_info['normal_days'] = $daily_model->getDriverNormalDays($driver_id);
        $driver_info['online_days'] = intval($online_info['online']) ? intval($online_info['online']): 0;
        $driver_info['p_online'] = intval($online_info['p_online']) ? intval($online_info['p_online']) : 0;
        $driver_info['p_continuous'] = intval($online_info['p_continuous']) ? intval($online_info['p_continuous']) : 0;
        */
        //奖励次数
        /*
        $recommand_model = new DriverRecommand();
        $driver_info['recommand'] = $recommand_model->getRecommandCount($driver_id);
        */
        //处罚次数
        /*
        $punish_model = new DriverPunish();
        $driver_info['punish'] = $punish_model->getPunishCount($driver_id);
        */
        //信息费余额

        $balance = DriverBalance::model()->getDriverBalance($driver_id);
        $driver_info['balance'] = $balance ? $balance->balance : 0;

        //被客人投诉数
        //$driver_info['customer_complain'] = CustomerComplain::model()->count("driver_id=:driver_id",array(":driver_id"=>$driver_id));

        //投诉客人数
        //$driver_info['driver_complain'] = DriverComplaint::model()->count('driver_user=:driver_id', array(':driver_id'=>$driver_id));


        //$status_model = new DriverStatus();
        //$status_info = $status_model->get($driver_id);
        //代驾次数
        //$driver_info['service_times'] = $status_info->service['service_times'];
        //好评数
        //$driver_info['high_times'] = $status_info->service['high_opinion_times'];
        //差评次数
        //$driver_info['low_times'] = $status_info->service['low_opinion_times'];
        return $driver_info;
    }

    /**
     * 获得司机基本信息
     * @param $driver_id
     * @return mixed
     */
    public function driverBasicData($driver_id)
    {
        $driver_info = $this->getDriverInfoByDriverId($driver_id);
        if($driver_info){
            //司机签约时间
            if ($driver_info['mark'] != 3) {
                $driver_info['entry_time'] = $driver_info['created'] > 0 ? self::dateDiff(date('Y-m-d', strtotime($driver_info['created']))) : '未知';
            } else {
                $criteria = new CDbCriteria();
                $criteria->condition = "driver_id=:driver_id and type=:type";
                $criteria->params = array(
                    ':driver_id' => $driver_id,
                    ':type' => DriverLog::LOG_MARK_LEAVE);
                $level_data = DriverLog::model()->find($criteria);
                if ($level_data) {
                    $level_ts = $level_data['created'];
                    $driver_info['entry_time'] = self::dateDiff(date('Y-m-d', strtotime($driver_info['created'])), date('Y-m-d', $level_ts));
                } else {
                    $driver_info['entry_time'] = '未知';
                }
            }


            //$driver_info['recharge'] = 0;
            //$driver_info['deductions'] = 0;
            //报名信息
            $key = 'DRIVER_RECRUITMENT_INFO_' . $driver_info['id_card'];
            $recruitment_data = Yii::app()->cache->get($key);
            if (!$recruitment_data) {
                $recruitment_model = new DriverRecruitment();
                $recruitment_data = $recruitment_model->getDriverByIDCard($driver_info['id_card']);
                Yii::app()->cache->set($key, $recruitment_data);
            }
            $driver_info['age'] = $recruitment_data['age'];
            $driver_info['district_id'] = $recruitment_data['district_id'];
            $driver_info['work_type'] = $recruitment_data['work_type'];
            $driver_info['driver_type'] = $recruitment_data['driver_type'];
            $driver_info['contact'] = $recruitment_data['contact'];
            $driver_info['contact_phone'] = $recruitment_data['contact_phone'];
            $driver_info['contact_relate'] = $recruitment_data['contact_relate'];
            $driver_info['src'] = $recruitment_data['src'];
            $driver_info['recommender'] = $recruitment_data['recommender'];
        }
        return $driver_info;
    }


    /**
     * 司机状态（空闲/服务中/下班/屏蔽/解约）
     * @param $driver_id
     */
    public function getDriverStatus($driver_id)
    {
        $driver = Driver::getProfile($driver_id);
        $statusLabel = '正常';
        if ($driver->mark == self::MARK_LEAVE) {
            $statusLabel = '解约';
        } elseif ($driver->mark == self::MARK_DISNABLE) {
            $statusLabel = '屏蔽';
        } else {
            switch ($driver->position['status']) {
                case DriverPosition::POSITION_IDLE :
                    $statusLabel = '空闲';
                    break;
                case DriverPosition::POSITION_WORK :
                    $statusLabel = '服务中';
                    break;
                case DriverPosition::POSITION_GETOFF :
                    $statusLabel = '下班';
                    break;
                default :
                    $statusLabel = '下班';
                    break;
            }

        }
        return $statusLabel;
    }


    public function getNewDriverStatus($driver_id)
    {
        $driver_id = strtoupper($driver_id);
        $driver = Driver::getProfile($driver_id);
        //$statusLabel = '正常';
        if (!$driver) {
            $result = array(
                'success' => false,
                'msg' => '司机不存在'
            );
        } elseif ($driver->mark == self::MARK_LEAVE) {
            $result = array(
                'success' => false,
                'msg' => '解约'
            );
            //$statusLabel = '解约';
        } elseif ($driver->mark == self::MARK_DISNABLE) {
            $result = array(
                'success' => false,
                'msg' => '屏蔽',
            );
            //$statusLabel = '屏蔽';
        } else {
            $arr['mongo']['code'] = $this->getMongoStatus($driver_id);
            $arr['mongo']['status'] = $this->getStatusString($arr['mongo']['code']);
            $arr['redis']['code'] = $this->getRedisStatus($driver_id);
            $arr['redis']['status'] = $this->getStatusString($arr['redis']['code']);
            $arr['db']['code'] = $this->getDbStatus($driver_id);
            $arr['db']['status'] = $this->getStatusString($arr['db']['code']);
            /*
            switch ($driver->position['status']) {
                case DriverPosition::POSITION_IDLE :
                    $statusLabel = '空闲';
                    break;
                case DriverPosition::POSITION_WORK :
                    $statusLabel = '服务中';
                    break;
                case DriverPosition::POSITION_GETOFF :
                    $statusLabel = '下班';
                    break;
                default :
                    $statusLabel = '下班';
                    break;
            }
            */
            $result = array(
                'success' => true,
                'msg' => $arr
            );
        }
        return $result;
    }

    /**
     * 获得工作状态中文
     * @param $status
     * @return string
     */
    public function getStatusString($status) {
        switch ($status) {
            case DriverPosition::POSITION_IDLE :
                $statusLabel = '空闲';
                break;
            case DriverPosition::POSITION_WORK :
                $statusLabel = '服务中';
                break;
            case DriverPosition::POSITION_GETOFF :
                $statusLabel = '下班';
                break;
            default :
                $statusLabel = '下班';
                break;
        }
        return $statusLabel;
    }

    /**
     * 从数据库中获得司机工作状态
     * @param $driver_id
     * @return string
     */
    public function getDbStatus($driver_id) {
        $driver = Driver::getProfile($driver_id);
        if ($driver && $driver->position) {
            return $driver->position['status'];
        } else {
            return '-1';
        }
    }

    /**
     * 从redis中获得司机工作状态
     * @param $driver_id
     * @return bool
     */
    public function getRedisStatus($driver_id) {
        $driver_info = DriverStatus::model()->get($driver_id);
        if ($driver_info) {
            return $driver_info->status;
        } else {
            return '-1';
        }
    }

    /**
     * 从mongo中获得司机工作状态
     * @param $driver_id
     * @return mixed
     */
    public function getMongoStatus($driver_id) {
        $status = DriverGPS::model()->get($driver_id);
        return $status;
    }

    /**
     * 获得司机订单信息 (订单数， 报单数， 补单数)
     * @param $driver_id
     * @return mixed
     */
    public function getDriverOrderInfo($driver_id)
    {
        $sql = "SELECT COUNT(*) AS all_count,"; //订单总数
        $sql .= "COUNT(IF(`status`=1 OR `status` = 4,TRUE,NULL)) AS complate_count,"; //报单数
        //$sql .= "COUNT(IF(`status`=0,TRUE,NULL)) AS ready_count,"; //
        $sql .= "COUNT(IF(`status`=3,TRUE,NULL)) AS cancel_count,"; //销单数
        $sql .= "COUNT(IF(`source`=2 OR `source`=3, TRUE, NULL)) AS add_count,"; //补单数，只要有补单无论最后是否报单都计数
        $sql .= "COUNT(distinct order_date) AS accept_days";
        //$sql .= "SUM(IF(`status`=1 OR `status` = 4,income,0)) AS income_complate,"; //总收入
        //$sql .= "SUM(IF(`status`=1 OR `status` = 4,abs(cast),0)) AS income_company"; //公司收入
        $sql .= " FROM t_daily_order_driver WHERE driver_user = :driver_id";
        $command = Yii::app()->dbreport->createCommand($sql);
        //$sql .= " FROM t_order WHERE driver_id = :driver_id";
        //$command = Yii::app()->db_readonly->createCommand($sql);
        $command->bindParam(":driver_id", $driver_id);
        $data = $command->queryRow();
        return $data;
    }


    /**
     * 给据司机工号获取司机接单数和司机收入
     * @param $driver_id
     * @return int
     * author mengtianxue
     */
    public function getDriverDeclaration($driver_id)
    {
        $arr = Order::getDbReadonlyConnection()->createCommand()
            ->select('sum(income) as income,count(*) as num')
            ->from('t_order')
            ->where('driver_id = :driver_id and status in (1,4)',
                array(':driver_id' => $driver_id))
            ->queryRow();
        return $arr;
    }

    /**
     * 获取司机充值记录
     * @param $driver_id
     * @return int
     * author mengtianxue
     */
    public function getDriverRecharge($driver_id)
    {
        $history_recharge = Yii::app()->db_finance->createCommand()
            ->select('(sum(t1) + sum(t2) + sum(t4) + sum(t6)) as deductions,(sum(t3) + sum(t5) + sum(t7) + sum(t8) + sum(t9) + sum(t10)) as recharge')
            ->from('t_employee_account_settle')
            ->where('user = :driver_id',
                array(':driver_id' => $driver_id))
            ->queryRow();
        //获取当月充值记录
        $table_name = 't_employee_account_' . date('Ym');

        $recharge_now = Yii::app()->db_finance->createCommand()
            //->select('(sum(t1) + sum(t2) + sum(t4) + sum(t6)) as deductions,(sum(t3) + sum(t5) + sum(t7) + sum(t8) + sum(t9) + sum(t10)) as recharge')
            ->select('
                SUM(IF(`type`=1 OR `type` = 2 OR `type` = 4 OR `type` = 6, cast, 0)) AS deductions,
                SUM(IF(`type`=3 OR `type` = 5 OR `type` = 7 OR `type` = 8 OR `type`=9 OR `type`=10, cast, 0)) AS recharge
            ')
            ->from($table_name)
            ->where('user = :driver_id',
                array(':driver_id' => $driver_id))
            ->queryRow();
        $back = array();
        //给公司带来的收
        $back['deductions'] = abs($history_recharge['deductions'] + $recharge_now['deductions']);
        //总充值金额
        $back['recharge'] = $history_recharge['recharge'] + $recharge_now['recharge'];
        return $back;
    }

    /**
     * 根据is_test 获取司机工号
     * @param int $is_test
     * @return mixed
     * @auther mengtianxue
     */
    public function getTestDriver($is_test = self::IS_TEST_TRUE)
    {
        $driver = array();

        $cache_key = 'DRIVER_TEST_' . $is_test;
        $json = Yii::app()->cache->get($cache_key);

        if (!$json || $json == '[]') {
            $test_driver = Yii::app()->db_readonly->createCommand()
                ->select('user')
                ->from('t_driver')
                ->where('is_test = :is_test',
                    array(':is_test' => $is_test))
                ->queryAll();
            foreach ($test_driver as $k => $v) {
                $driver[$k] = $v['user'];
            }
            Yii::app()->cache->set($cache_key, json_encode($driver), 3600);
        }else{
            $driver = json_decode($json);
        }
        return $driver;
    }

    /**
     * 生成司机身份证图片地址
     * @param string $id_card 身份证号
     * @param string $city_id 城市
     * @param string $size 尺寸 self::PICTURE_SMALL（小图117px） self::PICTURE_MIDDLE (中图156px) self::PICTURE_NORMAL(544px)
     * @return string
     */
    public static function createIdCardPictureUrl($id_card, $city_id, $size = self::PICTURE_MIDDLE, $version = false)
    {
        $url = self::PICTURE_HOST . 'id_card/'. $city_id . '/' . self::createIdCardPicName($id_card);

        $url .= $size ? '_' . $size : '';

        if ($version) {
            $url = $url . '?ver=' . time();
        }
        return $url;
    }



    //$id 报名id
    public static function createPicPictureUrl($pic,$id,$id_card, $city_id, $size = self::PICTURE_MIDDLE, $version = false)
    {
        $url = self::PICTURE_HOST . $city_id . '/' . self::createPicPicName($pic,$id);

        $url .= $size ? '_' . $size : '';

        if ($version) {
            $url = $url . '?ver=' . time();
        }
        return $url;
    }

    public static function createIdCardPicName($id_card) {
        return md5($id_card.'icard') . '.jpg';
    }

    public static function createDriverCardPicName($driver_card){
        return md5($driver_card.'dcard') . '.jpg';
    }

    public static function createPicPicName($pic,$id){
        return $id.'_'.$pic. '.jpg';
    }

    /**
     * 生成司机驾驶证图片地址
     * @param string $id_card 身份证号
     * @param string $city_id 城市
     * @param string $size 尺寸 self::PICTURE_SMALL（小图117px） self::PICTURE_MIDDLE (中图156px) self::PICTURE_NORMAL(544px)
     * @return string
     */
    public static function createDriverCardPictureUrl($driver_card, $city_id, $size = self::PICTURE_MIDDLE, $version = false)
    {
        $url = self::PICTURE_HOST . 'driver_card/'. $city_id . '/' . self::createDriverCardPicName($driver_card);
        $url .= $size ? '_' . $size : '';
        if ($version) {
            $url = $url . '?ver=' . time();
        }
        return $url;
    }

    public function getIdCardPic($driver_id, $size=self::PICTURE_MIDDLE) {
        $driver = Driver::model()->getProfile($driver_id);
        if ($driver) {
            return self::createIdCardPictureUrl($driver->id_card, $driver->city_id, $size);
        } else {
            return false;
        }
    }

    public function getDriverCardPic($driver_id, $size=self::PICTURE_MIDDLE) {
        $driver = Driver::model()->getProfile($driver_id);
        if ($driver) {
            return self::createIdCardPictureUrl($driver->id_card, $driver->city_id, $size);
        } else {
            return false;
        }
    }

    /**
     * 获取司机状态信息
     * @return mixed
     * author mengtianxue
     */
    public function getDriverInfo($city_id = 0)
    {
        $where = '';
        $params = array();

        if ($city_id != 0) {
            $where .= 'city_id = :city_id';
            $params[':city_id'] = $city_id;
        }

        $month_income = Yii::app()->db_readonly->createCommand()
            ->select("SUM( IF( (`mark` = 0), 1, 0 ))  AS normal,
                    SUM( IF( (`mark` = 1), 1 , 0 ))  AS shielding,
                    SUM( IF( (`mark` = 3), 1 , 0 ))  AS departure")
            ->from("t_driver")
            ->where($where, $params)
            ->queryRow();
        return $month_income;
    }


    /**
     * 统计 通知司机签约数据
     * @param int $city_id
     * @return mixed
     */
    public function getDriverInduction($city_id = 0)
    {
        $created = date('Y-m-01 00:00:00');
        $where = 'mark in(0,1) and created > :created';
        $params[':created'] = $created;
        if ($city_id != 0) {
            $where .= ' and city_id = :city_id';
            $params[':city_id'] = $city_id;
        }

        $num = Yii::app()->db_readonly->createCommand()
            ->select('count(1)')
            ->from('t_driver')
            ->where($where, $params)
            ->queryScalar();
        return $num;

    }


    /**
     *   解除司机所有屏蔽
     *
     */
    public function unBlockDriver($driver_id){
        $res = $this->updateAll(array('mark'=>self::MARK_ENABLE,'block_mt'=>self::MARK_ENABLE,'block_at'=>self::MARK_ENABLE),
            'user = :user',array(':user'=>$driver_id));
        if($res > 0){
            //更新redis
            DriverStatus::model()->reload($driver_id,false);
            //更新解除屏蔽时间表，防止下次再屏蔽的时候，会把之前屏蔽的时间累计上
            DriverPunish::model()->updateAll(
                array('status'=>DriverPunish::STATUS_OVER),
                'status = :status and driver_id = :driver_id',
                array(':status' => DriverPunish::STATUS_DISABLE,':driver_id'=>$driver_id));
        }
        return $res;
    }

    /**
     *   设置司管app
     *
     */
    public function setManager($driver_id,$manager){
        $res = $this->updateAll(array('driver_manager'=>$manager),
            'user = :user',array(':user'=>$driver_id));

        return $res;
    }


    /**
     *   获取司机城市
     *
     */
    public function getDriveCityById($driver_id){
        $result=0;
        try{
            $criteria = new CDbCriteria;
            $criteria->select = 'city_id';
            $criteria->addCondition('user=:user');
            $criteria->params[':user'] = $driver_id;
            self::$db = Yii::app()->db_readonly;
            $data=self::model()->find($criteria);
            self::$db = Yii::app()->db;
            if($data){
                EdjLog::info(serialize($data));
                $result=$data['city_id'];
            }
        }catch (Exception $e){
            EdjLog::error($e);
        }
        return $result;
    }

    /**
     * @param $driver_id
     * @return string
     */
    public function getHeadUrl($driver_id){
        $url='';
        $driver = Driver::getProfile($driver_id);
        if($driver){
            $url = $driver['picture'];
        }
        return $url;
    }


    /**
     * @param $driver_id
     * @return string
     */
    public function getCodeUrl($driver_id){
        $url='';
        $driver = Driver::getProfile($driver_id);
        if($driver){
            $url = $driver['two_code_pic'];
        }
        return $url;
    }
    
    /**
     * @author zhongfuhai
     * @param $driver
     * @return Driver
     */
    public static function refreshDrivingYear($driver){
    	if($driver instanceof Driver){
    		$recordDrivingYear = $driver['year'];
    		$licenseDate = date_parse($driver['license_date']);
    		$licenseYear = $licenseDate['year'];
    		$licenseMonth = $licenseDate['month'];
    
    		$currentYear = date("Y");
    		$currentMonth = date("m");
    		$currentDrivingYear = 0;
    
    		if($currentMonth>=$licenseMonth){
    			$currentDrivingYear = $currentYear - $licenseYear + 1;
    		}else{
    			$currentDrivingYear = $currentYear - $licenseYear;
    		}
    
    		if($currentDrivingYear!=$recordDrivingYear){
    			//if the driving year record is wrong, update the db record
    			//the driving year will be updated in the next AR update.
    			$driver['year'] = $currentDrivingYear;
    		}
    	}
    	 
    	return $driver;
    }
    
}
