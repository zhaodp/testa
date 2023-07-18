<?php
/**
 * 一口价洗车余额支付 获取客户余额接口
 * User: jack
 * Date: 2015/1/21
 * Time: 19:04
 * test02环境上的测试用例：php /sp_edaijia/www/v2/protected/yiic  demo  washCarPay --views='open.finance.info'
 */
//验证参数
$token = isset($params['token']) ? trim($params['token']) : '';
$phone = isset($params['phone']) ? $params['phone'] : '';
$expect_cast = isset($params['expect_cast']) ? $params['expect_cast'] : 0;//期望金额
if (empty($token) || empty($phone)) {
    $ret = array('code' => 2, 'message' => '参数有误');
    echo json_encode($ret);return ;
}
//验证token
$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
    EdjLog::info('---token验证失败---'.$token);
    $ret = array('code' => 1 , 'message' => 'token验证失败');
    echo json_encode($ret);return ;
}else{
    $data=VipPhone::model()->getPrimary($phone);
    if($data){
        //是vip
        $vipArray = VipPhone::model()->getVipInfoByPhone($phone,true);
        $_balance = $vipArray['balance'];//vip主卡或副卡对应的主卡卡余额
        if( $data['type']==VipPhone::TYPE_VICE){
            //vip副卡
            EdjLog::info("---vip副卡--phone: $phone-");
            $is_enough = ($_balance >= $expect_cast) ? true : false;//副卡对应的主卡余额是否充足  不回传余额信息给客户端
            $ret = array('code' => 0 ,
                'data' => array('is_enough'=>$is_enough,'type'=>3) ,
                'message' => '获取客户余额成功'
            );
            echo json_encode($ret);return ;
        }else{
            //vip主卡
            EdjLog::info("---vip主卡-phone:$phone--");
            $is_enough = ($_balance >= $expect_cast) ? true : false;//主卡余额是否余额充足
            $ret = array('code' => 0 ,
                'data' => array('balance'=>$_balance,'is_enough'=>$is_enough,'type'=>2) ,
                'message' => '获取客户余额成功'
            );
            echo json_encode($ret);return ;
        }
    }else{
        //普通客户
        EdjLog::info("---普通客户-phone:$phone--");
        $customerMain = CustomerMain::model()->getCustomer($phone);
        $user_id = $customerMain ? $customerMain->id : 0;
        $model = CarCustomerAccount::model()->getCustomerAccountInfo($user_id);
        if(empty($model)){
            $ret = array('code' => 3  , 'message' => '获取用户余额失败(可能该用户不存在账户)');
            echo json_encode($ret);return ;
        }else{
            $balance = $model['amount'];//用户当前金额  此处写成$model->amount报错
            $is_enough = ($balance >= $expect_cast) ? true : false;//是否余额充足
            $ret = array('code' => 0 ,
                'data' => array('balance'=>$balance,'is_enough'=>$is_enough,'type'=>1) ,
                'message' => '获取客户余额成功'
            );
            echo json_encode($ret);return ;
        }
    }
}



