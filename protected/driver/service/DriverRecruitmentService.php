<?php
/**
 * 司机招募service
 * Created by PhpStorm.
 * User: zhaoyingshuang
 * Date: 15-4-30
 * Time: 下午12:20
 */

class DriverRecruitmentService {
    public static $road_dict_source;
    public static $road_dict;
    public static $rank;
    public static $exam_dict_source;
    public static $exam_dict;
    public static $hours_desc;
    public static $hours_name;
    public static $hours_used;
    private static $instance;

    const STATUS_ROAD_FIELD_FAILED = DriverRecruitment::STATUS_ROAD_FIELD_FAILED;//路考不通过
    const STATUS_ONLINE_EXAM_INIT = DriverRecruitment::STATUS_ONLINE_EXAM_INIT;//默认状态
    const STATUS_ONLINE_EXAM_PASS = DriverRecruitment::STATUS_ONLINE_EXAM_PASS;//在线考核通过
    const STATUS_ONLINE_EXAM_FAILED = DriverRecruitment::STATUS_ONLINE_EXAM_FAILED;//在线考核不通过
    const TYPE_DRIVER_PRISE_EXAM = DriverExamnewOnline::TYPE_DRIVER_PRISE_EXAM;//司机端有奖答题
    const TYPE_TEST = DriverExamnewOnline::TYPE_TEST;//模拟考试
    const TYPE_ONLINE = DriverExamnewOnline::TYPE_ONLINE;//在线考试正式的
    const TYPE_DRIVER_PRISE_EXAM_DES = DriverExamStudy::TYPE_DRIVER_PRISE_EXAM;//司机端有奖答题,和DriverExamnewOnline下的重名
    const TYPE_DRIVER_EXAM = DriverExamStudy::TYPE_DRIVER_EXAM;//被投诉司机考核
    const CATE_MUST_ID = DriverExamStudy::CATE_MUST_ID;
    const REWARD_PUNISH_TYPE = DriverWealthLog::REWARD_PUNISH_TYPE;//奖赏或处罚
    const SMS = BookingExamSetting::SMS;//短信模版

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new DriverRecruitmentService();
        }
        self::init();
        return self::$instance;
    }

    /**
     * 初始化
     */
    public static function init() {
        self::$road_dict_source = DriverRecruitment::$road_dict_source;
        self::$road_dict = DriverRecruitment::$road_dict;
        self::$rank = DriverRoadExam::$rank;
        self::$exam_dict_source = DriverRecruitment::$exam_dict_source;
        self::$exam_dict = DriverRecruitment::$exam_dict;
        self::$hours_desc = BookingExamSetting::$hours_desc;
        self::$hours_name = BookingExamSetting::$hours_name;
        self::$hours_used = BookingExamSetting::$hours_used;
    }

    /**
     * 通过driverid删除token
     * @param $driver_id 司机ID
     * @return mixed
     */
    public function delTokenByDriverId($driver_id) {
        return DriverManagerToken::model()->delTokenByDriverId($driver_id);
    }

    /**
     * 生成司管app的token验证
     * @param $driver_id 司机ID
     * @return mixed
     */
    public function createManagerToken($driver_id) {
        return DriverToken::model()->createManagerToken($driver_id);
    }

    /**
     * 根据token获取司管信息
     * @param $token
     * @return mixed
     */
    public function getDriverManagerToken($token) {
        return DriverStatus::model()->getDriverManagerToken($token);
    }

    /**
     * 退出登陆
     * @param $token
     * @return mixed
     */
    public function logout($token) {
        return DriverManagerToken::model()->logout($token);
    }

    /**
     * 根据身份证号获取验证信息
     * @param $id_card 身份证号
     * @return mixed
     */
    public function getDriverInfoByIDCard($id_card) {
        return DriverRecruitment::model()->getDriverByIDCard($id_card);
    }

    /**
     * 根据城市和招聘入职id，获取报名流水号
     * @param $city_id 城市ID
     * @param $recruitment_id 招聘入职ID
     * @return string 报名流水号
     */
    public function getSerialNum($city_id, $recruitment_id) {
        return DriverRecruitment::model()->getSerialNum($city_id, $recruitment_id);
    }

    /**
     * 是否通过路考
     * @param $road 路考状态
     * @return bool
     */
    public function isRoadPass($road) {
        return DriverRoadExam::model()->isRoadPass($road);
    }

    /**
     * 获取最近的一条记录 （路考结果）
     * @param $serial_number 报名流水号
     * @return mixed
     */
    public function getPassInfo($serial_number) {
        return DriverRoadExam::model()->getPassInfo($serial_number);
    }

    /**
     * 获得某日的路考信息
     * @param $date 路考日期
     * @param $serialNum 司机报名流水号，不包括城市前缀
     * @return mixed
     */
    public function getRoadExamInfo($date, $serial_number) {
        return DriverRoadExam::model()->getRoadExamInfo($date, $serial_number);
    }

    /**
     * 增加考试记录
     * @param $params
     *  idCard 身份证号
     *  cityId 城市ID
     *  serialNum 报名流水号
     *  pass 是否通过
     *  operator 考官工号
     *  autoRoadExam 自动挡打分
     *  manualRoadExam 手动档打分
     *  reason 不通过原因
     * @return bool
     */
    public function addRoadInfo($params) {
        return DriverRoadExam::model()->addRoadInfo($params);
    }

    /**
     * 线上考试开始，司机端点击开始后生成考题
     * @param $id
     * @return array
     */
    public function startExam($id) {
        return DriverExamnewOnline::model()->startExam($id);
    }

    /**
     * 获取value值
     * @param $key
     * @return mixed
     */
    public function single_get($key) {
        return DriverStatus::model()->single_get($key);
    }

    /**
     * 设置valve到key中
     * @param $key
     * @param $value
     * @param $expire
     * @return bool
     */
    public function single_set($key, $value, $expire) {
        return DriverStatus::model()->single_set($key, $value, $expire);
    }

    /**
     * 更新司机招聘信息
     * @param $id 招聘入职ID
     * @param $param 需更新字段数组
     * @return mixed
     */
    public function updateInfoByID($id, $param) {
        return DriverRecruitment::model()->updateByPk($id,$param);
    }

    /**
     * 通过openid获取用户信息
     * @param $open_id 微信openid
     * @return array|bool
     */
    public function getInfoByOpenid($open_id) {
        return DriverRecruitment::model()->getInfoByOpenid($open_id);
    }

    /**
     * 通过微信openid获取所有错题
     * @param $open_id
     * @return mixed
     */
    public function getAllWrong($open_id) {
        return DriverExamnewPractice::model()->getAllWrong($open_id);
    }

    /**
     * 检测用户是否在允许的考试次数内
     * @param $open_id
     * @param $type
     * @return bool
     */
    public function checkExamTime($open_id, $type) {
        return DriverExamnewOnline::model()->checkExamTime($open_id, $type);
    }

    /**
     * 是否存在考题
     * @param $open_id
     * @param $type
     * @return bool
     */
    public function checkExistQuestion($open_id, $type) {
        return DriverExamnewOnline::model()->checkExistQuestion($open_id, $type);
    }

    /**
     * 通过主健查找考题
     * @param $exam_id
     * @return mixed
     */
    public function findExamByID($exam_id) {
        return DriverExamnewOnline::model()->findByPk($exam_id);
    }

    /**
     * 司机是否需要参加答题
     * @param $driver_id
     * @param $type
     * @return array
     */
    public function checkUserNeedExam($driver_id, $type) {
        return DriverExamnewOnline::model()->checkUserNeedExam($driver_id, $type);
    }

    /**
     * 创建被投诉司机考题，调用前需要先check 是否有题了
     * @param $open_id
     * @param $signup_id
     * @param $type
     * @param $rules = array(3=>10,5=>10); 分类id => 考题数目
     * @return array
     */
    public function createQuestionStudy($open_id, $cate_id, $type, $rules, $city_id) {
        return DriverExamnewOnline::model()->createQuestionStudy($open_id, $cate_id, $type, $rules, $city_id);
    }

    /**
     * 生成模拟考试考题
     * @param $params
     *  open_id
     *  signup_id 报名ID
     *  questions 题目
     *  answers 答案
     *
     * @return mixed
     */
    public function saveExam($params) {
        $time = date('Y-m-d H:i:s');
        $params['update_time'] = $time;
        $params['create_time'] = $time;
        $params['type'] = DriverExamnewOnline::TYPE_TEST;

        $model = new DriverExamnewOnline();
        $model->attributes = $params;
        return $model->save();
    }

    /**
     * 存储用户考试id
     * @param $open_id
     * @param $exam_id
     * @param $type
     * @return bool
     */
    public function addExamId($open_id, $exam_id, $type) {
        return DriverExamnewOnline::model()->addExamId($open_id, $exam_id, $type);
    }

    /**
     * 删除用户考试id
     * @param $open_id
     * @param $type
     * @return bool
     */
    public function delExamId($open_id, $type) {
        return DriverExamnewOnline::model()->delExamId($open_id, $type);
    }

    /**
     * 计算考题分数
     * @param $exam_id
     * @param $open_id 用户微信open_id
     * @param $type 类型 模拟，正式考试
     * @param $user_answer
     * @return array 返回总题数，正确数
     */
    public function checkAnswer($exam_id, $open_id, $type, $user_answer) {
        return DriverExamnewOnline::model()->checkAnswer($exam_id, $open_id, $type, $user_answer);
    }

    /**
     * 通过主健查找考题信息
     * @param $exam_id
     * @return mixed
     */
    public function getInfo($exam_id) {
        return DriverExamnewOnline::model()->getInfo($exam_id);
    }

    /**
     * 司机发，扣e币入口
     * @param $driver_id
     * @param $wealth
     * @param $type
     * @param $city_id
     * @param $create_time
     * @param $des
     * @return bool|int
     */
    public function driverWealth($driver_id, $wealth, $type, $city_id, $create_time, $des) {
        return DriverExt::model()->driverWealth($driver_id, $wealth, $type, $city_id, $create_time, $des);
    }

    /**
     * 根据司机报名身份证，返回司机报名所有相关状态信息数组
     * @param $id_card
     * @return array
     */
    public function getDriverStateByIdCard($id_card) {
        return DriverRecruitment::model()->getDriverStateByIdCard($id_card);
    }

    /**
     * 通过发送短信的次数查找司机
     * @param $send_times
     * @param $city_id
     * @param $start_id
     * @return mixed
     */
    public function getDriverByScendTimes($send_times, $city_id, $start_id) {
        return DriverRecruitment::model()->getDriverByScendTimes($send_times, $city_id, $start_id);
    }

    /**
     * 更新发送短信次数
     * @param $id
     * @param $times
     * @return mixed
     */
    public function updateSendTimes($id, $times) {
        return DriverRecruitment::model()->updateSendTimes($id, $times);
    }

    /**
     * 取出某个月的报名状态
     * @param $month 例子：201501
     * @param $city_id 城市ID
     * @return mixed
     */
    public function getMonthData($month, $city_id) {
        return BookingExamSetting::model()->getMonthData($month, $city_id);
    }

    /**
     * 通过身份证号获取当天预约信息
     * @param $id_card
     * @return mixed
     */
    public function getDataByIdCard($id_card) {
        $book_record = BookingExamDriver::model()->find('id_card = :id_card AND date >= :date',
            array(':id_card' => $id_card,
                ':date' => date('Ymd'),
            )
        );
        return $book_record;
    }

    /**
     * 司机预约
     * @param $city_id
     * @param $date
     * @param $id_card
     * @param $hour
     * @return mixed
     */
    public function driverBook($city_id, $date, $id_card, $hour) {
        return BookingExamDriver::model()->driverBook($city_id, $date, $id_card, $hour);
    }

    /**
     * 通过身份证号查询招募信息
     * @param $id_card
     * @return mixed
     */
    public function findByIDCard($id_card) {
        return DriverRecruitment::model()->findByIDCard($id_card);
    }

    /**
     * 保存司机招募信息
     * @param $param
     *  name
     *  address
     *  register_city
     *  signup_src
     *  recommender
     *  act_type
     * @return mixed
     */
    public function saveData($param) {
        $param['apply_time'] = time();
        $model = new DriverRecruitment();
        $model->attributes = $param;
        $model->work_type = 1;
        $model->register_city = $param['register_city'];
        $model->signup_src = $param['signup_src'];
        $model->recommender = $param['recommender'];
        $model->act_type = $param['act_type'];
        return $model->save(false);
    }

    /**
     * 通过多个id获取问题
     * @param $ids
     * @param bool $shuffle
     * @return bool
     */
    public function getQuestionById($ids, $shuffle = true) {
        return QuestionNew::model()->getQuestionById($ids, $shuffle);
    }

    /**
     * 格式化问题
     * @param $question
     * @param bool $have_answer
     * @return mixed
     */
    public  function formatQuestion($question, $have_answer = false) {
        return QuestionNew::model()->formatQuestion($question, $have_answer);
    }

    /**
     * 取出某时间段的开始结束时间
     * @param $hour
     * @return array
     */
    public function getHourStartEnd($hour) {
        return BookingExamSetting::model()->getHourStartEnd($hour);
    }

    /**
     * 获取城市某天预约列表
     * @param $city_id 城市ID
     * @param $booking_date 20150101
     * @return mixed
     */
    public function getBookingList($city_id, $booking_date) {
        return BookingExamDriver::model()->getBookingList($city_id, $booking_date);
    }
}