<?php
/**
 * 
 * 定期进行的数据维护
 * @author dayuer
 *
 */

class JobsCommand extends CConsoleCommand {

	
	/**
	 * 
	 * 迁移一个日期的司机日志到stat表
	 * @param string $date
	 */
	protected function employeeTrack($date){
		
	}
	
	/*
	 * 创建stat日期归档表
	 * 
	 */
	protected function createTables(){
		$data = date('Ymd',time());
		
		$sql = "CREATE TABLE IF NOT EXISTS `t_employee_track_$data` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `imei` varchar(50) DEFAULT NULL,
				  `mnc` int(11) DEFAULT NULL,
				  `mcc` int(11) DEFAULT NULL,
				  `lac` int(11) DEFAULT NULL,
				  `ci` int(11) DEFAULT NULL,
				  `state` int(11) DEFAULT NULL,
				  `insert_time` datetime DEFAULT NULL,
				  PRIMARY KEY (`id`),
				  KEY `imei` (`imei`),
				  KEY `state` (`state`),
				  KEY `insert_time` (`insert_time`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		
		Yii::app()->stat->createCommand($sql)->execute();
				
	}
}