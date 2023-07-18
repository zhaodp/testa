<?php

Yii::import('application.models.schema.system.DomainSetting');

class DomainSettingRedis extends CRedis{
    public $host='redis01n.edaijia.cn'; //10.132.17.218
    public $port=6379;
    public $password='k74FkBwb7252FsbNk2M7';
    protected static $_models=array();

    const SHOST_MD5_STR = 'shost';       //mysql从库监控配置key

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
     * 重置md5处理（修改数据库后调用此方法）
     * @return <string>
     */
    public function resetDataMd5(){
        $md5Str = $this->buildDataMd5();
        return $this->redis->set(self::SHOST_MD5_STR, $md5Str);
    }
    
    /**
     * md5处理
     * @return <string>
     */
    public function buildDataMd5(){
        $dataArr = array();
        $models = DomainSetting::model()->findAllByAttributes(array('active'=>DomainSetting::ACTIVE_OK, 'useable'=>DomainSetting::USEABLE_OK));
        if(is_array($models)){
            foreach($models as $model){
                $name = $model->name;
                $ip = $model->ip;
                $dataArr[] = $ip."\t".$name;
            }
        }
        $dataStr = implode("\n", $dataArr);
        return md5($dataStr);
    }

}
