<?php
/**
 * 发送VIP账单
 * @author baiyang.li
 */
//邮件引用
Yii::import('application.models.demo.*');
class sendMailCommand extends LoggerExtCommand 
{

    /**
     * 发送VIP上月帐单
     * @author libaiyang
     * @editor sunhongjing ,2013-04-02 修改函数名称为actionSendVipMonthlyBills
     * @editor mengtianxue 2013-05-31 重构actionSendVipMonthlyBills 方法
     * php yiic.php sendMail SendVipMonthlyBills --month=2013-10-01
     */
    public function actionSendVipMonthlyBills($month = 0)
    {

        echo Common::jobBegin("发送VIP上月帐单");

        if ($month == 0) {
            $month = date('Y-m-01', strtotime("-1 month"));
        }
        $vipList = $this->getAllVip();
//	$vipList=array(array('id'=>830791));
        if ($vipList) {
            foreach ($vipList as $list) {
                $vip_card = $list['id'];
                $this->sendMonthVipBill($vip_card, 1, $month);
		sleep(10);
            }
        } else {
            echo '未找到需要发送的VIP用户\n';
        }
        echo Common::jobEnd("发送VIP上月帐单");
    }

    /**
     * 发送VIP昨天的订单
     * @param int $day
     * @auther mengtianxue
     * php yiic.php sendMail SendVipDayBills --day=2013-10-01
     */
    public function actionSendVipDayBills($day = 0)
    {
        echo Common::jobBegin("发送VIP昨天的订单");
        $vipList = $this->getAllVip();
        if ($vipList) {
            foreach ($vipList as $list) {
                $vip_card = $list['id'];
                $this->sendMonthVipBill($vip_card, 0, $day);
		sleep(10);
            }
        } else {
            echo '未找到需要发送的VIP用户\n';
        }
        echo Common::jobEnd("发送VIP昨天的订单");
    }


    /**
     * 定时发送邮件  每十分钟执行一次
     * author mengtianxue
     * php yiic.php sendMail SendEmailTime
     */
    public function actionSendEmailTime()
    {
        echo Common::jobBegin("定时发送邮件  每十分钟执行一次");
        $email_log = VipEmailLog::model()->getSendList();
        if ($email_log) {
            foreach ($email_log as $email) {
                $vip_card = $email['vipcard'];
                $type = $email['type'];
                $is_repeat = 1;
                $this->sendMonthVipBill($vip_card, $type, $email['vip_bill_time'], $is_repeat);
                VipEmailLog::model()->updateLog($email['id']);
            }
        }
        echo Common::jobEnd("定时发送邮件  每十分钟执行一次");
    }

    /**
     * 发送vip月账单
     * @param $date_time
     * @param $type
     * @param $vip_card
     * @param $is_send
     * @return bool
     * author mengtianxue
     */
    public function sendMonthVipBill($vip_card, $type = 0, $date_time = 0, $is_repeat = 0)
    {
        //获取vip消费历史参数
        $data = array();
        if ($date_time == 0) {
            $yesterday = strtotime("-1 day");
            $month_start = strtotime(date('Y-m-d 15:00:00', $yesterday));
        } else {
            $date_time = $date_time . ' 15:00:00';
            $month_start = strtotime($date_time);
        }

        if ($type == 0) {
            $month_end = $month_start + 86400;

            // 记录上个月月份
            $title_month = date("m月d日", $month_start);
        } else {
            $t = date('t', $month_start); //获取当月天数
            $month_end = $month_start + $t * 86400;

            // 记录上个月月份
            $title_month = date("m月", $month_start);
        }

        $data['start_time'] = $month_start;
        $data['end_time'] = $month_end;
        $data['vipcard'] = $vip_card;
        $list = Vip::model()->getPrimary($vip_card);
        if ($list) {

            $model = new VipTrade();

            // 获取所有附属卡
            $vipPhone = VipPhone::model()->getVipCardPhone($vip_card);
            //获取消费记录
            $dataProvider = $model->VipTradePrintList($data, 1);
            $dataProviderList = $dataProvider->getData();
            $vip_consume_html = '';
            if (!empty($dataProviderList)) {
                $vip_consume_html = Vip::model()->vipConsumeHtml($dataProviderList, $list['city_id']);
            }

            //获取充值明细
            $dataProviderRecharge = $model->VipTradePrintList($data, 0);
            $vip_recharge_list = $dataProviderRecharge->getData();
            $vip_recharge_html = '';
            if (!empty($vip_recharge_list)) {
                $vip_recharge_html = Vip::model()->vipRechargeHtml($vip_recharge_list);
            }

            $html_main = '';
            if (!empty($vip_consume_html) || !empty($vip_recharge_html)) {
                $html_main = Vip::model()->getMailBody($vipPhone, $list, $title_month);

                //把消费记录 和 充值记录替换到html中
                $html_main = str_replace('{vip_consume_html}', $vip_consume_html, $html_main);
                $html_main = str_replace('{vip_recharge_html}', $vip_recharge_html, $html_main);

                $title = 'E代驾' . $title_month . 'VIP明细单';
                // 发送Mail
                Mail::sendMail(array(
                    $list['email'], 'accountbackup@edaijia-staff.cn',
//			'lidingcai@edaijia-inc.cn',
                    
//                    'mengtianxue@edaijia-staff.cn','shenguangliang@edaijia-staff.cn'
                ), $html_main, $title);

                if ($is_repeat == 0) {
                    //添加
                    $params = array(
                        'email' => $list['email'],
                        'vipcard' => $vip_card,
                        'type' => $type,
                        'remarks' => $title,
                        'vip_bill_time' => date('Y-m-d', $month_start),
                        'send_time' => date('Y-m-d H:i:s'),
                        'create_by' => '系统',
                        'status' => 1,
                        'created' => date('Y-m-d H:i:s'),
                    );

                    VipEmailLog::model()->addLog($params);
                }
                echo $vip_card . ":发送成功\n";
            } else {
                echo $vip_card . "\n";
            }
        }
        return true;
    }

    /**
     * 获取所有有邮件号码的vip卡号
     * @author mengtianxue 2013-05-30
     * @return array
     */
    public function getAllVip()
    {
        $allVip = Yii::app()->db_finance->createCommand()
            ->select("*")
            ->from("{{vip}}")
            ->where("email != :email",
                array(':email' => ''))
            ->queryAll();
        return $allVip;
    }

    /*
    private function getAllUserEmail()
    {
        return array(
                    'yuanrong@edaijia-inc.cn',
                    'shida@edaijia-inc.cn',
                    'dengxiaoming@edaijia-inc.cn',
                    'zhangzichao@edaijia-inc.cn',
                    'zhaoxinlei@edaijia-inc.cn',
                    'chenyan@edaijia-inc.cn',
                    'cuiluzhe@edaijia-inc.cn',
                    'qiujianping@edaijia-inc.cn',
                    'tl@edaijia-inc.cn',
                    'pd@edaijia-inc.cn',
                    'lead@edaijia-inc.cn'
                    );
    }


    private function getSpecifiedUserEmail()
    {
        return array(
                    'cuiluzhe@edaijia-inc.cn',
                    'chenxin@edaijia-inc.cn'
                    );
    }
    */

    /**
     *发送开票情况邮件  测试时需要加参数执行只发给我和qa
     *@author  cuiluzhe 2014-11-24
    **/
     public function actionSendInvoiceCount($to = '')
     {
        $error_occured = false;
	    $datas = CustomerInvoiceReport::model()->getInvoiceReport();
        if ($datas) {
	        $title = 'E代驾最近30日发票明细单';
	        $html_main = CustomerInvoice::model()->invoiceConsumeHtml($datas);
	        /*
            if($to == 'all'){
                $mail_to = $this->getAllUserEmail();
	        } else {
                $mail_to = MailConfig::model()->getMailToUsers(__CLASS__, __FUNCTION__);//$this->getSpecifiedUserEmail();
	        }
	        */
            $mail_to = MailConfig::model()->getMailToUsers(__CLASS__, __FUNCTION__);

            // send email one by one
            foreach ($mail_to as $to) {
                if ($this->sendInvoiceCountProcessed($to) !== false) {
                    continue;
                }

                if (Mail::sendMail(array($to), $html_main, $title) === false) {
                    $error_occured = true;
                } else if ($this->markSendInvoiceCountProcessed($to) !== true) {
                    EdjLog::info('markSendInvoiceCountProcessed failed');
                }
                sleep(1);
            }
        } else {
            EdjLog::info('未找到需要发送的发票数据');
        } 
        
        return $error_occured ? -1 : 0;
    }

    private function getSendInvoiceCountCheckingKey($email)
    {
        $namespace = 'SENDMAIL';
        $prefix = date('Y-m-d');
        return "$namespace|$prefix|$email";
    }

    private function markSendInvoiceCountProcessed($email)
    {
        return RedisHAProxy::model()->set($this->getSendInvoiceCountCheckingKey($email), 1, 24*60*60);
    }

    private function sendInvoiceCountProcessed($email)
    {
        return RedisHAProxy::model()->get($this->getSendInvoiceCountCheckingKey($email)) !== false;
    }

}
