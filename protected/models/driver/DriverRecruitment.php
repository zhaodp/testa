<?php

/**
 * This is the model class for table "{{driver_recruitment}}".
 *
 * The followings are the available columns in table '{{driver_recruitment}}':
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
 * @property integer $inform_time
 * @property integer $cultivate_time
 * @property integer $entrant_time
 * @property integer $discard_time
 * @property integer $apply_time
 * @property integer $batch
 * @property integer $driver_id
 * @property integer $imei
 * @property integer $driver_phone
 * @property integer $noentry
 * @property integer $complete
 * @property string $other_src
 * @property integer $confirmfees_time
 * @property integer $exam_times
 * @property string $rank
 * @property string $recommender
 */

class DriverRecruitment extends CActiveRecord {

    //短信类型（普通）
    const SMS_TYPE_COMMON = 1;
    //短信类型（通知面试）
    const SMS_TYPE_EXAM = 2;
    //面试cached前缀
    const INTERVIEW_PREFIX = 'INTERVIEW_PREFIX_';

    const DRIVER_ENTRY_PREFIX = 'DRIVER_ENTRY_PREFIX_';

    public static $sms_type_dict = array(
        self::SMS_TYPE_COMMON => '普通短信',
        self::SMS_TYPE_EXAM => '面试通知'
    );

    /****exam字段***/
    const STATUS_ONLINE_EXAM_INIT = 0;//默认状态
    const STATUS_ONLINE_EXAM_PASS = 1;//在线考核通过
    const STATUS_ONLINE_EXAM_FAILED =2; //在线考核不通过

    public static $exam_dict_source=array(
        self::STATUS_ONLINE_EXAM_INIT,
        self::STATUS_ONLINE_EXAM_FAILED,
        self::STATUS_ONLINE_EXAM_PASS,
    );

    public static $exam_dict= array(
        -1=>'全部',
        self::STATUS_ONLINE_EXAM_INIT=>'未参加',
        self::STATUS_ONLINE_EXAM_FAILED=>'不通过',
        self::STATUS_ONLINE_EXAM_PASS=>'通过',
    );

    /*****road_new字段*****/
    const STATUS_ROAD_INIT=0;//未预约
    const STATUS_ROAD_RESERVATION=1;//已预约
    const STATUS_ROAD_FIELD_PROCESS=2;//路考进行中
    const STATUS_ROAD_FIELD_PASS=3;//路考通过
    const STATUS_ROAD_FIELD_FAILED=4;//路考不通过

    public static $road_dict_source=array(
        self::STATUS_ROAD_INIT,
        self::STATUS_ROAD_RESERVATION,
        self::STATUS_ROAD_FIELD_PROCESS,
        self::STATUS_ROAD_FIELD_FAILED,
        self::STATUS_ROAD_FIELD_PASS,
    );

    public static $road_dict=array(
        -1=>'全部',
        self::STATUS_ROAD_INIT=>'未预约',
        self::STATUS_ROAD_RESERVATION=>'已预约',
        self::STATUS_ROAD_FIELD_PROCESS=>'进行中',
        self::STATUS_ROAD_FIELD_FAILED=>'不通过',
        self::STATUS_ROAD_FIELD_PASS=>'通过',
    );

    /*****status字段****/
    //司机入职status状态（旧版本）
    const STATUS_ENROLL = 1; //已报名
    const STATUS_EXAM_PASS = 7; //在线考试通过
    const STATUS_INTERVIEW_PASS = 2; //面试通过
    const STATUS_ROAD_PASS = 3; //路考通过
    const STATUS_ENTRY_OK = 4; //成功签约
    //新增已领装备状态
    const STATUS_SIGNED=8;//装备订单签收

    public static $status_dict_source=array(
        self::STATUS_ENROLL,
        self::STATUS_INTERVIEW_PASS,
        self::STATUS_ROAD_PASS,
        self::STATUS_ENTRY_OK,
        self::STATUS_SIGNED,
        self::STATUS_EXAM_PASS,
    );

    public static $status_dict = array(
        0=>'全部',
        self::STATUS_ENROLL=>'已报名',
        self::STATUS_INTERVIEW_PASS=>'已面试通过',
        self::STATUS_ROAD_PASS=>'已路考通过',
        self::STATUS_ENTRY_OK=>'已签约',
        self::STATUS_SIGNED=>'已领装备',
        self::STATUS_EXAM_PASS=>'在线考核通过',
    );

    public static $domicile_map = array(
        11 => '北京',
        12 => '天津',
        13 => '河北',
        14 => '山西',
        15 => '内蒙古',
        21 => '辽宁',
        22 => '吉林',
        23 => '黑龙江',
        31 => '上海',
        32 => '江苏',
        33 => '浙江',
        34 => '安徽',
        35 => '福建',
        36 => '江西',
        37 => '山东',
        41 => '河南',
        42 => '湖北',
        43 => '湖南',
        44 => '广东',
        45 => '广西',
        46 => '海南',
        50 => '重庆',
        51 => '四川',
        52 => '贵州',
        53 => '云南',
        54 => '西藏',
        61 => '陕西',
        62 => '甘肃',
        63 => '青海',
        64 => '宁夏',
        65 => '新疆',
    );

    public $verifyCode; //为User Model 设置一个新的属性
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return DriverRecruitment the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{driver_recruitment}}';
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
                'name, mobile, city_id, district_id, work_type, gender, age, id_card, id_driver_card, assure, marry, political_status, driver_type,  driver_year,  src, signup_src',
                'required'),
            array (
                'experience',
                'length',
                'max'=>1024),
            array (
                'company',
                'length',
                'max'=>100),
            array (
                'join_company',
                'length',
                'max'=>100),
            array (
                'company_contact',
                'length',
                'max'=>50),
            array (
                'city_id, road_new, id_driver_card, district_id, age, political_status, edu, driver_type, driver_year, status, inform_time, cultivate_time,confirmfees_time, entrant_time, discard_time, apply_time,batch, signup_src, wechat_bind_send_msg_time',
                'numerical',
                'integerOnly'=>true),
            array(
                'age',
                'numerical',
                'integerOnly'=>true,
                'min'=>23,
                'max'=>60),
            array (
                'name, pro, contact,interview',
                'length',
                'max'=>50),
            array (
                'mobile,driver_phone,company_mobile',
                'length',
                'max'=>11),
            array (
                'complete,exam_times',
                'length',
                'max'=>4),
            array(
                'driver_id, rank, recommender',
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
                'address, domicile, recycle_reason,imei,qr_code',
                'length',
                'max'=>255),
            array (
                'domicile',
                'length',
                'max'=>10),
            array (
                'ip,src',
                'length',
                'max'=>15),
            array (
                'other_src',
                'length',
                'max'=>50),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array (
                'id, name, mobile, road_new, city_id, src, other_src,district_id, work_type, gender, age, id_card, id_driver_card, domicile, register_city, assure, marry, political_status, edu, pro, driver_type, driver_card, driver_year, driver_cars, contact, contact_phone, contact_relate, experience, status, recycle, recycle_reason, ip, inform_time, cultivate_time, entrant_time, discard_time,apply_time,confirmfees_time,batch,driver_id,driver_phone,imei,noentry,complete,exam_times, rank, recommender, interview, road, signup_src',
                'safe',
                'on'=>'search'),
            array(
                'id_card',
                'match',
                'pattern' => '/^(\d{18,18}|\d{15,15}|\d{17,17}x)$/i'),
            array(
                'mobile',
                'match',
                'pattern' => '/^1[345678]{1}[0-9]{9}$/'),
            array(
                'id_driver_card',
                'length',
                'min' => 10,
                'max' => 12,),
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

    /**
     * @param $id
     * @param $status
     * @param string $operator
     * @return mixed
     * 更新司机入职状态
     */
    public function updateStatus($id, $status, $operator = 'system'){
        $model = new DriverRecruitment();
        $result = $model->updateByPk($id,array('status'=>$status));
        if($result){
            EdjLog::info('operator='.$operator.'更新id='.$id.'状态为='.$status.'成功');
        }else{
            EdjLog::info('operator='.$operator.'更新id='.$id.'状态为='.$status.'失败');
        }
        return $result;
    }


    public function updateStatusByDriverId($driver_id,$status,$operator = 'system'){
        $recruitment_info = DriverRecruitment::model()->findByDriverId($driver_id);
        if($recruitment_info && isset($recruitment_info->id)){
            $id = $recruitment_info->id;
            return $this->updateStatus($id,$status,$operator);
        }
        return false;


    }

    /***
     * @param $id_card
     * @param $road
     * @param string $operator
     * @return mixed
     * 更新路考状态
     */
    public function updateRoadStatus($id_card, $road, $operator='system'){
        $model = new DriverRecruitment();
        $result = $model->updateAll(array('road_new'=>$road),'id_card=:id_card',array('id_card'=>$id_card));
        if($result){
            EdjLog::info('路考operator='.$operator.'更新id_card='.$id_card.'状态为='.$road.'成功');
        }else{
            EdjLog::info('路考operator='.$operator.'更新id_card='.$id_card.'状态为='.$road.'失败');
        }
        return $result;
    }

    /***
     * @param $id_card
     * @param $road
     * @param string $operator
     * @return mixed
     * 更新在线考核状态
     */
    public function updateExamStatus($id_card, $exam, $operator='system'){
        $model = new DriverRecruitment();
        $result = $model->updateAll(array('exam'=>$exam),'id_card=:id_card',array('id_card'=>$id_card));
        if($result){
            EdjLog::info('在线考核operator='.$operator.'更新id_card='.$id_card.'状态为='.$exam.'成功');
        }else{
            EdjLog::info('在线考核operator='.$operator.'更新id_card='.$id_card.'状态为='.$exam.'失败');
        }
        return $result;
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
//		if ($this->district_id==0) {
//			$this->addError('district_id', '请选择居住区域');
//			return false;
//		}
        if ($this->age<18||$this->age>60) {
            $this->addError('age', '年龄必须在18岁到60岁之间');
            return false;
        }
        if ( (time() - $this->driver_year) < 157680000) {
            $this->addError('driver_year', '驾龄必须在5年以上');
            return false;
        }
        if ( $this->src==8){
            if($this->other_src==""||empty($this->other_src)){
                $this->addError('other_src', '请输入来源渠道');
                return false;
            }
        }
        return true;
    }


    public function countAge($cardid) {
        $ageyear =substr( $cardid,6,4);
        /*
        $agemonth =substr( $cardid,10,2);
        $ageday =substr( $cardid,12,2);
        $age=$ageyear.'-'.$agemonth.'-'.$ageday ;
        $data = strtotime(trim($age));
        $now = time();
        $age = ($now-$data)/60/60/24/365;
        $age = intval($age);
         */
        $age = date('Y') - $ageyear;
        return $age ;
    }

    public function countSex($cardid) {
        $sex =intval(substr( $cardid,-2,-1))%2;
        $sex = ($sex == 0 ? 2 : 1);
        return $sex ;
    }

    /**
     * 取身份证号码所在地
     */
    public function countDomicile($id){
        $domicile = intval(substr($id, 0, 2));
        return isset(self::$domicile_map[$domicile]) ? self::$domicile_map[$domicile] : '';
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
            'id_driver_card'=>'驾照档案编号',
            'domicile'=>'户口所在地',
            'register_city' => '户口所在市',
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
            'company'=>'现(前)单位名称',
            'company_mobile'=>'联系方式',
            'company_contact'=>'联系人',
            'join_company'=>'单位名称',
            'experience'=>'代驾经验',
            'status'=>'状态',
            'recycle'=>'是否回收',
            'recycle_reason'=>'回收原因',
            'ip'=>'IP',
            'inform_time'=>'通知培训时间',
            'cultivate_time'=>'已培训考核时间',
            'entrant_time'=>'签约时间',
            'discard_time'=>'回收时间',
            'apply_time'=>'报名时间',
            'batch'=>'批次',
            'driver_phone'=>'司机工作号码',
            'imei'=>'IMEI',
            'driver_id'=>'司机工号',
            'noentry'=>'未签约原因',
            'complete'=>'是否完整',
            'src'=>'来源渠道',
            'other_src'=>'其他来源渠道',
            'confirmfees_time'=>'财务确认收款时间',
            'rank' => '司机等级',
            'recommender' => '推荐人编号',
            'interview' => '面试成绩',
            'road' => '路考成绩',
            'height' => '司机身高',
            'size' => '司机上衣尺寸',
            'mail_phone' => '收货人电话',
            'mail_name' => '收货人姓名',
            'mail_addr' => '收货人地址',
            'qr_code' => '司机报名信息二维码',
            'road_new'=>'新路考状态',
            'wechat_bind_send_msg_time'=>'绑定短信发送次数'
        );
    }

    public function setZhaopinSuccessStatus($id_card)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition("id_card='$id_card'");
        $model = DriverRecruitment::Model()->find($criteria);
        if (!empty($model))
        {
            $dataZhaopin = $model->attributes;
            $status = $dataZhaopin['status'];
            $dataZhaopin['status'] = 4;
            $dataZhaopin['entrant_time'] = time();
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
        $criteria->compare('id_driver_card', $this->id_driver_card, true);
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
        $criteria->compare('inform_time', $this->inform_time);
        $criteria->compare('cultivate_time', $this->cultivate_time);
        $criteria->compare('entrant_time', $this->entrant_time);
        $criteria->compare('discard_time', $this->discard_time);
        $criteria->compare('apply_time', $this->apply_time);
        $criteria->compare('confirmfees_time', $this->confirmfees_time);
        $criteria->compare('batch', $this->batch);
        $criteria->compare('imei', $this->imei);
        $criteria->compare('driver_id', $this->driver_id);
        $criteria->compare('driver_phone', $this->driver_phone);
        $criteria->compare('noentry', $this->noentry);
        $criteria->compare('complete', $this->complete);
        $criteria->compare('rank',$this->rank,true);
        $criteria->compare('recommender',$this->recommender,true);
        $criteria->compare('interview',$this->interview,true);
        $criteria->compare('road',$this->road,true);
        if ($this->register_city) {
            $criteria->compare('register_city',$this->register_city,true);
        }

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
            $zhaopin = new DriverRecruitment();
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $quest_id);
            $zhaopinList = $zhaopin->findAll($criteria);

            if($zhaopinList){
                $num = 0;
                $sms_message = $data['sms_content'];
                foreach($zhaopinList as $list){
                    $phone = $list->mobile;
                    Sms::SendSMS($phone, $sms_message);
                    $zhaopin->updateAll(array('status'=>'2','batch'=>$data['batch'],'inform_time'=>time()),'id = :id',array(':id'=>$list->id));
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
        $zhaopin = new DriverRecruitment();
        $zhaopinList = $zhaopin->findAll('batch = :batch and status = 2 and complete = 0',array(':batch'=>$batch));
        if($zhaopinList){
            foreach($zhaopinList as $list){
                $phone = $list->mobile;
                if($this->smsLog($phone) < 2){
                    $zhaopin->updateAll(array('status'=>'1','batch'=>0,'inform_time'=>0),'id = :id',array(':id'=>$list->id));
                    $this->insertLog($list->id_card);
                    $Reduction++;
                }else{
                    $zhaopin->updateAll(array('recycle'=>'1','recycle_reason'=>'通知两次都没有来培训','discard_time'=>time()),'id = :id',array(':id'=>$list->id));
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
        return $log == false ? 0 : $log;
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
            $zhaopin = new DriverRecruitment();
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
                $DriverZhaopinPrefile = DriverRecruitment::model()->find('id_card = :id_card',array(':id_card'=>$data['id_card']));
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
        $driverZhaopinInfo['cultivate_time'] = $data['status'] == 3 ? time() : 0;
        $driverZhaopinInfo['city_id'] = $city_id;
        $driverZhaopinInfo['batch'] = $data['batch'];
        $driverZhaopinInfo['apply_time'] = time();
        $driverZhaopinInfo['inform_time'] = time();
        $model->attributes = $driverZhaopinInfo;
        $model->update();
    }

    public function insertDriverZhaopin($data){
        $model = new DriverRecruitment();
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
        $driverZhaopinInfo['cultivate_time'] = $data['status'] == 3 ? time() : 0;
        $driverZhaopinInfo['city_id'] = $city_id;
        $driverZhaopinInfo['batch'] = $data['batch'];
        $driverZhaopinInfo['apply_time'] = time();
        $driverZhaopinInfo['inform_time'] = time();
        $model->attributes = $driverZhaopinInfo;
        $model->insert();
    }

    /**
     * 批量签约
     */
    public function batchEntry($data){
        $return = 0; //失败
        $driverZhaopin = new DriverRecruitment();
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
                                    $driverZhaopin->updateAll(array('entrant_time'=>time(),'status'=>4,'noentry'=>0),'status = 3 and id = :id',array(':id'=>$list->id));
                                }else{
                                    $driverZhaopin->updateAll(array('entrant_time'=>time(),'status'=>4),'status = 3 and id = :id',array(':id'=>$list->id));
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
        $driverZhaopin = new DriverRecruitment();
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
    /**
     * 批量激活不加批次 并增加财务确认环节
     */
    public function batchActivationNoBatch($data){
        $return = 0;
        $driverZhaopin = new DriverRecruitment();
        $criteria = new CDbCriteria();
        if(isset($data['id'])){
            $quest_id = explode(',', $data['id']);
            $criteria->addInCondition('id', $quest_id);
        }
        $criteria->addCondition('status = 6');
        $criteria->addCondition('complete = 1');
        $driverZhaopinList = $driverZhaopin->findAll($criteria);
        if($driverZhaopinList){
            foreach($driverZhaopinList as $list){
                $id = $list->driver_id;
                $mark =Driver::MARK_ENABLE;
                $type = DriverLog::LOG_MARK_ENABLE;
                $reason = '激活';
                if(Driver::block($id, $mark, $type, $reason)){
                    $driverZhaopin->updateAll(array('status'=>5),'status = 6 and id = :id',array(':id'=>$list->id));
                }
                //	记录流水日志
                $insertArr = array();
                $insertArr['name'] =$list->name;
                $insertArr['id_card'] =$list->id_card;
                $insertArr['message'] ='激活';
                $insertArr['time'] =time();
                Yii::app()->db	->createCommand()->insert('t_recruitment_log', $insertArr);
                unset($insertArr);
                //end
            }
            $return = 1;
        }
        return $return;
    }

    public function batchInfo($batch){
        $connection = Yii::app()->db;
        $sql = 'select SUM(IF((status = 4), 1, 0)) as entry,
				SUM(IF((status = 3), 1, 0)) as train,
				SUM(IF((status = 2), 1, 0)) as come,
				COUNT(*) as count
				from t_driver_recruitment where batch = :batch';
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

        $zhaopin = new DriverRecruitment();
        $log = $zhaopin::model()->find('id_card=:id_card', array (
            ':id_card'=>$id_card));
        $data = $log->attributes;
        $data['operator'] = $operator;
        $data['created'] = time();
        $connection = Yii::app()->dbstat
            ->createCommand()
            ->insert('t_driver_recruitment_log', $data);
    }

    public function insertRecruitmentLog($name,$id_card,$message){
        $data['name'] = $name;
        $data['id_card'] = $id_card;
        $data['message'] = $message;
        $data['time'] = time();
        $connection = Yii::app()->db
            ->createCommand()
            ->insert('t_recruitment_log', $data);
    }

    public function getDriverByIDCard($id_card) {
        $command = Yii::app ()->db_readonly->createCommand();
        $command->select('*');
        $command->from('t_driver_recruitment');
        $command->where('id_card=:id_card', array('id_card'=>$id_card));
        $data = $command->queryRow();
        return $data;
    }

    public function getDriverByDriverId($driverId){
        $command = Yii::app ()->db_readonly->createCommand();
        $command->select('*');
        $command->from('t_driver_recruitment');
        $command->where('id_card=:driverId', array('driverId'=>$driverId));
        $data = $command->queryRow();
        return $data;
    }

    /**
     * 根据报名流水号获得司机报名信息
     * @param $queue_number （报名流水号 如：BJ00012）
     * @return array
     */
    public function getDriverByQueueNumber($queue_number) {
        $id = substr($queue_number, 2);
        $command = Yii::app ()->db_readonly->createCommand();
        $command->select('*');
        $command->from('t_driver_recruitment');
        $command->where('id=:id', array('id'=>$id));
        $data = $command->queryRow();
        return $data;
    }

    /**
     * 增加某司机报名流水号到列表
     * @param $date 日期 如：20130607
     * @param $queue_number 报名流水号 如BJ00014
     * @return bool
     */
    public function setInterviewList($date, $queue_number) {
        $driver_data = $this->getDriverByQueueNumber($queue_number);
        if (is_array($driver_data) && count($driver_data)) {
            if ($driver_data['status'] == 7 || $driver_data['status'] == 2) {
                $key = self::INTERVIEW_PREFIX.$date;
                $cached = new DriverInterviewCache();
                $data = $this->getInterviewList($date);
                $data = is_array($data) ? $data : array();
                array_push($data, $queue_number);
                array_unique($data);
                $data = serialize($data);
                return $cached->set($key, $data);
            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    /**
     * 获得某天来面试的司机报名流水号列表
     * @param $date 日期 如：20130607
     * @return array|bool
     */
    public function getInterviewList($date) {
        $cached = new DriverInterviewCache();
        $key = self::INTERVIEW_PREFIX.$date;
        $data = $cached->get($key);
        if ($data) {
            return unserialize($data) ;
        } else {
            return false;
        }
    }

    /**
     * 将司机放入回收站
     * @param $id
     * @param $reason
     * @return int
     */
    public function deleteRecord($id, $reason,$operator='system') {
        $model = self::model()->findByPk($id);
        $dataRecruitment = $model->attributes;
        $dataRecruitment['recycle'] = '1';
        $dataRecruitment['discard_time'] = time();
        $dataRecruitment['recycle_reason'] = $reason;
        $model->attributes = $dataRecruitment;
        if($model->update()){
            //记录日志
            $insertArr = array();
            $insertArr['name'] = $dataRecruitment['name'];
            $insertArr['id_card'] = $dataRecruitment['id_card'];
            $insertArr['message'] = '将用户移动到回收站，原因：'.$reason.'操作人:'.$operator;
            $this->insertDriverStatusLog($insertArr);
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * 司机入回收站LOG
     * @param $inserArr
     */
    public function insertDriverStatusLog($inserArr){
        $data = array();
        $data['name'] = $inserArr['name'];
        $data['id_card'] = $inserArr['id_card'];
        $data['message'] = $inserArr['message'];
        $data['time'] = time();
        $connection = Yii::app()->db
            ->createCommand()
            ->insert('t_recruitment_log', $data);
    }

    /**
     * 增加某司机签约流水号到列表
     * @param $date 日期 如：20130607
     * @param $queue_number 报名流水号 如BJ00014
     * @return bool
     */
    public function setDriverEntryList($date, $queue_number) {
        $driver_data = $this->getDriverByQueueNumber($queue_number);
        if (is_array($driver_data) && count($driver_data)) {
            $status = $driver_data['status'];
            $canSigned = $this->canSigned($status,$driver_data['city_id']);
            if ($canSigned) {
                $key = self::DRIVER_ENTRY_PREFIX.$date;
                $cached = new DriverInterviewCache();
                $data = $this->getDriverEntryList($date);
                $data = is_array($data) ? $data : array();
                if (!in_array($queue_number, $data)) {
                    array_push($data, $queue_number);
                    array_unique($data);
                }
                $data = serialize($data);
                return $cached->set($key, $data);
            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    /**
     * @param $status
     * @param $cityId
     * @return bool
     * 是否能签约
     */
    public function canSigned($status,$cityId){
        $canSigned = false;
        //新流程，针对开通城市，签约和装备签收的流水号不能加入
        $open = DriverOrder::model()->checkOpenCity($cityId);
        if($open){
            if($status != self::STATUS_ENTRY_OK && $status != self::STATUS_SIGNED){
                $canSigned = true;
            }
        }else{
            if($status == self::STATUS_ROAD_PASS){
                $canSigned = true;
            }
        }
        return $canSigned;
    }

    /**
     * 将司机从当天的列表中删除
     * @param $queue_number 报名流水号
     * @return bool
     */
    public function removeDriverFromList($queue_number) {
        $date = date('Ymd', time());
        $data = $this->getDriverEntryList($date);
        if (in_array($queue_number, $data)) {
            $key = array_search($queue_number, $data);
            unset($data[$key]);
            $key = self::DRIVER_ENTRY_PREFIX.$date;
            $cached = new DriverInterviewCache();
            $data = serialize($data);
            return $cached->set($key, $data);
        } else {
            return false;
        }
    }

    /**
     * 获得某天来签约的司机报名流水号列表
     * @param $date 日期 如：20130607
     * @return array|bool
     */
    public function getDriverEntryList($date) {
        $cached = new DriverInterviewCache();
        $key = self::DRIVER_ENTRY_PREFIX.$date;
        $data = $cached->get($key);
        if ($data) {
            return unserialize($data) ;
        } else {
            return false;
        }
    }
    /*
    public function beforeSave() {
        $this->sort = $this->apply_time;
        return parent::beforeSave();
    }
    */
    /**
     * 获得司机状态信息LOG
     * @param $id_card
     * @return mixed
     */
    public function getRecruitmentLog($id_card) {
        $recruitment_log=Yii::app()->db_readonly->createCommand()->select('*')->from('t_recruitment_log')->where('id_card=:id_card', array(
            ':id_card'=>$id_card
        ))->order('time ASC')->queryAll();
        return $recruitment_log;
    }


    /**
     * 统计司机签约数据
     * @param int $city_id
     * @author duke
     * @return mixed
     */
    public function getSign($city_id = 0)
    {
        $where = 'status in(1,2,3,7) and recycle != 1';
        $params = array();
        if ($city_id != 0) {
            $where .= ' and city_id = :city_id';
            $params[':city_id'] = $city_id;
        }

        $num = Yii::app()->db_readonly->createCommand()
            ->select('count(1)')
            ->from('t_driver_recruitment')
            ->where($where, $params)
            ->queryScalar();
        return $num;

    }


    /**
     * 获取通知司机人数
     * @param int $city_id
     * @return mixed
     * author mengtianxue
     */
    public function getRecruitment($city_id = 0)
    {
        $inform_time = strtotime(date('Y-m-01'));
        $where = 'inform_time >= :inform_time';
        $params = array(':inform_time' => $inform_time);
        if ($city_id != 0) {
            $where .= ' and city_id = :city_id';
            $params[':city_id'] = $city_id;
        }

        $num = Yii::app()->db_readonly->createCommand()
            ->select('count(1) as notice')
            ->from('t_driver_recruitment')
            ->where($where, $params)
            ->queryScalar();
        return $num;
    }


    /**
     * 统计可以签约的司机
     * @param int $city_id
     * @return mixed
     */
    public function getCanJoinData($city_id = 0){
        //可签约
        $where = 'status = :status';
        $params = array(':status' => 3);
        if ($city_id != 0) {
            $where .= ' and city_id = :city_id';
            $params[':city_id'] = $city_id;
        }

        $entry = Yii::app()->db_readonly->createCommand()->select('count(1)')
            ->from('t_driver_recruitment')
            ->where($where,$params)
            ->queryScalar();
        return $entry;
    }

    /**
     * @param $cityId
     * @param $recruitmentId
     * @return string
     * 根据城市和招聘入职id，获取报名流水号
     */
    public function getSerialNum($cityId,$recruitmentId){
        $pre_city = RCityList::model()->getCityByID($cityId, 'city_prifix');
        $serialNum = $pre_city.$recruitmentId;
        return $serialNum;
    }

    public function findByDriverId($driver_id = 0)
    {
        $ret = DriverRecruitment::model()->findByAttributes(array('driver_id' => $driver_id));
        if (!$ret) {
            $driver = Driver::model()->findByAttributes(array('user' => $driver_id));
            $ret = DriverRecruitment::model()->findByAttributes(array('id_card' => $driver['id_card']));
        }
        return $ret;
    }
    /**
    *	更改司机入职状态
    *
    */
    public function changeStatus($id,$status,$operator){
    	EdjLog::info('operator='.$operator.',更改id='.$id.',状态status='.$status);
        $res = $this->updateByPk($id,array('status'=>$status));
        if($res){
        	EdjLog::info('operator='.$operator.',更改id='.$id.',状态status='.$status.' ok');
        }else{
        	EdjLog::info('operator='.$operator.',更改id='.$id.',状态status='.$status.' fail');
        }
        return $res;
    }

    public function findByIDCard($id_card){
        $ret = DriverRecruitment::model()->findByAttributes(array('id_card' => $id_card));
        return $ret;
    }

    /**
     * @param $driverId
     * 只有装备签收和在线考试通过才能开始工作,针对开通新规则的城市
     * @return boolean
     */
    public function canWork($driverId){
        $canWork = array(
            'code'=>0,
            'message'=>'可以正常工作'
        );
        $recruitment = $this->findByDriverId($driverId);
        //工作城市，获取t_driver表，防止更改司机城市
        $driver = Driver::model()->getProfile($driverId);
        if($recruitment && $driver){
            $open = DriverOrder::model()->checkOpenCity($driver['city_id']);
            if($open){
                //路考通过、在线考试通过、领取装备
                if($recruitment['exam'] != self::STATUS_ONLINE_EXAM_PASS){
                    $canWork['code']=3;
                    $canWork['message']='上线考核通过之后才能开始工作哟';
                    return $canWork;
                }
                if($recruitment['road_new'] != self::STATUS_ROAD_FIELD_PASS ){
                    $canWork['code']=3;
                    $canWork['message']='路考通过之后才能开始工作哟';
                    return $canWork;
                }
                //todo 加上装备已发放
            }
        }
        return $canWork;
    }

    /**
     * @param $idCard
     * 根据司机报名身份证，返回司机报名所有相关状态信息数组
     * @return array 见http://wiki.edaijia.cn/dwiki/doku.php?id=driver.signup.info
     */
    public function getDriverStateByIdCard($idCard)
    {
        $ret = array(
            'signup' => 0, //未报名
            'road' => self::STATUS_ROAD_INIT,
            'exam' => self::STATUS_ONLINE_EXAM_INIT,
            'sign' => 0,//未签约
            'complete' => 0,//未完善信息
            'apm_date' => '',//预约时间段
            'can_exam'=>true,//是否能在线考核，只有通过的不能考核
        );
        if ($idCard) {
            $recruitment = $this->findByIDCard($idCard);
            if ($recruitment) {
                //通过考试的，不能参加
                if($recruitment['exam'] == self::STATUS_ONLINE_EXAM_PASS){
                    $ret['can_exam'] = false;
                }
                $ret['signup'] = self::STATUS_ENROLL;
                $ret['road'] = (int)$recruitment['road_new'];
                //已预约，返回预约时间,格式如:2015年5月24日 上午10:00-12:00"
                $booking = BookingExamDriver::model()->getDriverLastBooking($idCard);
                if($booking){
                    $hours=$booking['hours'];
                    $cityId = $recruitment['city_id'];
                    $hours=BookingHoursSetting::model()->getHoursDesc($cityId,$hours);
                    $date =date ( "Y年m月d日",strtotime($booking['date']));
                    $date=$date.' '.$hours;
                    $ret['apm_date'] = $date;
                }
                $ret['exam'] = (int)$recruitment['exam'];
                //和司管app保持一致
                $driver_id = $recruitment['driver_id'];
                if ($driver_id) {
                    $ret['sign'] = self::STATUS_ENTRY_OK;
                }
                if ($recruitment['height']) {
                    $ret['complete'] = 1;
                }
            }
        }
        return $ret;
    }

    public function getInfoByOpenid($open_id){
        $info_mod = new Wechat();
        $weinfo = $info_mod->getInfo($open_id);
        $id_card = $weinfo['data']['idCardNum'];
        //$phone = $weinfo['data']['mobileNum'];
        $signupInfo = $this->find(' id_card = :card',array(':card'=>$id_card));
        if($signupInfo){
            $data = array(
                'name'=>$signupInfo->name,
                'id'=>$signupInfo->id,
                'city_id'=>$signupInfo->city_id,
                'id_card'=>$signupInfo->id_card,
                'qr_code'=>$signupInfo->qr_code,
                'phone'=>$signupInfo->mobile
            );
            return $data;
        }else return false;

    }


    public function getDriverByScendTimes($send_times, $city_id = '', $start_id = '') {
        $command = Yii::app ()->db_readonly->createCommand();
        $command->select('id,name,mobile,id_card,wechat_bind_send_msg_time');
        $command->from('t_driver_recruitment');
        $where = 'wechat_bind_send_msg_time < :wb';
        $params = array(':wb' => $send_times);
        if($city_id){
            $where .= ' and city_id = :city_id';
            $params[':city_id'] = $city_id;
        }

        if($start_id){
            $where .= ' and id > :start_id';
            $params[':start_id'] = $start_id;
        }
        $command->where($where,$params);
        $command->order('id asc');
        $command->limit(100);
        $data = $command->queryAll();
        return $data;
    }


    public function updateSendTimes($id,$times){
        $mod = new DriverRecruitment();
        //$data = array('');
        $res = $mod->updateByPk($id,array('wechat_bind_send_msg_time'=>$times));
        return $res;
    }

    /**
     * @param $recruitId
     * @return bool
     * 司机是否完善个人资料
     */
    public function isComplete($recruitId){
        $complete = false;
        $recruit = DriverRecruitment::model()->findByPk($recruitId);
        if($recruit){
            if($recruit['mail_province']){
                $complete = true;
            }
        }
        return $complete;
    }

    /***
     * @param $idCard
     * 判断司机是否签约，t_driver_recruitment中driver_id能找到或者t_driver中存在
     * @return boolean
     */
    public function isSigned($idCard){
        $signed = false;
        $recruitment = $this->getDriverByIDCard($idCard);
        $driver = Driver::model()->getDriverByIdCard($idCard);
        if($recruitment && $recruitment['driver_id']){
            $signed = true;
        }
        if($driver){
            $signed = true;
        }
        return $signed;
    }
}
//###
