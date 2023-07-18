<?php

/**
 * This is the model class for table "t_subsidy_record".
 *
 * The followings are the available columns in table 't_subsidy_record':
 * @property integer $id
 * @property integer $type
 * @property string $user_id
 * @property integer $city_id
 * @property double $cast
 * @property integer $push_status
 * @property integer $sms_status
 * @property integer $settle_status
 * @property string $settle_date
 * @property integer $cancel_type
 * @property string $date
 * @property string $created
 * @property integer $subsidy_type
 */
Yii::import('application.models.schema.customer.CarCustomerTrans');
Yii::import('application.models.schema.customer.CarCustomerAccount');
class SubsidyRecord extends FinanceActiveRecord
{
	/** 北京连续补贴高峰在线 */
	const SUBSIDY_TYPE_ONLINE = 0;

	/** 北京每周五的第二单发放固定奖励10元补贴，补贴计入周四补贴发放 */
	const SUBSIDY_TYPE_FRITWO = 1;

	/** 广州、厦门、福州、、哈尔滨：(长春、沈阳) 每周五，报第二单时发放10元红包  该种补贴现已经去掉*/
	const SUBSIDY_TYPE_EVERYFRI = 2;

	/** 上海3、深圳6、广州5、哈尔滨24、宁波12、沈阳19、福州27：每周五连续在线3小时20：00-23：00发放g改为20元红包。 */
	const SUBSIDY_TYPE_ONLINE2 = 3;

	/** 上海3:远距离接单红包：接单距离大于1，报单后发放补贴，红包金额固定为接单距离*2元。   */
	const SUBSIDY_TYPE_DISTANCE = 4;

	/** 北京夜间15分钟不到，补贴起步价；[余额直冲，app通知客户活动内容，短信通知客户具体补贴情况] 补贴客户       */
	const SUBSIDY_TYPE_STARTPRICE = 5;

	/** 评价送优惠券：app五星评价送10元优惠券，小于5星，送20-80元不等优惠券。优惠券全部为仅app可用，优惠券绑定成功有。app评价push文案微调，提示用户有补贴，请评价。400评价短信直接引导评价。 */
	const SUBSIDY_TYPE_COUPON = 6;

	/** 需要每日按照规则给司机转账信息费，作为收入，转账金额为：一口价洗车订单报单数*20元/单 */
	const SUBSIDY_TYPE_WASH_UNIT = 7;

	/** 南京1-7日报单的司机 */
	const SUBSIDY_TYPE_NANJING = 8;

	/** 广州北京远距离派单补贴 */
	const SUBSIDY_TYPE_REMOTEORDER = 9;

	/** 司机拉新司机补贴 */
	const SUBSIDY_TYPE_FETCHNEWDRIVER = 10;

	/** 春节司机送回家 */
	const SUBSIDY_TYPE_DRIVERHOME = 11;

	const USER_TYPE_DRIVER  = 0;//司机

	const USER_TYPE_USER    = 1;//普通用户

	const USER_TYPE_VIP    = 2;//VIP用户

	const PUSH_STATUS_TODO = 0;// 待发送状态

	const PUSH_STATUS_DONE = 1;// 已经发送

	const CANCEL_TYPE_DEFAULT  = 0;//补贴情况,0表示可以补贴

	const SETTLE_STATUS_TODO  = 0; //待补贴

	const SETTLE_STATUS_DONE = 1; //已经补贴

    //北京1,广州5、厦门22、福州27、哈尔滨24,合肥23[新加] (长春36,沈阳19去掉) 北京周五高峰在线奖励30报第二单10，
    //其余城市高峰在线调整为20元 报第二单取消
    //0116日其余城市改为 (上海3、深圳6、广州5、哈尔滨24、宁波12、沈阳19、福州27) 7个城市周五补贴20元
    //0123日城市变为上海、广州、福州、厦门、哈尔滨、宁波、长沙、合肥(去掉沈阳,深圳  增加长沙9  合肥23)
//    public static $awardCityArr= array(1,5,22,24,27,23);
//0128日调整; 今天0128至本周五0130，共3天，上海3、武汉10依据高峰在线累计天数发放奖励，分别是：累计1天 奖励10元，累计2天 奖励30元，累计3天 奖励66元。
    public static $awardCityArr= array(1,3,5,6,12,19,24,27,9,23);

    //根据评价级别奖励给司机的优惠券   5：10元 4：20元3：30元2：40元 1：50元
    public static $couponArr= array('1'=>50,'2'=>40,'3'=>30,'4'=>20,'5'=>10);

    /**
     * Returns the static model of the specified AR class.
     * @return SubsidyRecord the static model class
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
        return 't_subsidy_record';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, city_id, date, created', 'required'),
            array('type, city_id, push_status, sms_status, settle_status, cancel_type, subsidy_type', 'numerical', 'integerOnly'=>true),
            array('cast', 'numerical'),
            array('user_id', 'length', 'max'=>12),
            array('settle_date, date', 'length', 'max'=>10),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, type, user_id, city_id, cast, push_status, sms_status, settle_status, settle_date, cancel_type, date, created, subsidy_type,meta', 'safe', 'on'=>'search'),
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
            'type' => 'Type',
            'user_id' => 'User',
            'city_id' => 'City',
            'cast' => 'Cast',
            'push_status' => 'Push Status',
            'sms_status' => 'Sms Status',
            'settle_status' => 'Settle Status',
            'settle_date' => 'Settle Date',
            'cancel_type' => 'Cancel Type',
            'date' => 'Date',
            'created' => 'Created',
            'subsidy_type' => 'Subsidy Type',
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

        $criteria->compare('type',$this->type);

        $criteria->compare('user_id',$this->user_id,true);

        $criteria->compare('city_id',$this->city_id);

        $criteria->compare('cast',$this->cast);

        $criteria->compare('push_status',$this->push_status);

        $criteria->compare('sms_status',$this->sms_status);

        $criteria->compare('settle_status',$this->settle_status);

        $criteria->compare('settle_date',$this->settle_date,true);

        $criteria->compare('cancel_type',$this->cancel_type);

        $criteria->compare('date',$this->date,true);

        $criteria->compare('created',$this->created,true);

        $criteria->compare('subsidy_type',$this->subsidy_type);

        return new CActiveDataProvider('SubsidyRecord', array(
            'criteria'=>$criteria,
        ));
    }
	/**
	 * 更新结账之后各种状态
	 *
	 * @param $userId
	 * @param $subsidyType | 补贴类型
	 * @param $dateStart
	 * @param $dateEnd
	 * @param $settleStatus
	 * @param $smsStatus
	 * @param $settleDate
	 * @return int
	 */
	public function updateSettleStatus($userId, $subsidyType, $dateStart, $dateEnd, $settleStatus, $smsStatus, $settleDate){
		$criteria = new CDbCriteria();
		$criteria->compare('user_id', $userId);
//		$criteria->compare('subsidy_type', $subsidyType);//可能存在重叠补贴现象 比如北京周五有高峰在线和第二单补助
		$criteria->compare('cancel_type', self::CANCEL_TYPE_DEFAULT);
		$criteria->addBetweenCondition('date', $dateStart, $dateEnd);
		$attributes = array(
			'settle_status' => $settleStatus,
			'sms_status'	=> $smsStatus,
			'settle_date'   => $settleDate,
		);
		return self::model()->updateAll($attributes, $criteria);
	}
	/**
	 * 更新一口价洗车充值之后各种状态 (更新拉取新司机状态)
     * 春节回家司机拼车20150202
	 * @param $userId
	 * @param $settleStatus
	 * @param $smsStatus
	 * @param $settleDate
	 * @return int
     * 2015-01-20
	 */
	public function updateSettleStatusForCommon($userId,$date, $settleStatus, $smsStatus, $settleDate,$subsidy_type){
		$criteria = new CDbCriteria();
		$criteria->compare('user_id', $userId);
		$criteria->compare('date', $date);
		$criteria->compare('subsidy_type', $subsidy_type);//一口价洗车或拉取新司机或司机春节拼车回家
		$attributes = array(
			'settle_status' => $settleStatus,
			'sms_status'	=> $smsStatus,
			'settle_date'   => $settleDate,
		);
		return self::model()->updateAll($attributes, $criteria);
	}

    /**更新南京补贴成功后的各种状态
     * @param $userId
     * @param $date
     * @param $settleStatus
     * @param $smsStatus
     * @param $settleDate
     * @return int
     */
    public function updateSettleStatusForNanJing($userId,$settleStatus, $smsStatus, $settleDate){
        $criteria = new CDbCriteria();
        $criteria->compare('user_id', $userId);
        $criteria->compare('subsidy_type', self::SUBSIDY_TYPE_NANJING);//南京
        $attributes = array(
            'settle_status' => $settleStatus,
            'sms_status'	=> $smsStatus,
            'settle_date'   => $settleDate,
        );
        return self::model()->updateAll($attributes, $criteria);
    }

    /**  北京 超过十五分钟给客户补贴起步价
     * @param $userId
     * @param $subsidyType
     * @param $cast
     * @param $updateStatus 如果更新状态失败则把date时间调到当前，便于下一次查询到该失败的记录
     * @return int
     */
    public function updateCustomerSettleStatus($userId,$time, $subsidyType,$updateStatus=1,$smsStatus=0,$cast=0){
        $criteria = new CDbCriteria();
        $criteria->compare('user_id', $userId);
		$criteria->compare('subsidy_type', $subsidyType);//补贴类型必须确定
        $criteria->compare('cancel_type', self::CANCEL_TYPE_DEFAULT);
        $criteria->addCondition('created >:created');
        $criteria->params[':created'] = $time;
        $attributes = array();
        if($updateStatus == 1){
            $attributes = array(
                'push_status' => self::PUSH_STATUS_DONE,
                'settle_status' => self::SETTLE_STATUS_DONE,
                'sms_status'	=> $smsStatus,
                'settle_date'   => date('Y-m-d', time()),//最多只能容纳10个字符
            );
        }else if($updateStatus == 2){
            $attributes = array(
                'push_status' => self::PUSH_STATUS_DONE,
                'settle_status' => self::SETTLE_STATUS_DONE,
                'settle_date'   => date('Y-m-d', time()),//最多只能容纳10个字符
                'cast' => $cast, //补贴客户优惠券成功后将补贴的金额回写到表中的cast中便于建平那边统计
            );
        }else if($updateStatus == 3){
//            如果更新状态失败则把date时间调到当前，便于下一次查询到该失败的记录
            $attributes = array(
                'created' => date('Y-m-d H:i:s', time()),
            );
        }
        return self::model()->updateAll($attributes, $criteria);
    }

	/**
	 * 返回要补贴的司机记录
	 *
	 * @param $dateStart
	 * @param $dateEnd
	 * @param $subsidyType
	 * @param string $driverId
	 * @return CActiveRecord[] | 如果指定了 driverId,那么返回的就是这个司机的记录, 否则就只返回司机
	 */
	public function getSubsidyDriverList($dateStart, $dateEnd, $driverId = ''){
		$criteria = new CDbCriteria();
		$criteria->compare('cancel_type', self::CANCEL_TYPE_DEFAULT);
		$criteria->compare('settle_status', self::SETTLE_STATUS_TODO);
		$criteria->compare('push_status', self::PUSH_STATUS_DONE);//因为周六周天不再高峰补贴所以此处限制周六日的数据
		$criteria->compare('type', self::USER_TYPE_DRIVER);
//		$criteria->compare('subsidy_type', $subsidyType);//去掉subsidy_type值的校验，目的为了达到奖励重叠值发送一条短信
		$criteria->addBetweenCondition('date', $dateStart, $dateEnd);
		if(empty($driverId)){
			$criteria->select = 'distinct user_id';
		}else{
			$criteria->compare('user_id', $driverId);
			$criteria->order = 'date asc';
		}
		return self::model()->findAll($criteria);
	}

    /**
     * 根据条件得到一口价洗车报单的司机数目和信息
     * @param $date
     * @param string $driverId
     * @return CActiveRecord[]
     */
    public function getSubsidyDriverForWashUnitList($date,$driverId = ''){
        $criteria = new CDbCriteria();
		$criteria->compare('subsidy_type', self::SUBSIDY_TYPE_WASH_UNIT);
		$criteria->compare('settle_status', self::SETTLE_STATUS_TODO);
        $criteria->compare('type', self::USER_TYPE_DRIVER);
        $criteria->compare('date', $date);
        if(empty($driverId)){
            $criteria->select = 'distinct user_id';
        }else{
            $criteria->compare('user_id', $driverId);
            $criteria->order = 'date asc';
        }
        return self::model()->findAll($criteria);
    }
    /**
     * 根据条件得到补贴的司机--通用方法(现在应用于司机拉取新司机)
     * @param $date
     * @param $subsidy_type
     * @param string $driverId
     * @return CActiveRecord[]
     * 2015-01-20
     */
    public function getSubsidyDriverListCommon($date,$subsidy_type,$driverId = ''){
        $criteria = new CDbCriteria();
		$criteria->compare('subsidy_type',$subsidy_type);//以此字段区分
		$criteria->compare('settle_status', self::SETTLE_STATUS_TODO);
        $criteria->compare('type', self::USER_TYPE_DRIVER);
        $criteria->compare('date', $date);
        if(empty($driverId)){
            $criteria->select = 'distinct user_id';
        }else{
            $criteria->compare('user_id', $driverId);
            $criteria->order = 'date asc';
        }
        return self::model()->findAll($criteria);
    }

    /**
     * 得到待补贴的南京的司机
     * @param string $driverId
     * @return CActiveRecord[]
     */
    public function getSubsidyDriverForNanJing($driverId = ''){
        $criteria = new CDbCriteria();
        $criteria->compare('subsidy_type', self::SUBSIDY_TYPE_NANJING);
        $criteria->compare('settle_status', self::SETTLE_STATUS_TODO);
        $criteria->compare('type', self::USER_TYPE_DRIVER);
        if(empty($driverId)){
            $criteria->select = 'distinct user_id';
        }else{
            $criteria->compare('user_id', $driverId);
            $criteria->order = 'date asc';
        }
        return self::model()->findAll($criteria);
    }

	/**
	 * 根据日期返回普通补贴待发送 push 的司机列表
	 *
	 * @param $date | 2014-12-19
	 * @return array|CActiveRecord|mixed|null
	 */
	public function getPushDriverList($date){
		$criteria = new CDbCriteria();
		$criteria->compare('push_status', self::PUSH_STATUS_TODO);
		$criteria->compare('settle_status', self::SETTLE_STATUS_TODO);//防止洗车的数据进去
		$criteria->compare('type', self::USER_TYPE_DRIVER);
		$criteria->compare('date', $date);
        $criteria->addCondition('subsidy_type !=:subsidy_type');//此处去除上海大于1公里接单的数据
        $criteria->params[':subsidy_type']=self::SUBSIDY_TYPE_DISTANCE;
		return self::model()->findAll($criteria);
	}
	/**
	 * 根据日期返回北京代驾司机15分钟不到补贴起步价的普通客户
	 *暂时不支持vip客户的补贴
	 * @param $time | 2014-12-23 15:50:53
	 * @param $type | 暂时只支持普通客户
	 * @param $subsidy_type | 主要是北京的15分钟未到补贴起步价   客户评价返优惠券
	 * @return array|CActiveRecord|mixed|null
	 */
	public function getPushCustomerList($time,$type,$subsidy_type){
		$criteria = new CDbCriteria();
		$criteria->compare('settle_status', self::SETTLE_STATUS_TODO);//没有补贴的
		$criteria->compare('type', $type);//普通客户
		$criteria->compare('subsidy_type', $subsidy_type);//仅为北京的15分钟未到
        $criteria->addCondition('created >:created');
        $criteria->params[':created'] = $time;
		return self::model()->findAll($criteria);
	}
	/**
	 * 根据日期返回接单距离大于1上海的待发送 push 的司机列表
	 *
	 * @param $date | 2014-12-19
	 * @return array|CActiveRecord|mixed|null
	 */
	public function getPushDistDriverList($date,$driverId = ''){
		$criteria = new CDbCriteria();
		$criteria->compare('push_status', self::PUSH_STATUS_TODO);
		$criteria->compare('type', self::USER_TYPE_DRIVER);
		$criteria->compare('city_id', 3);//上海
		$criteria->compare('subsidy_type',self::SUBSIDY_TYPE_DISTANCE);//接单距离大于1
		$criteria->compare('date', $date);

        if(empty($driverId)){
            $criteria->select = 'distinct user_id,subsidy_type';
        }else{
            $criteria->compare('user_id', $driverId);
            $criteria->order = 'date asc';
        }
		return self::model()->findAll($criteria);
	}

    /**
     * 得到符合条件的一口价洗车的数据
     * @param $timeStart
     * @param $timeEnd
     * @return CActiveRecord[]
     */
    public function getWashDataByOrder($timeStart,$timeEnd){
        $criteria = new CDbCriteria();
        $criteria->addInCondition('source', array(Order::SOURCE_WASHCAR_CLIENT,Order::SOURCE_WASHCAR_CALLCENTER,Order::SOURCE_WASHCAR_CLIENT_INPUT,Order::SOURCE_WASHCAR_CALLCENTER_INPUT));
        $criteria->addInCondition('status', array(Order::ORDER_COMPLATE,Order::ORDER_NOT_COMFIRM));//状态1和4
        $criteria->addCondition('created >=:timeStart');
        $criteria->addCondition('created <=:timeEnd');
        $criteria->params[':timeStart'] = $timeStart;
        $criteria->params[':timeEnd'] = $timeEnd;
        return Order::model()->findAll($criteria);
    }

    /**
     * 根据时间得到南京的报单数据
     * @param $timeStart
     * @param $timeEnd
     */
    public function getNanJingDataByOrder($timeStart,$timeEnd){
        $criteria = new CDbCriteria();
        $criteria->compare('city_id', 8);//南京
        $criteria->addInCondition('status', array(Order::ORDER_COMPLATE,Order::ORDER_NOT_COMFIRM));//状态1和4
        $criteria->addCondition('created >=:timeStart');
        $criteria->addCondition('created <=:timeEnd');
        $criteria->params[':timeStart'] = $timeStart;
        $criteria->params[':timeEnd'] = $timeEnd;
        return Order::model()->findAll($criteria);
    }


































    /**
     * 插入符合条件南京的数据
     * @param $driverId
     * @param $city_id
     * @param $date
     * @param $orderId
     * $orderId改为cast
     */
    public function NanJingDataInsert($driverId,$city_id,$createTime,$cast){
        $model = new SubsidyRecord();
        $model->type = self::USER_TYPE_DRIVER;
        $model->user_id = $driverId;
        $model->city_id = $city_id;
        $model->created =  date('Y-m-d H:i:s',$createTime);
        $model->date =  date('Y-m-d');
        $model->subsidy_type = self::SUBSIDY_TYPE_NANJING;
        $model->cast =  $cast;
        $ret = $model->save();//插入数据
        if(!$ret){
            EdjLog::info('insert NANJING subsidy_record fail: user_id:'.$driverId.'--');
            FinanceUtils::sendFinanceAlarm('补贴南京数据入库数据失败 user_id ----- ', $driverId.'===');
            EdjLog::info(json_encode($model->getErrors()));
        }
        return $ret;

    }


    /**北京夜间15分钟不到，补贴起步价；[余额直冲，app通知客户活动内容，短信通知客户具体补贴情况]
     * @param $phone 客户电话
     * @param $amount 补贴的金额
     * @param $order_id 订单号
     * @param int $city_id  城市
     */
    public function insertSubsidyRecord($phone,$amount,$order_id,$city_id = 1){
        EdjLog::info('insertSubsidyRecord:'.json_encode(func_get_args()));
        $model = new SubsidyRecord();
        $isVip = VipService::service()->isVip($phone);//验证客户手机号是否为VIP 禁用的vip不算vip
        $type = $isVip ? self::USER_TYPE_VIP : self::USER_TYPE_USER;
        $user_id = '';//用户id-如果vip用户为vipid 普通用户为t_customer_main的id
        if($isVip){
            $vipPhone = VipPhone::model()->getPrimary($phone);//根据电话获取vip卡信息
            $user_id = $vipPhone ? $vipPhone['vipid'] : 0;
        }else{
            $customerInfo = CustomerService::service()->getCustomer($phone,1);
            $user_id = $customerInfo ? $customerInfo->id : 0;
        }
        $arr = array('phone'=>$phone);
        $phonejson = json_encode($arr);//电话转json格式存入meta字段

        $model->type = $type;
        $model->user_id = $user_id;
        $model->city_id = $city_id;
        $model->cast = $amount;
        $model->date =  date('Y-m-d');//字段限制只能10个字符
        $model->created =  date('Y-m-d H:i:s');
        $model->subsidy_type = self::SUBSIDY_TYPE_STARTPRICE;
        $model->order_id =  $order_id;
        $model->meta =  $phonejson;
        $ret = $model->save();//插入数据
        if(!$ret){
            EdjLog::info('insert subsidy_record fail: user_id:'.$user_id.'-phone:'.$phone.'--');
            FinanceUtils::sendFinanceAlarm('北京补贴司机起步价入库数据失败 user_id ----- ', $user_id.'==='.$phone);
            EdjLog::info(json_encode($model->getErrors()));
        }
        return $ret;
    }
    /**客户评价送优惠券
     * @param $phone 客户电话
     * @param $level 补贴级别 5：10元 4：20元 3：30元 2：40元 1：50元
     * @param int $city_id  城市
     */
    public function bindBonusForOrderComment($city_id,$phone,$level){
        $model = new SubsidyRecord();
        $isVip = VipService::service()->isVip($phone);//验证客户手机号是否为VIP 禁用的vip不算vip
        $type = $isVip ? self::USER_TYPE_VIP : self::USER_TYPE_USER;
        $user_id = '';//用户id-如果vip用户为vipid 普通用户为t_customer_main的id
        if($isVip){
            $vipPhone = VipPhone::model()->getPrimary($phone);//根据电话获取vip卡信息
            $user_id = $vipPhone ? $vipPhone['vipid'] : 0;
        }else{
            $customerInfo = CustomerService::service()->getCustomerInfo($phone);
            $user_id = $customerInfo ? $customerInfo->id : 0;
        }
        $arr = array('phone'=>$phone,'level'=>$level);
        $metajson = json_encode($arr);//电话和级别转json格式存入meta字段

        $model->type = $type;
        $model->user_id = $user_id;
        $model->city_id = $city_id;
        $model->date =  date('Y-m-d');//字段限制只能为10个字符
        $model->created =  date('Y-m-d H:i:s');
        $model->subsidy_type = self::SUBSIDY_TYPE_COUPON;
        $model->meta =  $metajson;
        $ret = $model->save();//插入数据
        if(!$ret){
            EdjLog::info('insert subsidy_record fail: user_id:'.$user_id.'-phone:'.$phone.'--');
            FinanceUtils::sendFinanceAlarm('评价送优惠券入库数据失败 user_id ----- ', $user_id.'==='.$phone);
            EdjLog::info(json_encode($model->getErrors()));
        }
        return $ret;
    }

    /**  因洗车业务支付方案调整如下：一口价洗车订单，司机端不收费，司机应得的钱通过信息费充值到司机账户；
     * 洗车业务上线后，需要每日按照规则给司机转账信息费，作为收入，转账金额为：一口价洗车订单报单数*20元/单
     * @param $driverId
     * @param $city_id
     * @param $order_id
     * @param $created //订单时间
     */
    public function washCarUnitInsert($driverId,$city_id,$order_id,$created){
        $model = new SubsidyRecord();
        $config = FinanceConfig::model()->getConfig(FinanceConfig::TYPE_SUBSIDY_WASH);
        $cast = isset($config['cast']) ? $config['cast'] : 20;//一口价洗车的金额默认为20
        if($city_id != 1){
            $cast = 19;//非北京的城市都是补助19
        }

        $model->type = self::USER_TYPE_DRIVER;//司机类型
        $model->user_id = $driverId;
        $model->city_id = $city_id;
        $model->date =  date('Y-m-d');
        $model->created =  date('Y-m-d H:i:s',$created);//此处写入订单的时间
        $model->subsidy_type = self::SUBSIDY_TYPE_WASH_UNIT;
        $model->order_id =  $order_id;
        $model->cast =  $cast;
        $ret = $model->save();//插入数据
        if(!$ret){
            EdjLog::info('insert subsidy_record fail: driverId:'.$driverId.'--');
            FinanceUtils::sendFinanceAlarm('洗车一口价报单插入数据失败 driverID ----- ', $driverId);
            EdjLog::info(json_encode($model->getErrors()));
        }
        return $ret;
    }

    /**距离远也尽量接单，大于2km订单公司有每公里5元的补助。
     * 测试城市：广州，北京
     * 补贴到账：每周四
     * @param $driverId
     * @param $city_id
     * @param $order_id
     * @param $created
     * 2015-01-19
     */
    public function remoteDispatchInsert($driverId,$cast,$city_id,$order_id,$created){
        $model = new SubsidyRecord();

        $model->type = self::USER_TYPE_DRIVER;//司机类型
        $model->user_id = $driverId;
        $model->city_id = $city_id;
        $model->push_status = self::PUSH_STATUS_DONE;//此处必须是1
        $model->date =  date('Y-m-d');
        $model->created =  date('Y-m-d H:i:s',$created);
        $model->subsidy_type = self::SUBSIDY_TYPE_REMOTEORDER;
        $model->order_id =  $order_id;
        $model->cast =  $cast;
        $ret = $model->save();//插入数据
        if(!$ret){
            EdjLog::info('remote dispatch subsidy_record fail: driverId:'.$driverId.'--');
            FinanceUtils::sendFinanceAlarm('远程派单补贴插入数据失败 driverID ----- ', $driverId);
            EdjLog::info(json_encode($model->getErrors()));
        }
        return $ret;

    }
    /**1.司机拉取新司机接口
     * 2.司机春节送回家
     * 补贴到账：给头天的司机充值信息费
     * @param $driverId
     * @param $city_id
     * @param $order_id
     * @param $created
     * @param $subsidy_type
     * @param $meta  春节回家对应的司机短信
     * 2015-01-20
     */
    public function newDriverFetchInsert($driverId,$cast,$city_id,$order_id,$created,$subsidy_type='',$meta=''){
        EdjLog::info("拉新司机 $subsidy_type 传进来的参数".json_encode(func_get_args(),true));
        if(empty($subsidy_type)){
            $subsidy_type = self::SUBSIDY_TYPE_FETCHNEWDRIVER;
        }
        $model = new SubsidyRecord();

        $model->type = self::USER_TYPE_DRIVER;//司机类型
        $model->user_id = $driverId;
        $model->city_id = $city_id;
        $model->push_status = self::PUSH_STATUS_DONE;//此处必须是1
        $model->date =  date('Y-m-d');
        $model->created =  $created;//传过来就是时分秒格式
        $model->subsidy_type = $subsidy_type;
        $model->order_id =  $order_id;
        $model->cast =  $cast;
        $model->meta =  $meta;
        $ret = $model->save();//插入数据
        if(!$ret){
            EdjLog::info('remote dispatch subsidy_record fail: driverId:'.$driverId.'--');
            FinanceUtils::sendFinanceAlarm('拉取新司机或春节司机送回家补贴插入数据失败 driverID ----- ', $driverId."==subsidy_type: $subsidy_type");
            EdjLog::info(json_encode($model->getErrors()));
        }
        return $ret;

    }



}