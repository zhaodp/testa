<?php

/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 14-4-17
 * Time: 下午2:41
 * auther mengtianxue
 */
class RCityList extends RedisHAProxy
{
    protected static $_models = array();

    //城市数据
    private $city = 'City_';
    private $city_prifix = 'City_prifix_';
    private $OpenCityList = 'OpenCityList';
    private $OpenCityAll = 'OpenCityAll';
    private $CloseCityAll = 'CloseCityAll';
    private $CityFeeEq = 'CityFeeEq';
    private $capitalCity = 'capitalCity';
    private $DriverCityLt = 'DriverCityLt';
    private $cityListByCat = 'cityListByCat_';

    private $fee = 'fee_';

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

    public function deleteAll()
    {
        $keys = array(
            $this->city,
            $this->city_prifix,
            $this->OpenCityList,
            $this->OpenCityAll,
            $this->CloseCityAll,
            $this->CityFeeEq,
            $this->DriverCityLt,
            //$this->fee,
            //$this->cityListByCat,
        );

        foreach ($keys as $k => $v) {
            $cache_keys = $this->redis->keys($v . '*');
            if ($cache_keys) {
                foreach ($cache_keys as $item) {
                    $this->redis->del($item);
                }
            }
        }
    }

    /**
     * 装载城市信息
     */
    public function load()
    {
        //$this->deleteAll();
        $this->loadCityFeeEq();
        $this->loadCity();
        $this->loadOpenCityList();
        $this->loadOpenCityAll();
        $this->loadCloseCityAll();
        $this->loadDriverCityLt();
        //$this->loadDriverCityLt100();
    }

    /**
     * load 上线城市费用
     * @param $fee_id
     * @return array
     * @auther mengtianxue
     */
    public function loadFee()
    {
        $city = self::getAllCity();
        $fee_list = array();

        if ($city) {
            foreach ($city as $item) {

                $city_id = (int)$item['city_id'];
                $fee_list[$city_id] = Common::fee($item['fee_id']);
            }
        }
        return $fee_list;
    }



    /**
     * load 上线城市费用(费用分类为key)
     * @param $fee_id
     * @return array
     * @auther mengtianxue
     */
    public function loadFeeGroupbyFid()
    {
        $city = self::getAllCity();

        $fee_list = array();

        if ($city) {
            foreach ($city as $item) {
                $city_id = (int)$item['city_id'];
                $fee_list['citys'][$item['fee_id']][$city_id] = $item['city_name'];
                if(!isset($fee_list['fees'][$item['fee_id']])){
                    $fee_list['fees'][$item['fee_id']] = Common::fee($item['fee_id']);
                }

            }
        }
        //print_r($fee_list);die;
        return $fee_list;
    }

    public function getcityGroupByLevel(){
        $city = self::getAllCity();
        //print_r($city);
        $list = array();
        foreach($city as $v){
            $list[$v['city_level']][$v['city_id']] = $v['city_id'];
        }
        return $list;
    }


    /**
     * 获取城市收费标准
     * @param $city_id
     * @param $isDayTime 是否是日间业务
     * @param $haveDefault 是否返回未开通的默认价格
     * @return array
     * @auther mengtianxue
     * @updater duke
     */
    public function getFeeForGD($city_id,$isDayTime = false, $haveDefault = false)
    {
        $fee_id = $this->getCityByID($city_id, 'fee_id');
        if($fee_id){
            $res = $isDayTime ? Common::feeDayTimeOld($fee_id) : Common::feeback($fee_id);

            if($res){
                // if(in_array($city_id,array(4))){ //新日间业务价格表 客户端先上线杭州
                //     $res = Common::feeDayTime($fee_id);
                // }
                return $res;
            }
            return false;
        }
        else if($haveDefault){
            $res = $isDayTime ? Common::feeDayTimeOld('wx_single') : Common::fee('wx_single');
            return $res;
        }
        return false;
    }

    /**
     * 获取城市收费标准
     * @param $city_id
     * @param $isDayTime 是否是日间业务
     * @param $haveDefault 是否返回未开通的默认价格
     * @return array
     * @auther mengtianxue
     * @updater duke
     */
    public function getFee($city_id,$isDayTime = false, $haveDefault = false)
    {
        $fee_id = $this->getCityByID($city_id, 'fee_id');
        if($fee_id){
            $res = $isDayTime ? Common::feeDayTimeOld($fee_id) : Common::fee($fee_id);

            if($res){
                if(RCityList::model()->isOpenDayTime($city_id)){ //新日间业务价格表 客户端先上线杭州 徐州
                    $res = Common::feeDayTime($fee_id);
                }
                return $res;
            }
            return false;
        }
        else if($haveDefault){
            $res = $isDayTime ? Common::feeDayTimeOld('wx_single') : Common::fee('wx_single');
            return $res;
        }
        return false;
    }


    /**
     * 获取城市收费标准
     * @param $city_id
     * @param $isDayTime 是否是日间业务
     * @param $haveDefault 是否返回未开通的默认价格
     * @return array
     * @auther mengtianxue
     * @updater duke
     */
    public function getFeeOldVersion($city_id,$isDayTime = false, $haveDefault = false)
    {
        $fee_id = $this->getCityByID($city_id, 'fee_id');
        if($fee_id){
            $res = $isDayTime ? Common::feeDayTimeOld($fee_id) : Common::fee($fee_id);
            if($res){
                return $res;
            }
            return false;
        }
        else if($haveDefault){
            $res = $isDayTime ? Common::feeDayTimeOld('wx_single') : Common::fee('wx_single');
            return $res;
        }
        return false;
    }

    public function getFeeDriverClient($city_id,$isDayTime = false, $haveDefault = false){
        $res = $this->getFee($city_id, $isDayTime , $haveDefault);
        foreach($res['part_price'] as $k => $v){
            if($k == 0){
                if($v['end_time'] == '06:59'){
                    $time_range[] = array('part'=>'0-0','price'=>$v['price']);
                }
                else{
                    $time_range[] = array('part'=>'7-'.((int)substr($v['end_time'],0,-3)+1),'price'=>$v['price']);
                }
            }else{
                $time_range[] = array('part'=>(int)substr($v['start_time'],0,-3).'-'.((int)substr($v['end_time'],0,-3) + 1 ),'price'=>$v['price']);
            }

        }
        $res['part_price'] = $time_range;
        return $res;
    }

    /**
     * 日间业务价格表
     * @param $city_id
     * @param string $app_ver
     * @return string
     */
    public  function getDaytimePrice($city_id , $app_ver = '', $is_client = false){
        $day_time_data= '';
        $city_info = $this->getCityById($city_id);
        //日间业务
        $day_time_type = $city_info['daytime_price'];
        if(!$is_client){
            $check_appver = Helper::compareVersion($app_ver, '2.5.3');
            if($check_appver){
                if($day_time_type  && isset(Yii::app()->params['daytime_price_new'][$day_time_type])){
                    $day_time_data = Yii::app()->params['daytime_price_new'][$day_time_type];
                }
                else{ ////应司机端要求，没开通日间业务返回默认北京的价格表。duke
                    $day_time_data =  Yii::app()->params['daytime_price_new'][1] ;
                }
            }else {
                if($day_time_type  && isset(Yii::app()->params['daytime_price'][$day_time_type])){
                    $day_time_data = Yii::app()->params['daytime_price'][$day_time_type];
                }
                else{ //应司机端要求，没开通日间业务返回默认北京的价格表。duke
                    $day_time_data =  Yii::app()->params['daytime_price'][1] ;
                }
            }
        }
        else {
            if($day_time_type  && isset(Yii::app()->params['daytime_price_client'][$day_time_type])){
                $day_time_data = Yii::app()->params['daytime_price_client'][$day_time_type];
            }
            else { //应司机端要求，没开通日间业务返回默认北京的价格表。duke
                $day_time_data =  false ;
            }
        }
        return $day_time_data;
    }


    /**
     * 日间业务价格表 for old client
     * @param $city_id
     * @param string $app_ver
     * @return string
     */
    public  function getDaytimePriceOld($city_id ){
        $day_time_data= '';
        $city_info = $this->getCityById($city_id);
        //日间业务
        $day_time_type = $city_info['daytime_price'];

        if($day_time_type  && isset(Yii::app()->params['daytime_price_old'][$day_time_type])){
            $day_time_data = Yii::app()->params['daytime_price_old'][$day_time_type];
        }
        else{ //应司机端要求，没开通日间业务返回默认北京的价格表。duke
            $day_time_data =  false ;
        }

        return $day_time_data;
    }

    /**
     * 通过城市id 查看该城市是否开通日间单
     * @param $city_id
     * @return array
     */
    public  function getDayOrderCity($city_id,$app_ver){
        $config = array(
            'open'=>0,
            'start_time'=>'7:00',
            'end_time'=>'19:00',
            'price'=>'20'
        );

        $city_open = $this->getDaytimePrice($city_id, $app_ver,true);
        if(!empty($city_open)){
            $config['open']=1; //设置为开通
            $start_time = $city_open['start_time'];
            $end_time = $city_open['end_time'];
            $price = $city_open['price'];
            $config['start_time']=$start_time;
            $config['end_time']=$end_time;
            $config['price']=$price;

        }
        $open=$this->isOpenDayTime($city_id);
        if(!$open){ //下线城市或新开通城市，用另外的轮播图
            $config['open']=1;
            $config['start_time']='00:00';
            $config['end_time']='00:01';
        }
        return $config;
    }

    /**
     * 判断一个城市是否开通日间业务
     */
    public function isOpenDayTime($city_id){
        $open = false;
        $cityInfo = $this->getCityByID($city_id);
        if($cityInfo && $cityInfo['status']=1 && $cityInfo['daytime_price']){
            $open = true;
        }
        //C类城市和city_id=177的日间业务下线
        // $cityType=RCityList::model()->getOpenCityByID($city_id,'city_level');
        // $cityType= $cityType ? trim(substr($cityType, 0,1)) : '';
        // if($cityType != 'C' && $city_id!=177){
        //     $open = true;
        // }

        return $open;
    }

    /**
     * 司机划款最低金额城市
     * @auther mengtianxue
     */
    public function loadDriverCityLt($money = '')
    {
        $res = '';
        $city = $this->driverCityLtMoneyNew();
        if(!empty($city)){
            foreach ($city as $limit_money => $city_arr){
                $this->redis->hset($this->DriverCityLt,$limit_money,json_encode($city_arr));
                if($money && ($limit_money == $money)){
                    $res = json_encode($city_arr);
                }
            }
            return  $res;
        }
        //$this->redis->set($this->DriverCityLt100, json_encode($city));
    }



    public function driverCityLtMoneyNew()
    {
        $city = CityConfig::model()->getAllOnlineCity(); //查询所有上线的城市
        $cityList = array();
        if ($city) {
            foreach ($city as $item) {
                if($item->screen_money != 0 ){
                    $city_id = $item->city_id;
                    $cityList[$item->screen_money][$city_id] = $city_id;
                }
            }
        }
        return $cityList;
    }

    /**
     * 新课返现19元
     * @auther mengtianxue
     */
    public function loadCityFeeEq($money = 19)
    {
        $city = CityConfig::model()->getAllOnlineCity(); //查询所有上线的城市

        $current_backmoney = true;
        if ($city) {
            $CityList = array();
            foreach ($city as $item) {
                //$city_id = $item->city_id;
                $CityList[$item->bonus_back_money][$item->city_id] = $item->city_id;
            }

            foreach($CityList as $back_money => $cityid){
                $res = $this->redis->hSet($this->CityFeeEq, $back_money, json_encode($cityid));
                if($back_money == $money) {
                    $current_backmoney = json_encode($cityid);
                }
            }
            return $current_backmoney;
        }
        return false;
    }

    /**
     * 加载开通城市
     * @auther mengtianxue
     */
    public function loadOpenCityList()
    {
        $city = CityConfig::model()->getAllOnlineCity(); //查询所有上线的城市
        //print_r($city);die;
        if ($city) {

            $OpenCityList = array();
            foreach ($city as $item) {
                $city_id = (int)$item->city_id;
                $OpenCityList[$city_id] = $item->city_name;
            }

            $this->redis->set($this->OpenCityList, json_encode($OpenCityList));
        }
    }

    /**
     * 加载开通城市 的全部详细信息
     * @auther mengtianxue
     */
    public function loadOpenCityAll()
    {
        $city = CityConfig::model()->getAllOnlineCity(); //查询所有上线的城市
        $capital_city = array();
        if ($city) {

            $OpenCityAll = array();
            foreach ($city as $item) {
                $city_id = (int)$item->city_id;
                $OpenCityAll[$city_id] = $item->attributes;
                $item->captital && $capital_city[] = $city_id;
            }

            $this->redis->set($this->OpenCityAll, json_encode($OpenCityAll));
            $this->redis->set($this->capitalCity,json_encode($capital_city)); //省会城市 city_id
        }
    }
    /**
     * 加载未开通城市 的全部详细信息
     * @auther yuchao 
     */
    public function loadCloseCityAll()
    {
        $city = CityConfig::model()->getAllOfflineCity(); //查询所有未上线的城市
        $capital_city = array();
        if ($city) {

            $CloseCityAll = array();
            foreach ($city as $item) {
                $city_id = (int)$item->city_id;
                $CloseCityAll[$city_id] = $item->attributes;
                $item->captital && $capital_city[] = $city_id;
            }

            $this->redis->set($this->CloseCityAll, json_encode($CloseCityAll));
            //$this->redis->set($this->capitalCity,json_encode($capital_city)); //省会城市 city_id
        }
    }
    /**
     * 从redis获取未开通城市id列表
     * @author yuchao
     */
    public function getAllCloseCity(){
        $res = $this->redis->get($this->CloseCityAll);
        if($res){
            return json_decode($res,1);
        }else{
            $this->loadCloseCityAll();
            $res = $this->redis->get($this->CloseCityAll);
            if($res) return json_decode($res,1);
            else return false;
        }
    }

    public function getCapitalCity(){
        $res = $this->redis->get($this->capitalCity);
        if($res){
            return json_decode($res,1);
        }
        else{
            $this->loadOpenCityAll();
            $res = $this->redis->get($this->capitalCity);
            if($res) return json_decode($res,1);
            else return false;
        }
    }


    /*
        * 获取全部开通城市的信息
             */

    public function getAllCity(){
        if (!$this->redis->exists($this->OpenCityAll)) {
            $this->loadOpenCityAll();
        }
        $open_city_all = $this->redis->get($this->OpenCityAll);
        return json_decode($open_city_all,1);
    }

    /**
     * 通过城市名字获取city info
     * @auther duke
     */
    public function getOpenCityByName($city_name)
    {
        $open_city_list = self::getOpenCityList();
        $new_list = array_flip($open_city_list);
        if(isset($new_list[$city_name])) {
            $res = self::getCityByID($new_list[$city_name]);
            return $res;
        }
        return array();
    }



    /**
     * load 所有城市信息
     * @auther mengtianxue
     */
    public function loadCity()
    {
        $city = CityConfig::model()->findAll();
        if ($city) {
            foreach ($city as $item) {
                $this->redis->hMset($this->city . $item->city_id, $item->attributes);
                //$city_prefix = strtoupper($item->city_prifix);
                //$this->redis->hMset($this->city_prifix . $city_prefix, $item->attributes);\
                $this->redis->hMset($this->city_prifix . $item->city_prifix, $item->attributes);
            }


        }
    }

    /**
     * 优惠劵返现19元的城市
     * @return mixed
     * @auther mengtianxue
     */
    public function getCityFeeEq( $money = 19)
    {
        $city_feeEq =  $this->redis->HGet($this->CityFeeEq,$money);

        if(!$city_feeEq){
            $city_feeEq =  $this->loadCityFeeEq($money);
        }

        return json_decode($city_feeEq,1);
    }

    /**
     * 获取开通城市列表
     * $exclude_default  是否排出默认城市：未知
     * @return mixed
     * @auther mengtianxue
     */
    public function getOpenCityList($exclude_default = false)
    {
        if (!$this->redis->exists($this->OpenCityList)) {
            $this->loadOpenCityList();
        }
        $open_city_list = $this->redis->get($this->OpenCityList);
        $open_city_list = json_decode($open_city_list,1);
        if($exclude_default){
            $exclude_city_id = Dict::code('city','未开通');
            if($exclude_city_id) unset($open_city_list[$exclude_city_id]);
        }
        return $open_city_list;
    }

    /**
     * 获取城市信息
     * @param $city_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getCityByID($city_id, $fieldname = '')
    {
        $open_city = $this->getOpenCityList();
        if(!isset($open_city[$city_id])) $city_id = Dict::code('city','未开通');

        if (!$this->redis->exists($this->city . $city_id)) {
            $this->loadCity($city_id);
        }
        $city = $this->redis->hGetall($this->city . $city_id);
        if($city){
            if (empty($fieldname)) {
                return $city;
            } else {
                return $city[$fieldname];
            }
        }else return false;
    }


    /**
     * 获取城市信息
     * @param $city_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getOpenCityByID($city_id, $fieldname = '')
    {
        $open_city = $this->getOpenCityList();
        if(!isset($open_city[$city_id])) return false;

        if (!$this->redis->exists($this->city . $city_id)) {
            $this->loadCity($city_id);
        }
        $city = $this->redis->hGetall($this->city . $city_id);
        if($city){
            if (empty($fieldname)) {
                return $city;
            } else {
                return $city[$fieldname];
            }
        }else return false;
    }


    /**
     * 根据前缀获取城市信息
     * @param $city_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getCityByPrifix($city_prifix, $filename = '')
    {
        //$city_prifix = strtoupper($city_prifix);
        if (!$this->redis->exists($this->city_prifix . $city_prifix)) {
            //echo 'aaaaaa';die;
            $this->loadCity();
        }

        $city = $this->redis->hGetall($this->city_prifix . $city_prifix);
        if($city){
            if (empty($filename)) {
                return $city;
            } else {
                return $city[$filename];
            }
        }else return false;
    }


    /**
     * 小于 $limit_money 划款城市名单
     * @return mixed
     * @auther mengtianxue
     */
    public function getDriverCityLt($limit_money)
    {
        $driver_cityLt = $this->redis->hget($this->DriverCityLt,$limit_money);

        if (!$driver_cityLt) {
            $driver_cityLt = $this->loadDriverCityLt($limit_money);
        }
        return json_decode($driver_cityLt,1);
    }
}
