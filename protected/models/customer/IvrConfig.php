<?php

/**
 * This is the model class for table "{{ivr_config}}".
 *
 * The followings are the available columns in table '{{ivr_config}}':
 * @property string $id
 * @property string $name
 * @property string $sms
 * @property string $other_sms
 * @property integer $bonus_code_id
 * @property string $citys;
 * @property string $created
 * @property string $update_by
 * @property string $update
 * @property integer $status
 * @property timestamp $start_time
 * @property timestamp $end_time
 * @property integer $use_scope
 * @property integer $repeat_use
 */
class IvrConfig extends FinanceActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return IvrConfig the static model class
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
        return '{{ivr_config}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name,', 'required'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => '序号',
            'name' => '配置名字',
            'sms' => '短信内容',
            'bonus_code_id' => '优惠码ID',
            'citys' => '允许使用城市',
            'created' => '创建时间',
            'update_by' => '更新者',
            'update' => '生成时间',
            'status' => '状态',
            'start_time' => '开始使用时间',
            'end_time' => '结束使用时间',
            'use_scope' => '使用范围',
            'repeat_use' => '是否允许多次使用',
        );
    }


    /**
     * 保存之前要更新的字段
     * @return bool
     * author mengtianxue
     */
    public function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->created = date("Y-m-d H:i:s");
            } else {
                $this->update_by = Yii::app()->user->getId();
                $this->update = date("Y-m-d H:i:s");
            }
            return true;
        }
        return parent::beforeSave();
    }

    public function getIvrConfigByID($id)
    {
        $ivr_config = self::model()->findByPk($id);
        return $ivr_config;
    }


    //ivr 语音导航短信设置
//如果是座机、VIP、司机不赠送优惠券
//固定码，允许一个手机多次绑定）
//配置名称	优惠券id（不是必须的，） 短信sms(只走配置短信）	城市（包含全国）（先去查找所属的城市，如果没有去查看是否有全国通用的优惠券，在没有就break） 是否启用
    public function callHandle($phone)
    {
        $ivrConfigArr = self::model()->findAll("status = :status and start_time <= now() and end_time >= now()", array("status" => 1));

        $phone = trim($phone);

        if (Common::checkPhone($phone)) {
            //
        } else {
            Putil::report($phone . "非手机号");
            return;
        }
        $city_id = Helper::PhoneLocation($phone);

        if (empty($city_id)) {
            $tip = "找不到归属地,走全国配置";
            $city_id = 0;
            Putil::report($tip, 1);
        }

        $black_customer = CustomerStatus::model()->is_black($phone);;  //此处调用个黑名单验证方法(走缓存)
        $bind_right = true;
        if (empty($black_customer)) {
            // pass
        } else {
            Putil::report($phone . "黑名单,不允许绑定优惠券");
            $bind_right = false;
        }


        if (count($ivrConfigArr) == 0) {
            $tip = "没有配置信息";
            Putil::report($tip);
            return;
        }
        //找到该城市的不绑定优惠劵短息集合如果没有找出全国通用的
        $otherSmsArr = $this->getSms($ivrConfigArr, $city_id);
        if (empty($otherSmsArr) || count($otherSmsArr) == 0) {
            $otherSmsArr = $this->getSms($ivrConfigArr, 0);
        }

        //如果是vip或司机直接发送短信
        if ($this->checkPhone($phone)) {
            if (empty($otherSmsArr) || count($otherSmsArr) == 0) {
                $tip = "未找到不绑定优惠券时可发送的短信";
                Putil::report($tip);
                return;
            } else {
                $this->send_sms($phone, $otherSmsArr[0]);
                return;
            }
        }

        //先进行初步处理
        $first_res = self::checkCityConf($ivrConfigArr,$city_id,$phone);
        Putil::report("first time handle result is ".$first_res);
        if($first_res == 1){
            $bonusLibraryArr = self::findBonusLibrary($ivrConfigArr, $city_id);
        }elseif($first_res == 2){
            $city_id = 0;
            $bonusLibraryArr = self::findBonusLibrary($ivrConfigArr, 0);
        }else if($first_res == 0){
            return;
        }

        //Putil::report("当前有效配置优惠券信息：".print_r($bonusLibraryArr));


        //找到该城市的优惠券集合如果没有找出全国通用的优惠券
        if (count($bonusLibraryArr) == 0) {
            $city_id = 0;
            $bonusLibraryArr = self::findBonusLibrary($ivrConfigArr, 0);
        }
        $is_continue = 1;
        if ($bind_right) {
            //从可用的优惠券中绑定，用户只能在07：00到第二天07：00只能绑定一次优惠券
            if (CallBindLog::model()->isBind($phone . "ivr")) {
                $tip = "该用户已在特定时间段绑定过";
                Putil::report($tip);
                $is_continue = 1;
            } else {
                foreach ($bonusLibraryArr as $i) {
                    $r = self::bind_bonus($i, $phone, $city_id);
                    $tip = $phone . "|绑定优惠券|result|" . json_encode($r) . "|bonus_sn|" . $i->bonus_sn;
                    Putil::report($tip);
                    if ($r['code'] == -1) {
                        $is_continue = 1;
                        break;
                    } elseif ($r['code'] == 0) {
                        $is_continue = 0;
                        $this->log($phone, $i->bonus_id); // 记录绑定log
                        Putil::report($tip);
                        $callfigArr = IvrConfig::model()->findAll("bonus_code_id=:id", array('id' => $i->bonus_id));
                        foreach ($callfigArr as $callfig) {
                            if (!empty($callfig) && !empty($callfig['sms'])) {
                                $citys = $callfig['citys'];
                                if (empty($citys)) {
                                    //pass
                                } else {
                                    $citysArr = json_decode($citys);
                                    if (in_array($city_id, $citysArr)) { //判断对应城市配置的对应短信
                                        $this->send_sms( $phone, $callfig['sms']);
                                        Putil::report("已发送优惠券对应短信");
                                        break;
                                    }
                                }
                            }
                        }
                        break;
                    }
                }
            }
        }
        //如果没有可绑优惠券，请发送短信,30分钟内只可以发送一条
        if ($is_continue == 1) {
            Putil::report("未绑定优惠券，走发送短信通道");
            if (empty($otherSmsArr) || count($otherSmsArr) == 0) {
                $tip = "未找到不绑定优惠券时可发送的短信";
                Putil::report($tip);
                return;
            } else {
                $this->send_sms($phone, $otherSmsArr[0]);
            }
        }
        Putil::report("处理完毕");
    }

    function bind_bonus($i, $phone, $city_id)
    {
        $callfigArr = IvrConfig::model()->findAll("bonus_code_id=:id", array('id' => $i->bonus_id));
        foreach ($callfigArr as $callfig) {
            if (!empty($callfig)) {
                $citys = $callfig['citys'];
                if (empty($citys)) {
                    //pass
                } else {
                    $citysArr = json_decode($citys);
                    if (in_array($city_id, $citysArr)) { //判断对应城市配置的对应的优惠券配置
                        if($callfig['use_scope'] == 1){ // 只允许新客使用
                            if(CustomerOrderReport::model()->isNewAppCustomer($phone)){
                                Putil::report("是app新客");
                                // pass
                            }else{
                                $ret = array(
                                    'code' => -2,
                                    'message' => '这个配置只允许App新客绑定一次，但用户不是App新客');
                                return $ret;
                            }
                        }

                        if ($callfig['repeat_use'] == 0) { // 不可以重复绑定
                            if (CallBindLog::model()->isBind2($phone . "ivr")) {
                                $ret = array(
                                    'code' => -1,
                                    'message' => '这个配置允许用户绑定一次，用户已经绑定过了');
                                return $ret;
                            }
                        }
                        break;
                    }
                }
            }
        }
        $r = BonusLibrary::model()->BonusBinding($i->bonus_sn, $phone, 0, 0, 1, 0, 1);
        return $r;

    }


    /**
     * 30分钟内是否发送短信check
     * @param $cache_key
     * @return bool
     */
    function isSendSms($cache_key)
    {

        $flag = Yii::app()->cache->get($cache_key);
        Putil::report("是否发送短信:" . $flag);
        if (empty($flag))
            return true;
        else
            return false;
    }

    //插入绑定优惠券处理记录
    function log($phone, $bonus_id)
    {
        $log = CallBindLog::model()->find("phone=:phone", array('phone' => $phone . "ivr"));
        if ($log) {
            $log['bonus_code_id'] = $bonus_id;
            $log->update();
        } else {
            $log = new CallBindLog();
            $log['phone'] = $phone . "ivr";
            $log['bonus_code_id'] = $bonus_id;
            $log->save();
        }
    }

    function checkCityConf($callConfigArr,$city_id,$phone){
        foreach ($callConfigArr as $config) {
            if ($config['citys'] == "null" || empty($config['citys']))
                continue;
            else
                $citys = json_decode($config['citys']);
            if (in_array($city_id, $citys)){
                if(isset($config['bonus_code_id']) && self::checkBonus($config['bonus_code_id'])){
                    return 1; // 继续处理进行优惠券绑定
                }else{
                    if(isset($config['other_sms'])){
                        if(empty($config['other_sms'])){
                            return 2; // 应该走全国配置
                        }else{
                            Putil::report("此城市没有配置优惠券或优惠券已过期，直接发送短信");
                            $this->send_sms($phone,$config['other_sms']);
                            return 0; // 终止处理
                        }
                    }
                }
            }

        }
        return 2;
    }

    function checkBonus($code){
        $library = BonusLibrary::model()->find("bonus_id = :codeID", array("codeID" => $code));
        $now = date('y-m-d h:i:s');
        if (strtotime($library->end_date) > strtotime($now)) {
            return true;
        }
    }


    //找到对应城市的优惠券
    function findBonusLibrary($callConfigArr, $city_id)
    {
        $bonusCodeArr = array();
        foreach ($callConfigArr as $config) {
            if ($config['citys'] == "null" || empty($config['citys']))
                continue;
            else
                $citys = json_decode($config['citys']);
            if (in_array($city_id, $citys))
                array_push($bonusCodeArr, $config['bonus_code_id']);
        }
        //筛选出可用的优惠券(未过期)
        $bonusLibraryArr = array();
        foreach ($bonusCodeArr as $code) {
            if (!empty($code)) {
                $library = BonusLibrary::model()->find("bonus_id = :codeID", array("codeID" => $code));
                $now = date('y-m-d h:i:s');
                if (strtotime($library->end_date) > strtotime($now)) {
                    array_push($bonusLibraryArr, $library);
                }
            }
        }
        return $bonusLibraryArr;
    }

    //找到对应城市的不绑定优惠券短信内容
    function getSms($callConfigArr, $city_id)
    {
        $otherSmsArr = array();
        foreach ($callConfigArr as $config) {
            if ($config['other_sms'] == "null" || empty($config['other_sms']))
                continue;
            else
                $citys = json_decode($config['citys']);
            if (in_array($city_id, $citys))
                array_push($otherSmsArr, $config['other_sms']);
        }
        return $otherSmsArr;
    }

    function checkPhone($phone)
    {
        $vipPhone = VipPhone::model()->getPrimary($phone);
        if ($vipPhone) {
            return true;
        }
        $driver = Driver::model()->getDriverByPhone($phone);
        if ($driver)
            return true;
        return false;
    }

    function send_sms($phone, $content)
    {
        //Sms::SendSMS($phone, $content);
        //上面接口会是阻塞请求响应的速度，为了达到尽最快的速度响应，短信发送时机使用异步发送，请不要随意更改下面的log内容，open.api端将根据该log进行短信处理
        Putil::report($phone . "|发送短信：sms_start" . $content."sms_end");
        Putil::report("sms suc send");
    }


    public function insertRepeatCheck($cityIdArr)
    {
        if (!empty($cityIdArr)) {
            foreach ($cityIdArr as $city) {

            }
        }
    }

    function getAllCityArr($skip = -1)
    {
        $ivrConfigArr = self::model()->findAll("status = :status and id != :id", array("status" => 1, "id" => $skip));
        $cityArr = array();
        foreach ($ivrConfigArr as $config) {
            if ($config['citys'] == "null" || empty($config['citys']))
                continue;
            else {
                $citys = json_decode($config['citys']);
                for ($i = 0; $i < count($citys); $i++) {
                    if (in_array($citys[$i], $cityArr)) {
                        //
                    } else {
                        array_push($cityArr, $citys[$i]);
                    }
                }
            }

        }
        return $cityArr;
    }

}