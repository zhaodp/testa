<?php

class AgentDispatchLog extends ReportActiveRecord {
    
    const TYPE_CUSTOMER = 1;
    
    const TYPE_DRIVER = 2;
    
    const TYPE_MANUAL = 3;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CallcenterLog the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }
    
    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{callcenter_dispatch_log}}';
    }
}

?>