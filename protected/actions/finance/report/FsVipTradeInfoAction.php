<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-12-29
 * Time: 下午9:35
 * auther mengtianxue
 */
Yii::import('application.models.schema.report.ReportFsVipTradeInfo');
class FsVipTradeInfoAction extends CAction {

    public function run(){
        $model = new ReportFsVipTradeInfo('search');
        $model->unsetAttributes();
        if($_GET){
            $model->attributes = $_GET;
        }
        $this->controller->render('report/fs_vip_trade_info',
            array('model' => $model,
            'params' => $_GET)
        );
        
    }
    
} 