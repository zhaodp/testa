<?php
/**
 * 客户统计Command
 * @author AndyCong<congming@edaijia.cn>
 * @version web2.0 2013-03-15
 * @uses cmd : php yiic.php customerReport action --param
 */
class customerReportCommand extends CConsoleCommand {
	/**
	 * 客户统计初始化
	 * @uses cmd : php yiic.php customerReport CustomerReportInitialize
	 *             php yiic.php customerReport CustomerReportInitialize --month=2012-01
	 */
    public function actionCustomerReportInitialize($month = null) {
    	echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
		$CustomerStat = new CustomerStat();
		$CustomerStat->actionCustomerReportInitialize($month);
		echo "----------".date('Y-m-d H:i:s')."---job end------\n";
    }
    
	/**
     * 司机统计重载
     * @uses cmd : php yiic.php customerReport CustomerReportReload
     */
    public function actionCustomerReportReload() {
    	echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
		$CustomerStat = new CustomerStat();
		$CustomerStat->actionCustomerReportReload();
		echo "----------".date('Y-m-d H:i:s')."---job end------\n";
    }
    
    /**
     * 定时执行脚本
     * @uses php yiic.php customerReport Crontab
     */
    public function actionCrontab() {
    	echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
		$CustomerStat = new CustomerStat();
		$CustomerStat->actionCrontab();
		echo "----------".date('Y-m-d H:i:s')."---job end------\n";
    }
}