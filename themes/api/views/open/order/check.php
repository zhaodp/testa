<?php
/**
 *  用于查询单笔支付状态。返回true表示已支付，false则没有
 * Created by PhpStorm.
 * User: dingcheng
 * Date: 2015/1/22 0022
 * Time: 下午 4:56
 */
if( isset($params['order_id'])){
    echo(BUpmpPayOrder::checkOrderStatus($params['order_id']));
    return ;
}
echo(false);
return ;
?>