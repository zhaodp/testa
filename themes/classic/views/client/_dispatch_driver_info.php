<?php

$cs = Yii::app()->getClientScript();
$cs->registerScriptFile("http://api.map.baidu.com/library/MapWrapper/1.2/src/MapWrapper.min.js",CClientScript::POS_HEAD);
$driver = DriverStatus::model()->get($driver->user);
?>
司机位置：(上次更新时间<?php echo date(Yii::app()->params['formatDateTime'] ,$driver->heartbeat);?>)
<?php
	switch ($driver->status){
		case 0:
		case 3:
			echo '空闲';
			break;
		case 1:
			echo '服务中';
			break;
		case 2:
			echo '下班';
			break;
		default:
			echo '下班';
			break;
	} 
?>				

<div id="divMap" style="width:400px;height:300px;border:solid 1px gray"></div>
<script type="text/javascript">
$(document).ready(function(){
    var myMap = new BMap.Map("divMap");
    myMap.centerAndZoom(new BMap.Point(<?php echo $driver->position['longitude'].','.$driver->position['latitude'];?>), 16);
    
    //可以转化gps坐标
    var mapWforGPS = new BMapLib.MapWrapper(myMap, BMapLib.COORD_TYPE_GPS); 
    //添加gps坐标mkr
    var point = new BMap.Point(<?php echo $driver->position['longitude'].','.$driver->position['latitude'];?>);
    var gpsMkr = new BMap.Marker(point);
	mapWforGPS.addOverlay(gpsMkr);

})    

 </script>