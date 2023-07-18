<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-12-6
 * Time: 下午2:30
 * auther mengtianxue
 */

Yii::import('application.models.schema.report.ReportFsAccountRp');
Yii::import('application.models.schema.report.ReportFsAccountTag');

class BDailyAccountReport extends ReportFsAccountRp
{
    //channel 本期减少类型
    public static $bill_type = array(1, 2, 4, 6, 21, 17, 20, 22, 23);

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
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

        $criteria->compare('account_date', $date_time);

        if ($city_id != 0) {
            $criteria->compare('city_id', $city_id);
        }
        $criteria->group = "channel";

        $account_report = ReportFsAccountRp::model()->findAll($criteria);
        $account_bill = array();
        foreach ($account_report as $account) {
            $channel = $account['channel'];
            $account_bill[$channel] = $account;
        }
        return $account_bill;
    }

    /**
     * 检查当天是否有统计
     * @param $date_time
     * @param $city_id
     * @return bool
     * @auther mengtianxue
     */
    public function getDailyAccountReport($date_time, $city_id = 0)
    {
        $where = 'account_date = :account_date';
        $params = array(':account_date' => $date_time);
        if ($city_id != 0) {
            $where .= ' and :city_id';
            $params[':city_id'] = $city_id;
        }

        $report = ReportFsAccountRp::model()->findAll($where, $params);
        if ($report) {
            return true;
        } else {
            return false;
        }
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

            $report = ReportFsAccountRp::model()->find($where, $params);
            if ($report) {
                return true;
            }
        }
        return false;

    }

    /**
     * 修改状态
     * @param $params
     * @return bool
     * @auther mengtianxue
     */
    public function updateStart($params)
    {
        $where = 'account_date = :account_date and channel = :channel';
        //查询条件
        $where_params = array();
        $where_params[':account_date'] = $params['account_date'];
        $where_params[':channel'] = $params['channel'];

        if (!empty($params['city_id'])) {
            $where .= ' and city_id = :city_id';
            $where_params[':city_id'] = $params['city_id'];
        }

        //修改字段
        $update_params = array();
        $update_params['status'] = $params['status'];
        $update_params['operator'] = Yii::app()->user->getId();
        $update_params['updated'] = date('Y-m-d H:i:s');

        $return = ReportFsAccountRp::model()->updateAll($update_params, $where, $where_params);
        if ($return) {
            return true;
        }
        return false;
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

        $account_report = ReportFsAccountTag::model()->findAll($criteria);
        return $account_report;
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

        $account_report = ReportFsAccountTag::model()->findAll($criteria);
        return $account_report;
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
        $criteria->select = "sum(start_balance) as start_balance, sum(end_balance) as end_balance";
        if (!empty($date_time)) {
            $criteria->compare('daily_date', $date_time, true);
        }

        $account_report = ReportFsAccountTag::model()->find($criteria);
        if ($account_report) {
            if ($type == 0) {
                $money = $account_report->start_balance;
            } else {
                $money = $account_report->end_balance;
            }
        }
        return $money;
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
        $params = array();
        $criteria = new CDbCriteria;
        // money 接受接受本期增加值 用remark 接受本期减少值
        $criteria->select = "SUM(IF((bill_type = 1), money, 0)) AS money,SUM(IF((bill_type = 2), money, 0)) AS remark";
        $criteria->addCondition("channel != 0");
        if (!empty($date_time)) {
            $criteria->addCondition('account_date = :account_date');
            $params[':account_date'] = $date_time;
        }

        if (!empty($city_id)) {
            $criteria->addCondition('city_id = :city_id');
            $params[':city_id'] = $city_id;
        }
        $criteria->params = $params;
        $account_report = ReportFsAccountRp::model()->find($criteria);
        $account = array();
        $account['add_money'] = $account_report->money;
        $account['minus_money'] = $account_report->remark;
        return $account;
    }


    /**
     * 获取每一天的详情
     * @param int $date_time
     * @param int $city_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getDailyEndBalanceByCity($date_time = 0, $city_id = 0)
    {
        $criteria = new CDbCriteria;
        $criteria->select = "*";
        $criteria->compare('daily_date', $date_time);

        if (!empty($city_id)) {
            $criteria->compare('city_id', $city_id);
        }

        $account_tag = ReportFsAccountTag::model()->find($criteria);
        return $account_tag;
    }


}






















