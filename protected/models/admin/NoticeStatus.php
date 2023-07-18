<?php
/**
 * 公告缓存管理
 * User: zhanglimin
 * Date: 13-8-28
 * Time: 上午11:32
 */
class NoticeStatus extends CRedis{

    public $host='redis01n.edaijia.cn'; //10.132.17.218
    public $port=6379;
    public $password='k74FkBwb7252FsbNk2M7';
    protected static $_models=array();
    private   $notice_all_key = "NOTICE_ALL_";
    private   $notice_driver_read_key = "NOTICE_DRIVER_READ_";


    public static function model($className=__CLASS__) {
        $model=null;
        if (isset(self::$_models[$className]))
            $model=self::$_models[$className];
        else {
            $model=self::$_models[$className]=new $className(null);
        }
        return $model;
    }


    /**
     * 添加公告己城市做KEY 并把id做值 存放到缓存中
     * @param array $params
     * @return bool
     */
    public function add($params = array()){
        $city_ids = array();
        if(!isset($params['city_ids'])){
            return false;
        }
        if(!isset($params['notice_id'])){
            return false;
        }
        $city_ids =  explode(",",$params['city_ids']);
        if(empty($city_ids)){
            return false;
        }

        $cityPrefix= Dict::items('city_prefix');
        foreach($city_ids as $city_id){
            if(!empty($city_id) && isset($cityPrefix[$city_id])){
              $this->redis->sAdd($this->notice_all_key.strtoupper($cityPrefix[$city_id]),intval($params['notice_id']));
            }
        }
        return true;
    }


    /**
     * 修改公告缓存 先将原来的值删除，在插入
     * @param array $params
     * @return bool
     */
    public function update($params = array()){
        $old_city_ids  = $new_city_ids = array();
        if(!isset($params['old_city_ids'])){
            return false;
        }
        if(!isset($params['new_city_ids'])){
            return false;
        }
        if(!isset($params['notice_id'])){
            return false;
        }
        $old_city_ids =  explode(",",$params['old_city_ids']);
        if(empty($old_city_ids)){
            return false;
        }

        $new_city_ids =  explode(",",$params['new_city_ids']);
        if(empty($new_city_ids)){
            return false;
        }

        $cityPrefix= Dict::items('city_prefix');
        foreach($old_city_ids as $city_id){
            if(!empty($city_id) && isset($cityPrefix[$city_id])){
                $this->redis->sRem($this->notice_all_key.strtoupper($cityPrefix[$city_id]),intval($params['notice_id']));
            }
        }
        $data = array(
            'city_ids' => $params['new_city_ids'],
            'notice_id' => $params['notice_id'],
        );

        $this->add($data);
        return true;
    }

    /**
     * 删除公告缓存
     * @param array $params
     * @return bool
     */
    public function del($params = array()){
        $city_ids = array();
        if(!isset($params['city_ids'])){
            return false;
        }
        if(!isset($params['notice_id'])){
            return false;
        }
        $city_ids =  explode(",",$params['city_ids']);
        if(empty($city_ids)){
            return false;
        }
        $cityPrefix= Dict::items('city_prefix');
        foreach($city_ids as $city_id){
            if(!empty($city_id) && isset($cityPrefix[$city_id])){
                $this->redis->sRem($this->notice_all_key.strtoupper($cityPrefix[$city_id]),intval($params['notice_id']));
            }
        }
        return true;
    }

    /**
     * 设置司机己读公告
     */
    public function setDriverReadNotice($params = array()){

        if(!isset($params['driver_id'])){
            return false;
        }

        if(!isset($params['notice_id'])){
            return false;
        }

        $notice_id = intval($params['notice_id']);

        $this->redis->sAdd($this->notice_driver_read_key.strtoupper($params['driver_id']),$notice_id);

        return true;

    }


    /**
     * 获取当前司机公告id
     * @param array $params
     * @return array|bool
     */
    public function getDriverNoticeIds($params = array()){
        if(!isset($params['driver_id'])){
            return false;
        }

        $flag = isset($params['flag']) && $params['flag']==1 ? 1 : 0; //0 未读 1 己读

        $ids = array();

        if($flag == 0 ){
            //未读
            $cityPrefix= Dict::items('city_prefix');
            if(isset($params['city_id']) && isset($cityPrefix[$params['city_id']])){
                $all_notice_ids = $this->notice_all_key.strtoupper($cityPrefix[$params['city_id']]);
                $dirver_read_notice_ids = $this->notice_driver_read_key.strtoupper($params['driver_id']);
                $ids=$this->redis->sDiff($all_notice_ids,$dirver_read_notice_ids);
            }
        }elseif($flag == 1){
            //己读
            $dirver_read_notice_ids = $this->notice_driver_read_key.strtoupper($params['driver_id']);
            $ids = $this->redis->sMembers($dirver_read_notice_ids);
        }

        return $ids;
    }

    /**
     * 消除司机己读缓存
     * @param array $params
     */
    public function delDriverReadNoticeCache($params = array()){

        if(!isset($params['notice_id'])){
            return false;
        }

        $city_ids =  explode(",",$params['city_ids']);

        if(empty($city_ids)){
            return false;
        }


        $driver_id = isset($params['driver_id']) ? trim($params['driver_id']) : "";
        $notice_id = $params['notice_id'];

        if(empty($driver_id)){

            $cityPrefix= Dict::items('city_prefix');
            foreach($city_ids as $city_id){
                if(!empty($city_id) && isset($cityPrefix[$city_id])){
                    //获取该城市所有司机，如果公告缓存中存在则清除
                    $max=0;
                    while (true) {
                        $sql = "SELECT id,user FROM t_driver WHERE city_id=:city_id and id>:max and mark != 3 LIMIT 1000";
                        $command = Yii::app()->db_readonly->createCommand($sql);
                        $command->bindParam(":max", $max);
                        $command->bindParam(":city_id", $city_id);
                        $driver_list = $command->queryAll();
                        if ($driver_list) {
                            foreach ($driver_list as $driver) {
                                $max = $driver['id'];
                                $driver_id = $driver['user'];
                                //判断该公告是否在司机已读公告中
                                $key=$this->notice_driver_read_key.strtoupper($driver_id);
                                $res = $this->redis->sIsMember($key,$notice_id);
                                if($res){
                                    $this->redis->sRem($key,intval($notice_id));
                                }
                                echo '移除司机driver='.$driver_id.'已读公告notice='.$notice_id.' ok'.PHP_EOL;
                                EdjLog::info('移除司机driver='.$driver_id.'已读公告notice='.$notice_id.' ok');
                            }
                        }else{
                            break;
                        }
                    }
                }
            }
        }else{

            //只针对于当前司机操作
            $driver_key = $this->notice_driver_read_key.strtoupper($driver_id);
            if($this->redis->exists($driver_key)){
               $this->redis->sRem($driver_key,intval($params['notice_id']));
            }

        }
    }


    /**
     * 清除所有己过期公告缓存
     */
    public function delDeadlineCache($params = array()){

        if(!isset($params['notice_id'])){
            return false;
        }
        $begin = microtime(TRUE);
        //清除司机己读缓存
        $this->delDriverReadNoticeCache($params);
        $end = microtime(TRUE);
        $time=($end-$begin)*1000;
        echo '清除司机己读缓存,公告id='.$params['notice_id'].'耗费:'.$time.'ms'.PHP_EOL;
        EdjLog::info('清除司机己读缓存,公告id='.$params['notice_id'].'耗费:'.$time.'ms');

        $begin = microtime(TRUE);
        //清除公告缓存
        $this->del($params);
        $end = microtime(TRUE);
        $time=($end-$begin)*1000;
        echo '清除公告缓存,公告id='.$params['notice_id'].'耗费:'.$time.'ms'.PHP_EOL;
        EdjLog::info('清除公告缓存,公告id='.$params['notice_id'].'耗费:'.$time.'ms');

        return true;
    }

}