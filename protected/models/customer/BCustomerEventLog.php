<?php

Yii::import('application.models.schema.customer.CarCustomerEventLog');

class BCustomerEventLog extends CarCustomerEventLog {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function beforeSave() {
        $time = time();
        if (parent::beforeSave()) {
            if (!$this->create_time) {
                $this->create_time = $time;
            }
            return true;
        }
        return FALSE;
    }

    /**
     * 添加一个用户参加活动记录
     * @param <array> $data 保存的参数，必填项有：event_id、phone、imei; 注：每个活动对应一个唯一的event_id标识
     * @return <bool> 成功返回true，否则返回false
     */
    public function buildEventLog($data = array()) {
        $model = new BCustomerEventLog();
        $model->attributes = $data;
        $saveOk = $model->save();
        return $saveOk ? TRUE : FALSE;
    }

    /**
     * 验证一个用户是否有参加活动记录
     * @param <array> $data 保存的参数，必填项有：event_id，选填项phone、imei两个中必填一个
     * @return <bool> 如果有则返回记录信息$model(CActiveRecord)，否则返回false
     */
    public function hasEventLog($data = array()) {
        if (is_array($data)) {
            $model = self::model()->findByAttributes($data);
            return $model ? $model : FALSE;
        }
        return FALSE;
    }

}
