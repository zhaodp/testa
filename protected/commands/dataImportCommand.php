<?php
/**
 * 生成数据表、数据导入操作Command
 * @author AndyCong<congming@edaijia.cn>
 * @version web2.0 2013-01-24
 * @uses cmd : php yiic.php Data_import action --param=
 */
class dataImportCommand extends CConsoleCommand {
	/**
	 * 按月份生成t_daily_order_driver 每月生成一张表
	 * @uses cmd : php yiic.php dataImport CreateTable --year=2012
	 */
	public function actionCreateTable($year=null) {
		echo "~~~~~~start~~~~~~";
		$sql = "CREATE TABLE IF NOT EXISTS `t_daily_order_driver` (
		  `id` bigint(20) NOT NULL auto_increment COMMENT 'id(主键)',
		  `order_id` bigint(20) NOT NULL default '0' COMMENT '订单ID',
		  `order_number` varchar(20) collate utf8_unicode_ci COMMENT '订单号码',
		  `source` tinyint(4) default '0' COMMENT '渠道',
		  `city_id` smallint(6) default '0' COMMENT '城市ID',
		  `call_type` smallint(6) default '0' COMMENT '呼叫类型',
		  `call_time` int(11) default '0' COMMENT '呼叫时间',
		  `order_date` varchar(8) collate utf8_unicode_ci COMMENT '下单日期',
		  `booking_time` int(11) default '0' COMMENT '预约时间',
		  `reach_time` int(11) default '0' COMMENT '到达时间',
		  `reach_distance` smallint(6) default '0' COMMENT '到达距离',
		  `start_time` int(11) default '0' COMMENT '开始时间',
		  `end_time` int(11) default '0' COMMENT '结束时间',
		  `time_part` tinyint(2) default '7' COMMENT '时间段',
		  `current_month` int(11) default '0' COMMENT '当前月份',
		  `current_day` int(11) default '0' COMMENT '当前日期',
		  `year` smallint(4) default '0' COMMENT '年',
		  `month` tinyint(2) default '7' COMMENT '月',
		  `day` tinyint(2) default '7' COMMENT '日',
		  `distance` smallint(6) default '0' COMMENT '驾驶里程',
		  `charge` smallint(6) default '0' COMMENT '价格',
		  `location_start` varchar(30) collate utf8_unicode_ci COMMENT '起始位置',
		  `location_end` varchar(30) collate utf8_unicode_ci COMMENT '终止位置',
		  `income` smallint(6) default '0' COMMENT '收入',
		  `cast` smallint(6) default '0' COMMENT '费用',
		  `coupon` smallint(6) default '0' COMMENT '代金券金额',
		  `status` tinyint(2) default '0' COMMENT '状态',
		  `user_id` int(11) default '0' COMMENT '用户ID',
		  `customer_name` varchar(30) collate utf8_unicode_ci COMMENT '用户名',
		  `customer_type` tinyint(2) default '0' COMMENT '用户类型',
		  `phone` varchar(20) collate utf8_unicode_ci COMMENT '电话号',
		  `vipcard` varchar(15) collate utf8_unicode_ci COMMENT 'vip卡',
		  `is_new_user` tinyint(1) default '0' COMMENT '0:老用户、1:新用户',
		  `is_active` tinyint(1) default '0' COMMENT '0:非活跃用户、1:活跃用户',
		  `driver` varchar(30) collate utf8_unicode_ci COMMENT '司机用户名',
		  `driver_id` int(11) default '0' COMMENT '司机ID',
		  `driver_user` varchar(30) collate utf8_unicode_ci COMMENT '司机工号',
		  `driver_phone` varchar(20) collate utf8_unicode_ci COMMENT '司机手机号',
		  `driver_imei` varchar(20) collate utf8_unicode_ci COMMENT '司机imei号',
		  `driver_picture` varchar(200) collate utf8_unicode_ci COMMENT '司机图片',
		  `is_new_driver` tinyint(2) default '0' COMMENT '0:老用户、1:新用户',
		  `is_left` tinyint(2) default '0' COMMENT '0:未解约、1:已解约',
		  `created` int(11) default '0' COMMENT '创建时间',
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `order_id` (`order_id`),
		  KEY `source` (`source`),
		  KEY `city_id` (`city_id`),
		  KEY `time_part` (`time_part`),
		  KEY `current_month` (`current_month`),
		  KEY `current_day` (`current_day`),
		  KEY `year` (`year`),
		  KEY `month` (`month`),
		  KEY `day` (`day`),
		  KEY `distance` (`distance`),
		  KEY `income` (`income`),
		  KEY `driver_id` (`driver_id`),
		  KEY `is_left` (`is_left`),
		  KEY `is_new_driver` (`is_new_driver`),
		  KEY `vipcard` (`vipcard`),
		  KEY `user_id` (`user_id`),
		  KEY `is_new_user` (`is_new_user`),
		  KEY `is_active` (`is_active`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		
		Yii::app()->db_datastat->createCommand($sql)->execute();
		echo "~~~~~~end~~~~~~";
	}
	
	/**
	 * 获取统计中间表信息记录到t_daily_order_driver
	 * @uses cmd : php yiic.php dataImport DailyOrderDriver --date=2012-12-12
	 */
	public function actionDailyOrderDriver($date = null) {
		echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
		$DataImport = new DataImport();
		$DataImport->actionDailyOrderDriver( $date );
		echo "\n----------".date('Y-m-d H:i:s')."---job end------\n";
	}
	
	/**
	 * 未完成订单重载操作
	 * @uses php yiic.php DataImport DailyOrderDrvierReload
	 */
	public function actionDailyOrderDrvierReload() {
		echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
		$DataImport = new DataImport();
		$DataImport->actionDailyOrderDrvierReload();
		echo "\n----------".date('Y-m-d H:i:s')."---job end------\n";
	}
	
	/**
	 * 初始化操作
	 * @uses cmd : php yiic.php DataImport DailyOrderDriverInitialize
	 *             php yiic.php DataImport DailyOrderDriverInitialize --month=2012-01
	 *             php yiic.php DataImport DailyOrderDriverInitialize --month=2012-01 --order=1
	 */
	public function actionDailyOrderDriverInitialize($month = null , $order = 0) {
		echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
		$DataImport = new DataImport();
	    $DataImport->actionDailyOrderDriverInitialize( $month , $order );
		echo "\n----------".date('Y-m-d H:i:s')."---job end------\n";
	}
	
	/**
	 * 定时执行脚本
	 * @uses php yiic.php dataImport Crontab
	 */
	public function actionCrontab() {
		ini_set("memory_limit","500M");
		echo Common::jobBegin("开始导入数据");
		$DataImport = new DataImport();
		$DataImport->actionCrontab();
        echo Common::jobEnd("结束导入数据");
		
	}
}
