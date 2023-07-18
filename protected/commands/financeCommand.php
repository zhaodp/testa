<?php
/**
 * Cre
 * aut
 * Dat
 * Tim
 */
Yii::import('application.models.schema.report.ReportFsAccountRp');
Yii::import('application.models.schema.report.ReportFsAccountTag');
Yii::import('application.models.schema.report.ReportFsVipTag');


class financeCommand extends CConsoleCommand
{

    /*---------------------------------刷新信息费之前的数据------------------------------------*/

    /**
     * 刷信息费分表city_id数据
     * @auther mengtianxue
     * php yiic.php finance AccountRefreshCity_id --month=201308
     */
    public function ActionAccountRefreshCity_id($month)
    {
        $city_arr = Dict::items('city_prefix');
        foreach ($city_arr as $k => $v) {
            $table_name = 't_employee_account_' . $month;
            $sql = "UPDATE $table_name SET city_id = :city_id WHERE left( user, 2 ) = :city_prefix";
            $connection = Yii::app()->db_finance;
            $command = $connection->createCommand($sql);
            $command->bindParam(":city_id", $k);
            $command->bindParam(":city_prefix", $v);
            $command->execute();
            echo $k . " " . $v . "\n";
        }
    }

    /**
     * 刷信息费分表channel数据
     * @param $month
     * @auther mengtianxue
     * php yiic.php finance AccountRefreshChannel --month=201308
     */
    public function ActionAccountRefreshChannel($month)
    {
        $account_type = Dict::items('account_type');
        foreach ($account_type as $k => $v) {
            $table_name = 't_employee_account_' . $month;
            $sql = "UPDATE $table_name SET channel = :channel WHERE type = :type";
            $connection = Yii::app()->db_finance;
            $command = $connection->createCommand($sql);
            $command->bindParam(":channel", $k);
            $command->bindParam(":type", $k);
            $command->execute();
            echo $k . " " . $v . "\n";
        }
    }

    /**
     * 刷信息费之前的channel数据
     * @param $month
     * @auther mengtianxue
     * php yiic.php finance AccountRefreshIncomeChannel --month=201308
     */
    public function ActionAccountRefreshIncomeChannel($month)
    {
        $channel_arr = array(
            '10' => '优惠劵未返还',
            '11' => '交行划款',
            '12' => '现金充值',
            '14' => array('兑代金券', '代金卡'),
            '17' => '信息费提现',
            '18' => array('重复扣费', '重复报单', 'vip填写实际金额', '屏蔽状态下扣罚金',),
            '21' => '解约退信息费',
            '22' => '投诉',
            '23' => '充值错误',
            '24' => '其他'
        );
        foreach ($channel_arr as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $s) {
                    $this->updateChannel($month, $k, $s);
                }
            } else {
                $this->updateChannel($month, $k, $v);
            }
        }
    }

    public function updateChannel($month, $k, $v)
    {
        $table_name = 't_employee_account_' . $month;
        if ($k == 24) {
            $sql = "UPDATE $table_name SET channel = :channel WHERE type = :type and channel = 5";
        } else {
            $sql = "UPDATE $table_name SET channel = :channel WHERE type = :type and comment like '%$v%' ";
        }
        $type = 5;
        $connection = Yii::app()->db_finance;
        $command = $connection->createCommand($sql);
        $command->bindParam(":channel", $k);
        $command->bindParam(":type", $type);
        $command->execute();
        echo $k . "==>" . $v . "\n";
    }

    /*---------------------------------刷新信息费之前的数据------------------------------------*/


    /*---------------------------------信息费统计当前的数据------------------------------------*/
    /**
     * 统计信息费  每天要跑的脚本
     * @auther mengtianxue
     * php yiic.php finance DailyAccountReport
     */
    public function ActionDailyAccountReport()
    {
        $min_day = date('Y-m-d', strtotime("-1 day")); //$date_time
        $max_date = date('Y-m-d'); //$day_time

        $city = Dict::items("city");
        unset($city['0']);
        foreach ($city as $k => $v) {
            $city_id = $k;
            //获取当前的余额
            $balance = DriverBalance::model()->getBalance($city_id);
            echo $balance . "\n";
            //获取今天使用金额
            $money = BAccount::model()->getDailyCountAccount($max_date, $city_id);
            echo $money . "\n";

            //当天凌晨的余额
            $end_balance = $balance - $money;
            echo $balance . "\n";

            $max_arr = array();
            $min_arr = array();
            $countAccount = BDailyAccountReport::model()->getDailyCountAccount($min_day, $city_id);
            if ($countAccount) {
                $max_arr['start_balance'] = $end_balance;
                $min_arr['add_balance'] = $countAccount['add_money'];
                $min_arr['minus_balance'] = $countAccount['minus_money'];
            }
            $min_arr['city_id'] = $city_id;
            $min_arr['daily_date'] = $min_day;
            $min_arr['month_date'] = date('Y-m', strtotime($min_day));
            $min_arr['end_balance'] = $end_balance;
            $min_arr['status'] = 0;
            $min_arr['created'] = date('Y-m-d H:i:s');

            $max_arr['city_id'] = $city_id;
            $max_arr['daily_date'] = $max_date;
            $max_arr['month_date'] = date('Y-m', strtotime($max_date));
            $max_arr['status'] = 0;
            $max_arr['created'] = date('Y-m-d H:i:s');
            $params = array($max_arr, $min_arr);
            $this->AddReportFsAccountTag($params);
        }
    }

    /*---------------------------------信息费统计当前的数据------------------------------------*/


    /*---------------------------------信息费刷新老数据------------------------------------*/
    /**
     * 信息费按月详情汇总
     * @param int $month
     * @auther mengtianxue
     * php yiic.php finance MontyAccountReport --month=2013-08
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

        for ($d = 1; $d <= $day; $d++) {

            $daily = $month . $d;
            echo $daily . "\n\n";
            $this->ActionAccountReport($daily);
        }
    }

    /**
     * 信息费详情汇总
     * @param $day
     * @auther mengtianxue
     * php yiic.php finance AccountReport --day=2013-12-18
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

        $account_city = Yii::app()->db_finance->createCommand()
            ->select("channel, city_id, sum(cast) as money")
            ->from($table_name)
            ->where($where, $params)
            ->group("city_id, channel")
            ->queryAll();

        if ($account_city) {
            //检查统计是否存在，如果存在删除
            $account_report = BDailyAccountReport::model()->getDailyAccountReport($date_time);
            if ($account_report) {
                ReportFsAccountRp::model()->deleteAll('account_date = :account_date and channel not in(-1, 99)', array(':account_date' => $date_time));
            }
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


    /**
     * 刷新以前数据
     * @param int $date
     * @auther mengtianxue
     * php yiic.php finance EveryBalance --date=2013-08-31
     */
    public function ActionEveryBalance($date = 0)
    {
        if (empty($date)) {
            $time = strtotime("-1 day");
            $day = date('d', $time);
            $date_time = date('Y-m-d', $time);
        } else {
            $time = strtotime($date);
            $day = date('t', $time);
            $date_time = date('Y-m-' . $day, $time);
        }
        $model_tag = new ReportFsAccountTag();

        for ($d = 0; $d < $day; $d++) {
            $max_date = date('Y-m-d', strtotime("-$d day", $time));
            $k = $d + 1;
            $min_day = date('Y-m-d', strtotime("-$k day", $time));
            echo $max_date . '=>' . $min_day . "\n";

            //获取当前余额
            $account_report = $model_tag->findAll('daily_date = :daily_date', //10号余额
                array(':daily_date' => $max_date));
            if ($account_report) {
                foreach ($account_report as $list) {
                    $city_id = $list['city_id'];
                    $end_balance = $list['end_balance'];

                    $max_arr = array();
                    $min_arr = array();
                    $countAccount = BDailyAccountReport::model()->getDailyCountAccount($max_date, $city_id);
                    if ($countAccount) {
                        $max_arr['add_balance'] = $countAccount['add_money'];
                        $max_arr['minus_balance'] = $countAccount['minus_money'];
                        $start_balance = $end_balance - $countAccount['add_money'] - $countAccount['minus_money'];
                        $max_arr['start_balance'] = $start_balance;
                        $min_arr['end_balance'] = $start_balance;
                    }
                    $max_arr['city_id'] = $city_id;
                    $max_arr['daily_date'] = $max_date;
                    $max_arr['month_date'] = date('Y-m', strtotime($max_date));
                    $max_arr['end_balance'] = $end_balance;
                    $max_arr['status'] = 0;
                    $max_arr['created'] = date('Y-m-d H:i:s');

                    $min_arr['city_id'] = $city_id;
                    $min_arr['daily_date'] = $min_day;
                    $min_arr['month_date'] = date('Y-m', strtotime($min_day));
                    $min_arr['status'] = 0;
                    $min_arr['created'] = date('Y-m-d H:i:s');
                    $params = array($max_arr, $min_arr);
                    $this->AddReportFsAccountTag($params);

                }
            }
        }
    }

    public function AddReportFsAccountTag($params)
    {
        if ($params) {
            $i = 0;
            foreach ($params as $arr) {
                $model_tag = ReportFsAccountTag::model()->find('daily_date = :daily_date and city_id = :city_id',
                    array(':daily_date' => $arr['daily_date'], ':city_id' => $arr['city_id']));
                if (!$model_tag) {
                    $model_tag = new ReportFsAccountTag();
                }
                $model_tag->attributes = $arr;
                if ($model_tag->save()) {
                    echo "成功\n";
                    $daily_account = array();
                    $daily_account['status'] = 1;
                    $daily_account['created'] = date('Y-m-d H:i:s');
                    $daily_account['city_id'] = $arr['city_id'];
                    $daily_account['account_date'] = $arr['daily_date'];
                    if ($i == 0) {
                        $daily_account['bill_type'] = 0;
                        $daily_account['channel'] = -1;
                        $daily_account['money'] = $arr['start_balance'];
                    } else {
                        $daily_account['bill_type'] = 3;
                        $daily_account['channel'] = 99;
                        $daily_account['money'] = $arr['end_balance'];
                    }
                    $this->AddReportFsAccountRp($daily_account);
                }
                $i++;
            }
        }
    }

    public function AddReportFsAccountRp($params)
    {
        if ($params) {
            $account_report = ReportFsAccountRp::model()->find('account_date = :account_date and city_id = :city_id and bill_type = :bill_type',
                array(':account_date' => $params['account_date'], ':city_id' => $params['city_id'], ':bill_type' => $params['bill_type']));
            if (!$account_report) {
                $account_report = new ReportFsAccountRp();
            }
            $account_report->attributes = $params;
            if ($account_report->save()) {
                echo "成功\n";
            }
        }
    }

    /**
     * @auther mengtianxue
     * php yiic.php finance DeleteRp
     */
    public function actionDeleteRp()
    {
        $sql = "delete FROM t_fs_account_rp";
        $connection = Yii::app()->dbreport;
        $command = $connection->createCommand($sql);
        $command->execute();
    }

    /**
     * @auther mengtianxue
     * php yiic.php finance DeleteTag
     */
    public function actionDeleteTag()
    {
        $sql = "delete FROM t_fs_account_tag";
        $connection = Yii::app()->dbreport;
        $command = $connection->createCommand($sql);
        $command->execute();
    }

    /*---------------------------------信息费刷新老数据------------------------------------*/


    /*---------------------------------刷新VIP之前的数据------------------------------------*/

	/**
	 * @auther mengtianxue
	 * php yiic.php finance VipTradeInfo
	 */
	public function ActionVipTradeInfo()
	{
		$sql = "insert into db_report.t_fs_vip_trade_info(vipcard,type,order_id,amount, daily_date, created) select vipcard, TYPE ,order_id,
 amount, from_unixtime( created, '%Y-%m-%d %H:%i:%s' ), now() from db_car.`t_vip_trade`";
		$connection = Yii::app()->dbreport;
		$command = $connection->createCommand($sql);
		$command->execute();
		echo "success\n";
	}
    /**
     * @auther mengtianxue
     * php yiic.php finance VipRefreshChannel
     */
    public function ActionVipRefreshChannel()
    {
        $channel_arr = array(
            '0' => '0',
            '1' => '98',
            '2' => '2',
        );

        foreach ($channel_arr as $k => $v) {
            $table_name = 't_fs_vip_trade_info';
            $sql = "UPDATE $table_name SET channel = :channel WHERE type = :type";
            $connection = Yii::app()->dbreport;
            $command = $connection->createCommand($sql);
            $command->bindParam(":channel", $v);
            $command->bindParam(":type", $k);
            $command->execute();
            echo $k . " " . $v . "\n";
        }
    }

    /**
     * @auther mengtianxue
     * php yiic.php finance VipRefreshOrder
     */
    public function ActionVipRefreshOrder()
    {
        $table_name = 't_fs_vip_trade_info';
        $sql = "UPDATE $table_name SET channel = 98 WHERE order_id != ''";
        $connection = Yii::app()->dbreport;
        $command = $connection->createCommand($sql);
        $command->execute();
        echo "success\n";
    }

    /**
     * 刷t_vip_order 表中的数据
     * @param $date
     * @return mixed
     * @auther mengtianxue
     * php yiic.php finance RefreshVipTrade --date=2012-12
     */
    public function ActionRefreshVipTrade($date = 0)
    {
        Yii::import('application.models.schema.report.ReportFsVipTradeInfo');

        if (empty($date)) {
            $start_time = date('Y-m-01');
            $end_time = date('Y-m-01', strtotime('next month'));
        } else {
            $start_time = date('Y-m-01', strtotime($date));
            $end_time = date('Y-m-01', strtotime('next month', strtotime($date)));
        }
        echo $start_time . "\n" . $end_time . "\n";

        $pageSize = 200;
        //查询条件
        $criteria = new CDbCriteria;
        $criteria->select = "*";
        $criteria->addCondition("channel = 98");
        $criteria->addCondition('daily_date >= :start_time');
        $criteria->addCondition('daily_date < :end_time');
        $criteria->params = array(':start_time' => $start_time, ':end_time' => $end_time);

        $count = ReportFsVipTradeInfo::model()->count($criteria);
        echo $count . "\n";
        $page = ceil($count / $pageSize);
        echo "\n-------------------\n" . $page . "\n-------------------\n";
        for ($i = 0; $i <= $page; $i++) {
            echo "\n" . $i . "\n";

            $offset = $i * $pageSize;
            $criteria->limit = $pageSize;
            $criteria->offset = $offset;
            $criteria->order = "id desc";

            $vip_trade = ReportFsVipTradeInfo::model()->findAll($criteria);
            $k = 1;
            foreach ($vip_trade as $list) {
                $order_id = $list->order_id;

                $params = array();
                if ($start_time < '2013-01-01') {
                    $order = $this->getOrdersById($order_id);
                } else {
                    $order = Order::model()->getOrdersById($order_id);
                }

                if ($order) {
                    $params['phone'] = $order['phone'];
                    $params['city_id'] = $order['city_id'];
                }
                $params['cast'] = $params['insurance'] = $params['Invoice_money'] = $params['income'] = 0;

                $account = BAccount::model()->getAccountByOrder_id($start_time, $list->order_id);
                //vip销费正常扣款记录小于等于五条   重新结账 不记录信息费 保险和发票
                if (!empty($account) && count($account) <= 5) {

                    foreach ($account as $val) {
                        $params['driver_id'] = empty($val->user) ? '' : $val->user;
                        $cast = empty($val->cast) ? 0 : $val->cast;
                        switch ($val->type) {
                            case 0:
                                $params['income'] = $cast;
                                break;
                            case 1:
                                $params['cast'] = $cast;
                                break;
                            case 2:
                                $params['Invoice_money'] = $cast;
                                break;
                            case 6:
                                $params['insurance'] = $cast;
                                break;
                        }
                    }
                }
                $params['balance'] = $list->amount - $params['cast'] - $params['insurance'] - $params['Invoice_money'];
                ReportFsVipTradeInfo::model()->updateAll($params, 'id = :id', array(':id' => $list->id));
                echo $i . "-" . $k . ":" . $list->order_id . "\n";
                $k++;
            }
        }
    }

    /**
     * 刷2012年数据用的方法
     * @param $order_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getOrdersById($order_id)
    {
        $order = Yii::app()->db_readonly->createCommand()
            ->select('*')
            ->from('t_order_2012')
            ->where('order_id=:order_id', array(':order_id' => $order_id))
            ->queryRow();
        return $order;
    }


    /**
     * 刷t_vip_order 表中的数据
     * @param $date
     * @param $type
     * @return mixed
     * @auther mengtianxue
     * php yiic.php finance RefreshVipTradeBug --date=2012-12 --type=1
     */
    public function ActionRefreshVipTradeBug($date = 0, $type = 0)
    {
        Yii::import('application.models.schema.report.ReportFsVipTradeInfo');

        if (empty($date)) {
            $start_time = date('Y-m-01');
            $end_time = date('Y-m-01', strtotime('next month'));
        } else {
            $start_time = date('Y-m-01', strtotime($date));
            $end_time = date('Y-m-01', strtotime('next month', strtotime($date)));
        }
        echo $start_time . "\n" . $end_time . "\n";

        if ($type === 0) {
            $table_date = date('Y-m-01', strtotime('-1 month', strtotime($date)));
        } else {
            $table_date = $end_time;
        }

        $pageSize = 200;
        //查询条件
        $criteria = new CDbCriteria;
        $criteria->select = "*";
        $criteria->addCondition("channel = 98");
        $criteria->addCondition("balance = 0");

        $criteria->addCondition('daily_date >= :start_time');
        $criteria->addCondition('daily_date < :end_time');
        $criteria->params = array(':start_time' => $start_time, ':end_time' => $end_time);

        $count = ReportFsVipTradeInfo::model()->count($criteria);
        echo $count . "\n";
        $page = ceil($count / $pageSize);
        echo "\n-------------------\n" . $page . "\n-------------------\n";
        for ($i = 0; $i <= $page; $i++) {
            echo "\n" . $i . "\n";

            $offset = $i * $pageSize;
            $criteria->limit = $pageSize;
            $criteria->offset = $offset;
            $criteria->order = "id desc";

            $vip_trade = ReportFsVipTradeInfo::model()->findAll($criteria);
            $k = 1;
            foreach ($vip_trade as $list) {
                $order_id = $list->order_id;

                $params = array();
                if ($start_time < '2013-01-01') {
                    $order = $this->getOrdersById($order_id);
                } else {
                    $order = Order::model()->getOrdersById($order_id);
                }

                if ($order) {
                    $params['phone'] = $order['phone'];
                    $params['city_id'] = $order['city_id'];
                }
                $params['cast'] = $params['insurance'] = $params['Invoice_money'] = 0;

                $account = BAccount::model()->getAccountByOrder_id($table_date, $list->order_id);
                //vip销费正常扣款记录小于等于五条   重新结账 不记录信息费 保险和发票
                if (!empty($account) && count($account) <= 5) {
                    foreach ($account as $val) {
                        $params['driver_id'] = empty($val->user) ? '' : $val->user;
                        $cast = empty($val->cast) ? 0 : $val->cast;
                        switch ($val->type) {
                            case 0:
                                $params['income'] = $cast;
                                break;
                            case 1:
                                $params['cast'] = $cast;
                                break;
                            case 2:
                                $params['Invoice_money'] = $cast;
                                break;
                            case 6:
                                $params['insurance'] = $cast;
                                break;
                        }
                    }
                    $params['balance'] = $list->amount - $params['cast'] - $params['insurance'] - $params['Invoice_money'];
                } else {
                    if ($type == 2) {
                        $params['balance'] = $list->amount;
                    }
                }
                ReportFsVipTradeInfo::model()->updateAll($params, 'id = :id', array(':id' => $list->id));
                echo $i . "-" . $k . ":" . $list->order_id . "\n";
                $k++;
            }
        }
    }


    /**
     * vip详情汇总
     * @param int $day
     * @auther mengtianxue
     * php yiic.php finance DailyOrderTrade --day=2013-12-24
     */
    public function ActionDailyOrderTrade($day = 0)
    {
        Yii::import('application.models.schema.report.ReportFsVipTradeInfo');
        //天
        if (empty($day)) {
            $end_time = strtotime(date('Y-m-d'));
            $start_time = $end_time - 86400;
        } else {
            $start_time = strtotime($day);
            $end_time = $start_time + 86400;
        }

        $criteria = new CDbCriteria;
        $criteria->select = "*";
        $criteria->addCondition('created >= :start_time');
        $criteria->addCondition('created < :end_time');
        $criteria->params = array(':start_time' => $start_time, ':end_time' => $end_time);
        $criteria->order = "id desc";
        $vip_trade = VipTrade::model()->findAll($criteria);
        if ($vip_trade) {
            foreach ($vip_trade as $vip) {
                $params = array();
                $params['type'] = $vip->type;
                $params['vipcard'] = $vip->vipcard;
                $params['order_id'] = $vip->order_id;
                $params['amount'] = $vip->amount;
                $params['daily_date'] = date('Y-m-d H:i:s', $vip->created);
                $params['created'] = date('Y-m-d H:i:s');

                if (($vip->type == 0 || $vip->type == 2) && $vip->order_id == 0) {
                    $params['channel'] = $vip->type;
                } else {
                    $params['channel'] = 98;

                    $order = Order::model()->getOrdersById($vip->order_id);
                    if ($order) {
                        $params['phone'] = $order['phone'];
                        $params['city_id'] = $order['city_id'];
                    }
                    $params['cast'] = $params['insurance'] = $params['Invoice_money'] = 0;
                    $account = BAccount::model()->getAccountByOrder_id(date('Y-m-d', $start_time), $vip->order_id);
                    //vip销费正常扣款记录小于等于五条   重新结账 不记录信息费 保险和发票
                    if (!empty($account) && count($account) <= 5) {
                        foreach ($account as $val) {
                            $params['driver_id'] = empty($val->user) ? '' : $val->user;
                            $cast = empty($val->cast) ? 0 : $val->cast;
                            switch ($val->type) {
                                case 0:
                                    $params['income'] = $cast;
                                    break;
                                case 1:
                                    $params['cast'] = $cast;
                                    break;
                                case 2:
                                    $params['Invoice_money'] = $cast;
                                    break;
                                case 6:
                                    $params['insurance'] = $cast;
                                    break;
                            }
                        }
                    }
                    $params['balance'] = $vip->amount - $params['cast'] - $params['insurance'] - $params['Invoice_money'];
                }
                $this->addVipTradeInfo($params);
            }
        }
    }

    public function addVipTradeInfo($params)
    {

        $model = ReportFsVipTradeInfo::model()->find('order_id = :order_id and vipcard = :vipcard and amount = :amount and daily_date = :daily_date',
            array(':order_id' => $params['order_id'], ':vipcard' => $params['vipcard'], ':amount' => $params['amount'], ':daily_date' => $params['daily_date']));
        if (!$model) {
            $model = new ReportFsVipTradeInfo();
        }
        $model->attributes = $params;
        if ($model->save(false)) {
            echo $params['order_id'] . "success\n";
        } else {
            echo $params['order_id'] . "error\n";
        }
    }


    /*---------------------------------刷新VIP之前的数据------------------------------------*/


    /*---------------------------------刷新VIP统计之前的数据------------------------------------*/
    /**
     * VIP以月统计
     * @param int $month
     * @auther mengtianxue
     * php yiic.php finance MontyVipReport --month=2013-12
     */
    public function ActionMontyVipReport($month = 0)
    {
        $month_new = date('Y-m');

        if (empty($month) || $month_new <= $month) {
            $day = date('d');
            $month = date('Y-m-');
        } else {
            $day = date('t', strtotime($month));
            $month = date('Y-m-', strtotime($month));
        }

        for ($d = 1; $d <= $day; $d++) {

            $daily = $month . $d;
            echo $daily . "\n\n";
            $this->ActionVipReport($daily);
        }
    }

    /**
     * VIP以日统计
     * @param $day
     * @auther mengtianxue
     * php yiic.php finance VipReport --day=2013-12-24
     */
    public function ActionVipReport($day = 0)
    {
        Yii::import('application.models.schema.report.ReportFsVipRp');
        Yii::import('application.models.schema.report.ReportFsVipTradeInfo');
        //日
        if (empty($day)) {
            $end_time = date('Y-m-d');
            $start_time = date('Y-m-d', strtotime("-1 day"));
        } else {
            $start_time = $day;
            $end_time = date('Y-m-d', (strtotime($day) + 86400));
        }
        //检查当天数据是否存在  存在删除
        $vip_report = ReportFsVipRp::model()->find('account_date = :account_date', array(':account_date' => $start_time));
        if ($vip_report) {
            ReportFsVipRp::model()->deleteAll('account_date = :account_date and channel not in(-1, 99)', array(':account_date' => $start_time));
        }

        $criteria = new CDbCriteria;
        $criteria->select = "city_id, channel, type, daily_date, sum(income) as income, sum(amount) as amount, sum(cast) as cast, sum(insurance) as insurance, sum(Invoice_money) as Invoice_money,sum(balance) as balance";
        $criteria->addCondition('daily_date >= :start_time');
        $criteria->addCondition('daily_date < :end_time');
        $criteria->params = array(':start_time' => $start_time, ':end_time' => $end_time);
        $criteria->group = "channel";

        $vip_trade = ReportFsVipTradeInfo::model()->findAll($criteria);
        foreach ($vip_trade as $list) {
            $daily_date = empty($list->daily_date) ? 0 : date('Y-m-d', strtotime($list->daily_date));
            $params = Array
            (
                'city_id' => 0,
                'channel' => $list->channel,
                'type' => $list->type,
                'daily_date' => $daily_date,
                'income' => $list->income,
                'amount' => $list->amount,
                'cast' => $list->cast,
                'insurance' => $list->insurance,
                'Invoice_money' => $list->Invoice_money,
                'balance' => $list->balance
            );

            if ($this->addVipReport($params)) {
                echo $daily_date . "\n";
            }
        }
    }

    public function addVipReport($params)
    {
        Yii::import('application.models.schema.report.ReportFsVipRp');
        if ($params['channel'] <= 4 && $params['channel'] >= 0) {
            $params_arr = Array
            (
                'city_id' => $params['city_id'],
                'account_date' => $params['daily_date'],
                'channel' => $params['channel'],
                'money' => $params['amount'],
                'status' => 0,
                'bill_type' => 1,
                'created' => date('Y-m-d H:i:s')
            );
            $this->insertVipReport($params_arr);
        } else {
            $params_arr = Array
            (
                'city_id' => $params['city_id'],
                'account_date' => $params['daily_date'],
                'status' => 0,
                'bill_type' => 2,
                'created' => date('Y-m-d H:i:s')
            );

            $money = array(
                '5' => $params['cast'],
                '6' => $params['insurance'],
                '7' => $params['balance'],
                '8' => $params['Invoice_money'],
                '9' => $params['amount'],
                '10' => $params['income']
            );
            foreach ($money as $k => $v) {
                if (!empty($v)) {
                    $params_arr['channel'] = $k;
                    $params_arr['money'] = $v;
                    $this->insertVipReport($params_arr);
                }
            }
        }
        return true;
    }

    public function insertVipReport($params)
    {
        Yii::import('application.models.schema.report.ReportFsVipRp');
        $vip_order = new ReportFsVipRp();
        $vip_order->attributes = $params;
        if ($vip_order->insert()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @auther mengtianxue
     * php yiic.php finance FsVipRp
     */
    public function ActionFsVipRp()
    {
        Yii::import('application.models.schema.report.ReportFsVipRp');
        $min_date = date('Y-m-d', strtotime("-1 day")); //$date_time
        $max_date = date('Y-m-d'); //day_time
        //获取当前的余额
        $vip = Vip::model()->getVipBalanceTotal();
        $balance = $vip->balance;

        //获取今天使用金额
        $money = BAccount::model()->getDailyVipCountAccount($max_date);

        //当天凌晨的余额
        $end_balance = $balance + $money;
        $max_arr = array();
        $min_arr = array();

        $max_arr['start_balance'] = $end_balance;
        $countAccount = BReportDailyVipReport::model()->getDailyCountAccount($min_date);
        if ($countAccount) {
            $min_arr['add_balance'] = $countAccount['add_money'];
            $min_arr['minus_balance'] = $countAccount['minus_money'];
        }

        $min_arr['city_id'] = 0;
        $min_arr['daily_date'] = $min_date;
        $min_arr['month_date'] = date('Y-m', strtotime($min_date));
        $min_arr['end_balance'] = $end_balance;
        $min_arr['status'] = 0;
        $min_arr['created'] = date('Y-m-d H:i:s');

        $max_arr['city_id'] = 0;
        $max_arr['daily_date'] = $max_date;
        $max_arr['month_date'] = date('Y-m', strtotime($max_date));
        $max_arr['status'] = 0;
        $max_arr['created'] = date('Y-m-d H:i:s');
        $params = array($max_arr, $min_arr);
        $this->AddReportFsVipTag($params);


    }

    public function AddReportFsVipTag($params)
    {
        if ($params) {
            $i = 0;
            foreach ($params as $arr) {
                $model_tag = ReportFsVipTag::model()->find('daily_date = :daily_date',
                    array(':daily_date' => $arr['daily_date']));
                if (!$model_tag) {
                    $model_tag = new ReportFsVipTag();
                }
                $model_tag->attributes = $arr;
                if ($model_tag->save()) {
                    echo "成功\n";
                    $daily_account = array();
                    $daily_account['status'] = 1;
                    $daily_account['created'] = date('Y-m-d H:i:s');
                    $daily_account['city_id'] = 0;
                    $daily_account['account_date'] = $arr['daily_date'];
                    if ($i == 0) {
                        $daily_account['bill_type'] = 0;
                        $daily_account['channel'] = -1;
                        $daily_account['money'] = $arr['start_balance'];
                    } else {
                        $daily_account['bill_type'] = 3;
                        $daily_account['channel'] = 99;
                        $daily_account['money'] = $arr['end_balance'];
                    }
                    $this->AddReportFsVipRp($daily_account);
                }
                $i++;
            }
        }
    }

    public function AddReportFsVipRp($params)
    {
        if ($params) {
            $vip_report = ReportFsVipRp::model()->find('account_date = :account_date and bill_type = :bill_type',
                array(':account_date' => $params['account_date'], ':bill_type' => $params['bill_type']));
            if (!$vip_report) {
                $vip_report = new ReportFsVipRp();
            }
            $vip_report->attributes = $params;
            if ($vip_report->save(false)) {
                echo "成功ss\n";
            }
        }
    }


    /**
     * 对每一天进行日统计
     * @param $date
     * @return bool
     * @auther mengtianxue
     * php yiic.php finance EveryFsVipBalance --date=2013-12-01
     */
    public function ActionEveryFsVipBalance($date = 0)
    {
        if (empty($date)) {
            $time = strtotime("-1 day");
            $day = date('d', $time);
        } else {
            $time = strtotime($date);
            $day = date('t', $time);
        }
        $model_tag = new ReportFsVipTag();

        for ($d = 0; $d < $day; $d++) {
            $max_date = date('Y-m-d', strtotime("-$d day", $time));
            $k = $d + 1;
            $min_day = date('Y-m-d', strtotime("-$k day", $time));
            echo $max_date . '=>' . $min_day . "\n";

            //获取当前余额
            $account_report = $model_tag->find('daily_date = :daily_date', //10号余额
                array(':daily_date' => $max_date));
            if ($account_report) {
                $city_id = 0;
                $end_balance = $account_report->end_balance;

                $max_arr = array();
                $min_arr = array();
                $countAccount = BReportDailyVipReport::model()->getDailyCountAccount($max_date);
                if ($countAccount) {
                    $max_arr['add_balance'] = $countAccount['add_money'];
                    $max_arr['minus_balance'] = $countAccount['minus_money'];
                    $start_balance = $end_balance - $countAccount['add_money'] - $countAccount['minus_money'];
                    $max_arr['start_balance'] = $start_balance;
                    $min_arr['end_balance'] = $start_balance;
                }
                $max_arr['city_id'] = $city_id;
                $max_arr['daily_date'] = $max_date;
                $max_arr['month_date'] = date('Y-m', strtotime($max_date));
                $max_arr['end_balance'] = $end_balance;
                $max_arr['status'] = 0;
                $max_arr['created'] = date('Y-m-d H:i:s');

                $min_arr['city_id'] = $city_id;
                $min_arr['daily_date'] = $min_day;
                $min_arr['month_date'] = date('Y-m', strtotime($min_day));
                $min_arr['status'] = 0;
                $min_arr['created'] = date('Y-m-d H:i:s');
                $params = array($max_arr, $min_arr);
                $this->AddReportFsVipTag($params);
            }
        }
    }
    /*---------------------------------刷新VIP统计之前的数据------------------------------------*/


}
