<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-12-29
 * Time: 下午7:48
 * auther mengtianxue
 */

class VipGroupByDriverAction extends CAction {

    public function run(){
        $month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
        $data = BReportDailyVipReport::model()->getVipGroupByDriver($month);
        $this->controller->render('report/vip_group_driver',
            array('data' => $data,
            'month' => $month)
        );
    }
    
} 