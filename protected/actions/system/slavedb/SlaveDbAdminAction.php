<?php
Yii::import('application.models.system.SlaveDb');
/*
 * mysql从库监控配置
 * @auth liuxiaobo
 */

class SlaveDbAdminAction extends CAction {
    
    public function run() {
        $data = array();
        $dbs = SlaveDb::model()->getDbs();
        $data['dbs'] = $dbs;        //可选的db
        
        if(isset($_POST['submit'])){
            $postDbs = isset($_POST['dbs']) ? $_POST['dbs'] : array();
            $saveOk = FALSE;
            if(is_array($postDbs)){
                $saveOk = SlaveDb::model()->setDbCache($postDbs);
            }
            $msg = $saveOk ? '保存成功' : '保存失败';
            Yii::app()->user->setFlash('saveSlaveDb', $msg);
            $this->controller->redirect(Yii::app()->request->url);
        }
        $selectDb = SlaveDb::model()->getDbCache();
        $data['selectDb'] = $selectDb;
        
        $view = 'slavedb/admin';
        $this->controller->render($view, array('data'=>$data));
    }

}
