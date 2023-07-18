<?php
/**
 * 预警脚本
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-11-15
 * Time: 上午10:36
 * auther mengtianxue
 */

class warningCommand  extends CConsoleCommand {

    /**
     * @auther mengtianxue
     * php yiic.php warning OrderSettle
     */
    public function actionOrderSettle(){
       echo Common::jobBegin("未结账订单");
        $created = strtotime("-1 day");

        $num = Order::getDbReadonlyConnection()->createCommand()
            ->select('count(1)')
            ->from("t_order")
            ->where('created > :created and status = 1 and city_id != 8 and income > 40 and cast = 0',
            array(':created' => $created))
            ->queryScalar();
        if($num > 80){
            $message = '结账方法可能出问题了，现在结账不成功的有'.$num.'个，迅速查一下吧！';
            Sms::SendSMS('18511663962', $message);
        }
        echo Common::jobEnd("未结账订单");

    }
    
} 
