<?php
/**
 * Created by JetBrains PhpStorm.
 * author: mtx
 * Date: 13-6-9
 * Time: 下午2:32
 */
Yii::import('application.models.schema.report.ReportFsAccountRp');
Yii::import('application.models.schema.report.ReportFsAccountTag');
Yii::import('application.models.redis.*');
Yii::import('application.models.schema.customer.*');

class mtxCommand extends CConsoleCommand
{
    /**
     * @auther mengtianxue
     * php yiic.php mtx Sc
     */
    public function actionSc()
    {
        $driver_id = 'BJ9017';
        $driver = DriverStatus::model()->get($driver_id);
        print_r($driver);
    }


    /**
     * @auther mengtianxue
     * php yiic.php mtx CeShi
     */
    public function actionCeShi()
    {
        $order = Order::model()->getOrderById('1000000336');
        if (empty($order)) {
            $ret = array(
                'code' => 1,
                'message' => '请重新登录'
            );
            echo json_encode($ret);
            return;
        }

        if (strtoupper($order['driver_id']) != strtoupper('BJ9020')) {
            $ret = array(
                'code' => 1,
                'message' => '请重新登录'
            );
            echo json_encode($ret);
            return;
        }

        $orderLog = OrderLog::model()->getOrderLogByOrderId('1000000336');
        $log = array();
        foreach ($orderLog as $key => $value) {
            $value['created'] = date('Y-m-d H:i', $value['created']);
            $log[$key] = $value;
        }
        unset($order['user_id']);
        unset($order['car_id']);
        unset($order['driver']);
        unset($order['imei']);
        unset($order['driver_id']);
        unset($order['driver_phone']);
        unset($order['city_id']);
        unset($order['call_type']);
        unset($order['order_date']);
        unset($order['reach_time']);
        unset($order['reach_distance']);
        $order['phone'] = substr_replace($order['phone'], '****', 3, 4);
        $order['call_time'] = date('Y-m-d H:i', $order['call_time']);
        $order['booking_time'] = date('Y-m-d H:i', $order['booking_time']);
        $order['start_time'] = date('Y-m-d H:i', $order['start_time']);
        $order['end_time'] = date('Y-m-d H:i', $order['end_time']);


//$favorable = Order::model()->getOrderFavorable($order['phone'], $order['booking_time'], $order['source'], $params['order_id']);
//$order['type'] = $favorable['code'];
//if ($favorable['code'] != 0) {
//    $order['card'] = $favorable['card'];
//    $order['money'] = $favorable['money'];
//}

        $favorable = Order::model()->getOrderFavorable($order['phone'], $order['booking_time'], $order['source'], '1000000062');


        $order['type'] = $favorable['code'];
        $order['card'] = $favorable['card'];
        $order['money'] = $favorable['money'] + $favorable['user_money'];


        $orderExt = OrderExt::model()->getPrimary($order['order_id']);
        if (!empty($orderExt)) {
            $order['waiting_time'] = $orderExt['wait_time'];
            $order['remark'] = $orderExt['mark'];
        } else {
            $order['waiting_time'] = 0;
            $order['remark'] = '';
        }

        switch ($order['source']) {
            case Order::SOURCE_CLIENT:
                $order['source'] = '客户直接呼叫';
                break;
            case Order::SOURCE_CLIENT_INPUT:
                $order['source'] = '客户直接呼叫补单';
                break;
            case Order::SOURCE_CALLCENTER:
                $order['source'] = '呼叫中心派单';
                break;
            case Order::SOURCE_CALLCENTER_INPUT:
                $order['source'] = '呼叫中心派单补单';
                break;
        }

        switch ($order['status']) {
            case Order::ORDER_READY:
                $order['status'] = '未报单';
                break;
            case Order::ORDER_CANCEL:
                $order['status'] = '已销单';
                $order['cancel_type'] = Dict::item('cancel_type', $order['cancel_type']);
                break;
            case Order::ORDER_COMFIRM:
                $order['status'] = '销单待审核';
                $order['cancel_type'] = Dict::item('cancel_type', $order['cancel_type']);
                break;
            case Order::ORDER_COMPLATE:
                $order['status'] = '已报单';
                break;
            case Order::ORDER_NOT_COMFIRM:
                $order['status'] = '拒绝销单';
                break;
        }

        $ret = array(
            'code' => 0,
            'detail' => $order,
            'log' => $log,
            'message' => '读取成功');
        echo json_encode($ret);
        return;
    }

    /**
     * 生成面额为200元的人保卡号密码
     * author mengtianxue
     * php yiic.php mtx VipCard
     */
    public function actionVipCard()
    {
        for ($i = 1; $i <= 6000; $i++) {
            $params = array();
            $params['id'] = str_pad($i, 6, 0, STR_PAD_LEFT);
            $params['pass'] = rand(100000, 999999);
            $params['money'] = 200;
            $params['status'] = 1;
            $params['saled_by'] = '系统默认激活';
            $params['atime'] = time();
            $vip_card = new VipCard;
            $vip_card->attributes = $params;
            $vip_card->insert();
            echo $i . "\n";
        }
    }


    public function actionTest()
    {
        $file = '/';

        //获取后缀
        $extend = explode('.', $file['name']);
        $lastExtend = count($extend) - 1;
        $file_type = $extend[$lastExtend];

        if ($file_type != 'csv') {
            Yii::app()->clientScript->registerScript('alert', 'alert("文件格式不对,请重新上传")');
        } else {
            $driverBankResult = new DriverBankResult();
            $handle = fopen($file['tmp_name'], "r");
            $num = 0;
            while ($data = fgetcsv($handle, 0, ',')) {
                $num++;
                if ($num == 1) {
                    continue;
                }
                foreach ($data as $k => $val) {
//			    		$encode = mb_detect_encoding($val);
                    $data[$k] = trim(mb_convert_encoding($val, 'UTF-8', 'gb2312'));
                }
                $driverBankResult->driverBankSave($data);
            }
            fclose($handle);
            Yii::app()->clientScript->registerScript('alert', 'alert("上传成功")');
        }
    }

    /**
     * 司机投诉客户保存
     * @param $params
     * @return bool
     * author mengtianxue
     */
    public function driver_complain($params)
    {
        $order = Order::model()->getOrderById($params['order_id']);

        if (!empty($order) && $order['driver_id'] == $params['driver_id']) {
            $complaintArr = array();
            $complaintArr['order_id'] = $order['order_id'];
            $complaintArr['driver_user'] = $order['driver_id'];
            $complaintArr['customer_name'] = $order['name'];
            $complaintArr['city'] = $order['city_id'];
            $complaintArr['customer_phone'] = $order['phone'];
            $complaintArr['order_type'] = $order['status']; //1为报单
            $complaintArr['complaint_type'] = $params['type'];
            $complaintArr['complaint_content'] = $params['content'];
            $complaintArr['driver_time'] = $order['start_time']; //只记录出发时间
            $complaintArr['complaint_status'] = 0;
            $complaintArr['created'] = time();
            return DriverComplaint::model()->saveDriverComplaint($complaintArr);
        }
        return false;
    }

    /**
     * 用户余额和余额log记录
     * author mengtianxue
     * php yiic.php mtx AddDriverBalance
     */
    public function actionAddDriverBalance()
    {
        $driver = Driver::model()->findAll('mark < 2');
        foreach ($driver as $list) {
            $driverBalance = array();
            $driverBalance['user'] = $list->user;
            $driverBalance['cast'] = EmployeeAccount::model()->getDriverBalances($list->user);
            $driverBalance['order_id'] = 0;
            $driverBalance['type'] = EmployeeAccount::TYPE_INFOMATION;
            DriverBalance::model()->updateBalance($driverBalance, $driverBalance['type']);
            echo $list->user . "\n";
        }
    }

    /**
     * 每天三点钟结账
     * author mengtianxue
     * php yiic.php mtx OrderSettleDaily --date=2013-07-08
     */
    public function actionOrderSettleDaily($date = null)
    {
        OrderSettlement::model()->orderSettleDaily($date);
    }

    /**
     * 测试订单结账方法
     * author mengtianxue
     * php yiic.php mtx OrderTest --order_id=3143867
     */
    public function actionOrderTest($order_id)
    {
        OrderSettlement::model()->orderSettle($order_id);
    }

    public function actionEmployeeAccountTest()
    {
        EmployeeAccount::$table_name = '201307';
        $xinxi = EmployeeAccount::model()->findAll('order_id = :order_id', array(':order_id' => '1169564'));

        print_r(count($xinxi) . "\n");

    }

    public function actionNowMonthBill($driver_id)
    {
        OrderSettlement::model()->getDriverNowMonth($driver_id);
    }


    public function actionCity()
    {
        $city = Dict::items('city');
        $city_id = array_keys($city);
        print_r($city_id);

    }

    public function actionDriverList($cast)
    {
        $city = ',1 3, 5, 6';
        Driver::model()->DriverLists($city, $cast);

        $city = array(4, 7);
        Driver::model()->DriverLists($city, $cast);

    }


    public function actionOrderAgain($order_id)
    {
        $order = Yii::app()->db->createCommand()
            ->select("*")
            ->from('t_order')
            ->where('order_id = :order_id', array(':order_id' => $order_id))
            ->queryRow();
        if (!empty($order))
            OrderSettlement::model()->delEmployeeAccount($order);
    }

    /**
     * author mengtianxue
     * php yiic.php mtx DriverBalance
     */
    public function actionDriverBalance($user)
    {
        $balance = EmployeeAccount::model()->getDriverAmount($user);
        print_r($balance);
    }

    /**
     * author mengtianxue
     * php yiic.php mtx AccountAgainInvoicing
     */
    public function actionAccountAgainInvoicing()
    {
        $order = Yii::app()->db_finance->createCommand()
            ->select('*,count(1) AS cc')
            ->from('t_employee_account_201308')
            ->where('created < unix_timestamp("2013-08-02 11:40:00")')
            ->group('order_id')
            ->having('cc > 5 and cc < 9')
            ->queryAll();

        foreach ($order as $list) {
            OrderSettlement::model()->orderSettle($list['order_id'], 1);
            echo $list['order_id'] . "\n";

        }

    }


    /**
     * 重结信息费
     * author mengtianxue
     * php yiic.php mtx SettleAgainInformation --date=2013-07-21
     */
//    public function actionSettleAgainInformation($date)
//    {
//        $start_time = date('Y-m-d 23:00:00', strtotime($date));
//        $end_time = date('Y-m-d 07:00:00', strtotime($date) + 86400);
//
//        //获取一天结错的订单
//        $account = Yii::app()->db->createCommand()
//            ->select('*')
//            ->from('t_order')
//            ->where('call_time > unix_timestamp(:call_time_start) AND call_time < unix_timestamp(:call_time_end) AND city_id IN ( 3, 5, 6 ) AND STATUS IN ( ,1 4 ) and cast = 10',
//            array(':call_time_start'=> $start_time,':call_time_end' => $end_time))
//            ->queryAll();
//
//        foreach($account as $list){
//
//           $account = $this->getInformationByOrderId($list['order_id']);
//            if($account){
//                //退还原来的金额
//                $this->InformationReturn($account);
//
//                //重新结账
//                $this->InformationAgain($list);
//                echo "重结 ";
//
//            }
//            echo $list['order_id']."\n";
//        }
//    }


    /**
     * 获取要返还的信息费
     * @param $order_id
     * @return int
     * author mengtianxue
     */
    public function getInformationByOrderId($order_id)
    {
        $account = Yii::app()->db_finance->createCommand()
            ->select('*')
            ->from('t_employee_account_201307')
            ->where('order_id = :order_id', array(':order_id' => $order_id))
            ->queryRow();
        return $account;
    }

    /**
     * 信息费返还
     * @param $order_account
     * author mengtianxue
     */
    public function InformationReturn($order_account)
    {
        // 把退单金额还给用户
        $params = array(
            'user' => $order_account['user'],
            'type' => $order_account['type'],
            'order_id' => $order_account['order_id'],
            'cast' => $order_account['cast'],
            'comment' => '重新结账，退' . $order_account['comment'],
            'order_date' => time(),
        );
        OrderSettlement::model()->insertAccount($params);
    }

    /**
     * 信息费重结
     * @param $order
     * author mengtianxue
     */
    public function InformationAgain($order)
    {
        $params = array();
        $params['type'] = EmployeeAccount::TYPE_ORDER_INFOMATION;
        $params['user'] = $order['driver_id'];
        $params['order_id'] = $order['order_id'];
        $params['order_date'] = $order['created'];
        $params['comment'] = '重新结账，扣信息费 单号：' . $order['order_number'];

        $cast = Common::cast($order);
        $params['cast'] = $cast;

        // 订单信息费写回order表中cast字段
        Order::model()->updateByPk($order['order_id'], array(
            'cast' => $cast
        ));
        OrderSettlement::model()->insertAccount($params);
    }


    /**
     * 发送点评短信
     * 每天15点30分发送（视回复数调整）
     *
     */
    public function actionDianping($date = null)
    {
        if ($date) {
            $end_time = date('Y-m-d 07:00:00', strtotime($date));
            $begin_time = date('Y-m-d 07:00:00', strtotime($date) - 86400);
        } else {
            $end_time = date('Y-m-d 07:00:00', time());
            $begin_time = date('Y-m-d 07:00:00', time() - 86400);
        }

        $offset = 0;
        $pagesize = 50;
        $i = 0;
        $criteria = new CDbCriteria();
        $criteria->condition = 'call_time between :begin_time and :end_time';
        $criteria->limit = $pagesize;
        $criteria->params = array(
            ':begin_time' => strtotime($begin_time),
            ':end_time' => strtotime($end_time));
        while (true) {
            $criteria->offset = $offset;
            $orders = Order::model()->findAll($criteria);
            if ($orders) {
                foreach ($orders as $order) {
                    $flag = 0;
                    //排除公司司机的电话号码
                    $ret = Driver::getDriverByPhone($order->phone);
                    if ($ret) {
                        $flag++;
                    }
                    //排除固定的电话号码
                    $ret = in_array($order->phone, Yii::app()->params['whitelist']);
                    if ($ret == true) {
                        $flag++;
                    }
                    //过滤非手机号码,1开头的11位数字
                    if (!preg_match('%^1\d{10}%', $order->phone)) {
                        $flag++;
                    }

                    //电话号码有效 报完的订单 发送短信   @author mengtianxue  2013-05-13
                    if ($flag == 0 && $order->status != 0) {
                        echo $order->phone . "\n";
                        $order_status = '0'; //报单
                        $driver = Driver::getProfile($order->driver_id);
                        if ($driver) {
                            $content_price = "";
                            if ($order->status == 4 || $order->status == 1) {
                                $content = MessageText::getFormatContent(MessageText::NEW_CUSTOMER_ORDER_COMMENT, $driver->user);
                                //'请评价昨天的e代驾司机%s。如“上车未告知起始里程、恶意多收费、虚报里程、未出示工卡”，请回复"差评+内容"，感谢您使用！';

                                //发送价格确认短息内容
                                if (empty($order->vipcard)) {
                                    $content_price = '尊敬的客户，您昨日%s使用e代驾，现金支付%s元。 如与您实际现金支付不一致，可回复“实际支付的金额+说明”进行举报。例如“100 包含小费“';
                                    $content_price = sprintf($content_price, date('H时i分', $order->booking_time), $order->price);
                                } else {
                                    $content_price = '尊敬的客户，您昨日%s使用e代驾，代驾费%s元，其中支付现金%s元。 如与您实际支付不一致，可回复“实际金额+说明”进行举报。例如"100 包含小费"';
                                    $content_price = sprintf($content_price, date('H时i分', $order->booking_time), $order->income - $order->price, $order->price);
                                }
                            } else {
                                $order_status = '1'; //销单
                                $content = MessageText::getFormatContent(MessageText::NEW_CUSTOMER_ORDER_CANCEL, $driver->user);
                                //'非常抱歉昨天的e代驾司机%s未能为您服务！是否此司机借故拒单？请给予回复"差评+内容"，感谢您的支持！';
                            }

                            $content = sprintf($content, $driver->name);
                            $i++;
                            echo $content . "\n";
                            //type:评价短信0/短信询价1
                            $data = array(
                                'sender' => $order->phone,
                                'message' => $content,
                                'type' => 0,
                                'order_id' => $order->order_id,
                                'driver_id' => $driver->user,
                                'order_status' => $order_status,
                                'imei' => $driver->imei
                            );

                            //评价短信
//                            SmsSend::commentSmsEx($data);

                            //发送价格核实短信  @author mengtianxue 2013-05-09
                            if (!empty($content_price)) {
                                $data['type'] = 1;
                                $data['order_status'] = '0';
                                $data['message'] = $content_price;
//                                SmsSend::commentSmsEx($data);
                            }
                        }
                    }
                }
                $offset += $pagesize;
            } else {
                break;
            }
        }
        echo $i . "\n";
    }

    /**
     *
     * author mengtianxue
     * php yiic.php mtx DriverInfo
     */
    public function actionDriverInfo($type = 0)
    {
        $info = Summary::model()->getCouponBonus($type);
        print_r($info);

    }

    public function actionMtx()
    {
        $bj = EmployeeAccount::model()->getYesterdayIncome(1);
        print_r($bj);
    }

    public function actionVipTrade($date = 0)
    {
        if ($date != 0) {
            $start_date = date('Y-m-d', strtotime($date));
        }
        $vip_trade_list = Yii::app()->db_finance->createCommand()
            ->select("vipcard, sum(if(type = 0,amount,0)) as amount_income,
                                    sum(if(type = ,1amount,0)) as amount_fee,
                                    sum(if(type = 2,amount,0)) as amount_card_income")
            ->from('{{vip_trade}}')
            ->where()
            ->group('vipcard')
            ->queryAll();
        if ($vip_trade_list) {
            foreach ($vip_trade_list as $vip_trade) {
                $vip = Vip::model()->getVipInfo($vip_trade['vipcard']);
            }
        }

    }


    /*-------------------------------优惠劵单元测试start-------------------------------*/
    /**
     * 优惠码状态的修改
     * author mengtianxue
     * php yiic.php mtx BonusCodeStatus --id=1 --status=1
     */
    public function actionBonusCodeStatus($id, $status)
    {
        $bonusCode = BonusCode::model()->updateStatus($id, $status);
        var_dump($bonusCode) . "\n";
    }

    /**
     * 优惠码状态的修改
     * author mengtianxue
     * php yiic.php mtx CheckedBonusCode --id=1
     */
    public function actionCheckedBonusCode($id, $status = null)
    {
        $bonusCode = BonusCode::model()->getBonusCodeById($id, $status);
        var_dump($bonusCode) . "\n";
    }

    /**
     * 优惠劵绑定
     * @param $bonus_sn
     * @param $phone
     * author mengtianxue
     * php yiic.php mtx BonusBinding --bonus_sn=0190002598 --phone=18600750103
     */
    public function actionBonusBinding($bonus_sn, $phone, $password = 0)
    {
        $bonus = BonusLibrary::model()->BonusBinding($bonus_sn, $phone, $password);
        var_dump($bonus) . "\n";
    }

    /**
     * 优惠码被订单占用
     * author mengtianxue
     * php yiic.php mtx BonusOccupancy --phone=18511663962 --status=3 --order_id=1290962
     */

    public function actionBonusOccupancy($phone, $order_id, $status)
    {
        $bonus = BonusLibrary::model()->BonusOccupancy($phone, $order_id, $status);
        var_dump($bonus) . "\n";
    }

    /**
     * 优惠码占用的优惠码使用掉
     * author mengtianxue
     * php yiic.php mtx BonusUsed --phone=18511663962 --order_id=1801747
     */

    public function actionBonusUsed($phone, $order_id)
    {
        $bonus = BonusLibrary::model()->BonusUsed($phone, $order_id);
        var_dump($bonus) . "\n";
    }


    /**
     * 优惠码绑定
     * @param $phone
     * author mengtianxue
     * php yiic.php mtx BonusOldCode --phone=18511663962
     */
    public function actionBonusOldCode($phone)
    {
        var_dump(CustomerBonus::model()->bonusOldCode($phone));
    }


    /*-------------------------------优惠劵单元测试end-------------------------------*/


    /*-------------------------------重庆短信修改start-------------------------------*/

    /**
     * author mengtianxue
     * php yiic.php mtx CongQing --date=2013-09-18
     */
    public function actionCongQing($date)
    {
        //成单的或销单审核不通过统计
        $phone_all = Yii::app()->db->createCommand()
            ->select("phone")
            ->from('t_order')
            ->where('city_id = 7 and length(phone) = 11 and left(phone, 1) = 1')
            ->group('phone')
            ->queryAll();
        foreach ($phone_all as $phone) {
            $params = array();
            $params['sms_phones'] = $phone['phone'];
            $params['content'] = '五重保障服务升级，全城最低价：全天10公里只要39！百万代驾险！安全有保障，恶意多收费三倍赔付，高端大气上档次！';
            $params['status'] = 0;
            $params['pre_send_time'] = $date . ' 17:00:00';
            $params['user_id'] = '68';
            $params['created'] = date('Y-m-d H:i:s');

            MarketSms::model()->insertMarketSms($params);
            echo $phone['phone'] . "\n";
        }

    }

    /*-------------------------------重庆短信修改后end-------------------------------*/

    /**
     * author mengtianxue
     * php yiic.php mtx PingBi
     */
    public function actionPingBi()
    {
        $str = 'BJ0170,BJ0235,BJ0242,BJ0123,BJ0719,BJ0620,BJ068,1BJ0676,BJ9000,SH0276,BJ0707,BJ0760,BJ0758,BJ0715,BJ0875,BJ9002,BJ9003,GZ0516,SH0285,GZ0054,BJ1007,SH0313,SH0355,BJ1106,BJ1178,GZ0112,BJ1280,GZ0117,GZ015,1GZ0167,BJ1705,SH0626,SZ0103,GZ0189,GZ0199,GZ0207,SH0657,BJ1783,BJ1313,SH0678,SH0710,SZ0140,SH075,1BJ2107,BJ2077,BJ2159,SH0785,GZ0222,GZ0305,BJ2179,BJ9902,GZ0255,GZ0265,SH0827,GZ030,1BJ90,11GZ0296,BJ2292,SH088,1BJ2380,BJ2372,SH0896,BJ2507,BJ2395,SH0959,BJ260,1SH0972,SH1003,SH0772,BJ990,1BJ9903,BJ9905,BJ9906,BJ9907,BJ9908,BJ9909,BJ9910,BJ99,11SH1017,BJ9913,BJ2728,BJ2756,BJ2775,BJ9915,GZ0506,SZ0186,BJ2802,BJ9203,BJ2830,GZ0396,SH1072,SZ0236,BJ2922,BJ2933,BJ2836,GZ0580,BJ2958,BJ2977,BJ3000,SH1159,SH1150,SZ0269,GZ0595,GZ0626,SZ0284,BJ3016,SH1135,SH1207,BJ3103,SH123,1BJ3,111BJ3132,BJ3120,BJ3160,BJ3153,BJ3152,SH1279,SH1276,SH129,1BJ319,1SH1312,BJ3216,BJ3270,BJ327,1BJ3233,GZ0715,SH1355,GZ0676,GZ0759,SH1333,SZ0347,GZ0773,SH1395,SH139,1BJ3393,GZ0793,BJ339,1BJ3555,BJ356,1SH1575,SH155,1SH1509,SH1508,GZ0835,GZ0829,BJ3665,BJ3638,BJ3680,GZ086,1SH1616,BJ3655,GZ0795,SH1559,BJ3708,SH169,1SH1599,SH1612,SH1675,GZ0885,SH1738,GZ0978,BJ3736,BJ3739,BJ3750,BJ3699,SH1770,SH1763,SZ0454,BJ3788,SH1793,SH1785,BJ3798,BJ3792,SH1830,GZ1110,GZ1082,SH1520,GZ1062,BJ3817,SH1802,SH180,1GZ1006,SH1839,BJ3782,SH1687,BJ3929,BJ3930,BJ5008,BJ395,1BJ3935,SH1787,SH1912,SH1909,SH1907,SH1925,SH1890,BJ5084,BJ5168,BJ5187,BJ5196,GZ0266,SH1939,GZ1230,SH1808,SH1997,BJ5216,BJ5217,BJ5259,BJ5212,GZ1212,BJ5292,NJ9008,GZ0700,GZ0706,GZ0753,GZ0769,BJ532,1BJ5336,BJ5369,SH2029,SH2052,BJ5383,BJ5388,GZ1310,BJ5552,SH2062,SH2069,SH2075,SH208,1SH2085,GZ1080,GZ1053,GZ0960,BJ5569,BJ5572,SH2117,GZ1216,SH2125,GZ0986,SH2127,GZ1182,GZ0925,GZ1005,GZ0899,GZ102,1GZ0882,GZ1135,GZ1059,GZ079,1GZ1033,GZ0987,GZ1063,GZ0920,GZ1116,GZ1023,GZ0982,GZ1327,GZ1319,GZ1316,GZ1308,GZ1300,GZ1118,BJ5690,GZ1188,BJ5692,BJ5698,GZ1206,GZ1207,BJ5725,BJ5750,BJ578,1BJ5825,SH2177,SH2182,SH2183,BJ586,1BJ5887,SH2186,SH2202,SH2213,SH2219,BJ5950,BJ5997,BJ6050,BJ6063,GZ1257,BJ9099,SZ0605,SH2227,GZ1266,GZ1296,GZ1297,GZ1322,BJ6120,BJ6123,BJ6152,BJ6187,SH2232,SH2256,SH2258,GZ1370,GZ1378,GZ1390,BJ6209,BJ6227,GZ1515,BJ6230,GZ1522,GZ1525,BJ623,1GZ1537,GZ1539,GZ1558,GZ1559,GZ1567,GZ157,1GZ1572,GZ1583,SH2300,GZ1588,BJ6289,BJ6356,SH2328,SH2338,SH2357,SH2359,BJ638,1BJ6505,BJ6522,BJ6527,GZ1592,GZ1607,BJ658,1BJ6589,BJ6599,GZ1630,GZ1637,BJ66,11SH2512,SH2516,GZ1659,BJ6650,GZ166,1GZ1673,GZ168,1GZ1678,SH2536,SH2553,SH256,1SH2562,GZ1689,SH2566,BJ6695,SH2573,SH2577,BJ6700,GZ1697,SH2590,SZ0670,GZ1700,GZ170,1GZ1715,GZ1727,GZ1736,GZ1755,GZ1760,GZ176,1GZ1780,GZ178,1SH2597,GZ1795,GZ1796,SZ0713,GZ1800,BJ6707,GZ1803,GZ1832,SH26,11BJ6752,BJ6758,BJ676,1SH2652,SH2655,BJ6805,SZ0736,SZ0738,SH2666,SH2670,GZ1853,SH2692,BJ6879,BJ6890,BJ6910,SZ0775,BJ692,1BJ6926,BJ693,1BJ6933,SH27,11SH2712,SH2713,SH272,1SH2722,BJ6938,BJ6950,BJ6963,BJ6966,BJ6965,BJ6973,BJ6976,GZ1869,GZ188,1SZ0792,GZ1898,GZ1899,SH2726,GZ1907,SZ0807,GZ192,1SZ0815,BJ6986,SZ0830,BJ70,11SZ0836,BJ7030,GZ1935,GZ1936,GZ1959,SH2797,SH2798,GZ1963,SH280,1BJ7039,GZ1966,GZ1973,GZ1975,GZ1977,BJ705,1GZ1978,BJ7066,BJ7079,BJ7092,GZ1986,GZ1989,GZ1997,BJ7100,BJ7109,BJ7118,BJ7127,BJ7128,SH2808,SZ0870,SH28,11SH2812,SH2815,SH2817,SH2819,SH2823,SH283,1GZ200,1GZ2003,GZ2005,GZ2006,GZ2009,GZ2010,GZ20,11GZ2013,GZ2015,GZ2016,GZ2017,GZ2019,GZ202,1GZ2025,GZ2028,GZ2030,GZ203,1GZ2032,GZ205,1GZ2052,GZ2053,GZ2055,SZ0890,GZ206,1GZ2062,GZ2063,GZ2065,GZ2066,GZ2067,GZ2068,GZ207,1GZ2073,GZ2070,GZ2076,GZ2077,GZ2078,GZ2079,GZ2080,BJ7157,BJ716,1BJ717,1SZ0902,BJ7177,GZ2082,GZ2083,GZ2085,GZ2087,GZ2090,GZ209,1GZ2092,GZ2093,GZ2095,GZ2097,GZ2099,GZ2100,GZ210,1GZ2102,GZ2103,GZ2106,GZ2108,GZ2110,GZ2109,GZ2,111GZ2112,GZ2113,GZ2115,GZ2116,GZ2117,GZ2118,GZ2119,GZ2120,GZ212,1GZ2122,GZ2123,GZ2125,GZ2127,GZ2130,GZ2129,GZ2132,GZ213,1GZ2133,GZ2126,GZ2137,SZ0920,BJ7182,BJ7183,BJ7188,BJ719,1BJ7198,BJ7202,BJ7206,BJ7210,BJ7212,BJ7215,BJ7218,BJ7219,BJ7220,BJ7225,BJ7226,BJ7232,BJ7233,BJ725,1BJ7255,BJ7259,BJ7260,BJ726,1BJ7262,BJ7263,BJ7265,BJ7266,SH2835,SH285,1SH2852,SH2860,SH286,1SH2862,SH2867,SH287,1SH2872,SH2873,SH2877,SZ0936,SH2882,SH2886,SH2889,GZ2138,SH2896,BJ7276,BJ7278,BJ7279,BJ7280,BJ7282,SH2897,BJ7287,BJ7288,BJ7289,BJ729,1BJ7292,BJ7295,BJ7296,BJ7297,BJ7299,BJ7298,BJ7300,GZ2139,GZ2150,GZ2152,GZ2153,GZ2156,GZ2158,GZ2157,GZ2159,GZ2160,GZ216,1GZ2163,GZ2165,GZ2166,GZ2162,GZ2168,GZ2169,GZ2167,GZ2170,GZ217,1GZ2172,GZ2175,GZ2173,GZ2176,GZ2178,GZ2179,GZ2180,GZ218,1GZ2182,GZ2183,GZ2185,BJ7305,BJ7306,BJ7307,BJ7308,BJ7309,BJ73,11BJ7312,GZ2187,GZ2188,HZ0017,HZ0020,HZ003,1HZ0033,CQ00,11HZ0068,HZ0069,HZ0085,HZ0113,HZ0129,HZ015,1HZ0185,HZ0190,HZ0195,HZ0215,HZ0217,CQ025,1HZ0223,HZ0233,CQ0212,HZ0256,CQ0290,HZ0316,HZ0347,HZ036,1HZ0377,HZ0387,CQ0417,HZ0413,HZ0430,HZ0432,HZ0448,CD0017,CD0018,CD0020,CD003,1CD0033,CD0038,CD0050,CD0055,CD0057,CD0059,CD0060,CD006,1NJ0015,CD0065,CD0068,CD0069,NJ0069,NJ007,1NJ0053,NJ0077,NJ0028,NJ0027,NJ0080,NJ008,1NJ0035,CD0089,NJ0086,WH0003,WH0005,WH0018,WH0019,XA0035,XA0018,WH0023,WH0025,XA0020,HZ0458,XA0027,XA0036,XA0013,CD0098,HZ0470,ZZ0022,HZ0466,ZZ0056,HZ0477,XA0050,XA003,1ZZ0059,ZZ006,1HZ045,1ZZ0066,CD0105,CD0106,TJ000,1ZZ0077,CD0107,WH0029,WH0052,HZ0436,XA0012,WH0056,WH0059,XA006,1XA0062,XA0059,XA0056,TJ0002,ZZ0098,ZZ0099,TJ0023,ZZ010,1ZZ0102,TJ003,1TJ0030,TJ0032,XA0006,XA0075,ZZ0113,ZZ0115,WH0073,NJ0135,WH0088,WH0089,WH009,1WH0092,TJ0039,ZZ0125,XA0083,XA007,1JN900,1JN9002,JN0006,JN0003,XA0089,CQ0635,TJ900,1WH0098,WH0102,ZZ0129,WH0105,WH0106,XA0098,WH0107,JN002,1XA0106,CD0127,TJ0009,XA0108,XA9008,TJ007,1TJ0069,XA0077,WH0115,XA0,111XA0112,JN0038,WH0118,WH0119,XA0103,XA0118,WH012,1XA0092,CD0136,XA012,1XA0125,JN0055,JN0058,TJ008,1HZ055,1TJ0033,TJ0087,TJ0089,HZ0502,JN006,1XA0127,TJ006,1TJ0122,XA0132,JN0070,HZ0578,NJ0179,TJ0036,WH0160,JN0078,CD0183,HZ0596,NJ0188,CS0007,ZZ0186,XA0149,JN0079,JN0080,CS0019,JN0083,JN0085,HZ0613,TJ0065,TJ0073,JN0086,JN0087,JN0089,HZ0637,HZ0600,HZ0640,WH017,1CQ0730,TJ0103,ZZ0210,JN0092,JN0099,JN0097,CS0059,JN0096,WH0187,QD0003,JN0105,QD0015,QD0016,JN0110,JN0,111CS0075,HZ0700,HZ0669,CS0080,ZZ0228,CQ0768,CD0233,JN0125,TJ0157,HZ0772,CS0086,ZZ0250,TJ0163,HZ0735,JN0133,HZ0782,ZZ0252,HZ0797,HZ0800,HZ0776,HZ080,1JN0136,TJ0170,TJ0172,ZZ0257,TJ0173,TJ0175,CQ0783,XA0205,ZZ0262,ZZ0263,XA0202,TJ0176,CQ0790,TJ0178,TJ0177,TJ0180,TJ0182,CD0237,CD0239,ZZ027,1TJ0185,TJ0187,TJ0186,CQ0803,TJ0189,QD0060,TJ0190,TJ019,1CS0089,CS0090,CS009,1NJ0282,NJ028,1CD0258,CD0259,CD0260,CS0095,CD026,1CQ0807,HZ0808,CD0265,CD0263,ZZ0280,JN0151';
//        $str = 'BJ6823,BJ0623,BJ6379,BJ2013,BJ1566,BJ5656,BJ5166,BJ0988,BJ6870,BJ3506,BJ3893,BJ1833,BJ6206,BJ1728,BJ1558,BJ1310,BJ5287,BJ6559,BJ5092,BJ6157,BJ5823,BJ3883,BJ3797,BJ175,1BJ6228,BJ653,1BJ5860,BJ1505,BJ2678,BJ2026,BJ1653,BJ6250,BJ0669,BJ6620,BJ5955,BJ2915,BJ5875,BJ5250,BJ6315,BJ3679,BJ388,1BJ6188,BJ3870,BJ1839,BJ6166,BJ5803,BJ3285,BJ5295,BJ3272,BJ573,1BJ5302,BJ2519,BJ5112,BJ022,1BJ2052,BJ107,1BJ3633,BJ1229,BJ1582,BJ5122,BJ1208,BJ1766,BJ1682,BJ5009,BJ1189,BJ5706,BJ1300,BJ2373,BJ2215,BJ1093,BJ6799,BJ0312,BJ6092,BJ3768,BJ2169,BJ2387,BJ1923,BJ6193,BJ2118,BJ5902,BJ2980,BJ63,11BJ2766,BJ0887,BJ5039,BJ2675,BJ3613,BJ6815,BJ3563,BJ1072,BJ6537,BJ3758,BJ2797,BJ2962,BJ6773,BJ263,1BJ2526,BJ2636,BJ3063,BJ3762,BJ2997,BJ2960,BJ5899,BJ2653,BJ1687,BJ0264,BJ2706,BJ303,1BJ0717,BJ2006,BJ1879,BJ2896,BJ1319,BJ2310,BJ5233,BJ0706,BJ1712,BJ03,11BJ1359,BJ233,1BJ2259,BJ1128,BJ1069,BJ2679,BJ1286,BJ0300,BJ1703,BJ1852,BJ2176,BJ1819,BJ3038,BJ0288,BJ0252';
        $driver_arr = explode(',', $str);

        foreach ($driver_arr as $driver_id) {
            $driver = Driver::model()->find('user = :user', array(':user' => $driver_id));
            $phone = $driver->phone;
            if (1 == $driver->mark and 1 == $driver->block_at) {
                Driver::model()->block($driver_id, Driver::MARK_ENABLE, DriverLog::LOG_MARK_ENABLE, '临时解除屏蔽', true);
                $message = '师傅您好，由于我们节假日倒休设置错误，今天进行了自动欠费屏蔽，现已紧急截除屏蔽，您可正常工作，给您带来不便敬请谅解。';
                Sms::SendSMS($phone, $message);
                echo $driver_id . " un block success\n";
            } else {
                echo $driver_id . " not block\n";
            }
            //把司机状态置为正常状态
            //Driver::model()->block($driver_id, Driver::MARK_DISNABLE, DriverLog::LOG_MARK_DISABLE_COMPLAINTS, '恶意销单');


        }
    }


    /**
     * author mengtianxue
     * php yiic.php mtx PingBi
     */
    public function actionJiePing()
    {
        $str = 'BJ107,1BJ1208,BJ1558,BJ6157,BJ338,1BJ6228,BJ3506,BJ63,11BJ1653,BJ5803,BJ3613,BJ263,1BJ5302,BJ2675,BJ5287,BJ3679,BJ3768,BJ5955,BJ1839,BJ0623,BJ3380,BJ3285,BJ6379,BJ3797,BJ1582,BJ2962,BJ2980,BJ5092,BJ1505,BJ5250,BJ0887,BJ175,1BJ3870,BJ6823,BJ6799,BJ6226,BJ2215,BJ0312,BJ2052';
//        $str = 'BJ6823,BJ0623,BJ6379,BJ2013,BJ1566,BJ5656,BJ5166,BJ0988,BJ6870,BJ3506,BJ3893,BJ1833,BJ6206,BJ1728,BJ1558,BJ1310,BJ5287,BJ6559,BJ5092,BJ6157,BJ5823,BJ3883,BJ3797,BJ175,1BJ6228,BJ653,1BJ5860,BJ1505,BJ2678,BJ2026,BJ1653,BJ6250,BJ0669,BJ6620,BJ5955,BJ2915,BJ5875,BJ5250,BJ6315,BJ3679,BJ388,1BJ6188,BJ3870,BJ1839,BJ6166,BJ5803,BJ3285,BJ5295,BJ3272,BJ573,1BJ5302,BJ2519,BJ5112,BJ022,1BJ2052,BJ107,1BJ3633,BJ1229,BJ1582,BJ5122,BJ1208,BJ1766,BJ1682,BJ5009,BJ1189,BJ5706,BJ1300,BJ2373,BJ2215,BJ1093,BJ6799,BJ0312,BJ6092,BJ3768,BJ2169,BJ2387,BJ1923,BJ6193,BJ2118,BJ5902,BJ2980,BJ63,11BJ2766,BJ0887,BJ5039,BJ2675,BJ3613,BJ6815,BJ3563,BJ1072,BJ6537,BJ3758,BJ2797,BJ2962,BJ6773,BJ263,1BJ2526,BJ2636,BJ3063,BJ3762,BJ2997,BJ2960,BJ5899,BJ2653,BJ1687,BJ0264,BJ2706,BJ303,1BJ0717,BJ2006,BJ1879,BJ2896,BJ1319,BJ2310,BJ5233,BJ0706,BJ1712,BJ03,11BJ1359,BJ233,1BJ2259,BJ1128,BJ1069,BJ2679,BJ1286,BJ0300,BJ1703,BJ1852,BJ2176,BJ1819,BJ3038,BJ0288,BJ0252';
        $driver_arr = explode(',', $str);

        foreach ($driver_arr as $driver_id) {
            $driver = Driver::model()->find('user = :user', array(':user' => $driver_id));
            $phone = $driver->phone;
            //把司机状态置为正常状态
            Driver::model()->block($driver_id, Driver::MARK_ENABLE, DriverLog::LOG_MARK_ENABLE, '解除屏蔽');
            $message = '你因销单过多，极不正常，现将你即刻屏蔽，请在工作日预约到北京分公司做合理解释。预约电话：58694700 ';
            Sms::SendSMS($phone, $message);
            echo $driver_id . "\n";
        }
    }

    /*-------------------------------报单-------------------------------*/

    /**
     * 新课邀请码入library库
     * author mengtianxue
     * php yiic.php mtx DriverBonus
     */
    public function actionDriverBonus()
    {
        $bonus_arr = Yii::app()->db_readonly->createCommand()
            ->select("*")
            ->from("t_bonus_area_static")
            ->where("id <= 6000")
            ->queryAll();
        foreach ($bonus_arr as $bonus_sn) {

            $bonus_sn = $bonus_sn['bonus_sn'];
            $bonusLibrary = BonusLibrary::model()->checkIsBonus($bonus_sn);

            if ($bonusLibrary) {
                $bonus = array();
                $bonus['bonus_sn'] = $bonus_sn;
                $bonus['money'] = 39;
                $bonus['bonus_id'] = 27;
                $bonus['sn_type'] = 0;
                $bonus['effective_date'] = '2012-06-30 23:59:00';
                $bonus['binding_deadline'] = '2014-06-30 23:59:00';
                $bonus['end_date'] = '2014-12-30 23:59:00';
                $bonus['create_by'] = '孟天学';
                $bonus['created'] = date('Y-m-d H:i:s');
				$model = new BonusLibrary();
				$model->setAttributes($bonus);
				$add_bonus = $model->save();

                echo $bonus_sn . "\n";
            }
        }
    }

    /**
     * 固定码入library库
     * author mengtianxue
     * php yiic.php mtx BonusType
     */
    public function actionBonusType()
    {
        $bonusType = Yii::app()->db->createCommand()
            ->select('*')
            ->from('t_bonus_type')
            ->where('sn_type = :sn_type', array(':sn_type' => 1))
            ->queryAll();
        foreach ($bonusType as $type) {
            $bonus = array();
            $bonus['bonus_sn'] = $type['sn_start'];
            $bonus['money'] = $type['money'];
            $bonus['bonus_id'] = $type['id'];
            $bonus['sn_type'] = $type['sn_type'];
            $bonus['effective_date'] = date('Y-m-d H:i:s', $type['created']);
            $bonus['binding_deadline'] = '2014-06-30 23:59:00';
            $bonus['end_date'] = '2014-12-30 23:59:00';
            $bonus['create_by'] = $type['create_by'];
            $bonus['created'] = date('Y-m-d H:i:s', $type['created']);
			$model = new BonusLibrary();
			$model->setAttributes($bonus);
			$add_bonus = $model->save();

			echo $type['sn_start'] . "\n";
        }
    }

    /**
     * author mengtianxue
     * php yiic.php mtx BonusCode
     */
    public function actionBonusCode()
    {
		$criteria = new CDbCriteria();
		$criteria->addCondition(' id != 8');
		$bonusCode = BonusCode::model()->findAll($criteria);
        foreach ($bonusCode as $code) {
            $params = array(
                'balance' => $code['money'],
                'user_limited' => $code['user_limited'],
                'channel_limited' => $code['channel_limited'],
                'end_date' => $code['end_date'],
            );
            CustomerBonus::model()->updateAll($params, 'bonus_type_id = :bonus_type_id', array(':bonus_type_id' => $code['id']));
            echo $code['id'] . "\n";
        }
    }


    /**
     * author mengtianxue
     * php yiic.php mtx DriverBonuss
     */
    public function actionDriverBonuss()
    {
        $order['driver_id'] = 'BJ9010';
        $order['status'] = 0;
        $order['pageSize'] = 10;
        $order['offset'] = 0;
        $orderList = Order::model()->getDriverOrderListByType($order);
        print_r($orderList);
    }

    /**
     * @param $phone
     * author mengtianxue
     * php yiic.php mtx CustomerInfo --phone=13801020286
     */
    public function actionCustomerInfo($phone)
    {
        $customer_info = CustomerMain::model()->getCustomerInfo($phone);
        echo $customer_info;
    }

    /**
     * author mengtianxue
     * php yiic.php mtx Info
     */
    public function actionInfo()
    {
        $data = array();
        $data['phone'] = '18511663962';
        $data['name'] = 'ceshi';
        $data['gender'] = 1;
        $data['backup_phone'] = '18511663963';
        $data['address'] = 'adkslas';
        $data['car_num'] = '京A12345';

        $updateCustomer = CustomerMain::model()->updateCustomerInfo($data);

        var_dump($updateCustomer);


    }

    /**
     * author mengtianxue
     * php yiic.php mtx Orders
     */
    public function actionOrders()
    {
        $orders = CustomerApiOrder::model()->getOrderByPhone('18511663962', 0, 10, 'sssss');
        print_r($orders);
    }

    /**
     * author mengtianxue
     * php yiic.php mtx OrdersD
     */
    public function actionOrdersD()
    {
        $orders = CustomerApiOrder::model()->getOrderInfoByOrderID('2078290');
        print_r($orders);
    }

    /**
     * author mengtianxue
     * php yiic.php mtx BonusUsedAccount
     */
    public function actionBonusUsedAccount()
    {
        $orders = CustomerBonus::model()->getBonusUsedSummary('18511663962');

    }

    /**
     * author mengtianxue
     * php yiic.php mtx Command
     */
    public function actionCommand()
    {
        $command_data = array();
        $command_data['order_id'] = '1479574';
        $command_data['level'] = 1;
        $command_data['content'] = "ssssss";
        $command_data['reason'] = "ssss";
        $command_data['sender'] = '18511663962';
        $command_data['driver_id'] = 'BJ9013';
        $command_data['order_status'] = 1;

        $comment_sms = CommentSms::model()->addOrderCommand($command_data);

        print_r($comment_sms);
    }

    /**
     * author mengtianxue
     * php yiic.php mtx AddBonus
     */
    public function actionAddBonus()
    {
        $params = array();
        $params['num'] = 10;
        $params['phone'] = '15710010037';
        $params['bonus_sn'] = '28605';
        QueueProcess::model()->addCustomerBonusBatch($params);

    }

    /**
     * author mengtianxue
     * php yiic.php mtx AddBonusLibrary
     */
    public function actionAddBonusLibrary()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('created > :created');
        $criteria->params = array(':created' => '2013-12-01');
        $driver = Driver::model()->findAll($criteria);
        foreach ($driver as $v) {
            $driver_id = $v->user;
            $this->addBonusLibrary($driver_id);
            echo $driver_id . "\n";
        }
    }


    /**
     * 添加优惠劵
     * @param $driver_id
     * @return bool
     * author mengtianxue
     */
    public function addBonusLibrary($driver_id)
    {
        $code = substr($driver_id, 0, 2);
        $code_num = substr($driver_id, 2);

        $bonus_city = Dict::items('bonus_city');
        $bonus_code = array_flip($bonus_city);

        $city = $bonus_code[$code];
        if ($city) {
            $bonus_sn = $city . $code_num;
            $checkedBonus = $this->checkBonusLibrary($bonus_sn);
            if ($checkedBonus) {
                $bonus = array();
                $bonus['bonus_sn'] = $bonus_sn;
                $bonus['money'] = 10;
                $bonus['bonus_id'] = 8;
                $bonus['sn_type'] = 0;
                $bonus['effective_date'] = '2012-06-30 23:59:00';
                $bonus['binding_deadline'] = '2014-06-30 23:59:00';
                $bonus['end_date'] = '2014-12-30 23:59:00';
                $bonus['create_by'] = '孟天学';
                $bonus['created'] = date('Y-m-d H:i:s');
				$model = new BonusLibrary();
				$model->setAttributes($bonus);
				$add_bonus = $model->save();
                if ($add_bonus) {
                    return true;
                }
            }
        }
        return false;
    }

    public function checkBonusLibrary($bonus_sn)
    {
        $bonus = BonusLibrary::model()->getBonusByBonus_sn($bonus_sn);
        if ($bonus) {
            BonusLibrary::model()->deleteAll('bonus_sn = :bonus_sn', array(':bonus_sn' => $bonus_sn));
            return true;
        } else {
            return false;
        }
    }

    /**
     * author mengtianxue
     * php yiic.php mtx Vip
     */
    public function actionVip()
    {
        $vip_list = $this->getAllVip();
        foreach ($vip_list as $vip) {
            $vipcard = $vip['id'];
            echo $vipcard . "(" . $vip['name'] . "),";
            echo date('Y-m-d H:i:s', $vip['created']) . ",";
            echo $vip['status'] . ",";
            for ($i = 4; $i < 10; $i++) {
                $m = $i + 1;
                if ($m < 10) {
                    $m = '0' . $m;
                }

                $start_time = strtotime('2013-0' . $i . '-01 00:00:00');
                $end_time = strtotime('2013-' . $m . '-01 00:00:00');
                $mon_trade = $this->getVipTrade($vipcard, $start_time, $end_time);
                echo date('m月', $start_time) . ",";
                echo (empty($mon_trade['amount']) ? 0 : -$mon_trade['amount']) . ",";
                echo (empty($mon_trade['count']) ? 0 : $mon_trade['count']) . ",";
            }
            echo "\n";
        }


    }

    public function getAllVip()
    {
        $vip = Yii::app()->db_finance->createCommand()
            ->select('*')
            ->from('{{vip}}')
            ->order("created asc")
            ->queryAll();
        return $vip;
    }

    public function getVipTrade($vipcard, $start_time, $end_time)
    {
        $vipTradeInfo = Yii::app()->db_finance->createCommand()
            ->select('sum(amount) as amount, count(1) as count')
            ->from('{{vip_trade}}')
            ->where('vipcard = :vipcard and type = 1 and created >= :start_time and created < :end_time',
                array(':vipcard' => $vipcard, ':start_time' => $start_time, ':end_time' => $end_time))
            ->queryRow();
        return $vipTradeInfo;
    }

    /**
     * 青岛一个月以内的所有接单统计
     * author mengtianxue
     * php yiic.php mtx DriverOrderInfo
     */
    public function actionDriverOrderInfo()
    {
        $driver = Yii::app()->db_readonly->createCommand()
            ->select("*")
            ->from("t_driver")
            ->where("city_id = :city_id", array(':city_id' => 20))
            ->queryAll();

        echo "司机工号,签约时间,满一个月日期,订单号,订单金额,信息费金额,保险费\n";

        foreach ($driver as $d) {
            $user = $d['user'];
            $user_induction = $d['created'];
            $end_date = date('Y-m-d H:i:s', (strtotime($user_induction) + 86400 * 30));
            $user_num = substr($d['user'], -4);
            if ($user_num < 9000) {

                $order_list = $this->OrderListByDriverId($user, $user_induction, $end_date);
                if ($order_list) {
                    $income = $cast = $costs = 0;
                    foreach ($order_list as $order) {
                        //司机信息
                        echo $user . "," . $user_induction . "," . $end_date . ",";
                        echo $order['order_id'] . "," . $order['income'] . "," . $order['cast'] . ",2" . "\n";
                        $income += $order['income'];
                        $cast += $order['cast'];
                        $costs += 2;
                    }
                    echo $user . "总额,,,," . $income . "," . $cast . "," . $costs . "\n";
                }
            }
        }
    }


    public function OrderListByDriverId($driver_id, $user_induction, $end_date)
    {
        $start_time = strtotime($user_induction);
        $end_time = strtotime($end_date);
        $order_list = Yii::app()->db_readonly->createCommand()
            ->select("*")
            ->from("t_order")
            ->where("driver_id = :driver_id and status = 1 and created >= :start_time and created <= :end_time",
                array(':driver_id' => $driver_id, ':start_time' => $start_time, ':end_time' => $end_time))
            ->queryAll();
        return $order_list;
    }

    /**
     * author mengtianxue
     * php yiic.php mtx MerchantsBind
     */
    public function actionMerchantsBind()
    {
        BonusLibrary::model()->merchantsBind('00902', '18511663962');
    }

    /**
     * @auther mengtianxue
     * php yiic.php mtx CancelBonus
     */
    public function actionCancelBonus()
    {
        $bonus_library = BonusLibrary::model()->cancelBonus('60869', '13671311113', '2343862');
        var_dump($bonus_library);
    }

    /**
     * @auther mengtianxue
     * php yiic.php mtx Complaint
     */
    public function actionComplaint()
    {
        $params = array();
        $params['order_id'] = 327202;
        $params['content'] = "ceshixinxi";

        $complaint = QueueProcess::model()->complain($params);
        var_dump($complaint);
    }

    /**
     * @auther mengtianxue
     * php yiic.php mtx Items_arr
     */
    public function actionItems_arr()
    {
        $params = array('cancel_c_type', 'ts_o_type');
        $city_arr = Dict::items_arr($params);
        print_r($city_arr);
    }

    /**
     * @auther mengtianxue
     * php yiic.php mtx GetSign
     */
    public function actionGetSign()
    {
        $sign = Summary::model()->getSign();
        var_dump($sign);
    }

    /**
     * @auther mengtianxue
     * php yiic.php mtx feedback
     */
    public function actionfeedback()
    {
        $dataFeedback = array();
        $dataFeedback['email'] = '18511663962';
        $dataFeedback['content'] = 'ceshi';
        $dataFeedback['device'] = 'ASSS';
        $dataFeedback['os'] = 'ASSS';
        $dataFeedback['macaddress'] = 'ASSS';
        $dataFeedback['version'] = '7.9';
        $dataFeedback['source'] = 'ASSS';
        $dataFeedback['created'] = time();

        QueueProcess::model()->feedBack($dataFeedback);
    }


    /**
     * @auther mengtianxue
     * php yiic.php mtx Name
     */
    public function actionName()
    {
        $has = Common::hasSensitiveWords("王格日乐图");
        var_dump($has);
    }


    /**
     * @auther mengtianxue
     * php yiic.php mtx GetTestDriver
     */
    public function actionGetTestDriver()
    {
        $driver_arr = Common::getTestDriverID();
//        $driver_arr = Driver::model()->getTestDriver();
        print_r($driver_arr);
    }

    /**
     * @auther mengtianxue
     * php yiic.php mtx Driver
     */
    public function actionDriver()
    {
        $driver_arr = Driver::model()->DriverLists(1, 300);
//        $driver_arr = Driver::model()->getTestDriver();
        print_r($driver_arr);
    }

    /**
     * @auther mengtianxue
     * php yiic.php mtx DayTest
     */
    public function actionDayTest()
    {
        $data['user'] = 'BJ9013';
        $attr['user'] = $data['user'];
        echo $attr['user'];
        $city_prefix = substr($data['user'], 0, 2); //城市编号

        $attr['city_id'] = Dict::item('city_prefix', '1');
        echo $attr['city_id'];
    }


    /**
     * @auther mengtianxue
     * php yiic.php mtx Submit
     */
    public function  actionSubmit()
    {
        $array = array("appkey" => "30000001",
            "car_number" => "京N２０５E９",
            "car_type" => "Q７",
            "card" => "0",
            "cash_card" => "0",
            "cost_mark" => "[自动]",
            "cost_type" => "0",
            "distance" => "15",
            "end_time" => "2013-11-28 07:24:55",
            "gps_type" => "google",
            "gps_type_star" => "google",
            "income" => "119",
            "invoice" => "0",
            "lat" => "39.912007",
            "lat_start" => "39.865053",
            "lng" => "116.367846",
            "lng_star" => "116.423825",
            "log" => "报单",
            "method" => "driver.order.submit",
            "midway_wait_time" => "0",
            "name" => "",
            "order_id" => "BJ53501385590649",
            "order_number" => "BJ53501385590649",
            "other_cost" => "0",
            "price" => "119",
            "start_time" => "2013-11-28 06:49:00",
            "timestamp" => "2013-11-28 07:29:26",
            "token" => "94aeacd843280e6a80b2716dae3e727c",
            "ver" => "3",
            "waiting_time" => "5",
            "driver_id" => "BJ5350",
            "tip" => 0,
            "car_cost" => 0,
            "log_time" => "2013-11-28 07:24:55",
            "stop_wait_time" => "0",
            "coupon" => 0,
            "invoiced" => 0,
        );
        QueueProcess::model()->order_submit($array);
    }


    /**
     * @param $bonus_id
     * @auther mengtianxue
     * php yiic.php mtx BonusExport --bonus_id=80
     */
    public function actionBonusExport($bonus_id)
    {
        $bonus_arr = Yii::app()->db_readonly->createCommand()
            ->select("*")
            ->from("t_customer_bonus")
            ->where('bonus_type_id = :bonus_id', array(':bonus_id' => $bonus_id))
            ->order("used desc")
            ->queryAll();
        echo "司机工号,";
        echo "司机电话,";
        echo "客户电话,";
        echo "订单id,";
        echo "城市id,";
        echo "公里数,";
        echo "余额,";
        echo "绑定时间,";
        echo "使用时间\n";

        foreach ($bonus_arr as $k => $bonus) {

            if (empty($order_id)) {
                $order_id = "未使用";
                $distance = "";
                $city_id = '';
                $balance = '';
                echo ' , ';
                echo ' , ';

            } else {
                $order_id = $bonus['order_id'];
                $order = Order::model()->getOrdersById($order_id);
                $distance = $order['distance'];
                $city_id = $order['city_id'];
                $balance = $order['income'];
                echo $order['driver_id'] . ",";
                echo $order['driver_phone'] . ",";
            }
            echo $bonus['customer_phone'] . ",";
            echo $order_id . ",";
            echo $city_id . ",";
            echo $distance . ",";
            echo $balance . ",";
            echo (empty($bonus['created']) ? '' : date('Y-m-d H:i:s', $bonus['created'])) . ",";
            echo (empty($bonus['used']) ? '' : date('Y-m-d H:i:s', $bonus['used'])) . " \n";
        }
    }

    /**
     * @auther mengtianxue
     * php yiic.php mtx ConponBonus --order_id=2417502 --bonus_sn=0190058216
     */
    public function ActionConponBonus($order_id, $bonus_sn)
    {
        $order_settle = OrderSettlement::model()->couponUsed($order_id, $bonus_sn);
        var_dump($order_settle);
    }


    /**
     * @auther mengtianxue
     * php yiic.php mtx Account
     */
    public function ActionAccount()
    {
        BAccount::model()->getAccountByDriver_id("BJ9013");
    }


    /**
     * 统计信息费  每天要跑的脚本
     * @auther mengtianxue
     * php yiic.php mtx DailyAccountReport
     */
    public function ActionDailyAccountReport()
    {
        $date_time = date('Y-m-d', strtotime("-1 day"));
        $day_time = date('Y-m-d');

        $city = Dict::items("city");
        unset($city['0']);
        foreach ($city as $k => $v) {
            //获取当前的余额
            $balance = DriverBalance::model()->getBalance($k);
            echo $balance . "\n";
            //获取今天使用金额
            $money = BAccount::model()->getDailyCountAccount($day_time, $k);
            echo $money . "\n";
            //当天凌晨的余额
            $balance = $balance - $money;
            echo $balance . "\n";

            //添加每天统计信息
            $Account_daily_report = array();
            $Account_daily_report['city_id'] = $k;
            $Account_daily_report['daily_date'] = $date_time;
            $Account_daily_report['day_time'] = $day_time;
            $Account_daily_report['money'] = $balance;
            $Account_daily_report['status'] = 0;
            $Account_daily_report['created'] = date('Y-m-d H:i:s');

            //添加统计库
            $add_daily_account = $this->addDailyAccount($Account_daily_report);

            if ($add_daily_account) {
                echo "期初余额和期末余额已经保存\n";
                $this->AddDailyAccountReport($Account_daily_report);
            }
        }
    }


    /**
     * 信息费按月详情汇总
     * @param int $month
     * @auther mengtianxue
     * php yiic.php mtx MontyAccountReport --month=2013-12
     */
    public function ActionMontyAccountReport($month = 0)
    {
        $month_new = date('Y-m');

        if (empty($month) || $month_new <= $month) {
            $day = date('d');
            $month = date('Y-m-');
        } else {
            $day = date('t', strtotime($month));
            $month = date('Y-m-', strtotime($month));
        }

        for ($d = 1; $d < $day; $d++) {

            $daily = $month . $d;
            echo $daily . "\n\n";
            $this->ActionAccountReport($daily);
        }
    }

    /**
     * 信息费详情汇总  每天要跑的脚本
     * @param $day
     * @auther mengtianxue
     * php yiic.php mtx AccountReport --day=2013-08-01
     */
    public function ActionAccountReport($day = 0)
    {
        //天
        if (empty($day)) {
            $end_time = strtotime(date('Y-m-d'));
            $start_time = $end_time - 86400;
        } else {
            $start_time = strtotime($day);
            $end_time = $start_time + 86400;
        }

        $date_time = date('Y-m-d', $start_time);

        $month = date('Ym', $start_time);
        $table_name = 't_employee_account_' . $month;

        $where = '';
        $params = array();
        $where .= 'created >= :start_time and created < :end_time';
        $params[':start_time'] = $start_time;
        $params[':end_time'] = $end_time;

        $city = Dict::items("city");
        unset($city['0']);
        foreach ($city as $k => $v) {
            $where .= 'city_id = :city_id';
            $params[':city_id'] = $k;
            $daily_balance = BDailyAccountReport::model()->getDailyEndBalanceByCity($date_time, $k);
            $money = $daily_balance['end_balance'];


            $account_city = Yii::app()->db_finance->createCommand()
                ->select("channel, city_id, sum(cast) as money")
                ->from($table_name)
                ->where($where, $params)
                ->group("channel")
                ->queryAll();

            if ($account_city) {
                ReportFsAccountRp::model()->deleteAll('account_date = :account_date and city_id = :city_id',
                    array(':account_date' => $date_time, ':city_id' => $k));

                $bill_array = BDailyAccountReport::$bill_type;
                foreach ($account_city as $account) {
                    $channel = $account['channel'];
                    if (in_array($channel, $bill_array)) {
                        $bill_type = 2;
                    } else {
                        $bill_type = 1;
                    }

                    $model = new ReportFsAccountRp();
                    $params = array();
                    $params['city_id'] = $account['city_id'];
                    $params['channel'] = $account['channel'];
                    $params['money'] = $account['money'];
                    $params['account_date'] = $date_time;
                    $params['bill_type'] = $bill_type;
                    $params['created'] = date("Y-m-d H:i:s");
                    $model->attributes = $params;
                    $model->save();
                    echo $params['account_date'] . ":" . $account['city_id'] . "-" . $account['channel'] . "\n";
                }
            }
        }
    }

    /**
     * 把每天的统计添加tag库
     * @param $params
     * @return bool
     * @auther mengtianxue
     */
    public function addDailyAccount($params)
    {
        $date_time = $params['daily_date'];
        $model_tag = new ReportFsAccountTag();
        //检查$date_time统计是否存在，如果存在删除
        $account_report = $model_tag->find('daily_date = :daily_date and city_id = :city_id',
            array(':daily_date' => $date_time, ':city_id' => $params['city_id']));

        if ($account_report) {
            $model_tag->updateAll(array('end_balance' => $params['money']),
                'daily_date = :daily_date and city_id = :city_id',
                array(':daily_date' => $date_time, ':city_id' => $params['city_id']));
        } else {
            $countAccount = BDailyAccountReport::model()->getDailyCountAccount($params['daily_date'], $params['city_id']);
            $Account_daily = array();
            $Account_daily['city_id'] = $params['city_id'];
            $Account_daily['add_balance'] = $countAccount['add_money'];
            $Account_daily['minus_balance'] = $countAccount['minus_money'];
            $Account_daily['end_balance'] = $params['money'];
            $Account_daily['daily_date'] = $params['daily_date'];
            $Account_daily['month_date'] = date('Y-m', strtotime($params['daily_date']));
            $Account_daily['status'] = 0;
            $Account_daily['created'] = $params['created'];
            $model_tag->attributes = $Account_daily;
            $model_tag->save();
        }

        //检查day_time统计是否存在，如果存在删除
        $account = $model_tag->find('daily_date = :daily_date and city_id = :city_id',
            array(':daily_date' => $params['day_time'], ':city_id' => $params['city_id']));

        if ($account) {
            $model_tag->updateAll(array('start_balance' => $params['money']),
                'daily_date = :daily_date and city_id = :city_id',
                array(':daily_date' => $params['day_time'], ':city_id' => $params['city_id']));
            return true;
        } else {
            $account_new = array();
            $account_new['city_id'] = $params['city_id'];
            $account_new['daily_date'] = $params['day_time'];
            $account_new['month_date'] = date('Y-m', strtotime($params['day_time']));
            $account_new['start_balance'] = $params['money'];
            $account_new['status'] = 0;
            $account_new['created'] = $params['created'];
            $model = new ReportFsAccountTag();
            $model->attributes = $account_new;
            if ($model->save()) {
                return true;
            }
        }
        return false;
    }


//VIP


    /**
     * 对每天的脚本进行汇总
     * @param int $date
     * @auther mengtianxue
     * php yiic.php mtx DailyVipTag --date=2013-12-01
     */
    public function ActionDailyVipTag($date = 0)
    {
        Yii::import('application.models.schema.report.ReportFsVipTag');
        if (empty($day)) {
            $end_time = date('Y-m-d');
            $start_time = date('Y-m-d', strtotime("-1 day"));
        } else {
            $start_time = $day;
            $end_time = date('Y-m-d', (strtotime($day) + 86400));
        }

        //查询条件
        $criteria = new CDbCriteria;
        $criteria->select = "sum(money) as money, bill_type, city_id";
        $criteria->addCondition("phone = ''");
        $criteria->addBetweenCondition('daily_date', $start_time, $end_time);
        $criteria->group = 'bill_type, city_id';
        $vip_tag = ReportFsVipTag::model()->findAll($criteria);
        $params = array();
        $params['daily_date'] = $start_time;
        $params['month_date'] = date('Y-m', strtotime($start_time));
        $params['created'] = date('Y-m-d H:i:s');
        $params['status'] = 0;
        foreach ($vip_tag as $v) {
            $params['city_id'] = $v->city_id;
            switch ($v->bill_type) {
                case 0:
                    $params['start_balance'] = $v->money;
                    break;
                case 1:
                    $params['add_balance'] = $v->money;
                    break;
                case 2:
                    $params['minus_balance'] = $v->money;
                    break;
                case 3:
                    $params['end_balance'] = $v->money;
                    break;
            }
        }
        ReportFsVipTag::model()->insert($params);
        echo $start_time . "\n";
    }

    /**
     * 刷新一个月内vip起初和期末余额
     * @param int $date
     * @return bool
     * @auther mengtianxue
     * php yiic.php mtx VipElevenDaily
     */
    public function ActionVipElevenDaily($date = 0)
    {
        Yii::import('application.models.schema.report.ReportFsVipRp');
        if (empty($date)) {
            $time = strtotime("-1 day");
        } else {
            $time = strtotime($date);
        }
        $date_time = date('Y-m-d', $time);
        $model_tag = new ReportFsVipRp();
        $vip_report = $model_tag->findAll('account_date = :account_date and channel = :channel', //10号余额
            array(':account_date' => $date_time, ':channel' => 99));
        if ($vip_report) {
            foreach ($vip_report as $list) {
                $city_id = $list['city_id'];
                $balance = $list['money'];
                $day = date('t', $time);
                for ($d = 1; $d <= $day; $d++) {
                    $first_day = date('Y-m-d', strtotime("-$d day", $time)); // 9号
                    $k = $d - 1;
                    $Tus_day = date('Y-m-d', strtotime("-$k day", $time)); // 10号

                    $money = $this->getDailyCountAccount($Tus_day, $city_id);
                    $balance = $balance - $money;
                    //添加每天统计信息
                    $vip_daily_report = array();
                    $vip_daily_report['city_id'] = $k;
                    $vip_daily_report['daily_date'] = $first_day;
                    $vip_daily_report['day_time'] = $Tus_day;
                    $vip_daily_report['amount'] = $balance;
                    $vip_daily_report['status'] = 0;
                    $vip_daily_report['channel'] = -1;
                    $vip_daily_report['created'] = date('Y-m-d H:i:s');

                    //添加ReportFsAccountTag
                    $add_daily_account = $this->addVipReport($vip_daily_report);

                    //添加ReportFsAccountRp
                    if ($add_daily_account) {
                        echo "期初余额和期末余额已经保存\n";
                    }
                }
            }
            return true;

        } else {
            return false;
        }


    }


    /**
     * 根据时间获取天总额
     * @param $daily
     * @param $city_id
     * @return array|mixed|null
     * @auther mengtianxue
     */
    public function getDailyCountAccount($daily = 0, $city_id = 0)
    {
        $params = array();
        //如果不传递参数，默认为当前
        if ($daily == 0) {
            $daily = date('Y-m-d');
        }

        //查询条件
        $criteria = new CDbCriteria;
        $criteria->select = "sum(money) as money, bill_type, city_id";
        $criteria->compare('city_id', $city_id);
        $criteria->compare('account_date', $daily);
        $vip_tag = ReportFsVipRp::model()->find($criteria);
        return $vip_tag->money;
    }


    /**
     * @param $phone
     * @auther mengtianxue
     * php yiic.php mtx getCityId --phone=13722595981
     */
    public function ActionGetCityId($phone)
    {
        $city_id = Helper::PhoneLocation($phone);
        echo $city_id . "\n";
    }

    /**
     * @auther mengtianxue
     * php yiic.php mtx SendMill
     */
    public function ActionSendMill()
    {
        Mail::sendMail(array('mengtianxue@edaijia-staff.cn'), '测试', '测试标题');
    }

    /**
     * @param $phone
     * @param $order_id
     * @auther mengtianxue
     * php yiic.php mtx ConponNum --phone=15110012180 --order_id=2966863
     */
    public function ActionConponNum($phone, $order_id)
    {
        $bonus_info = CustomerBonus::model()->getBonusUseCount($phone, $order_id);

        if ($bonus_info) {
            $bonus_count = $bonus_info['count'];
        } else {
            $bonus_count = 0;
        }
        echo $bonus_count . "\n";
    }


    /**
     * @auther mengtianxue
     * php yiic.php mtx Vips
     */
    public function ActionVips()
    {
        BReportDailyVipReport::model()->getVipMonthList('2013');

    }

    /**
     * @param int $order_id
     * @auther mengtianxue
     * php yiic.php mtx SetRedis --order_id=202012
     */
    public function ActionSetRedis($order_id = 0)
    {
        $arr = array();
        $arr['is_comment'] = 'Y';
        $arr['level'] = 5;
        ROrderComment::model()->setComment($order_id, $arr);
    }

    /**
     * @param int $order_id
     * @auther mengtianxue
     * php yiic.php mtx GetRedis --order_id=2814301
     */
    public function ActionGetRedis($order_id = 0)
    {
        $commend = ROrderComment::model()->getComment($order_id);
        var_dump($commend);
    }

    /**
     * @param int $order_id
     * @auther mengtianxue
     * php yiic.php mtx DelRedis --order_id=2814301
     */
    public function ActionDelRedis($order_id = 0)
    {
        $commend = ROrderComment::model()->delete($order_id);
        var_dump($commend);
    }

    /**
     * @param int $order_id
     * @auther mengtianxue
     * php yiic.php mtx GetRedis --order_id=202012
     */
    public function ActionGetRediss($order_id = 0)
    {
        $comment = CustomerApiOrder::model()->checkedCommentByOrderID($order_id);
        print_r($comment);
    }

    /**
     * @param int $phone
     * @auther mengtianxue
     * php yiic.php mtx CustomerGet --phone=18511663962
     */
    public function ActionCustomerGet($phone = 0)
    {
        $customerInfo = RCustomerInfo::model()->getByPhone($phone);
        print_r($customerInfo);
    }

    /**
     * @param $phone
     * @auther mengtianxue
     * php yiic.php mtx ViewInfo --phone=18511663962
     */
    public function ActionViewInfo($phone)
    {
        $customer_info = CustomerMain::model()->getCustomerInfo($phone);
        $name = $customer_info->name;
        $backup_phone = $customer_info->backup_phone;
        $address = $customer_info->address;
        $car_num = $customer_info->car_num;
        $gender = intval($customer_info->gender);

        //客户信息
        $customer_data = array(
            'phone' => $phone,
            'name' => $name,
            'gender' => $gender,
            'backup_phone' => $backup_phone,
            'car_num' => $car_num,
            'address' => $address
        );
        print_r($customer_data);
    }

    /**
     * @auther mengtianxue
     * php yiic.php mtx RPust
     */
    public function ActionRPust()
    {
        $key = 'CESHI_NUM1';
        for ($i = 1; $i <= 50; $i++) {
            RCustomerInfo::model()->RPustList($key, $i);
            echo $i;
        }
    }

    /**
     * @param $start
     * @param $end
     * @auther mengtianxue
     * php yiic.php mtx LRand --start=0 --end=9
     */
    public function ActionLRand($start, $end)
    {
        $key = 'CESHI_NUM1';
        $num = RCustomerInfo::model()->getLRand($key, $start, $end);
        var_dump($num);
    }

    /**
     * @auther mengtianxue
     * php yiic.php mtx Rechange
     */
    public function ActionRechange()
    {
        $params = array('comment' => '新司机签约', 'user' => 'BJ9033', 'city_id' => 1, 'channel' => 25, 'cast' => 100);
        $order = OrderSettlement::model()->driverRecharge($params, true);
        var_dump($order);
    }


    /**
     * @param $prefix
     * @param $len
     * @auther mengtianxue
     * php yiic.php mtx Number
     */
    public function ActionNumber($prefix = '73', $len = '8')
    {
        $min = intval(str_pad($prefix, $len, '0')) + 1;
        $max = intval(str_pad($prefix, $len, '9')) - 1;
        echo $min . "\n";
        echo $max . "\n";
		$criteria = new CDbCriteria();
		$criteria->addBetweenCondition('number', $min, $max);
		$criteria->select = 'max(number) as number';
		$library = BonusLibrary::model()->find($criteria);
        if ($library) {
            print_r($library);
            echo $library['number'];
        } else {
            echo 1;
        }

    }

    /**
     * @param int $phone
     * @param int $status
     * @auther mengtianxue
     * php yiic.php mtx Ce
     */
    public function ActionCe($phone = 13611126764, $status = 0)
    {
        $bonus = BonusLibrary::model()->getBonus_sn($phone, $status);
        print_r($bonus);
    }


    /**
     * @auther mengtianxue
     * php yiic.php mtx GetVip
     */
    public function ActionGetVip()
    {
        BVip::model()->getVipList();
    }

    public function ActionTransList()
    {
        $array = array('trans_card' => 18811663963);
        $vip_trans = BVip::model()->getVipTradeList($array);
        foreach ($vip_trans as $trans) {
            echo $trans->id . "\n";
        }
    }

    /**
     * @auther mengtianxue
     * php yiic.php mtx TransListApi
     */
    public function ActionTransListApi()
    {
        $user_id = 611688;
        $customers = BCustomers::model()->getCustomerTradeListApi($user_id);
        $ret = array(
            'code' => 0,
            'data' => $customers,
            'message' => '获取成功'
        );
        echo json_encode($ret) . "\n";
    }

    /**
     * @auther mengtianxue
     * php yiic.php mtx Favorable
     */
    public function ActionFavorable()
    {
        $phone = '18511663962';
        $booking_time = '1394181198';
        $favorable = Order::model()->getOrderFavorable($phone, $booking_time);
        print_r($favorable);
    }


    /**
     * @auther mengtianxue
     * php yiic.php mtx UserAccount
     */
    public function ActionUserAccount()
    {
        $id = '611691';
        $user_account = BCustomers::model()->getAccount(array('user_id' => $id));
        $user_account_data = $user_account['data'];
        $balance = $user_account_data->amount;
        print_r($balance);
    }


    /**
     * @auther mengtianxue
     * php yiic.php mtx DriverHistoryAccount
     */
    public function ActionDriverHistoryAccount()
    {
        $params_arr = array();
        $params_arr['driver_id'] = 'BJ9005';
        $params_arr['channel'] = 25;
        $params_arr['count'] = 10;
        $params_arr['min_id'] = 0;
        $historyList = EmployeeAccount::model()->getIncomeListByChannel($params_arr);
        $ret = array(
            'code' => 0,
            'message' => '读取成功',
            'datetime' => $historyList['datetime'],
            'list' => $historyList['orderList']
        );
        echo json_encode($ret);
    }


    /**
     * @auther mengtianxue
     * php yiic.php mtx Income --user_id=
     */
    public function ActionIncome($user_id = '611722')
    {
        $user_id = 106001;
        $amount = 10;
        $params = array();
        $params['trans_card'] = '18612013051';
        $income = BCustomers::model()->income($user_id, $amount, $params = array());
        print_r($income);
    }

    /**
     * @auther mengtianxue
     * php yiic.php mtx SSS
     */
    public function ActionSSS()
    {
        CustomerBonus::model()->getBonusUseCount('15110012180', '1000000251');
    }

    /**
     * @param null $date
     * @auther mengtianxue
     * php yiic.php mtx DelHistoryRedis
     */
    public function actionDelHistoryRedis($date = null)
    {
        if ($date) {
            $end_time = date('Y-m-d 07:00:00', strtotime($date));
            $begin_time = date('Y-m-d 07:00:00', strtotime($date) - 86400);
        } else {
            $end_time = date('Y-m-d H:i:s', time());
            $begin_time = date('Y-m-d 07:00:00', time() - 86400);
        }

        $offset = 0;
        $pagesize = 500;
        $criteria = new CDbCriteria();
        $criteria->condition = 'call_time between :begin_time and :end_time';
        $criteria->group = 'phone';
        $criteria->limit = $pagesize;
        $criteria->params = array(
            ':begin_time' => strtotime($begin_time),
            ':end_time' => strtotime($end_time));
        while (true) {
            $criteria->offset = $offset;
            $orders = Order::model()->findAll($criteria);
            if ($orders) {
                foreach ($orders as $order) {
                    $phone = $order->phone;
                    ROrderHistory::model()->delOrderHistory($phone);
                    echo "del:" . $phone . "\n";
//                    ROrderHistory::model()->loadCustomerOrder($phone);
//                    echo "load:" . $phone . "\n";
                }
                $offset += $pagesize;
            } else {
                break;
            }
        }
    }


    /**
     * @param $phone
     * @auther mengtianxue
     * php yiic.php mtx Binding --phone=18511760287
     */
    public function actionBinding($phone = '18511663962')
    {
        $channel = '99';
        $money = '12';
        $phone = $phone;
        $bonus = BonusLibrary::model()->channelBind($channel, $money, $phone);
        print_r($bonus);
    }

    /**
     * @auther mengtianxue
     * php yiic.php mtx PageBonus --phone=18511760287
     */
    public function actionPageBonus($phone = '18511760287', $pageNO = 0)
    {
        $params = array();
        $params['phone'] = $phone;
        $params['pageNO'] = $pageNO;
        $params['pageSize'] = 10;

        $bonus_arr = BBonus::model()->getCustomerBonus($params);
        $ret = array(
            'code' => 0,
            'message' => '获取成功',
            'data' => $bonus_arr
        );
        echo json_encode($ret);

    }

    /**
     * @param string $bonus_sn
     * @auther mengtianxue
     * php yiic.php mtx BonusBing --bonus_sn =
     */
    public function actionBonusBing($bonus_sn = '')
    {
        if (!empty($bonus_sn)) {
            $phone = '18511663962';
            $pwd = '0';
            $ret = BonusLibrary::model()->BonusBinding($bonus_sn, $phone, $pwd);
            print_r($ret);
        }
    }

    /**
     * 统计用户报单详情
     * @auther mengtianxue
     * php yiic.php mtx CustomerOrderReport
     */
    public function ActionCustomerOrderReport()
    {
        $offset = 0;
        $pageSize = 500;
        $criteria = new CDbCriteria();
        $criteria->select = "user_id, phone, count(1) as vipcard"; //vipCard 接受订单数
        $criteria->condition = 'status = 1';
        $criteria->group = 'phone';
        $criteria->limit = $pageSize;
        while (true) {
            $criteria->offset = $offset;
            $orders = Order::model()->findAll($criteria);
            if ($orders) {
                foreach ($orders as $order) {
                    $phone = $order->phone;
                    $user_id = $order->user_id;
                    if ($user_id == 0) {
                        $customer_info = CustomerMain::model()->getCustomer($phone);
                        if (!empty($customer_info)) {
                            $user_id = $customer_info->id;
                        }
                    }
                    $arr = array();
                    $arr['phone'] = $phone;
                    $arr['user_id'] = $user_id;
                    $arr['complate'] = $order->vipcard;
                    $customer_order_report = new CustomerOrderReport();
                    $customer_order_report->attributes = $arr;
                    $customer_order_report->insert();
                    echo "Phone:" . $phone . "\n";
                }
                $offset += $pageSize;
            } else {
                break;
            }
        }
    }

    /**
     * @auther mengtianxue
     * php yiic.php mtx AddCustomerOrderReport
     */
    public function ActionAddCustomerOrderReport(){
        $params = array('user_id' => 123456789, 'phone' => '1213121231231', 'complate' => 1);
        CustomerOrderReport::model()->addCustomerOrder($params);
        print_r($params);
    }

    /**
     * @auther mengtianxue
     * php yiic.php mtx AddIncome --num=0
     */
    public function ActionAddIncome($num = 0)
    {
        $arr = array(
            '5049537,5049549,5049573',
            '5049574,5049578,5049583,5049587,5049590,5049592,5049594',
            '5049629,5049631,5049632,5049638,5049647,5049651,5049652,5049653,5049667,5049669',
            '5049700,5049702,5049704,5049705,5049717,5049723,5049733,5049741,5049749,5049750,5049752,5049754,5049758,5049759,5049770,5049773,5049779,5049790,5049792,5049806,5049814,5049824,5049829,5049841,5049845,5049847,5049858,5049862,5049869,5049871,5049874,5049875,5049880,5049881,5049888,5049889,5049890,5049916,5049936,5049943,5049955,5049956,5049957,5049964,5049966,5049969,5049970,5049973,5049974,5049982,5049983,5049985,5049989,5049992,5049993,5049994,5050001,5050008,5050011,5050018,5050025,5050030,5050041,5050049,5050054,5050058,5050061,5050077,5050078,5050090,5050104,5050105,5050115,5050116,5050121,5050125,5050131,5050147,5050148,5050155,5050159,5050166,5050168,5050175,5050178,5050179,5050191,5050192,5050193,5050196,5050197,5050202,5050203,5050216,5050234,5050239,5050242,5050248,5050252,5050255,5050283,5050289,5050300,5050302,5050307,5050329,5050341,5050362,5050373,5050383,5050386,5050389,5050390,5050392,5050393,5050394,5050406,5050407,5050411,5050412,5050413,5050437,5050450,5050452,5050463,5050466,5050470,5050475,5050478,5050485,5050488,5050493,5050500,5050508,5050520,5050528,5050539,5050543,5050549,5050550,5050552,5050556,5050569,5050574,5050586,5050589,5050596,5050597,5050600,5050601,5050602,5050605,5050608,5050610,5050614,5050623,5050624,5050641,5050650,5050655,5050656,5050662,5050667,5050678,5050690,5050693,5050715,5050716,5050721,5050722,5050733,5050738,5050741,5050742,5050747,5050755,5050761,5050762,5050763,5050764,5050773,5050774,5050799,5050800,5050801,5050823,5050824,5050825,5050829,5050845,5050847,5050850,5050853,5050854,5050880,5050882,5050884,5050885,5050886,5050900,5050920,5050925,5050928,5050929,5050949,5050971,5050973,5050974,5050975,5050980,5051007,5051011,5051013,5051015,5051017,5051022,5051029,5051037,5051050,5051069,5051075,5051077,5051084,5051086,5051089,5051102,5051108,5051109,5051110,5051111,5051112,5051114,5051115,5051116,5051122,5051123,5051125,5051132,5051139,5051142,5051144,5051152,5051153,5051154,5051157,5051161,5051165,5051175,5051177,5051178,5051179,5051182,5051190,5051195,5051199,5051226,5051229,5051231,5051232,5051234,5051237,5051241,5051246,5051251,5051269,5051271,5051283,5051286,5051288,5051298,5051305,5051309,5051320,5051327,5051330,5051333,5051337,5051345,5051351,5051357,5051367,5051372,5051388,5051399,5051401,5051406,5051411,5051429,5051430,5051435,5051437,5051459,5051463,5051465,5051467,5051478,5051489,5051495,5051504,5051514,5051525,5051526,5051527,5051528,5051534,5051542,5051547,5051553,5051563,5051571,5051586,5051587,5051598,5051607,5051611,5051623,5051626,5051627,5051641,5051642,5051644,5051645,5051664,5051680,5051681,5051685,5051689,5051703,5051704,5051707,5051713,5051714,5051715,5051722,5051724,5051734,5051745,5051749,5051750,5051765,5051766,5051767,5051771,5051800,5051803,5051821,5051837,5051848,5051856,5051861,5051886,5051891,5051907,5051908,5051913,5051914,5051918,5051920,5051923,5051933,5051936,5051942,5051947,5051950,5051954,5051957,5051959,5051960,5051966,5051971,5051972,5051980,5051982,5051986,5051992,5051997,5052001,5052002,5052003,5052006,5052008,5052011,5052017,5052018,5052019,5052030,5052031,5052041,5052046,5052063,5052064,5052065,5052069,5052070,5052078,5052079,5052084,5052089,5052094,5052104,5052109,5052110,5052119,5052121,5052129,5052131,5052134,5052135,5052140,5052163,5052164,5052167,5052180,5052190,5052192,5052196,5052201,5052205,5052211,5052212,5052225,5052228,5052229,5052232,5052233,5052234,5052238,5052250,5052251,5052255,5052274,5052275,5052278,5052282,5052285,5052287,5052293,5052300,5052314,5052321,5052324,5052328,5052332,5052335,5052341,5052359,5052371,5052381,5052397,5052400,5052401,5052407,5052409,5052410,5052412,5052413,5052427,5052431,5052432,5052450,5052452,5052465,5052472,5052475,5052477,5052480,5052491,5052492,5052497,5052498,5052500,5052514,5052517,5052518,5052533,5052540,5052551,5052557,5052575,5052584,5052593,5052599,5052602,5052604,5052606,5052609,5052610,5052616,5052621,5052624,5052631,5052637,5052638,5052642,5052643,5052646,5052648,5052657,5052667,5052673,5052674,5052679,5052696,5052701,5052703,5052710,5052723,5052728,5052741,5052758,5052761,5052770,5052776,5052777,5052792,5052794,5052801,5052812,5052814,5052816,5052817,5052822,5052823,5052824,5052825,5052833,5052842,5052843,5052857,5052861,5052868,5052870,5052876,5052881,5052882,5052894,5052896,5052901,5052904,5052905,5052910,5052914,5052919,5052923,5052938,5052939,5052941,5052942,5052956,5052959,5052977,5052979,5052992,5052995,5052999,5053003,5053008,5053009,5053023,5053024,5053025,5053034,5053037,5053047,5053049,5053050,5053055,5053076,5053078,5053080,5053081,5053082,5053086,5053089,5053092,5053095,5053103,5053111,5053128,5053130,5053131,5053132,5053142,5053169,5053180,5053187,5053189,5053191,5053192,5053199,5053203,5053220,5053225,5053230,5053238,5053246,5053247,5053252,5053263,5053265,5053268,5053275,5053298,5053304,5053306,5053307,5053309,5053312,5053316,5053323,5053324,5053326,5053334,5053336,5053339,5053340,5053342,5053350,5053352,5053357,5053364,5053367,5053370,5053380,5053384,5053392,5053396,5053401,5053402,5053418,5053419,5053429,5053430,5053441,5053463,5053477,5053479,5053483,5053485,5053488,5053489,5053494,5053497,5053498,5053499,5053502,5053517,5053522,5053524,5053526,5053533,5053534,5053536,5053544,5053548,5053563,5053588,5053592,5053596,5053600,5053616,5053617,5053621,5053627,5053635,5053638,5053652,5053664,5053669,5053670,5053685,5053687,5053698,5053712,5053718,5053719,5053730,5053732,5053742,5053749,5053765,5053776,5053780,5053783,5053784,5053786,5053788,5053789,5053792,5053796,5053806,5053813,5053816,5053818,5053819,5053832,5053834,5053835,5053837,5053839,5053850,5053856,5053857,5053858,5053860,5053871,5053876,5053877,5053884,5053886,5053908,5053918,5053923,5053931,5053935,5053938,5053940,5053943,5053944,5053948,5053950,5053956,5053965,5053980,5053983,5053995,5053996,5054000,5054004,5054011,5054015,5054017,5054020,5054022,5054029,5054030,5054041,5054055,5054065,5054071,5054087,5054088,5054094,5054098,5054099,5054105,5054114,5054128,5054129,5054139,5054140,5054150,5054151,5054167,5054179,5054191,5054198,5054201,5054216,5054217,5054222,5054226,5054233,5054242,5054246,5054250,5054254,5054255,5054269,5054270,5054273,5054278,5054287,5054302,5054310,5054311,5054316,5054326,5054329,5054330,5054333,5054337,5054348,5054351,5054360,5054362,5054364,5054366,5054385,5054394,5054408,5054415,5054421,5054429,5054435,5054443,5054449,5054452,5054468,5054469,5054470,5054482,5054483,5054484,5054485,5054486,5054506,5054508,5054514,5054515,5054518,5054522,5054525,5054527,5054532,5054541,5054553,5054554,5054583,5054584,5054585,5054586,5054587,5054596,5054597,5054605,5054608,5054618,5054626,5054629,5054632,5054637,5054639,5054645,5054647,5054654,5054661,5054662,5054678,5054683,5054695,5054717,5054742,5054748,5054752,5054767,5054770,5054785,5054787,5054798,5054799,5054803,5054805,5054810,5054813,5054830,5054841,5054854,5054860,5054866,5054872,5054873,5054875,5054878,5054880,5054886,5054890,5054891,5054898,5054900,5054902,5054907,5054908,5054916,5054933,5054949,5054955,5054956,5054971,5054976,5054979,5054994,5055024,5055030,5055033,5055034,5055042,5055053,5055056,5055063,5055069,5055072,5055075,5055076,5055083,5055084,5055100,5055103,5055114,5055119,5055122,5055125,5055128,5055133,5055134,5055137,5055142,5055157,5055173,5055182,5055184,5055189,5055192,5055195,5055201,5055202,5055213,5055220,5055222,5055226,5055234,5055247,5055248,5055258,5055262,5055269,5055272,5055284,5055291,5055297,5055300,5055309,5055310,5055324,5055333,5055338,5055340,5055348,5055350,5055357,5055377,5055382,5055387,5055389,5055399,5055404,5055408,5055410,5055415,5055424,5055439,5055440,5055442,5055444,5055446,5055447,5055449,5055450,5055457,5055466,5055478,5055479,5055486,5055494,5055510,5055524,5055528,5055529,5055540,5055541,5055542,5055543,5055545,5055555,5055557,5055576,5055581,5055587,5055592,5055593,5055595,5055598,5055600,5055607,5055616,5055634,5055636,5055641,5055651,5055653,5055660,5055664,5055672,5055678,5055692,5055702,5055703,5055709,5055725,5055736,5055739,5055741,5055745,5055751,5055756,5055758,5055762,5055770,5055796,5055802,5055803,5055824,5055838,5055843,5055857,5055866,5055871,5055877,5055879,5055886,5055897,5055902,5055907,5055911,5055915,5055922,5055923,5055930,5055932,5055933,5055939,5055942,5055945,5055952,5055956,5055968,5055993,5055996,5056001,5056011,5056013,5056025,5056029,5056030,5056039,5056043,5056050,5056056,5056065,5056067,5056086,5056094,5056105,5056117,5056119,5056139,5056144,5056145,5056162,5056168,5056186,5056190,5056204,5056205,5056207,5056210,5056220,5056221,5056223,5056233,5056242,5056251,5056257,5056262,5056264,5056265,5056274,5056282,5056289,5056294,5056295,5056297,5056300,5056316,5056320,5056322,5056323,5056327,5056337,5056357,5056362,5056371,5056375,5056380,5056383,5056384,5056397,5056399,5056404,5056410,5056428,5056434,5056435,5056448,5056452,5056468,5056471,5056477,5056505,5056529,5056547,5056559,5056565,5056574,5056579,5056584,5056588,5056589,5056597,5056612,5056613,5056625,5056627,5056640,5056670,5056673,5056674,5056676,5056679,5056697,5056698,5056700,5056703,5056724,5056725,5056729,5056730,5056731,5056736,5056747,5056752,5056762,5056771,5056783,5056786,5056789,5056792,5056804,5056809,5056811,5056814,5056815,5056820,5056826,5056828,5056831,5056837,5056845,5056851,5056855,5056857,5056867,5056871,5056872,5056885,5056900,5056903,5056904,5056914,5056915,5056931,5056949,5056954,5056962,5056980,5056986,5056992,5056997,5057004,5057007,5057016,5057023,5057025,5057032,5057037,5057060,5057063,5057066,5057067,5057075,5057085,5057087,5057089,5057091,5057096,5057098,5057099,5057112,5057114,5057121,5057124,5057137,5057142,5057158,5057160,5057161,5057167,5057176,5057196,5057197,5057198,5057209,5057213,5057219,5057233,5057242,5057264,5057271,5057273,5057274,5057288,5057297,5057299,5057301,5057303,5057307,5057308,5057310,5057315,5057323,5057330,5057337,5057340,5057341,5057345,5057350,5057354,5057365,5057370,5057376,5057377,5057385,5057389,5057393,5057397,5057398,5057400,5057406,5057417,5057418,5057421,5057422,5057424,5057435,5057439,5057452,5057454,5057457,5057494,5057496,5057505,5057508,5057517,5057518,5057521,5057531,5057542,5057549,5057557,5057571,5057583,5057590,5057600,5057612,5057616,5057618,5057622,5057630,5057633,5057641,5057642,5057659,5057670,5057672,5057673,5057677,5057689,5057691,5057697,5057706,5057707,5057725,5057729,5057745,5057757,5057762,5057763,5057764,5057770,5057778,5057780,5057781,5057790,5057796,5057801,5057810,5057815,5057831,5057845,5057846,5057850,5057859,5057862,5057876,5057885,5057897,5057924,5057925,5057946,5057952,5057967,5057970,5057971,5057972,5058008,5058010,5058022,5058039,5058049,5058059,5058065,5058106,5058107,5058111,5058147,5058157,5058164,5058166,5058172,5058173,5058189,5058205,5058206,5058215,5058217,5058226,5058227,5058247,5058251,5058252,5058256,5058259,5058260,5058262,5058267,5058268,5058269,5058275,5058277,5058284,5058300,5058304,5058309,5058322,5058323,5058329,5058349,5058358,5058367,5058371,5058386,5058388,5058392,5058398,5058401,5058406,5058417,5058437,5058459,5058460,5058469,5058478,5058488,5058490,5058492,5058507,5058529,5058530,5058533,5058548,5058550,5058557,5058572,5058573,5058579,5058581,5058585,5058590,5058597,5058598,5058604,5058608,5058611,5058636,5058642,5058660,5058674,5058697,5058709,5058736,5058738,5058742,5058755,5058759,5058764,5058770,5058772,5058782,5058783,5058791,5058794,5058820,5058847,5058863,5058869,5058872,5058876,5058892,5058893,5058916,5058929,5058939,5058944,5058945,5058971,5058980,5058995,5058996,5059005,5059007,5059016,5059020,5059022,5059029,5059062,5059074,5059092,5059115,5059127,5059129,5059130,5059139,5059143,5059144,5059148,5059151,5059162,5059166,5059174,5059176,5059190,5059225,5059227,5059234,5059258,5059265,5059297,5059302,5059320,5059334,5059346,5059351,5059358,5059359,5059360,5059386,5059416,5059418,5059431,5059455,5059476,5059479,5059499,5059513,5059514,5059515,5059531,5059587,5059653,5059654,5059661,5059719,5059722,5059761,5059811,5059819,5059872,5060152,5060156,5060157,5060240,5060439,5060498,5060551,5060567,5060640,5060665,5060805,5060827,5060925,5061160,5061269,5061291,5061482,5061497,5061690,5061699,5061963,5062252,5062570,5062596,5062598,5062667,5062684,5063177,5063180,5063215,5063243,5063254,5063347,5063352,5063529,5063823,5064296,5064378,5064379,5064389,5064511,5064552,5064858,5064932,5065308,5065368,5065448'
        );

        $order_str = $arr[$num];
        $order_arr = explode(',', $order_str);
        $params = array();
        $params['channel'] = '24';
        $params['comment'] = '多扣司机保险费返现';
        $params['cast'] = '2';
        foreach ($order_arr as $order_id) {
            echo $order_id.":";
            $params['order_id'] = $order_id;
            $order = Order::model()->getOrderById($order_id);
            if ($order) {
                $params['user'] = $order['driver_id'];
                $params['city_id'] = $order['city_id'];
                OrderSettlement::model()->driverRecharge($params, true);
                echo "充值";
            }
            echo "\n";
        }

    }

}





















