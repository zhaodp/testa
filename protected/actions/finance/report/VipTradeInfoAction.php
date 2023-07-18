<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-12-27
 * Time: 下午1:46
 * auther mengtianxue
 */

class VipTradeInfoAction extends CAction {

    public function run(){
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        $data = BReportDailyVipReport::model()->getVipMonthList($year);
        $this->controller->render('report/vip_month_info',
            array('data' => $data)
        );
        
    }
    
} 