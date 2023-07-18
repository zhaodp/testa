<?php
/**
 * 订单时间线
 * @author  liuxiaobo
 * @since 2013-12-17
 */

class OrderTimeLineWidget extends CWidget
{
    public $orderId;            //订单id

    public $is_partner = false;

    public function run()
    {
        $data = array();
        $orderId = $this->orderId;
        
        $order = Order::model()->findByPk($orderId);
        if ($order) {
            $data = $this->getOrderTimeLineFromCache($orderId);
//          $data = Order::model()->getOrderTimeLineFromCache($orderId);
        }
        $data['order'] = $order;
	    $orderMoney = array();
	    $orderExt = OrderExt::model()->getPrimary($orderId);
	    $orderAttributes = $order->attributes;
	    $totalMoney = FinanceCastHelper::getOrderIncome($orderAttributes, $orderExt);
	    $subsidyMoney = FinanceCastHelper::getSubsidy($orderAttributes, $orderExt);
	    $tips         = FinanceCastHelper::getOrderFeeByExt($orderExt);
	    $orderMoney['total_money']      = bcsub($totalMoney, 0, 2);//总的钱
	    $orderMoney['subsidy_money']    = bcsub($subsidyMoney, 0, 2);//远程订单补贴
	    $orderMoney['tip_money']        = bcsub($tips, 0, 2);//小费
	    $orderMoney['income_money']     = isset($orderAttributes['income']) ? bcsub($orderAttributes['income'], 0, 2) : 0.00;//里程费
	    $orderMoney['price_money']      = isset($orderAttributes['price']) ? bcsub($orderAttributes['price'], 0 ,2) : 0.00;//订单现金
	    $this->render('time_line', array(
            'data' => $data,
            'is_partner' => $this->is_partner,
		    'order_money'   => $orderMoney,
        ));
    }
    
    /**
     * 接单
     * @param <array> $path
     * @return <array>
     * @author liuxiaobo
     */
    public function recevingOrder($path = array()){
        $recevingOrder = array();
        if(isset($path['receive'])){
            $receive = $path['receive'];
            $recevingOrder['log_time'] = isset($receive['timestamp']) ? date('Y-m-d H:i:s',$receive['timestamp']) : 0;
            $baidu_lng = isset($receive['lng']) ? $receive['lng'] : 0;
            $baidu_lat = isset($receive['lat']) ? $receive['lat'] : 0;
            $recevingOrder['street'] = GPS::model()->getStreetByBaiduGPS($baidu_lng, $baidu_lat);
        }
        return $recevingOrder;
    }
    
    /**
     * 到达
     * @param <array> $path
     * @return <array>
     * @author liuxiaobo
     */
    public function arrivePlace($path = array()){
        $arrivePlace = array();
        if(isset($path['arrive'])){
            $receive = $path['arrive'];
            $arrivePlace['log_time'] = isset($receive['timestamp']) ? date('Y-m-d H:i:s',$receive['timestamp']) : 0;
            $baidu_lng = isset($receive['lng']) ? $receive['lng'] : 0;
            $baidu_lat = isset($receive['lat']) ? $receive['lat'] : 0;
            $arrivePlace['street'] = GPS::model()->getStreetByBaiduGPS($baidu_lng, $baidu_lat);
        }
        return $arrivePlace;
    }
    
    /**
     * 开车
     * @param <array> $path
     * @return <array>
     * @author liuxiaobo
     */
    public function drivingStart($path = array()){
        $drivingStart = array();
        if(isset($path['start'])){
            $receive = $path['start'];
            $drivingStart['log_time'] = isset($receive['timestamp']) ? date('Y-m-d H:i:s',$receive['timestamp']) : 0;
            $drivingStart['wait_time_before_driving'] = isset($path['wait_time']) ? $path['wait_time'] : 0;
            $drivingStart['mid_wait_time'] = isset($path['mid_way_wait']) ? $path['mid_way_wait'] : 0;
        }
        return $drivingStart;
    }
    
    /**
     * 服务结束
     * @param <array> $path
     * @return <array>
     * @author liuxiaobo
     */
    public function drivingEnd($path = array()){
        $drivingEnd = array();
        if(isset($path['finish'])){
            $receive = $path['finish'];
            $drivingEnd['log_time'] = isset($receive['timestamp']) ? date('Y-m-d H:i:s',$receive['timestamp']) : 0;
        }
        return $drivingEnd;
    }
    
    /**
     * 报单
     * @param <array> $path
     * @return <array>
     * @author liuxiaobo
     */
    public function submitOrder($path = array()){
        $submitOrder = array();
        if(isset($path['submit'])){
            $receive = $path['submit'];
            $submitOrder['log_time'] = isset($receive['timestamp']) ? date('Y-m-d H:i:s',$receive['timestamp']) : 0;
        }
        return $submitOrder;
    }
    
    /**
     * 获取该订单的时间线
     * @param <obj> $order
     * @return <array>
     * @author liuxiaobo
     */
    public function getOrderTimeLine($order){
        $result = array();
        if(isset($order->order_id) && 0<$order->order_id){
            $orderId = $order->order_id;
            $result['create'] = $order->attributes;             //创建订单时的信息
            $dispatchInfo = Order::model()->getDispatchInfoByOrderId($orderId);             //接单
            $cancelOrderInfo = Order::model()->getOrderCancelByOrderId($orderId);           //取消订单
            $result['dispatch'] = $dispatchInfo;                    //派单时的信息
            $result['cancelOrder'] = $cancelOrderInfo;              //取消订单
            $path = Order::model()->getOrderPathFromCache($orderId);
            if($path){
                $recevingOrderInfo = $this->recevingOrder($path);   //接单
                $arrivePlaceInfo = $this->arrivePlace($path);       //到达客户指定位置
                $drivingStartInfo = $this->drivingStart($path);     //开车
                $drivingEndInfo = $this->drivingEnd($path);         //开车结束
                $submitOrderInfo = $this->submitOrder($path);       //报单
                
                $result['recevingOrder'] = $recevingOrderInfo;      //接单时的信息
                $result['arrivePlace'] = $arrivePlaceInfo;          //到达客户指定位置时的信息
                $result['drivingStart'] = $drivingStartInfo;        //开车时的信息
                $result['drivingEnd'] = $drivingEndInfo;            //开车结束时的信息
                $result['submitOrder'] = $submitOrderInfo;          //报单时的信息
            }
            /* 空数据时读库 */
            if(empty($result['recevingOrder'])){
                $result['recevingOrder'] = Order::model()->getOrderPositionByOrderId($orderId, 1);
            }
            if(empty($result['arrivePlace'])){
                $result['arrivePlace'] = Order::model()->getOrderPositionByOrderId($orderId, 20);
            }
            if(empty($result['drivingStart'])){
                $result['drivingStart'] = Order::model()->getOrderPositionByOrderId($orderId, 2);
            }
            if(empty($result['drivingEnd'])){
                $result['drivingEnd'] = Order::model()->getOrderPositionByOrderId($orderId, 29);
            }
            if(empty($result['submitOrder'])){
                $result['submitOrder'] = Order::model()->getOrderPositionByOrderId($orderId, 3);
            }
        }
        return $result;
    }
    
    /**
     * 根据订单id获取该订单的时间线(从缓存中获取)
     * @param <int> $orderId
     * @return <array>
     * @author liuxiaobo
     */
    public function getOrderTimeLineFromCache($orderId=0){
        $result = array();
        //如果有缓存就从缓存中取(已经完成的订单会被存到缓存中)
        $cache = OrderCache::model()->getTimeLine($orderId);
        if($cache){
            return $cache;
        }
        $order = Order::model()->findByPk($orderId);
        if($order){
            $result = $this->getOrderTimeLine($order);
            $onWay = in_array($order->status, Order::model()->getOnWayStatus());
            //非进行中的订单存到缓存中
            if(!$onWay && !empty($result)){
                $toCache = OrderCache::model()->setTimeLine($orderId,$result);
            }
        }
        return $result;
    }
}
