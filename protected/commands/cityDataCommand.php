<?php
/**
* 星级规则前后数据对比
* by jiajingtao
*/
class cityDataCommand extends CConsoleCommand
{

	/*
	public function run($params){
		if($params[0]=='new'){
			$this->actionGetNewData($params[1]);
		} else {
			$this->actionExtInitialize($params[1]);
		}
	}*/


	/**
     *
     * 初始化司机补充信息
     * php protected/yiic driver GetStarLevelData
     * @param $cityid  为空时为全国  $rule = oldrule (旧规则) 或 newrule (新规则)
     */
    public function actionGetStarLevelData($cityid,$rule)
    {
        echo Common::jobBegin('citydata_GetStarLevelData');
        $start_time = date('Y-m-d H:i:s');
        $offset = 0;
        $pageSize = 1000;
        $i = 0;
        while (true) {
            $criteria = new CDbCriteria();
            $criteria->select = "user";
            if($cityid){
            
		$criteria->addCondition('city_id = :city_id');
            }
            $criteria->addCondition('mark != :mark');
            if($cityid){
                $criteria->params = array(
                    ':city_id'=>$cityid,
                    ':mark' => 3
                );
            }else{
                $criteria->params = array(
                    ':mark' => 3
                );
            }
	
            $criteria->order = 'id asc';
            $criteria->offset = $offset;
            $criteria->limit = $pageSize;

            $driver = Driver::model()->findAll($criteria);
            if ($driver) {
                foreach ($driver as $v) {
                    echo $v->user." ";
                    self::actionUpdateExt($v->user,$rule);
                    $i ++;
                }
            } else {
                $content = '司机星级和代价次数等司机扩展信息定时更新完毕。开始时间'.$start_time.'结束时间'.date('Y-m-d H:i:s').'共更新司机'.$i.'个';
                //if($start_id)$content.=' 开始id :'.$start_id.' 升序执行';
                //echo $content;
                //Mail::sendMail(array("dengxiaoming@edaijia-inc.cn",'dongkun@edaijia-inc.cn','yangmingli@edaijia-inc.cn'),$content, "司机扩展信息更新状态每日邮件");
                break;
            }

            $offset += $pageSize;
        }
        echo Common::jobEnd('citydata_GetStarLevelData');
    }

	
	/**
     *
     * 更新司机补充信息
     * php protected/yiic driver updateext --user=BJ9000
     */
    public function actionUpdateExt($user,$rule)
    {
        $employee = Driver::getProfile($user);
        $ext = DriverExt::model()->find('driver_id=:driver_id', array(
            ':driver_id' => $employee->attributes['user']));
        if (!$ext) {
            $ext = new DriverExt();
            $ext->initializeExt($employee->attributes['user']);
        }

		$data_result=1;
		if($rule =='oldrule' ){ //24 哈尔滨
			$level_result = self::actionLevelNewRule($user, $employee->attributes['city_id']);
		} elseif($rule=='newrule') {
			$level_result = self::actionLevelNewRule2($user, $employee->attributes['city_id']);
		}
        
        //DriverStatus::model()->reload($user);
        echo ' id: '.$employee->id.' '.$user.' data: '.$data_result . ' level: ' . $level_result . " \n ";
    }
	
	
	
	
	/**
     * 新规则获取司机星级
     * @param string $user
     * @param int $city_id
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-09-27
     */
    public function actionLevelNewRule($user, $city_id)
    {
        $point_date = '2013-10-15 15:00:00';
        //前60天所有评价 
        $offset = 3600 * 24 * 60;

        //t_comments_sms表中，司机最后的评价
        $drivers = Yii::app()->db_readonly->createCommand()
            ->select('created')
            ->from('t_comment_sms')
            ->where('driver_id=:user and level>0 and level<=5 and sms_type=0 and order_status in(1,4)', array(':user' => $user))
            ->order('id DESC')
            ->limit(1)
            ->queryRow();
        if (!empty($drivers) && $drivers['created'] != '') {

            //最后一条评价的日期
            $lastDay = date('Y-m-d', strtotime($drivers['created']));

            $star_arr = array(
                'star_one' => 0, //客户评一星次数
                'star_two' => 0, //客户评二星次数
                'star_three' => 0, //客户评三星次数
                'star_four' => 0, //客户评四星次数
                'star_five' => 0, //客户评五星次数
                'comments_num' => 0, //评价星级总次数
                'point' => 0,
            );

            //获取前60天所有评价
            $comments = Yii::app()->db_readonly->createCommand()->select('sender,level,content,created')->from('t_comment_sms')
                ->where('driver_id=:user AND order_status in(1,4) and level>0 and level<=5 and sms_type=0 AND created>=:start AND created<=:end', array(':user' => $user, ':start' => date('Y-m-d 00:00:00', strtotime($lastDay) - $offset), ':end' => $lastDay . ' 23:59:59'))
                ->queryAll();
            $point = 0;
            if (!empty($comments)) {
                foreach ($comments as $comment) {
                    //判定是否为vip
                    $is_vip = $this->_validateVip($comment['sender']);
                    //获取所评星级（*vip有可能加倍)
                    $data = $this->_getStarNum($comment, $star_arr, $is_vip, $point_date);
                }
            }

            //获取服务次数
            $driverExt = DriverExt::model()->getExt($user);
            $serviceCount = (int)$driverExt['service_times'];

            $level = Common::_getDriverLevelNewRule($star_arr, $serviceCount, $city_id);
			return 	$level;
            //更新数据
           // $result = $this->_updateDriverLevelInfo($user, $level, $serviceCount, $lastDay);
            //echo $user.'----'.$level."----".$lastDay."----UPDATE SUCCESS \n";
            //return $result;
        } else {
            //TODO 此处一定要重写 zhangtingyi
            //获取服务次数
            $driverExt = DriverExt::model()->getExt($user);
            $serviceCount = (int)$driverExt['service_times'];
            $lastDay = date('Y-m-d', time());
            /*
            if ($serviceCount > 0) {
                $star_arr['point'] = $serviceCount > 0 ? 5 : 0;
                $star_arr['comments_num'] = 1;
                $level = Common::_getDriverLevelNewRule($star_arr, $serviceCount, $city_id);
            } else {
                $level = 0;
            }
            */
            //根据赵新磊需求，无短信回评无论司机有多少订单星级都为0
            $level = 0;
            //$result = $this->_updateDriverLevelInfo($user, $level, $serviceCount, $lastDay);
			return 	$level;
            //return $result;
        }
    }
	
	
	/**
     * 新规则获取司机星级
     * @param string $user
     * @param int $city_id
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-09-27
     */
    public function actionLevelNewRule2($user, $city_id)
    {
        $point_date = '2013-10-15 15:00:00';
        //前60天所有评价
        $offset = 3600 * 24 * 60;
		
		$star_arr = array(
                'star_one' => 0, //客户评一星次数
                'star_two' => 0, //客户评二星次数
                'star_three' => 0, //客户评三星次数
                'star_four' => 0, //客户评四星次数
                'star_five' => 0, //客户评五星次数
                'comments_num' => 0, //评价星级总次数
                'point' => 0,
            );

        //t_comments_sms表中，司机最后的评价
        $drivers = Yii::app()->db_readonly->createCommand()
            ->select('created')
            ->from('t_comment_sms')
            ->where('driver_id=:user and level>0 and level<=5 and sms_type=0 and order_status in(1,4)', array(':user' => $user))
            ->order('id DESC')
            ->limit(1)
            ->queryRow();
        if (!empty($drivers) && $drivers['created'] != '') {

            //最后一条评价的日期
            $lastDay = date('Y-m-d', strtotime($drivers['created']));

            //获取前60天所有评价
            $comments = Yii::app()->db_readonly->createCommand()->select('sender,level,content,created')->from('t_comment_sms')
                ->where('driver_id=:user AND order_status in(1,4) and level>0 and level<=5 and sms_type=0 AND created>=:start AND created<=:end', array(':user' => $user, ':start' => date('Y-m-d 00:00:00', strtotime($lastDay) - $offset), ':end' => $lastDay . ' 23:59:59'))
                ->queryAll();
            $point = 0;
            if (!empty($comments)) {
                foreach ($comments as $comment) {
                    //判定是否为vip
                    $is_vip = $this->_validateVip($comment['sender']);
                    //获取所评星级（*vip有可能加倍)
                    $data = $this->_getStarNum($comment, $star_arr, $is_vip, $point_date);
                }
            }           
        }
			//获取服务次数
            $driverExt = DriverExt::model()->getExt($user);
            $serviceCount = (int)$driverExt['service_times'];

            $level = Common::_getDriverLevelNewRule2($star_arr, $serviceCount, $city_id);
			return 	$level;
            //更新数据
            //$result = $this->_updateDriverLevelInfo($user, $level, $serviceCount);
            //echo $user.'----'.$level."----".$lastDay."----UPDATE SUCCESS \n";
            //return $result;
    }
	
	
	/**
     * 获取每个星级评价次数(vip单独算)
     * @param array $comment
     * @param array $star_arr
     * @param boolean $is_vip
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-09-27
     */
    private function _getStarNum($comment, &$star_arr, $is_vip, $point_date = '2013-10-15 15:00:00')
    {
        if ($comment['level'] == 1 && $comment['content'] != '' && $comment['content'] != 1) {
            if (strtotime($comment['created']) > strtotime($point_date)) {
                $star_arr['star_one'] += 1;
                $star_arr['point'] = $star_arr['point'] - 20;
            } else {
                $star_arr['star_one'] += 1;
                $star_arr['point'] = $star_arr['point'] - 25;
            }
        } else if ($comment['level'] == 2 && $comment['content'] != '' && $comment['content'] != 2) {
            if (strtotime($comment['created']) > strtotime($point_date)) {
                $star_arr['star_two'] += 1;
                $star_arr['point'] = $star_arr['point'] - 10;
            } else {
                $star_arr['star_two'] += 1;
                $star_arr['point'] = $star_arr['point'] - 15;
            }
        } else if ($comment['level'] >= 3 && $comment['level'] <= 5) {
            $level_s = self::getNewLevel($comment['level'], $comment['created']);
            switch ($level_s) {
                case 3:
                    if (strtotime($comment['created']) > strtotime($point_date)) {
                        $star_arr['star_three'] += 1;
                        $star_arr['point'] = $star_arr['point'] - 5;
                    } else {
                        $star_arr['star_three'] += 1;
                        $star_arr['point'] = $star_arr['point'] + 3;
                    }
                    break;
                case 4:
                    if (strtotime($comment['created']) > strtotime($point_date)) {
                        $star_arr['star_four'] += 1;
                        $star_arr['point'] = $star_arr['point'] - 1;
                    } else {
                        $star_arr['star_four'] += 1;
                        $star_arr['point'] = $star_arr['point'] + 4;
                    }
                    break;
                case 5:
                    if (strtotime($comment['created']) > strtotime($point_date)) {
                        $star_arr['star_five'] += 3;
                        if ($is_vip) {
                            $star_arr['point'] = $star_arr['point'] + 15;
                        } else {
                            $star_arr['point'] = $star_arr['point'] + 5;
                        }
                    } else {
                        $star_arr['star_five'] += 1;
                        $star_arr['point'] = $star_arr['point'] + 5;
                    }
                    break;

            }
        }
        $star_arr['comments_num']++;
    }
	
	/**
     * 验证评分客户是否为vip
     * @param string $phone
     * @return boolean
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-09-27
     */
    private function _validateVip($phone)
    {
        $is_vip = false;
        /*
		$vip = Vip::getPrimaryPhone($phone);
		if ($vip && ($vip->attributes['status'] == Vip::STATUS_NORMAL || $vip->attributes['status'] == Vip::STATUS_ARREARS)) {
			$is_vip = true;
		}
        */
        $vip = VipPhone::model()->getPrimary($phone);
        if ($vip) {
            $is_vip = true;
        }
        return $is_vip;
    }
	
	
	public function getNewLevel($level, $created)
    {
        $newLevel = 0;
        if ($created < '2013-04-12') {
            if ($level != '' && $level != 0 && $level != 0.0) {
                if ($level == 3) {
                    $newLevel = 5;
                } else {
                    $newLevel = $level;
                }
            }
        } else {
            $newLevel = $level;
        }

        return $newLevel;
    }
	
	
	
	public function actionGetNewData($cityid){
		
		echo Common::jobBegin('citydata_GetNewData');
        $start_time = date('Y-m-d H:i:s');
        $offset = 0;
        $pageSize = 1000;
        $i = 0;
        while (true) {
            $criteria = new CDbCriteria();
            $criteria->select = "id,user,level";
            if($cityid){
                $criteria->addCondition('city_id = :city_id');
            }
            $criteria->addCondition('mark != :mark');
            if($cityid){
                $criteria->params = array(
                    ':city_id'=>$cityid,
                    ':mark' => 3
                );
            }else{
                $criteria->params = array(
                    ':mark' => 3
                );
            }
	
            $criteria->order = 'id asc';
            $criteria->offset = $offset;
            $criteria->limit = $pageSize;

            $driver = Driver::model()->findAll($criteria);
            if ($driver) {
                foreach ($driver as $v) {
                    echo $v->user." ";
                    //self::actionUpdateExt($v->user);
					echo ' id: '.$v->id.' '.$v->user . ' data: 1  level: ' . $v->level . " \n ";
                    $i ++;
                }
            } else {
                $content = '司机星级和代价次数等司机扩展信息定时更新完毕。开始时间'.$start_time.'结束时间'.date('Y-m-d H:i:s').'共更新司机'.$i.'个';
               //if($start_id)$content.=' 开始id :'.$start_id.' 升序执行';
                //echo $content;
                //Mail::sendMail(array("dengxiaoming@edaijia-inc.cn",'dongkun@edaijia-inc.cn','yangmingli@edaijia-inc.cn'),$content, "司机扩展信息更新状态每日邮件");
                break;
            }

            $offset += $pageSize;
        }
        echo Common::jobEnd('citydata_GetNewData');
	}
	
	
	
	
	
	
	
	
	
}




?>
