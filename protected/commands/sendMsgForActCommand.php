<?php

/**
 * 立减39元活动发短信、绑定优惠劵
 */
class sendMsgForActCommand extends LoggerExtCommand
{
    /**
     *   短息追加39元活动链接,不绑定优惠劵
     */
    public function actionSendActUrl($file_path)
    {
        if(!isset($file_path)){
            EdjLog::info('请指定文件路径');
            return;
        }
        echo 'file_path='.$file_path.PHP_EOL;
        EdjLog::info('file_path='.$file_path);
        $message = '忙碌的生活让人感到疲惫不堪，诸多的应酬让你迫不得已。已经忙碌了一周就不要自己开车了，e代驾送您39元红包，支付时直接减免。立即领取http://t.cn/RZrJ4DA';
        $handle = @fopen($file_path, "r");
        if ($handle) {
            while (!feof($handle)) {
                $phone = trim(fgets($handle, 4096));
                $phone = str_replace('\r\n','',$phone);
                if (!empty($phone)) {
                    echo $phone.PHP_EOL;
                    $is_vip = CustomerMain::model()->isVip($phone);
                    if($is_vip){
                        echo 'phone=' . $phone . '的用户是vip客户不参与此次活动,请换个手机号'.PHP_EOL;
                        EdjLog::info('phone=' . $phone . '的用户是vip客户不参与此次活动,请换个手机号');
                        continue;
                    }
                    if (RActivity::model()->isAttend('weixin_39', $phone)) {//领取
                        echo 'phone=' . $phone . '的用户已经领取过,不再发送'.PHP_EOL;
                        EdjLog::info('phone=' . $phone . '的用户已经领取过,不再发送');
                        continue;
                    }
                    if (RActivity::model()->existsPhoneSendRecord('weixin_39', $phone)) {//发送过(防止job中断)
                        echo 'phone=' . $phone . '的用户已经发送过,不再发送'.PHP_EOL;
                        EdjLog::info('phone=' . $phone . '的用户已经发送过,不再发送');
                        continue;
                    }
                    $res = Sms::SendForActive($phone, $message);//国都营销短信通道
                    if ($res) {
                        echo '给phone=' . $phone . '的用户发送短信成功'.PHP_EOL;
                        EdjLog::info('给phone=' . $phone . '的用户发送短信成功');
                        RActivity::model()->phoneHasSend('weixin_39', $phone);//记录给此用户发送过
                        if(RActivity::model()->getSendNum(1) == 25000){
                            return;
                        }
                    } else {
                        echo '给phone=' . $phone . '的用户发送短信失败'.PHP_EOL;
                        EdjLog::info('给phone=' . $phone . '的用户发送短信失败');
                    }
                }
            }
            fclose($handle);
        }
    }

    /**
     * @param $file_path
     * 绑定优惠劵，并发送h5下单地址
     */
    public function actionSendH5OrderUrl($file_path)
    {
        if(!isset($file_path)){
            EdjLog::info('请指定文件路径');
            return;
        }
        $short_message = '辛辛苦苦又一年，您默默的承受着生活工作的压力，不曾给自己一个放松的机会；e代驾送您39元红包，困了，累了，醉了叫个代驾把您舒心送回家。立即使用http://t.cn/RZrJL7Z';
        $bonus_code = '9356294566';
        $handle = @fopen($file_path, "r");
        if ($handle) {
            while (!feof($handle)) {
                $phone = trim(fgets($handle, 4096));
                $phone = str_replace('\r\n','',$phone);
                echo $phone . PHP_EOL;
                if (!empty($phone)) {
                    $is_vip = CustomerMain::model()->isVip($phone);
                    if($is_vip){
                        EdjLog::info('phone=' . $phone . '的用户是vip客户不参与此次活动,请换个手机号');
                        continue;
                    }
                    if (!RActivity::model()->setLock('weixin_39',$phone)) {//领取
                        EdjLog::info('phone=' . $phone . '的用户已经领取过,不再发送');
                        continue;
                    }
                    if (RActivity::model()->existsPhoneSendRecord('weixin_39', $phone)) {//发送过(防止job中断)
                        EdjLog::info('phone=' . $phone . '的用户已经发送过,不再发送');
                        continue;
                    }
                    $left_num = RActivity::model()->getFromCache('weixin_39');
                    if ($left_num < 0) {
                        EdjLog::info('优惠劵已经被发完');
                        return;
                    }
                    //绑定优惠劵,记录日志
                    $binding_ret = FinanceWrapper::bindBonusGenerate($phone , $bonus_code , 1 , $short_message);
                    $log = new Bonus39Log();
                    $log->act_name = 'weixin_39';
                    $log->open_id = '';
                    $log->phone = $phone;
                    $log->create_time = date('Y-m-d H:i:s',time());
                    if($binding_ret && $binding_ret['code'] == 0){
                        EdjLog::info('weixin_39活动为phone='.$phone.'的用户绑定优惠劵成功');
                        $log->status=1;
                    }else{
                        EdjLog::info('weixin_39活动为phone='.$phone.'的用户绑定优惠劵失败');
                        $log->status=0;
                    }
                    $ret = $log->save();
                    RActivity::model()->setBindPhone('weixin_39', 'phone', '', $phone);
                    RActivity::model()->phoneHasSend('weixin_39', $phone);//记录给此用户发送过
                    if(RActivity::model()->getSendNum(2) == 25000){
                        return;
                    }
                }
            }
            fclose($handle);
        }
    }

    /**
     * 绑定优惠劵并且发短信
     * @param $file_path 文件路径
     * @param $act_name  活动名字
     * @param $send_num  发送数量
     * @param $bonus_sn  固定码
     * @param $data_type 数据类型 3:e代驾客户 4:对手客户
     * 绑定优惠劵，并发送h5下单地址
     */
    public function actionBindBonusAndSendMsg($file_path,$act_name,$send_num,$bonus_sn,$data_type,$short_message)
    {
        if(!isset($file_path) || !isset($act_name)  || !isset($send_num)  || !isset($bonus_sn)|| !isset($data_type) || !isset($short_message)){
            EdjLog::info('请指定正确的参数');
            return;
        }
        $handle = @fopen($file_path, "r");
        if ($handle) {
            while (!feof($handle)) {
                $phone = trim(fgets($handle, 4096));
                $phone = str_replace('\r\n','',$phone);
                echo $phone . PHP_EOL;
                if (!empty($phone)) {
                    $is_phone = Common::checkPhone($phone);
                    if( ! $is_phone ){
                        EdjLog::info('phone=' . $phone . '手机号有误，请更正');
                        continue;
                    }
                    $is_vip = CustomerMain::model()->isVip($phone);
                    if($is_vip){
                        EdjLog::info('phone=' . $phone . '的用户是vip客户不参与此次活动,请换个手机号');
                        continue;
                    }
                    if (!RActivity::model()->setLock($act_name,$phone)) {//领取
                        EdjLog::info('phone=' . $phone . '的用户已经领取过,不再发送');
                        continue;
                    }
                    if (RActivity::model()->existsPhoneSendRecord($act_name, $phone)) {//发送过(防止job中断)
                        EdjLog::info('phone=' . $phone . '的用户已经发送过,不再发送');
                        continue;
                    }
                    $left_num = RActivity::model()->getFromCache($act_name);
                    if ($left_num < 0) {
                        EdjLog::info('优惠劵已经被发完');
                        return;
                    }
                    //绑定优惠劵,记录日志
                    $binding_ret = FinanceWrapper::bindBonusGenerate($phone , $bonus_sn , 1 , $short_message);
                    $log = new Bonus39Log();
                    $log->act_name = $act_name;
                    $log->open_id = '';
                    $log->phone = $phone;
                    $log->create_time = date('Y-m-d H:i:s',time());
                    if($binding_ret && $binding_ret['code'] == 0){
                        EdjLog::info($act_name.'活动为phone='.$phone.'的用户绑定优惠劵成功');
                        $log->status=1;
                    }else{
                        EdjLog::info($act_name.'活动为phone='.$phone.'的用户绑定优惠劵失败');
                        $log->status=0;
                    }
                    $ret = $log->save();
                    RActivity::model()->setBindPhone($act_name, 'phone', '', $phone);
                    RActivity::model()->phoneHasSend($act_name, $phone);//记录给此用户发送过
                    $key = $act_name.'_'.$data_type;
                    if(RActivity::model()->getSendNum($key) == $send_num){//发送数量
                        return;
                    }
                }
            }
            fclose($handle);
        }
    }

}
