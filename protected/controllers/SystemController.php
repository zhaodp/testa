<?php

Yii::import('application.actions.system.search.*');      //搜索相关action
Yii::import('application.actions.system.slavedb.*');     //mysql从库监控配置
Yii::import('application.actions.system.sundry.*');
Yii::import('application.actions.system.setting.*');    //系统设置
class SystemController extends Controller {

    public function actions() {
        return array(
            /** 搜索 start **/
            'search' => 'SearchAction',         //全局搜索
            'searchItem' => 'SearchItemAction', //特定搜索
            /** 搜索 end **/
            
            /** mysql从库监控配置 start **/
            'slaveDbAdmin' => 'SlaveDbAdminAction',         //mysql从库监控配置
            /** mysql从库监控配置 end **/
            
            /** 域名解析管理 start **/
            'domainAdmin' => 'DomainAdminAction',             //域名解析管理
            /** 域名解析管理 end **/

            'sms_log' => 'SmsLogAction',
        );
    }

}
