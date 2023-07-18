<?php

/**
 * This is the model class for table "{{driver_ext}}".
 *
 * The followings are the available columns in table '{{driver_ext}}':
 * @property string $driver_id
 * @property integer $is_by_army
 * @property integer $is_full_time
 * @property integer $has_car
 * @property integer $license_date
 * @property string $mark_reason
 * @property integer $service_times
 * @property integer $high_opinion_times
 * @property integer $last_low_opinion_date
 * @property integer $low_opinion_times
 * @property integer $newest_high_opinion_times
 */
class DriverExt extends CActiveRecord
{
    const STATUS_NEED_TRAIN = 1;
    const STATUS_NOTNEED_TRAIN = 0;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverExt the static model class
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
		return '{{driver_ext}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('driver_id, license_date, service_times, high_opinion_times, last_low_opinion_date, low_opinion_times, newest_high_opinion_times', 'required'),
            array('is_by_army, score, is_full_time, has_car, license_date, service_times, high_opinion_times, last_low_opinion_date, low_opinion_times, newest_high_opinion_times, all_count, cancel_count, add_count, accept_days, online_days, recommend, punish, c_complain, d_complain, normal_days, p_online, p_continuous', 'numerical', 'integerOnly'=>true),
            array('deductions, recharge', 'numerical'),
            array('driver_id', 'length', 'max'=>50),
            array('mark_reason', 'length', 'max'=>255),
            array('start_score_time', 'safe'), //fix bug 2279 aiguoxin 2014-06-25
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('driver_id,start_score_time,year_driver_count, is_by_army, is_full_time, has_car, license_date, mark_reason, service_times, high_opinion_times, last_low_opinion_date, low_opinion_times, newest_high_opinion_times, all_count, cancel_count, add_count, accept_days, online_days, recommend, punish, c_complain, d_complain, normal_days, p_online, p_continuous, deductions, recharge', 'safe', 'on'=>'search'),
        );
    }



	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array (
			'employee'=>array (
				self::HAS_ONE, 
				'Employee', 
				'user'
			),
            'driver'=> array(self::HAS_ONE, 'Driver', '', 'on' => 'driver_id = driver.user')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'driver_id'=>'司机编号',
			'is_by_army'=>'是否军转',
			'is_full_time' => '是否全职',
			'has_car' => '有私家车',
			'license_date' => '驾照申领时间',
            'mark_reason' => 'Mark Reason',
            'service_times' => 'Service Times',
            'high_opinion_times' => 'High Opinion Times',
            'last_low_opinion_date' => 'Last Low Opinion Date',
            'low_opinion_times' => 'Low Opinion Times',
            'newest_high_opinion_times' => 'Newest High Opinion Times',
            'all_count' => 'Complate Count',
            'cancel_count' => 'Cancel Count',
            'add_count' => 'Add Count',
            'accept_days' => 'Accept Days',
            'online_days' => 'Online Days',
            'deductions' => 'Deductions',
            'recharge' => 'Recharge',
            'recommend' => 'Recommend',
            'punish' => 'Punish',
            'c_complain' => 'C Complain',
            'd_complain' => 'D Complain',
            'normal_days' => 'Normal Days',
            'p_online' => 'P Online',
            'p_continuous' => 'P Continuous',
            'score' => '司机当年分数',
            'start_score_time' => '司机今年开始计算分数时间',
            'train'=>'是否需要培训',
            'year_driver_count' => '司机当年驾驶次数',
            'total_wealth' =>'e币总数',
		);
	}
	
	public function initializeExt($user){

		$attr = array(
			'driver_id'=>$user,
			'is_by_army'=>0,
			'is_full_time'=>0,
			'has_car'=>0,
			'license_date'=>time(),
			'mark_reason'=>'',
			'service_times'=>0,
			'high_opinion_times'=>0,
			'last_low_opinion_date'=>0,
			'low_opinion_times'=>0,
			'newest_high_opinion_times'=>0,
            'all_count' => 0,
            'cancel_count' => 0,
            'add_count' => 0,
            'accept_days' => 0,
            'online_days' => 0,
            'deductions' => 0,
            'recharge' => 0,
            'recommend' => 0,
            'punish' => 0,
            'c_complain' => 0,
            'd_complain' => 0,
            'normal_days' => 0,
            'p_online' => 0,
            'p_continuous' => 0,
		);
        // //判断司机所在城市是否开通了代驾分制度
        // //如果开通则初始化
        // $driver_info = Driver::model()->getProfile($user);
        // $driver_city_id = $driver_info->city_id;  //会报空指针，目前全国已经开通，可以不判断了
        // if(Common::checkOpenScore($driver_city_id,time())){
            $attr['score'] = 12;
            $attr['start_score_time'] = date('Y-m-d H:i:s');
        // }
		$this->attributes = $attr;
		$this->save();
	}

    public static function getTrainStatus(){
        return array(self::STATUS_NEED_TRAIN=>'培训',
                    self::STATUS_NOTNEED_TRAIN=> '不需要培训');
    }
	
	public function getExt($driverID){
		$ext = self::model()->find('driver_id=:driver_id', array (
			':driver_id'=>$driverID));
		
		if (!$ext){
			$ext = new DriverExt();
			//TODO 检查此model的connection在哪里被设置成了readonly
			$ext::$db = Yii::app()->db;
			$ext->initializeExt($driverID);
		}
		return $ext;
	}
	
	public function updateLicenseDate($user = 'BJ9000', $licenseDate = 0){
		$ext = DriverExt::model()->getExt($user);
		$attributes = $ext->attributes;
		$attributes['license_date'] = strtotime($licenseDate);
		$ext->attributes = $attributes;
		$ext->save();
	}

    public function updateScoreStartTime($user, $start_score_time){
        $ext = DriverExt::model()->getExt($user);
        $attributes = $ext->attributes;
        $attributes['start_score_time'] = $start_score_time;
        $ext->attributes = $attributes;
        $ext->save();
    }

	/*
	*	add by aiguoxin
	*	add score for driver
	*/
	public function addScore($driverId,$addScore){
        $ext = self::model()->getDriverExt($driverId);
        if($ext){
            $score = $ext['score'] + $addScore;

            if($score > 12){
                $score = 12;
            }

    		$sql = "UPDATE `t_driver_ext` SET `score` = :score WHERE driver_id = :driver_id";
            return Yii::app()->db->createCommand($sql)->execute(array(
                ':driver_id' => $driverId,
                ':score' => $score,
            ));
        }
        return 0;
	}
	
	/**
	* by jiajingtao
	* $driverId 司机id $staticstime 代驾分统计时间节点
	*/
	public function updateStaticsTime($driverId,$staticstime){
		
        if($driverId && $staticstime){

    		$sql = "UPDATE `t_driver_ext` SET `statics_created` = :statics_created WHERE driver_id = :driver_id";
            return Yii::app()->db->createCommand($sql)->execute(array(
                ':driver_id' => $driverId,
                ':statics_created' => $staticstime,
            ));
        }
        return 0;
		
	}
	
	
    /*
    *   add by aiguoxin
    *   add e money for driver
    */
    public function addWealth($driverId,$addWealth){
        $ext = self::model()->getExt($driverId);
        if($ext){
            $wealth = $ext['total_wealth'] + $addWealth;
            $sql = "UPDATE `t_driver_ext` SET `total_wealth` = :wealth WHERE driver_id = :driver_id";
            return Yii::app()->db->createCommand($sql)->execute(array(
                ':driver_id' => $driverId,
                ':wealth' => $wealth,
            ));
        }
        return 0;
    }


    /**
     * 司机发，扣e币入口
     * @param $driverId
     * @param $Wealth
     * @param $type
     * @param $city_id
     * @param string $create_time
     * @param string $des
     * @return bool|int
     */
    public function driverWealth($driverId,$Wealth,$type,$city_id,$create_time='',$des=''){
        $log_res = DriverWealthLog::model()->addLog($driverId, $type, $Wealth, $city_id, $create_time,$des);
        if($log_res){
            $res = $this->addWealth($driverId,$Wealth);
            return $res;
        }
        return false;
    }
    /**
    *   add by aiguoxin
    *   update year count
    */
    public function changeYearCount($driver_id,$count,$type=0){
        //increase
        $sql = "UPDATE `t_driver_ext` SET `year_driver_count` = year_driver_count + :count WHERE driver_id = :driver_id";
        if($type == 1){
            $sql = "UPDATE `t_driver_ext` SET `year_driver_count` = :count WHERE driver_id = :driver_id";  
        }
        return Yii::app()->db->createCommand($sql)->execute(array(
            ':driver_id' => $driver_id,
            ':count' => $count,
        ));
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

		$criteria->compare('driver_id',$this->driver_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * 扣除司机代驾分
     * @param $driverId 司机工号
     * @param $cityId 城市id
     * @param $score 扣除分数
     * @param $reason 扣分原因
     * @param $complainTypeId 投诉类型id
     * @param $sendMsg 是否发送短信
     * @param $complainContent 投诉内容，默认与reason一样
     */
    public function deductScore($driverId,$cityId,$score,$reason,$complainTypeId=0,$sendMsg=true,$complainContent=''){
        $score = -$score; //扣分取反
        //生成对应的投诉，保证司机可以申诉
        if(empty($complainContent)){
            $complainContent = $reason;
        }
        $complainId = CustomerComplain::model()->addCompleteComplain($driverId,$cityId,$complainContent,$complainTypeId);
        echo 'complainId='.$complainId.PHP_EOL;
        //push一条通知
        DriverPush::model()->pushUnreadComplain($driverId);
        if($score >= 0){ //不是扣分
            EdjLog::info('driverId='.$driverId.',score='.$score.',不走扣分流程');
            return true;
        }
        $res = $this->scoreDeduct($driverId,$score,1,$sendMsg);
        $block_day = $res['update_res'] && $res['had_punished'] ? $res['block_day'] : 0; //司机是否被屏蔽了
        $param = array(
            'driver_id' => $driverId,
            'customer_complain_id' => $complainId,
            'complain_type_id' => $complainTypeId,
            'operator' => 'system',
            'driver_score'=>$score,
            'block_day' =>$block_day,//需要存，延迟屏蔽需要这个数据
            'comment_sms_id' => 0,
            'city_id'=>$cityId,
            'create_time' => date('Y-m-d H:i:s'),
            'deduct_reason' =>$reason,
            'revert'=> DriverPunishLog::REVERT_NO_EXECUTE,
        );
        $res = DriverPunishLog::model()->addData($param);
        return ($res == 1);
    }


    /**
     * 司机扣分
     * $driver_id  = 'BJ9003';
     * $score       = '-3';
     * duke add
     */

    public function scoreDeduct($driver_id, $score, $reason_id = 1, $send_message=true){

        if($score == 0 )return array('update_res' => 0,'had_punished'=> 0); ;
        //查询driver_punish_log  看最后一次处罚是否执行了惩罚（屏蔽，培训)
        //$punish_log = DriverPunishLog::model()->getLastPunish($driver_id);
        $driver_extinfo = $this->find('driver_id=:driver_id',array(':driver_id'=>$driver_id));
        $driver_now_score = $driver_extinfo->score >= 0 ? ($driver_extinfo->score < 12 ? $driver_extinfo->score : 12) : 0;
        $driver_info = Driver::model()->getProfile($driver_id);
        //print_r($driver_info);die;
        //$driver_info =
        //print_r($driver_extinfo);die;
        //$punish_log_score = $punish_log['driver_score'];
        //echo $driver_now_score.'-----';

        //echo '111';
        $city_open_config = Common::checkOpenScoreCity($driver_info->city_id,'all');
        //print_r($city_open_config);die;
//echo $driver_now_score;
        $now_score_punish = $city_open_config['block'][$driver_now_score]; //Common::checkOpenScoreCity($driver_info->city_id,'block',$driver_now_score);
        //echo '222';
        $new_score = $driver_extinfo->score = $driver_now_score + $score;
        $new_score = $new_score >= 0 ? $new_score : 0; //扣除后的剩余分数必须大于等于0
        $going_score_punish = $city_open_config['block'][$new_score]; //屏蔽的天数
        //echo 'aaaaa';echo $now_score_punish.'----'.$going_score_punish;die;
        if($now_score_punish != $going_score_punish){
            $punish = true;
            $driver_deduct_score = 12 - $new_score; //司机累计扣除分
            $message = '您扣分达到 '.$driver_deduct_score.' 分,将被屏蔽 '.$going_score_punish.' 天';
            //echo 'aaa';die;
            if($going_score_punish){
                //发送司机扣分屏蔽短信
                //$reason = '司机代价分扣除后屏蔽';

                $message_study = '';

                //如果司机的剩余分数达到培训标准则标记为需要培训 并且发送培训通知短信
                if(($city_open_config['disable_score'] <= $driver_deduct_score)  && $new_score != 0){
                    $driver_extinfo->train = self::STATUS_NEED_TRAIN;
                    //发送短信 通知来培训
                    $message_study = ',请在收到当地分公司通知后回分公司培训';

                }else if($new_score == 0) {
                    //发送司机永久屏蔽短信
                    $message = '您扣分达到12分,您的服务不符合e代驾宗旨,将自动与e代驾解除合作,请安排好时间去分公司办理手续';
                    //Sms::SendSMS($driver_info->phone, $message);
                }
                $app_ver = DriverStatus::model()->app_ver($driver_id);
                if(empty($app_ver) || $app_ver<'2.4.0'){
                    $message = $message. $message_study.',请退出重登录查看,屏蔽将在48小时后生效。不允许拨打客户电话进行沟通，如发现一律扣6分！';
                }else{
                    $message = $message. $message_study.',屏蔽将在48小时后生效，如有需要请登录司机端申诉。不允许拨打客户电话进行沟通，如发现一律扣6分！';
                }
                // $res = DriverPunish::model()->disable_driver($driver_id,$reason_id,$message,$going_score_punish);
                // if($res == 2){ //司机状态没有修改则补发短信
                //     $message = $driver_id.' 师傅，您已被屏蔽，原因：'.$message.'。';
                //     //echo $driver_info->ext_phone;die;
                if($send_message){
                    Sms::SendSMS($driver_info->ext_phone, $message);
                }
                // }
            }
        }
        else {
            $punish = false;
        }


        $ress = $driver_extinfo->save();
        return array('update_res' => $ress,'had_punished'=> $punish,'block_day'=>$going_score_punish);
//        if($ress){
//            //发送司机扣分短信
//            //$message = $message. $message_study;
//            Sms::SendSMS($driver_info->phone, $message);
//        }

    }

    /*
	*	add by aiguoxin
	*	get driver EXT
	*/
	public function getDriverExt($driver_id){
		$command = Yii::app()->db_readonly->createCommand();
        $command->select('UNIX_TIMESTAMP(start_score_time) as startTime,score,year_driver_count');
        $command->from('t_driver_ext');
        $command->where('driver_id=:driver_id', array(':driver_id'=>$driver_id));
        
        return $command->queryRow();
	}


    /*
    *   update train log
    *   aiguoxin
    */
    public function revertUnTrain($driver_id){
        $sql = "UPDATE `t_driver_ext` SET `train` = `train`-1 WHERE driver_id = :driver_id and train>0";
        return Yii::app()->db->createCommand($sql)->execute(array(
            ':driver_id' => $driver_id,
        ));
    }
}
