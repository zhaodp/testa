<?php
/**
 * 订单轨迹信息
 * @author  liuxiaobo
 * @since 2013-12-17
 */

class OrderPathWidget extends CWidget
{
    public $orderId;            //订单id
    public $htmlOptions;            //HTML标签属性
    public $viewName;           //视图文件名称

    public function run()
    {
        $data = array(
            'city' => '上海',
            'addPoint' => '',
            'linePoint' => '',
        );
        $orderId = $this->orderId;
        
        $order = Order::model()->findByPk($orderId);
        if ($order) {
            $data['city'] = Dict::item('city', $order->city_id);
            $path = Order::model()->getOrderPathFromCache($order->order_id);
            $etrack = isset($path['etrack']) ? $path['etrack'] : array();
            $addPoint = '';
            $linePoint = '';
            $centerLng = '';
            $centerLat = '';
            $etrackCount = count($etrack);
            $useI = 0;
            foreach ($etrack as $i => $position) {
                if(!isset($position['lat']) || !isset($position['lng']) || !isset($position['status']) || !isset($position['timestamp'])){
                    continue;
                }
                $latitude = $position['lat'];
                $longitude = $position['lng'];
                $state = 0;
                $datetime = date("Y-m-d H:i:s", $position['timestamp']);
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
                $latitude = $position['lat'];
                $longitude = $position['lng'];
                $state = $position['status'];
                $datetime = date("Y-m-d H:i:s", $position['timestamp']);
                $addPoint .= sprintf('marker = addDriver(%s, %s, "%s",%s);' . "\n", $latitude, $longitude, '终点：'.$datetime, $state);
            }
            $data['centerLng'] = $centerLng;
            $data['centerLat'] = $centerLat;
            $data['addPoint'] = $addPoint;
            $data['linePoint'] = $linePoint;
            if(!empty($addPoint)){
                $this->render($this->viewName ? $this->viewName : 'path', array(
                    'data' => $data,
                    'htmlOptions' => $this->htmlOptions,
                ));
            }
        }
    }
}