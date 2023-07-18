<?php
/**
 * 订单数据统计
 * @author AndyCong<congming@edaijia.cn>
 * @version web2.0 2013-01-24
 * @uses cmd : php yiic.php orderReport action --param
 */
class orderReportCommand extends CConsoleCommand {	
	/**
	 * 获取订单中间表信息记录到t_order_trend_report
	 * @uses cmd : php yiic.php orderReport OrderTrendReport --date=2012-12-12
	 */
	public function actionOrderTrendReport($date = null) {
		echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
		$OrderStat = new OrderStat();
		$OrderStat->actionOrderTrendReport($date);
		echo "----------".date('Y-m-d H:i:s')."---job end------\n";
	}
	
	/**
	 * 周报统计
	 * @uses cmd : php yiic.php orderReport WeekReport --date=2012-12-12
	 */
	public function actionOrderWeeklyReport($date = null , $type = null) {
		echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
		$OrderStat = new OrderStat();
		$OrderStat->actionOrderWeeklyReport($date , $type);
		echo "----------".date('Y-m-d H:i:s')."---job end------\n";
	}
	
	/**
	 * 月报统计
	 * @uses cmd : php yiic.php orderReport MonthReport
	 */
	public function actionMonthReport() {
		echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
		$OrderStat = new OrderStat();
		$OrderStat->getOrderWeekReport($date , $type);
		echo "----------".date('Y-m-d H:i:s')."---job end------\n";
	}
	
	/**
	 * 用户统计
	 * @uses cmd : php yiic.php orderReport CustomerReport
	 */
	public function actionCustomerReport() {
		echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
		$OrderStat = new OrderStat();
		$OrderStat->getOrderCustomerReport($date , $type);
		echo "----------".date('Y-m-d H:i:s')."---job end------\n";
	}
	
	/**
	 * 订单地狱分布统计
	 * @uses cmd : php yiic.php orderReport AreaDistributeReport
	 */
	public function actionAreaDistributeReport() {
		echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
		$OrderStat = new OrderStat();
		$OrderStat->getAreaDistributeReport($date , $type);
		echo "----------".date('Y-m-d H:i:s')."---job end------\n";
	}
	
	/**
	 * 订单统计重载
	 */
	public function actionOrderReportReload() {
		echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
		$OrderStat = new OrderStat();
		$OrderStat->actionOrderReportReload();
		echo "----------".date('Y-m-d H:i:s')."---job end------\n";
	}
	
	/**
	 * 订单统计定时任务方法
	 * @uses cmd : php yiic.php orderReport OrderReportInitialize
	 *             php yiic.php orderReport OrderReportInitialize --month=2012-01
	 */
	public function actionOrderReportInitialize($month = null) {
		echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
		$OrderStat = new OrderStat();
		$OrderStat->actionOrderReportInitialize($month);
		echo "----------".date('Y-m-d H:i:s')."---job end------\n";
	}
	
	/**
	 * 定时执行脚本
	 * @uses php yiic.php orderReport Crontab
	 */
	public function actionCrontab() {
		echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
		$OrderStat = new OrderStat();
		$OrderStat->actionCrontab();
		echo "----------".date('Y-m-d H:i:s')."---job end------\n";
	}


    public function actionOrderMonthlyStat($type = 'update') {
        echo "----------".date('Y-m-d H:i:s')."---job begin------\n";
        $openCityList = RCityList::model()->getOpenCityList();
        $OrderStat = new OrderStat();
        if($type === 'update') $type = 'update';
        else $type = 'init';
        $OrderStat->setOrderMonthlyData(0,$type);
        foreach($openCityList as $city_id => $name){
            $OrderStat->setOrderMonthlyData($city_id,$type);
        }
        echo "----------".date('Y-m-d H:i:s')."---job end------\n";
    }
}