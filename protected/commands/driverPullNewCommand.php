<?php

/**
 * 司机拉新活动command
 * clz
 */
class driverPullNewCommand extends LoggerExtCommand
{
    /**
     * 每日查询昨日签约的司机,奖励推荐人
     * @param $begin_time
     * @param $end_time
     */
    public function actionRewardRecommender($signup_date = '')
    {
        //set_time_limit(0);
        $title = 'handle contract driver';
        echo Common::jobBegin($title);
        $config = DriverPullNewConfig::model()->getConfig();
        if (!$config) {
            EdjLog::info('没有配置的活动信息');
            return;
        }
        $yestoday = $signup_date;
        if(empty($signup_date)){
            $yestoday = date("Y-m-d", strtotime("-1 day"));
        }
        EdjLog::info('=============='.$yestoday);
        EdjLog::info('开始处理' . $yestoday . '日签约的司机');
        $drivers = Driver::model()->getDriverBydate($yestoday);//获取昨日签约司机
        if (!$drivers) {
            EdjLog::info($yestoday . '日没有司机签约');
            return;
        }
        $citys = explode(',', $config['city_id']);
        foreach ($drivers as $driver) {
            if (empty($driver['recommender'])) {
                EdjLog::info('工号为' . $driver['user'] . '司机的推荐人为空');
                continue;
            }
            $recommender = Driver::model()->find('user=:user', array(':user' => $driver['recommender']));
            //判断推荐人是不是司机
            if (!$recommender) {
                EdjLog::info('不存在工号为' . $driver['recommender'] . '的推荐人司机');
                continue;
            }
            //判断推荐人司机是否解约
            if ($recommender['mark'] == 3) {
                EdjLog::info('工号为' . $driver['recommender'] . '的推荐人司机已解约');
                continue;
            }
            //city_id=0 全国
            if ($config['city_id'] != 0 && !in_array($driver['city_id'], $citys)) {
                EdjLog::info('城市' . $driver['city_id'] . '不参加活动');
                continue;
            }
            $id_card = $driver['id_card'];
            $driver_recruitment = DriverRecruitment::model()->find('id_card=:id_card', array(':id_card' => $id_card));
            if (!$driver_recruitment) {
                EdjLog::info('身份证号为' . $id_card . '的司机没有报名信息');
                continue;
            }
            if ($driver_recruitment['act_type']!=1 && $driver_recruitment['act_type']!=2) {
                EdjLog::info('身份证号为' . $id_card . '的司机报名渠道不是本活动');
                continue;
            }
            $apply_time = date("Y-m-d", $driver_recruitment['apply_time']);
            if ($apply_time < $config['begin_time'] || $apply_time > $config['end_time']) {
                EdjLog::info('身份证号为' . $id_card . '的司机报名时间不在活动期间内');
                continue;
            }
            EdjLog::info('判断' . $driver['user'] . '完毕,开始将其推荐人' . $driver['recommender'] . '放入待补偿表中');
            //$message = '您推荐的'.$driver_recruitment['name'].'师傅已成功签约，'.$config['amount'].'元信息费将在3个工作日内发放。';
            $time = date('Y-m-d H:i:s', time());
            $ret = SubsidyRecord::model()->newDriverFetchInsert($driver['recommender'], $config['amount'], $driver['city_id'], 0, $time,10,$driver_recruitment['name']);
            if (!$ret) {
                EdjLog::info($driver['recommender'] . '放入待补偿表失败');
                continue;
            }
            EdjLog::info($driver['recommender'] . '放入待补偿表成功');
        }
        echo Common::jobEnd($title);
    }

    /**
     * 按城市维度统计每日报名数据
     */
    public function actionCityDataReport()
    {
        //set_time_limit(0);
        $title = 'count data';
        echo Common::jobBegin($title);
        $config = DriverPullNewConfig::model()->getConfig();
        if (!$config) {
            EdjLog::info('没有配置的活动信息');
            return;
        }
        $yestoday = date("Y-m-d", strtotime("-1 day"));
        EdjLog::info('开始统计' . $yestoday . '日数据');
        $yestoday_time_begin = strtotime(date('Y-m-d' , strtotime('-1 day')).' 00:00:00');
        $yestoday_time_end = strtotime(date('Y-m-d' , time()).' 00:00:00');
        EdjLog::info($yestoday_time_begin. '=========' . $yestoday_time_end);
        $citys = explode(',', $config['city_id']);
        foreach($citys as $city){
            //昨日参加活动报名的司机 报名人数
            $recruitment_drivers = DriverRecruitment::model()->findAll('apply_time>=:apply_time_begin and apply_time<:apply_time_end and (act_type=1 or act_type=2) and city_id=:city_id',
                                                            array(':apply_time_begin'=>$yestoday_time_begin, ':apply_time_end'=>$yestoday_time_end,':city_id'=>$city));
            if(!$recruitment_drivers){
                $recruitment_drivers_num = 0;//报名人数
                EdjLog::info('city_id='.$city.'的城市没有参加活动报名的司机');
            }else{
                $recruitment_drivers_num = count($recruitment_drivers);
            }
            $sign_drivers = DriverRecruitment::model()->findAll('entrant_time>=:entrant_time_begin and entrant_time<:entrant_time_end and (act_type=1 or act_type=2) and city_id=:city_id',
                array(':entrant_time_begin'=>$yestoday_time_begin, ':entrant_time_end'=>$yestoday_time_end,':city_id'=>$city));
            if(!$sign_drivers){
                $sign_drivers_num = 0;//昨日签约人数
                EdjLog::info('city_id='.$city.'的城市没有司机签约');
            }else{
                $sign_drivers_num = count($sign_drivers);
            }
            if(!$sign_drivers){
                $reward_driver_num = 0;//奖励实际司机数
            }else{
                $reward_driver_num = 0;
                foreach($sign_drivers as $sign_driver){
                    $recommender = $sign_driver['recommender'];
                    if(!empty($recommender)){
                        $recommender_model = Driver::model()->find('user=:user', array(':user' => $recommender));
                        //判断推荐人是不是司机
                        if (!$recommender_model) {
                            EdjLog::info('工号为' . $recommender . '的推荐人不是司机');
                            continue;
                        }
                        $reward_driver_num++;
                    }
                }
            }
            $total_amount = $reward_driver_num * $config['amount'];//奖励司机总金额
            $pullNewCityData = new PullNewCityData();
            $pullNewCityData->city_id = $city;
            $pullNewCityData->recruitment_drivers_num = $recruitment_drivers_num;
            $pullNewCityData->sign_drivers_num = $sign_drivers_num;
            $pullNewCityData->total_amount = $total_amount;
            $pullNewCityData->create_time = date('Y-m-d', time());
            $ret = $pullNewCityData->save();
            if(!$ret){
                echo 'error'.var_dump($pullNewCityData);
                echo "\n";
                echo json_encode($pullNewCityData->getErrors());
            }
        }
        echo Common::jobEnd($title);
    }

    /**
     * 按推荐人工号维度统计每日司机推荐数据
     */
    public function actionDriverDataReport()
    {
        //set_time_limit(0);
        $title = 'count data';
        echo Common::jobBegin($title);
        $config = DriverPullNewConfig::model()->getConfig();
        if (!$config) {
            EdjLog::info('没有配置的活动信息');
            return;
        }
        $yestoday = date("Y-m-d", strtotime("-1 day"));
        EdjLog::info('开始统计' . $yestoday . '日数据');
        $yestoday_time_begin = strtotime(date('Y-m-d', strtotime('-1 day')) . ' 00:00:00');
        $yestoday_time_end = strtotime(date('Y-m-d', time()) . ' 00:00:00');
        EdjLog::info($yestoday_time_begin . '=========' . $yestoday_time_end);
        $recruitment_drivers = DriverRecruitment::model()->findAll('((apply_time>=:apply_time_begin and apply_time<:apply_time_end) or (entrant_time>=:entrant_time_begin and entrant_time<:entrant_time_end)) and (act_type=1 or act_type=2)',
            array(':apply_time_begin' => $yestoday_time_begin, ':apply_time_end' => $yestoday_time_end, ':entrant_time_begin' => $yestoday_time_begin, ':entrant_time_end' => $yestoday_time_end));
        if (!$recruitment_drivers) {
            EdjLog::info($yestoday . '日没有入职或签约的司机');
            return;
        }
        $recruitment_array = array();
        foreach ($recruitment_drivers as $recruitment_driver) {
            if (!empty($recruitment_driver['recommender'])) {
                if (!in_array($recruitment_driver['recommender'], $recruitment_array)) {
                    array_push($recruitment_array, $recruitment_driver['recommender']);
                }
            }
        }
        if (empty($recruitment_array)) {
            EdjLog::info($yestoday . '日没有入职和签约的司机推荐人都是空');
            return;
        }
        foreach ($recruitment_array as $recommender) {
            $baoming_drivers = DriverRecruitment::model()->findAll('apply_time>=:apply_time_begin and apply_time<:apply_time_end and (act_type=1 or act_type=2) and recommender=:recommender',
                array(':apply_time_begin' => $yestoday_time_begin, ':apply_time_end' => $yestoday_time_end, ':recommender' => $recommender));
            if (!$baoming_drivers) {
                $baoming_num = 0;
                EdjLog::info('工号为' . $recommender . '的司机没有推荐司机报名');
            } else {
                $baoming_num = count($baoming_drivers);
            }

            $signup_drivers = DriverRecruitment::model()->findAll('entrant_time>=:entrant_time_begin and entrant_time<:entrant_time_end and (act_type=1 or act_type=2) and recommender=:recommender',
                array(':entrant_time_begin' => $yestoday_time_begin, ':entrant_time_end' => $yestoday_time_end, ':recommender' => $recommender));
            if (!$signup_drivers) {
                $signup_num = 0;
                EdjLog::info('工号为' . $recommender . '的司机没有推荐的司机签约');
            } else {
                $signup_num = count($signup_drivers);
            }
            $driver_model = Driver::model()->find('user=:user', array(':user' => $recommender));
            if (!$driver_model) {
                $total_amount = 0;
                EdjLog::info('工号为' . $recommender . '的推荐人不存在');
            } else {
                $total_amount = $signup_num * $config['amount'];
            }

            $pullNewDriverData = new PullNewDriverData();
            $pullNewDriverData->driver = $recommender;
            $pullNewDriverData->recruitment_drivers_num = $baoming_num;
            $pullNewDriverData->sign_drivers_num = $signup_num;
            $pullNewDriverData->total_amount = $total_amount;
            $pullNewDriverData->create_time = date('Y-m-d', time());
            $ret = $pullNewDriverData->save();
            if (!$ret) {
                echo 'error' . var_dump($pullNewDriverData);
                echo "\n";
                echo json_encode($pullNewDriverData->getErrors());
            }
        }
        echo Common::jobEnd($title);
    }

    /**
     * 司机拉新活动每日邮件
     */
    public function actionMail($to = 'all')
    {
        echo Common::jobBegin("发送司机拉新活动数据邮件");
        $today = date("Y-m-d", time());//今日
        $date = date("Y-m-d", strtotime("-1 day"));//昨日
        $city_datas = PullNewCityData::model()->getCityDataReport($today);//获取按城市纬度数据
        $driver_datas = PullNewDriverData::model()->getDriverDataReport($today);//获取按司机纬度数据
        $title = '司机拉新活动数据';
        $html_main = PullNewCityData::model()->dataHtml($city_datas,$driver_datas, $date);
        if ($to == 'all') {
            echo 'to all user............';
            Mail::sendMail(array('shida@edaijia-inc.cn', 'cuiluzhe@edaijia-inc.cn'), $html_main, $title);
        } else {
            echo 'only to me and qa';
            Mail::sendMail(array('cuiluzhe@edaijia-inc.cn', 'shida@edaijia-inc.cn', 'changweikai@edaijia-inc.cn'), $html_main, $title);
        }
        echo Common::jobEnd("发送司机拉新活动数据邮件");


    }

}
