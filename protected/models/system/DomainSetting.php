<?php

Yii::import('application.models.schema.system.setting.SysDomainSetting');
Yii::import('application.models.system.DomainSettingRedis');

class DomainSetting extends SysDomainSetting {
    
    const ACTIVE_OK = 1;            //已激活
    const ACTIVE_NO = 0;            //未激活
    const USEABLE_OK = 1;           //符合使用要求
    const USEABLE_NO = 0;           //不符合使用要求
    const USEABLE_UNKNOW = -1;      //不知道是否符合使用要求

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public function beforeSave() {
        $time = time();
        if(parent::beforeSave()){
            if($this->isNewRecord){
                $this->create_time = $time;
            }
            $this->update_time = $time;
            return true;
        }
        return false;
    }
    
    public function afterSave() {
        parent::afterSave();
        DomainSettingRedis::model()->resetDataMd5();        //重置数据md5字符串
    }

}
