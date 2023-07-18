<?php
/**
 * mysql从库监控配置
 * @author liuxiaobo
 * @since 2013-12-30
 */
class SlaveDb extends CRedis{
    public $host='redis01n.edaijia.cn'; //10.132.17.218
    public $port=6379;
    public $password='k74FkBwb7252FsbNk2M7';
    protected static $_models=array();

    const DB_SLAVE_KEY_PRE = 'db_slave_monitor';       //mysql从库监控配置key

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
     * 返回数据库列表
     * @return <array>
     * @author liuxiaobo
     * @since 2013-12-30
     */
    public function getDbs(){
        $dbs = array(
            'db01'=>'db01',
            'db03'=>'db03',
            'db04'=>'db04',
            'db05'=>'db05',
            'db06'=>'db06',
            'db07'=>'db07',
            'db08'=>'db08',
            'backup'=>'backup',
            'dbstat01'=>'dbstat01',
        );
        return $dbs;
    }
    
    /**
     * 获取已监控的数据库
     * @return <array>
     * @author liuxiaobo
     * @since 2013-12-30
     */
    public function getDbCache(){
        $key = self::DB_SLAVE_KEY_PRE;
        $val = $this->redis->sMembers($key);
        return $val;
    }
    
    /**
     * 设置监控的数据库
     * @param <array> $dbs  数据库
     * @return <boolean>
     * @author liuxiaobo
     * @since 2013-12-30
     */
    public function setDbCache($dbs=array()){
        $key = self::DB_SLAVE_KEY_PRE;
        $this->redis->delete($key);
        $result = TRUE;
        foreach ($dbs as $db){
            $saveOk = $this->redis->sAdd($key, $db);
        }
        return $result;
    }
}