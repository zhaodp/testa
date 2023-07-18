<?php

Yii::import('application.models.schema.activity.CarActivity');

class BActivity extends CarActivity {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function beforeSave() {
        $time = time();
        $user = Yii::app()->user->id;
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->create_time = $time;
                $this->start_person = $user;

                //创建活动时自动分配一个活动标识
                if (empty($this->activity_key)) {
                    $this->activity_key = $this->autoBuildKey();
                }
            }
            $this->modify_time = $time;

            return TRUE;
        }
        return FALSE;
    }

    public function getActivityByKey($key) {
        $activity = $this->findByAttributes(array('activity_key' => $key));
        return $activity;
    }

    /**
     * 此活动是否现在可用
     * @return boolean
     */
    public function nowInActivity() {
        $res = FALSE;
        $time = time();
        $statusOk = $this->status == 1 ? TRUE : FALSE;
        $timeOk = ($time >= $this->begin_time && $time < $this->end_time) ? TRUE : FALSE;
        if ($statusOk && $timeOk) {
            $res = TRUE;
        }
        return $res;
    }

    /**
     * 某个用户是否参加过这个活动
     * @param type $user
     */
    public function usedTheActivity($user = array()) {
        $activityKey = $this->activity_key;
        $params = array_merge(array('event_id' => $activityKey), $user);
        $hasEventLog = BCustomerEventLog::model()->hasEventLog($params);    //验证是否已经参加过此活动

        return $hasEventLog;
    }

    /**
     * 获取扩展配置信息
     * @return <array>
     */
    public function getExtraIni() {
        $res = array();
        if ($this->extra) {
            $extra = json_decode($this->extra, TRUE);
        }

        return isset($extra) ? $extra : $res;
    }
    
    /**
     * 随机生成字符串，作为活动标识
     * @return <string>
     * @author liuxiaobo
     * @since 2014-03-24
     */
    public function autoBuildKey() {
        $key = '';
        $time = (string) time();
        $len = strlen($time);
        $hash = rand(0, 10);
        $ASCII_MIN = 65;
        for ($i = 0; $i < $len; $i++) {
            $key .= chr($ASCII_MIN + $time[$i] + $hash);
        }
        return $key;
    }

}
