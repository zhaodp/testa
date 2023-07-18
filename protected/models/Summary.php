<?php
/**
 * Created by JetBrains PhpStorm.
 * author: mtx
 * Date: 13-8-8
 * Time: 下午2:42
 */

class Summary
{
    private static $_models;

    public static function model($className = __CLASS__)
    {
        $model = null;
        if (isset(self::$_models[$className]))
            $model = self::$_models[$className];
        else {
            $model = self::$_models[$className] = new $className(null);
        }
        return $model;
    }

    public function getOperate($city_id)
    {
        $driver = array();
        //获取信息费总余额
        $driver['balance'] = DriverBalance::model()->getBalance($city_id);

        //vip总余额
        $vip_balance = Vip::model()->getVipBalanceTotal($city_id);
        $driver['vip_balance'] = $vip_balance ? $vip_balance->balance : 0;

        //司机当月总收入
        $driver['driver_month_income'] = EmployeeAccount::model()->getMonthIncome($city_id);

        //昨天的收入
        $driver['yesterdayIncome'] = EmployeeAccount::model()->getYesterdayIncome($city_id);

        return $driver;

    }

    public function getOrder($city_id)
    {
        $driver = array();
        //获取订单的信息
        $driver['orderWeekInfo'] = DailyTrendCollect::model()->getCollectDataByDay($city_id);
        return $driver;
    }

    public function getDriver($city_id)
    {
        $driver = array();
        $driver_mod = Driver::model();
        //获取司机的信息
        $driver['driverInfo'] = $driver_mod->getDriverInfo($city_id);

        //通知司机签约
        $driver_recruitment_mod = DriverRecruitment::model();
        $driver['sign'] = $driver_recruitment_mod->getSign($city_id);

        //通知司机面试
        $driver['recruitment'] = $driver_recruitment_mod->getRecruitment($city_id);

        //通知司机签约
        $driver['driverInduction'] = $driver_mod->getDriverInduction($city_id);

        //获取皇冠司机总数
        $driver_recommand_mod = DriverRecommand::model();
        $driver['driverRecommand'] = $driver_recommand_mod->getDriverRecommand($city_id);

        //获取被投诉的司机
        $customer_complain_deduct_mod = CustomerComplainDeduct::model();
        $driver['Complaints'] = $customer_complain_deduct_mod->getComplaints($city_id);


        //获取司机上线天数
        if(date('H') >= '17'){
            $date_day = date('Y-m-d', strtotime("-1 day"));
        }else{
            $date_day = date('Y-m-d', strtotime("-2 day"));
        }

        //每日司机在线
        $driver['day_line'] = DailyOrderReport::model()->getCityOnlineData($date_day, $city_id);

        //未处理投诉数量
        $driver['untreated'] = CustomerComplain::model()->getUntrictedData($city_id);

        //可用面试时间

        $driver['interview'] = DriverInterviewTime::model()->getCanInterviewTime($city_id);

        //可用工号
        $address = new DriverIdPool();
        $driver_id = $address->getCountDriverId($city_id);
        $driver['driver_id'] = $driver_id;

        //可以解除屏蔽的司机（手动屏蔽和系统屏蔽）
        $driver['activation'] = DriverPunish::model()->getToUnPunish($city_id);

        //可签约司机
        $driver['entry'] = $driver_recruitment_mod->getCanJoinData($city_id);

        return $driver;

    }


//    public function Summarys(){
//        $driver = array();
//        //获取信息费总余额
//        $driver['balance'] = DriverBalance::model()->getBalance();
//        //vip总余额
//        $vip_balance = Vip::model()->getVipBalanceTotal();
//        $driver['vip_balance'] = $vip_balance ? $vip_balance->balance : 0;
//
//        //司机总收入
//        $driver['driver_month_income'] = EmployeeAccount::model()->getMonthIncome();
//
//        //昨天的收入
//        $driver['yesterdayIncome'] = EmployeeAccount::model()->getYesterdayIncome();
//
//        //司机信息
//        $driver['driverInfo'] = $this->getDriverInfo();
//
//        //获取订单的信息
//        $driver['orderWeekInfo'] = $this->getOrderWeekInfo();
//
//        //获取皇冠司机总数
//        $driver['driverRecommand'] = $this->getDriverRecommand();
//
//        //通知司机面试
//        $driver['recruitment'] = $this->getRecruitment();
//
//        //通知司机签约
//        $driver['sign'] = $this->getSign();
//
//        //通知司机签约
//        $driver['driverInduction'] = $this->getDriverInduction();
//
//        //获取被投诉的司机
//        $driver['Complaints'] = $this->getComplaints();
//
//
//
//        return $driver;
//
//    }


    /**
     * 获取top前20的错误日志
     * @return mixed
     * author mengtianxue
     */
    public function getErrorLog()
    {
        $apiLog = $this->getAppLog();
        return $apiLog;
    }



    /**
     * 获取订单的信息
     * @param int $city_id
     * @return mixed
     * author mengtianxue
     */
    public function getOrderWeekInfo($city_id = 0)
    {
        $end_time = date("Ymd", strtotime("-1 day"));
        $start_time = date("Ymd", strtotime("-8 day"));
        $where = 'order_date between :end_time and :start_time';
        $params = array(':end_time' => $end_time, ':start_time' => $start_time);

        if ($city_id != 0) {
            $where .= ' and city_id = :city_id';
            $params[':city_id'] = $city_id;
        }

        $order_date = Order::getDbReadonlyConnection()->createCommand()
            ->select("order_date,
                    count(1) as order_totle,
                    SUM( IF( (`status` = 1), 1, 0 ))  AS status_1,
                    SUM( IF( (`status` = 2), 1 , 0 ))  AS status_2,
                    SUM( IF( (`status` = 3), 1 , 0 ))  AS status_3,
                    SUM( IF( (`status` = 4), 1 , 0 ))  AS status_4")
            ->from("t_order")
            ->where($where, $params)
            ->group("order_date")
            ->queryAll();
        return $order_date;

    }


    /**
     * 获取最多的错误日志
     * @return mixed
     * author mengtianxue
     */
    public function getAppLog()
    {
        $start_time = date("Y-m-d H:i:s", strtotime("-1 day"));

        $order_date = Yii::app()->dbstat->createCommand()
            ->select("id,type,message,file,count(1) as log_total")
            ->from("t_api_error_log")
            ->where('created > :start_time',
                array(':start_time' => $start_time))
            ->group("line")
            ->order("log_total desc")
            ->limit('10')
            ->queryAll();
        return $order_date;
    }












    /**
     * 获取优惠劵绑定总数
     * @param int $type 优惠劵获取类型 （0 全部、1 本月、2 昨天）
     * author mengtianxue
     */
    public function getCouponBonus($type = 0)
    {
        $where = '';
        $params = array();
        switch ($type) {
            case 1:
                $start_time = strtotime(date('Y-m-01 00:00:00'));
                $end_time = time();
                $where .= 'created between :start_time and :end_time';
                $params = array(':start_time' => $start_time, ':end_time' => $end_time);
                break;
            case 2:
                $end_time = strtotime(date('Y-m-d'));
                $start_time = $end_time - 86400;
                $where .= 'created between :start_time and :end_time';
                $params = array(':start_time' => $start_time, ':end_time' => $end_time);
                break;
            default:
                break;
        }

        $bonusCount = Yii::app()->db_finance->createCommand()
            ->select('count(1) as bonus_count, SUM(IF((`order_id` != 0), 1, 0 )) as bonus_use')
            ->from('t_customer_bonus')
            ->where($where, $params)
            ->queryRow();
        return $bonusCount;
    }

    /**
     * 获取优惠劵使用总数
     * @param int $type 优惠劵使用类型 （0 全部、1 本月、2 昨天）
     * author mengtianxue
     */
    public function getCouponBonusUse($type = 0)
    {
        $where = '';
        $params = array();
        switch ($type) {
            case 1:
                $start_time = strtotime(date('Y-m-01 00:00:00'));
                $end_time = time();
                $where .= 'used between :start_time and :end_time';
                $params = array(':start_time' => $start_time, ':end_time' => $end_time);
                break;
            case 2:
                $end_time = strtotime(date('Y-m-d'));
                $start_time = $end_time - 86400;
                $where .= 'used between :start_time and :end_time';
                $params = array(':start_time' => $start_time, ':end_time' => $end_time);
                break;
            default:
                break;
        }

        $bonusUseCount = Yii::app()->db_finance->createCommand()
            ->select('count(1) as bonus_use')
            ->from('t_customer_bonus')
            ->where($where, $params)
            ->queryScalar();
        return $bonusUseCount;
    }






    public function getCouponFee($city_id = 0, $type = 0)
    {
        $city_id = isset($_GET['city_id']) ? $_GET['city_id'] : 0;
        $params = array();
        //默认去除公司测试帐号
        $where = "id > 0";

        if ($city_id != 0) {
            $where .= " and left(user,2) = :city_prefix";
            $params[':city_prefix'] = Dict::item('city_prefix', $city_id);
        }
        $where .= " and user not in (" . Common::getTestDriverID(). ")";

        $list = Yii::app()->db_finance->createCommand()->select('sum(if(type=0,cast,0)) as t0,
									sum(if(type=1,cast,0)) as t1,
									sum(if(type=2,cast,0)) as t2,
									sum(if(type=3,cast,0)) as t3,
									sum(if(type=4,cast,0)) as t4,
									sum(if(type=5,cast,0)) as t5,
									sum(if(type=6,cast,0)) as t6,
									sum(if(type=7,cast,0)) as t7,
									sum(if(type=8,cast,0)) as t8,
									sum(if(type=9,cast,0)) as t9,
									sum(if(type=10,cast,0)) as t10,
									sum(cast)-sum(if(type=0,cast,0)) as totle')
            ->from('t_employee_account_' . date('Ym'))
            ->where($where, $params)
            ->queryRow();
        return $list;
    }


}
