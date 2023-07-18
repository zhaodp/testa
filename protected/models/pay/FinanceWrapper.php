<?php
Yii::import('application.models.pay.orderSettlementImpl.*');

/**
 *
 * 封装财务的给别人提供接口的情况
 *
 * User: tuan
 * Date: 15/1/14
 * Time: 15:54
 */

class FinanceWrapper {

    /**
     * 提供对于司机信息费的操作,默认为扣款
     *
     *
     * @param $driverId | 对应司机工号
     * @param $cityId |司机对应城市 id
     * @param $cast | 费用, 由于信息费默认是扣除的,所以 如果是扣除,传入正数即可, 如果是充值,需要传入负数 | 如果为正数,不能大于当前司机拥有的钱
     * @param $channel | 必传参数,主要用来后续的统计, 原则上有财务人员进行分配, 调用方不能自己设定
     * @param $comment | 必传参数, 关于扣款费用的说明, 会在后台看到, 需要简短明了的表示扣款原因
     * @return array|void 如果成功, code 会为0, 可以通过 FinanceConstants::isNotSuccess($ret) 和 FinanceConstants::isSuccess($ret) 判断是成功还是失败
     */
    public static function settleDriver($driverId, $cityId, $cast, $channel, $comment){
        if(empty($driverId) || empty($cityId) || empty($comment)) {
            return array(
                'code' => 2,
                'message' => 'arguments error',
            );
        }
        if(empty($channel)){
            $channel = EmployeeAccount::TYPE_ORDER_INFOMATION;
        }
        $balance = DriverBalance::model()->getBalance($cityId, $driverId);
        if($balance < $cast){
            return array(
                'code' => 2,
                'message' => 'driver have no enough money',
            );
        }
        $order = array(
            'order_id' => 0,
            'city_id'  => $cityId,
            'created'  => time(),
            'driver_id' => $driverId,
        );
        $settle = New SpecialInformationSettlement($order, array(), 0);
        $settle->setChannel($channel);
        $settle->setComment($comment);
        $settle->setBase($cast);
        $settle->setRate(1);
        $ret = $settle->settlement();
        if(FinanceConstants::isNotSuccess($ret)){
           FinanceUtils::sendFinanceAlarm('Finance Wrapper error', json_encode(func_get_args()));
        }
        return $ret;
    }


    /**
     * 把一个优惠码绑给某个用户
     *
     * @param $phone
     * @param $bonusSn
     * @return bool | 成功返回 true
     */
    public static function bindBonusBySn($phone, $bonusSn){
        EdjLog::info(json_encode(func_get_args()));
        $ret = BonusLibrary::model()->BonusBinding($bonusSn, $phone);
        $code = isset($ret['code']) ? $ret['code'] : -1;
        return $code == 0;
    }

    /**
     * @param $phone  用户手机号码
     * @param $bonus_sn  优惠码
     * @param $num  绑定张数
     * @param $sms 短信内容(传空字符串则不发短信)
     * @return array
     */
    public static function bindBonusGenerate($phone , $bonus_sn , $num , $sms){
        $result = array(
            'code' => FinanceConstants::CODE_FAIL ,
            'msg' => ''
        );
        if (empty($phone) || empty($bonus_sn) || empty($num)) {
            $result['msg'] = '参数错误' ;
            return $result ;
        } else {
                //vip用户限制
                $vip_phone = VipPhone::model()->getPrimary($phone);
                if (!empty($vip_phone)) {
                    $result['msg'] = '该用户为VIP用户' ;
                    return $result ;
                }
                $bonus_library = BonusLibrary::model()->getBonusByBonus_sn($bonus_sn, 1);
                if ($bonus_library) {
                    $params = array();
                    $params['bonus_id'] = $bonus_library['bonus_id'];
                    $params['bonus_sn'] = $bonus_sn;
                    $params['num'] = $num;
                    $params['sms'] = $sms;
                    $params['phone'] = trim($phone);

                    //添加task队列向数据中添加
                    $task = array(
                        'method' => 'addCustomerBonusBatch',
                        'params' => $params
                    );
                    Queue::model()->putin($task, 'order');
                } else {
                    $result['msg'] = '优惠码不存在' ;
                    return $result ;
                }
        }
        $result['msg'] = 'success' ;
        $result['code'] = FinanceConstants::CODE_SUCCESS ;
        return $result ;
    }


    /**
     *
     * 给一个客户充钱
     *
     * @param $phone | 要重启的客户电话
     * @param $cast | 充值金额
     * @param $from | 由财务这边分配
     * @param $comment | 会作为流水记录,不要写太长(40极限)
     * @param int $orderId | 可不填
     */
    public static function chargeUser($phone, $cast, $from, $comment, $orderId = 0){
        $isVip = CustomerMain::model()->isVip($phone);
        $ret = false;
        if($isVip){
            $ret = self::chargeVip($phone, $cast, $from, $comment, $orderId);
        }else{
            $ret = self::chargeNormal($phone, $cast, $from, $comment, $orderId);
        }
        return $ret;
    }

    public static function chargeVip($phone, $cast, $from, $comment, $orderId = 0){
        //TODO ... add implements
    }

    public static function chargeNormal($phone, $cast, $from, $comment ,$orderId = 0){
        EdjLog::info(json_encode(func_get_args()));
        $settlement = new CustomerSettlement($phone);
        $settlement->setCast($cast);
        $settlement->setComment($comment);
        $settlement->setSource(CarCustomerTrans::TRANS_SOURCE_RE);
        $settlement->setType(CarCustomerTrans::TRANS_TYPE_RE);
        $settlement->setOrderId($orderId);
        return $settlement->settlement();
    }



}