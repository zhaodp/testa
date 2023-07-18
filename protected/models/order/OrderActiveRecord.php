<?php

class OrderActiveRecord extends CActiveRecord {

    public function getDbConnection() {
        return self::getDbMasterConnection();
    }
    
    public static function getDbMasterConnection() {
        return Yii::app()->dborder;
    }
    
    public static function getDbReadonlyConnection() {
        return Yii::app()->dborder_readonly;
    }
}

?>