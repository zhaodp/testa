<?php
class ComplainAddAction extends CAction {
    public function run() {
        $model = new CustomerComplain();
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'customer-complain-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        if (isset($_POST['CustomerComplain']) && !isset($_POST['ajax'])) {
            $model->attributes = $_POST['CustomerComplain'];
            $model->complain_type = $_POST['sub_type'] ? $_POST['sub_type'] : $_POST['complain_maintype'];
            $model->service_time = $_POST['service_time'];
            $model->create_time = date('Y-m-d H:i:s', time());
            $model->operator = $model->created = Yii::app()->user->id;
            $model->status = 1;
            $model->cs_process = 1;    //客服创建
            if ($model->save()) {
                // 添加日志 by 曾志海
                $complainlog = new CustomerComplainLog();
                $id = $model->attributes['id'];
                if ($complainlog->insertData($complainlog, $id, $complainlog::PROCESS_ONE, '品监创建')) {
                    //add by aiguoxin 2014-09-22 app、短信评价加入意见列表
                    if($model->source == 2 || $model->source ==3){
                        $phone=$model->phone;
                        $title=$model->detail;
                        CustomerSuggestion::model()->initSuggestion($phone,$title,CustomerSuggestion::TYPE_COMPLAIN,$id);
                    }
                    $this->controller->redirect(Yii::app()->createUrl('complain/list'));
                }
                
            }
        }
        $complainType = CustomerComplainType::model()->getComplainTypeByID(0);
        $typeArr = array('-1' => '全部');
        foreach ($complainType as $item) {
            $typeArr[$item->id] = $item->name;
        }

        $this->controller->render('create', array(
            'model' => $model,
            'typelist' => $typeArr,
        ));
    }
}