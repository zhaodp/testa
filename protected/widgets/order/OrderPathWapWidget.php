<?php
/**
 * 订单轨迹信息
 * @author  liuxiaobo
 * @since 2013-12-17
 */

class OrderPathWapWidget extends CWidget
{
    public $orderId;            //订单id
    public $orderIdEncrypt = false;     //订单号是否是加密处理过的,默认是没处理过
    public $htmlOptions;            //HTML标签属性
    public $viewName;           //视图文件名称

    public function run()
    {
        $data = array(
            'city' => '上海',
            'addPoint' => '',
            'linePoint' => '',
        );
        $orderId = $this->orderIdEncrypt ? Common::decryptOrderId($this->orderId) : $this->orderId;
        
        $order = Order::model()->findByPk($orderId);
        if (!empty($order)) {
            $driverId = $order->driver_id;
            $startTime = $order->start_time;
            $endTime = $order->end_time;
            if($driverId && $startTime > 0 && $endTime > 0){
                $user_id = 0;
                $driver = Driver::model()->findByAttributes(array('user'=>$driverId));
                if (!empty($driver))
                {
                    $user_id = $driver->id;
                }else{
                    $driver = new Driver();
                }
                try {
//                    $pointData = DriverPosition::model()->getDriverPositionTrackByTime(5723, '2013-12-01 00:08:37', '2013-12-01 00:42:03');
                    $pointData = DriverPosition::model()->getDriverPositionTrackByTime($user_id, date('Y-m-d H:i:s', $startTime), date('Y-m-d H:i:s', $endTime));
                } catch (Exception $e) {
                    
                }
            }
            
            
            $data['city'] = Dict::item('city', 1);
            $etrack = isset($pointData['pointData']) ? $pointData['pointData'] : array();
            $addPoint = '';
            $linePoint = '';
            $centerLng = '';
            $centerLat = '';
            $etrackCount = count($etrack);
            $useI = 0;
            foreach ($etrack as $i => $position) {
                if(!isset($position['baidu_lat']) || !isset($position['baidu_lng']) || !isset($position['state']) || !isset($position['created'])){
                    continue;
                }
                $latitude = $position['baidu_lat'];
                $longitude = $position['baidu_lng'];
                $state = 0;
                $datetime = $position['created'];
                if ($latitude != '' && $longitude != '') {
                    if(!$addPoint){
                        $addPoint = sprintf('marker = addDriver(%s, %s, "%s",%s);' . "\n", $latitude, $longitude, '起点：'.$datetime, $state);
                    }
                    if($etrackCount > 20 && $i%10!=0){
                        continue;
                    }
                    $centerLng = $centerLng ? ($centerLng+$longitude)/2 : $longitude;
                    $centerLat = $centerLat ? ($centerLat+$latitude)/2 : $latitude;
                    $linePoint .= sprintf('new BMap.Point(%s, %s),' . "\n", $longitude, $latitude);
                    $useI = $i;
                }
            }
            if($addPoint && $useI !== ''){
                $position = $etrack[$useI];
                $latitude = $position['baidu_lat'];
                $longitude = $position['baidu_lng'];
                $state = $position['state'];
                $datetime = $position['created'];
                $addPoint .= sprintf('marker = addDriver(%s, %s, "%s",%s);' . "\n", $latitude, $longitude, '终点：'.$datetime, 1);
            }
            $data['centerLng'] = $centerLng;
            $data['centerLat'] = $centerLat;
            $data['addPoint'] = $addPoint;
            $data['linePoint'] = $linePoint;
            if(!empty($addPoint)){
                $this->render($this->viewName ? $this->viewName : 'path_wap', array(
                    'data' => $data,
                    'htmlOptions' => $this->htmlOptions,
                ));
            }
        }
    }
}