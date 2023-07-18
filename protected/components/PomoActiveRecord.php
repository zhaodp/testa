<?php
/**
 * Created by PhpStorm.
 * User: Thinkpad
 * Date: 2015/3/24
 * Time: 15:55
 */

class PomoActiveRecord extends CActiveRecord {

    public function getDbConnection() {
        return Yii::app()->db_pomo;
    }
}