<?php
/**
 *
 * 该类主要提供给外部结账的支持, 所有的结算
 *
 * User: feature@edaijia-inc.cn  liutuanwang@edaijia-inc.cn
 * Date: 15/4/28
 * Time: 17:53
 */

class SettleService {

    /**
     * 用来进行结算, 内部根据财务的类型进行映射然后走到对应的计算流程里面去
     * 目前支持 夜间订单结算, 远程订单结算, 日间订单结算
     *
     * @param $orderId
     * @param array $order
     * @param array $extraParams
     * @param int $isSubsidyHour | 默认没有日间业务补贴
     */
    public static  function settle($orderId, $order = array(), $extraParams = array(), $isSubsidyHour = 1){
        EdjLog::info('settle_service_settle ---'.json_encode(func_get_args()));
        $financeOrderType = FinanceUtils::getOrderType($order);
        $settleParams = array();
        if ($financeOrderType) {
            $settleParams = FinanceUtils::getLegalParamList($financeOrderType, $extraParams);
        }
        $settleParams['subsidy_hour'] = $isSubsidyHour;//日间单2.5.4版本为1去掉每小时补贴10元
        if (FinanceUtils::isParamSettle($financeOrderType)) {
            OrderSettlement::model()->submitSettle($orderId, $settleParams);
        } else {
            if (FinanceUtils::isRemoteOrder($order, OrderExt::model()->getPrimary($orderId))) {
                OrderSettlement::model()->remoteOrderSettle($orderId);
            } else {
                OrderSettlement::model()->orderSettle($orderId);
            }
        }
    }

    /**
     * 返回合法的结算参数
     *
     * @param $order
     * @param $params
     * @return array
     */
    public static function getLegalParamList($order, $params){
        $orderType = FinanceUtils::getOrderType($order);
        return FinanceUtils::getLegalParamList($orderType, $params);
    }

    /**
     * 订单是否能够结账
     *
     * @param $order
     * @return mixed
     */
    public static function canSettle($order){
        return SettleStatusLogic::model()->canSettle($order);
    }
}