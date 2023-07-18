<?php

class DomainAdminAction extends CAction {

    public function run($id = null, $operation=null) {
        Yii::import('application.models.system.DomainSetting');

        if ($id !== null) {
            $model = DomainSetting::model()->findByAttributes(array('id' => $id));
            if($operation == 'delete'){
                $deleteOk = $model->delete();
                $msg = $deleteOk ? '删除成功' : '删除失败';
                Yii::app()->user->setFlash('DomainSetting', $msg);
                $this->controller->redirect(array($this->controller->route));
                Yii::app()->end();
            }
        }
        if (!isset($model) || empty($model)) {
            $model = new DomainSetting;
        }

        if (isset($_POST['DomainSetting'])) {
            if (is_array($_POST['DomainSetting'])) {
                foreach ($_POST['DomainSetting'] as $key => $val) {
                    $_POST['DomainSetting'][$key] = str_replace(' ', '', $val);
                }
            }
            $model->attributes = $_POST['DomainSetting'];
            $saveOk = $model->save();
            $msg = $saveOk ? '保存成功' : '保存失败';
            Yii::app()->user->setFlash('DomainSetting', $msg);
            $this->controller->redirect(array($this->controller->route));
            Yii::app()->end();
        }

        $searchModel = new CActiveDataProvider($model, array(
            'criteria' => array('order' => 'id DESC'),
            'pagination' => array(
                'pageSize' => 50,
            )
        ));

        $this->controller->render('setting/domain_admin', array(
            'model' => $model,
            'searchModel' => $searchModel,
        ));
    }

}
