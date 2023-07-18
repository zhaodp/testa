<?php

class ComplainNewAction extends CAction {
    public function run() {
        $model = new CustomerComplain();
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'customer-complain-cs-add-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        if (isset($_GET['phone'])) {
            $model->attributes = $_GET;
        }

        if (isset($_POST['CustomerComplain'])) {
            $model->unsetAttributes();  // clear any default values
            $model->attributes = $_POST['CustomerComplain'];
            $model->complain_type = isset($_POST['sub_type'])?$_POST['sub_type']:'';

            $model->create_time = date('Y-m-d H:i:s', time());
            $model->operator = $model->created = Yii::app()->user->id;
            $model->source = '1';     //固定400创建
            $model->status = $model->cs_process = CustomerComplain::STATUS_CS;    //客服创建

            if ($model->save()) {
                $complainlog = new CustomerComplainLog();
                $id = $model->attributes['id'];
                if ($complainlog->insertData($complainlog, $id, $complainlog::PROCESS_ZERO, '客服创建')) {
                    Yii::app()->clientScript->registerScript('alert', 'alert("添加成功");');
                    Yii::app()->clientScript->registerScript('close', '
                        if (navigator.userAgent.indexOf("Firefox") > 0){
                            window.location.href = "about:blank";
                        }else{
                            window.opener=null;window.open("","_self");window.close();
                        }
                    ');
                    $this->controller->renderText('');
                    Yii::app()->end();
                }
            }
        }

        $complainType = CustomerComplainType::model()->getComplainTypeByID(0);
        $typeArr = array('-1' => '全部');
        foreach ($complainType as $item) {
            $typeArr[$item->id] = $item->name;
        }
        $this->controller->render('new', array(
            'model' => $model,
            'typelist' => $typeArr,
        ));
    }

}