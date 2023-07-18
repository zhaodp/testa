<?php

class driverCommand extends LoggerExtCommand
{
    /**
     * 重建司机头像图
     */
    public function actionBuildPhoto()
    {
        $root_path = '/tmp/';
        $pagesize = 100;

        $offset = 0;
        $alioss = new OSS('edaijia');

        while (true) {
            $criteria = new CDbCriteria(array(
                'select' => 'id,user,picture,city_id',
                'order' => 'id',
                'offset' => $offset,
                'condition' => 'mark=0',
                'limit' => $pagesize,
                'params' => array(
                    ':picture' => 'http://www.edaijia.cn%')));

            $ret = Driver::model()->findAll($criteria);
            if ($ret) {
                foreach ($ret as $driver) {
                    $sql = 'SELECT driver_id, version FROM `t_driver_img`
							WHERE driver_id = :driver_id
							ORDER BY version DESC
							LIMIT 1;';

                    $photo = DriverImg::model()->findBySql($sql, array(
                        ':driver_id' => $driver->user));

                    if ($photo) {
                        echo $driver->user . "\n";
                        $picture_url = 'http://img.edaijia.cn/edaijia/' . sprintf('%s/%s/middle_%s.jpg', $driver->city_id, strtoupper(trim($photo->driver_id)), $photo->version);
                        if ($picture_url != $driver->picture) {
                            $sql = 'SELECT driver_id,bin_data,version FROM `t_driver_img`
									WHERE driver_id = :driver_id
									ORDER BY version DESC
									LIMIT 1;';
                            $photo = DriverImg::model()->findBySql($sql, array(
                                ':driver_id' => $driver->user));

                            $img = imagecreatefromstring(stripslashes($photo->bin_data));
                            imagejpeg($img, $root_path . 'tmp.jpg', 80);

                            Yii::app()->thumb->load($root_path . 'tmp.jpg')->setOptions(array(
                                'resizeUp' => false))->resize(120, 144)->save($root_path . "small.jpg", "JPG");
                            $oss_object = sprintf('%s/%s/small_%s.jpg', $driver->city_id, strtoupper(trim($photo->driver_id)), $photo->version);
                            $alioss->upload_by_file($oss_object, $root_path . "small.jpg");

                            Yii::app()->thumb->load($root_path . 'tmp.jpg')->setOptions(array(
                                'resizeUp' => false))->resize(160, 192)->save($root_path . "middle.jpg", "JPG");
                            $oss_object = sprintf('%s/%s/middle_%s.jpg', $driver->city_id, strtoupper(trim($photo->driver_id)), $photo->version);
                            $alioss->upload_by_file($oss_object, $root_path . "middle.jpg");

                            Yii::app()->thumb->load($root_path . 'tmp.jpg')->setOptions(array(
                                'resizeUp' => true))->resize(640, 768)->save($root_path . "normal.jpg", "JPG");
                            $oss_object = sprintf('%s/%s/normal_%s.jpg', $driver->city_id, strtoupper(trim($photo->driver_id)), $photo->version);
                            $alioss->upload_by_file($oss_object, $root_path . "normal.jpg");

                            $oss_object = sprintf('%s/%s/middle_%s.jpg', $driver->city_id, strtoupper(trim($photo->driver_id)), $photo->version);
                            $picture = 'http://img.edaijia.cn/edaijia/' . $oss_object;
                            $driver->picture = $picture;
                            Driver::model()->updateByPk($driver->id, array(
                                'picture' => $picture));
                        }
                    }
                }
            } else {
                break;
            }
            $offset += $pagesize;
        }
    }

    /**
     *
     * 初始化司机补充信息
     * php protected/yiic driver extinitialize
     * @param $start_id  start id
     */
    public function actionExtInitialize($start_id = '')
    {
        echo Common::jobBegin('driver_extinitialize');
        $start_time = date('Y-m-d H:i:s');
        $offset = 0;
        $pageSize = 1000;
        $i = 0;
        while (true) {
            $criteria = new CDbCriteria();
            $criteria->select = "user";
            if($start_id){
                $criteria->addCondition('id >= :id');
            }
            $criteria->addCondition('mark != :mark');
            if($start_id){
                $criteria->params = array(
                    ':id'=>$start_id,
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
                    echo $v->user."--++++ ";
                    self::actionUpdateExt($v->user);
                    $i ++;
                }
            } else {
                $content = '司机星级和代价次数等司机扩展信息定时更新完毕。开始时间'.$start_time.'结束时间'.date('Y-m-d H:i:s').'共更新司机'.$i.'个';
                if($start_id)$content.=' 开始id :'.$start_id.' 升序执行';
                //echo $content;
                Mail::sendMail(array("dengxiaoming@edaijia-inc.cn",'dongkun@edaijia-inc.cn','yangmingli@edaijia-inc.cn'),$content, "司机扩展信息更新状态每日邮件");
                break;
            }

            $offset += $pageSize;
        }
        echo Common::jobEnd('driver_extinitialize');
    }

    /**
     *
     * 把司机的信息全部装载到缓存
     */
    public function actionLoadToCache()
    {
        $dirvers = Driver::model()->findAll();

        foreach ($dirvers as $dirver) {
            $cache_key = Yii::app()->params['CACHE_KEY_DRIVER_INFO'] . $dirver->imei;
            echo $cache_key . "\n";
            $json = json_encode(array(
                'city_id' => $dirver->city_id,
                'driver_id' => $dirver->user,
                'comment_count' => Driver::getDriverComments($dirver->user),
                'order_count' => Driver::getDriverOrder($dirver->user),
                'state' => 0));
            Yii::app()->cache->set($cache_key, $json, 3600);

            echo $json;
        }
    }

    /**
     *
     * 更新司机补充信息
     * php protected/yiic driver updateext --user=BJ9000
     */
    public function actionUpdateExt($user)
    {
        $employee = Driver::getProfile($user);
        $ext = DriverExt::model()->find('driver_id=:driver_id', array(
            ':driver_id' => $employee->attributes['user']));
        if (!$ext) {
            $ext = new DriverExt();
            $ext->initializeExt($employee->attributes['user']);
        }

        $sql_high_opinion = "SELECT count(sender) as c, max(created) as t FROM t_comment_sms WHERE level>3 and driver_id='{$user}'";
        $high_command = Yii::app()->db_readonly->createCommand($sql_high_opinion);

        $high_data = $high_command->queryRow();
        $data['highOpinionCount'] = $high_data['c'];

        $sql_low_opinion = "SELECT COUNT(sender) as c, max(created) as t FROM t_comment_sms WHERE level<3 and driver_id='{$user}'";
        $low_command = Yii::app()->db_readonly->createCommand($sql_low_opinion);
        $low_data = $low_command->queryRow();
        $data['lowOpinionCount'] = $low_data['c'];

        $data['lastLowOpinionTime'] = empty($low_data['t']) ? '0' : strtotime($low_data['t']);

        if ($data['lastLowOpinionTime']) {

            $sql_new_high_opinion = "SELECT count(id) as c FROM t_comment_sms WHERE level>3 and driver_id='{$user}' and created>'{$low_data['t']}'";
            $new_high_command = Yii::app()->db_readonly->createCommand($sql_new_high_opinion);
            $new_high_data = $new_high_command->queryRow($sql_new_high_opinion);
            $data['newestHighOpinionCount'] = intval($new_high_data['c']);
        } else {
            $data['newestHighOpinionCount'] = 0;
        }

        $sql = "SELECT COUNT(id) FROM t_daily_order_driver WHERE driver_user=:driver_id and status in (1, 4)";
        $command = Yii::app()->dbreport->createCommand($sql);
        $command->bindParam(":driver_id", $user);
        $data['service_times'] = intval($command->queryScalar());

        $attr = array(
            'service_times' => $data['service_times'],
            'high_opinion_times' => $data['highOpinionCount'],
            'last_low_opinion_date' => $data['lastLowOpinionTime'],
            'low_opinion_times' => $data['lowOpinionCount'],
            'newest_high_opinion_times' => $data['newestHighOpinionCount']
        );

        $ext->attributes = $attr;
        $data_result = $ext->save();
		if($employee->attributes['city_id']==24 ){ //24 哈尔滨
			$level_result = self::actionLevelNewRule2($user, $employee->attributes['city_id']);
		} else {
			$level_result = self::actionLevelNewRule($user, $employee->attributes['city_id']);
		}
        
        DriverStatus::model()->reload($user);
        echo 'id:'.$employee->id.'-- '.$user . '--data:' . intval($data_result) . '--level:' . intval($level_result) . "\n";
    }

    /**
     *
     * 初始化司机星级  先讨论一下 不要执行
     * php protected/yiic driver levelinitialize
     */
    public function actionLevelInitialize()
    {
        $connection = Yii::app()->db;
        $sql = "SELECT user, city_id, year FROM t_driver";
        $command = $connection->createCommand($sql);
        $driver_initialize = $command->queryAll();

        foreach ($driver_initialize as $initialize) {
            self::actionUpdateLevel($initialize['user'], $initialize['city_id']);
        }
    }

    /**
     * 按城市更新司机扩展表中的数据
     * @param $city
     */
    public function updateExtDataByCity($city)
    {
        $openCity = RCityList::model()->getOpenCityList();
        if (array_key_exists($city, $openCity)) {
            $connection = Yii::app()->db;
            $sql = "SELECT user FROM t_driver WHERE mark = 0 and city_id={$city}";
            $command = $connection->createCommand($sql);
            $driver_ext_initialize = $command->queryAll();
            foreach ($driver_ext_initialize as $initialize) {
                self::actionUpdateExt($initialize['user']);
            }
        }
    }

    /**
     * @author libaiyang 2013-05-08
     * 计算司机星级
     * php protected/yiic driver UpdateLevel --user=BJ0036
     */
    public function actionUpdateLevel($user, $city_id)
    {

        $offset = 3600 * 24 * 60;
        //一个一星=5个5星
        $one = 5 * 5;
        //一个两星=3个5星
        $two = 3 * 5;
        $level = 0;
        $old_level = 0;

        //t_comments_sms表中，司机最后的评价
        $drivers = Yii::app()->db_readonly->createCommand()
            ->select('created')
            ->from('t_comment_sms')
            ->where('driver_id=:user and level>0 and level<=5 and sms_type=0 and order_status in(1,4)', array(':user' => $user))
            ->order('id DESC')
            ->limit(1)
            ->queryRow();
        if (!empty($drivers) && $drivers['created'] != '') {

            //最后一天
            $lastDay = date('Y-m-d', strtotime($drivers['created']));
            $star_one = 0;
            $star_two = 0;
            $old_level = 0;
            $all_comments = 0;
            /**********************/
            $comments = Yii::app()->db_readonly->createCommand()->select('level,content,created')->from('t_comment_sms')
                ->where('driver_id=:user AND order_status in(1,4) and level>0 and level<=5 and sms_type=0 AND created>=:start AND created<=:end', array(':user' => $user, ':start' => date('Y-m-d 00:00:00', strtotime($lastDay) - $offset), ':end' => $lastDay . ' 23:59:59'))
                ->queryAll();

            if (!empty($comments)) {
                foreach ($comments as $comment) {
                    if ($comment['level'] == 1 && $comment['content'] != '' && $comment['content'] != 1) {
                        $star_one++;
                    } else if ($comment['level'] == 2 && $comment['content'] != '' && $comment['content'] != 2) {
                        $star_two++;
                    } else if ($comment['level'] >= 3 && $comment['level'] <= 5) {
                        $old_level += self::getNewLevel($comment['level'], $comment['created']);
                        $all_comments++;
                    }
                }
            }

            //得到正常分数总和
            $all_comments += $star_one + $star_two;
            /**********************/
            $driverExt = DriverExt::model()->getExt($user);
            $serviceCount = (int)$driverExt['service_times'];

            $level = Common::_getDriverLevel($old_level, $serviceCount, $star_one, $star_two, $all_comments, $city_id);
            //更新t_driver,t_employee
            Yii::app()->db->createCommand()->update('t_driver', array('level' => $level), 'user="' . $user . '"');
            Yii::app()->db->createCommand()->update('t_employee', array('level' => $level), 'user="' . $user . '"');

            $data = array(
                'driver_id' => $user,
                'level' => $level,
                'created' => date('Y-m-d'),
                'order_count' => $serviceCount
            );
            Yii::app()->db->createCommand()->insert('t_driver_level', $data);
            echo $user . '----' . $level . "----" . $lastDay . "----UPDATE SUCCESS \n";
        }


    }

    /**
     * 司机每月10日15点批量结账
     * php protected/yiic driver SettleDriverAccountInitialize
     */
    public function actionSettleDriverAccountInitialize($month = '')
    {
        if ($month == '') {
            $month = date('Ym', time() - 240 * 3600);
        }

        $criteria = new CDbCriteria();
        $criteria->select = "LTRIM(RTRIM(user)) AS user";
        $criteria->addCondition("FROM_UNIXTIME(created,'%Y%m')=:created");
        $criteria->params = array(
            ':created' => $month);
        $criteria->group = 'user';
        $users = EmployeeAccount::model()->findAll($criteria);
        foreach ($users as $user) {
            self::actionSettleDriverAccount($user->user, $month);
        }
    }

    /**
     * 司机结账
     */
    public function actionSettleDriverAccount($user, $month)
    {

        $sql = 'SELECT cast FROM t_employee_account_settle WHERE driver_id=:driver_id AND settle_date=:settle_date';
        $record = EmployeeAccount::model()->findBySql($sql, array(
            ":driver_id" => $user,
            ":settle_date" => $month));

        if (!$record) {
            $criteria = new CDbCriteria();
            $criteria->select = "SUM(cast) AS cast";
            $criteria->addCondition("LTRIM(RTRIM(user))=:user AND type > 0 AND FROM_UNIXTIME(created,'%Y%m')<=:created");
            $criteria->params = array(
                ':user' => $user,
                ':created' => $month);
            $criteria->group = 'user';
            $lastAccount = EmployeeAccount::model()->find($criteria);

            $cast = ($lastAccount) ? $lastAccount['cast'] : 0;

            $sql = 'INSERT INTO t_employee_account_settle(driver_id, settle_date, cast, type, created) VALUES(:driver_id, :settle_date, :cast, 1, :created)';
            $command = Yii::app()->db_finance->createCommand($sql);

            $created = time();
            $command->bindParam(":driver_id", $user);
            $command->bindParam(":settle_date", $month);
            $command->bindParam(":cast", $cast);
            $command->bindParam(":created", $created);
            $command->execute();
            $command->reset();

            $sql = "UPDATE t_employee_account SET is_settle=1, settle_date=:settle_date WHERE LTRIM(RTRIM(user))=:driver_id AND FROM_UNIXTIME(created, '%Y%m')=:created";
            $command = Yii::app()->db_finance->createCommand($sql);
            $command->bindParam(":settle_date", $month);
            $command->bindParam(":driver_id", $user);
            $command->bindParam(":created", $month);
            $command->execute();
            $command->reset();
        }
    }

    /**
     * 同步司机IMEI到统计表
     * php protected/yiic driver TransmissionDriver
     */
    public function actionTransmissionDriver()
    {
        $statImport = new StatImport();
        $statImport->actionTransmissionDriverImei();
    }

    /**
     * 同步司机在线数据到统计表
     * php protected/yiic driver DriverActive --day=20120701
     */
    public function actionDriverActive($day = '')
    {
        $statImport = new StatImport();
        $statImport->actionDriverActive($day);
    }

    /**
     * 初始化司机在线数据到统计表
     * php protected/yiic driver DriverActiveInitialize
     */
    public function actionDriverActiveInitialize()
    {
        $statImport = new StatImport();
        $day = date('Ymd', time() - 24 * 3600);
        do {
            $statImport->actionDriverActive($day);
            $day = date('Ymd', strtotime($day) - 24 * 3600);
        } while ($day > '20120501');
    }

    /**
     * 初始化日均接单数据到统计表
     * php protected/yiic driver DriverOrderDailyInitialize
     */
    public function actionDriverOrderDailyInitialize()
    {
        $statImport = new StatImport();
        $day = date('Ymd', time() - 24 * 3600);
        do {
            $statImport->actionDriverOrderDaily($day);
            $day = date('Ymd', strtotime($day) - 24 * 3600);
        } while ($day > '20120501');
    }

    /**
     * 同步司机日均接单数据到统计表
     * php protected/yiic driver DriverOrderDaily --day=20120701
     */
    public function actionDriverOrderDaily($day = '')
    {
        $statImport = new StatImport();
        $statImport->actionDriverOrderDaily($day);
    }

    /**
     * 每月15日修正司机日均接单数据到统计表
     * php protected/yiic driver DriverOrderDailyReload --month=201207
     */
    public function actionDriverOrderDailyReload($month = '')
    {
        $statImport = new StatImport();
        $statImport->actionDriverOrderDailyReload($month);
    }

    /**
     * 周六，周日，周一不生成指定额度一下的司机
     * 每日信息费低于指定额度的司机工号名单
     * 2013-04-19  孟天学修改 添加节假日不生成名单，屏蔽的司机名单也列出来
     */
    public function actionRechargeList()
    {
        $do_notify_recharge = true;

        $curr_week = date('w');
        $current_day = date("Y-m-d");
        $holiday = Holiday::model()->getHolidayByDate($current_day);

        if (!empty($holiday)) {
            if (1 == $holiday['status']) { //设定了节假日不屏蔽
                $do_notify_recharge = false;
            }
        } else {

            if ($curr_week == 6 || $curr_week == 0) {
                $do_notify_recharge = false;
            }
        }

	if ($do_notify_recharge) {
            $citys = RCityList::model()->getDriverCityLt(200);
            $drivers = Driver::model()->DriverLists($citys, 200);
            EdjLog::info(date("Y-m-d H:i:s")." driver.200.count:".count($drivers));
            $citys_area = RCityList::model()->getDriverCityLt(100);
            $drivers_area = Driver::model()->DriverLists($citys_area, 100);
            EdjLog::info(date("Y-m-d H:i:s")." driver.100.count:".count($drivers_area));
            $drivers = array_merge($drivers, $drivers_area);
            EdjLog::info(date("Y-m-d H:i:s")." driver.all.count:".count($drivers_area));
			DriverRecharge::model()->deleteAll();
            if (!empty($drivers)) {
                $success=0;
                $fail=0;
                $notAllow=0;
                foreach ($drivers as $item) {
                    //享受优惠的司机不屏蔽  mengtianxue 2013-07-30
//                    $discount = Common::driver_fee_discount($item['driver_id']);
                    //block_at=1  欠费自动屏蔽  mark=1 屏蔽 || block_mt=1 mark=1 手动屏蔽
                    if ((($item['block_at'] == 1 && $item['mark'] == 1) || $item['mark'] == 0)) {
                        $params = array(
                            'id' => $item['id'],
                            'driver_id' => $item['driver_id'],
                            'cast' => $item['balance'],
                            'created' => date('Y-m-d', time()));
						$driverRecharge = new DriverRecharge();
						$driverRecharge->attributes = $params;
						$rs = $driverRecharge->save();
                        if($rs) {
                            $success++;
                            EdjLog::info(date("Y-m-d H:i:s")." insert success driver_id:".$item['driver_id']."|".$item['block_at']."|". $item['mark']."|".$item['mark']);
                        }else{
                            $fail++;
                            EdjLog::info(date("Y-m-d H:i:s")." insert faile driver_id:".$item['driver_id']."|".$item['block_at']."|". $item['mark']."|".$item['mark']);
                        }
                    }else{
			$notAllow++;
		        EdjLog::info(date("Y-m-d H:i:s")." notAllow driver_id:".$item['driver_id']."|".$item['block_at']."|". $item['mark']."|".$item['mark']);		
		    }
                }
                EdjLog::info(date("Y-m-d H:i:s")." insert result success:".$success."|fail:".$fail."|notAllow:".$notAllow);



    } 
   }//if
}//class

    /**
     * 测算司机等级机制
     * php protected/yiic driver DriverLevelTest
     * @author libaiyang 2013-05-04
     */
    public function actionDriverLevelTest()
    {

        //按t_driver表司机全部过一遍，得到司机基础星级
        $drivers = Yii::app()->db_readonly->createCommand()->select('user,city_id')->from('t_driver')->where('mark=0')->queryAll();
        foreach ($drivers as $item) {
            if ($item['user']) {
                //-------
                $offset = 3600 * 24 * 60;
                //一个一星=5个5星
                $one = 5 * 5;
                //一个两星=3个5星
                $two = 3 * 5;
                $level = 0;
                $old_level = 0;

                //t_comments_sms表中，司机最后的评价
                $drivers = Yii::app()->db_readonly->createCommand()
                    ->select('created')
                    ->from('t_comment_sms')
                    ->where('driver_id=:user and level>0 and level<=5 and order_status=0', array(':user' => $item['user']))
                    ->order('id DESC')
                    ->limit(1)
                    ->queryRow();
                if (!empty($drivers) && $drivers['created'] != '') {
                    //-----------------------------
                    //最后一天
                    $lastDay = date('Y-m-d', strtotime($drivers['created']));
                    //计算1星评价(不用区分日期)
                    $star_one = Yii::app()->db_readonly->createCommand()->select('count(id) AS num')->from('t_comment_sms')
                        ->where('driver_id=:user AND order_status=0 AND level=1 AND created>=:start AND created<=:end', array(':user' => $item['user'], ':start' => date('Y-m-d 00:00:00', strtotime($lastDay) - $offset), ':end' => $lastDay . ' 23:59:59'))
                        ->queryRow();
                    //计算2星评价(不用区分日期)
                    $star_two = Yii::app()->db_readonly->createCommand()->select('count(id) AS num')->from('t_comment_sms')
                        ->where('driver_id=:user AND order_status=0 AND level=2 AND created>=:start AND created<=:end', array(':user' => $item['user'], ':start' => date('Y-m-d 00:00:00', strtotime($lastDay) - $offset), ':end' => $lastDay . ' 23:59:59'))
                        ->queryRow();
                    //正常评价
                    $db_comments = Yii::app()->db_readonly->createCommand()->select('level,created')->from('t_comment_sms')
                        ->where('driver_id=:user AND order_status=0 AND level>=3 AND level<=5 AND created>=:start AND created<=:end', array(':user' => $item['user'], ':start' => date('Y-m-d 00:00:00', strtotime($lastDay) - $offset), ':end' => $lastDay . ' 23:59:59'))
                        ->queryAll();
                    //一共总数
                    $all_comments = Yii::app()->db_readonly->createCommand()->select('count(id) AS num')->from('t_comment_sms')
                        ->where('driver_id=:user AND order_status=0 AND level>=1 AND level<=5 AND created>=:start AND created<=:end', array(':user' => $item['user'], ':start' => date('Y-m-d 00:00:00', strtotime($lastDay) - $offset), ':end' => $lastDay . ' 23:59:59'))
                        ->queryRow();

                    //得到正常分数总和
                    if (!empty($db_comments)) {
                        foreach ($db_comments as $comments) {
                            $old_level += self::getNewLevel($comments['level'], $comments['created']);
                        }
                    }

                    $driverExt = DriverExt::model()->getExt($item['user']);
                    $report = (int)$driverExt['service_times'];
                    $level = Common::_getDriverLevel($old_level, $report, $star_one['num'], $star_two['num'], $all_comments['num'], $item['city_id']);


                    $data = array(
                        'driver_id' => $item['user'],
                        'level' => $level,
                        'created' => date('Y-m-d'),
                        'order_count' => $report
                    );
                    Yii::app()->db->createCommand()->update('t_driver', array('level' => $level), 'user="' . $item['user'] . '"');
                    Yii::app()->db->createCommand()->update('t_employee', array('level' => $level), 'user="' . $item['user'] . '"');

                    Yii::app()->db->createCommand()->insert('t_driver_level', $data);
                    echo $item['user'] . '----' . $level . "----" . $lastDay . "----UPDATE SUCCESS \n";
                }
                //-------
            }
        }
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


    /**
     *  更新redis中 driver 信息
     * @author mengtianxue 2013-05-27
     *  php yiic.php driver ReloadDriverAmountRedis
     */
    public function actionReloadDriverAmountRedis()
    {
        $driver_list = Yii::app()->db_readonly->createCommand()
            ->select("user")
            ->from("t_driver")
            ->where("mark < 2")
            ->queryAll();

        foreach ($driver_list as $list) {
            $driver_id = $list['user'];
            $driver = DriverStatus::model()->get($driver_id);
            $driverAmount = EmployeeAccount::model()->getDriverAmount($driver_id);
            $driver->account = $driverAmount;
            echo $driver_id . "\n";
        }
    }

    /**
     * 司机消单加入用户投诉
     * //http://db03.edaijia.cn/data/spam_order/2013-06-24.data
     * @author bidong 2013-07-04
     */
    public function actionFetchDataForComplain()
    {
        //只在每天12点执行,跑前一天的数据
        if (date('H', time()) != '12') {
            echo "\n---" . date('Y-m-d H:i:s') . "---runed---not match time---\n";
            return;
        }

        $tmp_current_date = date('Y-m-d', strtotime("-1 days"));
        $host = 'http://db03.edaijia.cn/data/spam_order/';
        $fetch_url = $host . $tmp_current_date . '.data';

        $headerArray = get_headers($fetch_url, 1);
        if (preg_match('/200/', $headerArray[0])) {
            $f = fopen($fetch_url, 'r');
            $complainArr = array();
            while (!feof($f)) {
                $line = fgets($f);
                $attr_arr = json_decode($line, true);
                if (is_array($attr_arr) && count($attr_arr)) {
                    $arr = array();
                    $mark = '';
                    if ($attr_arr['type_id'] == '101')
                        $mark = '司机异常位移';
                    $arr['driver_id'] = $attr_arr['driver_id'];
                    $arr['order_id'] = $attr_arr['order_id'];
                    $arr['city_id'] = $attr_arr['city_id'];
                    $arr['operator'] = '系统';
                    $arr['source'] = 6; //来源为系统
                    $arr['detail'] = $mark;
                    $arr['create_time'] = date('Y-m-d', time());
                    $arr['status'] = 1;
                    $complainArr[] = $arr;

                    echo $attr_arr['order_id'] . "\r\n";
                }
            }
            fclose($f);
            CustomerComplain::model()->insertComplain($complainArr);
        } else {
            echo "无效url资源！";
        }
    }

    public function actionUpdateDriverExt($driver_id)
    {
        $data = MonthOrderReport::model()->getDriverExtData($driver_id);
        $ext = DriverExt::model()->find('driver_id=:driver_id', array(
            ':driver_id' => $driver_id));
        if (!$ext) {
            $ext = new DriverExt();
            $ext->initializeExt($driver_id);
        }
        $ext->all_count = intval($data['accept']);
        $ext->cancel_count = intval($data['cancel']);
        $ext->add_count = intval($data['additional']);
        $ext->accept_days = intval($data['accept_days']);
        $ext->online_days = intval($data['online']);
        $ext->normal_days = intval($data['normal_days']);
        $ext->p_online = intval($data['p_online']);
        $ext->p_continuous = intval($data['p_continuous']);

        echo $driver_id . "---" . date('Y-m-d') . "---" . $ext->save() . "\n";
    }


    public function actionOtherExtInitialize()
    {
        $connection = Yii::app()->db_readonly;
        $sql = "SELECT user FROM t_driver WHERE mark = 0";
        $command = $connection->createCommand($sql);
        $driver_ext_initialize = $command->queryAll();
        $start = time();
        foreach ($driver_ext_initialize as $initialize) {
            $this->actionUpdateDriverExt($initialize['user']);
        }
        $end = time();
        echo $end - $start;
    }

    public function actionUpdateComplain()
    {
        $connection = Yii::app()->db_readonly;
        $sql = "SELECT user FROM t_driver WHERE mark = 0";
        $command = $connection->createCommand($sql);
        $driver_ext_initialize = $command->queryAll();
        $start = time();
        foreach ($driver_ext_initialize as $initialize) {
            $driver_id = $initialize['user'];
            $ext = DriverExt::model()->find('driver_id=:driver_id', array(
                ':driver_id' => $driver_id));
            if (!$ext) {
                $ext = new DriverExt();
                $ext->initializeExt($driver_id);
            }
            $ext->punish = DriverPunish::model()->getPunishCount($driver_id);
            $ext->recommend = DriverRecommand::model()->getRecommandCount($driver_id);
            $ext->c_complain = CustomerComplain::model()->count("driver_id=:driver_id", array(":driver_id" => $driver_id));
            $ext->d_complain = DriverComplaint::model()->count('driver_user=:driver_id', array(':driver_id' => $driver_id));
            echo $driver_id . '--' . $ext->save() . "\n";
        }
        $end = time();
        echo $end - $start;
    }

    /**
     *
     * 初始化司机星级  先讨论一下 不要执行
     * php protected/yiic driver levelinitialize
     */
    public function actionLevelInitializeTest()
    {
        $connection = Yii::app()->db;
        $sql = "SELECT user, city_id, year FROM t_driver WHERE mark = 0";
        $command = $connection->createCommand($sql);
        $driver_initialize = $command->queryAll();
        foreach ($driver_initialize as $initialize) {
            self::actionLevelNewRule($initialize['user'], $initialize['city_id']);
        }
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

            //更新数据
            $result = $this->_updateDriverLevelInfo($user, $level, $serviceCount, $lastDay);
            //echo $user.'----'.$level."----".$lastDay."----UPDATE SUCCESS \n";
            return $result;
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
            $result = $this->_updateDriverLevelInfo($user, $level, $serviceCount, $lastDay);
            return $result;
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

            //更新数据
            $result = $this->_updateDriverLevelInfo($user, $level, $serviceCount);
            //echo $user.'----'.$level."----".$lastDay."----UPDATE SUCCESS \n";
            return $result;
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

    /**
     * 更新司机星级信息
     * @param string $user
     * @param float $level
     * @param int $serviceCount
     * @return boolean
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-09-12
     */
    private function _updateDriverLevelInfo($user, $level, $serviceCount)
    {
        //更新t_driver,t_employee
        Yii::app()->db->createCommand()->update('t_driver', array('level' => $level), 'user="' . $user . '"');
        Yii::app()->db->createCommand()->update('t_employee', array('level' => $level), 'user="' . $user . '"');

        $data = array(
            'driver_id' => $user,
            'level' => $level,
            'created' => date('Y-m-d'),
            'order_count' => $serviceCount
        );
        return Yii::app()->db->createCommand()->insert('t_driver_level', $data);
    }

    /**
     * 优惠劵或VIP返现，您的余额大于规定额度，予以激活   每天15：30 运行
     * @auther mengtianxue
     * php yiic.php driver EnableByFee
     */
    public function actionEnableByFee()
    {
        $driver_list = Yii::app()->db_readonly->createCommand()
            ->select("*")
            ->from("t_driver")
            ->where("mark = 1 and block_at = 1")
            ->queryAll();
        foreach ($driver_list as $driver) {
            $base_balance = 100;
            $city_id = $driver['city_id'];
            $driver_id = $driver['user'];
            $balance = DriverBalance::model()->getBalance(0, $driver_id);
            $balance_200 = RCityList::model()->getDriverCityLt(200);
            if (in_array($city_id, $balance_200)) {
                $base_balance = 200;
            }

            if ($balance > $base_balance) {
                $type = DriverLog::LOG_MARK_ENABLE;
                $reason = '优惠劵或VIP返现，您的余额大于规定额度，予以激活。';
                Driver::model()->block($driver_id, Employee::MARK_ENABLE, $type, $reason, true);
            }

        }


    }


    /**
     *  更新redis中 driver 信息
     * @author mengtianxue 2013-05-27
     *  php yiic.php driver DriverSms
     */
    public function actionDriverSms($city_id)
    {
        $offset = 0;
        $pageSize = 100;
        while (true) {
            $criteria = new CDbCriteria();
            $criteria->select = "*";
            $criteria->addCondition('city_id = :city_id');
            $criteria->addCondition('mark = :mark');
            $criteria->params = array(
                ':city_id' => $city_id,
                ':mark' => 0
            );
            $criteria->offset = $offset;
            $criteria->limit = $pageSize;

            $driver = Driver::model()->findAll($criteria);
            if ($driver) {
                foreach ($driver as $v) {
                    $message = '一年最挣钱的时间已经到来，从今天到春节都是各位挣钱的黄金期，本周末订单更会创全年新高，跑一周当平时跑一个月，一年一次错过后悔一年，请大家上线挣钱过年。';
                    $phone = empty($v->ext_phone) ? $v->phone : $v->ext_phone;
                    Sms::SendSMS($phone, $message);
                    echo $phone . "\n";
                }
            } else {
                break;
            }
            $offset += $pageSize;
        }
    }

    /**
     * Get comments count for different level
     *
     * @author qiujianping@edaijia-staff.cn 2014-05-08
     */
    private function _getLevelCommentsCountByDriverId($driver_id, $level = 1){
	echo "[INFO] Get ".$level." comments count for ".$driver_id."\n";
	$sql = "SELECT count(a.order_id) as order_count 
	    FROM t_comment_sms as b, t_order as a
	    WHERE a.driver_id='{$driver_id}' and a.order_id=b.order_id 
	    and b.level='{$level}' and b.sms_type='0'";
        $connection = Yii::app()->db_readonly;
	$command = $connection->createCommand($sql);
	$value = $command->queryRow();
	$ret = 0;
	if(!empty($value)) {
	    $ret = $value['order_count'];
	}
	return $ret;
    }

    /**
     * Get basic order data for a driver in a time range
     *
     * @author qiujianping@edaijia-staff.cn 2014-05-08
     */
    private function _getOrderDataByDriverId($driver_id, $start_time, $end_time){
	$sql = "SELECT count(order_id) as order_count,max(order_id) as max_order_id,
	    min(order_id) as min_order_id 
	    FROM t_order WHERE FROM_UNIXTIME(created)>'{$start_time}' 
	    and FROM_UNIXTIME(created)<'{$end_time}' and driver_id='{$driver_id}'";
	$command = Order::getDbReadonlyConnection()->createCommand($sql);
	$accept_order_count = 0;
	$max_order_id = 0;
	$min_order_id = 0;
	$value = $command->queryRow();
	if(!empty($value)) {
	    $accept_order_count = $value['order_count'];
	    $max_order_id =  isset($value['max_order_id'])? $value['max_order_id']:0;
	    $min_order_id =  isset($value['min_order_id'])? $value['min_order_id']:0;
	}

	if($accept_order_count <= 0) {
	    $accept_order_count = 1;
	}

	$ret = array(
		'accept_order_count' => $accept_order_count,
		'min_order_id' => $min_order_id,
		'max_order_id' => $max_order_id,
		);
	return $ret;
    }

    /**
     * Get cancel rate for a driver in a time range
     *
     * @author qiujianping@edaijia-staff.cn 2014-05-08
     */
    private function _getCancelRateByDriverId($driver_id, $start_time, $end_time, $accept_order_count){
	$sql = "SELECT count(order_id) as order_count 
	    FROM t_order WHERE FROM_UNIXTIME(created)>'{$start_time}' 
	    and FROM_UNIXTIME(created)<'{$end_time}' and (status='2' or status='3') 
	    and driver_id='{$driver_id}'";
        $connection = Order::getDbReadonlyConnection();
	$command = $connection->createCommand($sql);
	$value = $command->queryRow();

	$cancel_order_count = 0;
	$cancel_rate = 0;
	if(!empty($value)) {
	    $cancel_order_count = $value['order_count'];
	    $cancel_rate = $cancel_order_count / $accept_order_count; 
	    $ret = array(
		'cancel_order_count' => $cancel_order_count,
		'cancel_rate' => $cancel_rate,
		);
	} else {
	    $ret = array(
		'cancel_order_count' => 0,
		'cancel_rate' => 0,
		);
	}
	return $ret;
    }

    /**
     * Get reject rate for a driver in a time range
     *
     * @author qiujianping@edaijia-staff.cn 2014-05-08
     */
    private function _getRejectRateByDriverId($driver_id, $start_time, $end_time, $accept_order_count){
	$sql = "SELECT count(order_id) as order_count 
	    FROM t_order_process 
	    WHERE driver_id='{$driver_id}'
	    and created>'{$start_time}' and created<'{$end_time}'
	    and (fail_type='2' or fail_type='3')";

        $connection = Yii::app()->dbreport;
	$command = $connection->createCommand($sql);
	$value = $command->queryRow();
	$reject_order_count = 0;
	$reject_rate = 0;
	if(!empty($value)) {
	    $reject_order_count = $value['order_count'];
	    $reject_rate = $reject_order_count/($reject_order_count + $accept_order_count);
	    $ret = array(
		'reject_order_count' => $reject_order_count,
		'reject_rate' => $reject_rate,
		);
	} else {
	    $ret = array(
		'reject_order_count' => 0,
		'reject_rate' => 0,
		);
	}
	return $ret;
    }

    /**
     * Get comments less than 5
     *
     * @author qiujianping@edaijia-staff.cn 2014-05-08
     */
    private function _getNoPraiseCountByDriverId($driver_id, $min_order_id, $max_order_id){
	$sql = "SELECT count(a.order_id) as order_count 
	    FROM t_order as a,t_comment_sms as b 
	    WHERE a.order_id>='{$min_order_id}' and a.order_id<='{$max_order_id}'
	    and a.driver_id='{$driver_id}' and a.order_id=b.order_id 
	    and b.level<'5' and b.sms_type='0'";
	$command = Yii::app()->db_readonly->createCommand($sql);
	$value = $command->queryRow();
	$non_praise_count = 0;
	if(!empty($value)) {
	    $non_praise_count = $value['order_count'];
	}
	return $non_praise_count;
    }

    /**
     * Get online time for a driver in a time range
     *
     * @author qiujianping@edaijia-staff.cn 2014-05-08
     */
    private function _getOnlineTimeByDriverId($driver_id, $start_time, $end_time){
	echo "[INFO] Get online times for last 7 days for driver from {$start_time} to {$end_time}\n";
	$sql = "SELECT online_time FROM t_driver_online_log 
	    WHERE driver_id='{$driver_id}' 
	    and create_time>'{$start_time}' 
	    and create_time<'{$end_time}'";

	$command = Yii::app()->db_readonly->createCommand($sql);
	$values = $command->queryAll();
	$online_time = 0;
	if(!empty($values)) {
	    // Calculate the sum
	    foreach ($values as $value) {
		$online_time =  $online_time + $value['online_time'];
	    }
	}

	// We get the minutes on line
	$online_time = ceil($online_time/60000);

	return $online_time;
    }

    /**
     * Get received Order number  for a driver in a time range
     *
     * @author qiujianping@edaijia-staff.cn 2014-05-08
     */
    private function _getReceiveOrderCountByDriverId($driver_id, $start_time, $end_time){
	$sql = "SELECT count(order_id) as order_count FROM t_order 
	    WHERE FROM_UNIXTIME(created)>'{$start_time}' and FROM_UNIXTIME(created)<'{$end_time}' 
	    and driver_id='{$driver_id}'";
	$command = Order::getDbReadonlyConnection()->createCommand($sql);
	$value = $command->queryRow();
	$receive_order_count = 0;
	if(!empty($value)) {
	    $receive_order_count = $value['order_count'];
	}
	return $receive_order_count;
    }

    /**
     * Get compeleted Order number  for a driver in a time range
     *
     * @author qiujianping@edaijia-staff.cn 2014-05-08
     */
    private function _getCompleteOrderCountByDriverId($driver_id, $start_time, $end_time){
	$sql = "SELECT count(order_id) as order_count FROM t_order 
	    WHERE FROM_UNIXTIME(created)>'{$start_time}' and FROM_UNIXTIME(created)<'{$end_time}' 
	    and status='1' and driver_id='{$driver_id}'";
	$command = Yii::app()->db_readonly->createCommand($sql);
	$value = $command->queryRow();
	$completed_order_count = 0;
	if(!empty($value)) {
	    $completed_order_count = $value['order_count'];
	}
	return $completed_order_count;
    }

    /**
     * Get average accept time ready time for
     * a driver in a time range
     *
     * @author qiujianping@edaijia-staff.cn 2014-05-08
     */
    private function _getTimeValuesByDriverId($driver_id, $start_time, $end_time, $speed = 200, $arrive_time = 600){
	$sql = "SELECT b.driver_receive_time as receive_time,
	    b.driver_ready_time as ready_time ,b.driver_ready_distance as ready_distance 
	    FROM t_order_ext as b,t_order as a 
	    WHERE a.order_id = b.order_id and FROM_UNIXTIME(a.created) >='{$start_time}' 
	    and FROM_UNIXTIME(a.created) <= '{$end_time}' and a.driver_id='{$driver_id}'";
	$command = Order::getDbReadonlyConnection()->createCommand($sql);
	$values = $command->queryAll();
	$avg_receive_time = 0;
	$avg_ready_time = 0;
	$faster_count = 0;
	$all_count = 0;
	if(!empty($values)) {
	    foreach ($values as $value) {
		$all_count = $all_count + 1;

		// Get count whose speed is faster
		$avg_ready_time = $avg_ready_time + $value['ready_time'];
		if($value['ready_time'] > $arrive_time) {
		    $avg_speed = ($value['ready_distance']*60000)/$value['ready_time']; 
		    if($avg_speed >= $speed) {
			$faster_count =  $faster_count + 1;
		    }
		} else {
		    $faster_count =  $faster_count + 1;
		}

		// Avg receive time
		$avg_receive_time = $avg_receive_time + $value['receive_time'];
	    }
	}

	// Get arrive on time rate
	// Calculate the value
	$faster_ratio = 0;
	if($all_count > 0) {
	    $faster_ratio = $faster_count/$all_count;
	    $avg_receive_time = $avg_receive_time/$all_count;
	    $avg_ready_time = $avg_ready_time/$all_count;
	} 

	$ret = array(
		'ready_on_time_rate' => $faster_ratio,
		'ready_on_time_count' => $faster_count,
		'record_ready_time_count' => $all_count,
		'avg_receive_time' => $avg_receive_time,
		'avg_ready_time' => $avg_ready_time,
		);
	return $ret;
    }


    /**
     * Get inspire data for a specified driver
     *
     * @author qiujianping@edaijia-staff.cn 2014-05-08
     */
    private function _getDriverInspireDataByDriverId($driver_id, $city_id = 0, $speed = 200, $arrive_time = 600){
	$start_time = date("Y-m-d", strtotime("last month"));
	$start_time = $start_time." 07:00:00";
	$start_time_week = date("Y-m-d", strtotime("-1 week"));
	$start_time_week = $start_time_week." 07:00:00";
	$end_time = date("Y-m-d");
	$end_time = $end_time." 07:00:00";
        $connection = Yii::app()->db_readonly;

	echo "[INFO] Get driver inspire datas for driver: ".$driver_id."\n";
	// Get all order numbers for the driver
	$basic_order_info = $this->_getOrderDataByDriverId($driver_id, $start_time, $end_time);
	$accept_order_count =  isset($basic_order_info['accept_order_count'])? $basic_order_info['accept_order_count']:1;
	$max_order_id =  isset($basic_order_info['max_order_id'])? $basic_order_info['max_order_id']:0;
	$min_order_id =  isset($basic_order_info['min_order_id'])? $basic_order_info['min_order_id']:0;

	// Get cancel rate
	$cancel_datas = $this->_getCancelRateByDriverId($driver_id,
		$start_time, $end_time, $accept_order_count); 

	// get reject rate
	$reject_datas = $this->_getRejectRateByDriverId($driver_id,
		$start_time, $end_time, $accept_order_count);

	// get comments less than 5
	$non_praise_count = $this->_getNoPraiseCountByDriverId($driver_id, $min_order_id, $max_order_id);

	// Get datas for last 7 days
	//
	// Get online time
	$online_times = $this->_getOnlineTimeByDriverId($driver_id, $start_time_week, $end_time);

	// Get receive order
	$receive_order_count = $this->_getReceiveOrderCountByDriverId($driver_id, $start_time_week, $end_time);

	// Get completed order
	$completed_order_count = $this->_getCompleteOrderCountByDriverId($driver_id, $start_time_week, $end_time);

	// Get avg accept time
	$time_values = $this->_getTimeValuesByDriverId($driver_id, $start_time_week, $end_time, $speed, $arrive_time);

	// Get level comments
	$five_star_count =  $this->_getLevelCommentsCountByDriverId($driver_id, 5);
	$four_star_count =  $this->_getLevelCommentsCountByDriverId($driver_id, 4);
	$three_star_count =  $this->_getLevelCommentsCountByDriverId($driver_id, 3);
	$two_star_count =  $this->_getLevelCommentsCountByDriverId($driver_id, 2);
	$one_star_count =  $this->_getLevelCommentsCountByDriverId($driver_id, 1);

	// Total comments
	$total_comments = $five_star_count + $four_star_count + $three_star_count +
	    $two_star_count + $one_star_count;

	// Set the attributes
	$params['driver_id'] = $driver_id;
	$params['city_id'] = $city_id;
	$params['online_time'] = $online_times;
	$params['complete_order_count'] = $completed_order_count;
	$params['receive_order_count'] = $receive_order_count;
	$params['reject_rate'] = $reject_datas['reject_rate'];
	$params['cancel_rate'] = $cancel_datas['cancel_rate'];
	$params['non_praise_count'] = $non_praise_count;
	$params['accept_time'] = $time_values['avg_receive_time'];
	$params['ready_time'] = $time_values['avg_ready_time'];
	$params['ready_on_time_rate'] = $time_values['ready_on_time_rate'];
	$params['ready_on_time_count'] = $time_values['ready_on_time_count'];
	$params['record_ready_time_count'] = $time_values['record_ready_time_count'];
	$params['five_star_count'] = $five_star_count;
	$params['four_star_count'] = $four_star_count;
	$params['three_star_count'] = $three_star_count;
	$params['two_star_count'] = $two_star_count;
	$params['one_star_count'] = $one_star_count;
	$params['total_comments'] = $total_comments;

	// Delete the data first and then add the data
	$sql = "DELETE FROM t_driver_inspire_data WHERE `driver_id` = :driver_id";
	$command = Yii::app()->dbreport->createCommand($sql);
	$command->bindParam(":driver_id" , $driver_id);
	$delete = $command->execute();
	Yii::app()->dbreport->createCommand()->insert('t_driver_inspire_data',$params);
    }

    /**
     * Get avg inspire data for a specified city
     *
     * @author qiujianping@edaijia-staff.cn 2014-05-08
     */
    private function _getAvgDriverInspireDataByCityId($city_id = 0){
        echo "[INFO] Get avg infomation for:".$city_id."\n";
	$start_time = date("Y-m-d", strtotime("-1 week"));
	$start_time = $start_time." 07:00:00";
	$end_time = date("Y-m-d");
	$end_time = $end_time." 07:00:00";

	$DEFAULT_DRIVER = 'BJ00000';
	$connection =  Yii::app()->dbreport;

	// Delete the data first and then add the data
	$sql = "DELETE FROM t_driver_inspire_data WHERE `driver_id` = :driver_id and `city_id` = :city_id";
	$command = $connection->createCommand($sql);
	$command->bindParam(":driver_id" , $DEFAULT_DRIVER);
	$command->bindParam(":city_id" , $city_id);
	$delete = $command->execute();

	// Get all the inspire data for the city
	if($city_id != 0) {
	    $sql = "SELECT online_time,complete_order_count,receive_order_count,accept_time,ready_on_time_count,record_ready_time_count FROM t_driver_inspire_data WHERE driver_id != '{$DEFAULT_DRIVER}' and city_id = '{$city_id}'";
	} else {
	    $sql = "SELECT online_time,complete_order_count,receive_order_count,accept_time,ready_on_time_count,record_ready_time_count FROM t_driver_inspire_data WHERE driver_id != '{$DEFAULT_DRIVER}'";
	}
        $command = $connection->createCommand($sql);
        $inspire_datas = $command->queryAll();

	$inspire_datas_count = 0;

	// Neede data
	$avg_online_time = 0;
	$avg_completed_order_count = 0;
	$avg_receive_order_count = 0;
	$avg_ready_on_time_rate = 0;
	$avg_receive_time = 0;

	$total_online_time = 0;
	$total_completed_order_count = 0;
	$total_receive_order_count = 0;
	$total_ready_on_time_count = 0;
	$total_record_ready_time_count = 0;
	$total_receive_time = 0;
	// End

	// Maybe needed in the future
	$avg_reject_rate = 0;
	$avg_cancel_rate = 0;
	$avg_ready_time = 0;
	// End

	foreach($inspire_datas as $driver_inspire_data) {
	    $total_online_time = $total_online_time + $driver_inspire_data['online_time'];
	    $total_completed_order_count = $total_completed_order_count + $driver_inspire_data['complete_order_count'];
	    $total_receive_order_count = $total_receive_order_count + $driver_inspire_data['receive_order_count'];
	    $total_receive_time = $total_receive_time + ($driver_inspire_data['accept_time']*$driver_inspire_data['receive_order_count']);
	    $total_ready_on_time_count = $total_ready_on_time_count + $driver_inspire_data['ready_on_time_count'];
	    $total_record_ready_time_count =  $total_record_ready_time_count + $driver_inspire_data['record_ready_time_count'];
	    $inspire_datas_count =  $inspire_datas_count +1;
	}

	if($inspire_datas_count == 0) {
	    $inspire_datas_count = 1;
	}

	if($total_completed_order_count == 0) {
	    $total_completed_order_count = 1;
	}

	if($total_receive_order_count == 0) {
	    $total_receive_order_count = 1;
	}

	// Get the avg value
	// Avg count set to be *100 
	$avg_receive_order_count = floor(($total_receive_order_count*100)/$inspire_datas_count);

	// Avg completed order num. Set to be *100
	$avg_completed_order_count = floor(($total_completed_order_count*100)/$inspire_datas_count);

	// Avg time value
	$avg_online_time = ceil($total_online_time/$inspire_datas_count);
	$avg_receive_time = ceil($total_receive_time/$total_receive_order_count);

	// Avg ready on time rate 
	if($total_record_ready_time_count == 0) {
	  $total_record_ready_time_count = 1;
	}
	$avg_ready_on_time_rate = $total_ready_on_time_count/$total_record_ready_time_count;

	$params['driver_id'] = $DEFAULT_DRIVER;
	$params['city_id'] = $city_id;
	$params['online_time'] = $avg_online_time;
	$params['complete_order_count'] = $avg_completed_order_count;
	$params['receive_order_count'] = $avg_receive_order_count;
	$params['reject_rate'] = $avg_reject_rate;
	$params['cancel_rate'] = $avg_cancel_rate;
	$params['non_praise_count'] = 0;
	$params['accept_time'] = $avg_receive_time;
	$params['ready_time'] = $avg_ready_time;
	$params['ready_on_time_rate'] = $avg_ready_on_time_rate;
	$params['ready_on_time_count'] = 0;
	$params['record_ready_time_count'] = 0;
	$params['five_star_count'] = 0;
	$params['four_star_count'] = 0;
	$params['three_star_count'] = 0;
	$params['two_star_count'] = 0;
	$params['one_star_count'] = 0;
	$params['total_comments'] = 0;

	$connection->createCommand()->insert('t_driver_inspire_data',$params);
    }

    /**
     * Action test for Driver Inspire data
     * @author qiujianping@edaijia-staff.cn 2014-05-08
     *
     *  php yiic.php driver TestDriverInspireData
     */
    public function actionTestDriverInspireData($driver_id, $city_id = 0)
    {
	$this->_getDriverInspireDataByDriverId($driver_id, $city_id); 
    }

    /**
     * Daily calculate the driver's data for a specified driver.
     * Include cancel rate,
     * reject rate and comments less than 5 for last 30 days;
     * online time, completed order, order accept time, and
     * arrive on time rate for last 7 days.
     *
     * @author qiujianping@edaijia-inc.cn 2014-05-12
     *
     *  php yiic.php driver DriverInspireDataByCityId
     */
    public function actionDriverInspireDataByCityId($city_id = 0, $speed = 200, $arrive_time = 600)
    {
	// Get datas for last 30 days
	//
	// Get all the drivers
        $connection = Yii::app()->db_readonly;
        $sql = "SELECT user,city_id FROM t_driver WHERE mark != 3 and city_id='{$city_id}'";
        $command = $connection->createCommand($sql);
        $drivers = $command->queryAll();

	// For each driver
        foreach ($drivers as $driver) {
	   $this->_getDriverInspireDataByDriverId($driver['user'], $driver['city_id'], $speed, $arrive_time); 
        }

	// At last, we save the avg information as BJ00000 and city_id=0
	// Get all the cites
	$this->_getAvgDriverInspireDataByCityId($city_id);
    }

    /**
     * Daily calculate the driver's data. Include cancel rate,
     * reject rate and comments less than 5 for last 30 days;
     * online time, completed order, order accept time, and
     * arrive on time rate for last 7 days.
     *
     * @author qiujianping@edaijia-inc.cn 2014-05-05
     *
     *  php yiic.php driver DriverInspireData
     */
    public function actionDriverInspireData($speed = 200, $arrive_time = 600)
    {
	// Get datas for last 30 days
	//
	// Get all the drivers
        $connection = Yii::app()->db_readonly;
        $sql = "SELECT user,city_id FROM t_driver WHERE mark != 3";
        $command = $connection->createCommand($sql);
        $drivers = $command->queryAll();

	// For each driver
        foreach ($drivers as $driver) {
	   $this->_getDriverInspireDataByDriverId($driver['user'], $driver['city_id'], $speed, $arrive_time); 
        }

	// At last, we save the avg information as BJ00000 and city_id=0
	// Get all the cites
	$this->_getAvgDriverInspireDataByCityId(0);

        $sql = "SELECT distinct city_id FROM t_driver WHERE mark != 3";
        $command = $connection->createCommand($sql);
        $cities = $command->queryAll();

	// For each city
	echo "[INFO] Get informations for different city\n";
        foreach ($cities as $city) {
	    if($city['city_id'] != 0) {
		$this->_getAvgDriverInspireDataByCityId($city['city_id']);
	    }
        }
    }

 
    /**
     *  手动屏蔽的司机 定时解封
     * @author duke 2014-04-1
     *  php yiic.php driver DriverEnable --start_time=2014-01-01_10:00:00 --is_test=true or false
     */
    public function actionDriverEnable( $is_test = false)
    {
        $offset = 0;
        $pageSize = $is_test ? 2 : 200;
        $i = 0;
       

        while (true) {
            //获取被屏蔽的司机信息
            $sql = 'select * from t_driver_punish where status = '.Driver::MARK_DISNABLE.' and unix_timestamp(un_punish_time) <= '.time().' and limit_time < 3600  order by id asc limit '.$offset.','.$pageSize;
  
            $driver_punish = Yii::app()->db_readonly->createCommand($sql)->queryAll();
            if ($driver_punish) {
                foreach ($driver_punish as $v) {

                    $criterias = new CDbCriteria();
                    $criterias->select = "*";
                    $criterias->addCondition('user = :user');
                    $criterias->addCondition('mark = :mark');
                    $criterias->addCondition('block_mt != :block_mt'); //aiguoxin 2014-08-20 加入了系统屏蔽，取值：0,1,2,3
                    $criterias->params = array(
                        ':user' => $v['driver_id'],
                        ':mark' => Driver::MARK_DISNABLE,
                        ':block_mt' => Driver::MARK_ENABLE
                    );
                    //查找司机表 如果司机状态为屏蔽 且是手动屏蔽则解屏蔽。
                    $driver = Driver::model()->find($criterias);

                    //desc
                    if ($driver)
                    {
                        $driver_id = $driver->user;
                        $mark = $driver->mark;
                        $block = $driver->block_mt;
                        //desc
                        if ($is_test)
                        {
                            var_dump($driver_id); var_dump($mark); var_dump($block);
                        }
                        //到了解除屏蔽时间，自动解除屏蔽
                        $res = DriverPunish::model()->enable_driver($driver_id,DriverPunish::STATUS_ENABLE , '屏蔽时间结束','system',true);
                        //$driver_id, $type, $reason, $operator='system'
                        echo $driver_id.'----enable----'.date('Y-m-d H:i:s')."\n";
                    }

                }
            } else {
                echo 'ok done';
                break;
            }
            $offset += $pageSize;
            $i ++;
            //desc
            if ( $is_test && $i >= 2)
            {
                break;
            }
        }
    }

    /**
    *   add by aiguoxin
    *   统计司机当前代驾次数
    */
    public function actionYearCount(){
        echo '------------start to count driver year driver count...'.PHP_EOL;
        $max=0;
        
        while (true) {
            $sql = "SELECT id,user,city_id FROM t_driver WHERE id>:max and mark != 3 LIMIT 5000";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(":max",$max);
            $driver_list = $command->queryAll();
            if ($driver_list) {
                foreach ($driver_list as $driver) {
                    $flag = false;
                    $max = $driver['id'];
                    $driver_id = $driver['user'];
                    //find open city drivers
                    $city_open = Common::getScoreOpenCitys();
                    foreach ($city_open as $key => $value) {
                        if($key == $driver['city_id']){
                            $flag = true;
                            break;
                        }
                    }
                    //not in open city
                    if(!$flag){
                        echo "id=".$driver['id'].",driver=".$driver_id.' not open this project'.PHP_EOL;
                        continue;
                    }
                    // echo "driver=".$driver_id.' counting...'.PHP_EOL;
                    $driver_ext = DriverExt::model()->getDriverExt($driver_id);
                    //todo 加个下一年计算日期时间，一年后，starttime从新计算
                    
                    //default time 0000:00:00
                    if($driver_ext['startTime'] == 0){
                        echo "id=".$driver['id'].",driver=".$driver_id.' not open this project'.PHP_EOL;
                        continue;
                    }
                    $sql = 'SELECT COUNT(*) FROM t_order where driver_id=:driver_id and booking_time>=:booking_time and status=1';
                    $count = Order::getDbReadonlyConnection()->createCommand($sql)
                    ->bindParam(':driver_id', $driver_id)
                    ->bindParam(':booking_time',$driver_ext['startTime'])
                    ->queryScalar();
                    //update driver_ext
                    $res = DriverExt::model()->changeYearCount($driver_id,$count,1);
                    echo "id=".$driver['id'].",driver=".$driver_id.',startTime='.$driver_ext['startTime'].',count='.$count.PHP_EOL;
                }
            }else{
                break;
            }
        }

    }

    /**
    *   add by aiguoxin
    *   统计司机昨天代驾次数，并更新driver_ext，只累加当年的
    */
    public function actionDriverCount(){
        $yesterday = date("Y-m-d 00:00:00",strtotime("-1 day"));
        $today = date("Y-m-d 00:00:00");
        echo $yesterday.PHP_EOL;
        echo $today.PHP_EOL;
        //find driver and yesterday order count
        $driverList = Order::getDbReadonlyConnection()->createCommand()
            ->select("driver_id,count(1) as total")
            ->from('t_order')
            ->where('status=1 and 
                (FROM_UNIXTIME(booking_time) between :yesterday and :today)', 
                array(
                ':yesterday' => $yesterday,
                ':today' => $today))
            ->group('driver_id')
            ->queryAll();
        //update driver_ext year_driver_count
        $current = strtotime("now");
        // echo count($driverList).PHP_EOL;
        foreach($driverList as $k=>$val){
            $driver_id = $val['driver_id'];
            $orderCount = $val['total'];
            $driverExt = DriverExt::model()->getDriverExt($driver_id);
            if(empty($driverExt)){
                continue;
            }
            $next_year = $driverExt['startTime']+366*24*60*60;
            if($current >= $next_year){//replace year_driver_count
                $res = DriverExt::model()->changeYearCount($driver_id,$orderCount,1);
                // echo 'replace ok, res='.$res.PHP_EOL;
            }else{ //increase year_driver_count
                $res = DriverExt::model()->changeYearCount($driver_id,$orderCount,0);
                // echo 'increase ok, res='.$res.PHP_EOL;
            }
            echo "driver=".$driver_id.',count='.$orderCount.PHP_EOL;
        }
    }

    /**
    *   全国代驾分初始化
    *   @param $start_time 代驾分开始执行时间
    *   @param $is_all 全国执行，默认最初开通的5个城市不初始化
    *
    */
    public function actionDriverScoreInitAllCitys($start_time,$is_all=false,$is_test=false){
        $city_open = Common::getScoreOpenCitys();
        $five_open_city = Yii::app()->params['driverScore'];

        foreach ($city_open as $key => $value) {
            $city_id = $key;
            //最初5个城市是否初始化
            if(!$is_all && isset($five_open_city['scoreCity'][$city_id])){
                continue;
            }
            $this->actionDriverScoreStartTime($city_id,$start_time,$is_test);
        }
    }

    /**
     * @param $city_id
     * @param $start_time '2014-06-09 07:00:00'
     * php yiic driver DriverScoreStartTime --city_id=2 --start_time='2014-06-01 07:00:00' --is_test=1
     * @param bool $is_test
     */
    public function actionDriverScoreStartTime($city_id, $start_time , $is_test = false){
        $offset = 0;
        $pageSize = $is_test ? 2 : 2000;
        $i = 0;
        //echo $start_time.'9999999';die;

        while (true) {
            //获取被屏蔽的司机信息
            $sql = 'select * from t_driver where city_id = '.$city_id.' order by id asc limit '.$offset.','.$pageSize;

            $driver = Yii::app()->db_readonly->createCommand($sql)->queryAll();
            //print_r($driver);die;
            if ($driver) {
                $connection =  Yii::app()->db;
                foreach ($driver as $v) {
                    $sql = "update  t_driver_ext set score = 12 , start_score_time = :start_time  WHERE driver_id=:driver_id";
                    $command = $connection->createCommand($sql);
                    $command->bindParam(":start_time",$start_time);
                    $command->bindParam(":driver_id" , $v['user']);
                    $delete = $command->execute();
                }
            } else {
                echo 'ok done '.$city_id;
                break;
            }
            sleep(3);
            $offset += $pageSize;
            $i ++;
            //desc
            if ( $is_test && $i >= 2)
            {
                break;
            }
        }
    }

    /**
    *   add by aiguoxin
    *   司机扣分盘点--中期盘点
    */
    public function actionScoreMiddle(){
        $max=0;
        while (true) {
            $sql = "SELECT id,user,city_id,phone,ext_phone FROM t_driver WHERE id>:max and mark != 3 LIMIT 1000";
            //test
            // $sql = "SELECT id,user,city_id,phone,ext_phone FROM t_driver WHERE user in('BJ9010','BJ9017','BJ9036','BJ9035','BJ9005')";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(":max",$max);
            $driver_list = $command->queryAll();
            if ($driver_list) {
                foreach ($driver_list as $driver) {
                    $flag = false;
                    $max = $driver['id'];
                    $driver_id = $driver['user'];
                    //find open city drivers
                    $city_open = Common::getScoreOpenCitys();
                    foreach ($city_open as $key => $value) {
                        if($key == $driver['city_id']){
                            $flag = true;
                            break;
                        }
                    }
                    if(!$flag){
                        continue;
                    }
                    //handle driver
                    $driverExt = DriverExt::model()->getDriverExt($driver_id);
                    if(empty($driverExt)){
                        continue;
                    }
                    $message = $driver_id.'师傅,恭喜您截止到目前尚未有扣分,请再接再厉,提供稳定优质的代驾服务。';
                    if($driverExt['score'] < 12){
                        $currentScore = $driverExt['score'];
                        $deductScore = 12 - $currentScore;
                        $complainCount=DriverPunishLog::model()->getPunishCount($driver_id);
                        $message = $driver_id.'师傅,截止到目前您已受到 '.$complainCount.' 次投诉,共扣 '.$deductScore.' 分,余 '.$currentScore.' 分,为了您能继续接单工作,请注意提升您的服务品质。';
                    }
                    $i_phone = ($driver['ext_phone']) ? $driver['ext_phone'] : $driver['phone'];
                    echo 'phone='.$i_phone.',driver='.$driver_id.PHP_EOL;
                    // $i_phone=15101061387;
                    $res = Sms::SendSMS($i_phone, $message);
                }
            }else{
                break;
            }
        }
    }


    /**
    *   add by aiguoxin
    *   司机扣分盘点--末期盘点 send message
    */
    public function actionScoreEnd(){
        $max=0;
          while (true) {
            $sql = "SELECT id,user,city_id,phone,ext_phone FROM t_driver WHERE id>:max and mark != 3 LIMIT 1000";
            //test
            // $sql = "SELECT id,user,city_id,phone,ext_phone FROM t_driver WHERE user in('BJ9010','BJ9017','BJ9036','BJ9035','BJ9005')";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(":max",$max);
            $driver_list = $command->queryAll();
            if ($driver_list) {
                foreach ($driver_list as $driver) {
                    $flag = false;
                    $max = $driver['id'];
                    $driver_id = $driver['user'];
                    //find open city drivers
                    $city_open = Common::getScoreOpenCitys();
                    foreach ($city_open as $key => $value) {
                        if($key == $driver['city_id']){
                            $flag = true;
                            break;
                        }
                    }
                    if(!$flag){
                        continue;
                    }
                    //handle driver
                    $driverExt = DriverExt::model()->getDriverExt($driver_id);
                    if(empty($driverExt)){
                        continue;
                    }
                    //send message
                    $message = $driver_id.'师傅,本次试运营结束，代驾分将于7.1日恢复至12分，7.1日开始正式运营，代驾分将根据一年代驾次数恢复。扣分记录详见司机端。';
                    $i_phone = ($driver['ext_phone']) ? $driver['ext_phone'] : $driver['phone'];
                    echo 'phone='.$i_phone.PHP_EOL;
                    // $i_phone=15101061387;
                    $res = Sms::SendSMS($i_phone, $message);
                }
            }else{
                break;
            }
        }
    }

    /**
    *   add by aiguoxin
    *   司机扣分盘点--末期盘点 recover score 12
    */
    public function actionScoreRecover(){
        $max=0;
          while (true) {
            $sql = "SELECT id,user,city_id,phone,ext_phone FROM t_driver WHERE id>:max and mark != 3 LIMIT 1000";
            //test
            // $sql = "SELECT id,user,city_id,phone,ext_phone FROM t_driver WHERE user in('BJ9010','BJ9017','BJ9036','BJ9035','BJ9005')";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(":max",$max);
            $driver_list = $command->queryAll();
            if ($driver_list) {
                foreach ($driver_list as $driver) {
                    $flag = false;
                    $max = $driver['id'];
                    $driver_id = $driver['user'];
                    //find open city drivers
                    $city_open = Common::getScoreOpenCitys();
                    foreach ($city_open as $key => $value) {
                        if($key == $driver['city_id']){
                            $flag = true;
                            break;
                        }
                    }
                    if(!$flag){
                        continue;
                    }
                    //handle driver
                    $driverExt = DriverExt::model()->getDriverExt($driver_id);
                    if(empty($driverExt)){
                        continue;
                    }
                    //recover score 12
                    DriverExt::model()->addScore($driver_id,PHP_INT_MAX-12);// > 12 ,set score 12
                }
            }else{
                break;
            }
        }
    }

   /*
    *   系统扣分规则
    */
    public function actionDriverPunish(){
        //判断是否当月最后一天，不是则退出
        $firstday = date('Y-m-01'); 
        $lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day")); 
        $currentday=date('Y-m-d');
        echo  "currentday=".$currentday.",lastday=".$lastday.PHP_EOL;
        EdjLog::info("currentday=".$currentday+",lastday=".$lastday);
        if($currentday != $lastday){
            echo "today is not last day,exit".PHP_EOL;
            EdjLog::info("today is not last day,exit");
            return;
        }

        $currentMonth = date("Y-m");
        $refuseMsg="师傅，您本月拒单率达到本地前5%，且拒单在3单及以上，扣1分，请减少拒单行为。";
        $cancelMsg="师傅，您本月销单率达到本地前5%，且销单在4单及以上，扣1分，请减少不必要的销单行为。";
        //1.给惩罚日志完善城市ID
        // $punishList = DriverPunishLog::model()->getPunishList();
        // if($punishList){
        //     foreach ($punishList as $punishLog) {
        //         $driver = Driver::model()->getProfile($punishLog['driver_id']);
        //         if($driver){
        //             EdjLog::info('update punishLog id='.$punishLog['id'].' city ok');
        //             echo 'update punishLog id='.$punishLog['id'].' city ok'.PHP_EOL;
        //             DriverPunishLog::model()->updatePunishCity($punishLog['id'],$driver->city_id);
        //         }
        //     }
        // }
        /******************添加记录前，先检查本月本市是否操作过*************************/
        //test drivers
        $drivers=array();
        
        $citys = MonthDeductCity::model()->getAllSettingCitys();
        foreach($citys as $key=>$value) {
            $city_id = $value;
            $ruleArray=Common::checkOpenScoreCity($city_id,'rule');
            if(empty($ruleArray)){
                continue;
            }
            $reject_count=DriverPunishLog::model()->getCountByCity($city_id,DriverPunishLog::REJECT_RATE_TYPE);
            
            EdjLog::info($currentMonth.',city='.$city_id.',reject_count='.$reject_count);
            echo $currentMonth.',city='.$city_id.',reject_count='.$reject_count.PHP_EOL;
            //2.每个月执行一次拒单率，complain_type_id=10000
            $drivers=DriverInspireData::model()->getTopRejectDriverByCityId($city_id,5);
            //test
            // $drivers=array('BJ9016','BJ9029','BJ9017','BJ9020','BJ9021');
            if($reject_count == 0){
               for($i=0;$i<count($drivers);$i++){
                    $driver_id=$drivers[$i];
                    
                    if(DriverStatus::model()->single_get('reject'.$driver_id)){
                        continue;
                    }

                    if(!$this->canSend($driver_id,$firstday,$currentday,DriverPunishLog::REJECT_RATE_TYPE)){
                        continue;
                    }

                     //-1
                    $driver_ext_mod = new DriverExt(); //扣除司机对应分数 、 查看扣分后是否应该屏蔽司机、 发送扣分短信，屏蔽短信
                    $res = $driver_ext_mod->scoreDeduct($driver_id,-1,DriverPunishLog::REJECT_RATE_TYPE);
                    $block_day = $res['update_res'] && $res['had_punished'] ? $res['block_day'] : 0; //司机是否被屏蔽了

                    $param = array(
                                    'driver_id' => $driver_id,
                                    'customer_complain_id' => 0,
                                    'complain_type_id' => DriverPunishLog::REJECT_RATE_TYPE,
                                    'operator' => 'system',
                                    'driver_score'=>-1,
                                    'block_day' =>$block_day,//需要存，延迟屏蔽需要这个数据
                                    'comment_sms_id' => 0,
                                    'city_id'=>$city_id,
                                    'create_time' => date('Y-m-d H:i:s'),
                                    'deduct_reason' =>$ruleArray['0'] ['norm'].$ruleArray['0']['mark_norm'],
                                    'revert'=> DriverPunishLog::REVERT_NO_EXECUTE,
                                );
                    $res = DriverPunishLog::model()->addData($param);
                    EdjLog::info($currentMonth.' add driver='.$driver_id.' for reject rate 10%');
                    echo $currentMonth.' add driver='.$driver_id.' for reject rate 10%'.PHP_EOL;
                   
                    EdjLog::info($currentMonth.' -1 driver='.$driver_id.' for reject rate 10%');
                    echo $currentMonth.' -1 driver='.$driver_id.' for reject rate 10%'.PHP_EOL;
                    $driver_info = Driver::model()->getProfile($driver_id);
                    //send msg
                    if($driver_info){
                        $i_phone = ($driver_info->ext_phone) ? $driver_info->ext_phone : $driver_info->phone;
                        $res = Sms::SendSMS($i_phone, $driver_id.$refuseMsg);
                        echo 'send msg to '.$i_phone.' ok'.PHP_EOL;
                    }
                    DriverStatus::model()->single_set('reject'.$driver_id,1,3600*24);
                    //统计每个司机具体的拒单率
                    $res = DriverInspireData::model()->getRejectRateByDriverId($driver_id);
                    echo 'driver='.$driver_id.' 排名='.$res['ranking'].' 拒单率='.$res['reject_rate'].PHP_EOL;
                    EdjLog::info('driver='.$driver_id.' 排名='.$res['ranking'].' 拒单率='.$res['reject_rate']);
                    //test
                    // break;
                } 
            }
            //3.每个月执行一次销单率，complain_type_id=10001
            $cancel_count=DriverPunishLog::model()->getCountByCity($city_id,DriverPunishLog::CANCEL_RATE_TYPE);
            EdjLog::info($currentMonth.',city='.$city_id.',cancel_count='.$cancel_count);
            echo $currentMonth.',city='.$city_id.',cancel_count='.$cancel_count.PHP_EOL;
            $drivers=DriverInspireData::model()->getTopCancelDriverByCityId($city_id,5);
            //test
            // $drivers=array('BJ9016','BJ9010','BJ9036','BJ9005');

            if($cancel_count == 0){
               for($i=0;$i<count($drivers);$i++){
                    $driver_id=$drivers[$i];
                    if(DriverStatus::model()->single_get('cancel'.$driver_id)){
                        continue;
                    }

                    if(!$this->canSend($driver_id,$firstday,$currentday,DriverPunishLog::CANCEL_RATE_TYPE)){
                        continue;
                    }
                    //-1
                    $driver_ext_mod = new DriverExt(); //扣除司机对应分数 、 查看扣分后是否应该屏蔽司机、 发送扣分短信，屏蔽短信
                    $res = $driver_ext_mod->scoreDeduct($driver_id,-1,DriverPunishLog::CANCEL_RATE_TYPE);
                    $block_day = $res['update_res'] && $res['had_punished'] ? $res['block_day'] : 0; //司机是否被屏蔽了

                    $param = array(
                                    'driver_id' => $driver_id,
                                    'customer_complain_id' => 0,
                                    'complain_type_id' => DriverPunishLog::CANCEL_RATE_TYPE,
                                    'operator' => 'system',
                                    'driver_score'=>-1,
                                    'block_day' =>$block_day,
                                    'comment_sms_id' => 0,
                                    'city_id'=>$city_id,
                                    'create_time' => date('Y-m-d H:i:s'),
                                    'deduct_reason' =>$ruleArray['1'] ['norm'].$ruleArray['1']['mark_norm'],
                                    'revert'=> DriverPunishLog::REVERT_NO_EXECUTE,
                                );
                    $res = DriverPunishLog::model()->addData($param);
                    EdjLog::info($currentMonth.' add driver='.$driver_id.' for cancel rate 10%');
                    echo $currentMonth.' add driver='.$driver_id.' for cancel rate 10%'.PHP_EOL;
                   
                    EdjLog::info($currentMonth.' -1 driver='.$driver_id.' for cancel rate 10%');
                    echo $currentMonth.' -1 driver='.$driver_id.' for cancel rate 10%'.PHP_EOL;
                    $driver_info = Driver::model()->getProfile($driver_id);
                    //send msg
                    if($driver_info){
                        $i_phone = ($driver_info->ext_phone) ? $driver_info->ext_phone : $driver_info->phone;
                        $res = Sms::SendSMS($i_phone, $driver_id.$cancelMsg);
                        echo 'send msg to '.$i_phone.' ok'.PHP_EOL;
                    }
                    DriverStatus::model()->single_set('cancel'.$driver_id,1,3600*24);
                    //获取每个司机销单率
                    $res = DriverInspireData::model()->getCancelRateByDriverId($driver_id);
                    echo 'driver='.$driver_id.' 排名='.$res['ranking'].' 销单率='.$res['cancel_rate'].PHP_EOL;
                    EdjLog::info('driver='.$driver_id.' 排名='.$res['ranking'].' 销单率='.$res['cancel_rate']);
                } 
            }
        //test
        // break;
        }
        
    }

    /**
    *   司机是否符合扣分条件
    *
    *
    */
    private function canSend($driver_id,$firstday,$currentday,$type){
        $canSend = true;
         //解约司机不发送
        if(Driver::model()->getStatus($driver_id) == 3){
            $canSend = false;
        }
        //派单次数需要高于10次
        $order_count= Order::model()->getOrderCountByTime($driver_id,$firstday,$currentday);
        $refuse_order_count = DriverRejectOrderDetail::model()->getOrderNumByDriverIdAndTime($driver_id,$firstday,$currentday);
        $all_order_count = $order_count + $refuse_order_count;
        if($all_order_count <= 10){
            $canSend = false;
        }
        $res = DriverInspireData::model()->getInspireDataByDriverId($driver_id);
        $monthCount= $res['mon_order_count'];
        if($type==DriverPunishLog::REJECT_RATE_TYPE){//拒单需要3单及以上
            $rate = $res['reject_rate'];
            if($rate<1){
                try{
                    $count = ($monthCount*$rate)/(1-$rate); //拒单率=拒单数/(拒单数+总订单数)
                    $count = round($count);
                    if($count < 3){
                        $canSend = false;
                    }
                }catch(Exception $e){
                    $canSend = false;
                }
            }
        }

        if($type==DriverPunishLog::CANCEL_RATE_TYPE){//销单需要4单及以上
            $rate = $res['cancel_rate'];
             try{
                 $count = $monthCount*$rate; //销单率=销单数/总订单数
                 $count = round($count);
                 if($count < 4){
                    $canSend = false;
                }
            }catch(Exception $e){
                $canSend = false;
            }
        }

        return $canSend;
    }

    /**
    *   上线前统计拒单和销单的司机
    *
    */
    public function actionRandTest($city_id){
        $drivers=DriverInspireData::model()->getTopRejectDriverByCityId($city_id,10);
        foreach($drivers as $driver_id){
            $res = DriverInspireData::model()->getRejectRateByDriverId($driver_id);
            echo 'driver='.$driver_id.' reject='.$res['ranking'].' rate='.$res['reject_rate'].PHP_EOL;
        }

        $drivers=DriverInspireData::model()->getTopCancelDriverByCityId($city_id,10);
        foreach($drivers as $driver_id){
            $res = DriverInspireData::model()->getCancelRateByDriverId($driver_id);
            echo 'driver='.$driver_id.' cancel='.$res['ranking'].' rate='.$res['cancel_rate'].PHP_EOL;
        }
    }

    /**
    *   恢复司机签约信息
    *   aiguoxin 
    *
    */
    public function actionRevertDriverEntry(){

        $sql = "select id_card from t_driver_recruitment where status=3";
        $command = Yii::app()->db_readonly->createCommand($sql);
        $recuritment_list = $command->queryAll();
        foreach ($recuritment_list as $recuritment) {
            $id_card=$recuritment['id_card'];
            //find driver
            $sql = "SELECT user,name,city_id FROM t_driver WHERE id_card=:id_card";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(":id_card",$id_card);
            $driver_list = $command->queryAll();
            foreach($driver_list as $driver){
                echo 'driver='.$driver['user'].',start to recover entry data'.PHP_EOL;
                // 1.update recruitment status
                $update_sql="update t_driver_recruitment set status=4 where id_card=:id_card and status=3";
                Yii::app()->db->createCommand($update_sql)->execute(array(
                    ':id_card' => $id_card,
                ));
                echo 'driver='.$driver['user'].',update recruitment status'.PHP_EOL;
                // //2.记录log流水
                // $insertArr = array();
                // $insertArr['name'] = $driver['name'];
                // $insertArr['id_card'] = $id_card;
                // $insertArr['message'] = '签约成功';
                // $log_status = Driver::model()->insertDriverStatusLog($insertArr);
                // echo 'driver='.$driver['user'].',记录log流水 ok'.PHP_EOL;
                // //3.更新t_driver_id_address表的状态
                // $address = new DriverIdPool();
                // $address->usedDriverId($driver['user']);
                // echo 'driver='.$driver['user'].',更新t_driver_id_address表的状态 ok'.PHP_EOL;
                //4.初始优惠券信息
                BonusLibrary::model()->addBonusLibrary($driver['user']);

                echo 'driver='.$driver['user'].',队列执行初始化信息费，优惠券 ok'.PHP_EOL;
            }
        }
    }

    /**
    *   恢复司机代驾分和时间
    *
    */
    public function actionRevertDriverScore(){
        $sql = "SELECT driver_id FROM t_driver_ext WHERE start_score_time='0000-00-00 00:00:00'";
        $command = Yii::app()->db_readonly->createCommand($sql);
        $driver_list = $command->queryAll();
        foreach($driver_list as $driver_ext){
            $driver_id = $driver_ext['driver_id'];
            echo 'start to handler driver='.$driver_id.PHP_EOL;
            //获取司机的签约时间
            $driver_info = Driver::model()->getProfile($driver_id);
            $create_time = $driver_info['created'];
            //给司机+12分
            DriverExt::model()->addScore($driver_id,12);
            //更新代驾时间
            DriverExt::model()->updateScoreStartTime($driver_id,$create_time);
        }
    }

    /**
    *   @author aiguoxin
    *   每天早上6:50点运行：获取在线司机，从6:50开始重新设置为在线.防止司机端6:59左右再次上线问题，导致记录不了在线时间
    */
    public function actionOnline($proxy=false){
        if (date('H', time()) == 06) { //
            $this->resetDriverOnline($proxy);
        }else{
            echo "\n--------not run scape time------\n";
        }
    }

    /**
    *   @author aiguoxin
    *   重新设置司机在线状态
    */
    private function resetDriverOnline($proxy=false){
        $today = date("Y-m-d 06:50:00",time());
        $current_time = strtotime($today);
        //获取当前在线的司机
	if($proxy) {
	    $onlines=DriverStatus::model()->onlines_redishaproxy();
	}
	else {
            $onlines=DriverStatus::model()->onlines();
	}
        foreach($onlines as $driver_id=>$timestamp) {
            echo '开始处理driver='.$driver_id.PHP_EOL;
            EdjLog::info('开始处理driver='.$driver_id);
            //读取redis信息，获取上次登录信息,这些司机一定是7点之前已经登录,先计算这段时间存入db,并更新上线时间为当前时间
            $last_online=DriverStatus::model()->getWorkTimeStamp($driver_id);
            if($last_online){
                $online_time = ($current_time-$last_online)*1000;//转毫秒
                if($online_time <=0){
                    echo 'driver='.$driver_id.'上线时间有误，过滤'.PHP_EOL;
                    EdjLog::info('driver='.$driver_id.'上线时间有误，过滤');
                    continue;
                }
                if($online_time > DriverOnlineLog::MAX_ONLINE_TIME){
                    echo 'driver='.$driver_id.'在线时间时间超过一天，过滤，online='.$last_online.',current_time='.$current_time.PHP_EOL;
                    EdjLog::info('driver='.$driver_id.'在线时间时间超过一天，过滤，online='.$last_online.',current_time='.$current_time);
                    continue;
                }
                //更新redis上线时间点
                DriverStatus::model()->setWorkTimeStamp($driver_id,$current_time);
                //存入db
                $res=DriverOnlineLog::model()->addDriverOnlineLog($driver_id,$online_time,$today);//存毫秒
                if($res){
                    echo 'driver='.$driver_id.'上线时间更新成功'.PHP_EOL;
                    EdjLog::info('driver='.$driver_id.'上线时间更新成功'.',online='.$last_online.',finish='.$current_time);

                }else{
                    echo 'driver='.$driver_id.'上线时间更新失败'.PHP_EOL;
                    EdjLog::info('driver='.$driver_id.'上线时间更新失败');
                }
            }
        }
    }

    /**
    *   司机扣分屏蔽延迟48小时执行,每5分钟执行一次
    *
    */
    public function actionScoreBlock(){
        //获取48小时之前记录，并且revert=2的记录
        $now = time();
        $create_time = $now-48*3600;
        $create_time = date('Y-m-d H:i:s',$create_time);
        echo '开始执行日期：'.$create_time.' 之前的扣分屏蔽处罚'.PHP_EOL;
        EdjLog::info('开始执行日期：'.$create_time.' 之前的扣分屏蔽处罚');
        $driver_punish_list = DriverPunishLog::model()->findAll('revert=:revert and create_time<:create_time', 
            array(':revert'=>DriverPunishLog::REVERT_NO_EXECUTE,':create_time'=>$create_time));
        if($driver_punish_list){
            //开始处罚，调用屏蔽
            foreach ($driver_punish_list as $driver_punish) {
                $driver_id = $driver_punish['driver_id'];
                $reason_id = $driver_punish['customer_complain_id'];
                $going_score_punish = $driver_punish['block_day'];
                //更新revert状态为0,已处理状态
                $attr = array('revert'=>DriverPunishLog::REVERT_NO);
                $res = DriverPunishLog::model()->updateByPk($driver_punish['id'], $attr);

                if($going_score_punish < 1){ //不屏蔽的话，则跳过
                    echo 'id='.$driver_punish['id'].'不进行屏蔽操作'.PHP_EOL;
                    EdjLog::info('id='.$driver_punish['id'].'不进行屏蔽操作');
                    continue;
                }
                $message = '投诉'.$reason_id.'生效，扣'.abs($driver_punish['driver_score']).'分，屏蔽'.$going_score_punish.'天';
                $res = DriverPunish::model()->disable_driver($driver_id,$reason_id,$message,$going_score_punish);
                if($res == 2){ //司机状态没有修改则补发短信
                    $message = $driver_id.' 师傅，您已被屏蔽，原因：'.$message.'。';
                    $driver_info = Driver::model()->getProfile($driver_id);
                    if($driver_info){
                        Sms::SendSMS($driver_info->ext_phone, $message);
                    }
                }
                echo 'id='.$driver_punish['id'].'屏蔽操作成功'.PHP_EOL;
                EdjLog::info('id='.$driver_punish['id'].'屏蔽操作成功');
            }
        }

    }

    /**
    *   加载司机redis
    */
    public function actionLoadDriver(){
        $handle = @fopen("/opt/driver.txt", "r");
        if ($handle) {
            while (!feof($handle)) {
                $driver_id = trim(fgets($handle, 4096));
                //更新司机支持代驾和洗车
                Driver::model()->updateAll(array('service_type'=>'00000000000000000000000000000011'),
                        'user = :user',array(':user'=>$driver_id));
                //刷新缓存
                DriverStatus::model()->loadDriver($driver_id);
                echo 'driver_id='.$driver_id.'加载成功...'.PHP_EOL;
            }
            fclose($handle);
        }
        
    }

    /**
    *   去除洗车司机，状态改成只支持代驾
    *
    */
    public function actionRmXiCheDriver(){
        $handle = @fopen("/opt/rmdriver.txt", "r");
        if ($handle) {
            while (!feof($handle)) {
                $driver_id = trim(fgets($handle, 4096));
                //更新司机支持代驾和洗车
                Driver::model()->updateAll(array('service_type'=>Driver::SERVICE_TYPE_FOR_DAIJIA),
                        'user = :user',array(':user'=>$driver_id));
                //刷新缓存
                DriverStatus::model()->loadDriver($driver_id);
                echo 'driver_id='.$driver_id.'加载成功...'.PHP_EOL;
            }
            fclose($handle);
        }
    }


    /*
    *   定时脚本扣分
    */
    public function actionDriverScore(){ 
        $msg="师傅，公司严禁私自刷白天业务订单，扣6分，请严格遵守公司规定。";
        $reason='白天业务刷单作弊';
        $sql = "SELECT id,driver_id,comment FROM t_activity_anti_checklist WHERE updated is null";
        $command = Yii::app()->dbreport->createCommand($sql);
        $drivers = $command->queryAll();

        $score = -6;
        foreach($drivers as $driver){
            $driver_id=$driver['driver_id'];
            //更新状态
            $res=ActivityAntiChecklist::model()->updateByPk($driver['id'], array (
                    'updated'=>1));
            if($res){
                EdjLog::info('driver='.$driver_id.' -6');
                echo 'driver='.$driver_id.' 更新状态成功'.PHP_EOL;
            }else{
                EdjLog::info('driver='.$driver_id.' -6');
                echo 'driver='.$driver_id.' 更新状态失败'.PHP_EOL;
            }
             //-6
            $driver_info = Driver::model()->getProfile($driver_id);
            if(empty($driver_info)){
                continue;
            }
            $city_id=$driver_info->city_id;
            $driver_ext_mod = new DriverExt(); //扣除司机对应分数 、 查看扣分后是否应该屏蔽司机、 发送扣分短信，屏蔽短信
            $res = $driver_ext_mod->scoreDeduct($driver_id,$score,DriverPunishLog::V2_REWARD_TYPE);
            $block_day = $res['update_res'] && $res['had_punished'] ? $res['block_day'] : 0; //司机是否被屏蔽了

            $param = array(
                            'driver_id' => $driver_id,
                            'customer_complain_id' => 0,
                            'complain_type_id' => DriverPunishLog::V2_REWARD_TYPE,
                            'operator' => 'system',
                            'driver_score'=>$score,
                            'block_day' =>$block_day,//需要存，延迟屏蔽需要这个数据
                            'comment_sms_id' => 0,
                            'city_id'=>$city_id,
                            'create_time' => date('Y-m-d H:i:s'),
                            'deduct_reason' =>$reason,
                            'revert'=> DriverPunishLog::REVERT_NO_EXECUTE,
                        );
            $res = DriverPunishLog::model()->addData($param);
            EdjLog::info('add driver='.$driver_id.' -6');
            echo 'add driver='.$driver_id.' -6'.PHP_EOL;
           
            //send msg
            if($driver_info){
                $i_phone = ($driver_info->ext_phone) ? $driver_info->ext_phone : $driver_info->phone;
                $res = Sms::SendSMS($i_phone, $driver_info->name.$msg);
                echo 'send msg to '.$i_phone.' ok'.PHP_EOL;
            }
            
        } 
    }

    /**
     * 扣除司机装备押金,获取未付款的订单
     * 每2分钟执行一次
     */
    public function actionDeductDeposit(){
        $sql = "SELECT id, city_id, driver_id,order_number FROM t_driver_order WHERE order_status=0";
        $command = Yii::app()->db_readonly->createCommand($sql);
        $orderList = $command->queryAll();
        foreach($orderList as $order){
            $driver_id = $order['driver_id'];
            $city_id = $order['city_id'];
            $id = $order['id'];
            //开始扣款
            $ret = FinanceWrapper::settleDriver($driver_id,$city_id , DriverOrder::DEPOSIT_MONEY,
                EmployeeAccount::CHANNEL_DEVICE_FEE, DriverOrder::DEPOSIT_TYPE);
            $success = FinanceConstants::isSuccess($ret);
            if($success){
                echo 'id'.$id.'已经扣款成功'.PHP_EOL;
                EdjLog::info('id'.$id.'已经扣款成功');
                DriverOrder::model()->updateStatus($id,DriverOrder::STATUS_PAYED);
            }else{
                echo 'id'.$id.'已经扣款失败----error'.json_encode($ret).PHP_EOL;
                EdjLog::info('id'.$id.'已经扣款失败-----error'.json_encode($ret));
            }
        }
    }

    /**
     * t_driver_recruitment中已经入职司机driver_id为空，数据恢复
     */
    public function actionCardRecover()
    {
        $sql = "SELECT id, id_card FROM t_driver_recruitment where status in(4,5,6) and driver_id =''";
        $command = Yii::app()->db_readonly->createCommand($sql);
        $driverRecuritments = $command->queryAll();
        foreach ($driverRecuritments as $driverRecuritment) {
            //找到司机工号
            $idCard = $driverRecuritment['id_card'];
            $driver = Driver::model()->getDriverByIdCard($idCard);
            if($driver){
                $driver_id = $driver['user'];
                //更新数据
                $res = DriverRecruitment::model()->updateByPk($driverRecuritment['id'],array(
                    'driver_id'=>$driver_id)
                );
                if($res){
                    echo 'id='.$driverRecuritment['id'].'更新司机工号成功driver_id='.$driver_id.PHP_EOL;
                    EdjLog::info('id='.$driverRecuritment['id'].'更新司机工号成功driver_id='.$driver_id);
                }else{
                    echo 'id='.$driverRecuritment['id'].'更新司机工号失败......driver_id='.$driver_id.PHP_EOL;
                    EdjLog::info('id='.$driverRecuritment['id'].'更新司机工号失败......driver_id='.$driver_id);
                }
            }
        }
    }

    /**
     * @param int $city
     * 更新旧签约流程司机状态
     */
    public function actionFlowData($city=0){
        /**更新已经路考通过和签约的司机，把road_new数据更新**/
        $max=0;
        while (true) {
            if($city){
                $sql = "SELECT id FROM t_driver_recruitment  WHERE id>:max and city_id=:city_id and road_new!=3 and status in(3,4,5,6) LIMIT 1000";
            }else {
                $sql = "SELECT id FROM t_driver_recruitment  WHERE id>:max and road_new!=3 and status in(3,4,5,6) LIMIT 1000";
            }
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(":max", $max);
            if($city) {
                $command->bindParam(":city_id", $city);
            }
            $driver_list = $command->queryAll();
            if ($driver_list) {
                foreach ($driver_list as $driver) {
                    DriverRecruitment::model()->updateByPk($driver['id'],array(
                        'road_new'=>DriverRecruitment::STATUS_ROAD_FIELD_PASS));
                    echo 'id='.$driver['id'].'路考状态同步road_new ok'.PHP_EOL;
                }
            }else{
                break;
            }
        }
        /****同步在线考试通过和签约司机信息到exam中****/
        $max=0;
        while (true) {
            if($city){
                $sql = "SELECT id FROM t_driver_recruitment  WHERE id>:max and city_id=:city_id and exam!=1 and status in(2,3,4,5,6,7) LIMIT 1000";
            }else{
                $sql = "SELECT id FROM t_driver_recruitment  WHERE id>:max and exam!=1 and status in(2,3,4,5,6,7) LIMIT 1000";
            }
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(":max", $max);
            if($city){
                $command->bindParam(":city_id", $city);
            }
            $driver_list = $command->queryAll();
            if ($driver_list) {
                foreach ($driver_list as $driver) {
                    DriverRecruitment::model()->updateByPk($driver['id'],array(
                        'exam'=>DriverRecruitment::STATUS_ONLINE_EXAM_PASS));
                    echo 'id='.$driver['id'].'在线考核状态同步road_new ok'.PHP_EOL;
                }
            }else{
                break;
            }
        }
    }


    /**
     * 更改司机装备订单未待制卡
     * driver_order中order_status是已经付款，并且司机有二维码的,才能制作工卡
     * 目前二维码是每天中午生成一次，这个可以放到每天下午2点执行一次即可
     */
    public function actionDriverOrderStatus(){
        //遍历t_driver_order中已经付款司机
        $max=0;
        while (true) {
            $sql = "SELECT id,driver_id FROM t_driver_order  WHERE id>:max and order_status=1  LIMIT 1000";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(":max", $max);
            $order_list = $command->queryAll();
            if ($order_list) {
                foreach ($order_list as $order) {
                    //t_driver表判断是否有二维码
                    $driver = Driver::model()->getProfile($order['driver_id']);
                    if($driver){
                        $qrCode = $driver['two_code_pic'];
                        $pic = $driver['picture'];
                        if($qrCode && $pic){
                            DriverOrder::model()->updateStatus($order['id'],DriverOrder::STATUS_TO_CARD);
                            echo 'driver_id='.$order['driver_id'].'状态更新成功'.PHP_EOL;
                            EdjLog::info('driver_id='.$order['driver_id'].'状态更新成功');
                        }
                    }
                }
            }else{
                break;
            }
        }
    }
}
