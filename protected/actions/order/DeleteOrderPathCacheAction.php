<?php
/**
 * 删除订单时间线的缓存（redis）信息
 * @author liuxiaobo
 */
class DeleteOrderPathCacheAction extends CAction {

    public function run($orderId = 0) {
        $this->controller->layout = FALSE;
        $result = array('error'=>0,'msg'=>'刷新成功');
        $isOrder = Order::model()->exists('order_id = :order_id', array(':order_id'=>$orderId));
        if($isOrder){
            $delPath = OrderCache::model()->deleteOrderPath($orderId);
            $delTimeLine = OrderCache::model()->deleteTimeLine($orderId);
            $initOrderInfo = OrderCache::model()->initOrderInfo($orderId);
        }
        echo CJSON::encode($result);
        Yii::app()->end();
    }

}
