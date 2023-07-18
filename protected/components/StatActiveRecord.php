<?php
/**
 * StateActiveRecord
 *
 * @author syang
 */
class StatActiveRecord extends CActiveRecord {
    
    public function behaviors() {
		return array_merge(parent::behaviors(), array(
		    'MasterSlaveBehavior' => array(
			'class'=>'application.behaviors.MasterSlaveBehavior',
			'masterDbHandlerName' => array('dbstat'),
			'slaveDbHandlerName' => array('dbstat_readonly')
		)));
    }
}
