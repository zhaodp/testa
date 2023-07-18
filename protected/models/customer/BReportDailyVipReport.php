<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-12-4
 * Time: 下午4:28
 * auther mengtianxue
 */

Yii::import('application.models.schema.report.ReportFsVipRp');
Yii::import('application.models.schema.report.ReportFsVipTag');
Yii::import('application.models.schema.report.ReportFsVipTradeInfo');

class BReportDailyVipReport extends ReportFsVipRp
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getDetailsList($date_time = 0)
    {
        $criteria = new CDbCriteria;
        $criteria->select = "id, status, daily_date, sum(start_balance) as start_balance, sum(add_balance) as add_balance, sum(minus_balance) as minus_balance, sum(end_balance) as end_balance";
        if (!empty($date_time)) {
            $criteria->compare('daily_date', $date_time, true);
        }
        $criteria->order = 'daily_date asc';
        $criteria->group = 'daily_date';

        $vip_report = ReportFsVipTag::model()->findAll($criteria);
        return $vip_report;
    }

    public function getMonthDailyList($date_time = 0)
    {
        $criteria = new CDbCriteria;
        $criteria->select = "month_date, sum(add_balance) as add_balance, sum(minus_balance) as minus_balance";
        if (!empty($date_time)) {
            $criteria->compare('month_date', $date_time, true);
        }
        $criteria->order = 'month_date asc';
        $criteria->group = 'month_date';

        $vip_report = ReportFsVipTag::model()->findAll($criteria);
        return $vip_report;
    }

    /**
     * @param $date_time
     * @param int $city_id
     * @return array
     * @auther mengtianxue
     */
    public function getDailyAccountInfo($date_time, $city_id = 0)
    {
        $criteria = new CDbCriteria;
        $criteria->select = "account_date, channel, sum(money) as money, bill_type";

        $criteria->compare('account_date', $date_time, true);

        if ($city_id != 0) {
            $criteria->compare('city_id', $city_id, true);
        }
        $criteria->group = "channel";

        $vip_report = ReportFsVipRp::model()->findAll($criteria);
        $vip_bill = array();
        foreach ($vip_report as $vip) {
            $channel = $vip['channel'];
            $vip_bill[$channel] = $vip;
        }
        return $vip_bill;
    }

    /**
     * 账单信息
     * @param $params
     * @return CActiveDataProvider
     * @auther mengtianxue
     */
    public function getAccountBill($params)
    {
        $model = new ReportFsVipTradeInfo();

        $criteria = new CDbCriteria;
        $criteria->compare('city_id', $params['city_id']);

        switch ($params['channel']) {
            case 0:
            case 1:
            case 2:
            case 3:
            case 4:
                $criteria->compare('channel', $params['channel']);
                break;
            case 5:
                $criteria->addCondition("cast != 0");
                break;
            case 6:
                $criteria->addCondition("insurance != 0");
                break;
            case 7:
                $criteria->addCondition("balance != 0");
                break;
            case 8:
                $criteria->addCondition("Invoice_money != 0");
                break;
        }

        if (!empty($params['operator'])) {
            $criteria->addSearchCondition('operator', $params['operator'], true);
        }

        if (!empty($params['daily_date'])) {
            $start_time = $params['daily_date'];
            $end_time = date('Y-m-d', (strtotime($params['daily_date']) + 86400));
            $criteria->addBetweenCondition('daily_date', $start_time, $end_time);
        }

        return new CActiveDataProvider($model, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 20
            ),
        ));
    }


    /**
     * 根据查询条件获取信息费总额
     * @param $params
     * @return array|mixed|null
     * @auther mengtianxue
     */
    public function getAccountCount($params)
    {
        $model = new ReportFsVipTradeInfo();

        $criteria = new CDbCriteria;


        switch ($params['channel']) {
            case 0:
            case 1:
            case 2:
            case 3:
            case 4:
                $criteria->select = "sum(amount) as cast";
                $criteria->compare('channel', $params['channel']);
                break;
            case 5:
                $criteria->select = "sum(cast) as cast";
                $criteria->addCondition("cast != 0");
                break;
            case 6:
                $criteria->select = "sum(insurance) as cast";
                $criteria->addCondition("insurance != 0");
                break;
            case 7:
                $criteria->select = "sum(balance) as cast";
                $criteria->addCondition("balance != 0");
                break;
            case 8:
                $criteria->select = "sum(Invoice_money) as cast";
                $criteria->addCondition("Invoice_money != 0");
                break;

        }
        $criteria->compare('city_id', $params['city_id']);
        $criteria->compare('operator', $params['operator']);

        if (!empty($params['daily_date'])) {
            $start_time = $params['daily_date'];
            $end_time = date('Y-m-d', (strtotime($params['daily_date']) + 86400));
            $criteria->addBetweenCondition('daily_date', $start_time, $end_time);
        }
        $account = $model->find($criteria);
        return $account->cast;
    }

    /**
     * 根据时间统计本期增加和本期减少
     * @param $date_time
     * @param $city_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getDailyCountAccount($date_time = 0, $city_id = 0)
    {
        $account = array('add_money' => 0, 'minus_money' => 0);
        $params = array();
        $criteria = new CDbCriteria;
        // money 接受接受本期增加值 用remark 接受本期减少值
        $criteria->select = "bill_type, sum(money) as money";
        $criteria->addCondition('channel <= 8');

        if (!empty($date_time)) {
            $criteria->addCondition('account_date = :account_date');
            $params[':account_date'] = $date_time;
        }

        if (!empty($city_id)) {
            $criteria->addCondition('city_id = :city_id');
            $params[':city_id'] = $city_id;
        }
        $criteria->group = "bill_type";
        $criteria->params = $params;
        $account_report = ReportFsVipRp::model()->findAll($criteria);
        if ($account_report) {
            foreach ($account_report as $bill_money) {
                if ($bill_money->bill_type == 1) {
                    $account['add_money'] = $bill_money->money;
                } elseif ($bill_money->bill_type == 2) {
                    $account['minus_money'] = $bill_money->money;
                }
            }
        }
        return $account;
    }

    public function getDaily($date_time = 0, $city_id = 0)
    {
        $params = array();
        $criteria = new CDbCriteria;
        $criteria->select = "SUM(money) AS money";

        $criteria->addCondition('channel not in (-1, 99)');
        if (!empty($date_time)) {
            $criteria->addCondition('account_date = :account_date');
            $params[':account_date'] = $date_time;
        }

        if (!empty($city_id)) {
            $criteria->addCondition('city_id = :city_id');
            $params[':city_id'] = $city_id;
        }

        $criteria->params = $params;
        $account_report = ReportFsVipRp::model()->find($criteria);
        return $account_report->money;
    }


    /**
     * 获取当前是否对账成功
     * @param $daily
     * @param null $channel
     * @param int $city_id
     * @return bool
     * @auther mengtianxue
     */
    public function getDailyAccountNotStatus($daily, $channel = null, $city_id = 0)
    {
        if (!empty($daily)) {
            $where = 'status = 0 and account_date = :account_date';
            $params = array(':account_date' => $daily);
            if (!empty($channel)) {
                $where .= ' and channel = :channel';
                $params[':channel'] = $channel;
            }

            if (!empty($city_id)) {
                $where .= ' and city_id = :city_id';
                $params[':city_id'] = $city_id;
            }

            $report = ReportFsVipRp::model()->find($where, $params);
            if ($report) {
                return true;
            }
        }
        return false;

    }


    /**
     *
     * @param $date_time
     * @param int $type
     * @return int
     * @auther mengtianxue
     */
    public function getDailyMoney($date_time, $type = 0)
    {
        $money = 0;
        $criteria = new CDbCriteria;
        $criteria->select = "start_balance, end_balance";
        if (!empty($date_time)) {
            $criteria->compare('daily_date', $date_time, true);
        }

        $account_report = ReportFsVipTag::model()->find($criteria);
        if ($account_report) {
            if ($type == 0) {
                $money = $account_report->start_balance;
            } else {
                $money = $account_report->end_balance;
            }
        }
        return $money;
    }


    public function getVipMonthList($year)
    {
        $month = 12;
        $now_year = date('Y');
        if ($now_year == $year) {
            $month = date('m');
        }
        $date = array();
        for ($m = 1; $m <= $month; $m++) {
            $start_time = $year . "-" . $m . '-01';
            $end_time = date('Y-m-d', strtotime("Next month", strtotime($start_time)));
            $model = new ReportFsVipTradeInfo();

            $criteria = new CDbCriteria;
            $criteria->select = "sum(income) as income,
            sum(amount) as amount,
            sum(cast) as cast,
            sum(insurance) as insurance,
            sum(Invoice_money) as Invoice_money,
            sum(balance) as balance";
            $criteria->addCondition('channel = 98');
            $criteria->addBetweenCondition('daily_date', $start_time, $end_time);
            $info = $model->find($criteria);
            $date[$start_time] = $info->attributes;
        }
        return $date;
    }

    /**
     * 每月司机VIP收入
     * @param int $month
     * @return array|CActiveRecord|mixed|null
     * @auther mengtianxue
     */

    public function getVipGroupByDriver($month = 0)
    {
        $start_time = $month . '-01';
        $end_time = date('Y-m-d', strtotime("Next month", strtotime($start_time)));
        $model = new ReportFsVipTradeInfo();

        $criteria = new CDbCriteria;
        $criteria->select = "driver_id,
            daily_date,
            count(1) as order_id,
            sum(income) as income,
            sum(amount) as amount,
            sum(cast) as cast,
            sum(insurance) as insurance,
            sum(Invoice_money) as Invoice_money,
            sum(balance) as balance";
        $criteria->addCondition('channel = 98');
        $criteria->addBetweenCondition('daily_date', $start_time, $end_time);
        $criteria->group = 'driver_id';
        $criteria->order = 'amount asc';

        return new CActiveDataProvider($model, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 20
            ),
        ));
    }


}