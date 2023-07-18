<?php
/**
 * User: zhanglimin
 * Date: 13-7-10
 * Time: 下午6:44
 */
class AddressHistoryCommand extends CConsoleCommand {


    /**
     * @author zhanglimin
     * 刷新mongo历史地址数据
     * php protected/yiic addressHistory AddressHistoryMongoReload
     * 每周一执行
     */
    public function actionAddressHistoryMongoReload(){
        AddressCallHistory::model()->init();
        echo date("Y-m-d H:i:s")."  刷新mongo历史地址数据\n";
    }

    /**
     * @author zhanglimin
     * 刷新redis缓存历史地址数据
     * php protected/yiic addressHistory AddressHistoryCacheReload
     * 每周二执行
     */
    public function actionAddressHistoryCacheReload(){
        GPS::model()->addressHistoryReload();
        echo date("Y-m-d H:i:s")."  刷新redis缓存历史地址数据\n";
    }

}