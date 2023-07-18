<?php
/**
 * User: zhanglimin
 * Date: 13-7-9
 * Time: 下午2:05
 */
class AddressCallHistory{

    public $host='mongodb://mongo.edaijia.cn';
    public $port=27017;
    private $_mongo;
    private $_coll;
    private $options=array(
        'safe'=>true,
        'upsert'=>true
    );
    protected static $_models=array();

    public static function model($className=__CLASS__) {
        $model=null;
        if (isset(self::$_models[$className]))
            $model=self::$_models[$className];
        else {
            $model=self::$_models[$className]=new $className(null);
        }
        return $model;
    }

    public function __construct() {
        $this->_mongo=new Mongo($this->host.':'.$this->port);
        $this->_coll=$this->_mongo->address_pool->location;
    }

    public function __destruct() {
        if ($this->_mongo) {
            $this->_mongo->close();
        }
    }


    /**
     * 初始化mongo库
     */
    public function init() {
        $this->_mongo->dropDB('address_pool');
        $offset=0;
        $pagesize=100;
        echo "starting...\n";
        while(true) {
            $criteria=new CDbCriteria(array(
                'select'=>'id,hashkey,city_id,address,lng,lat,times',
                'offset'=>$offset,
                'limit'=>$pagesize
            ));
            $address_poll=AddressPool::model()->findAll($criteria);
            if ($address_poll) {
                foreach($address_poll as $address) {
                    if (!empty($address)) {
                        $position=array(
                            '_id'=>intval($address['id']),
                            'hashkey'=>$address['hashkey'],
                            'address'=>$address['address'],
                            'city_id'=>$address['city_id'],
                            'times'=>intval($address['times']),
                            'location'=>array(
                                'lng'=>doubleval($address['lng']),
                                'lat'=>doubleval($address['lat'])
                            ),
                            'update'=>date(Yii::app()->params['formatDateTime'], time())
                        );
                        $rs=$this->_coll->update(array(
                            '_id'=>intval($address['id'])
                        ), $position, $this->options);
                    }
                }
            } else {
                break;
            }
            $offset+=$pagesize;
            echo $offset."\n";
        }
        echo "end...\n";

        $this->_coll->ensureIndex(array(
            'location'=>'2d','times'=>1
        ));

        $this->_coll->ensureIndex(array(
            'times'=>1
        ));
    }


    /**
     * 查找历史地址
     * @param $lnt
     * @param $lat
     * @param $address
     * @param $city_name
     * @return array
     */
    public function nearby($lng , $lat ,$gps_type , $address ,$city_name = ""){
        $result = array('city_name'=>'','address_list'=>array());
        if(empty($city_name)){
            $city = $this->_getCity($lng , $lat , $gps_type);
            
        }else{
            $city = $city_name;
        }
        $result['city_name'] = $city;
        
        $gps_baidu = Helper::getBaiduGPSByAddress($city , $address);
        if(empty($gps_baidu)){
            return $result;
        }
        $key = $city."_".$address;
        $get_cache_result = GPS::model()->addressHistory($key);
        
        //利民，这里原来变量名写错了。add by sunhongjing 2013-11-18
        if(!empty($get_cache_result)){
            $result['address_list'] = empty($get_cache_result['list']) ? array() : $get_cache_result['list'];
        }else{
            $ret = $this->findHistory($gps_baidu['location']['lng'],$gps_baidu['location']['lat']);
            $result['address_list'] = $ret;

            //往缓存写数据
            $data = array(
              'lng' => $gps_baidu['location']['lng'],
              'lat' => $gps_baidu['location']['lat'],
              'list' => $ret,
            );
            GPS::model()->addressHistory($key,$data);
        }
        return $result;
    }


    /**
     * 获取当前城市名
     * @param $lnt
     * @param $lat
     * @param $gps_type
     * @return mixed
     */
    private function _getCity($lng,$lat,$gps_type){
        //通过gps反推城市
        $gps_location = array(
            'longitude' => $lng,
            'latitude' => $lat,
        );
        $gps = GPS::model()->convert($gps_location , $gps_type);
        $city = GPS::model()->getCityByBaiduGPS($gps['baidu_lng'] , $gps['baidu_lat']);
        return $city;
    }

    /**
     * 查找己往的记录
     * @param $lng
     * @param $lat
     * @param int $limit
     * @param int $max_distance
     * @return array
     */
    public  function findHistory($lng,$lat,$limit = 20,$max_distance = 3000){
        //取max_distance的两倍远作为mongod的maxDistance
        $mongo_max_distance=isset(Yii::app()->params['EarthRound'])?$max_distance*2*360.0/Yii::app()->params['EarthRound']:10.0;
        $near_condition=array(
            'location'=>array(
                '$near'=>array(
                    doubleval($lng),
                    doubleval($lat)
                ),
               '$maxDistance'=>doubleval($mongo_max_distance)
            ),
        );
        //多取一倍的数据
        $historys=$this->_coll->find($near_condition)->limit($limit*2);
        $near_historys=array();

        foreach($historys as $item) {
            $history_lng=$item['location']['lng'];
            $history_lat=$item['location']['lat'];
            $_distance=Helper::Distance($lat, $lng, $history_lat, $history_lng);
            if ($_distance<=$max_distance) {
                $near_historys[]=array(
                    'id'=>$item['_id'],
                    'address'=>$item['address'],
                    'lng'=>$item['location']['lng'],
                    'lat'=>$item['location']['lat'],
                    'times'=>$item['times'],
                    'distance'=>$_distance
                );
            }
        }

        //排序
        $near_historys = Common::array_sort($near_historys,'distance','asc',true);

        $near_historys=array_slice($near_historys, 0, $limit);

        return $near_historys;
    }



}
