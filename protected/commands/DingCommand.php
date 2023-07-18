<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/1/22 0022
 * Time: 下午 5:13
 */

class dingCommand extends CConsoleCommand {


    public function actionPayOrder()
    {
        $order_id = '20140305110838249590';
        $qn= '---';
        $resp_msg = '++++++';
        $order = new BUpmpPayOrder();
        $order->updateAllOrder($order_id, $qn, $resp_msg);
        echo("success");


    }

    public function actionCheckOrder(){
        echo(BUpmpPayOrder::checkOrderStatus('20140305110838249590'));

    }


}
?>