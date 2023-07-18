<?php
/**
 * 司机位置(Mongo从库,只读)
 * 2015-01-27
 */
class SearchDriverGPS {
    public $host='mongodb://mongo01.edaijia-inc.cn';
    public $port=27017;
    public $password='k74FkBwb7252FsbNk2M7';
    protected static $_models=array();
    private $_mongo;
    private $_coll;
    private $options=array(
                'safe'=>true,
                'upsert'=>true
            );

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
        $this->_coll=$this->_mongo->driver->location;
    }

    public function __destruct() {
        if ($this->_mongo) {
            $this->_mongo->close();
        }
    }

    public function get_all_drivers() {
        return $this->_coll->find();
    }
}
