<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ZhangTingyi
 * Date: 13-6-26
 * Time: 下午4:07
 * To change this template use File | Settings | File Templates.
 */
Yii::import('application.models.schema.report.DriverOnlineStat');
Yii::import('application.models.schema.report.ReportDailyOrderDriver');
class driverOrderDailyCommand extends CConsoleCommand {

    //走内网IP
    public $url = 'http://db03n.edaijia.cn/data/stat_driver_online/';
    //public $url = 'http://db03.edaijia.cn/data/stat_driver_online/';

    /**
     * 抓取信息插入数据表(每天数据)
     * 每天运行一次 早八点后执行
     */
    public function actionFetchDataInsertTable() {
        if (date('H', time()) == 17) {
            $record_date = date('Y-m-d',strtotime('-1 day'));
            $this->fetchDataAndInsert($record_date);
        }else{
        	echo "\n--------not run scape time------\n";
        }
    }

    /**
     * 恢复缺失数据
     * @param string $start_date
     * @param string $end_date
     */
    public function actionResetLoseDate($start_date='2013-11-11', $end_date='2013-11-18') {
        $start_ts = strtotime($start_date);
        $end_ts = strtotime($end_date);
        $date_list = array();
        while ($start_ts<=$end_ts){
            $date_list[] = date('Y-m-d', $start_ts);
            $start_ts = intval($start_ts+86400);
        }
        if (is_array($date_list) && count($date_list)) {
            foreach($date_list as $d) {
                $this->fetchDataAndInsert($d);
            }
        }
    }

    /**
     * 更新每月司机在线情况数据
     * 每天运行一次 早九点后
     */
    public function actionFetchDataToMonthTable() {
        if (date('H', time()) == 18) {
            $record_date = date('Ym', time());
            $current_month = intval(date('m', time()));
            $current_year = date('Y', time());
            $order_model = new DailyOrderReport();
            $data = $order_model->buildReportByMonthCity($current_year, $current_month);
            if (is_array($data) && count($data)) {
                foreach ($data as $v) {
                    $v['driver_name'] = DailyOrderReport::getDriverName($v['driver_id']);
                    $v['income'] = $v['income_total'];
                    $v['current_month'] = $record_date;
                    $_model = MonthOrderReport::model()->getModelByDriverDate($v['driver_id'], $record_date);
                    if (!$_model) {
                        $_model = new MonthOrderReport();
                        $_model->created = date('Y-m-d H:i:s', time());
                    }
                    $_model->attributes = $v;
                    $_model->p_online = $v['p_online'];
                    $_model->p_continuous = $v['p_continuous'];
                    $_model->c_complain = $v['c_complain'];
                    $_model->d_complain = $v['d_complain'];
                    $_model->high_opinion = $v['high_opinion'];
                    $_model->bad_review = $v['bad_review'];
                    $_model->normal_days = $v['normal_days'];
                    $_model->p_active = $v['p_active'];
                    $_model->updated = date('Y-m-d H:i:s', time());
                    echo "\n".$v['driver_id'].'=='.$record_date.'=='.intval($_model->save())."\n";

                }
            }
        }else{
        	echo "\n--------not run scape time------\n";
        }
    }

    /**
     * 获得从三月一日到运行时间的数据
     * 上线时执行一次
     */
    public function actionFetchBeforeDataAndInset() {
        $tmp_current_date = '2013-08-10';
        $fetch_url = $this->url.$tmp_current_date.'.data';
        $f = fopen($fetch_url, 'r');
        $online_model = new DailyOnlineReport();
        $order_model = new DailyOrderReport();
        $i = 0;
        while (!feof($f)) {
            $line = fgets($f);
            $attr_arr = json_decode($line, true);
            if (is_array($attr_arr) && count($attr_arr)) {
                $tmp_date = $attr_arr['date'];
                $order_result = $order_model->insertDataToOrder($tmp_date, $attr_arr);
                $online_result = $online_model->insertData($tmp_date, $attr_arr['driver_id'], $attr_arr['online']);
                echo $tmp_date.'|'.$attr_arr['driver_id']."  order ".intval($order_result).' | online '.intval($online_result).'|'.$i."\n";
            }
            $i++;
        }
    }

    /**
     * 恢复之前月份月统计
     */
    public function actionBeforeFetchDataToMonthTable() {
        $date_list = array(
            'current_date' => '201407',
            'current_month' => 7,
            'current_year'=>2014,
            'city'=>104
        );

        $current_date = $date_list['current_date'];
        $current_month = $date_list['current_month'];
        $current_year = $date_list['current_year'];
        $order_model = new DailyOrderReport();
        $city_id = $date_list['city'] ? $date_list['city'] : 0;
        $data = $order_model->buildReportByMonthCity($current_year, $current_month,$city_id);
        if (is_array($data) && count($data)) {
            foreach ($data as $v) {
                $v['driver_name'] = DailyOrderReport::getDriverName($v['driver_id']);
                $v['income'] = $v['income_total'];
                $v['current_month'] = $current_date;
                $_model = MonthOrderReport::model()->getModelByDriverDate($v['driver_id'], $current_date);
                if (!$_model) {
                    $_model = new MonthOrderReport();
                    $_model->created = date('Y-m-d H:i:s', time());
                }
                $_model->attributes = $v;
                $_model->p_online = $v['p_online'];
                $_model->p_continuous = $v['p_continuous'];
                $_model->c_complain = $v['c_complain'];
                $_model->d_complain = $v['d_complain'];
                $_model->high_opinion = $v['high_opinion'];
                $_model->bad_review = $v['bad_review'];
                $_model->normal_days = $v['normal_days'];
                $_model->p_active = $v['p_active'];
                $_model->updated = date('Y-m-d H:i:s', time());
                echo $v['driver_id'].'=='.$current_date.'=='.intval($_model->save())."\n";
            }
        }

    }

    public function actionGetOtherDataInsertTable($m) {
        $y = '2013';
        $cdate = strtotime($y.'-'.$m.'-01');
        $now = strtotime(date ('Y-m-d', mktime(0,0,0,$m + 1,0,$y)));
        $start = $now;
        $cdateArr = array();
        while ($start >= $cdate) {
            $cdateArr[] = date("Y-m-d", $start);
            $start = $start - 86400;
        }
        foreach($cdateArr as $date_start) {

            $command = Yii::app()->dbreport->createCommand();
            $command->select('id');
            $command->from('t_daily_order_report');
            $command->where('record_date=:record_date', array(':record_date'=>$date_start));
            $data = $command->queryAll();

            //$data = DailyOrderReport::model()->findAll('record_date=:record_date', array(':record_date'=>$date_start));
            if (is_array($data) && count($data)) {
                foreach ($data as $k=>$m) {
                    $v = DailyOrderReport::model()->findByPk($m['id']);
                    $insert_data = $v->attributes;
                    $online_data = $v->checkOnline(json_decode($v->online_data, true));
                    $v->p_active = intval($online_data['p_active']);
                    $v->online = intval($online_data['online']);
                    $v->p_online = intval($online_data['p_online']);
                    $v->p_continuous = intval($online_data['p_continuous']);
                    /*
                    $insert_data['online'] = intval($online_data['online']);
                    $insert_data['p_online'] = intval($online_data['p_online']);
                    $insert_data['p_continuous'] = intval($online_data['p_continuous']);
                    $date_start = $v->record_date.' 00:00:00';
                    $date_end = $v->record_date.' 23:59:59';
                    $date_start_ts = strtotime($date_start);
                    $date_end_ts = strtotime($date_end);
                    $insert_data['c_complain'] = CustomerComplain::model()->count('driver_id=:driver_id and service_time>=:date_start and service_time<:date_end', array(':driver_id'=>$v->driver_id, ':date_start'=>$date_start, ':date_end'=>$date_end));
                    $insert_data['d_complain'] = DriverComplaint::model()->count('driver_user=:driver_user and driver_time>=:date_start_ts and driver_time<:date_end_ts', array(':driver_user'=>$v->driver_id, ':date_start_ts'=>$date_start_ts, ':date_end_ts'=>$date_end_ts));
                    $comment_sms_model = new CommentSms();
                    $insert_data['high_opinion'] = $comment_sms_model->getHighOpinionCount($v->driver_id, $v->record_date);
                    $insert_data['bad_review'] = $comment_sms_model->getBadReview($v->driver_id, $v->record_date);
                    $v->attributes = $insert_data;
                    */
                    echo $v->record_date.'-'.$v->driver_id.'-'.intval($v->save()).'-'.$k."\n";
                    unset($v);
                }
                unset($data);
            } else {
                echo $date_start.'-没数据'."\n";
            }

        }
    }

    /**
     * 通过传入日期，抓取并存储数据
     * @param $current_date 如 ： 2012-08-09
     */
    private function fetchDataAndInsert($current_date) {
        $tmp_record_date = $current_date;
        $fetch_url = $this->url.$tmp_record_date.'.data';
        $content = file_get_contents($fetch_url);
        $data = Common::myStr2Array($content);
        //$tmp_dir = '/tmp/';
        //$tmp_file_name = tempnam($tmp_dir, $current_date);
        //$fp = fopen($tmp_file_name, "w+");
        //fputs($fp, $content);
        //fclose($fp);
        //$f = fopen($fetch_url, 'r');
        //$f = fopen($tmp_file_name, 'r');
        $online_model = new DailyOnlineReport();
        $order_model = new DailyOrderReport();
        $i = 0;

        foreach ($data as $v) {
            $line = $v;
            $attr_arr = json_decode($line, true);
            if (is_array($attr_arr) && count($attr_arr)) {
                $order_result = $order_model->insertDataToOrder($tmp_record_date, $attr_arr);
                $online_result = $online_model->insertData($tmp_record_date, $attr_arr['driver_id'], $attr_arr['online']);
                echo $tmp_record_date.'|'.$attr_arr['driver_id']."  order ".intval($order_result).' | online '.intval($online_result).'|'.$i."\n";
            }
            $i++;
        }
        //unlink($tmp_file_name);
    }

    public function actionResetData() {
        $date_list = array(
            //'2013-07-30',
            '2013-09-02',
        );
        foreach ($date_list as $date) {
            $this->fetchDataAndInsert($date);
        }
    }

    public function actionUpdateBeforeMonthData() {
        $sql = "SELECT distinct(current_month) FROM `t_daily_order_driver` where current_month<201303";
        $command = Yii::app()->dbreport->createCommand($sql);
        $month_date = $command->queryAll();
        unset($command);
        foreach ($month_date as $m) {
            $_sql = "SELECT driver_user AS driver_id,city_id, COUNT(*) AS accept,COUNT(IF(`status`=1 OR `status` = 4,TRUE,NULL)) AS complete,COUNT(IF(`status`=3,TRUE,NULL)) AS cancel,COUNT(IF(`source`=2 OR `source`=3, TRUE, NULL)) AS additional,COUNT(distinct order_date) AS accept_days,SUM(IF(`status`=1 OR `status` = 4,income,0)) AS income FROM t_daily_order_driver where current_month='{$m['current_month']}' group by driver_user";
            $command = Yii::app()->dbreport->createCommand($_sql);
            $data = $command->queryAll();

            if (is_array($data) && count($data)) {
                foreach ($data as $v) {
                    $v['current_month'] = $m['current_month'];
                    $v['driver_name'] = DailyOrderReport::getDriverName($v['driver_id']);
                    $_model = MonthOrderReport::model()->getModelByDriverDate($v['driver_id'], $m['current_month']);
                    if (!$_model) {
                        $_model = new MonthOrderReport();
                        $_model->updated = date('Y-m-d', time());
                        $_model->created = date('Y-m-d', time());
                    }
                    $date_start = date('Y-m-01', strtotime($m['current_month'].'01'));
                    $date_end = date('Y-m-d', strtotime("$date_start +1 month -1 day"));
                    $date_start_ts = strtotime($date_start);
                    $date_end_ts = strtotime($date_end);
                    $v['c_complain'] = CustomerComplain::model()->count('driver_id=:driver_id and service_time>=:date_start and service_time<:date_end', array(':driver_id'=>$v['driver_id'], ':date_start'=>$date_start, ':date_end'=>$date_end));
                    $v['d_complain'] = DriverComplaint::model()->count('driver_user=:driver_user and driver_time>=:date_start_ts and driver_time<:date_end_ts', array(':driver_user'=>$v['driver_id'], ':date_start_ts'=>$date_start_ts, ':date_end_ts'=>$date_end_ts));
                    $sql_low = "SELECT COUNT(id) FROM t_comment_sms WHERE level<3 and driver_id='{$v['driver_id']}' and sms_type=0 and created>='{$date_start}' and created<='{$date_end_ts}'";
                    $low_command = Yii::app()->db_readonly->createCommand($sql_low);
                    $v['bad_review'] = intval($low_command->queryScalar());
                    $sql_high = "SELECT COUNT(id) FROM t_comment_sms WHERE level>3 and driver_id='{$v['driver_id']}' and sms_type=0 and created>='{$date_start}' and created<='{$date_end_ts}'";
                    $high_command = Yii::app()->db_readonly->createCommand($sql_high);
                    $v['high_opinion'] = intval($high_command->queryScalar());
                    $_model->attributes = $v;
                    echo $v['driver_id'].'---'.$m['current_month'].'---'.intval($_model->save())."\n";
                }
            }
        }
    }

    /**
     * 更新司机在线等数据到一个中间表，后台展示用
     * @param string $start_date Y-m-d
     * @param string $end_date Y-m-d
     *
     */

    public function actiondriverOnlineStat($start_date = '',$end_date = ''){
        echo Common::jobBegin('driver_online stat');
        $init = false;
        $city_list = RCityList::model()->getOpenCityList();
        //print_r($city_list);
        if(!$start_date){
            $start_date = date('Y-m-d',strtotime('-2 day'));
        }
        else{
            $init = true;
         }
        if(!$end_date){
            $end_date = date('Y-m-d',strtotime('-1 day'));
        }
        if(strtotime($start_date) > strtotime($end_date)){
            return false;
        }

        $date_line = DailyOrderReport::getDateLine($start_date, $end_date);
        foreach($date_line as $date){
            $this->insertDriverOnlineStat($date,0);
            if(is_array($city_list)){
                foreach($city_list as $city_id => $name){
                    $this->insertDriverOnlineStat($date,$city_id);
                }
            }
        }
        echo Common::jobEnd('driver_online stat');

    }


    private  function getdriverOnlineData($date,$city_id){
        $work_log_model = new WorkLog();
        $report_model = new DailyOrderReport();
        $work_log_data = $work_log_model->getPeakChartsData($date, $city_id);
        $online = $report_model->getDriverOnlineCount($date, $city_id);
        $accept = $report_model->getDriverAcceptCount($date, $city_id);
        $notonline = $report_model->getDriverNotOnlineCount($date, $city_id);

        $_table['free'] =  $work_log_data['free'];
        $_table['busy'] = $work_log_data['busy'];
        $_table['free_proportion'] = $work_log_data['busy'] > 0 ? sprintf("%01.1f",($work_log_data['free']/$work_log_data['busy'])*100): '0';
        $_table['accept'] = $accept;
        $_table['online'] = $online;
        $_table['online_proportion'] = ($online+$notonline) > 0 ? sprintf("%01.1f",($online/($online+$notonline))*100) : '0';
        $_table['notonline'] = $notonline;
        $_table['accept_proportion'] = $online > 0 ? sprintf("%01.1f",($accept/$online)*100) : '0';
        $_table['date'] = $date;
        $_table['city_id'] = $city_id;
        $_table['create_time'] = date('Y-m-d H:i:s');
        return $_table;
    }


    private  function insertDriverOnlineStat($date,$city_id){
        $model_stat = DriverOnlineStat::model();
        $model_stat_new = new DriverOnlineStat();
        $all_data = $this->getdriverOnlineData($date,$city_id);
        $check_exist = DriverOnlineStat::model()->find('date = :date and city_id = :city_id',array(':date'=>$date,':city_id'=>$city_id));
        if($check_exist){
            $model_stat -> deleteAllByAttributes(array('date'=>$date,'city_id'=>$city_id));
            echo 'data existed ----'.$date.'---- city_id '.$city_id."\n";
        }
        $model_stat_new->attributes = $all_data;
        //print_r($all_data);//die;
        $res = $model_stat_new->save();
        //var_dump($res);die;
        if($res){
            echo 'success----date'.$date.'---- city_id '.$city_id."\n";
        }else {
            print_r($all_data);
            echo 'field----date'.$date.'---- city_id '.$city_id."\n";
        }
    }




}