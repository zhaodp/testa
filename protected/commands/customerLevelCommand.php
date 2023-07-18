 <?php
class customerLevelCommand extends LoggerExtCommand
{
    /**
     * @param $cycle_times  循环次数
     * @param $number_per_time  每次发送短信个数
     */
    public function actionPushAndMsg($cycle_times, $number_per_time)
    {
        //set_time_limit(0);
        $title = 'send feedback message to customer';
        echo Common::jobBegin($title);
        $max = RCustomerFeedback::model()->getMaxId();
	    echo 'max_id = '.$max.PHP_EOL;
	    $i = 0;
        while ($i < $cycle_times) {
            $customer_list = CustomerLevel::model()->getCustomerListByMaxId($max, $number_per_time);
            if($customer_list){
                foreach ($customer_list as $customer) {
                    $phone = $customer->phone;
                    $encode_phone = base64_encode($phone);//将手机号base64位加密
                    $level = $customer->level;
                    if($level == 1){
                        $push_msg = '亲，奉上99元代驾费，快来领取吧。';
                        $url = 'http://eevent.b0.upaiyun.com/coupon/?'.$encode_phone;
                        $short_msg = '快过年了，咱不玩虚的，直接送您99元代驾费。感谢您使用e代驾，祝您新年发大财。打开e代驾手机软件即可领取。http://t.cn/RzejUrp';
                    }else if($level == 2){
                        $push_msg = '亲，奉上99元代驾费，快来领取吧。';
                        $url = 'http://eevent.b0.upaiyun.com/coupon/?'.$encode_phone;
                        $short_msg = '快过年了，咱不玩虚的，直接送您99元代驾费。感谢您使用e代驾，祝您新年发大财。打开e代驾手机软件即可领取。http://t.cn/RzejUrp';
                    }else if($level == 3){
                        $push_msg = '亲，奉上79元代驾费，快来领取吧。';
                        $url = 'http://eevent.b0.upaiyun.com/coupon/?'.$encode_phone;
                        $short_msg = '快过年了，咱不玩虚的，直接送您79元代驾费。感谢您使用e代驾，祝您新年发大财。打开e代驾手机软件即可领取。http://t.cn/RzejUrp';
                    }else{
                        $push_msg = '亲，奉上50元代驾费，快来领取吧。';
                        $url = 'http://eevent.b0.upaiyun.com/coupon/?'.$encode_phone;
                        $short_msg = '快过年了，咱不玩虚的，直接送您50元代驾费。感谢您使用e代驾，祝您新年发大财。打开e代驾手机软件即可领取。http://t.cn/RzejUrp';
                    }
                    $app_ver = $customer['app_ver'];
                    echo 'app_ver='.$app_ver.PHP_EOL;
                    //发送短信
                   // $res = Sms::SendForOrderComment($phone, $short_msg, 123);
                    $res = Sms::SendForActive($phone, $short_msg);//国都营销短信通道
                    echo 'phone.='.$phone.' send ok'.PHP_EOL;

                    $client = CustomerClient::model()->getByPhoneAndLast($phone);
                    if(!empty($client)){//判断是否安装客户端
                        echo $phone.' has client'.PHP_EOL;
                        //$app_min_ver = '5.1.0';
                        //if(isset($app_ver) && $app_ver>$app_min_ver){
                        echo $phone.' may push'.PHP_EOL;
                        ClientPush::model()->pushMsgForCustomerFeedback($client, $phone, $push_msg, $url,$app_ver);//推送消息
                        echo 'phone='.$phone.' push ok'.PHP_EOL;
                        //}
                    }
                     $max = $customer->ID;
                     RCustomerFeedback::model()->setMaxId($max);//放入缓存作为下次的起点
	            }
	        }
	        $i++;
        }
        echo Common::jobEnd($title);
    }
    /**
      *三周年奖励A2，A3级充值客户
      *$begin_time 活动开始时间加一天
      *$end_time 活动结束时间加一天
    **/
    public function actionFeedback($begin_time ,$end_time)
    {
	    $title = 'give amount to customer';
        echo Common::jobBegin($title);
        echo $begin_time.'_'.$end_time.PHP_EOL;
        $now = date('Y-m-d H:i:s', time());
        echo 'now:'.$now.PHP_EOL;
        if($now<$begin_time || $now>$end_time){//只在活动期间运行脚本
            echo '只在活动期间运行脚本'.PHP_EOL;
            return;
        }
        $yestoday_begin = date('Y-m-d 00:00:00', time()-86400);
        $yestoday_end = date('Y-m-d 23:59:59', time()-86400);
        echo 'yestoday_begin:'.$yestoday_begin.'到yestoday_end:'.$yestoday_end.PHP_EOL;
        $trans = CustomerTrans::model()->getRechargeList($yestoday_begin, $yestoday_end);//获取昨日充值记录(银联 支付宝 pp钱包)
        if(!$trans || empty($trans)){
            echo '昨日充值记录条数为0'.PHP_EOL;
            EdjLog::info('昨日充值记录条数为0');
            return;
        }
        foreach($trans as $tran){
            $user_id = $tran['user_id'];
            $user_id_param = array('id'=>$user_id);
            $customer_info = BCustomers::model()->getCustomerInfo($user_id_param);
            if(!$customer_info || $customer_info['code'] == 1){
                echo 'id为'.$user_id.'的用户不存在'.PHP_EOL;
                EdjLog::info('id为'.$user_id.'的用户不存在');
                continue;
            }
            $customer = $customer_info['data'];//用户信息
            $phone = $customer['phone'];
            $customerLevel = CustomerLevel::model()->find('phone=:phone', array(':phone'=>$phone));
            if(empty($customerLevel)){
                echo 'id为'.$user_id.'的用户不存在'.PHP_EOL;
                EdjLog::info('id为'.$user_id.'的用户不存在');
                continue;
            }
            if($customerLevel->level ==1){
                echo 'id为'.$user_id.'的用户级别为A1'.PHP_EOL;
                EdjLog::info('id为'.$user_id.'的用户级别为A1');
                continue;
            }
            if($customerLevel->given == 1){
                echo 'id为'.$user_id.'的用户已经赠送过'.PHP_EOL;
                EdjLog::info('id为'.$user_id.'的用户已经赠送过');
                continue;
            }
            $level = $customerLevel->level;
            if($level==2){
                $amount = 99;
            }else if($level == 3){
                $amount = 79;
            }else{
                $amount = 50;
            }
            $user_account = $customer_info['user_account'];//用户账户
            $param = array(
                'user_id'=>$customer['id'],
                'city_id'=>$customer['city_id'],
                'type'   =>$customer['type'],
                'amount' =>$amount,
                'vip_card'=>$customer['vip_card'],
            );
            $ret = BCustomers::model()->addAccount($param);
            if($ret['code'] == 1){
                $msg = '三周年活动为'.$phone.'充值失败'.PHP_EOL;
                echo $msg;
                EdjLog::info($msg);
                Mail::sendMail(array('cuiluzhe@edaijia-inc.cn'), $msg, '三周年活动充值失败');
                continue;
            }
            //充值成功记录交易流水
            $balance = $user_account['amount'] + $amount;
            $trans_param = array(
                'user_id'=>$customer['id'],
                'trans_order_id'=>0,
                'trans_card'=>0,
                'trans_type'=>CarCustomerTrans::TRANS_TYPE_RE,//充值奖励
                'amount'=>$amount,
                'balance'=>$balance,
                'source'=>CarCustomerTrans::TRANS_SOURCE_RE,//系统奖励
                'operator'=>'系统',
                'create_time'=>date("Y-m-d H:i:s", time()),
                'remark'=>'三周年活动奖励',
            );
           $ret = BCustomers::model()->addCustomerTrade($trans_param);
           if($ret['code'] == 1){
                $msg = '三周年活动为'.$phone.'添加用户交易流水失败';
               echo $msg.PHP_EOL;
                EdjLog::info($msg);
                Mail::sendMail(array('cuiluzhe@edaijia-inc.cn'), $msg, '三周年活动记录交易流水失败');
           }
           $customerLevel->given = 1;
           $customerLevel->given_time = time();
           $ret=$customerLevel->save();
           if(!$ret){
               $msg = '赠送'.$phone.'用户金额成功但更新赠送状态失败';
               echo $msg.PHP_EOL;
               EdjLog::info($msg);
               Mail::sendMail(array('cuiluzhe@edaijia-inc.cn'), $msg, '三周年活动记录交易流水失败');
           }else{
                $push_msg = 'e代驾赠送的'.$amount.'已经到达您的账户,请注意查收';
                $client = CustomerClient::model()->getByPhoneAndLast($phone);
                $app_ver = $customer['app_ver'];
                if(!empty($client)){
                     //if($app_ver>='5.1.0'){
                          $help_url = 'http://eevent.b0.upaiyun.com/coupon/help.html';
                          ClientPush::model()->pushMsgForCustomerFeedback($client, $phone, $push_msg, $help_url,$app_ver);//推送消息
                          echo 'phone='.$phone.' push ok'.PHP_EOL;
                    //}
                }
           }
        }
        echo Common::jobEnd($title);
     }
}
