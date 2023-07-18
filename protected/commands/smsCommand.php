<?php

class SmsCommand extends LoggerExtCommand
{

    public function actionSendSmsTestSpeed()
    {
        $count = time();
        Sms::SendSMS('13810349756', '刚才呼入的（135111111111）为VIP客户，卡号: 65001010，账户余额:1000.00，不足部分请收取现金。报单时,系统将自动从VIP账户划转代驾费到您账户，并短信通知客户扣款金额。');
        echo(time() - $count);
    }

    //脚本超时发送短信
    public function actionScriptsTimeoutSendSms($cronId) {
    	if (0 == date('i') % 10) {
    		$sql = "SELECT
	cronId,
	task,
	timeout,
	host_name
FROM
	sys_crontab c,
	sys_crontab_host h
WHERE
	c.`host` = h.`host` AND 
    cronId = ?
    	";
    		$data = Yii::app()->dbsys->createCommand($sql)->queryRow(true, array($cronId));
    		if (is_array($data) && $data['timeout']>0) {
    			$msg = "taskId ". $data['cronId'] ." " . $data['task'] ." 在 ". $data['host_name'] ." 上运行时间已经超过了设置时间 ". $data['timeout'] ." 分钟。";
    			Sms::SendSMS('18701552183', $msg, Sms::CHANNEL_SOAP);
                Sms::SendSMS('18600296880', $msg, Sms::CHANNEL_SOAP);
    		}
    	}
    }

	//脚本报警
	public function actionScriptsWaring($msg,$time=10,$phone=NULL) {
		if ($msg!="") {
            $right = false;
            if (intval($time)==0) {
                $right = true;
                $time=1;
            }
            if ($right == true || 0 == date('i') % intval($time)) {

                if ($phone==NULL) {
                    $phones = array(
                        '18701552183', //邓小明
                        '18600296880', //于杨
                        //'18710151637', //岳洋
                        '15201112120', //从从
                        '13021031591', //王健
                        '13426307748', //丘建平
                        //'15001162563', //冯广祥
                    );
                }
                else {
                    $phones = explode(",", $phone);
                }
                foreach($phones as $p) {
                    $send_phone = intval($p);
                    if ($send_phone==0) break;
                    Sms::SendSMS($send_phone, $msg, Sms::CHANNEL_SOAP);
                }

			}
		}
	}


    /**
     * 发送市场营销短信
     * 命令  php protected/yiic sms senddriverssms
     *
     * @editor AndyCong<congming@edaijia.cn> 2013-04-12 15:40:00
     * @return unknown_type
     */
    public function actionSendMarketingSms()
    {
        $status = 0;
        $current = date("Y-m-d H:i:s");
        $sql = "SELECT id,phone,content,pre_send_time FROM t_market_sms WHERE `status` = :status and pre_send_time<=:current limit 100 ";
		echo "开始获得数据，查询数据库\n";
		echo $sql."\n";
        $command = Yii::app()->db_readonly->createCommand($sql);
        echo "创建command对象\n";
        $command->bindParam(":status", $status);
        $command->bindParam(":current", $current);
        echo "开始查询\n";
        $marketing_sms_list = $command->queryAll();
        echo "查询完成\n";

        echo "----------" . date("Y-m-d H:i:s") . "---job begin------\n";
        $i = 1;
        $arr = array();
        foreach ($marketing_sms_list as $item) {
            echo $i . ':' . $item['phone'];
            $send_ret = Sms::SendSMS($item['phone'], $item['content']); //修改为指联在线-下行通道发短信 BY AndyCong
            echo ",result:" . $send_ret . "\n";
            $arr[] = $item['id'];
            $i++;
        }
        //整体更新 2013-12-04
        if (!empty($arr)) {
        	$update_sql = "update t_market_sms set `status`=1 WHERE `id` in(".implode(',' , $arr).") ";
        	$change_status = Yii::app()->db->createCommand($update_sql)->execute();
        	echo "-------execute ".$change_status." record------\n";
        }

        //整体更新 2013-12-04 END
        echo "----------" . date("Y-m-d H:i:s") . "---job   end------\n";
    }

    /**
     *
     * 为司机推送短信通知
     */
    public function actionSendDriverTmpSms()
    {
        $status = 0;
        $current = date("Y-m-d H:i:s");
        $sql = "SELECT user,name,ext_phone as phone FROM `t_driver` WHERE city_id='1' and mark =0 ";

        $command = Yii::app()->db_readonly->createCommand($sql);
        $driver_list = $command->queryAll();

        $i = 1;
        foreach ($driver_list as $item) {
            echo $i . '.' . $item['phone'] . "\n";
            $content = '师傅们：近期订单量猛增，大家赶快上线赚钱啦.
高峰期以下区域严重司机短缺，建议师傅们高峰前到以下地区等待： 西单、金融街、广安门、崇文门、雍和宫、交道口、西直门、蓟门桥、安贞、四季青、亚运村、天宁寺、魏公村、万丰路等。郊区的师傅也可在四环附近等待。';
            //echo  $content . "\n";
            $send_ret = Sms::SendSMS($item['phone'], $content, Sms::CHANNEL_ZLZX); //修改为指联在线-下行通道发短信 BY AndyCong
            print_r($send_ret);
            $i++;
        }
    }


    /**
     *
     * 推送app客户微信推广短信
     * @author sunhongjing 2013-06-13
     */
    public function actionSendWeixinAppCustomerSms($status = 12)
    {
        $sql = "SELECT id , phone FROM `t_weixin_customer_sms` WHERE id>0 and status={$status} and is_send=0 order by id asc limit 5000";
        $command = Yii::app()->db_readonly->createCommand($sql);
        $customer_list = $command->queryAll();

        $update_sql = "update t_weixin_customer_sms set `is_send`=:is_send WHERE `id` = :id ";
        $new_status = 1;
        foreach ($customer_list as $item) {
            $content = '【微信版e代驾】微信e代驾正式上线！搜索并关注e代驾微信公众账号“edaijia”或“e代驾”，即可瞬间呼叫周边司机，e代驾5200名专业司机为您护航到家！打开此链接： http://data.edaijia.cn/qrcode.html  让您的朋友也扫一扫哟。';
            $send_ret = Sms::SendSMS($item['phone'], $content, Sms::CHANNEL_ZLZX); //修改为指联在线-下行通道发短信 BY AndyCong
            echo $item['id'] . '---' . $item['phone'] . '---' . $send_ret . "\n";
            $change_status = Yii::app()->db->createCommand($update_sql);

            $change_status->bindParam(":is_send", $new_status);
            $change_status->bindParam(":id", $item['id']);
            $change_status->execute();
            $change_status->reset();
        }

    }


    /**
     *
     * 推送400客户微信推广短信
     * @author sunhongjing 2013-06-06
     */
    public function actionSendWeixin400CustomerSms()
    {
        $sql = "SELECT id , phone FROM `t_weixin_customer_sms` WHERE id>0 order by id asc ";
        $command = Yii::app()->db_readonly->createCommand($sql);
        $customer_list = $command->queryAll();
        foreach ($customer_list as $item) {
            $content = '【微信版e代驾】微信叫e代驾正式上线！搜索并关注e代驾微信公众账号"edaijia"或"e代驾"，即可瞬间呼叫周边司机，e代驾5200名专业司机为您护航到家！';
            $send_ret = Sms::SendSMS($item['phone'], $content, Sms::CHANNEL_ZLZX); //修改为指联在线-下行通道发短信 BY AndyCong
            echo $item['id'] . '---' . $item['phone'] . '---' . $send_ret . "\n";
        }
    }

    /**
     *
     * 推送司机微信推广短信
     * @author sunhongjing 2013-06-06
     */
    public function actionSendWeixinDriverSms()
    {

        $sql = "SELECT user,name,ext_phone as phone FROM `t_driver` WHERE mark =0 ";
        $command = Yii::app()->db_readonly->createCommand($sql);
        $driver_list = $command->queryAll();

        $i = 1;
        foreach ($driver_list as $item) {
            $content = 'e代驾微信叫代驾正式上线！让呼叫中心的排队占线成为浮云！推荐客户关注e代驾微信公众账号：edaijia 或点击链接 http://data.edaijia.cn/qrcode.html  ，让客户扫二维码即可叫代驾！';
            $send_ret = Sms::SendSMS($item['phone'], $content, Sms::CHANNEL_ZLZX); //修改为指联在线-下行通道发短信 BY AndyCong
            echo $i . '---' . $item['phone'] . '---' . $send_ret . "\n";
            $i++;
        }

    }

    /**
     * 队列状态监控，超过100发短信
     *
     */
    public function actionQueueStatusMonitor()
    {
        //每分钟执行一次，每15分钟发一条短信
        $queue_len = Queue::model()->length();

        //定义：
        //queue_dumplog:0
        //queue_task:0
        //queue_heartbeat:13
        //queue_position:4
        $warning = 0;
        $msg = '';
        $queue_str = '';
        $reg = "/^!/";

        foreach ($queue_len as $k => $v) {
            if (preg_match($reg, $k)) {
                $msg .= '队列' . $k . '已积压数据' . $v . '条,';
                $warning = 1;
            }
            break;

            $queue_str .= $k . ':' . $v . "\n";
        }

        if (1 == $warning) {
            $msg .= '请及时检查系统是否正常。'.date("Y-m-d H:i:s");
        }

        //15分钟报一次
        if (1 == $warning && 0 == (date('i') % 15) ) {
            //邓小明
            Sms::SendSMS('18701552183', $msg, Sms::CHANNEL_SOAP);
            //于杨
            Sms::SendSMS('18600296880', $msg, Sms::CHANNEL_SOAP);
        }
        echo date("Y-m-d H:i:s") . "---job begin------\n";
        echo $queue_str;
        echo date("Y-m-d H:i:s") . "---job   end------\n";
    }


    /**
     * 获取短信余量，低于20000条发送警告
     * @author congming
     * @editor sunhongjing 2013-04-20
     */
    public function actionGetBalance()
    {
    	$refer_num = 50000;
        $last_total = 0;
        $ret = Sms::GetBalance(Sms::CHANNEL_SOAP);
        $total = intval($ret->GetBalanceResult);

        $command = Yii::app()->dbstat->createCommand();
        $command->select('total')->from('t_sms_log')->order('id desc')->limit('1')->offset('0');
        $row = $command->queryAll();

        if ($row) {
            $last_total = intval($row[0]['total']);
        }
		//print_r($total);
		echo "e达信剩余:".$total."条\n";
        if ($last_total != $total) {
            $sql = 'insert into t_sms_log(total) value (:total)';
            Yii::app()->dbstat->createCommand($sql)->execute(array(
                ':total' => $total));
            //发送警告信息
            if ($total < $refer_num) {
                Sms::SendSMS('13520489430', "E达信短信余量{$total},低于{$refer_num}条，请及时充值。".date("Y-m-d H:i:s"));
                Sms::SendSMS('18911883373', "E达信短信余量{$total},低于{$refer_num}条，请及时充值。".date("Y-m-d H:i:s"));
                Sms::SendSMS('18701552183', "E达信短信余量{$total},低于{$refer_num}条，请及时充值。".date("Y-m-d H:i:s"));
                Sms::SendSMS('18600296880', "E达信短信余量{$total},低于{$refer_num}条，请及时充值。".date("Y-m-d H:i:s"));
                Sms::SendSMS('18701552183', "E达信短信余量{$total},低于{$refer_num}条，请及时充值。".date("Y-m-d H:i:s"));
            }
        }


        $ret = Sms::GetBalance(Sms::CHANNEL_ZCYZ);
        $total = intval($ret->GetBalanceResult);

		//print_r($total);
		echo "e达信剩余:".$total."条\n";
            //发送警告信息
        if ($total < $refer_num) {
                Sms::SendSMS('13520489430', "E达信-验证码通道短信余量{$total},低于{$refer_num}条，请及时充值。".date("Y-m-d H:i:s"));
                Sms::SendSMS('18911883373', "E达信-验证码通道短信余量{$total},低于{$refer_num}条，请及时充值。".date("Y-m-d H:i:s"));
                Sms::SendSMS('18701552183', "E达信-验证码通道短信余量{$total},低于{$refer_num}条，请及时充值。".date("Y-m-d H:i:s"));
                Sms::SendSMS('18600296880', "E达信-验证码通道短信余量{$total},低于{$refer_num}条，请及时充值。".date("Y-m-d H:i:s"));
        }

        //指联在线短信提醒判定 BY AndyCong<congming@edaijia.cn>
        /*
        $ret_zlzx = Sms::Balance(Sms::CHANNEL_ZLZX );
        if ($ret_zlzx) {
            $str_arr = explode(':' , $ret_zlzx);
            if ($str_arr[0] == 'success') {
                if ($str_arr[1] < 1000) {
                    $str_phone = '13581619658,18701552183,18911883373,18911933768';
                    Sms::SendSMS($str_phone, '指联在线短信余量'.$str_arr[1].'条,已低于1000条，请及时充值。' , Sms::CHANNEL_ZLZX );
                }
            }
        }*/
        //指联在线短信提醒判定 end


        //33易9短信提醒判定 BY AndyCong<congming@edaijia.cn> 2013-05-03
        $ret_gsms = Sms::Balance(Sms::CHANNEL_GSMS);
       echo "33e9剩余:".$ret_gsms."条\n";
        if (intval($ret_gsms) < $refer_num) {
            Sms::SendSMS('13520489430', '33易9短信余量' . $ret_gsms . "条,已低于{$refer_num}条，请及时充值。".date("Y-m-d H:i:s"));
            Sms::SendSMS('18701552183', '33易9短信余量' . $ret_gsms . "条,已低于{$refer_num}条，请及时充值。".date("Y-m-d H:i:s"));
            Sms::SendSMS('18911883373', '33易9短信余量' . $ret_gsms . "条,已低于{$refer_num}条，请及时充值。".date("Y-m-d H:i:s"));
            Sms::SendSMS('18600296880', '33易9短信余量' . $ret_gsms . "条,已低于{$refer_num}条，请及时充值。".date("Y-m-d H:i:s"));
        }
//echo "33e9剩余:".$ret_gsms."条\n";
        //33易9短信提醒判定 end
    }

    /**
     *
     * 发送最近通过手机端拨打司机电话的短信
     */
    public function actionLastCall()
    {
        return;
        //对最近一小时上报的call history记录预处理
        $start = date('Y-m-d H:i:s.000', time() - 300);
        $end = date('Y-m-d H:i:s.999', time());

        //		$start = date('2012-09-18 20:00:00');
        //		$end = date('2012-09-18 20:30:00');


        //从通话记录中生成需要发送短信的客户电话
        $criteria = new CDbCriteria(array(
            'select' => 'c.imei,c.phone,c.gap,c.insert_time',
            'condition' => 'c.type=0 AND e.phone is null',
            'join' => 'LEFT JOIN t_employee e ON c.phone = e.phone',
            'order' => 'id'));

        $criteria->alias = 'c';
        $criteria->addBetweenCondition('insert_time', $start, $end);

        $ret = CallHistory::model()->findAll($criteria);
        if ($ret) {
            foreach ($ret as $item) {
                $flag = 0;
                if(CustomerWhiteList::model()->in_whitelist($item->phone)){
                    $flag = 1;
                }
                //判断电话是1开头的11位数字，发短信
                if (!preg_match('%1\d{10}%', $item->phone)) {
                    $flag = 1;
                }
                //查询电话是否司机的电话
                $ret = Driver::getDriverByPhone($item->phone);
                if ($ret) {
                    echo 'driver phone:' . $item->phone . "\n";
                    $flag = 1;
                }

                if ($flag == 0) {
                    $driver = Driver::getProfileByImei($item->imei);


                    $content = MessageText::getFormatContent(MessageText::CUSTOMER_DRIVER_DIRECT, $driver->user, $driver->name, $driver->phone);

                    //'欢迎预约e代驾!'.$driver->user.$driver->name.'('.$driver->phone.')为您服务，代驾员已出发，祝您一路平安！监督电话:4006506955';
                    //$content = 'e代驾邀您为'.$driver->name.'师傅评星！如有拒单、不穿工装、故意迟到、多收费，或态度恶劣，请在App中予以差评，核实后双倍返还代驾费！投诉电话：4006506955';
                    echo $item->phone . "\n";

                    //$content = '欢迎监督并点评'.$item->driver->name.'的服务，如拒单、迟到或虚报里程。如服务不满意，请在App中予以差评并留下联系方式！监督电话4006506955';
                    //$content = '欢迎监督并点评'.$item->driver->name.'的服务，如拒单、迟到或虚报里程。如不穿工装和出示工牌，您可以拒付代驾费！监督电话4006506955';
                    //$content = '欢迎监督并点评'.$item->driver->name.'的服务，如：有无拒单、拖延迟到或虚报里程多收费；如没有身着工装和出示工牌，您可以拒付代驾费！服务监督电话4006506955';
                    //$content = '欢迎监督e代驾的服务！如果代驾员'.$item->driver->name.'没有身着工装和出示工牌，您可以拒付代驾费。服务监督电话：400 650 6955';
                    //$content = 'e代驾邀您对司机'.$item->driver->name.'的服务进行评价！在客户端中点击＂我的代驾＂即可点评。欢迎在新浪微博分享您的e代驾体验。';
                    //$content = 'e代驾邀您对司机'.$item->driver->name.'进行评级：您的评级决定司机的饭碗！在App中点击＂我的代驾＂添加点评。服务监督：400-650-6955';
                    //$content = 'e代驾邀您对代驾员'.$item->driver->name.'的服务进行评价！在App中进入＂我的代驾＂即可点评该司机。服务监督电话：400-650-6955';


                    //上报时间和呼叫时间误差超过2小时的，不再发送短信
                    if ($item->gap < 7200) {
                        $status = SmsClient::WAIT_SEND;
                    } else {
                        $status = SmsClient::NOT_SEND;
                    }

                    $call_time = strtotime($item->insert_time) - $item->gap;
                    $params = array(
                        'sender' => $item->imei,
                        'receiver' => $item->phone,
                        'message' => $content,
                        'status' => $status,
                        'call_date' => date('Ymd', strtotime($item->insert_time)),
                        'created' => $call_time);
                    $queue = new SmsClient();
                    $queue->attributes = $params;
                    $ret = $queue->save();
                }

                //如果保存成功，安排一条40分钟的定时短信
                //				if($ret == 1){
                //					$content = '请点评e代驾司机'.$item->driver->name.'的服务！如：有无拒单、拖延迟到、工装工牌和虚报里程多收费；（在APP点击＂我的代驾＂即可点评）';
                //					Sms::gxmt($item->phone, $content,'' ,date('Y-m-d H:i:s',time()+2400),'');
                //				}
            }
        }
        echo 'start send sms...' . "\n";

        //开始发送队列中的短信
        while (true) {
            $offset = 0;
            $criteria = new CDbCriteria(array(
                'condition' => 'status=' . SmsClient::WAIT_SEND,
                'limit' => 50,
                'offset' => $offset,
                'order' => 'id desc'));
            $sms_items = SmsClient::model()->findAll($criteria);
            if (!$sms_items) {
                break;
            } else {
                foreach ($sms_items as $item) {
                    $mobile = $item->receiver;
                    echo $mobile;
                    echo "\n";
                    $sms_ret = Sms::SendSMS($mobile, $item->message);

                    //解决短信通道返回值不统一导致的bug ，by sunhongjing 2013-05-08
                    if ($sms_ret) {
                        SmsClient::model()->updateByPk($item->id, array(
                            'status' => SmsClient::SUCCESS_SEND));
                    }
                }
            }
            $offset += 50;
        }
    }

    /**
     *
     * 对Call Center派单用户发送推荐下载短信
     */
    public function actionCallCenter()
    {
        //$ret = Sms::gxmt('18911883373', '测试短信', '', '2012-04-29 19:29:00', '');
        $ret = Sms::SendSMSEx('18911883373', '测试5位扩展码', '12345');
        print_r($ret);
    }

    /**
     * 记录发送点评短信 到t_sms_send
     */
    public function actionDianPingFirst($date = null)
    {
        $begin_time=$end_time='';
        if ($date) {
            $end_time = date('Y-m-d 07:00:00', strtotime($date));
            $begin_time = date('Y-m-d 07:00:00', strtotime($date) - 86400);
        } else {
            $end_time = date('Y-m-d 07:00:00', time());
            $begin_time = date('Y-m-d 07:00:00', time() - 86400);
        }

        echo '-------------save sms to t_sms_send------- start: '.date('Y-m-d H:i:s',time())."\r\n\r\n";

        //获取短信内容
//        $temp=SmsTemplate::model()->getContentBySubject('dianping_complete_order');
//        $message_complete_order=$temp['content'];

        //2013-11-08 暂停发送销单短信
//        $temp=SmsTemplate::model()->getContentBySubject('dianping_cancel_order');
//        $message_cancel_order=$temp['content'];

        $max = 0;
        $pagesize = 1000;

        $begin_time = strtotime($begin_time);
        $end_time = strtotime($end_time);

        $criteria = new CDbCriteria();
        $criteria->select = "order_id,channel,driver_id,phone,contact_phone,status,start_time,call_time,price,income,imei,source";
        $criteria->condition = "order_id>:max and source not in(0,2,30,32,40,41,42,43) and status='1' and call_time between :begin_time and :end_time";
        $criteria->group = 'phone,driver_id';
        $criteria->order = 'order_id asc';
        $criteria->limit = $pagesize;

        while (true) {
            $criteria->params = array(
        						':max' => $max,
					            ':begin_time' => $begin_time,
					            ':end_time' => $end_time,
        					);
            $orders = Order::model()->findAll($criteria);
            if ($orders) {
                foreach ($orders as $order) {
                	$max = ( $max > $order->order_id ) ? $max : $order->order_id;
                    $phone = empty($order->contact_phone) ? $order->phone : $order->contact_phone;
                    $driver_id=$order->driver_id;
                    $order_id=$order->order_id;
                    $channel=$order->channel;
                    $status=$order->status;
                    $start_time=$order->start_time;
                    $income=$order->income;
                    $imei=$order->imei;
                    //放入队列
                    $data = array(
                        'phone' => $phone,
                        'driver_id' => $driver_id,
                        'channel' => $channel,
                        'order_id' => $order_id,
                        'status' => $status,
                        'start_time' => $start_time,
                        'income' => $income,
                        'imei' => $imei,

                    );
                    $task=array(
                        'method'=>'collect_order_msg',
                        'params'=>$data,
                    );
                    echo 'order_id='.$order_id.'短信评价推送加入队列处理...'.PHP_EOL;
                    Queue::model()->putin($task,'message');
                }
                sleep(120);//休眠2分钟，要不然队列处理不过来，会报警
            } else {
                break;
            }
        }

        echo '-------------save sms to t_sms_send------- end: '.date('Y-m-d H:i:s', time())."\r\n\r\n";

    }

    /**
     * 从t_sms_send 表取出待发短信
     * 发送价格核实和评价短信
     * @author bidong 2013-08-08
     */
    public function actionDianPingSecond(){

        echo '-------------get sms from {t_sms_send} to send------- start: '.date('Y-m-d H:i:s', time())."\r\n\r\n";

        $criteria=new CDbCriteria;
        $criteria->select='*';
        $criteria->condition=' status=:status and created >:time ';
        $criteria->limit=500;
        $criteria->order='id asc';
        $criteria->params=array(':status'=>0,':time'=>date('Y-m-d',strtotime('-3 day')));

        $sms_data= SmsSend::model()->findAll($criteria);
        foreach($sms_data as $sms){
            if(!empty($sms)){
                //置中间状态 为 3.待发送；发送成功后置为 1；
                SmsSend::model()->updateByPk($sms->id,array('status'=>3));

                $flag = 0;
                //排除公司司机的电话号码
                $ret = Driver::getDriverByPhone($sms->receiver);
                if ($ret) {
                    $flag++;
                }
                //排除白名单电话号码
                if(CustomerWhiteList::model()->in_whitelist($sms->receiver)){
                    $flag++;
                }
                //过滤非手机号码,1开头的11位数字
                if (!preg_match('%^1\d{10}%', $sms->receiver)) {
                    $flag++;
                }

                if($flag==0){
                    //发送短信,后期改为模板
                    if(isset($sms->receiver) && isset($sms->message) && isset($sms->subcode) ){
                        //再次验证当前状态
                        $smsModel=SmsSend::model()->findByPk($sms->id);
                        if(!empty($smsModel) && intval($smsModel->status)==3){
                            echo 'start send --- receiver:'.$sms->receiver.'--- subcode:'.$sms->subcode.'---channel:'.Sms::CHANNEL_MT."\r\n";
                            // $result=Sms::SendForOrderComment($sms->receiver, $sms->message, $sms->subcode);
                            $result=Sms::SendSMS($sms->receiver, $sms->message, Sms::CHANNEL_MT, $sms->subcode);
                            if($result){
                                //发送成功后，更新 t_sms_send 状态
                                $succ= SmsSend::model()->updateByPk($sms->id,array('status'=>1));
                                if($succ){
                                    echo "succ \r\n";
                                }
                            }else{
                                //发送失败 置为待发送状态 0
                                SmsSend::model()->updateByPk($sms->id,array('status'=>0));
                                echo 'error --- receiver:'.$sms->receiver.'--- subcode:'.$sms->subcode.'---channel:'.Sms::CHANNEL_MT."\r\n";
                            }
                        }
                    }
                }
            }
        }
        echo '-------------get sms from {t_sms_send} to send------- end: '.date('Y-m-d H:i:s', time())."\r\n\r\n";

    }

    /**
     *
     * 接收司机评价短信RecSms
     */
    public function actionMoSmsRec()
    {

		//启动一个进程，随机执行3-10分钟。防止长时间不完。
		$timestamp=time();
		$quit_time=rand(5, 15)*60;

		while(true) {
			if ( time() - $timestamp > $quit_time ) {
					echo "-----over define process time: runed {$quit_time}s------\n";
					break;
			}else{

				//接收短信
		        $moSmsArr = Sms::moSms();
		        //$moSmsArr = array(array('recvtel'=>'30011800229','sender'=>'13683214122','content'=>'1满。；意。，？','recdate'=>'2013-05-11 12:12:12'));

		        if (!empty($moSmsArr)){
		            foreach ($moSmsArr as $sms) {
		            	echo 'recid:'.$sms['recvtel'].'---sender:'.$sms['sender'].'---content:'.$sms['content'].'----recdate:'.$sms['recdate']."\n";
                        try {
                            $model = new SmsMo();
                            $model->attributes = $sms;
                            $model->created = date("Y-m-d H:i:s");
                            $model->channel = Sms::CHANNEL_SOAP;
                            $model->subcode = strlen($sms['recvtel']) > 6 ? substr($sms['recvtel'], 6) : ''; //从第六位开始截取，只适用与E达信
                            if ($model->save()) {
                                echo "success---" . 'subcode:' . $model->subcode . '---' . "\n";

                                //一键登录判断上报短信内容 bidong 2014-1-18
                                $clogic = new CustomerLogic();
                                $clogic->customerSmsCompare($sms['sender'], $sms['content']);

                            }
                        } catch (Exception $e) {

                            echo "----- db error -----\n";
                            print_r($e);
                            echo "----- db error -----\n";
                        }

			        }
		        }
		        sleep(1);
			}
		}

    }

    /**
     * 修复因为subcode 错误造成的短信收取问题
     * @author bidong 2013-08-13
     */
    public function actionRepairRecSms()
    {
        $c=0;
        $offset = 0;
        $pagesize = 200;
        while (true) {

            $param = array(':channel' => Sms::CHANNEL_SOAP,':time'=>'2013-08-09');
            $retSmsArr = Yii::app()->db->createCommand()->select('*')
                ->from('{{sms_mo}}')
                ->where('channel = :channel and length(subcode)=1 and created>=:time')
                ->limit($pagesize)->offset($offset)->queryAll(true, $param);

            if (empty($retSmsArr)) {
                break;
            }

            foreach ($retSmsArr as $sms) {

                $smsSendInfo = Yii::app()->db->createCommand()->select('*')
                    ->from('{{sms_send}}')
                    ->where('receiver = :receiver', array(':receiver' => $sms['sender']))
                    ->order('id DESC')->limit(1)->queryRow();

                if ($smsSendInfo) {
                    $order_status = $smsSendInfo['order_status'];
                    $id = $smsSendInfo['driver_id'];
                    $order_id = $smsSendInfo['order_id'];
                    //sms_type 0.评价短信  1.价格核实短信
                    $ret = Helper::getSmsStar($sms['content']);

                    $attributes = array(
                        'sender' => $sms['sender'],
                        'driver_id' => $id,
                        'imei' => $smsSendInfo['imei'],
                        'level' => 0,
                        'content' => $ret['content'],
                        'raw_content' => $sms['content'],
                        'confirm' => 1,
                        'order_status' => $order_status,
                        'created' => $sms['recdate'],
                        'order_id' => $order_id,
                        'sms_type' => 0,
                        'status' => 0);

                    $isPingjia = strpos($sms['content'], '元');

                    if ($isPingjia !== false) {
                        $attributes['level'] = 0;
                        $attributes['sms_type'] = 1;
                    } else {
                        if ($ret) {
                            $attributes['level'] = $ret['level'];
                            $attributes['sms_type'] = 0;
                        }
                    }
                    Yii::app()->db->createCommand()->insert('t_comment_sms', $attributes);


                    echo $sms['sender'].'--'.$attributes['level'].'--'.$attributes['sms_type']."\r\n";
                    $c++;
                    echo $c."\r\n";
                }

            }
            $offset += $pagesize;
        }
    }


    /**
     * 接收用户评价司机短信 from t_sms_mo
     * 短信回评入库 t_comment_sms
     * @author bidong 2013-08-08
     */
    public function actionNewRecSms(){

        echo '-------------receive sms from {SmsMo} ------- start: '.date('Y-m-d H:i:s', time())."\r\n\r\n";
        echo '--------读取上发短信--START-------'."\r\n\r\n";
        $retSmsArr =SmsMo::model()->getSmsData(Sms::CHANNEL_SOAP, 0, 3);
        echo '--------读取上发短信--END-------'."\r\n\r\n";
        if (empty($retSmsArr)){
            echo '----no data----'.date('Y-m-d H:i:s', time())."\r\n\r\n";
            exit('no sms');
        }
        echo '--------获取短信模板--START-------'."\r\n\r\n";
        //获取短信内容
        $level_5=SmsTemplate::model()->getContentBySubject('user_reply_level_5');
        $level_4=SmsTemplate::model()->getContentBySubject('user_reply_level_4');
        $level_3=SmsTemplate::model()->getContentBySubject('user_reply_level_3');
        $level_2=SmsTemplate::model()->getContentBySubject('user_reply_level_2');
        echo '--------获取短信模板--END-------'."\r\n\r\n";
        echo '--------处理短信--START-------'."\r\n\r\n";


        foreach ($retSmsArr as $sms) {

            //如果是测试号，走新规则（一级互访短信回评不进入投诉，二级互访短信回评后差评进投诉）
            $this->_newRecSms($sms);
            continue;


            $is_save_comment=false;     //是否直接存储评论
            $comment_status=0;          //短信评价 处理状态
            $record_complaint=false;    //是否 转为投诉
            $complain_type=0;           //投诉分类  1->多收费；2->态度恶劣；3->驾驶技术；4->标准工装
            $user_reply= array('level' => 0, 'content' => ''); //用户回评分析
            //大于3天的回复不处理
            $reply_time = strtotime($sms['recdate']);
            if (time() - $reply_time > 259200){
                continue;
            }

            $sender=$sms['sender'];     //发送人
            $subcode =$sms['subcode'];  //附加码
            $rep_sms_content=$sms['content'];
            if($subcode){
                //找到原始发送短信,取 status=1 成功发送短信的
                $smsSendInfo = Yii::app()->db_readonly->createCommand()
                    ->select('*')
                    ->from('{{sms_send}}')
                    ->where('subcode = :subcode and status=:status and sender=:sender', array(':subcode' => (int)$subcode,':status'=>1,':sender'=>$sender))
                    ->order('id DESC')->queryRow();

                if(!empty($smsSendInfo)){
                    //sms_type 0.评价短信  1.价格核实短信
                    $new_send_sms_type=0;
                    $sms_type=$smsSendInfo['sms_type'];
                    $smsSend_id=$smsSendInfo['id'];
                    $order_id=$smsSendInfo['order_id'];
                    $driver_id=$smsSendInfo['driver_id'];

                    $order_status=$smsSendInfo['order_status'];
                    $imei=$smsSendInfo['imei'];

                    $level=0;
                    $content='';//解析后的内容
                    if($sms_type==0){
                        $sms_content='';

                        //解析回复短信内容，取出评分
                        $user_reply=Helper::getSmsScore($rep_sms_content);
                        $level=intval($user_reply['level']);
                        $content=$user_reply['content'];
                        switch($level){
                            case 5:
                                $sms_content=$level_5;
                                $is_save_comment=true;
                                break;
                            case 4:
                                $sms_content=$level_4;
                                $is_save_comment=true;
                                $comment_status=1;      //短信评价 已处理状态
                                $record_complaint=true; //转入投诉系统
                                $complain_type=27;      //标准工装
                                break;
                            case 3:
                                $sms_content=$level_3;
                                $is_save_comment=true;
                                $comment_status=1;      //短信评价 已处理状态
                                $record_complaint=true; //转入投诉系统
                                $complain_type=4;       //驾驶技术
                                break;
                            case 2:
                                $sms_content=$level_2;
                                $is_save_comment=true;
                                $comment_status=1;      //短信评价 已处理状态
                                $record_complaint=true; //转入投诉系统
                                $complain_type=39;      //态度恶略
                                break;
                            case 1:
                                //当用户回复多收费时，补发价格核实短信
                                //同时插入 t_sms_send 一条数据，类型为价格核实
                                //SmsSend::saveSmsLog($data)
                                $orderInfo = Order::getDbReadonlyConnection()->createCommand()
                                    ->select('*')
                                    ->from('{{order}}')
                                    ->where('order_id = :order_id', array(':order_id' => $order_id))
                                    ->order('order_id DESC')->queryRow();
                                if($orderInfo){
                                    $order_status=$orderInfo['status'];
                                    $sms_param=array('$time$'=>date('H时i分', $orderInfo['booking_time']),'$cost$'=>$orderInfo['price']);
                                    $sms_content=SmsTemplate::model()->getContentBySubject('user_reply_level_1',$sms_param);
                                }
                               //添加发送记录,发送类型为价格核实
                                $new_send_sms_type=1;
                                $is_save_comment=true;
                                $comment_status=1;      //短信评价 已处理状态
                                $record_complaint=true; //转入投诉系统
                                $complain_type=18;      //多收费
                                break;
                            case 0:
                                //直接存储 t_comment_sms
                                $is_save_comment=true;
                                break;
                            default:

                                break;
                        }
                        //发送互动短信
                        if(!empty($sms_content)){
                            $data = array(
                                'sender' => $sender,
                                'message' => $sms_content['content'],
                                'type' => $new_send_sms_type,    //0.评价短信/1.价格核实
                                'order_id' => $order_id,
                                'driver_id' => $driver_id,
                                'order_status' => $order_status,
                                'imei' => $imei
                            );

                            //评价短信。改为只记录数据，再起一个JOB 循环发送 bidong 2013-08-08
                            SmsSend::model()->saveSmsLog($data);
                        }
                    }
                    if($sms_type==1){
                        //价格核实，直接存储到 t_comment_sms
                        $content=$rep_sms_content;  //价格核实内容 存储相同
                        $is_save_comment=true;
                    }
                    //评价有效直接存储
                    if($is_save_comment){

                        $attributes = array(
                            'sender' => $sender,
                            'driver_id' => $driver_id,
                            'imei' => $imei,
                            'level' => $level,
                            'content' =>$content,
                            'raw_content' => $rep_sms_content,
                            'confirm' => 1,
                            'order_status' => $order_status,
                            'created' => $sms['recdate'],
                            'order_id' => $order_id,
                            'sms_type' => $sms_type,
                            'status' => $comment_status);
                        $result=  Yii::app()->db->createCommand()->insert('t_comment_sms', $attributes);
                        unset($attributes);

                        //接收回评短信后，更改sms_send 表status 为 2，标识已接收回评短信
                        Yii::app()->db->createCommand()->update('t_sms_send', array('status' => 2), 'id=:id',array(':id'=>$smsSend_id));

                        //收取评价后，清除 订单评论缓存 2013-11-1
//                        $cache_key = 'ORDER_COMMENT_' . $order_id;
//                        Yii::app()->cache->delete($cache_key);
                        $arr = array('is_comment' => 'Y', 'level' => $level);
                        ROrderComment::model()->setComment($order_id, $arr);


                    }

                    //短信回评 汇入投诉系统
                    if($record_complaint){
                        $customer_complian=new CustomerComplain();
                        $customer_complian->name=$sender;
                        $customer_complian->phone=$sender;
                        $customer_complian->customer_phone=$sender;
                        $customer_complian->driver_id=$driver_id;

                        $customer_complian->order_id=$order_id;
                        $customer_complian->complain_type=$complain_type;

                        $customer_complian->source=2;//来源短信评价
                        $customer_complian->create_time=$sms['recdate'];
                        $customer_complian->created=$customer_complian->operator='系统';
                        $customer_complian->status=1;
                        //$citys=array_flip(Dict::items('city_prefix'));

                        $customer_complian->city_id=DriverStatus::model()->getItem($driver_id,'city_id');

                        $customer_complian->cs_process=1;//客服创建
                        $customer_complian->detail=$content;

                        $res= $customer_complian->insert();
                        //add by aiguoxin 加入意见列表
                        CustomerSuggestion::model()->initSuggestion($sender,$content,
                            CustomerSuggestion::TYPE_COMPLAIN,$customer_complian->attributes['id']);

                        var_dump($res);

                    }

                    echo "-----sender:-----".$sms['sender']."-----content:----".$sms['content']."\r\n";

                }
            }

        }
        echo '--------处理短信--END-------'."\r\n\r\n";
        echo '-------------receive sms from {SmsMo} ------- end: '.date('Y-m-d H:i:s', time())."\r\n\r\n";

    }

    /**
     * 新规则---短信拆分 第一次发送一级互动短信 当评分小于5则第二次发送二级互动短信，回评后直接进入品鉴投诉
     * @param $sms
     * @return bool
     * @AndyCong<congming@edaijia-inc.cn>
     * @version 2014-04-14
     */
    private function _newRecSms($sms) {

        //大于3天的回复不处理
        $reply_time = strtotime($sms['recdate']);
        if (time() - $reply_time > 259200){
            return true;
        }

        EdjLog::info(json_encode($sms).'|RecSms start' , 'console');

        $sender=$sms['sender'];     //发送人
        $subcode =$sms['subcode'];  //附加码
        $rep_sms_content=$sms['content'];

        if(!$subcode) {
            return false;
        }

        EdjLog::info($sender.'|'.$subcode.'|1' , 'console');

        $smsSendInfo = Yii::app()->db_readonly->createCommand()
            ->select('*')
            ->from('{{sms_send}}')
            ->where('subcode = :subcode and status=:status and sender=:sender', array(':subcode' => (int)$subcode,':status'=>1,':sender'=>$sender))
            ->order('id DESC')->queryRow();

        if(empty($smsSendInfo)) {

            EdjLog::info($sender.'|'.$subcode.'|回评|发送短信数据不存在|1' , 'console');

            return false;
        }

        EdjLog::info($sender.'|'.$subcode.'|回评|1' , 'console');

        //如果是第二次回评 则直接进投诉
        $send_num = isset($smsSendInfo['send_num']) ? intval($smsSendInfo['send_num']) : 1;
        if($send_num == 2) {

            EdjLog::info($sender.'|'.$subcode.'|回评|直接进投诉|2' , 'console');

            $customer_complian=new CustomerComplain();

            //已投诉过的则不在覆盖
            $con = array(
                ':order_id'  => $smsSendInfo['order_id'],
                ':driver_id' => $smsSendInfo['driver_id'],
            );
            $complain = $customer_complian->find('order_id=:order_id and driver_id=:driver_id' , $con);
            if($complain) {
                return true;
            }

            $customer_complian->name=$sender;
            $customer_complian->phone=$sender;
            $customer_complian->customer_phone=$sender;
            $customer_complian->driver_id=$smsSendInfo['driver_id'];

            $customer_complian->order_id=$smsSendInfo['order_id'];
            $customer_complian->complain_type=6; //是否需要对应到摸一个类型 例（1:多收费......）

            $customer_complian->source=2;//来源短信评价
            $customer_complian->create_time=$sms['recdate'];
            $customer_complian->created=$customer_complian->operator='系统';
            $customer_complian->status=1;
            //$citys=array_flip(Dict::items('city_prefix'));
            $customer_complian->city_id = DriverStatus::model()->getItem(trim($smsSendInfo['driver_id']),'city_id');

            $customer_complian->cs_process=1;//客服创建
            $customer_complian->detail=Helper::getSmsMoContent($rep_sms_content);

            $res= $customer_complian->insert();
            //add by aiguoxin 加入意见列表
            CustomerSuggestion::model()->initSuggestion($sender,$customer_complian->detail,
                CustomerSuggestion::TYPE_COMPLAIN,$customer_complian->attributes['id']);

            EdjLog::info($sender.'|'.$subcode.'|回评|投诉写DB完成|'.$res.'|end' , 'console');

            return $res;
        }

        EdjLog::info($sender.'|'.$subcode.'|回评|触发二级回评短信|2' , 'console');

        //否则按照类型再去给客户发条短信
        $content_level_1 = SmsTemplate::model()->getContentBySubject('customer_reply_level_1');
        $content_level_2 = SmsTemplate::model()->getContentBySubject('customer_reply_level_2');
        $content_level_3 = SmsTemplate::model()->getContentBySubject('customer_reply_level_3');
        $content_level_4 = SmsTemplate::model()->getContentBySubject('customer_reply_level_4');
        $content_level_5 = SmsTemplate::model()->getContentBySubject('customer_reply_level_5');
        $content_level_0 = SmsTemplate::model()->getContentBySubject('customer_reply_level_0');

        $user_reply=Helper::getSmsScore($rep_sms_content);
        $level=intval($user_reply['level']);
        $content=$user_reply['content'];

        EdjLog::info($sender.'|'.$subcode.'|回评分数'.$level.'|回评内容'.$content.'|2' , 'console');

        $sms_content = '';
        $is_save_comment = false;
        $send_num = 2;
        switch($level) {
            case 1:
                $sms_content = $content_level_1;
                $is_save_comment = true;
                break;
            case 2:
                $sms_content = $content_level_2;
                $is_save_comment = true;
                break;
            case 3:
                $sms_content = $content_level_3;
                $is_save_comment = true;
                break;
            case 4:
                $sms_content = $content_level_4;
                $is_save_comment = true;
                break;
            case 5:
                $sms_content = $content_level_5;
                $is_save_comment = true;
                break;
            case 0:
                if($smsSendInfo['send_num'] != 999 ) {
                    $sms_content = $content_level_0;
                    $send_num = 999; //下一次回复再次回复错误代码 则不在发送第二条互动短信
                }
                break;
            default:
                break;
        }

        if(!empty($sms_content)){
            $data = array(
                'sender' => $sender,
                'message' => $sms_content['content'],
                'type' => 0,    //0.评价短信/1.价格核实
                'order_id' => $smsSendInfo['order_id'],
                'driver_id' => $smsSendInfo['driver_id'],
                'order_status' => $smsSendInfo['order_status'],
                'imei' => $smsSendInfo['imei'],
                'send_num' => $send_num,
            );

            //评价短信。改为只记录数据，再起一个JOB 循环发送 bidong 2013-08-08
            SmsSend::model()->saveSmsLog($data);

            EdjLog::info($sender.'|'.$subcode.'|二次回评短信保存成功|3' , 'console');
        }

        //add by aiguoxin
        if(CommentSms::model()->getCommandSmsByOrderIdAndType($smsSendInfo['order_id'],$smsSendInfo['sms_type'])){
            $is_save_comment=false;
        }
        
        if($is_save_comment){

            $attributes = array(
                'sender' => $sender,
                'driver_id' => $smsSendInfo['driver_id'],
                'imei' => $smsSendInfo['imei'],
                'level' => $level,
                'content' =>$content,
                'raw_content' => $rep_sms_content,
                'confirm' => 1,
                'order_status' => $smsSendInfo['order_status'],
                'created' => $sms['recdate'],
                'order_id' => $smsSendInfo['order_id'],
                'sms_type' => $smsSendInfo['sms_type'],
                'status' => 1);
            $result=  Yii::app()->db->createCommand()->insert('t_comment_sms', $attributes);

            EdjLog::info($sender.'|'.$subcode.'|回评内容保存到t_comment_sms|保存状态'.$result.'|4' , 'console');

            unset($attributes);

            //接收回评短信后，更改sms_send 表status 为 2，标识已接收回评短信
            Yii::app()->db->createCommand()->update('t_sms_send', array('status' => 2), 'id=:id',array(':id'=>$smsSendInfo['id']));

            //再次更新缓存 做保障
            $arr = array('is_comment' => 'Y', 'level' => $level);
            ROrderComment::model()->setComment($smsSendInfo['order_id'] , $arr);

            EdjLog::info($sender.'|'.$subcode.'|'.$smsSendInfo['order_id'].'缓存更新成功|RecSms end' , 'console');

        }
        
    }

    /**
     * 添加各城市的区分  --孟天学  2013-04-03
     * 周六，周日，周五不屏蔽司机
     * 司机信息费低于200元的通知充值
     */
    public function actionNotifyRecharge()
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
            if ($curr_week == 0 || $curr_week == 5 || $curr_week == 6) {
                $do_notify_recharge = false;
            }
        }

        if ($do_notify_recharge) {
            //信息费低于限额的司机名单 (北京,上海，广州，深圳)
            $citys = RCityList::model()->getDriverCityLt(200);
            $drivers = Driver::model()->DriverLists($citys, 200);
            $message = '%s师傅，您的信息费%s已经低于200元，每天10点、16点财务扣款，系统每天中午12点进行自动屏蔽处理，为了不影响您的正常接单，请您及时通过司机端或交行卡充值。';
            foreach ($drivers as $item) {
                //享受优惠的司机不屏蔽  mengtianxue 2013-07-30
                $discount = Common::driver_fee_discount($item['driver_id']);
                if ($discount == 1) {
                    $content = sprintf($message, $item['driver_id'], $item['balance']);
                    $phone = (isset($item['phone'])) ? $item['phone'] : $item['ext_phone'];
                    Sms::SendSMS($phone, $content);
                    echo $item['driver_id'] . "\n";
                }
            }

            //信息费低于限额的司机名单（杭州，重庆）
            $citys_area = RCityList::model()->getDriverCityLt(100);
            $drivers_area = Driver::model()->DriverLists($citys_area, 100);
//            $drivers_area = Driver::model()->getArrearage_area(100);
            $message_area = '%s师傅，您的信息费%s已经低于100元，每天10点、16点财务扣款，系统每天中午12点进行自动屏蔽处理，为了不影响您的正常接单，请您及时通过司机端或交行卡充值。';
            foreach ($drivers_area as $item) {
                //享受优惠的司机不屏蔽  mengtianxue 2013-07-30
                $discount = Common::driver_fee_discount($item['driver_id']);
                if ($discount == 1) {
                    $content = sprintf($message, $item['driver_id'], $item['balance']);
                    $phone = (isset($item['phone'])) ? $item['phone'] : $item['ext_phone'];
                    Sms::SendSMS($phone, $content);
                    echo $item['driver_id'] . "\n";
                }
            }
        }


    }

    /**
     * 周六，周日，周一不屏蔽司机
     * 连续三天不报单的司机屏蔽
     */
    public function actionNotifyBlock()
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
            if ($curr_week == 0 || $curr_week == 1 || $curr_week == 6) {
                $do_notify_recharge = false;
            }
        }

        if ($do_notify_recharge) {

            //信息费低于限额的司机名单 (北京,上海，广州，深圳)
//            $drivers = Driver::model()->getArrearage(200);
            $citys = RCityList::model()->getDriverCityLt(200);
            $drivers = Driver::model()->DriverLists($citys, 200);
            $message = '信息费余额%s元,已经不足200元，公司给予屏蔽处理，请您尽快在司机客户端充值，恢复正常接单';
            foreach ($drivers as $item) {
                //享受优惠的司机不屏蔽  mengtianxue 2013-07-30
                $discount = Common::driver_fee_discount($item['driver_id']);
                if ($discount == 1 && $item['mark'] != 1) {
                    echo $item['driver_id']."\n";
                    $i_message = sprintf($message, $item['balance']);
                    Driver::model()->block($item['driver_id'], Employee::MARK_DISNABLE, DriverLog::LOG_MARK_DISABLE_FEE, $i_message, true);
                }elseif($item['mark'] == 1){
                    echo $item['driver_id'] . "---该司机已经被屏蔽---\n";
                }else{
                	echo $item['driver_id'] . "---新城市优惠期内折扣不屏蔽---\n";
                }
            }

            //信息费低于限额的司机名单（杭州，重庆）
//            $drivers_area = Driver::model()->getArrearage_area(100);
            $citys_area = RCityList::model()->getDriverCityLt(100);
            $drivers_area = Driver::model()->DriverLists($citys_area, 100);
            $message_area = '信息费余额%s元,已经不足100元，公司给予屏蔽处理，请您尽快在司机客户端充值，恢复正常接单。';
            foreach ($drivers_area as $item) {
                //享受优惠的司机不屏蔽  mengtianxue 2013-07-30
                $discount = Common::driver_fee_discount($item['driver_id']);
                if ($discount == 1 && $item['mark'] != 1) {
                    echo $item['driver_id'] . "\n";
                    $i_message = sprintf($message_area, $item['balance']);
                    Driver::model()->block($item['driver_id'], Employee::MARK_DISNABLE, DriverLog::LOG_MARK_DISABLE_FEE, $i_message, true);
                }elseif ($item['mark'] == 1) {
                    echo $item['driver_id'] . "---该司机已经被屏蔽---\n";
                } else {
                    echo $item['driver_id'] . "---新城市优惠期内折扣不屏蔽---\n";
                }
            }

        }
    }

    /**
     * 添加各城市的区分  --孟天学  2013-04-03
     * 因欠费屏蔽的司机，VIP分账之后激活,实时结账后废弃不用了
     */
    public function actionNotifyEnable()
    {
    	echo "该方法已废弃.\n";
    	return;
//        $drivers = Yii::app()->db_readonly->createCommand()->select('v.user,v.name,v.city_id,d.phone,d.ext_phone, (t1+t2+t3+t4+t5+t6+t7+t8) cast')->from('t_view_employee_account_sum v, t_driver d')->where('v.user= d.user and d.mark =1 and d.city_id in(1,3,5,6)')->having('cast>200')->queryAll();
//        $drivers_area = Yii::app()->db_readonly->createCommand()->select('v.user,v.name,v.city_id,d.phone,d.ext_phone, (t1+t2+t3+t4+t5+t6+t7+t8) cast')->from('t_view_employee_account_sum v, t_driver d')->where('v.user= d.user and d.mark =1 and d.city_id in(4,7)')->having('cast>100')->queryAll();
//        $drivers = array_merge($drivers, $drivers_area);
//        if ($drivers) {
//            $message = '信息费余额%s元。已解除屏蔽。';
//            foreach ($drivers as $item) {
//                $lastMarkLog = DriverLog::model()->getLastMarkLog($item['user']);
//                if (!empty($lastMarkLog) && $lastMarkLog->type == DriverLog::LOG_MARK_DISABLE_FEE) {
//                    $i_message = sprintf($message, $item['cast']);
//                    Driver::model()->block($item['user'], Employee::MARK_ENABLE, DriverLog::LOG_MARK_ENABLE, $i_message, true);
//                    echo $item['user'] . "\n";
//                }
//            }
//        }
    }

    /**
     *
     * 司机信息费低于300元的发通知充值
     */
    /*注释掉无效代码，稳定后直接删除这些代码，谁再用团望会打他的
    public function actionNotify300()
    {
        $curr_week = date('w');

        if ($curr_week != 0 && $curr_week != 5 && $curr_week != 6) {
            $drivers = Driver::model()->getArrearage(300);

            $message = '%s师傅，您的信息费余额为%s元,已经临近200元，为了不影响您的正常接单，请您及时补交信息费。';
            foreach ($drivers as $item) {
                $i_message = sprintf($message, $item['user'], $item['cast']);
                Sms::SendSMS($item['phone'], $i_message);
            }
        }
    }
    */


    // php protected/yiic sms notifyjd
    public function actionNotifyJD()
    {
        $phones = array();
        //$phones = Yii::app()->db->createCommand()->select('*')->from('t_tt')->queryAll();
        //$message = '为了感谢大家在恶劣天气出勤工作，今日接单司机一律补贴路费30元（资金来源于罚款），以表公司对各位师傅的感谢。天冷路滑，大家出行注意安全，周末愉快！';
        //$message = '12月5日(周三),10号线缩短运营时间3小时,巴沟站末班车提前至19:26；劲松站末班车提前至20:15。8日(周六)全天停止运营,请师傅们知晓。';
        //$message = '明天12月8日（周六），10号线全天停止运营，请师傅们提前选择好交通工具和出行路线。';
        $message = '司机师傅，今晚系统故障导致定位不准，非常抱歉。现系统已经恢复正常，请重新开机上线。坐席非常繁忙，请尽量不要拨打400。';

        foreach ($phones as $item) {
            echo $item['phone'] . "\n";
            Sms::SendSMS($item['phone'], $message);
        }
    }

    // php protected/yiic sms notifyjdlocation
    public function actionNotifyJDLocation()
    {
        $phones = array();
        //$phones = Yii::app()->db->createCommand()->select('*')->from('t_tt')->queryAll();
        //$message = '为了感谢大家在恶劣天气出勤工作，今日接单司机一律补贴路费30元（资金来源于罚款），以表公司对各位师傅的感谢。天冷路滑，大家出行注意安全，周末愉快！';
        //$message = '12月5日(周三),10号线缩短运营时间3小时,巴沟站末班车提前至19:26；劲松站末班车提前至20:15。8日(周六)全天停止运营,请师傅们知晓。';
        //$message = '明天12月8日（周六），10号线全天停止运营，请师傅们提前选择好交通工具和出行路线。';

        $message_bj = '赚钱时候到了!各位师傅,平安夜和圣诞,酒会聚餐火爆,订单猛增！另，推荐畅行和畅饮的司机签约，更有300元相赠。';
        $message_other = '赚钱时候到了!各位师傅,平安夜和圣诞,酒会聚餐火爆,订单猛增！赚钱的兄弟跟上！';

        foreach ($phones as $item) {
            echo $item['driver_id'] . "_" . $item['phone'] . "\n";
            $local = strtoupper(substr($item['driver_id'], 0, 2));
            switch ($local) {
                case 'BJ':
                    $message = $message_bj;
                    break;
                default:
                    $message = $message_other;
            }
            Sms::SendSMS($item['phone'], $message);
        }
    }

    public function actionNotifyMonthlyDelayBlock()
    {
        $drivers = array();
        //$drivers = Yii::app()->db->createCommand()->select('driver_id,phone')->from('t_tt')->queryAll();
        $message = '在规定的时间内未来公司交确认单，公司给予屏蔽处理，请您尽快到公司交单，恢复正常接单。';
        foreach ($drivers as $item) {
            Driver::model()->block($item['driver_id'], Employee::MARK_DISNABLE, DriverLog::LOG_MARK_DISABLE_MONTHLY_DELAY, $message);
        }
    }

    public function actionNotifyDriverPasswdChange()
    {
        $connect = Yii::app()->db;
        $drivers = $connect->createCommand()->select('user, mark, id_card, RIGHT(LTRIM(RTRIM(id_card)), 6) as npwd, phone, ext_phone')->from('t_driver')->where('password=6688')->queryAll();
        $message = '因后台系统升级，您的密码已改为您身份证号码后六位。新密码为：%s';
        $sql_driver = "UPDATE t_driver SET password = RIGHT(RTRIM(LTRIM(id_card)), 6) WHERE user='%s'";
        $sql_employee = "UPDATE t_employee SET password = RIGHT(LTRIM(RTRIM(id_card)), 6) WHERE user='%s'";
        foreach ($drivers as $item) {
            $sqldriver = sprintf($sql_driver, $item['user']);
            $sqlemployee = sprintf($sql_employee, $item['user']);
            $connect->createCommand($sqldriver)->execute();
            $connect->createCommand($sqlemployee)->execute();
            echo $item['phone'] . "\n";
            if ($item['mark'] == 0) {
                $newmsg = sprintf($message, $item['npwd']);
                echo $item['phone'] . "\n";
                Sms::SendSMS($item['phone'], $item['user'] . $newmsg);
                if (!empty($item['ext_phone'])) {
                    Sms::SendSMS($item['ext_phone'], $item['user'] . $newmsg);
                }
            }
        }
    }


    /**
     * 指联在线-下行短信接口测试
     */
    public function actionTestChannelZLZX()
    {
        //测试电话号
        $phones = array(
            array('phone' => '13611126764'), //孙洪静电话
        );
        $content = '测试下行通道';
        $i = 1;
        foreach ($phones as $item) {
            echo $i . '.' . $item['phone'] . "\n";
            //echo  $content . "\n";
            $ret = Sms::SendSms($item['phone'], $content, Sms::CHANNEL_ZLZX, '778');
            print_r($ret);
            $i++;
        }
    }

    /**
     * 指联在线-余额查询测试
     */
    public function actionTestBalanceZLZX()
    {
        $ret = Sms::Balance(Sms::CHANNEL_ZLZX);
        var_dump($ret);
        exit;
    }

    /**
     * 定时推送消息
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-05-09 10:00:00
     * @uses php yiic.php sms PushMessage
     */
    public function actionPushMessage()
    {
    	echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
        //查询发送列表数据
        $status = 0;
        $type = 'cmd';
        $current = date("Y-m-d H:i:s");
        $sql = "SELECT id,client_id,content,type,level,driver_id FROM t_push_message WHERE `status` = :status AND type <> :type AND pre_send_time<=:current ORDER BY id ASC LIMIT 200";
        $command = Yii::app()->dbreport->createCommand($sql);
        $command->bindParam(":status", $status);
        $command->bindParam(":current", $current);
        $command->bindParam(":type", $type);
        $push_message_list = $command->queryAll();
        //查询发送列表数据 END

        //更新及发送消息
        $update_sql = "update t_push_message set `status`=:status WHERE `id` = :id ";
        $i = 1;
        $time = time();
        foreach ($push_message_list as $item) {
            echo $i . '.' . $item['driver_id'] . "\n";
            $params = array(
                'type' => $item['type'], //类型 order订单  status订单状态 msg_driver司机客户端消息 msg_customer
                //notice_driver公告司机端  notice_customer公告客户端
                'content' => $item['content'],
                'level' => $item['level'], //级别
                'driver_id' => $item['driver_id'],
                'created' => date('Y-m-d H:i:s', $time),
            );
            if (IGtPush::TYPE_CMD == $item['type']) {
                $params['message'] = $item['content'];
                $params['offline_time'] = 3 * 3600;
            }
            
            //add by yangzhi
            $params['_message_type_'] = 'timing_push_message';
            
            $send_ret = PushMessage::model()->organize_message_push($params);
            print_r($send_ret);
            $change_status = Yii::app()->dbreport->createCommand($update_sql);
            $new_status = 1;
            $change_status->bindParam(":status", $new_status);
            $change_status->bindParam(":id", $item['id']);
            $change_status->execute();
            $change_status->reset();
            $i++;
        }
        echo "----------".date('Y-m-d H:i:s')."---job end------\n";
        //更新及发送消息 END
    }

    /**
     * 定时添加指令发送记录
     * @param int $city_id
     * @uses php yiic.php Sms InsertPushCmd
     */
    public function actionInsertPushCmd()
    {
    	echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
        //return true;
        $hour = date('H', time());
        $city = Dict::items('city');
        //$city_ids = array(1 , 2 , 3 , 4 , 5 , 6 , 7 , 8);
        $city_ids = array(1);
        if (in_array($hour, array('19', '21', '01'))) {
            foreach ($city_ids as $city_id) {
                $mark = 0;
                $drivers = Driver::model()->getDrivers($city_id, $mark);
                $clients = PushMessage::model()->getClientsByDrivers($drivers);
                $data = array();
                $data['pre_send_time'] = date('Y-m-d H:i:s', time());
                $data['content'] = 'dis_call_fw';
                $data['type'] = 'cmd';
                $data['city_id'] = $city_id;
                $data['level'] = 1;
                $data['version'] = 'driver';
                $data['user_id'] = 130;
                $result = PushMessage::model()->insertRecord($clients, $data);
                print_r($result);
            }
        }
        return $result;
        echo "----------".date('Y-m-d H:i:s')."---job end------\n";
    }

    /**
     * 定时推送消息
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-05-09 10:00:00
     * @uses php yiic.php sms PushCmd
     */
    public function actionPushCmd()
    {
        //查询发送列表数据
        return true;
        $status = 0;
        $type = 'cmd';
        $current = date("Y-m-d H:i:s");
        $sql = "SELECT id,client_id,content,type,level,driver_id FROM t_push_message WHERE `status` = :status  AND `type` = :type AND `pre_send_time` <= :current  ORDER BY id ASC LIMIT 200";
        $command = Yii::app()->dbreport->createCommand($sql);
        $command->bindParam(":status", $status);
        $command->bindParam(":current", $current);
        $command->bindParam(":type", $type);
        $push_message_list = $command->queryAll();
        //查询发送列表数据 END

        //更新及发送消息
        $update_sql = "update t_push_message set `status`=:status WHERE `id` = :id ";
        $i = 1;
        $time = time();
        foreach ($push_message_list as $item) {
            echo $i . '.' . $item['driver_id'] . "\n";
            $params = array(
                'type' => $item['type'],
                'level' => $item['level'], //级别
                'driver_id' => $item['driver_id'],
                'message' => $item['content'],
                'offline_time' => 3 * 3600,
            );
            $send_ret = PushMessage::model()->PushCmd($params);
            print_r($send_ret);
            $change_status = Yii::app()->dbreport->createCommand($update_sql);
            $new_status = 1;
            $change_status->bindParam(":status", $new_status);
            $change_status->bindParam(":id", $item['id']);
            $change_status->execute();
            $change_status->reset();
            $i++;
        }
        //更新及发送消息 END
    }

    /**
     * 个推消息推送失败转成短信发送订单信息
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-05-10
     * @uses php yiic.php sms GetuiMoveSms
     */
    public function actionGetuiMoveSms()
    {
        $start_time = date("Y-m-d H:i:s", time() - 600);
        $end_time = date("Y-m-d H:i:s", time() - 120);
        $flag = 1;

        //TODO 分页
        $push_message_list = Yii::app()->dbreport->createCommand()
            ->select('*')
            ->from('t_message_log')
            ->where('created BETWEEN :start_time AND :end_time AND flag = :flag AND type IN (:type_order_detail , :type_msg_leader)', array(
                ':start_time' => $start_time,
                ':end_time' => $end_time,
                ':flag' => $flag,
                ':type_order_detail' => IGtPush::TYPE_ORDER_DETAIL,
                ':type_msg_leader' => IGtPush::TYPE_MSG_LEADER,
            ))->queryAll();
        foreach ($push_message_list as $item) {
            echo 'push:' . $item['queue_id'] . ' ' . $item['driver_id'] . "\n";
            $test_driver_ids = Common::getCallOrderAutoTestDriverIds();
            if (in_array($item['driver_id'], $test_driver_ids)) {
//				$result = AutoOrder::model()->GetuiMoveToSms($item['queue_id'], $item['driver_id'] , $item['type']);
                echo "测试工号不做任何处理\n";
            } else {
                $result = IGtPush::model()->GetuiMoveToSms($item['queue_id'], $item['driver_id'], $item['type']);
            }
        }
    }

    /**
     * 司机解锁--根据message_log中状态不为3 并且类型为order
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-05-15
     */
    public function actionDriverUnlock()
    {
        $flag = 3;
        $start_time = date("Y-m-d H:i:s", time() - 1200);
        $end_time = date("Y-m-d H:i:s", time() - 60);
        $push_message_list = Yii::app()->dbreport->createCommand()
            ->select('*')
            ->from('t_message_log')
            ->where('created between :start_time AND :end_time AND flag <> :flag AND type = :type', array(
                ':start_time' => $start_time,
                ':end_time' => $end_time,
                ':flag' => $flag,
                ':type' => IGtPush::TYPE_ORDER,
            ))->queryAll();
        foreach ($push_message_list as $message) {
            echo 'driver_id:' . $message['driver_id'] . "\n";
            //删除派单队列
            QueueDispatchDriver::model()->delete($message['driver_id']);
        }
    }

    /**
     * 高德活动，优惠券绑定 $code = '5eWtYQ8g';
     * @author bidong 2013-09-16
     */
    public function actionAutonaviSMS()
    {
        $title = '高德活动，优惠券绑定';
        echo Common::jobBegin($title);

        $offset = 0;
        $pagesize = 200;
        while (true) {
            echo Common::jobBegin("开始取数据");
            $sms_list = SmsMo::model()->getAutoNaviSMS($pagesize, $offset);
            echo Common::jobEnd("结束取数据");
            if ($sms_list) {
                $offset += $pagesize;
                foreach ($sms_list as $sms) {
                    $id = $sms['id'];
                    $phone = $sms['sender'];
                    //绑定优惠券
                    echo Common::jobBegin("开始绑定优惠券:$phone");
                    CustomerBonus::model()->bonusOldCode($phone , 0);
                    echo Common::jobEnd("优惠券绑定完成:$phone");
                    //绑定成功修改状态
                    echo Common::jobBegin("开始更新状态");
                    Yii::app()->db->createCommand()->update('t_sms_mo', array('status' => 1,'update_time'=>date('Y-m-d H:i:s')), 'id=:id',array(':id'=>$id));
                    echo Common::jobEnd("完成更新状态");

                    echo 'phone：' . $phone . "\r\n";
                }
            } else {
                break;
            }
        }
        echo Common::jobEnd($title);

    }

    /**
     * send message to client(order in 2 days)
     * @author aiguoxin 2014-04-08
     */
    public function actionPushUncommentedOrderMsg()
    {
        $title = 'send unCommented order message to customer';
        echo Common::jobBegin($title);

        $before_yesterday = date("Y-m-d H:i:s",strtotime("-2 day"));
        $today = date("Y-m-d");
        $before_yesterday_format = date("Ymd",strtotime("-2 day"));
        //collecion order data -> add order log
        echo 'start to collection after '.$before_yesterday.' success order data...'."\r\n";
        $max = 0;
        while (true) {
            $sql = "select order_id,order_date,phone FROM t_order 
            WHERE order_date>:order_date AND status=1 AND order_id > :max ORDER BY order_id LIMIT 200";
            $command = Order::getDbReadonlyConnection()->createCommand($sql);
            $command->bindParam(":order_date", $before_yesterday_format);
            $command->bindParam(":max",$max);
            $order_list = $command->queryAll();
            if($order_list){
                foreach ($order_list as $order) {
                    $max = ( $max > $order['order_id'] ) ? $max : $order['order_id'];
                    $comment_status = CommentSms::model()->getCommandSmsByOrderId($order['order_id']);
                    $comment_status = empty($comment_status) ? 0 : 1;
                    $orderCommentLog = array(
                        'order_id' => $order['order_id'],
                        'order_date' => $order['order_date'],
                        'phone' => $order['phone'],
                        'comment_status' => $comment_status
                        );
                    try{
                        OrderCommentLog::model()->addOrderCommentLog($orderCommentLog);
                    }catch(Exception $e){
                        echo "order_id=".$order['order_id']." has been added \r\n";
                        continue 1;    
                    }
                }
            }else{
                break;
            }
        }
        echo 'collection after '.$before_yesterday.' success order data finish'."\r\n";

        echo 'start to handle after '.$before_yesterday.' unCommented order data...'."\r\n";
        $min=PHP_INT_MAX;
        $comment_status=0;
        $reason="";
        $message = "您还有未评价的订单，快去给他们提提建议吧";
        while (true) {
            echo 'order_date='.$before_yesterday.',comment_status='.$comment_status.',min='.$min."\r\n";
            $sql = "SELECT id,order_id,notice_phone FROM t_order_comment_log 
            WHERE order_date>:order_date AND comment_status = :comment_status AND id<:min ORDER BY id DESC LIMIT 200";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(":comment_status", $comment_status);
            $command->bindParam(":order_date", $before_yesterday);
            $command->bindParam(":min",$min);
            $order_id_list = $command->queryAll();
            echo 'unCommented order  count='.count($order_id_list)."\r\n";

            if ($order_id_list) {
                foreach ($order_id_list as $order) {
                    $min = ( $min < $order['id'] ) ? $min : $order['id'];
                    //remove the order which commented in send notice period
                    $comment_status = CommentSms::model()->getCommandSmsByOrderId($order['order_id']);
                    if (!empty($comment_status)) {
                        $reason = 'order='.$order['order_id'].' has been commented just now!';
                        try{
                            OrderCommentLog::model()->updateNoticeStatusFail($order['order_id'],$reason);
                        }catch(Exception $e){
                            echo "Exception=".$e."\r\n";
                            continue 1;    
                        }
                        continue 1;
                    }

                    $phone = $order['notice_phone'];
                    if(empty($phone)){
                        $reason = 'phone='.$phone.' is not exist!';
                        try{
                            OrderCommentLog::model()->updateNoticeStatusFail($order['order_id'],$reason);
                        }catch(Exception $e){
                            echo "Exception=".$e."\r\n";
                            continue 1;    
                        }
                        continue 1;
                    }
                    if(!OrderCommentLog::model()->canNotice($phone, $today,$order['id'])){
                        $reason = 'phone='.$phone.' today has benn sent once!';
                        try{
                            OrderCommentLog::model()->updateNoticeStatusFail($order['order_id'],$reason);
                        }catch(Exception $e){
                            echo "Exception=".$e."\r\n";
                            continue 1;    
                        }
                        continue 1;
                    }

                    //find user client_id
                    $customer_client = CustomerClient::model()->getByPhoneAndLast($phone);
                    if(empty($customer_client)){
                        $reason = 'phone='.$phone.' customer_client can not be found!';
                        try{
                            OrderCommentLog::model()->updateNoticeStatusFail($order['order_id'],$reason);
                        }catch(Exception $e){
                            echo "Exception=".$e."\r\n";
                            continue 1;    
                        }
                        continue 1; 
                    }
                    $client_id = $customer_client['client_id'];
                    
                    //find user uncommented order number
                    $num = OrderCommentLog::model()->getUncommentedOrderNum($phone,$before_yesterday);
                    //Android use getui
                    if($customer_client['type'] == 1){
                        echo "use getui service\r\n";
                        $params = array(
                        'content' => $message,
                        'orderId' => $order['order_id'],
                        'orderNum' => $num
                            );
                        $content = PushMsgFactory::model()->orgPushMsg($params,PushMsgFactory::TYPE_MSG_CUSTOMER);
                        try{
                            $result = EPush::model('customer')->send($client_id,$content);                        
                            if ($result['result']!='ok') {
                                $reason = 'phone='.$phone.',use getui service failed!';
                                OrderCommentLog::model()->updateNoticeStatusFail($order['order_id'],$reason);
                                continue 1; 
                            }
                        }catch(Exception $e){
                            echo "Exception=".$e."\r\n";
                            continue 1;    
                        }
                    }else if($customer_client['type'] == 0){
                    //ios use APNS
                        echo "use iphone service\r\n";
                    //see http://wiki.edaijia.cn/dwiki/doku.php?id=push_%E5%8D%8F%E8%AE%AE%E5%AE%9A%E4%B9%89
                        $params = array(
                            'message' => $message,
                            'orderId' => $order['order_id'],
                            'badge' => 1,
                            'type' => '2',
                            'sound' => 'ping1',
                            'orderNum' =>$num
                            );
                        $applePush = new ApplePush;
                        try{
                            $applePush->sendMsg($customer_client['client_id'],$params);
                        }catch(Exception $e){
                            $reason = 'phone='.$phone.' use ApplePush failed!reason='.$e;
                            try{
                                OrderCommentLog::model()->updateNoticeStatusFail($order['order_id'],$reason);
                            }catch(Exception $e){
                                echo "Exception=".$e."\r\n";
                                continue 1;    
                            }
                            continue 1;
                        }
                    }else {
                            $reason = 'phone='.$phone.',type='.$customer_client['type'].' is not exist!';
                            try{
                                OrderCommentLog::model()->updateNoticeStatusFail($order['order_id'],$reason);
                            }catch(Exception $e){
                                echo "Exception=".$e."\r\n";
                                continue 1;    
                            }
                            continue 1; 
                    }
                    //send ok, update send status
                    try{
                        OrderCommentLog::model()->updateNoticeStatusOk($order['order_id'],$phone);
                    }catch(Exception $e){
                        echo "Exception=".$e."\r\n";
                        continue 1;    
                    }
                }
            } else {
                break;
            }
        }
        echo 'handle after '.$before_yesterday.' unCommented order data finish'."\r\n";;
        echo Common::jobEnd($title);
    }

    /**
    ** send message to apple user by phone
    */
    public function actionSend2Apple($client_id){
        $message="您还有未评价的订单，快去给他们提提建议吧";
        $orderNum = 1;
        $params = array(
            'message' => $message,
            'orderId' => 1,
            'badge' => 1,
            'type' => '2',
            'sound' => 'ping1',
            'orderNum' => 1
            );
        $applePush = new ApplePush;
        $applePush->sendMsg($client_id,$params);
    }

    /**
    ** send message to apple user by getui for andriod
    */
    public function actionSend2Andriod($client_id){
        $message="您还有未评价的订单，快去给他们提提建议吧";
        $orderNum = 1;
        $params = array(
            'content' => $message,
            'orderId' => 1,
            'orderNum' => 1
            );
        $content = PushMsgFactory::model()->orgPushMsg($params,PushMsgFactory::TYPE_MSG_CUSTOMER);
        $result = EPush::model('customer')->send($client_id,$content);  
        if ($result['result']=='ok') {
            echo 'send ok';
        }else{
            echo "send fail";
            print_r($result);
        }
    }

    /**
    * push msg added by aiguoxin
    */
    public function actionSendUncommentedOrderMsg(){
        $title = 'send unCommented order message to customer';
        echo Common::jobBegin($title);
        $before_yesterday = date("Y-m-d H:i:s",strtotime("-2 day"));
        $before_yesterday_format = date("Ymd",strtotime("-2 day"));
        $today = date("Y-m-d");
        echo 'start to handle after '.$before_yesterday.' unCommented order data...'."\r\n";
        $min=PHP_INT_MAX;
        $comment_status=0;
        $reason="";
        $message = "您还有未评价的订单，快去给他们提提建议吧";
        while (true) {
            echo 'order_date='.$before_yesterday.',comment_status='.$comment_status.',min='.$min."\r\n";
            $sql = "SELECT id,order_id,notice_phone FROM t_order_comment_log 
            WHERE order_date>:order_date AND comment_status = :comment_status AND id<:min ORDER BY id DESC LIMIT 200";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(":comment_status", $comment_status);
            $command->bindParam(":order_date", $before_yesterday);
            $command->bindParam(":min",$min);
            $order_id_list = $command->queryAll();
            echo 'unCommented order  count='.count($order_id_list)."\r\n";

            if ($order_id_list) {
                foreach ($order_id_list as $order) {
                    $min = ( $min < $order['id'] ) ? $min : $order['id'];
                    //remove the order which commented in send notice period
                    $comment_status = CommentSms::model()->getCommandSmsByOrderId($order['order_id']);
                    if (!empty($comment_status)) {
                        $reason = 'order='.$order['order_id'].' has been commented just now!';
                        try{
                            OrderCommentLog::model()->updateNoticeStatusFail($order['order_id'],$reason);
                        }catch(Exception $e){
                            echo "Exception=".$e."\r\n";
                            continue 1;    
                        }
                        continue 1;
                    }

                    $phone = $order['notice_phone'];
                    if(empty($phone)){
                        $reason = 'phone='.$phone.' is not exist!';
                        try{
                            OrderCommentLog::model()->updateNoticeStatusFail($order['order_id'],$reason);
                        }catch(Exception $e){
                            echo "Exception=".$e."\r\n";
                            continue 1;    
                        }
                        continue 1;
                    }
                    if(!OrderCommentLog::model()->canNotice($phone, $today,$order['id'])){
                        $reason = 'phone='.$phone.' today has benn sent once!';
                        try{
                            OrderCommentLog::model()->updateNoticeStatusFail($order['order_id'],$reason);
                        }catch(Exception $e){
                            echo "Exception=".$e."\r\n";
                            continue 1;    
                        }
                        continue 1;
                    }

                    //find user client_id
                    $customer_client = CustomerClient::model()->getByPhoneAndLast($phone);
                    if(empty($customer_client)){
                        $reason = 'phone='.$phone.' customer_client can not be found!';
                        try{
                            OrderCommentLog::model()->updateNoticeStatusFail($order['order_id'],$reason);
                        }catch(Exception $e){
                            echo "Exception=".$e."\r\n";
                            continue 1;    
                        }
                        continue 1; 
                    }
                    $client_id = $customer_client['client_id'];
                    
                    //find user uncommented order number
                    $num = OrderCommentLog::model()->getUncommentedOrderNum($phone,$before_yesterday);
                    //Android use getui
                    if($customer_client['type'] == 1){
                        echo "use getui service\r\n";
                        $params = array(
                        'content' => $message,
                        'orderId' => $order['order_id'],
                        'orderNum' => $num
                            );
                        $content = PushMsgFactory::model()->orgPushMsg($params,PushMsgFactory::TYPE_MSG_CUSTOMER);
                        try{
                            $result = EPush::model('customer')->send($client_id,$content);                        
                            if ($result['result']!='ok') {
                                $reason = 'phone='.$phone.',use getui service failed!';
                                OrderCommentLog::model()->updateNoticeStatusFail($order['order_id'],$reason);
                                continue 1; 
                            }
                        }catch(Exception $e){
                            echo "Exception=".$e."\r\n";
                            continue 1;    
                        }
                    }else if($customer_client['type'] == 0){
                    //ios use APNS
                        echo "use iphone service\r\n";
                    //see http://wiki.edaijia.cn/dwiki/doku.php?id=push_%E5%8D%8F%E8%AE%AE%E5%AE%9A%E4%B9%89
                        $params = array(
                            'message' => $message,
                            'orderId' => $order['order_id'],
                            'badge' => 1,
                            'type' => '2',
                            'sound' => 'ping1',
                            'orderNum' =>$num
                            );
                        $applePush = new ApplePush;
                        try{
                            $applePush->sendMsg($customer_client['client_id'],$params);
                        }catch(Exception $e){
                            $reason = 'phone='.$phone.' use ApplePush failed!reason='.$e;
                            try{
                                OrderCommentLog::model()->updateNoticeStatusFail($order['order_id'],$reason);
                            }catch(Exception $e){
                                echo "Exception=".$e."\r\n";
                                continue 1;    
                            }
                            continue 1;
                        }
                    }else {
                            $reason = 'phone='.$phone.',type='.$customer_client['type'].' is not exist!';
                            try{
                                OrderCommentLog::model()->updateNoticeStatusFail($order['order_id'],$reason);
                            }catch(Exception $e){
                                echo "Exception=".$e."\r\n";
                                continue 1;    
                            }
                            continue 1; 
                    }
                    //send ok, update send status
                    try{
                        OrderCommentLog::model()->updateNoticeStatusOk($order['order_id'],$phone);
                    }catch(Exception $e){
                        echo "Exception=".$e."\r\n";
                        continue 1;    
                    }
                }
            } else {
                break;
            }
        }
        echo 'handle after '.$before_yesterday.' unCommented order data finish'."\r\n";
    }

    /**
     * 给预约了第二天路考的司机发提醒短信
     * yiic.php sms RoadExamRemindSms prod
     *      如果没有 prod 只打印手机号和内容到屏幕，不真发短信
     */
    public function actionRoadExamRemindSms()
    {
        $date = date("Ymd", strtotime("+1 days"));
        $date_str = date("n月j日", strtotime("+1 days"));
        $hours_start = array();

        //可以将城市和预约的司机一起联表查出来
        $sql = "SELECT city_name, city_id, booking_sms as address FROM t_city_config";
        $cities = Yii::app()->db_readonly->createCommand($sql)->queryAll();
        foreach($cities as $city){
            foreach(BookingExamSetting::$hours_name as $k => $v){
                list($hours_start[$k], $dummy) = BookingHoursSetting::model()->getHourStartEnd($city['city_id'], $k);
            }
            $id = $city['city_id'];
            $address = $city['address'];
            $city_name = $city['city_name'];
            $sql = "SELECT book.hours, driver.mobile FROM t_booking_exam_driver book, t_driver_recruitment driver WHERE date = {$date} AND book.city_id = {$id} AND driver.id_card = book.id_card;";
            $drivers = Yii::app()->db_readonly->createCommand($sql)->queryAll();
            foreach($drivers as $driver){
                if(empty($driver['mobile'])){
                    continue;
                }
                $start = $hours_start[$driver['hours']];
                $content = sprintf(BookingExamSetting::SMS, $city_name, $date_str . $start, $address);
                global $argv;
                $env = 'test';
                if(isset($argv[3])){
                    $env = $argv[3];
                }
                if('prod' == $env){
                    Sms::SendSMS($driver['mobile'], $content);
                }else{
                    echo "==== 测试脚本 ====\n";
                    echo "{$driver['mobile']}\t{$content}\n";
                }
            }
        }
    }

}
