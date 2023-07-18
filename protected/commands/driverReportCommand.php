<?php
/**
 * 司机数据统计Command
 * @author AndyCong<congming@edaijia.cn>
 * @version web2.0 2013-01-24
 * @uses cmd : php yiic.php driverReport action --param
 */
class driverReportCommand extends CConsoleCommand {
    /**
	 * 获取排行榜数据记录到t_driver_ranking
	 * @uses cmd : php yiic.php driverReport DriverRankingReport --date=2012-12-12
	 */
    public function actionDriverRankingReport($date = null) {
    	echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
		$DriverStat = new DriverStat();
		$DriverStat->actionDriverRankingReport($date);
		echo date("Y-m-d H:i:s",time());
		echo "----------".date('Y-m-d H:i:s')."---job end------\n";
    }
    
    /**
	 * 司机平均收入统计 t_driver_average_income
	 * @uses cmd : php yiic.php driverReport DriverAverageIncome
	 */
    public function actionIncomeRangeReport($date = null) {
    	echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
		$DriverStat = new DriverStat();
		$DriverStat->getDriverAverageIncomeReport($date);
		echo "----------".date('Y-m-d H:i:s')."---job end------\n";
    }
    
    /**
	 * 司机平均收入统计 t_driver_average_income
	 * @uses cmd : php yiic.php driverReport DriverEntry
	 */
    public function actionAttendIncomeReport() {
    	echo 'this action is DriverEntry';
    }
    
    /**
	 * 地区司机收入统计
	 * @uses cmd : php yiic.php driverReport AreaIncomeReport
	 */
    public function actionAreaIncomeReport(){
    	
    }
    
    /**
     * 签约解约统计
     * @uses cmd : php yiic.php driverReport EntryLeftReport
     */
    public function actionEntryLeftReport() {
    	
    }
    
    /**
     * 司机统计重载
     */
    public function actionDriverReportReload() {
    	echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
		$DriverStat = new DriverStat();
		$DriverStat->actionDriverReportReload();
		echo "----------".date('Y-m-d H:i:s')."---job end------\n";
    }
    
    /**
	 * 司机平均收入统计 t_driver_average_income
	 * @uses cmd : php yiic.php driverReport DriverReportInitialize
	 *             php yiic.php driverReport DriverReportInitialize --month=2012-01
	 */
    public function actionDriverReportInitialize($month = null) {
    	echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
		$DriverStat = new DriverStat();
		$DriverStat->actionDriverReportInitialize($month);
		echo "----------".date('Y-m-d H:i:s')."---job end------\n";
    }
    
    /**
     * 定时执行脚本
     * @uses php yiic.php driverReport Crontab
     */
    public function actionCrontab() {
    	echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
		$DriverStat = new DriverStat();
		$DriverStat->actionCrontab();
		echo "----------".date('Y-m-d H:i:s')."---job end------\n";
    }
}
