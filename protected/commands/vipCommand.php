<?php

class vipCommand extends CConsoleCommand
{

    /**
     * vip_info 数据平移到 vip  vip_phone
     * php yiic.php vip Translation
     */
    public function actionTranslation()
    {
        $model = new UserVip();
        $allUserVip = $model->findall();
        if ($allUserVip) {
            foreach ($allUserVip as $row) {
                $phone = $row->phone;
                $phone = explode(';', $phone);
                $id = $row->id;
                $name = $row->name;
                $totleamount = $row->money;
                $balance = $row->remain;
                $created = strtotime($row->create_time);
                $type = $this->vipType($name);
                $status = $this->vipStatus($name);
                if (!empty($phone)) {
                    $num = 0;
                    foreach ($phone as $value) {
                        $num++;
                        if ($num == 1) {
                            $vipModel = new Vip();
                            $vip = $vipModel->attributes;
                            $vip['id'] = $id;
                            $vip['name'] = $name;
                            $vip['phone'] = $vip['send_phone'] = $value;
                            $vip['type'] = $type;
                            $vip['credit'] = 0;
                            $vip['city_id'] = 0;
                            $vip['totelamount'] = $totleamount;
                            $vip['balance'] = $balance;
                            $vip['status'] = $status;
                            $vip['created'] = $created;
                            $vipModel->attributes = $vip;
                            $vipModel->save();
                        }
                        $vipPhoneModel = new VipPhone();
                        $vipPhone = $vipPhoneModel->attributes;
                        $vipPhone['vipid'] = $id;
                        $vipPhone['type'] = $num == 1 ? 1 : 0;
                        $vipPhone['phone'] = $value;
                        $vipPhone['name'] = $this->getGuestNameForOrder($value);
                        $vipPhone['status'] = $status;
                        $vipPhone['created'] = $created;
                        $vipPhoneModel->attributes = $vipPhone;
                        $vipPhoneModel->save();
                        echo $value . "\n";
                    }
                }
            }
        }
    }

    public function getGuestNameForOrder($phone)
    {
        $order = Order::model()->find('phone = :phone', array(':phone' => $phone));
        if ($order) {
            return $order->name;
        } else {
            return '';
        }
    }


    public function vipStatus($name)
    {
        if (strstr($name, "停用"))
            return Vip::STATUS_DISABLE;
        else
            return Vip::STATUS_NORMAL;
    }

    public function vipType($name)
    {
        if (strstr($name, "补偿"))
            return Vip::TYPE_COMPENSATE;
        else
            return Vip::TYPE_CREDIT;
    }

    /**
     *
     * php yiic.php vip VipList --date=2013-02-03
     */
    public function actionVipList($date = 0)
    {
        if ($date == 0) {
            $date = date('Y-m-d 15:00:00', strtotime("-1 day"));
        }

        $start_date = strtotime($date);
        $end_date = $start_date + 86400;
        $listNum = 0;
        $syncNum = 0;
        try {
            $trade_time = date('Ymd');
            $sqlDelete = 'DELETE FROM t_vip_trade_log_report WHERE created = :created';
            $commandDelete = Yii::app()->dbstat->createCommand($sqlDelete);
            $commandDelete->bindParam(':created', $trade_time);
            $commandDelete->execute();
            $commandDelete->reset();


            $sql = 'INSERT INTO t_vip_trade_log_report
					(vipcard, name, phone, booking_time, location_start, location_end, distance, driver_id, waiting_time, waiting_amount, type, amount, created)
				VALUES
					(:vipcard, :name, :phone, :booking_time, :location_start, :location_end, :distance, :driver_id, :waiting_time, :waiting_amount, :type, :amount, :created)';
            $params = array(
                'start_time' => $start_date,
                'end_time' => $end_date,
            );
            $vipTrade = VipTrade::model()->getVipTradeList($params, VipTrade::TYPE_ORDER);
            $listNum = count($vipTrade);

            foreach ($vipTrade as $list) {
                try {
                    echo $list['order_id'] . "\n";
                    $order_info = Order::model()->find('order_id = :order_id', array(':order_id' => $list['order_id']));
                    if ($order_info) {
                        $phone = $order_info->phone;
                        $vipPhone = VipPhone::model()->getPrimary($phone);
                        $name = empty($vipPhone['name']) ? $order_info->name : $vipPhone['name'];
                        $booking_time = $order_info->booking_time;
                        $location_start = $order_info->location_start;
                        $location_end = $order_info->location_end;
                        $distance = $order_info->distance;
                        $driver_id = $order_info->driver_id;
                        $waiting_time = OrderExt::getOrderExm($list['order_id']);
                        $waiting_amount = floor($waiting_time / 30) * 20;

                        $command = Yii::app()->dbstat->createCommand($sql);
                        $command->bindValue(':vipcard', $list['vipcard']);
                        $command->bindParam(':name', $name);
                        $command->bindParam(':phone', $phone);
                        $command->bindParam(':booking_time', $booking_time);
                        $command->bindParam(':location_start', $location_start);
                        $command->bindParam(':location_end', $location_end);
                        $command->bindParam(':distance', $distance);
                        $command->bindParam(':driver_id', $driver_id);
                        $command->bindParam(':waiting_time', $waiting_time);
                        $command->bindParam(':waiting_amount', $waiting_amount);
                        $command->bindValue(':type', $list['type']);
                        $command->bindValue(':amount', $list['amount']);
                        $command->bindParam(':created', $trade_time);
                        $command->execute();
                        $command->reset();
                        $syncNum++;
                        echo $order_info->phone . "\n";
                    }
                } catch (Exception $e_list) {
                    EdjLog::error('order_id:' . $list['order_id'] . $e_list->getMessage());
                }
            }

            $mailTitle = 'php yiic.php vip VipList success!';
            $content = '总共' . $listNum . '条trade交易记录,同步' . $syncNum . '条';
            FinanceUtils::sendFinanceAlarm($mailTitle, $content);
        } catch (Exception $e) {
            EdjLog::error('php yiic.php vip VipList select from VipTrade error:' . $e->getMessage());
            $mailTitle = 'php yiic.php vip VipList select from VipTrade error:';
            $content = $e->getMessage() . ';' . '共：' . $listNum . '条记录，已同步条数：' . $syncNum;
            FinanceUtils::sendFinanceAlarm($mailTitle, $content);
        }
    }

    /**
     *
     * php yiic.php vip VipGetReport --date=20130203
     */
    public function actionVipGetReport($date = 0)
    {
        if ($date == 0) {
            $created = date('Ymd');
        } else {
            $created = $date;
        }
        $listNum = 0;
        $syncNum = 0;
        try {
            $sqlDelete = 'DELETE FROM t_vip_trade_log_report_url WHERE created = :created';
            $commandDelete = Yii::app()->dbstat->createCommand($sqlDelete);
            $commandDelete->bindParam(':created', $created);
            $commandDelete->execute();
            $commandDelete->reset();

            $vipTrade = Yii::app()->dbstat->createCommand()
                ->select('vipcard,sum(amount) as amount,count(1) as count')
                ->from('t_vip_trade_log_report')
                ->where('created = :created', array(':created' => $created))
                ->group('vipcard')
                ->queryAll();
            $sql = "INSERT INTO t_vip_trade_log_report_url
					(vipcard, order_count, consumpte, balance, created, url)
				VALUE
					(:vipcard, :order_count, :consumpte, :balance, :created, :url)";
            $listNum = count($vipTrade);
            foreach ($vipTrade as $list) {
                try {
                    $id = $list['vipcard'];
                    $url = $this->shortUrl($list['vipcard'] . $created);
                    $vip = Vip::model()->find('id = :id', array(':id' => $id));
                    $balance = $vip->balance;
                    $command = Yii::app()->dbstat->createCommand($sql);
                    $command->bindParam(':vipcard', $list['vipcard']);
                    $command->bindParam(':order_count', $list['count']);
                    $command->bindParam(':consumpte', $list['amount']);
                    $command->bindParam(':balance', $balance);
                    $command->bindParam(':created', $created);
                    $command->bindParam(':url', $url);
                    $command->execute();
                    $command->reset();
                    $syncNum++;
                    echo $list['vipcard'] . "\n";
                } catch (Exception $e) {
                    EdjLog::error('actionVipGetReport error;vipcard:' . $list['vipcard'] . ';order:' . $list['order_id'] . $e->getMessage());
                }
            }
            $mailTitle = 'php yiic.php vip actionVipGetReport success!';
            $content = '总共' . $listNum . '条t_vip_trade_log_report记录,同步' . $syncNum . '条';
            FinanceUtils::sendFinanceAlarm($mailTitle, $content);
        } catch (Exception $e) {
            EdjLog::error('php yiic.php vip actionVipGetReport error:' . $e->getMessage());
            $mailTitle = 'php yiic.php vip actionVipGetReport error';
            $content = $e->getMessage() . ';' . '共：' . $listNum . '条记录，已同步条数：' . $syncNum;
            FinanceUtils::sendFinanceAlarm($mailTitle, $content);
        }
    }

    /**
     *
     * php yiic.php vip VipSend --date=2013-02-03
     * @param unknown_type $date
     */
    public function actionVipSend($date = 0)
    {
        if ($date == 0) {
            $created = date('Ymd');
            $date = date('m月d日', strtotime('-1 day'));
        } else {
            $created = date('Ymd', strtotime($date));
            $date = date('m月d日', strtotime($date));
        }
        $listNum = 0;
        $syncNum = 0;
        try {
            $vipTradeUrl = Yii::app()->dbstat->createCommand()
                ->select('*')
                ->from('t_vip_trade_log_report_url')
                ->where('created = :created', array(':created' => $created))
                ->queryAll();
            $listNum = count($vipTradeUrl);
            foreach ($vipTradeUrl as $vipList) {
                try {
                    $vip = Vip::model()->find('id = :id', array(':id' => $vipList['vipcard']));
                    if ($vipList['order_count'] == 1 || $vip->send_type == Vip::SEND_TYPE_SMS) {
                        $vipTrade = Yii::app()->dbstat->createCommand()
                            ->select('*')
                            ->from('t_vip_trade_log_report')
                            ->where('created = :created and vipcard = :vipcard', array(':created' => $created, ':vipcard' => $vipList['vipcard']))
                            ->queryAll();
                        foreach ($vipTrade as $send_list) {
                            //vip短信内容去除电话，添加余额  @author mengtianxue 2013-05-09
                            $message = '尊敬VIP客户，您好，电话' . $send_list['phone'] . '，预约时间' . date('Y-m-d H:i', $send_list['booking_time']) . '使用代驾，从' . $send_list['location_start'] . '到' . $send_list['location_end'] . '，' . $send_list['distance'] . '公里，等候时间' . $send_list['waiting_time'] . '分钟，等候金额' . $send_list['waiting_amount'] . '总计金额' . $send_list['amount'] . '元,当前余额' . $vipList['balance'];
                            //Sms::SendSMS('13811480665', $message);
                            $result = Sms::SendSMS($vip->send_phone, $message);
                            if (!$result) {
                                $mailTitle = 'php yiic.php vip actionVipSend error';
                                $content = '手机号:' . $vip->send_phone . ';' . $vipList['vipcard'] . "发送失败";
                                FinanceUtils::sendFinanceAlarm($mailTitle, $content);
                            }
                            echo $vip->send_phone . "\n";
                        }
                    } else {
                        $message = 'VIP客户' . $vip->name . '您好!您的账户于' . $date . '使用' . $vipList['order_count'] . '次代驾,消费' . -$vipList['consumpte'] . '元,余额' . $vipList['balance'] . '元.消费详情请点击>> http://wap.edaijia.cn/info.html?' . $vipList['url'];// . ' 监督电话:4006913939';
                        //Sms::SendSMS('13811480665', $message);
                        $result = Sms::SendSMS($vip->send_phone, $message);
                        echo $vip->send_phone . "\n";
                    }

                    if ($result) {
                        $syncNum++;
                    } else {
                        $mailTitle = 'php yiic.php vip actionVipSend error';
                        $content = '手机号:' . $vip->send_phone . ';vipcard:' . $vipList['vipcard'] . "发送失败";
                        FinanceUtils::sendFinanceAlarm($mailTitle, $content);
                    }
                } catch (Exception $e_vipList) {
                    EdjLog::error('vipcard:' . $vipList['vipcard'] . ';' . $e_vipList->getMessage());
                }
            }
            $mailTitle = 'vip昨日消费日账单!';
            $content = '总共' . $listNum . '条记录,发送成功' . $syncNum . '条';
            FinanceUtils::sendFinanceAlarm($mailTitle, $content, 1);
        } catch (Exception $e) {
            EdjLog::error('php yiic.php vip actionVipSend error:' . $e->getMessage());
            $mailTitle = 'php yiic.php vip actionVipSend error';
            $content = $e->getMessage() . ';' . '共：' . $listNum . '条记录，已同步条数：' . $syncNum;
            FinanceUtils::sendFinanceAlarm($mailTitle, $content);
        }
    }

    /**
     * vip余额不足shit发短信通知
     * @author mengtianxue 2013-05-17
     * php yiic.php vip VipBalanceInform --date=2013-05-16
     */
    public function actionVipBalanceInform($date = 0)
    {
        if ($date == 0) {
            $created = date('Ymd');
            $date = date('m月d日', strtotime('-1 day'));
        } else {
            $created = date('Ymd', strtotime($date));
            $date = date('m月d日', strtotime($date));
        }
        $vipTradeList = Yii::app()->dbstat->createCommand()
            ->select('*')
            ->from('t_vip_trade_log_report_url')
            ->where('created = :created', array(':created' => $created))
            ->queryAll();
        foreach ($vipTradeList as $list) {
            $message = '';
            $yesterdayBalance = $list['balance'] - $list['consumpte'];
            $nowBalance = $list['balance'];

            if ($yesterdayBalance > 500 && $nowBalance < 500) {
                $message = '尊敬的VIP客户，您好。您的余额已低于500元。为了不影响正常使用，请您及时充值。咨询电话：010 58693979';
            }
            if ($yesterdayBalance > 200 && $nowBalance < 200) {
                $message = '尊敬的VIP客户，您好。您的余额已低于200元。为了不影响正常使用，请您及时充值。咨询电话：010 58693979';
            }
            if ($nowBalance <= 0) {
                $message = '尊敬的VIP客户，您好。您已欠费' . $nowBalance . '元。为了不影响正常使用，请您及时充值。咨询电话：010 58693979';
            }

            if (!empty($message)) {
                $vip = Vip::model()->getPrimary($list['vipcard']);
                if ($vip) {
                    $phone = empty($vip['send_phone']) ? $vip['phone'] : $vip['send_phone'];
                    Sms::SendSMS($phone, $message);
                    echo $phone . "." . $message . "金额" . $list['balance'] . "." . $list['consumpte'] . "\n";

                }
            }
        }
    }

    public function shortUrl($long_url)
    {
        $key = 'edaijai';
        $base32 = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";

        // 利用md5算法方式生成hash值
        $hex = hash('md5', $long_url . $key);
        $hexLen = strlen($hex);
        $subHexLen = $hexLen / 8;

        $output = array();
        for ($i = 0; $i < $subHexLen; $i++) {
            // 将这32位分成四份，每一份8个字符，将其视作16进制串与0x3fffffff(30位1)与操作
            $subHex = substr($hex, $i * 8, 8);
            $idx = 0x3FFFFFFF & (1 * ('0x' . $subHex));

            // 这30位分成6段, 每5个一组，算出其整数值，然后映射到我们准备的62个字符
            $out = '';
            for ($j = 0; $j < 6; $j++) {
                $val = 0x0000003D & $idx;
                $out .= $base32[$val];
                $idx = $idx >> 5;
            }
            $output[$i] = $out;
        }

        return $output[0];
    }

    public function actionConvert()
    {

        $userVipModel = new UserVip();

        $dataSrc = $userVipModel->findall();
        if (!empty($dataSrc)) {
            foreach ($dataSrc as $row) {
                $phone = $row->phone;
                $arrPhone = explode(";", $phone);

                if (!empty($arrPhone)) {

                    $status = 1;
                    $name = $row->name;//mb_convert_encoding($row->name, 'utf-8', 'gbk');

                    if (strstr($name, "停用"))
                        $status = 2;

                    if (strstr($name, "欠费"))
                        $status = 3;

                    $pos = strpos($name, "(");
                    if ($pos) {
                        $name = substr($name, 0, $pos);

                    } else {
                        $pos = strpos($name, "（");
                        if ($pos)
                            $name = substr($name, 0, $pos);
                    }

                    $name = trim($name);
                    $ctime = strtotime($row->create_time);

                    $vip = Vip::model()->findByPk($row->id);


                    if (empty($vip)) {
                        $dataVip = array(
                            'vipcard' => $row->id,
                            'name' => $name,
                            'phone' => $arrPhone[0],
                            'type' => 1,
                            'status' => $status,
                            'money' => $row->money,
                            'remain' => $row->remain,
                            'utime' => $ctime,
                            'ctime' => $ctime
                        );
                        $vipModel = new Vip;
                        $vipModel->attributes = $dataVip;
                        $vipModel->save();
                    } else {
                        $dataVip = array(
                            'name' => $name,
                            'phone' => $arrPhone[0],
                            'type' => 1,
                            'status' => $status,
                            'money' => $row->money,
                            'remain' => $row->remain,
                            'utime' => $ctime,
                            'ctime' => $ctime
                        );

                        $vip->attributes = $dataVip;
                        $ret = $vip->save();
                    }

                    array_shift($arrPhone);
                    if (!empty($arrPhone)) {
                        foreach ($arrPhone as $phone) {
                            $criteria = new CDbCriteria();
                            $criteria->addCondition("vipcard='$row->id'");
                            $criteria->addCondition("phone='$phone'");
                            $vipPhone = VipPhone::model()->find($criteria);
                            if ($vipPhone) {
                                $vipPhoneModel = new VipPhone();
                                $dataVipPhone = $vipPhoneModel->attributes;
                                $dataVipPhone['vipcard'] = $row->id;
                                $dataVipPhone['phone'] = $phone;
                                $dataVipPhone['status'] = 1;
                                $dataVipPhone['ctime'] = time();
                                $vipPhoneModel->attributes = $dataVipPhone;
                                $vipPhoneModel->insert();
                            }
                        }
                    }
                }
            }
        }
    }

    public function actionGenerate()
    {
        //生成500面值卡
        $startNoCard1 = 65001010;

        for ($i = 0; $i < 1000; $i++) {
            $cardNo1 = $startNoCard1 + $i;
            echo $cardNo1 . "\n";
            srand();
            $pass1 = rand(100000, 999999);

            while (VipCard::model()->find('pass=:pass', array(':pass' => $pass1))) {
                echo "重复\n";
                srand();
                $pass1 = rand(100000, 999999);
            }

            $card = VipCard::model()->find('id=:id', array(':id' => $cardNo1));
            if (!$card) {

                $dataCard = array(
                    'id' => $cardNo1,
                    'pass' => $pass1,
                    'money' => 500,
                    'status' => 0
                );
                $vipcard = new VipCard();
                $vipcard->attributes = $dataCard;
                $vipcard->insert();

            }
        }

        //生成1000面值卡
        $startNoCard2 = 67001010;
        for ($i = 0; $i < 1000; $i++) {
            $cardNo2 = $startNoCard2 + $i;
            echo $cardNo2 . "\n";

            srand();
            $pass2 = rand(100000, 999999);
            while (VipCard::model()->find('pass=:pass', array(':pass' => $pass2))) {
                echo "重复\n";
                srand();
                $pass2 = rand(100000, 999999);
            }


            $card = VipCard::model()->find('id=:id', array(':id' => $cardNo2));
            if (!$card) {
                $dataCard = array(
                    'id' => $cardNo2,
                    'pass' => $pass2,
                    'money' => 1000,
                    'status' => 0
                );
                $vipcard = new VipCard();
                $vipcard->attributes = $dataCard;
                $vipcard->insert();
            }
        }

    }

    //修改vip 2013-04-18 至 2013-04-23 没有扣款问题
    public function actionVipBalance()
    {
        $vip_list = Yii::app()->dbstat->createCommand()
            ->select("vipcard, sum( amount ) AS amount")
            ->from("t_vip_trade_log_report")
            ->where("created >= :created", array(":created" => '20130418'))
            ->group("vipcard")
            ->queryAll();
        foreach ($vip_list as $list) {
            $vip = Vip::model()->find('id = :id', array(':id' => $list['vipcard']));
            Vip::model()->updateByPk($vip->id, array('balance' => $vip->balance + $list['amount']));
            echo $vip->id . "余额" . $vip->balance . "扣除" . $list['amount'] . "剩余" . $vip->balance + $list['amount'] . "\n";
        }
    }

    /**
     * VIP修改地址发送给每个用户发短信
     * @auther mengtianxue
     * php yiic.php vip VipUpdateArr
     */
    public function actionVipUpdateArr()
    {
        $pagesize = 100;
        $offset = 0;

        while (true) {
            $criteria = new CDbCriteria();
            $criteria->select = "*";
            $criteria->addCondition("type = 0");
            $criteria->order = "created asc";
            $criteria->offset = $offset;
            $criteria->limit = $pagesize;
            $ret = Vip::model()->findAll($criteria);
            if ($ret) {
                foreach ($ret as $vip) {
                    if (!empty($vip->send_phone)) {
                        $phone = trim($vip->send_phone);
                    } else {
                        $phone = trim($vip->phone);
                    }

                    if (strlen($phone) == 11) {
                        $message = '尊敬的VIP客户，e代驾公司地址搬迁至北京朝阳区望京利泽中园二区203号洛娃大厦A座5层，VIP热线电话变更为：010-64392767。 感谢您一直以来对我们的支持，新的一年里我们将为您提供更加优质的服务！';
                        echo $phone . "\n";
                        Sms::SendSMS($phone, $message);
                    }
                }
            } else {
                break;
            }
            $offset += $pagesize;
        }
    }


    /**
     * vip月消费统计
     * @param <int> $year               开始年份
     * @param <int> $month              开始月份
     * @param <int> $afterMonths        统计几个月的数据
     * @example --year=2014 --month=1 --afterMonths=2
     * @author liuxiaobo
     * @since 2014-1-17
     */
    public function actionBuildVipCostMonth($year = null, $month = null, $afterMonths = 2)
    {
        $time = time();
        $year = $year === null ? date('Y', strtotime('-1 month')) : $year;      //默认从上个月开始
        $month = $month === null ? date('m', strtotime('-1 month')) : $month;
        $arg = '$year=' . $year . ',' . '$month=' . $month . ',' . '$afterMonths=' . $afterMonths;
        echo "\r\n start=========" . $arg . "======" . date('Y-m-d H:i:s', $time) . "======>";
        $count = VipCostMonth::model()->processReportCostMonth($year, $month, $afterMonths, FALSE, TRUE);
        echo 'update Items ' . $count . "\r\n";
        echo "\r\n end=========" . $arg . "========" . date('Y-m-d H:i:s', time()) . "====>";
    }

    /**
     * 单个vip周消费统计
     * @param <int> $year               开始年份
     * @param <int> $month              开始月份
     * @param <int> $day                开始日期
     * @param <int> $afterWeeks         统计几个月的数据
     * @example --year=2014 --month=1 --day=31 --afterWeeks=2
     * @author liuxiaobo
     * @since 2014-1-17
     */
    public function actionBuildSingleVipCostWeek($year = null, $month = null, $day = null, $afterWeeks = 2)
    {
        $time = time();
        $year = $year === null ? date('Y', strtotime('-1 week')) : $year;      //默认从上一周开始统计
        $month = $month === null ? date('m', strtotime('-1 week')) : $month;
        $day = $day === null ? date('d', strtotime('-1 week')) : $day;
        $arg = '$year=' . $year . ',$month=' . $month . ',$day=' . $day . ',$afterWeeks=' . $afterWeeks;
        echo "\r\nstart=========" . $arg . "======" . date('Y-m-d H:i:s', $time) . "======>\r\n";
        echo VipSingleWeekTrend::model()->buildWeekTrend($year, $month, $day, $afterWeeks, TRUE);   //统计vip周消费情况
        echo VipCostExt::model()->refreshData(TRUE);                          //刷新vip消费扩展表数据
        echo "\r\nend=========" . $arg . "========" . date('Y-m-d H:i:s', time()) . "====>";
    }

    /**
     * 用户投诉补偿，走用户个人账户，不再走VIP补偿用户
     * @auther duke
     * php yiic.php vip VipToCustomer
     *
     * 今天我看了一下你写的代码，感觉逻辑你还是不是很清楚，有些地方写的有点反复，哥们给你提点意见，你要是觉得没必要就这样也行。
     *
     * 逻辑思路
     * 1.VIP分页查询这个逻辑没有哦问题
     *
     * 2.根据手机号查询 CustomerMain 里面有没有信息
     *      如果有，查询 t_customer_account 是否有改用户信息
     *          有，修改 t_customer_account 的金额
     *        没有，添加 t_customer_account 添加信息
     *
     *     如果没有，添加 CustomerMain，然后直接在 t_customer_account 添加信息
     *
     * 3.在 t_customer_trans 添加充值记录
     *
     * 4.上面执行完了，把Vip和VipPhone
     *
     */

    public function actionVipToCustomer($is_test = false, $read = false, $stop = false, $title = false)
    {

        $pagesize = 100;
        if ($is_test) {
            $pagesize = 10;
        }
        $offset = 0;
        $i = 0;
        $time = date('Y-m-d H:i:s');
        $data = array();
        while (true) {
            $criteria = new CDbCriteria();
            $criteria->select = "*";
            $criteria->addCondition("type = 2 and phone != '' and status = 1");
            $criteria->order = "created asc";
            $criteria->offset = $offset;
            $criteria->limit = $pagesize;
            $ret = Vip::model()->findAll($criteria);
            if ($is_test) {
                $vip_phone = array();
                foreach ($ret as $v) {
                    //echo $v->phone.'   '.$v->name.'    '.$v->balance."\r\n";
                    $data[$v->phone] = array('phone' => $v->phone, 'name' => $v->name, 'user_id' => 0, 'pre_vip_balance' => $v->balance, 'customer_amount' => 0, 'aft_customer_amount' => 0);
                    $vip_phone[] = $v->phone;
                }
                // echo '----------------------'."\r\n";
                if ($read == 1) {
                    if (!empty($vip_phone)) {
                        $phone_str = "'" . implode("','", $vip_phone) . "'";
                        //echo $phone_str;die;
                        $sqlmain = "select * from t_customer_main where phone in ({$phone_str}); ";
                        echo $sqlmain . "\r\n";
                        $maincommand = Yii::app()->db->createCommand($sqlmain);
                        $customer_main_res = $maincommand->queryAll();
                        //print_r($customer_main_res);echo "\r\n";
                        if (!empty($customer_main_res)) {
                            $cm_phone = $user_id_arr = array();
                            foreach ($customer_main_res as $cv) {
                                $user_id_arr[] = $cv['id'];
                                $cm_phone[$cv['id']] = $cv;
                            }
                            //echo '22222';print_r($cm_phone);echo "\r\n";die;
                            // print_r($user_id_arr);die;
                            $customer_id_arr = implode(',', $user_id_arr);
                            $sqlacc = "select * from t_customer_account where user_id in ({$customer_id_arr}); ";
                            echo $sqlacc . "\r\n";
                            $cus_acc_command = Yii::app()->db_finance->createCommand($sqlacc);
                            $customer_acc_res = $cus_acc_command->queryAll();
//print_r($customer_acc_res);die;
                            if (!empty($customer_acc_res)) {
                                $ca_arr = array();
                                foreach ($customer_acc_res as $ca) {
                                    $ca_arr[$ca['user_id']] = $ca;
                                }
                                //print_r($ca_arr);die;
//echo "补偿前 用户余额：\r\n";
                                foreach ($ca_arr as $ck => $car) {
                                    //print_r($car);
                                    $ca_arr[$ck]['phone'] = $cm_phone[$ck]['phone'];
                                    $data[$cm_phone[$ck]['phone']]['customer_amount'] = $car['amount'];
                                    $data[$cm_phone[$ck]['phone']]['user_id'] = $ck;
                                    //echo "phone:{$cm_phone[$ck]['phone']}-----money:{$car['amount']} \r\n";
                                }


                            }
                        }
                    }

                    echo 'phone,name,user_id,vip卡内余额,转移前用户余额,转移后用户余额' . "\r\n";
                    foreach ($data as $v) {

                        echo implode(',', $v);
                        echo "\r\n";
                    }
                }

                if ($stop) {
                    die;
                }
            }
            if ($ret) {
                $vipphone_mod = VipPhone::model(); //实例化  直接 New VipPhone();就可以了   mengtianxue
                foreach ($ret as $vip) {
                    //首先查询customer_main 是否有数据 如果有则更新数据 没有则insert
                    $customermain_mod = CustomerMain::model();
                    $customer_main_exist = $customermain_mod->findByAttributes(array('phone' => $vip->phone));
                    //desc
                    if ($customer_main_exist && $customer_main_exist->phone) { //如果没有数据会报错。  Trying to get property of non-object   添加判断   mengtianxue
                        if ($vip->balance > 0) {
                            $res_update = $customermain_mod->updateAll(array('amount' => $vip->balance + $customer_main_exist->amount, 'update_time' => $time, 'operator' => 'dongkun'), 'phone = :phone', array(':phone' => $vip->phone));
                        }
                        $customermain_id = $customer_main_exist->id; // $customer_main_exist  查询来的是一个对象  mengtianxue
                    } else {
                        $insert_balance = $vip->balance > 0 ? $vip->balance : 0;
                        $sqlmain = "insert into t_customer_main set name='{$vip->name}' , phone='{$vip->phone}' , email='{$vip->email}', backup_phone = '{$vip->send_phone}' , city_id = {$vip->city_id}, amount = {$insert_balance} , status = 1, invoice_title = '{$vip->commercial_invoice}', operator = 'dongkun',create_time = '{$time}' ";

                        $maincommand = Yii::app()->db->createCommand($sqlmain);

                        $res_main = $maincommand->execute();
                        $customermain_id = Yii::app()->db->getLastInsertID();
                    }


                    if ($customermain_id > 0) {

                        //获取vip 副卡数据
                        $vipPhone = $vipphone_mod->getVipCardPhone($vip->id, true);

                        //把所有vip副卡的type 都设为禁用 status = 2
                        if (!empty($vipPhone)) {
                            //desc
                            foreach ($vipPhone as $v) {

                                $re444 = $vipphone_mod->updateByPk($v['id'], array('status' => 2));
                                //echo 'set status = forbiden ->2 id:' . $v->id . '----';

                            }
                        }


                        if ($vip->balance > 0) {
                            // $customer_account  先查询是否有记录 然后在update insert
                            $check_sql = "select count(*)  as count from t_customer_account where user_id = {$customermain_id}";
                            $checkcommand = Yii::app()->db_finance->createCommand($check_sql);
                            $check_account_res = $checkcommand->queryAll(); //queryScalar()  mengtianxue

                            //desc
                            if ($check_account_res[0]['count']) {
                                $sql_account = "update t_customer_account set amount = amount + {$vip->balance}, update_time='{$time}' where user_id = {$customermain_id}";

                            } else {
                                $sql_account = "insert into t_customer_account set
                            user_id = {$customermain_id},
                            vip_card=0 ,
                            type=1 ,
                            city_id = {$vip->city_id},
                            amount = {$vip->balance} ,
                            update_time = '{$time}'
                            ";

                            }

                            $accountcommand = Yii::app()->db_finance->createCommand($sql_account);
                            $res_account = $accountcommand->execute();
                        }

                        //customer trans 添加一条记录
                        //$trans_sql = "insert into t_customer_trans set
                        //  user_id = {$customermain_id} ,
                        //  trans_type = 5  ,
                        //  amount = {$vip->balance}  ,
                        //  balance = {$vip->balance} ,
                        //  source = 1  ,
                        //  create_time = '{$time}' ,
                        //  remark = 'vip 补偿用户 转成普通用户'
                        //";
                        //$transcommand = Yii::app()->db->createCommand($trans_sql);
                        //$res_trans = $transcommand->execute();

                        $vip_mods = Vip::model();
                        $vip_change_status = $vip_mods->updateByPk($vip->id, array('status' => 2));
                        if ($is_test) {
                            $check_sql = "select  *  from t_customer_account where user_id = {$customermain_id}";
                            $checkcommand = Yii::app()->db_finance->createCommand($check_sql);
                            $check_account_res = $checkcommand->queryAll(); //queryScalar()  mengtianxue
                            //print_r($check_account_res);
                            foreach ($check_account_res as $v) {
                                //echo $vip->phone.'----'.$v['amount']."\r\n";
                                $data[$vip->phone]['aft_customer_amount'] = $v['amount'];
                            }
                        }
                    }
                }
            } else {
                break;
            }
            if ($title) echo 'phone,name,vip卡内余额,转移前用户余额,转移后用户余额' . "\r\n";
            foreach ($data as $v) {

                echo implode(',', $v);
                echo "\r\n";
            }
            $i++;
            $offset += $pagesize;
            //上线之前 注释掉
            if ($is_test && $i == 1) {
                break;
            }
        }
    }

}


