<?php
/**
 * 司机service
 * Class DriverService
 */
Class DriverService{
    /**
     * 有效司机
     */
    const MARK_ENABLE = 0;
    /**
     * 已屏蔽的司机
     */
    const MARK_DISNABLE = 1;
    /**
     * 已换手机的司机
     */
    const MARK_CHANGE = 2;

    /**
     * 解约的司机
     */
    const MARK_LEAVE = 3;

    //测试账号
    const IS_TEST_TRUE = 1;

    //普通账号
    const IS_TEST_FALSE = 0;

    //返程车
    const IS_TEST_RETRUN = 2;

    //司管
    const IS_DRIVER_MANAGER =1;//1是司管，0不是

    //司机支持的业务，目前有代驾、洗车,共32位...............................1
    const SERVICE_TYPE_FOR_DAIJIA = '00000000000000000000000000000001'; //代驾
    const SERVICE_TYPE_FOR_XICHE  = '00000000000000000000000000000010'; //洗车


    //======== wiki 已定义的接口 =======
    //http://wiki.edaijia.cn/dwiki/doku.php?id=%E8%AE%A2%E5%8D%95%E5%B9%B3%E5%8F%B0%E4%BE%9D%E8%B5%96%E5%95%86%E4%B8%9A%E5%B9%B3%E5%8F%B0service%E5%88%97%E8%A1%A8

    public static function getByToken($token){
        //DriverStatus::model()->getByToken($token);
        //DriverToken::model()->validateToken($token);
		$driver = DriverStatus::model()->getByToken($token);
		if ($driver&&$driver->token!=null) {
			return true;
		}
		return false;
    }

    /**
     * 获取司机状态信息
     * @param $driver_id
     */
    public static function status($driver_id){
        return DriverStatus::model()->get($driver_id);
    }

    //========== wiki 已定义的接口 end =======

    //========== 根据接口整理 =======
    //http://wiki.edaijia.cn/dwiki/doku.php?id=api%E5%88%97%E8%A1%A8
	public static function register($imei, $phone) {
        return Employee::register($imei, $phone);
    }

    public static function validateDriverPhone($imei, $sim, $driver_id){
        return DriverPhone::model()->validateDriverPhone($imei, $sim, $driver_id);
    }

    public static function setAppVerision($driver_id, $app_ver) {
        return DriverStatus::model()->set_app_ver($driver_id, $app_ver);
    }
    public static function getVersionByCity($city_id){
        return DriverCityVersion::model()->getVesionByCity($city_id);
    }

    public static function updateRedisAccount($driver_id, $account){
        return DriverStatus::model()->updateAccount($driver_id, $account);
    }

    /**
     * 激活司机
     * @param $data  必须包扣 user、comment
     * @return bool
     * author mengtianxue
     */
    public static function active($data){
        //检查用户状态 如果是屏蔽状态 把用户状态改成正常状态
        $driver = Driver::getProfile($data['user']);
        if ($driver->mark != Driver::MARK_LEAVE && $driver->mark != Driver::MARK_ENABLE) {
            //把司机状态置为正常状态
            Driver::model()->block($data['user'], Driver::MARK_ENABLE, DriverLog::LOG_MARK_ENABLE, $data['comment'], true);
            //更新redis
            DriverAccountService::reloadRedisDriverBalance($data['user']);
            return true;
        }
        return false;
    }
    //========== 根据接口整理 end =======

    /**
     * 看一个电话是否有对应的司机
     *
     * @param $phone
     * @return bool
     */
    public static function isDriver($phone){
        return Driver::model()->isDriver($phone);
    }

    /**
     * 获取司机状态
     */
    public static function mark($mark = ''){
        return Driver::getMark($mark);
    }
    /**
     *
     * 屏蔽司机
     * @param 司机工号 $user
     * @param 屏蔽激活 $mark
     * @param 屏蔽类型 $type
     * @param 屏蔽原因 $reason
     * @param $is_auto 是否欠费屏蔽
     * @param $enable_auto 是否自动解除屏蔽
     */
    public static function block($user, $mark, $type, $reason, $is_auto = false, $is_system= false, $enable_auto= false) {
        return Driver::model()->block($user, $mark, $type, $reason, $is_auto, $is_system, $enable_auto);
    }

    public static function enableByFee($driverId){
        return Driver::enableByFee($driverId);
    }

    public static function driverOrderCnt($driver_id){
        return Driver::getDriverOrder($driver_id);
    }

    public static function driverCommentCnt($driver_id){
        return Driver::getDriverComments($driver_id);
    }

    public static function driverReadyOrderCnt($driver_id){
        return Driver::getDriverReadyOrder($driver_id);
    }

    /**
     * 根据电话号码查询司机信息
     * @editor sunhongjing 2013-09-07 增加条件验证和排除解约司机
     * @param int $phone
     */
    public static function getDriverByPhone($phone){
        return Driver::getDriverByPhone($phone);
    }

    /**
     * 获取司机的最新状态信息
     * @param int $user_id
     * @return string $status
     */
    public static function satus($user_id){
        return Driver::model()->getStatus($user_id);
    }

    public static function orderList($driver_id){
        return Driver::model()->getOrderList($driver_id);
    }

    /**
     *
     * 查询推荐司机列表
     */
    public static function recommandedDrivers(){
        return Driver::model()->getRecommandList();
    }

    /**
     *
     * 获取司机位置列表
     * @param int $city_id
     * @param string $mark
     * @param string $status
     */
    public static function driversPosition($city_id, $mark){
        return Driver::model()->getStatusList($city_id, $mark);
    }
    /**
     * 通过身份证号查询司机信息
     * @param $id_card 身份证号
     * @return array
     */
    public static function getDriverByIdCard($id_card){
        return Driver::model()->getDriverByIdCard($id_card);
    }
    /**
     *
     * 查询司机的被推荐信息
     * @param string $driver_id
     */
    public static function getDriverRecommand($driver_id){
        return Driver::model()->getDriverRecommand($driver_id);
    }
    /**
     *
     * 查询司机列表
     * @param int $city_id
     * @param string $mark
     * @return boolean $result
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-05-13
     */
    public static function getDriverList($city_id, $mark){
        return Driver::model()->getDriverList($city_id, $mark);
    }

    public static function getProfileWithManagerCity($driver_id){
        return Driver::model()->getProfileWithManagerCity($driver_id);
    }

    /**
     * 校验密码
     */
    public static function validatePassword($password){
        return Driver::model()->validatePassword($password);
    }
    /**
     *
     * 用工号查询司机信息
     * @param string $driver_id
     */
    public static function getDriverByDriverId($driver_id){
        return Driver::model()->getProfile($driver_id);
    }
    /**
     *
     * 用IMEI查询司机工号
     * @param string $imei
     */
    public static function getProfileByImei($imei){
        return Driver::model()->getProfileByImei($imei);
    }
    public static function getProfileById($id){
        return Driver::model()->getProfileById($id);
    }
    public static function validateImei($imei){
        return Driver::validateImei($imei);
    }
    /**
     * 用司机工号取IEMI
     */
    public static function getImei($id){
        return Driver::getImei($id);
    }
    /**
     * 查找要扣款城市的司机
     * @param $city 城市id(string ,隔开)
     * @param $cast 最低金额
     * @return array
     * author mengtianxue
     */
    public static function DriverLists($city, $cast){
        return Driver::model()->DriverLists($city, $cast);
    }
    /**
     * 司机签约唯一方法
     * @param     $id 报名表（t_driver_recruitment）主键
     * @param     $v_number   V号 （t_driver_phone）
     * @param     $driver_phone 司机工作电话
     * @param int $assure 担保信息（默认为未担保）
     * @return array
     * @throws Exception
     */
    public static function driverEntry($id, $driver_id, $v_number, $driver_phone, $assure = 8){
        return Driver::model()->driverEntry($id, $driver_id, $v_number, $driver_phone, $assure);
    }
    /**
     * 向 t_driver 表中插入数据
     * @param $data
     * @return bool
     */
    public static function insertDriverRecord($data){
        return Driver::model()->insertDriverRecord($data);
    }
    /**
     * 获得司机头像地址
     * @param string $driver_id 司机工号
     * @param string $city_id 城市
     * @param string $size 尺寸 self::PICTURE_SMALL（小图117px） self::PICTURE_MIDDLE (中图156px) self::PICTURE_NORMAL(544px)
     * @return string
     */
    public static function getPictureUrl($driver_id, $city_id, $size = self::PICTURE_MIDDLE, $version = false){
        return Driver::getPictureUrl($driver_id, $city_id, $size, $version);
    }
    /**
     * 获得上传的司机头像地址 上传头像时使用 其他不是用
     * @param string $driver_id 司机工号
     * @param string $city_id 城市
     * @param string $size 尺寸 self::PICTURE_SMALL（小图117px） self::PICTURE_MIDDLE (中图156px) self::PICTURE_NORMAL(544px)
     * @return string
     */
    public static function getUploadPictureUrl($driver_id, $city_id, $size = self::PICTURE_MIDDLE, $version = false){
        return Driver::getUploadPictureUrl($driver_id, $city_id, $size, $version);
    }
    public static function insertLog($id_card){
        return Driver::model()->insertLog($id_card);
    }
    //记录司机状态变化，生成流水
    public static function insertDriverStatusLog($inserArr){
        return Driver::model()->insertDriverStatusLog($inserArr);
    }
    /**
     * @author libaiyang    2013-05-07
     * @param string $userName
     */
    public static function getDriverByName($userName){
        return Driver::getDriverByName($userName);
    }
    /**
     * 判断司机是否已经签约(根据司机工号和imei)
     * @param $driver_id
     * @param $imei
     * @return bool
     */
    public static function checkDriverEntry($driver_id, $imei){
        return Driver::model()->checkDriverEntry($driver_id, $imei);
    }
    /**
     * 根据工号(多个)获取司机信息
     * @param $driver_id
     * @return mixed
     * author duke
     */
    public static function getDriverByIds($driver_ids){
        return Driver::model()->getDriverByIds($driver_ids);
    }
    /**
     * 根据城市获得未使用的最大的司机工号
     * @param $city_id
     * @return bool|string
     */
    public static function getNewDriverId($city_id){
        return Driver::model()->getNewDriverId($city_id);
    }
    public static function getDrivers($city_id, $mark){
        return Driver::model()->getDrivers($city_id, $mark);
    }
    /**
     * 通过mark获取司机工号
     * @param int $city_id
     * @param int $mark
     * @return array $result
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-06-08
     */
    public static function getDriverByMark($city_id = 0, $mark){
        return Driver::model()->getDriverByMark($city_id, $mark);
    }
    /**
     * 通过状态获取司机（需改成mongo...）
     * @param int $city_id
     * @param int $flag
     * @return array $result
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-06-08
     */
    public static function getDriverByStatus($city_id = 0, $flag = 0){
        return Driver::model()->getDriverByStatus($city_id, $flag);
    }
    /**
     * 获得某天的司机签约数
     * @param null $date 2013-01-01
     * @return int
     */
    public static function getDriverBydate($date = null, $city_id = 0){
        return Driver::model()->getDriverBydate($date, $city_id);
    }
    /***
     * 司机签约天数
     * @param $driver_id
     */
    public static function getEntryTime($driver_id){
        return Driver::model()->getEntryTime($driver_id);
    }
    /**
     * 司机扩展信息
     * @param $driver_id
     * @return mixed
     */
    public static function driverExtendData($driver_id) {
        return Driver::model()->driverExtendData($driver_id);
    }
    /**
     * 获得司机基本信息
     * @param $driver_id
     * @return mixed
     */
    public static function driverBasicData($driver_id){
        return Driver::model()->driverBasicData($driver_id);
    }
    public static function getNewDriverStatus($driver_id){
        return Driver::model()->getNewDriverStatus($driver_id);
    }
    /**
     * 获得工作状态中文
     * @param $status
     * @return string
     */
    public static function getStatusString($status) {
        return Driver::model()->getStatusString($status);
    }
    /**
     * 从数据库中获得司机工作状态
     * @param $driver_id
     * @return string
     */
    public static function getDbStatus($driver_id) {
        return Driver::model()->getDbStatus($driver_id);
    }
    /**
     * 从redis中获得司机工作状态
     * @param $driver_id
     * @return bool
     */
    public static function getRedisStatus($driver_id) {
        return Driver::model()->getRedisStatus($driver_id);
    }
    /**
     * 从mongo中获得司机工作状态
     * @param $driver_id
     * @return mixed
     */
    public static function getMongoStatus($driver_id) {
        return Driver::model()->getMongoStatus($driver_id);
    }
    /**
     * 获得司机订单信息 (订单数， 报单数， 补单数)
     * @param $driver_id
     * @return mixed
     */
    public static function getDriverOrderInfo($driver_id){
        return Driver::model()->getDriverOrderInfo($driver_id);
    }
    /**
     * 给据司机工号获取司机接单数和司机收入
     * @param $driver_id
     * @return int
     * author mengtianxue
     */
    public static function getDriverDeclaration($driver_id) {
        return Driver::model()->getDriverDeclaration($driver_id);
    }
    /**
     * 获取司机充值记录
     * @param $driver_id
     * @return int
     * author mengtianxue
     */
    public static function getDriverRecharge($driver_id){
        return Driver::model()->getDriverRecharge($driver_id);
    }
    /**
     * 根据is_test 获取司机工号
     * @param int $is_test
     * @return mixed
     * @auther mengtianxue
     */
    public static function getTestDriver($is_test = self::IS_TEST_TRUE){
        return Driver::model()->getTestDriver($is_test);
    }
    /**
     * 生成司机身份证图片地址
     * @param string $id_card 身份证号
     * @param string $city_id 城市
     * @param string $size 尺寸 self::PICTURE_SMALL（小图117px） self::PICTURE_MIDDLE (中图156px) self::PICTURE_NORMAL(544px)
     * @return string
     */
    public static function createIdCardPictureUrl($id_card, $city_id, $size = self::PICTURE_MIDDLE, $version = false) {
        return Driver::createIdCardPictureUrl($id_card, $city_id, $size , $version);
    }
    //$id 报名id
    public static function createPicPictureUrl($pic,$id,$id_card, $city_id, $size = self::PICTURE_MIDDLE, $version = false){
        return Driver::createPicPictureUrl($pic,$id,$id_card, $city_id, $size, $version);
    }
    public static function createIdCardPicName($id_card) {
        return Driver::createIdCardPicName($id_card);
    }
    public static function createDriverCardPicName($driver_card){
        return Driver::createDriverCardPicName($driver_card);
    }
    public static function createPicPicName($pic,$id){
        return Driver::createPicPicName($pic,$id);
    }
    /**
     * 生成司机驾驶证图片地址
     * @param string $id_card 身份证号
     * @param string $city_id 城市
     * @param string $size 尺寸 self::PICTURE_SMALL（小图117px） self::PICTURE_MIDDLE (中图156px) self::PICTURE_NORMAL(544px)
     * @return string
     */
    public static function createDriverCardPictureUrl($driver_card, $city_id, $size = self::PICTURE_MIDDLE, $version = false){
        return Driver::createDriverCardPictureUrl($driver_card, $city_id, $size, $version);
    }
    public static function getIdCardPic($driver_id, $size=self::PICTURE_MIDDLE) {
        return Driver::model()->getIdCardPic($driver_id, $size);
    }
    public static function getDriverCardPic($driver_id, $size=self::PICTURE_MIDDLE) {
        return Driver::model()->getDriverCardPic($driver_id, $size);
    }
    /**
     * 获取司机状态信息
     * @return mixed
     * author mengtianxue
     */
    public static function getDriverInfo($city_id = 0){
        return Driver::model()->getDriverInfo($city_id);
    }
    /**
     * 统计 通知司机签约数据
     * @param int $city_id
     * @return mixed
     */
    public static function getDriverInduction($city_id = 0){
        return Driver::model()->getDriverInduction($city_id);
    }
    /**
     *   解除司机所有屏蔽
     *
     */
    public static function unBlockDriver($driver_id){
        return Driver::model()->unBlockDriver($driver_id);
    }
    /**
     *   设置司管app
     *
     */
    public static function setManager($driver_id,$manager){
        return Driver::model()->setManager($driver_id,$manager);
    }
    /**
     *   获取司机城市
     *
     */
    public static function getDriveCityById($driver_id){
        return Driver::model()->getDriveCityById($driver_id);
    }
    /**
     * @param $driver_id
     * @return string
     */
    public static function getHeadUrl($driver_id){
        return Driver::model()->getHeadUrl($driver_id);
    }
    /**
     * @param $driver_id
     * @return string
     */
    public static function getCodeUrl($driver_id){
        return Driver::model()->getCodeUrl($driver_id);
    }

    //DriverBatch
	public static function batchUpdataStatus($data){
        return DriverBatch::model()->updataStatus($data);
    }
    public static function batchUpdataEntryCount($data){
        return DriverBatch::model()->updataEntryCount($data);
    }
	public static function batchUpdataEntrynum($data){
        return DriverBatch::model()->updataEntrynum($data);
    }

    //DriverCallLog
	public static function insertCallLog($record = array()){
        return DriverCallLog::model()->insertCallLog($record);
    }

    //DriverComplaint
	public static function saveDriverComplaint($data){
        return DriverComplaint::model()->saveDriverComplaint($data);
    }
}
