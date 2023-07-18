<?php

/**
 * vip跟进记录
 * @author liuxiaobo
 * @since 2014-1-2
 */
class RecordListAction extends CAction {

    public function run($vipId = 0, $dialog = 0) {
        if ($dialog) {
            $this->controller->layout = 'main_no_nav';
        }
        $model = new VipRecord('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['VipRecord'])) {
            $model->attributes = $_GET['VipRecord'];
        }
        $model->vip_id = $vipId;
        $this->controller->render('record/list', array(
            'model' => $model,
        ));
    }

}
