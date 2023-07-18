<?php

/**
 * @property integer $id
 * @property string $city_id
 * @property string $city_name
 */
class ChinaDistrictData extends CActiveRecord{

    CONST MEM_KEY_SUB_DISTRICTS = "mem_key_sub_districts_%s"; //parent_id

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{city_config}}';
    }

    public static function getSubDistricts($parent_id = 0, $ignore_cache = FALSE){
        if(!is_numeric($parent_id)){
            return FALSE;
        }
        $key = sprintf(self::MEM_KEY_SUB_DISTRICTS, $parent_id);
        if($ignore_cache == FALSE){
            $data =  unserialize(RedisHAProxy::model()->redis->get($key));
            if(!empty($data)){
                return $data;
            }
        }
        $sql = "SELECT id, name FROM t_china_district_data WHERE parent_id = {$parent_id}";
        $result = Yii::app()->db_readonly->createCommand($sql)->queryAll();
        foreach($result as $v){
            $data[$v['id']] = $v['name'];
        }
        RedisHAProxy::model()->redis->set($key, serialize($data));
        return $data;
    }
}
