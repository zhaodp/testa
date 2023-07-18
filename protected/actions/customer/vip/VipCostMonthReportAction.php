<?php

/**
 * vip消费月统计
 * @author liuxiaobo
 * @since 2014-1-3
 */
class VipCostMonthReportAction extends CAction {

    public function run() {
        $model = new VipCostMonth('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['VipCostMonth'])) {
            $model->attributes = $_GET['VipCostMonth'];
        }
        $this->controller->render('report/costReportList', array(
            'model' => $model,
        ));
    }

}
