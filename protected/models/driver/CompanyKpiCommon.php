<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zhangtingyi
 * Date: 13-8-29
 * Time: 上午11:28
 * To change this template use File | Settings | File Templates.
 */
class CompanyKpiCommon
{

    CONST MEM_KEY_PREFIX = 'KPI_PREFIX_TEST_';

    //每月招募KPI, month, city_id
    CONST MEM_MONTH_RECRUITMENT_KPI = 'MONTH_RECRUITMENT_KPI_%s_%s';
    //每月实际招募数, month, city_id
    CONST MEM_MONTH_RECRUITMENT_COUNT = 'MONTH_RECRUITMENT_COUNT_%s_%s';
    //每月招募转化率数据, month: 2005-01-01, city_id
    CONST MEN_MONTH_FUNNEL_DATA = 'MONTH_FUNNEL_DATA_%s_%s';
    //每月招募转化率数据，招聘优化后新版
    CONST MEN_MONTH_FUNNEL_DATA_V2 = 'MONTH_FUNNEL_DATA_V2_%s_%s';
    //每月招募从报名到入职平均时间
    CONST MEN_MONTH_AVG_NTRY_TIME = 'MONTH_AVG_ENTRY_TIME_%s_%s';

    const TYPE_SAFE = 1;        //安全类

    const TYPE_SERVICE = 2;     //服务类

    const TYPE_DISPUTE = 3;     //纠纷类

    const TYPE_STANDARD = 4;    //标准类

    const TYPE_OTHER = 5;       //其它类

    const TYPE_ENTRY = 6;       //签约司机数

    const TYPE_PEAK = 7;        //峰值时段空闲数

    const TYPE_ORDER = 8;       //订单增长环比

    const TYPE_RESTAURANT = 9;  //餐厅推广

    const TYPE_BANK = 10;       //银行推广

    const TYPE_KTV = 11;        //KTV推广

    const TYPE_4S = 12;         //4S点推广

    const TYPE_ORDER_NUM = 13;  //订单数

    const BACKGROUND_OPERATE = 0; //运营部门KPI

    const BACKGROUND_BUSINESS = 1; //市场部门KPI

    /**
     * 运营城市()
     */
    public static $operate_city = array(
        1	,//	北京
        3	,//	上海
        4	,//	杭州
        5	,//	广州
        6	,//	深圳
        7	,//	重庆
        2	,//	成都
        10	,//	武汉
        15	,//	济南
        18	,//	郑州
        8	,//	南京
        11	,//	西安
        14	,//	天津
        9	,//	长沙
        20	,//	青岛
        12	,//	宁波
        27	,//	福州
        22	,//	厦门
        23	,//	合肥
        21	,//	大连
        19	,//	沈阳
        29	,//	太原
        25	,//	石家庄
        41	,//	海口
        43  ,//银川
        24  ,//哈尔滨
        33  ,//贵阳
        77  ,//西宁
        26  ,//南昌
        36  ,//长春
        35  ,//南宁
        38  ,//呼和浩特
        17  ,//昆明
        16  ,//suzhou
        30  ,//wuxi
        31  ,//changzhou
    );

    public static $service_list = array(
        self::TYPE_SAFE     =>  '安全类',
        self::TYPE_SERVICE  =>  '服务类',
        self::TYPE_DISPUTE  =>  '纠纷类',
        self::TYPE_STANDARD =>  '标准类',
        self::TYPE_OTHER    =>   '其它类',
    );

    public static $operate_list = array(
        self::TYPE_ENTRY => '签约司机数',
        self::TYPE_PEAK  => '峰值时段空闲数',
        self::TYPE_ORDER => '订单增长环比',
        self::TYPE_ORDER_NUM => '订单数',
    );

    public static $business_list = array(
        self::TYPE_RESTAURANT => '餐厅推广',
        self::TYPE_BANK => '银行推广',
        self::TYPE_KTV => 'KTV推广',
        self::TYPE_4S => '4S店推广',
    );

    //服务类属性
    public static $service_attr = array(
        'basic_score' , // '分类基础分',
        'chanllenge' ,  // '挑战值',
        'goal' ,        // '目标值',
        'standard' ,    // '合格值',
        'c_score' ,     // '完成挑战值得分',
        'g_score' ,     // '完成目标值得分',
        's_score' ,     // '完成合格值得分',
        'uns_score' ,   // '不合格得分',
    );

    //市场属性
    public static $business_attr = array(
        'basic_score' , // '分类基础分',
        'chanllenge' ,  // '挑战值',
        'goal' ,        // '目标值',
        'standard' ,    // '合格值',
        'c_score' ,     // '完成挑战值得分',
        'g_score' ,     // '完成目标值得分',
        's_score' ,     // '完成合格值得分',
        'uns_score' ,   // '不合格得分',
    );

    //运营属性
    public static $operate_attr = array(
        'basic_score' , // 'Basic Score',
        'grade' ,       // 'Grade',
    );


    /** 运营城市()
     * @return bool|mixed
     */
    public static function getOperateCity(){
        $mod = new RCityList();
        return $mod->getCapitalCity();
    }

    public  function getBackgroundCityList($type) {
        if ($type != CompanyKpiCommon::BACKGROUND_OPERATE && $type != CompanyKpiCommon::BACKGROUND_BUSINESS) {
            return false;
        }
        $city_arr = array();
        $city_list = Dict::items("city");
        unset($city_list[0]);
        $operate_city = $this->getOperateCity();
        foreach ($operate_city as $city_id) {
            if (isset($city_list[$city_id])) {
                $city_arr[CompanyKpiCommon::BACKGROUND_OPERATE][$city_id] = $city_list[$city_id];
                unset($city_list[$city_id]);
            }
        }
        $city_arr[CompanyKpiCommon::BACKGROUND_BUSINESS] = $city_list;
        return $city_arr[$type];
    }

    public static $corresponding = array(
        1=>1,  //安全类
        6=>2,  //服务类
        17=>3, //纠纷类
        25=>4, //标准类
        35=>5, //其它类
    );

    /*
     * 根据城市获得后台类型
     */
    public static function getBackgroundType($city_id) {
        $operate_city = self::getOperateCity();
        if (in_array($city_id, $operate_city)) {
            return self::BACKGROUND_OPERATE;
        } else {
            return self::BACKGROUND_BUSINESS;
        }
    }

    public static function getMemKey($city_id, $use_date, $type_id){
        $key = CompanyKpiCommon::MEM_KEY_PREFIX.$city_id.'_'.$use_date.'_'.$type_id;
        return $key;
    }

    public function getAllSettingInfo($city_id, $use_date) {
        $data['service'] = CompanyServiceSetting::model()->getSettingInfo($city_id, $use_date);
        $data['business'] = CompanyServiceSetting::model()->getBusinessSettingInfo($city_id, $use_date);
        $data['operate'] = CompanyOperateSetting::model()->getSettingInfo($city_id, $use_date);
        return $data;
    }

    public function getList($use_date=null, $type=CompanyKpiCommon::BACKGROUND_OPERATE) {
        $use_date = intval($use_date);
        if (!$use_date) {
            $sql = "select distinct city_id,use_date, DATE_FORMAT(created,'%Y-%m-%d') as created from t_company_operate_setting group by city_id, use_date order by use_date desc";
        } else {
            $sql = "select distinct city_id,use_date, DATE_FORMAT(created,'%Y-%m-%d') as created from t_company_operate_setting where use_date={$use_date} group by city_id, use_date order by use_date desc";
        }
        $command = Yii::app()->db_readonly->createCommand($sql);
        $data = $command->queryAll();
        $city_arr = self::getBackgroundCityList($type);
        $city_list = array_keys($city_arr);
        if (is_array($data) && count($data)) {
            foreach ($data as $k=>$v) {
                if (!in_array($v['city_id'], $city_list)) {
                    unset($data[$k]);
                }
            }
        }
        return $data;
    }

    public function getMouldList($type){
        $current_month = date('Ym',time());
        $list = $this->getList($current_month, $type);
        $mould_list = array();
        if (is_array($list) && count($list)) {
            foreach($list as $v) {
                $key = $v['city_id'].'_'.$v['use_date'];
                $mould_list[$key] = Dict::item("city", $v['city_id']).$v['use_date'];
            }
        }
        return $mould_list;
    }

    public static function getMonthFirstAndLastDay($use_date)
    {
        $date = $use_date.'01';
        $firstday = date('Y-m-01', strtotime($date));
        $lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
        return array($firstday, $lastday);
    }

    public function getOperateData($city_id, $use_date, $reload_cache = false) {
        $key = self::MEM_KEY_PREFIX.'_operate_'.$city_id.'_'.$use_date;
        $data = Yii::app()->cache->get($key);
        if (!$data || $reload_cache) {
            $data[self::TYPE_ENTRY] = $this->getDriverEntryNum($city_id, $use_date);
            $data[self::TYPE_PEAK] = $this->getFreeDays($city_id, $use_date);
            $data[self::TYPE_ORDER] = $this->getOrderUp($city_id, $use_date);
            $y = date('Y', strtotime($use_date.'01'));
            $m = date('m', strtotime($use_date.'01'));
            $data[self::TYPE_ORDER_NUM] = $this->getOrderCount($city_id, $y, $m);

            if (date('Ym',time()) == date('Ym',strtotime($use_date.'01')) && !$reload_cache) {
                $mem_time = 12 * 3600;
            } else {
                $mem_time = 86400 * 60;
            }

            Yii::app()->cache->set($key, $data, $mem_time);
        }
        return $data;
    }

    public function getServiceData($city_id, $use_date, $reload_cache = false) {
        $key = self::MEM_KEY_PREFIX.'_service_'.$city_id.'_'.$use_date;
        $data = Yii::app()->cache->get($key);
        if (!$data || $reload_cache) {
            $month_date = CompanyKpiCommon::getMonthFirstAndLastDay($use_date);
            $_tmp_data = $this->getKpiData($city_id, $month_date[0], $month_date[1]);
            $corresponding = CompanyKpiCommon::$corresponding;
            if (is_array($_tmp_data) && count($_tmp_data)) {
                foreach($_tmp_data as $v) {
                    if (isset($corresponding[$v['id']]))
                        $data[$corresponding[$v['id']]] = $v['weight_rate'];
                }
            }

            if (date('Ym',time()) == date('Ym',strtotime($month_date[0])) && !$reload_cache) {
                $mem_time = 12 * 3600;
            } else {
                $mem_time = 86400 * 60;
            }
            Yii::app()->cache->set($key, $data, $mem_time);
        }
        return $data;
    }

    public function getDriverEntryNum($city_id, $use_date) {
        $month_date = self::getMonthFirstAndLastDay($use_date);
        $sql = "SELECT COUNT(*) FROM t_driver WHERE city_id=:city_id AND created>=:date_start and created<:date_end";
        $command = Yii::app()->db_readonly->createCommand($sql);
        $command->bindParam(":city_id" , $city_id);
        $command->bindParam(":date_start" , $month_date[0]);
        $command->bindParam(":date_end" , date('Y-m-d', strtotime($month_date[1])+86400));
        $data = $command->queryScalar();
        $command->reset();
        return intval($data);
    }

    public function getFreeDays($city_id, $use_date) {
        $year = date('Y', strtotime($use_date.'01'));
        $month = date('m', strtotime($use_date.'01'));
        $date_start = date('Y-m-d', strtotime($use_date.'01'));
        $num = DailyOrderReport::get_day($year, $month);
        $date_end = date('Y-m-d', strtotime($use_date.'01')+($num-1)*86400);
        $work_log_model = new WorkLog();
        $date_line = DailyOrderReport::getDateLine($date_start, $date_end);
        $operate_model = new CompanyOperateSetting();
        $setting = $operate_model->getSettingInfoByType($city_id, $use_date, self::TYPE_PEAK);
        $i = 0;
        foreach($date_line as $date) {
            $work_log_data = $work_log_model->getPeakChartsData($date, $city_id);
            if ($work_log_data['free'] > $setting['grade']) {
                $i++;
            }
        }
        return $i;
    }

    public function getOrderUp($city_id, $use_date) {
        $year = date('Y', strtotime($use_date.'01'));
        $month = date('m', strtotime($use_date.'01'));
        $last_year = date('Y', strtotime($use_date.'01')-86400);
        $last_month = date('m', strtotime($use_date.'01')-86400);
        $last_mem_key = self::MEM_KEY_PREFIX.'_orderup_'.$city_id.$use_date;
        $last_order = Yii::app()->cache->get($last_mem_key);
        if (!$last_order) {
            $last_order = $this->getOrderCount($city_id, $last_year, $last_month);
            Yii::app()->cache->set($last_mem_key, $last_order,10*3600);
        }
        $current_order = $this->getOrderCount($city_id, $year, $month);
        $data = $last_order > 0 ? sprintf('%.2f%%',($current_order-$last_order)/$last_order*100) : 0;
        return $data;
    }

    public function getOrderCount($city_id, $year, $month) {
        $key = self::MEM_KEY_PREFIX.'_ordercount_'.$city_id.$year.$month;
        $data = Yii::app()->cache->get($key);
        if (!$data) {
            $sql = "SELECT COUNT(IF(`status`=1 OR `status` = 4,TRUE,NULL)) AS order_complate_count FROM t_daily_order_driver WHERE year = :year ";
            $sql .= ' AND month=:month AND city_id= :city_id';
            $command = Yii::app()->dbreport_readonly->createCommand($sql);
            $command->bindParam(":city_id" , $city_id);
            $command->bindParam(":year" , $year);
            $command->bindParam(":month" , $month);
            $data = $command->queryRow();
            if (date('Y',time()) == $year && $month == date('m', time())) {
                $mem_time = 1200;
            } else {
                $mem_time = 10*3600;
            }
            Yii::app()->cache->set($key, $data, $mem_time);
        }
        return intval($data['order_complate_count']);
    }

    public function getKpiData($city_id, $start_time, $end_time) {
        $typeArr= CustomerComplainType::model()->getComplainTypeList();
        //$typeArr = CustomerComplainType::model()->getComplainTypeByID(0);
        $data = $this->getComplainListData($city_id, $start_time, $end_time, $typeArr);
        return $data;
    }


    protected function getParentDataBytype($complaint_data,$parent_type_id,$type_data){
        $sub_type_count=$sub_weight_num=0;
        foreach($type_data as $type){
            if($type['parent_id']==$parent_type_id){
                $t_count=isset($complaint_data[$type['id']])?$complaint_data[$type['id']]:0;
                $sub_type_count+=$t_count;
                $sub_weight_num+=$t_count*$type['weight'];
            }
        }
        return array('type_count'=>$sub_type_count,'weight_num'=>$sub_weight_num);
    }

    public function getAllOrderCount($city_id, $start_time, $end_time) {
        $criteria_order = new CDbCriteria();
        $criteria_order->addCondition('city_id=:city_id');
        $params[':city_id']=$city_id;
        $criteria_order->addCondition('from_unixtime(booking_time, \'%Y-%m-%d\')  BETWEEN :s_time AND :e_time');
        $params[':s_time']=$start_time;
        $params[':e_time']=$end_time;
        $criteria_order->params=$params;
        $criteria_orderArr=$criteria_order->toArray();
        //当前城市、当前月份的所有状态订单数
        //$orderCount='100';
        $command=Order::getDbReadonlyConnection()->createCommand();
        $orderCount=$command->select('count(order_id)')
            ->from('t_order')
            ->where($criteria_orderArr['condition'],$criteria_orderArr['params'])
            ->queryScalar();
        $command->reset();
        return $orderCount;
    }

    public function getComplainData($city_id, $start_time, $end_time) {
        $sql = 'select count(id) as cnt,complain_type from t_customer_complain where city_id = :city_id and DATE_FORMAT(create_time, \'%Y-%m-%d\') BETWEEN :s_time AND :e_time and status in(2,3,4,8) and source!=7 group by complain_type'; 
        $command = Yii::app()->db_readonly->createCommand($sql);
        $command->bindParam(':city_id',$city_id);
        $command->bindParam(':s_time',$start_time);
        $command->bindParam(':e_time',$end_time);
        $complainData = $command->queryAll(); 
        $complainArr=array();
        foreach($complainData as $item){ 
            $complainArr[$item['complain_type']]=$item['cnt'];
        } 
        return $complainArr;
    }

    public function getComplainListData($city_id, $start_time, $end_time, $typeArr) {
        $orderCount = $this->getAllOrderCount($city_id, $start_time, $end_time);
        $complainArr = $this->getComplainData($city_id, $start_time, $end_time);
        $dataArr=array();
        $i=1;
        $total_cnt=$total_num=$total_rate=0;
        if($orderCount<=0) $orderCount=1;
        foreach($typeArr as $v) {
            $name='';
            $tmpArr=array();
            $tmpArr['id']=$v['id'];
            $tmpArr['parent_id']=$v['parent_id'];
            $tmpArr['type_count']=$tmpArr['weight_num']=$tmpArr['weight_rate']='';
            if($v['parent_id']==0){
                $name=$i.'.'.$v['name'];
                $i++;
                $total_type=$this->getParentDataBytype($complainArr,$v['id'],$typeArr);
                $tmpArr['type_count']=$total_type['type_count'];
                $tmpArr['weight_num']=$total_type['weight_num'];
                $tmpArr['weight_rate']=round(($tmpArr['weight_num']/$orderCount)*10000,4);

            }else{
                $tmpArr['type_count']=isset($complainArr[$v['id']])?$complainArr[$v['id']]:0;  //某分类投诉数量
                $tmpArr['weight_num']=$tmpArr['type_count']*$v['weight']; //加权分数
                //加权投诉率
                // 加权投诉率=投诉次数*分类权重系数/总订单数(当前城市、当前月份的所有状态订单数)*100％
                $tmpArr['weight_rate']=round(($tmpArr['weight_num']/$orderCount)*10000,4);
                $total_rate+=intval($tmpArr['weight_rate']);

                $total_cnt+=intval($tmpArr['type_count']);
                $total_num+=intval($tmpArr['weight_num']);
            }
            $tmpArr['type_name']=$v['parent_id']==0?$name:$v['name'];
            $dataArr[]=$tmpArr;
        }
        $total_arr=array(
            'id'=>0,'parent_id'=>0,
            'type_name'=>'总计',
            'type_count'=>$total_cnt,
            'weight_num'=>$total_num,
            'weight_rate'=>round(($total_num/$orderCount)*10000,4));
        $dataArr[]=$total_arr;
        return $dataArr;
    }

    public function getBusinessData($city_id, $use_date ,$reload_cache = false){
        $tmp = array();


        $key = self::MEM_KEY_PREFIX.'business_'.$city_id.'_'.$use_date;
        $data = Yii::app()->cache->get($key);
        if (!$data || $reload_cache) {
            $sql = "SELECT type_id, count( * ) as c FROM `t_company_business_info` WHERE city_id ='{$city_id}' AND use_date ='{$use_date}' AND STATUS =2 GROUP BY type_id";
            $data_obj = Yii::app()->db_readonly->createCommand($sql)->queryAll();
            if (isset($data_obj) && count($data_obj)) {
                foreach ($data_obj as $v) {
                    $tmp[$v['type_id']] = $v['c'];
                }

            }
            $data = $tmp;

            if (date('Ym',time()) == date('Ym',strtotime($use_date.'01')) && !$reload_cache) {
                $mem_time = 2 * 3600;
            } else {
                $mem_time = 86400 * 60;
            }

            Yii::app()->cache->set($key, $data, $mem_time);
        }
        return $data;
    }

    /**
     * @param $month
     * @param $city_id
     */
    public static function getMonthRecruitmentKpi($month, $city_id = 'all', $ignore_cache = FALSE){
        $key = sprintf(self::MEM_MONTH_RECRUITMENT_KPI, $month, $city_id);
        if(FALSE == $ignore_cache){
            $data =  RedisHAProxy::model()->redis->get($key);
            if(!empty($data)){
                return $data;
            }
        }
        $type = self::TYPE_ENTRY;
        if($city_id == 'all'){
            $sql = "SELECT sum(grade) AS grade FROM `t_company_operate_setting` WHERE type_id = {$type} AND use_date = :month";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(':month', $month);
        }else{
            $sql = "SELECT grade FROM `t_company_operate_setting` WHERE type_id = {$type} AND city_id = :city_id AND use_date = :month";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(':city_id', $city_id);
            $command->bindParam(':month', $month);
        }
        $result = $command->queryAll();
        if(empty($result) || is_null($result[0]['grade'])){
            $result = 0;
        }else{
            $result = $result[0]['grade'];
        }
        RedisHAProxy::model()->redis->set($key, $result);
        return $result;
    }

    /**
     * 取出某月的司机招聘数量
     */
    public static function getMonthRecruitmentCount($month, $city_id = 'all', $ignore_cache = FALSE){
        $key = sprintf(self::MEM_MONTH_RECRUITMENT_COUNT, $month, $city_id);
        $begin = date('Y-m-d', strtotime($month . '01'));
        $end = date('Y-m-d', strtotime($begin. "+1 months"));
        if(FALSE == $ignore_cache){
            $data =  RedisHAProxy::model()->redis->get($key);
            if(!empty($data)){
                return $data;
            }
        }
        if($city_id == 'all'){
            $sql = "SELECT COUNT(1) AS count FROM t_driver WHERE `created` BETWEEN '{$begin}' AND '{$end}'";
            $command = Yii::app()->db_readonly->createCommand($sql);
        }else{
            $sql = "SELECT COUNT(1) AS count FROM t_driver WHERE `city_id` = :city_id AND`created` BETWEEN '{$begin}' AND '{$end}'";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(':city_id', $city_id);
        }
        $result = $command->queryAll();
        if(empty($result)){
            $result = 0;
        }else{
            $result = $result[0]['count'];
        }
        if(date('Ym') == $month){
            RedisHAProxy::model()->redis->set($key, $result, 3600); //当前月，1小时一更新
        }else{
            RedisHAProxy::model()->redis->set($key, $result);
        }
        return $result;
    }

    /**
     * 取出所有漏斗图的数据
     * 司机招聘优化后的标准
     * $param $month '2015-1-1'
     * @return Array
     */
    public static function getFunnelChartDataV2($month, $city_id = 'all', $ignore_cache = FALSE){
        $start = strtotime(date('Y-m-1', strtotime($month)));
        $end = strtotime(date('Y-m-1', strtotime($month . "+1 months")));

        $key = sprintf(self::MEN_MONTH_FUNNEL_DATA_V2, $start, $city_id);
        if(FALSE == $ignore_cache){
            $data = unserialize(RedisHAProxy::model()->redis->get($key));
            if(!empty($data)){
                return $data;
            }
        }

        $data = array();
        if(!is_numeric($city_id) && 'all' != $city_id){
            return FALSE;
        }

        $city_condition = '';
        $recruitment_city_condition = '';
        if('all' != $city_id){
            $city_condition = 'AND city_id = '. $city_id;
            $recruitment_city_condition = 'AND t_driver_recruitment.`city_id` ='. $city_id;
        }
        //报名
        $sql_apply = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE 1 {$city_condition} AND `apply_time` BETWEEN '{$start}' AND '{$end}'";
        //预约
        $sql_date = "SELECT COUNT(1) AS count FROM t_driver_recruitment, t_booking_exam_driver WHERE 1 {$recruitment_city_condition} AND `apply_time` BETWEEN '{$start}' AND '{$end}' AND t_driver_recruitment.id_card = t_booking_exam_driver.id_card;";
        //参加考试
        //$sql_attend_exam = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE 1 {$city_condition} AND exam <> 0 AND `apply_time` BETWEEN '{$start}' AND '{$end}'";
        //路考通过
        $sql_road_pass = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE 1 {$city_condition} AND `apply_time` BETWEEN '{$start}' AND '{$end}' AND road_new = ". DriverRecruitment::STATUS_ROAD_FIELD_PASS;
        //签约
        $sql_sign = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE 1 {$city_condition} AND `apply_time` BETWEEN '{$start}' AND '{$end}' AND (status = " . DriverRecruitment::STATUS_ENTRY_OK . " OR status = ". DriverRecruitment::STATUS_SIGNED. ")";
        //线上考试通过
        $sql_online_exam_pass = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE 1 {$city_condition} AND exam = 1 AND `apply_time` BETWEEN '{$start}' AND '{$end}'";
        //签收装备
        $sql_received_equip = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE 1 {$city_condition} AND `apply_time` BETWEEN '{$start}' AND '{$end}' AND status = " . DriverRecruitment::STATUS_SIGNED;
        //激活
        $sql_active = "SELECT COUNT(1) AS count FROM t_driver_recruitment, t_driver WHERE 1 {$recruitment_city_condition} AND t_driver_recruitment.`apply_time` BETWEEN '{$start}' AND '{$end}' AND t_driver_recruitment.driver_id = t_driver.user and t_driver.mark = 0 AND t_driver_recruitment.`status` = 4";
        //活跃
        $sql_lively = "SELECT COUNT(DISTINCT(t_driver_recruitment.driver_id)) AS count FROM t_driver_recruitment, t_driver_online_log WHERE 1 {$recruitment_city_condition} AND t_driver_recruitment.`apply_time` BETWEEN '{$start}' AND '{$end}' AND t_driver_recruitment.driver_id = t_driver_online_log.driver_id and t_driver_online_log.`hot_time` > 0";
        $result = Yii::app()->db_readonly->createCommand($sql_apply)->queryRow();
        $data['apply'] = $result['count'];
        $result = Yii::app()->db_readonly->createCommand($sql_date)->queryRow();
        $data['date'] = $result['count'];
        //$result = Yii::app()->db_readonly->createCommand($sql_attend_exam)->queryRow();
        //$data['attend_exam'] = $result['count'];
        $result = Yii::app()->db_readonly->createCommand($sql_road_pass)->queryRow();
        $data['road_pass'] = $result['count'];
        $result = Yii::app()->db_readonly->createCommand($sql_sign)->queryRow();
        $data['sign'] = $result['count'];
        $result = Yii::app()->db_readonly->createCommand($sql_online_exam_pass)->queryRow();
        $data['online_exam_pass'] = $result['count'];
        $result = Yii::app()->db_readonly->createCommand($sql_received_equip)->queryRow();
        $data['received_equip'] = $result['count'];
        $result = Yii::app()->db_readonly->createCommand($sql_active)->queryRow();
        $data['active'] = $result['count'];
        $result = Yii::app()->db_readonly->createCommand($sql_lively)->queryRow();
        $data['lively'] = $result['count'];

        //求转化率
        $base = $data['apply'];
        if(0 == $base){
            $data['p']['apply']          = 0;
            $data['p']['date']           = 0;
            //$data['p']['attend_exam']    = 0;
            $data['p']['road_pass']      = 0;
            $data['p']['sign']           = 0;
            $data['p']['online_exam_pass']           = 0;
            $data['p']['received_equip'] = 0;
            $data['p']['active']         = 0;
            $data['p']['lively']         = 0;
        }else{
            $data['p']['apply']          = 100;
            $data['p']['date']           = round($data['date'] / $base * 100, 2);
            //$data['p']['attend_exam']    = round($data['attend_exam'] / $base * 100, 2);
            $data['p']['road_pass']      = round($data['road_pass'] / $base * 100, 2);
            $data['p']['sign']           = round($data['sign'] / $base * 100, 2);
            $data['p']['online_exam_pass']           = round($data['online_exam_pass'] / $base * 100, 2);
            $data['p']['received_equip'] = round($data['received_equip'] / $base * 100, 2);
            $data['p']['active']         = round($data['active'] / $base * 100, 2);
            $data['p']['lively']         = round($data['lively'] / $base * 100, 2);
        }
        RedisHAProxy::model()->redis->set($key, serialize($data), 86400);//每日更新
        return $data;
    }



    /**
     * 取出所有漏斗图的数据
     * $param $month '2015-1-1'
     * @return Array
     */
    public static function getFunnelChartData($month, $city_id = 'all', $ignore_cache = FALSE){
        $start = strtotime(date('Y-m-1', strtotime($month)));
        $end = strtotime(date('Y-m-1', strtotime($month . "+1 months")));

        $key = sprintf(self::MEN_MONTH_FUNNEL_DATA, $start, $city_id);
        //RedisHAProxy::model()->redis->delete($key);
        if(FALSE == $ignore_cache){
            $data = unserialize(RedisHAProxy::model()->redis->get($key));
            if(!empty($data)){
                return $data;
            }
        }

        $data = array();
        if($city_id == 'all'){
            $sql_apply = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE `apply_time` BETWEEN '{$start}' AND '{$end}'";
            //$sql_pass_exam = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE `apply_time` BETWEEN '{$start}' AND '{$end}' AND status = 7";
            $sql_pass_exam = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE `apply_time` BETWEEN '{$start}' AND '{$end}' AND status <> 1";
            //$sql_inform_interview = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE `apply_time` BETWEEN '{$start}' AND '{$end}' AND exam_times > 0 AND status = 7";
            $sql_inform_interview = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE `apply_time` BETWEEN '{$start}' AND '{$end}' AND exam_times > 0";
            $sql_attend_interview = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE `apply_time` BETWEEN '{$start}' AND '{$end}' AND interview IS NOT NULL";
            $sql_pass_interview = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE `apply_time` BETWEEN '{$start}' AND '{$end}' AND status = 2";
            $sql_pass_road_exam = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE `apply_time` BETWEEN '{$start}' AND '{$end}' AND status = 3";
            $sql_sign = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE `apply_time` BETWEEN '{$start}' AND '{$end}' AND status = 4";
            $sql_active = "SELECT COUNT(1) AS count FROM t_driver_recruitment, t_driver WHERE t_driver_recruitment.`apply_time` BETWEEN '{$start}' AND '{$end}' AND t_driver_recruitment.driver_id = t_driver.user and t_driver.mark = 0 AND t_driver_recruitment.`status` = 4";
            $sql_lively = "SELECT COUNT(DISTINCT(t_driver_recruitment.driver_id)) AS count FROM t_driver_recruitment, t_driver_online_log WHERE t_driver_recruitment.`apply_time` BETWEEN '{$start}' AND '{$end}' AND t_driver_recruitment.driver_id = t_driver_online_log.driver_id and t_driver_online_log.`hot_time` > 0";

        }else if(is_numeric($city_id)){
            $sql_apply = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE `city_id` = {$city_id} AND `apply_time` BETWEEN '{$start}' AND '{$end}'";
            $sql_pass_exam = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE `city_id` = {$city_id} AND `apply_time` BETWEEN '{$start}' AND '{$end}' AND status <> 1";
            $sql_inform_interview = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE `city_id` = {$city_id} AND `apply_time` BETWEEN '{$start}' AND '{$end}' AND  exam_times > 0";
            $sql_attend_interview = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE `city_id` = {$city_id} AND `apply_time` BETWEEN '{$start}' AND '{$end}' AND interview IS NOT NULL";
            $sql_pass_interview = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE `city_id` = {$city_id} AND `apply_time` BETWEEN '{$start}' AND '{$end}' AND status = 2";
            $sql_pass_road_exam = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE `city_id` = {$city_id} AND `apply_time` BETWEEN '{$start}' AND '{$end}' AND status = 3";
            $sql_sign = "SELECT COUNT(1) AS count FROM t_driver_recruitment WHERE `city_id` = {$city_id} AND `apply_time` BETWEEN '{$start}' AND '{$end}' AND status = 4";
            $sql_active = "SELECT COUNT(1) AS count FROM t_driver_recruitment, t_driver WHERE t_driver_recruitment.`city_id` = {$city_id} AND t_driver_recruitment.`apply_time` BETWEEN '{$start}' AND '{$end}' AND t_driver_recruitment.driver_id = t_driver.user and t_driver.mark = 0 and t_driver_recruitment.`status` = 4";
            $sql_lively = "SELECT COUNT(DISTINCT(t_driver_recruitment.driver_id)) AS count FROM t_driver_recruitment, t_driver_online_log WHERE t_driver_recruitment.`city_id` = {$city_id} AND t_driver_recruitment.`apply_time` BETWEEN '{$start}' AND '{$end}' AND t_driver_recruitment.driver_id = t_driver_online_log.driver_id and t_driver_online_log.`hot_time` > 0";
        }else{
            return FALSE;
        }
        $result = Yii::app()->db_readonly->createCommand($sql_apply)->queryAll();
        $data['apply'] = $result[0]['count'];
        $result = Yii::app()->db_readonly->createCommand($sql_pass_exam)->queryAll();
        $data['pass_exam'] = $result[0]['count'];
        //去掉通知面试数据
        //$result = Yii::app()->db_readonly->createCommand($sql_inform_interview)->queryAll();
        //$data['inform_interview'] = $result[0]['count'];
        $result = Yii::app()->db_readonly->createCommand($sql_attend_interview)->queryAll();
        $data['attend_interview'] = $result[0]['count'];
        $result = Yii::app()->db_readonly->createCommand($sql_pass_interview)->queryAll();
        $data['pass_interview'] = $result[0]['count'];
        $result = Yii::app()->db_readonly->createCommand($sql_pass_road_exam)->queryAll();
        $data['pass_road_exam'] = $result[0]['count'];
        $result = Yii::app()->db_readonly->createCommand($sql_sign)->queryAll();
        $data['sign'] = $result[0]['count'];
        $result = Yii::app()->db_readonly->createCommand($sql_active)->queryAll();
        $data['active'] = $result[0]['count'];
        $result = Yii::app()->db_readonly->createCommand($sql_lively)->queryAll();
        $data['lively'] = $result[0]['count'];

        //整理结果：数据不是从同一维度取出，所有以 status 为维度取出的数据，
        //上层等于所有下层相加
        //status 变化路径：
        //      已报名0→1
        //      在线考试通过1→7
        //      已面试通过7→2
        //      已路考通过2→3
        //      入职成功3→4
        $data['pass_road_exam']   += $data['sign'];
        $data['pass_interview']   += $data['pass_road_exam'];
        //$data['pass_exam']        += $data['pass_interview'];
        //$data['inform_interview'] += $data['pass_interview'];
        //不加status = 1 条件，直接 sql 计算报名人数
        //$data['apply']            += $data['pass_exam'];
        //求转化率
        $base                       = $data['apply'];
        if(0 == $base){
            $data['p']['apply']            = 0;
            $data['p']['pass_exam']        = 0;
            //$data['p']['inform_interview'] = 0;
            $data['p']['attend_interview'] = 0;
            $data['p']['pass_interview']   = 0;
            $data['p']['pass_road_exam']   = 0;
            $data['p']['sign']             = 0;
            $data['p']['active']           = 0;
            $data['p']['lively']           = 0;
        }else{
            $data['p']['apply']            = 100;
            $data['p']['pass_exam']        = round($data['pass_exam'] / $base * 100, 2);
            //$data['p']['inform_interview'] = round($data['inform_interview'] / $base * 100, 2);
            $data['p']['attend_interview'] = round($data['attend_interview'] / $base * 100, 2);
            $data['p']['pass_interview']   = round($data['pass_interview'] / $base * 100, 2);
            $data['p']['pass_road_exam']   = round($data['pass_road_exam'] / $base * 100, 2);
            $data['p']['sign']             = round($data['sign'] / $base * 100, 2);
            $data['p']['active']           = round($data['active'] / $base * 100, 2);
            $data['p']['lively']           = round($data['lively'] / $base * 100, 2);
        }
        RedisHAProxy::model()->redis->set($key, serialize($data), 86400);//每日更新
        return $data;
    }

    /**
     * 平均入职时间，返回x年x月x日
     */
    public static function averageEntryTimeCost($month, $city_id = 'all', $ignore_cache = FALSE){
        $start = strtotime(date('Y-m-1', strtotime($month)));
        $end = strtotime(date('Y-m-1', strtotime($month . "+1 months")));
        $key = sprintf(self::MEN_MONTH_AVG_NTRY_TIME, $month, $city_id);
        if($ignore_cache == FALSE){
            $data =  RedisHAProxy::model()->redis->get($key);
            if(!empty($data)){
                return $data;
            }
        }
        if('all' == $city_id){
            $sql = "SELECT ROUND(AVG(UNIX_TIMESTAMP(t_driver.created)  -  t_driver_recruitment.apply_time)) AS avg_time FROM t_driver_recruitment, t_driver WHERE t_driver_recruitment.driver_id = t_driver.user AND UNIX_TIMESTAMP(t_driver.created)  >  t_driver_recruitment.apply_time AND t_driver_recruitment.`apply_time` BETWEEN '{$start}' AND '{$end}';";
        }else{
            $sql = "SELECT ROUND(AVG(UNIX_TIMESTAMP(t_driver.created)  -  t_driver_recruitment.apply_time)) AS avg_time FROM t_driver_recruitment, t_driver WHERE t_driver_recruitment.driver_id = t_driver.user AND UNIX_TIMESTAMP(t_driver.created)  >  t_driver_recruitment.apply_time AND t_driver_recruitment.`apply_time` BETWEEN '{$start}' AND '{$end}' AND t_driver_recruitment.`city_id` = {$city_id};";
        }
        $result = Yii::app()->db_readonly->createCommand($sql)->queryAll();
        $result = $result[0]['avg_time'];
        if(empty($result)){
            return '没有符合条件的司机';
        }
        $result = self::_secondsToTime($result);
        RedisHAProxy::model()->redis->set($key, ($result), 86400);//每日更新
        return $result;
    }

    public static function _secondsToTime($seconds) {
        $dtF = new DateTime("@0");
        $dtT = new DateTime("@$seconds");
        return $dtF->diff($dtT)->format('%a 天, %h 小时, %i 分');
    }

    /**
     * 删除最近一年的缓存
     */
    public static function clearFunnelCache($city_id = 'all'){
        for($i = 0; $i < 12; $i++){
            $month = date('Y-m-1', strtotime("-{$i} months"));
            if('all' == $city_id){
                $cities = Dict::items('city');
                foreach($cities as $id => $name){
                    $key1 = sprintf(self::MEN_MONTH_FUNNEL_DATA, $month, $id);
                    $key2 = sprintf(self::MEN_MONTH_FUNNEL_DATA_V2, $month, $id);
                    RedisHAProxy::model()->redis->delete($key1);
                    RedisHAProxy::model()->redis->delete($key2);
                }
            }else{
                $key1 = sprintf(self::MEN_MONTH_FUNNEL_DATA, $month, $city_id);
                $key2 = sprintf(self::MEN_MONTH_FUNNEL_DATA_V2, $month, $city_id);
                RedisHAProxy::model()->redis->delete($key1);
                RedisHAProxy::model()->redis->delete($key2);
            }
        }
    }
}
