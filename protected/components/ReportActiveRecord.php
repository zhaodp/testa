<?php
/**
 * ReportActiveRecord
 *
 * @author syang
 * 
class ReportActiveRecord extends CActiveRecord {
    public $dbHandlerName = 'dbreport';
    
    public function behaviors() {
		return array(
	    	'MasterSlaveBehavior' => array(
				'class'=>'application.behaviors.MasterSlaveBehavior',
				'masterDbHandlerName' => array('dbreport'),
				'slaveDbHandlerName' => array('dbreport')
		));
    }
}
 */

class ReportActiveRecord extends CActiveRecord {
    
    public function getDbConnection() {
        return Yii::app()->dbreport;
    }
}
