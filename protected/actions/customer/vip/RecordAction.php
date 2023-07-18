<?php

/**
 * 添加vip跟进备注
 * @author liuxiaobo
 * @since 2014-1-2
 */
class RecordAction extends CAction {

    public function run($vipId = 0, $dialog = 0) {
        if ($dialog) {
            $this->controller->layout = 'main_no_nav';
        }
        $model = new VipRecord;
        if (!$model) {
            throw new CHttpException(404, '没有该信息');
        }
        if (isset($_POST['VipRecord'])) {
            $model->attributes = $_POST['VipRecord'];
            $model->vip_id = $vipId;
            if ($model->insertRecord()) {
                $this->controller->renderText('<script>window.parent.reloadItems();</script>');
            }
        }

        $this->controller->render('record/create', array(
            'model' => $model,
        ));
    }

}
