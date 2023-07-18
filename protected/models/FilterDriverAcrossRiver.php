<?php


class FilterDriverAcrossRiver extends CRedis {
    
    public $host = 'redishaproxy.edaijia-inc.cn';
    
    public $port = 22121;
    
    private static $URL_PREFIX = 'http://api.map.baidu.com/direction/v1/routematrix?output=json&ak=504b96e42c0a4b4cdbfd27cbd9a7053d&mode=walking';
    
    private static $CITY_PREFIX = 'filter_driver_across_river_';
    
    private static $RIVER_LINES_PREFIX = 'river_lines_';
    
    private static $STRATEGY_BY_ROUTE = 'by_route';

    private static $STRATEGY_BY_INTERSECTED = 'by_intersected';
    
    private static $STRATEGY_BOTH = 'by_both';
    
    public static function model($className = __CLASS__) {
        $model = null;
        if (isset(self::$_models[$className] ))
            $model = self::$_models[$className];
        else {
            $model = self::$_models[$className] = new $className(null);
        }
        return $model;
    }
    
    public function filter($city_id, $drivers, $lng, $lat, $range) {
        if(empty($drivers)) {
            return $drivers;
        }
        
        $config = $this->redis->get(self::$CITY_PREFIX . $city_id);
        if(empty($config)) {
            return $drivers; 
        }
        $config = json_decode($config, true);

        if(isset($config['strategy'])) {
            if(self::$STRATEGY_BY_ROUTE == $config['strategy']) {
                EdjLog::info('using ' . self::$STRATEGY_BY_ROUTE . ' strategy');
                return $this->filterByRoute($drivers, $lng, $lat, $range);
            } else if(self::$STRATEGY_BY_INTERSECTED == $config['strategy']) {
                EdjLog::info('using ' . self::$STRATEGY_BY_INTERSECTED . ' strategy');
                return $this->filterByIntersected($city_id, $drivers, $lng, $lat);
            }
        }
        
        if(fmod(time(), 2)) {
            EdjLog::info('using ' . self::$STRATEGY_BY_ROUTE . ' strategy');
            return $this->filterByRoute($drivers, $lng, $lat, $range);
        }
        
        EdjLog::info('using ' . self::$STRATEGY_BY_INTERSECTED . ' strategy');
        return $this->filterByIntersected($city_id, $drivers, $lng, $lat);
    }
    
    private function filterByRoute($drivers, $lng, $lat, $range) {
        $locations = array();
        foreach($drivers as $driver) {
            $locations[] = $driver['lat'] . ',' . $driver['lng'];
        }
    
        $baidu_route_data = $this->sendBaiduRouteRequest($locations, $lng, $lat);
        $baidu_route_distances = $this->getDistances($baidu_route_data);
    
        if(empty($baidu_route_distances)) {
            EdjLog::warning('can not filter drivers by route distance since no response from baidu api');
            return $drivers;
        }
    
        $inRangeDrivers = array();
        for($i = 0; $i < count($baidu_route_distances); $i++) {
            if($baidu_route_distances[$i] < $range) {
                $inRangeDrivers[] = $drivers[$i];
            } else {
                EdjLog::info('driver ' . json_encode($drivers[$i]) . ' filtered out due to route distance is ' . $baidu_route_distances[$i]);
            }
        }
        return $inRangeDrivers;
    }
    
    
    private function sendBaiduRouteRequest($locations, $lng, $lat) {
        $url = self::$URL_PREFIX . "&destinations=$lat,$lng" . '&origins=' . implode('|', $locations); 
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $start = microtime(true);
        $data = curl_exec($curl);
        EdjLog::info('sendBaiduRouteRequest costs ' . (microtime(true) - $start));
        curl_close($curl);
        return $data;
    }
    
    private function getDistances($baidu_route_data) {
        if(empty($baidu_route_data)) {
            return false;
        }
    
        $data = json_decode($baidu_route_data, true);
        if(!isset($data['status'])
            || $data['status'] != 0
            || empty($data['result'])
            || empty($data['result']['elements'])) {
            return false;
        }
    
        $routeDistances = array();
        foreach($data['result']['elements'] as $item) {
            if(isset($item['distance']) && isset($item['distance']['value'])) {
                $routeDistances[] = $item['distance']['value'];
            } else {
                $routeDistances[] = -1;
            }
        }
        return $routeDistances;
    }
    
    private function filterByIntersected($city_id, $drivers, $lng, $lat) {
        $startTime = microtime(true);
        
        $river_lines = $this->redis->lrange(self::$RIVER_LINES_PREFIX . $city_id, 0, -1);
        if(empty($river_lines)) {
            EdjLog::info("no river lines defined in city $city_id, skip filter");
            return $drivers;
        }
        
        $customerPosition = array('lng' => $lng, 'lat' => $lat);
        EdjLog::info("customer point is $lng , $lat");
        $resultDrivers = array();
        
        foreach($drivers as $driver) {
            $driverPosition = array('lng' => $driver['lng'], 'lat' => $driver['lat']);
            $intersected = false;
            
            foreach($river_lines as $river_line) {
                $river_line = json_decode($river_line, true);
                if($this->isIntersected($customerPosition, $driverPosition, $river_line['start'], $river_line['end'])) {
                    EdjLog::info("driver " . json_encode($driver) . ' filtered out due to intersected with river line ' . json_encode($river_line));
                    $intersected = true;
                    break;
                }
            }
            
            if(!$intersected) {
                $resultDrivers[] = $driver;
            }
        }
        
        EdjLog::info('filterByIntersected time costs:' . (microtime(true) - $startTime));
        return $resultDrivers;
    }
    
    /**
     * 判断平面上客户和司机的连线是否和河面线段相交
     * 参考以下文章
     * http://blog.csdn.net/rickliuxiao/article/details/6259322 (算法2)
     * http://wenku.baidu.com/view/0e740cc58bd63186bcebbcb4.html
     */
    private function isIntersected($customerPosition, $driverPosition, $lineStartPosition, $lineEndPosition) {
        $customerLocation = $this->pointToPixel($customerPosition);
        $driverLocation = $this->pointToPixel($driverPosition);
        $lineStartLocation = $this->pointToPixel($lineStartPosition);
        $lineEndLocation = $this->pointToPixel($lineEndPosition);
        
        // 快速排斥试验判断以两条线段为对角线的两个矩形是否相交
        if(max($customerLocation['x'], $driverLocation['x']) < min($lineStartLocation['x'], $lineEndLocation['x'])) {
            return false;
        }
        if(max($customerLocation['y'], $driverLocation['y']) < min($lineStartLocation['y'], $lineEndLocation['y'])) {
            return false;
        }
        if(max($lineStartLocation['x'], $lineEndLocation['x']) < min($customerLocation['x'], $driverLocation['x'])) {
            return false;
        }
        if(max($lineStartLocation['y'], $lineEndLocation['y']) < min($customerLocation['y'], $driverLocation['y'])) {
            return false;
        }
        
        // 跨立试验
        if($this->multi($lineStartLocation, $driverLocation, $customerLocation)
            * $this->multi($driverLocation, $lineEndLocation, $customerLocation) < 0) {
            return false;
        }
        if($this->multi($customerLocation, $lineEndLocation, $lineStartLocation)
            * $this->multi($lineEndLocation, $driverLocation, $lineStartLocation) < 0) {
            return false;
        }
        
        return true;
    }
        
    /**
     * 求矢量[p3, p1], [p3, p2]的叉积, p3是顶点
     * 若结果等于0，则这三点共线
     * 若结果大于0，则p3p2在p3p1的逆时针方向
     * 若结果小于0，则p3p2在p3p1的顺时针方向
     */
    private function multi($p1, $p2, $p3) {
        return ($p1['x'] - $p3['x']) * ($p2['y'] - $p3['y']) - ($p2['x'] - $p3['x']) * ($p1['y'] - $p3['y']);
    }
    
    private function pointToPixel($position) {
        $pixel = BaiduMap::pointToPixel($position['lng'], $position['lat']);
        return array('x' => $pixel[0], 'y' => $pixel[1]);
    }
    
    public function getRetryDriverBase($city_id) {
        $config = $this->redis->get(self::$CITY_PREFIX . $city_id);
        if(empty($config)) {
            return null;
        }
        $config = json_decode($config, true);
        if(!empty($config['retry_driver_base'])) {
            return $config['retry_driver_base'];
        }
        return null;
    }
}