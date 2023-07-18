<?php
/**
 * vip跟进记录
 */
Yii::import('application.models.schema.customer.CarVipRecord');
Yii::import('application.models.customer.VipRecordRedis');

class VipRecord extends CarVipRecord {

    public static $_lastRecordModels = array();

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function beforeSave() {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->create_time = time();
                $this->operator_id = Yii::app()->user->id;
            }
            return TRUE;
        }
        return FALSE;
    }

    public function afterSave() {
        parent::afterSave();
        VipRecordRedis::model()->addLastRecordToCache($this->vip_id);       //将本周跟进过的vipid放入缓存集合中
    }

    /**
     * 获取最后一次的跟进记录
     * @param <string> $vipId
     * @return <mixed>  false 或 model
     */
    public function getLastRecord($vipId = 0) {
        if (isset(self::$_lastRecordModels[$vipId])) {
            return self::$_lastRecordModels[$vipId];
        }
        $model = self::model()->findByAttributes(array('vip_id' => $vipId), array('order' => 'id DESC'));
        self::$_lastRecordModels[$vipId] = $model ? $model : FALSE;
        return $model;
    }

    /**
     * 添加一条跟进记录
     * @return <bool>   是否保存成功
     */
    public function insertRecord() {
        if (!$this->ext_info_cost) {
            $this->ext_info_cost = $this->getExtInfoCost();     //扩展信息（当前消费信息）
        }
        return $this->save();
    }

    /**
     * 获取需要保存的扩展信息（当前消费信息）
     * @return <string>     json_encode后的数据
     */
    public function getExtInfoCost() {
        $vipId = $this->vip_id;
        $vipCostExt = VipCostExt::model()->findByAttributes(array('vip_id' => $vipId), array('order' => 'id DESC'));
        if ($vipCostExt) {
            return json_encode($vipCostExt->attributes);
        }
        return '';
    }

}
