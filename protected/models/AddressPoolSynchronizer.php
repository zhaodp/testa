<?php

Class AddressPoolSynchronizer
{
    static private $index = 'address_pool_index';
    static private $type = 'address_pool';

    static public function add($param)
    {
        if (empty($param) || !isset($param['hashkey'])) {
            return false;
        }

        $sql = "select city_id, address, lng, lat from t_address_pool where hashkey=:hashkey";  
        $doc = Yii::app()->db->CreateCommand($sql)->queryRow(true,array('hashkey'=>$param['hashkey']));

        if (empty($doc)) {
            EdjLog::error('cannot find address with hashkey: '.$param['hashkey']);
            return false;
        }

        return ElasticsearchSynchronizer::addDocument(self::$index, self::$type, $param['hashkey'], $doc);
    }

    static public function delete($param)
    {
        if (empty($param) || !isset($param['hashkey'])) {
            return false;
        }

        return ElasticsearchSynchronizer::deleteDocument(self::$index, self::$type, $param['hashkey']);
    }

    static public function update($param)
    {
        if (empty($param) || !isset($param['hashkey'])) {
            return false;
        }

        $sql = "select city_id, address, lng, lat from t_address_pool where hashkey=:hashkey";  
        $doc = Yii::app()->db->CreateCommand($sql)->queryRow(true,array('hashkey'=>$param['hashkey']));

        if (empty($doc)) {
            EdjLog::error('cannot find address with hashkey: '.$param['hashkey']);
            return false;
        }

        return ElasticsearchSynchronizer::updateDocument(self::$index, self::$type, $param['hashkey'], $doc);
    }
}
