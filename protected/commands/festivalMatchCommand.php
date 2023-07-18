<?php

/**
 * 春节司机送回家活动 匹配司机和客户
 * clz
 */
class festivalMatchCommand extends LoggerExtCommand
{
    /**
     * @param int $stage
     * @param $driver_file_path 已经匹配过 想重新匹配的司机工号文件路径
     * * @param $customer_file_path 已经匹配过 想重新匹配的客户电话文件路径
     * 分阶段匹配司机和客户
     */
    public function actionMatch($stage = 1,$driver_file_path,$customer_file_path)
    {
        //set_time_limit(0);
        $title = 'match drivers and customers';
        echo Common::jobBegin($title);
        if(!isset($stage)){
            EdjLog::info('请输入活动阶段');
            return;
        }
        $gonghao_array = array();//重新匹配的司机集合
        if(isset($driver_file_path)){
            $handle = @fopen($driver_file_path, "r");
            if ($handle) {
                while (!feof($handle)) {
                    $gonghao = trim(fgets($handle, 4096));
                    $gonghao = str_replace('\r\n','',$gonghao);
                    if (!empty($gonghao)) {
                        array_push($gonghao_array, $gonghao);
                    }
                }
                fclose($handle);
            }
            if(empty($gonghao_array)){
                EdjLog::info('读取重新匹配司机工号错误');
                return;
            }
        }
        $phone_array = array();//重新匹配的客户集合
        if(isset($customer_file_path)){
            $handle = @fopen($customer_file_path, "r");
            if ($handle) {
                while (!feof($handle)) {
                    $c_phone = trim(fgets($handle, 4096));
                    $c_phone = str_replace('\r\n','',$c_phone);
                    if (!empty($c_phone)) {
                        array_push($phone_array, $c_phone);
                    }
                }
                fclose($handle);
            }
            if(empty($phone_array)){
                EdjLog::info('读取重新匹配客户手机号错误');
                return;
            }
        }
        $not_match_customers = FestivalCustomer::model()->getNotMatchedCustomers();//包括新报名客户和重新匹配的客户
        if (!$not_match_customers) {
            EdjLog::info('没有获取到未匹配的客户信息');
            if($stage == 1 || $stage == 2){
                return;
            }
        }
        if ($stage == 1) {//第一阶段 该阶段在在活动期间重复执行
            EdjLog::info('=====开始匹配第一阶段=====');
            foreach ($not_match_customers as $customer) {
                $not_match_drivers = FestivalDriver::model()->getNotMatchedDrivers();//包括新报名司机和重新匹配的司机
                if (!$not_match_drivers) {
                    EdjLog::info('没有获取到未匹配的司机信息');
                    return;
                }
                foreach ($not_match_drivers as $driver) {
                    //第一阶段,出发地目的地完全吻合
                    if ($customer['start_city'] == $driver['start_city'] && $customer['end_city'] == $driver['end_city']) {
                        //判断出发时间关系
                        if ($customer['start_time_begin'] > $driver['start_time_end'] || $customer['start_time_end'] < $driver['start_time_begin']) {
                            EdjLog::info('时间段不匹配');
                            continue;
                        }
                        $this->processMatch($customer, $driver,$stage,$gonghao_array,$phone_array);
                        break;
                    }
                }
            }
        }else if($stage == 2){//第二阶段 该阶段只能在活动报名结束后执行 执行一遍即可
            //优先匹配客户和司机的出发地目的地 不计出发时间
            EdjLog::info('=====开始匹配第二阶段=====');
            EdjLog::info('-----开始匹配第二阶段(1)-----');
            foreach ($not_match_customers as $customer) {
                $not_match_drivers = FestivalDriver::model()->getNotMatchedDrivers();
                if (!$not_match_drivers) {
                    EdjLog::info('没有获取到未匹配的司机信息');
                    return;
                }
                foreach ($not_match_drivers as $driver) {
                    if ($customer['start_city'] == $driver['start_city'] && $customer['end_city'] == $driver['end_city']) {
                        $this->processMatch($customer, $driver,$stage,$gonghao_array,$phone_array);
                        break;
                    }
                }
            }
            EdjLog::info('-----结束匹配第二阶段(1)-----');
            EdjLog::info('-----开始匹配第二阶段(2)-----');
            $not_match_customers = FestivalCustomer::model()->getNotMatchedCustomers();
            if (!$not_match_customers) {
                EdjLog::info('没有获取到未匹配的客户信息');
                return;
            }
            foreach ($not_match_customers as $customer) {
                $customer_pass_city = $customer['pass_city'];
                if(empty($customer_pass_city)){
                    EdjLog::info('客户途径城市为空');
                    continue;
                }
                $not_match_drivers = FestivalDriver::model()->getNotMatchedDrivers();
                if (!$not_match_drivers) {
                    EdjLog::info('没有获取到未匹配的司机信息');
                    break;
                }
                foreach ($not_match_drivers as $driver) {
                    //第二阶段,出发地完全吻合
                    if ($customer['start_city'] == $driver['start_city']) {
                        //判断出发时间关系
                        if ($customer['start_time_begin'] > $driver['start_time_end'] || $customer['start_time_end'] < $driver['start_time_begin']) {
                            EdjLog::info('时间段不匹配');
                            continue;
                        }
                        $customer_pass_city_array = explode(',', $customer_pass_city);
                        $driver_end_city = $driver['end_city'];
                        if(in_array($driver_end_city,$customer_pass_city_array)){//客户途经城市和司机目的地相符,匹配成功
                            $this->processMatch($customer, $driver,$stage,$gonghao_array,$phone_array);
                            break;
                        }

                    }
                }
            }
            EdjLog::info('-----结束匹配第二阶段(2)-----');
            /*//2、匹配客户目的城市和司机途经城市 产品去掉此逻辑
            $not_match_customers = FestivalCustomer::model()->getNotMatchedCustomers();
            if (!$not_match_customers) {
                EdjLog::info('没有获取到未匹配的客户信息');
                return;
            }
            foreach ($not_match_customers as $customer) {
                $not_match_drivers = FestivalDriver::model()->getNotMatchedDrivers();
                if (!$not_match_drivers) {
                    EdjLog::info('没有获取到未匹配的司机信息');
                    return;
                }
                foreach ($not_match_drivers as $driver) {
                    //第二阶段,出发地完全吻合
                    if ($customer['start_city'] == $driver['start_city']) {
                        //判断出发时间关系
                        if ($customer['start_time_begin'] > $driver['start_time_end'] || $customer['start_time_end'] < $driver['start_time_begin']) {
                            EdjLog::info('时间段不匹配');
                            continue;
                        }
                        $driver_pass_city = $driver['pass_city'];
                        if(empty($driver_pass_city)){
                            EdjLog::info('司机途径城市为空');
                            continue;
                        }
                        $driver_pass_city_array = explode(',', $driver_pass_city);
                        $customer_end_city = $customer['end_city'];
                        if(in_array($customer_end_city,$driver_pass_city_array)){//客户目的地和司机途径城市相符,匹配成功
                            $this->processMatch($customer, $driver);
                        }

                    }
                }
            }*/
            //3、匹配客户和司机的高速公路  产品说不匹配高速路了 改为匹配省内城市
            /*$not_match_customers = FestivalCustomer::model()->getNotMatchedCustomers();
            if (!$not_match_customers) {
                EdjLog::info('没有获取到未匹配的客户信息');
                return;
            }
            foreach ($not_match_customers as $customer) {
                if (empty($customer['road'])){
                    continue;
                }
                $not_match_drivers = FestivalDriver::model()->getNotMatchedDrivers();
                if (!$not_match_drivers) {
                    EdjLog::info('没有获取到未匹配的司机信息');
                    return;
                }
                foreach ($not_match_drivers as $driver) {
                    if (empty($driver['road'])){
                        continue;
                    }
                    //第二阶段,高速公路相同
                    if ($customer['road'] == $driver['road']) {
                        //判断出发时间关系
                        if ($customer['start_time_begin'] > $driver['start_time_end'] || $customer['start_time_end'] < $driver['start_time_begin']) {
                            EdjLog::info('时间段不匹配');
                            continue;
                        }
                        $this->processMatch($customer, $driver);
                    }
                }
            }*/
            //匹配出发地相同,目的地是一个省的
            EdjLog::info('-----开始匹配第二阶段(3)-----');
            $not_match_customers = FestivalCustomer::model()->getNotMatchedCustomers();
            if (!$not_match_customers) {
                EdjLog::info('没有获取到未匹配的客户信息');
                return;
            }
            foreach ($not_match_customers as $customer) {
                $not_match_drivers = FestivalDriver::model()->getNotMatchedDrivers();
                if (!$not_match_drivers) {
                    EdjLog::info('没有获取到未匹配的司机信息');
                    break;
                }
                foreach ($not_match_drivers as $driver) {
                    //判断出发城市
                    if ($customer['start_city'] != $driver['start_city']){
                        EdjLog::info('出发城市不一致');
                        continue;
                    }
                    //判断时间
                    if ($customer['start_time_begin'] > $driver['start_time_end'] || $customer['start_time_end'] < $driver['start_time_begin']) {
                        EdjLog::info('时间段不匹配');
                        continue;
                    }
                    //判断目的地是否在一个省
                    if (FestivalCustomer::model()->isSameProvince($customer['end_city'], $driver['end_city'])) {
                        $this->processMatch($customer, $driver,$stage,$gonghao_array,$phone_array);
                        break;
                    }
                }
            }
            EdjLog::info('-----结束匹配第二阶段(3)-----');
        }else if($stage ==3){//第三阶段，给未匹配上的客户绑定优惠劵,司机补钱
            //第二轮第三阶段新  不用处理上一轮未匹配到的司机和客户 因为已经在上一轮给过补偿了
            $not_match_customers = FestivalCustomer::model()->getToCompensateCustomers();
            if (!$not_match_customers) {
                EdjLog::info('没有获取到待绑定优惠劵的客户信息');
            }else{
                //绑定优惠劵,补偿用户
                $short_message = '感谢参与”春节e代驾送你回家”活动，我们抱歉的通知您目前没有匹配到与您回家路途相符的司机，已为您账户补贴3张99元优惠券，请注意查收。';
                $bonus_sn = '93896018571';
                foreach($not_match_customers as $customer){
                    if(!empty($phone_array) && in_array($customer['phone'],$phone_array)){
                        EdjLog::info('phone='.$customer['phone'].'的客户是重新匹配的客户,不绑定优惠劵');
                        continue;
                    }
                    $binding_ret = FinanceWrapper::bindBonusGenerate($customer['phone'] , $bonus_sn , 3 , $short_message);
                    if($binding_ret && $binding_ret['code'] == 0){
                        EdjLog::info('为phone='.$customer['phone'].'用户绑定优惠劵成功');
                        $compensate_time =  date("Y-m-d H:i:s", time());
                        $update_ret = FestivalCustomer::model()->updateAll(array('compensate_status'=> 1, 'compensate_time'=> $compensate_time), 'phone=:phone',array(':phone'=>$customer['phone']));
                        if($update_ret>0){
                            EdjLog::info('更新phone='.$customer['phone'].'用户补偿状态成功');
                        }else{
                            EdjLog::info('更新phone='.$customer['phone'].'用户补偿状态失败');
                        }
                    }else{
                        EdjLog::info('为phone='.$customer['phone'].'用户绑定优惠劵失败');
                    }
                }
            }

            //给司机e币  这个最后执行  产品说不奖励e币了 补偿信息费
           /* $not_match_drivers = FestivalDriver::model()->getToCompensateDrivers();
            if (!$not_match_customers) {
                EdjLog::info('没有获取待补偿e币的司机信息');
                return;
            }
            foreach ($not_match_drivers as $driver) {
                $driver_model = Driver::model()->find('user=:user', array(':user'=>$driver['driver']));
                if(!$driver_model){
                    EdjLog::info('工号为'.$driver['driver'].'的司机不存在');
                    return;
                }
                $ret = DriverExt::model()->addWealth($driver['driver'],DriverWealthLog::WEEK_WEALTH);
                if($ret <= 0){
                    EdjLog::info('为工号为'.$driver['driver'].'的司机补偿e币失败');
                    continue;
                }
                $ret = DriverWealthLog::model()->addLog($driver['driver'],DriverWealthLog::WEEK_TYPE,DriverWealthLog::WEEK_WEALTH,$driver_model['city_id']);
                if(!$ret){
                    EdjLog::info('为工号为'.$driver['driver'].'的司机记录补偿e币日志失败');
                    continue;
                }
            }*/
            $not_match_drivers = FestivalDriver::model()->getToCompensateDrivers();
            if (!$not_match_drivers) {
                EdjLog::info('没有获取待补偿信息费的司机信息');
            }else{
                $mess = '感谢参与”春节e代驾送你回家”活动，我们抱歉的通知您目前没有匹配到与您回家路途相符的客户，我们会为您提供188元回家补贴，信息费将在本日发放到您账户，请注意查收。';
                foreach ($not_match_drivers as $driver){
                    if(!empty($gonghao_array) && in_array($driver['driver'],$gonghao_array)){
                        EdjLog::info('工号为'.$driver['driver'].'的司机是重新匹配的司机,不再补贴');
                        continue;
                    }
                    $driver_model = Driver::model()->find('user=:user', array(':user'=>$driver['driver']));
                    if(!$driver_model){
                        EdjLog::info('工号为'.$driver['driver'].'的司机不存在');
                        continue;
                    }
                    $compensate_time =  date("Y-m-d H:i:s", time());
                    $update_ret = FestivalDriver::model()->updateAll(array('compensate_status'=> 1, 'compensate_time'=> $compensate_time), 'driver=:driver',array(':driver'=>$driver['driver']));
                    if($update_ret>0){
                        EdjLog::info('更新工号为'.$driver['driver'].'的司机补偿状态成功');
                    }else{
                        EdjLog::info('更新工号为'.$driver['driver'].'的司机补偿状态失败');
                    }
                    //SubsidyRecord::model()->newDriverFetchInsert($driverId,$cast,$city_id,$order_id,$created,$subsidy_type='',$meta='')；
                    $ret = SubsidyRecord::model()->newDriverFetchInsert($driver['driver'],188, $driver_model['city_id'], 0, $compensate_time,11,$mess);
                    if (!$ret) {
                        EdjLog::info($driver['driver'] . '放入待补偿表失败');
                        continue;
                    }
                    EdjLog::info($driver['driver'] . '放入待补偿表成功');
                }
            }
        }
        echo Common::jobEnd($title);
    }

    /**
     * 处理客户和司机的匹配关系
     * @param $customer
     * @param $driver
     */
    private function processMatch($customer, $driver,$stage,$gonghao_array,$phone_array){
        $today = date('Y-m-d',time());
        if($customer['start_time_end']<=$today){
            EdjLog::info('phone='.$customer['phone'].'客户已过期');
            return;
        }
        if($driver['start_time_end']<=$today){
            EdjLog::info('工号为'.$driver['driver'].'司机已过期');
            return;
        }
        $driver_model = Driver::model()->find('user=:user', array(':user'=>$driver['driver']));
        if(!$driver_model){
            EdjLog::info('工号为'.$driver['driver'].'的司机不存在');
            return;
        }
        //更新客户和司机匹配关系
        $time = date("Y-m-d H:i:s", time());
        $customer_ret = FestivalCustomer::model()->updateByPk($customer['id'],array('match_driver' => $driver['driver'], 'match_time' => $time));
        if (!$customer_ret) {
            EdjLog::info('更新id为' . $customer['id'] . '的用户匹配信息时失败,匹配到的司机工号是' . $driver['driver']);
        }
        $driver_ret = FestivalDriver::model()->updateByPk($driver['id'],   array('match_customer_phone' => $customer['phone'], 'match_time' => $time));
        if (!$driver_ret) {
            EdjLog::info('更新id为' . $driver['id'] . '的司机匹配信息时失败,匹配到的客户手机号是' . $customer['phone']);
        }
        //发短信告知司机和客户
        if($stage == 1){
            //$customer_message = '尊敬的客户您好，为您匹配到司机(姓名:'.$driver_model['name'].',工号:'.$driver_model['user'].',电话:'.$driver_model['phone'].')，师傅稍后会联系您，感谢您的参加';
            //$customer_message = '感谢报名参加“春节e代驾送你回家”活动，我们已为您匹配好司机'.$driver_model['user'].'，联系电话：'.$driver_model['phone'].'，请尽快联系，e代驾将为您支付相关费用，祝一路顺风~';
            $customer_message = '【e代驾春节返乡第二次补充匹配】我们已为您匹配好司机'.$driver_model['user'].'，联系电话：'.$driver_model['phone'].'，请尽快联络，如果不需要也烦请通知司机，e代驾已为您支付代驾费，祝一路顺风~';
        }else{
            //$customer_message = '感谢报名参加“春节e代驾送你回家”活动，我们已为您匹配好司机'.$driver_model['user'].'，联系电话：'.$driver_model['phone'].'，此司机可能与您行程不完全一致，请尽快联系协商，e代驾将为您支付相关费用，祝一路顺风~';
            $customer_message = '【e代驾春节返乡第二次补充匹配】我们已为您匹配好司机'.$driver_model['user'].'，联系电话：'.$driver_model['phone'].'，此司机可能与您行程不完全一致，请尽快联络，如果不需要也烦请通知司机，e代驾已为您支付代驾费，祝一路顺风~';
        }
        EdjLog::info($customer_message);
        $ret_sms_customer = Sms::SendSMS( $customer['phone'], $customer_message, Sms::CHANNEL_ZCYZ);
        if (empty($ret_sms_customer)) {
            EdjLog::info('给手机号为'.$customer['phone'].'的客户发送匹配通知短信失败');
        }
        $start_date = $customer['start_time_begin']> $driver['start_time_begin'] ? $driver['start_time_begin'] : $customer['start_time_begin'];
        //$driver_message = '师傅您好，给您匹配到用户先生 '.$customer['phone'].','.$start_date.'从'.$customer['start_city'].'到'.$customer['end_city'].'，请及时联系客户';
        if($stage == 1){
            //$driver_message = '感谢报名参加“春节e代驾送你回家”活动，我们已为您匹配好客户，联系电话：'.$customer['phone'].'，请尽快联系，信息费99元将在两日内补贴至您账户，请勿再向客户收费，祝一路顺风~';
            $driver_message = '【e代驾春节返乡第二次补充匹配】我们已为您匹配好客户，联系电话：'.$customer['phone'].'，请尽快联络，如果不需要也烦请通知客户，代驾费99元将在本日补贴至您账户(每位司机只补贴一次)，请勿向客户收费，祝一路顺风~';
        }else{
           // $driver_message = '感谢报名参加“春节e代驾送你回家”活动，我们已为您匹配好客户，联系电话：'.$customer['phone'].'，此客户可能与您行程不完全一致，请尽快联系协商，信息费99元将在两日内补贴至您账户，请勿再向客户收费，祝一路顺风~';
            $driver_message = '【e代驾春节返乡第二次补充匹配】我们已为您匹配好客户，联系电话：'.$customer['phone'].'，此客户可能与您行程不完全一致，请尽快联络，如果不需要也烦请通知客户，代驾费99元将在本日补贴至您账户(每位司机只补贴一次)，请勿向客户收费，祝一路顺风~';
        }
         EdjLog::info($driver_message);
         $ret_sms_driver = Sms::SendSMS($driver_model['phone'], $driver_message, Sms::CHANNEL_ZCYZ);
         if (empty($ret_sms_driver)) {
             EdjLog::info('给工号为'.$driver_model['user'].'的司机发送匹配通知短信失败');
         }
        //产品修改为一旦匹配上 不管客户加油次数 立即给司机信息费
        $driver_model = Driver::model()->find('user=:user', array(':user'=>$driver['driver']));
        if(!$driver_model){
            EdjLog::info('工号为'.$driver['driver'].'的司机不存在');
            return;
        }
        if(!empty($gonghao_array) && in_array($driver['driver'],$gonghao_array)){
            EdjLog::info('工号为'.$driver['driver'].'的司机是重新匹配的司机,已经给过补贴');
            return;
        }
        if($driver['compensate_status'] == 1){
            EdjLog::info('工号为'.$driver['driver'].'的司机是一期未匹配上的司机,已经给过补偿');
            return;
        }
        $driver_message = $driver['driver'].'，您参与e代驾春节返乡活动的代驾费补贴99元已到账，请查收';
        $ret = SubsidyRecord::model()->newDriverFetchInsert($driver['driver'],99, $driver_model['city_id'], 0, $time,11,$driver_message);
        if (!$ret) {
            EdjLog::info($driver['driver'] . '放入待补偿表失败');
        }
        EdjLog::info($driver['driver'] . '放入待补偿表成功');
    }


    /**
     * 给客户发送匹配信息短信 在第三阶段补偿执行之前进行
     */
    public function actionSendMsgToCustomer(){
        $matched_customers = FestivalCustomer::model()->getMatchedCustomers();
        if (!$matched_customers) {
            EdjLog::info('没有获取到匹配的客户信息');
            return;
        }
        foreach ($matched_customers as $customer) {
            $driver_num = $customer['match_driver'];
            if(empty($driver_num)){
                EdjLog::info('该客户没有匹配到司机');
                continue;
            }
            $driver_model = Driver::model()->find('user=:user', array(':user'=>$driver_num));
            if(!$driver_model){
                EdjLog::info('工号为'.$driver_num.'的司机不存在');
                continue;
            }
            //发短信告知客户
            $customer_message = '尊敬的客户您好，为您匹配到司机(姓名:'.$driver_model['name'].',工号:'.$driver_model['user'].',电话:'.$driver_model['phone'].')，师傅稍后会联系您，感谢您的参加';
            EdjLog::info($customer_message);
            /*$ret_sms_customer = Sms::SendSMS( $customer['phone'], $customer_message, Sms::CHANNEL_ZCYZ);
            if (empty($ret_sms_customer)) {
                EdjLog::info('给手机号为'.$customer['phone'].'的客户发送匹配通知短信失败');
            }*/
        }
    }
    /**
     * 给司机发送匹配信息短信 在第三阶段补偿执行之前进行
     */
    public function actionSendMsgToDriver(){
        $matched_drivers = FestivalDriver::model()->getMatchedDrivers();
        if (!$matched_drivers) {
            EdjLog::info('没有获取到未匹配的司机信息');
            return;
        }
        foreach ($matched_drivers as $driver) {
            if(empty($driver['match_customer_phone'])){
                EdjLog::info('该司机没有匹配到客户');
                continue;
            }
            $phone = $driver['match_customer_phone'];
            $customer = FestivalCustomer::model()->find('phone=:phone', array(':phone'=>$phone));
            if(!$customer){
                EdjLog::info('手机号为'.$phone.'客户不存在');
                continue;
            }
            $start_date = $customer['start_time_begin']> $driver['start_time_begin'] ? $driver['start_time_begin'] : $customer['start_time_begin'];
            $driver_message = '师傅您好，给您匹配到用户先生 '.$customer['phone'].','.$start_date.'从'.$customer['start_city'].'到'.$customer['end_city'].'，请及时联系客户';
            EdjLog::info($driver_message);
           /* $ret_sms_driver = Sms::SendSMS($driver['phone'], $driver_message, Sms::CHANNEL_ZCYZ);
            if (empty($ret_sms_driver)) {
                 EdjLog::info('给工号为'.$driver['user'].'的司机发送匹配通知短信失败');
            }*/
        }
    }

    /**
     * @param $file_path
     * 给线下客户绑定优惠劵
     */
    public function actionProcessCustomerData($file_path)
    {
        if(!isset($file_path)){
            EdjLog::info('请指定文件路径');
            return;
        }
        $short_message = '感谢参与”春节e代驾送你回家”活动，很抱歉您未能与到司机一同返乡，已为您账户补贴3张99元优惠券(限app使用)';
        $bonus_sn = '93896018571';
        $handle = @fopen($file_path, "r");
        if ($handle) {
            while (!feof($handle)) {
                $phone = trim(fgets($handle, 4096));
                $phone = str_replace('\r\n','',$phone);
                echo $phone . PHP_EOL;
                if (!empty($phone)) {
                    $is_phone = Common::checkPhone($phone);
                    if( ! $is_phone ){
                        EdjLog::info('phone=' . $phone . '的手机号格式不正确');
                        continue;
                    }
                    $is_vip = CustomerMain::model()->isVip($phone);
                    if($is_vip){
                        EdjLog::info('phone=' . $phone . '的用户是vip客户不能绑定优惠劵');
                        continue;
                    }
                    $customer = FestivalCustomer::model()->find('phone=":phone',array(':phone'=>$phone));
                    if(!$customer){
                        EdjLog::info('phone=' . $phone . '的客户未报名');
                        continue;
                    }
                    if($customer['compensate_status'] == 1){
                        EdjLog::info('phone=' . $phone . '的客户已经补偿过');
                        continue;
                    }
                    $binding_ret = FinanceWrapper::bindBonusGenerate($phone , $bonus_sn , 3 , $short_message);
                    if($binding_ret && $binding_ret['code'] == 0){
                        EdjLog::info('为phone='.$phone.'用户绑定优惠劵成功');
                        $compensate_time =  date("Y-m-d H:i:s", time());
                        $update_ret = FestivalCustomer::model()->updateAll(array('compensate_status'=> 1, 'compensate_time'=> $compensate_time), 'phone=:phone',array(':phone'=>$phone));
                        if($update_ret>0){
                            EdjLog::info('更新phone='.$phone.'用户补偿状态成功');
                        }else{
                            EdjLog::info('更新phone='.$phone.'用户补偿状态失败');
                        }
                    }else{
                        EdjLog::info('为phone='.$phone.'用户绑定优惠劵失败');
                    }
                }
            }
            fclose($handle);
        }
    }

    /**
     * @param $file_path
     * 给线下司机补贴
     */
    public function actionProcessDriverData($file_path)
    {
        if(!isset($file_path)){
            EdjLog::info('请指定文件路径');
            return;
        }
        $short_message = '感谢参与”春节e代驾送你回家”活动，很抱歉您未能与客户一同返乡，我们在之前99元代驾费补贴的基础上再为您提供89元回家补贴，已经发放到您账户，请查收';
        $handle = @fopen($file_path, "r");
        if ($handle) {
            while (!feof($handle)) {
                $driver_number = trim(fgets($handle, 4096));
                $driver_number = str_replace('\r\n','',$driver_number);
                echo $driver_number . PHP_EOL;
                if (!empty($driver_number)) {
                    $driver_model = Driver::model()->find('user=:user',array(':user'=>$driver_number));
                    if(!$driver_model){
                        EdjLog::info('工号为' . $driver_number . '的司机不存在');
                        continue;
                    }
                    $festival_driver = FestivalDriver::model()->find('driver=":driver',array(':driver'=>$driver_number));
                    if(!$festival_driver){
                        EdjLog::info('工号为' . $driver_number . '的司机未报名');
                        continue;
                    }
                    if($festival_driver['compensate_status'] == 1){
                        EdjLog::info('工号为' . $driver_number . '的司机已经补偿过');
                        continue;
                    }
                    $compensate_time =  date("Y-m-d H:i:s", time());
                    $update_ret = FestivalDriver::model()->updateAll(array('compensate_status'=> 1, 'compensate_time'=> $compensate_time), 'driver=:driver',array(':driver'=>$driver_number));
                    if($update_ret>0){
                        EdjLog::info('更新工号为'.$driver_number.'的司机补偿状态成功');
                    }else{
                        EdjLog::info('更新工号为'.$driver_number.'的司机补偿状态失败');
                    }
                    $ret = SubsidyRecord::model()->newDriverFetchInsert($driver_number,89, $driver_model['city_id'], 0, $compensate_time,11,$short_message);
                    if (!$ret) {
                        EdjLog::info($driver_number . '放入待补偿表失败');
                        continue;
                    }
                    EdjLog::info($driver_number . '放入待补偿表成功');
                }
            }
            fclose($handle);
        }
    }

}
