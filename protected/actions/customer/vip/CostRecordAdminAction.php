<?php

/**
 * vip消费跟进记录
 * @author liuxiaobo
 * @since 2014-1-2
 */
class CostRecordAdminAction extends CAction {

    public function run() {
        $model = new Vip('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['Vip'])) {
            $model->attributes = $_GET['Vip'];
        }
        $this->controller->render('record/costRecordAdmin', array(
            'model' => $model,
        ));
    }

}
