<?php
/**
 * Created by JetBrains PhpStorm.
 * author: ztk
 * Date: 14-4-5
 * Time: 下午3:38
 */
Yii::import('application.models.schema.report.ReportFsAccountRp');
Yii::import('application.models.schema.report.ReportFsAccountTag');
Yii::import('application.models.redis.*');
Yii::import('application.models.schema.customer.*');

class ztkCommand extends CConsoleCommand
{
    public function actionTest()
    {
    }
    /**
     * 南京返款测试
     * $order_id 订单id
     * $cost_type(1-vip用户,4or8,普通用户)
     * @auther zhangtongkai
     * php yiic.php ztk Nanjing --order_id=1000002596 --cost_type=4
     * php yiic.php ztk Nanjing --order_id=1000002628 --cost_type=1
     */
    public function actionNanjing($order_id, $cost_type = 1, $bonus_money = 0)
    {
        if (!empty($order_id)) {
            $params = array('order_id' => $order_id, 'cost_type' => $cost_type, 'bonus_money' => $bonus_money);
            OrderSettlement::model()->loadOrderExt($params);
            /*$task = array(
                'method' => 'load_order_ext',
                'params' => $params,
            );
            Queue::model()->putin($task, 'orderext');*/
            echo 'success' . "\n";
        }
    }


}
