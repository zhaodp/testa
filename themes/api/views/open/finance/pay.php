<?php
/**
 * 一口价洗车支付接口
 * User: jack
 * Date: 2015/1/21
 * Time: 19:31
 *
 */
Yii::import('application.models.pay.settlement.*');
Yii::import('application.models.pay.subsidy.*');
//验证参数
$token = isset($params['token']) ? trim($params['token']) : '';
$phone = isset($params['phone']) ? $params['phone'] : '';
$cast = isset($params['cast']) ? $params['cast'] : 0;
$order_id = isset($params['order_id']) ? $params['order_id'] : '';
$channel = isset($params['channel']) ? $params['channel'] : '';//1.一口价洗车 2.其他
EdjLog::info("--financePay一口价洗车支付传过来的参数---- " . serialize($params));
if (empty($phone) || empty($cast) || empty($order_id) || empty($channel)|| $cast <= 0) {
    $ret = array('code' => 2 , 'message' => '参数有误');
    echo json_encode($ret);return ;
}
//客户端传过来的是order_num,转换为争取的order_id
if (strlen($order_id) > 11 && is_numeric($order_id)) {
    //从redies中获取数据库中的order_id
    $redis_order_id = ROrder::model()->getOrder($order_id , 'order_id');
    if (empty($redis_order_id)) {
        //redeis不存在则去数据库查找
        $order = Order::model()->getOrderByOrderNumberOnly($order_id);
        $db_order_id = $order->order_id;
        if(empty($db_order_id)){
            $ret = array('code' => 2 ,'message' => '参数有误(不存在该orderId)');
            echo json_encode($ret);return ;
        }else{
            $order_id = $db_order_id;
        }
    }else{
        $order_id = $redis_order_id;
    }
}
//step1---------.验证token
$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
    EdjLog::info("--token验证失败---token:- " . $token);
    $ret = array( 'code' => 1,  'message' => 'token验证失败',);
    echo json_encode($ret); return;
}else{
    //step2-----.区分客户vip还是普通客户
    $phone2 = trim($validate['phone']);
    if($phone != $phone2){
        EdjLog::info("--司机端传过来的电话和token得到的电话不一致-tokenPhone:-$phone2-phone:$phone- ");
        $ret = array('code' => 2 ,'message' => '参数有误');
        echo json_encode($ret); return;
    }
    $data=VipPhone::model()->getPrimary($phone);
    if($data){
        /**vip客户*/
        //step3-------.扣除vip客户余额插入流水
        $companySettle = new CompanySubsidySettlement('', 1);
        $ret = $companySettle->chargeVipPay($phone,$channel,$order_id,$cast);
        if(!$ret){
            $ret = array('code' => 3, 'message' => '支付失败');
            echo json_encode($ret);return ;
        }else{
            //step4-----.给对应公司账户充值并插入流水
            $user_id = $phone;//如果是vip该user_id用电话号码
            $companySettle = new CompanySubsidySettlement('', 1);
            $ret2 = $companySettle->reChargeCompanyAccount($user_id,$channel,$order_id,$cast);//给对应的公司账户充值并插入流水 当前为一口价洗车
            if(!$ret2){
                $ret = Sms::SendSMS('18301221389', "扣除vip客户 $user_id 余额后充值公司账户或插入公司账户流水失败");
                EdjLog::info("--扣除客户 $user_id 余额后充值公司账户或插入公司账户流水失败---- " . serialize($companyAccountTransAttributes));
            }
            $ret = array('code' => 0 ,'message' => '支付成功');
            echo json_encode($ret);return ;
        }
    }else{
        /**普通客户*/
        //step3-----.扣除客户余额插入流水
        $customer = CustomerMain::model()->getCustomer($phone);
        $user_id = $customer->id;
        $companySettle = new CompanySubsidySettlement('', 1);
        $ret = $companySettle->chargeCustomerPay($user_id,$channel,$order_id,$cast);//扣除客户账户支付的余额并插入流水
        if(!$ret){
            $ret = array('code' => 3 ,'message' => '支付失败(可能该用户不存在该账户)');
            echo json_encode($ret);return ;
        }else{
            //step4-------.给对应公司账户充值并插入流水
            $companySettle = new CompanySubsidySettlement('', 1);
            $ret2 = $companySettle->reChargeCompanyAccount($user_id,$channel,$order_id,$cast);//给对应的公司账户充值并插入流水 当前为一口价洗车
            if(!$ret2){
                $ret = Sms::SendSMS('18301221389', "扣除普通客户 $user_id 余额后充值公司账户或插入公司账户流水失败");
                EdjLog::info("--扣除客户 $user_id 余额后充值公司账户或插入公司账户流水失败---- " . serialize($companyAccountTransAttributes));
            }
            $ret = array('code' => 0 , 'message' => '支付成功');
            echo json_encode($ret);return ;
        }
    }

}
